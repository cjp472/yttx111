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
<?php include_once ("top.php");?>
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

<div id="sidetree"> 
<div class="treeheader">&nbsp;<br />
<strong>地区分类</strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
		echo ShowTreeMenu($sortarr,0);

		foreach($sortarr as $areavar)
		{
			$areaarr[$areavar['AreaID']] = $areavar['AreaName'];
		}

	?>
</ul>
 </div>
<!-- tree -->   
       	  </div>
        	<div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               
               <thead>
                <tr>

                  <td width="4%" class="bottomlinebold">行号</td>
                  <td width="8%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">客户名称</td>
				  <td width="12%" class="bottomlinebold">登陆帐号</td>
				  <td width="10%" class="bottomlinebold">联系人</td>
                  <td width="12%" class="bottomlinebold">电话</td>
                  <td width="8%" class="bottomlinebold" >地区</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['aid'])) $sqlmsg .= ' and ClientArea='.$in['aid'].' ';
	if(!empty($in['kw']))  $sqlmsg .= " and (ClientName like '%".$in['kw']."%' or ClientCompanyName like '%".$in['kw']."%' ) ";   
	$n=1;
	$datasql   = "SELECT ClientID,ClientName,ClientNO,ClientCompanyName,ClientTrueName,ClientPhone,ClientArea FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and ClientFlag=1  ORDER BY ClientID DESC";
	$list_data = $db->get_results($datasql);
	if(!empty($list_data))
	{
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['ClientID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['ClientNO'];?></td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['ClientID'];?>" target="_blank"><? echo $lsv['ClientCompanyName'];?></a></td>
                  <td class="TitleNUM2"><? echo $lsv['ClientName'];?></td>
                  <td ><? echo $lsv['ClientTrueName'];?></td>
                  <td ><? echo $lsv['ClientPhone'];?>&nbsp;</td>
				   <td ><? echo $areaarr[$lsv['ClientArea']];?>&nbsp;</td>             
                  <td align="right"><a href="javascript:void(0);" onclick="do_restore('<? echo $lsv['ClientID'];?>');" >还原</a>&nbsp;<br />&nbsp;<a href="javascript:void(0);" onclick="do_quite_delete('<? echo $lsv['ClientID'];?>');" >彻底删除</a></td>
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
