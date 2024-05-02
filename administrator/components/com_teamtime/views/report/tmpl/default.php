<?php
defined('_JEXEC') or die('Restricted access');

$view_type = "month";
if (isset($this->date_select["name"])) {
  if (stripos($this->date_select["name"], "day") !== false &&
      stripos($this->date_select["name"], "days") === false)
    $view_type = "day";
  else if (stripos($this->date_select["name"], "week") !== false)
    $view_type = "week";
}

if (!in_array($this->period, array("last30", "month", "year"))) {
  $filter_date = "&start_date=" . $this->from_period;
}
else {
  $filter_date = "";
}
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
  <div id="content-controls">

    <div class="select-project" style="margin-left: 5px;">
      <?php echo $this->lists['select_type']; ?><p>
        <?php echo $this->lists['select_task']; ?>
    </div>

    <div class="select-project">
      <?php echo $this->lists['select_project']; ?>
    </div>
    <div class="select-user">
      <?php echo $this->lists['select_user']; ?>
    </div>
    <div class="select-date">
      <?php echo $this->lists['select_date']; ?>
      <?php echo JHTML::_('calendar', $this->from_period, 'from_period', 'from-period', JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME')); ?>
      <?php echo JHTML::_('calendar', $this->until_period, 'until_period', 'until-period', JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME')); ?>
      <button
        onclick="this.form.submit();"
        ><?php echo JText::_('Go'); ?></button>
    </div>
  </div>
  <div id="content-area">
    <?php if ($this->contentLayout)
      require_once(dirname(__FILE__) . DS . $this->contentLayout . '.php'); ?>
  </div>
  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="showReport" />
  <?php echo JHTML::_('form.token'); ?>
</form>

<script type="text/javascript">
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
</script>

<form action="" id="exportForm">
  <table width="100%">
    <tr>
      <td align="right" height="25">
        <a href="#" id="bExportAsXls"><?= JText::_('REPORT EXPORT EXCEL') ?></a> &nbsp;&nbsp;
        <a href="#" id="bExportAsHtm"><?= JText::_('REPORT EXPORT HTML') ?></a>
      </td>
    </tr>
  </table>
</form>

<script>
  (function($){

    $(document).ready(function(){

      //if($('#from-period').val() == "" && $('#until-period').val() == ""){
      //  document.adminForm.submit();
      //}

      $('#bExportAsXls').click(function(){
        $(this).attr("href",
        "/administrator/index.php?"+$("#adminForm").serialize()+"&format=xls");
        return true;
      });

      $('#bExportAsHtm').click(function(){
        $(this).attr("href",
        "/administrator/index.php?"+$("#adminForm").serialize()+"&format=htm");
        return true;
      });
    });

  })(jQuery);
</script>