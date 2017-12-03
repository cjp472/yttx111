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
        	  <form id="FormSearch" name="FormSearch" method="post" action="company_order.php">
			  
        	    <label>
        	      &nbsp;&nbsp;订单号，公司： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="company_order.php">客户订单</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or o.order_no like '%".$in['kw']."%' ) ";


	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_buy_order o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT o.*,c.CompanyName FROM ".DATABASEU.DATATABLE."_buy_order o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ORDER BY o.id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="3%" class="bottomlinebold">行号</td>
                  <td width="10%" class="bottomlinebold">订单号</td>
                  <td width="10%" class="bottomlinebold">公司</td>
                  <td width="10%" class="bottomlinebold">版本</td>
				  <td width="5%" align="right" class="bottomlinebold">赠送短信</td>
				  <td width="5%" align="right" class="bottomlinebold">购买数量</td>
				  <td align="right"  width="7%" class="bottomlinebold">单价(元)</td>
				  <td align="right"  width="7%" class="bottomlinebold">总价(元)</td>
				  <td align="right"  width="7%" class="bottomlinebold">已收款(元)</td>
				  <td align="center" width="10%" class="bottomlinebold">下单时间</td>
				  <td align="left"  class="bottomlinebold">备注</td>
				  <td align="center" width="5%" class="bottomlinebold">支付状态</td>	
				  <td align="center" width="5%" class="bottomlinebold">开通状态</td>
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
                  <td ><? echo $lsv['order_no'];?></td>
                  <td ><? echo '<a title="" href="manager_company.php?ID='.$lsv['company_id'].'">'.$lsv['CompanyName'].'</a>' ;?></td>
                  <td ><? echo $lsv['title'];?></td>
				  <td align="right" ><? if($lsv['type']=='product'){$data = json_decode($lsv['data'],true); echo $data['gift_sms'].'条';}?></td>
				  <td align="right" ><? echo $lsv['buy_count']; if(($lsv['type']=='product')||($lsv['type']=='erp')){echo '年 ';}elseif ($lsv['type']=='sms'){echo '条 ';}elseif ($lsv['type']=='weixin'){echo '次 ';}?></td>
				  <td align="right" ><? echo '￥'.$lsv['amount'];?></td>
				  <td align="right" ><? echo '￥'.$lsv['total'];?></td>
				  <td align="right" ><? echo '￥'.$lsv['integral'];?></td>
				  <td align="center" ><? echo date("y-m-d H:i",$lsv['time']);?></td>
				  <td align="left" ><? echo $lsv['remark'];?></td>
				  <td align="center"><? if(empty($lsv['pay_status'])){ echo '[<a href="javascript:;" onclick="showPay('.$lsv['id'].",'".$lsv['CompanyName']."',".$lsv['total'].')" title="确认订单到账">到账</a>]';}else{echo '是';}?></td>
				  <td align="center"><? if(empty($lsv['status'])&&!empty($lsv['pay_status'])){ echo '[<a href="javascript:;" onclick="do_companyorder_status('.$lsv['id'].')" title="确认订单开通">开通</a>]';}elseif (empty($lsv['status'])&&empty($lsv['pay_status'])){echo '未付款';}elseif (!empty($lsv['status'])){echo '是';}?></td>

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
   			         
       			     <td  align="right"><? echo $page->ShowLink('company_order.php');?></td>
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
        <h3 id="windowtitle">订单支付信息</h3>
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
                <td align="right">转入方式：</td>
                <td align="left">
                	<input name="payID" type="hidden" id="payID" value=""/>
                    <select name="account">
                        <option value="">请选择</option>
                        <option value="支付宝">支付宝</option>
                        <option value="网银转账">网银转账</option>
                    </select>
                </td>
            </tr>
            <tr class="bottomline">
                <td colspan="2">
                    <input onclick="do_companyorder_pay()" class="button_1" type="button" value="确认" />
                    <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                </td>
            </tr>
        </table>
    </div>
</div>
    
</body>
</html>