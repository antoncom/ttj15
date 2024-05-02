<?php
/**
 * Highslide JS Plugin
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.filesystem.file');
jimport( 'joomla.utilities.string');
jimport( 'joomla.language.helper');

/**
 * Highslide Content Plugin
 *
 */
class plgContentHighslide extends JPlugin
{

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param object $params  The object that holds the plugin parameters
	 * @since 1.5
	 */
	function plgContentHighslide( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}

	/**
	 * onPrepareContent
	 *
	 * Method is called by the view
	 *
	 * @param 	object		The article object.  Note $article->text is also available
	 * @param 	object		The article params
	 * @param 	int			The 'page' number (defaults to 0)
	 */
	function onPrepareContent( &$article, &$params, $limitstart=0 )
	{
		global $mainframe;

		jimport('joomla.environment.browser');
		$plugin			= &JPluginHelper::getPlugin('content', 'Highslide');
		$pluginParams	= new JParameter( $plugin->params );
		$document 		= &JFactory::getDocument();
        $menu           = &JSite::getMenu();
		$browser		= &JBrowser::getInstance();

		$hs_base    = JURI::root(true). '/plugins/content/highslide/';
		$lang		= $this->getLanguage( $pluginParams );

		if ($pluginParams->getValue('includehsconfig') == '0')
		{
			if (JString::stristr($article->text, 'class="highslide"') === false)
			{
				return;
			}
		}

		$inchighslide = $pluginParams->getValue('includehighslide');
		if ($inchighslide == '0')
		{
			$HS_JS_TYPE = 'highslide-full.js';
		}
		if ($inchighslide == '1')
		{
			$HS_JS_TYPE = 'highslide-full.packed.js';
		}

        if ($menu->getActive() == $menu->getDefault() && isset($HS_JS_TYPE))
        {
        	//	user is on the front-page
	        $headdata = $document->getHeadData();
	        $hsscript = $hs_base.$HS_JS_TYPE;
	        if (isset($headdata['scripts'][$hsscript]))
			{
				//	include hs configuation only once.
	        	return;
	        }
        }

		if (isset($article->id))
		{
			$id = $article->id;
			$fname = JPATH_ROOT . DS . 'hsconfig'.DS.'css'.DS.'highslide-article-' . $article->id . '-styles.css';
			if (!JFile::exists($fname))
			{
				$id = -1;
			}
		}
		else
		{
			$id = -1;
		}
		$fnameCss = $this->_createCssUrl( $id );
		$fnameJs = $this->_createJsUrl( $id );
		if ($inchighslide != '2')
		{
			$document->addStylesheet( $hs_base.'highslide.css');
			if ($browser->getBrowser() == "msie" && $browser->getMajor() <= 6)
			{
				$ie6css = JPATH_ROOT.DS.'plugins'.DS.'content'.DS.'highslide'.DS.'highslide-ie6.css';
				if (JFile::exists($ie6css))
				{
					$styledata = JFile::read($ie6css);
					$newtext = eregi_replace( "src=( )*'( )*([^' ]+)'", "src='" . JURI::root(true) . "\\3" . "'", $styledata );
					$document->addStyleDeclaration($newtext);
				}
			}
		}
		$document->addStylesheet( $fnameCss );
		if ($inchighslide != '2')
		{
			$document->addScript( $hs_base.$HS_JS_TYPE );
			$document->addScript( $hs_base.'easing_equations.js');
			$document->addScript( $hs_base.'swfobject.js');
		}
		if (isset($lang))
		{
			$document->addScript( $hs_base.'language/'.$lang.'.js');
		}
		else
		{
			if ($pluginParams->getValue('defaultlang') != -1)
			{
				$document->addScript( $hs_base.'language/'.$pluginParams->getValue('defaultlang').'.js');
			}
		}
		$document->addScript( $fnameJs);
		$document->addScriptDeclaration( "hs.graphicsDir = '".JURI::root(true)."/plugins/content/highslide/graphics/';");
	}

	/**
	 * Determine if the id given represents the site configuration
	 *
	 * @param mixed $id
	 * @return true if id is for the site configuration, otherwise false
	 */
	function _isSiteConfig( $id )
	{
		return ($id == -1);
	}

	/**
	 * create a CSS stylesheet name based upon the id given
	 *
	 * @param mixed $id
	 * @return full path file name
	 */
	function _createCssUrl( $id )
	{
		$fname = JURI::root(true).'/hsconfig/css/';

		if ($this->_isSiteConfig($id) )
		{
			$fname .=  'highslide-sitestyles.css';
		}
		else
		{
			$fname .= 'highslide-article-' . $id . '-styles.css';
		}
		return $fname;
	}

	/**
	 * create a Javascript file name based upon the id given
	 *
	 * @param mixed $id
	 * @return full path file name
	 */
	function _createJsUrl( $id )
	{
		$fname = JURI::root(true).'/hsconfig/js/';

		if ($this->_isSiteConfig($id) )
		{
			$fname .=  'highslide-sitesettings.js';
		}
		else
		{
			$fname .= 'highslide-article-' . $id . '-settings.js';
		}
		return $fname;
	}

	function getLanguage( $params )
	{
		if ($params->getValue('detectlang') == '1')
		{
			$dir = dirname( __FILE__ ).DS.'highslide'.DS.'language'.DS;
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				$browserLangs	= explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );

				foreach ($browserLangs as $browserLang)
				{
					// slice out the part before ; on first step, the part before - on second, place into array
					$browserLang = substr( $browserLang, 0, strcspn( $browserLang, ';' ) );
					$primary_browserLang = substr( $browserLang, 0, 2 );

					if (file_exists( $dir.$browserLang.'.js'))
					{
						return $browserLang;
					}
					if (file_exists( $dir.$primary_browserLang.'.js'))
					{
						return $primary_browserLang;
					}
				}
			}
		}
		return;
	}
}
?>