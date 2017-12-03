<?php 
include_once ("../common.php");
include_once ("./arr_data.php");
$inv = new Input();
$in  = $inv->parse_incoming();
$in  = $inv->_htmlentities($in);
$db  = dbconnect::dataconnect()->getdb();


if($in['m'] == "regiester_save")
{
	$in['data_CompanyPrefix'] = strtolower(trim($in['data_CompanyPrefix']));
	if(!is_number_english($in['data_CompanyPrefix'])) exit('英文简称 只能由英文字母和数字组成!');
	if(strlen($in['data_CompanyPrefix']) < 3 || strlen($in['data_CompanyPrefix']) > 18) exit('请输入3-18位英文简称!');
	if (in_array($in['data_CompanyPrefix'], $prefixarr)) exit('此英文简称已存在，换名再试!');
	if(!is_phone($in['data_CompanyMobile'])) exit('请输入正确的手机号码!');
	if(strlen($in['data_IdentificationNumber']) < 10 || strlen($in['data_IdentificationNumber']) > 20) exit('请输入正确的营业执照号码！');

	$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company_reg where BusinessLicense='".$in['data_BusinessLicense']."' limit 0,1");
	if(!empty($clientinfo['orwname'])) exit('此公司名称已存在，请不要重复提交!');

	$Prefixinfo = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company where CompanyPrefix='".$in['data_CompanyPrefix']."' limit 0,1");
	$Prefixinfo2 = $db->get_row("SELECT count(*) as orwname FROM ".DATABASEU.DATATABLE."_order_company_reg where CompanyPrefix='".$in['data_CompanyPrefix']."' limit 0,1");
	if(!empty($Prefixinfo['orwname']) || !empty($Prefixinfo2['orwname'])) exit('此英文简称已存在，换名再试!<br /> 如果您已经提交过了，请不要重复提交！');
	
	$upsql = "insert into ".DATABASEU.DATATABLE."_order_company_reg(CompanyArea,CompanyIndustry,CompanyName,CompanySigned,CompanyPrefix,CompanyCity,CompanyContact,CompanyMobile,CompanyPhone,CompanyFax,CompanyAddress,CompanyEmail,CompanyWeb,CompanyRemark,CompanyDate,BusinessLicense,IdentificationNumber) values(".intval($in['data_CompanyArea']).",".intval($in['data_CompanyIndustry']).",'".$in['data_CompanyName']."','".$in['data_CompanySigned']."','".$in['data_CompanyPrefix']."','".$in['data_CompanyCity']."','".$in['data_CompanyContact']."','".$in['data_CompanyMobile']."','".$in['data_CompanyPhone']."','".$in['data_CompanyFax']."','".$in['data_CompanyAddress']."','".$in['data_CompanyEmail']."','".$in['data_CompanyWeb']."','".$in['data_CompanyRemark']."',".time().",'".$in['data_BusinessLicense']."','".$in['data_IdentificationNumber']."')";
	$update  = $db->query($upsql);	
	if($update)
	{
		exit("ok");
	}else{
		exit("注册不成功，请联系我们, 电话：400 6311 682, QQ: 1656591743 ");
	}
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
<script  type="text/javascript">

function do_save_regiest()
{
	document.MainForm.referer.value = document.location;

	if($('#data_CompanyArea').val()=="" || $('#data_CompanyArea').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属地区！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);	
	}
	else if($('#data_CompanyIndustry').val()=="" || $('#data_CompanyIndustry').val()=="0")
	{
		$.blockUI({ message: "<p>请选择所属行业！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);	
	
	}
  else if($('#data_BusinessLicense').val()=="")
  {
    $.blockUI({ message: "<p>请输入您的公司名称！</p>" });
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
  }
  else if($('#data_IdentificationNumber').val()=="" )
  {
    $.blockUI({ message: "<p>请输入您的营业执照号！</p>" });
    $('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
  }
	else if($('#data_CompanyName').val()=="")
	{
		$.blockUI({ message: "<p>请输入您的订货系统名称！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);	
	}
	else if($('#data_CompanyContact').val()=="")
	{
		$.blockUI({ message: "<p>请输入联系人！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);	
	}
	else if($('#data_CompanyMobile').val()=="")
	{
		$.blockUI({ message: "<p>请输入您的手机号码！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);	
	
	}
	else if(!isEmail($('#data_CompanyEmail').val()))
	{
		$.blockUI({ message: "<p>请输入正确的邮箱！</p>" });
		$('.blockOverlay').attr('title','点击返回').click($.unblockUI);	
	
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("company_regiest.php?m=regiester_save",$("#MainForm").serialize(),
			function(data){
				if(data == "ok"){
					$.blockUI({ message: "<p>注册成功，我会尽快为您开通正试帐号,发送到您邮箱："+$('#data_CompanyEmail').val()+",请注意查收！</p>" });
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
					window.setTimeout($.unblockUI, 3000);
				}				
			}		
		);
	}

}

function isEmail(str){ 
	var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/; 
	return reg.test(str); 
} 
</script>

</head>

<body>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>正式帐号注册</strong></div>
   	        </div>
            
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_regiest();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			</div>            
        </div>
		
        <div class="line2"></div>
        <div class="bline" >
            <fieldset  class="fieldsetstyle" title="带 “*” 为必填项!">
		    <legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                  <td width="55%"><label>
                  <select name="data_CompanyArea" id="data_CompanyArea"  class="select2">
                    <option value="0">⊙ 请选择客户所在地区</option>
                    <?
					$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city ORDER BY  AreaID ASC ");
					echo ShowTreeMenu($sortarr,0,0,1);
					?>
                  </select>
                    <span class="red">*</span></label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                  <td>
                  <select name="data_CompanyIndustry" id="data_CompanyIndustry"  class="select2">
                    <option value="0">⊙ 请选择客户所属行业</option>
                    <? 
					$industryarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
					foreach($industryarr as $ivar)
					{
						echo '<option value="'.$ivar['IndustryID'].'">┠'.$ivar['IndustryName'].'</option>';
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td></td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_BusinessLicense" id="data_BusinessLicense" value="" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">公司营业执照上的名称</td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_IdentificationNumber" id="data_IdentificationNumber" value="" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">营业执照号</td>
                </tr>  

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">订货系统名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_CompanyName" id="data_CompanyName" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">公司名或店名</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">中文简称：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanySigned" id="data_CompanySigned" maxlength="8" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;用于短信签名（2-8个字）</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户端专属域名：</div></td>
                  <td bgcolor="#FFFFFF" title="客户端专属二级域名" style="font-size:16px;">http://<input type="text" name="data_CompanyPrefix" id="data_CompanyPrefix"  maxlength="10" style="width:100px;" />.dhb.hk
                  &nbsp;&nbsp;&nbsp;&nbsp;<span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">如：http://rsung.dhb.hk/（建义3-10位数字字母组成）</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyCity" id="data_CompanyCity"  /></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyContact" id="data_CompanyContact" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">手机号码：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyMobile" id="data_CompanyMobile" maxlength="11" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;"  />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">用于短信通知，等操作(13************)</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyPhone" id="data_CompanyPhone" />
                  </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyFax" id="data_CompanyFax" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;        </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyAddress" id="data_CompanyAddress" /></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">电子邮箱：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyEmail" id="data_CompanyEmail" />&nbsp; <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">公司网站：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_CompanyWeb" id="data_CompanyWeb" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_CompanyRemark" rows="5"  id="data_CompanyRemark"></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>


          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_regiest();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />

		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
    <div class="bodyline" style="background-image:url(img/bottom_bg.jpg); height:12px;">
        <div class="leftdiv"><img src="img/bottom_left.jpg" /></div>    	
        <div class="rightdiv"><img src="img/bottom_right.jpg" /></div>
	</div>
    
    <div id="copyright"><span class="rightdiv">Powered By Rsung DingHuoBao (<a href="http://www.dhb.hk" target="_blank">WWW.DHB.HK</a>) System © 2006 - 2013 <a href="http://www.rsung.com" target="_blank">Rsung</a> Ltd.</span></div>

<link href="../scripts/select2/select2.min.css" rel="stylesheet" />
<script src="../scripts/select2/select2.min.js"></script>
<script src="../scripts/select2/zh-CN.js"></script>
<script>
        $(function(){
            if($(".select2").length >0){
                $(".select2").select2();
            }
        });
</script>
	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-1);
				if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}

	function is_number_english($str)
	{
		 if(strlen($str) < 3 || strlen($str) > 10 ) return false;
		 return preg_match("/^[A-Za-z0-9]+$/", $str);
	}
?>
