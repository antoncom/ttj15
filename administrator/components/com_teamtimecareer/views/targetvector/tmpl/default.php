<?php
defined('_JEXEC') or die('Restricted access');

JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);
$editor = & JFactory::getEditor();
?>

<form action="index.php" method="post" name="adminForm">

  <div class="col width-60">

    <table cellpading="0" cellspacing="0">  
      <tr>
        <td valign="top">

          <fieldset class="adminform">
            <legend><?php echo JText::_('Details'); ?></legend>
            <table class="admintable">
              <tr>
                <td width="110" class="key">
                  <label for="title">
                    <?php echo JText::_('Goal'); ?>:
                  </label>
                </td>
                <td>
                  <input class="inputbox" type="text" name="title" id="title" size="60"
                         value="<?php echo $this->item->title; ?>" />
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
                         value="<?php echo $this->item->num; ?>" />
                </td>          
              </tr>

              <tr>
                <td width="110" class="key">
                  <label for="hourprice">
                    <?php echo JText::_('Hourly rate'); ?>:
                  </label>
                </td>
                <td>
                  <input class="inputbox" type="text" name="hourprice" id="hourprice" size="10"
                         value="<?php echo $this->item->hourprice; ?>" />
									<br>									
									<input name="apply_for_children" id="apply_for_children" type="checkbox">
									<label for="apply_for_children">
										<?php echo JText::_('Apply the hourly rate to all sub-goals'); ?>									
									</label>									
                </td>          
              </tr>

              <tr>
                <td width="110" class="key">
                  <label for="num_tree">
                    <?php echo JText::_('Value, including sub-goals'); ?>:
                  </label>
                </td>
                <td>
                  <?php echo $this->item->num_tree; ?>
                </td>          
              </tr>

              <tr>
                <td width="110" class="key" valign="top">
                  <label for="parent_id">
                    <?php echo JText::_('Parent goal'); ?>:
                  </label>
                </td>
                <td>
                  <?php echo $this->lists['select_goal']; ?>
                </td>          
              </tr>

              <tr>
                <td width="110" class="key" valign="top">
                  <label for="is_skill">
                    <?php echo JText::_('Sub goal is skill'); ?>:
                  </label>
                </td>
                <td>
                  <input type="hidden" name="is_skill" value="0"/>

                  <input class="inputbox" type="checkbox" name="is_skill" id="is_skill" value="1"
                  <?= $this->item->is_skill ? "checked" : "" ?>
                         <?= $this->hasChildren ? "disabled" : "" ?> />
                </td>          
              </tr>

            </table>
          </fieldset>

        </td>

        <td width="60%" valign="top">

          <? if (!$this->item->is_skill) { ?>

            <fieldset id="skillsList">
              <legend><?= JText::_('Skills for goal') ?></legend>

              <table class="adminlist" id="skillsTable">
                <thead>
                  <tr>
                    <th width="5">
                      <?php echo JText::_('NUM'); ?>
                    </th>
                    <th><?= JText::_('Skill name') ?></th>
                    <th><?= JText::_('Skill value') ?></th>
                    <th><?= JText::_('Skill actions') ?></th>
                  </tr>

                <tbody>
                  <?php
                  foreach ($this->skillItems as $i => $row) {
                    ?>

                    <tr id="skillrow<?= $row->id ?>" class="<?php echo "row1"; ?>">
                      <td>
                        <?= $i + 1 ?>
                      </td>

                      <td class="skillcell_title">
                        <div id="skill_title<?= $row->id ?>">
                          <?= $row->title ?>
                        </div>
                        <input type="text" name="skill_title[<?= $row->id ?>]"
                               value="<?= htmlspecialchars($row->title) ?>"
                               size="60" style="display:none;"
                               id="edit_skill_title<?= $row->id ?>" />

                        <input type="hidden" class="deleteskillflag" value="0"
                               name="skill_delete[<?= $row->id ?>]" />
                      </td>

                      <td  class="skillcell_num" align="center">
                        <div id="skill_num<?= $row->id ?>">
                          <?= $row->num ?>
                        </div>
                        <input type="text" name="skill_num[<?= $row->id ?>]" size="10"
                               value="<?= $row->num ?>"
                               style="display:none;"
                               id="edit_skill_num<?= $row->id ?>" />
                      </td>

                      <td align="center">
                        <a href="#deleteskill<?= $row->id ?>"
                           class="deleteskill"><?= JText::_('Delete') ?></a>
                      </td>
                    </tr>

                    <?php
                  }

                  $i++;
                  ?>

                  <tr class="skillnewrow <?php echo "row0"; ?>">
                    <td>
                      <?= $i + 1 ?>
                    </td>

                    <td>
                      <input type="text" name="newskill_title[]" size="60"/>
                    </td>

                    <td align="center">
                      <input type="text" name="newskill_num[]" size="10"/>
                    </td>

                    <td align="center">
                      <a href="javascript:void(0);"
                         class="addskill"><?= JText::_('Add') ?></a>
                    </td>
                  </tr>

                </tbody>
              </table>

            </fieldset>

          <? } ?>

        </td>
      </tr>
    </table>

    <fieldset class="adminform">
      <legend><?php echo JText::_('Goal description'); ?></legend>
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
  <?php echo JHTML::_('form.token'); ?>

</form>

<script>
  TeamTime.jQuery(function($) {

    $('#num').autoNumeric({mNum:6, mDec:0, aSep:''});

    $('#hourprice').autoNumeric({mDec:2, aSep:''});

    // skill row edit functions
    
    $('#is_skill').click(function () {
      if ($('#is_skill').is(':checked')) {
        $("#skillsList").css("display", "none");
      }
      else {
        $("#skillsList").css("display", "block");
      }
    });

    var deleteSkillRow = function () {
      var id = $(this).attr("href");

      // delete existing row
      if (typeof(id) !== 'undefined' && id.indexOf("deleteskill") >= 0) {
        id = id.replace("#deleteskill", "");
        $("#skillrow" + id + " input.deleteskillflag").val("1");
        $("#skillrow" + id).css("display", "none");
      }
      // delete new row
      else {        
        $(this).closest("tr").css("display", "none");
      }
      
      return false;
    };

    var addSkillRow = function () {
      var newRow = $('tr:last.skillnewrow').clone();
      var html = "<tr class='skillnewrow'>" + $(newRow).html() + "</tr>";
      $("#skillsTable tr:last").after(html);

      var rows = $("#skillsTable tr");
      var rowsNum = rows.length;

      // change action link
      var oldCell = $(rows[rowsNum - 2]).children("td:last");
      $(oldCell).html('<a href="javascript:void(0);" class="deleteskill"><?= JText::_('Delete') ?></a>');
      $(oldCell).children("a.deleteskill").click(deleteSkillRow);
      
      // add click handler
      var cells = $("#skillsTable tr:last td");
      $(cells[0]).html(rowsNum - 1);      
      $("#skillsTable tr:last td:last a.addskill").click(addSkillRow);
    };
    
    $('.addskill').click(addSkillRow);
    $('.deleteskill').click(deleteSkillRow);

    var lastCellName = "";

    $('.skillcell_title, .skillcell_num').click(function () {
      var row = $(this).parent("tr");
      var id = $(row).attr("id");
      id = id.replace("skillrow", "");

      var cellName = "";
      if ($(this).hasClass("skillcell_title")) {
        cellName = "skill_title";
      }
      else if ($(this).hasClass("skillcell_num")) {
        cellName = "skill_num";
      }

      if (lastCellName != "") {
        $("#edit_" + lastCellName).attr("style", "display:none");
        $("#" + lastCellName).html($("#edit_" + lastCellName).val());
        $("#" + lastCellName).removeAttr("style");
      }

      if (cellName != "") {
        lastCellName = cellName + id;
        $("#" + cellName + id).attr("style", "display:none");
        $("#edit_" + cellName + id).removeAttr("style");
      }
    });

  });

</script>