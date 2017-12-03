<?php
    $menu_flag = "sale_count";
    include_once ("header.php");

    if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<!--<link rel="stylesheet" href="css/jquery.treeview.css" />-->
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/generalize.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>

<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
            <form id="FormSearch" name="FormSearch" method="get" action="">
                <tr>
                    <td></td>
                    <td align="right"><div class="location"><strong>当前位置：</strong><a href="javascript:history.go(0);"><?php echo $_GET['name']?>&nbsp;&nbsp;的个性化地址</a></div></td>
                </tr>
            </form>
        </table>
    </div>

    <div class="line2"></div>
    <div class="bline">


        <div style="width:96%; margin:2px auto;">
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <div class="font12h">新增个性化地址:</div>
                <table width="80%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%" class="bold">编号:</td>
                        <td width="16%"><input type="text" name="data_generalizeNo" id="data_generalizeNo" value="" /></td>
                        <td width="4%" class="bold" >类型:</td>
                        <td width="10%" ><input style="width: 90%" readonly type="text" value="seller" id="data_generalizeType" name="data_generalizeType"/></td>
                        <td width="4%" class="bold" >地址:</td>
                        <td width="15%" ><input style="width: 90%" type="text" id="data_url" name="data_url"/></td>
                        <td >
                            <input value="<?php echo $_GET['saleId']?>" name="saleId" type="hidden"/>
                            <input type="button" name="savebutton" id="savebutton" value="保 存" class="button_2" onclick="do_save_generalize();" /> </td>
                    </tr>
                </table>

                <hr />

                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <td width="8%" class="bottomlinebold">行号</td>
                        <td width="10%" class="bottomlinebold">编号</td>
                        <td width="10%" class="bottomlinebold">类型</td>
                        <td width="10%" class="bottomlinebold">次数</td>
                        <td class="bottomlinebold">个性化地址</td>
                        <td width="10%" class="bottomlinebold" align="center">管理</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    $datasql   = "SELECT generalizeID,generalizeNo,generalizeType,generalizeUrl FROM ".DATABASEU.DATATABLE."_order_generalize where generalizeType = 'seller' and saleId = ".$_GET['saleId']." Order by generalizeID Desc";
                    $list_data = $db->get_results($datasql);
                    if(!empty($list_data))
                    {
                        $n=1;
                        $hasGno = ''; //当前页的编号数据
                        foreach($list_data as $lsv)
                        {
                            $hasGno = $hasGno.$lsv['generalizeNo'].',';
                            $ids = $ids.$lsv['generalizeID'].',';
                            ?>
                            <tr id="line_<? echo $lsv['generalizeID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                                <td ><? echo $n++;?></td>
                                <td ><? echo $lsv['generalizeNo'];?></td>
                                <td ><strong><? echo $lsv['generalizeType'];?></strong></td>
                                <td ><strong><span id="relation-<?php echo $lsv['generalizeID'];?>"><img src="img/loader.gif"/></span></strong></td>
                                <td >
                                    <? echo $lsv['generalizeUrl'];?>
                                    <a target="_blank" href="createWeixin.php?text=<? echo $lsv['generalizeUrl'];?>&size=400&date=<? echo random(10);?>">[二维码]</a>
                                </td>
                                <td align="center">
                                    <a href="#editbill" onclick="set_edit_bill('<?php echo $lsv['generalizeID']; ?>','<?php echo $lsv['generalizeNo']; ?>','<?php echo $lsv['generalizeName']; ?>','<?php echo $lsv['generalizeType']; ?>','<?php echo $lsv['generalizeUrl']; ?>')" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_bill('<? echo $lsv['generalizeID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>
                                </td>
                            </tr>
                        <? }
                        echo '<tr><td></td><td></td><td></td><td id="totalNum" style="font-weight:bold;"></td></tr>';
                    }else{?>
                        <tr>
                            <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
                        </tr>
                    <? }?>
                    </tbody>
                </table>
                <a name="editbill"></a>
                <div id="edit_generalize"  style="display:none;">
                    <div class="font12h">新增个性化地址:</div>
                    <INPUT TYPE="hidden" name="update_id" id="update_id" value ="" >
                    <table width="80%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td class="bold" width="4%">编号:</td>
                            <td width="16%" ><input type="text" name="edit_GeneralizeNO" id="edit_GeneralizeNO" value="" /></td>
                            <td width="4%" class="bold" >类型:</td>
                            <td width="10%" ><input style="width: 90%" readonly type="text" id="edit_GeneralizeType" name="edit_GeneralizeType"/></td>
                            <td width="4%" class="bold" >地址:</td>
                            <td width="15%" ><input style="width: 90%" type="text" id="edit_Url" name="edit_Url"/></td>
                            <td >
                                <input value="<?php echo $_GET['saleId']?>" name="edit_saleId" type="hidden"/>
                                <input type="button" name="editbutton" id="editbutton" value="保 存" class="button_2" onclick="do_edit_generalize();" /> </td>
                        </tr>
                    </table>
                </div>

                <INPUT TYPE="hidden" name="referer" value ="" >
            </form>
        </div>
    </div>
    <br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>
<script src="js/function.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        var hasGn = "<?php echo $hasGno;?>";
        var ids = "<?php echo $ids;?>";
        $.post(
            "do_generalize.php",
            {
                m:"generalize_select_generalize", //请求方法
                url:"<?php echo DHB_HK_URL;?>/?f=count_generalize", //请求第三方数据源地址
                p:Math.random(),
                s:hasGn
            },
            function(result){
                var obj = ids.split(",");
                for(var j=0,len=obj.length;j<len;j++){
                    $("#relation-"+obj[j]).text(0);
                }
                var totalNum = 0;
                if(result.length != 0){
                    for(var i=0;i < result.length;i++) {
                        totalNum = Number(totalNum) + Number(result[i].count);
                        $("#relation-"+result[i].generalizeID).text(result[i].count);
                    }
                }
                $("#totalNum").html("总共："+totalNum+"次");
            },'json');
    });
</script>