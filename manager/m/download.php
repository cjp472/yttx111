<?php
include_once ("header.php");
$ua = $_SERVER["HTTP_USER_AGENT"];
if($in['m']=='accounts'){
    $path = "http://resource.dhb.hk/file/dhb_allinpay.rar";
    $file = 'tonglian.docx';
    $saveName = '通联支付网上支付商户信息表.docx';

    $output = "";
    $filePath = 'img/explanations/file/'.$file;
    $fp = fopen($filePath,'r');
    while(!@feof($fp)){
        $output .= fread($fp,1024);
    }

}else{
    echo "非法操作";
    exit();
}

$encoded_filename = urlencode($saveName);
$encoded_filename = str_replace("+", "%20", $saveName);

header('Content-Type: application/octet-stream');
if (preg_match("/MSIE/", $ua)) {
    header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
} else if (preg_match("/Firefox/", $ua)) {
    header('Content-Disposition: attachment; filename*="utf8\'\'' . $saveName . '"');
} else {
    header('Content-Disposition: attachment; filename="' . $saveName . '"');
}

echo $output;
exit;