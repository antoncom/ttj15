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

class AcesearchViewAcesearch extends AcesearchView {

	function display($tpl = null){
		JToolBarHelper::title(JText::_('COM_ACESEARCH_COMMON_PANEL'),'acesearch');
		$this->toolbar->appendButton('Popup', 'cache', JText::_('COM_ACESEARCH_COMMON_CLEAN_CACHE'), 'index.php?option=com_acesearch&amp;controller=purge&amp;task=cache&amp;tmpl=component', 300, 250);
		JToolBarHelper::divider();
		$this->toolbar->appendButton('Popup', 'help1', JText::_('Help'), 'http://www.joomace.net/support/docs/acesearch/user-manual/control-panel?tmpl=component', 650, 500);

		jimport('joomla.html.pane');
		$pane =& JPane::getInstance('sliders');
		
		$this->assignRef('pane'		, $pane);
		$this->assignRef('info'		, $this->get('Info'));
		$this->assignRef('stats'	, $this->get('Stats'));
		
		parent::display($tpl);
	}
	
	function quickIconButton($link, $image, $text, $modal = 0, $x = 500, $y = 450, $new_window = false) {
		// Initialise variables
		$lang = & JFactory::getLanguage();
		
		$new_window	= ($new_window) ? ' target="_blank"' : '';
  		?>

		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<?php
				if ($modal) {
					JHTML::_('behavior.modal');
					
					if (!strpos($link, 'tmpl=component')) {
						$link .= '&amp;tmpl=component';
					}
				?>
					<a href="<?php echo $link; ?>" style="cursor:pointer" class="modal" rel="{handler: 'iframe', size: {x: <?php echo $x; ?>, y: <?php echo $y; ?>}}"<?php echo $new_window; ?>>
				<?php
				} else {
				?>
					<a href="<?php echo $link; ?>"<?php echo $new_window; ?>>
				<?php
				}
					echo JHTML::_('image', 'administrator/components/com_acesearch/assets/images/'.$image, $text );
				?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}
}