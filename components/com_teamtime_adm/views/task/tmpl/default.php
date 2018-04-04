<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);

$user = & JFactory::getUser();

$editor = & JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm">
  <input type="hidden" name="rate" size="10" value="<?php echo $this->item->rate; ?>" />

  <div class="col width-60">
    <fieldset class="adminform">
      <legend><?php echo JText::_('Details'); ?></legend>
      <table class="admintable">
        <tr>
          <td>
        <tr>
          <td width="110" class="key">
            <label for="name">
              <?php echo JText::_('Name'); ?>:
            </label>
          </td>
          <td>
            <input class="inputbox" type="text" name="name" id="name" size="60" value="<?php echo $this->item->name; ?>" />
          </td>
        </tr>
        <tr>
          <td width="110" class="key">
            <label for="state">
              <?php echo JText::_('State'); ?>:
            </label>
          </td>
          <td>
            <?php echo $this->lists['select_state']; ?>
          </td>
        </tr>
        <tr>
          <td width="110" class="key" valign="top">
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
              <?php echo JText::_('Type'); ?>:
            </label>
          </td>
          <td>
            <?php echo $this->lists['select_type']; ?>
          </td>
        </tr>

        <? if ($this->lists["select_goals"] != "") { ?>

          <tr>
            <td width="110" class="key" valign="top">
              <label for="target_id">
                <?php echo JText::_('Vector of goals'); ?>:
              </label>
            </td>
            <td>
              <?php echo $this->lists['select_goals']; ?>
            </td>
          </tr>

        <? } ?>

        <tr>
          <? if ($user->usertype == "Super Administrator") { ?>
            <td width="110" class="key">
              <label for="name">
                <?php echo JText::_('Rate'); ?>:
              </label>
            </td>
            <td>
              <input class="inputbox" type="text" name="rate" id="rate" size="10" value="<?php echo $this->item->rate; ?>" />
              <?= $this->elements["dotu_price"] ?>
            </td>
          <? }
          else if ($user->usertype == "Administrator") { ?>
            <td width="110" class="key">
              <label for="name">
                <?php echo JText::_('Rate'); ?>:
              </label>
            </td>
            <td>
              <input class="inputbox" type="text" disabled size="10" value="<?php echo $this->item->rate; ?>" />
            </td>
          <? }
          else { ?>
            <td></td>
            <td></td>
          <? } ?>
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
            echo $editor->display('description', $this->item->description, '550', '300', '60', '20', array());
            ?>
          </td>
        </tr>
      </table>
    </fieldset>
  </div>

  <div class="clr"></div>

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="selectedProjects" id="selectedProjects" value="" />
  <?php echo JHTML::_('form.token'); ?>

</form>

<script>
  
  (function($) {

    $(document).ready(function() {
      getSelPro($("#project_id")[0]);
      //alert($("#selectedProjects").val());
      
<?= Teamtime::_("oncheck_user_hourprice_teamtimedotu", "") ?>
    });
    
  })(jQuery);
  
</script>