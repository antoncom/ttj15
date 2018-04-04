<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div style="display:none;">
	<!-- need for include joomla calendar widget code -->
	<?=
	JHTML::_('calendar', date("Y-m-d"), 'tmp_date', 'tmp_date',
			JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME'))
	?>
</div>

<!-- report content data -->

<div id="report-filter-area"></div>
<script src="<?= $this->reportUrl ?>"></script>

<!-- /report content data -->