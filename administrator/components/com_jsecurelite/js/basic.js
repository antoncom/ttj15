var AjaxObj = new Object();
AjaxObj.showMessage=1;
AjaxObj.Message='';
AjaxObj.favouriteId = 0;
AjaxObj.Request = function(url, callbackMethod)
{
	AjaxObj.request = AjaxObj.createRequestObject();
	AjaxObj.request.onreadystatechange = callbackMethod;
	AjaxObj.request.open("POST", url, true);
	AjaxObj.request.send(url);
}

AjaxObj.setMessage = function (message)
{
	AjaxObj.Message=message;
}

AjaxObj.setShowMessage = function (e)
{
	AjaxObj.showMessage=m;
}

AjaxObj.createRequestObject = function()
{
	var obj;
 	if(window.XMLHttpRequest)
 	{
  		obj = new XMLHttpRequest();
 	}
 	else if(window.ActiveXObject)
 	{
  		obj = new ActiveXObject("MSXML2.XMLHTTP");
 	}
 	return obj;
}
AjaxObj.CheckReadyState = function(obj)
{
 	if( obj.readyState == 4 )
 	{ 
  		return true;
 	} 
	return false;
}

/* End AjaxObj Code */


function showUpdates()
{
	var k = jQuery.noConflict();
	k('#image').show();
	k('#version').hide();
	k('#notes').hide();
	var strParam="index.php?option=com_jsecurelite&task=getVersion";
	AjaxObj.Request(strParam,generateUpdates);
	return false;
}

function generateUpdates()
{
	if(AjaxObj.CheckReadyState(AjaxObj.request))
	{
		var k = jQuery.noConflict();	
		var extensionVersion = AjaxObj.request.responseText;
		k.ajax({
		url:"http://www.joomlaserviceprovider.com/index.php?option=com_extensionversion&task=getVersionInfo&extension=jsecurelite",
		dataType: 'jsonp', 
		success:function(json)
		{
			if(extensionVersion < json.version){
			var version="<font color='#FF0000'>New Version Available - "+json.version+"</font><br/><a href='http://www.joomlaserviceprovider.com/component/docman/doc_details/15-jsecure-lite.html' title='Click here to get latest version' target='_blank'>Click here</a> to get latest version";
			var notes= json.notes;
			k('#version').html(version);
			k('#notes').html(notes);
		}
		
		else
		{
			var version="<font color='#51A351'>Version is up to date</font>";
			var notes= json.notes;
			k('#version').html(version);
			k('#notes').html(notes);
			k('#show_notes').hide();
			
		}
		},
		error:function(){
		alert("Error");
		},
		});
    k('#image').hide();
    k('#version').show();
    k('#notes').show();

	}

}
function hideCustomPath(optionsValue) {
	if(optionsValue.value == "1"){
		document.getElementById("custom_path").style.display = "";
	} else {
		document.getElementById("custom_path").style.display = "none";
	}
}

function submitbutton(pressbutton) {
	var submitForm = document.adminForm;
	
	if(pressbutton=="help"){
		submitForm.task.value=pressbutton;
		submitForm.submit();
		return true;
	}
	if(!alphanumeric(submitForm.key.value)){
		submitForm.key.value="";
		alert("Secret Key should not have special characters. Please enter Alpha-Numeric Key");
		submitForm.key.focus();
		return false;
	}
	if(pressbutton=="save"){
		submitForm.task.value='saveBasic';
		submitForm.submit();
		return true;
	}	
	
	
 
	submitForm.task.value=pressbutton;
	submitForm.submit();
}

function alphanumeric(keyValue) {
	
	var numaric = keyValue;
	for(var j=0; j<numaric.length; j++){
		  var alphaa = numaric.charAt(j);
		  var hh = alphaa.charCodeAt(0);
		  if(!((hh > 47 && hh<58) || (hh > 64 && hh<91) || (hh > 96 && hh<123))){
		  	return false;
		  }
	}
	return true;
}

function isNumeric(val) {
	val.value=val.value.replace(/[^0-9*]/g, '');
	if (val.value.indexOf('*') != '-1')
		val.value = '*';
}