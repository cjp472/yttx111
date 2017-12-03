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

			$("#bdate").datepicker({changeMonth: true,	changeYear: true});
    		$("#edate").datepicker({changeMonth: true,	changeYear: true});
		});
</script>
</head>

<body>
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

    

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="client.php">
        		<tr>
					<td width="120" align="center"><strong>用户名/公司名称：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        			<td width="120" align="center"><strong>最近下单时间：</strong></td> 
					<td width="220" nowrap="nowrap">从<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> 到 <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="client.php">药店管理 &#8250;&#8250; <? if(empty($sortinfo)) echo "所有药店"; else echo $sortinfo['AreaName'];?></a></div></td>
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
             	Erp用户请通过接口同步新增药店资料
            <?php } ?>
		 </div>
         <hr style="clear:both;" />

<div class="leftlist"> 
<div >
<strong><a href="client.php">药店级别</a></strong></div>

	<?
	$valuearr = get_set_arr('clientlevel');
	if(!empty($valuearr))
	{
		$levelarr = $valuearr;
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
            <?php
            $sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
            //echo ShowTreeMenu($sortarr,0);

            foreach($sortarr as $areavar)
            {
                $areaarr[$areavar['AreaID']]   = $areavar['AreaName'];
                $areaarr_p[$areavar['AreaID']] = $areavar['AreaParentID'];
            }
            ?>
            <!-- tree -->
       	  </div>
          <div id="sortright">
<?

	$sqlmsg = '';
	
	if(!empty($in['bdate'])) $sqlmsg .= ' and lastOrderAt >= '.strtotime($in['bdate'].'00:00:00').' ';
	if(!empty($in['edate'])) $sqlmsg .= ' and lastOrderAt <= '.strtotime($in['edate'].'23:59:59').' ';
	
	if(!empty($in['lid']))
	{
		$sqlmsg .= " and c.ClientLevel like '%%".$in['lid']."%%' ";
	}else{
		$in['lid'] = '';
	}
	if(!empty($in['kw']))  $sqlmsg .= " and (c.ClientName like binary '%%".$in['kw']."%%' or c.ClientCompanyName like binary '%%".$in['kw']."%%' or c.ClientCompanyPinyi like binary '%%".strtoupper($in['kw'])."%%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"aid"=>$in['aid'],"lid"=>$in['lid']);        
	
	$datasql   = "SELECT c.ClientID,c.ClientLevel,c.ClientArea,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientMobile,c.ClientFlag FROM ".DATATABLE."_order_client  c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and (c.ClientFlag=0 OR c.ClientFlag=9) ".$sqlmsg." ORDER BY c.ClientID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
?>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td class="bottomlinebold">单位名称</td>
				  <td width="16%" class="bottomlinebold">登陆帐号</td>
				  <td width="16%" class="bottomlinebold">手机号码</td>
				  <td width="12%" class="bottomlinebold">&nbsp;联系人</td>
                  <td width="10%" class="bottomlinebold" >&nbsp;地区</td>
                  <td width="5%" class="bottomlinebold" >审核</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>       		
      		<tbody>
<?
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
                  <td ><? echo $areaarr[$lsv['ClientArea']];?>&nbsp;</td>
                    <td title="审核状态"><? if(empty($lsv['ClientFlag'])) echo "<font color=green>√</font>"; else echo '<strong>X</strong>';  ?>&nbsp;</td>
                  <td align="center"><a href="client_edit.php?ID=<? echo $lsv['ClientID'];?>" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete('<? echo $lsv['ClientID'];?>');" >
                 <span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>
                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>

   			       <td class="sublink"></td>
       			     <td  align="right"><? echo $page->ShowLink('client.php');?></td>
     			 </tr>
              </table>
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
<?
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