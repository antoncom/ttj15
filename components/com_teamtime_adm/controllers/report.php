<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/*
   Class: ReportController
   The controller class for Report
*/
class ReportController extends JController {

 	/*
	   Function: Constructor

	   Parameters:

	      $default -

	   Returns:

	      See Also:

	*/
	function __construct($default = array()) {
		parent::__construct($default);
	}

	function display() {

		// set the default view
		$view = JRequest::getCmd('view');
		if (empty($view)) {
			JRequest::setVar('view', 'report');
		}

		parent::display();
	}
}

if(!function_exists("convert_to_links")){
	function convert_to_links($text){
		if(!function_exists("__convert_to_links")){
			function __convert_to_links($m){
				if(strpos($m[0], '<a') !== false || strpos($m[0], '>') !== false)
					return $m[0];

				$s = $m[2];
				$prefix = strpos($m[2], "http://") !== false? "" : "http://";
				if(strlen($s) > 30)
					$s = substr($s, 0, 20)."...".substr($s, -10);
				return '<a href="'.$prefix.$m[2].'" target=_blank>'.$prefix.$s.'</a>';
			}
		}

		$text = preg_replace(
			"/(?<!http:\\/\\/)(www)([^<\\s]+)/si",
			'http://www\\2',
			$text);
		$text = preg_replace_callback(
			"/(<a\s+[^>]+>http:\\/\\/[^<\\s]+)|(http:\\/\\/[^<\\s]+)/si",
			"__convert_to_links",
			$text);
		return $text;
	}
}