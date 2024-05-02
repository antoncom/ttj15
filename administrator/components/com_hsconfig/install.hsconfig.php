<?php
/**
 * Highslide JS Configuration database install
 *
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class HsInstallHelper
{
	function getParams( $element )
	{
		if (!is_a($element, 'JSimpleXMLElement') || !count($element->children())) {
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return null;
		}

		// Get the array of parameter nodes to process
		$params = $element->children();
		if (count($params) == 0) {
			// No params to process
			return null;
		}

		// Process each parameter in the $params array.
		$ini = null;
		foreach ($params as $param) {
			if (!$name = $param->attributes('name')) {
				continue;
			}

			if (!$value = $param->attributes('default')) {
				continue;
			}

			$ini .= $name."=".$value."\n";
		}
		return $ini;
	}
}

global $mainframe;
$db =& JFactory::getDBO();
jimport('joomla.filesystem.file');

$query = "CREATE TABLE IF NOT EXISTS `#__hsconfig` (
	`id` int(11) NOT NULL,
	`css` text NOT NULL default '',
	`overlayhtml` text NOT NULL default '',
	`skincontrols` text NOT NULL default '',
	`skincontent` text NOT NULL default '',
	`params` text NOT NULL,
	`published` bool NOT NULL default 0,
	`modified` datetime NOT NULL default '0000-00-00 00:00:00',
	`publish_tmst` datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY  (`id`)
		) TYPE=MyISAM CHARACTER SET `utf8`";
$db->setQuery( $query );

if( !$db->query() )
{
 	$mainframe->enqueueMessage( JText::_('Unable to create Highslide Configuration Table!'), 'alert' );
 	$mainframe->enqueueMessage( $db->getErrorMsg(), 'alert' );
}
else
{
	$query = "ALTER TABLE `#__hsconfig` ADD COLUMN `skincontrols` text NOT NULL default '' AFTER `overlayhtml`";
	$db->setQuery( $query );
	$db->query();
	$query = "ALTER TABLE `#__hsconfig` ADD COLUMN `skincontent` text NOT NULL default '' AFTER `skincontrols`";
	$db->setQuery( $query );
	$db->query();

	$query = "SELECT id FROM `#__hsconfig` WHERE id = -1";
	$db->setQuery( $query );
	$id = $db->loadResult();
	if ($id === null)
	{
		$datenow =& JFactory::getDate();
		$mySqlDate = $datenow->toFormat();
		$filename = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_hsconfig'.DS.'css'.DS.'default.css';
		if (JFile::exists($filename))
		{
			$css = JFile::read($filename);
		}
		$installer =& JInstaller::getInstance();
		$manifest =&$installer->getManifest();
		$root =& $manifest->document;
		$elements = $root->children();
		$params = "";
		foreach ($elements as $element )
		{
			if ($element->_name == 'params')
			{
				$params .= HsInstallHelper::getParams( $element );
			}
		}
//		$params = $installer->getParams();
		$query = "INSERT INTO `#__hsconfig` ( `id`, `css`, `overlayhtml`, `skincontrols`, `skincontent`, `params`, `published`, `modified`, `publish_tmst`) VALUES
			(-1, '". $db->getEscaped($css) ."', '', '', '', '". $db->getEscaped($params) ."', 0, '" . $db->getEscaped($mySqlDate) . "', '0000-00-00 00:00:00')";
		$db->setQuery( $query );
		if (!$db->query())
		{
			$mainframe->enqueueMessage( JText::_('Unable to update Highslide Configuration Table!'), 'alert' );
			$mainframe->enqueueMessage( $db->getErrorMsg(), 'alert' );
		}
		else
		{
			$mainframe->enqueueMessage( JText::_('Default site configuration added to database.'));
		}
	}
}
?>