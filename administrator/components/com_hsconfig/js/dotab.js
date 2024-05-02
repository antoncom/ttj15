/**
* Highslide JS for Joomla
* @version $Id$
* @subpackage highslide.js
* @author Ken Lowther
* @license Limited  http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/
function doTab()
{

var thisObject = this;

this.keypressed = function keypressed(e)
{
	//get key pressed
	var key = null;
	if(window.event) key = event.keyCode;
	else if(e.which) key = e.which;

	//if tab pressed
	if(key != null && key == 9)
	{
		//IE
		if(document.selection)
		{
			//get focus
			this.focus();

			//get selection
			var sel = document.selection.createRange();

			//insert tab
			sel.text = '\t';
		}
		//Mozilla + Netscape
		else if(this.selectionStart || this.selectionStart == "0")
		{
			//save scrollbar positions
			var scrollY = this.scrollTop;
			var scrollX = this.scrollLeft;

			//get current selection
			var start = this.selectionStart;
			var end = this.selectionEnd;

			//insert tab
			this.value = this.value.substring(0,start) + '\t' + this.value.substring(end,this.value.length);

			//move cursor back to insert point
			this.focus();
			this.selectionStart = start+1;
			this.selectionEnd = start+1;

			//reset scrollbar position
			this.scrollTop = scrollY;
			this.scrollLeft = scrollX;
		}
		//time for a new browser!!!
		else this.value += '\t';

		//stop the real tab press
		return false;
	}
}

this.init = function init()
{
	var eles = document.getElementsByTagName( 'textarea' );
	for (var i = 0; i < eles.length; i++)
	{
		eles[i].onkeydown = this.keypressed;
	}
}

}