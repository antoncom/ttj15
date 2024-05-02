<?php



include('kcaptcha.php');
//echo var_dump($_REQUEST);

session_id($_GET['sid']);
session_name($_GET['sname']);
session_start();

//echo var_dump($_SESSION);
//exit;

$captcha = new KCAPTCHA();

if($_REQUEST[session_name()]){
	$_SESSION['captcha_keystring'] = md5($captcha->getKeyString());
}

?>