<?php

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($this->todo && $this->todo->description) : ?>
<div class="project-description">
	<h1><?php echo $this->todo->title;?></h1>
	<?php echo $this->todo->description;?>	
</div>
<?php endif; ?>