<?php
/**
 * Main Plugin File
 * Does all the magic!
 *
 * @package    Modules Anywhere
 * @version    1.1.4
 * @since      File available since Release 1.0.0
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl/modulesanywhere
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Import library dependencies
jimport( 'joomla.event.plugin' );

/**
* Button Plugin that places Editor Buttons
*/
class plgButtonModulesAnywhere extends JPlugin
{
	/**
	* Constructor
	*
	* For php4 compatability we must not use the __constructor as a constructor for
	* plugins because func_get_args ( void ) returns a copy of all passed arguments
	* NOT references. This causes problems with cross-referencing necessary for the
	* observer design pattern.
	*/
	function plgButtonModulesAnywhere( &$subject, $config )
	{
		parent::__construct( $subject, $config );

		// Load plugin parameters
		$this->_params = new JParameter( $config['params'] );

		// Load plugin language
		$lang =& JFactory::getLanguage();
		$lang->load( 'plg_'.$config['type'].'_'.$config['name'], JPATH_ADMINISTRATOR );
	}

	/**
	* Display the button
	*
	* @return array A two element array of ( imageName, textToInsert )
	*/
	function onDisplay( $name )
	{
		global $mainframe;

		$button = new JObject();
		$_document =& JFactory::getDocument();

		$_enable_frontend = $this->_params->get( 'enable_frontend', 1 );

		if ( !$mainframe->isAdmin() && !$_enable_frontend ) {
			return $button;
		}

		JHTML::_( 'behavior.modal' );

		$_css = '
			.button2-left .modulesanywhere {
				background: transparent url('.JURI::root( true ).'/plugins/editors-xtd/modulesanywhere/images/button_right.png) no-repeat 100% 0px;
			}
			';
		$_document->addStyleDeclaration( $_css );

		$_link = 'plugins/editors-xtd/modulesanywhere/elements/modulesanywhere.page.php?name='.$name;
		if ( $mainframe->isAdmin() ) {
			$_link = '../'.$_link;
		}

		$button->set( 'modal', true );
		$button->set( 'link', $_link );
		$button->set( 'text', JText::_( 'Module' ) );
		$button->set( 'name', 'modulesanywhere' );
		$button->set( 'options', "{handler: 'iframe', size: {x: 700, y: 420}}" );

		return $button;
	}
}