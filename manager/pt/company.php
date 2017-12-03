<?php 
$menu_flag = "common_count";
include_once ("header.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);
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

        	  <form id="FormSearch" name="FormSearch" method="get" action="company.php">

        	    <label>

        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" value="<?php echo $in['kw']; ?>" class="inputline" />

       	        </label>
				<label>
				<select id="iid" name="iid"  style="width:165px;" class="select2">
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
				<select id="gid" name="gid"  style="width:255px;" class="select2">
				<option value="" >⊙ 所属代理商</option>
				<?php 
					$n = 0;		
					$agentdata = $db->get_results("SELECT AgentID,AgentName FROM ".DATABASEU.DATATABLE."_order_agent ORDER BY AgentID ASC ");
					foreach($agentdata as $var)
					{
						$n++;
						$agentarr[$var['AgentID']] = $var['AgentID'];

						if($in['gid'] == $var['AgentID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$var['AgentID'].'" '.$smsg.' title="'.$var['AgentName'].'"  >'.$n.' 、 '.$var['AgentName'].'</option>';
					}
					
				?>
				</select>
				</label>				

        	    <label>
        	      <select id="date_field" name="date_field"  style="width:105px;" class="select2">
					<option value="end_date" <?php if($in['date_field'] == 'end_date') echo 'selected="selected"';?> >到期时间</option>
					<option value="begin_date" <?php if($in['date_field'] == 'begin_date') echo 'selected="selected"';?> >开通时间</option>
				  </select>
       	        </label>
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

       	        </label>
				<label>

       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

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

	//$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0'  ".$sqlmsg."  ");
   
	
	//$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID DESC limit 0,500";
	$datasql   = "SELECT c.*,s.*,l.SerialNumber,l.Password,l.Status,l.Version FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_api_serial as l ON l.CompanyID=c.CompanyID where c.CompanyFlag='0' and s.CS_Flag='T' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
	$list_data = $db->get_results($datasql);			
?>




          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 
					 <td align="right"  height="30" class="bottomlinebold">
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="company.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
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
				  <td width="7%" class="bottomlinebold">前缀</td>
				  <td width="8%" class="bottomlinebold">用户/短信数</td>
                  <td width="12%" class="bottomlinebold">联系人/联系方式</td>
                  <td width="10%" class="bottomlinebold" align="left">开通/到期时间</td>

                </tr>
     		 </thead> 

      		<tbody>

<?php
	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{

?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
				
                  <td >10<? echo $lsv['CompanyID'];?></td>

                  <td ><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?></a></td>
				  <td><?php
				  echo $industryarr[$lsv['CompanyIndustry']].'<br />'.$areaarr[$lsv['CompanyArea']];
				  ?></td>

				  <td ><? echo '<a href="http://'.$lsv['CompanyPrefix'].'.dhb.hk" target="_blank">'.$lsv['CompanyPrefix'].'</a>';?></td>
				  <td >&nbsp;<strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];?><br /><? echo $lsv['CS_SmsNumber'];?> 条 </strong></td>

                  <td ><? echo $lsv['CompanyContact'].'<br />'.$lsv['CompanyMobile'];?></td>

                  <td ><? echo $lsv['CS_BeginDate'];?>
                  <?
				  $timsgu = strtotime($lsv['CS_EndDate']);

				  if($timsgu - time() < 30*24*60*60){

					echo " - <font color=red>".$lsv['CS_EndDate']."</font>";

				  }else{

					echo '<br /> - '.$lsv['CS_EndDate'];

				  }				  

				  ?>
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
					echo '<a href="company.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
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
                <td colspan="2">
                    <input class="button_1 dredgeErp" type="button" value="开通" />
                    <input class="button_1 stopErp" type="button" data-status="F" value="停用"/>
                    <input class="button_1 startErp" type="button" data-status="T" value="启用"/>
                    <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                </td>
            </tr>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        /**
         * @desc 显示接口信息
         */
        $(".showErp").click(function(){
            var serial = $(this).data('serial');
            var password = $(this).data('password');
            var version = $(this).data('version');
            var ct = $("#windowContent2");
            var status = $(this).data('status') == 'T';
            ct.find("input[name='company']").val($(this).data('company'));
            if(serial){
                ct.find("span[data-serial]").html(serial);
                ct.find("input[name='serial']").val(serial);
                ct.find("span[data-password]").html(password);
                ct.find("input[name='password']").val(password);
                if(status){
                    $(".dredgeErp,.startErp").hide();
                    $(".stopErp").show();
                }else{
                    $(".dredgeErp,.stopErp").hide();
                    $(".startErp").show();
                }
                $("select[name='version']").val($(this).data('version'));

            }else{
                $.post("do_company.php?m=buildErp",function(json){
                    serial = json.serial;
                    password = json.password;
                    ct.find("span[data-serial]").html(serial);
                    ct.find("input[name='serial']").val(serial);
                    ct.find("span[data-password]").html(password);
                    ct.find("input[name='password']").val(password);
                    $("select[name='version']").val('');
                    $(".startErp,.stopErp").hide();
                    $(".dredgeErp").show();
                },'json');
            }
            $.blockUI({ message : $("#windowForm")});
        });

        /**
         * @desc 启用/停用接口
         */
        $(".stopErp,.startErp").click(function(){
            var status = $(this).data('status');
            var company = $("#windowContent2 input[name='company']").val();
            var version = $("#windowContent2 select[name='version']").val();
            $.post("do_company.php?m=erpDisabled",{company:company,version:version,status:status},function(json){
                $.unblockUI();
                $.blockUI({ message : '<p>'+json.info+'</p>'});
                if(json.status==1){
                    setTimeout(function(){
                        window.location.reload();
                    },700);
                }
            },'json');
        });

        /**
         * @desc 确认开通接口
         */
        $(".dredgeErp").click(function(){
            var ct = $("#windowContent2");
            var serial = ct.find("input[name='serial']").val();
            var password = ct.find("input[name='password']").val();
            var version = ct.find("select[name='version']").val();
            var company = ct.find("input[name='company']").val();
            $.post("do_company.php?m=dredgeErp",{serial:serial,password:password,version:version,company:company},function(json){
                $.unblockUI();
                $.blockUI({ message : '<p>'+json.info+'</p>'});
                if(json.status==1){
                    setTimeout(function(){
                        window.location.reload();
                    },700);
                }
            },'json');

        });

    });
</script>

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