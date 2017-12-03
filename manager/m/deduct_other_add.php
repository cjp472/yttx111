<?php 
$menu_flag = "saler";
$pope		   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

 	$sqlmsg = " and DeductType = 'R' ";
	if(empty($in['sid']))
	{
		$in['sid'] = '';
		$sidmsg    = '';
	}else{
		$sqlmsg .=" and DeductUser = ".intval($in['sid'])." ";
		$sidmsg  = '&sid='.$in['sid'];
	}
	if(!empty($in['tid']))
	{
		$sqlmsg .= " and DeductStatus = '".$in['tid']."' ";
	}else{
		$in['tid'] = '';
	}

setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/saler.js?v=f<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker();
		$("#edate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
   <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" action="" >

		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>&nbsp;&nbsp;当前位置：</strong><a href="saler.php">客情官</a> &#8250;&#8250; <a href="得多deduct_add.php">新增业务提成</a></div>
   	        </div>            
			
            <div class="rightdiv sublink" style="padding-right:20px; ">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_deduct();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
			</div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset title="“*”为必填项！" class="fieldsetstyle">
			<legend>提成信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">          
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客情官：</div></td>
                  <td width="55%"><label>
                    <select name="data_DeductUser" id="data_DeductUser" class="select2" style="width:555px;" onChange="javascript: get_clientselect(this.options[this.selectedIndex].value);" >
					<option value="0" >⊙ 请选择客情官</option>
					<?php 
					$n = 0;
					$clientdata = $db->get_results("select UserID,UserName,UserTrueName from ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." and UserType='S'  order by UserID asc");
					foreach($clientdata as $areavar)
					{
						$n++;
						if($in['sid'] == $areavar['UserID']) $smsg = 'selected="selected"'; else $smsg ="";
						echo '<option value="'.$areavar['UserID'].'" '.$smsg.' title="'.$areavar['UserTrueName'].'"  > '.$areavar['UserName'].' - '.$areavar['UserTrueName'].'</option>';
						$salerarr[$areavar['UserID']] = $areavar['UserTrueName'];
					}
					?>
                  </select>
                    <span class="red">*</span></label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">药店：</div></td>
                  <td>
				  <select name="data_ClientID" id="data_ClientID" class="select2" style="width:555px;" >
                    <option value="0">⊙ 请选择客户（药店）</option>
                    <? 
					$orderintoarr = $db->get_results("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0  ORDER BY ClientCompanyPinyi ASC ");
					if(!empty($oinfo['OrderUserID'])) $scid = $oinfo['OrderUserID']; else $scid = $orderintoarr[0]['ClientID'];
					foreach($orderintoarr as $orderinfovar)
					{
						if($scid == $orderinfovar['ClientID']) $smsg = 'selected="selected"'; else $smsg='';
						echo '<option value="'.$orderinfovar['ClientID'].'" title="'.$orderinfovar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$orderinfovar['ClientCompanyPinyi']).'" '.$smsg.' >'.substr($orderinfovar['ClientCompanyPinyi'],0,1).' - '.$orderinfovar['ClientCompanyName'].'</option>';
					}
					?>
                  </select>
                  <span class="red">*</span></td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">相关订单：</div></td>
                  <td><input type="text" name="data_OrderSN" id="data_OrderSN" /></td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">提成金额：</div></td>
                  <td><input type="text" name="data_DeductTotal" id="data_DeductTotal" /> <span class="red">*</span></td>
                  <td></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">备注说明：</div></td>
                  <td><textarea name="data_Remark" rows="3"  id="data_Remark"></textarea></td>
                  <td></td>
                </tr>

            </table>
           </fieldset>
           
		  <br style="clear:both;" />

          <div class="rightdiv sublink" style="padding-right:20px;">
			<input name="saveclientid" type="button" class="button_1" id="saveclientid" value="保 存" onclick="do_save_deduct();" />
			<input name="resetproductid" type="reset" class="button_3" id="resetproductid" value="重 置" />
			<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		 </div>
            
         </div>
         <INPUT TYPE="hidden" name="referer" value ="" >
         </form>
        <br style="clear:both;" />
    </div>



<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  

    <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">订单提成明细</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>

</body>
</html>