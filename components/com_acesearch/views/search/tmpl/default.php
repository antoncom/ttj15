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

$query = AcesearchSearch::getQuery();

?>

<script type="text/javascript">	

	function submitbutton(){
		var query = document.getElementById("q").value.length;
		if (query >= "<?php echo $this->AcesearchConfig->search_char; ?>") {
			return true;
		} 
		else {
			alert("<?php echo JText::_('COM_ACESEARCH_QUERY_ERROR'). ' ' .$this->AcesearchConfig->search_char. ' ' .JText::_('COM_ACESEARCH_QUERY_ERROR_CHARS');?>");
			return false;
		}
	}
	
	<?php if ($this->AcesearchConfig->enable_complete == '1') { ?>
	window.addEvent('domready', function() {
		var url = '<?php echo JRoute::_('index.php?option=com_acesearch&task=complete&format=raw', false); ?>';
		var completer = new Autocompleter.Ajax.Json($('q'), url, {'postVar': 'q'});
	});
	<?php } ?>
</script>

<form id="adminForm"  name="adminForm" action="<?php echo JRoute::_(JFactory::getURI()->toString()); ?>" method="post" onsubmit="return submitbutton();" >
	<h1>
		<?php
		$page_title = $this->params->get('page_title', '');
		if (($this->params->get('show_page_title', '0') == '1') && !empty($page_title)) {
			echo $page_title;
		} 
		?>
	</h1>		
	<fieldset class="acesearch_fieldset">
		<legend class="acesearch_legend"><?php echo JText::_('COM_ACESEARCH_SEARCH'); ?></legend>
		<div>
			<input class="acesearch_input" type="text" name="query" id="q" value="<?php echo $query; ?>" maxlength="<?php echo $this->AcesearchConfig->max_search_char; ?>" />&nbsp;
			
			<?php
			$filter = JRequest::getInt('filter');
			if (!empty($filter)) {
				echo $this->lists['filter'];
			}
			elseif ($this->AcesearchConfig->show_ext_flt == '1') {
				echo $this->lists['extension'];									
			}
			?>
			
			<button class="acesearch_button"><?php echo JText::_('COM_ACESEARCH_SEARCH' ); ?></button>
			
			<?php 
			if ($this->AcesearchConfig->show_adv_search == '1') {
				$Itemid = '';
				
				$i_id = JRequest::getInt('Itemid', 0, 'get');
				if (!empty($i_id)) {
					$Itemid = '&Itemid='.$i_id;
				}
				
				?>
				<a style="font-size:12px;" href="<?php echo JRoute::_('index.php?option=com_acesearch&view=advancedsearch'.$this->lists['adf'].$Itemid); ?>" title="<?php echo JText::_('COM_ACESEARCH_SEARCH_ADVANCED_SEARCH'); ?>" >
				<?php echo JText::_('COM_ACESEARCH_SEARCH_ADVANCED_SEARCH'); ?>
				</a>
			<?php } ?>
		</div>
	</fieldset>
	
	<div class="acesearch_clear"></div>
	
	<?php
	if (!empty($query)) {
		if ($this->AcesearchConfig->results_format == '1') {
			echo $this->loadTemplate('results');
		}
		else {
			echo $this->loadTemplate('results_table');
		}
	}
	?>
	<input type="hidden" name="limitstart" value="" />
	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="task" value="search" />
	<input type="hidden" name="filter" value="<?php echo JRequest::getCmd('filter', ''); ?>"/>
</form>

<div class="acesearch_clear"></div>