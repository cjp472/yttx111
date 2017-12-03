<?php 
$menu_flag = "client";
$pope	   = "pope_form";
include_once ("header.php");
		
		if(empty($in['aid']))
		{
			$sortinfo  = null;
			$in['aid'] = 0;
		}else{	 
			$areainfo  = $db->get_row("SELECT * FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." and AreaID=".intval($in['aid'])." limit 0,1");
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

<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

    

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="client_recycle.php">
        		<tr>
					<td width="120" align="center"><strong>用户名/公司名称：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250;<a href="client_recycle.php">回收站</a></div></td>
				</tr>
   	          </form>
			 </table>              
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 

<div class="leftlist"> 
<div >
<strong>药店级别</strong></div>  	  

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
					echo '<li> <a href="client_recycle.php?lid='.$ke.'_'.$key.'" class="locationli">'.substr($key,6).'、'.$var.'</a></li>';
				}else{
					echo '<li> <a href="client_recycle.php?lid='.$ke.'_'.$key.'">'.substr($key,6).'、'.$var.'</a></li>';
				}
			}
			echo '</ul>';
		}
	}
	?>
 </div>
<!-- tree -->   
       	  </div>
        	<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               
               <thead>
                <tr>

                  <td width="6%" class="bottomlinebold">行号</td>
                  <td class="bottomlinebold">单位名称</td>
				  <td width="20%" class="bottomlinebold">登陆帐号</td>
				  <td width="12%" class="bottomlinebold">联系人</td>
                  <td width="18%" class="bottomlinebold">电话</td>
                  <td width="8%" class="bottomlinebold" >地区</td>
                  <td width="14%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['lid']))
	{
		$sqlmsg .= " and c.ClientLevel like '%%".$in['lid']."%%' ";
	}else{
		$in['lid'] = '';
	}
	if(!empty($in['kw']))  $sqlmsg .= " and (c.ClientName like binary '%%".$in['kw']."%%' or c.ClientCompanyName like binary '%%".$in['kw']."%%' or c.ClientCompanyPinyi like binary '%%".strtoupper($in['kw'])."%%' ) ";
	$n=1;
	$datasql   = "SELECT c.ClientID,c.ClientLevel,c.ClientArea,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientMobile FROM ".DATATABLE."_order_client  c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=1 ".$sqlmsg." ORDER BY c.ClientID DESC";
	$list_data = $db->get_results($datasql);
	if(!empty($list_data))
	{
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['ClientID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['ClientID'];?>" target="_blank"><? echo $lsv['ClientCompanyName'];?></a></td>
                  <td class="TitleNUM2"><? echo $lsv['ClientName'];?></td>
                  <td ><? echo $lsv['ClientTrueName'];?></td>
                  <td ><? echo $lsv['ClientPhone'];?>&nbsp;</td>
				   <td ><? echo $areaarr[$lsv['ClientArea']];?>&nbsp;</td>             
                  <td align="center"><a href="javascript:void(0);" onclick="do_restore('<? echo $lsv['ClientID'];?>');" >还原</a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
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
					$frontMsg  .= '<li><a href="client_recycle.php?aid='.$var['AreaID'].'"><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="client_recycle.php?aid='.$var['AreaID'].'">'.$var['AreaName'].'</a>';
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
