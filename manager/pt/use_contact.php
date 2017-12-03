<?php
$menu_flag = "common_count";
//define('READ_EXP',true);
include_once ("header.php");
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
    <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
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
    <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
    <div id="searchline">
    	<div class="leftdiv width300">
    	 <div class="locationl"><strong>当前位置：</strong><a href="visit.php">回访记录</a> &#8250;&#8250; <?php if(!empty($in['vid'])) echo "编辑"; else echo "新增";?>记录</div>
        </div>
        
        <div class="rightdiv sublink" style="padding-right:20px; ">
    		<input type="button" class="button_1" id="addcontact" onclick="addvisit('<?php echo $in['vid'];?>');" value="保存"/>
    		<input name="resetcompanyid" type="button" class="button_3" onclick="resets();" id="resetcompanyid" value="重 置" />
    		<input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
		</div>
        
    </div>

    <div class="line2"></div>
        <div class="bline">
         <div id="" style="width:100%; margin:8px;">  
                <?php 
					if(!empty($in['vid'])){
					    $vinfo = $db->get_row("SELECT c.CompanyName,v.* FROM ".DATABASEU.DATATABLE."_order_company_visit v inner join ".DATABASEU.DATATABLE."_order_company c on v.CompanyID=c.CompanyID where v.ID=".$in['vid']." limit 0,1");
					}
				?>
    		   <fieldset title="" class="fieldsetstyle">
    			<legend>新增回访信息</legend>
    			<form method="post" action="do_use.php?m=add_visit" id="visitinfo" target="exe_iframe">
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访公司：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;
                      <?php 
                            if(!empty($vinfo['CompanyID'])){
                                echo $vinfo['CompanyName'];
                            }
                            else {
                      ?>
                        <select id="CompanyID" name="CompanyID"  style="width:555px;" class="select2">
            				<option value="" >⊙ 选择公司</option>
            				<?php 
                                $agentdata = $db->get_results("SELECT CompanyID,CompanyName,CompanyPrefix,CompanyContact,CompanyMobile FROM ".DATABASEU.DATATABLE."_order_company ORDER BY CompanyName ASC ");
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
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访时间：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;
                      <input name="RecordDate" id="RecordDate" value="<?php if(!empty($vinfo['CompanyID'])) echo $vinfo['RecordDate']; else echo date("Y-m-d");?>" style="width:255px"/>&nbsp;
                      <span class="red">*</span></td>
                    </tr>  
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">联系人：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactName" id="ContactName" value="<?php echo $vinfo['ContactName'];?>" />&nbsp;
                      <span class="red">*</span></td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">联系人职务：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactJob" id="ContactJob" value="<?php echo $vinfo['ContactJob'];?>" style="width: 150px;"/>
                        <select id="Job" style="width: 150px;" class="select2"  onchange=" document.getElementById('ContactJob').value = this.options[this.selectedIndex].value">
                            <option value="">⊙ 选择职位</option>
                            <option value='经理'>经理</option>
                            <option value='仓库'>仓库</option>
                            <option value='总经理'>总经理</option>
                            <option value='办公室文员'>办公室文员</option>
                            <option value='财务'>财务</option>
                            <option value='网运部经理'>网运部经理</option>
                            <option value='文员'>文员</option>
                            <option value='助理'>助理</option>
                            <option value='新媒体运营'>新媒体运营</option>
                            <option value='客户服务部主管'>客户服务部主管</option>
                            <option value='总务'>总务</option>
                            <option value='业务主管'>业务主管</option>
                            <option value='销售商务'>销售商务</option>
                            <option value='会计'>会计</option>
                      </select>
                      &nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">联系电话：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactPhone" id="ContactPhone" value="<?php echo $vinfo['ContactPhone'];?>" />&nbsp;
                      <span class="red">*</span></td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">回访简情：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="VisitGeneral" id="VisitGeneral" value="<?php echo $vinfo['VisitGeneral'];?>" />&nbsp;
                      <span class="red">*</span></td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="13%"><div align="right">记录内容：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<textarea name="VisitContent" id="VisitContent"  rows="5"><?php echo $vinfo['VisitContent'];?></textarea></td>
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
                 <input type="button" class="button_1" data="" id="addcontact" onclick="addvisit('<?php echo $vinfo['ID'];?>');" value="保存"/>
                 <input name="resetcompanyid" type="reset" class="button_3" id="resetcompanyid" value="重 置" />
			     <input name="backid" type="button" class="button_3" id="backid" value="返 回" onclick="history.go(-1)" />
              </div>
              </fieldset>  
          <br style="clear:both;" />
       	  </div>
    
        <?php
            $InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_visit o where CompanyID=".$in['id']." ");
            
            $page = new ShowPage;
            $page->PageSize = 10;
            $page->Total = $InfoDataNum['allrow'];
            $page->LinkAry = array("kw"=>$in['kw'],"id"=>$in['id']);
            
            $sql = "SELECT * FROM ".DATABASEU.DATATABLE."_order_company_visit WHERE CompanyID=" . $in['id'] . " ORDER BY CreateDate DESC";
            $list_data = $db->get_results($sql." ".$page->OffSet());
        ?>
        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <td width="5%" class="bottomlinebold">行号</td>
                    <td width="10%" class="bottomlinebold">联系人</td>
                    <td width="13%" class="bottomlinebold">记录时间</td>
                    <td width="13%" class="bottomlinebold">回访人</td>
                    <td width="10%" class="bottomlinebold">操作</td>
                </tr>
                </thead>
                <tbody>
                <?
                $n = 1;
                if(!empty($list_data))
                {

                    if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
                    foreach($list_data as $lsv)
                    {
                ?>
                        <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                            <td ><? echo $n++;?></td>
                            <td ><? echo $lsv['ContactName'];?></td>
                            <td ><? echo $lsv['RecordDate'];?></td>
                            <td ><? echo $lsv['VisitName'];?></td>
                            <td >
                                <a target='_blank' href='use_detail.php?ID=<? echo $lsv['ID']; ?>&CID=<? echo $lsv['CompanyID'];?>'>详情</a>
                            </td>
                        </tr>
                    <? } }else{?>
                    <tr>
                        <td colspan="5" height="30" align="center">暂无该用户的回访信息!</td>
                    </tr>
                <? }?>
                </tbody>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('ty.php');?></td>
                    </tr>
                 </table>
                <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
        

    </div>
     </form>
    <br style="clear:both;" />   
</div>

<? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>