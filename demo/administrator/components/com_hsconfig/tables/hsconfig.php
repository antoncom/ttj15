<?php
/**
 * Highslide Configuration table class
 *
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


/**
 * HsConfig Table class
 */
class TableHsConfig extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	/**
	 * @var string
	 */
	var $css = null;

	/**
	 * @var string
	 */
	var $overlayhtml = null;

	/**
	 * @var string
	 */
	var $skincontrols = null;

	/**
	 * @var string
	 */
	var $skincontent = null;

	/**
	 * @var text
	 */
	var $params = null;

	/**
	* @var published
	*/
	var $published = null;

	/**
	* @var publish_up
	*/
	var $publish_tmst = null;

	/**
	* @var modified
	*/
	var $modified = null;


	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	function TableHsConfig(& $db) {
		parent::__construct('#__hsconfig', 'id', $db);
	}

	/**
	* Overloaded bind function
	*
	* @access public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/
	function bind($array, $ignore = '')
	{
		if (isset( $array['params'] ) && is_array($array['params']))
		{
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
		$array['skincontrols'] = JRequest::getVar('skincontrols', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$array['skincontent'] = JRequest::getVar('skincontent', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$array['overlayhtml'] = JRequest::getVar('overlayhtml', '', 'post', 'string', JREQUEST_ALLOWRAW);

		return parent::bind($array, $ignore);
	}
}
?>