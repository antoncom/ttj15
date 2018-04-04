<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

class TableAcesearchFilters extends JTable {

	var $id 		= null;
	var $title		= null;
	var $published 	= null;
	var $author		= null;
	var $extension  = null;
	var $category 	= null;
	var $date 		= null;
	
	function __construct(& $db) {
		parent::__construct('#__acesearch_filters', 'id', $db);
	}
}