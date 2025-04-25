<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	Quick Icons
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

function getAcesearchIcon($link, $image, $text) {
	$mainframe =& JFactory::getApplication();
	$img_path 	= '/components/com_acesearch/assets/images/';
	$lang		=& JFactory::getLanguage();
	?>
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="<?php echo $link; ?>">
				<?php echo JHtml::_('image.site',  $image, $img_path, NULL, NULL, $text); ?>
				<span><?php echo $text; ?></span>
			</a>
		</div>
	</div>
	<?php
}

function getAcesearchVersion() {
	$factory_file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'library'.DS.'factory.php';
	$utility_file = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'library'.DS.'utility.php';
	$cache_file   = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'library'.DS.'cache.php';
	
	if (!file_exists($factory_file) || !file_exists($utility_file)) {
		return 0;
	}
	
	require_once($factory_file);
	require_once($utility_file);
	require_once($cache_file);
	
	$utility = new AcesearchUtility();
	$cache   = new AcesearchCache($lifetime = '315360000');
	
	$installed = $utility->getXmlText(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'acesearch.xml', 'version');
	$version_info = $cache->getRemoteInfo();
	$latest = $version_info['acesearch'];
	
	$version = version_compare($installed, $latest);
	
	return $version;
}

?>

<div id="cpanel">
	<?php
	if ($params->get('acesearch_version', '1') == '1') {
		$link = 'index.php?option=com_acesearch&amp;controller=upgrade&amp;task=view';
		$version = getAcesearchVersion();
		if ($version != 0) {
			getAcesearchIcon($link, 'icon-48-version-up.png', JText::_('MOD_UPGRADE_AVAILABLE'));
		} else {
			getAcesearchIcon($link, 'icon-48-version-ok.png', JText::_('MOD_UP-TO-DATE'));
		}
	}
	
	if ($params->get('acesearch_configuration', '0') == '1') {
		$link = 'index.php?option=com_acesearch&amp;controller=config&amp;task=edit';
		getAcesearchIcon($link, 'icon-48-configuration.png', JText::_('MOD_ACESEARCH_CONFIG'));
	}
	
	if ($params->get('acesearch_extensions', '0') == '1') {
		$link = 'index.php?option=com_acesearch&amp;controller=extensions&amp;task=view';
		getAcesearchIcon($link, 'icon-48-extensions.png', JText::_('MOD_ACESEARCH_EXT'));
	}
	
	if ($params->get('acesearch_search', '1') == '1') {
		$link = 'index.php?option=com_acesearch&amp;controller=search&amp;task=view';
		getAcesearchIcon($link, 'icon-48-search.png', JText::_('MOD_ACESEARCH_SEARCH'));
	}
	
	if ($params->get('acesearch_filter', '0') == '1') {
		$link = 'index.php?option=com_acesearch&amp;controller=filters&amp;task=view';
		getAcesearchIcon($link, 'icon-48-filters.png', JText::_('MOD_ACESEARCH_FILTERS'));
	}
	
	if ($params->get('acesearch_statistics', '0') == '1') {
		$link = 'index.php?option=com_acesearch&amp;controller=statistics&amp;task=view';
		getAcesearchIcon($link, 'icon-48-statistics.png', JText::_('MOD_ACESEARCH_STATISTICS'));
	}
	
	?>
</div>