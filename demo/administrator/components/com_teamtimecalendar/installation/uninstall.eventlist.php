<?php

$db =& JFactory::getDBO();
$db->setQuery("SELECT *  FROM  `#__components`
	WHERE `name` = 'TeamTime' and parent = 0");
$component_menu_data = $db->loadObject();

if($component_menu_data){
	$db->Execute("delete from `#__components`
		where parent = {$component_menu_data->id} and name = 'Calendar'");
}