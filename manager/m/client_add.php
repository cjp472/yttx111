<?php 
$menu_flag = "client";
$pope	   = "pope_form";
include_once ("header.php");

if(empty($in['aid'])) $in['aid'] = 0;

if($is_erp = erp_is_run($db,$_SESSION['uinfo']['ucompany'])) {
    exit('新增药店档案,请重ERP通过接口同步至DHB!谢谢!' . "<a href='javascript:;' onclick='history.back()'>返回</a>");
}

$productarr  = get_set_arr('product');
$price1_name = $productarr['product_price']['price1_name'] ? $productarr['product_price']['price1_name'] : "价格一";
$price2_name = $productarr['product_price']['price2_name'] ? $productarr['product_price']['price2_name'] : "价格二";

$company_id = $_SESSION['uc']['CompanyID'];
$cs_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");
$company_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company WHERE CompanyID=" . $company_id);

//注释了20免费版未上传资质不能添加药店的限制 by wanjun @20160229
// $client_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DATABASEU.DATATABLE."_order_dealers WHERE ClientCompany={$company_id} LIMIT 1");
// if(in_array($cs_info['CS_Flag'],array('W','F')) && $client_cnt >= 1) {
//     if($company_info['CompanySigned']) {
//         $to_url = "company_upload.php?sy=c";
//     } else {
//         $to_url = "company_edit.php?sy=c";
//     }
//     echo "温馨提示！您需要完善详细资料才能正式开通订货平台供客户订货!";
//     echo "<script>" , "setTimeout(function(){window.location.href='{$to_url}';},710);","</script>";
//     exit;
// }

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

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="client.php">药店管理</a> &#8250;&#8250; <a href="client_add.php">新增药店</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_client();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">		
			<legend>登陆信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆帐号：</div></td>
                  <td width="55%"><div style="border:1px solid #cccccc; height:24px; width:90%; padding:2px; float:left;">
                    <input name="ClientName" type="text" id="ClientName" value=""  maxlength="32" style="width:70%; border:0;" />
                   </div>&nbsp;<span class="red">*</span></td>
                  <td width="29%">可以是数字、字母、下划线</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登陆密码：</div></td>
                  <td><input name="ClientPassword" type="text" id="ClientPassword" value="123456"  maxlength="32" /></td>
                  <td>默认初始密码为123456，可以是数字、字母、下划线</td> 
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手机号码：</div></td>
                  <td><input name="ClientMobile" type="text" id="ClientMobile" value=""  maxlength="11" />
                  </td>
                  <td title="启用后，即可用帐号登陆也可以用手机号码登陆"><input type="checkbox" name="loginmobile" id="loginmobile" value="on" checked="checked" style="width:18px; height:18px;" />&nbsp;&nbsp;启用手机号码登录</td>
                </tr>
            </table>
           </fieldset>  
            
            <br style="clear:both;" />
            <fieldset title="“*”为必填项！" class="fieldsetstyle">
			  <legend>药店基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店所在地区：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                  <select name="ClientArea" id="ClientArea" class="select2" style="width:567px;">
                    <option value="0">⊙ 请选择药店所在地区</option>
                    <? 
					$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaPinyi,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['aid'],1);
					?>
                  </select>
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >若无地区，请先点击添加&nbsp;<a href="client_area.php">[添加地区]</a></td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="ClientCompanyName" onblur="check_client_name(this,null);" id="ClientCompanyName" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">
                     <!-- 公司名或店名-->
                      <span style="display:none;color:red;" id="client_name_unique">
                          药店名称已存在!
                      </span>
                  </td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">药店编号：</div></td>
                  <td  bgcolor="#FFFFFF"><label>
                    <input type="text" name="ClientNO" id="ClientNO" onblur="check_client_no(this,null);" /></label></td>
                  <td  bgcolor="#FFFFFF">
                      <span style="display:none;color:red;" id="client_no_unique">
                          药店编号已存在!
                      </span>
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientTrueName" id="ClientTrueName" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系方式：</div></td>
                  <td bgcolor="#FFFFFF"><div style="width:280px; float:left;">&nbsp;&nbsp;电话：<input type="text" name="ClientPhone" id="ClientPhone" style="width:200px;" /></div>
                  <div style="width:288px; float:left; text-align:right;">传真：<input type="text" name="ClientFax" id="ClientFax" style="width:200px;" /></div></td>
                  <td bgcolor="#FFFFFF">&nbsp;  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">电子邮箱：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientEmail" id="ClientEmail" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;  </td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">地 址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientAdd" id="ClientAdd" />&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="ClientAbout" rows="5"  id="ClientAbout"></textarea>
                  &nbsp;</td>
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
                  <td width="55%" bgcolor="#FFFFFF">
                    <input type="text" name="AccountName" id="AccountName" value="" /></td>
                  <td width="29%" bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">开户银行：</div></td>
                  <td  bgcolor="#FFFFFF">
                    <input type="text" name="BankName" id="BankName" value="" /></td>
                  <td  bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">银行帐号：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="BankAccount" id="BankAccount" value="" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">开票抬头：</div></td>
                  <td bgcolor="#FFFFFF"><input name="InvoiceHeader" id="InvoiceHeader" type="text"  value=""  /></td>
                  <td bgcolor="#FFFFFF">&nbsp; </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">纳税人识别号：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="TaxpayerNumber" id="TaxpayerNumber" value=""   /></td>
                  <td bgcolor="#FFFFFF">&nbsp;  </td>
                </tr>
              </table>
      </fieldset>
      

			<br style="clear:both;" />
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
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
                                    <option value="<?php echo $val['UserID']; ?>" ><?php echo $val['UserTrueName']; ?></option>
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
					if(!empty($topsort))
					{
						foreach($topsort as $v)
						{
								echo '<div title="'.$v['SiteName'].'"><input id="ClientSortID_'.$v['SiteID'].'" name="ClientSortID[]" type="checkbox" value="'.$v['SiteID'].'" />'.$v['SiteName'].'</div>';
						}
					}					
					?>
					</div>
                    </td>					
                   <td valign="top" >  
				  <br />&nbsp;选择您要屏蔽的商品分类<br />（屏蔽单个商品请在商品管理处操作）</td>
                </tr>
             <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店级别：</div></td>
                  <td  colspan="2">				                    
				  <?php
					$valuearr = get_set_arr('clientlevel');
					if(!empty($valuearr))
					{
						if(count($valuearr, COUNT_RECURSIVE)==count($valuearr))
						{
							$levelarr['A'] = $valuearr;
							$levelarr['A']['id']    = "A";
							$levelarr['A']['name']  = "方式A";
							$levelarr['isdefault']  = "A";
						}else{
							$levelarr = $valuearr;
						}

						foreach($levelarr as $lkey=>$lvar)
						{
							if($lkey=="isdefault") continue;
							echo '<div style="clear:both; width:90%; height:34px;"><select name="ClientLevel_'.$lkey.'" id="ClientLevel_'.$lkey.'" style="width:220px; ">';
							echo '<option value=""> ⊙ 请选择药店级别</option>';
							foreach($lvar as $key=>$var)
							{
								if($key=="id" || $key=="name") continue;
								echo '<option value="'.$key.'">'.substr($key,6).'、'.$var.'</option>';
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
                      <option value="Price1"><?php echo $price1_name; ?></option>
                      <option value="Price2"><?php echo $price2_name; ?></option>
                    </select>
                    <span class="red">*</span></label></td>
                  <td ><input name="ClientPercent" type="text" id="ClientPercent" value="10"  maxlength="4" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" style="width:200px;"  /></td>
                  <td width="29%">默认为 10 无折扣（如：9.2折 就填 9.2）</td>
              </tr>
			  
			  <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">最小订单金额：</div></td>
                  <td ><input name="OrderAmount"  onkeyup="this.value=this.value.replace(/\D/g,'')"  onafterpaste="this.value=this.value.replace(/\D/g,'')"  type="text" id="OrderAmount" value="0"  style="width:180px;"  />（元）</td>
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
					  $branddata = $db->get_results("SELECT BrandID,BrandNO,BrandName FROM ".DATATABLE."_order_brand where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY BrandID ASC Limit 0,100");
					  $n=1;
					  foreach($branddata as $bv)
					  {						  
					  ?>
                      <tr onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                        <td height="28">&nbsp;<?php echo $bv['BrandNO'];?></td>
						<td >&nbsp;<?php echo $bv['BrandName'];?></td>
                        <td ><input name="BrandPercent_<?php echo $bv['BrandID'];?>" type="text" id="BrandPercent_<?php echo $bv['BrandID'];?>" value=""  maxlength="4" onfocus="this.select();" style="width:98%; border:#cbcbcd solid 1px;"  /></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>

				  </td>
                  <td valign="top"><br />品牌折扣优先于默认折扣，<br />未设置折扣的品牌按默认折扣执行<br />
				  <font color=red>注：特价商品不执行折扣！</font>
				  </td>
              </tr>	

          </table>


            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">常用货运公司：</div></td>
                  <td >
				  
				  <div style="width:100%; height:200px; overflow:scroll;" id="showuserorder">
				    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#ccc;">
                        <td width="8%">&nbsp;</td>
						<td width="14%"><strong>&nbsp;行号</strong></td>
                        <td width="38%"><strong>&nbsp;公司名称</strong></td>
                        <td ><strong>&nbsp;地址</strong></td>
                      </tr>
					  <?

					  $logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsContact,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsPinyi asc, LogisticsID ASC Limit 0,500");
					  $n=1;
					  foreach($logisticsarr as $olvar)
					  {
					  ?>
                      <tr height="28" id="selected_line_<? echo $olvar['LogisticsID'];?>"  >
                        <td class="selectinput"><input id="ClientConsignment_<? echo $olvar['LogisticsID'];?>" name="ClientConsignment[]" type="checkbox" onfocus="selectorderlinefocus('<? echo $olvar['LogisticsID'];?>')" value="<? echo $olvar['LogisticsID'];?>"  /></td>
                        <td onclick="selectorderline('<? echo $olvar['LogisticsID'];?>')" >&nbsp;<? echo $n++;?></td>
                        <td onclick="selectorderline('<? echo $olvar['LogisticsID'];?>')">&nbsp;<? echo $olvar['LogisticsName'];?></td>
                        <td onclick="selectorderline('<? echo $olvar['LogisticsID'];?>')">&nbsp;<? echo $olvar['LogisticsAddress'];?></td>
                      </tr>
                      <? }?>
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
				  
				  <div style="width:100%; height:200px; overflow:scroll;" id="showuserorder">
				    <table width="96%" border="0" cellspacing="0" cellpadding="0" class="bottonline">
                      <tr id="selected2_line_0" style="height:28px;background-color:#ccc;">
                        <td width="8%">&nbsp;</td>
						<td width="14%"><strong>&nbsp;行号</strong></td>
                        <td ><strong>&nbsp;付款方式</strong></td>
                      </tr>
					  <?
					  $typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype where TypeClose=0 ORDER BY TypeID DESC Limit 0,50");
					  foreach($typedata as $tvar)
					  {		  
					  ?>
                      <tr height="28" id="selected2_line_<? echo $tvar['TypeID'];?>" >
                        <td class="selectinput"><input id="ClientPay_<? echo $tvar['TypeID'];?>" name="ClientPay[]" type="checkbox" onfocus="selectorder2linefocus('<? echo $tvar['TypeID'];?>')" value="<? echo $tvar['TypeID'];?>"  /></td>
                        <td onclick="selectorder2line('<? echo $tvar['TypeID'];?>')" >&nbsp;<? echo $tvar['TypeID'];?></td>
                        <td onclick="selectorder2line('<? echo $tvar['TypeID'];?>')">&nbsp;<? echo $tvar['TypeName']." -- ".$tvar['TypeAbout']."";?></td>
                      </tr>
                      <? }?>
                  </table>
                  <div>	
				  
				  </td>
                  <td width="30%" valign="top"><br />&nbsp;限定该药店支持的付款方式<br />（不设置默认支持所有的付款方式）</td>
              </tr>
          </table>
          </fieldset>
		  
			<br style="clear:both;" />
    		<fieldset class="fieldsetstyle">
			<legend>短信通知</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                  <td >
					<input type="checkbox" name="sendsmsuser" id="sendsmsuser" value="1" checked="checked" style="width:18px; height:18px;" />&nbsp;&nbsp; 通过短信告知药店帐号密码。</td>
                  <td width="30%">注：请在基本资料里 填写药店手机号</td>
              </tr>
          </table>
          </fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_client();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backproductid" type="button" class="button_3" id="backproductid" value="返 回" onclick="history.go(-1);" />
		</div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    

	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?
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