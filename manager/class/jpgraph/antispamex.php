<?php
ini_set('session.save_path','../../sessiontemp');
session_start();
unset($_SESSION['vcode']);
require_once "jpgraph_antispam.php";
$spam = new AntiSpam();
$chars = $spam->_seed(5,1);
$_SESSION['vcode'] = $chars;
if( $spam->Stroke() === false ) {
    die('Illegal or no data to plot');
}
?>

