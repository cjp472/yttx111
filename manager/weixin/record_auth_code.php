<?php
    include_once('../common.php');

    $ip = $_GET['ip'];
    $auth_code = $_GET['auth_code'];

    // $table = 'db_dhb_ty_user.rsung_buy_sms';
    $table = DB_DATABASE . '.rsung_by_sms';
    
    $db  = dbconnect::dataconnect()->getdb();

    $db->query("INSERT INTO " . $table . "(type, code, time, ip) VALUES('valid', " . mysql_real_escape_string($auth_code) . ", " . time() . " ,'" . mysql_real_escape_string($ip) . "')");

    exit;
?>