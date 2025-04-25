<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2010 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

// Set defines
define('ACESEARCH_PACK', 'basic');
define('JPATH_ACESEARCH', JPATH_ROOT . '/components/com_acesearch');
define('JPATH_ACESEARCH_ADMIN', JPATH_ROOT . '/administrator/components/com_acesearch');

$library = dirname(__FILE__);

JLoader::register('AcesearchCache', $library.'/cache.php');
JLoader::register('AcesearchExtension', $library.'/extension.php');
JLoader::register('AcesearchHTML', $library.'/html.php');
JLoader::register('AcesearchFactory', $library.'/factory.php');
JLoader::register('AcesearchSearch', $library.'/search.php');
JLoader::register('AcesearchUtility', $library.'/utility.php');

if (!class_exists('AceDatabase')) {
	JLoader::register('AceDatabase', $library.'/database.php');
}

if (JFactory::getApplication()->isAdmin()) {
	JLoader::register('AcesearchController', $library.'/controller.php');
	JLoader::register('AcesearchModel', $library.'/model.php');
	JLoader::register('AcesearchView', $library.'/view.php');
}