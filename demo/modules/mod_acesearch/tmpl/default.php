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
<script type="text/javascript">
	window.onload = function(){
		var module_progress = $('module_progress');
		new Ajax("<?php echo JRoute::_('index.php?option=com_acesearch&task=changeExtensionMod&module=1&format=raw', false); ?>",
				{method: 'get',
				onRequest: function(){module_progress.setStyle('visibility', 'visible');},
				onComplete: function(){module_progress.setStyle('visibility', 'hidden');},
				update: $('custom_fields_module'), 
				data: 'ext=<?php echo JRequest::getCmd('ext', '', 'post'); ?>&category=<?php echo JRequest::getInt('category', '', 'post'); ?>'
				}).request();
	} 
	
	<?php if ($params->get('enable_complete', '0') == '1') { ?>
	window.addEvent('domready', function() {
		var url = '<?php echo JRoute::_('index.php?option=com_acesearch&task=complete&module=1&format=raw', false); ?>';
		var completer = new Autocompleter.Ajax.Json($('qr'), url, {'postVar': 'q'});
	});
	<?php } ?>
	
	
	
	function changeExtModule(a){
		var module_progress = $('module_progress');
		new Ajax("<?php echo JRoute::_('index.php?option=com_acesearch&task=changeExtensionMod&module=1&format=raw', false); ?>",
				{method: 'get',
				onRequest: function(){module_progress.setStyle('visibility', 'visible');},
				onComplete: function(){module_progress.setStyle('visibility', 'hidden');},
				update: $('custom_fields_module'), 
				data: 'ext='+a
				}).request();
	}
	
	function searchsubmit(){
		var moquery = document.getElementById("qr").value.length;
		
		if (moquery >= "<?php echo $this->AcesearchConfig->search_char; ?>"  ) {
			return true;
		} 
		else {
			alert("<?php echo JText::_('MOD_ACESEARCH_QUERY_ERROR'). ' ' .$this->AcesearchConfig->search_char. ' ' .JText::_('MOD_ACESEARCH_QUERY_ERROR_CHARS');?>");
			return false;
		}
	}
	
</script>

<form id="acesearchModule" action="index.php" method="post" name="acesearchModule" onsubmit="return searchsubmit();">
	<div class="search<?php echo $params->get('moduleclass_sfx', ''); ?>">
		<input type="hidden" name="option" value="com_acesearch"/>
		<input type="hidden" name="view" value="search"/>
		<input type="hidden" name="task" value="search"/>
		<input type="hidden" name="ext" value=""/>
		
		<?php
		echo $output;
	
		if (!empty($filter_id)) { 
			$filter = AcesearchCache::getFilter($filter_id);
		
			echo AcesearchSearch::getAdvancedSearch($filter->extension, true);
			
			if ($params->get('show_button', '1') == '1') {
			?>
			<div class="clear" style="margin-bottom:10px;"></div>
			
			<button class="button" style="height:28px; float:left;"><?php echo JText::_('COM_ACESEARCH_SEARCH'); ?></button>
			<?php } ?>
			
			<div class="acesearch_clear"></div>
			<input type="hidden" name="filter" value="<?php echo $filter_id; ?>" />
			<input type="hidden" name="ext" value="<?php echo $filter->extension; ?>"/>
			<input type="hidden" name="category" value="<?php echo $filter->category; ?>"/>
			<input type="hidden" name="usr" value="<?php echo $filter->author; ?>"/><?php
		}
		elseif ($params->get('show_ext_flt', '0') == '1') {
			$lists = AcesearchSearch::getExtensionList('0','-1','_module');	?>
			
			<div style="float:left;">
				<span class="acesearch_span_label_module">
					<?php echo JText::_('MOD_ACESEARCH_SEARCH_SECTION'); ?>
				</span>
				<span class="acesearch_span_field_module">
					<?php echo $lists['extension']; ?>
					&nbsp;<div id="module_progress"></div>
				</span>
			</div>
			
			<div id="custom_fields_module"></div>
			
			<?php if ($params->get('show_button', '1') == '1') { ?>
			<div class="acesearch_clear"></div>
			<button class="<?php echo $params->get('button_class', 'acesearch_button');?>" id="module_button" style="height:28px; float:left;"><?php echo JText::_('COM_ACESEARCH_SEARCH'); ?></button>
			<?php } ?>
			
			<div class="acesearch_clear"></div>
			<?php 
		}
		elseif ($params->get('show_button', '1') == '1') {
			?>
			<button class="<?php echo $params->get('button_class', 'acesearch_button');?>" id="module_button"><?php echo JText::_('COM_ACESEARCH_SEARCH'); ?></button>
			<?php
		}
		
		if (!empty($Itemid)) { ?>
		<input type="hidden" name="mod_itemid" value="<?php echo $Itemid;?>"/>
		<?php }  ?>
	</div>
</form>
<div class="acesearch_clear"></div>