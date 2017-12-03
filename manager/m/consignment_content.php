<?php 
$menu_flag = "consignment";
$pope	     = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".intval($in['ID'])." limit 0,1");
	$oinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_orderinfo where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and OrderSN='".$cinfo['ConsignmentOrder']."' limit 0,1");

	$cartdata = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,l.ContentNumber,i.Coding,i.Casing,i.Units from ".DATATABLE."_order_cart c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and l.ConsignmentID=".$cinfo['ConsignmentID']." and l.ConType='c' order by i.SiteID asc,c.ID asc");

	$cartdatag = $db->get_results("select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,l.ContentNumber,i.Coding,i.Casing,i.Units from ".DATATABLE."_order_cart_gifts c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and l.ConsignmentID=".$cinfo['ConsignmentID']." and l.ConType='g' order by i.SiteID asc,c.ID asc");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/consignment.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <script type="text/javascript">
        function setSendFlag(cid)
        {
            if(confirm('确定货物已签收吗?'))
            {
                $.blockUI({ message: "<p>正在执行，请稍后...</p>" });
                $.post("do_consignment.php",
                    {m:"setSendFlag", ConsignmentID: cid},
                    function(data){
                        data = Jtrim(data);
                        if(data == "ok"){
                            $.blockUI({ message: "<p>设置成功!</p>" });
                            window.location.reload();
                        }else{
                            $.blockUI({ message: "<p>"+data+"</p>" });
                        }
                    }
                );
                window.setTimeout($.unblockUI, 1000);
            }else{
                return false;
            }
            window.setTimeout($.unblockUI, 1000);
        }
    </script>
</head>

<body>
 <?php include_once ("top.php");?>

    <div id="bodycontent">
    	<div class="lineblank"></div>
      <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="ConsignmentID" id="ConsignmentID" value="<? echo $cinfo['ConsignmentID'];?>" />
		   
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="consignment.php">发货</a> &#8250;&#8250; <a href="#">发货单详细</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px;padding-top: 8px;">
            <ul>
              <li><a href="#" onclick="javascript:window.location.href= 'print.php?u=print_consignment&ID=<? echo $cinfo['ConsignmentID'];?>'">打印发货单</a></li>
              <li><a href="#" onclick="javascript:document.getElementById('exe_iframe').src = 'print_kuaidi.php?ID=<? echo $cinfo['ConsignmentID'];?>'">打印快递单</a></li>
              <li><a href="#" onclick="send_message()">补发短信</a></li>
              <li><a href="#" onclick="window.close();">关 闭</a></li>
            </ul>
            </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset class="fieldsetstyle">			
			<legend>发货信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  >               
                <tr>
                  <td width="12%" bgcolor="#F0F0F0"><div align="right">客户订单：</div></td>
                  <td width="38%"><label>&nbsp; <a href="order_manager.php?SN=<? echo $cinfo['ConsignmentOrder'];?>">
                    <? 	echo $cinfo['ConsignmentOrder'];?>
					</a></label></td>
                  <td width="12%" bgcolor="#F0F0F0"><div align="right">物流公司：</div></td>
                  <td width="38%">&nbsp; 
                    <? 
					if(empty($cinfo['ConsignmentLogistics']) || $cinfo['ConsignmentLogistics']=="0")
					{
						echo '上门自提';
					}else{
						$logisticsarr = $db->get_row("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsPinyi,LogisticsContact,LogisticsPhone FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$cinfo['ConsignmentLogistics']." ORDER BY LogisticsID DESC Limit 0,1");
						echo $logisticsarr['LogisticsName']." (".$logisticsarr['LogisticsPhone'].")";
					}
					?>
                  </td>
                </tr>

                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">运单号：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp;<? echo $cinfo['ConsignmentNO'];?></label></td>
                  <td  bgcolor="#F0F0F0"><div align="right">发货经办人：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp;  <? echo $cinfo['ConsignmentMan'];?></label></td>
                </tr>  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">发货时间：</div></td>
                  <td bgcolor="#FFFFFF"><label>&nbsp; <? echo $cinfo['ConsignmentDate'];?> </label></td>
                  <td  bgcolor="#F0F0F0"><div align="right">运费/付款方式：</div></td>
                  <td bgcolor="#FFFFFF"> &nbsp; <?					
						if($cinfo['ConsignmentMoneyType'] == "1") echo '已付'; else echo '到付';
					?>
                &nbsp;¥ <? echo $cinfo['ConsignmentMoney'];?> 元</td>
                 </td>
                </tr>  
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">备注/说明：</div></td>
                  <td bgcolor="#FFFFFF" colspan="3"><label><? echo nl2br($cinfo['ConsignmentRemark']);?> </label></td>
                </tr> 
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">操作员：</div></td>
                  <td bgcolor="#FFFFFF" ><label>&nbsp; <? echo $cinfo['ConsignmentUser'];?></label></td>
				  <td align="center" colspan="2"></td>
                </tr> 
            </table>
          </fieldset>  

			<br style="clear:both;" />
			<fieldset class="fieldsetstyle">			
			<legend>物流跟踪</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  >                         
                <tr>
                  <td >
<?php
if(!empty($logisticsarr['LogisticsPinyi']) && !empty($cinfo['ConsignmentNO']))
{
	include("arr_kuaidi.php");
	$typeCom = $logisticsarr['LogisticsPinyi'];//快递公司
	$typeNu = $cinfo['ConsignmentNO'];  //快递单号
	$AppKey = KUDAIDIAPPKEY;

	if (in_array($logisticsarr['LogisticsPinyi'], $arr_html_kuaidi))
	{
		$url  = 'http://www.kuaidi100.com/applyurl?key='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'';
	}else{
		$url = 'http://api.kuaidi100.com/api?id='.$AppKey.'&com='.$typeCom.'&nu='.$typeNu.'&show=2&muti=1&order=asc';
	}
	
	//请勿删除变量$powered 的信息，否者本站将不再为你提供快递接口服务。
	$powered = '<div style="display:none;">查询数据由：<a href="http://kuaidi100.com" target="_blank">KuaiDi100.Com （快递100）</a> 网站提供 </div>';

	//优先使用curl模式发送数据
	if (function_exists('curl_init') == 1){
	  $curl = curl_init();
	  curl_setopt ($curl, CURLOPT_URL, $url);
	  curl_setopt ($curl, CURLOPT_HEADER,0);
	  curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt ($curl, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	  curl_setopt ($curl, CURLOPT_TIMEOUT,5);
	  $get_content = curl_exec($curl);
	  curl_close ($curl);
	}else{
	  include("../class/snoopy.php");
	  $snoopy = new Snoopy();
	  $snoopy->referer = 'http://www.google.com/';//伪装来源
	  $snoopy->fetch($url);
	  $get_content = $snoopy->results;
	}
	if (in_array($logisticsarr['LogisticsPinyi'], $arr_html_kuaidi))
	{
		echo '<iframe name="kudiaurl"  frameborder="0" scrolling="no" width="70%" height="350" scrolling="auto" src="'.$get_content.'" ></iframe> ';
	}else{
		print_r($get_content . '<br/>' . $powered);
	}
}else{
	echo '<p ><font color=red>无法获取物流状态! 请确认物流公司编号和运单号正确! </font></p><p ><a href="kuaidi_search.php" target="_blank"  title="快递查询"><img src="img/c1.jpg" alt="快递查询" /></a>&nbsp; &nbsp; &nbsp; &nbsp; <a href="wuliu_search.php" target="_blank"  title="物流查询"><img src="img/c2.jpg" alt="物流查询" /></a></p>';
}
?>
				  
				  </td>
                </tr> 
            </table>
          </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			<legend>发货明细单</legend>
<table width="100%" border="0" cellspacing="1" cellpadding="2" >
  <thead>
  <tr class="bottomline">
    <td width="6%" >&nbsp;行号</td>
	<td width="12%">编号/货号</td>
    <td>&nbsp;商品名称</td>
    <td width="12%">&nbsp;颜色</td>
	<td width="12%">&nbsp;规格</td>
	<td width="12%">&nbsp;包装</td>
    <td width="6%" >数量</td>
    <td width="5%" >单位</td>
  </tr>
   </thead>
   <tbody id="listcartdataid">
	<? 
	$alltotal = 0;
	$allnumber = 0;
	$n=1;
	foreach($cartdata as $ckey=>$cvar)
	{
		$allnumber = $allnumber + $cvar['ContentNumber'];
	?>
   <tr class="bottomline" id="linegoods_<? echo $cvar['ID'];?>"   >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;	<? echo $cvar['Coding'];?></td>
    <td><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<? if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?>  </td>
    <td>&nbsp;<? if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td>&nbsp;<? if(!empty($cvar['Casing'])) echo $cvar['Casing'];?> </td>
    <td >&nbsp;<? echo $cvar['ContentNumber'];?></td>
    <td >&nbsp;<? echo $cvar['Units'];?>	</td>
  </tr>
   <? 
		}
	if(!empty($cartdatag))
	{
			foreach($cartdatag as $ckey=>$cvar)
			{
				$allnumber = $allnumber + $cvar['ContentNumber'];
	?>
   <tr class="bottomline" id="linegoods_g_<? echo $cvar['ID'];?>" bgcolor="#efefef" title="赠品" >
    <td height="30">&nbsp;<? echo $n++;?></td>
	<td >&nbsp;	<? echo $cvar['Coding'];?></td>
    <td><a href="product_content.php?ID=<? echo $cvar['ContentID'];?>" target="_blank"><? echo $cvar['ContentName'];?></a></td>
    <td>&nbsp;<? if(!empty($cvar['ContentColor'])) echo $cvar['ContentColor'];?>  </td>
    <td>&nbsp;<? if(!empty($cvar['ContentSpecification'])) echo $cvar['ContentSpecification'];?> </td>
    <td>&nbsp;<? if(!empty($cvar['Casing'])) echo $cvar['Casing'];?> </td>
    <td >&nbsp;<? echo $cvar['ContentNumber'];?></td>
    <td >&nbsp;<? echo $cvar['Units'];?>	</td>
  </tr>
	<?
			}
	}
	?>

   <tr class="bottomline" id="linegoods_"   >
    <td height="30">合计：</td>
	<td >&nbsp;	</td>
    <td>&nbsp;<strong><? echo $n-1;?>&nbsp;种</strong></td>
    <td>&nbsp;  </td>
    <td>&nbsp; </td>
    <td>&nbsp; </td>
    <td >&nbsp;<strong><? echo $allnumber;?></strong></td>
    <td >&nbsp;</td>
  </tr>
   </tbody>
</table>
		  </fieldset>
		 <br style="clear:both;" />


            <fieldset  class="fieldsetstyle">
		<legend>收货信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" >
                 <tr>
                  <td width="12%" bgcolor="#F0F0F0"><div align="right">收货人：</div></td>
                  <td width="38%" bgcolor="#FFFFFF"><label> &nbsp;<? echo $cinfo['InceptMan'];?></label></td>
				  <td width="12%" bgcolor="#F0F0F0"><div align="right">收货公司：</div></td>
                  <td width="38%" bgcolor="#FFFFFF" >&nbsp;<? echo $cinfo['InceptCompany'];?></td>
                </tr>               

                <tr>
				  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $cinfo['InceptPhone'];?></td>
				  <td bgcolor="#F0F0F0"><div align="right">收货人地址：</div></td>
                  <td bgcolor="#FFFFFF">&nbsp;<? echo $cinfo['InceptAddress'];?></td>
                </tr>
              </table>
		  </fieldset>

			<br style="clear:both;" />

         <div class="rightdiv sublink" style="padding-right:20px;">
         <ul>
             <?php if(empty($cinfo['ConsignmentFlag'])) { ?>
             <li><a href="javascript:;" onclick="setSendFlag(<?php echo $cinfo['ConsignmentID']; ?>);">确认收货</a></li>
             <?php } ?>
          <li><a href="#" onclick="javascript:window.location.href= 'print.php?u=print_consignment&ID=<? echo $cinfo['ConsignmentID'];?>'">打印发货单</a></li>
          <li><a href="#" onclick="javascript:document.getElementById('exe_iframe').src = 'print_kuaidi.php?ID=<? echo $cinfo['ConsignmentID'];?>'">打印快递单</a></li>
          <li><a href="#" onclick="send_message()">补发短信</a></li>
          <li><a href="#" onclick="window.close();">关 闭</a></li>
          </ul>
          </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" id="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
    <div id="windowForm">
    <div class="windowHeader">
      <h3 id="windowtitle">补发短信通知</h3>
      <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent">
    <?php
      $clientinfo = $db->get_row("SELECT ClientID,ClientTrueName,ClientMobile FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientID=".$cinfo['ConsignmentClient']." limit 0,1");
      if(!empty($cinfo['ConsignmentLogistics']) && $cinfo['ConsignmentLogistics'] != '0')
      {
        $loinfo = $db->get_row("SELECT LogisticsName,LogisticsPhone FROM ".DATATABLE."_order_logistics where LogisticsCompany = ".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$cinfo['ConsignmentLogistics']." limit 0,1");
      }
      if(!empty($loinfo['LogisticsName'])) $linkmsg = "由".$loinfo['LogisticsName'].""; else $linkmsg= "";
      $message = "【".$_SESSION['uc']['CompanySigned']."】您订单号:".$cinfo['ConsignmentOrder']."的货物已于".$cinfo['ConsignmentDate']."日<br />".$linkmsg."发出,请注意查收(运单号:<input value='".$cinfo['ConsignmentNO']."' name='sendcno' id='sendcno' style=widht:100px; />),退订回复TD";
      $message2 = "【".$_SESSION['uc']['CompanySigned']."】您订单号:".$cinfo['ConsignmentOrder']."的货物已于".$cinfo['ConsignmentDate']."日".$linkmsg."发出,请注意查收(运单号:{kuaidi_danhao}),退订回复TD";
    ?>
       <form id="sendmessageform" name="sendmessageform" enctype="multipart/form-data" method="post" target="exe_iframe"  action="" >
        <INPUT TYPE="hidden" name="consignmentid" id="consignmentid" value ="<? echo $cinfo['ConsignmentID'];?>" />
       <INPUT TYPE="hidden" name="clientid" id="clientid" value ="<? echo $clientinfo['ClientID'];?>" />
       <INPUT TYPE="hidden" name="sendcontent" id="sendcontent" value ="<? echo $message2;?>" />
       <INPUT TYPE="hidden" name="sendmobile" id="sendmobile" value ="<? echo $clientinfo['ClientMobile'];?>" />
       <div align="left"><strong>短信内容：</strong><br /><?php echo $message;?></div>
       <div align="left"><strong>接收号码：</strong><br /><?php echo $clientinfo['ClientTrueName'].'  '.$clientinfo['ClientMobile'];?></div> 
       <div ><input name="sendmessagebtn" type="button" class="button_3" id="sendmessagebtn" value=" 发 送 " onclick="sendtomessage()" /></div>
       </form>
    </div>
  </div>
</body>
</html>