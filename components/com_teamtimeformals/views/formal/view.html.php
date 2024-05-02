<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class TeamlogFormalViewFormal extends JView {

	function display($tpl = null) {
		$mFormal = new TeamtimeformalsModelFormal();
		$user = & JFactory::getUser();

		$date = & JFactory::getDate();

		$post["created"] = $date->toMySQL();
		$post["doctype_id"] = $mFormal->getTemplateIdByGenerator("earnings");

		$post["from_period"] = JRequest::getVar("from_period", date("Y-m-01"));
		$post["until_period"] = JRequest::getVar("until_period", date("Y-m-31"));

		$post["project_id"] = $user->id;
		//$post["project_id"] = 73;

		$post["is_dynamic"] = true;
		$post["dynamic_url"] = $_SERVER["PHP_SELF"] . "?option=com_teamtimeformals";

		$format = "%d.%m.%Y"; //JText::_('DATE_FORMAT_LC4');
		$from_period = JHTML::_('date', $post["from_period"], $format);
		$until_period = JHTML::_('date', $post["until_period"], $format);
		$doctype = $mFormal->getDoctype($post["doctype_id"]);

		//$post["name"] = JText::_($doctype->name) . " " . sprintf(
		//		JText::_("FORMAL DOCUMENT TEMPLATENAME"), $from_period, $until_period);

		$res = $mFormal->generateContent($post, $doctype->using_in);

		$this->assignRef('formal_content', $res[0]);

		parent::display($tpl);
	}

}