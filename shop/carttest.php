<?php
/**
 * @desc 纸板测试页面
 */
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/cart.class.php");

$input		=	new Input;
$in			=	$input->parse_incoming();
$in			=  $input->_htmlentities($in);

$isnotshowloadcart = true;

$fp = array('+','/','=','_');
$rp = array('-','|','DHB',' ');
$in['m'] = $in['m'] ? $in['m'] : 'cart';
$price = array(
    "D515D-A"=>2.7414,'D515D-B'=>2.8570,'D515D-C'=>1.9547,'D515D-E'=>2.3542,'D515D-G'=>2.9742,'D515D-F'=>1.5478,'D515D-AB'=>1.6547,'D515D-BC'=>2.0121,'D515D-BAB'=>2.6587,'D515D-BCA'=>1.7985,'D515D-BE'=>2.0123,

    "D516C-A"=>2.5456,'D516C-B'=>2.3985,'D516C-C'=>1.4511,'D516C-E'=>1.9854,'D516C-G'=>2.3654,'D516C-F'=>2.8521,'D516C-AB'=>1.4875,'D516C-BC'=>3.0100,'D516C-BAB'=>1.7896,'D516C-BCA'=>1.1547,'D516C-BE'=>2.1021,

    "D525D-A"=>2.6521,'D525D-B'=>2.1698,'D525D-C'=>2.6587,'D525D-E'=>2.1365,'D525D-G'=>2.9784,'D525D-F'=>2.1865,'D525D-AB'=>2.1365,'D525D-BC'=>2.5478,'D525D-BAB'=>2.2145,'D525D-BCA'=>2.3214,'D525D-BE'=>2.9632,

    "D545D-A"=>3.0123,'D545D-B'=>3.1254,'D545D-C'=>3.6541,'D545D-E'=>3.2532,'D545D-G'=>3.1487,'D545D-F'=>2.8541,'D545D-AB'=>2.7653,'D545D-BC'=>2.5687,'D545D-BAB'=>2.3654,'D545D-BCA'=>2.6479,'D545D-BE'=>2.3482,

    "D555C-A"=>3.2145,'D555C-B'=>3.158,'D555C-C'=>3.1457,'D555C-E'=>3.1111,'D555C-G'=>3.2112,'D555C-F'=>3.3113,'D555C-AB'=>2.3132,'D555C-BC'=>1.1987,'D555C-BAB'=>2.1990,'D555C-BCA'=>2.1988,'D555C-BE'=>2.1654,

    "D555D-A"=>2.3250,'D555D-B'=>2.1487,'D555D-C'=>2.1697,'D555D-E'=>2.1654,'D555D-G'=>2.3333,'D555D-F'=>3.2258,'D555D-AB'=>2.6974,'D555D-BC'=>2.3645,'D555D-BAB'=>2.3658,'D555D-BCA'=>2.9763,'D555D-BE'=>2.3125,

    "D557C-A"=>3.3210,'D557C-B'=>2.3654,'D557C-C'=>2.2978,'D557C-E'=>2.6547,'D557C-G'=>2.3641,'D557C-F'=>2.3698,'D557C-AB'=>2.1984,'D557C-BC'=>2.8365,'D557C-BAB'=>2.3654,'D557C-BCA'=>2.3965,'D557C-BE'=>2.1387,

    "D5C-A"=>2.1236,'D5C-B'=>2.1458,'D5C-C'=>2.1458,'D5C-E'=>2.1395,'D5C-G'=>2.9871,'D5C-F'=>2.6547,'D5C-AB'=>2.6352,'D5C-BC'=>2.3624,'D5C-BAB'=>2.9654,'D5C-BCA'=>2.3675,'D5C-BE'=>2.1458,

    "D5D-A"=>2.1452,'D5D-B'=>2.6985,'D5D-C'=>2.3214,'D5D-E'=>2.3214,'D5D-G'=>2.6985,'D5D-F'=>2.1456,'D5D-AB'=>2.8745,'D5D-BC'=>2.3216,'D5D-BAB'=>2.3286,'D5D-BCA'=>2.1478,'D5D-BE'=>2.3698,

    "D5D4-A"=>3.2145,'D5D4-B'=>3.2654,'D5D4-C'=>1.3698,'D5D4-E'=>2.9875,'D5D4-G'=>3.2145,'D5D4-F'=>2.6654,'D5D4-AB'=>3.2245,'D5D4-BC'=>3.2655,'D5D4-BAB'=>1.9987,'D5D4-BCA'=>2.3145,'D5D4-BE'=>2.3322,

    "D5W3-A"=>2.1452,'D5W3-B'=>2.2241,'D5W3-C'=>2.3321,'D5W3-E'=>2.3214,'D5W3-G'=>2.1458,'D5W3-F'=>3.2145,'D5W3-AB'=>2.9514,'D5W3-BC'=>2.3214,'D5W3-BAB'=>3.1120,'D5W3-BCA'=>1.0214,'D5W3-BE'=>1.9021,

    "D5Y5C-A"=>2.0125,'D5Y5C-B'=>3.1502,'D5Y5C-C'=>2.1750,'D5Y5C-E'=>3.0145,'D5Y5C-G'=>1.2100,'D5Y5C-F'=>2.1452,'D5Y5C-AB'=>2.3547,'D5Y5C-BC'=>2.1654,'D5Y5C-BAB'=>2.1785,'D5Y5C-BCA'=>2.1795,'D5Y5C-BE'=>3.0012,
);
if($in['m']=='cart'){
    $cz = explode(",","D515D,D516C,D525D,D545D,D555C,D555D,D557C,D5C,D5D,D5D4,D5W3,D5Y5C");
    $lb = explode(",","A,B,C,E,G,F,AB,BC,BAB,BCA,BE");
    $yx = explode(",","五点凹凸型");

    $price = json_encode($price);
    include template("carttest");
}else{
    $data = array();
    $amount = 0;
    foreach($in['isClone'] as $k=>$v){
        if($in['isClone'][$k]==1 || empty($in['long'][$k]) || empty($in['width'][$k]) || empty($in['num'][$k]) || $in['num'][$k]==0){
            continue;
        }
        $total = 0;
        $p = $price[$in['cz'][$k].'-'.$in['lb'][$k]];
        $p += $in['trimming'] == 1 ? 0.05 : 0;
        $p += $in['bundle'] == 1 ? 0.05 : 0;
        $p += $in['crimping'] == 1 ? 0.05 : 0;
        $total = $p * $in['num'][$k];
        $data[] = array(
            'cz'=>$in['cz'][$k],
            'lb'=>$in['lb'][$k],
            'long'=>$in['long'][$k],
            'width'=>$in['width'][$k],
            'crimping'=>$in['crimping'][$k],
            'num'=>$in['num'][$k],
            'crimpingType'=>$in['crimpingType'][$k],
            'trimming'=>$in['trimming'][$k],
            'bundle'=>$in['bundle'][$k],
            'date'=>$in['date'][$k],
            'produceNote'=>$in['produceNote'][$k],
            'shippingNote'=>$in['shippingNote'][$k],
            'orderNote'=>$in['orderNote'][$k],
            'price'=>$p,
            'total'=>$total,
        );
        $amount += $total;
    }
    include template("carttest1");
}

?>