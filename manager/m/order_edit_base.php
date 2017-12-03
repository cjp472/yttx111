<?php
$menu_flag = "order";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!empty($in['ID']))
{
    $oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderID=".intval($in['ID'])." limit 0,1");
}elseif(!empty($in['SN'])){
    $oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$in['SN']."' limit 0,1");
}else{
    exit('错误参数!');
}

$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$oinfo['OrderUserID']." limit 0,1");

$sql1 = "select * from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by SiteID asc,ID asc";
$cartdata = $db->get_results($sql1);

$cidmsg = '';
$valuearr = get_set_arr('product');
setcookie("backurl", $_SERVER['REQUEST_URI']);

?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
        <script type="text/javascript">
            $(function(){
                $("#bdate").datepicker({changeMonth: true,	changeYear: true});
                $("#edate").datepicker({changeMonth: true,	changeYear: true});
                $("input[name='DeliveryDate']").datepicker({changeMonth:true,changeYear:true});
            });
        </script>
    </head>

    <body>
    <?php include_once ("top.php");?>
    <div id="bodycontent">
    <div class="lineblank"></div>
    <div id="searchline">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" >
            <form id="FormSearch" name="FormSearch" method="get" action="order.php">
                <tr>
                    <td width="80" align="center"><strong>订单搜索：</strong></td>
                    <td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" /></td>
                    <td width="80">
                        <select name="stype" id="stype" class="selectline">
                            <option value="ordersn" <?php if($in['stype']=="ordersn") echo 'selected="selected"'; ?> >订单号</option>
                            <option value="productname" <?php if($in['stype']=="productname") echo 'selected="selected"'; ?> > 商品名称 </option>
                            <option value="giftsname" <?php if($in['stype']=="giftsname") echo 'selected="selected"'; ?>>赠品名称</option>
                        </select>
                    </td>
                    <td align="center" width="100"><select name="dtype" id="dtype" class="selectline">
                            <option value="order" <?php if($in['dtype']=="order") echo 'selected="selected"'; ?> >订单日期</option>
                            <option value="delivery" <?php if($in['dtype']=="delivery") echo 'selected="selected"'; ?> > 交货日期 </option>
                        </select></td>
                    <td width="220" nowrap="nowrap">从<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> 到 <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
                    <td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
                    <td aling="right"><div class="location"><strong>当前位置：</strong><a href="order.php">订单管理</a>  <? echo $locationmsg;?></div></td>
                </tr>
            </form>
        </table>
    </div>

    <div class="line2"></div>

    <div class="bline">
    <br class="clearfloat" />
    <div class="border_line">
        <div class="line font14">订单号：<span class="font14h"><? echo $oinfo['OrderSN'];?> <? if($oinfo['OrderType']=="M") echo "(管理员代下单)"; elseif($oinfo['OrderType']=="S") echo "(客情官代下单)";?></span>
            <? if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on"){?>&nbsp;&nbsp;&nbsp;&nbsp;   初审状态：<span class="red"><? if($oinfo['OrderSaler']=="T") echo '已初核'; else echo '未初审';?></span><? }?>
            &nbsp;&nbsp;&nbsp;&nbsp;   订单状态：<span class="font14h"><? echo $order_status_arr[$oinfo['OrderStatus']];?></span></div>
        <div class="rightdiv">下单时间：<? echo date("Y-m-d H:i",$oinfo['OrderDate']);?></div>
    </div>

    <br class="clearfloat" />
    <div class="border_line">
        <form method="post" id="order_base_fm" action="do_order.php?m=save_order_base">
        <input type="hidden" name="OrderID" value="<?php echo $in['ID']; ?>" />
        <div class="line font14">订单信息：</div>
        <div class="line bgw">
            <div class="line22 font12">客户信息</div>
            <div class="line22"><strong>经 销 商：</strong><a href="client_content.php?ID=<? echo $cinfo['ClientID'];?>" target="_blank"><? echo $cinfo['ClientCompanyName'];?>（<? echo $cinfo['ClientName'];?>）</a></div>
            <div class="line45"><strong>联 系 人：</strong><? echo $cinfo['ClientTrueName'];?></div>
            <div class="line45"><strong>联系电话：</strong><? echo $cinfo['ClientPhone'].','.$cinfo['ClientMobile'];?></div>

        </div>
        <br class="clearfloat" />
        <div class="line bgw">
            <div class="line22 font12">收货信息</div>
            <div class="line45"><strong>收货人/公司：</strong>
                <input type="text" name="OrderReceiveCompany" value="<?php echo $oinfo['OrderReceiveCompany']; ?>" style="width:300px;" />
            </div>
            <div class="line45"><strong>联 系 人：&nbsp;</strong>
                <input type="text" name="OrderReceiveName" value="<?php echo $oinfo['OrderReceiveName'];?>" style="width:300px;" />
                </div>
            <div class="line45"><strong>联&nbsp;系&nbsp;电&nbsp;话&nbsp;：&nbsp;</strong>
                <input type="text" name="OrderReceivePhone" value="<? echo $oinfo['OrderReceivePhone'];?>" style="width:300px;" />
            </div>
            <div class="line45"><strong>收货地址：</strong>
                <input type="text" name="OrderReceiveAdd" value="<? echo $oinfo['OrderReceiveAdd'];?>" style="width:300px;" />
            </div>
        </div>
        <br class="clearfloat" />
        <div class="line bgw">
            <div class="line22 font12">支付及配送方式</div>
            <div class="line45"><strong>配送方式：</strong>
                <select name="OrderSendType" style="height:auto;line-height:auto;margin:0px;">
                    <?php foreach($senttypearr as $key=>$val): ?>
                        <option value="<?php echo $key; ?>" <?php if($oinfo['OrderSendType']==$key){echo "selected='selected'";} ?> ><?php echo $val; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="line45"><strong>配送状态：</strong><span class="font12h"><? echo $send_status_arr[$oinfo['OrderSendStatus']];?></span>&nbsp;&nbsp;&nbsp;&nbsp; <?php
                if($oinfo['OrderSendStatus'] == 1 || $oinfo['OrderSendStatus']==3)
                {
                    $paytypeidarr = array('1','2','3','7');

                    if($oinfo['OrderPayStatus'] < 2 && in_array($oinfo['OrderPayType'],$paytypeidarr))
                    {

                    }else{
                        echo '<a href="consignment_add.php?ID='.$oinfo['OrderID'].'" class="buttonb"> &#8250; 添加发货单 </a>';
                    }
                }
                ?>

            </div>
            <div class="line45"><strong>支付方式：</strong>
                <select name="OrderPayType" style="height:auto;line-height:auto;margin:0px;">
                    <?php foreach($paytypearr as $key=>$val): ?>
                    	<?php if($key < 9){ ?>
                        <option value="<?php echo $key; ?>" <?php if($oinfo['OrderPayType']==$key){echo "selected='selected'";} ?> ><?php echo $val; ?></option>
                        <? }?>
                        <?php if(($key >= 9)&&($oinfo['OrderPayType']==$key)){ ?>
                        <option value="<?php echo $key; ?>" selected='selected'	><?php echo $val; ?></option>
                        <? }?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="line45"><strong>支付状态：</strong><span class="font12h"><? echo $pay_status_arr[$oinfo['OrderPayStatus']];?> <? if($oinfo['OrderPayStatus']=="3") echo '&nbsp;&nbsp;¥ '.$oinfo['OrderIntegral'].'';?></span>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                if($oinfo['OrderStatus']!="8" && $oinfo['OrderStatus']!="9"){
                    if($oinfo['OrderPayStatus']=="0" || $oinfo['OrderPayStatus']=="3") echo '<a href="finance_add.php?oid='.$oinfo['OrderID'].'" class="buttonb"> &#8250; 添加收款单 </a>';
                }
                ?>
            </div>
        </div>
        <?php
        if($oinfo['InvoiceType'] == "P" || $oinfo['InvoiceType'] == "Z"){
            $sql_i = "select InvoiceID,InvoiceType,AccountName,BankName,BankAccount,InvoiceHeader,InvocieContent,TaxpayerNumber,InvoiceDate,InvoiceFlag,InvoiceSendDate from ".DATATABLE."_order_invoice where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID=".$oinfo['OrderID']." order by InvoiceID desc";
            $invoice	= $db->get_row($sql_i);
            ?>
            <br class="clearfloat" />
            <div class="line bgw">
                <div class="line22 font12">开票信息</div>
                <div class="line45">
                    <strong>开票类型：</strong><span class="font12h"><? if($oinfo['InvoiceType'] == "Z") echo '增值税发票'; else echo '普通发票'; ?></span><br />
                    <strong>开票抬头：</strong><?php echo $invoice['InvoiceHeader'];?><br />
                    <strong>开票内容：</strong><?php echo $invoice['InvocieContent'];?><br />
                    <strong>开票状态：</strong>
							<span id="show_invoice_div"><?php if($invoice['InvoiceFlag'] == 'T'){ echo '<font color="green">已开票 时间：'.date("Y-m-d",$invoice['InvoiceSendDate'])."</font>"; }else{ echo '<font color=red>未开票</font>';?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="已开票" class="bluebtn" name="invoicebtn" id="invoicebtn" onclick="set_invoice('<?php echo $invoice['InvoiceID'];?>');" />
                                <?php }?>
							</span>
                </div>
                <div class="line45">
                    <?php
                    if($oinfo['InvoiceType'] == "Z"){
                        ?>
                        <strong>纳税人识别号：</strong><?php echo $invoice['TaxpayerNumber']; ?><br />
                        <strong>开户名称：</strong><?php echo $invoice['AccountName']; ?><br />
                        <strong>开户银行：</strong><?php echo $invoice['BankName']; ?><br />
                        <strong>银行帐号：</strong><?php echo $invoice['BankAccount']; ?>
                    <?php }?>
                </div>
            </div>
        <?php }?>
        <?php
        if(!empty($oinfo['DeliveryDate']) && $oinfo['DeliveryDate'] != '0000-00-00'){
            ?>
            <br class="clearfloat" />
            <div class="line bgw">
                <div class="line22 font12" style="height:40px; line-height:40px;">交货时间：<input type="text" name="DeliveryDate" value="<?php echo $oinfo['DeliveryDate'] ?>" /></div>
            </div>
        <?php }?>
        <br class="clearfloat" />
        <div class="line bgw">
            <div class="line22 font12">特殊要求说明：</div>
            <div class="line22">
                <textarea name="OrderRemark" style="width: 864px; height: 88px;"><? echo nl2br($oinfo['OrderRemark']);?></textarea>

            </div>
        </div>
        <a id="list" name="list"></a>
            <br/>
            <div style="margin-bottom:15px;margin-left:15px;">
                <input type="submit" class="redbtn" id="save_base_btn" value="保存修改"  />
                <input type="button" class="bluebtn" value="返回订单管理" onclick="window.location.href = 'order_manager.php?ID=<?php echo $in['ID']; ?>';"  />
            </div>

        </form>
        <script type="text/javascript">
            $(function(){
                $("#order_base_fm").submit(function(){
                    var fm = $(this);
                    $.blockUI({ message : '<p>处理中,请稍后...</p>'});
                    $.post(fm.attr('action'),fm.serialize(),function(json){
                        if(json.status==1){
                            $.unblockUI();
                            window.location.href = "order_manager.php?ID=<?php echo $in['ID'] ?>";
                        }else{
                            $.blockUI({ message : '<p>操作失败请重试!</p>'});
                            setTimeout(function(){
                                $.unblockUI();
                            },300);
                        }
                    },'json');
                    return false;
                });
            });

        </script>
    </div>
    </div>
    <br class="clearfloat" />
    <div class="line">&nbsp;</div>
    </div>
    </div>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm6">
        <div class="windowHeader">
            <h3 id="windowtitle">查看未发货订单商品</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent"> </div>
    </div>

    <div id="old_show_list"><script type="text/javascript">$("#old_show_list").html($("#show_list").html());</script></div>
    </body>
    </html>
<?php
function OrderStatus($ostatus,$oid,$sstatus,$pstatus)
{
    $ext = "";

    if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['order']['pope_audit'] != 'Y')
    {
        $disablemsg1 = 'disabled="disabled"';
        $classmsg1	 = 'darkbtn';
    }else{
        $disablemsg1 = '';
        $classmsg1	 = 'redbtn';
    }

    if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['consignment']['pope_audit'] != 'Y')
    {
        $disablemsg2 = 'disabled="disabled"';
        $disablemsg4 = 'disabled="disabled"';
        $classmsg2	 = 'darkbtn';
        $classmsg4	 = 'darkbtn';
    }else{
        if($sstatus == "2"){
            $disablemsg2 = 'disabled="disabled"';
            $disablemsg4 = '';
            $classmsg2    = 'darkbtn';
            $classmsg4    = 'greenbtn';
        }elseif($sstatus == "3"){
            $disablemsg2 = 'disabled="disabled"';
            $disablemsg4 = 'disabled="disabled"';
            $classmsg2    = 'darkbtn';
            $classmsg4    = 'darkbtn';
        }elseif($sstatus == "4"){
            $disablemsg2 = 'disabled="disabled"';
            $disablemsg4 = 'disabled="disabled"';
            $classmsg2    = 'darkbtn';
            $classmsg4    = 'darkbtn';
        }else{
            $disablemsg2 = '';
            $disablemsg4 = 'disabled="disabled"';
            $classmsg2    = 'greenbtn';
            $classmsg4    = 'darkbtn';
        }
    }

    if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up']['finance']['pope_audit'] != 'Y')
    {
        $disablemsg3 = 'disabled="disabled"';
        $classmsg3	  = 'darkbtn';
    }else{
        if($pstatus == "2")
        {
            $disablemsg3 = 'disabled="disabled"';
            $classmsg3	 = 'darkbtn';
        }else{
            $classmsg3	 = 'bluebtn';
            $disablemsg3 = '';
        }
    }

    if($_SESSION['uinfo']['userflag']=="9" || ($_SESSION['up']['order']['pope_audit'] == 'Y' && $_SESSION['up']['consignment']['pope_audit'] == 'Y' && $_SESSION['up']['finance']['pope_audit'] == 'Y'))
    {
        $classmsg6	  = 'redbtn';
        $disablemsg6 = '';
    }else{
        $classmsg6	  = 'darkbtn';
        $disablemsg6 = 'disabled="disabled"';
    }
    if($_SESSION['uinfo']['userflag']=="9")
    {
        $classmsg7	  = 'redbtn';
        $disablemsg7 = '';
    }else{
        $classmsg7	  = 'darkbtn';
        $disablemsg7 = 'disabled="disabled"';
    }

    switch($ostatus)
    {
        case 0:
        {
            $ext = '
					<input type="button" value="审核订单" class="'.$classmsg1.'" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'Audit\',\''.$oid.'\')" '.$disablemsg1.'  />&nbsp;&nbsp;
					<input type="button" value="取消订单" class="'.$classmsg1.'" name="confirmbtn0" id="confirmbtn0"  onclick="do_order_status(\'Cancel\',\''.$oid.'\')" '.$disablemsg1.'  />';
            break;
        }
        case 1:
        {
            $ext = '
						<input type="button" value="反审核订单" class="'.$classmsg1.'" name="confirmbtn1" id="confirmbtn1" onclick="do_order_status(\'UnAudit\',\''.$oid.'\')" '.$disablemsg1.' />&nbsp;&nbsp;
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick="do_order_status(\'Send\',\''.$oid.'\')" '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick="do_order_status(\'Incept\',\''.$oid.'\')" '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3"  onclick="do_order_status(\'Pay\',\''.$oid.'\')" '.$disablemsg3.' />&nbsp;&nbsp;
						';
            break;
        }
        case 2:
        {
            $ext = '
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick="do_order_status(\'Send\',\''.$oid.'\')" '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick="do_order_status(\'Incept\',\''.$oid.'\')" '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3" onclick="do_order_status(\'Pay\',\''.$oid.'\')" '.$disablemsg3.' />&nbsp;&nbsp;
						<input type="button" value="已完结" class="'.$classmsg6.'" name="confirmbtn6" id="confirmbtn6" onclick="do_order_status(\'Over\',\''.$oid.'\')" '.$disablemsg6.' />';
            break;
        }
        case 3:
        {
            $ext = '
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick="do_order_status(\'Send\',\''.$oid.'\')" '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick="do_order_status(\'Incept\',\''.$oid.'\')" '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="'.$classmsg3.'" name="confirmbtn3" id="confirmbtn3" onclick="do_order_status(\'Pay\',\''.$oid.'\')" '.$disablemsg3.' />&nbsp;&nbsp;
						<input type="button" value="已完结" class="'.$classmsg6.'" name="confirmbtn6" id="confirmbtn6" onclick="do_order_status(\'Over\',\''.$oid.'\')" '.$disablemsg6.' />';
            break;
        }
        case 5:
        {
            $ext = '
						<input type="button" value="已发货" class="'.$classmsg2.'" name="confirmbtn2" id="confirmbtn2"  onclick="do_order_status(\'Send\',\''.$oid.'\')" '.$disablemsg2.'  />&nbsp;&nbsp;
						<input type="button" value="已收货" class="'.$classmsg4.'" name="confirmbtn4" id="confirmbtn4"  onclick="do_order_status(\'Incept\',\''.$oid.'\')" '.$disablemsg4.' />&nbsp;&nbsp;
						<input type="button" value="已到帐" class="darkbtn" name="confirmbtn3" id="confirmbtn3"  onclick="do_order_status(\'Pay\',\''.$oid.'\')" disabled="disabled" />&nbsp;&nbsp;
						<input type="button" value="已完结" class="'.$classmsg6.'" name="confirmbtn6" id="confirmbtn6" onclick="do_order_status(\'Over\',\''.$oid.'\')" '.$disablemsg6.' />';
            break;
        }
        case 8:
        {
            $ext = '<input type="button" value="删除订单" class="'.$classmsg7.'" name="confirmbtn7" id="confirmbtn7" onclick="do_order_status(\'Delete\',\''.$oid.'\')" '.$disablemsg7.'  />&nbsp;&nbsp;
						';
            break;
        }
        case 9:
        {
            $ext = '<input type="button" value="删除订单" class="'.$classmsg7.'" name="confirmbtn7" id="confirmbtn7" onclick="do_order_status(\'Delete\',\''.$oid.'\')" '.$disablemsg7.'  />&nbsp;&nbsp;
						';
            break;
        }
        default:
            $ext = "&nbsp;&nbsp;";
            break;
    }
    return $ext;
}



//应收款
function get_client_money($db,$cid)
{
    $cid       =  intval($cid);
    $sqlunion  = " and FinanceClient = ".$cid." ";
    $statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".DATATABLE."_order_finance where FinanceCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
    $statdata2 = $db->get_row($statsql2);

    $sqlunion  = " and ClientID = ".$cid." ";
    $statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".DATATABLE."_order_expense where CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and FlagID = '2' ";
    $statdata4 = $db->get_row($statsql4);

    $sqlunion  = " and OrderUserID   = ".$cid." ";
//     $statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 ";
    $statsqlt  = "SELECT sum(OrderTotal) as Ftotal from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
    $statdatat = $db->get_row($statsqlt);

    $sqlunion   = " and ReturnClient  = ".$cid." ";
    $statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['uinfo']['ucompany']." ".$sqlunion." and (ReturnStatus=3 or ReturnStatus=5) ";
    $statdata1  = $db->get_row($statsqlt1);

    $begintotal = $statdatat['Ftotal'] - $statdata2['Ftotal'] - $statdata1['Ftotal'] - $statdata4['Ftotal'];

    return $begintotal;
}
?>