<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php
JFilterOutput::objectHTMLSafe($this->item, ENT_QUOTES);


jimport('joomla.html.pane');

$user = & JFactory::getUser();

$editor = & JFactory::getEditor();

$pane = & JPane::getInstance('sliders');
$format = JText::_('DATE_FORMAT_LC2');
?>

<style>

  select#curtaskid, select#project_id, select#curtodoid {
    background-color: #FFDDDD;
  }

  select#curtaskid option.option2, select#project_id option.option2{
    color: #777777;
    background-color: #E6E6E6;
  }

  select#curtodoid {
    width: 250px;
  }

  select#curtaskid {
    width: 100%;
  }

</style>

<script>

  jQuery(document).ready(function ($) {

    var loadPrice = function(target_id) {
      // update hourly rate

      var target_param = "";
      if (typeof(target_id) != 'undefined' && target_id != 0) {
        target_param = "&target_id=" + target_id;
      }

      var params = "&user_id=" + $("#user_id").val()
        + "&task_id=" + $("#curtaskid").val()
        + "&project_id=" + $("#project_id").val()
        + target_param
        + "&t=" + new Date().getTime();

      $.get("/administrator/index.php?option=com_teamtime&controller=task&task=loadpriceinfo" +
        params,
      function (data) {
        $("#hourly_rate").val(data);
      });
    };

    var onTaskChangeD = function() {
<?= TeamTime::_("js_ontaskchange_teamtimedotu", "") ?>
    };

    var onTaskChange = function() {
      loadPrice();
      onTaskChangeD();
    };

    $("#project_id option:first").attr("disabled", "disabled");
    var task_id = $("#curtaskid").val();
    var parent_todo_id = $("#curtodoid").val();

    // first load tasks list
    if ($("#project_id").val() != "") {
      //get tasks list for project
      $.get(
      "/administrator/index.php?option=com_teamtime&controller=task&task=loadtasks&project_id="+
        $("#project_id").val()+
        "&todo_id=0&t="+new Date().getTime(),

      function (data) {
        $("#block_task_id").html(data);
        $("#curtaskid").val(task_id);
        $("#curtaskid").change(onTaskChange);

        onTaskChange();
      });

      //get todos list for project
      $.get("/administrator/index.php?option=com_teamtime&controller=todo&task=get_list_todos&project_id=" +
        $("#project_id").val() +
        "&todo_id=" + parent_todo_id +
        "&current_id=" + <?= $this->item->id ?> +
        "&t=" + new Date().getTime(),
      function(data){
        $("#curtodolabel").hide();
        $("#block_todo_id").html(data);
        $("#curtodoid").val(parent_todo_id);
      });
    }
    else{
      $("#project_id")[0].selectedIndex = -1;

      $("#curtodoid").attr("disabled", "disabled");
      $("#curtodolabel").show();
    }

    $("#project_id").change(function() {

      //get tasks list for project
      $.get(
      "/administrator/index.php?option=com_teamtime&controller=task&task=loadtasks&project_id="+
        $("#project_id").val() + "&todo_id=0&t="+new Date().getTime(),

      function (data) {
        $("#block_task_id").html(data);
        $("#curtaskid").change(onTaskChange);

        onTaskChange();
      });

      //get todos list for project
      $.get("/administrator/index.php?option=com_teamtime&controller=todo&task=get_list_todos&project_id=" +
        $("#project_id").val() +
        "&current_id=" + <?= $this->item->id ?> +
        "&t=" + new Date().getTime(),
      function(data){
        $("#curtodolabel").hide();
        $("#block_todo_id").html(data);
      });
    });

    $("#user_id, #target_id").change(function () {
      onTaskChange();
    });
    onTaskChangeD();

    $("#is_parent").change(function(){
      if(!this.checked){
        this.checked = !confirm(
        '<?= JText::_("THIS TODO CONTENTS CHILD TODOS MAKE IT ORDINARY") ?>?');
        if(!this.checked)
          this.disabled = true;
      }
    });

  });

</script>

<form action="index.php" method="post" name="adminForm">

  <table cellspacing="0" cellpadding="0" border="0" width="100%">
    <tr>
      <td valign="top">
        <fieldset class="adminform">
          <legend><?php echo JText::_('Details'); ?></legend>
          <table class="admintable">
            <tr>
              <td width="110" class="key">
                <label for="state">
                  <?php echo JText::_('User'); ?>:
                </label>
              </td>
              <td>
                <?php echo $this->lists['select_user']; ?>
              </td>

              <td align="left"  class="key">
                <label for="costs">
                  <?php echo JText::_('Overhead Expenses'); ?>
                </label>
              </td>
              <td>
                <input name="costs" id="costs" value="<?= $this->item->costs ?>">
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

              <? if ($user->usertype == "Super Administrator") { ?>
                <td align="left"  class="key">
                  <label for="hourly_rate">
                    <?php echo JText::_('Hour Price'); ?>:
                  </label>
                </td>
                <td>
                  <input name="hourly_rate" id="hourly_rate"
                         value="<?= $this->item->hourly_rate ?>">
                </td>
                <?
              }
              else if ($user->usertype == "Administrator") {
                ?>
                <td align="left"  class="key">
                  <label for="hourly_rate">
                    <?php echo JText::_('Hour Price'); ?>:
                  </label>
                </td>
                <td>
                  <input disabled
                         value="<?= $this->item->hourly_rate ?>">
                  <input name="hourly_rate" id="hourly_rate" type="hidden"
                         value="<?= $this->item->hourly_rate ?>">
                </td>
                <?
              }
              else {
                ?>
                <td></td>
                <td>
                  <input name="hourly_rate" id="hourly_rate" type="hidden"
                         value="<?= $this->item->hourly_rate ?>">
                </td>
              <? } ?>
            </tr>

            <tr>
              <td class="key">
                <label for="directory">
                  <?php echo JText::_('Project'); ?>:
                </label><br>
                <div>
                  <?php echo $this->lists['select_project']; ?>
                </div>
              </td>

              <td width="110" class="key">
                <label for="directory">
                  <?php echo JText::_('Type'); ?> / <?php echo JText::_('Task'); ?>:
                </label><br>
                <div id="block_task_id">
                  <?php echo $this->lists['select_task']; ?>
                </div>
              </td>

              <td colspan="2">
                <div id="block_todo_id">
                  <?php echo $this->lists['select_todo']; ?>
                </div>
                <div id="curtodolabel" style="display:none">
                  <!-- <?= JText::_("Select project first") ?> -->
                </div>
              </td>
            </tr>

            <? if ($this->lists["select_goals"] != "") { ?>
              <tr>
                <td width="110" class="key">
                  <label for="target_id">
                    <?= JText::_('Vector of goals') ?>:
                  </label>
                </td>
                <td>

                  <div id="block_target_id">
                    <?= $this->lists['select_goals'] ?>
                  </div>

                </td>
              </tr>
            <? } ?>

            <tr>
              <td width="110" class="key">
                <label for="state">
                  <?php echo JText::_('Plan'); ?>:
                </label>
              </td>
              <td>
                <input class="inputbox" type="text" name="hours_plan" id="hours_plan" size="7" value="<?php echo $this->item->hours_plan; ?>" />
              </td>
            </tr>
            <tr>
              <td width="110" class="key">
                <label for="state">
                  <?php echo JText::_('Fact'); ?>:
                </label>
              </td>
              <td>
                <?php echo ($this->item->hours_fact > 0) ? $this->item->hours_fact : "0" ?>
              </td>
            </tr>

            <tr>
              <td width="110" class="key">
                <label for="is_parent">
                  <?php echo JText::_('This is a Team todo'); ?>:
                </label>
              </td>
              <td>
                <input type="hidden" name="is_parent" value="0">
                <input type="checkbox" name="is_parent" id="is_parent"
                       <?= (int) $this->item->is_parent ? "checked" : "disabled" ?> value="1">
              </td>
            </tr>

            <tr>
              <td width="110" class="key">
                <label for="state">
                  <?php echo JText::_('Note by email'); ?>:
                </label>
              </td>
              <td>
                <input type="checkbox" name="sendmail" value="1">
              </td>
            </tr>

            <? TeamTime::_("Formals_getTodoNotifyClient", $this->item, "") ?>

            <? TeamTime::_("Formals_getTodoParams", $this->item, "standart") ?>

          </table>
        </fieldset>
        <fieldset class="adminform">
          <legend><?php echo JText::_('Description'); ?></legend>
          <table class="admintable">
            <tr>
              <td width="110" class="key">
                <label for="name">
                  <?php echo JText::_('Title'); ?>:
                </label>
              </td>
              <td valign="top">
                <input class="inputbox" type="text" name="title" id="title" size="100" value="<?php echo $this->item->title; ?>" />
              </td>
            </tr>
            <tr>
              <td valign="top" colspan="3">
                <?php
                // parameters : areaname, content, width, height, cols, rows, show xtd buttons
                echo $editor->display('description', $this->item->description, '550', '300', '60', '20', array());
                ?>
              </td>
            </tr>

            <tr>
              <td>
                <? TeamTime::getTodoRepeatParams2($this->item, "") ?>
              </td>
            </tr>

          </table>
        </fieldset>
      </td>
      <td valign="top" width="320" style="padding: 7px 0 0 5px">
        <?php
        $db = & JFactory::getDBO();

        $create_date = null;
        $nullDate = $db->getNullDate();
        ?>
        <table width="100%" style="border: 1px dashed silver; padding: 5px; margin-bottom: 10px;">
          <?php
          if ($this->item->id) {
            ?>
            <tr>
              <td>
                <strong><?php echo JText::_('Todo ID'); ?>:</strong>
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
              <strong><?php echo JText::_('Created'); ?></strong>
            </td>
            <td>
              <?php
              if ($this->item->created == $nullDate) {
                echo JText::_('New item');
              }
              else {
                echo JHTML::_('date', $this->item->created, $format);
              }
              ?>
            </td>
          </tr>
          <tr>
            <td>
              <strong><?php echo JText::_('Modified'); ?></strong>
            </td>
            <td>
              <?php
              if ($this->item->modified == $nullDate) {
                echo JText::_('Not modified');
              }
              else {
                echo JHTML::_('date', $this->item->modified, $format);
              }
              ?>
            </td>
          </tr>
        </table>
        <?php
        // Create the form
        $form = new JParameter('', JPATH_COMPONENT . DS . 'models' . DS . 'todo.xml');

        // Details Group
        $format = JText::_('DATE_FORMAT_MYSQL');
        $active = (intval($this->item->user_id) ?
                        intval($this->item->user_id) : $this->user->get('id'));
        $form->set('user_id', $active);
        $form->set('created', JHTML::_('date', $this->item->created, $format));

        $title = JText::_('Parameters - Todo');
        echo $pane->startPane("content-pane");
        echo $pane->startPanel($title, "detail-page");
        echo $form->render('details');
        echo $pane->endPanel();
        echo $pane->endPane();
        ?>
      </td>
    </tr>
  </table>

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
  <input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
  <?php echo JHTML::_('form.token'); ?>

</form>

<script>

  function submitbutton(pressbutton) {
    var $ = jQuery;

    if($("#user_id").val() != "" && (pressbutton == "save" || pressbutton == "apply")){
      jQuery.get("/administrator/index.php?option=com_teamtime&controller=todo&task=check_project_for_user"+
        "&project_id="+$("#project_id").val()+
        "&user_id="+$("#user_id").val(),
      function(data){
        var enable_project = false;
        var alert_msg = false;

        var user_name = $("#user_id").val()?
          $("#user_id")[0].options[$("#user_id")[0].selectedIndex].text : "";
        var project_name = $("#project_id").val()?
          $("#project_id")[0].options[$("#project_id")[0].selectedIndex].text : "";

        if(data == "0"){
          var confirm_str = '<?= JText::_("CHECK_PROJECT_USER_STR") ?>';
          confirm_str = confirm_str.replace("{name}", user_name);
          confirm_str = confirm_str.replace("{project}", project_name);
          confirm_str = confirm_str.replace("{newline}", "\n");

          if(confirm(confirm_str)){
            enable_project = true;
          }
          else{
            alert_msg = true;
          }
        }

        if(enable_project){
          $.get("/administrator/index.php?option=com_teamtime&controller=todo&task=enable_project_for_user"+
            "&project_id="+$("#project_id").val()+
            "&user_id="+$("#user_id").val(),
          function(data){
            submitform(pressbutton);
          });
        }
        else{
          if(alert_msg){
            var alert_str = '<?= JText::_("ALERT_PROJECT_USER_STR") ?>';
            alert_str = alert_str.replace("{name}", user_name);

            alert(alert_str);
          }

          submitform(pressbutton);
        }
      });
    }
    else
      submitform(pressbutton);
  }

  (function($) {

    $(document).ready(function() {

      $("#costs").autoNumeric({mNum:5, mDec:2, aSep:''});

    });

  })(jQuery);
</script>