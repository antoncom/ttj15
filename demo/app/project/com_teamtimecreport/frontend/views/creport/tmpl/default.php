<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('piecharts.css', "components/com_teamtimecreport/assets/css/");
?>


<div style="display:none;">
	<!-- need for include joomla calendar widget code -->
	<?=
	JHTML::_('calendar', date("Y-m-d"), 'tmp_date', 'tmp_date',
			JText::_('DATE_FORMAT_MYSQL_WITHOUT_TIME'))
	?>
</div>

<style type="text/css">
  /** Table styles **/

  table.adminlist {
    width: 100%;
    border-spacing: 1px;
    background-color: #e7e7e7;
    color: #666;
    font-size: 8pt;
  }

  table.adminlist td,
  table.adminlist th { padding: 4px; }

  table.adminlist thead th {
    text-align: center;
    background: #f0f0f0;
    color: #666;
    border-bottom: 1px solid #999;
    border-left: 1px solid #fff;
  }

  table.adminlist thead a:hover { text-decoration: none; }

  table.adminlist thead th img { vertical-align: middle; }

  table.adminlist tbody th { font-weight: bold; }

  table.adminlist tbody tr			{ background-color: #fff;  text-align: left; }
  table.adminlist tbody tr.row1 	{ background: #f9f9f9; border-top: 1px solid #fff; }

  table.adminlist tbody tr.row0:hover td,
  table.adminlist tbody tr.row1:hover td  { background-color: #ffd ; }

  table.adminlist tbody tr td 	   { height: 25px; background: #fff; border: 1px solid #fff; }
  table.adminlist tbody tr.row1 td { background: #f9f9f9; border-top: 1px solid #FFF; }

  table.adminlist tfoot tr { text-align: center;  color: #333; }
  table.adminlist tfoot td,
  table.adminlist tfoot th { background-color: #f3f3f3; border-top: 1px solid #999; text-align: center; }

  table.adminlist td.order 		{ text-align: center; white-space: nowrap; }
  table.adminlist td.order span { float: left; display: block; width: 20px; text-align: center; }

  table.adminlist .pagination { display:table; padding:0;  margin:0 auto;	 }

</style>

<script>
  var creport_process_table = function () {
    var $ = jQuery;

    var config = <?= $this->conf_data ?>;

    if (config.col_date == 0) {
      $(".col_date").hide();
    }

    if (config.col_project == 0) {
      $(".col_project").hide();
    }

    if (config.col_type == 0) {
      $(".col_type").hide();
    }

    if (config.col_task == 0){
      $(".col_task").hide();
    }

    if (config.col_todo == 0) {
      $(".col_todo").hide();
      $(".col_todo2").attr("colspan", 1);
    }

    if (config.col_log == 0) {
      $(".col_log").hide();
      if (config.col_todo == 0) {
        $(".col_todo2").hide();
      }
      else {
        $(".col_todo2").attr("colspan", 1);
      }
    }

    if (config.col_planned_actual_hours == 0) {
      $(".col_planned_actual_hours").hide();
    }

    if (config.col_actual_hours == 0) {
      $(".col_actual_hours").hide();
    }

    if (config.col_hourly_rate == 0) {
      $(".col_hourly_rate").hide();
    }

    if (config.col_planned_cost == 0) {
      $(".col_planned_cost").hide();
    }

    if (config.col_actual_cost == 0) {
      $(".col_actual_cost").hide();
    }

    if (config.col_statement_cost == 0) {
      $(".col_statement_cost").hide();
    }

    if (config.col_overhead_expenses == 0) {
      $(".col_overhead_expenses").hide();
    }

    if (config.col_user == 0) {
      $(".col_user").hide();
    }

		// init fancybox
		$(".fancybox").fancybox({
			type: 'iframe',
			width: 800,
			//height: 750,
			autoSize: false,
			padding : 1,
			openEffect: 'none',
			closeEffect: 'none',
			helpers : {
				overlay : {
					css : {
						'background' : 'rgba(0, 0, 0, 0.4)'
					}
				}
			}
		});
  };
</script>

<!--style>
  div.highslide-header {
    background-image: none;
  }
</style>
<script type="text/javascript">
  hs.graphicsDir = "<?= JURI::root() ?>components/com_teamtimecreport/assets/highslide/graphics/";
  hs.outlineType = "rounded-white";
  hs.wrapperClassName = "draggable-header";
  hs.showCredits = false;
  hs.width = 740;
  /*hs.dimmingOpacity = 0.4;
        hs.maxWidth = 800;
        hs.maxHeight = 200;
        hs.maxHeight = 600;
        hs.align = "auto";
        hs.allowWidthReduction = true;*/
</script-->

<div id="report-filter-area"></div>
<script type="text/javascript"
src="<?= $this->remote_url ?><?= $this->report_url ?>"></script>