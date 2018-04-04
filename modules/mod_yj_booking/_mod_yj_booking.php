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


//  do not edit below this line


include ("modules/mod_yj_booking/getmail.php");

//get the titles

  
  
function getTitles($params){

  $now 		    = date('Y-m-d H:i:s');
  $database 	=& JFactory::getDBO();
  $nullDate 	= $database->getNullDate();


$work_section=$params->get('work_section',1);
$query = 'SELECT title ,sectionid FROM #__content WHERE (state=1) and (sectionid='.$work_section.') ORDER by ordering';
$database->setQuery($query);
$titles = $database->loadObjectList();
echo "<option value=\"".$titles[0]->title."\" selected=\"selected\" >".$titles[0]->title."</option>";
array_shift($titles);
foreach ( $titles as $row ) {

echo "<option value=\"$row->title\">".$row->title."</option>";

}
}
?>

<script type="text/javascript">
var formlanguage = "en-US"; // set default form language
</script>

   


<div id="yj_booking">
<div style="margin: 3px 0 20px 14px">
<a href="http://www.icq.com/whitepages/cmd.php?uin=204266112&action=message" style="background:none;"><img alt="ICQ"  title="ICQ"   src="http://web.icq.com/whitepages/online?icq=204266112&img=5" border="0"><img src="/images/icqn.gif" alt="ICQ" border="0" /></a>
</div>

    
    <?php if($show_accordion ==1) { ?>
<script type="text/javascript">
        window.addEvent('domready', function() {
	var accordion = new Accordion($$('.toggler'),$$('.element'), {
		opacity: 0
		//onActive: function(toggler) { toggler.setStyle('color', '#000'); },
		//onBackground: function(toggler) { toggler.setStyle('color', '#fff'); }
	});
});
                
  </script>
  	<!-- reservation form begin -->
	<form id="ReservationForm"  method="post" action="<?php echo stayThere(); ?>"  onsubmit="return validate();" onReset="resetForm();">
    
		<div class="reservation">
			<fieldset class="destination_pack_legend">
            <legend class="toggler"><?php echo $destination_pack_legend ?></legend>
            <div class="element">
<div>
					<label for="destination_pack" id="destination_pack_label"><?php echo $destination_pack_label ?></label>
					<div style="width:200px">
					<select name="destination_pack" id="destination_pack" >
                      <?php getTitles($params);?>
                    </select>
					<label for="number_of_guests" id="number_of_guests_label" style="white-space:nowrap;"><?php echo $number_of_guests_label ?></label>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><input type="text" name="number_of_guests" id="number_of_guests" size="3" maxlength="3" value="" onKeyUp="hideWarning('number_of_guests_validator');"  class="text-align-right" /></td>
    <td width="100%" style="padding-left:5px""><div id="number_of_guests_validator" class="validator display-none"><!-- --></div></td>
  </tr>
</table>
</div>
				<div>
					<label for="paytype" id="paytype"><?php echo "Вид оплаты:" ?></label>
					<select  name="pay_type" id="pay_type">
						<option value="Наличными" selected="selected">Наличными</option>
						<option value="Перечислением">Перечислением</option>
					</select>
				</div>
				<div style="margin-top:8px; height:60px">
					<label for="guestName" id="guestName"><?php echo "Фамилия И.О. гостя:" ?></label>
						<input type="text" name="FIO_guest" id="guestFIO" size="26" maxlength="64" value="" onKeyUp="hideWarning('guestFIO_validator');"  class="text-align-right" />
										<div id="guestFIO_validator" class="validator display-none"><!-- --></div>
				</div>



					
                    <div id="MojInfo"></div>
			  </div>
            </div>
			</fieldset>
      </div>

		<div class="reservation">
			<fieldset>
				<legend class="toggler"><?php echo $arrival_legend ?></legend>
                <div class="element">
				<div>
				
<label for="day_of_arrival" id="day_of_arrival_label"><?php echo $day_of_arrival_label ?></label>
                   


                    
                    
				
	<select name="day_of_arrival" id="day_of_arrival">
						<option  value="1"<?php if(date("j", time()) == "1") echo 'selected="selected"'; ?>>1</option>
						<option value="2"<?php if(date("j", time()) == "2") echo 'selected="selected"'; ?>>2</option>
						<option value="3"<?php if(date("j", time()) == "3") echo 'selected="selected"'; ?>>3</option>
						<option value="4"<?php if(date("j", time()) == "4") echo 'selected="selected"'; ?>>4</option>
						<option value="5"<?php if(date("j", time()) == "5") echo 'selected="selected"'; ?>>5</option>
						<option value="6"<?php if(date("j", time()) == "6") echo 'selected="selected"'; ?>>6</option>
						<option value="7"<?php if(date("j", time()) == "7") echo 'selected="selected"'; ?>>7</option>
						<option value="8"<?php if(date("j", time()) == "8") echo 'selected="selected"'; ?>>8</option>
						<option value="9"<?php if(date("j", time()) == "9") echo 'selected="selected"'; ?>>9</option>
						<option value="10"<?php if(date("j", time()) == "10") echo 'selected="selected"'; ?>>10</option>
						<option value="11"<?php if(date("j", time()) == "11") echo 'selected="selected"'; ?>>11</option>
						<option value="12"<?php if(date("j", time()) == "12") echo 'selected="selected"'; ?>>12</option>
						<option value="13"<?php if(date("j", time()) == "13") echo 'selected="selected"'; ?>>13</option>
						<option value="14"<?php if(date("j", time()) == "14") echo 'selected="selected"'; ?>>14</option>
						<option value="15"<?php if(date("j", time()) == "15") echo 'selected="selected"'; ?>>15</option>
						<option value="16"<?php if(date("j", time()) == "16") echo 'selected="selected"'; ?>>16</option>
						<option value="17"<?php if(date("j", time()) == "17") echo 'selected="selected"'; ?>>17</option>
						<option value="18"<?php if(date("j", time()) == "18") echo 'selected="selected"'; ?>>18</option>
						<option value="19"<?php if(date("j", time()) == "19") echo 'selected="selected"'; ?>>19</option>
						<option value="20"<?php if(date("j", time()) == "20") echo 'selected="selected"'; ?>>20</option>
						<option value="21"<?php if(date("j", time()) == "21") echo 'selected="selected"'; ?>>21</option>
						<option value="22"<?php if(date("j", time()) == "22") echo 'selected="selected"'; ?>>22</option>
						<option value="23"<?php if(date("j", time()) == "23") echo 'selected="selected"'; ?>>23</option>
						<option value="24"<?php if(date("j", time()) == "24") echo 'selected="selected"'; ?>>24</option>
						<option value="25"<?php if(date("j", time()) == "25") echo 'selected="selected"'; ?>>25</option>
						<option value="26"<?php if(date("j", time()) == "26") echo 'selected="selected"'; ?>>26</option>
						<option value="27"<?php if(date("j", time()) == "27") echo 'selected="selected"'; ?>>27</option>
						<option value="28"<?php if(date("j", time()) == "28") echo 'selected="selected"'; ?>>28</option>
						<option value="29"<?php if(date("j", time()) == "29") echo 'selected="selected"'; ?>>29</option>
						<option value="30"<?php if(date("j", time()) == "30") echo 'selected="selected"'; ?>>30</option>
						<option value="31"<?php if(date("j", time()) == "31") echo 'selected="selected"'; ?>>31</option>
				  </select>
				</div>
				<div>
					<label for="month_of_arrival" id="month_of_arrival_label"><?php echo $month_of_arrival_label ?></label>
					<select name="month_of_arrival" id="month_of_arrival">
						<option value="Январь"<?php if(date("n", time()) == "1") echo 'selected="selected"'; ?>>Январь</option>
						<option value="Февраль"<?php if(date("n", time()) == "2") echo 'selected="selected"'; ?>>Февраль</option>
						<option value="Март"<?php if(date("n", time()) == "3") echo 'selected="selected"'; ?>>Март</option>
						<option value="Апрель"<?php if(date("n", time()) == "4") echo 'selected="selected"'; ?>>Апрель</option>
						<option value="Май"<?php if(date("n", time()) == "5") echo 'selected="selected"'; ?>>Май</option>
						<option value="Июнь"<?php if(date("n", time()) == "6") echo 'selected="selected"'; ?>>Июнь</option>
						<option value="Июль"<?php if(date("n", time()) == "7") echo 'selected="selected"'; ?>>Июль</option>
						<option value="Август"<?php if(date("n", time()) == "8") echo 'selected="selected"'; ?>>Август</option>
						<option value="Сентябрь"<?php if(date("n", time()) == "9") echo 'selected="selected"'; ?>>Сентябрь</option>
						<option value="Октябрь"<?php if(date("n", time()) == "10") echo 'selected="selected"'; ?>>Октябрь</option>
						<option value="Ноябрь"<?php if(date("n", time()) == "11") echo 'selected="selected"'; ?>>Ноябрь</option>
						<option value="Декабрь"<?php if(date("n", time()) == "12") echo 'selected="selected"'; ?>>Декабрь</option>
					</select>
				</div>
				<div>
					<label for="year_of_arrival" id="year_of_arrival_label"><?php echo $year_of_arrival_label ?></label>
					<select name="year_of_arrival" id="year_of_arrival">
						<option value="<?php echo date("Y", time()); ?>" selected="selected"><?php echo date("Y", time()); ?></option>
						<option value="<?php echo (date("Y", time())+1); ?>"><?php echo (date("Y", time())+1); ?></option>
					</select>
				</div>
				<div>
					<label for="arrival_time" id="arrival_time"><?php echo "Время прибытия в гостиницу:" ?></label>
					<select  name="daytime_of_arrival" id="daytime_of_arrival">
						<option value="До 12:00" selected="selected">До 12:00</option>
						<option value="После 12:00">После 12:00</option>
					</select>
				</div>
             </div>
			</fieldset>
			
     
			<fieldset>
				<legend class="toggler"><?php echo $departure_legend ?></legend>
                <div class="element">
				<div>
                
                
				
<label for="day_of_departure" id="day_of_departure_label"><?php echo $day_of_departure_label ?></label>

             
                                      
					
					<select name="day_of_departure" id="day_of_departure">
						<option  value="1"<?php if(date("j", time()) == "25") echo 'selected="selected"'; ?>>1</option>
						<option value="2"<?php if(date("j", time()) == "26") echo 'selected="selected"'; ?>>2</option>
						<option value="3"<?php if(date("j", time()) == "27") echo 'selected="selected"'; ?>>3</option>
						<option value="4"<?php if(date("j", time()) == "28") echo 'selected="selected"'; ?>>4</option>
						<option value="5"<?php if(date("j", time()) == "29") echo 'selected="selected"'; ?>>5</option>
						<option value="6"<?php if(date("j", time()) == "30") echo 'selected="selected"'; ?>>6</option>
						<option value="7"<?php if(date("j", time()) == "31") echo 'selected="selected"'; ?>>7</option>
						<option value="8"<?php if(date("j", time()) == "1") echo 'selected="selected"'; ?>>8</option>
						<option value="9"<?php if(date("j", time()) == "2") echo 'selected="selected"'; ?>>9</option>
						<option value="10"<?php if(date("j", time()) == "3") echo 'selected="selected"'; ?>>10</option>
						<option value="11"<?php if(date("j", time()) == "4") echo 'selected="selected"'; ?>>11</option>
						<option value="12"<?php if(date("j", time()) == "5") echo 'selected="selected"'; ?>>12</option>
						<option value="13"<?php if(date("j", time()) == "6") echo 'selected="selected"'; ?>>13</option>
						<option value="14"<?php if(date("j", time()) == "7") echo 'selected="selected"'; ?>>14</option>
						<option value="15"<?php if(date("j", time()) == "8") echo 'selected="selected"'; ?>>15</option>
						<option value="16"<?php if(date("j", time()) == "9") echo 'selected="selected"'; ?>>16</option>
						<option value="17"<?php if(date("j", time()) == "10") echo 'selected="selected"'; ?>>17</option>
						<option value="18"<?php if(date("j", time()) == "11") echo 'selected="selected"'; ?>>18</option>
						<option value="19"<?php if(date("j", time()) == "12") echo 'selected="selected"'; ?>>19</option>
						<option value="20"<?php if(date("j", time()) == "13") echo 'selected="selected"'; ?>>20</option>
						<option value="21"<?php if(date("j", time()) == "14") echo 'selected="selected"'; ?>>21</option>
						<option value="22"<?php if(date("j", time()) == "15") echo 'selected="selected"'; ?>>22</option>
						<option value="23"<?php if(date("j", time()) == "16") echo 'selected="selected"'; ?>>23</option>
						<option value="24"<?php if(date("j", time()) == "17") echo 'selected="selected"'; ?>>24</option>
						<option value="25"<?php if(date("j", time()) == "18") echo 'selected="selected"'; ?>>25</option>
						<option value="26"<?php if(date("j", time()) == "19") echo 'selected="selected"'; ?>>26</option>
						<option value="27"<?php if(date("j", time()) == "20") echo 'selected="selected"'; ?>>27</option>
						<option value="28"<?php if(date("j", time()) == "21") echo 'selected="selected"'; ?>>28</option>
						<option value="29"<?php if(date("j", time()) == "22") echo 'selected="selected"'; ?>>29</option>
						<option value="30"<?php if(date("j", time()) == "23") echo 'selected="selected"'; ?>>30</option>
						<option value="31"<?php if(date("j", time()) == "24") echo 'selected="selected"'; ?>>31</option>
					</select>
				</div>
				
				<div>
					<label for="month_of_departure" id="month_of_departure_label"><?php echo $month_of_departure_label ?></label>
					<select name="month_of_departure" id="month_of_departure">
						<option value="Январь"<?php if((date("n", time()) == "1" && date("j", time()) < 25) ||
														(date("n", time()) == "12" && date("j", time()) >= 25)) echo 'selected="selected"'; ?>>Январь</option>
						<option value="Февраль"<?php if((date("n", time()) == "2" && date("j", time()) < 25) ||
														(date("n", time()) == "1" && date("j", time()) >= 25)) echo 'selected="selected"'; ?>>Февраль</option>
						<option value="Март"<?php if((date("n", time()) == "3" && date("j", time()) < 25) ||
														(date("n", time()) == "2" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Март</option>
						<option value="Апрель"<?php if((date("n", time()) == "4" && date("j", time()) < 25) ||
														(date("n", time()) == "3" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Апрель</option>
						<option value="Май"<?php if((date("n", time()) == "5" && date("j", time()) < 25) ||
														(date("n", time()) == "4" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Май</option>
						<option value="Июнь"<?php if((date("n", time()) == "6" && date("j", time()) < 25) ||
														(date("n", time()) == "5" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Июнь</option>
						<option value="Июль"<?php if((date("n", time()) == "7" && date("j", time()) < 25) ||
														(date("n", time()) == "6" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Июль</option>
						<option value="Август"<?php if((date("n", time()) == "8" && date("j", time()) < 25) ||
														(date("n", time()) == "7" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Август</option>
						<option value="Сентябрь"<?php if((date("n", time()) == "9" && date("j", time()) < 25) ||
														(date("n", time()) == "8" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Сентябрь</option>
						<option value="Октябрь"<?php if((date("n", time()) == "10" && date("j", time()) < 25) ||
														(date("n", time()) == "9" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Октябрь</option>
						<option value="Ноябрь"<?php if((date("n", time()) == "11" && date("j", time()) < 25) ||
														(date("n", time()) == "10" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Ноябрь</option>
						<option value="Декабрь"<?php if((date("n", time()) == "12" && date("j", time()) < 25) ||
														(date("n", time()) == "11" && date("j", time()) >= 25))echo 'selected="selected"'; ?>>Декабрь</option>
					</select>
				</div>
				<div>
					<label for="year_of_departure" id="year_of_departure_label"><?php echo $year_of_departure_label ?></label>
					<select name="year_of_departure" id="year_of_departure">
						<option value="<?php echo date("Y", time()); ?>"<?php if((date("n", time())*30+date("j", time())) < 355 ) echo 'selected="selected"'; ?>><?php echo date("Y", time()); ?></option>
						<option value="<?php echo (date("Y", time())+1); ?>"<?php if((date("n", time())*30+date("j", time())) >= 355 ) echo 'selected="selected"'; ?>><?php echo (date("Y", time())+1); ?></option>
					</select>
				
				</div>
				<div>
					<label for="departure_time" id="departure_time"><?php echo "Время отъезда из гостиницы:" ?></label>
					<select  name="daytime_of_departure" id="daytime_of_departure">
						<option value="До 12:00">До 12:00</option>
						<option value="После 12:00" selected="selected">После 12:00</option>
					</select>
				</div>
		    </div>
			</fieldset>
	<fieldset class="personal-data">
            	<legend class="personal_data"><?php echo $personal_info_legend ?></legend>


				<div>
					<label for="first_name" id="first_name_label"><?php echo $first_name_label ?></label>
                    
					<input type="text"  name="first_name" id="first_name"  value="" onKeyUp="hideWarning('name_validator');" />
					<div id="name_validator" class="validator display-none"><!-- --></div>
				</div>
				<br class="break" />
				<div>
					<label for="email" id="email_label"><?php echo $email_label ?></label>
					<input type="text" name="email" id="email" value="" onKeyUp="hideWarning('email_validator');" />
					<div id="email_validator" class="validator display-none"><!-- --></div>
				</div>
				<div>
					<label for="phone" id="phone_label"><?php echo $phone_label ?></label>
					<input type="text" name="phone" id="phone" value="" onKeyUp="hideWarning('phone_validator');" class="text-align-right" />
					<div id="phone_validator" class="validator display-none"><!-- --></div>

		
	                
				</div>
		  </fieldset>
			
		<div class="reservation>">
			<fieldset>
				<legend class="toggler"><?php echo $additional_info_legend ?></legend>
                <div class="element">
				<textarea name="additional_info" id="additional_info" cols="" style="width: 100%; height:40px;"  rows="2"></textarea>
                </div>
			</fieldset>
		</div>
			<fieldset>
                    

                <input type="submit" name="salji" id="submit_button" value="<?php echo $submit_button ?>" />
				<button type="reset" id="reset_button" title=""><?php echo $reset_button ?></button>
			</fieldset>
			<div class="display-none">
				<input type="hidden" id="date" />
				<input type="hidden" name="Subject" value="RESERVATION INFORMATION" />
				<input type="text" id="generatedantispamcode" name="generatedantispamcode"/>
				<input type="text" id="submittedantispamcode" name="submittedantispamcode"/>
				<textarea id="reservation_information" name="RESERVATION_INFORMATION" cols="" rows=""></textarea>
			</div>
		</div>
	</form>
  
  
  
  
  
  
  
  
  
  
  
  
  <?php } ?>
    
    
    
    
</div>