
function getSelPro(v)  {
	sstr = "";
	for(i=0; i < v.length; i++)
	{
		if (v.options[i].selected)
		{
			if(v.options[i].value != "")
			{
				sstr = sstr + v.options[i].value + ",";
			}
		}
	}
	sstr = sstr.substring(0,sstr.length-1);
	document.getElementById("selectedProjects").value = sstr;
}
	
