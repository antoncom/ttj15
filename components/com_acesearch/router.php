<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

//No Permision
defined( '_JEXEC' ) or die( 'Restricted access' );

function AcesearchBuildRoute(&$query){

	$segments = array();

	if (isset($query['view'])) {
		require_once(JPATH_ADMINISTRATOR . '/components/com_acesearch/library/utility.php');
		$Itemid = AcesearchUtility::getItemid();
		
		if (empty($Itemid) || $query['view'] == 'advancedsearch') {
			$sef = new AcesearchSefPrefix();
			
			if ($sef->addPrefix()) {
				$segments[] = 'acesearch';
			}
			
			$segments[] = $query['view'];
		}
		
		unset($query['view']);
	}

	return $segments;
}

function AcesearchParseRoute($segments) {
	$vars = array();

	if (!empty($segments[0])) {
		$sef = new AcesearchSefPrefix();
		
		if ($sef->addPrefix()) {
			$vars['view'] = $segments[1];
		}
		else {
			$vars['view'] = $segments[0];
		}
	}
	else {
		$vars['view'] = 'search';
	}

	return $vars;
}

class AcesearchSefPrefix {

	function addPrefix() {
		$acesef = JPATH_ADMINISTRATOR . '/components/com_acesef/acesef.xml';
		if (!file_exists($acesef)) {
			return false;
		}
		
		return self::acesef();
	}

	function acesef() {
		require_once(JPATH_ADMINISTRATOR . '/components/com_acesef/library/loader.php');
		
		$this->AcesefConfig = AcesefFactory::getConfig();
		$cache = AcesefFactory::getCache();
		
		$params = $cache->getExtensionParams('com_acesearch');
		
		if (($params->get('prefix', '') != '')) {
			return false;
		}
		
		return true;
	}
}