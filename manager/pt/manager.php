<?php 
$menu_flag = "manager";
include_once ("header.php");
$erp_version = include_once("inc/erp_version.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);

if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="css/icon.css"/>
<script type="text/javascript">
$(function() {

	$("#tree").treeview({
		collapsed: true,
		animated: "medium",
		control:"#sidetreecontrol",
		persist: "location"
	});
	$("#bdate").datepicker({changeMonth: true,	changeYear: true});
	$("#edate").datepicker({changeMonth: true,	changeYear: true});
})
				
function show_time_log(cid,cname)
{
	$("#windowtitle").html(cname+' - 时间线');
	$('#windowContent').html('<iframe src="show_time_log.php?ID='+cid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'580px',top:'8%'
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}

function show_pay(cid,cname)
{
	$("#windowtitle").html(cname+' - 在线支付');
	$('#windowContent').html('<iframe src="show_pay.php?ID='+cid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '620px',height:'580px',top:'8%'
            }			
		});
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}
</script>
</head>



<body>
<?php include_once ("top.php");?> 
<?php include_once ("inc/son_menu_bar.php");?>      

    <div id="bodycontent">
    	<div class="lineblank"></div>

    	<div id="searchline">
        	<div class="leftdiv">

        	  <form id="FormSearch" name="FormSearch" method="get" action="manager.php">
        	    <label>
        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
				<label>
				<select id="iid" name="iid"  style="width:160px;" class="select2">
				<option value="" >⊙ 所有行业</option>
				<?php 
					$n = 0;		
					$accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");

					foreach($accarr as $accvar)
					{
						$n++;
						$industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];

						if($in['iid'] == $accvar['IndustryID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$accvar['IndustryID'].'" '.$smsg.' title="'.$accvar['IndustryName'].'"  >'.$accvar['IndustryName'].'</option>';
					}
				?>
				</select>
				</label>
				<label>
				<select id="aid" name="aid"  style="width:135px;" class="select2">
				<option value="" >⊙ 所有地区</option>
				<?php 
					$n = 0;		
					$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city  ORDER BY AreaParent asc,AreaID ASC ");
					foreach($sortarr as $areavar)
					{
						$n++;
						if($areavar['AreaID']==$in['aid']) $areainfoselected = $areavar;
						$areaarr[$areavar['AreaID']] = $areavar['AreaName'];
					}
					echo ShowTreeMenu($sortarr,0,$in['aid'],1);
				?>
				</select>
				</label>

        	    <label>
        	      <select id="date_field" name="date_field"  style="width:85px;" class="select2">
					<option value="end_date" <?php if($in['date_field'] == 'end_date') echo 'selected="selected"';?> >到期时间</option>
					<option value="begin_date" <?php if($in['date_field'] == 'begin_date') echo 'selected="selected"';?> >开通时间</option>
				  </select>
       	        </label>
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

       	        </label>
				<label>

                    <input name="d" type="hidden" value="<?php echo $in['d']; ?>"/>
       	          <input type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>
   	          </form>
   	        </div>
        </div>    	

        <div class="line2"></div>

        <div class="bline">
<?php

	$sqlmsg = '';
	if(empty($in['num'])){
		if(!empty($in['iid']))  $sqlmsg .= " and c.CompanyIndustry=".$in['iid']." "; else $in['iid'] = '';
		if(!empty($in['aid']))
		{
			if(empty($areainfoselected['AreaParent']))
			{
				$sqlmsg .= " and ( c.CompanyArea=".$in['aid']." or c.CompanyArea in (SELECT AreaID FROM ".DATABASEU.DATATABLE."_order_city where  AreaParent=".$in['aid']." ORDER BY AreaID ASC) ) ";
			}else{
				$sqlmsg .= " and c.CompanyArea=".$in['aid']." ";
			}
		}else{
			$in['aid'] = '';
		}
		if(!empty($in['gid']))  $sqlmsg .= " and c.CompanyAgent=".$in['gid']." "; else $in['gid'] = '';
		if($in['date_field'] == 'begin_date'){
			$datefield = 's.CS_BeginDate';
		}else{
			$datefield = 's.CS_EndDate';
		}
		if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
		if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";
		
		//来源
		if(!(empty($in['ctype'])) && $in['ctype'] == 'market'){
		    $sqlmsg .= " and c.CompanyType in('ali','shuan','suning')";
		}elseif(!(empty($in['ctype']))){
		    $sqlmsg .= " and c.CompanyType='".$in['ctype']."' ";
		}

		//if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' ";
        if(!empty($in['kw'])) {
            $sqlmsg .= " AND (";
            $likeArr = array();
            foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail') as $lk) {
                $likeArr[] = " {$lk} like '%".$in['kw']."%'";
            }
            $sqlmsg .= implode(" OR " , $likeArr);
            $sqlmsg .= ")";
        }

		if(empty($sqlmsg)){
			$databasearr1 = $databasearr;
			if(!isset($in['d']) || $in['d'] == '') $in['d'] = array_pop($databasearr1);
			$in['d'] = intval($in['d']); 
			$sqlmsg = " and c.CompanyDatabase=".$in['d']." ";
		}
	}

	//$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0' and s.CS_Flag='T'  ".$sqlmsg."  ");
   
	
	//$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0' ".$sqlmsg." ORDER BY c.CompanyID DESC limit 0,500";
	$datasql1   = "SELECT c.*,s.*,l.SerialNumber,l.Password,l.Status,l.Version,l.TransferCheck,l.RunStatus,l.Develop FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_api_serial as l ON l.CompanyID=c.CompanyID where c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID DESC"; //and s.CS_Flag='T' 
	
	$datasql2   = "SELECT c.*,s.*,l.SerialNumber,l.Password,l.Status,l.Version,l.TransferCheck,l.RunStatus,l.Develop FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_api_serial as l ON l.CompanyID=c.CompanyID where c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
	
	//Notice:以上两个SQL以1为主。2为单独显示天力精算的数据，去除了审核通过的条件 by wanjun @20160418
	$datasql = $in['ctype'] == 'teeny' ? $datasql2 : $datasql1;
	
	$list_data = $db->get_results($datasql);
?>




          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold"><input type="button" name="newbutton" id="newbutton" value=" 新增客户 " class="button_2" onclick="javascript:window.location.href='company_add.php'" />&nbsp;&nbsp;&nbsp;&nbsp;[<a href="manager.php?num=more">MORE</a>]</td>
					 <td align="right"  height="30" class="bottomlinebold">
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="manager.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
					 </td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>
                <tr>
                    <td width="6%" class="bottomlinebold">编号</td>
                    <td class="bottomlinebold">公司名称/系统名称</td>
		  			<td width="15%" class="bottomlinebold">地区/行业</td>
                    <td width="15%" class="bottomlinebold">药店/待审</td>
		  			<td width="10%" class="bottomlinebold">前缀</td>
		  			<td width="8%" class="bottomlinebold">用户/短信数</td>
              		<td width="12%" class="bottomlinebold">联系人/联系方式</td>
             		<td width="8%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 

      		<tbody>

<?php
    
	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{
		/**	
		if($_GET['m'] == 'add'){
        if(!(file_exists (RESOURCE_PATH.$lsv['CompanyID'])))
        {
            _mkdir(RESOURCE_PATH,$lsv['CompanyID']);
            echo RESOURCE_PATH.$lsv['CompanyID'];
        }
		}
		**/
?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
				<?php 
				if(in_array($_SESSION['uinfo']['userid'],$allAdminArr)){				
				?>
                  	<td onclick="javascript: window.location.href='do_login.php?m=admintologin&companyid=<? echo $lsv['CompanyID'];?>'">10<? echo $lsv['CompanyID'];?>
                  		<br /><?php echo '['.$lsv['CompanyDatabase'].']';?>
                  	</td>
				<?php }else{?>
                  	<td >10<? echo $lsv['CompanyID'];?></td>
				<?php }?>
                 	<td><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank">
                      <? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?>
                        <span style="color:red;"><?  echo ' '.$yunType[$lsv['CompanyType']];?></span></a>
                        <?php if($lsv['CompanyCredit'] ==1){?>
                            <i class="iconfont icon-zhang" style="color: #faa70d;font-size: 20px;vertical-align: top;margin-left: 10px;"></i>
                       <?php  }?>

                    </td>
                          
				  	<td><?php
				  echo $industryarr[$lsv['CompanyIndustry']].'<br />'.$areaarr[$lsv['CompanyArea']];
				  ?></td>
                    <td class="bottomline">                              
				  <?php 
				  	$clientSql = "select count(*) from ".DATATABLE."_order_client where ClientCompany=".$lsv['CompanyID']." and ClientFlag=0";
				  	echo $db->get_var($clientSql).' 个';
				  	echo '<br />';
				  	$clientSql = "select count(*) from ".DATATABLE."_order_client where ClientCompany=".$lsv['CompanyID']." and C_Flag='D'";
				  	$count = $db->get_var($clientSql);
				  	echo $count.' 个';
				  	echo '[<a href="client_verify.php?id='.$lsv['CompanyID'].'" target="_blank">查看列表</a>]';
				  ?>
                    </td>
				  	<td ><? echo '<a href="http://'.$lsv['CompanyPrefix'].'.dhb.hk" target="_blank">'.$lsv['CompanyPrefix'].'</a><br />'.$lsv['CompanySigned'];?></td>
				 	<td >&nbsp;<strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];?><br /><? echo $lsv['CS_SmsNumber'];?> 条 </strong></td>
                  	<td ><? echo $lsv['CompanyContact'].'<br />'.$lsv['CompanyMobile'];?></td>
                  	<td align="right" title="<?php echo $lsv['CompanyRemark'];?>">

					    <a href="company_user.php?ID=<? echo $lsv['CompanyID'];?>" ><img src="img/user.gif" border="0" class="img" title="帐号" /></a>&nbsp;&nbsp;
	
						<a href="company_edit.php?ID=<? echo $lsv['CompanyID'];?>" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;
						<?php
						if(in_array($_SESSION['uinfo']['userid'],$topAdminArr)){
						?>
						<br /><a href="javascript:void(0);" onclick="do_delete('<? echo $lsv['CompanyID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>&nbsp;&nbsp;&nbsp;
						<?php }?>
				  	</td>
                </tr>
<? } }else{?>

     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>
              </table>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="100" align="center"><?php echo count($list_data);?></td>
					 <td align="right"  height="30" >
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="manager.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
					 </td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >



              </form>

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


<div id="windowForm">
    <div class="windowHeader">
        <h3 id="windowtitle">ERP接口信息</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent2">
        <form id="open_erp_fm">
            <input name="m" value="set_erp_info" type="hidden"/>
        <input name="company" type="hidden" value=""/>
        <table width="100%">
            <tr class="bottomline">
                <td width="24%" align="right">序列号：</td>
                <td align="left">
                    <input name="serial" value="" type="hidden"/>
                    <span data-serial></span>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">密码：</td>
                <td align="left">
                    <input name="password" type="hidden" value=""/>
                    <span data-password></span>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">版本：</td>
                <td align="left">
                    <select name="version">
                        <option value="">请选择</option>
                        <?php
                            foreach($erp_version as $ver){
                                echo "<option value='".$ver."'>".$ver."</option>";
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">是否开通：</td>
                <td align="left">
                    <label><input type="radio" name="status" value="F" />关闭</label>
                    <label><input type="radio" name="status" value="T" />开通</label>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">是否运行：</td>
                <td align="left">
                    <label><input type="radio" name="isOpen" value="F" />关闭</label>
                    <label><input type="radio" name="isOpen" value="T" />运行</label>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">订单传输开始时间：</td>
                <td align="left">
                    <label>
                        <input type="text" id="begin_date" name="transStart" />
                    </label>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">订单传递：</td>
                <td align="left">
                    <label><input type="radio" name="transferCheck" value="F" />无需审核</label>
                    <label><input type="radio" name="transferCheck" value="T" />需要审核</label>
                </td>
            </tr>
            <tr class="bottomline">
                <td align="right">接口开发方：</td>
                <td align="left">
                    <label><input type="radio" name="develop" value="DHB" />订货宝开发</label>
                    <label><input type="radio" name="develop" value="OTHER" />第三方开发</label>
                </td>
            </tr>
            <tr class="bottomline">
                <td colspan="2">
                    <input class="button_1 btn_erp_submit" type="button" value="提交"/>
                    <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                </td>
            </tr>
        </table>
        </form>
    </div>
</div>
<?php 
if($_SESSION['uinfo']['userid'] == "1"){
?>
<script type="text/javascript">
/*
     $.post("do_sms1.php?m=shownumber1",$("#alipayment").serialize(),
		function(data){
			if(data < 3000) alert('系统通知短信数：'+data);
		});

	 $.post("do_sms1.php?m=shownumber3",$("#alipayment").serialize(),
		function(data){
			if(data < 1000) alert('校验码短信数：'+data);
		});
*/
</script>
<?php }?>
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
				$repeatMsg = str_repeat(" -+- ", $layer-2);
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