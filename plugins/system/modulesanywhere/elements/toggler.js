/**
 * JavaScript file for Element: Toggler
 * Adds slide in and out functionality to elements based on an elements value
 *
 * @package    NoNumber! Elements
 * @version    v1.0.5
 *
 * @author     Peter van Westen <peter@nonumber.nl>
 * @link       http://www.nonumber.nl
 * @copyright  Copyright (C) 2009 NoNumber! All Rights Reserved
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// an object of all the toggle areas
var nn_togglers = {};
// an object of all the elements with the toggle areas they effect
var nn_elements = {};

window.addEvent( 'domready', function() {
	nn_toggler_init();
});

function nn_toggler_init()
{
	var f = document.forms['adminForm'];
	var togglers = $$('.toggler');
	togglers.each( function( toggler, i ) {
		if ( typeof( nn_togglers[toggler.id] ) == "undefined" ) {
			nn_togglers[toggler.id] = {}; 
		}
		    
		if ( typeof( nn_togglers[toggler.id].fxs ) == "undefined" ) {
			nn_togglers[toggler.id].fxs = new Array();
		}
		toggler_fx = new Fx.Slide( toggler );
		toggler_fx.hide();
		toggler_fx.nofx = 0;
		if ( toggler.hasClass( 'toggler_nofx' ) ) {
			toggler_fx.nofx = 1;
		}
		nn_togglers[toggler.id].fxs.push( toggler_fx );

		elements = {};
		ids = toggler.id.split( ' ' );
		for ( var i = 0; i < ids.length; i++ ) {
			keyval = ids[i].split( '.' );
			
			if ( keyval.length != 2 ) { continue; }

			element_name = keyval[0];
			
			element = f['params['+element_name+']'];
			if ( element == undefined ) {
				element = f['params['+element_name+'][]'];
			}
			if ( typeof( element ) == undefined ) { continue; }
			
			// set elements in togglers
			if ( typeof( elements[element_name] ) == "undefined" ) {
				elements[element_name] = new Array();
			}
			elements[element_name].push( keyval[1] );

			if ( typeof( nn_elements[element_name] ) == "undefined" ) {
				nn_elements[element_name] = new Array();
			}
			nn_elements[element_name].push( toggler.id );
			
			// set togglers in elements
			
		}
		nn_togglers[toggler.id].elements = elements;
		
		toggler.parentNode.parentNode.style.padding = '0px';
	});
	
	nn_toggler_setElementActions();
	
	nn_toggle_all();
}

function nn_toggle_all()
{
	// first handle all togglers that have to be opened
	for ( id in nn_togglers ) {
		nn_toggler_autoHeightParents( id );
		nn_toggler_update( id, 2 );
	};

	// then handle all togglers that have to be closed
	for ( id in nn_togglers ) {
		nn_toggler_update( id, 1 );
	};
}

function nn_toggle( name )
{
	var name = nn_toggler_cleanName( name );
	var togglerids = nn_toggler_getTogglerIds( name );
	togglerids.each( function( id ) {
		nn_toggler_autoHeightParents( id );
		nn_toggler_update( id );
	});
}

function nn_toggler_update( id, init )
{	
	var toggler = nn_togglers[id];
	if ( toggler == undefined ) { return; }
	
	var elements = toggler.elements;
	var show = 0;
	for ( name in elements ) {
		values = nn_toggler_getActiveValues( name );
		if( 
			nn_toggler_in_array( elements[name], values ) ||
			( elements[name] == '' && values.length == 0 )
		) {
			show = 1;
		}
	}
	toggler.fxs.each( function( fx ) {
		var nofx = fx.nofx
		if ( init == 1 ) {
			if( !show ) {
				fx.hide();
			}
		} else if ( init == 2 ) {
			if( show ) {
				nn_toggler_autoHeightParents( id );
				fx.show();
			}
		} else {
			fx.stop();
			if ( fx.nofx ) {
				if( show ) {
					fx.show();
				} else {
					fx.hide();
				}
			} else {
				if( show ) {
					fx.slideIn();
				} else {
					fx.slideOut();
				}
			}
		}
	});
}

function nn_toggler_autoHeightParents( id )
{
	var togglers = $$('.toggler');
	togglers.each( function( toggler ) {
		if ( toggler.id == id ) {
			var parent = toggler.parentNode;
			for ( i = 0; i < 50; i++) {
				if ( parent ) {
					if ( parent.name == 'adminForm' ) {
						break;
					}
					if ( parent.tagName == 'DIV' ) {
						parent.style.height = 'auto';
					}
					parent = parent.parentNode;
				}
			}
		}
	});
}

function nn_toggler_getTogglerIds( element_name )
{
	var togglers = nn_elements[element_name];
	if ( togglers == undefined ) {
		togglers = new Array();
	}
	return togglers;
}

function nn_toggler_getActiveValues( name )
{
	var active = new Array();
	
	var f = document.forms['adminForm'];
	var element = f['params['+name+']'];
	if ( element == undefined ) {
		element = f['params['+name+'][]'];
	}
	if ( element == undefined ) { return active; }
	
	if ( !element.length ) {
		if ( element.checked || element.selected ) {
			active.push( element.value );
		}
	} else {
		for ( var i = 0; i < element.length; i++ ) {
			if ( element[i].checked || element[i].selected ) {
				active.push( element[i].value );
			}
		}
	}
	return active;
}

function nn_toggler_setElementActions()
{	
	var f = document.forms['adminForm'];
	for ( name in nn_elements ) {
		element = f['params['+name+']'];
		if ( element == undefined ) {
			element = f['params['+name+'][]'];
		}
		if ( element == undefined ) { continue; }

		if( element.tagName == 'SELECT' ) {
			$(element).addEvent( 'change', function(event) { nn_toggle( this.name ); });
		} else if ( !element.length ) {
			$(element).addEvent( 'click', function(event) { nn_toggle( this.name ); });
		} else {
			for ( var i = 0; i < element.length; i++ ) {
				$(element[i]).addEvent( 'click', function(event) { nn_toggle( this.name ); });
			}
		}
	}
}

function nn_toggler_cleanName( name )
{
	var name = name.replace( 'params[', '' );
	name = name.split( ']' );
	return name[0];
}
function nn_toggler_in_array( needle, haystack )
{
	if( {}.toString.call(needle).slice(8, -1) != 'Array' ) {
		arr = new Array();
		arr[0] = needle;
		needle = arr;
	}
	if( {}.toString.call(haystack).slice(8, -1) != 'Array' ) {
		arr = new Array();
		arr[0] = haystack;
		haystack = arr;
	}
	
	for ( var h = 0; h < haystack.length; h++ ) {
		for ( var n = 0; n < needle.length; n++ ) {
	        if ( haystack[h] == needle[n] ) {
	            return true;
		    }
		}
	}
    return false;
}