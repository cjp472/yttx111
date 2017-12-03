<?php 
$menu_flag = "client";
$pope	   = "pope_view";
include_once ("header.php");

if(empty($in['aid']))
{
	$sortinfo = null;
	$in['aid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and AreaID=".intval($in['aid'])." limit 0,1");
}
setcookie("backurl", $_SERVER['REQUEST_URI']);
$company_id = $_SESSION['uc']['CompanyID'];
$cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");
$company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company WHERE CompanyID=" . $company_id);
$client_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DATABASEU.DATATABLE."_order_dealers WHERE ClientCompany={$company_id} LIMIT 1");
if($company_info['CompanySigned']) {
    $to_url = "company_upload.php?sy=c";
} else {
    $to_url = "company_edit.php?sy=c";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?>- 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});

            $("#to_info").click(function(){
                window.location.href = "<?php echo $to_url; ?>";
            });

            $("#bdate").datepicker({changeMonth: true,	changeYear: true});
    		$("#edate").datepicker({changeMonth: true,	changeYear: true});
		})
		
		
function wxqy_client(aid)
{
	var selectedid = chk();
		if(confirm('确认立即同步药店吗?'))
		{
			$.ajax({
             type: "POST",
             url: "do_client.php",
             data: {m:"wxqy_follow"},
             dataType: "html",
			 beforeSend:function(data){
				 $.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			 },
			 success: function(data){
				data = Jtrim(data);
				if(data =="ok"){
					$.blockUI({ message: "<p>邀请关注成功!</p>" }); 
					//$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client_wxqy.php');
					window.setTimeout("window.location='client_wxqy.php'",1000); 
				}else if(data =="nofollow"){
					$.blockUI({ message: "<p>您的微信企业号还没授权医统天下套件,请在新页面授权套件</p>" });
					window.setTimeout($.unblockUI, 2000); 
					var openLink = $("#aopen");openLink.attr('href', '/wxqy/');openLink[0].click();
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" }); 
					//$('.blockOverlay').attr('title','点击返回!').click(window.location.href='client_wxqy.php');
					window.setTimeout("window.location='client_wxqy.php'",1000); 
				 }	
			},
			 error: function(data){
				 $.blockUI({ message: "<p>网络错误，请稍候...</p>" }); 
				window.setTimeout("window.location='client_wxqy.php'",1000); 
			 },
			});
		}else{
			return false;
		}
}
</script>
</head>
<body>
<?php include_once ("top.php");?>
<div id="bodycontent">
<div id="client_notify" style="display:none;width:400px;">
  <div style="font-size:14px;"><b style="font-size:18px;">温馨提示！</b><br/>
    您需要完善详细资料才能正式开通订货平台供客户订货!</div>
  <div style="margin-top:5px;border-top:1px solid #ccc;padding-top:10px;">
    <button id="to_info" style="background:rgb(0,153,205);color:#fff;font-family:微软雅黑;border:1px solid #CCC;padding:6px 15px;cursor:pointer;">立即完善</button>
  </div>
</div>
<div class="lineblank"></div>
<div id="searchline">
  <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
    <form id="FormSearch" name="FormSearch" method="post" action="client_wxqy.php">
      <tr>
        <td width="120" align="center"><strong>用户名/公司名称：</strong></td>
        <td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        <td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
        <td aling="right"><div class="location"><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250;
            <? if(empty($sortinfo)) echo "所有药店"; else echo $sortinfo['AreaName'];?>
          </div></td>
      </tr>
    </form>
  </table>
</div>
<div class="line2"></div>
<div class="bline">
  <div id="sortleft">
    <div class="linebutton">
      <?php if(!erp_is_run($db,$_SESSION['uinfo']['ucompany'])) { ?>
      <input type="button" name="newbutton" id="newbutton" value="新增药店" class="button_2" onclick="javascript:window.location.href='client_add.php<? if(!empty($in['sid'])) echo '?sid='.$in['sid'];?>'" />
      <?php }else { ?>
      Erp用户请通过接口同步新增药店资料。
      <?php } ?>
    </div>
    <hr style="clear:both;" />
    <div class="leftlist">
      <div > <strong><a href="client.php">药店级别</a></strong></div>
      <?php
	$valuearr = get_set_arr('clientlevel');
	if(!empty($valuearr))
	{
		if(count($valuearr, COUNT_RECURSIVE)==count($valuearr))
		{
			$levelarr['A'] = $valuearr;
			$levelarr['A']['id']   = "A";
			$levelarr['A']['name'] = "方式A";
			$levelarr['isdefault']   = "A";
			$valuemsg = serialize($levelarr);
			$db->query("update ".DATABASEU.DATATABLE."_order_companyset set SetValue = '".$valuemsg."' where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetID='".$setinfo['SetID']."'");
		}else{
			$levelarr = $valuearr;
		}
		foreach($levelarr as $ke=>$va)
		{
			if($ke=="isdefault") continue;
			echo '<hr style="clear:both;" />';
			echo '<div>'.$va['name'].'</div><ul>';
			foreach($va as $key=>$var)
			{
				if($key=="id" || $key=="name") continue;
				if($in['lid']==$ke.'_'.$key)
				{
					echo '<li> <a href="client.php?lid='.$ke.'_'.$key.'" class="locationli">'.substr($key,6).'、'.$var.'</a></li>';
				}else{
					echo '<li> <a href="client.php?lid='.$ke.'_'.$key.'">'.substr($key,6).'、'.$var.'</a></li>';
				}
			}
			echo '</ul>';
		}
	}
	?>
      <hr style="clear:both;" />
    </div>
    <!-- tree -->
    <div id="sidetree">
      <div class="treeheader"> <strong><a href="client.php">地区分类</a></strong></div>
      <div id="sidetreecontrol"><img src="css/images/home.gif" alt="地区"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
      <ul id="tree">
        <?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
		echo ShowTreeMenu($sortarr,0);

		foreach($sortarr as $areavar)
		{
			$areaarr[$areavar['AreaID']]   = $areavar['AreaName'];
			$areaarr_p[$areavar['AreaID']] = $areavar['AreaParentID'];
		}
	?>
      </ul>
    </div>
    <!-- tree -->
  </div>
  <div id="sortright">
    <?php
	$clientrow = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8) ");
	
	$sqlmsg = '';
	if(!empty($in['aid']))
	{
		if(empty($areaarr_p[$in['aid']]))
		{
			$sqlmsg .= "and (ClientArea in (SELECT AreaID FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and (AreaParentID=".$in['aid']." OR AreaID=".$in['aid'].") ORDER BY AreaID ASC ) or ClientArea=".$in['aid'].") ";
		}else{
			$sqlmsg .= ' and ClientArea='.$in['aid'].' ';
		}
	}
	if($in['dtype'] == 'noorder'){
		if(!empty($in['bdate'])) $sqlmsg .= ' and lastOrderAt <= '.strtotime($in['bdate'].'00:00:00').' ';
		if(!empty($in['edate'])) $sqlmsg .= ' and lastOrderAt >= '.strtotime($in['edate'].'23:59:59').' ';	
	}else{
		if(!empty($in['bdate'])) $sqlmsg .= ' and lastOrderAt >= '.strtotime($in['bdate'].'00:00:00').' ';
		if(!empty($in['edate'])) $sqlmsg .= ' and lastOrderAt <= '.strtotime($in['edate'].'23:59:59').' ';
	}
	

		
	if(!empty($in['lid']))
	{
		if(substr($in['lid'],0,1)=="A")
		{
			$sqlmsg .= " and (FIND_IN_SET('".$in['lid']."',ClientLevel) or ClientLevel = '".substr($in['lid'],2)."')";
		}else{
			//$sqlmsg .= " and ClientLevel like '%".$in['lid']."%' ";
			$sqlmsg .= " and FIND_IN_SET('".$in['lid']."',ClientLevel) ";
		}		
	}else{
		$in['lid'] = '';
	}




		if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(ClientName,ClientNO,ClientCompanyName,ClientCompanyPinyi,ClientTrueName,ClientMobile) like '%".$in['kw']."%' ";

		$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8)");
        
        $tempfollow = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_clientfollow where ClientCompany = ".$_SESSION['uinfo']['ucompany']."");
        $totalfollow=$tempfollow['allrow'];

		$page = new ShowPage;
		$page->PageSize = 30;
		$page->Total = $InfoDataNum['allrow'];
		$page->LinkAry = array("kw"=>$in['kw'],"aid"=>$in['aid'],"lid"=>$in['lid'],"dtype"=>$in['dtype'],"si"=>$in['si']);        
	
		$datasql   = "SELECT ClientID,ClientLevel,ClientArea,ClientName,ClientNO,ClientCompanyName,ClientTrueName,ClientMobile,ClientFlag,lastOrderAt,ClientEmail FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8) ORDER BY ClientID DESC";
		$list_data = $db->get_results($datasql." ".$page->OffSet());
?>
    <div class="warning"> 您的帐号共有<?=$InfoDataNum['allrow']?>家客户，已同步 <?=$totalfollow?> 家。　
      <input type="button" name="newbutton" id="newbutton2" value="同步到企业号" class="button_2" onclick="wxqy_client();" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="newbutton3" id="newbutton3" value="导入药店" class="button_2" onclick="window.location='client_wximport.php'" /><a href='' id="aopen" target="_blank"></a></div>
      <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <thead>
          </thead>
          <tbody>
            <tr id="line_<? echo $lsv['ClientID'];?>" class="bottomline">
              <td ><p style="font-size:16px; margin-left:16px;"><strong>为什么要同步通讯录？ </strong></p>
<ol><li style="font-size:14px">为了保障您的信息安全，只有授权用户才可关注与使用您的企业号功能，同步通讯录后即可完成对客户的授权。</li></ol>
                <p style="font-size:16px; margin-left:16px;"><strong>为什么要对接微信企业号？ </strong></p>
                <ol>
                  <li style="font-size:14px"><strong>1)快速移动化办公</strong>。企业在开通企业号后，可以直接利用微信及企业号的基础能力，加强员工的沟通与协同，提升企业文化建设、公告通知、知识管理，快速实现企业应用的移动化； </li>
                  <li style="font-size:14px"><strong>2)随时随地业务处理</strong>。将医统天下与微信企业号对接后，为一线员工，如一线销售、行销代理、售后服务、巡检巡店、仓管后勤等人员提供工作管理与支撑。 </li>
                  <li style="font-size:14px"><strong>3)业务协同</strong>：适宜于企业与上下游合作伙伴、供应商的订单管理、工作协同，不受时空限制，能用微信就能处理业务。 </li>
                  <li style="font-size:14px"><strong>4)零门槛使用</strong>。用户微信扫码关注即可使用，在玩微信时，随手处理企业号消息，无需学习即可流畅使用。 </li>
                  <li style="font-size:14px"><strong>5)更广阔的用户</strong>。基于微信8亿用户，移动分销拥有无限可能。 </li>
                  <li style="font-size:14px"><strong>6)内部连接</strong>。企业号可以建立企业任何内部IT&nbsp;系统或硬件物理设备与员工微信的连接，实现企业系统的移动化的同时，实现端到端的流程闭环。 </li>
              </ol></td>
            </tr>
          </tbody>
        </table>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr >
            <td width="52" colspan="4" align="center" style="font-size:16px; margin-left:16px;">&nbsp;&nbsp;<a href="/wxqy/" target="_blank">立即对接》</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://qy.weixin.qq.com" target="_blank">了解微信企业号》</a></td>
          </tr>
        </table>
        <INPUT TYPE="hidden" name="referer" value ="" >
    </form>
  </div>
  <br style="clear:both;" />
</div>
</div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<div id="windowForm6">
  <div class="windowHeader">
    <h3 id="windowtitle">批量修改药店资料</h3>
    <div class="windowClose">
      <div class="close-form" onclick="closewindowui()" title="关闭" >x</div>
    </div>
  </div>
  <div id="windowContent"> 正在载入数据... </div>
</div>
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				if($var['AreaParentID']=="0")
				{
					$frontMsg  .= '<li><a href="client.php?aid='.$var['AreaID'].'"><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="client.php?aid='.$var['AreaID'].'">'.$var['AreaName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>