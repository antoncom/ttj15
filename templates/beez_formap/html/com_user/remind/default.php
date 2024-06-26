<?php // @version $Id: default.php$
defined('_JEXEC') or die('Restricted access');
?>
<?php if($this->params->get('show_page_title',1)) : ?>
<h2 class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
	<?php echo $this->escape($this->params->get('page_title')) ?>
</h2>
<?php endif; ?>
<h1 class="componentheading"><?php echo JText::_('FORGOT_YOUR_USERNAME'); ?></h1>

<form action="index.php?option=com_user&amp;task=remindusername" method="post" class="josForm form-validate">
	<p><?php echo JText::_('REMIND_USERNAME_DESCRIPTION'); ?></p>

	<label for="email" class="hasTip" title="<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TITLE'); ?>::<?php echo JText::_('REMIND_USERNAME_EMAIL_TIP_TEXT'); ?>"><?php echo JText::_('Email Address'); ?>:</label>
	<input id="email" name="email" type="text" class="required validate-email" />

	<button type="submit" class="validate"><?php echo JText::_('Submit'); ?></button>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
