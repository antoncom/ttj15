<?php
/**
 * HsConfig Model for Highslide Configuration Component
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');

class HsConfigsModelHsConfig extends JModel
{

	var $_articleList = null;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the hsconfig identifier
	 *
	 * @access	public
	 * @param	int Hsconfig identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}


	/**
	 * Method to get a hsconfig
	 * @return object with data
	 */
	function &getData()
	{
		jimport('joomla.filesystem.file');
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT a.*, b.title FROM #__hsconfig a '.
					 ' LEFT JOIN #__content b '.
					 ' ON a.id = b.id '.
					'  WHERE a.id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->id = 0;
			$this->_data->css = null;
			$this->_data->skincontrols = null;
			$this->_data->skincontent = null;
			$this->_data->overlayhtml = null;
			$this->_data->params = null;
			$this->_data->published = false;
			$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'css'.DS.'default.css';
			if (JFile::exists($filename))
			{
				$this->_data->css = JFile::read($filename);
			}
		}
		if ($this->_state->task == 'copy')
		{
			$this->_data->id = 0;
		}
		return $this->_data;
	}

	function &getArticleList()
	{
		$query = ' SELECT a.id, a.title from #__content as a '
		        .' LEFT JOIN #__hsconfig as b on a.id = b.id '
		        .' WHERE b.modified is null';
		$this->_db->setQuery( $query );
		$this->_articleList = $this->_db->loadObjectList();
		return $this->_articleList;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store()
	{
		$data = JRequest::get( 'post' );
		$isnew = false;

		if ($data['id'] == 0)
		{
			$data['id'] = $data['cid'];
			$isnew = true;
		}
		$row =& $this->getTable();

		// Bind the form fields to the hsconfig table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Make sure the hsconfig record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$row->published = HsConfigHelper::isPublished( $row->id );

		$datenow =& JFactory::getDate();
		$row->modified = $datenow->toMySQL();

		if ($isnew)
		{
			if (!$row->_db->insertObject( $row->_tbl, $row, $row->_tbl_key ))
			{
				$row->setError(get_class( $row ).'::store failed - '.$row->_db->getErrorMsg());
				$this->setError( $this->_db->getErrorMsg());
				return false;
			}
		}
		else
		{
			// Store the web link table to the database
			if (!$row->store()) {
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
		}

		return true;
	}
	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();

		if (count( $cids ))
		{
			foreach($cids as $cid) {
				if (HsConfigHelper::isSiteConfig($cid))
				{
					$this->setError( JText::_('Site Configuration cannot be deleted').'.');
					return false;
				}
				if (HsConfigHelper::isPublished( $cid ))
				{
					$this->setError( JText::_('Configuration must be unpublished before deletion').'.');
					return false;
				}

				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function publish()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (count( $cids ))
		{
			foreach($cids as $cid)
			{
				$this->setId( $cid );
				$this->getData();
				$registry = new JRegistry();
				$registry->loadINI($this->_data->params);
				$hs_base = JURI::root(true).'/plugins/content/highslide/';

				//	generate the configuration files
				if (!$this->_savecss( $registry, $cid, $hs_base, $this->_data ))
					return false;
				if (!$this->_savejs( $registry, $cid, $hs_base, $this->_data ))
					return false;
			}

			$datenow =& JFactory::getDate();
			$row =& $this->getTable();
			JArrayHelper::toInteger( $cid );
			$k			= $row->_tbl_key;

			$cidx = $k . ' = ' . implode( ' OR ' . $k . '=', $cids );

			$query = 'UPDATE '. $row->_tbl
			. ' SET published = ' . (int) 1
			. ', publish_tmst = '. "'". $datenow->toMySQL() ."'"
			. ' WHERE ('.$cidx.')'
			;

			$row->_db->setQuery( $query );
			if (!$row->_db->query())
			{
				$row->setError($row->_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function publishOne( $id )
	{
		$this->setId( $id );
		$this->getData();
		$registry = new JRegistry();
		$registry->loadINI($this->_data->params);
		$hs_base = JURI::root(true).'/plugins/content/highslide/';

		//	generate the configuration files
		if (!$this->_savecss( $registry, $id, $hs_base, $this->_data ))
			return false;
		if (!$this->_savejs( $registry, $id, $hs_base, $this->_data ))
			return false;

		$datenow =& JFactory::getDate();
		$row =& $this->getTable();

		$query = 'UPDATE '. $row->_tbl
		. ' SET published = ' . (int) 1
		. ', publish_tmst = '. "'". $datenow->toMySQL() ."'"
		. ' WHERE id = ' .$id.' '
		;

		$row->_db->setQuery( $query );
		if (!$row->_db->query())
		{
			$row->setError($row->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function unpublish()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (count( $cids ))
		{
			foreach($cids as $cid)
			{
				if (!$this->_delete( HsConfigHelper::createJsName( $cid )))
					return false;
				if (!$this->_delete( HsConfigHelper::createCssName( $cid )))
					return false;
			}
			$row =& $this->getTable();
			$row->publish( $cids, false );
		}
		return true;
	}

	function _getVer(){
		return ('2.0.1');
	}

	/**
	 * Save a CSS stylesheet for the given id's configuration
	 *
	 * @param mixed $registry	configuration parameters
	 * @param mixed $id			associated configuration id
	 * @param mixed $hs_base	base path of highslide installation
	 * @return 					none. errors will be thrown
	 */
	function _savecss( &$registry, $id, $hs_base, &$data )
	{

		$filelines = array();
		$filelines[] = '/* ';

		if (HsConfigHelper::isSiteConfig($id) )
		{
			$filelines[] = '*  Highslide site styles';
		}
		else
		{
			$filelines[] = '*  Highslide article specific styles';
		}

		$fname = HsConfigHelper::createCssName( $id );
		$jnow		=& JFactory::getDate();

		$filelines[] = '*  DO NOT EDIT. Generated on ' . $jnow->toFormat() . ' (GMT) by the Highslide Configuration Component ' . $this->_getVer();
		$filelines[] = '*/';
		$filelines[] = '';

		//	Thumbnail visibility
		$filelines[] = '.highslide-active-anchor img {';
		$filelines[] = '	visibility: ' . $registry->getValue('thumbNail') . ';';
		$filelines[] = '}';

		// Opacity dimming
	   	$dimcolor = $registry->getValue('backgroundDimmingColor');
	   	if ($dimcolor == '')
	  	{
	   		$dimcolor = 'black';
	   	}
	   	$filelines[] = 	'.highslide-dimming {';
	   	$filelines[] = 	'	width: 100%;';
	   	$filelines[] = 	'	background: ' . $dimcolor . ';';
	   	$filelines[] = 	'}';

		if ($data->css != "")
		{
			$filelines[] = '/* Beginning of inserted CSS configuration parameters */';
			$newtext = eregi_replace( "url( )*\(( )*([^( ]+)", "url(" . JURI::root(true) . "\\3", $data->css );
			$filelines[] = $newtext;
			$filelines[] = '/* End of inserted CSS configuration parameters */';
		}

		if ($registry->getValue('ssPreset') != "")
		{
			$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'presets'.DS.'slideshow'.DS.$registry->getValue('ssPreset').'.css';
			if (JFile::exists($filename))
			{
				$presetdata = JFile::read($filename);
				$newtext = eregi_replace( "url( )*\(( )*([^( ]+)", "url(" . JURI::root(true) . "\\3", $presetdata );
				$filelines[] = $newtext;
			}
		}
		if ($registry->getValue('opPreset') != "")
		{
			$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'presets'.DS.'overlay'.DS.$registry->getValue('opPreset').'.css';
			if (JFile::exists($filename))
			{
				$presetdata = JFile::read($filename);
				$newtext = eregi_replace( "url( )*\(( )*([^( ]+)", "url(" . JURI::root(true) . "\\3", $presetdata );
				$filelines[] = $newtext;
			}
		}
		$filedata = implode( "\r\n", $filelines );

		return ($this->_write( $fname, $filedata ));
	}

	/**
	 * Save a Javascript file for the given id's configuration
	 *
	 * @param mixed $registry	configuration parameters
	 * @param mixed $id			associated configuration id
	 * @param mixed $hs_base	base path of highslide installation
	 * @return 					none. errors will be thrown
	 */
	function _savejs( &$registry, $id, $hs_base, &$data )
	{

		$filelines = array();
		$filelines[] = '/*';
		if (HsConfigHelper::isSiteConfig($id) )
		{
			$filelines[] = '*  Highslide site settings';
		}
		else
		{
			$filelines[] = '*  Highslide article specific settings';
		}

		$fname = HsConfigHelper::createJsName( $id );
		$jnow		=& JFactory::getDate();

		$filelines[] = '*  DO NOT EDIT. Generated on ' . $jnow->toFormat() . ' (GMT) by the Highslide Configuration Component ' . $this->_getVer();
		$filelines[] = '*/';
		$filelines[] = '';

		// Credits display
		$filelines[] = "hs.showCredits = " . $registry->getValue('showCredits') ."e;";
		if ($registry->getValue('crVPosition') != -1 || $registry->getValue('crHPosition') != -1)
		{
			$pos = "";
			$spc = "";
			if ($registry->getValue('crVPosition') != -1)
			{
				$pos .= $registry->getValue('crVPosition');
				$spc = " ";
			}
			if ($registry->getValue('crHPosition') != -1) {
				$pos .= $spc . $registry->getValue('crHPosition');
			}
			$filelines[] = "hs.creditsPosition = '" . $pos . "';";
		}

		$filelines[] = "hs.graphicsDir = 'plugins/content/highslide/graphics/';";

		// outlineType: outer-glow, rounded-white
		if ($registry->getValue('outlineType') != -1)
		{
			if ($registry->getValue('outlineType') == 'no-border' || $registry->getValue('outlineType') == "")
			{
				$filelines[] = "hs.outlineType = null;";
			}
			else
			{
				$filelines[] = "hs.outlineType = '" . $registry->getValue('outlineType') . "';";
			}
		}
		if (is_numeric($registry->getValue('outlineStartOffset')))
		{
	    	$filelines[] = "hs.outlineStartOffset = " . $registry->getValue('outlineStartOffset') . ";";
		}
		if ($registry->getValue('wrapperClassname') != '')
		{
			$filelines[] = "hs.wrapperClassName = '" . JText::_($registry->getValue('wrapperClassname')) . "';";
		}
		if ($registry->getValue('outlineWhileAnimating') == '2')
		{
			$filelines[] = "hs.outlineWhileAnimating = " . $registry->getValue('outlineWhileAnimating') . ";";
		}
		else
		{
			$filelines[] = "hs.outlineWhileAnimating = " . $registry->getValue('outlineWhileAnimating') . "e;";
		}
		if ($registry->getValue('loadingText') != '')
		{
			$filelines[] = "hs.lang.loadingText = '" . JText::_($registry->getValue('loadingText')) . "';";
		}
		if ($registry->getValue('loadingTitle') != '')
		{
			$filelines[] = "hs.lang.loadingTitle = '" . JText::_($registry->getValue('loadingTitle')) . "';";
		}
		if (is_numeric($registry->getValue('loadingOpacity')))
		{
	    	$filelines[] = "hs.loadingOpacity = " . $registry->getValue('loadingOpacity') . ";";
		}

		// Dimming background opacity
		if (is_numeric($registry->getValue('dimmingOpacity')))
		{
	    	$filelines[] = "hs.dimmingOpacity = " . $registry->getValue('dimmingOpacity') . ";";
		}
		if ($registry->getValue('captionText') != '')
		{
			$filelines[] = "hs.captionText = '" . JText::_($registry->getValue('captionText')) . "';";
		}
		if ($registry->getValue('captionEval') != '-1')
		{
			$filelines[] = "hs.captionEval = '" . $registry->getValue('captionEval') . "';";
		}
		if ($registry->getValue('headingText') != '')
		{
			$filelines[] = "hs.headingText = '" . JText::_($registry->getValue('headingText')) . "';";
		}
		if ($registry->getValue('headingEval') != '-1')
		{
			$filelines[] = "hs.headingEval = '" . $registry->getValue('headingEval') . "';";
		}
		if ($registry->getValue('maincontentText') != '')
		{
			$filelines[] = "hs.maincontentText = '" . JText::_($registry->getValue('maincontentText')) . "';";
		}
		if ($registry->getValue('maincontentEval') != '-1')
		{
			$filelines[] = "hs.maincontentEval = '" . $registry->getValue('maincontentEval') . "';";
		}
		$filelines[] = "hs.padToMinWidth = " . $registry->getValue('padToMinWidth') . "e;";
		$filelines[] = "hs.padToMinWidth = " . $registry->getValue('padToMinWidth') . "e;";
		if ($registry->getValue('focusTitle') != '')
		{
			$filelines[] = "hs.lang.focusTitle = '" . JText::_($registry->getValue('focusTitle')) . "';";
		}
		if ($registry->getValue('cssDirection') != '-1')
		{
			$filelines[] = "hs.lang.cssDirection = '" . JText::_($registry->getValue('cssDirection')) . "';";
		}
		if ($registry->getValue('closeText') != '')
		{
			$filelines[] = "hs.lang.closeText = '" . JText::_($registry->getValue('closeText')) . "';";
		}
		if ($registry->getValue('closeTitle') != '')
		{
			$filelines[] = "hs.lang.closeTitle = '" . JText::_($registry->getValue('closeTitle')) . "';";
		}
		if ($registry->getValue('resizeTitle') != '')
		{
			$filelines[] = "hs.lang.resizeTitle = '" . JText::_($registry->getValue('resizeTitle')) . "';";
		}
		if ($registry->getValue('moveText') != '')
		{
			$filelines[] = "hs.lang.moveText = '" . JText::_($registry->getValue('moveText')) . "';";
		}
		if ($registry->getValue('moveTitle') != '')
		{
			$filelines[] = "hs.lang.moveTitle = '" . JText::_($registry->getValue('moveTitle')) . "';";
		}
		if ($registry->getValue('nextText') != '')
		{
			$filelines[] = "hs.lang.nextText = '" . JText::_($registry->getValue('nextText')) . "';";
		}
		if ($registry->getValue('nextTitle') != '')
		{
			$filelines[] = "hs.lang.nextTitle = '" . JText::_($registry->getValue('nextTitle')) . "';";
		}
		if ($registry->getValue('previousText') != '')
		{
			$filelines[] = "hs.lang.previousText = '" . JText::_($registry->getValue('previousText')) . "';";
		}
		if ($registry->getValue('previousTitle') != '')
		{
			$filelines[] = "hs.lang.previousTitle = '" . JText::_($registry->getValue('previousTitle')) . "';";
		}
		if ($registry->getValue('playText') != '')
		{
			$filelines[] = "hs.lang.playText = '" . JText::_($registry->getValue('playText')) . "';";
		}
		if ($registry->getValue('playTitle') != '')
		{
			$filelines[] = "hs.lang.playTitle = '" . JText::_($registry->getValue('playTitle')) . "';";
		}
		if ($registry->getValue('pauseText') != '')
		{
			$filelines[] = "hs.lang.pauseText = '" . JText::_($registry->getValue('pauseText')) . "';";
		}
		if ($registry->getValue('pauseTitle') != '')
		{
			$filelines[] = "hs.lang.pauseTitle = '" . JText::_($registry->getValue('pauseTitle')) . "';";
		}
		if (JString::strtolower($registry->getValue('expandCursor')) != 'null')
		{
			$filelines[] = "hs.expandCursor = '" . $registry->getValue('expandCursor') . "';";
		}
		else
		{
			$filelines[] = "hs.expandCursor = null;";
		}
		if (JString::strtolower($registry->getValue('restoreCursor')) != 'null')
		{
			$filelines[] = "hs.restoreCursor = '" . $registry->getValue('restoreCursor') . "';";
		}
		else
		{
			$filelines[] = "hs.restoreCursor = null;";
		}
		if ($registry->getValue('creditsHref') != '')
		{
			$filelines[] = "hs.creditsHref = '" . JText::_($registry->getValue('creditsHref')) . "';";
		}
		if ($registry->getValue('creditsText') != '')
		{
			$filelines[] = "hs.lang.creditsText = '" . JText::_($registry->getValue('creditsText')) . "';";
		}
		if ($registry->getValue('creditsTitle') != '')
		{
			$filelines[] = "hs.lang.creditsTitle = '" . JText::_($registry->getValue('creditsTitle')) . "';";
		}
		if ($registry->getValue('number') != '')
		{
			$filelines[] = "hs.lang.number = '" . JText::_($registry->getValue('number')) . "';";
		}
		if ($registry->getValue('easing') != '')
		{
			if($registry->getValue('easing') == 'null')
			{
				$filelines[] = "hs.easing = '';";
			}
			else
			{
				$filelines[] = "hs.easing = '" . $registry->getValue('easing') . "';";
			}
		}
		if ($registry->getValue('easingClose') != '')
		{
			$filelines[] = "hs.easingClose = '" . $registry->getValue('easingClose') . "';";
		}
		else
		{
			$filelines[] = "hs.easingClose = hs.easing;";
		}
		if ($registry->getValue('objectType') != '-1')
		{
			$filelines[] = "hs.objectType = '" . $registry->getValue('objectType') . "';";
		}
		if (is_numeric($registry->getValue('fullExpandOpacity')))
		{
	    	$filelines[] = "hs.fullExpandOpacity = " . $registry->getValue('fullExpandOpacity') . ";";
		}
		if ($registry->getValue('numberPosition') != '-1')
		{
			$filelines[] = "hs.numberPosition = '" . $registry->getValue('numberPosition') . "';";
		}
		if (is_numeric($registry->getValue('height')))
		{
	    	$filelines[] = "hs.height = " . $registry->getValue('height') . ";";
		}
		if (is_numeric($registry->getValue('width')))
		{
	    	$filelines[] = "hs.width = " . $registry->getValue('width') . ";";
		}
		if ($registry->getValue('fullExpandTitle') != '')
		{
			$filelines[] = "hs.lang.fullExpandTitle = '" . JText::_($registry->getValue('fullExpandTitle')) . "';";
		}
		if ($registry->getValue('fullExpandText') != '')
		{
			$filelines[] = "hs.lang.fullExpandText = '" . JText::_($registry->getValue('fullExpandText')) . "';";
		}
		if ($registry->getValue('targetX') != '')
		{
			$filelines[] = "hs.targetX = '" . JText::_($registry->getValue('targetX')) . "';";
		}
		if ($registry->getValue('targetY') != '')
		{
			$filelines[] = "hs.targetY = '" . JText::_($registry->getValue('targetY')) . "';";
		}
		if (is_numeric($registry->getValue('marginTop')))
		{
	    	$filelines[] = "hs.marginTop = " . $registry->getValue('marginTop') . ";";
		}
		if (is_numeric($registry->getValue('marginBottom')))
		{
	    	$filelines[] = "hs.marginBottom = " . $registry->getValue('marginBottom') . ";";
		}
		if (is_numeric($registry->getValue('marginLeft')))
		{
	    	$filelines[] = "hs.marginLeft = " . $registry->getValue('marginLeft') . ";";
		}
		if (is_numeric($registry->getValue('marginRight')))
		{
	    	$filelines[] = "hs.marginRight = " . $registry->getValue('marginRight') . ";";
		}
		if (is_numeric($registry->getValue('minHeight')))
		{
	    	$filelines[] = "hs.minHeight = " . $registry->getValue('minHeight') . ";";
		}
		if (is_numeric($registry->getValue('minWidth')))
		{
	    	$filelines[] = "hs.minWidth = " . $registry->getValue('minWidth') . ";";
		}
		if (is_numeric($registry->getValue('maxHeight')))
		{
			$filelines[] = "hs.maxHeight = " . $registry->getValue('maxHeight') . ";";
		}
		if (is_numeric($registry->getValue('maxWidth')))
		{
			$filelines[] = "hs.maxWidth = " . $registry->getValue('maxWidth') . ";";
		}
		if (is_numeric($registry->getValue('objectHeight')))
		{
	    	$filelines[] = "hs.objectHeight = " . $registry->getValue('objectHeight') . ";";
		}
		if (is_numeric($registry->getValue('objectWidth')))
		{
	    	$filelines[] = "hs.objectWidth = " . $registry->getValue('objectWidth') . ";";
		}
		if (is_numeric($registry->getValue('numberOfImagesToPreload')))
		{
	    	$filelines[] = "hs.numberOfImagesToPreload = " . $registry->getValue('numberOfImagesToPreload') . ";";
		}
		if ($registry->getValue('transitions') != '')
		{
			$filelines[] = "hs.transitions = [" . JText::_($registry->getValue('transitions')) . "];";
		}

		if ($registry->getValue('fullExpandVPosition') != -1 || $registry->getValue('fullExpandHPosition') != -1)
		{
			$pos = "";
			$spc = "";
			if ($registry->getValue('fullExpandVPosition') != -1)
			{
				$pos .= $registry->getValue('fullExpandVPosition');
				$spc = " ";
			}
			if ($registry->getValue('fullExpandHPosition') != -1) {
				$pos .= $spc . $registry->getValue('fullExpandHPosition');
			}
			$filelines[] = "hs.fullExpandPosition = '" . $pos . "';";
		}
		$filelines[] = "hs.objectLoadTime = '" . $registry->getValue('objectLoadTime') . "';";
		$filelines[] = "hs.align = '" . $registry->getValue('align') . "';";
		$filelines[] = "hs.anchor = '" . $registry->getValue('anchor') . "';";
		$filelines[] = "hs.allowSizeReduction = " . $registry->getValue('allowSizeReduction') . "e;";
		$filelines[] = "hs.fadeInOut = " . $registry->getValue('fadeInOut') . "e;";
		$filelines[] = "hs.allowMultipleInstances = " . $registry->getValue('allowMultipleInstances') . "e;";
		$filelines[] = "hs.allowWidthReduction = " . $registry->getValue('allowWidthReduction') . "e;";
		$filelines[] = "hs.allowHeightReduction = " . $registry->getValue('allowHeightReduction') . "e;";
		$filelines[] = "hs.blockRightClick = " . $registry->getValue('blockRightClick') . "e;";
		$filelines[] = "hs.enableKeyListener = " . $registry->getValue('enableKeyListener') . "e;";
		$filelines[] = "hs.dynamicallyUpdateAnchors = ". $registry->getValue('dynamicallyUpdateAnchors') . "e;";
		$filelines[] = "hs.useBox = ". $registry->getValue('useBox') . "e;";
		$filelines[] = "hs.cacheAjax = " . $registry->getValue('cacheAjax') . "e;";
		$filelines[] = "hs.preserveContent = " . $registry->getValue('preserveContent') . "e;";
		$filelines[] = "hs.dragByHeading = " . $registry->getValue('dragbyheading') . "e;";
		if ($registry->getValue('openerTagNames') != '')
		{
			$otn = explode( ',', $registry->getValue('openerTagNames'));
			$otntext = "";
			$sep = " ";
			foreach ($otn as $tn)
			{
				$otntext .= $sep."'".JString::trim($tn)."'";
				$sep = ", ";
			}
			$filelines[] = "hs.openerTagNames = [" . $otntext . " ];";
		}
		if (is_numeric($registry->getValue('dragSensitivity')))
		{
			$filelines[] = "hs.dragSensitivity = " . $registry->getValue('dragSensitivity') . ";";
		}
		if (is_numeric($registry->getValue('dimmingDuration')))
		{
			$filelines[] = "hs.dimmingDuration = " . $registry->getValue('dimmingDuration') . ";";
		}
		if (is_numeric($registry->getValue('expandDuration')))
		{
			$filelines[] = "hs.expandDuration = " . $registry->getValue('expandDuration') . ";";
		}
		if (is_numeric($registry->getValue('transitionDuration')))
		{
			$filelines[] = "hs.transitionDuration = " . $registry->getValue('transitionDuration') . ";";
		}
		if (is_numeric($registry->getValue('expandSteps')))
		{
			$filelines[] = "hs.expandSteps = " . $registry->getValue('expandSteps') . ";";
		}
		if (is_numeric($registry->getValue('restoreCursorDuration')))
		{
			$filelines[] = "hs.restoreCursorDuration = " . $registry->getValue('restoreCursorDuration') . ";";
		}
		if (is_numeric($registry->getValue('restoreCursorSteps')))
		{
			$filelines[] = "hs.restoreCursorSteps = " . $registry->getValue('restoreCursorSteps') . ";";
		}
		if (is_numeric($registry->getValue('zIndexCounter')))
		{
			$filelines[] = "hs.zIndexCounter = " . $registry->getValue('zIndexCounter') . ";";
		}
		if ($registry->getValue('restoreTitle') != '')
		{
			$filelines[] = "hs.lang.restoreTitle = '" . JText::_($registry->getValue('restoreTitle')) . "';";
		}
		if ($registry->getValue('caEnableCaptionOverlay') == 1)
		{
			$filelines[] = "hs.captionOverlay.fade = " . $registry->getValue('caFade') . ";";
			if ($registry->getValue('caOVVPosition') != -1 || $registry->getValue('caOVHPosition') != -1)
			{
				$pos = "";
				$spc = "";
				if ($registry->getValue('caOVVPosition') != -1)
				{
					$pos .= $registry->getValue('caOVVPosition');
					$spc = " ";
				}
				if ($registry->getValue('caOVHPosition') != -1) {
					$pos .= $spc . $registry->getValue('caOVHPosition');
				}
				$filelines[] = "hs.captionOverlay.position = '" . $pos . "';";
			}
			if (is_numeric($registry->getValue('caOVOffsetX')))
			{
				$filelines[] = "hs.captionOverlay.offsetX = " . $registry->getValue('caOVOffsetX') .";";
			}
			if (is_numeric($registry->getValue('caOVOffsetY')))
			{
				$filelines[] = "hs.captionOverlay.offsetY = " . $registry->getValue('caOVOffsetY') .";";
			}
			if ($registry->getValue('caOVRelativeTo') != -1)
			{
				$filelines[] = "hs.captionOverlay.relativeTo = '" . $registry->getValue('caOVRelativeTo') . "';";
			}
			$filelines[] = "hs.captionOverlay.hideOnMouseOut = " . $registry->getValue('caHideOnMouseOut') . "e;";
			if (is_numeric($registry->getValue('caOpacity')))
			{
				$filelines[] = "hs.captionOverlay.opacity = " . $registry->getValue('caOpacity') .";";
			}
			if ($registry->getValue('caOVWidth') != '')
			{
				$filelines[] = "hs.captionOverlay.width = '" . $registry->getValue('caOVWidth') . "';";
			}
			if ($registry->getValue('caOVClassname') != '')
			{
				$filelines[] = "hs.captionOverlay.className = '" . $registry->getValue('caOVClassname') . "';";
			}
		}
		if ($registry->getValue('hdEnableHeadingOverlay') == 1)
		{
			$filelines[] = "hs.headingOverlay.fade = " . $registry->getValue('hdFade') . ";";
			if ($registry->getValue('hdOVVPosition') != -1 || $registry->getValue('hdOVHPosition') != -1)
			{
				$pos = "";
				$spc = "";
				if ($registry->getValue('hdOVVPosition') != -1)
				{
					$pos .= $registry->getValue('hdOVVPosition');
					$spc = " ";
				}
				if ($registry->getValue('hdOVHPosition') != -1) {
					$pos .= $spc . $registry->getValue('hdOVHPosition');
				}
				$filelines[] = "hs.headingOverlay.position = '" . $pos . "';";
			}
			if (is_numeric($registry->getValue('hdOVOffsetX')))
			{
				$filelines[] = "hs.headingOverlay.offsetX = " . $registry->getValue('hdOVOffsetX') .";";
			}
			if (is_numeric($registry->getValue('hdOVOffsetY')))
			{
				$filelines[] = "hs.headingOverlay.offsetY = " . $registry->getValue('hdOVOffsetY') .";";
			}
			if ($registry->getValue('hdOVRelativeTo') != -1)
			{
				$filelines[] = "hs.headingOverlay.relativeTo = '" . $registry->getValue('hdOVRelativeTo') . "';";
			}
			$filelines[] = "hs.headingOverlay.hideOnMouseOut = " . $registry->getValue('hdHideOnMouseOut') . "e;";
			if (is_numeric($registry->getValue('hdOpacity')))
			{
				$filelines[] = "hs.headingOverlay.opacity = " . $registry->getValue('hdOpacity') .";";
			}
			if ($registry->getValue('hdOVWidth') != '')
			{
				$filelines[] = "hs.headingOverlay.width = '" . $registry->getValue('hdOVWidth') . "';";
			}
			if ($registry->getValue('hdOVClassname') != '')
			{
				$filelines[] = "hs.headingOverlay.className = '" . $registry->getValue('hdOVClassname') . "';";
			}
		}
		if($registry->getValue("mouseHoverAction") != "")
		{
			if ($registry->getValue("mouseHoverAction") == "focus")
			{
				$filelines[] = "hs.Expander.prototype.onMouseOver = function (sender) { ";
				$filelines[] = "   sender.focus();";
				$filelines[] = "};";
			}
			if ($registry->getValue("mouseHoverAction") == "close")
			{
				$filelines[] = "hs.Expander.prototype.onMouseOut = function (sender) { ";
				$filelines[] = "   sender.close();";
				$filelines[] = "};";
			}
		}
		if ($registry->getValue("modalWhenDimmed") == "yes")
		{
			$filelines[] = "hs.onDimmerClick = function () { ";
			$filelines[] = "   return false;";
			$filelines[] = "};";
		}

		$filelines[] = "hs.Expander.prototype.onBeforeGetCaption = function(sender)";
		$filelines[] = "{";
		$filelines[] = "	if (typeof sender.captionId != 'undefined' && sender.captionId != null)";
		$filelines[] = "	{";
		$filelines[] = "		if (document.getElementById( sender.captionId ) == null && sender.a.onclick != null)";
		$filelines[] = "		{";
		$filelines[] = "			var onclick = sender.a.onclick.toString();";
		$filelines[] = "			var onclickprop = onclick.match(/(hsjcaption:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "			if (onclickprop != null)";
		$filelines[] = "			{";
		$filelines[] = "				var text = unescape( onclickprop[3] );";
		$filelines[] = "				var div = document.createElement('div');";
		$filelines[] = "				div['innerHTML'] = hs.replaceLang( text );";
		$filelines[] = "				div['id'] = sender.captionId;";
		$filelines[] = "				div['className'] = 'highslide-caption';";
		$filelines[] = "				var onclickstyle = onclick.match(/(hsjcaptionstyle:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "				if (onclickstyle != null)";
		$filelines[] = "				{";
		$filelines[] = "					var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);";
		$filelines[] = "					if (styles != null)";
		$filelines[] = "					{";
		$filelines[] = "						for (var i = 0; i < styles.length; i++)";
		$filelines[] = "						{";
		$filelines[] = "							var arr;";
		$filelines[] = "							arr = styles[i].split(\":\");";
		$filelines[] = "							div.style[arr[0]] = arr[1].replace( \" \", \"\");";
		$filelines[] = "						}";
		$filelines[] = "					}";
		$filelines[] = "				}";
		$filelines[] = "				sender.a.appendChild( div );";
		$filelines[] = "			}";
		$filelines[] = "		}";
		$filelines[] = "	}";
		$filelines[] = "}";
		$filelines[] = "hs.Expander.prototype.onBeforeGetHeading = function(sender)";
		$filelines[] = "{";
		$filelines[] = "	if (typeof sender.headingId != 'undefined' && sender.headingId != null)";
		$filelines[] = "	{";
		$filelines[] = "		if (document.getElementById( sender.headingId ) == null && sender.a.onclick != null)";
		$filelines[] = "		{";
		$filelines[] = "			var onclick = sender.a.onclick.toString();";
		$filelines[] = "			var onclickprop = onclick.match(/(hsjheading:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "			if (onclickprop != null)";
		$filelines[] = "			{";
		$filelines[] = "				var text = unescape( onclickprop[3] );";
		$filelines[] = "				var div = document.createElement('div');";
		$filelines[] = "				div['innerHTML'] = hs.replaceLang( text );";
		$filelines[] = "				div['id'] = sender.headingId;";
		$filelines[] = "				div['className'] = 'highslide-heading';";
		$filelines[] = "				var onclickstyle = onclick.match(/(hsjheadingstyle:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "				if (onclickstyle != null)";
		$filelines[] = "				{";
		$filelines[] = "					var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);";
		$filelines[] = "					if (styles != null)";
		$filelines[] = "					{";
		$filelines[] = "						for (var i = 0; i < styles.length; i++)";
		$filelines[] = "						{";
		$filelines[] = "							var arr;";
		$filelines[] = "							arr = styles[i].split(\":\");";
		$filelines[] = "							div.style[arr[0]] = arr[1].replace( \" \", \"\");";
		$filelines[] = "						}";
		$filelines[] = "					}";
		$filelines[] = "				}";
		$filelines[] = "				sender.a.appendChild( div );";
		$filelines[] = "			}";
		$filelines[] = "		}";
		$filelines[] = "	}";
		$filelines[] = "	return true;";
		$filelines[] = "}";
		$filelines[] = "hs.Expander.prototype.onBeforeGetContent = function(sender)";
		$filelines[] = "{";
		$filelines[] = "	if (typeof sender.contentId != 'undefined' && sender.contentId != null)";
		$filelines[] = "	{";
		$filelines[] = "		if (document.getElementById( sender.contentId ) == null && sender.a.onclick != null)";
		$filelines[] = "		{";
		$filelines[] = "			var onclick = sender.a.onclick.toString();";
		$filelines[] = "			var onclickprop = onclick.match(/(hsjcontent:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "			if (onclickprop != null)";
		$filelines[] = "			{";
		$filelines[] = "				var text = unescape( onclickprop[3] );";
		$filelines[] = "				var div = document.createElement('div');";
		$filelines[] = "				div['innerHTML'] = hs.replaceLang( text );";
		$filelines[] = "				div['id'] = sender.contentId;";
		$filelines[] = "				div['className'] = 'highslide-html-content';";
		$filelines[] = "				var onclickstyle = onclick.match(/(hsjcontentstyle:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "				if (onclickstyle != null)";
		$filelines[] = "				{";
		$filelines[] = "					var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);";
		$filelines[] = "					if (styles != null)";
		$filelines[] = "					{";
		$filelines[] = "						for (var i = 0; i < styles.length; i++)";
		$filelines[] = "						{";
		$filelines[] = "							var arr;";
		$filelines[] = "							arr = styles[i].split(\":\");";
		$filelines[] = "							div.style[arr[0]] = arr[1].replace( \" \", \"\");";
		$filelines[] = "						}";
		$filelines[] = "					}";
		$filelines[] = "				}";
		$filelines[] = "				sender.a.appendChild( div );";
		$filelines[] = "			}";
		$filelines[] = "		}";
		$filelines[] = "	}";
		$filelines[] = "	return true;";
		$filelines[] = "}";
		if ($registry->getValue('htEnableSkin') == '1')
		{
			if ($data->skincontrols != '')
			{
				$data->skincontrols = ereg_replace("[\n|\r|\t]", "", $data->skincontrols );
				$data->skincontrols = addslashes( $data->skincontrols );
				$filelines[] = "hs.skin.controls = '" . $data->skincontrols . "';";
			}
			if ($data->skincontent != '')
			{
				$data->skincontent = ereg_replace("[\n|\r|\t]", "", $data->skincontent );
				$data->skincontent = addslashes( $data->skincontent );
				$filelines[] = "hs.skin.contentWrapper = '" . $data->skincontent . "';";
			}
		}
		if ( $registry->getValue('flVersion') != ""
		   ||$registry->getValue('flExpressInstallURL') != ""
		   ||$registry->getValue('flFlashvars') != ""
		   ||$registry->getValue('flParams') != ""
		   ||$registry->getValue('flAttribs') != ""
		   )
		{
			$flversion = ($registry->getValue('flVersion') != "") ? $registry->getValue('flVersion') : "7";
			$filelines[] = "hs.swfOptions = { ";
			$filelines[] = "	version: '" . $flversion . "'";
			if ($registry->getValue('flExpressInstallURL') != "")
			{
				$filelines[] = "	,expressInstallSwfurl: '" . addslashes($registry->getValue('flExpressInstallURL')) . "'";
			}
			if ($registry->getValue('flFlashvars') != "")
			{
				$filelines[] = "	,flashvars: { " . $this->_filterVars( $registry->getValue('flFlashvars') ) . " }";
			}
			if ($registry->getValue('flParams') != "")
			{
				$filelines[] = "	,params: { " . $this->_filterVars( $registry->getValue('flParams') ) . " }";
			}
			if ($registry->getValue('flAttribs') != "")
			{
				$filelines[] = "	,attributes: { " . $this->_filterVars( $registry->getValue('flAttribs') ) . " }";
			}
			$filelines[] = "};";
		}
		if ($registry->getValue("ovOverlayId") != "")
		{
			if ($data->overlayhtml != "")
			{
				$filelines[] = "hs.Expander.prototype.onCreateOverlay = function(sender, e)";
				$filelines[] = "{";
				$filelines[] = "    if (e.overlay.innerHTML.indexOf( '{thumbalt}', 0 ) != -1) {";
				$filelines[] = "		e.overlay.innerHTML = e.overlay.innerHTML.replace('{thumbalt}', sender.thumb.alt);";
				$filelines[] = "	}";
				$filelines[] = "    if (e.overlay.innerHTML.indexOf( '{thumbtitle}', 0 ) != -1) {";
				$filelines[] = "		e.overlay.innerHTML = e.overlay.innerHTML.replace('{thumbtitle}', sender.thumb.title);";
				$filelines[] = "	}";
				$filelines[] = "    if (e.overlay.innerHTML.indexOf( '{popuptitle}', 0 ) != -1) {";
				$filelines[] = "		e.overlay.innerHTML = e.overlay.innerHTML.replace('{popuptitle}', sender.a.title);";
				$filelines[] = "	}";
				$filelines[] = "   return true;";
				$filelines[] = "}";
			}

			$filelines[] = "hs.registerOverlay(";
			$filelines[] = "{";
			if ($registry->getValue("ovThumbnailId") == "")
			{
				$filelines[] = "    thumbnailId: null,";
			}
			else
			{
				$filelines[] = "    thumbnailId: '" . $registry->getValue("ovThumbnailId") . "',";
			}
			if ($registry->getValue("ovSlideshowGroup") != "")
			{
				$filelines[] = "    slideshowGroup: '" . $registry->getValue("ovSlideshowGroup") . "',";
			}
			$filelines[] = "    fade: " . $registry->getValue("ovFade") . ",";
			$filelines[] = "    overlayId: '" . $registry->getValue("ovOverlayId") . "',";
			if ($registry->getValue('ovVPosition') != -1 || $registry->getValue('ovHPosition') != -1)
			{
				$pos = "";
				$spc = "";
				if ($registry->getValue('ovVPosition') != -1)
				{
					$pos .= $registry->getValue('ovVPosition');
					$spc = " ";
				}
				if ($registry->getValue('ovHPosition') != -1) {
					$pos .= $spc . $registry->getValue('ovHPosition');
				}
				$filelines[] = "    position: '" . $pos . "',";
			}
			if (is_numeric($registry->getValue('ovOVOffsetX')))
			{
				$filelines[] = "    offsetX: " . $registry->getValue("ovOVOffsetX") . ",";
			}
			if (is_numeric($registry->getValue('ovOVOffsetY')))
			{
				$filelines[] = "    offsetY: " . $registry->getValue("ovOVOffsetY") . ",";
			}
			if ($registry->getValue('ovOVRelativeTo') != -1)
			{
				$filelines[] = "    relativeTo: '" . $registry->getValue("ovOVRelativeTo") . "',";
			}
			$filelines[] = "    hideOnMouseOut: " . $registry->getValue("ovHideOnMouseOut") . "e,";
			if (is_numeric($registry->getValue('ovOpacity')))
			{
				$filelines[] = "    opacity: " . $registry->getValue("ovOpacity") . ",";
			}
			if ($registry->getValue('ovWidth') != '')
			{
				$filelines[] = "    width: '" . $registry->getValue('ovWidth') . "',";
			}
			if ($registry->getValue('ovOVClassname') != '')
			{
				$filelines[] = "    className: '" . $registry->getValue('ovOVClassname') . "',";
			}
			$filelines[] = "    useOnHtml: " . $registry->getValue("ovUseOnHtml") . "e";
			$filelines[] = "});";
		}
		if ($registry->getValue('opPreset') != -1
		   && $registry->getValue('opOverlayId') != "")
		{
			$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'presets'.DS.'overlay'.DS.$registry->getValue('opPreset').'.html';
			if (JFile::exists($filename))
			{
				$oppresetdata = JFile::read($filename);
				$filelines[] = "hs.registerOverlay(";
				$filelines[] = "{";
				if ($registry->getValue("opThumbnailId") == "")
				{
					$filelines[] = "    thumbnailId: null,";
				}
				else
				{
					$filelines[] = "    thumbnailId: '" . $registry->getValue("opThumbnailId") . "',";
				}
				if ($registry->getValue("opSlideshowGroup") != "")
				{
					$filelines[] = "    slideshowGroup: '" . $registry->getValue("opSlideshowGroup") . "',";
				}
				$filelines[] = "    fade: " . $registry->getValue("opFade") . ",";
				$filelines[] = "    overlayId: '" . $registry->getValue("opOverlayId") . "',";
				if ($registry->getValue('opVPosition') != -1 || $registry->getValue('opHPosition') != -1)
				{
					$pos = "";
					$spc = "";
					if ($registry->getValue('opVPosition') != -1)
					{
						$pos .= $registry->getValue('opVPosition');
						$spc = " ";
					}
					if ($registry->getValue('opHPosition') != -1) {
						$pos .= $spc . $registry->getValue('opHPosition');
					}
					$filelines[] = "    position: '" . $pos . "',";
				}
				if (is_numeric($registry->getValue('opOVOffsetX')))
				{
					$filelines[] = "    offsetX: " . $registry->getValue("opOVOffsetX") . ",";
				}
				if (is_numeric($registry->getValue('opOVOffsetY')))
				{
					$filelines[] = "    offsetY: " . $registry->getValue("opOVOffsetY") . ",";
				}
				if ($registry->getValue('opOVRelativeTo') != -1)
				{
					$filelines[] = "    relativeTo: '" . $registry->getValue("opOVRelativeTo") . "',";
				}
				$filelines[] = "    hideOnMouseOut: " . $registry->getValue("opHideOnMouseOut") . "e,";
				if (is_numeric($registry->getValue('opOpacity')))
				{
					$filelines[] = "    opacity: " . $registry->getValue("opOpacity") . ",";
				}
				if ($registry->getValue('opWidth') != '')
				{
					$filelines[] = "    width: '" . $registry->getValue('opWidth') . "',";
				}
				if ($registry->getValue('opOVClassname') != '')
				{
					$filelines[] = "    className: '" . $registry->getValue('opOVClassname') . "',";
				}
				$filelines[] = "    useOnHtml: " . $registry->getValue("opUseOnHtml") . "e";
				$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'presets'.DS.'overlay'.DS.$registry->getValue('opPreset').'.js';
				if (JFile::exists($filename))
				{
					$filelines[] = JFile::read($filename);
				}
				else
				{
					$filelines[] = "});";
				}
			}
		}

		$filelines[] = "hs.Expander.prototype.onBeforeExpand = function (sender) {";
		$filelines[] = "	if (this.custom != null";
		$filelines[] = "       &&this.custom['overlayId'] != null)";
		$filelines[] = "    {";
		$filelines[] = "		sender.createOverlay( this.custom );";
		$filelines[] = "	}";
		$filelines[] = "	return true;";
		$filelines[] = "}";

		$filelines[] = "hs.Expander.prototype.onInit = function (sender)";
		$filelines[] = "{";
		$filelines[] = "	if (sender.a.onclick != null)";
		$filelines[] = "	{";
		$filelines[] = "		var onclick = sender.a.onclick.toString();";
		$filelines[] = "		var onclickprop = onclick.match(/(overlayId:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "		if (onclickprop != null)";
		$filelines[] = "		{";
		$filelines[] = "			var overlayId = onclickprop[3];";
		$filelines[] = "			if (document.getElementById( overlayId ) == null)";
		$filelines[] = "			{";
		$filelines[] = "				var onclickprop = onclick.match(/(hsjcustomOverlay:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "				if (onclickprop != null)";
		$filelines[] = "				{";
		$filelines[] = "					var text = unescape( onclickprop[3] );";
		$filelines[] = "					var div = document.createElement('div');";
		$filelines[] = "					div['innerHTML'] = hs.replaceLang( text );";
		$filelines[] = "					div['id'] = overlayId;";
		$filelines[] = "					div['className'] = 'highslide-overlay';";
		$filelines[] = "					var onclickstyle = onclick.match(/(hsjcustomOverlayStyle:)+\s*('|\")([^'\"]*)/);";
		$filelines[] = "					if (onclickstyle != null)";
		$filelines[] = "					{";
		$filelines[] = "						var styles = onclickstyle[3].match(/([^:; ])*:\s*([^,;}])*/g);";
		$filelines[] = "						if (styles != null)";
		$filelines[] = "						{";
		$filelines[] = "							for (var i = 0; i < styles.length; i++)";
		$filelines[] = "							{";
		$filelines[] = "								var arr;";
		$filelines[] = "								arr = styles[i].split(\":\");";
		$filelines[] = "								div.style[arr[0]] = arr[1].replace( \" \", \"\");";
		$filelines[] = "							}";
		$filelines[] = "						}";
		$filelines[] = "					}";
		$filelines[] = "					sender.a.appendChild( div );";
		$filelines[] = "					var overlayExists = false;";
		$filelines[] = "					for (var i = 0; i < hs.overlays.length; i++)";
		$filelines[] = "					{";
		$filelines[] = "						if (hs.overlays[i].overlayId == overlayId)";
		$filelines[] = "						{";
		$filelines[] = "							overlayExists = true;";
		$filelines[] = "						}";
		$filelines[] = "					}";
		$filelines[] = "					if (! overlayExists)";
		$filelines[] = "					{";
		$filelines[] = "						onclickprop = onclick.match(/(customOverlay:)+\s*{\s*([^}]*)}/);";
		$filelines[] = "						if (onclickprop != null)";
		$filelines[] = "						{";
		$filelines[] = "							try";
		$filelines[] = "							{";
		$filelines[] = "								eval( \"var opts = {\" + onclickprop[2] + \"}\" );";
		$filelines[] = "								opts.overlayId = overlayId;";
		$filelines[] = "								if (typeof sender.thumb.id != \"undefined\" && sender.thumb.id != \"\")";
		$filelines[] = "								{";
		$filelines[] = "									opts.thumbnailId = sender.thumb.id;";
		$filelines[] = "									hs.registerOverlay( opts );";
		$filelines[] = "								}";
		$filelines[] = "								else";
		$filelines[] = "								if (typeof sender.a.id != \"undefined\" && sender.a.id != \"\")";
		$filelines[] = "								{";
		$filelines[] = "									opts.thumbnailId = sender.a.id;";
		$filelines[] = "									hs.registerOverlay( opts );";
		$filelines[] = "								}";
		$filelines[] = "							}";
		$filelines[] = "							catch(e)";
		$filelines[] = "							{";
		$filelines[] = "								//	ignore";
		$filelines[] = "							}";
		$filelines[] = "						}";
		$filelines[] = "					}";
		$filelines[] = "				}";
		$filelines[] = "			}";
		$filelines[] = "		}";
		$filelines[] = "	}";

		if ($registry->getValue("ovOverlayId") != "")
		{
			if ($data->overlayhtml != "")
			{
				$data->overlayhtml = ereg_replace("[\n|\r|\t]", "", $data->overlayhtml );
				$data->overlayhtml = addslashes( $data->overlayhtml );
				$filelines[] = "	if (document.getElementById('" . $registry->getValue('ovOverlayId') ."') == null)";
				$filelines[] = "	{";
				$filelines[] = "		var div = document.createElement('div');";
				$filelines[] = "		var txt = '" . $data->overlayhtml . "';";
				$filelines[] = "		div['innerHTML'] = hs.replaceLang(txt);";
				$filelines[] = "		div['id'] = '" . $registry->getValue('ovOverlayId') . "';";
				$filelines[] = "		div['className'] = 'highslide-overlay';";
				$filelines[] = "		sender.a.appendChild( div );";
				$filelines[] = "	}";
			}
		}
		if ($registry->getValue("opOverlayId") != "")
		{
			if (isset($oppresetdata))
			{
				$oppresetdata = ereg_replace("[\n|\r|\t]", "", $oppresetdata );
				$oppresetdata = addslashes( $oppresetdata );
				$filelines[] = "	if (document.getElementById('" . $registry->getValue('opOverlayId') ."') == null)";
				$filelines[] = "	{";
				$filelines[] = "		var div = document.createElement('div');";
				$filelines[] = "		var txt = '" . $oppresetdata . "';";
				$filelines[] = "		div['innerHTML'] = hs.replaceLang(txt);";
				$filelines[] = "		div['id'] = '" . $registry->getValue('opOverlayId') . "';";
				$filelines[] = "		div['className'] = 'highslide-overlay';";
				$filelines[] = "		sender.a.appendChild( div );";
				$filelines[] = "	}";
			}
		}
		$filelines[] = "	return true;";
		$filelines[] = "}";
		if ($registry->getValue('ssEnableSlideshow') == 1)
		{
			$filelines[] = "hs.addSlideshow( {";
			if (is_numeric($registry->getValue('ssInterval')))
			{
				$filelines[] = "	interval: " . $registry->getValue('ssInterval');
			}
			if ($registry->getValue('ssSlideshowGroup') != '')
			{
				$ssg = explode( ',', $registry->getValue('ssSlideshowGroup'));
				$ssgtext = "";
				$sep = " ";
				foreach ($ssg as $ss)
				{
					$ssgtext .= $sep."'".JString::trim($ss)."'";
					$sep = ", ";
				}
				$filelines[] = "	,slideshowGroup: [" . $ssgtext . " ]";
			}
			$filelines[] = "	,repeat: " . $registry->getValue('ssRepeat') . "e";
			$filelines[] = "	,useControls: " . $registry->getValue('ssUseControls') . "e";
			if ($registry->getValue('ssFixedControls') == 'fit')
			{
				$filelines[] = "	,fixedControls: 'fit'";
			}
			else
			{
				$filelines[] = "	,fixedControls: " . $registry->getValue('ssFixedControls') . "e";
			}
			if ($registry->getValue('ssUseControls') == 'tru')
			{
				$filelines[] = "	,overlayOptions: {";
				$filelines[] = "		fade: " . $registry->getValue('ssFade');
				if ($registry->getValue('ssOVVPosition') != -1 || $registry->getValue('ssOVHPosition') != -1)
				{
					$pos = "";
					$spc = "";
					if ($registry->getValue('ssOVVPosition') != -1)
					{
						$pos .= $registry->getValue('ssOVVPosition');
						$spc = " ";
					}
					if ($registry->getValue('ssOVHPosition') != -1) {
						$pos .= $spc . $registry->getValue('ssOVHPosition');
					}
					$filelines[] = "		,position: '" . $pos . "'";
				}
				if (is_numeric($registry->getValue('ssOVOffsetX')))
				{
					$filelines[] = "    	,offsetX: " . $registry->getValue("ssOVOffsetX");
				}
				if (is_numeric($registry->getValue('ssOVOffsetY')))
				{
					$filelines[] = "    	,offsetY: " . $registry->getValue("ssOVOffsetY");
				}
				if ($registry->getValue('ssOVRelativeTo') != -1)
				{
					$filelines[] = "    	,relativeTo: '" . $registry->getValue("ssOVRelativeTo") . "'";
				}
				$filelines[] = "    	,hideOnMouseOut: " . $registry->getValue('ssHideOnMouseOut') . "e";
				if (is_numeric($registry->getValue('ssOpacity')))
				{
					$filelines[] = "    	,opacity: " . $registry->getValue('ssOpacity');
				}
				if ($registry->getValue('ssOVWidth') != '')
				{
					$filelines[] = "		,width: '" . $registry->getValue('ssOVWidth') . "'";
				}
				if ($registry->getValue('ssOVClassname') != '')
				{
					$filelines[] = "    	,className: '" . $registry->getValue('ssOVClassname') . "'";
				}
				$filelines[] = "	}";
			}
			if ($registry->getValue('tsEnableThumbstrip') == 1)
			{
				$filelines[] = "	,thumbstrip: {";
				$filelines[] = "		fade: " . $registry->getValue('tsFade');
				if ($registry->getValue('tsOVVPosition') != -1 || $registry->getValue('tsOVHPosition') != -1)
				{
					$pos = "";
					$spc = "";
					if ($registry->getValue('tsOVVPosition') != -1)
					{
						$pos .= $registry->getValue('tsOVVPosition');
						$spc = " ";
					}
					if ($registry->getValue('tsOVHPosition') != -1) {
						$pos .= $spc . $registry->getValue('tsOVHPosition');
					}
					$filelines[] = "		,position: '" . $pos . "'";
				}
				if (is_numeric($registry->getValue('tsOVOffsetX')))
				{
					$filelines[] = "    	,offsetX: " . $registry->getValue("tsOVOffsetX");
				}
				if (is_numeric($registry->getValue('tsOVOffsetY')))
				{
					$filelines[] = "    	,offsetY: " . $registry->getValue("tsOVOffsetY");
				}
				if ($registry->getValue('tsOVMode') != -1)
				{
					$filelines[] = "    	,mode: '" . $registry->getValue("tsOVMode") . "'";
				}
				if ($registry->getValue('tsOVRelativeTo') != -1)
				{
					$filelines[] = "    	,relativeTo: '" . $registry->getValue("tsOVRelativeTo") . "'";
				}
				$filelines[] = "    	,hideOnMouseOut: " . $registry->getValue('tsHideOnMouseOut') . "e";
				if (is_numeric($registry->getValue('tsOpacity')))
				{
					$filelines[] = "    	,opacity: " . $registry->getValue('tsOpacity');
				}
				if ($registry->getValue('tsOVWidth') != '')
				{
					$filelines[] = "		,width: '" . $registry->getValue('tsOVWidth') . "'";
				}
				if ($registry->getValue('tsOVClassname') != '')
				{
					$filelines[] = "    	,className: '" . $registry->getValue('tsOVClassname') . "'";
				}
				$filelines[] = "	}";
			}
			$filelines[] = "});";
			$filelines[] = "hs.autoplay = " . $registry->getValue('ssAutoplay') . "e";
		}

		if ($registry->getValue('ssPreset') != -1)
		{
			$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'presets'.DS.'slideshow'.DS.$registry->getValue('ssPreset').'.js';
			if (JFile::exists($filename))
			{
				$presetdata = JFile::read($filename);
				$filelines[] = $presetdata;
				if (is_numeric($registry->getValue('sspInterval')))
				{
					$filelines[] = "	,interval: " . $registry->getValue('sspInterval');
				}
				$filelines[] = "	,repeat: " . $registry->getValue('sspRepeat') . "e";
				if ($registry->getValue('sspSlideshowGroup') != '')
				{
					$ssg = explode( ',', $registry->getValue('sspSlideshowGroup'));
					$ssgtext = "";
					$sep = " ";
					foreach ($ssg as $ss)
					{
						$ssgtext .= $sep."'".JString::trim($ss)."'";
						$sep = ", ";
					}
					$filelines[] = "	,slideshowGroup: [" . $ssgtext . " ]";
				}
				$filelines[] = "});";
				$filelines[] = "hs.autoplay = " . $registry->getValue('sspAutoplay') . "e";
			}
		}


		$filedata = implode( "\r\n", $filelines );

		return ($this->_write( $fname, $filedata ) );
	}

	function _filterVars( $vars )
	{
		if (preg_match_all( "/([^, :]*):\s*'([^']*)'/", $vars, $arr ))
		{
			$c = "";
			$str = "";
			foreach ($arr[0] as $v)
			{
				$str .= $c.$v;
				$c = ", ";
			}
		}

		return $str;
	}

	/**
	 * Delete the specified file if it exists
	 *
	 * @param mixed $fname		full path name of file to be deleted
	 * @return 					none, errors will be thrown
	 */
	function _delete( $fname )
	{
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		if (JPath::isOwner($fname) && !JPath::setPermissions($fname, '0644'))
		{
			JError::raiseNotice('SOME_ERROR_CODE', 'Could not make ' . $fname . ' writable');
		}

		if (JFile::exists( $fname))
		{
			JFile::delete( $fname );
		}

		// Try to make configuration.php unwriteable
		if (JPath::isOwner($fname) && !JPath::setPermissions($fname, '0444'))
		{
			if (JPath::isOwner($fname) && !JPath::setPermissions($fname, '0444'))
			{
				JError::raiseNotice('SOME_ERROR_CODE', 'Could not make ' . $fname . ' unwritable');
			}
		}
		return true;
	}

	/**
	 * Create the specified file.
	 *
	 * @param mixed $fname		full path name of file to be written
	 * @param mixed $data		data to be written to the file
	 * @return 					none, errors will be thrown
	 */
	function _write( &$fname, &$data )
	{
		jimport('joomla.filesystem.path');
		jimport('joomla.filesystem.file');

		if (JPath::isOwner($fname) && !JPath::setPermissions($fname, '0644'))
		{
			JError::raiseNotice('SOME_ERROR_CODE', 'Could not make ' . $fname . ' writable');
		}

		JFile::write( $fname, $data );

		// Try to make configuration.php unwriteable
		if (JPath::isOwner($fname) && !JPath::setPermissions($fname, '0444'))
		{
			if (JPath::isOwner($fname) && !JPath::setPermissions($fname, '0444'))
			{
				JError::raiseNotice('SOME_ERROR_CODE', 'Could not make ' . $fname . ' unwritable');
			}
		}
		return true;
	}
}
?>