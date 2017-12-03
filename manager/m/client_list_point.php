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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript">
/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="client_list_point.php">
        		<tr>
					<td width="120" align="center"><strong>用户名/公司名称：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250; <a href="product.php"><? if(empty($sortinfo)) echo "所有药店"; else echo $sortinfo['AreaName'];?></a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">


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
	$clientrow = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and (ClientFlag=0 OR ClientFlag=9) ");
	
	$sqlmsg = '';
	if(!empty($in['aid']))
	{
		if(empty($areaarr_p[$in['aid']]))
		{
			$sqlmsg .= "and (c.ClientArea in (SELECT AreaID FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and (AreaParentID=".$in['aid']." OR AreaID=".$in['aid'].") ORDER BY AreaID ASC ) or c.ClientArea=".$in['aid'].") ";
		}else{
			$sqlmsg .= ' and ClientArea='.$in['aid'].' ';
		}
	}
	if(!empty($in['lid']))
	{
		if(substr($in['lid'],0,1)=="A")
		{
			$sqlmsg .= " and (FIND_IN_SET('".$in['lid']."',c.ClientLevel) or c.ClientLevel = '".substr($in['lid'],2)."')";
		}else{
			//$sqlmsg .= " and ClientLevel like '%".$in['lid']."%' ";
			$sqlmsg .= " and FIND_IN_SET('".$in['lid']."',c.ClientLevel) ";
		}		
	}else{
		$in['lid'] = '';
	}
	if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.ClientName, c.ClientCompanyName,c.ClientCompanyPinyi, c.ClientTrueName,c.ClientMobile) like '%".$in['kw']."%' ";
	$InfoDataNum = $db->get_results("SELECT count(*) AS allrow FROM ".DATATABLE."_order_point p INNER JOIN ".DATATABLE."_order_client c ON p.`PointClient` = c.`ClientID` WHERE p.PointCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and c.ClientFlag=0 GROUP BY p.PointClient");
	
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = count($InfoDataNum);
//     $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"aid"=>$in['aid'],"lid"=>$in['lid']);        
	

	$datasql = "SELECT SUM(p.PointValue) AS pTotal,c.ClientID,c.ClientName,c.`ClientTrueName`,c.`ClientCompanyName`,c.ClientMobile FROM ".DATATABLE."_order_point p INNER JOIN ".DATATABLE."_order_client c ON p.`PointClient` = c.`ClientID` WHERE p.PointCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and c.ClientFlag=0 GROUP BY p.PointClient ORDER BY pTotal DESC ";

	$list_data = $db->get_results($datasql." ".$page->OffSet());
?>


          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
				  
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td class="bottomlinebold">药店名称</td>
				  <td width="16%" class="bottomlinebold">登陆帐号</td>
				  <td width="16%" class="bottomlinebold">手机号码</td>
				  <td width="12%" class="bottomlinebold">&nbsp;联系人</td>
                  <td width="15%" class="bottomlinebold" align="right" >积分&nbsp;</td>
				  <td width="3%">&nbsp;</td>
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
				 
                  <td ><? echo $n++;?></td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['ClientID'];?>" target="_blank"><? echo $lsv['ClientCompanyName'];?></a></td>
				  <td ><? echo $lsv['ClientName'];?></td>
				  <td ><? echo $lsv['ClientMobile'];?>&nbsp;</td>
				  <td ><? echo $lsv['ClientTrueName'];?>&nbsp;</td>
				  <td align="right"><? echo $lsv['pTotal'];?>&nbsp;</td>
				  <td>&nbsp;</td>

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
       				<td width="4%"  height="30" class="selectinput" ></td>
					<td width="8%" ></td>

       			    <td  align="right"><? echo $page->ShowLink('client_list_point.php');?>&nbsp;</td>
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