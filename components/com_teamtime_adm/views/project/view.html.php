<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/*
  Class: ProjectViewProject
  The View Class for Project
 */

class ProjectViewProject extends JView {

  function display($tpl = null) {
    global $mainframe;

    $db = & JFactory::getDBO();
    $user = & JFactory::getUser();

    // get request vars
    $option = JRequest::getCmd('option');
    $controller = JRequest::getWord('controller');
    $edit = JRequest::getVar('edit', true);

    $user_ids = JRequest::getVar('users');

    // set toolbar items
    $text = $edit ? JText::_('Edit') : JText::_('New');
    JToolBarHelper::title(JText::_('Project') . ': <small><small>[ '
        . $text . ' ]</small></small>', TEAMLOG_ICON);
    JToolBarHelper::save();
    JToolBarHelper::apply();
    $edit ? JToolBarHelper::cancel('cancel', 'Close') : JToolBarHelper::cancel();

    // get data from the model
    $item = & $this->get('data');

    // state select
    $options = JHTML::_('select.option', '', '- ' . JText::_('Select State') . ' -');
    $lists['select_state'] = JHTML::_(
            'teamlog.projectstatelist', $options, 'state', 'class="inputbox"', 'value', 'text', $item->state);

    $options = JHTML::_(
            'select.option', '', '- ' . JText::_('Select User') . ' -');
    $lists['select_users'] = JHTML::_(
            'teamlog.userlist', $options, 'users[]', 'size="10" multiple class="inputbox"', 'value', 'text', $user_ids);

    $options = array(
        JHTML::_('select.option', '0', JText::_("Fixed price")),
        JHTML::_('select.option', '1', JText::_("Multiplier of man-hour price"))
    );
    $lists['radio_rate'] = JHTML::_(
            'select.radiolist', $options, 'dynamic_rate', 'class="inputbox"', 'value', 'text', $item->dynamic_rate);

    $model = new ProjectModelProject();
    $item->maxRate = $model->getMaxRate($item->id, $item->dynamic_rate);
    $item->minRate = $model->getMinRate($item->id, $item->dynamic_rate);;

    $conf_data = TeamTime::getConfig();

    // set template vars
    $this->assignRef('user', $user);
    $this->assignRef('option', $option);
    $this->assignRef('controller', $controller);
    $this->assignRef('lists', $lists);
    $this->assignRef('item', $item);

    $this->assignRef('conf_data', $conf_data);

    parent::display($tpl);
  }

}