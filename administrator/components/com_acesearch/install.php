<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	Installer
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * 
 * This is the special installer addon based on the one created by Andrew Eddie and the team of JXtended.
 * We thank for this cool idea of extending the installation process easily
 */

// No Permission
defined('_JEXEC') or die('Restricted Access');

// Import Libraries
jimport('joomla.filesystem.file');

$status = new JObject();
$status->adapter = array();
$status->extensions = array();
$status->modules = array();
$status->plugins = array();

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* EXTENSION INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$extensions = &$this->manifest->getElementByPath('extensions');
if (is_a($extensions, 'JSimpleXMLElement') && count($extensions->children())) {
	foreach ($extensions->children() as $extension) {
		$option	= $extension->attributes('option');
		
		$file = $this->parent->getPath('source').'/admin/extensions/'.$option.'.xml';
		if (!file_exists($file)) {
			continue;
		}
		
		$manifest = $this->parent->_isManifest($file);
		
		if (is_null($manifest)) {
			continue;
		}
		
		$root =& $manifest->document;
		
		$ename = $root->getElementByPath('name');
		$ename = JFilterInput::clean($ename->data(), 'string');
		
		$db =& JFactory::getDBO();
		$db->setQuery('SELECT id FROM #__acesearch_extensions WHERE extension = '.$db->Quote($option));
		$ext = $db->loadResult();
		
		if (empty($ext)) {
			$client = $root->getElementByPath('client')->data();

			$prm = array();
			$prm['handler'] = 'handler=1';
			$prm['custom_name'] = 'custom_name=';
			$prm['access'] = 'access=0';
			$prm['result_limit'] = 'result_limit=';
			
			$element = $root->getElementByPath('install/defaultparams');
			if (is_a($element, 'JSimpleXMLElement') && count($element->children())) {
				$defaultParams = $element->children();
				if (count($defaultParams) != 0) {
					foreach ($defaultParams as $param) {
						if ($param->name() != 'defaultparam') {
							continue;
						}
						
						$name = $param->attributes('name');
						$value = $param->attributes('value');
						
						$prm[$name] = $name.'='.$value;
					}
				}
			}
			
			$params = implode("\n", $prm);
			
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_acesearch/tables');
			$row =& JTable::getInstance('AcesearchExtensions', 'Table');	
			$row->name 			= $ename;
			$row->extension 	= $option;
			$row->params 		= $params;
			$row->client 		= $client;
			$row->store();
		}
		
		$status->extensions[] = array('name' => $ename);
	}
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* MODULE INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/

$modules = &$this->manifest->getElementByPath('modules');
if (is_a($modules, 'JSimpleXMLElement') && count($modules->children())) {

	foreach ($modules->children() as $module) {
		$mtitle		= $module->attributes('title');
		$mname		= $module->attributes('module');
		$mclient	= JApplicationHelper::getClientInfo($module->attributes('client'), true);

		// Set the installation path
		if (!empty ($mname)) {
			$this->parent->setPath('extension_root', $mclient->path.DS.'modules'.DS.$mname);
		} else {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('No module file specified'));
			return false;
		}

		/*
		* If the module directory already exists, then we will assume that the
		* module is already installed or another module is using that directory.
		*/
		if (file_exists($this->parent->getPath('extension_root'))&&!$this->parent->getOverwrite()) {
			$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Another module is already using directory').': "'.$this->parent->getPath('extension_root').'"');
			return false;
		}

		// If the module directory does not exist, lets create it
		$created = false;
		if (!file_exists($this->parent->getPath('extension_root'))) {
			if (!$created = JFolder::create($this->parent->getPath('extension_root'))) {
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.JText::_('Failed to create directory').': "'.$this->parent->getPath('extension_root').'"');
				return false;
			}
		}

		/*
		* Since we created the module directory and will want to remove it if
		* we have to roll back the installation, lets add it to the
		* installation step stack
		*/
		if ($created) {
			$this->parent->pushStep(array ('type' => 'folder', 'path' => $this->parent->getPath('extension_root')));
		}

		// Copy all necessary files
		$element = &$module->getElementByPath('files');
		if ($this->parent->parseFiles($element, -1) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy language files
		$element = &$module->getElementByPath('languages');
		if ($this->parent->parseLanguages($element, $mclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		// Copy media files
		$element = &$module->getElementByPath('media');
		if ($this->parent->parseMedia($element, $mclient->id) === false) {
			// Install failed, roll back changes
			$this->parent->abort();
			return false;
		}

		$mtitle		= $module->attributes('title');
		$mposition	= $module->attributes('position');
		$morder		= $module->attributes('order');
		$mparams  =  'menu='.$module->attributes('menu');

		if ($mtitle && $mposition) {
			// if module already installed do not create a new instance
			$db =& JFactory::getDBO();
			$query = 'SELECT `id` FROM `#__modules` WHERE module = '.$db->Quote( $mname);
			$db->setQuery($query);
			if (!$db->Query()) {
				// Install failed, roll back changes
				$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
				return false;
			}
			$id = $db->loadResult();

			if (!$id){
				$row = & JTable::getInstance('module');
				$row->title		= $mtitle;
				$row->ordering	= $morder;
				$row->position	= $mposition;
				$row->showtitle	= 0;
				$row->iscore	= 0;
				$row->access	= ($mclient->id) == 1 ? 2 : 0;
				$row->client_id	= $mclient->id;
				$row->module	= $mname;
				$row->published	= 1;
				$row->params	= $mparams;

				if (!$row->store()) {
					// Install failed, roll back changes
					$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
					return false;
				}
				
				// Make visible evertywhere if site module
				if ($mclient->id==0){
					$query = 'REPLACE INTO `#__modules_menu` (moduleid,menuid) values ('.$db->Quote( $row->id).',0)';
					$db->setQuery($query);
					if (!$db->query()) {
						// Install failed, roll back changes
						$this->parent->abort(JText::_('Module').' '.JText::_('Install').': '.$db->stderr(true));
						return false;
					}
				}
			}
		}

		$status->modules[] = array('name' => $mtitle, 'client' => $mclient->name);
	}
}

/***********************************************************************************************
* ---------------------------------------------------------------------------------------------
* ADAPTER INSTALLATION SECTION
* ---------------------------------------------------------------------------------------------
***********************************************************************************************/
$adp_src = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_acesearch'.DS.'adapters'.DS.'acesearch_ext.php';
$adp_dst = JPATH_LIBRARIES.DS.'joomla'.DS.'installer'.DS.'adapters'.DS.'acesearch_ext.php';
if (is_writable(dirname($adp_dst))) {
	JFile::copy($adp_src, $adp_dst);
	$status->adapter[] = 1;
}

$rows = 0;
?>
<img src="components/com_acesearch/assets/images/logo.png" alt="Joomla! Search Component" width="60" height="89" align="left" />

<h2>AceSearch Installation</h2>
<h2><a href="index.php?option=com_acesearch">Go to AceSearch Control Panel</a></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'AceSearch '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php
if (count($status->adapter)) : ?>
		<tr class="row1">
			<td class="key" colspan="2"><?php echo 'AceSearch Adapter'; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php
endif;
if (count($status->extensions)) : ?>
		<tr>
			<th colspan="3"><?php echo JText::_('AceSearch Extension'); ?></th>
		</tr>
	<?php foreach ($status->extensions as $extension) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key" colspan="2"><?php echo $extension['name']; ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->modules)) : ?>
		<tr>
			<th><?php echo JText::_('Module'); ?></th>
			<th><?php echo JText::_('Client'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
if (count($status->plugins)) : ?>
		<tr>
			<th><?php echo JText::_('Plugin'); ?></th>
			<th><?php echo JText::_('Group'); ?></th>
			<th></th>
		</tr>
	<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	<?php endforeach;
endif;
 ?>

	</tbody>
</table>