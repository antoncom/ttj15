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

$check = isset($this->results[0]) ? $this->results[0] : "";
?>

<fieldset class="acesearch_fieldset">
	<legend class="acesearch_legend"><?php echo JText::_('COM_ACESEARCH_SEARCH_RESULTS'); ?></legend>
	<?php
	if (!empty($this->results["suggest"])) { 
		echo $this->results["suggest"];
	}
	
	if (!empty($check)) {
		?>
		
		<span class="about"><?php echo JText::_('COM_ACESEARCH_SEARCH_TOTAL_RESULTS').'&nbsp;'.$this->total.'&nbsp;'.JText::_('COM_ACESEARCH_SEARCH_RESULTS_FOUND'); ?> </span>
	
		<div class="clear" style="margin-bottom:10px;"></div>
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
	
		<div id="dotttt"></div>
	
		<div class="acesearch_clear"></div>
		
		<?php
		$n = count($this->results);
		
		for ($i = 0; $i < $n; $i++){
			$result = isset($this->results[$i]) ? $this->results[$i] : "";
			
			if (!empty($result)){
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
				<div id="dotttt"></div>
				
				<div>
					<font size="3px" color="#6a6767"><?php echo $this->pagination->getRowOffset($i); ?>.</font>
					<font size="3px"><a href="<?php echo $route; ?>"><?php echo $result->name; ?></a></font>
				</div>
				
				<?php
				if ($this->AcesearchConfig->show_desc) {
					?>
					<div>
						<?php echo $result->description; ?>
					</div>
					<?php				
				}
				
				if (!empty($result->properties)) {
					?>
					<div>
						<font color="#6a6767">
							<?php echo $result->properties;	?>
						</font>
					</div>
					<?php
				}
			
				if ($this->AcesearchConfig->show_url) {
					?>
					<div>
						<a href="<?php echo $link; ?>">
							<font color="green"><?php echo $link; ?></font>
						</a>
					</div>
					<?php
				}
			}
		}
		?>
		
		<div class="clear" style="margin-bottom:10px;"></div>
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
		<span><?php echo JText::_('COM_ACESEARCH_SEARCH_NO_RESULTS_QUERY'); ?><?php echo $this->getSearchQuery(); ?></span>
		<?php
	}
	?>
</fieldset>

<input type="hidden" name="filter" value="<?php echo JRequest::getCmd('filter', ''); ?>"/>

<div class="acesearch_clear"></div>