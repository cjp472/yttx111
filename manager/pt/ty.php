<?php
$menu_flag = "ty";
define('READ_EXP',true);
include_once ("header.php");
include_once ("../class/ip2location.class.php");

if($in['m'] == 'mark_reg') {
    if(!empty($in['phones'])) {
        $sql = "UPDATE ".DATABASEU.DATATABLE."_experience_contact SET IsReg='T' WHERE INSTR(',".$in['phones'].",',CONCAT(',',Phone,','))";
        $db->query($sql);
    }
    exit('ok');
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
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
            $("#bdate").datepicker({changeMonth: true,	changeYear: true});
            $("#edate").datepicker({changeMonth: true,	changeYear: true});
        });
    </script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <div class="leftdiv">
            <form id="FormSearch" name="FormSearch" method="get" action="ty.php">

                <label>
                    &nbsp;&nbsp;姓名，电话： <input type="text" name="kw" value="<?php echo $in['kw']; ?>" id="kw" class="inputline" />
                </label>
                <label>
                    <select name="region" id="region" class="select2" style="width:120px;">
                        <option value="">全部地区</option>
                        <option value="四川成都市" <?php echo $in['region'] == "四川成都市" ? "selected='selected'" : "";  ?> >成都地区</option>
                        <option value="OTHER" <?php echo $in['region'] == "OTHER" ? "selected='selected'" : "";  ?> >其它地区</option>
                    </select>
                </label>
                <label>
                    时间段：
                </label>
                <label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
                <label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

                </label>
                <label>
                    <select name="IsReg" id="IsReg" class="select2" style="width:120px;">
                        <option value="">全部</option>
                        <option value="T" <?php echo $in['IsReg'] == "T" ? "selected='selected'" : "";  ?> >已注册</option>
                        <option value="F" <?php echo $in['IsReg'] == "F" ? "selected='selected'" : "";  ?> >未注册</option>
                    </select>
                </label>
                <label>
                    <select name="UniqueP" id="UniqueP" class="select2" style="width:120px;">
                        <option value="">全部数据</option>
                        <option value="T" <?php echo $in['UniqueP'] == "T" ? "selected='selected'" : "";  ?> >手机号去重</option>
                    </select>
                </label>
                <label>
                    <input type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
                </label>
            </form>
        </div>

        <div class="location"><strong>当前位置：</strong><a href="ty.php">体验信息</a> </div>
    </div>

    <div class="line2"></div>
    <div class="bline">

        <?php
        $sqlmsg = '';
        $groupmsg = '';
        if(!empty($in['kw']))  $sqlmsg .= " and (ContactName like '%".$in['kw']."%' or Phone like '%".$in['kw']."%' ) ";

        if(!empty($in['bdate'])) {
            $sqlmsg .= " AND Date >= " . strtotime($in['bdate'] . ' 00:00:00');
        }
        if(!empty($in['edate'])) {
            $sqlmsg .= " AND Date <= " . strtotime($in['edate'] . ' 23:59:59');;
        }

        if(!empty($in['region'])) {
            if($in['region'] == 'OTHER') {
                $sqlmsg .= " AND Region<>'四川成都市' ";
            } else {
                $sqlmsg .= " AND Region='{$in['region']}' ";
            }

        }
        if(!empty($in['IsReg'])) {
            $sqlmsg .= " AND IsReg = '" . $in['IsReg']."'";
        }
        if(!empty($in['UniqueP'])) {
            $groupmsg .= " GROUP BY Phone ";
        }
        
        $InfoDataNum = $db->get_results("SELECT IsReg,VisitType,COUNT(*) as Total FROM (
                                            SELECT o.* FROM ".DATABASEU.DATATABLE."_experience_contact o ".$groupmsg." ORDER BY o.Date ASC".
            ") AS EC where 1=1 ".$sqlmsg." GROUP BY IsReg,VisitType");
            
        $CNT['F'] = $CNT['T'] = array();
        foreach($InfoDataNum as $calc)
        {
            $CNT[$calc['IsReg']][$calc['VisitType']] = $calc['Total'];
        }
        
        $allrow = array_sum($CNT['F']) + array_sum($CNT['T']);

        $page = new ShowPage;
        $page->PageSize = 50;
        $page->Total =  $allrow;
        $page->LinkAry = array("kw"=>$in['kw'],'bdate' => $in['bdate'],'edate' => $in['edate'],'region' => $in['region'],'IsReg' => $in['IsReg'],'UniqueP' => $in['UniqueP']);

        $datasql   = "SELECT * FROM (
                            SELECT o.* FROM ".DATABASEU.DATATABLE."_experience_contact o ".$groupmsg." ORDER BY o.Date ASC".
                     ") AS EC where 1=1 ".$sqlmsg." ORDER BY ID DESC";
        $list_data = $db->get_results($datasql." ".$page->OffSet());
        ?>
        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold">
                         <label><strong>共 <?php echo $allrow; ?> 条，</strong></label>
                         <label>未注册体验用户&nbsp;&nbsp;<?php echo array_sum($CNT['F']);?>&nbsp;人，&nbsp;</label>
                         <label>
                                                                                     其中电脑端&nbsp;&nbsp;<?php echo intval($CNT['F']['PC']); ?>&nbsp;人，
                                                                                     移动端&nbsp;&nbsp;<?php echo intval($CNT['F']['MOBILE']); ?>人； &nbsp;
                         </label>
                         <label>已注册体验用户&nbsp;&nbsp;<?php echo array_sum($CNT['T']); ?>&nbsp;人，&nbsp;</label>
                         <label>
                                                                                     其中电脑端&nbsp;&nbsp;<?php echo intval($CNT['T']['PC']); ?>&nbsp;人，
                                                                                     移动端&nbsp;&nbsp;<?php echo intval($CNT['T']['MOBILE']); ?>人 &nbsp;
                         </label>
       				 </td>
					 <td align="right"  height="30" class="bottomlinebold">
					 </td>
     			 </tr>
              </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <td width="8%" class="bottomlinebold">编号</td>
                    <td width="10%" class="bottomlinebold">联系人</td>
                    <td width="10%" class="bottomlinebold">联系方式</td>
                    <td width="10%" class="bottomlinebold">添加时间</td>
                    <td class="bottomlinebold">备注</td>
                    <td width="20%" class="bottomlinebold">IP地址</td>
                    <td width="8%" class="bottomlinebold">是否注册</td>
                    <td width="8%" class="bottomlinebold">状态</td>
                    <td width="8%" class="bottomlinebold">来源</td>
                    <td width="6%" class="bottomlinebold">操作</td>
                </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($list_data)) {

                    $phones = array_column($list_data,'Phone',null);
                    $phones = array_unique(array_filter($phones));
                    $phone_str = implode(',',$phones);
                    $record = $db->get_results("SELECT ID,Phone FROM ".DATABASEU.DATATABLE."_experience_contact WHERE INSTR('{$phone_str}',Phone) AND Phone <> '' ");
                    $valid = array();
                    foreach($record as $rec) {
                        $valid[$rec['Phone']][] = $rec['ID'];
                    }

                    $IPAddress = new IPAddress();
                    foreach($list_data as $lsv)
                    {
                        if($lsv['Region']) {
                            $localArea = $lsv['Region'];
                        } else {
                            $iparr = explode(",",$lsv['IP']);
                            $IPAddress->qqwry($iparr[0]);
                            $localArea = $IPAddress->replaceArea();
                            $db->query("UPDATE ".DATABASEU.DATATABLE."_experience_contact SET Region='{$localArea}' WHERE ID=" . $lsv['ID']);
                        }

                        ?>
                        <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" <?php if(count($valid[$lsv['Phone']]) > 1 && $lsv['ID'] != min($valid[$lsv['Phone']]) ) { echo "style='background-color:rgba(239, 220, 94, 0.76);'"; } ?>  >
                            <td><? echo $lsv['ID']; ?></td>
                            <td><? echo $lsv['ContactName'];?></td>
                            <td><? echo $lsv['Phone'];?></td>
                            <td><? echo date("y-m-d H:i",$lsv['Date']);?></td>
                            <td><? echo $lsv['Remark'];?></td>
                            <td><? echo $localArea." (".$lsv['IP'].")";?></td>
                            <td data-phone="<?php echo $lsv['IsReg'] == 'F' ? $lsv['Phone'] : ''; ?>">
                                <?php echo $lsv['IsReg'] == 'T' ? '已注册' : '未注册'; ?>
                            </td>
                            <td ><? echo $contact_arr[$lsv['Status']];?></td>
                            <td ><? echo $lsv['VisitType'] == 'PC' ? '电脑' : '移动设备';?></td>
                            <td >
                                [<a href="ty_contact.php?id=<?php echo $lsv['ID']; ?>">回访记录</a>]
                            </td>
                        </tr>
                    <? } }else{?>
                    <tr>
                        <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
                    </tr>
                <? }?>
                </tbody>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="4%"  height="30" ></td>

                    <td  align="right"><? echo $page->ShowLink('ty.php');?></td>
                </tr>
            </table>
            <INPUT TYPE="hidden" name="referer" value ="" >
        </form>

    </div>
    <br style="clear:both;" />
</div>

<? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<script type="text/javascript">
    $(function(){
        var phone = [];
        $("td[data-phone]").each(function(){
            var p = $(this).attr('data-phone');
            if(p) {
                phone.push(p);
            }

        });
        if(phone.length > 0) {
            $.post("do_company.php",{
                m:'is_reg_check',
                phones:phone.join(',')
            },function(data){
                if(data.length > 0) {
                    $(data).each(function(){
                        $("td[data-phone='"+this.toString()+"']").html('已注册');
                    });

                    $.post("ty.php" , {
                        m:'mark_reg',
                        phones:data.join(',')
                    } , function(sdata){
                        console.log(sdata);
                    } , 'text');
                }
            },'json');
        }

    });
</script>
</body>
</html>