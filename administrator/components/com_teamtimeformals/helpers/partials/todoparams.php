<?php
$db = & JFactory::getDBO();

$mark_expenses = "";
$mark_hours_plan = "";
if ($item) {
	$db->setQuery("SELECT * FROM  `#__teamtimeformals_todo` as a
			WHERE a.todo_id = " . (int) $item->id);
	$row = $db->loadObject();
	if ($row) {
		$mark_expenses = $row->mark_expenses ? " checked " : "";
		$mark_hours_plan = $row->mark_hours_plan ? " checked " : "";
	}
}
?>
<? if ($display == "standart") { ?>

	<tr>
		<td width="110" class="key">
			<label for="mark_hours_plan">
				<?php echo JText::_('Include the planned hours into statement to be paid'); ?>
			</label>
		</td>
		<td>
			<input type="checkbox" id="mark_hours_plan"
						 name="mark_hours_plan" value="1" <?= $mark_hours_plan ?>>
		</td>
	</tr>
	<tr>
		<td width="110" class="key">
			<label for="mark_expenses">
				<?php echo JText::_('Include the overhead expenses into statement to be paid'); ?>:
			</label>
		</td>
		<td>
			<input type="checkbox" id="mark_expenses"
						 name="mark_expenses" value="1" <?= $mark_expenses ?>>
		</td>
	</tr>

	<?
}
else if ($display == "hoursplan") {
	?>

	<span><input type="checkbox" id="mark_hours_plan"
							 name="mark_hours_plan" value="1" <?= $mark_hours_plan ?>>
		<?php echo JText::_('Include into statement to be paid'); ?></span>

	<?
}
else if ($display == "expenses") {
	?>

	<span><input type="checkbox" id="mark_expenses"
							 name="mark_expenses" value="1" <?= $mark_expenses ?>>
		<?php echo JText::_('Include into statement to be paid'); ?></span>

<? } ?>
