<?php
defined('_JEXEC') or die('Restricted access');

$format = JText::_('DATE_FORMAT_LC2');
?>

<form action="index.php" method="post" name="adminForm">

  <table>
    <tr>
      <td align="left" width="100%">        
        <?php echo JText::_('Filter'); ?>:
        <input type="text" name="search" id="search"
               value="<?php echo $this->lists['search']; ?>"
               class="text_area" onchange="document.adminForm.submit();" />
        <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
        <button id="adminForm_reset"><?php echo JText::_('Reset'); ?></button>
        <p>
        <div class="select-date">
          <?= $this->lists['select_date'] ?>
          <?= JHTML::_(
              'calendar', $this->from_period, 'from_period', 'from-period', JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME')); ?>
          <?= JHTML::_(
              'calendar', $this->until_period, 'until_period', 'until-period', JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME')); ?>
        </div>
      </td>
      <td nowrap="nowrap">
        <?= $this->lists['select_user'] ?>
        <?= $this->lists['select_target'] ?>
        <?= $this->lists['select_type'] ?>
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
            <input type="checkbox" name="toggle" value=""
                   onclick="checkAll(<?php echo count($this->items); ?>);" />
          </th>

          <th  class="title" width="5%">
            <?php echo JHTML::_(
                'grid.sort', 'Date', 'a.date', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th  class="title" width="60%">
            <?php echo JHTML::_(
                'grid.sort', 'Experience', 'a.description', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th  class="title" width="20%">
            <?php echo JHTML::_(
                'grid.sort', 'Vector of goals', 'tv.title', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'Value', 'a.num', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th  class="title" width="20%">
            <?php echo JHTML::_(
                'grid.sort', 'User', 'u.name', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>

          <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_(
                'grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'], @$this->lists['order']); ?>
          </th>
        </tr>
      </thead>

      <tfoot>
        <tr>
          <td colspan="8">
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

          $exp_descr = strip_tags($row->description);
          if (mb_strlen($exp_descr) > 100) {
            $exp_descr = mb_substr($exp_descr, 0, 100) . "...";
          }
          ?>

          <tr class="<?php echo "row$k"; ?>">
            <td>
              <?php echo $this->pagination->getRowOffset($i); ?>
            </td>

            <td align="center">
              <?php echo $checked; ?>
            </td>

            <td align="center">
              <?php echo JHTML::_('date', $row->date, $format); ?>
            </td>

            <td align="left">
              <span class="editlinktip hasTip"
                    title="<?php echo JText::_('Edit item'); ?>::<?php echo $row->goal; ?>">
                <a href="<?php echo $link ?>"><?= $exp_descr ?></a>
              </span>              
            </td>

            <td align="center">
              <?php echo JText::_($row->goal); ?>
            </td>

            <td align="center">
              <?= $row->num ?>
            </td>

            <td align="center">
              <?= $row->user_name ?>
            </td>

            <td align="center">
              <?php echo $row->id; ?>
            </td>
          </tr>

          <?php
          $k = 1 - $k;
        }
        ?>

        <tr>
          <td>             
          </td>

          <td>             
          </td>

          <td>             
          </td>

          <td>             
          </td>

          <td align="right"> 
            <b><?= JText::_("Total") ?>:</b>&nbsp;&nbsp;
          </td>

          <td align="center">             
            <?= $this->total_stat["num"] ?>
          </td>

          <td>             
          </td>

          <td>             
          </td>
        </tr>

      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
  <?php echo JHTML::_('form.token'); ?>

</form>

<script>
  
  window.addEvent('domready', function(){
    $('period').addEvent('change', function(){
      var from  = $('from-period');
      var until = $('until-period');
      switch (this.value) {
<?php
foreach ($this->date_presets as $name => $value) {
  $case = "case '" . $name . "':\n";
  $case .= "from.setProperty('value', '" . $value['from'] . "');\n";
  $case .= "until.setProperty('value', '" . $value['until'] . "');\n";
  $case .= "break;\n";
  echo $case;
}
?>
      }
      document.adminForm.submit();
    });
  });
  
  jQuery(function($) {

    $('#adminForm_reset').click(function () {

      $('#search').val('');	
      $('#from-period').val('');
      $('#until-period').val('');

      $('#filter_user_id')[0].selectedIndex = 0;
      $('#filter_target_id')[0].selectedIndex = 0;	
      $('#filter_type')[0].selectedIndex = 0;
      
      $('#adminForm').submit();

    });

  });


</script>