<?php
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);
$editor = & JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm">
  <!--input type="hidden" name="title" value="" /-->

  <div class="col width-60">

    <table cellpading="0" cellspacing="0">
      <tr>
        <td valign="top">


          <fieldset class="adminform">
            <legend><?php echo JText::_('Details'); ?></legend>
            <table class="admintable">
              <tr>
                <td width="110" class="key" valign="top">
                  <label for="user_id">
										<?php echo JText::_('user'); ?>:
                  </label>
                </td>
                <td>
									<?= $this->lists["select_user"] ?>
                </td>
              </tr>

              <tr>
                <td width="110" class="key" valign="top">
                  <label for="target_id">
										<?php echo JText::_('Goal'); ?>:
                  </label>
                </td>
                <td>
									<?= $this->lists["select_goal"] ?>
                </td>
              </tr>

              <tr>
                <td width="110" class="key">
                  <label for="num">
										<?php echo JText::_('Value'); ?>:
                  </label>
                </td>
                <td>
                  <input class="inputbox" type="text" name="num" id="num" size="10"
                         value="<?= abs($this->item->num) ?>" />
                </td>
              </tr>

              <tr>
                <td width="110" class="key">
                  <label for="indication">
										<?php echo JText::_('Indication'); ?>:
                  </label>
                </td>
                <td>
									<?= $this->lists["select_sign"] ?>
                </td>
              </tr>

            </table>
          </fieldset>

        </td>

        <td width="60%" valign="top">

          <div id="skillsBlock">
          </div>

        </td>
      </tr>
    </table>

    <fieldset class="adminform">
      <legend><?php echo JText::_('Experience'); ?></legend>
      <table class="admintable">

        <tr>
          <td valign="top" colspan="3">
						<?php
						// parameters : areaname, content, width, height, cols, rows, show xtd buttons
						echo $editor->display(
								'description', $this->item->description, '550', '300', '60', '20', array());
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
	<?php echo JHTML::_('form.token'); ?>
</form>

<script>
  TeamTime.jQuery(function ($) {

    $('#num').autoNumeric({mNum:6, mDec:0, aSep:''});

<? //if (!$this->edit) {                   ?>

    /*
        var skillsForTargets = <?= json_encode($this->skillsForTargets) ?>;
     */

    var updateSkillsTable = function () {
      var user_id = $("#user_id").val();
      var target_id = $("#target_id").val();

      if (user_id == "" || user_id == null || target_id == "" || target_id == null) {
        $("#skillsBlock").html("");
        return;
      }

      /*
          var userKey = "u" + user_id;
          var targetKey = "t" + target_id;

          if (userKey in skillsForTargets) {
            if (targetKey in skillsForTargets[userKey]) {
              console.log(skillsForTargets[userKey][targetKey]);
            }
          }
       */

      var url = "<?= JURI::base() . "index.php?option={$this->option}&controller={$this->controller}" ?>&task=edit&format=raw"
        + "&user_id=" + user_id
        + "&target_id=" + target_id;

      $.get(url, function (data) {
        if (data != "") {
          // set html data
          $("#skillsBlock").html(data);

          // change names and values for checkbox
          $('#skillsBlock td.skillcell_check input[name*="cid"]').each(function (i, n) {
            $(n).attr("name", "skill_num[" + $(n).val() + "]");
            $(n).attr("value", "1");

            var checked = $(n).closest("td").children('input[name*="skill_checked"]').val();
            if (checked == "1") {
              $(n).attr("checked", "checked");
            }
          });

          // set handler for checkbox
          $('#skillsBlock td.skillcell_check input').change(function () {
            var p = $(this).closest("tr");
            var v = $(p).children("td.skillcell_num").children("input").val();

            if (!$(this).attr("checked")) {
              v = 0;
            }
            $(p).children("td.skillcell_new").children("input").val(v);
          });

          // set handler for input field
          $("input.new_skill_value").change(function () {
            var p = $(this).closest("tr");
            var v = $(p).children("td.skillcell_num").children("input").val();
            var chb = $(p).children('td.skillcell_check').children('input[name*="skill_num"]');

            // auto check box
            if (parseFloat($(this).val()) >= parseFloat(v)) {
              $(chb).attr("checked", "checked");
            }
            else {
              //$(chb).removeAttr("checked");
            }
          });
        }
        else {
          $("#skillsBlock").html("");
        }
      });
    };

    $("#target_id, #user_id").change(updateSkillsTable);

    updateSkillsTable();

<? //}                   ?>

  });
</script>