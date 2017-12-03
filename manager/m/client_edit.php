<?php 
$menu_flag = "client";
$pope	       = "pope_form";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$clientinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_client  where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['ID'])." limit 0,1");
	$in['aid'] = $clientinfo['ClientArea'];

	$dealersinfo = $db->get_row("SELECT ClientID,ClientName,ClientPassword,ClientMobile,ClientFlag FROM ".DATABASEU.DATATABLE."_order_dealers  where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['ID'])." limit 0,1");
}
$is_erp = erp_is_run($db,$_SESSION['uinfo']['ucompany']);
$productarr = get_set_arr("product");
$erparr     = get_set_arr('erp');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";
$company_id = $_SESSION['uinfo']['ucompany'];
$saler_id = $db->get_var("SELECT SalerID FROM ".DATATABLE."_order_salerclient WHERE CompanyID={$company_id} AND ClientID=".$clientinfo['ClientID']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
<!--
#show_sort_sel{width:100%; height:120px; overflow:auto;}
#show_sort_sel div{width:105px; height:24px; line-height:24px; float:left; margin:2px;overflow:hidden;}
#show_sort_sel div input{ width:15px; height:15px; VERTICAL-ALIGN: middle;  }
-->
</style>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		<input name="ClientID" type="hidden" id="ClientID" value="<? echo $clientinfo['ClientID'];?>"  />
		
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250; <a href="#">修改药店</a></div>
   	        </div>
			
            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_client();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">

			<legend>登录信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <?php
                if($_SESSION['uinfo']['userflag']!="9" && $_SESSION['up'][$menu_flag]['pope_audit'] != 'Y'){?>
                    <tr style="display:none;">
                        <td bgcolor="#F0F0F0"><div align="right">帐号审核：</div></td>
                        <td>
                            <label>
                                <input name="ClientAudit" type="radio" id="ClientAudit" value="0"  style="width:18px; height:18px;" <? if(empty($dealersinfo['ClientFlag']) || $dealersinfo['ClientFlag']=="0") echo 'checked="checked"';?> /> 已审
                            </label>
                            <label>
                                <input name="ClientAudit" type="radio" id="ClientAudit" value="9"  style="width:18px; height:18px;" <? if($dealersinfo['ClientFlag']=="9") echo 'checked="checked"';?> /> 待审
                            </label>
                            <label>
                                <input name="ClientAudit" type="radio" id="ClientAudit" value="8"  style="width:18px; height:18px;" <? if($dealersinfo['ClientFlag']=="8") echo 'checked="checked"';?> /> 只读
                            </label>

                            <!--<input name="ClientAudit" type="checkbox" id="ClientAudit" value="ok"  style="width:18px; height:18px;" <?/* if(empty($dealersinfo['ClientFlag']) || $dealersinfo['ClientFlag']=="0") echo 'checked="checked"';*/?> /> &nbsp;

                            <font color=red>审核通过请打勾</font>-->
                        </td>
                        <td > &nbsp;</td>
                    </tr>
                <?php
                }else{?>
                    <tr>
                        <td bgcolor="#F0F0F0"><div align="right">帐号审核：</div></td>
                        <td>
                            <label>
                                <input name="ClientAudit" type="radio" id="ClientAudit" value="0"  style="width:18px; height:18px;" <? if(empty($dealersinfo['ClientFlag']) || $dealersinfo['ClientFlag']=="0") echo 'checked="checked"';?> /> 已审
                            </label>
                            <label>
                                <input name="ClientAudit" type="radio" id="ClientAudit" value="9"  style="width:18px; height:18px;" <? if($dealersinfo['ClientFlag']=="9") echo 'checked="checked"';?> /> 待审
                            </label>
                            <label>
                                <input name="ClientAudit" type="radio" id="ClientAudit" value="8"  style="width:18px; height:18px;" <? if($dealersinfo['ClientFlag']=="8") echo 'checked="checked"';?> /> 只读
                            </label>

                            <!--<input name="ClientAudit" type="checkbox" id="ClientAudit" value="ok"  style="width:18px; height:18px;" <?/* if(empty($dealersinfo['ClientFlag']) || $dealersinfo['ClientFlag']=="0") echo 'checked="checked"';*/?> /> &nbsp;

                            <font color=red>审核通过请打勾</font>-->
                        </td>
                        <td > &nbsp;</td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登录帐号：</div></td>
                  <td width="55%"><div style="border:1px solid #cccccc; height:24px; width:90%; padding:2px; float:left;">
                    
                    <!-- 无论是否启用ERP，账号都可以修改
                          <?php
                            if($is_erp){?>
                                <input name="ClientName" type="hidden" id="ClientName" value="<?php echo str_replace($_SESSION['uc']['CompanyPrefix']."-","",$clientinfo['ClientName']); ?>"/>
                                <input type="text" readonly="readonly" disabled="disabled" value="<? echo str_replace($_SESSION['uc']['CompanyPrefix']."-","",$clientinfo['ClientName']);?>"  maxlength="32" style="width:70%; border:0;" />
                            <?php
                            }else{?>
                                <input name="ClientName" type="text" id="ClientName" value="<? echo str_replace($_SESSION['uc']['CompanyPrefix']."-","",$clientinfo['ClientName']);?>"  maxlength="32" style="width:70%; border:0;" />
                            <?php
                            }
                          ?>
                          -->
                          <input name="ClientName" type="text" id="ClientName" value="<? echo str_replace($_SESSION['uc']['CompanyPrefix']."-","",$clientinfo['ClientName']);?>"  maxlength="32" style="width:70%; border:0;" />
                          
                    </div>&nbsp;<span class="red">*</span></td>
                  <td width="29%">可以是数字、字母、下划线</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登录密码：</div></td>
                  <td><input name="ClientPassword" type="text" id="ClientPassword" value=""  maxlength="32"  /></td>
                  <td>为空表示不用修改当前密码，默认初始密码为123456，可以是数字、字母、下划线</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手机号码：</div></td>
                  <td><input name="ClientMobile" type="text" id="ClientMobile" value="<? if(!empty($dealersinfo['ClientMobile'])) echo $dealersinfo['ClientMobile']; elseif(!empty($clientinfo['ClientMobile'])) echo $clientinfo['ClientMobile'];?>"  maxlength="11" />
                  </td>
                  <td  title="启用后，即可用帐号登陆也可以用手机号码登陆"><input type="checkbox" name="loginmobile" id="loginmobile" value="on" style="width:18px; height:18px;" <? if(!empty($dealersinfo['ClientMobile'])) echo 'checked="checked"';?> />&nbsp;&nbsp;启用手机号码登录</td>
                </tr>

            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset class="fieldsetstyle">
		<legend>药店基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店所在地区：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                  <select name="ClientArea" id="ClientArea" class="select2" style="width:90.8%;">
                    <option value="0">⊙ 请选择药店所在地区</option>
                    <? 
					$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaPinyi,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['aid'],1);
					?>
                  </select><span class="red">  *</span>
                  </label></td>
                  <td width="29%" bgcolor="#FFFFFF" >若无地区，请先点击添加&nbsp;<a href="client_area.php">[添加地区]</a></td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="ClientCompanyName" id="ClientCompanyName" onblur="check_client_name(this,<?php echo $clientinfo['ClientID']; ?>)" value="<? echo $clientinfo['ClientCompanyName'];?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">
                      <!--公司名或店名-->
                      <span style="display:none;color:red;" id="client_name_unique">
                          药店名称已存在!
                      </span>
                  </td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">药店编号：</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                          <?php
                          if($is_erp && $clientinfo['ERP'] == 'T'){
                              ?>
                          <input name="ClientNO" type="hidden" id="ClientNO" value="<?php echo $clientinfo['ClientNO']; ?>"/>
                          <input type="text" readonly="readonly" disabled="disabled" value="<? if(!empty($clientinfo['ClientNO'])) echo $clientinfo['ClientNO'];?>" />
                          <?php
                          }else{?>
                              <input type="text" name="ClientNO" id="ClientNO" onblur="check_client_no(this,<?php echo $clientinfo['ClientID']; ?>);" value="<? if(!empty($clientinfo['ClientNO'])) echo $clientinfo['ClientNO'];?>" />
                          <?php
                          }
                          ?>

                      </label>
                    </td>
                  <td  bgcolor="#FFFFFF">
                      <span style="display:none;color:red;" id="client_no_unique">
                          药店编号已存在!
                      </span>
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联 系 人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientTrueName" id="ClientTrueName" value="<? echo $clientinfo['ClientTrueName'];?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系方式：</div></td>
                  <td bgcolor="#FFFFFF"><div style="width:250px; float:left;">&nbsp;&nbsp;电话：<input type="text" name="ClientPhone" id="ClientPhone" value="<? echo $clientinfo['ClientPhone'];?>" style="width:200px;" /></div>
                  <div style="width:254px; float:left; text-align:right;">传真：<input type="text" name="ClientFax" id="ClientFax" value="<? echo $clientinfo['ClientFax'];?>" style="width:200px;"  /></div></td>
                  <td bgcolor="#FFFFFF">&nbsp; </td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">电子邮箱：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientEmail" id="ClientEmail" value="<? echo $clientinfo['ClientEmail'];?>"   /></td>
                  <td bgcolor="#FFFFFF">&nbsp;  </td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地 址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientAdd" id="ClientAdd" value="<? echo $clientinfo['ClientAdd'];?>"  />&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="ClientAbout" rows="3"  id="ClientAbout"><? echo $clientinfo['ClientAbout'];?></textarea>
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
                  <td width="55%" bgcolor="#FFFFFF">
                    <input type="text" name="BusinessValidity" id="BusinessValidity" value="<? echo $clientinfo['BusinessValidity'];?>" /></td>
                  <td width="29%" bgcolor="#FFFFFF">格式：2017-11-06</td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">GSP/GMP有效期：</div></td>
                  <td  bgcolor="#FFFFFF">
                    <input type="text" name="GsmpValidity" id="GsmpValidity" value="<? if(!empty($clientinfo['GsmpValidity'])) echo $clientinfo['GsmpValidity'];?>" /></td>
                  <td  bgcolor="#FFFFFF">格式：2017-11-06</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">医疗机构执业/药品经营许可证有效期：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="LicenceValidity" id="LicenceValidity" value="<? echo $clientinfo['LicenceValidity'];?>" /></td>
                  <td bgcolor="#FFFFFF">格式：2017-11-06</td>
                </tr>
              </table>
      </fieldset>

      <br style="clear:both;" />
            <fieldset class="fieldsetstyle">
            <legend>财务信息</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">              
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">开户名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF">
                    <input type="text" name="AccountName" id="AccountName" value="<? echo $clientinfo['AccountName'];?>" /></td>
                  <td width="29%" bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">开户银行：</div></td>
                  <td  bgcolor="#FFFFFF">
                    <input type="text" name="BankName" id="BankName" value="<? if(!empty($clientinfo['BankName'])) echo $clientinfo['BankName'];?>" /></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">银行帐号：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="BankAccount" id="BankAccount" value="<? echo $clientinfo['BankAccount'];?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">开票抬头：</div></td>
                  <td bgcolor="#FFFFFF"><input name="InvoiceHeader" id="InvoiceHeader" type="text"  value="<? echo $clientinfo['InvoiceHeader'];?>"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp; </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">纳税人识别号：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="TaxpayerNumber" id="TaxpayerNumber" value="<? echo $clientinfo['TaxpayerNumber'];?>"   /></td>
                  <td bgcolor="#FFFFFF">&nbsp;  </td>
                </tr>
              </table>
      </fieldset>

			<br style="clear:both;" />
    		<fieldset  class="fieldsetstyle">
			<legend>设置</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <tr>
                    <td width="16%" bgcolor="#F0F0F0">
                        <div align="right">所属客情官：</div>
                    </td>
                    <td width="30%">
                        <label  >
                            <?php
                            $company_id = $_SESSION['uinfo']['ucompany'];
                            $saler = $db->get_results("SELECT UserID,UserName,UserTrueName FROM ".DATABASEU. DATATABLE."_order_user WHERE UserCompany={$company_id} AND UserType='S'");
                            ?>
                            <select name="saler" style="width:220px;">
                                <option value="">请选择客情官</option>
                                <?php foreach($saler as $val) { ?>
                                    <option value="<?php echo $val['UserID']; ?>" <?php echo $val['UserID'] == $saler_id ? "selected='selected'" : ""; ?> ><?php echo $val['UserTrueName']; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </td>
                </tr>
             <tr>
                  <td width="16%" height="120" bgcolor="#F0F0F0"><div align="right">屏蔽商品分类：</div></td>
                  <td width="55%" colspan="2">
				   <div style="" id="show_sort_sel">
					<?php
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
									echo '<div title="'.$v['SiteName'].'"><input id="ClientSortID_'.$v['SiteID'].'" name="ClientSortID[]" type="checkbox" value="'.$v['SiteID'].'" checked="checked" />'.$v['SiteName'].'</div>';
								}else{
									echo '<div title="'.$v['SiteName'].'"><input id="ClientSortID_'.$v['SiteID'].'" name="ClientSortID[]" type="checkbox" value="'.$v['SiteID'].'" />'.$v['SiteName'].'</div>';
								}
						}
					}					
					?>
					</div>
                    </td>					
                   <td valign="top">  
				  <br />选择您要屏蔽的商品分类<br />（屏蔽单个商品请在商品管理处操作）</td>
                </tr>
			 
			 <tr>
                  <td bgcolor="#F0F0F0"><div align="right">药店级别：</div></td>
                  <td  colspan="2">                  
				  <?php
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
									$clientlevelarr[substr($cl,0,1)] = substr($cl,2);
								}
							}else{
								$cl = $clientinfo['ClientLevel'];
								if(substr($cl,0,1)==="l")
								{
									$clientlevelarr['A'] = $cl;
								}else{
									$clientlevelarr[substr($cl,0,1)] = substr($cl,2);
								}
							}
						}

						foreach($levelarr as $lkey=>$lvar)
						{
							if($lkey=="isdefault") continue;
							echo '<div style="clear:both; width:90%; height:34px;"><select name="ClientLevel_'.$lkey.'" id="ClientLevel_'.$lkey.'" style="width:220px; ">';
							echo '<option value="">⊙ 请选择药店级别</option>';
							foreach($lvar as $key=>$var)
							{
								if($key=="id" || $key=="name") continue;
								if($clientlevelarr[$lkey]==$key)
								{
									echo '<option value="'.$key.'" selected="selected">'.substr($key,6).'、'.$var.'</option>';
								}else{
									echo '<option value="'.$key.'">'.substr($key,6).'、'.$var.'</option>';
								}
							}
							echo '</select>';
							echo '&nbsp;&nbsp;&nbsp;&nbsp;('.$lvar['name'].')';
							echo '</div>';
						}
					}					
					?>
                    </td>					
                   <td ><a href="client_level.php">药店级别设置</a><br />同一药店可设置多个不同的级别，分别应用到不同的商品类型中</td>
                </tr>    

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">默认执行价格/折扣：</div></td>
                  <td width="30%"><label>
                    <select name="ClientSetPrice" id="ClientSetPrice" style="width:220px;">
                      <?php if($clientinfo['ClientSetPrice'] == "Price2"){?>
                       <option value="Price1"><?php echo $price1_name; ?></option>
                      <option value="Price2" selected="selected"><?php echo $price2_name; ?></option>
                      <?php }else{?>
                      <option value="Price1" selected="selected"><?php echo $price1_name; ?></option>
                      <option value="Price2"><?php echo $price2_name; ?></option>
                      <?php }?>
                    </select>
                    <span class="red">*</span></label></td>
                  <td ><input name="ClientPercent" type="text" id="ClientPercent" value="<? echo $clientinfo['ClientPercent'];?>"  maxlength="4" onfocus="this.select();" style="width:180px;"  /></td>
                  <td >默认为 10 无折扣（如：9.2折 就填 9.2）</td>
              </tr>
			  
			  <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">最小订单金额：</div></td>
                  <td ><input name="OrderAmount"    type="text" id="OrderAmount" value="<? echo $clientinfo['OrderAmount'];?>"  style="width:180px;"  />（元）</td>
				  <td width="30%"></td>
                  <td >客户订单的最小金额（如果为0则表示不限制）</td>
              </tr>
			  
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">按品牌执行折扣：</div></td>
                  <td colspan="2">
				  
				  <div style="width:100%; height:200px; overflow:auto;" id="showuserbrand">
				    <table width="98%" border="0" cellspacing="0" cellpadding="0" class="bottonline" >
                      <tr style="height:28px;background-color:#ccc;">
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
                        <td ><input name="BrandPercent_<?php echo $bv['BrandID'];?>" type="text" id="BrandPercent_<?php echo $bv['BrandID'];?>" value="<?php if(!empty($brandpercent[$bv['BrandID']])) echo $brandpercent[$bv['BrandID']];?>"  maxlength="4" onfocus="this.select();" style="width:98%; border:#cbcbcd solid 1px;"  /></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>	
				  
				  </td>
                  <td valign="top"><br />品牌折扣优先于默认折扣，<br />未设置折扣的品牌按默认折扣执行<br />
				  <font color=red>注：特价商品不执行折扣！</font></td>
              </tr>			  
          </table>

            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">常用货运公司：</div></td>
                  <td >				  
				  <div style="width:100%; height:200px; overflow:auto;" id="showuserorder">
				    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#ccc;">
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
					  $logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsContact,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsPinyi asc, LogisticsID ASC Limit 0,500");
					  $n=1;
					  foreach($logisticsarr as $olvar)
					  {						  
						  if(@in_array($olvar['LogisticsID'], $Foarr)){ $chmsg = 'checked="checked"'; $bgmsg = 'bgcolor="#efefef"'; }else{ $chmsg = ''; $bgmsg = 'bgcolor="#ffffff"';}
					  ?>
                      <tr height="28" id="selected_line_<? echo $olvar['LogisticsID'];?>" <? echo $bgmsg;?> >
                        <td class="selectinput"><input id="ClientConsignment_<? echo $olvar['LogisticsID'];?>" name="ClientConsignment[]" type="checkbox" onfocus="selectorderlinefocus('<? echo $olvar['LogisticsID'];?>')" value="<? echo $olvar['LogisticsID'];?>" <? echo $chmsg;?> /></td>
                        <td onclick="selectorderline('<? echo $olvar['LogisticsID'];?>')" >&nbsp;<? echo $n++;?></td>
                        <td onclick="selectorderline('<? echo $olvar['LogisticsID'];?>')">&nbsp;<? echo $olvar['LogisticsName'];?></td>
                        <td onclick="selectorderline('<? echo $olvar['LogisticsID'];?>')">&nbsp;<? echo $olvar['LogisticsAddress'];?></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>	
				  
				  </td>
                  <td width="30%">&nbsp;[<a href="logistics.php">管理物流公司</a>]</td>
              </tr>
          </table>

            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">付款方式：</div></td>
                  <td style="">
				  
				  <div style="width:101.8%; height:200px;overflow:auto;" id="showuserorder">
				    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#ccc;">
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
					  $typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 ORDER BY TypeID DESC Limit 0,50");
					  $n=1;
					  foreach($typedata as $tvar)
					  {						  
						  if(@in_array($tvar['TypeID'], $Foarr)){ $chmsg = 'checked="checked"'; $bgmsg = 'bgcolor="#efefef"'; }else{ $chmsg = ''; $bgmsg = 'bgcolor="#ffffff"';}
					  ?>
                      <tr height="28" id="selected2_line_<? echo $tvar['TypeID'];?>" <? echo $bgmsg;?> >
                        <td class="selectinput"><input id="ClientPay_<? echo $tvar['TypeID'];?>" name="ClientPay[]" type="checkbox" onfocus="selectorder2linefocus('<? echo $tvar['TypeID'];?>')" value="<? echo $tvar['TypeID'];?>" <? echo $chmsg;?> /></td>
                        <td onclick="selectorder2line('<? echo $tvar['TypeID'];?>')" >&nbsp;<? echo $tvar['TypeID'];?></td>
                        <td onclick="selectorder2line('<? echo $tvar['TypeID'];?>')">&nbsp;<? echo $tvar['TypeName']." -- ".$tvar['TypeAbout']."";?></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>
				  </td>
                  <td width="30%" valign="top" ><br />&nbsp;&nbsp;&nbsp;&nbsp;限定该药店支持的付款方式<br />&nbsp;&nbsp;&nbsp;（不设置默认支持所有的付款方式）</td>
              </tr>
          </table>
          </fieldset>

			<br style="clear:both;" />
    		<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>短信通知</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">    
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                  <td >
					<input type="checkbox" name="sendsmsuser" id="sendsmsuser" value="1" style="width:18px; height:18px;" />&nbsp;&nbsp;通过短信告知药店帐号密码。</td>
                  <td width="30%">注：请在基本资料里 填写药店手机号</td>
              </tr>
          </table>
          </fieldset> 

            <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_edit_client();" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";		
		if($var['AreaParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-2);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >┠-".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	
				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>