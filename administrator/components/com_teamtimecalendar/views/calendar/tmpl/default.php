<?php
defined('_JEXEC') or die('Restricted access');
?>

<style>
  @import url('/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/calendar.css');
	@import url('/<?= URL_MEDIA_COMPONENT_ASSETS ?>css/main.css');
</style>

<div id="main_calendar_block">

  <div id="calhead" style="padding-left:1px;padding-right:1px;">
    <div class="cHead">
      <div class="ftitle" style="font-size:17px; float:left;"><?= JText::_("CALENDAR_TITLE") ?></div>

      <div id="caltoolbar" class="ctoolbar" style="font-weight:normal; font-size:12px;">
        <div id="faddbtn" class="fbutton">
          <div><span title='Click to Create New Event' class="addcal">&nbsp;<?php echo JText::_('New Event'); ?></span></div>
        </div>

        <div id="showtodaybtn" class="fbutton">
          <div><span title='Click to back to today ' class="showtoday"><?php echo JText::_('Today'); ?></span></div>
        </div>

        <div id="showdaybtn" class="fbutton<?=
$this->view_type == "day" ? " fcurrent" : ""
?>">
          <div><span title='Day' class="showdayview"><?php echo JText::_('Day'); ?></span></div>
        </div>

        <div  id="showweekbtn" class="fbutton<?=
						 $this->view_type == "week" ? " fcurrent" : ""
?>">
          <div><span title='Week' class="showweekview"><?php echo JText::_('Week'); ?></span></div>
        </div>

        <div  id="showmonthbtn" class="fbutton<?=
							$this->view_type == "month" ? " fcurrent" : ""
?>">
          <div><span title='Month' class="showmonthview"><?php echo JText::_('Month'); ?></span></div>
        </div>

        <div  id="showreflashbtn" class="fbutton">
          <div><span title='Refresh view' class="showdayflash"><?php echo JText::_('Refresh'); ?></span></div>
        </div>

        <!--div class="btnseparator"></div-->

        <div id="sfprevbtn" title="Prev"  class="fbutton">
          <span class="fprev"></span>
        </div>
        <div id="sfnextbtn" title="Next" class="fbutton">
          <span class="fnext"></span>
        </div>
        <div class="fshowdatep fbutton">
          <div>
            <input type="hidden" name="txtshow" id="hdtxtshow" />
            <span id="txtdatetimeshow"><?php echo JText::_('Loading'); ?></span>
          </div>
        </div>

        <div class="clear"></div>
      </div>

      <div id="loadingpannel" class="ptogtitle loadicon" style="display: none;"><?php echo JText::_('Loading data'); ?>...</div>
      <div id="errorpannel" class="ptogtitle loaderror" style="display: none;">Sorry, could not load your data, please try again later</div>
    </div>

    <div id="caltoolbar" class="ctoolbar">
      <div  class="fbutton">
        <div id="projects_list">
					<?= $this->lists['select_project'] ?>
        </div>
      </div>

      <div   class="fbutton">
        <div  id="types_list">
					<?= $this->lists['select_type'] ?>
        </div>
      </div>

      <div  class="fbutton">
        <div  id="tasks_list">
					<?= $this->lists['select_task'] ?>
        </div>
      </div>

      <div  class="fbutton">
        <div><span title='Users'>
						<?= $this->lists['select_user'] ?>
          </span></div>
      </div>

      <div  class="fbutton">
        <div><span title='Periods'>
						<?= $this->lists['select_period'] ?>
          </span></div>
      </div>

      <div class="fbutton">
        <div style="padding-top:0px;"><span title='Suborders'>
            <input id="hidesuborders" type="checkbox" style="margin-top:2px;">
            <label id="lbl-hidesuborders" for="hidesuborders"><?= JText::_("Hide completed") ?></label>
          </span></div>
      </div>
    </div>

  </div>

  <div style="padding:1px;">
    <div class="t1 chromeColor">&nbsp;</div>

    <div class="t2 chromeColor">&nbsp;</div>

    <div id="dvCalMain" class="calmain printborder">
      <div id="gridcontainer" style="overflow-y: visible;"> </div>
    </div>

    <div class="t2 chromeColor">&nbsp;</div>

    <div class="t1 chromeColor">&nbsp;</div>
  </div>

</div>

<? TeamTime::helper()->getBase()->getRepeatTodoEditcode() ?>