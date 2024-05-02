<?php
/**
 * Highslide Configuration Controller for the component
 *
 * @license		GNU/GPL
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * HsConfigs Controller
 */
class HsConfigsControllerHsConfig extends HsConfigsController
{
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
		$this->registerTask( 'copy', 'edit');
		$this->registerTask( 'apply', 'save' );
		$this->registerTask( 'pubsave', 'save');
		$this->registerTask( 'pubapply', 'save');
		$this->registerTask( 'close','cancel' );
	}

	/**
	 * display the edit form
	 * @return void
	 */
	function edit()
	{
		JRequest::setVar( 'view', 'hsconfig' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * save or apply changes to a record (and redirect to appropriate page)
	 * @return void
	 */
	function save()
	{
		$model = $this->getModel('hsconfig');
		$task 	= $this->getTask();
		$data = JRequest::get( 'post' );
		if ($data['id'] == 0)
		{
			$data['id'] = $data['cid'];
		}
		$id = $data['id'];
		$updated = ($model->store());

		switch ( $task )
		{
			case 'apply':
			case 'pubapply':
				if (($updated))
				{
					if ($task == 'pubapply')
					{
						if (!$model->publishOne($id))
						{
							$msg = JText::_( 'Error Applying Configuration') . ' - ' . $model->getError();
						}
						else
						{
							$msg = JText::_('Configuration Applied/Published') .'!';
						}
					}
					else
					{
						$msg = JText::_('Configuration Applied') .'!';
					}
				}
				else
				{
					$msg = JText::_( 'Error Applying Configuration') . ' - ' . $model->getError();
				}
				$link = 'index.php?option=com_hsconfig&controller=hsconfig&task=edit&cid[]='.$id;
				break;

			case 'save':
			case 'pubsave':
			default:
				if (($updated))
				{
					if ($task == 'pubsave')
					{
						if (!$model->publishOne($id))
						{
							$msg = JText::_( 'Error Saving Configuration') . ' - ' . $model->getError();
						}
						else
						{
							$msg = JText::_('Configuration Saved/Published') .'!';
						}
					}
					else
					{
						$msg = JText::_('Configuration Saved') .'!';
					}
				}
				else
				{
					$msg = JText::_( 'Error Saving Configuration') . ' - ' . $model->getError();
				}
				$link = 'index.php?option=com_hsconfig';
				break;
		}
		$this->setRedirect($link, $msg);
	}

	/**
	 * remove record(s)
	 * @return void
	 */
	function remove()
	{
		$model = $this->getModel('hsconfig');
		if(!$model->delete()) {
			$msg = JText::_( 'Error: One or More Configurations Could not be Deleted'.' - '.$model->getError() );
		} else {
			$msg = JText::_( 'Configuration(s) Deleted' );
		}

		$this->setRedirect( 'index.php?option=com_hsconfig', $msg );
	}

	/**
	 * cancel editing a record
	 * @return void
	 */
	function cancel()
	{
		$task 	= $this->getTask();

		if ($task == 'cancel')
		{
			$msg = JText::_( 'Operation Cancelled' );
			$this->setRedirect( 'index.php?option=com_hsconfig', $msg );
		}
		else
		{
			$this->setRedirect( 'index.php?option=com_hsconfig' );
		}

	}

	function unpublish()
	{
		$model = $this->getModel('hsconfig');
		if(!$model->unpublish())
		{
			$msg = JText::_( 'Error: One or More Configurations Could not be Unpublished'.' - '.$model->getError() );
		}
		else
		{
			$msg = JText::_( 'Configuration(s) Unpublished' );
		}

		$this->setRedirect( 'index.php?option=com_hsconfig', $msg );
	}

	function publish()
	{
		$model = $this->getModel('hsconfig');
		if(!$model->publish())
		{
			$msg = JText::_( 'Error: One or More Configurations Could not be Published'.' - '.$model->getError() );
		}
		else
		{
			$msg = JText::_( 'Configuration(s) Published' );
		}

		$this->setRedirect( 'index.php?option=com_hsconfig', $msg );
	}
}
?>
