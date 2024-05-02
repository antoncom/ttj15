/**
* Highslide JS for Joomla
* @version $Id$
* @subpackage highslide.js
* @author Ken Lowther
* @license Limited  http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
function OpExample()
{

var thisObject = this;
this.exampimg = null;
this.oppreset = null;
this.baseUrl = "";

this.onchange = function(e)
{
	var sshotname = "";

	if (thisObject.oppreset.childNodes[thisObject.oppreset.selectedIndex].value == -1)
	{
		sshotname = "none-selected";
	}
	else
	{
		sshotname = thisObject.oppreset.childNodes[thisObject.oppreset.selectedIndex].value;
	}
	thisObject.exampimg.src = thisObject.baseUrl + "/administrator/components/com_hsconfig/presets/overlay/" + sshotname + ".jpg";
	thisObject.exampimg.style.visibility = 'visible';
	var foundit = false;
	for (var i = 0; i < thisObject.oppreset.childNodes.length; i++)
	{
		var name = thisObject.oppreset.childNodes[i].value;
		if (name == -1)
		{
			name = "none-selected";
		}
		var ele = document.getElementById( 'hsconfig-op-' + name );
		if (ele != null)
		{
			if (name == sshotname)
			{
				ele.style.display = "block";
				foundit = true;
			}
			else
			{
				ele.style.display = "none";
			}
		}

		ele = document.getElementById( 'hsconfig-op-no-info' );
		if (ele != null)
		{
			if (!foundit)
			{
				ele.style.display = 'block';
			}
			else
			{
				ele.style.display = 'none';			}
		}
	}
	return true;
}

this.init = function init( opts )
{
	this.baseUrl = opts.base;
	this.exampimg = document.getElementById( 'overlayexample_image' );
	this.oppreset = document.getElementById( 'paramopPreset' );
	if (this.oppreset == null)
	{
		this.oppreset = document.getElementById( 'paramsopPreset' );
	}
	if (this.oppreset != null)
	{
		this.oppreset.onchange = this.onchange;
		this.onchange();
	}
}

}