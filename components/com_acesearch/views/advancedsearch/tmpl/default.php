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

$uri = JFactory::getURI();
$component = $this->params->get('component', $uri->getVar('ext')); 
?>

<script type="text/javascript">
	function submitbutton(){
		var e = document.getElementById("e").value.length;
		var a = document.getElementById("a").value.length;
		var n = document.getElementById("n").value.length;
		
		if (e >= "<?php echo $this->AcesearchConfig->search_char; ?>" || a >= "<?php echo $this->AcesearchConfig->search_char; ?>") {
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
		var c1 = new Autocompleter.Ajax.Json($('e'), url, {'postVar': 'q'});
		var c2 = new Autocompleter.Ajax.Json($('a'), url, {'postVar': 'q'});
		var c3 = new Autocompleter.Ajax.Json($('n'), url, {'postVar': 'q'});
	});
	<?php } ?>
   
	function ChangeType(a){
		var acesearch_progress = $('acesearch_progress');
		new Ajax("<?php echo JRoute::_('index.php?option=com_acesearch&task=changeExtension&format=raw', false); ?>",
				{method: 'get',
				onRequest: function(){acesearch_progress.setStyle('visibility', 'visible');},
				onComplete: function(){acesearch_progress.setStyle('visibility', 'hidden');},
				update: $('custom_fields'), 
				data: 'ext='+a
				}).request();
	}
	
	function ajaxFunction(selected, data, html_field){
		new Ajax('<?php echo JRoute::_('index.php?option=com_acesearch&task=ajaxFunction&format=raw', false); ?>',
			{method: 'get',
			update: $(html_field), 
			data: data+'&selected='+selected
			}).request();
	}
</script>

<form id="adminForm" action="<?php echo JRoute::_(JFactory::getURI()->toString());?>" method="post" name="adminForm" onsubmit="return submitbutton();">
	<h1>
		<?php
		$page_title = $this->params->get('page_title', '');
		if (($this->params->get('show_page_title', '0') == '1') && !empty($page_title)) {
			echo $page_title;
		} 
		?>
	</h1>
	
	<fieldset class="acesearch_fieldset">
		<legend class="acesearch_legend"><?php echo JText::_('COM_ACESEARCH_SEARCH');?></legend>

		<div style="float:left;width:100%;">
			<span class="acesearch_span_label">
				<?php echo JText::_('COM_ACESEARCH_SEARCH_EXACT');?>
			</span>
			<span class="acesearch_span_field">
				<input class="acesearch_input" id="e" type="text" name="exact" value=""  maxlength="<?php echo $this->AcesearchConfig->max_search_char; ?>"/>
			</span>
		</div>
		
		<div style="float:left;width:100%;">
			<span class="acesearch_span_label">
				<?php echo JText::_('COM_ACESEARCH_SEARCH_ANY');?>
			</span>
			<span class="acesearch_span_field">
				<input class="acesearch_input" id="a" type="text" name="any" value="" maxlength="<?php echo $this->AcesearchConfig->max_search_char; ?>" />
			</span>
		</div>
		
		<div style="float:left;width:100%;">
			<span class="acesearch_span_label">
				<?php echo JText::_('COM_ACESEARCH_SEARCH_NONE');?>
			</span>
			<span class="acesearch_span_field">
				<input class="acesearch_input" id="n" type="text" name="none" value="" maxlength="<?php echo $this->AcesearchConfig->max_search_char; ?>" />
			</span>
		</div>
		
		<?php if (AcesearchUtility::getConfigState($this->params, 'show_ext_flt') && empty($component)) { ?>
		<div style="float:left;width:100%;">
			<span class="acesearch_span_label">
				<?php echo JText::_('COM_ACESEARCH_SEARCH_SECTION');?>
			</span>
			<span class="acesearch_span_field">
				<?php echo $this->lists['extension']; ?>
				&nbsp;<div id="acesearch_progress"></div>
			</span>
		</div>
		<?php } ?>
		
	</fieldset>
	
	<div class="acesearch_clear"></div>
	
	<?php 
	if (!empty($component)) {
		echo AcesearchSearch::getAdvancedSearch($component);
	}
	else {
		?>
		<div id="custom_fields"></div>
		<?php
	}
	?>
	
	<div class="clear" style="height:10px;width:100%;"></div>
	<button class="acesearch_button"><?php echo JText::_('COM_ACESEARCH_SEARCH' ); ?></button>
	
	<div class="acesearch_clear"></div>
	
	<input type="hidden" name="option" value="com_acesearch" />
	<input type="hidden" name="task" value="search" />
	
	<?php if (is_numeric($uri->getVar('filter'))) { ?>
		<input type="hidden" name="filter" value="<?php echo is_int($uri->getVar('filter')); ?>"/>
	<?php
	}
	
	if (!empty($component)) {
		?>
		<input type="hidden" name="ext" value="<?php echo $component; ?>"/>
		<?php
	}
	?>
</form>