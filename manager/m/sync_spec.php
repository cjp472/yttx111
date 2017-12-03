<?php
/**
 * Created by PhpStorm.
 * User: YF
 * Date: 2014/12/17
 * Time: 16:46
 * DESC: 同步产品规格&颜色
 */

include_once ("header.php");
header('Content-Type:text/html;charset=utf-8');
set_time_limit(0);
if($in['m']!=='sync'){
    echo '参数错误!';
    exit;
}

$sql = "SELECT
          CompanyID,
          GROUP_CONCAT(Color) AS Color,
          GROUP_CONCAT(Specification) AS Specification
        FROM
          ".DATATABLE."_order_content_index
        GROUP BY CompanyID";

$list = $db->get_results($sql);

if(!empty($list)){
    $spec = array();
    foreach($list as $val){
        $companyID = $val['CompanyID'];
        $color = fExists(arrUF($val['Color']),'Color',$companyID);
        $spec = fExists(arrUF($val['Specification']),'Specification',$companyID);
        specification($color,'Color',$companyID);
        specification($spec,'Specification',$companyID);
    }
    echo "数据同步完成!";
}else{
    exit('没有需要同步的数据!');
}

//XXX:array_chunk 100
function specification($arr,$specType,$companyID){
    global $db;
    if(empty($arr)){
        return true;
    }
    $header = "INSERT INTO ".DATATABLE."_order_specification (`SpecName`,`SpecType`,`CompanyID`) VALUES";
    $body = array();
    foreach($arr as $val){
        $body[] = "('{$val}','{$specType}',{$companyID})";
    }
    $db->query($header.implode(",",$body));

}

//过滤数据库中已存在的数据
function fExists($arr,$specType,$companyID){
    global $db;
    $apos = array_map('addApos',$arr);
    $sql = "SELECT SpecName FROM ".DATATABLE."_order_specification WHERE SpecType='".$specType."' AND CompanyID=".$companyID." AND SpecName IN(".implode(',',$apos).")";
    $exists = $db->get_col($sql);
    $exists = $exists ? $exists : array();
    return array_diff($arr,$exists);
}

function arrUF($str){
    $temp = explode(',',$str);
    $temp = array_filter($temp);
    foreach($temp as $key=>$val){
        $temp[$key] = trim($val);
    }
    $temp = array_unique($temp);
    return $temp;
}

function addApos($item){
    return "'".$item."'";
}

