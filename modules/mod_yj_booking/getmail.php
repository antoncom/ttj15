<?php 
/*----------------------------------------------------------------------
#Youjoomla YJ Booking Module 1.0
# ----------------------------------------------------------------------
# Copyright (C) 2007 You Joomla. All Rights Reserved.
# Coded by: NEO
# License: Youjoomla LLC
# Website: http://www.youjoomla.com
------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Restricted index access' );

echo "<!-- http://www.Youjoomla.com  YJ Booking Module for Joomla 1.5 starts here -->	";
echo "<script type='text/javascript' src='".JURI::base()."/modules/mod_yj_booking/files/form.js'></script>\n";
$linktag_yj_book="<link rel='stylesheet' type='text/css' href='".JURI::base()."/modules/mod_yj_booking/files/form_css.css'/>\n";

if ($ismooloaded == 1) {
	$linktag_yj_book="<link rel='stylesheet' type='text/css' href='".JURI::base()."/modules/mod_yj_booking/files/mootools.".$mooext."'/>\n";
}





$mainframe->addCustomHeadTag($linktag_yj_book);


// UTF-8 translation

function utf8_to_win($string){

	for ($c=0;$c<strlen($string);$c++){

		$i=ord($string[$c]);

		if ($i <= 127) @$out .= $string[$c];

		if (@$byte2){

			$new_c2=($c1&3)*64+($i&63);

			$new_c1=($c1>>2)&5;

			$new_i=$new_c1*256+$new_c2;

			if ($new_i==1025){

				$out_i=168;

			} else {

				if ($new_i==1105){

					$out_i=184;

				} else {

					$out_i=$new_i-848;

				}

			}

			@$out .= chr($out_i);

			$byte2 = false;

		}

		if (($i>>5)==6) {

			$c1 = $i;

			$byte2 = true;

		}

	}

	return $out;

}


//send mail

if($_POST['action']=='sendform'){
	// get posted data into local variables
	$EmailFrom = $SMTP_email;
	$EmailTo = $your_email ;
	$Subject = $email_subject ;

	// FORM VARS
	$destination_pack = Trim(stripslashes($_POST['destination_pack']));
	$first_name = Trim(stripslashes($_POST['first_name']));
	$surname = Trim(stripslashes($_POST['surname']));
	$email = Trim(stripslashes($_POST['email']));
	$phone = Trim(stripslashes($_POST['phone']));
	$day_of_arrival = Trim(stripslashes($_POST['day_of_arrival']));
	$month_of_arrival = Trim(stripslashes($_POST['month_of_arrival']));
	$year_of_arrival = Trim(stripslashes($_POST['year_of_arrival']));
	$day_of_departure = Trim(stripslashes($_POST['day_of_departure']));
	$month_of_departure = Trim(stripslashes($_POST['month_of_departure']));
	$year_of_departure = Trim(stripslashes($_POST['year_of_departure']));
	$type_of_room = Trim(stripslashes($_POST['type_of_room']));
	$number_of_guests = Trim(stripslashes($_POST['number_of_guests']));
	$additional_info = Trim(stripslashes($_POST['additional_info']));
	$daytime_of_arrival = Trim(stripslashes($_POST['daytime_of_arrival']));
	$daytime_of_departure = Trim(stripslashes($_POST['daytime_of_departure']));
	$pay_type = Trim(stripslashes($_POST['pay_type']));
	$FIO_guest = Trim(stripslashes($_POST['FIO_guest']));


	$captcha_keystring = $_SESSION["captcha_keystring"];
	$captcha_keystring_image = md5($_POST['passimage']);

	$validationOK=true;
	$ERROR_DATA = array();
	$ERROR_MESSAGE = "";

	if($captcha_keystring<>$captcha_keystring_image) $ERROR_DATA['passimage'] = "Неправильный код с картинки";
	if(!empty($email)&&!preg_match("/^([a-z0-9_]|\-|\.)+@(([a-z0-9_]|\-)+\.)+[a-z]{2,4}$/", $email)) $ERROR_DATA['email'] = "Неправильный Емайл";
	if(empty($first_name)) $ERROR_DATA['first_name'] = "Укажите ФИО для контакта";
	if(empty($FIO_guest)) $ERROR_DATA['FIO_guest'] = "Укажите Фамилия И.О. гостя";
	if(empty($phone)) $ERROR_DATA['phone'] = "Укажите Телефон";
	if(!is_numeric($number_of_guests)) $ERROR_DATA['number_of_guests'] = "Укажите цифрой Количество гостей";
	if($number_of_guests=="0") $ERROR_DATA['number_of_guests'] = "Укажите цифрой Количество гостей";

	if(sizeof($ERROR_DATA)>=1){
		$validationOK = false;
		$ERROR_MESSAGE = "<span class='err'>Ошибка заполнения заявки! Проверьте поля помеченные</span> <img src='/modules/mod_yj_booking/newfiles/er.gif' width='11' height='12' hspace='4' >";
	}

	if($validationOK){
		// validation

		// prepare email body text
		$Body = "";
		$Body .= "\nТип номера: ";
		$Body .= $destination_pack;
		$Body .= "\nДата прибытия: ";
		$Body .= $day_of_arrival;
		$Body .= "/";
		$Body .= $month_of_arrival;
		$Body .= "/" . $year_of_arrival;
		$Body .= "  -  " . $daytime_of_arrival;
		$Body .= "\nДата отбытия: ";
		$Body .= $day_of_departure;
		$Body .= "/" . $month_of_departure;
		$Body .= "/" . $year_of_departure;
		$Body .= "  -  " . $daytime_of_departure;
		$Body .= "\nВид оплаты: ";
		$Body .= $pay_type;
		//$Body .= "\nType of room: ";
		//$Body .= $type_of_room;
		$Body .= "\nКоличество человек: ";
		$Body .= $number_of_guests;
		$Body .= "\nФамилия И.О. гостя: ";
		$Body .= $FIO_guest;
		$Body .= "\n---------------------------------------";
		$Body .= "\nКонтактное лицо: ";
		$Body .= $first_name;
		//$Body .= "\nSurname:";
		//$Body .= $surname;
		$Body .= "\nЕмайл: ";
		$Body .= ($email === "") ? "-" : $email;
		$Body .= "\nТелефон: ";
		$Body .= ($phone === "") ? "-" : $phone;
		$Body .= "\n---------------------------------------";
		$Body .= "\nПримечание:";
		$Body .= ($additional_info === "") ? "\n-" : "\n".$additional_info;
		$Body .= "\n";
		$Body .= "\nIP-адрес посетителя: " . $_SERVER['REMOTE_ADDR'];

	
		// send email
$successOrder = mail($EmailTo, utf8_to_win($Subject), utf8_to_win($Body), "From: <$EmailFrom>");
		//	mail($email_address, $Subject, $Body, "From: <$EmailFrom>");
		//mail($email_address, utf8_to_win($subject_prefix), utf8_to_win($message), utf8_to_win("From: journal@chudo-koni.ru"));




		// redirect to success page
		if ($successOrder){
?>
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 13px;
}
.style3 {
	color: #666666;
	font-size: 10px;
}
.style4 {font-size: 6px}
.style5 {
	color: #006600;
	font-weight: bold;
}
-->
</style>
<script type="text/javascript">
function closeIt() {
	if (parent.window.hs) {
		if (parent.window.document.getElementById("bronform") != null)
		{
			var exp = parent.window.hs.getExpander("bronform");
			if(exp != null)
			{
				exp.close();
				return;
			}
			else
			{
				if (parent.window.document.getElementById("bronformS"))
				{
					var exp = parent.window.hs.getExpander("bronformS");
					exp.close();
					return;
				}
			}
		}
		else
		{
			if (parent.window.document.getElementById("bronformS") != null)
			{
				var exp = parent.window.hs.getExpander("bronformS");
				if(exp != null)
				{
					exp.close();
					return;
				}
			}
		}
	}
}
</script>

<table width="690" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td colspan="4" align="center"><img src="/modules/mod_yj_booking/newfiles/sp.gif" alt="Спасибо" width="124" height="30" vspace="40" /></td>
  </tr>
  <tr>
    <td align="right" valign="top"><img src="/modules/mod_yj_booking/newfiles/bell.jpg" width="100" height="121" hspace="25" style="margin-left:88px;"/></td>
    <td width="1" background="/modules/mod_yj_booking/newfiles/vl.gif"><img src="/modules/mod_yj_booking/newfiles/1.gif" width="1" height="1" /></td>
    <td width="25" valign="top">&nbsp;</td>
    <td valign="top" class="tt"><p class="style1"><span class="style5">Спасибо, Ваша заявка 
      направлена 
          <br />
        в службу бронирования! </span><br />
        <span class="style5"><br />
        Администратор гостиницы свяжется с Вами <br />
        для подтверждения брони.</span><br />
        <br />
        <br />
        <br />
        <br />
    </p>
      <p class="style1"><a href="/index.php?option=com_content&amp;view=article&amp;id=125&amp;Itemid=92">Отправить новую заявку</a><br />
        <span class="style4">&nbsp;</span><br />
      <a href="#" onclick="closeIt();">Выход</a></p>
      </td>
  </tr>
  <tr>
    <td colspan="4" align="center"><br />        
    <br /></td>
  </tr>
</table>

<?
		
		}
		else{
			//print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
			echo '<div id="log">';
			print "Your Message wa not sent.<br /> Please check module settings";
			echo '</div>';
		}
	}
}

//  stay there



function stayThere(){
	$cururl_sendform = JRequest::getURI();
	if(($pos = strpos($cururl_sendform, "index.php"))!== false){
		$cururl_sendform = substr($cururl_sendform,$pos);
	}
	$cururl_sendform = JRoute::_($cururl_sendform);
	return $cururl_sendform;
}


//echo '<pre>';      // Using PRE for readability
//print_r($_POST);
//echo '</pre>';
?>
