<?php
$menu_flag = "common_count";
include_once ("header.php");
if(!empty($in['ID']))
{
    $companyinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");

    $csql = "SELECT v.*,u.UserTrueName FROM ".DATABASEU.DATATABLE."_order_company_contact v LEFT JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUID=u.UserID where v.CompanyID =".$in['ID']." ORDER BY ID DESC";
    $cinfo = $db->get_results($csql);
    
    $vDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_visit v where v.CreateUID is not null AND v.CompanyID =".$in['ID']);
     
    $vpage = new ShowPage;
    $vpage->PageSize = 20;
    $vpage->Total = $vDataNum['allrow'];
    $vpage->LinkAry = array("ID"=>$in['ID']);
    
    $vsql = "SELECT u.UserTrueName,v.* FROM ".DATABASEU.DATATABLE."_order_company_visit v LEFT JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUID=u.UserID WHERE v.CreateUID is not null AND v.CompanyID =".$in['ID']. " ORDER BY UpdateDate DESC";
    $vinfo = $db->get_results($vsql." ".$vpage->OffSet());
    
    $clientInfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_remarks WHERE CompanyID =".$in['ID']);
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
    <style type="">
        .radio input{width:auto;}
    </style>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>
    <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
    <div id="searchline">
    	<div class="leftdiv width300" style="width:70%">
    	   <div class="locationl"><strong>当前位置：</strong><a href="manager_company.php?ID=<?php echo $in['ID'];?>"><?php echo $companyinfo['CompanyName']?>></a> &#8250;&#8250; 联系人/回访信息</div>
        </div>
    </div>

    <div class="line2"></div>
        <div class="bline">
            <div id="" style="width:100%; margin:4px;">  
                <!-- 公司基本信息Start -->
                    <fieldset title="基本资料" class="fieldsetstyle">
                      <legend>基本资料</legend>
                      <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                          <tr>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><label>
                                      <? echo $companyinfo['BusinessLicense'];?>
                                  </label></td>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">营业执照号：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><label>
                                      <? echo $companyinfo['IdentificationNumber'];?>
                                  </label></td>
                          </tr>
                          <tr>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">订货系统名称：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><label>
                                      <? echo $companyinfo['CompanyName'];?>
                                  </label></td>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanySigned'];?></td>
                          </tr>
                          <tr>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyPrefix'];?></td>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyCity'];?></td>
                          </tr>        
                          <tr>       
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">联系人：</div></td>       
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyContact'];?></td>       
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>       
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyMobile'];?>        
                          </tr>        
                          <tr>        
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>       
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyPhone'];?>        
                              </td>        
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">传 真：</div></td>        
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyFax'];?></td>        
                          </tr>        
                          <tr>        
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>        
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyAddress'];?></td>        
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>       
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyEmail'];?>&nbsp;</td>        
                          </tr>        
                          <tr>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyWeb'];?></td>
                              <td width="15%" bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                              <td width="35%" bgcolor="#FFFFFF"><? echo $companyinfo['CompanyUrl'];?>&nbsp;</td>
                          </tr>       
                          <tr>       
                              <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>        
                              <td bgcolor="#FFFFFF"><? echo nl2br($companyinfo['CompanyRemark']);?>&nbsp;</td>
                              <td bgcolor="#FFFFFF">&nbsp;</td>
                          </tr>
                      </table>
                  </fieldset>
                <!-- 公司基本信息End -->
                
                <br style="clear:both;" />
                <!-- 客户信息Start -->
                    <fieldset title="客户信息" class="fieldsetstyle">
                          <legend>客户信息</legend>
                          <input type="button" class="mainbtn" style="float: right;" onclick="change_company_remarks();" value="修改客户信息" title="修改客户信息" />
                          <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                              <tr>
                                  <td width="15%" bgcolor="#F0F0F0"><div align="right">要求开通时间：</div></td>
                                  <td width="35%" bgcolor="#FFFFFF"><label>
                                          <? echo $clientInfo['OpenDate'];?>
                                      </label></td>
                                  <td width="15%" bgcolor="#F0F0F0"><div align="right">回访提醒时间：</div></td>
                                  <td width="35%" bgcolor="#FFFFFF"><label>
                                          <? echo $clientInfo['VisitDate'];?>
                                      </label></td>
                              </tr>
                              <tr>
                                  <td width="15%" bgcolor="#F0F0F0"><div align="right">客户简介：</div></td>
                                  <td width="35%" bgcolor="#FFFFFF" collspan="3"><label><? echo $clientInfo['ClientInfo'];?></label></td>
                              </tr>
                          </table>
                      </fieldset>
                <!-- 客户信息End -->
                
                <br style="clear:both;" />
                <!-- 公司基本信息End -->
                    <fieldset title="设置" class="fieldsetstyle">
                          <legend>设置</legend>
                          <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                              <tr>
                                  <td bgcolor="#F0F0F0" width="16%"><div align="right">用户数：</div></td>
                                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $companyinfo['CS_Number'];?>&nbsp;</td>
                                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(限制客户经销商的个数)</td>
                              </tr>
                              <tr>
                                  <td bgcolor="#F0F0F0" width="16%"><div align="right">开通时间：</div></td>
                                  <td bgcolor="#FFFFFF" width="55%"><? echo $companyinfo['CS_BeginDate'];?>&nbsp;</td>
                                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(客户交费开通日期)</td>
                              </tr>
                              <tr>
                                  <td bgcolor="#F0F0F0" width="16%"><div align="right">到期时间：</div></td>
                                  <td bgcolor="#FFFFFF" width="55%"><? echo $companyinfo['CS_EndDate'];?>&nbsp;</td>
                                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                              </tr>
                              <tr>
                                  <td bgcolor="#F0F0F0" width="16%"><div align="right">最近更新时间：</div></td>
                                  <td bgcolor="#FFFFFF" width="55%"><? echo $companyinfo['CS_UpDate'];?>&nbsp;</td>
                                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(更新续费日期)</td>
                              </tr>
                              <tr>
                                  <td bgcolor="#F0F0F0" width="16%"><div align="right">短信条数：</div></td>
                                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $companyinfo['CS_SmsNumber'];?></td>
                                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                              </tr>
                          </table>
                      </fieldset>
                <!-- 公司基本信息End -->
                
                <br style="clear:both;" />
                
                <!-- 公司备用联系人信息Start -->
                <?php
                    $InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_visit o where CompanyID=".$in['id']." ");
                    
                    $page = new ShowPage;
                    $page->PageSize = 10;
                    $page->Total = $InfoDataNum['allrow'];
                    $page->LinkAry = array("kw"=>$in['kw']);
                    
                    $sql = "SELECT * FROM ".DATABASEU.DATATABLE."_order_company_visit WHERE CompanyID=" . $in['id'] . " ORDER BY CreateDate DESC";
                    $list_data = $db->get_results($sql." ".$page->OffSet());
                ?>
                <fieldset title="" class="fieldsetstyle">
                    <legend>备用联系人信息</legend>
                    <form method="post" action="do_use.php?m=add_visit" id="visitinfo" target="exe_iframe">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <td width="5%" class="bottomlinebold">行号</td>
                                <td width="9%" class="bottomlinebold">联系人</td>
                                <td width="12%" class="bottomlinebold">联系人职务</td>
                                <td width="12%" class="bottomlinebold">电话</td>
                                <td width="12%" class="bottomlinebold">手机</td>
                                <td width="12%" class="bottomlinebold">QQ</td>
                                <td width="12%" class="bottomlinebold">邮箱</td>
                                <td width="8%" class="bottomlinebold">操作人</td>
                                <td width="10%" class="bottomlinebold">操作时间</td>
                                <td width="9%" class="bottomlinebold"><input type="button" class="mainbtn" onclick="location.href='contacts_edit.php?CID=<?php echo $in['ID']?>'" value="新增联系人" title="新增联系人信息" /></td>
                            </tr>
                            </thead>
                            <tbody>
                                <?
                                    $n = 1;
                                    if(!empty($cinfo))
                                    {
                                        foreach($cinfo as $lsv)
                                        {
                                ?>
                                            <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                                            <td ><? echo $n++;?></td>
                                            <td ><? echo $lsv['ContactName'];?></td>
                                            <td ><? echo $lsv['ContactJob'];?></td>
                                            <td ><? echo $lsv['ContactPhone'];?></td>
                                            <td ><? echo $lsv['ContactMobile'];?></td>
                                            <td ><? echo $lsv['ContactQQ'];?></td>
                                            <td ><? echo $lsv['ContactEmail'];?></td>
                                            <td ><? echo $lsv['UserTrueName'];?></td>
                                            <td ><? echo date('Y-m-d H:i',$lsv['UpdateDate']);?></td>
                                            <td ><a href="contacts_edit.php?ID=<?php echo $lsv['ID']?>">[编辑]</a></td>
                                            </tr>
                                <? } }else{?>
                                        <tr>
                                            <td colspan="9" height="30" align="center">暂无该公司联系人信息!</td>
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
                    </form>
                </fieldset>  
                <!-- 公司备用联系人信息End -->
                
                <br style="clear:both;" />
                
                <!-- 公司回访信息Start -->
                <fieldset title="" class="fieldsetstyle">
                    <legend>客户回访信息</legend>
                    <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <td width="4%" class="bottomlinebold">行号</td>
                                    <td width="6%" class="bottomlinebold">联系人</td>
                                    <td width="9%" class="bottomlinebold">回访时间<br />联系电话</td>
                                    <td width="16%" class="bottomlinebold">回访简情</td>
                                    <td width="38%" class="bottomlinebold">回访详情</td>
                                    <td width="5%" class="bottomlinebold">使用状态</td>
                                    <td width="4%" class="bottomlinebold">操作人</td>
                                    <td width="10%" align="center" class="bottomlinebold">操作时间</td>
                                    <td width="8%" class="bottomlinebold"><input type="button" class="mainbtn" onclick="location.href='visit_contact.php?CID=<?php echo $in['ID']?>'" value="新增回访记录" title="新增回访记录信息" /></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    $n = 1;
                                    if(!empty($vinfo))
                                    {
                                    
                                        if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
                                        foreach($vinfo as $lsv)
                                        {
                                ?>
                                            <tr id="line_<? echo $lsv['ID'];?>" title="<? echo $lsv['VisitContent'];?>" class="bottomline" <?php if($lsv['UseFlag'] =='F') { echo "style='background-color:rgba(239, 220, 94, 0.76);'"; } elseif ($lsv['UseFlag'] =='L'){ echo "style='background-color:rgba(255, 38, 38, 0.56);'"; }?>>
                                            <td ><? echo $n++;?></td>
                                            <td ><? echo $lsv['ContactName'];?></td>
                                            <td ><? echo $lsv['RecordDate'];?><br /><? echo $lsv['ContactPhone'];?></td>
                                            <td ><? echo $lsv['VisitGeneral'];?></td>
                                            <td><? echo $lsv['VisitContent'];?></td>
                                            <td align="center" >
                                                <? if($lsv['UseFlag'] =='F')echo "未用";
                                                   else if($lsv['UseFlag'] =='L') echo "失联";
                                                   else echo "使用";
                                                ?>
                                             </td>
                                            
                                            <td><? echo $lsv['UserTrueName'];?></td>
                                            <td ><? echo date("Y-m-d H:i",$lsv['UpdateDate']);?></td>
                                            <td ><a href="visit_contact.php?vid=<? echo $lsv['ID'];?>">[编辑]</a></td>
                                            </tr>
                                <? } }else{?>
                                        <tr>
                                            <td colspan="8" height="30" align="center">暂无该公司的回访信息!</td>
                                        </tr>
                                <? }?>
                            </tbody>
                        </table>
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
                                <td width="4%"  height="30" ></td>
                                <td  align="right"><? echo $vpage->ShowLink('company_visit_contact.php');?></td>
                            </tr>
                        </table>
                    </form>
                </fieldset> 
                <!-- 公司回访信息End -->
            </div>
    </div>
 </form>
 <br style="clear:both;" />   
</div>

<? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<div id="windowForm">
        <div class="windowHeader">
            <h3 id="windowtitle">客户简介</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent2">
            <form id="open_erp_fm">
                <input name="m" value="change_remarks_info" type="hidden"/>
                <input name="company" type="hidden" value="<?php echo $in['ID']?>"/>
                <table width="100%">
                    <tr class="bottomline">
                        <td width="24%" align="right">要求开通时间：</td>
                        <td align="left">
                            <input type="text" id="open_date" name="open_date" value="<?php echo $clientInfo['OpenDate']?>" />
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">回访提醒时间：</td>
                        <td align="left">
                            <input type="text" id="visit_date" name="visit_date" value="<?php echo $clientInfo['VisitDate']?>" />
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">客户简介：</td>
                        <td align="left">
                            <label>
                                <textarea rows="3" id="remarks" name="remarks" cols="50"><?php echo $clientInfo['ClientInfo']?></textarea>
                            </label><br/>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td colspan="2">
                            <input class="button_1 btn_erp_submit" type="button" value="提交"/>
                            <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
</body>
</html>
<script type="text/javascript">
        $(function(){
            $(".btn_erp_submit").click(function(){
                //验证变更
                var opendate = $("#open_date").val();
                var visitdate = $("#visit_date").val();
                var remarks = $("#remarks").val();

                if(opendate == '')
                {
                    alert('开通日期不能为空!');
                	return;
                }
                if(visitdate == '')
                {
                	alert('回访提醒日期不能为空!');
                	return;
                }
                if(remarks == '')
                {
                	alert('客户简介不能为空!');
                	return;
                }

                
            	//提交ERP接口信息
                var fm = $("#open_erp_fm");
                $.post("do_use.php",fm.serialize(),function(data){
                    data = Jtrim(data);
                    if(data == 'ok') {
                        $.blockUI({
                            message : '<p>操作成功!</p>'
                        });
                        setTimeout(function(){
                            window.location.reload();
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>'+data+'</p>'
                        });
                        window.setTimeout($.unblockUI, 2000);
                    }
                },'text');
            });
        });
    </script>