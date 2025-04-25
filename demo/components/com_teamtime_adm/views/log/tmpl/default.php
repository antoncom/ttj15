<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
	JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

	jimport('joomla.html.pane');

	$editor =& JFactory::getEditor();
	$pane	=& JPane::getInstance('sliders');
	$format = JText::_( 'DATE_FORMAT_LC2' );
?>

<form action="index.php" method="post" name="adminForm">

		<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top">
				<fieldset class="adminform">
					<legend><?php echo JText::_('Details'); ?></legend>
					<table class="admintable">
						<tr>
							<td width="110" class="key">
								<label for="directory">
									<?php echo JText::_('User'); ?>:
								</label>
							</td>
							<td>
								<?php echo $this->lists['select_user']; ?>
							</td>
						</tr>
						<tr>
							<td width="110" class="key">
								<label for="directory">
									<?php echo JText::_('Project'); ?>:
								</label>
							</td>
							<td>
								<?php echo $this->lists['select_project']; ?>
							</td>
						</tr>
						<tr>
							<td width="110" class="key">
								<label for="directory">
									<?php echo JText::_('Task'); ?>:
								</label>
							</td>
							<td>
								<span id="form-add-task-span">
									<?php echo $this->lists['select_task']; ?>
								</span>
							</td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('Description'); ?></legend>
					<table class="admintable">
						<tr>
							<td valign="top" colspan="3">
								<?php
								// parameters : areaname, content, width, height, cols, rows, show xtd buttons
								echo $editor->display('description', $this->item->description, '550', '300', '60', '20', array()) ;
								?>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td valign="top" width="320" style="padding: 7px 0 0 5px">
				<?php
					$db =& JFactory::getDBO();

					$create_date 	= null;
					$nullDate 		= $db->getNullDate();
				?>
				<table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
					<?php
					if ( $this->item->id ) {
					?>
					<tr>
						<td>
							<strong><?php echo JText::_( 'Log ID' ); ?>:</strong>
						</td>
						<td>
							<?php echo $this->item->id; ?>
						</td>
					</tr>
					<?php
					}
					?>
					<tr>
						<td>
							<strong><?php echo JText::_( 'Created' ); ?></strong>
						</td>
						<td>
							<?php
							if ( $this->item->created == $nullDate ) {
								echo JText::_( 'New item' );
							} else {
								echo JHTML::_('date',  $this->item->created,  $format );
							}
							?>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_( 'Modified' ); ?></strong>
						</td>
						<td>
							<?php
								if ( $this->item->modified == $nullDate ) {
									echo JText::_( 'Not modified' );
								} else {
									echo JHTML::_('date',  $this->item->modified, $format);
								}
							?>
						</td>
					</tr>
				</table>
			<?php
				// Create the form
				$form = new JParameter('', JPATH_COMPONENT.DS.'models'.DS.'log.xml');

				// Details Group
				$format = JText::_('DATE_FORMAT_MYSQL');
				$form->set('created', JHTML::_('date', $this->item->created, $format));
				$form->set('date', JHTML::_('date', $this->item->date, $format));
				$form->set('duration', $this->item->duration);
				$form->set('money', $this->item->money);

				$title = JText::_( 'Parameters - Log' );
				echo $pane->startPane("content-pane");
				echo $pane->startPanel($title, "detail-page");
				echo $form->render('details');
				echo $pane->endPanel();
				echo $pane->endPane();
			?>
			</td>
		</tr>
	</table>
</div>

<div class="clr"></div>

<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
<?php echo JHTML::_('form.token'); ?>

</form>

<script language="javascript" type="text/javascript">
function getTasks(url) {
	var project_id = $('project_id').getProperty('value');

	url = url + '&project_id=' + project_id;

	new Ajax(url, {
		method: 'get',
		update: $('form-add-task-span')
	}).request();

};
</script>