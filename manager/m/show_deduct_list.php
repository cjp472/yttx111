<? 
$menu_flag = "saler";
$pope		   = "pope_view";
include_once ("header.php");
if(empty($in['sid']))
{
	exit('参数错误!');
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo SITE_NAME;?> - 管理平台</title>

<link rel="stylesheet" href="css/showpage.css" />
<style type="text/css">
<!--
td,div,p,span,select{color:#333333; font-size:12px; line-height:180%; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; }
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
thead td{font-weight:bold; height:28px; background-color:#efefef;}
tbody td{border-bottom:#cccccc solid 1px;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.font12{font-weight:bold;}
.title_green_w {color: #009933;	font-weight: bold; font-family:Verdana, "Times New Roman", Arial, Helvetica, sans-serif; font-size:14px;}
-->
</style>
</head>

<body >
<div >
         	<table width="100%" border="0" cellspacing="0" cellpadding="2">
               <thead>
                <tr>
                  <td height="28" width="12%" align="center" class="bottomlinebold">序号</td>
                  <td  class="bottomlinebold" >订单号</td>
				  <td width="20%" class="bottomlinebold" align="right" >提成金额</td>
                  <td width="20%" class="bottomlinebold" align="center">发放状态</td>
				  <td width="20%" class="bottomlinebold" align="center">发放时间</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
	$sqlmsg .= " and DeductUser = ".$in['sid']." ";
	$sqlmsg .= " and DeductDate > '".strtotime($in['begindate'].'00:00:00')."' ";
	$sqlmsg .= " and DeductDate < '".strtotime($in['enddate'].'23:59:59')."' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 12;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("sid"=>$in['sid'],"begindate"=>$in['begindate'],"enddate"=>$in['enddate']);        
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_deduct where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." Order by DeductID Desc";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	if(!empty($list_data))
	{     
		$n = 1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['DeductID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td align="center">
				  <? echo $n++;?>
				  </td>
                  <td ><a href="order_manager.php?ID=<? echo $lsv['OrderID'];?>" target="_blank"><? echo $lsv['OrderSN'];?></a></td>  
                  <td align="right"><? echo "<span  class=font12>¥ ".$lsv['DeductTotal']."</span>";?> 元</td>  
                  <td align="center" id="status_<? echo $lsv['DeductID'];?>"><? 
					if($lsv['DeductStatus']=="T"){
						echo '<span class="title_green_w" title="已发放" >√</span> ';
					}elseif($lsv['DeductStatus']=="F"){ ?> 
						X
				  <? }?></td> 
                  <td align="center" id="status_<? echo $lsv['DeductID'];?>"><? 
					if($lsv['DeductStatus']=="T"){
						echo ' '.date("Y-m-d",$lsv['DeductToDate']); 
					}?>&nbsp;</td> 				  
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
				   <td  align="right"><? echo $page->ShowLink('show_deduct_list.php');?></td>
     			 </tr>
              </table>
</div>
</body>
</html>