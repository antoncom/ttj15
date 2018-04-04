<?php
/*********** XML PARAMETERS AND VALUES ************/
$xml_item = "component";// component | template
$xml_file = "phocadocumentation.xml";		
$xml_name = "PhocaDocumentation";
$xml_creation_date = "05/02/2010";
$xml_author = "Jan Pavelka (www.phoca.cz)";
$xml_author_email = "";
$xml_author_url = "www.phoca.cz";
$xml_copyright = "Jan Pavelka";
$xml_license = "GNU/GPL";
$xml_version = "1.1.0";
$xml_description = "Phoca Documentation";
$xml_copy_file = 1;//Copy other files in to administration area (only for development), ./front, ./language, ./other

$xml_menu = array (0 => "Phoca Documentation", 1 => "option=com_phocadocumentation", 2 => "components/com_phocadocumentation/assets/images/icon-16-pdoc-menu.png");
$xml_submenu[0] = array (0 => "Control Panel", 1 => "option=com_phocadocumentation", 2 => "components/com_phocadocumentation/assets/images/icon-16-pdoc-control-panel.png");
$xml_submenu[1] = array (0 => "Info", 1 => "option=com_phocadocumentation&view=phocadocumentations", 2 => "components/com_phocadocumentation/assets/images/icon-16-pdoc-doc.png");
$xml_submenu[2] = array (0 => "Info", 1 => "option=com_phocadocumentation&view=phocadocumentationin", 2 => "components/com_phocadocumentation/assets/images/icon-16-pdoc-info.png");


$xml_install_file = 'install.phocadocumentation.php'; 
$xml_uninstall_file = 'uninstall.phocadocumentation.php';
/*********** XML PARAMETERS AND VALUES ************/
?>