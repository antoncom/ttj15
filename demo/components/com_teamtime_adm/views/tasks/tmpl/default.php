<?php
defined('_JEXEC') or die('Restricted access');

$user = & JFactory::getUser();
$colspan = 9;
?>

<form action="index.php" method="post" name="adminForm">
  <table>
    <tr>
      <td align="left" width="100%">
        <?php echo JText::_('Filter'); ?>:
        <input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
        <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
        <button id="adminForm_reset"><?php echo JText::_('Reset'); ?></button>
      </td>
      <td nowrap="nowrap">
        <?= $this->lists['select_state']; ?>&nbsp;
        <?= $this->lists['select_project']; ?>&nbsp;
        <?= $this->lists['select_type']; ?>&nbsp;
        <?= $this->lists['select_target'] ?>
      </td>
    </tr>
  </table>
  <div id="tablecell">
    <table class="adminlist">
      <thead>
        <tr>
          <th width="5">
            <?php echo JText::_('NUM'); ?>
          </th>
          <th width="20">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
          </th>
          <th  class="title">
            <?php echo JHTML::_('grid.sort', 'Name', 'a.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>
          <th  class="title">
            <?php echo JHTML::_('grid.sort', 'Type', 'type_name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>
          <th  class="title">
            <?php echo JHTML::_('grid.sort', 'Project', 'project_name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <? if ($this->lists['select_target'] != "") {
            $colspan++; ?>
            <th  class="title">
              <?php echo JHTML::_('grid.sort', 'Vector of goal', 'tv.title', @$this->lists['order_Dir'], @$this->lists['order']); ?>
            </th>
          <? } ?>

          <th  class="title">
            <?php echo JHTML::_('grid.sort', 'State', 'a.state', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>
          <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <? if (!in_array($user->usertype, array("Manager"))) {
            $colspan = 10; ?>
            <th width="1%" nowrap="nowrap">
              <?php echo JHTML::_('grid.sort', 'Rate', 'a.rate', @$this->lists['order_Dir'], @$this->lists['order']); ?>
            </th>
          <? } ?>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="<?= $colspan ?>">
            <?php echo $this->pagination->getListFooter(); ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <?php
        $k = 0;
        for ($i = 0, $n = count($this->items); $i < $n; $i++) {
          $row = &$this->items[$i];
          $link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&view=type&task=edit&cid[]=' . $row->id);
          $checked = JHTML::_('grid.id', $i, $row->id);
          ?>
          <tr class="<?php echo "row$k"; ?>">
            <td>
              <?php echo $this->pagination->getRowOffset($i); ?>
            </td>
            <td align="center">
              <?php echo $checked; ?>
            </td>
            <td>
              <span class="editlinktip hasTip" title="<?php echo JText::_('Edit Project'); ?>::<?php echo $row->name; ?>">
                <a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
              </span>
            </td>
            <td>
              <?php echo $row->type_name; ?>
            </td>
            <td>
              <?php echo $row->project_name; ?>
            </td>

            <? if ($this->lists['select_target'] != "") { ?>
              <td>
                <?= $row->target_title ?>
              </td>
            <? } ?>

            <td align="center">
              <?php
              echo JHTML::_('teamlog.taskstatelist', array(), "state" . $row->id, 'class="inputbox" onchange="submitStateChange(\'setState\', ' . $row->id . ');"', 'value', 'text', $row->state);
              ?>
            </td>
            <td align="center">
              <?php echo $row->id; ?>
            </td>

            <? if (!in_array($user->usertype, array("Manager"))) { ?>
              <td align="center">
                <?= $row->is_dotu_price ? $row->target_hourprice : $row->rate; ?>
              </td>
            <? } ?>
          </tr>
          <?php
          $k = 1 - $k;
        }
        ?>
      </tbody>
    </table>
  </div>
  <input type="hidden" id="state_change_id" name="state_change_id" value="" />
  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
  <?php echo JHTML::_('form.token'); ?>

</form>

<script language="javascript" type="text/javascript">
  function submitStateChange(pressbutton, id) {
    var form = document.adminForm;
    if (pressbutton == 'cancel') {
      submitform( pressbutton );
      return;
    }

    $('state_change_id').setProperty('value',id);
    submitform( pressbutton );
  }
  
  jQuery(function($) {

    $('#adminForm_reset').click(function () {
      $('#search').val('');	
      
      $('#filter_state').val('');      
      $('#filter_project').val('');      
      $('#filter_type').val('');
      
      $('#filter_target_id').val('');
      
      $('#adminForm').submit();

    });

  });
</script>