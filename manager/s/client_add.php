<?php 
$menu_flag = "client";
$pope	   = "pope_form";
include_once ("header.php");

if(empty($in['aid'])) $in['aid'] = 0;
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
#show_sort_sel{width:100%; height:100px; overflow:auto;}
#show_sort_sel div{width:105px; height:24px; line-height:24px; float:left; margin:2px;overflow:hidden;}
#show_sort_sel div input{ width:15px; height:15px; VERTICAL-ALIGN: middle;  }
-->
</style>
</head>

<body>
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>
<!-- <div class="bodyline" style="height:32px;"><div class="leftdiv" style=" margin-top:8px; padding-left:12px;"><span><h4><?php echo $_SESSION['uc']['CompanyName'];?></h4></span><span valign="bottom">&nbsp;&nbsp;<? echo $_SESSION['uinfo']['usertruename']."(".$_SESSION['uinfo']['username'].")";?> 欢迎您！</span>&nbsp;&nbsp;<span>[<a href="change_pass.php">修改密码</a>]</span>&nbsp;&nbsp;<span>[<a href="do_login.php?m=logout">退出</a>]</span></div>
        <div class="rightdiv">
       	  <span class="leftdiv"><img src="img/menu2_left.jpg" /></span>
            <span id="menu2">
            	<ul>
               	  <li class="current2"><a href="client.php">药 店</a></li>
				  <li><a href="client_recycle.php">回 收 站</a></li>
                  <li ><a href="client_log.php" >登录日志</a></li>
             						
                </ul>
            </span>
            <span><img src="img/menu2_right.jpg" /></span>
        </div>
</div>   --> 
    
    	<div class="bodyline" style="height:70px; background-image:url(img/bodyline_bg.jpg);">
   		  <div class="leftdiv"><img src="img/blue_left.jpg" /></div>
                <div class="leftdiv"><h1><? echo $menu_arr[$menu_flag];?></h1></div>
                <div class="rightdiv" style="color:#ffffff; padding-right:20px; padding-top:40px;">此栏目针对药店进行分组，分区管理，帐号管理，添加，删除，修改等操作。</div>
        </div>
        
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
                    <td bgcolor="#F0F0F0"><div align="right">帐号审核：</div></td>
                    <td><input name="ClientAudit" type="checkbox" id="ClientAudit" value="ok"  style="width:18px; height:18px;" <? if(empty($dealersinfo['ClientFlag']) || $dealersinfo['ClientFlag']=="0") echo 'checked="checked"';?> /> &nbsp; <font color=red>审核通过请打勾</font>
                    </td>
                    <td > &nbsp;</td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">登陆帐号：</div></td>
                  <td width="55%"><div style="border:1px solid #cccccc; height:24px; width:90%; padding:2px; float:left;">
                    <font style="font-size:14px; font-weight:bold; padding:2px;"><? echo $_SESSION['uc']['CompanyPrefix']."-"; ?></font><input name="ClientName" type="text" id="ClientName" value=""  maxlength="32" style="width:70%; border:0;" />
                   </div>&nbsp;<span class="red">*</span></td>
                  <td width="29%">可以是数字、字母、下划线</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">登陆密码：</div></td>
                  <td><input name="ClientPassword" type="text" id="ClientPassword" value="<? echo rand(100000,999999);?>"  maxlength="32" />
                  <span class="red">*</span></td>
                  <td>可以是数字、字母、下划线</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手机号码：</div></td>
                  <td><input name="ClientMobile" type="text" id="ClientMobile" value=""  maxlength="11" />
                  </td>
                  <td title="启用后，即可用帐号登陆也可以用手机号码登陆"><input type="checkbox" name="loginmobile" id="loginmobile" value="on" style="width:18px; height:18px;" />&nbsp;&nbsp;启用手机号码登录</td>
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
                  <select name="ClientArea" id="ClientArea" class="select2" style="width:435px;">
                    <option value="0">⊙ 请选择药店所在地区</option>
                    <? 
					$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaPinyi,AreaAbout FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['aid'],1);
					?>
                  </select>
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" >&nbsp;可输入名称首字母快速匹配</td>
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
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="ClientTrueName" id="ClientTrueName" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系方式：</div></td>
                  <td bgcolor="#FFFFFF"><div style="width:220px; float:left;">&nbsp;&nbsp;电话：<input type="text" name="ClientPhone" id="ClientPhone" style="width:150px;" /></div>
                  <div style="width:220px; float:left; text-align:right;">传真：<input type="text" name="ClientFax" id="ClientFax" style="width:150px;" /></div></td>
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
                  <td bgcolor="#FFFFFF"><textarea name="ClientAbout" rows="5"  id="ClientAbout"></textarea></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

			<br style="clear:both;" />
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>设置</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">

             <tr>
                  <td width="16%" height="100" bgcolor="#F0F0F0"><div align="right">屏蔽商品分类：</div></td>
                  <td width="55%" colspan="2">
				   <div style="" id="show_sort_sel">
					<?
					$topsort = $db->get_results("SELECT SiteID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and ParentID=0 ORDER BY SiteOrder DESC,SiteID ASC ");
					if(!empty($topsort))
					{
						foreach($topsort as $v)
						{
								echo '<div><input id="ClientSortID_'.$v['SiteID'].'" name="ClientSortID[]" type="checkbox" value="'.$v['SiteID'].'" />'.$v['SiteName'].'</div>';
						}
					}					
					?>
					</div>
                    </td>					
                   <td >  
				  &nbsp;选择您要屏蔽的商品分类<br />（屏蔽单个商品请在商品管理处操作）</td>
                </tr>
             <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">药店级别：</div></td>
                  <td  colspan="2">
				                    
				  <?
					$valuearr = get_set_arr('clientlevel');
					if(!empty($valuearr))
					{
						if(count($valuearr, COUNT_RECURSIVE)==count($valuearr))
						{
							$levelarr['A'] = $valuearr;
							$levelarr['A']['id']   = "A";
							$levelarr['A']['name']  = "方式A";
							$levelarr['isdefault']   = "A";
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
                   <td >同一药店可设置多个不同的级别，分别应用到不同的商品类型中</td>
              </tr>
			  
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">默认执行价格/折扣：</div></td>
                  <td width="30%"><label>
                    <select name="ClientSetPrice" id="ClientSetPrice" style="width:220px;">
                      <option value="Price1">价格1</option>
                      <option value="Price2">价格2</option>
                    </select>
                    <span class="red">*</span></label></td>
                  <td ><input name="ClientPercent" type="text" id="ClientPercent" value="10"  maxlength="4" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" style="width:200px;"  /></td>
                  <td width="29%">默认为 10 无折扣（如：9.2折 就填 9.2）</td>
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
                      <tr id="selected_line_0">
                        <td width="8%">&nbsp;</td>
						<td width="14%"><strong>&nbsp;行号</strong></td>
                        <td width="38%"><strong>&nbsp;公司名称</strong></td>
                        <td ><strong>&nbsp;地址</strong></td>
                      </tr>
					  <?

					  $logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsContact,LogisticsAddress FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsID ASC Limit 0,100");
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
                      <tr id="selected2_line_0">
                        <td width="8%">&nbsp;</td>
						<td width="14%"><strong>&nbsp;行号</strong></td>
                        <td ><strong>&nbsp;付款方式</strong></td>
                      </tr>
					  <?
					  
					  $typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype where TypeClose = 0 ORDER BY TypeID ASC Limit 0,50");
					  foreach($typedata as $tvar)
					  {		  
					  ?>
                      <tr height="28" id="selected2_line_<? echo $tvar['TypeID'];?>" >
                        <td class="selectinput"><input id="ClientPay_<? echo $tvar['TypeID'];?>" name="ClientPay[]" type="checkbox" onfocus="selectorder2linefocus('<? echo $tvar['TypeID'];?>')" value="<? echo $tvar['TypeID'];?>"  /></td>
                        <td onclick="selectorder2line('<? echo $tvar['TypeID'];?>')" >&nbsp;<? echo $tvar['TypeID'];?></td>
                        <td onclick="selectorder2line('<? echo $tvar['TypeID'];?>')">&nbsp;<? echo $tvar['TypeName']." (".$tvar['TypeAbout'].")";?></td>
                      </tr>
                      <? }?>
                  </table>
                  <div>	
				  
				  </td>
                  <td width="30%">&nbsp;限定该药店支持的付款方式<br />（不设置默认支持所有的付款方式）</td>
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
					<input type="checkbox" name="sendsmsuser" id="sendsmsuser" value="1" style="width:18px; height:18px;" />&nbsp;&nbsp; 通过短信告知药店帐号密码。</td>
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
    
<? include_once ("bottom.php");?>
	
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
				$repeatMsg = str_repeat("--", $layer-1);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>