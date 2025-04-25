<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class TaskController extends JController {

  function __construct($default = array()) {
    parent::__construct($default);

    $this->registerTask('apply', 'save');
    $this->registerTask('preview', 'display');
    $this->registerTask('edit', 'display');
    $this->registerTask('add', 'display');
  }

  function display() {

    switch ($this->getTask()) {
      case 'add':
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('view', 'task');
        JRequest::setVar('edit', false);
        break;
      case 'edit':
        JRequest::setVar('hidemainmenu', 1);
        JRequest::setVar('view', 'task');
        JRequest::setVar('edit', true);
        break;

      case 'loadtasks':
        $this->loadTasks();
        break;

      case 'loadpriceinfo':
        $this->loadPriceInfo();
        break;
    }

    // set the default view
    $view = JRequest::getCmd('view');
    if (empty($view)) {
      JRequest::setVar('view', 'tasks');
    }

    parent::display();
  }

  function setState() {
    global $option;

    // Check for request forgeries
    JRequest::checkToken() or die('Invalid Token');

    $cid = JRequest::getVar('state_change_id', array(), 'post', 'array');

    $id = (isset($cid[0])) ? (int) $cid[0] : null;
    $state = JRequest::getVar('state' . $id, 0);

    $model = $this->getModel();

    if ($model->storeState($id, $state)) {
      $msg = JText::_('State Changed');
    }
    else {
      $msg = JText::_('Error Changing State');
    }

    $link = 'index.php?option=' . $option . '&controller=' . $this->getName();
    $this->setRedirect($link, $msg);
  }

  function save() {
    global $option;

    // Check for request forgeries
    JRequest::checkToken() or die('Invalid Token');

    $post = JRequest::get('post');

    $cid = JRequest::getVar('cid', array(0), 'post', 'array');
    $post['id'] = (int) $cid[0];
    $post['description'] = $_REQUEST["description"];

    //print "<pre>";
    //var_dump($post);
    //exit();

    $model = $this->getModel();

    $msg = "";
    if ($this->checkPost($post)) {
      if ($model->store($post)) {
        $msg = JText::_('Task Saved');
      }
      else {
        $msg = JText::_('Error Saving Task');
      }
    }

    switch ($this->_task) {
      case 'apply':
        $item = & $model->getData();
        $link = 'index.php?option=' . $option . '&controller=' . $this->getName() .
            '&view=type&task=edit&cid[]=' . $item->id;
        break;
      case 'save':
      default:
        $link = 'index.php?option=' . $option . '&controller=' . $this->getName();
        break;
    }

    $this->setRedirect($link, $msg);
  }

  function checkPost($post) {
    if (!isset($post['name']) || $post['name'] == "") {
      JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid name'));
      return false;
    }
    if (!isset($post['type_id']) || $post['type_id'] == "" || $post['type_id'] == 0) {
      JError::raiseWarning(0, JText::_('Error Saving: Please enter a valid Type'));
      return false;
    }
    return true;
  }

  function remove() {
    global $option;

    // Check for request forgeries
    JRequest::checkToken() or die('Invalid Token');

    $cid = JRequest::getVar('cid', array(), 'post', 'array');
    JArrayHelper::toInteger($cid);

    if (count($cid) < 1) {
      JError::raiseError(500, JText::_('Select an item to delete'));
    }

    $model = $this->getModel();

    if (!$model->delete($cid)) {
      JError::raiseError(500, $model->getError());
    }

    $msg = JText::_('Task Deleted');
    $link = 'index.php?option=' . $option . '&controller=' . $this->getName();

    $this->setRedirect($link, $msg);
  }

  function loadTasks() {
    $params = JRequest::get('get');

    $project = new Project($params["project_id"]);
    $this->task_type_array = $project->getTaskTypeArray();
    ?>
    <?php
    $typeNums = count($this->task_type_array);
    $todo_id = JRequest::getVar('todo_id');

    if (isset($todo_id) && $todo_id != 0) {
      $nTodo = new Todo($todo_id);
      $log_task_id = $nTodo->task_id;
      $log_proj_id = $nTodo->project_id;
      $disabled = "";
    }
    else {
      $disabled = ($log_proj_id > 0 && $log_task_id > 0) ? " disabled style='background-color: #FFDDDD;'" : "";
    }
    ?>

    <select id="curtaskid" class="task" name="task_id" size="15" <?php echo $disabled; ?>>
      <option disabled class="option1" value ="">-- <?php echo JText::_('Task'); ?> --</option>
      <?php foreach ($this->task_type_array as $typename => $tasks) : ?>
        <option disabled class="option2" value =""><?php echo $typename; ?></option>
        <?php foreach ($tasks as $task) : ?>
          <?php $taskNums = count($tasks); ?>
          <?php $selected = (($log_task_id == $task->id) || ($taskNums == 1 && $typeNums == 1)) ? " selected" : ""; ?>
          <option value ="<?php echo $task->id; ?>"<?php echo $selected ?>>- <?php echo $task->name; ?></option>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </select><?
    exit();
  }

  function loadPriceInfo() {
    $todo = new Todo();
    $price = $todo->getHourlyRateByParams(JRequest::get('get'));

    print round($price, 2);

    jexit();
  }

}