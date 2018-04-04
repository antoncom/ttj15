<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

  <table>
    <tr>
      <td align="left" width="100%">
        <?php echo JText::_('Filter'); ?>:
        <input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
        <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
        <button id="adminForm_reset"><?php echo JText::_('Reset'); ?></button>
      </td>

      <td nowrap="nowrap">
        <?= JText::_('Show goals') ?>&nbsp;<?php echo $this->lists['select_goalsonly']; ?>
        <?= JText::_('Max Levels') ?>&nbsp;<?php echo $this->lists['levellist']; ?>
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
            <?php echo JHTML::_('grid.sort', 'Goal', 'a.title', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th width="8%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'Order by', 'a.ordering', @$this->lists['order_Dir'], @$this->lists['order']); ?>
            <?php if ($this->ordering)
              echo JHTML::_('grid.order', $this->items); ?>
          </th>

          <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'Value', 'a.num', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'Hourly rate', 'a.hourprice', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>          
        </tr>
      </thead>

      <tfoot>
        <tr>
          <td colspan="7">
            <?php echo $this->pagination->getListFooter(); ?>
          </td>
        </tr>
      </tfoot>

      <tbody>
        <?php
        $k = 0;
        $rows = &$this->items;
        for ($i = 0, $n = count($this->items); $i < $n; $i++) {
          $tree_row = &$this->items[$i];
          $row = &$this->items[$i]->data;
          $link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&cid[]=' . $row->id);
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
              <? if ($tree_row->children) { ?>
                <span class="editlinktip hasTip item_parent" 
                      title="<?php //echo JText::_('Edit Todo');                 ?>::<?php //echo $row->description;                  ?>">
                        <?= $tree_row->treename ?>
                  <a href="<?php echo $link ?>">[<?= $row->title; ?>]</a>
                </span>
              <? }
              else { ?>
                <span class="editlinktip hasTip" 
                      title="<?php //echo JText::_('Edit Todo');                 ?>::<?php //echo $row->description;                  ?>">
                        <?= $tree_row->treename ?>
                  <a href="<?php echo $link ?>"><?= $row->title; ?></a>
                </span>					
              <? } ?>
            </td>

            <td class="order" nowrap="nowrap">
              <span><?php echo $this->pagination->orderUpIcon($i, $row->parent == 0 || $row->parent == @$rows[$i - 1]->parent, 'orderup', 'Move Up', $this->ordering); ?></span>
              <span><?php echo $this->pagination->orderDownIcon($i, $n, $row->parent == 0 || $row->parent == @$rows[$i + 1]->parent, 'orderdown', 'Move Down', $this->ordering); ?></span>
              <?php $disabled = $this->ordering ? '' : 'disabled="disabled"'; ?>
              <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
            </td>

            <td align="center" nowrap>
              <?= $row->num ?>              
            </td>			

            <td align="center" nowrap>
              <?= round($row->hourprice, 2) ?>              
            </td>

            <td align="center">
              <?php echo $row->id; ?>
            </td>
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

<script type="text/javascript">
	
  jQuery(function($) {

    $('#adminForm_reset').click(function() {
      $('#search').val('');	      
      $('#adminForm').submit();
    });

  });

</script>