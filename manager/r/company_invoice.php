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
<script src="js/company.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

 <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="company_invoice.php">
			  
        	    <label>
        	      &nbsp;&nbsp;公司： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="company_invoice.php">开票申请</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' ) ";


	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_buy_invoice o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT o.*,c.CompanyName FROM ".DATABASEU.DATATABLE."_buy_invoice o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ORDER BY o.id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="3%" class="bottomlinebold">行号</td>
                  <td width="8%" class="bottomlinebold">公司</td>
                  <td width="8%" class="bottomlinebold">票号</td>
                  <td width="8%" class="bottomlinebold">抬头</td>
				  <td class="bottomlinebold">内容</td>
				  <td align="right" width="6%" class="bottomlinebold">金额(元)</td>
				  <td align="center" width="5%" class="bottomlinebold">联系人</td>
				  <td width="7%" class="bottomlinebold">联系电话</td>
				  <td width="5%" class="bottomlinebold">邮编</td>
				  <td width="15%" class="bottomlinebold">邮寄地址</td>
				  <td width="8%" class="bottomlinebold">申请日期</td>
				  <td width="8%" class="bottomlinebold">开票日期</td>
				  <td width="5%" class="bottomlinebold">开通状态</td>
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
                  <td ><? echo $lsv['CompanyName'];?></td>
                  <td ><? echo $lsv['invoice_no'];?></td>
                  <td ><? echo $lsv['header'];?></td>
                  <td ><? echo $lsv['body'];?></td>
                  <td align="right"><? echo '￥'.$lsv['total'];?></td>
                  <td align="center"><? echo $lsv['name'];?></td>
                  <td ><? echo $lsv['phone'];?></td>
                  <td ><? echo $lsv['zipcode'];?></td>
                  <td ><? echo $lsv['address'];?></td>
				  <td ><? echo date("y-m-d H:i",$lsv['time']);?></td>
				  <td ><? if(!empty($lsv['to_time'])){echo $lsv['to_time'];}?></td>
				  <td ><? if($lsv['status']=='F'){ echo '[<a href="javascript:;" onclick="showInvoice('.$lsv['id'].",'".$lsv['CompanyName']."',".$lsv['total'].')" title="开票">开票</a>]';}else{echo '已开票';}?></td>

                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="13" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>
   			         
       			     <td  align="right"><? echo $page->ShowLink('company_invoice.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
 
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<div id="windowForm">
    <div class="windowHeader">
        <h3 id="windowtitle">开票申请信息</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <table width="100%">
            <tr class="bottomline">
                <td width="24%" align="right">公司：</td>
                <td align="left">
                    <span data-company></span>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">金额：</td>
                <td align="left">
                    <span data-total></span>
                </td>
            </tr>
           <tr class="bottomline">
                <td align="right">开票票号：</td>
                <td align="left">
                    <input name="invoice_no" style="width:200px;" value=""/>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">开票日期：</td>
                <td align="left">
                	<input name="invoiceID" type="hidden" id="invoiceID" value=""/>
                    <input name="to_time" style="width:200px;" id="to_time" value=""/>
                    <span >格式(YYYY-MM-DD)</span>
                </td>
            </tr>
            <tr class="bottomline">
                <td colspan="2">
                    <input onclick="do_companyinvoice_edit()" class="button_1" type="button" value="确认" />
                    <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                </td>
            </tr>
        </table>
    </div>
</div>
    
</body>
</html>