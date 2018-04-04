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

$factory = JPATH_ADMINISTRATOR.'/components/com_acesearch/library/factory.php';
if (!file_exists($factory)) {
    return;
}

require_once($factory);
$AcesearchConfig = AcesearchFactory::getConfig();

$document =& JFactory::getDocument();

$document->addStyleSheet('../components/com_acesearch/assets/css/acesearch.css');

if (isset($AcesearchConfig->admin_enable_complete) && $AcesearchConfig->admin_enable_complete == '1') {
    $document->addScript('../components/com_acesearch/assets/js/autocompleter.js');
}

$q = JRequest::getString('query');
$focus = JText::_('MOD_ACESEARCH_ADMIN_SEARCH').'...';
if(!empty($q)){
	$focus = $q;
}
?>

<script type="text/javascript">		
   window.addEvent('domready', function() {
		var url = '<?php echo JRoute::_('index.php?option=com_acesearch&controller=ajax&task=complete&format=raw', false); ?>';
		var completer = new Autocompleter.Ajax.Json($('qm'), url, {'postVar': 'q'});
	});	
</script>

<form action="index.php?option=com_acesearch&amp;controller=search&amp;task=view" method="post" >
	<div style="float:right;margin-top:4px;">
		<input  type="text" name="query" id="qm" value="<?php echo $focus;?>" size="20" maxlength="255" style="width:160px"  onblur="if(this.value=='') this.value='<?php echo $focus; ?>';" onfocus="if(this.value='<?php echo $focus; ?>') this.value='';" />
		<input type="submit" value="<?php echo JText::_('MOD_ACESEARCH_ADMIN_SEARCH');?>"/>&nbsp;&nbsp;
	</div>
</form>