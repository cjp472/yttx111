<?php
$menu_flag = "client";
$pope	   = "pope_form";
include_once ("header.php");

if(empty($in['sid']))
{
    $sortinfo = null;
    $in['sid'] = 0;
}
//$fLog = KLogger::instance(LOG_PATH);
$tempwx = $db->get_row("SELECT CorpID,CompanyID,Permanent_code,agentidinfo FROM ".DATABASEU.DATATABLE."_order_weixinqy WHERE CompanyID='{$_SESSION['uinfo']['ucompany']}'");
$tempsuite_ticket=file(WEB_ROOT_URL."/wxqy/data/ticket.txt");
$tempsuite_access_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token",json_encode(array('suite_id'=>"tj4e38773e39a823b9",'suite_secret'=>"hnUJ3WVAK9eAVm8Gdn_I2Ieik3Ok3ilWlbcGk2Te94LWeVNj1aNFYIOBSXMBNPm5",'suite_ticket'=>trim($tempsuite_ticket[0])))); //获取应用套件令牌
$tempaccess_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?suite_access_token=". $tempsuite_access_token['suite_access_token'],json_encode(array('suite_id'=>"tj4e38773e39a823b9",'auth_corpid'=>$tempwx['CorpID'],'permanent_code'=>$tempwx['Permanent_code'])));//通过永久授权码获取ACCESS_TOKEN
//$fLog->logInfo("tempaccess_token" . " params: ",$tempaccess_token['access_token']);

$tempdepartment=curl_get_data("https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=".$tempaccess_token['access_token']);
foreach($tempdepartment['department'] as $a=>$b){
	if($a==0){
		$fdepartment=$b->id;
	}
	$newdepartment[$b->id]=$b->name;
}
if(!empty($in['did'])){
	$tempmember=curl_get_data("https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=".$tempaccess_token['access_token']."&department_id=".$in['did']."&fetch_child=0&status=0");
}else{
	$tempmember=curl_get_data("https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token=".$tempaccess_token['access_token']."&department_id=".$fdepartment."&fetch_child=0&status=0");
}	
$list_data=$tempmember['userlist'];

$oldclient=$db->get_results("SELECT ClientName FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8) ORDER BY ClientID DESC");
foreach($oldclient as $a=>$b){
	$newclient[$a]=$b['ClientName'];
}
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script src="js/client.js?v=43<? echo VERID;?>" type="text/javascript"></script>
        <script type="text/javascript">
            function remove_content_line(pid){
                var delline = "line_" + pid;
                $("#"+delline).remove();
            }
			
function muledit_client_wx(aid)
{
	var selectedid = chk();
	if(selectedid == ''){
		alert('请先选择您要设置的药店');
	}else{	
		if(confirm('确认立即设为药店吗?'))
		{
			$.blockUI.defaults.css = {padding:	'2px',
		margin:		0,
		width:		'auto',
		top:		'30%',
		left:		'40%',
		textAlign:	'center',
		color:		'#000',
		border:		'12px solid #aaa',
		backgroundColor:'#efefef',
		cursor:		'wait'};
			$.ajax({
             type: "POST",
             url: "do_client.php",
             data: {m:"wxqy_clientimport", clientdata:selectedid},
             dataType: "html",
			 beforeSend:function(data){
				 $.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			 },
             success: function(data){
				//data = Jtrim(data);
//				if(data =="ok"){
//					$.blockUI({ message: "<p>已经成功设置为药店!默认密码为123456,请及时通知药店修改!</p>" }); 
//					//$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client_wxqy.php');
//					window.setTimeout("window.location='client_wximport.php'",2000); 
//				}else{
					$.blockUI({ message:data}); 
					//$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client_wxqy.php');
					//window.setTimeout("window.location='client_wximport.php'",2000); 
				 // }	
			 },
			 error: function(data){
				 $.blockUI({ message: "<p>网络错误，请稍候...</p>" }); 
				window.setTimeout("window.location='client_wximport.php'",1000); 
			 },
			});
		}else{
			return false;
		}
	}
}

function testclose(){
	$.unblockUI();
	window.location='client_wximport.php';
}
     </script>
</head>

    <body>
    <?php include_once ("top.php");?>
    <div id="bodycontent">
        <div class="lineblank"></div>

        <div id="searchline">
            <div class="rightdiv">
                <div class="locationl"><a name="editname" id="editname"></a><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250; <a href="client_import.php">批量导入</a> </div>
            </div>
        </div>

        <div class="line2" style=" border-bottom:#CCC solid 1px;"></div>
        <div class="bline" >
            <form id="MainFormwx" name="MainFormwx" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td height="33" colspan="11" class="bottomlinebold" style="font-size:14px;">微信企业号通讯录内部门列表:<?php foreach($newdepartment as $c=>$d){echo "<a href='client_wximport.php?did=".$c."' >".$d."</a>>>";}?>                    (点击部门即可列出部门内成员)　当前部门:<font color="#FF0000"><? echo empty($in['did'])?$newdepartment[$fdepartment]:$newdepartment[$in['did']];?></font></td>
                 </tr>
                <tr>
				  <td width="2%" class="bottomlinebold">&nbsp;</td>
                  <td width="3%" class="bottomlinebold">行号</td>
                  <td width="8%" class="bottomlinebold">是否是药店</td>
                  <td width="16%" class="bottomlinebold">设为药店后的登录账号</td>
                  <td width="10%" class="bottomlinebold">名称</td>
				  <td width="12%" class="bottomlinebold">微信帐号</td>
				  <td width="11%" class="bottomlinebold">联系手机</td>
				  <td width="10%" class="bottomlinebold">邮箱</td>
				  <td width="5%" class="bottomlinebold">性别</td>
				  <td width="12%" class="bottomlinebold" >&nbsp;是否关注企业号</td>
                  <td width="11%" class="bottomlinebold" >头像</td>
                 </tr>
     		 </thead>       		
      		<tbody>
                <?php
	$n=1;
	if(!empty($list_data))
	{
	 foreach($list_data as $lsv)
	 {
	 if(is_numeric(array_search($_SESSION['uc']['CompanyPrefix']."-".$lsv->userid,$newclient))){
		$ifclient="是";
        $ifdisables='disabled="disabled"';
	 }else{
		$ifclient="否";
        $ifdisables='';
	 }
?>
                <tr id="line_<? echo $lsv->userid;?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv->userid;?>" value="<?=$lsv->userid;?>|<?=$lsv->name;?>|<?=$lsv->mobile;?>|<?=$lsv->email;?>" <?=$ifdisables?>></td>
                  <td ><? echo $n++;?>&nbsp;</td>
                  <td ><?=$ifclient?>&nbsp;</td>
                  <td ><? echo $lsv->userid;?>&nbsp;</td>
                  <td ><? echo $lsv->name;?>&nbsp;</td>
				  <td ><? echo $lsv->weixinid;?>&nbsp;</td>
				  <td ><? echo $lsv->mobile;?>&nbsp;</td>
				  <td ><? echo $lsv->email;?>&nbsp;</td>
				  <td ><? if($lsv->gender=="1"){echo "男";}elseif($lsv->gender=="0"){echo "女";}else{echo "未知";}?>&nbsp;</td>
				  <td ><? if($lsv->status=="1"){ echo "已关注";}elseif($lsv->status=="2"){ echo "已冻结";}elseif($lsv->status=="4"){echo "未关注";}?>&nbsp;</td>
                  <td><?php  if(!empty($lsv->avatar)){?><img src="<?=$lsv->avatar?>" width="50" height="50"/><? }else{?>&nbsp;<? }?></td>
                </tr>
<?php } }else{?>
     			 <tr>
       				 <td colspan="11" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<?php }?>
 				</tbody>
                
              </table>
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
       				<td width="4%"  height="30" class="selectinput" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
					<td width="8%" >全选/取消</td>
					<td width="40%" class="sublink"><ul>
					<li><a href="javascript:void(0);" onclick="muledit_client_wx()" >导入药店</a></li>
					</ul></td>
					<td></td>
   			 </tr>
     			 <tr >
       			    <td colspan="4" align="right">&nbsp;</td>
     			 </tr>
              </table>
          </form>
        </div>
    </div>
    </body>
    </html>
<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "┠-";
    $selectmsg = "";

    if($var['ParentID']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['ParentID'] == $p_id)
        {
            $repeatMsg = str_repeat("-+-", $layer-2);
            if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";

            $frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";

            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
            $frontMsg  .= $frontMsg2;
        }
    }
    return $frontMsg;
}

function charblank($char)
{
    if(strlen($char) > 5)
    {
        $rchar = substr($char,0,4);
    }else{
        $rchar = $char.str_repeat(" -", (4-strlen($char)));
    }
    return $rchar;
}


?>

