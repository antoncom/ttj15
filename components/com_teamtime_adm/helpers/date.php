<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class DateHelper{

	function formatTimespan($time, $format) {
		
		$hours   = intval($time / 60);
		$minutes = intval($time % 60);

		if (strlen($minutes) < 2) {
			$minutes = '0' . $minutes;
		}

		$format = str_replace('hr mi', ($hours > 0 ? $hours.' '.JText::_('Hrs.') : '').' '.
			($minutes > 0 ? $minutes.' '.JText::_('Min.') : '') , $format);

		$format = str_replace('HR MI', ($hours > 0 ? ($hours == 1 ? JText::_('Hour') : JText::_('Hours')) : '').' '.
			($minutes > 0 ? ($minutes == 1 ? JText::_('Minute') : JText::_('Minutes')) : '') , $format);

		$format = str_replace('h:m', $hours.':'.$minutes, $format);
		
		return trim($format);
	}

}