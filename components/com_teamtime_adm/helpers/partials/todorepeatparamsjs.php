<script>

	function check_repeat_event(todo_id, f_repeat, f_norepeat) {
		var $ = jQuery;

		$.get("/administrator/index.php?option=com_teamtime&controller=todo&task=check_repeat_event"+
			"&todo_id=" + todo_id,
		function(data){
			if(data == "1")
				f_repeat();
			else
				f_norepeat();
		});
	}

	function delete_repeat_events_box(todo_id, current_date, ondelete) {
		var $ = jQuery;

		var title = "<?= JText::_("REPEATING DELETE BOX TITLE") ?>";
		var content = [
			"<div>",
			"<input type='button' value='<?= JText::_("REPEATING ONLY THIS TODO") ?>' id='only_repeat_todo'>",
			"<input type='button' value='<?= JText::_("REPEATING ALL THIS TODO") ?>' id='all_repeat_todo'>",
			"<input type='button' value='<?= JText::_("REPEATING REST THIS TODO") ?>' id='rest_repeat_todo'>",
			"<input type='button' value='<?= JText::_("REPEATING CANCEL THIS TODO") ?>' id='cancel_repeat_todo'>",
			"</div>"
		].join("&nbsp;&nbsp;&nbsp;");

		var d = [current_date.getFullYear(), current_date.getMonth() + 1, current_date.getDate()].join("-") + " " +
			[current_date.getHours(), current_date.getMinutes(), current_date.getSeconds()].join(":");

		hiBox(content, title, null, null,
		function(){
			$("#all_repeat_todo").click(function(){
				$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=remove_all_repeat_todo"+
					"&id=" + todo_id +
					"&current_date=" + d, ondelete);
				$.alerts._hide();
			});

			$("#rest_repeat_todo").click(function(){
				$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=remove_rest_repeat_todo"+
					"&id=" + todo_id +
					"&current_date=" + d, ondelete);
				$.alerts._hide();
			});

			$("#only_repeat_todo").click(function(){
				$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=remove_only_repeat_todo"+
					"&id=" + todo_id +
					"&current_date=" + d, ondelete);
				$.alerts._hide();
			});

			$("#cancel_repeat_todo").click(function(){
				$.alerts._hide();
			});
		});
	}

	function edit_repeat_events_box(todo_id, current_date, onedit) {
		var $ = jQuery;

		var title = "<?= JText::_("REPEATING EDIT BOX TITLE") ?>";
		var content = [
			"<div>",
			"<input type='button' value='<?= JText::_("REPEATING ONLY THIS TODO") ?>' id='only_repeat_todo'>",
			"<input type='button' value='<?= JText::_("REPEATING ALL THIS TODO") ?>' id='all_repeat_todo'>",
			"<input type='button' value='<?= JText::_("REPEATING REST THIS TODO") ?>' id='rest_repeat_todo'>",
			"<input type='button' value='<?= JText::_("REPEATING CANCEL THIS TODO") ?>' id='cancel_repeat_todo'>",
			"</div>"
		].join("&nbsp;&nbsp;&nbsp;");

		var d = [current_date.getFullYear(), current_date.getMonth() + 1, current_date.getDate()].join("-") + " " +
			[current_date.getHours(), current_date.getMinutes(), current_date.getSeconds()].join(":");

		hiBox(content, title, null, null,
		function(){
			$("#all_repeat_todo").click(function(){
				//$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=edit_all_repeat_todo"+
				//	"&id=" + todo_id +
				//	"&current_date=" + d, onedit);
				$("#fmEdit").submit();
				$.alerts._hide();
			});

			$("#rest_repeat_todo").click(function(){
				$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=edit_rest_repeat_todo"+
					"&id=" + todo_id +
					"&current_date=" + d, function(data){
					var d = [current_date.getMonth() + 1, current_date.getDate(), current_date.getFullYear()].join("/");
					$("#stpartdate").val(d);
					$("#start_date").val(d);
					$("#fmEdit").attr("action",
					$("#fmEdit").attr("action").replace("&id=" + todo_id, ""));
					$("#fmEdit").submit();
				});
				$.alerts._hide();
			});

			$("#only_repeat_todo").click(function(){
				$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=edit_only_repeat_todo"+
					"&id=" + todo_id +
					"&current_date=" + d, function(data){
					var d = [current_date.getMonth() + 1, current_date.getDate(), current_date.getFullYear()].join("/");
					$("#stpartdate").val(d);
					$("#start_date").val(d);

					// set flag for todo - was repeat
					if($("#repeat").attr('checked')) {
						$("#was_repeat").val("1");
					}
					// reset repeat checkbox
					$("#repeat").removeAttr("checked");

					$("#fmEdit").attr("action",
					$("#fmEdit").attr("action").replace("&id=" + todo_id, ""));
					$("#fmEdit").submit();
				});
				$.alerts._hide();
			});

			$("#cancel_repeat_todo").click(function(){
				$.alerts._hide();
			});
		});
	}

	function exclude_repeat_events_box(todo_id, new_date, old_date, onmove) {
		var $ = jQuery;

		var title = "<?= JText::_("Exclude todo from repeated") ?>";
		var data = null;

		$.alerts.okButton = "Ok";
		$.alerts.cancelButton = "Cancel";
		hiConfirm(title, 'Confirm',
		function(confrm_result){
			$.alerts._hide();

			if (confrm_result) {
				$.post("/administrator/index.php?option=com_teamtime&controller=todo&task=exclude_repeat_todo",
				{todo_id: todo_id, new_date: new_date, old_date: old_date},
				function(data){
					$("#gridcontainer").reload();
				});
			}

			onmove(data);
		});
	}

</script>