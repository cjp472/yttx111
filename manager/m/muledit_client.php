<?php 
include_once ("header.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo SITE_NAME;?> - 管理平台</title>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script language="javascript">
<!--
function returnNodeID()
{
	if(document.MainForm.mulType.value == '') {
		alert("请先选择您要操作的对象!");
		return false;
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
		//console.log($("#MainForm"));
		$.post("do_client.php?m=muledit_save", $("#MainForm").serialize(),
			function(data){
				if(data.status == "ok"){
					$.blockUI({ message: "<p>"+data.info+"</p>" });
					alert(data.info);
					parent.window.location.reload();
				}else{
					$.blockUI({ message: "<p>"+data.info+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			},"json"		
		);
	}
}

function set_input(sid)
{
	document.getElementById("show_input_Audit").style.display = 'none';
	document.getElementById("show_input_Area").style.display = 'none';
	document.getElementById("show_input_Catalog").style.display = 'none';
	document.getElementById("show_input_Level").style.display = 'none';
	document.getElementById("show_input_Price").style.display = 'none';
	document.getElementById("show_input_Consignment").style.display = 'none';
	document.getElementById("show_input_Finance").style.display = 'none';
	document.getElementById("show_input_Sms").style.display = 'none';

	if(sid != '')
	{
		document.getElementById("show_input_"+sid).style.display = 'block';
	}
}
//-->
</script>

<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p,span,select{color:#333333; font-size:14px; line-height:180%; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; }
input{border:#cbcbcd solid 1px; height:24px; font-size:12px; line-height:150%; color:#333333; VERTICAL-ALIGN: middle; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif; width:80%;}
select{line-height:20px; height:24px; margin:4px; padding:2px; width:80%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.redbtn {
     width:auto; padding:1px 8px; background:#ff9f36;  color: #FFF;  border:#ff9f36 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
 }

.bluebtn {
   width:auto; padding:1px 8px; background:#47c4d7;  color: #FFF;  border:#47c4d7 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}


#show_sort_sel{width:100%; height:120px; overflow:auto;}
#show_sort_sel div{width:105px; height:24px; line-height:24px; float:left; margin:2px;overflow:hidden;}
#show_sort_sel div input{ width:15px; height:15px; VERTICAL-ALIGN: middle;  }

.bottomline td{border-bottom:#CCCCCC dotted 1px; height:auto; padding:4px 0; line-height:28px;}
.bottonline input{border:0;}
-->
</style>
</head>

<body >
<table width="96%" border="0" cellspacing="4" align="center">
  <form name="MainForm" id="MainForm" enctype="multipart/form-data" method="post" action="" onsubmit="return false;">
	<input type="hidden" name="SelectClient" id="SelectClient" value="<? echo $in['s'];?>"  />
    <tr> 
      <td colspan="2" align="left" height="34">
	  	<strong>请选择操作对象:</strong> 
      </td>
    </tr>

    <tr> 
      <td colspan="2" align="left" height="34">
		<select name="mulType" id="mulType" style="padding:2px; line-height:22px;"  onChange="javascript:set_input(this.options[this.selectedIndex].value);" >
			<option value="">⊙ 请选择操作对象</option>
			<option value="Audit">┠- 审核药店</option>
			<option value="Area">┠- 所在地区</option>
			<option value="Saler">┠- 选择客情官</option>
			<option value="Catalog">┠- 屏蔽商品分类</option>
			<option value="Level">┠- 药店级别</option>
			<option value="Price">┠- 执行价格/折扣</option>
			<option value="Consignment">┠- 常用货运公司</option>
			<option value="Finance">┠- 常用付款方式</option>
			<option value="Sms">┠- 短信告知药店帐号密码</option>
		</select>
	 </td>
    </tr>
    <tr> 
      <td colspan="2" align="left" height="30" >
		<strong>操作对像的值:</strong> 
	 </td>
    </tr>
    <tr> 
      <td colspan="2" align="left"  id="set_input_line">

	<div id="show_input_Audit" style="display:none;">
		<input name="ClientAudit" type="checkbox" id="ClientAudit" value="ok" style="width:18px; height:18px;" /> &nbsp; <font color=red>审核通过请打勾</font>
	</div>

	<div id="show_input_Area" style="display:none;">
		  <select name="ClientArea" id="ClientArea">
			<option value="0">⊙ 请选择药店所在地区</option>
			<?php 
			$sortarr = $db->get_results("SELECT AreaID,AreaParentID,AreaName,AreaPinyi FROM ".DATATABLE."_order_area where AreaCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY AreaID ASC ");
			echo ShowTreeMenu($sortarr,0,$in['aid'],1);
			?>
		  </select>
	</div>
	<div id="show_input_Saler" style="display:none;">
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
	</div>
	<div id="show_input_Catalog" style="display:none;">
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
	</div>
	<div id="show_input_Level" style="display:none;">
				  <?php
					$valuearr = get_set_arr('clientlevel');
					if(!empty($valuearr))
					{
						$levelarr = $valuearr;
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
	</div>

	<div id="show_input_Price" style="display:none;">
			<table width="98%" border="0" cellspacing="2" cellpadding="0"  >
                <tr>
                  <td width="22%" bgcolor="#F0F0F0"><div align="right">默认价格/折扣：</div></td>
                  <td width="46%" bgcolor="#F0F0F0"><label>
                    <select name="ClientSetPrice" id="ClientSetPrice" style="width:100px;">
                      <option value="Price1">价格1</option>
                      <option value="Price2">价格2</option>
                    </select>
                    </label></td>
                  <td bgcolor="#F0F0F0"><input name="ClientPercent" type="text" id="ClientPercent" value="10"  maxlength="4" onfocus="this.select();"  onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" style="width:160px;"  /></td>
              </tr>

                <tr>
                  <td ><div align="right">按品牌执行折扣：</div></td>
                  <td colspan="2">
				  
				  <div style="width:100%; height:140px; overflow:auto;" id="showuserbrand">
				    <table width="98%" border="0" cellspacing="1" cellpadding="0" class="bottonline" >
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
                      <tr >
                        <td height="24">&nbsp;<?php echo $bv['BrandNO'];?></td>
						<td >&nbsp;<?php echo $bv['BrandName'];?></td>
                        <td ><input name="BrandPercent_<?php echo $bv['BrandID'];?>" type="text" id="BrandPercent_<?php echo $bv['BrandID'];?>" value=""  maxlength="4" onfocus="this.select();" style="width:98%; border:#cbcbcd solid 1px;"  /></td>
                      </tr>
                      <?php }?>
                  </table>
                  <div>	
				  
				  </td>
	           </tr>	
			</table>
	</div>


	<div id="show_input_Consignment" style="display:none;">
			<div style="width:100%; height:180px; overflow:scroll;" id="showuserorder">
				    <table width="98%" border="0" cellspacing="1" cellpadding="0" class="bottonline">
                      <tr id="selected_line_0" style="height:28px;background-color:#efefef;">
                        <td width="8%">&nbsp;</td>
						<td width="10%"><strong>&nbsp;行号</strong></td>
                        <td ><strong>&nbsp;公司名称</strong></td>
                      </tr>
					  <?
					  $logisticsarr = $db->get_results("SELECT LogisticsID,LogisticsCompany,LogisticsName,LogisticsContact FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY LogisticsID ASC Limit 0,100");
					  $n=1;
					  foreach($logisticsarr as $olvar)
					  {
					  ?>
                      <tr height="24" id="selected_line_<? echo $olvar['LogisticsID'];?>"  >
                        <td class="selectinput"><input id="ClientConsignment_<? echo $olvar['LogisticsID'];?>" name="ClientConsignment[]" type="checkbox"  value="<? echo $olvar['LogisticsID'];?>"  /></td>
                        <td >&nbsp;<? echo $n++;?></td>
                        <td >&nbsp;<? echo $olvar['LogisticsName'];?></td>
                      </tr>
                      <? }?>
                  </table>
              </div>	
	</div>

	<div id="show_input_Finance" style="display:none;">
				<div style="width:100%; height:180px; overflow:scroll;" id="showuserorder">
				    <table width="98%" border="0" cellspacing="1" cellpadding="0" class="bottonline">
                      <tr id="selected2_line_0" style="height:28px;background-color:#efefef;">
                        <td width="8%">&nbsp;</td>
						<td width="10%"><strong>&nbsp;行号</strong></td>
                        <td ><strong>&nbsp;付款方式</strong></td>
                      </tr>
					  <?
					  $typedata = $db->get_results("SELECT TypeID,TypeName,TypeAbout FROM ".DATABASEU.DATATABLE."_order_paytype ORDER BY TypeID ASC Limit 0,50");
					  foreach($typedata as $tvar)
					  {		  
					  ?>
                      <tr height="24" id="selected2_line_<? echo $tvar['TypeID'];?>" >
                        <td class="selectinput"><input id="ClientPay_<? echo $tvar['TypeID'];?>" name="ClientPay[]" type="checkbox"  value="<? echo $tvar['TypeID'];?>"  /></td>
                        <td >&nbsp;<? echo $tvar['TypeID'];?></td>
                        <td >&nbsp;<? echo $tvar['TypeName']." -- ".$tvar['TypeAbout']."";?></td>
                      </tr>
                      <? }?>
                  </table>
             </div>	
	</div>

	<div id="show_input_Sms" style="display:none;">
		<input type="checkbox" name="sendsmsuser" id="sendsmsuser" value="1" style="width:18px; height:18px;" />&nbsp;&nbsp; 通过短信告知药店帐号密码。
	</div>

	 </td>
    </tr>
	<tr>
      <td align="left"> <input name="Submit1" type="button" class="redbtn" onclick="returnNodeID();" value=" 提 交 "> 
      &nbsp;&nbsp;<input name="Submit2" type="button" class="bluebtn" onclick='parent.closewindowui();' value=" 取 消 "></td>
    </tr>
  </form>
</table>


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
				$repeatMsg = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $layer-1);
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