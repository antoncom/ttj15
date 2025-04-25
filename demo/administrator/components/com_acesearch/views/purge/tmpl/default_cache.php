<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL, http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');

?>
<form action="index.php?option=com_acesearch&amp;controller=purge&amp;task=cache&amp;tmpl=component" name="adminForm" method="post">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACESEARCH_COMMON_CACHE'); ?></legend>
		<table>
			<tr>
				<td width="5%" height="20">
					&nbsp;
				</td>
				<td width="75%" height="20">
					<label for="name">
						<b><?php echo JText::_('Type'); ?></b>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<b><?php echo JText::_('COM_ACESEARCH_COMMON_RECORDS'); ?></b>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_versions" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_VERSION'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['versions']; ?>
					</label>
				</td>
			</tr>
			<tr>
				<td width="5%" height="20">
					<input type="checkbox" name="cache_extensions" value="1" />
				</td>
				<td width="75%" height="20">
					<label for="name">
						<?php echo JText::_('COM_ACESEARCH_CPANEL_EXTENSIONS'); ?>
					</label>
				</td>
				<td width="20%" height="20">
					<label for="name">
						<?php echo $this->count['extensions']; ?>
					</label>
				</td>
			</tr>
		</table>
		<br/>
		<input type="submit" class="button" name="cleancache" onclick="window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close();', 1500);" value="<?php echo JText::_('COM_ACESEARCH_COMMON_CLEAN_CACHE'); ?>" />
	</fieldset>
	
	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="controller" value="purge" />
	<input type="hidden" name="task" value="cleancache" />
	<?php echo JHTML::_('form.token'); ?>
</form>
<br/>