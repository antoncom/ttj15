<?php
/**
 * Highslide Configuration Model
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

/**
 * HsConfig Model
 */
class HsConfigModelHsConfig extends JModel
{
	/**
	 * Gets the id
	 * @return string The id to be displayed to the user
	 */
	function getId()
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT id FROM #__hsconfig';
		$db->setQuery( $query );
		$id = $db->loadResult();

		return $id;
	}
}
