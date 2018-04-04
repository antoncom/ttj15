<?php



/*----------------------------------------------------------------------
#Youjoomla YJ Booking Module 1.0
# ----------------------------------------------------------------------
# Copyright (C) 2007 You Joomla. All Rights Reserved.
# Coded by: NEO
# License: Youjoomla LLC
# Website: http://www.youjoomla.com
------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.');

$destination_pack_legend = $params->get( 'destination_pack_legend',"Choose your destination:" );
$destination_pack_label = $params->get( 'destination_pack_label',"Destination Package:" );
$personal_info_legend = $params->get( 'personal_info_legend',"Personal Information" );
$first_name_label = $params->get( 'first_name_label',"Name:" );
$name_validator = $params->get( 'name_validator' );
$surname_label = $params->get( 'surname_label',"Surname:" );
//$surname_validator = $params->get( 'surname_validator' );
$email_label = $params->get( 'email_label',"E-mail:" );
$email_validator = $params->get( 'email_validator' );
$phone_label = $params->get( 'phone_label',"Phone:" );
$phone_validator = $params->get( 'phone_validator' );
$arrival_legend = $params->get( 'arrival_legend',"Check-in:" );
$day_of_arrival_label = $params->get( 'day_of_arrival_label',"Day:" );
$month_of_arrival_label = $params->get( 'month_of_arrival_label',"Month:" );
$year_of_arrival_label = $params->get( 'year_of_arrival_label',"Year" );
$departure_legend = $params->get( 'departure_legend',"Check-out:" );
$day_of_departure_label = $params->get( 'day_of_departure_label',"Day:" );
$month_of_departure_label = $params->get( 'month_of_departure_label',"Month" );
$year_of_departure_label = $params->get( 'year_of_departure_label',"Year:" );
$accomodation_legend = $params->get( 'accomodation_legend',"Accomodation" );
$type_of_room_label = $params->get( 'type_of_room_label',"Type of Room:" );
$number_of_guests_label = $params->get( 'number_of_guests_label',"Количество гостей:" );
$number_of_guests_validator = $params->get( 'number_of_guests_validator' );
$additional_info_legend = $params->get( 'additional_info_legend',"Additional Information" );
$submit_button = $params->get( 'submit_button',"Submit Form" );
$reset_button = $params->get( 'reset_button',"Reset Form" );

$your_email = $params->get( 'your_email',"change@toyourmail.com" );
$SMTP_email = $params->get( 'SMTP_email',"your@smtpusername.com" );
$email_subject = $params->get( 'email_subject',"Booking from site" );

$show_accordion = $params->get( 'show_accordion',0 );
$ismooloaded = $params->get ( 'ismooloaded',0 );
$compress = $params->get ( 'compress',0 );
$sentmsg1 =  $params->get ( 'sentmsg1',"Thank you for your inquiry about." );
$sentmsg2 =  $params->get ( 'sentmsg2',"We will contact you back." );

if($compress == 1){
	$mooext = 'php';
}else{
	$mooext = 'css';
}

?>
<style type="text/css">
<!--
.fh {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 13px;
	font-weight: bold;
	color: #004c02;
}
.err {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #FF0000;
	margin-bottom: 20px;
	padding-bottom: 20px;
}
.tdh {	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
	font-weight: bold;
	color: #9d9d9d;
	background-color: #FFFFFF;
}
.tdt {	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	color: #000000;
	background-color: #FFFFFF;
}
.tt {	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
}
a.cl {	font-family: Arial, Helvetica, sans-serif;
	font-size: 18px;
	font-weight: bold;
	color: #a4a4a4;
	text-decoration: underline;
}
.fs {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
form {
	margin: 0px;
	padding: 0px;
}
-->
</style>
<?
//  do not edit below this line

$successOrder = false;
include ("modules/mod_yj_booking/getmail.php");

//get the titles



function getTitles($params){

	$now 		    = date('Y-m-d H:i:s');
	$database 	=& JFactory::getDBO();
	$nullDate 	= $database->getNullDate();


	$work_section=$params->get('work_section',1);
	$query = 'SELECT title ,sectionid, id FROM #__content WHERE (state=1) and (sectionid='.$work_section.') ORDER by ordering';
	$database->setQuery($query);
	$titles = $database->loadObjectList();
	//echo "<option value=\"".$titles[0]->title."\" selected=\"selected\" >".$titles[0]->title."</option>";
	//array_shift($titles);
	foreach ( $titles as $row ) {

		if($_POST['destination_pack']==$row->title) $selectFild = "selected";
		echo "<option value=\"$row->title\" $selectFild >".$row->title."</option>";
		$selectFild = "";

	}

}


?>



<script type="text/JavaScript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script>


<script type="text/javascript">
var formlanguage = "en-US"; // set default form language

function submitform(formname)
{
    document.forms[formname].submit();
}
MM_preloadImages('/modules/mod_yj_booking/newfiles/bb2.gif');

function changeDays(element_month, element_days, element_year) {
        var number_of_days = 0;
        switch (element_month.selectedIndex) {
                case 0: //jan
                case 2: //mar
                case 4: //may
                case 6: //jul
                case 7: //aug
                case 9: //oct
                case 11: //dec
                        number_of_days = 31;
                        break;
                case 3: //apr
                case 5: //jun
                case 8: //sep
                case 10: //nov
                        number_of_days = 30;
                        break;
                case 1: //feb
                        number_of_days = element_year.value % 4 == 0 ? 29 : 28;
                        break;
                default:
                        number_of_days = 31;
                        break;
        }
        var selected = element_days.value;
        element_days.options.length = 0;
        for (var i = 1; i <= number_of_days; i++) {
                element_days.options[element_days.options.length] = new Option(i, i);
                if (selected == i) {
                        element_days.selectedIndex = i - 1;
                }
        }
}
</script>

<!-- reservation form begin -->

<? 

if(empty($_POST['number_of_guests'])&&$_POST['number_of_guests']<>0) $_POST['number_of_guests']=2;


if(isset($_SERVER['HTTP_REFERER'])&&!isset($_POST['destination_pack'])){
//	if(preg_match("/\&id\=(\d+)/", $_SERVER['HTTP_REFERER'], $temp)){
	if(preg_match("/([a-zA-Z0-9]+)(\.html)/", $_SERVER['HTTP_REFERER'], $temp)){
		$ARTID = $temp[0];
		if($ARTID=="1m.html") $_POST['destination_pack']= "Одноместный - 900 р.";
		if($ARTID=="2m.html") $_POST['destination_pack']= "Двухместный - 1700 р.";
		if($ARTID=="studio.html") $_POST['destination_pack']= "Номер-студия - 2400 р.";
		if($ARTID=="halflux.html") $_POST['destination_pack']= "Полулюкс - 2700 р.";
		if($ARTID=="3k.html") $_POST['destination_pack']= "Полулюкс 3-х комн. - 3300 р.";
	}
	
}




if(!$successOrder){
?>

	<form style="margin-left:25px!important; margin-left:15px;" id="ReservationForm"  method="post" action="<?php echo /* stayThere(); */ "/utility/request.html?a=".time(); ?>"  onsubmit="return validate();" onReset="resetForm();">
<input name="action" type="hidden" id="action" value="sendform">
	<table width="690" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><img src="/modules/mod_yj_booking/newfiles/ht.gif" width="328" height="29" /><br />
    <img src="/modules/mod_yj_booking/newfiles/1.gif" width="1" height="30" align="absmiddle"/> <? echo $ERROR_MESSAGE ?></td>
  </tr>
</table>
<table width="690" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="230" valign="top"><table width="230" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="25">&nbsp;</td>
        <td width="210"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center" bgcolor="#00590f"><img src="/modules/mod_yj_booking/newfiles/im1.gif" width="111" height="16" /></td>
          </tr>
          <tr>
            <td><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="8" /><br><select name="destination_pack" id="destination_pack" style="width: 100%;" class="fs">
                      <?php getTitles($params); ?>
                    </select>
              <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="12" /><br />
              <span class="fh">Количество гостей:</span><? if(isset($ERROR_DATA['number_of_guests'])) echo "<img src='/modules/mod_yj_booking/newfiles/er.gif' alt='".$ERROR_DATA['number_of_guests']."' title='".$ERROR_DATA['number_of_guests']."' hspace='4'>" ?><br />
              <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="7" /><br />
			  <input type="text" name="number_of_guests" id="number_of_guests" size="3" maxlength="3" value="<? echo $_POST['number_of_guests'] ?>" onKeyUp="hideWarning('number_of_guests_validator');"  class="fs" />
			  <br />
			  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="12" /><br />
			  <span class="fh">Фамилия И.О. гостя:</span><? if(isset($ERROR_DATA['FIO_guest'])) echo "<img src='/modules/mod_yj_booking/newfiles/er.gif' alt='".$ERROR_DATA['FIO_guest']."' title='".$ERROR_DATA['FIO_guest']."' hspace='4'>" ?><br />
			  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="7" /><br />
			  <input type="text" name="FIO_guest" id="FIO_guest" size="10" maxlength="64" value="<? echo $_POST['FIO_guest'] ?>" onKeyUp="hideWarning('guestFIO_validator');"  class="fs" style="width: 190px;"/>
			  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="12" /><br />
			  <span class="fh">Вид оплаты:</span><br />
			  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="7" /><br />
			  <select  name="pay_type" id="pay_type" class="fs">
						<option value="Наличными" <?php if (!isset($_POST['pay_type']) || $_POST['pay_type'] == 'Наличными') echo 'selected';?>>Наличными</option>
						<option value="Перечислением" <?php if ($_POST['pay_type'] == 'Перечислением') echo 'selected';?>>Перечислением</option>
			    </select>
			  <br />
			  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="20" /></td>
          </tr>
		  <tr>
                    <td align="center" bgcolor="#00590f"><img src="/modules/mod_yj_booking/newfiles/im4.gif" width="129" height="16" /></td>
              </tr>
        </table></td>
        <td width="25">&nbsp;</td>
      </tr>
    </table></td>
    <td width="1" valign="top" background="/modules/mod_yj_booking/newfiles/vl.gif"><img src="/modules/mod_yj_booking/newfiles/vl.gif" width="1" height="8" /></td>
    <td width="460" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="230" valign="top"><table width="230" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="25"><img src="/modules/mod_yj_booking/newfiles/1.gif" width="25" height="1" /></td>
              <td width="180"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td align="center" bgcolor="#00590f"><img src="/modules/mod_yj_booking/newfiles/im2.gif" width="101" height="16" /></td>
                  </tr>
                  <tr>
                    <td><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="8" /><br />
                      <nobr><select name="day_of_arrival" id="day_of_arrival" class="fs">
                        <option value="1"<?php if($_POST['day_of_arrival']==1 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "31")) echo 'selected="selected"'; ?>>1</option>
                        <option value="2"<?php if($_POST['day_of_arrival']==2 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "1")) echo 'selected="selected"'; ?>>2</option>
                        <option value="3"<?php if($_POST['day_of_arrival']==3 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "2")) echo 'selected="selected"'; ?>>3</option>
                        <option value="4"<?php if($_POST['day_of_arrival']==4 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "3")) echo 'selected="selected"'; ?>>4</option>
                        <option value="5"<?php if($_POST['day_of_arrival']==5 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "4")) echo 'selected="selected"'; ?>>5</option>
                        <option value="6"<?php if($_POST['day_of_arrival']==6 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "5")) echo 'selected="selected"'; ?>>6</option>
                        <option value="7"<?php if($_POST['day_of_arrival']==7 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "6")) echo 'selected="selected"'; ?>>7</option>
                        <option value="8"<?php if($_POST['day_of_arrival']==8 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "7")) echo 'selected="selected"'; ?>>8</option>
                        <option value="9"<?php if($_POST['day_of_arrival']==9 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "8")) echo 'selected="selected"'; ?>>9</option>
                        <option value="10"<?php if($_POST['day_of_arrival']==10 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "9")) echo 'selected="selected"'; ?>>10</option>
                        <option value="11"<?php if($_POST['day_of_arrival']==11 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "10")) echo 'selected="selected"'; ?>>11</option>
                        <option value="12"<?php if($_POST['day_of_arrival']==12 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "11")) echo 'selected="selected"'; ?>>12</option>
                        <option value="13"<?php if($_POST['day_of_arrival']==13 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "12")) echo 'selected="selected"'; ?>>13</option>
                        <option value="14"<?php if($_POST['day_of_arrival']==14 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "13")) echo 'selected="selected"'; ?>>14</option>
                        <option value="15"<?php if($_POST['day_of_arrival']==15 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "14")) echo 'selected="selected"'; ?>>15</option>
                        <option value="16"<?php if($_POST['day_of_arrival']==16 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "15")) echo 'selected="selected"'; ?>>16</option>
                        <option value="17"<?php if($_POST['day_of_arrival']==17 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "16")) echo 'selected="selected"'; ?>>17</option>
                        <option value="18"<?php if($_POST['day_of_arrival']==18 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "17")) echo 'selected="selected"'; ?>>18</option>
                        <option value="19"<?php if($_POST['day_of_arrival']==19 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "18")) echo 'selected="selected"'; ?>>19</option>
                        <option value="20"<?php if($_POST['day_of_arrival']==20 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "19")) echo 'selected="selected"'; ?>>20</option>
                        <option value="21"<?php if($_POST['day_of_arrival']==21 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "20")) echo 'selected="selected"'; ?>>21</option>
                        <option value="22"<?php if($_POST['day_of_arrival']==22 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "21")) echo 'selected="selected"'; ?>>22</option>
                        <option value="23"<?php if($_POST['day_of_arrival']==23 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "22")) echo 'selected="selected"'; ?>>23</option>
                        <option value="24"<?php if($_POST['day_of_arrival']==24 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "23")) echo 'selected="selected"'; ?>>24</option>
                        <option value="25"<?php if($_POST['day_of_arrival']==25 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "24")) echo 'selected="selected"'; ?>>25</option>
                        <option value="26"<?php if($_POST['day_of_arrival']==26 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "25")) echo 'selected="selected"'; ?>>26</option>
                        <option value="27"<?php if($_POST['day_of_arrival']==27 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "26")) echo 'selected="selected"'; ?>>27</option>
                        <option value="28"<?php if($_POST['day_of_arrival']==28 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "27")) echo 'selected="selected"'; ?>>28</option>
                        <option value="29"<?php if($_POST['day_of_arrival']==29 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "28")) echo 'selected="selected"'; ?>>29</option>
                        <option value="30"<?php if($_POST['day_of_arrival']==30 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "29")) echo 'selected="selected"'; ?>>30</option>
                        <option value="31"<?php if($_POST['day_of_arrival']==31 || (!isset($_POST['day_of_arrival'])&&date("j", time()) == "30")) echo 'selected="selected"'; ?>>31</option>
                        </select><select name="month_of_arrival" id="month_of_arrival" class="fs" onchange="changeDays(month_of_arrival, day_of_arrival, year_of_arrival)">
                          <option value="Января"<?php if($_POST['month_of_arrival']=="Января" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "1")) echo 'selected="selected"'; ?>>Января</option>
                          <option value="Февраля"<?php if($_POST['month_of_arrival']=="Февраля" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "2")) echo 'selected="selected"'; ?>>Февраля</option>
                          <option value="Марта"<?php if($_POST['month_of_arrival']=="Марта" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "3")) echo 'selected="selected"'; ?>>Марта</option>
                          <option value="Апреля"<?php if($_POST['month_of_arrival']=="Апреля" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "4")) echo 'selected="selected"'; ?>>Апреля</option>
                          <option value="Мая"<?php if($_POST['month_of_arrival']=="Мая" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "5")) echo 'selected="selected"'; ?>>Мая</option>
                          <option value="Июня"<?php if($_POST['month_of_arrival']=="Июня" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "6")) echo 'selected="selected"'; ?>>Июня</option>
                          <option value="Июля"<?php if($_POST['month_of_arrival']=="Июля" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "7")) echo 'selected="selected"'; ?>>Июля</option>
                          <option value="Августа"<?php if($_POST['month_of_arrival']=="Августа" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "8")) echo 'selected="selected"'; ?>>Августа</option>
                          <option value="Сентября"<?php if($_POST['month_of_arrival']=="Сентября" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "9")) echo 'selected="selected"'; ?>>Сентября</option>
                          <option value="Октября"<?php if($_POST['month_of_arrival']=="Октября" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "10")) echo 'selected="selected"'; ?>>Октября</option>
                          <option value="Ноября"<?php if($_POST['month_of_arrival']=="Ноября" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "11")) echo 'selected="selected"'; ?>>Ноября</option>
                          <option value="Декабря"<?php if($_POST['month_of_arrival']=="Декабря" || (!isset($_POST['month_of_arrival'])&&date("n", time()) == "12")) echo 'selected="selected"'; ?>>Декабря</option>
                          </select><select name="year_of_arrival" id="year_of_arrival" class="fs" onchange="changeDays(month_of_arrival, day_of_arrival, year_of_arrival)">
                            <option value="<?php echo date("Y", time()); ?>" selected="selected"><?php echo date("Y", time()); ?></option>
                            <option value="<?php echo (date("Y", time())+1); ?>"><?php echo (date("Y", time())+1); ?></option>
                            </select></nobr><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="12" /><br /><select  name="daytime_of_arrival" id="daytime_of_arrival" class="fs">
    <option value="До 12:00" <? if($_POST['daytime_of_arrival']=="До 12:00") echo "selected" ?>>До 12:00</option>
    <option value="После 12:00" <? if($_POST['daytime_of_arrival']=="После 12:00") echo "selected" ?>>После 12:00</option>
  </select>
  <br />
  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="20" /></td>
                  </tr>
				  <tr>
                    <td align="center" bgcolor="#00590f"><img src="/modules/mod_yj_booking/newfiles/im5.gif" width="92" height="16" /></td>
                  </tr>
              </table></td>
              <td width="25">&nbsp;</td>
            </tr>
        </table></td>
        <td width="1" valign="top" background="/modules/mod_yj_booking/newfiles/vl.gif"><img src="/modules/mod_yj_booking/newfiles/vl.gif" width="1" height="8" /></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="25">&nbsp;</td>
            <td width="180"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td align="center" bgcolor="#00590f"><img src="/modules/mod_yj_booking/newfiles/im3.gif" width="102" height="16" /></td>
                </tr>
                <tr>
                  <td><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="8" /><br />
                    <nobr><select name="day_of_departure" id="day_of_departure" class="fs">
                      <option  value="1"<?php if($_POST['day_of_departure']==1 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "30")) echo 'selected="selected"'; ?>>1</option>
                      <option value="2"<?php if($_POST['day_of_departure']==2 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "31")) echo 'selected="selected"'; ?>>2</option>
                      <option value="3"<?php if($_POST['day_of_departure']==3 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "1")) echo 'selected="selected"'; ?>>3</option>
                      <option value="4"<?php if($_POST['day_of_departure']==4 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "2")) echo 'selected="selected"'; ?>>4</option>
                      <option value="5"<?php if($_POST['day_of_departure']==5 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "3")) echo 'selected="selected"'; ?>>5</option>
                      <option value="6"<?php if($_POST['day_of_departure']==6 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "4")) echo 'selected="selected"'; ?>>6</option>
                      <option value="7"<?php if($_POST['day_of_departure']==7 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "5")) echo 'selected="selected"'; ?>>7</option>
                      <option value="8"<?php if($_POST['day_of_departure']==8 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "6")) echo 'selected="selected"'; ?>>8</option>
                      <option value="9"<?php if($_POST['day_of_departure']==9 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "7")) echo 'selected="selected"'; ?>>9</option>
                      <option value="10"<?php if($_POST['day_of_departure']==10 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "8")) echo 'selected="selected"'; ?>>10</option>
                      <option value="11"<?php if($_POST['day_of_departure']==11 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "9")) echo 'selected="selected"'; ?>>11</option>
                      <option value="12"<?php if($_POST['day_of_departure']==12 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "10")) echo 'selected="selected"'; ?>>12</option>
                      <option value="13"<?php if($_POST['day_of_departure']==13 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "11")) echo 'selected="selected"'; ?>>13</option>
                      <option value="14"<?php if($_POST['day_of_departure']==14 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "12")) echo 'selected="selected"'; ?>>14</option>
                      <option value="15"<?php if($_POST['day_of_departure']==15 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "13")) echo 'selected="selected"'; ?>>15</option>
                      <option value="16"<?php if($_POST['day_of_departure']==16 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "14")) echo 'selected="selected"'; ?>>16</option>
                      <option value="17"<?php if($_POST['day_of_departure']==17 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "15")) echo 'selected="selected"'; ?>>17</option>
                      <option value="18"<?php if($_POST['day_of_departure']==18 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "16")) echo 'selected="selected"'; ?>>18</option>
                      <option value="19"<?php if($_POST['day_of_departure']==19 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "17")) echo 'selected="selected"'; ?>>19</option>
                      <option value="20"<?php if($_POST['day_of_departure']==20 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "18")) echo 'selected="selected"'; ?>>20</option>
                      <option value="21"<?php if($_POST['day_of_departure']==21 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "19")) echo 'selected="selected"'; ?>>21</option>
                      <option value="22"<?php if($_POST['day_of_departure']==22 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "20")) echo 'selected="selected"'; ?>>22</option>
                      <option value="23"<?php if($_POST['day_of_departure']==23 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "21")) echo 'selected="selected"'; ?>>23</option>
                      <option value="24"<?php if($_POST['day_of_departure']==24 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "22")) echo 'selected="selected"'; ?>>24</option>
                      <option value="25"<?php if($_POST['day_of_departure']==25 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "23")) echo 'selected="selected"'; ?>>25</option>
                      <option value="26"<?php if($_POST['day_of_departure']==26 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "24")) echo 'selected="selected"'; ?>>26</option>
                      <option value="27"<?php if($_POST['day_of_departure']==27 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "25")) echo 'selected="selected"'; ?>>27</option>
                      <option value="28"<?php if($_POST['day_of_departure']==28 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "26")) echo 'selected="selected"'; ?>>28</option>
                      <option value="29"<?php if($_POST['day_of_departure']==29 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "27")) echo 'selected="selected"'; ?>>29</option>
                      <option value="30"<?php if($_POST['day_of_departure']==30 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "28")) echo 'selected="selected"'; ?>>30</option>
                      <option value="31"<?php if($_POST['day_of_departure']==31 || (!isset($_POST['day_of_departure'])&&date("j", time()) == "29")) echo 'selected="selected"'; ?>>31</option>
                      </select><select name="month_of_departure" id="month_of_departure" class="fs" onchange="changeDays(month_of_departure, day_of_departure, year_of_departure)">
                        <option value="Января"<?php if($_POST['month_of_departure']=="Января" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "1" && date("j", time()) < 25) ||
														(date("n", time()) == "12" && date("j", time()) >= 25))) echo 'selected="selected"'; ?>>Января</option>
                        <option value="Февраля"<?php if($_POST['month_of_departure']=="Февраля" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "2" && date("j", time()) < 25) ||
														(date("n", time()) == "1" && date("j", time()) >= 25))) echo 'selected="selected"'; ?>>Февраля</option>
                        <option value="Марта"<?php if(($_POST['month_of_departure']=="Марта" || (!isset($_POST['month_of_departure'])&&date("n", time()) == "3" && date("j", time()) < 25) ||
														(date("n", time()) == "2" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Марта</option>
                        <option value="Апреля"<?php if($_POST['month_of_departure']=="Апреля" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "4" && date("j", time()) < 25) ||
														(date("n", time()) == "3" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Апреля</option>
                        <option value="Мая"<?php if(($_POST['month_of_departure']=="Мая" || (!isset($_POST['month_of_departure'])&&date("n", time()) == "5" && date("j", time()) < 25) ||
														(date("n", time()) == "4" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Мая</option>
                        <option value="Июня"<?php if($_POST['month_of_departure']=="Июня" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "6" && date("j", time()) < 25) ||
														(date("n", time()) == "5" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Июня</option>
                        <option value="Июля"<?php if($_POST['month_of_departure']=="Июля" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "7" && date("j", time()) < 25) ||
														(date("n", time()) == "6" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Июля</option>
                        <option value="Августа"<?php if($_POST['month_of_departure']=="Августа" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "8" && date("j", time()) < 25) ||
														(date("n", time()) == "7" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Августа</option>
                        <option value="Сентября"<?php if($_POST['month_of_departure']=="Сентября" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "9" && date("j", time()) < 25) ||
														(date("n", time()) == "8" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Сентября</option>
                        <option value="Октября"<?php if($_POST['month_of_departure']=="Октября" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "10" && date("j", time()) < 25) ||
														(date("n", time()) == "9" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Октября</option>
                        <option value="Ноября"<?php if($_POST['month_of_departure']=="Ноября" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "11" && date("j", time()) < 25) ||
														(date("n", time()) == "10" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Ноября</option>
                        <option value="Декабря"<?php if($_POST['month_of_departure']=="Декабря" || (!isset($_POST['month_of_departure'])&&(date("n", time()) == "12" && date("j", time()) < 25) ||
														(date("n", time()) == "11" && date("j", time()) >= 25)))echo 'selected="selected"'; ?>>Декабря</option>
                        </select><select name="year_of_departure" id="year_of_departure" class="fs" onchange="changeDays(month_of_departure, day_of_departure, year_of_departure)">
                          <option value="<?php echo date("Y", time()); ?>"<?php if((date("n", time())*30+date("j", time())) < 355 ) echo 'selected="selected"'; ?>><?php echo date("Y", time()); ?></option>
                          <option value="<?php echo (date("Y", time())+1); ?>"<?php if((date("n", time())*30+date("j", time())) >= 355 ) echo 'selected="selected"'; ?>><?php echo (date("Y", time())+1); ?></option>
                          </select></nobr><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="12" /><br /><select  name="daytime_of_departure" id="daytime_of_departure" class="fs">
    <option value="До 12:00" <? if($_POST['daytime_of_departure']=="До 12:00") echo "selected" ?>>До 12:00</option>
    <option value="После 12:00" <? if(!isset($_POST['daytime_of_departure'])||$_POST['daytime_of_departure']=="После 12:00") echo "selected" ?>>После 12:00</option>
  </select></td></tr>
            </table></td>
            <td width="25">&nbsp;</td>
          </tr>
        </table></td>
      </tr>
    </table>
      <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="7" /><br />
      <table width="460" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="25"><img src="/modules/mod_yj_booking/newfiles/1.gif" width="25" height="1" /></td>
          <td><textarea name="additional_info" id="additional_info" cols="" style="width: 100%; height:40px;"  rows="2" class="fs"><? echo $_POST['additional_info'] ?></textarea><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="10" />
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><span class="fh">Введите код с картинки:</span><? if(isset($ERROR_DATA['passimage'])) echo "<img src='/modules/mod_yj_booking/newfiles/er.gif' alt='".$ERROR_DATA['passimage']."' title='".$ERROR_DATA['passimage']."' hspace='4'>" ?><br />
                  <img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="7" /><br />
                <input name="passimage" type="text" id="passimage" size="10" style="width: 190px;" class="fs"/></td>
                <td width="10">&nbsp;</td>
                <td><img src="/modules/mod_yj_booking/pass_image/imageview.php?<?php echo "sname=".session_name()."&sid=".session_id() ?>"/></td>
              </tr>
            </table>
		  </td>
          <td width="25">&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
</table><img src="/modules/mod_yj_booking/newfiles/1.gif" width="10" height="8" /><table width="690" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td width="15" rowspan="3">&nbsp;</td>
    <td width="85" class="fh">ФИО:<? if(isset($ERROR_DATA['first_name'])) echo "<img src='/modules/mod_yj_booking/newfiles/er.gif' alt='".$ERROR_DATA['first_name']."' title='".$ERROR_DATA['first_name']."' hspace='4'>" ?></td>
    <td><input type="text"  name="first_name" id="first_name"  value="<? echo $_POST['first_name'] ?>" onKeyUp="hideWarning('name_validator');" style="width: 195px;"/></td>
    <td width="400" rowspan="3" align="center" valign="middle"><a href="javascript:submitform('ReservationForm')" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('submitImage','','/modules/mod_yj_booking/newfiles/bb2.gif',1)"><img src="/modules/mod_yj_booking/newfiles/bb1.gif" alt="Отправить" name="submitImage" width="105" height="37" border="0" id="submitImage" /></a><br />
      <img src="/modules/mod_yj_booking/newfiles/bb3.gif" width="105" height="28" /></td>
  </tr>
  <tr>
    <td class="fh">Email:<? if(isset($ERROR_DATA['email'])) echo "<img src='/modules/mod_yj_booking/newfiles/er.gif' alt='".$ERROR_DATA['email']."' title='".$ERROR_DATA['email']."' hspace='4'>" ?></td>
    <td><input type="text" name="email" id="email" value="<? echo $_POST['email'] ?>" onKeyUp="hideWarning('email_validator');" style="width: 140px;"/></td>
  </tr>
  <tr>
    <td class="fh">Телефон:<? if(isset($ERROR_DATA['phone'])) echo "<img src='/modules/mod_yj_booking/newfiles/er.gif' alt='".$ERROR_DATA['phone']."' title='".$ERROR_DATA['phone']."' hspace='4'>" ?></td>
    <td><input type="text" name="phone" id="phone" value="<? echo $_POST['phone'] ?>" onKeyUp="hideWarning('phone_validator');" class="text-align-right" style="width: 105px;"/></td>
  </tr>
</table>
</form>
<script type="text/javascript">
	changeDays(document.getElementById('month_of_arrival'), document.getElementById('day_of_arrival'), document.getElementById('year_of_arrival'));
	changeDays(document.getElementById('month_of_departure'), document.getElementById('day_of_departure'), document.getElementById('year_of_departure'));
</script>
<?
}
?>
