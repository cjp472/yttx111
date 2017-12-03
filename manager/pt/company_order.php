<?php 
$menu_flag = "company_order";
include_once ("header.php");

if(!isset($in['pay_status'])) {
    //$in['pay_status'] = '1'; // 默认已到账
    $in['pay_status'] = '-1'; // 默认全部
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    <script src="../scripts/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script src="js/company.js?v=<? echo VERID;?>" type="text/javascript"></script>

    <!--<script type="text/javascript" src="../plugin/My97DatePicker/jquery-ui.js"></script>-->
    <script type="text/javascript" src="../plugin/My97DatePicker/jquery-ui-slide.min.js"></script>
    <script type="text/javascript" src="../plugin/My97DatePicker/jquery-ui-timepicker-addon.js"></script>
    <style type="text/css">
        .ui-timepicker-div .ui-widget-header { margin-bottom: 8px;}
        .ui-timepicker-div dl { text-align: left; }
        .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
        .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
        .ui-timepicker-div td { font-size: 90%; }
        .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
        .ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label,.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px}
    </style>
    <script type="text/javascript">
        $(function(){
            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });
            $("#bdate").datepicker({changeMonth: true,	changeYear: true});
            $("#edate").datepicker({changeMonth: true,	changeYear: true});
            //$("[data-result-end-date]").datepicker({changeMonth: true,	changeYear: true});
            $('[data-result-end-date]').timepicker();
        });
    </script>
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
                      <select name="type"  style="width:105px;" class="select2">
                          <option value="">全部</option>
                          <option value="renewals" <?php if($in['type'] == 'renewals') echo 'selected="selected"';?> >系统续费</option>
                          <option value="product" <?php if($in['type'] == 'product') echo 'selected="selected"';?> >系统开通</option>
                          <option value="weixin" <?php if($in['type'] == 'weixin') echo 'selected="selected"';?> >微信</option>
                          <option value="erp" <?php if($in['type'] == 'erp') echo 'selected="selected"';?> >ERP接口</option>
                          <option value="sms" <?php if($in['type'] == 'sms') echo 'selected="selected"';?> >购买短信</option>
						  <option value="api" <?php if($in['type'] == 'api') echo 'selected="selected"';?> >API接口</option>
                      </select>
                  </label>
                  <label>
                      <select name="pay_status"  style="width:105px;" class="select2">
                          <option value="-1">全部</option>
                          <option value="1" <?php if($in['pay_status'] == '1') echo 'selected="selected"';?> >已到账</option>
                          <option value="0" <?php if($in['pay_status'] == '0') echo 'selected="selected"';?> >未到账</option>

                      </select>
                  </label>
                  <label>
                      时间过滤
                  </label>
                  <label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
                  <label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

                  </label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location">
                <!--<strong>当前位置：</strong><a href="company_order.php">客户订单</a>-->
                <input type="button" class="mainbtn" onclick="location.href='company_order_add.php'" value="新增订单" title="新增订单及支付信息" />
            </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or o.order_no like '%".$in['kw']."%' ) ";
    if(!empty($in['bdate'])) {
        $sqlmsg .= " AND time >= " . strtotime($in['bdate'] . ' 00:00:00');
    }

    if(!empty($in['edate'])) {
        $sqlmsg .= " AND time <= " . strtotime($in['edate'] . ' 23:59:59');
    }

    if(!empty($in['type'])) {
        $sqlmsg .= " AND o.type='{$in['type']}' ";
    }

    if($in['pay_status'] != -1) {
        $sqlmsg .= " AND o.pay_status=" . $in['pay_status'];
    }

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_buy_order o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],'bdate' => $in['bdate'] , 'edate' => $in['edate'] , 'type' => $in['type'], 'pay_status' => $in['pay_status']);
	
	$datasql   = "SELECT o.*,c.CompanyName FROM ".DATABASEU.DATATABLE."_buy_order o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ORDER BY o.id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="3%" class="bottomlinebold">行号</td>
                  <td width="14%" class="bottomlinebold">订单号/公司</td>
                  <td width="10%" class="bottomlinebold">订单类型</td>
                  <td width="10%" class="bottomlinebold">版本</td>
				  <td width="5%" align="right" class="bottomlinebold">赠送短信</td>
				  <td width="5%" align="right" class="bottomlinebold">购买数量</td>
				  <td align="right"  width="7%" class="bottomlinebold">单价(元)</td>
				  <td align="right"  width="7%" class="bottomlinebold">总价(元)</td>
				  <td align="right"  width="7%" class="bottomlinebold">已收款(元)</td>
				  <td align="center" width="10%" class="bottomlinebold">下单时间</td>
				  <td align="left"  class="bottomlinebold">备注</td>
				  <td align="center" width="8%" class="bottomlinebold">支付状态</td>
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
                  <td >
                      <? echo $lsv['order_no'];?><br/>
                      <? echo '<a title="" href="manager_company.php?ID='.$lsv['company_id'].'">'.$lsv['CompanyName'].'</a>' ;?>
                  </td>
                  <td >
                      <?php
                        switch($lsv['type']) {
                            case 'product':
                                echo "系统开通";
                                break;
                            case 'sms':
                                echo "购买短信";
                                break;
                            case 'weixin':
                                echo "微信";
                                break;
                            case 'erp':
                                echo "ERP接口";
                                break;
							case 'api':
                                echo "API接口";
                                break;
                            case 'renewals':
                                echo "系统续费";
                                break;
                        }
                      ?>
                  </td>
                  <td ><? echo $lsv['title'];?></td>
				  <td align="right" ><? if(in_array($lsv['type'],array('renewals','product'))){$data = json_decode($lsv['data'],true); echo $data['gift_sms'].'条';}?></td>
				  <td align="right" ><? echo $lsv['buy_count']; if(in_array($lsv['type'],array('renewals','product','erp'))){echo '年 ';}elseif ($lsv['type']=='sms'){echo '条 ';}elseif ($lsv['type']=='weixin'){echo '次 ';}?></td>
				  <td align="right" ><? echo '￥'.$lsv['amount'];?></td>
				  <td align="right" ><? echo '￥'.$lsv['total'];?></td>
				  <td align="right" ><? echo '￥'.$lsv['integral'];?></td>
				  <td align="center" ><? echo date("y-m-d H:i",$lsv['time']);?></td>
				  <td align="left" ><? echo $lsv['remark'];?></td>
				  <td align="center">
                      <? if(empty($lsv['pay_status'])){
                          echo '[<a href="javascript:;" onclick="showPay('.$lsv['id'].",'".$lsv['CompanyName']."',".$lsv['total'].')" title="确认订单到账">支付</a>]';
                      }else{
                          echo '已到账-';
                      }?>
                      [<a href="javascript:;" onclick="showPayInfo('<?php echo $lsv['id']; ?>','<?php echo $lsv['CompanyName']; ?>');" data-order-no="<? echo $lsv['order_no']; ?>">查看</a>]
                  </td>
				  <td align="center">
                      <? if(empty($lsv['status'])&&!empty($lsv['pay_status'])){
                          //echo '[<a href="javascript:;" onclick="do_companyorder_status('.$lsv['id'].')" title="确认订单开通">开通</a>]';
                          //echo '[<a href="javascript:;" onclick="sureOpenForm('.$lsv['id'].',\''.$lsv['type'].'\')" title="确认订单开通">确认开通</a>]';
                      }elseif (empty($lsv['status'])&&empty($lsv['pay_status'])){
                          //echo '未付款';
                      }elseif (!empty($lsv['status'])){
                          //echo '是';
                      }?>

                      <?php if(!empty($lsv['status'])) {
                          echo '已开通';
                      } else {
                          echo '[<a href="javascript:;" onclick="sureOpenForm('.$lsv['id'].',\''.($lsv['type'] &&  $lsv['type']!='api'? $lsv['type'] : 'erp').'\')" title="确认订单开通">开通</a>]';
                      } ?>
                  </td>

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
<div id="windowForm" style="width:400px;">
    <div class="windowHeader">
        <h3 id="windowtitle">新增转账信息</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <form action="" id="line_fm">
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
                <td align="right">转入账号：</td>
                <td align="left">
                	<input name="payID" type="hidden" id="payID" value=""/>
                    <select name="account">
                        <option value="">请选择</option>
                        <option value="公司支付宝">公司支付宝</option>
                        <option value="对私-建设银行">对私-建设银行</option>
                        <option value="对公-招商银行">对公-招商银行</option>
                    </select>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">备注：</td>
                <td align="left">
                    <textarea name="remark" style="width:300px;height:80px;"></textarea>
                </td>
            </tr>
            <tr class="bottomline">
                <td colspan="2">
                    <input onclick="do_companyorder_pay()" class="button_1" type="button" value="确认" />
                    <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                </td>
            </tr>
        </table>
        </form>
    </div>
</div>
<div id="windowForm_info" style="display:none;width:400px;">
    <div class="windowHeader">
        <h3 id="windowtitle">订单付款信息</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <form action="" id="line_fm">
            <table width="100%">
            	<tr class="bottomline">
                    <td width="24%" align="right">支付状态：</td>
                    <td align="left">
                        <span data-pay-status></span>
                    </td>
                </tr>
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
                    <td align="right">支付时间：</td>
                    <td align="left">
                        <span data-pay-time></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">到账时间：</td>
                    <td align="left">
                        <span data-to-time></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">转入账号：</td>
                    <td align="left">
                        <span data-line></span>
                        <input name="payID" type="hidden" id="payID" value=""/>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">流水单号：</td>
                    <td align="left">
                        <span data-stream_no></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">支付平台单号：</td>
                    <td align="left">
                        <span data-trade_no></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">备注：</td>
                    <td align="left">
                        <span data-remark></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td colspan="2">
                        <!--<input onclick="console.log('todo:');" class="button_1" type="button" value="确认到账" />-->
                        <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div id="windowForm_product" style="display:none;width:500px;">
    <div class="windowHeader">
        <h3 id="windowtitle">确定到账并开通</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <form action="do_company.php" id="line_fm_product">
            <input name="m" value="order_sure_and_open" type="hidden"/>
            <input name="order_id" type="hidden"/>
            <table width="100%">
                <tr class="bottomline">
                    <td width="24%" align="right">公司：</td>
                    <td align="left">
                        <span data-company></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单金额：</td>
                    <td align="left">
                        <span data-total></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">支付金额：</td>
                    <td align="left">
                        <span data-integral></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">转入账号：</td>
                    <td align="left" data-account>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">购买时长：</td>
                    <td align="left" data-time>
                    </td>
                </tr>
                <tr class="bottomline" data-buy-result>
                    <td align="right">赠送时长：</td>
                    <td align="left" data-gift-time>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">用户数：</td>
                    <td align="left" data-user-number></td>
                </tr>
                <tr class="bottomline">
                    <td align="right">到期时间：</td>
                    <td align="left" data-old-end-date></td>
                </tr>
                <tr class="bottomline" id="result-tr">
                    <td align="right">续费到：</td>
                    <td align="left">
                        <input type="text" data-result-end-date name="end_date" style="z-index:10010;position:relative;"/>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">短信余额：</td>
                    <td align="left" data-surplus-sms>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">赠送短信：</td>
                    <td align="left">
                        <input type="text" data-input-sms name="gift_sms" style="padding-left:5px;" /> (条)
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单备注：</td>
                    <td align="left" data-remark>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td colspan="2">
                        <input onclick="sureAndOpen('product')" class="button_1" type="button" value="到账并开通" />
                        <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div id="windowForm_sms" style="display:none;width:400px;">
    <div class="windowHeader">
        <h3 id="windowtitle">确定到账并开通</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <form action="do_company.php" id="line_fm_sms">
            <input name="m" type="hidden" value="order_sure_and_open"/>
            <input name="order_id" type="hidden"/>
            <table width="100%">
                <tr class="bottomline">
                    <td width="24%" align="right">公司：</td>
                    <td align="left">
                        <span data-company></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单金额：</td>
                    <td align="left">
                        <span data-total></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">支付金额：</td>
                    <td align="left">
                        <span data-integral></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">转入账号：</td>
                    <td align="left" data-account>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">短信余额：</td>
                    <td align="left" data-surplus-sms>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">购买短信：</td>
                    <td align="left">
                        <input type="text" data-sms name="sms" style="padding-left:5px;" /> (条)
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单备注：</td>
                    <td align="left" data-remark>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td colspan="2">
                        <input onclick="sureAndOpen('sms')" class="button_1" type="button" value="到账并开通" />
                        <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div id="windowForm_weixin" style="display:none;width:400px;">
    <div class="windowHeader">
        <h3 id="windowtitle">确定到账并开通</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <form action="do_company.php" id="line_fm_weixin">
            <input name="m" type="hidden" value="order_sure_and_open"/>
            <input name="order_id" type="hidden"/>
            <table width="100%">
                <tr class="bottomline">
                    <td width="24%" align="right">公司：</td>
                    <td align="left">
                        <span data-company></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单金额：</td>
                    <td align="left">
                        <span data-total></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">支付金额：</td>
                    <td align="left">
                        <span data-integral></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">转入账号：</td>
                    <td align="left" data-account>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">购买次数：</td>
                    <td align="left" data-time>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单备注：</td>
                    <td align="left" data-remark>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td colspan="2">
                        <input onclick="sureAndOpen('weixin')" class="button_1" type="button" value="到账并开通" />
                        <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div id="windowForm_erp" style="display:none;width:400px;">
    <div class="windowHeader">
        <h3 id="windowtitle">确定到账并开通</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
        <form action="do_company.php" id="line_fm_erp">
            <input name="m" type="hidden" value="order_sure_and_open"/>
            <input name="order_id" type="hidden"/>
            <table width="100%">
                <tr class="bottomline">
                    <td width="24%" align="right">公司：</td>
                    <td align="left">
                        <span data-company></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单金额：</td>
                    <td align="left">
                        <span data-total></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">支付金额：</td>
                    <td align="left">
                        <span data-integral></span>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">转入账号：</td>
                    <td align="left" data-account>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">购买时长：</td>
                    <td align="left" data-time>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td align="right">订单备注：</td>
                    <td align="left" data-remark>
                    </td>
                </tr>
                <tr class="bottomline">
                    <td colspan="2">
                        <input onclick="sureAndOpen('erp')" class="button_1" type="button" value="到账并开通" />
                        <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
</body>
</html>