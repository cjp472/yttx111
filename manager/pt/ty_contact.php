<?php
$menu_flag = "ty";
define('READ_EXP',true);
include_once ("header.php");
include_once ("../class/ip2location.class.php");

$info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_experience_contact WHERE ID=" . $in['id']);

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
        $sql = "SELECT * FROM ".DATABASEU.DATATABLE."_experience_log WHERE ContactID=" . $in['id'] . " ORDER BY CreateDate ASC";
        $list_data = $db->get_results($sql);

        ?>
        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <td width="5%" class="bottomlinebold">行号</td>
                    <td width="10%" class="bottomlinebold">联系人</td>
                    <td width="13%" class="bottomlinebold">记录时间</td>
                    <td class="bottomlinebold">内容</td>
                    <td width="10%" class="bottomlinebold">操作</td>
                </tr>
                </thead>
                <tbody>
                <?
                $n = 1;
                if(!empty($list_data))
                {

                    if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
                    $IPAddress = new IPAddress();
                    foreach($list_data as $lsv)
                    {
                        $iparr = explode(",",$lsv['IP']);
                        $IPAddress->qqwry($iparr[0]);
                        $localArea = $IPAddress->replaceArea();

                        ?>
                        <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                            <td ><? echo $n++;?></td>
                            <td ><? echo $lsv['CreateUser'];?></td>
                            <td ><? echo date('Y-m-d H:i:s',$lsv['CreateDate']);?></td>
                            <td ><? echo $lsv['Content'];?></td>
                            <td >
                                --
                            </td>
                        </tr>
                    <? } }else{?>
                    <tr>
                        <td colspan="5" height="30" align="center">暂无该体验用户的联系信息!</td>
                    </tr>
                <? }?>
                </tbody>
            </table>
        </form>
        <div style="border-top:2px solid #ccc;">
            <form id="MainForm_Back"  method="post" action="ty_contact.php" target="exe_iframe" >
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td align="center" style="line-height:40px;">
                            留言用户：
                        </td>
                        <td><?php echo $info['ContactName']; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td align="center" style="line-height:40px;">联系电话：</td>
                        <td><?php echo $info['Phone']; ?></td>
                        <td></td>
                    </tr>
                        <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                            <td align="center" width="20%">
                                记录内容：
                            </td>
                            <td>
                                <input name="m" value="record_info" type="hidden"/>
                                <input name="id" value="<?php echo $info['ID'] ?>" type="hidden"/>
                                <textarea name="content" style="width:850px;" rows="5"></textarea>
                            </td>
                            <td align="center" width="30%">
                                <input type="button" class="button_2" id="submit_record" value="提交"/>
                                <input class="button_3" type="button" value="返回" onclick="history.back();"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>

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