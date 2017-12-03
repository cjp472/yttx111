<?php
$menu_flag = "common_count";
include_once ("header.php");
if(!empty($in['ID'])){
    $vinfo = $db->get_row("SELECT c.CompanyName,v.* FROM ".DATABASEU.DATATABLE."_order_company_contact v inner join ".DATABASEU.DATATABLE."_order_company c on v.CompanyID=c.CompanyID where v.ID=".$in['ID']." limit 0,1");
    $companyname = $vinfo['CompanyName'];
    $companyid = $vinfo['CompanyID'];
}
if(!empty($in['CID']))
{
    $cinfo = $db->get_row("SELECT c.* FROM ".DATABASEU.DATATABLE."_order_company c where c.CompanyID=".$in['CID']." limit 0,1");
    $companyname = $cinfo['CompanyName'];
    $companyid = $cinfo['CompanyID'];
};
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/function.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
    	<div class="leftdiv">
    	 <div class="location"><strong>当前位置：</strong><a href="contacts.php">联系人</a> &#8250;&#8250; <?php if(!empty($in['ID'])) echo "编辑"; else echo "新增";?>联系人</div>
        </div>
			<div class="rightdiv sublink" style="padding-right:20px; ">
    		<input type="button" id="savecontact" class="button_1" name="savecontact" data="<?php echo $in['ID']?>" cdata="<?php echo $companyid;?>" onclick="savecontacts();" value="保存"/>
    		<input name="resetcompanyid" type="button" class="button_3" onclick="resets();" id="resetcompanyid" value="重 置" />
    		<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		</div>
        </div>  	

        <div class="line2"></div>
        <div class="bline">
		   <fieldset title="" class="fieldsetstyle">
			<legend><?php if(!empty($in['ID'])) echo "编辑"; else echo "新增";?>联系人</legend>
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">公司：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;
                      <?php 
                            if(!empty($vinfo['CompanyID']) || !empty($in['CID']))
                            {
                                echo $companyname;
                            }
                            else {
                      ?>
                        <select id="CompanyID" name="CompanyID"  style="width:555px;" class="select2">
            				<option value="" >⊙ 选择公司</option>
            				<?php 	
            					$agentdata = $db->get_results("SELECT c.CompanyID,c.CompanyName,c.CompanyPrefix,c.CompanyContact,c.CompanyMobile FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company WHERE c.CompanyFlag='0' and s.CS_Flag='T'  ORDER BY c.CompanyID DESC ");
            					foreach($agentdata as $var)
            					{
            						if($vinfo['CompanyID'] == $var['CompanyID']) $smsg = 'selected="selected"'; else $smsg ="";
            
            						echo '<option value="'.$var['CompanyID'].'" '.$smsg.' title="'.$var['CompanyName'].'"  >'.$var['CompanyPrefix'] . '-' . $var['CompanyName'] . ' - '.$var['CompanyContact'].' - ' . $var['CompanyMobile'].'</option>';
            					}
                            ?>
        				</select>
                      &nbsp;<span class="red">*</span>
                      <?php }?>
                      </td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">联系人：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactName" id="ContactName" value="<?php echo $vinfo['ContactName']?>" /><span style="color:red;">*</span>&nbsp;</td>
                    </tr>  
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">职     务：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactJob" id="ContactJob" value="<?php echo $vinfo['ContactJob']?>" style="width:150px;"/>&nbsp;&nbsp;&nbsp;
                          <select id="Job" class="select2" style="width: 150px;" onchange=" document.getElementById('ContactJob').value = this.options[this.selectedIndex].value">
                            <option value="">⊙ 选择职位</option>
                            <option value='经理' <?php if($vinfo['ContactJob'] == '经理') echo 'selected="selected"';?>>经理</option>
                            <option value='仓库' <?php if($vinfo['ContactJob'] == '仓库') echo 'selected="selected"';?>>仓库</option>
                            <option value='总经理' <?php if($vinfo['ContactJob'] == '总经理') echo 'selected="selected"';?>>总经理</option>
                            <option value='办公室文员' <?php if($vinfo['ContactJob'] == '办公室文员') echo 'selected="selected"';?>>办公室文员</option>
                            <option value='财务' <?php if($vinfo['ContactJob'] == '财务') echo 'selected="selected"';?>>财务</option>
                            <option value='网运部经理' <?php if($vinfo['ContactJob'] == '网运部经理') echo 'selected="selected"';?>>网运部经理</option>
                            <option value='文员' <?php if($vinfo['ContactJob'] == '文员') echo 'selected="selected"';?>>文员</option>
                            <option value='助理' <?php if($vinfo['ContactJob'] == '助理') echo 'selected="selected"';?>>助理</option>
                            <option value='新媒体运营' <?php if($vinfo['ContactJob'] == '新媒体运营') echo 'selected="selected"';?>>新媒体运营</option>
                            <option value='客户服务部主管' <?php if($vinfo['ContactJob'] == '客户服务部主管') echo 'selected="selected"';?>>客户服务部主管</option>
                            <option value='总务' <?php if($vinfo['ContactJob'] == '总务') echo 'selected="selected"';?>>总务</option>
                            <option value='业务主管' <?php if($vinfo['ContactJob'] == '业务主管') echo 'selected="selected"';?>>业务主管</option>
                            <option value='销售商务' <?php if($vinfo['ContactJob'] == '销售商务') echo 'selected="selected"';?>>销售商务</option>
                            <option value='会计' <?php if($vinfo['ContactJob'] == '会计') echo 'selected="selected"';?>>会计</option>
                          </select>
                      </td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">电     话：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactPhone" id="ContactPhone" value="<?php echo $vinfo['ContactPhone']?>" />&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">手     机：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactMobile" id="ContactMobile" value="<?php echo $vinfo['ContactMobile']?>" />&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">Q  Q：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactQQ" id="ContactQQ" value="<?php echo $vinfo['ContactQQ']?>" />&nbsp;</td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">邮    箱：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactEmail" id="ContactEmail" value="<?php echo $vinfo['ContactEmail']?>" /></td>
                    </tr>
              </table>
              <div class="rightdiv sublink" style="padding-right:20px; ">
        		<input type="button" id="savecontact" class="button_1" name="savecontact" data="<?php echo $vinfo['ID']?>" cdata="<?php echo $companyid;?>" onclick="savecontacts();" value="保存"/>
        		<input name="resetcompanyid" type="button" class="button_3" onclick="resets();" id="resetcompanyid" value="重 置" />
        		<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
    		  </div>
		  </form>
          </fieldset>  
        </div>
        <br style="clear:both;" />
    </div>   
<?php include_once ("bottom.php");?>
 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
         <div id="windowForm6">
		<div class="windowHeader" >
			<h3 id="windowtitle" style="width:540px">时间线</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
                                    正在载入数据...       
        </div>
	</div> 
</body>
</html>