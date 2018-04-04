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

?>

<script language="javascript" type="text/javascript">
	function upgrade() {	    
	    document.adminForm.controller.value = 'upgrade';
		document.adminForm.submit();
	}
</script>

<form action="index.php?option=com_acesearch" method="post" name="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<th>		
				<?php
					if(strlen($this->AcesearchConfig->download_id) != 32 && (ACESEARCH_PACK == 'plus' || ACESEARCH_PACK == 'pro')){
						JError::raiseWarning('100', JText::sprintf('COM_ACESEARCH_CPANEL_STATUS_NOTE_DOWNLOAD_ID', '<a href="index.php?option=com_acesearch&controller=config&task=edit">', '</a>'));
					}
				?>	
			</th>
		</tr>
		<tr>
			<td valign="top" width="58%">
				<table class="adminlist">
					<tr>
						<td>
							<div id="cpanel" width="30%">
							<?php
							$option = JRequest::getWord('option');
							
							$link = 'index.php?option='.$option.'&amp;controller=config&amp;task=edit';
							$this->quickIconButton($link, 'icon-48-configuration.png', JText::_('COM_ACESEARCH_CPANEL_CONFIGURATION'));

							$link = 'index.php?option='.$option.'&controller=extensions&task=view';
							$this->quickIconButton($link, 'icon-48-extensions.png', JText::_('COM_ACESEARCH_CPANEL_EXTENSIONS'));
							
							$link = 'index.php?option='.$option.'&amp;controller=upgrade&amp;task=view';
							$this->quickIconButton($link, 'icon-48-upgrade.png', JText::_('COM_ACESEARCH_CPANEL_UPGRADE'));
							?>
							
							<br /><br /><br /><br /><br /><br /><br /><br />
							
							<?php
							$link = 'index.php?option='.$option.'&amp;controller=filters&amp;task=view';
							$this->quickIconButton($link, 'icon-48-filters.png', JText::_('COM_ACESEARCH_CPANEL_FILTERS'));
							
							$link = 'index.php?option='.$option.'&amp;controller=statistics&amp;task=view';
							$this->quickIconButton($link, 'icon-48-statistics.png', JText::_('COM_ACESEARCH_CPANEL_STATISTICS'));
							
							$link = 'index.php?option='.$option.'&amp;controller=search&amp;task=view';
							$this->quickIconButton($link, 'icon-48-search.png', JText::_('COM_ACESEARCH_CPANEL_SEARCH'));
							
							$link = 'index.php?option='.$option.'&amp;controller=css&amp;task=view';
							$this->quickIconButton($link, 'icon-48-css.png', JText::_('CSS'));
							?>
							
							<br /><br /><br /><br /><br /><br /><br /><br />
							
							<?php
							$link = 'index.php?option='.$option.'&amp;controller=support&amp;task=support';
							$this->quickIconButton($link, 'icon-48-support.png', JText::_('COM_ACESEARCH_CPANEL_SUPPORT'), true, 650, 420);
							
							$link = 'index.php?option='.$option.'&amp;controller=support&amp;task=translators';
							$this->quickIconButton($link, 'icon-48-translators.png', JText::_('COM_ACESEARCH_CPANEL_TRANSLATORS'), true);
							
							$link = 'http://www.joomace.net/joomla-extensions/acesearch/changelog?tmpl=component';
							$this->quickIconButton($link, 'icon-48-changelog.png', JText::_('COM_ACESEARCH_CPANEL_CHANGELOG'), true);
							
							$link = 'http://www.joomace.net';
							$this->quickIconButton($link, 'icon-48-joomace.png', 'JoomAce.net', false, 0, 0, true);
							
							?>
							</div>
						</td>
					</tr>
				</table>
			</td>
		
			<td valign="top" width="42%" style="padding: 7px 0 0 5px">
				<?php
				echo $this->pane->startPane('stat-pane');
				echo $this->pane->startPanel(JText::_('COM_ACESEARCH_CPANEL_WELLCOME'), 'wellcome');
				?>
				<table class="adminlist">
					<tr>
						<td valign="top" width="50%" align="center">
							<table class="adminlist">
								<?php
									$rowspan = 5;
									$download_id = ((ACESEARCH_PACK == 'plus' || ACESEARCH_PACK == 'pro') && strlen($this->AcesearchConfig->download_id) != 32); 
									if ($download_id) {
										$rowspan = 6;
									}
								?>
								<tr height="70">
									<td width="%25">
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo JHTML::_('image', 'administrator/templates/khepri/images/header/icon-48-info.png', null);
											} elseif ($this->info['version_status'] == 0) {
												echo JHTML::_('image', 'administrator/templates/khepri/images/header/icon-48-checkin.png', null);
											} elseif($this->info['version_status'] == -1) {
												echo JHTML::_('image', 'administrator/templates/khepri/images/header/icon-48-help_header.png', null);
											} else {
												echo JHTML::_('image', 'administrator/templates/khepri/images/header/icon-48-help_header.png', null);
											}
										?>
									</td>
									<td width="%35">
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo '<b>'.JText::_('COM_ACESEARCH_CPANEL_VERSION_CHECKER_DISABLED_1').'</b>';
											} elseif ($this->info['version_status'] == 0) {
												echo '<b><font color="green">'.JText::_('COM_ACESEARCH_CPANEL_LATEST_VERSION_INSTALLED').'</font></b>';
											} elseif($this->info['version_status'] == -1) {
												echo '<b><font color="red">'.JText::_('COM_ACESEARCH_CPANEL_OLD_VERSION').'</font></b>';
											} else {
												echo '<b><font color="orange">'.JText::_('COM_ACESEARCH_CPANEL_NEWER_VERSION').'</font></b>';
											}
										?>
									</td>
									<td align="center" rowspan="<?php echo $rowspan; ?>">
										<a href="http://www.joomace.net/joomla-extensions/acesearch" target="_blank">
										<img src="components/com_acesearch/assets/images/logo.png" width="149" height="222" alt="AceSearch" title="AceSearch" align="middle" border="0">
										</a>
									</td>
								</tr>
								<?php if ($download_id) { ?>
								<tr height="40">
									<td>
										<?php echo '<b><font color="red">'.JText::_('COM_ACESEARCH_CONFIG_MAIN_UPGRADE_ID').'</font></b>';?>
									</td>
									<td>
										<input type="text" name="download_id" id="download_id" class="inputbox" size="18" />
										&nbsp;
										<input type="button" onclick="javascript: submitbutton('saveDownloadID')" value="<?php echo JText::_('Save'); ?>" />
									</td>
								</tr>
								<?php } ?>
								<tr height="40">
									<td>
										<?php
											if($this->info['version_status'] == 0 || $this->info['version_enabled'] == 0) {
												echo JText::_('COM_ACESEARCH_CPANEL_LATEST_VERSION');
											} elseif($this->info['version_status'] == -1) {
												echo '<b><font color="red">'.JText::_('COM_ACESEARCH_CPANEL_LATEST_VERSION').'</font></b>';
											} else {
												echo '<b><font color="orange">'.JText::_('COM_ACESEARCH_CPANEL_LATEST_VERSION').'</font></b>';
											}
										?>
									</td>
									<td>
										<?php
											if ($this->info['version_enabled'] == 0) {
												echo JText::_('COM_ACESEARCH_CPANEL_VERSION_CHECKER_DISABLED_2');
											} elseif($this->info['version_status'] == 0) {
												echo $this->info['version_latest'];
											} elseif($this->info['version_status'] == -1) {
												// Version output in red
												echo '<b><font color="red">'.$this->info['version_latest'].'</font></b>&nbsp;&nbsp;&nbsp;&nbsp;';
												?>
												<input type="button" class="button hasTip" value="<?php echo JText::_('COM_ACESEARCH_CPANEL_UPGRADE'); ?>" onclick="upgrade();" />
												<?php
											} else {
												echo '<b><font color="orange">'.$this->info['version_latest'].'</font></b>';
											}
										?>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo JText::_('COM_ACESEARCH_CPANEL_INSTALLED_VERSION'); ?>
									</td>
									<td>
										<?php 
											if ($this->info['version_enabled'] == 0) {
												echo JText::_('COM_ACESEARCH_CPANEL_VERSION_CHECKER_DISABLED_2');
											} else {
												echo $this->info['version_installed'];
											}
										?>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo JText::_('COM_ACESEARCH_CPANEL_COPYRIGHT'); ?>
									</td>
									<td>
										<a href="http://www.joomace.net" target="_blank"><?php echo AcesearchUtility::getXmlText(JPATH_ACESEARCH_ADMIN.DS.'acesearch.xml', 'copyright'); ?></a>
									</td>
								</tr>
								<tr height="40">
									<td>
										<?php echo JText::_('COM_ACESEARCH_CPANEL_LICENSE'); ?>
									</td>
									<td>
										<?php 
											$license = AcesearchUtility::getXmlText(JPATH_ACESEARCH_ADMIN.DS.'acesearch.xml', 'license');
											list($l_name, $l_link) = explode(', ', $license);
										?>
										<a href="http://www.joomace.net/company/license-policy" target="_blank"><?php echo $l_name; ?></a>
									</td>
								</tr>
							</table>
						</td>		
					</tr>
				</table>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->startPanel(JText::_('COM_ACESEARCH_CPANEL_STATISTICS'), 'stats');
				?>
				<table class="adminlist">
					<tr>
						<td width="25%">
							<?php echo JText::_('COM_ACESEARCH_CPANEL_EXTENSIONS'); ?>
						</td>
						<td width="75%">
							<b><?php echo $this->stats['extensions']; ?></b>
						</td>
					</tr>
					<tr>
						<td width="25%">	
							<?php echo JText::_('COM_ACESEARCH_CPANEL_KEYWORD'); ?>
						</td>
						<td width="75%">
							<b><?php echo $this->stats['statistics'];?></b>
						</td>
					</tr>
					<tr>
						<td width="25%">	
							<?php echo JText::_('COM_ACESEARCH_CPANEL_FILTERS'); ?>
						</td>
						<td width="75%">
							<b><?php echo $this->stats['filters'];?></b>
						</td>
					</tr>
				</table>
				<?php
				echo $this->pane->endPanel();
				echo $this->pane->endPane();
				?>
			</td>
		</tr>
	</table>
	
	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="controller" value="acesearch"/>
	<input type="hidden" name="task" value="view" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>