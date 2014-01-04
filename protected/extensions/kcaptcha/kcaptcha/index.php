<?php

error_reporting(0);

include('kcaptcha.php');

session_start();

$captcha = new KCAPTCHA();

if($_REQUEST[session_name()]){
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
}

?>