<?php
/**
 * Main File
 *
 * @package    Modules Anywhere
 * @version    1.1.4
 * @since      File available since Release v1.0.0
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl/modulesanywhere
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// Ensure this file is being included by a parent file
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.event.plugin' );

/**
* Plugin that loads modules
*/
class plgSystemModulesAnywhere extends JPlugin
{
	/**
	* Constructor
	*
	* For php4 compatability we must not use the __constructor as a constructor for
	* plugins because func_get_args ( void ) returns a copy of all passed arguments
	* NOT references.  This causes problems with cross-referencing necessary for the
	* observer design pattern.
	*/
	function plgSystemModulesAnywhere( &$subject )
	{
		global $mainframe;

		if ( $mainframe->isAdmin() ) { return; }

		parent::__construct( $subject );

		// load plugin parameters
		$this->_MA_plugin = JPluginHelper::getPlugin( 'system', 'modulesanywhere' );
		$this->_MA_params = new JParameter( $this->_MA_plugin->params );
		$this->_MA_syntax = '{module';
		
		$_lang = & JFactory::getLanguage();
		// load plugin language file
		$_lang->load( 'plg_system_modulesanywherer', JPATH_ADMINISTRATOR );
	}

////////////////////////////////////////////////////////////////////
// ARTICLES
////////////////////////////////////////////////////////////////////

	function onPrepareContent( &$article )
	{
		global $mainframe;

		// return if current page is an administrator page
		if( $mainframe->isAdmin() ) { return; }

		$this->replaceInArticles( $article );
	}

////////////////////////////////////////////////////////////////////
// COMPONENTS
////////////////////////////////////////////////////////////////////

	function onAfterDispatch()
	{
		global $mainframe, $option;

		// return if current page is an administrator page
		if( $mainframe->isAdmin() ) { return; }

		$_document	=& JFactory::getDocument();
		$_docType = $_document->getType();

		if ( $_docType == 'feed' && isset( $_document->items ) ) {
			for ( $_i = 0; $_i < count( $_document->items ); $_i++ ) {
				$this->replaceInArticles( $_document->items[$_i] );
			}
		}

		if ( isset( $_document->_buffer ) ) {
			$_document->_buffer = $this->tagArea( $_document->_buffer, 'component' );
		}

		// PDF
		if ( $_docType == 'pdf' ) {
			if ( isset( $_document->_header ) ) {
				$this->replaceInTheRest( $_document->_header );
				$this->cleanLeftoverJunk( $_document->_header );
			}
			if ( isset( $_document->title ) ) {
				$this->replaceInTheRest( $_document->title );
				$this->cleanLeftoverJunk( $_document->title );
			}
			if ( isset( $_document->_buffer ) ) {
				$this->replaceInTheRest( $_document->_buffer );
				$this->cleanLeftoverJunk( $_document->_buffer );
			}
		}
	}

////////////////////////////////////////////////////////////////////
// OTHER AREAS
////////////////////////////////////////////////////////////////////
	function onAfterRender()
	{
		global $mainframe;

		// return if current page is an administrator page
		if( $mainframe->isAdmin() ) { return; }

		$_document	=& JFactory::getDocument();
		$_docType = $_document->getType();

		// not in pdf's
		if ( $_docType == 'pdf' ) { return; }

		$_html = JResponse::getBody();

		$this->protect( $_html );
		$this->replaceInTheRest( $_html );
		$this->unprotect( $_html );

		$this->cleanLeftoverJunk( $_html );

		JResponse::setBody( $_html );
	}

////////////////////////////////////////////////////////////////////
// FUNCTIONS
////////////////////////////////////////////////////////////////////
	function replaceInArticles ( &$article ) {
		$message = '';

		if ( isset( $article->created_by ) ) {
			// Lookup group level of creator
			$_acl =& JFactory::getACL();
			$_article_group	= $_acl->getAroGroup( $article->created_by );

			$_security_group		= $_acl->get_group_data( $this->_MA_params->get( 'articles_security_level', 23 ) );

			// Set if security is passed
			// passed = creator is equal or higher than security group level
			if ( $_security_group['4'] > $_article_group->lft ) {
				$message = JText::_( 'REMOVED, SECURITY' );
			}
		}

		if ( isset( $article->text ) ) {
			$this->processModules( $article->text, 'articles', $message );
		}
		if ( isset( $article->description ) ) {
			$this->processModules( $article->description, 'articles', $message );
		}
		if ( isset( $article->title ) ) {
			$this->processModules( $article->title, 'articles', $message );
		}
		if ( isset( $article->author ) ) {
			if ( isset( $article->author->name ) ) {
				$this->processModules( $article->author->name, 'articles', $message );
			} else {
				$this->processModules( $article->author, 'articles', $message );
			}
		}
	}

	function replaceInTheRest( &$str, $_docType = 'html' )
	{
		global $option;

		if ( $str == '' ) { return; }

		$_document	=& JFactory::getDocument();
		$_docType = $_document->getType();

		// COMPONENT
		if ( $_docType == 'feed' ) {
			$_search_regex = '#(<item[^>]*>.*</item>)#si';
			$str = preg_replace( $_search_regex, '<!-- START: MODA_COMPONENT -->\1<!-- END: MODA_COMPONENT -->', $str );
		}
		if ( strpos( $str, '<!-- START: MODA_COMPONENT -->' ) === false ) {
			$str = $this->tagArea( $str, 'component' );
		}

		$_components = $this->_MA_params->get( 'components', '' );
		if ( !is_array( $_components ) ) {
			$_components = explode( ',', $_components );
		}

		$message = '';
		if ( in_array( $option, $_components ) ) {
			// For all components that are selected, set the meassage
			$message = JText::_( 'REMOVED, NOT ENABLED' );
		}

		$_components = $this->getTagArea( $str, 'component' );

		foreach ( $_components as $_component ) {
			$this->processModules( $_component[1], 'components', $message );
			$str = str_replace( $_component[0], $_component[1], $str );
		}

		// EVERYWHERE
		$this->processModules( $str, 'other' );
	}

	function tagArea( $str, $area = '' )
	{
		if ( $area ) {
			if ( is_array( $str ) ) {
				foreach ( $str as $_key => $_val ) {
					$str[ $_key ] = $this->tagArea( $_val, $area );
				}
			} else if ( $str ) {
				$str = '<!-- START: MODA_'.strtoupper( $area ).' -->'.$str.'<!-- END: MODA_'.strtoupper( $area ).' -->';
			}
		}

		return $str;
	}
	function getTagArea( $str, $area = '' )
	{
		$matches = array( '', '' );

		if ( $str && $area ) {
			preg_match_all( '#<\!-- START: MODA_'.strtoupper( $area ).' -->(.*?)<\!-- END: MODA_'.strtoupper( $area ).' -->#s', $str, $matches, PREG_SET_ORDER );
		}

		return $matches;
	}

	function processModules( &$string, $area = 'articles', $message = '' )
	{
		jimport('joomla.application.module.helper');

		$_module_tag = $this->_MA_params->get( 'module_tag', 'module' );
		$_modulepos_tag = $this->_MA_params->get( 'modulepos_tag', 'modulepos' );
		$_tags = $_module_tag.'|'.$_modulepos_tag;
		if ( $this->_MA_params->get( 'handle_loadposition', 0 ) ) { $_tags .= '|loadposition'; }
		$_regex = '#\{\s*('.$_tags.')\s+([^\}]+?)((?:\|[^\}]+)?)\}#';
		if ( preg_match_all( $_regex, $string, $_matches, PREG_SET_ORDER ) > 0 ) {
			JPluginHelper::importPlugin( 'content' );

			$_params_style = $this->_MA_params->get( 'style', 'none' );
			$_params_override_style = $this->_MA_params->get( 'override_style', 1 );

			if (
				$area == 'articles' && !$this->_MA_params->get( 'articles_enable', 1 ) ||
				$area == 'components' && !$this->_MA_params->get( 'components_enable', 1 ) ||
				$area == 'other' && !$this->_MA_params->get( 'other_enable', 1 )
			) {
				$message = JText::_( 'REMOVED, NOT ENABLED' );
			}

			foreach ( $_matches as $_match ) {
				$_module_html = $_match['0'];
				$_type = trim( $_match['1'] );
				$_module = trim( $_match['2'] );
				$_style = trim( $_match['3'] );

				if ( $message != '' ) {
					$_module_html = '<!-- '.JText::_( 'Comment - Modules Anywhere' ).': '.$message.' -->';
				} else {

					if ( $_params_override_style && $_style ) {
						$_style = substr( $_style, 1 );
					} else {
						$_style = $_params_style;
					}

					if ( $_type == $_module_tag ) {
						// module
						$_module_html	= $this->processModule( $_module, $_style );
					} else {
						// module position
						$_module_html	= $this->processPosition( $_module, $_style );
					}
				}
				$string = str_replace( $_match['0'], $_module_html, $string );
			}
		}
	}
	function processPosition( $position, $style = 'none' )
	{
		$_document	= &JFactory::getDocument();
		$_renderer	= $_document->loadRenderer( 'module' );
		$_params	= array( 'style'=>$style );

		$html = '';
		foreach ( JModuleHelper::getModules( $position ) as $_mod ) {
			$html .= $_renderer->render( $_mod, $_params );
		}
		return $html;
	}

	function processModule( $module, $style = 'none' )
	{
		global $mainframe;

		$_db		=& JFactory::getDBO();
		$_user		=& JFactory::getUser();
		$_aid		= $_user->get( 'aid', 0 );

		$_where = ' AND ( m.title="'.$module.'"';
		if ( is_numeric( $module ) ) {
			$_where .= ' OR m.id='.$module;
		}
		$_where .=  ' ) ';

		$_query = 'SELECT *'.
			' FROM #__modules AS m'.
			' WHERE m.access <= '.(int) $_aid.
			' AND m.client_id = '.(int) $mainframe->getClientId().
			$_where.
			' ORDER BY ordering'.
			' LIMIT 1';

		$_db->setQuery( $_query );
		$_row = $_db->loadObject();

		$html = '';
		if ( $_row ) {
			//determine if this is a custom module
			$_row->user = ( substr( $_row->module, 0, 4 ) == 'mod_' ) ? 0 : 1;
			$_row->style = $style;

			$_attribs = array();
			$_attribs['style'] = $style;

			$html = JModuleHelper::renderModule( $_row, $_attribs );
		}
		return $html;
	}

		/*
	 * Protect input and text area's
	 */
	function protect( &$string )
	{
		global $mainframe, $option;
		$_task = JRequest::getCmd( 'task' );

		// Protect complete adminForm (to prevent Sourcerer messing stuff up when editing articles and such)
		$_regex = '#<form [^>]*name="adminForm".*?>.*?<div id="editor-xtd-buttons".*?</form>#si';
		if ( preg_match_all( $_regex, $string, $_matches, PREG_SET_ORDER ) > 0 ) {
			$_protected_syntax = $this->protectStr( $this->_MA_syntax );
			foreach ( $_matches as $_match ) {
				if ( !( strpos( $_match[0], $this->_MA_syntax ) === false ) ) {
					$_form_string = str_replace( $this->_MA_syntax, $_protected_syntax, $_match[0] );
					$string = str_replace( $_match[0], $_form_string, $string );
				}
			}
		}
	}

	function unprotect( &$string )
	{
		$_protected_start = $this->protectStr( $this->_MA_syntax );
		$string = str_replace( $_protected_start, $this->_MA_syntax, $string );
	}

	function protectStr( $string )
	{
		$string = base64_encode( $string );
		return $string;
	}

	function cleanLeftoverJunk( &$str )
	{
		$str = preg_replace( '#\<\!-- (START|END): MODA_[^>]* -->#', '', $str );
	}
}