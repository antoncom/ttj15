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
 ?>
 
<script language="javascript">
	function upgradeExt(url) {		
		document.adminForm.task.value = 'installUpgrade';
		document.adminForm.joomaceurl.value = url;
		document.adminForm.submit();
	}

	function noDownloadId() {
		var res = alert('No Download-ID, please enter your Download-ID into extension parameters');
		return res;
	}

	function apply() {
		var selection = document.getElementById('ext_selection').value;
		
		if (selection == 'selected' && document.adminForm.boxchecked.value == 0) {
			alert('Please make a selection from the list');
			return;
		}
		
		// Call the action
		document.adminForm.selection.value = selection;
		submitbutton(action);
	}

	function resetFilters() {
		document.adminForm.search_name.value = '';
		document.adminForm.filter_handler.value = '-1';
		document.adminForm.search_cname.value = '';
		document.adminForm.submit();
	}
</script>

<form enctype="multipart/form-data" action="index.php" method="post" name="adminForm_pck">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_INSTALL'); ?></legend>
		<table class="adminform">
			<tbody>
				<tr>
					<td width="80">
						<label for="install_package"><?php echo JText::_('COM_ACESEARCH_COMMON_SELECT_FILE'); ?>:</label>
					</td>
					<td>
						<input class="input_box" type="file" size="57" id="install_package" name="install_package" />
						<input class="button" type="submit" onclick="submitbutton()" value="<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_INSTALL_UPLOAD'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="controller" value="extensions" />
	<input type="hidden" name="task" value="installUpgrade" />
	<?php echo JHTML::_('form.token'); ?>
</form>

<br />

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm">
	<table class="adminlist">
	<thead>
		<tr>
			<th width="13">
				<?php echo JText::_('COM_ACESEARCH_COMMON_NUM'); ?>
			</th>
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
			</th>
			<th nowrap="nowrap">
				<?php echo JHTML::_('grid.sort', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_COMPONENT'), 'name', $this->lists['order_dir'], $this->lists['order']);?>
			</th>
			<th width="60" class="title">
				<?php echo  JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_VERSION'); ?>
			</th>
			<th width="90" class="title">
				<?php echo JText::_('COM_ACESEARCH_CPANEL_LATEST_VERSION'); ?>
			</th>
			<th width="80" class="title">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_LICENSE'); ?>
			</th>
			<th width="90" class="title">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS'); ?>
			</th>
			<th width="85">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_ACCESS'); ?>
			</th>
			<th width="130" class="title">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_HANDLER'); ?>
			</th>
			<th width="100" class="title">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_CUSTOM_NAME'); ?>
			</th>
			<th width="60" class="title">
				<?php echo JHTML::_('grid.sort', JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_ORDER'), 'ordering', $this->lists['order_dir'], $this->lists['order']); ?>
			</th>
			<th width="110" class="title">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_CILENT'); ?>
			</th>
			<th width="90" class="title">
				<?php echo JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_AUTHOR'); ?>
			</th>
		</tr>
		<tr>
			<th nowrap="nowrap" colspan="2">
				<?php echo $this->lists['reset_filters']; ?>
			</th>
			<th nowrap="nowrap">
				<?php echo $this->lists['search_name']; ?>
			</th>
			<th nowrap="nowrap">
				&nbsp;
			</th>
			<th nowrap="nowrap">
				&nbsp;
			</th>
			<th nowrap="nowrap">
				&nbsp;
			</th>
			<th nowrap="nowrap">
				&nbsp;
			</th>
			<th nowrap="nowrap">
				<?php echo $this->lists['access_list']; ?>
			</th>
			<th nowrap="nowrap">
				<?php echo $this->lists['handler_list']; ?>
			</th>
			<th nowrap="nowrap">
				<?php echo $this->lists['search_cname']; ?>
			</th>
			<th nowrap="nowrap">
				&nbsp;
			</th>
			<th nowrap="nowrap">
				<?php echo $this->lists['client']; ?>
			</th>
			<th>
				&nbsp;
			</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	$n = count($this->items);
	for ($i=0; $i < $n; $i++) {
		$row	= &$this->items[$i];
		$xml_file = JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$row->extension.'.xml';
		
		// Load parameters
		$params = new JParameter($row->params);
		
		// Version checker is disabled
		if (!isset($this->info)) {
			$license = JText::_('Disabled');
			$info->version = JText::_('Disabled');
		}
		elseif (!isset($this->info[$row->extension])) {
			$license = JText::_('Not Available');
			$info->version = JText::_('Not Available');
		}
		else {
			$info = $this->info[$row->extension];
			$license = 'Commercial';
			if (strpos($info->description, 'free') === 0) {
				$license = 'Free';
			}
		}
		
		// Name
		if (!empty($row->name)) {
			$edit_link = JRoute::_('index.php?option=com_acesearch&controller=extensions&task=edit&cid[]='.$row->id.'&amp;tmpl=component');
			$name = '<a href="'.$edit_link.'" style="cursor:pointer" class="modal" rel="{handler: \'iframe\', size: {x: 550, y: 500}}">'.$row->name.'</a>';
		} else {
			$name = $row->extension;
		}
		
		// Installed Version, author, author URL
		$installed_version = "-";
		$author = "-";
		if (file_exists($xml_file)) {
			$author_name 		= AcesearchUtility::getXmlText($xml_file, 'author');
			$author_url			= AcesearchUtility::getXmlText($xml_file, 'authorurl');
			$author 			= '<a href="http://'.$author_url.'" target= "_blank">'.$author_name.'</a>';
			$installed_version	= AcesearchUtility::getXmlText($xml_file, 'version');
		}
		
		// Latest version
		if ($info->version == JText::_('Disabled') || $info->version == JText::_('Not Available')) {
			$latest_version = $info->version;
		} else {
			$compared = version_compare($installed_version, $info->version);
			if ($compared == 0) {
				$latest_version = '<strong><font color="green">'.$info->version.'</font></strong>';
			} elseif($compared == -1) {
				$latest_version = '<a href="http://www.joomace.net" target="_blank"><b><font color="red">'.$info->version.'</font></b></a>';
			} else {
				$latest_version = '<a href="http://www.joomace.net" target="_blank"><b><font color="orange">'.$info->version.'</font></b></a>';
			}
		}
		
		// Status
		if ($info->version == JText::_('Disabled')) {
			$status = $info->version;
		} elseif ($info->version == JText::_('Not Available')) {
			$status = '<input type="button" class="button hasTip" value="'.JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_4').'" onclick="window.open(\'http://www.joomace.net/services/new-extension-request\');" title="'.JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_GET').'" />';
		} else {
			if (file_exists(JPATH_ACESEARCH_ADMIN.DS.'extensions'.DS.$row->extension.'.php')) {
				$compared = version_compare($installed_version, $info->version);
				if ($compared != -1) { // Up-to-date
					$status = JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_1');
				} else { // Upgrade needed
					$download_id = $params->get('download_id', '');
					if ($license == 'Free') {
						$url = $info->link."/download?method=upgrade";
						$func = "upgradeExt('".$url."');";
					} elseif (strlen($download_id) == 32) {
						$url = "do"."wnl"."oad"."s/dow"."nlo"."ad-r"."equ"."est?do"."wnl"."oa"."d_i"."d=".$download_id;
						$func = "upgradeExt('".$url."');";
					} else {
						$func = "return noDownloadId();";
					}
					$status = '<input type="button" class="button hasTip" value="'.JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_2').'" onclick="'.$func.'" title="'.JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_UPGRADE').'"/>';
				}
			} else {
				$status = '<input type="button" class="button hasTip" value="'.JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_3').'" onclick="window.open(\''. $info->link .'\');" title="'.JText::_('COM_ACESEARCH_EXTENSIONS_VIEW_STATUS_GET').'" />';
			}
		}
		
		// Handler
		$handler = JHTML::_('select.genericlist', AcesearchUtility::getHandlerList($row->extension), 'handler['.$row->id.']', 'class="inputbox" size="1"', 'value', 'text', $params->get('handler', '1'));

		// Custom Name
		$custom_name = $params->get('custom_name');
		
		$client = '-';
		if (file_exists($xml_file) || ($params->get('handler', '1') == '2')){
			if ($row->client == 0) {
				$client =  JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_FRONT');
			}
			
			if ($row->client == 1) {
				$client = JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_BACK');
			}
			
			if ($row->client == 2) {
				$client =  JText::_('COM_ACESEARCH_EXTENSIONS_CLIENT_BOTH');
			}
		}
		
		$row->access = $params->get('access', 0);
		$row->groupname = $this->groups[$row->access]->name;
		$access  = JHTML::_('grid.access', $row, $i);
		$checked = JHTML::_('grid.id', $i, $row->id);
		
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset($i); ?>
			</td>
			<td>
				<?php echo $checked; ?>
			</td>
			<td>
				<?php	echo $name;?>
				<input type="hidden" name="id[<?php echo $row->id; ?>]" value="<?php echo $row->id; ?>">
			</td>
			<td align="center">
				<?php 	echo $installed_version;?>
			</td>
			<td align="center">
				<?php echo $latest_version; ?>
			</td>
			<td align="center">
				<?php echo $license; ?>
			</td>
			<td align="center">
				<?php echo $status; ?>
			</td>
			<td align="center">
				<?php echo $access; ?>
			</td>
			<td align="center">
				<?php echo $handler; ?>
			</td>
			<td align="center">
				<input type="text" name="custom_name[<?php echo $row->id; ?>]" size="20" value="<?php echo $custom_name; ?>" />
			</td>
			<td class="order">
				<input type="text" name="order[<?php echo $row->id; ?>]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
			</td>
			<td align="center">
				<?php echo $client; ?>
			</td>
			<td align="center">
				<?php echo $author; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="13">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>

	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="controller" value="extensions" />
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
	