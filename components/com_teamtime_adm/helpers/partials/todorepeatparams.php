<?php
if ($event) {
	$model = new TodoModelTodo();
	$repeat_params = $model->get_repeat_params($event->id);
	if ($repeat_params) {
		$repeat_params->start_date = JHTML::_('date', $repeat_params->start_date, '%Y-%m-%d %H:%M:%S');
		$tmp = explode(" ", TeamTime::php2JsTime(TeamTime::mySql2PhpTime($repeat_params->start_date)));
		$repeat_params->start_date = $tmp[0];

		if ($repeat_params->end_date == '0000-00-00 00:00:00')
			$repeat_params->end_date = "";
		else {
			$repeat_params->end_date = JHTML::_('date', $repeat_params->end_date, '%Y-%m-%d %H:%M:%S');
			$tmp = explode(" ", TeamTime::php2JsTime(TeamTime::mySql2PhpTime($repeat_params->end_date)));
			$repeat_params->end_date = $tmp[0];
		}
	}
}
?>
<div class="repeatParamsFieldset">
	<h2 style="margin-bottom:3px;"><label><input type="checkbox" name="repeat" id="repeat"
			<?=
			$event && $repeat_params ? "checked" : ""
			?>
																							 value="repeat" /><?= JText::_('Repeated Todo') ?></label></h2>
	<div id="repeating_params">
		<table width="100%">

			<tr>
				<td>
					<strong><?= JText::_('Repeating') ?>:</strong>
				</td>
				<td>
					<select size="1" name="repeating" id="repeating">
						<option value="monthly"
						<?=
						$event && $repeat_params && $repeat_params->repeat_mode == "monthly" ? "selected" : ""
						?>
										label="<?= JText::_('Every month') ?>"><?= JText::_('Every month') ?></option>
						<option value="weekly"
						<?=
						$event && $repeat_params && $repeat_params->repeat_mode == "weekly" ? "selected" : ""
						?>
										label="<?= JText::_('Every Week') ?>"><?= JText::_('Every Week') ?></option>
						<option value="yearly"
						<?=
						$event && $repeat_params && $repeat_params->repeat_mode == "yearly" ? "selected" : ""
						?>
										label="<?= JText::_('Every year') ?>"><?= JText::_('Every year') ?></option>
					</select>
				</td>

				<td><strong><?= JText::_('Repeating interval') ?>:</strong></td>
				<td nowrap nowrap="nowrap"><select size="1" name="repeat_interval" id="repeat_interval">
						<?
						foreach (range(1, 24) as $i) {
							?>
							<option value="<?= $i ?>"
							<?=
							$event && $repeat_params && $repeat_params->repeat_interval == $i ? "selected" : ""
							?>
											label="<?= $i ?>"><?= $i ?></option>
										<? } ?>
					</select>&nbsp;<? //=JText::_('REPEATING WEEKS')                ?></td>

				<td><strong><?= JText::_('Days of repeating') ?>:</strong></td>
				<td nowrap nowrap="nowrap"><input
						type="checkbox" name="mon" id="mon"
						<?=
						$event && $repeat_params && $repeat_params->repeat_mon == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('MON') ?>&nbsp;<input
						type="checkbox" name="tue" id="tue"
						<?=
						$event && $repeat_params && $repeat_params->repeat_tue == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('TUE') ?>&nbsp;<input
						type="checkbox" name="wed" id="wed"
						<?=
						$event && $repeat_params && $repeat_params->repeat_wed == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('WED') ?>&nbsp;<input
						type="checkbox" name="thu" id="thu"
						<?=
						$event && $repeat_params && $repeat_params->repeat_thu == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('THU') ?>&nbsp;<input
						type="checkbox" name="fri" id="fri"
						<?=
						$event && $repeat_params && $repeat_params->repeat_fri == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('FRI') ?>&nbsp;<input
						type="checkbox" name="sat" id="sat"
						<?=
						$event && $repeat_params && $repeat_params->repeat_sat == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('SAT') ?>&nbsp;<input
						type="checkbox" name="sun" id="sun"
						<?=
						$event && $repeat_params && $repeat_params->repeat_sun == 1 ? "checked" : ""
						?>
						value="1" /><?= JText::_('SUN') ?>&nbsp;</td>
			</tr>

			<tr>
				<td><strong><?= JText::_('Start date') ?>:</strong></td>
				<td><input type="text" name="start_date" id="start_date"
									 value="<?=
						$event && $repeat_params ? $repeat_params->start_date : $todo_date
						?>"
									 size="10" maxlength="10" /></td>

				<td colspan="4" nowrap>
					<strong><?= JText::_('End date') ?>:</strong>

					<input type="radio" id="end_date_type0"
								 name="end_date_type"
								 <?=
								 (($event && $repeat_params && $repeat_params->end_date == "") ||
										 !$repeat_params) ? "checked" : ""
								 ?>
								 value="0" /> <?= JText::_('REPEATING END NEVER') ?>&nbsp;&nbsp;<input type="radio"
								 id="end_date_type1" name="end_date_type"
								 <?=
								 $event && $repeat_params && $repeat_params->end_date != "" ? "checked" : ""
								 ?>
								 value="1" /><?= JText::_('REPEATING END UNTILL') ?>: <input type="text"
								 name="end_date" id="end_date"
								 value="<?=
								 $event && $repeat_params ? $repeat_params->end_date : ""
								 ?>"
								 size="10" maxlength="10" />
				</td>
			</tr>

			<tr>
				<td><strong><?= JText::_('REPEATING RESULT') ?>:</strong></td>
				<td colspan="5"><div id="repeating_result"><strong></strong></div></td>
			</tr>

		</table>
	</div>
</div>

<script>

	jQuery(function ($) {

		var change_repeating_params = function(){
			var s = "";

			if(!$("#repeat").attr('checked')){
				$("#repeating_result").html('<strong>' + s + '</strong>');
				return;
			}

			var repeating = $("#repeating option:selected")[0].text;
			var repeat_interval = $("#repeat_interval").val();

			if(repeat_interval != "1"){
				var tmp = repeating.split(" ");
				repeating = tmp[0] + " " + repeat_interval + " " + tmp[1];
			}
			s += repeating;

			var created_date = $("#stpartdate").val().split("/");
			var month_strs = [
				"",
				"<?= JText::_("JANUARY") ?>",
				"<?= JText::_("FEBRUARY") ?>",
				"<?= JText::_("MARCH") ?>",
				"<?= JText::_("APRIL") ?>",
				"<?= JText::_("MAY") ?>",
				"<?= JText::_("JUNE") ?>",
				"<?= JText::_("JULY") ?>",
				"<?= JText::_("AUGUST") ?>",
				"<?= JText::_("SEPTEMBER") ?>",
				"<?= JText::_("OCTOBER") ?>",
				"<?= JText::_("NOVEMBER") ?>",
				"<?= JText::_("DECEMBER") ?>"
			];

			var tmp_month_s = created_date[0].indexOf("0") == 0?
				created_date[0].replace("0", "") : created_date[0];
			s += ", " + month_strs[parseInt(tmp_month_s)];

			var week_days = [];

			if($("#mon").attr('checked'))
				week_days.push("<?= JText::_('MON') ?>");
			if($("#tue").attr('checked'))
				week_days.push("<?= JText::_('tue') ?>");
			if($("#wed").attr('checked'))
				week_days.push("<?= JText::_('wed') ?>");
			if($("#thu").attr('checked'))
				week_days.push("<?= JText::_('thu') ?>");
			if($("#fri").attr('checked'))
				week_days.push("<?= JText::_('fri') ?>");
			if($("#sat").attr('checked'))
				week_days.push("<?= JText::_('sat') ?>");
			if($("#sun").attr('checked'))
				week_days.push("<?= JText::_('sun') ?>");

			if(week_days.length > 0){
				s += ", " + week_days.join(", ") + ", <?= JText::_("REPEATING NEAR DAY") ?> ";
			}
			else{
				s += ", ";
			}

			var day_value = created_date[1].indexOf("0") == 0? created_date[1].replace("0", "") : created_date[1];
			if(week_days.length == 0 && $("#repeating").val() == "monthly"){
				var tmp_date = new Date(parseInt(created_date[2]), parseInt(tmp_month_s), 0);
				if(tmp_date.getDate() == parseInt(day_value))
					day_value = "<?= JText::_('REPEATING LAST') ?>";
			}

			s += day_value + " <?= JText::_("REPEATING DAY") ?>";

			if($("#end_date_type1").attr("checked") && $("#end_date").val() != "")
				s += " - <?= JText::_('REPEATING END UNTILL') ?> " + $("#end_date").val();

			$("#repeating_result").html('<strong>' + s + '</strong>');
		};

		//$("#start_date").change(change_repeating_params);
		//$("#end_date").change(change_repeating_params);

		$("#repeat").change(change_repeating_params);
		$("#repeating").change(change_repeating_params);
		$("#repeat_interval").change(change_repeating_params);
		$("#end_date_type0").change(change_repeating_params);
		$("#end_date_type1").change(change_repeating_params);

		$("#mon").change(change_repeating_params);
		$("#tue").change(change_repeating_params);
		$("#wed").change(change_repeating_params);
		$("#thu").change(change_repeating_params);
		$("#fri").change(change_repeating_params);
		$("#sat").change(change_repeating_params);
		$("#sun").change(change_repeating_params);

		change_repeating_params();

		if (!dateFormat || typeof (dateFormat) != "function") {
			var dateFormat = function(format) {
				var o = {
					"M+": this.getMonth() + 1,
					"d+": this.getDate(),
					"h+": this.getHours(),
					"H+": this.getHours(),
					"m+": this.getMinutes(),
					"s+": this.getSeconds(),
					"q+": Math.floor((this.getMonth() + 3) / 3),
					"w": "0123456".indexOf(this.getDay()),
					"S": this.getMilliseconds()
				};
				if (/(y+)/.test(format)) {
					format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
				}
				for (var k in o) {
					if (new RegExp("(" + k + ")").test(format))
						format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
				}
				return format;
			};
		}

		// repeated todo
		$("#start_date").datepicker({
			picker: "<button class='calpick'></button>",
			onReturn: function(dateText, inst){
				$("#start_date").val(dateFormat.call(dateText, i18n.datepicker.dateformat.fulldayvalue));
				change_repeating_params();
			}
		});

		$("#end_date").datepicker({
			picker: "<button class='calpick'></button>",
			onReturn: function(dateText, inst){
				$("#end_date").val(dateFormat.call(dateText, i18n.datepicker.dateformat.fulldayvalue));
				change_repeating_params();
			}
		});

	});

</script>