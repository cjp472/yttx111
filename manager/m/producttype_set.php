<?php 
$menu_flag = "system";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" />
<style type="text/css">

    a:hover{text-decoration:underline}
    ol,ul{list-style:none}
    body{height:100%; font:12px/18px Tahoma, Helvetica, Arial, Verdana, "\5b8b\4f53", sans-serif; color:#51555C;}
    img{border:none}
    .demo{width:500px; margin:20px auto}
    .demo h4{height:32px; line-height:32px; font-size:14px}
    .demo h4 span{font-weight:500; font-size:12px}
    .demo p{line-height:28px;}
    input{width:200px; height:20px; line-height:20px; padding:2px; border:1px solid #d3d3d3}
    pre{padding:6px 0 0 0; color:#666; line-height:20px; background:#f7f7f7}
    
    .ui-datepicker {display:none;}
    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px;}
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
    .ui_tpicker_hour_label,.ui_tpicker_minute_label,.ui_tpicker_second_label,.ui_tpicker_millisec_label,.ui_tpicker_time_label{padding-left:20px}
</style>
<script src="../scripts/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/My97DatePicker/jquery-ui.js"></script>
<script type="text/javascript" src="../plugin/My97DatePicker/jquery-ui-slide.min.js"></script>
<script type="text/javascript" src="../plugin/My97DatePicker/jquery-ui-timepicker-addon.js"></script>

<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <script type="text/javascript">
    var weekDay = new Array('周一','周二','周三','周四','周五','周六','周日');
        $(function(){
            $("input[name='regiester_type']").change(function(){
                var val = $("input[name='regiester_type']:checked").val();
                if(val == 'on') {
                    //开放注册 显示默认注册后账户类型
                    $(".rely_reg").show();
                } else {
                    //不开放注册 隐藏
                    $(".rely_reg").hide();
                }
            }).change();

            $("input[name='stair_status']").change(function(){
                var val = $("input[name='stair_status']:checked").val();
                if(val == 'Y') {
                    //启用满省功能
                    $("#stair_div").show();
                } else {
                    //禁用满省功能
                    $("#stair_div").hide();

                }
            }).change();

            $("input[name='order_time']").click(function(){
                var ordertime = $(this).val();
                if(ordertime == 'on'){
                	$('#ordertime_date').show();
                }else{
                    $('#ordertime_date').hide();
                }

                $("input[name='order_time']").removeAttr("checked");
                $(this).attr('checked',true);
            });

            $('#ordertime_timestart').timepicker({
              	   hourGrid: 4,
              	   minuteGrid: 10
              });

          	$('#ordertime_timeend').timepicker({
          		   hourGrid: 4,
          		   minuteGrid: 10
          		});
        });     
        
    /*    function checkErp(is_erp){
        	if(is_erp!=''){
        		alert('Erp同步药店客户不允许开放注册！');
        		$('#regiester_type2').attr("checked",'checked');
        	}
        } */
    </script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="producttype_set.php">模式设置</a></div>
   	        </div>     
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->

<br style="clear:both;" />
</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">
			<div id="oldinfo" class="line">
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<fieldset  class="fieldsetstyle">		
			<legend>模式设置</legend>
            <tr><td height="5"></td><td></td></tr>
			<table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >                
                <?
                //$is_erp = erp_is_run($db,$_SESSION['uinfo']['ucompany']);
				$valuearr = null;
				$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$_SESSION['uinfo']['ucompany']." and SetName='product' limit 0,1");
				if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);
				?>
                <tr>
                  <td ><div align="right"></div></td>
                  <td><input type="button" name="sendbuttoninfo" id="sendbuttoninfo" value="保存设置" class="button_2" onclick="savesettype('product');" /></td>
                </tr>

				<tr>
                  <td height="50" width="28%" bgcolor="#F0F0F0" align="right"><strong>是否启用订单核准：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="checkandapprove" id="checkandapprove_off" value="off" <? if($valuearr['checkandapprove'] == "off" || empty($valuearr['checkandapprove'])) echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;不启用</strong></div>
						<div style="height:28px; clear:both;"><input type="radio" name="checkandapprove" id="checkandapprove_on"  value="on"  <? if($valuearr['checkandapprove'] == "on") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;启用</strong></div>			  
				  </td>
                </tr>
				<tr>
                  <td height="50" width="28%" bgcolor="#F0F0F0" align="right"><strong>商品默认显示方式：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="producttype" id="producttype_img" value="imglist" <? if($valuearr['producttype'] == "imglist" || empty($valuearr['producttype'])) echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;图文方式显示</strong></div>
						<div style="height:28px; clear:both;"><input type="radio" name="producttype" id="producttype_text" value="textlist" <? if($valuearr['producttype'] == "textlist") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;列表方式显示</strong></div>			  
				  </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>

				<tr>
                  <td height="50" bgcolor="#F0F0F0" align="right"><strong>是否启用商品库存：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="producttype_number" id="producttype_number1" value="on" <? if($valuearr['product_number'] == "on") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;启 用</strong>（如启用 将按现有库存订货，发货）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="producttype_number" id="producttype_number2" value="off" <? if($valuearr['product_number'] == "off" || empty($valuearr['product_number'])) echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;不启用</strong></div>			  
				  </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50" bgcolor="#F0F0F0" align="right"><strong>是否允许负库存订货：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="producttype_negative" id="producttype_negative1" value="on" <? if($valuearr['product_negative'] == "on") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;允 许</strong>（如允许 商品订购数量将允许超过商品的可用库存）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="producttype_negative" id="producttype_negative2" value="off" <? if($valuearr['product_negative'] == "off" || empty($valuearr['product_negative'])) echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;不允许</strong></div>			  
				  </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50" bgcolor="#F0F0F0" align="right"><strong>是否显示库存状况：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="producttype_number_show" id="producttype_number_show1" value="on" <? if($valuearr['product_number_show'] == "on") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;显 示</strong>（如启用 将在商品列表页显示库存状况。）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="producttype_number_show" id="producttype_number_show2" value="off" <? if(empty($valuearr['product_number_show']) || $valuearr['product_number_show'] == "off") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;不显示</strong></div>			  
				  </td>
                </tr>

                <tr><td height="5"></td><td></td></tr>
                <tr>
                    <td height="70" bgcolor="#F0F0F0" align="right"><strong>价格配置</strong></td>
                    <td >
                        <table width="350px;">
                            <tr>
                                <td>
                                    <input class="price-check" type="checkbox" value="on" name="producttype_price1_show" <?php echo $valuearr['product_price']['price1_show']=='on' ? "checked='checked'" : ''; ?> /><strong>是否显示</strong>
                                </td>
                                <td>
                                    价格一: <input type="text" style="width:100px;" name="producttype_price1_name" value="<?php echo $valuearr['product_price']['price1_name']; ?>" maxlength="6"/> &nbsp;&nbsp; 如:参考价
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="price-check" type="checkbox" value="on" name="producttype_price2_show" <?php echo $valuearr['product_price']['price2_show']=='on' ? "checked='checked'" : ''; ?> /><strong>是否显示</strong>
                                </td>
                                <td>
                                    价格二: <input type="text" style="width:100px;" name="producttype_price2_name" value="<?php echo $valuearr['product_price']['price2_name']; ?>"  maxlength="6" /> &nbsp;&nbsp; 如:吊牌价 
                                </td>
                            </tr>
							<tr><td colspan="2">(注：是否显示是指在订货端显示，两个价格同时只能显示一个)</td></tr>

                        </table>
                        <script type="text/javascript">
                            $(".price-check").click(function(){
                                var isCheck = this.checked;
                                $(".price-check").attr('checked',false);
                                this.checked = isCheck;
                            });
                        </script>
                    </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>

                <tr>
                    <td height="50"  bgcolor="#F0F0F0" align="right"><strong>显示余额：</strong></td>
                    <td >
                        <div style="height:28px; clear:both;"><input type="radio" name="show_money" id="show_money1" value="on" checked="checked" style="border:0;" /><strong>&nbsp;显示</strong>（注：将在订货端顶端显示当前用户余额信息!）</div>
                        <div style="height:28px; clear:both;"><input type="radio" name="show_money" id="show_money2" value="off" <? if($valuearr['show_money'] == "off") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;不显示</strong></div>
                    </td>
                </tr>

                <tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>退货方式：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="return_type" id="return_type1" value="order" <? if($valuearr['return_type'] == "order") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;根据客户订单</strong>（注：先查询订单，然后选择订单中可退货的商品）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="return_type" id="return_type2" value="product" <? if(empty($valuearr['return_type']) || $valuearr['return_type'] == "product") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;根据订购商品</strong>（注：直接查询可退货的商品，再退货）</div>			  
				  </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>客情官提成：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="deduct_type" id="deduct_type1" value="on" <? if($valuearr['deduct_type'] == "on") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;启用</strong>（注：根据商品资料里设置好的提成比例来计算提成）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="deduct_type" id="deduct_type2" value="off" <? if(empty($valuearr['deduct_type']) || $valuearr['deduct_type'] == "off") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;不启用</strong></div>			  
				  </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>订单审核：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="audit_type" id="audit_type1" value="on" <? if($valuearr['audit_type'] == "on") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;二级审核</strong>（注：客情官初审订单后，管理员再复审订单）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="audit_type" id="audit_type2" value="off" <? if(empty($valuearr['audit_type']) || $valuearr['audit_type'] == "off") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;一级审核</strong>（注：由管理员或客情官审核订单）</div>			  
				  </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>客户注册：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="regiester_type" id="regiester_type1" value="on" <? if($valuearr['regiester_type'] == "on") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;开放注册</strong>（注：客户注册后，管理员审核后方可登录订货！）</div>
						<div style="height:28px; clear:both;"><input type="radio" name="regiester_type" id="regiester_type2" value="off" <? if(empty($valuearr['regiester_type']) || $valuearr['regiester_type'] == "off") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;关闭注册</strong>（注：不开放注册功能）</div>
				  </td>
                </tr>

                <tr class="rely_reg"><td height="5"></td><td></td></tr>
                <tr class="rely_reg">
                    <td height="50"  bgcolor="#F0F0F0" align="right"><strong>客户注册类型：</strong></td>
                    <td >
                        <div style="height:28px; clear:both;"><input type="radio" name="regiester_type_status" value="9" <? if($valuearr['regiester_type_status'] == "9" || !isset($valuearr['regiester_type_status'])) echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;待审核</strong> (注：管理员审核后方可登录订货!)</div>
                        <div style="height:28px; clear:both;"><input type="radio" name="regiester_type_status" value="8" <? if($valuearr['regiester_type_status'] == "8") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;只读</strong> (注：允许浏览,不可下单)</div>
                        <div style="height:28px; clear:both;"><input type="radio" name="regiester_type_status" value="0" <? if($valuearr['regiester_type_status'] == "0") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;正式账号</strong></div>
                    </td>
                </tr>

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>开票设置：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="checkbox" name="invoice_type_p" id="invoice_type_p" value="P" <? if($valuearr['invoice_p'] == "Y") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;可开普票</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;税点：<input type="text" name="taxingpppoint_p" id="taxingpppoint_p"  style="width:100px; text-align:right;" value="<?php echo intval($valuearr['invoice_p_tax']);?>" />&nbsp;%</div>
						<div style="height:28px; clear:both;"><input type="checkbox" name="invoice_type_z" id="invoice_type_z" value="Z" <? if($valuearr['invoice_z'] == "Y") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;可开增值税发票</strong>&nbsp;&nbsp;&nbsp;&nbsp;税点：<input type="text" name="taxingpppoint_z" id="taxingpppoint_z"  style="width:100px;text-align:right;" value="<?php echo intval($valuearr['invoice_z_tax']);?>"  />&nbsp;%</div>
				  </td>
                </tr>               

				<tr><td height="5"></td><td></td></tr>
				<tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>交货时间：</strong></td>
                  <td >
						<div style="height:28px; clear:both;"><input type="radio" name="delivery_time" id="delivery_time1" value="N" <? if(empty($valuearr['delivery_time']) || $valuearr['delivery_time'] == "N") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;不启用</strong>&nbsp;</div>
						<div style="height:28px; clear:both;"><input type="radio" name="delivery_time" id="delivery_time2" value="Y" <? if($valuearr['delivery_time'] == "Y") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;启用非必填</strong>&nbsp;&nbsp;&nbsp;</div>
						<div style="height:28px; clear:both;"><input type="radio" name="delivery_time" id="delivery_time3" value="B" <? if($valuearr['delivery_time'] == "B") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;启用且必填</strong>&nbsp;&nbsp;&nbsp;</div>			  
				  </td>
                </tr>

                <tr><td height="5"></td><td></td></tr>
                <tr>
                  <td height="50"  bgcolor="#F0F0F0" align="right"><strong>订单提交时间设置：</strong></td>
                  <td >
        				<div style="height:28px; clear:both;"><input type="radio" name="order_time" id="order_time1" value="off" <? if($valuearr['ordertime']['time_show'] == "off" || empty($valuearr['ordertime']['time_show'])) echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;不启用</strong></div>
        				<div style="height:28px; clear:both;"><input type="radio" name="order_time" id="order_time2" value="on" <? if($valuearr['ordertime']['time_show'] == "on") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;启用</strong>&nbsp;&nbsp;&nbsp;      				     
        				</div>
        				<div id="ordertime_date" style="<?php if($valuearr['ordertime']['time_show'] != "on") echo 'display:none';?>">
            				<div style="height:28px; clear:both;margin-left:32px;">
            				 工作时间：<select name="ordertime_datestart" id="ordertime_datestart"  style="width:75px;" onchange="orderitem_select();" value="<?php echo intval($valuearr['ordertime']['date_start']);?>">
                				      <option value="0" <? if($valuearr['ordertime']['date_start'] == '0' || empty($valuearr['ordertime']['date_start'])) echo "selected='selected'" ?>>选择时间</option>
                				      <?php 
                				        $weekday = array('1'=>'周一','2'=>'周二','3'=>'周三','4'=>'周四','5'=>'周五','6'=>'周六','7'=>'周日');
                				        $startdate = intval(empty($valuearr['ordertime']['date_start']) ? '0':$valuearr['ordertime']['date_start']);
            				            for($i = 1; $i <=7;$i++){
            				                if($i == $startdate)
            				                    echo "<option value='".$i."' selected='selected'>".$weekday[$i]."</option>"; 
            				                else 
            				                    echo "<option value='".$i."'>".$weekday[$i]."</option>";
            				            }
                				      ?>
            				      </select>
            				    &nbsp; 
            				    <input type="text" name="ordertime_timestart" id="ordertime_timestart"  style="width:68px;" value="<?php echo $valuearr['ordertime']['time_start'];?>"/>
            				      
            				</div>
            				<div style="height:28px; clear:both;margin-left:70px;margin-top:5px;">	
            				  至 &nbsp;
            				   <select name="ordertime_dateend" id="ordertime_dateend"  style="width:75px;" value="<?php echo intval($valuearr['ordertime']['date_end']);?>">
            				          <option value="0" <? if($valuearr['ordertime']['date_end'] == '0') echo "selected='selected'" ?>>选择时间</option>
                				      <?php 
                				        $enddate = intval($valuearr['ordertime']['date_end']=='' ? '0':$valuearr['ordertime']['date_end']);
            				            for($i = 1; $i <=7;$i++){
            				                if($i == $enddate)
            				                    echo "<option value='".$i."' selected='selected'>".$weekday[$i]."</option>"; 
            				                else 
            				                    echo "<option value='".$i."'>".$weekday[$i]."</option>";
            				            }
                				      ?>
            				      </select>
            				     &nbsp;
            				      <input type="text" name="ordertime_timeend" id="ordertime_timeend" readonly='true' style="width:68px;" value="<?php echo $valuearr['ordertime']['time_end'];?>"/>       				     
            				</div>
            				<div style="margin-left:32px;margin-top:5px;">
            				    <label id="timeInfo" style="color:green;">* 您可以设置跨周工作时间，如：周五 至 周二即本周五到次周二允许订货</label>	
            				</div>        				
        				</div>       				
        		  </td>
                </tr>

                <tr><td height="5"></td><td></td></tr>
                <tr>
                    <td bgcolor="#F0F0F0" align="right"><strong>是否启用满省：</strong></td>
                    <td >
                        <div style="height:28px; clear:both;">
                            <label>
                                <input type="radio" name="stair_status" value="N" <? if(empty($valuearr['stair_status']) || $valuearr['stair_status'] == "N") echo 'checked="checked"';?> style="border:0;" /><strong>&nbsp;不启用</strong>&nbsp;
                            </label>
                        </div>
                        <div style="height:28px; clear:both;">
                            <label>
                                <input type="radio" name="stair_status" value="Y" <? if($valuearr['stair_status'] == "Y") echo 'checked="checked"';?> style="border:0;"  /><strong>&nbsp;启用</strong>&nbsp;&nbsp;&nbsp;
                            </label>
                        </div>
                        <div style="clear:both;margin-left:32px;" id="stair_div">
                            <?php
                                $stair = $valuearr['stair'];
                            ?>
                            <table>
                                <?php if(empty($stair)) { ?>
                                <tr data-row="0">
                                    <td>
                                        满：
                                        <input type="text" name="stair[0][amount]" style="width:68px;"/>
                                        省
                                        <input type="text" name="stair[0][count]" style="width:68px;"/>
                                        <input type="button" value="+" style="border:1px solid #CCC;height:26px;width:40px;cursor:pointer;" onclick="add_stair_row();" title="增加一行"/>
                                    </td>
                                </tr>
                                <?php } else {
                                    foreach($stair as $sk => $sv) {?>
                                        <tr data-row="<?php echo $sk; ?>">
                                            <td>
                                                满：
                                                <input type="text" name="stair[<?php echo $sk; ?>][amount]" value="<?php echo $sv['amount']; ?>" style="width:68px;"/>
                                                省
                                                <input type="text" name="stair[<?php echo $sk; ?>][count]" value="<?php echo $sv['count']; ?>" style="width:68px;"/>
                                                <?php if($sk == 0) { ?>
                                                    <input type="button" value="+" style="border:1px solid #CCC;height:26px;width:40px;cursor:pointer;" onclick="add_stair_row();" title="增加一行"/>
                                                <?php } else { ?>
                                                    <input type="button" value="-" style="border:1px solid #CCC;height:26px;width:40px;cursor:pointer;" onclick="remove_stair_row(this);" title="减少一行"/>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>

                                <?php } ?>
                                <tr class="stair_clone" style="display:none;">
                                    <td>
                                        满：
                                        <input type="text" name="" style="width:68px;"/>
                                        省
                                        <input type="text" name="" style="width:68px;"/>
                                        <input type="button" value="-" style="border:1px solid #CCC;height:26px;width:40px;cursor:pointer;" onclick="remove_stair_row(this);" title="减少一行"/>
                                    </td>
                                </tr>
                            </table>
                            <label id="timeInfo" style="color:green;">* 请按金额升序填写,订单满省金额以满足条件优惠最大的项计算!</label>
                            <script type="text/javascript">
                                function add_stair_row() {
                                    var row = $(".stair_clone").clone(true).removeClass('stair_clone').show();
                                    var prev = $(".stair_clone").prev("tr");
                                    var row_idx = parseInt(prev.data('row')) + 1;
                                    row.find("input:eq(0)").attr('name','stair['+row_idx+'][amount]');
                                    row.find("input:eq(1)").attr('name','stair['+row_idx+'][count]');
                                    row.data('row', row_idx);
                                    row.insertBefore(".stair_clone");
                                }

                                function remove_stair_row(el) {
                                    $(el).parent().parent().remove();
                                }

                            </script>
                        </div>
                    </td>
                </tr>
				
                
                <tr><td height="5"></td><td></td></tr>

                <tr>
                  <td ><div align="right"></div></td>
                  <td><input type="button" name="sendbuttoninfo" id="sendbuttoninfo" value="保存设置" class="button_2" onclick="savesettype('product');" /></td>
                </tr>
            </table>
           </fieldset>  
            
			</form>
			<br style="clear:both;" />
            </div>
        	</div>              
        <br style="clear:both;" />
     </div>
	 </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>