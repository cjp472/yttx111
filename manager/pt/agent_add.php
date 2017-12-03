<?php 
include_once ("header.php");
$menu_flag = "manager";
if(!empty($in['ID'])){
	$InfoData = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_agent where AgentID=".intval($in['ID'])." limit 0,1");
}
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

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		$("#data_AgentBegin").datepicker();
		$("#data_AgentEnd").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
		 <input type="hidden" name="AgentID" id="AgentID" value="<? if(!empty($InfoData['AgentID'])) echo $InfoData['AgentID']; ?>" />
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="agent.php">代理商管理</a> &#8250;&#8250; <a href="agent_add.php">新增代理商</a></div>
   	        </div>
            
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_agent();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">		
			<legend>基本资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                                
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td width="55%"><label>
                  <select name="data_AgentArea" id="data_AgentArea" class="select2">
                    <option value="0">⊙ 请选择客户所在城市</option>
                    <?
					$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city ORDER BY  AreaID ASC ");
					echo ShowTreeMenu($sortarr,0,$InfoData['AgentArea'],1);
					?>
                  </select>
                    <span class="red">*</span></label></td>
                  <td width="29%">[<a href="area.php">新增地区</a>]</td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">代理商类型：</div></td>
                  <td><label>
                  <select name="data_AgentType" id="data_AgentType" class="select2">
                    >
                    <?
					foreach($agenttypearr as $k=>$v){
						if($InfoData['CompanyType'] == $k){
							echo '<option value="'.$k.'" selected="selected" >&nbsp;&nbsp;'.$v.'</option>';
						}else{
							echo '<option value="'.$k.'">┠-'.$v.'</option>';
						}
					}
					?>
                  </select>
                    <span class="red">*</span></label></td>
                  <td ></td>
                </tr>

                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">代理商名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <input type="text" name="data_AgentName" id="data_AgentName" value="<? if(!empty($InfoData['AgentName'])) echo $InfoData['AgentName']; ?>" />
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AgentContact" id="data_AgentContact" value="<? if(!empty($InfoData['AgentContact'])) echo $InfoData['AgentContact']; ?>" />
                  <span class="red">*</span></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">联系方式：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AgentPhone" id="data_AgentPhone" onfocus="this.select();"   value="<? if(!empty($InfoData['AgentPhone'])) echo $InfoData['AgentPhone']; ?>"  />
                  </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AgentAddress" id="data_AgentAddress" value="<? if(!empty($InfoData['AgentAddress'])) echo $InfoData['AgentAddress']; ?>" /></td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">Q Q：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AgentQQ" id="data_AgentQQ" value="<? if(!empty($InfoData['AgentQQ'])) echo $InfoData['AgentQQ']; ?>" /></td>
                  <td bgcolor="#FFFFFF">&nbsp;        </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AgentEmail" id="data_AgentEmail" value="<? if(!empty($InfoData['AgentEmail'])) echo $InfoData['AgentEmail']; ?>" />&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">合同时间：</div></td>
                  <td bgcolor="#FFFFFF">起&nbsp;&nbsp;<input type="text" name="data_AgentBegin" id="data_AgentBegin" value="<? if(!empty($InfoData['AgentBegin'])) echo $InfoData['AgentBegin']; ?>" style="width:100px;" />&nbsp;&nbsp;&nbsp;&nbsp;
				  止&nbsp;&nbsp;<input type="text" name="data_AgentEnd" id="data_AgentEnd" value="<? if(!empty($InfoData['AgentEnd'])) echo $InfoData['AgentEnd']; ?>" style="width:100px;" />
				  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">预付款金额：</div></td>
                  <td bgcolor="#FFFFFF"><input type="text" name="data_AgentMoney" id="data_AgentMoney" value="<? if(!empty($InfoData['AgentMoney'])) echo $InfoData['AgentMoney']; ?>" />&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>



                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>
                  <td bgcolor="#FFFFFF"><textarea name="data_CompanyRemark" rows="5"  id="data_AgentRemark"><? if(!empty($InfoData['AgentRemark'])) echo $InfoData['AgentRemark']; ?></textarea>
                  &nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
              </table>
			</fieldset>

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="savecompanyid" type="button" class="button_1" id="savecompanyid" value="保 存" onclick="do_save_agent();" />
			<input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		  </div>
            
        	</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />
    </div>
    
<?php include_once ("bottom.php");?>
	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠-";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-2);
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
