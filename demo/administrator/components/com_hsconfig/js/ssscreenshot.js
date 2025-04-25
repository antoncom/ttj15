/**
* Highslide JS for Joomla
* @version $Id$
* @subpackage highslide.js
* @author Ken Lowther
* @license Limited  http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
function SsScreenshot()
{

var thisObject = this;
this.sshotimg = null;
this.sspreset = null;
this.baseUrl = "";

this.onchange = function(e)
{
	var sshotname = "";

	if (thisObject.sspreset.childNodes[thisObject.sspreset.selectedIndex].value == -1)
	{
		sshotname = "none-selected";
	}
	else
	{
		sshotname = thisObject.sspreset.childNodes[thisObject.sspreset.selectedIndex].value;
	}
	thisObject.sshotimg.src = thisObject.baseUrl + "/administrator/components/com_hsconfig/presets/slideshow/" + sshotname + ".jpg";
	thisObject.sshotimg.style.visibility = 'visible';
	var foundit = false;
	for (var i = 0; i < thisObject.sspreset.childNodes.length; i++)
	{
		var name = thisObject.sspreset.childNodes[i].value;
		if (name == -1)
		{
			name = "none-selected";
		}
		var ele = document.getElementById( 'hsconfig-ss-' + name );
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

		ele = document.getElementById( 'hsconfig-ss-no-info' );
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
	this.sshotimg = document.getElementById( 'slideshowscreenshot_image' );
	this.sspreset = document.getElementById( 'paramssPreset' );
	if (this.sspreset == null)
	{
		this.sspreset = document.getElementById( 'paramsssPreset' );
	}
	if (this.sspreset != null)
	{
		this.sspreset.onchange = this.onchange;
		this.onchange();
	}
}

}