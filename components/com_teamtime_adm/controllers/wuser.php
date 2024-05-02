<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class WuserController extends JController {
  /*
    Function: Constructor

    Parameters:

    $default -

    Returns:

    See Also:

   */

  function __construct($default = array()) {
    parent::__construct($default);

    $this->registerTask('apply', 'save');
    $this->registerTask('preview', 'display');
    $this->registerTask('edit', 'display');
    //$this->registerTask('add', 'display');
    $this->registerTask('cancel', 'cancel');
  }

  function display() {
    global $mainframe;

    $is_set_backurl = JRequest::getVar('backurl', '', 'get');
    if ($is_set_backurl)
      $mainframe->setUserState('com_teamtimeformals_backurl', 1);
    else
      $mainframe->setUserState('com_teamtimeformals_backurl', 0);

    switch ($this->getTask()) {
      case 'add':
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('view', 'wuser');
        JRequest::setVar('edit', false);
        break;
      case 'edit':
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('view', 'wuser');
        JRequest::setVar('edit', true);
        break;
    }

    // set the default view
    $view = JRequest::getCmd('view');
    if (empty($view)) {
      JRequest::setVar('view', 'wusers');
    }

    parent::display();
  }

  function get_redirect_url($default_url) {
    global $mainframe;

    $is_set_backurl = $mainframe->getUserState('com_teamtimeformals_backurl');
    if ($is_set_backurl)
      $url = TeamTime::_("formalsdata_get_variables_url", "");
    else
      $url = $default_url;

    $mainframe->setUserState('com_teamtimeformals_backurl', 0);

    return $url;
  }

  function save() {
    global $option;

    $db = & JFactory::getDBO();

    // Check for request forgeries
    JRequest::checkToken() or die('Invalid Token');

    $post = JRequest::get('post');
    $cid = JRequest::getVar('cid', array(0), 'post', 'array');
    $post['id'] = (int) $cid[0];

    if (!isset($post['send_msg']))
      $post['send_msg'] = 0;

    if (!isset($post['hideforother']))
      $post['hideforother'] = 0;

    $query = "
			insert into #__teamlog_userdata (
				user_id, 
				send_msg, 
				hour_price, 
				hideforother, 
				salary
			)
			values(
				{$post['id']}, 
				{$post['send_msg']}, 
				'{$post['hour_price']}', 
				'{$post['hideforother']}', 
				'{$post['salary']}'
			)
			ON DUPLICATE KEY UPDATE 
				send_msg={$post['send_msg']}, 
				hour_price='{$post['hour_price']}', 
				hideforother='{$post['hideforother']}', 
				salary='{$post['salary']}'";
    $res = $db->Execute($query);

    $msg = "";
    if ($res) {
      TeamTime::_("save_user_params", $post);
      TeamTime::_("onsave_target_balance_teamtimedotu", $post);

      $msg = JText::_('User Saved');
    }
    else {
      $msg = JText::_('Error Saving User');
    }

    switch ($this->_task) {
      case 'apply':
        $link = 'index.php?option=' . $option . '&controller=' . $this->getName() .
            '&view=type&task=edit&cid[]=' . $post['id'];
        break;
      case 'save':
      default:
        $link = 'index.php?option=' . $option . '&controller=' . $this->getName();
        break;
    }

    $this->setRedirect($this->get_redirect_url($link), $msg);
  }

  function cancel() {
    global $option;

    $link = $this->get_redirect_url("");
    if ($link == "")
      $link = 'index.php?option=' . $option . '&controller=' . $this->getName();

    $this->setRedirect($this->get_redirect_url($link));
  }

  /* function checkPost($post){
    if(!isset($post['name']) || $post['name'] == ""){
    JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
    return false;
    }
    return true;
    } */

  /* function remove() {
    global $option;

    // Check for request forgeries
    JRequest::checkToken() or die( 'Invalid Token' );

    $cid = JRequest::getVar('cid', array(), 'post', 'array');
    JArrayHelper::toInteger($cid);

    if (count($cid) < 1) {
    JError::raiseError(500, JText::_('Select an item to delete'));
    }

    $model = $this->getModel();

    if(!$model->delete($cid)) {
    echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
    }

    $msg = JText::_('Type Deleted');
    $link = 'index.php?option='.$option.'&controller='.$this->getName();

    $this->setRedirect($link, $msg);
    } */
}