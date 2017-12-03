<?php
$menu_flag = "finance";
include_once ("header.php");
include_once ("../class/data.class.php");
include_once ("../class/sms.class.php");
include_once ("arr_data.php");

if(empty($in['m']) || $in['request_method']!="post")
{
    echo "error!";
    exit();
}

$ip = $_SERVER["REMOTE_ADDR"];
$in = $inv->_htmlentities($in);

if($in['m']=='do_add'){
    $in['data_SpecName'] = str_replace(array(',',',',','),'，',$in['data_SpecName']);
    $sign = $db->get_row("SELECT * FROM ".DATATABLE."_order_specification WHERE SpecType='".$in['data_SpecType']."' AND SpecName='".$in['data_SpecName']."' AND CompanyID=".$_SESSION['uinfo']['ucompany']);
    if($sign){
        echo ($in['data_SpecType'] ==  'Color' ? '颜色' : '规格') . ':' . $in['data_SpecName'].',已存在';
        exit;
    }
    $sign = empty($in['data_SpecNO']) ? false : $db->get_row("SELECT * FROM ".DATATABLE."_order_specification WHERE SpecType='".$in['data_SpecType']."' AND SpecNO='".$in['data_SpecNO']."' AND CompanyID=".$_SESSION['uinfo']['ucompany']);
    if($sign){
        echo '编号:'.$in['data_SpecNO'].',已存在!';
        exit;
    }
    $insert_sql = "INSERT INTO ".DATATABLE."_order_specification (SpecNO,SpecName,SpecType,CompanyID)VALUES('".$in['data_SpecNO']."','".$in['data_SpecName']."','".$in['data_SpecType']."',".$_SESSION['uinfo']['ucompany'].")";
    $rst = $db->query($insert_sql);
    if($rst!==false){
        echo 'ok';
    }else{
        echo '保存不成功!';
    }
    exit();
}else if($in['m']=='do_save'){
    $in['edit_SpecName'] = str_replace(array(',','，',','),'，',$in['edit_SpecName']);
    $sign = empty($in['edit_SpecNO']) ? false : $db->get_row("SELECT * FROM ".DATATABLE."_order_specification WHERE SpecType='".$in['edit_SpecType']."' AND SpecNO='".$in['edit_SpecNO']."' AND CompanyID=".$_SESSION['uinfo']['ucompany']);
    if($sign && $sign['SpecID']!=$in['update_id']){
        echo "编号:".$in['edit_SpecNO'].',已存在!';
        exit;
    }
    $sign = $db->get_row("SELECT * FROM ".DATATABLE."_order_specification WHERE SpecType='".$in['edit_SpecType']."' AND SpecName='".$in['edit_SpecName']."' AND CompanyID=".$_SESSION['uinfo']['ucompany']);
    if($sign && $sign['SpecID']!=$in['update_id']){
        echo ($in['edit_SpecType'] == 'Color' ? '颜色' : '规格' ).':'.$in['edit_SpecName'].',已存在!';
        exit;
    }
    $update_sql = "UPDATE ".DATATABLE."_order_specification SET SpecType='".$in['edit_SpecType']."',SpecNO='".$in["edit_SpecNO"]."',SpecName='".$in['edit_SpecName']."' WHERE SpecID=".$in['update_id'];
    $rst = $db->query($update_sql);
    if($rst!==false){
        echo 'ok';
    }else{
        echo "保存不成功!";
    }
    exit();
}else if($in['m'] = 'do_delete'){
    $del_sql = "DELETE FROM ".DATATABLE."_order_specification WHERE SpecType='".$in['SpecType']."' AND CompanyID=".$_SESSION['uinfo']['ucompany']." AND SpecID=".$in['ID'];
    $rst = $db->query($del_sql);
    if($rst!==false){
        echo 'ok';
    }else{
        echo '删除不成功!';
    }
    exit();
}
exit('非法操作!');
?>