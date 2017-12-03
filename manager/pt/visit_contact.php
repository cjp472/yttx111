<?php
$menu_flag = "common_count";
include_once ("header.php");

if(!empty($in['vid']))
{
    $vinfo = $db->get_row("SELECT c.CompanyName,c.CompanySigned,v.* FROM ".DATABASEU.DATATABLE."_order_company_visit v inner join ".DATABASEU.DATATABLE."_order_company c on v.CompanyID=c.CompanyID where v.ID=".$in['vid']." limit 0,1");
    $companyname = $vinfo['BusinessLicense']."（".$vinfo['CompanyName']."）";
    $companyid = $vinfo['CompanyID'];
    
    $contactname = $vinfo["ContactName"];
    $contactphone = $vinfo["ContactPhone"];
}
if(!empty($in['CID']))
{
    $cinfo = $db->get_row("SELECT c.* FROM ".DATABASEU.DATATABLE."_order_company c where c.CompanyID=".$in['CID']." limit 0,1");
    $companyname = $cinfo['BusinessLicense']."（".$cinfo['CompanyName']."）";
    $companyid = $cinfo['CompanyID'];
    
    $contactname = $cinfo["CompanyContact"];
    $contactphone = $cinfo["CompanyPhone"];
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name='robots' content='noindex,nofollow' />
    <title><? echo SITE_NAME;?> - 管理平台</title>
    <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="css/showpage.css" />
    <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
    
    <script src="../scripts/jquery.min.js" type="text/javascript"></script>
    <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
    <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
    
    <script src="js/function.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <style type="">
        .radio input{width:auto;}
    </style>
    <script>
        $(function(){
            $("#RecordDate").datepicker({changeMonth: true,	changeYear: true});
        });
    </script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

<div id="bodycontent">
    <div class="lineblank"></div>
    <form id="MainForm" name="MainForm" enctype="multipart/form-data" >
    <div id="searchline">
    	<div class="leftdiv width300">
    	 <div class="locationl"><strong>当前位置：</strong><a href="visit.php">回访记录</a> &#8250;&#8250; <?php if(!empty($in['vid']) && empty($in['CID'])) echo "编辑"; else echo "新增";?>记录</div>
        </div>      
        <div class="rightdiv sublink" style="padding-right:20px; ">
    		<input type="button" class="button_1" id="addcontact" cdata="<?php echo $companyid;?>" onclick="addvisit('<?php echo $in['vid'];?>');" value="保存"/>
    		<input name="resetcompanyid" type="button" onclick="resets();" class="button_3" id="resetcompanyid" value="重 置" />
    		<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		</div>        
    </div>

    <div class="line2"></div>
        <div class="bline">
         <div id="" style="width:100%; margin:8px;">  
    		   <fieldset title="" class="fieldsetstyle">
    			<legend><?php if(!empty($in['vid']) && empty($in['CID'])) echo "编辑"; else echo "新增";?>回访信息</legend>
    			<form method="post" id="visitinfo" name="visitinfo" >
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访公司：</div></td>
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
            						if($vinfo['CompanyID'] == $var['CompanyID'])
            						{
            						    $smsg = 'selected="selected"';
            						} 
            						else
            						{
            						    $smsg ="";
            						}
            						
            						echo '<option value="'.$var['CompanyID'].'" '.$smsg.' title="'.$var['CompanyName'].'"  >'.$var['CompanyPrefix'] . '-' . $var['CompanyName'] . ' - '.$var['CompanyContact'].' - ' . $var['CompanyMobile'].'</option>';
            					}
                            ?>
        				</select>
                        &nbsp;<span class="red">*</span>
                      <?php }?>
                      </td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访时间：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;
                      <input name="RecordDate" id="RecordDate" tdata="<?php echo date("Y-m-d");?>" value="<?php if(!empty($vinfo['CompanyID'])) echo $vinfo['RecordDate']; else echo date("Y-m-d");?>" style="width:255px"/>&nbsp;
                      <span class="red">*</span></td>
                    </tr>  
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">联系人：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactName" id="ContactName" value="<?php echo $contactname;?>" />&nbsp;
                      <span class="red">*</span></td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">联系人职务：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactJob" id="ContactJob" value="<?php echo $vinfo['ContactJob'];?>" style="width: 150px;"/>
                        <select class="select2" style="width: 150px;" id="Job" onchange=" document.getElementById('ContactJob').value = this.options[this.selectedIndex].value">
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
                      &nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">联系电话：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactPhone" id="ContactPhone" value="<?php echo $contactphone;?>" />&nbsp;
                      <span class="red">*</span></td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">QQ：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactQQ" id="ContactQQ" value="<?php echo $vinfo['ContactQQ'];?>" />&nbsp;
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">邮箱：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactEmail" id="ContactEmail" value="<?php echo $vinfo['ContactEmail'];?>" />&nbsp;
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访简情：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="VisitGeneral" id="VisitGeneral" value="<?php echo $vinfo['VisitGeneral'];?>" />&nbsp;
                      <span class="red">*</span></td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访内容：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<textarea name="VisitContent" id="VisitContent"  rows="5"><?php echo $vinfo['VisitContent'];?></textarea></td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访方式：</div>
                      <td bgcolor="#FFFFFF" width="55%">
                        <label class="radio"><input type="radio" name="ContactT" id="ContactP" value="Phone" <?php if($vinfo['ContactType']=='Phone') echo "checked='checked'";?>/>电话</label>&nbsp;&nbsp;
                        <label class="radio"><input type="radio" name="ContactT" id="ContactQ" value="QQ" <?php if($vinfo['ContactType']=='QQ') echo "checked='checked'";?>/>QQ</label>&nbsp;&nbsp;
                        <label class="radio"><input type="radio" name="ContactT" id="ContactE" value="Email" <?php if($vinfo['ContactType']=='Email') echo "checked='checked'";?>/>邮件</label>
                      </td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访状态：</div>
                      <td bgcolor="#FFFFFF" width="55%">
                        <label class="radio"><input type="radio" name="UseFlag" id="UseF" value="F" <?php if($vinfo['UseFlag']=='F') echo "checked='checked'";?>/>未用</label>&nbsp;&nbsp;
                        <label class="radio"><input type="radio" name="UseFlag" id="UseT" value="T" <?php if($vinfo['UseFlag']=='T') echo "checked='checked'";?>/>使用</label>&nbsp;&nbsp;
                        <label class="radio"><input type="radio" name="UseFlag" id="UseL" value="L" <?php if($vinfo['UseFlag']=='L') echo "checked='checked'";?>/>失联</label>
                      </td>
                    </tr>
              </table>
              </form>
              <div class="rightdiv sublink" style="padding-right:20px; ">
                 <input type="button" class="button_1" data="" id="addcontact" cdata="<?php echo $companyid;?>" onclick="addvisit('<?php echo $vinfo['ID'];?>');" value="保存"/>
                 <input name="resetcompanyid" type="button" onclick="resets();" class="button_3" id="resetcompanyid" value="重 置" />
			     <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
              </div>
              </fieldset>  
          <br style="clear:both;" />
       	  </div>
    </div>
     </form>
    <br style="clear:both;" />   
</div>

<? include_once ("bottom.php");?>

</body>
</html>