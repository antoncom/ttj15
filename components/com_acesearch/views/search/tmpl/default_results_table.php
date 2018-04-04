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

$check = isset($this->results[0]) ? $this->results[0] : '';
?>

<fieldset class="acesearch_fieldset">
	<legend class="acesearch_legend"><?php echo JText::_('COM_ACESEARCH_SEARCH_RESULTS'); ?></legend>
	<?php
	if (!empty($this->results["suggest"])) { 
		echo $this->results["suggest"];
	}

	if (!empty($check)) {
		?>
		
		<div id="acesearch_pagination">
			<?php
			if ($this->AcesearchConfig->show_display == '1') {
				?>
				<div class="limitbox">
					&nbsp;<?php echo JText::_('Display'); ?>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<?php 
			} 
			?>
			<div class="pagination_top">
				<?php echo $this->pagination->getPagesLinks(); ?>&nbsp;
			</div>
		</div>
		<div class="clear" style="margin-bottom:10px;"></div>
		
		<div id="editcell">
			<table width="100%" cellpadding="4" cellspacing="0" border="0" align="center" class="contentpane">
				<tr>
					<td class="sectiontableheader" width="1%">
						<?php echo JText::_('Num'); ?>
					</td>
					<td class="sectiontableheader">
						<?php echo JText::_('COM_ACESEARCH_FIELDS_TITLE'); ?>
					</td>
					<td class="sectiontableheader" width="150px">
						<?php echo JText::_('COM_ACESEARCH_SEARCH_SECTION'); ?>
					</td>		
				</tr>
				<?php
				$k =0;
				$n = count($this->results);	
				
				for ($i = 0; $i < $n; $i++){
					$b = $k +1;
					$result = isset($this->results[$i]) ? $this->results[$i] : "";
					
					if (!empty($result)) {
						AcesearchSearch::finalizeResult($result);
						
						if (substr($result->link, 0, 4) == 'http') {
							$link = $route = $result->link;
						}
						else {
							$uri =& JFactory::getURI();
							$route = JRoute::_($result->link);
							$link = $uri->getScheme().'://'.$uri->getHost().$route;
						}
						?>
						<tr class="sectiontableentry<?php echo $b;?>">
							<td>
								<font size="2px" color="#6a6767"><?php echo $this->pagination->getRowOffset($i); ?>.</font>
							</td>
							<td width="60%">
								<font size="2px"><a href="<?php echo $route; ?>"><?php echo $result->name; ?></a></font>
							</td>
							<td width="20%">
								<font size="2px"><?php echo $this->getExtensionName($result->acesearch_ext); ?></font>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					}
				}
				?>
				<div class="clear" style="margin-bottom:10px;"></div>
			</table>
		</div>
		<div id="acesearch_pagination">
			<div class="pagination_bottom">
				<?php echo $this->pagination->getPagesLinks(); ?>&nbsp;
			</div>
		</div>
		<?php
	}
	
	if (empty($check)){
		?>
		<h2><?php echo JText::_('COM_ACESEARCH_SEARCH_NO_RESULTS'); ?></h2>
		<span><?php echo JText::_('COM_ACESEARCH_SEARCH_NO_RESULTS_QUERY'); ?><?php echo $q; ?></span>
		<?php
	}
	?>
</fieldset>

<input type="hidden" name="filter" value="<?php echo JRequest::getCmd('filter', ''); ?>"/>

<div class="clear" style="margin-bottom:10px;"></div>