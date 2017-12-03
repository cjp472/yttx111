<?php 
$menu_flag = "client";
$pope	   = "pope_view";
include_once ("header.php");

$ucid = $_SESSION['uinfo']['ucompany'];

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$clientinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_client where ClientCompany=".$ucid." and ClientID=".intval($in['ID'])." limit 0,1");
	$in['aid']  = $clientinfo['ClientArea'];

	$dealersinfo = $db->get_row("SELECT ClientID,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount FROM ".DATABASEU.DATATABLE."_order_dealers  where ClientCompany=".$ucid." and ClientID=".intval($in['ID'])." limit 0,1");
}
$productarr  = get_set_arr('product');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";
$company_id = $_SESSION['uinfo']['ucompany'];
$saler_id = $db->get_var("SELECT SalerID FROM ".DATATABLE."_order_salerclient WHERE CompanyID={$company_id} AND ClientID=".$clientinfo['ClientID']);
$saler_info = $db->get_row("SELECT UserID,UserName,UserTrueName FROM ".DATABASEU.DATATABLE."_order_user WHERE UserCompany={$company_id} AND UserID=" . $saler_id);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--
#show_sort_sel{width:100%; height:100px; overflow:auto;}
#show_sort_sel div{width:105px; height:24px; line-height:24px; float:left; margin:2px; overflow:hidden;}
#show_sort_sel div input{ width:15px; height:15px; VERTICAL-ALIGN: middle;  }
-->
</style>
</head>

<body>        

<?php include_once ("top.php");?>

    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<input name="data_ClientID" type="hidden" id="data_ClientID" value="<? echo $clientinfo['ClientID'];?>"  />
		
		<?php include_once ("inc/menu_client.php");?>

        <div class="bline" >
			<fieldset  class="fieldsetstyle">		
			<legend>登陆信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                              
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆账号：</div></td>
                  <td width="55%"><label>
                    <? echo $clientinfo['ClientName'];?>
                    </label></td>
                  <td width="29%"></td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset  class="fieldsetstyle">
			  <legend>药店基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
	              <tr>
	                  <td width="16%" bgcolor="#F0F0F0"><div align="right">最近下单时间：</div></td>
	                  <td width="55%" bgcolor="#FFFFFF"><label>
	                    <? if(!empty($clientinfo['lastOrderAt'])) echo date('Y-m-d H:i',$clientinfo['lastOrderAt']);?>
	                  </label></td>
	                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
	                </tr>
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所在地区：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? 
					$sortarrname = $db->get_row("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$ucid." and AreaID=".$clientinfo['ClientArea']." ORDER BY AreaID ASC limit 0,1");
					echo $sortarrname['AreaName'];
					?>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $clientinfo['ClientCompanyName'];?>&nbsp;
                    </label></td>
                  <td width="29%" bgcolor="#FFFFFF">公司名或店名</td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店编号：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $clientinfo['ClientNO'];?>&nbsp;
                    </label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['ClientTrueName'];?>&nbsp;
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['ClientEmail'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['ClientPhone'];?>&nbsp;
                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['ClientFax'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;        </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手 机：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['ClientMobile'];?></td>
                  <td bgcolor="#FFFFFF">（可用于短信通知）</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地 址：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['ClientAdd'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><? echo nl2br($clientinfo['ClientAbout']);?>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>
			
			<br style="clear:both;" />
            <fieldset class="fieldsetstyle">
            <legend>资质效期</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">              
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照有效期：</div></td>
                  <td width="55%" bgcolor="#FFFFFF">  <? echo $clientinfo['BusinessValidity'];?></td>
                  <td width="29%" bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">GSP/GMP有效期：</div></td>
                  <td  bgcolor="#FFFFFF">
                    <? if(!empty($clientinfo['GsmpValidity'])) echo $clientinfo['GsmpValidity'];?></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">医疗机构执业/药品经营许可证有效期：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['LicenceValidity'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
      </fieldset>  


      <br style="clear:both;" />
            <fieldset class="fieldsetstyle">
            <legend>财务信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">              
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">开户名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF">  <? echo $clientinfo['AccountName'];?></td>
                  <td width="29%" bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">开户银行：</div></td>
                  <td  bgcolor="#FFFFFF">
                    <? if(!empty($clientinfo['BankName'])) echo $clientinfo['BankName'];?></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">银行账号：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['BankAccount'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">开票抬头：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['InvoiceHeader'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp; </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">纳税人识别号：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $clientinfo['TaxpayerNumber'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;  </td>
                </tr>
              </table>
      </fieldset>      

			<br style="clear:both;" />
    		<fieldset  class="fieldsetstyle">
			<legend>设置</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">  
             <tr>
                <tr>
                    <td width="16%" bgcolor="#F0F0F0">
                        <div align="right">所属客情官：</div>
                    </td>
                    <td width="30%">
                        <label for="">
                            <?php echo $saler_info['UserTrueName']; ?>
                        </label>
                    </td>
                </tr>
                  <td width="16%" height="100" bgcolor="#F0F0F0"><div align="right">屏蔽商品分类：</div></td>
                  <td width="55%" colspan="2">
				   <div  id="show_sort_sel">
					<?
					$topsort = $db->get_results("SELECT SiteID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and ParentID=0 ORDER BY SiteOrder DESC,SiteID ASC ");
					$csarr = null;
					if(!empty($clientinfo['ClientShield']))
					{
						$csarr = explode(",",$clientinfo['ClientShield']);
					}
					
					if(!empty($topsort))
					{
						foreach($topsort as $v)
						{
								if (@in_array($v['SiteID'], $csarr)) 
								{
									echo '<div><input id="ClientSortID_'.$v['SiteID'].'" name="ClientSortID[]" type="checkbox" value="'.$v['SiteID'].'" checked="checked" disabled="disabled" />'.$v['SiteName'].'</div>';
								}else{
									echo '<div><input id="ClientSortID_'.$v['SiteID'].'" name="ClientSortID[]" type="checkbox" value="'.$v['SiteID'].'" disabled="disabled" />'.$v['SiteName'].'</div>';
								}
						}
					}					
					?>
					</div>
                    </td>					
                   <td >  
				  &nbsp;</td>
                </tr>
				<tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店级别：</div></td>
                  <td colspan="2">
				<?
					$valuearr = get_set_arr('clientlevel');
					if(!empty($valuearr))
					{
						if(count($valuearr, COUNT_RECURSIVE)==count($valuearr))
						{
							$levelarr['A'] = $valuearr;
							$levelarr['A']['id']   = "A";
							$levelarr['A']['name'] = "方式A";
							$levelarr['isdefault'] = "A";
						}else{
							$levelarr = $valuearr;
						}
						if(!empty($clientinfo['ClientLevel']))
						{
							if(strpos($clientinfo['ClientLevel'],","))
							{
								$clientlevelarr1 = explode(",", $clientinfo['ClientLevel']);
								foreach($clientlevelarr1 as $cl)
								{
									if(!empty($levelarr[substr($cl,0,1)]['name'])) echo '<div><strong>'.$levelarr[substr($cl,0,1)]['name'].": </strong> &nbsp;&nbsp;&nbsp;&nbsp;".$levelarr[substr($cl,0,1)][substr($cl,2)].'</div>';
								}
							}else{
								$cl = $clientinfo['ClientLevel'];
								if(substr($cl,0,1)==="l")
								{
									echo '<div><strong>'.$levelarr['A']['name'].": </strong>   &nbsp;&nbsp;&nbsp;&nbsp;".$levelarr['A'][$cl].'</div>';
								}else{
									echo '<div><strong>'.$levelarr[substr($cl,0,1)]['name'].": </strong>&nbsp;&nbsp;&nbsp;&nbsp;  ".$levelarr[substr($cl,0,1)][substr($cl,2)].'</div>';
								}
							}
						}
					}
				?> 
				  </td>
                  <td width="29%"></td>
              </tr>                

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">默认执行价格/折扣：</div></td>
                  <td width="30%"><label>
                    <select name="ClientSetPrice" id="ClientSetPrice" style="width:220px;" disabled="disabled">
                      <?php if($clientinfo['ClientSetPrice'] == "Price2"){?>
                       <option value="Price1"><?php echo $price1_name; ?></option>
                      <option value="Price2" selected="selected"><?php echo $price2_name; ?></option>
                      <?php }else{?>
                      <option value="Price1" selected="selected"><?php echo $price1_name; ?></option>
                      <option value="Price2"><?php echo $price2_name; ?></option>
                      <?php }?>
                    </select>
                    <span class="red">*</span></label></td>
                  <td ><? echo $clientinfo['ClientPercent'];?></td>
                  <td ></td>
              </tr>
			  
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">按品牌执行折扣：</div></td>
                  <td colspan="2">
				  
				  <div style="width:100%; height:200px; overflow:auto;" id="showuserbrand">
				    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="bottonline" >
                      <tr style="height:28px;background-color:#efefef;">
                        <td width="20%"><strong>&nbsp;品牌编号</strong></td>
						<td width="40%"><strong>&nbsp;品牌名称</strong></td>
                        <td width="40%"><strong>&nbsp;执行折扣</strong></td>
                      </tr>
					  <?php
					  if(!empty($clientinfo['ClientBrandPercent'])) $brandpercent = unserialize($clientinfo['ClientBrandPercent']);

					  $branddata = $db->get_results("SELECT BrandID,BrandNO,BrandName FROM ".DATATABLE."_order_brand where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY BrandID ASC Limit 0,100");
					  $n=1;
					  foreach($branddata as $bv)
					  {						  
					  ?>
                      <tr onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                        <td height="28">&nbsp;<?php echo $bv['BrandNO'];?></td>
						<td >&nbsp;<?php echo $bv['BrandName'];?></td>
                        <td ><?php if(!empty($brandpercent[$bv['BrandID']])) echo $brandpercent[$bv['BrandID']];?></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>	
				  
				  </td>
                  <td >品牌折扣优先于默认折扣，<br />未设置折扣的品牌按默认折扣执行</td>
              </tr>
          </table>
          </table>

            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">常用货运公司：</div></td>
                  <td >				  
				  <div style="width:100%; height:200px; overflow:auto;" id="showuserorder">
				    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#efefef;">
                        <td width="8%">&nbsp;</td>
						<td width="14%"><strong>&nbsp;行号</strong></td>
                        <td width="38%"><strong>&nbsp;公司名称</strong></td>
                        <td ><strong>&nbsp;地址</strong></td>
                      </tr>
					  <?php
					  $Foarr = null;
					  if(!empty($clientinfo['ClientConsignment']))
					  {
						$Foarr = explode(',',$clientinfo['ClientConsignment']);
					  }
					  $logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsContact,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsID ASC Limit 0,100");
					  $n=1;
					  foreach($logisticsarr as $olvar)
					  {						  
						  if(@in_array($olvar['LogisticsID'], $Foarr)){ $chmsg = 'checked="checked"'; $bgmsg = 'bgcolor="#efefef"'; }else{ $chmsg = ''; $bgmsg = 'bgcolor="#ffffff"';}
					  ?>
                      <tr height="28" id="selected_line_<? echo $olvar['LogisticsID'];?>" <? echo $bgmsg;?> >
                        <td class="selectinput"><input id="ClientConsignment_<? echo $olvar['LogisticsID'];?>" name="ClientConsignment[]" type="checkbox" disabled="disabled" value="<? echo $olvar['LogisticsID'];?>" <? echo $chmsg;?> /></td>
                        <td >&nbsp;<? echo $n++;?></td>
                        <td >&nbsp;<? echo $olvar['LogisticsName'];?></td>
                        <td >&nbsp;<? echo $olvar['LogisticsAddress'];?></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>	
				  
				  </td>
                  <td width="30%">&nbsp;</td>
              </tr>
          </table>

            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">付款方式：</div></td>
                  <td >
				  
				  <div style="width:101.8%; height:200px; overflow:auto;" id="showuserorder">
				    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#efefef;">
                        <td width="8%">&nbsp;</td>
						<td width="14%"><strong>&nbsp;行号</strong></td>
                        <td ><strong>&nbsp;公司名称</strong></td>
                      </tr>
					  <?php
					  $Foarr = null;
					  if(!empty($clientinfo['ClientPay']))
					  {
						$Foarr = explode(',',$clientinfo['ClientPay']);
					  }
					  $typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 ORDER BY TypeID ASC Limit 0,50");
					  $n=1;
					  foreach($typedata as $tvar)
					  {						  
						  if(@in_array($tvar['TypeID'], $Foarr)){ $chmsg = 'checked="checked"'; $bgmsg = 'bgcolor="#efefef"'; }else{ $chmsg = ''; $bgmsg = 'bgcolor="#ffffff"';}
					  ?>
                      <tr height="28" id="selected2_line_<? echo $tvar['TypeID'];?>" <? echo $bgmsg;?> >
                        <td class="selectinput"><input id="ClientPay_<? echo $tvar['TypeID'];?>" name="ClientPay[]" type="checkbox" disabled="disabled" value="<? echo $tvar['TypeID'];?>" <? echo $chmsg;?> /></td>
                        <td >&nbsp;<? echo $tvar['TypeID'];?></td>
                        <td >&nbsp;<? echo $tvar['TypeName']." -- ".$tvar['TypeAbout']."";?></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>
				  </td>
                  <td width="30%" valign="top"><br />&nbsp;&nbsp;&nbsp;限定该药店支持的付款方式<br />&nbsp;&nbsp;（不设置默认支持所有的付款方式）</td>
              </tr>
          </table>


          </fieldset>

			<br style="clear:both;" />
    		<fieldset  class="fieldsetstyle">
			<legend>登陆</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">     
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">最近一次登陆IP：</div></td>
                  <td ><label>
                      <? echo $dealersinfo['LoginIP'];?>
                    </label></td>
                  <td width="29%">&nbsp; </td>
              </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">最近一次登陆时间：</div></td>
                  <td ><label>
                      <? echo date("Y-m-d",$dealersinfo['LoginDate']);?>
                    </label></td>
                  <td width="29%">&nbsp; </td>
              </tr>
              <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆次数：</div></td>
                  <td ><label>
                      <? echo $dealersinfo['LoginCount'];?>
                    </label></td>
                  <td width="29%">&nbsp; </td>
              </tr>
          </table>
          </fieldset>

            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close();">关闭</a></li></ul></div>
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
            </form>
        <br style="clear:both;" />		
    </div>
    
<?php include_once ("bottom_content.php");?>
</body>
</html>