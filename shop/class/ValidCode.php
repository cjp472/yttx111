<?php
include_once ("../header.inc.php"); 
include_once ("validcode.class.php");
$validcode = new validcode();
$v_code = $validcode->create_check_code(5);
$_SESSION['v_code'] = $v_code;
$validcode->create_check_image($v_code);
?>