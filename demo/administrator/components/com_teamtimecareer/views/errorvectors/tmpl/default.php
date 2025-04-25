<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

  <table>
    <tr>
      <td align="left" width="100%">
				<?php echo $this->lists['select_user']; ?>
        <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
        <button id="adminForm_reset"><?php echo JText::_('Reset'); ?></button>
      </td>

      <td nowrap="nowrap">
				<?= JText::_('Show targets') ?>&nbsp;<?= $this->lists["select_showtargets"] ?>
      </td>
    </tr>
  </table>

  <div id="tablecell" class="errorvectortable">

		<?= $this->errorvector_content ?>

  </div>

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_('form.token'); ?>

</form>

<script type="text/javascript">

  TeamTime.jQuery(function($) {

    $('#adminForm_reset').click(function() {
      $('#filter_user').val('');
      $('#adminForm').submit();
    });

  });

</script>