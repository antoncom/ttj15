<?php

$db = & JFactory::getDBO();
$db->setQuery("SELECT *  FROM  `#__components`
	WHERE `name` = 'TeamTime' and parent = 0");
$component_menu_data = $db->loadObject();

if ($component_menu_data) {
  $db->setQuery("SELECT *  FROM  `#__components`
		WHERE parent = {$component_menu_data->id}
		order by ordering desc limit 1");
  $component_submenu_data = $db->loadObject();

  $db->Execute("INSERT INTO `#__components`
		(`name`, `link`, `menuid`, `parent`,
			`admin_menu_link`,
			`admin_menu_alt`, `option`, `ordering`,
			`admin_menu_img`, `iscore`, `params`, `enabled`)
		VALUES ('DOTU', '', 0, {$component_menu_data->id},
			'option=com_teamtimecareer',
			'DOTU', 'com_teamtimecareer', " . ($component_submenu_data->ordering + 1) . ",
			'{$component_submenu_data->admin_menu_img}', 0, '', 1)");
}
