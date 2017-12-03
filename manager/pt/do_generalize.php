<?php
$menu_flag = "open";
include_once ("header.php");

$in = $inv->_htmlentities($in);

if(empty($in['m']) || $in['request_method']!="post")
{
    echo "error!";
    exit();
}
/**
 * 请求登录次数在官网日志表里面
 */
if($in['m']=="generalize_select_generalize"){
//    $url = "http://www.dhb.com/?f=count_generalize";
    $param = array();
    $param['p'] = $in['s']; //请求参数
    $param['startTime'] = $in['startTime']; //请求参数
    $param['endTime'] = $in['endTime']; //请求参数
    $url = $in['url']; //请求地址
    //调用request_post模拟POST请求
    //http://www.dhb.com/?f=count_generalize $url demo
    $result = request_post($url, $param); //($url = '', $param = '')
    echo $result;
    exit;
}

//添加推广关系到数据库
if($in['m']=="generalize_add_save")
{
    //验证唯一 DATABASEU.DATATABLE
    $sql = "SELECT count(*) as a FROM ".DATABASEU.DATATABLE."_order_generalize where `generalizeNo` = '".$in['data_generalizeNo']."' limit 0,1";
    $setinfo = $db->get_row($sql);
    if(!empty($setinfo['a'])) exit('编号不能重复！');
    $in['saleId'] = empty($in['saleId'])?0:$in['saleId'];
    $insert_sql = "insert into ".DATABASEU.DATATABLE."_order_generalize(generalizeNo,saleId,generalizeName,generalizeType) values('".$in['data_generalizeNo']."',".$in['saleId'].",'".$in['data_generalizeName']."','".$in['data_generalizeType']."')";
    $isin = $db->query($insert_sql);
    $generalizeID = $db->insert_id;
    if($isin)
    {
        $in['data_url'] = ltrim($in['data_url'],'http://');
        $generalizeUrl = "http://".rtrim($in['data_url'],'/')."/a/".encodeParam("type=".$in['data_generalizeType']."&id=".$generalizeID."&no=".$in['data_generalizeNo']."&name=".$in['data_generalizeName'])."/";
        $sql_2 = "update ".DATABASEU.DATATABLE."_order_generalize set `generalizeUrl` = '".$generalizeUrl."' where `generalizeID`=".$generalizeID;
        $re_update = $db->query($sql_2);
        if($re_update)
        {
            exit("ok");
        }else{
            $db->query("delete from ".DATABASEU.DATATABLE."_order_generalize where `generalizeID` = ".$generalizeID." limit 1");
            exit("保存不成功!");
        }
    }else{
        exit("保存不成功!");
    }
}

if($in['m']=="generalize_edit_save")
{
    //验证唯一
    $update_id = intval($in['update_id']);
    $sql = "SELECT count(*) as a FROM ".DATABASEU.DATATABLE."_order_generalize where `generalizeNo` = '".$in['edit_GeneralizeNO']."' and `generalizeID` <> ".$update_id." limit 0,1";
    $setinfo = $db->get_row($sql);
    if(!empty($setinfo['a'])) exit('编号不能重复！');

    $in['edit_Url'] = ltrim($in['edit_Url'],'http://');
    $in['edit_saleId'] = empty($in['edit_saleId'])?0:$in['edit_saleId'];
    $generalizeUrl = "http://".rtrim($in['edit_Url'],'/')."/a/".encodeParam("type=".$in['edit_GeneralizeType']."&id=".$update_id."&no=".$in['edit_GeneralizeNO']."&name=".$in['edit_GeneralizeName'])."/";
    $isin = $db->query("replace into ".DATABASEU.DATATABLE."_order_generalize(generalizeID,saleId,generalizeNo,generalizeName,generalizeType,generalizeUrl) values(".$update_id.",".$in['edit_saleId'].",'".$in['edit_GeneralizeNO']."','".$in['edit_GeneralizeName']."','".$in['edit_GeneralizeType']."','".$generalizeUrl."')");
    if($isin)
    {
        exit("ok");
    }else{
        exit("保存不成功!");
    }
}

if($in['m']=="delete_generalize")
{
    if(empty($in['ID'])) exit('参数错误 ，请指定您要删除的内容！');
    $in['ID'] = intval($in['ID']);
    // todo 需要添加是否使用了该推广方式
    $id = intval($in['ID']);
    $isin = $db->query("delete from ".DATABASEU.DATATABLE."_order_generalize where `generalizeID` = ".$id." limit 1");
    if($isin)
    {
        exit("ok");
    }else{
        exit("删除不成功!");
    }
}
?>