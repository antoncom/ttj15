/*
	Hotel Reservation Script 1.0
	created by: Martin Ivanov
	website: http://acidmartin.com or http://acidmartin.wemakesites.net
	email: acid_martin@yahoo.com or martin@yahoo.com
*/

window.onload = function()
{
	localize();
	var randomnumber = Math.floor(Math.random()*10000);
	var date = new Date();
	var custom_month = new Date();
	var month = new Date();
	custom_month[0] = "January";
	custom_month[1] = "February";
	custom_month[2] = "March";
	custom_month[3] = "April";
	custom_month[4] = "May";
	custom_month[5] = "June";
	custom_month[6] = "July";
	custom_month[7] = "August";
	custom_month[8] = "September";
	custom_month[9] = "October";
	custom_month[10] = "November";
	document.getElementById('date').value = date.getDate() + '/' + custom_month[month.getMonth()] + '/' + date.getFullYear();
	document.getElementById('generatedantispamcode').value = randomnumber;
}

function validate()
{
	
	var spaces=/\s+/g
	var firstzero = /^0+/g
	var digits = /[0-9[^\+\-]]+/
	var plusminusFirst = /^[\+\-_]+/
	var plusminusMiddle = /[\+\-_]+/g
	var mmail = /^(?:[a-z0-9]+(?:[-_\.]?[a-z0-9]+)?(?:[-_\.]?[a-z0-9]+)?@[a-z0-9]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i
	var tel = /(^\([0-9]{1,6}\))|((^[0-9]{1,3}\([0-9]{1,6}\))[0-9]{3,15})|(^[0-9]{6,15})$/
	var persona = /^.{3,}$/

	

	
//    var result=reg.test(str) ? "Строка совпала" : "Строка не совпала"
		   
		   
	document.getElementById('number_of_guests').value = document.getElementById('number_of_guests').value.replace(spaces, "");
	document.getElementById('number_of_guests').value = document.getElementById('number_of_guests').value.replace(firstzero, "");
	document.getElementById('number_of_guests').value = document.getElementById('number_of_guests').value.replace(plusminusFirst, "");
	if(document.getElementById('number_of_guests').value == '')
	{
		document.getElementById('number_of_guests').focus();
		document.getElementById('number_of_guests_validator').className = 'validator display-block';
		document.getElementById('number_of_guests_validator').innerHTML = xmlDoc.getElementsByTagName("mandatoryfield")[0].childNodes[0].nodeValue;
		return false;
	}
	if(isNaN(document.getElementById('number_of_guests').value) && !digits.test(document.getElementById('number_of_guests').value))
	{
		document.getElementById('number_of_guests').select();
		document.getElementById('number_of_guests').focus();
		document.getElementById('number_of_guests_validator').className = 'validator display-block';
		document.getElementById('number_of_guests_validator').innerHTML = xmlDoc.getElementsByTagName("invalidnumberofguests")[0].childNodes[0].nodeValue;
		return false;
	}
	
	if(document.getElementById('guestFIO').value == '' || !persona.test(document.getElementById('guestFIO').value))
	{
		document.getElementById('guestFIO').select();
		document.getElementById('guestFIO').focus();
		document.getElementById('guestFIO_validator').className = 'validator display-block';
		document.getElementById('guestFIO_validator').innerHTML = xmlDoc.getElementsByTagName("mandatoryfield")[0].childNodes[0].nodeValue;
		return false;
	}

	if(document.getElementById('first_name').value == '' || !persona.test(document.getElementById('first_name').value))
	{
		document.getElementById('first_name').select();
		document.getElementById('first_name').focus();
		document.getElementById('name_validator').className = 'validator display-block';
		document.getElementById('name_validator').innerHTML = xmlDoc.getElementsByTagName("mandatoryfield")[0].childNodes[0].nodeValue;
		return false;
	}
	/* client didn't wont email validation
	if(document.getElementById('email').value == '')
	{
		document.getElementById('email').focus();
		document.getElementById('email_validator').className = 'validator display-block';
		document.getElementById('email_validator').innerHTML = xmlDoc.getElementsByTagName("mandatoryfield")[0].childNodes[0].nodeValue;
		return false;
	}
	if(!mmail.test(document.getElementById('email').value))
	{
		document.getElementById('email').select();
		document.getElementById('email').focus();
		document.getElementById('email_validator').className = 'validator display-block';
		document.getElementById('email_validator').innerHTML = xmlDoc.getElementsByTagName("invalidemail")[0].childNodes[0].nodeValue;
		return false;
	}
	*/

	document.getElementById('phone').value = document.getElementById('phone').value.replace(plusminusMiddle, "");
	document.getElementById('phone').value = document.getElementById('phone').value.replace(spaces, "");
	if(document.getElementById('phone').value == '')
	{
		document.getElementById('phone').focus();
		document.getElementById('phone_validator').className = 'validator display-block';
		document.getElementById('phone_validator').innerHTML = xmlDoc.getElementsByTagName("mandatoryfield")[0].childNodes[0].nodeValue;
		return false;
	}

	if(!tel.test(document.getElementById('phone').value))
	{
		document.getElementById('phone').select();
		document.getElementById('phone').focus();
		document.getElementById('phone_validator').className = 'validator display-block';
		document.getElementById('phone_validator').innerHTML = xmlDoc.getElementsByTagName("invalidphone")[0].childNodes[0].nodeValue;
		return false;
	}
	else
	{
		document.getElementById('reservation_information').value = '\n\ndate submitted: ' + document.getElementById('date').value + '\nname: ' + document.getElementById('first_name').value + ' ' + document.getElementById('surname').value + '\nemail: ' + document.getElementById('email').value + '\nphone number: ' + document.getElementById('phone').value + '\narrival: ' + document.getElementById('day_of_arrival').value + '/' + document.getElementById('month_of_arrival').value + '/' + document.getElementById('year_of_arrival').value + '\ndeparture: ' + document.getElementById('day_of_departure').value + '/' + document.getElementById('month_of_departure').value + '/' + document.getElementById('year_of_departure').value + '\ntype of room: ' + document.getElementById('type_of_room').value + '\nnumber of guests: ' + document.getElementById('number_of_guests').value + '\nadditional information: ' + document.getElementById('additional_info').value;
		document.getElementById('submittedantispamcode').value = document.getElementById('generatedantispamcode').value;
	}
}

function hideWarning(ValidatorId)
{
	document.getElementById(ValidatorId).className = 'validator display-none';
}

function resetForm()
{
	hideWarning('number_of_guests_validator');
	hideWarning('name_validator');
	hideWarning('email_validator');
	hideWarning('phone_validator');
}

function localize()
{
	var localizationfiles = 'modules/mod_yj_booking/files/language/' + formlanguage + '.xml';
	if(window.ActiveXObject)
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async = false;
		xmlDoc.load(localizationfiles);
		readLocalizationFile();
	}
	else if(document.implementation && document.implementation.createDocument)
	{
		xmlDoc = document.implementation.createDocument("","",null);
		xmlDoc.load(localizationfiles);
		xmlDoc.onload = readLocalizationFile;
	}
}

function readLocalizationFile()
{
//
//	
//	document.getElementById('arrival_legend').innerHTML = xmlDoc.getElementsByTagName("arrival")[0].childNodes[0].nodeValue;
//	document.getElementById('day_of_arrival_label').innerHTML = xmlDoc.getElementsByTagName("day")[0].childNodes[0].nodeValue;
//	document.getElementById('month_of_arrival_label').innerHTML = xmlDoc.getElementsByTagName("month")[0].childNodes[0].nodeValue;
//	document.getElementById('year_of_arrival_label').innerHTML = xmlDoc.getElementsByTagName("year")[0].childNodes[0].nodeValue;
//	
//	document.getElementById('departure_legend').innerHTML = xmlDoc.getElementsByTagName("departure")[0].childNodes[0].nodeValue;
//	document.getElementById('day_of_departure_label').innerHTML = xmlDoc.getElementsByTagName("day")[0].childNodes[0].nodeValue;
//	document.getElementById('month_of_departure_label').innerHTML = xmlDoc.getElementsByTagName("month")[0].childNodes[0].nodeValue;
//	document.getElementById('year_of_departure_label').innerHTML = xmlDoc.getElementsByTagName("year")[0].childNodes[0].nodeValue;
//	
//	document.getElementById('accomodation_legend').innerHTML = xmlDoc.getElementsByTagName("accomodation")[0].childNodes[0].nodeValue;
//	document.getElementById('type_of_room_label').innerHTML = xmlDoc.getElementsByTagName("typeofroom")[0].childNodes[0].nodeValue;
//	document.getElementById('number_of_guests_label').innerHTML = xmlDoc.getElementsByTagName("numberofguests")[0].childNodes[0].nodeValue;
//	
//	document.getElementById('additional_info_legend').innerHTML = xmlDoc.getElementsByTagName("additionalinfo")[0].childNodes[0].nodeValue;
//	
//	document.getElementById('submit_button').innerHTML = xmlDoc.getElementsByTagName("submitbutton")[0].childNodes[0].nodeValue;
//	document.getElementById('reset_button').innerHTML = xmlDoc.getElementsByTagName("resetbutton")[0].childNodes[0].nodeValue;
//	
//	document.getElementById('submit_button').title = xmlDoc.getElementsByTagName("submitbutton")[0].childNodes[0].nodeValue;
//	document.getElementById('reset_button').title = xmlDoc.getElementsByTagName("resetbutton")[0].childNodes[0].nodeValue;
};