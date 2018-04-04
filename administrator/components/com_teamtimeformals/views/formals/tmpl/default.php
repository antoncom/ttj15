<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php echo $this->print_popup_script ?>

<form action="index.php" method="post" name="adminForm">
  <table>
    <tr>
      <td align="left" width="100%">
				<?php echo JText::_('Filter'); ?>:
        <input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onchange="document.adminForm.submit();" />
        <button onclick="this.form.submit();"><?php echo JText::_('Go'); ?></button>
        <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_type').value='';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
      </td>
      <td nowrap="nowrap">
				<?php echo $this->lists['select_using']; ?>
				<?php echo $this->lists['select_project']; ?>
				<?php echo $this->lists['select_type']; ?>
      </td>
    </tr>
  </table>
  <div id="tablecell">
    <table class="adminlist">
      <thead>
        <tr>
          <th width="5">
						<?php echo JText::_('NUM'); ?>
          </th>
          <th width="20">
            <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
          </th>
          <th  class="title" width="100%">
						<?php echo JText::_('FORMAL DOCUMENT NAME'); ?>
          </th>

          <th nowrap="nowrap">
						<?php echo JText::_('Using in'); ?>
          </th>

          <th nowrap="nowrap">
						<?php
						echo JHTML::_('grid.sort', 'FORMAL DOCUMENT SUM', 'a.price',
								@$this->lists['order_Dir'], @$this->lists['order']);
						?>
          </th>

          <th nowrap="nowrap">
						<?php
						echo JHTML::_('grid.sort', 'FORMAL DOCUMENT CREATED', 'a.created',
								@$this->lists['order_Dir'], @$this->lists['order']);
						?>
          </th>

          <th nowrap="nowrap">
						<?php echo JText::_('Formals Template'); ?>
          </th>

          <th width="1%" nowrap="nowrap">
						<?php
						echo JHTML::_('grid.sort', 'ID', 'a.id', @$this->lists['order_Dir'],
								@$this->lists['order']);
						?>
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
						<?php echo $this->pagination->getListFooter(); ?>
          </td>
        </tr>
      </tfoot>
      <tbody>
				<?php
				$k = 0;
				for ($i = 0, $n = count($this->items); $i < $n; $i++) {
					$row = &$this->items[$i];
					$link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&view=type&task=edit&cid[]=' . $row->id);
					$checked = JHTML::_('grid.id', $i, $row->id);
					?>
					<tr class="<?php echo "row$k"; ?>">
						<td>
							<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td align="center">
							<?php echo $checked; ?>
						</td>
						<td>
							<span class="editlinktip hasTip" title="<?php echo JText::_('Edit Formal Document'); ?>::<?php echo $row->name; ?>">
								<a href="<?php echo $link ?>"><?php echo $row->name; ?></a>
							</span>
						</td>

						<td align="left">
							<?=
							$row->using_in == "project" ? $row->project_name : $row->user_name;
							?>
						</td>


						<td align="right" style="padding-right:20px;">
							<?php echo $row->price; ?>
						</td>

						<td align="left" style="padding-left:10px;">
							<?php
							echo JHTML::_('date', $row->created, "%d.%m.%Y");
							?>
						</td>

						<td align="left" style="padding-left:10px;">
							<?php echo JText::_($row->template_name); ?>
						</td>

						<td align="center">
							<?php echo $row->id; ?>
						</td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
      </tbody>
    </table>
  </div>
	
	<!--input type="hidden" id="filter_using" name="filter_using" value="<?= $this->filter_using ?>" /-->

  <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
  <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />  
	<?php echo JHTML::_('form.token'); ?>

</form>

<?php
$url = "index.php?option=" . $this->option .
		"&controller=" . $this->controller .
		"&task=print" .
		"&format=raw&cid[]=";
?>

<script>

  function submitbutton(pressbutton) {
    if (pressbutton == "print") {
      jQuery("input[name='cid[]']").each(function (i, n) {
        if (n.checked) {
          window.open("<?php echo $url; ?>" + n.value);
        }
      });
    }
    else {
      submitform(pressbutton);
    }
  }

	jQuery(function ($) {

		var url = "index.php?option=<?= $this->option ?>&controller=variable&task=loadusings";

		var loadUsings = function () {
			var currentType = $("#filter_project option:selected").val();

			$.get(url, {
				filter_using: $("#filter_using option:selected").val()
			},
			function (data) {
				$("#filter_project").html(data);

				$("#filter_project option").each(function (i, n) {
					if ($(n).val() == currentType) {
						$(n).attr("selected", true);
					}
				});
			});
		};

		$("#filter_using").change(function () {
			loadUsings();
		});

		loadUsings();

	});

	jQuery(function ($) {

		var url = "index.php?option=<?= $this->option ?>&controller=formal&task=load_templates&byname=1";

		var loadTemplates = function () {
			var currentType = $("#filter_type option:selected").val();
			
			$.get(url, {
				filter_using: $("#filter_using option:selected").val()
			},
			function (data) {
				$("#filter_type").html(data);

				$("#filter_type option").each(function (i, n) {
					if ($(n).val() == currentType) {
						$(n).attr("selected", true);
					}
				});
			});
		};

		$("#filter_using").change(function () {
			loadTemplates();
		});

		loadTemplates();
	});

</script>