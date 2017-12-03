<?php
$menu_flag = "open";
include_once ("header.php");
include_once ("../class/ip2location.class.php");

$info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_check_log WHERE 1=1");

if($in['m'] == 'record_info') {
    //记录信息
    $cuser = $_SESSION['uinfo']['usertruename'];
    $user_name = $_SESSION['uinfo']['username'];
    $content = $in['content'];
    $count = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATABASEU.DATATABLE."_experience_log WHERE ContactID={$in['id']} LIMIT 1");
    $rst = $db->query("INSERT INTO ".DATABASEU.DATATABLE."_experience_log (ContactID,CreateDate,CreateUser,Content) VALUES ({$in['id']},".time().",'{$cuser}','{$content}')");
    if($rst) {
        if((int)$count == 0) {
            $db->query("UPDATE ".DATABASEU.DATATABLE."_experience_contact SET Status=2,AdminUser='{$user_name}' WHERE ID={$in['id']} LIMIT 1");
        }
        exit('ok');
    } else {
        exit('记录信息发生错误,请重试!');
    }
    exit;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='robots' content='noindex,nofollow' />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />

    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

    <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <div class="leftdiv">
            <form id="FormSearch" name="FormSearch" method="get" action="ty.php">

                <!--<label>
                    &nbsp;&nbsp;姓名，电话： <input type="text" name="kw" value="<?php /*echo $in['kw']; */?>" id="kw" class="inputline" />
                </label>
                <label>
                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
                </label>-->
            </form>
        </div>

        <div class="location"><strong>当前位置：</strong><a href="experience_contact.php">体验信息</a> </div>
    </div>

    <div class="line2"></div>
    <div class="bline">

        <?php



        $sql = "SELECT l.Flag,l.Remark,l.Time,l.AdminUser,c.CompanyName,c.CompanyPrefix,c.CompanyContact,c.CompanyMobile FROM ".DATABASEU.DATATABLE."_order_company_check_log AS l
LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS c ON c.CompanyID = l.CompanyID WHERE 1=1 order by id desc";
        $row_cnt = $db->get_var("SELECT COUNT(*) Total FROM ".DATABASEU.DATATABLE."_order_company_check_log WHERE 1=1");

        $page = new ShowPage;
        $page->PageSize = 30;
        $page->Total = $row_cnt;
        $page->LinkAry = array("kw"=>$in['kw']);

        $list_data = $db->get_results($sql . ' ' . $page->OffSet());

        ?>
        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <td width="5%" class="bottomlinebold">行号</td>
                    <td width="15%" class="bottomlinebold">平台名称</td>
                    <td width="10%" class="bottomlinebold">联系人</td>
                    <td width="13%" class="bottomlinebold">记录时间</td>
                    <td width="8%" class="bottomlinebold">审核标记</td>
                    <td class="bottomlinebold">内容</td>
                    <td width="10%" class="bottomlinebold">操作人</td>
                </tr>
                </thead>
                <tbody>
                <?
                $n = 1;
                if(!empty($list_data))
                {

                    if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
                    foreach($list_data as $lsv)
                    {
                        ?>
                        <tr id="line_<? echo $lsv['id'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                            <td ><? echo $n++;?></td>
                            <td ><?php echo $lsv['CompanyName']; ?></td>
                            <td ><? echo $lsv['CompanyContact'];?></td>
                            <td ><? echo date('Y-m-d H:i:s',$lsv['Time']);?></td>
                            <td ><?php echo $lsv['Flag'] == 'T' ? '通过' : '不通过'; ?></td>
                            <td ><? echo $lsv['Remark'];?></td>
                            <td >
                                <? echo $lsv['AdminUser']; ?>
                            </td>
                        </tr>
                    <? } }else{?>
                    <tr>
                        <td colspan="7" height="30" align="center">暂无该体验用户的联系信息!</td>
                    </tr>
                <? }?>
                </tbody>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="4%"  height="30" ></td>

                    <td  align="right"><? echo $page->ShowLink('company_verify_check2.php');?></td>
                </tr>
            </table>
        </form>

    </div>
    <br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">
    $(function(){
        $("body").on('click','.blockOverlay',function(){
            $.unblockUI();
        });
        $("#submit_record").on('click',function(){
            var fm = $("#MainForm_Back");
            $.post(fm.attr('action'),fm.serialize(),function(data){
                data = Jtrim(data);
                if(data == 'ok') {
                    $.blockUI({
                        message : '<p>记录成功</p>'
                    });
                    setTimeout(function(){
                        $.unblockUI();
                        window.location.reload();
                    },1000);
                } else {
                    $.blockUI({
                        message : '<p>'+data+'</p>'
                    });
                }
            },'text');
        });
    });
</script>
</body>
</html>