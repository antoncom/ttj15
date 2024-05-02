<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
?>

<script language="JavaScript" type="text/javascript">
  function getSelPro(v)  {
    sstr = "";
    for(i=0; i < v.length; i++)
    {
      if (v.options[i].selected)
      {
        if(v.options[i].value != "")
        {
          sstr = sstr + v.options[i].value + ",";
        }
      }
    }
    sstr = sstr.substring(0,sstr.length-1);
    document.getElementById("selectedProjects").value = sstr;
  }
</script>

<?php

/*
  Class: TaskViewTask
  The View Class for Task
 */

class TaskViewTask extends JView {

  function display($tpl = null) {
    global $mainframe;

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();

    // get request vars
    $option = JRequest::getCmd('option');
    $controller = JRequest::getWord('controller');
    $edit = JRequest::getVar('edit', true);

    // set toolbar items
    $text = $edit ? JText::_('Edit') : JText::_('New');
    JToolBarHelper::title(
        JText::_('Task') . ': <small><small>[ ' . $text . ' ]</small></small>', TEAMLOG_ICON);
    JToolBarHelper::save();
    JToolBarHelper::apply();
    $edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

    // get data from the model
    $item = & $this->get('data');

    // set default state for new task
    if (!$edit) {
      $item->state = 0;
    }

    // state select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
    $lists['select_state'] = JHTML::_('teamlog.taskstatelist', $options, 'state', 'class="inputbox"', 'value', 'text', $item->state);

    // project select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Global (All Projects)') . ' -');
    $lists['select_project'] = JHTML::_('teamlog.projectlist', $options, 'project_id', 'class="inputbox" multiple="multiple" onclick="getSelPro(this);"', 'value', 'text', $item->project_id);

    // type select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select Type') . ' -');
    $lists['select_type'] = JHTML::_('teamlog.typelist', $options, 'type_id', 'class="inputbox"', 'value', 'text', $item->type_id);
    //onclick="alert($lists['select_type'])"
    // goals select
    $lists['select_goals'] = TeamTime::_("targetvector_selector", "", $item);

    // dotu price
    $elements['dotu_price'] = TeamTime::_("user_hourprice_teamtimedotu", "", $item);

    // set template vars
    $this->assignRef('user', $user);
    $this->assignRef('option', $option);
    $this->assignRef('controller', $controller);
    $this->assignRef('lists', $lists);
    $this->assignRef('elements', $elements);
    $this->assignRef('item', $item);

    parent::display($tpl);
  }

}