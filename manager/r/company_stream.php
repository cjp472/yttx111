<?php 
$menu_flag = "manager";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

 <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="company_stream.php">
			  
        	    <label>
        	      &nbsp;&nbsp;订单号，公司： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="company_stream.php">支付信息</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or o.order_no like '%".$in['kw']."%' ) ";


	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_buy_stream o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where status='T' ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT o.*,c.CompanyName FROM ".DATABASEU.DATATABLE."_buy_stream o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where status='T' ".$sqlmsg." ORDER BY o.id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="3%" class="bottomlinebold">行号</td>
                  <td width="14%" class="bottomlinebold">支付流水号</td>
                  <td width="12%" class="bottomlinebold">订单号</td>
                  <td class="bottomlinebold">公司</td>
				  <td width="7%" class="bottomlinebold">支付方式</td>
				  <td width="12%" class="bottomlinebold">支付账号或方式</td>
				  <td align="right" width="8%" class="bottomlinebold">金额(元)</td>
				  <td align="center" width="10%" class="bottomlinebold">支付时间</td>
				  <td align="center" width="10%" class="bottomlinebold">到账时间</td>
				  <td width="7%" class="bottomlinebold">操作人</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n = 1;
if(!empty($list_data))
{

     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {

?>
               <tr id="line_<? echo $lsv['id'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['stream_no'];?></td>
                  <td ><? echo $lsv['order_no'];?></td>
                  <td ><? echo $lsv['CompanyName'];?></td>
                  <td ><? echo $pay_arr[$lsv['pay_away']];?></td>
                  <td ><? echo $lsv['trade_no'];?></td>
                  <td align="right"><? echo '￥'.$lsv['amount'];?></td>
                  <td align="center"><? echo date("y-m-d H:i",$lsv['time']);?></td>
                  <td align="center"><? echo date("y-m-d H:i",$lsv['to_time']);?></td>
                  <td ><? echo $lsv['username'];?></td>

                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="10" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>
   			         
       			     <td  align="right"><? echo $page->ShowLink('company_stream.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
 
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    
</body>
</html>