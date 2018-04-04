
function submitStateChange(pressbutton, id) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	$('state_change_id').setProperty('value',id);
	submitform( pressbutton );
}

/*
function submitbutton(pressbutton) {
	var $ = jQuery;

	if (pressbutton == "remove") {
		var taskIds = [];
		$("input[name='cid[]']").each(function (i, n) {
			if (n.checked) {
				taskIds.push(n.value);
			}
		});

		TeamTime.Base.checkUsedTask(taskIds, function () {
			submitform(pressbutton);
		});
	}
	else {
		submitform(pressbutton);
	}
}*/

jQuery(function($) {

	$('#adminForm_reset').click(function () {
		$('#search').val('');

		$('#filter_state').val('');
		$('#filter_project').val('');
		$('#filter_type').val('');

		$('#filter_target_id').val('');

		$('#adminForm').submit();

	});

});