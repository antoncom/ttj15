<?php
/**
* @version		1.5.0
* @package		AceSearch
* @subpackage	AceSearch
* @copyright	2009-2011 JoomAce LLC, www.joomace.net
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
  $.noConflict();
  // Code that uses other library's $ can follow here.
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<?php
	echo $this->pane->startPane('pane');
	echo $this->pane->startPanel(JText::_('COM_ACESEARCH_COMMON_MAIN'), 'maintab');
	?>
	<fieldset class="adminform">
		<legend><?php echo JText::_('AceSearch'); ?></legend>
		<table class="admintable">
			<tr>
				<td class="key2">
					<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_VERSION_CHECKER'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_VERSION_CHECKER_HELP'); ?>">
						<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_VERSION_CHECKER'); ?>
					</span>
				</td>
				<td>
					<?php echo $this->lists['version_checker'];?>
				</td>
			</tr>
			<tr>
				<td class="key2">
					<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_SHOW_DB_ERRORS'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_SHOW_DB_ERRORS_HELP'); ?>">
						<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_SHOW_DB_ERRORS'); ?>
					</span>
				</td>
				<td>
					<?php echo $this->lists['show_db_errors'];?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACESEARCH_CONFIG_LEGEND_UPGRADE'); ?></legend>
		<table class="admintable">
			<tr>
				<td class="key2">
					<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_UPGRADE_ID'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_UPGRADE_ID_HELP'); ?>">
						<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_UPGRADE_ID'); ?>
					</span>
				</td>
				<td>
					<input type="password" name="download_id" id="download_id" value="<?php echo $this->AcesearchConfig->download_id;?>" style="width:300px"/>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_ACESEARCH_CONFIG_LEGEND_CACHE_PERMANENT'); ?></legend>
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key2">
						<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_VERSION'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_VERSION_HELP'); ?>">
							<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_VERSION'); ?>
						</span>
					</td>
					<td>
						<?php echo $this->lists['cache_versions']; ?>
					</td>
				</tr>
				<tr>
					<td class="key2">
						<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_PARAMS'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_PARAMS_HELP'); ?>">
							<?php echo JText::_('COM_ACESEARCH_CONFIG_MAIN_CACHE_PARAMS'); ?>
						</span>
					</td>
					<td>
						<?php echo $this->lists['cache_extensions']; ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('COM_ACESEARCH_COMMON_FRONTEND'), 'frontendtab');
	?>
	<table class="noshow">
		<tr>
			<td width="50%">
				<fieldset class="adminform">
					<legend><?php echo JText::_('COM_ACESEARCH_CONFIG_LEGEND_SEARCH'); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_EXT_FILTER'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_EXT_FILTER_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_EXT_FILTER'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['show_ext_flt'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_AUTOCOMPLETER'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_AUTOCOMPLETER_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_AUTOCOMPLETER'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['enable_complete'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_SUGGESTION'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_SUGGESTION_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_SUGGESTION'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['enable_suggestion'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_HIGHTLIGHT'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_HIGHTLIGHT_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_HIGHTLIGHT'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['enable_highlight'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SAVE_KEYWORDS'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SAVE_KEYWORDS_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SAVE_KEYWORDS'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['save_results'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_ADV_SEARCH'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_ADV_SEARCH_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_ADV_SEARCH'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['show_adv_search'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SEARCH_CHAR'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SEARCH_CHAR_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SEARCH_CHAR'); ?>
								</span>
							</td>
							<td>
								<input type="text" name="search_char" value="<?php echo $this->AcesearchConfig->search_char;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_LIMIT'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_LIMIT_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_LIMIT'); ?>
								</span>
							</td>
							<td>
								<input type="text" name="result_limit" value="<?php echo $this->AcesearchConfig->result_limit; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_MAX_SEARCH'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_MAX_SEARCH_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_MAX_SEARCH'); ?>
								</span>
							</td>
							<td>
								<input type="text" name="max_search_char" value="<?php echo $this->AcesearchConfig->max_search_char; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_BLACKLIST'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_BLACKLIST_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_BLACKLIST'); ?>
								</span>
							</td>
							<td>
								<textarea name="blacklist" cols="40" rows="8"><?php echo $this->AcesearchConfig->blacklist;?></textarea>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td width="%50">
				<fieldset class="adminform">
					<legend><?php echo JText::_('COM_ACESEARCH_SEARCH_RESULTS'); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULTS_FORMAT'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULTS_FORMAT_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULTS_FORMAT'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['results_format'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ACCESS_CHECKER'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ACCESS_CHECKER_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ACCESS_CHECKER'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['access_checker'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_URL'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_URL_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_URL'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['show_url'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_PROPERTIES'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_PROPERTIES_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_PROPERTIES'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['show_properties'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_TITLE_LNG'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_TITLE_LNG_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_TITLE_LNG'); ?>
								</span>
							</td>
							<td>
								<input type="text" name="title_length" id="title_length" value="<?php echo $this->AcesearchConfig->title_length;?>" style="width:100px"/>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DESC'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DESC_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DESC'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['show_desc'];?>&nbsp;&nbsp;&nbsp;<?php echo JText::_('COM_ACESEARCH_CONFIG_DESC_LNG'); ?>&nbsp;&nbsp;&nbsp;=>
								<input type="text" name="description_length" id="description_length" value="<?php echo $this->AcesearchConfig->description_length;?>" style="width:100px"/>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DISPLAY'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DISPLAY_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DISPLAY'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['show_display'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_DATE_FORMAT'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_DATE_FORMAT_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_DATE_FORMAT'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['date_format'];?>								
							</td>
						</tr>			
					</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('COM_ACESEARCH_COMMON_BACKEND'), 'backendtab');
	?>
	
	<table class="noshow">
		<tr>
			<td width="%50">
				<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ACESEARCH_CONFIG_LEGEND_SEARCH'); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_EXT_FILTER'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_EXT_FILTER_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_EXT_FILTER'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_show_ext_flt'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_AUTOCOMPLETER'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_AUTOCOMPLETER_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_AUTOCOMPLETER'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_enable_complete'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_SUGGESTION'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_SUGGESTION_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_SUGGESTION'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_enable_suggestion'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_HIGHTLIGHT'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_HIGHTLIGHT_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_ENABLE_HIGHTLIGHT'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_enable_highlight'];?>
							</td>
						</tr>
							<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_LIMIT'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_LIMIT_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_LIMIT'); ?>
								</span>
							</td>
							<td>
								<input type="text" name="admin_result_limit" value="<?php echo $this->AcesearchConfig->admin_result_limit;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_MAX_SEARCH'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_MAX_SEARCH_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_RESULT_MAX_SEARCH'); ?>
								</span>
							</td>
							<td>
								<input type="text" name="admin_max_search_char" value="<?php echo $this->AcesearchConfig->admin_max_search_char; ?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td width="50%">
				<fieldset class="adminForm">
				<legend><?php echo JText::_('COM_ACESEARCH_SEARCH_RESULTS'); ?></legend>
					<table class="admintable" valign="top">
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_URL'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_URL_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_URL'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_show_url'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_PROPERTIES'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_PROPERTIES_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_PROPERTIES'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_show_properties'];?>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_TITLE_LNG'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_TITLE_LNG_HELP'); ?>">
								<?php echo JText::_('COM_ACESEARCH_CONFIG_TITLE_LNG'); ?>
							</td>
							<td>
								<input type="text" name="admin_title_length" id="admin_title_length" value="<?php echo $this->AcesearchConfig->admin_title_length;?>" style="width:100px"/>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DESC'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DESC_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DESC'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_show_desc'];?>&nbsp;&nbsp;&nbsp;<?php echo JText::_('COM_ACESEARCH_CONFIG_DESC_LNG'); ?>&nbsp;&nbsp;&nbsp;=>
								<input type="text" name="admin_description_length" id="admin_description_length" value="<?php echo $this->AcesearchConfig->admin_description_length;?>" style="width:100px"/>
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DISPLAY'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DISPLAY_HELP'); ?>">
									<?php echo JText::_('COM_ACESEARCH_CONFIG_SHOW_DISPLAY'); ?>
								</span>
							</td>
							<td>
								<?php echo $this->lists['admin_show_display'];?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
	echo $this->pane->endPanel();
	echo $this->pane->startPanel(JText::_('COM_ACESEARCH_COMMON_STYLE'), 'css');
	?>
	
	<table class="noshow">
		<tr>
			<td width="%50">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ACESEARCH_COMMON_HIGHLIGH_TEXT'); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '1.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '1. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_text1" size="6" id="colorpickerField6" value="<?php echo $this->AcesearchConfig->highlight_text1;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '2.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '2. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_text2" size="6" id="colorpickerField7" value="<?php echo $this->AcesearchConfig->highlight_text2;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '3.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '3. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_text3" size="6" id="colorpickerField8" value="<?php echo $this->AcesearchConfig->highlight_text3;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '4.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '4. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_text4" size="6" id="colorpickerField9" value="<?php echo $this->AcesearchConfig->highlight_text4;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '5.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '5. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_text5" size="6" id="colorpickerField10" value="<?php echo $this->AcesearchConfig->highlight_text5;?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td width="%50">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ACESEARCH_COMMON_HIGHLIGH_BACK'); ?></legend>
					<table class="admintable">
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '1.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '1. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_back1" size="6" id="colorpickerField1" value="<?php echo $this->AcesearchConfig->highlight_back1;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '2.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '2. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_back2" size="6" id="colorpickerField2" value="<?php echo $this->AcesearchConfig->highlight_back2;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '3.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '3. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_back3" size="6" id="colorpickerField3" value="<?php echo $this->AcesearchConfig->highlight_back3;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '4.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '4. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_back4" size="6" id="colorpickerField4" value="<?php echo $this->AcesearchConfig->highlight_back4;?>" />
							</td>
						</tr>
						<tr>
							<td class="key2">
								<span class="hasTip" title="<?php echo '5.'.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>::<?php echo JText::_('COM_ACESEARCH_CONFIG_KEYWORD_HELP'); ?>">
									<?php echo '5. '.JText::_('COM_ACESEARCH_CONFIG_KEYWORD'); ?>
								</span>
							</td>
							<td>
								<input type="text" maxlength="6" name="highlight_back5" size="6" id="colorpickerField5" value="<?php echo $this->AcesearchConfig->highlight_back5;?>" />
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<?php
	echo $this->pane->endPanel();
	echo $this->pane->endPane();
	?>	
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="controller" value="config" />
	<input type="hidden" name="task" value="edit" />
	<?php echo JHTML::_('form.token'); ?>
</form>