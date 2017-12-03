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
<title><? echo SITE_NAME;?> - 管理平台</title>
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
        	  <form id="FormSearch" name="FormSearch" method="post" action="client.php">
        		<tr>
					<td width="120" align="center"><strong>用户名/公司名称：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
					<td width="120" align="center">
					<select name="dtype" id="dtype" class="selectline" >
						<option value="order" <?php if($in['dtype']=="order") echo 'selected="selected"'; ?> >最近下单时间</option>
						<option value="noorder" <?php if($in['dtype']=="noorder") echo 'selected="selected"'; ?> > 最近未下单时间 </option>
					</select>
					</td> 
					<td width="220" nowrap="nowrap">从<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> 到 <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250; <a href="product.php"><? if(empty($sortinfo)) echo "所有药店"; else echo $sortinfo['AreaName'];?></a></div></td>
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
<div >
<strong><a href="client.php">药店级别</a></strong></div>

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
<div class="treeheader">
<strong><a href="client.php">地区分类</a></strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
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
	if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(ClientName, ClientNO, ClientCompanyName,ClientCompanyPinyi, ClientTrueName,ClientMobile) like '%".$in['kw']."%' ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8) ");

	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"aid"=>$in['aid'],"lid"=>$in['lid'],"bdate"=>$in['bdate'],"edate"=>$in['edate'], "dtype"=>$in['dtype']);        
	
	$datasql   = "SELECT ClientID,ClientLevel,ClientArea,ClientName,ClientNO,ClientCompanyName,ClientTrueName,ClientMobile,ClientFlag,lastOrderAt FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and (ClientFlag=0 OR ClientFlag=9 OR ClientFlag=8) ORDER BY ClientID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
?>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
				  <td width="4%" class="bottomlinebold">&nbsp;</td>
                  <td width="4%" class="bottomlinebold">行号</td>
                  <td class="bottomlinebold">编号/名称</td>
				  <td width="16%" class="bottomlinebold">登陆帐号</td>
				  <td width="12%" class="bottomlinebold">联系人/手机</td>
				  <td width="14%" class="bottomlinebold">最近下单时间</td>
				  <td width="8%" class="bottomlinebold" >&nbsp;地区</td>
                  <td width="8%" class="bottomlinebold" >账号状态</td>
                  <td width="6%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>       		
      		<tbody>
<?php
	$n=1;
	if(!empty($list_data))
	{
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['ClientID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ClientID'];?>" value="<? echo $lsv['ClientID'];?>"  /></td>
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['ClientNO'];?>
                  <br /><a href="client_content.php?ID=<? echo $lsv['ClientID'];?>" target="_blank"><? echo $lsv['ClientCompanyName'];?></a></td>
				  <td ><? echo $lsv['ClientName'];?></td>
				  <td ><? echo $lsv['ClientTrueName'];?>
				  <br /><? echo $lsv['ClientMobile'];?>&nbsp;</td>
				  <td ><? if(!empty($lsv['lastOrderAt'])) echo date("Y-m-d H:i", $lsv['lastOrderAt']);?>&nbsp;</td>
				  <td ><? echo $areaarr[$lsv['ClientArea']];?>&nbsp;</td>
                  <td title="审核状态">
                      <?php
                        switch($lsv['ClientFlag']) {
                            case '9':
                                echo '待审核';
                                break;
                            case '8':
                                echo '只读';
                                break;
                            case '0':
                                echo "已审核";
                                break;
                            default:
                                break;
                        }
                      ?></td>
                  <td align="center"><a href="client_edit.php?ID=<? echo $lsv['ClientID'];?>" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete('<? echo $lsv['ClientID'];?>');" >
                  <span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a></td>
                </tr>
<?php } }else{?>
     			 <tr>
       				 <td colspan="9" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<?php }?>
 				</tbody>
                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				<td width="4%"  height="30" class="selectinput" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
					<td width="8%" >全选/取消</td>
					<td width="40%" class="sublink"><ul>
					<li><a href="javascript:void(0);" onclick="muledit_client('<? echo $in['aid'];?>')" >批量操作</a></li>
					<li><a href="javascript:void(0);" onclick="delete_client('<? echo $in['aid'];?>')" >批量删除</a></li>
					<li><a href="javascript:void(0);" onclick="going('outexcel','<? echo $in['aid'];?>')" >批量导出</a></li>
					<li><a href="javascript:void(0);" onclick="going('all_outexcel','<? echo $in['aid'];?>')" >全部导出</a></li>
					</ul></td>
					<td></td>
     			 </tr>
     			 <tr >
       			    <td colspan="4" align="right"><? echo $page->ShowLink('client.php');?>&nbsp;</td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
     <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">批量修改药店资料</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>   
	
<?php 
	include_once 'bottom_common.php';
?> 
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