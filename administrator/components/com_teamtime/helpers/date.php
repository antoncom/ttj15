<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

class DateHelper {

	function formatDate($date, $offset = null) {

		if (is_null($offset)) {
			$config = & JFactory::getConfig();
			$offset = $config->getValue('config.offset');
		}

		if (DateHelper::isToday($date, $offset)) {
			return JText::_('Today');
		}
		elseif (DateHelper::isYesterday($date, $offset)) {
			return JText::_('Yesterday');
		}
		else {
			return JHTML::_('date', $date, JText::_('DATE_FORMAT_SIMPLE'), $offset);
		}
	}

	function isToday($date, $offset = null) {

		if (is_null($offset)) {
			$config = & JFactory::getConfig();
			$offset = $config->getValue('config.offset');
		}

		$now = & JFactory::getDate();
		$now->setOffset($offset);
		$date = JFactory::getDate($date);
		$date->setOffset($offset);

		return date('Y-m-d', $date->toUnix(true)) == date('Y-m-d', $now->toUnix(true));
	}

	function isYesterday($date, $offset = null) {

		if (is_null($offset)) {
			$config = & JFactory::getConfig();
			$offset = $config->getValue('config.offset');
		}

		$now = & JFactory::getDate();
		$now->setOffset($offset);
		$date = JFactory::getDate($date);
		$date->setOffset($offset);

		return date('Y-m-d', $date->toUnix(true)) == date('Y-m-d', $now->toUnix(true) - 86400);
	}

	/*
	  Function: getDeltaOrWeekdayText
	  Get time delta of $date as text or the Weekday.

	  Returns:
	  Delta string.
	 */

	function getDeltaOrWeekdayText($date, $offset = null) {

		if (is_null($offset)) {
			$config = & JFactory::getConfig();
			$offset = $config->getValue('config.offset');
		}

		$now = & JFactory::getDate();
		$now->setOffset($offset);
		$date = & JFactory::getDate($date);
		$date->setOffset($offset);
		$delta = $now->toUnix(true) - $date->toUnix(true);

		if (DateHelper::isToday($date->toMySQL(), $offset)) {
			$hours = intval($delta / 3600);
			$hours = $hours > 0 ? $hours . JText::_('hr') : '';
			$mins = intval(($delta % 3600) / 60);
			$mins = $mins > 0 ? ' ' . $mins . JText::_('min') : '';
			$delta = $hours . $mins ? JText::sprintf('%s ago', $hours . $mins) : '';
		}
		else {
			$delta = JHTML::_('date', $date->toMySQL(true), JText::_(' %A')) . ',' . JHTML::_('date',
							$date->toMySQL(true), JText::_('DATE_FORMAT_SIMPLE'));
		}

		return $delta;
	}

	function formatTimespan($time, $format) {

		$hours = intval($time / 60);
		$minutes = intval($time % 60);

		if (strlen($minutes) < 2) {
			$minutes = '0' . $minutes;
		}

		$format = str_replace('hr mi',
				($hours > 0 ? $hours . ' ' . JText::_('Hrs.') : '') . ' ' .
				($minutes > 0 ? $minutes . ' ' . JText::_('Min.') : ''), $format);

		$format = str_replace('HR MI',
				($hours > 0 ? ($hours == 1 ? JText::_('Hour') : JText::_('Hours')) : '') . ' ' .
				($minutes > 0 ? ($minutes == 1 ? JText::_('Minute') : JText::_('Minutes')) : ''), $format);

		$format = str_replace('h:m', $hours . ':' . $minutes, $format);

		return trim($format);
	}

}