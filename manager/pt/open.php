<?php 
$menu_flag = "open";
include_once ("header.php");
include_once ("../class/ip2location.class.php");
$erp_version = include_once("inc/erp_version.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);

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
    //###########新增推广关系查看 # 2015-12-03 by dumhao #Start#
    $gzType = $in['generalizeType']?$in['generalizeType']:$in['findText'];
    if(!empty($gzType)) $sqlmsg .= " and c.CompanyID IN(SELECT company_id FROM db_dhb_test_user.rsung_order_type WHERE ttype = '".$gzType."')";
    //###################  END   ##
    if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
    if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";

    //if(!empty($in['kw']))  $sqlmsg .= " and (CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' OR  c.CompanyMobile like '%".$in['kw']."%') ";
    if(!empty($in['kw'])) {
        $sqlmsg .= " AND (";
        $likeArr = array();
        foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail') as $lk) {
            $likeArr[] = " {$lk} like '%".$in['kw']."%'";
        }
        $sqlmsg .= implode(" OR " , $likeArr);
        $sqlmsg .= ")";
    }

}
if(!empty($in['status'])) {
    $sqlmsg .= " AND s.CS_Flag='{$in['status']}' ";
}
if(!empty($in['type_Number'])) {
    $sqlmsg .= " AND s.IsCharge = '".$in['type_Number']."' ";
}
if(!empty($in['region'])) {
    if($in['region'] == 'OTHER') {
        $sqlmsg .= " AND a.region<>'四川成都市' AND a.region<>'广东汕头市' ";
    } else {
        $sqlmsg .= " AND a.region='{$in['region']}' ";
    }

}

//来源
if(!(empty($in['ctype'])) && $in['ctype'] == 'market'){
    $sqlmsg .= " and c.CompanyType in('ali','shuan','suning')";
}elseif(!(empty($in['ctype']))){
    $sqlmsg .= " and c.CompanyType='".$in['ctype']."' ";
}

$begin_company_id = 600;//fixme 需要改为600

$dataCntSql= "SELECT COUNT(*) as Total FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company right join ".DATABASEU.DATATABLE."_buy_account as a ON a.company_id=c.CompanyID where c.CompanyFlag='0' AND c.CompanyID > {$begin_company_id} ".$sqlmsg;
$datasql   = "SELECT c.*,s.*,a.id as account_id,a.ip,a.region,a.AdminUser FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company right join ".DATABASEU.DATATABLE."_buy_account as a ON a.company_id=c.CompanyID where c.CompanyFlag='0' AND c.CompanyID > {$begin_company_id} ".$sqlmsg."  ORDER BY c.CompanyID DESC";

$page = new ShowPage;
$page->PageSize = 100;
$page->Total = (int)$db->get_var($dataCntSql);
$page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],'gid' => $in['gid'] , 'date_field' => $in['date_field'],'status' => $in['status'] , 'region' => $in['region'],'bdate' => $in['bdate'],'edate' => $in['edate'],'num' => $in['num']);
$list_data = $db->get_results($datasql . ' ' . $page->OffSet());

$calc_data = $db->get_results("SELECT s.LoginFrom,COUNT(*) as Total FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company right join ".DATABASEU.DATATABLE."_buy_account as a ON a.company_id=c.CompanyID where c.CompanyFlag='0' AND c.CompanyID > {$begin_company_id} ".$sqlmsg."  GROUP BY s.LoginFrom");
$calc_data = array_column($calc_data ? $calc_data : array(),'Total','LoginFrom');

//统计云市场来源 addby wanjun 20160202
$marketSql = $datasql   = "SELECT c.CompanyType,COUNT(*) AS total FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company right join ".DATABASEU.DATATABLE."_buy_account as a ON a.company_id=c.CompanyID where c.CompanyFlag='0' AND (c.CompanyType!='dhb' AND c.CompanyType!='' AND c.CompanyType!='netcloud' AND c.CompanyType!='lingyuan') AND c.CompanyID > {$begin_company_id} ".$sqlmsg." GROUP BY c.CompanyType";
$marketResult = $db->get_results($marketSql);

$typeTotal = array();
$marketTotal = 0;
foreach ($marketResult as $mk=>$mv) {
    $marketTotal += intval($mv['total']);
    $typeTotal[$mv['CompanyType']] = $mv['total'];
}
$marketType = array(
                'ali'    => '阿里',
                'shuan'  => '曙安',
                'suning' => '苏宁',
                'teeny'  => '天力精算',
                'wxqy'   => '微信企业',
);

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

        	  <form id="FormSearch" name="FormSearch" method="get" action="open.php">
                  <input name="d" value="<?php echo $in['d']; ?>" type="hidden"/>
        	    <label>
        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" value="<?php echo $in['kw']; ?>" class="inputline" />
       	        </label>
       	        <label>
				<select id="ctype" name="ctype"  style="width:140px;" class="select2">
    				<option value="" >⊙ 所有来源</option>
    				<option value="dhb" <?php if($in['ctype'] == 'dhb')  echo 'selected="selected"';?>>┠-订货宝</option>
    				<option value="market" <?php if($in['ctype'] == 'market')  echo 'selected="selected"';?>>┠-云市场</option>
            <option value="teeny" <?php if($in['ctype'] == 'teeny')  echo 'selected="selected"';?>>┠-天力精算</option>
        			<option value="ali" <?php if($in['ctype'] == 'ali')  echo 'selected="selected"';?>>┠- -+- 阿里云</option>
        			<option value="shuan" <?php if($in['ctype'] == 'shuan')  echo 'selected="selected"';?>>┠- -+- 曙安</option>
        			<option value="suning" <?php if($in['ctype'] == 'suning')  echo 'selected="selected"';?>>┠- -+- 苏宁</option>
              <option value="wxqy" <?php if($in['ctype'] == 'wxqy')  echo 'selected="selected"';?>>┠- -+- 微信企业号</option>
				</select>
				</label>
        	    <label>

                    <label>
                        <select name="region" id="region" class="select2" style="width:120px;">
                            <option value="">全部地区</option>
                            <option value="四川成都市" <?php echo $in['region'] == "四川成都市" ? "selected='selected'" : "";  ?> >成都地区</option>
                            <option value="广东汕头市" <?php echo $in['region'] == "广东汕头市" ? "selected='selected'" : "";  ?> >汕头地区</option>
                            <option value="OTHER" <?php echo $in['region'] == "OTHER" ? "selected='selected'" : "";  ?> >其它地区</option>
                        </select>
                    </label>

        	      <select id="date_field" name="date_field"  style="width:105px;display:none;">
				  <option value="begin_date" <?php if($in['date_field'] == 'begin_date') echo 'selected="selected"';?> >注册时间</option>
					<option value="end_date" <?php if($in['date_field'] == 'end_date') echo 'selected="selected"';?> >到期时间</option>
					
				  </select>
       	        </label>
				<label>注册时间:&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

       	        </label>

        	    <label>
        	      &nbsp;&nbsp;审核状态： 
       	        </label>
                  <label>
					<select name="status" id="status">
					<option value="">请选择..</option>
					<option value="T">通过</option>
					<option value="F">不通过</option>
					<option value="D">待审</option>
					</select>
       	        </label>

                  <label>
                      &nbsp;&nbsp;类型：
                  </label>
                  <label>
                      <select name="type_Number" id="type_Number">
                          <option value="">请选择..</option>
                          <option value="F" <?php if($in['type_Number'] == 'F') echo 'selected="selected"';?>>免费</option>
                          <option value="T" <?php if($in['type_Number'] == 'T') echo 'selected="selected"';?>>付费</option>
                      </select>
                  </label>

				<label>				    
				<!--//#新增推广关系查看 # 2015-12-03 by dumhao #Start#---->
                    <input id="generalizeType" name="generalizeType" type="hidden" value="<?php echo $in['generalizeType']?$in['generalizeType']:$in['findText'];?>" />
                <!--//#----------------------- END   -------------------->
				</label>
                  
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>

   	          </form>
   	        </div>
        </div>    	

        <div class="line2"></div>

        <div class="bline">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold" style="border:none;">
                         <label><strong>统计来源</strong></label>
                         <label><strong>共 <?php echo array_sum($calc_data); ?> 个</strong></label>
                         <?php foreach($from_arr as $fk => $fv) { ?>
                             <label><?php echo $fv.'：' . ((int)$calc_data[$fk]); ?>个&nbsp;</label>
                         <?php } ?>
       				 </td>
       				 <td class="bottomlinebold" style="border:none;">
                         <label><strong>云市场统计</strong></label>
                         <label><strong id="market-total">共 <?php echo $marketTotal;?> 个</strong></label>
                         <?php foreach($marketType as $fk => $fv) {?>
                             <label><?php echo $fv.'：' . intval($typeTotal[$fk]); ?> 个&nbsp;</label>
                         <?php }?>
       				 </td>
					 <td align="right"  height="30" class="bottomlinebold" style="border:none;">&nbsp;
                         
					 </td>
     			 </tr>
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold">&nbsp;
                         
       				 </td>
       				 <td class="bottomlinebold">&nbsp;
                         
       				 </td>
					 <td align="right"  height="30" class="bottomlinebold">
                         <? echo $page->ShowLink('open.php');?>
					 </td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">联系人</td>
				  <td width="11%" class="bottomlinebold">手机号</td>
				  <td width="6%" class="bottomlinebold">前缀</td>
				  <td align="center" width="6%" class="bottomlinebold">用户</td>
          
                  <td width="8%" class="bottomlinebold" align="center">注册时间</td>
                  
                  <td width="8%" class="bottomlinebold" align="center">开通/到期时间</td>
                    <td width="12%" class="bottomlinebold" align="center">IP地址</td>
                  <td width="7%" class="bottomlinebold" align="center">状态/来源</td>
				
                    <td width="5%" class="bottomlinebold" align="center">分配用户</td>
                    <td width="7%" class="bottomlinebold" align="center">查看</td>
                </tr>
     		 </thead> 

      		<tbody>

<?php
	if(!empty($list_data))
	{
        $IPAddress = new IPAddress();
//        var_dump($list_data);exit;
		foreach($list_data as $lsv)
		{

?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >


                  <td >10<? echo $lsv['CompanyID'];?></td>

                  <td ><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['CompanyContact'];?><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName']; ?>
                      <span style="color:red;"><?  echo ' '.$yunType[$lsv['CompanyType']];?></span></a></td>
				  <td><?php
				  echo $lsv['CompanyMobile'];
				  ?></td>

				  <td ><? echo '<a href="http://'.$lsv['CompanyPrefix'].'.dhb.hk" target="_blank">'.$lsv['CompanyPrefix'].'</a>';?></td>
				  <td align="center">&nbsp;<strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];
           
          ?></strong></td>
           
                  <td align="center"><? echo $lsv['CS_BeginDate'];?>
                  </td>
                 
				  <td align="center" >
                  <?
                  echo $lsv['CS_OpenDate'].'<br />';
				  //$timsgu = strtotime($lsv['CS_EndDate']);
				  //if($timsgu - time() < 30*24*60*60){
					//echo "<font color=red>".$lsv['CS_EndDate']."</font>";
				  //}else{
					echo $lsv['CS_EndDate'];
				  //}
				  ?>
					
                    </td>
                    <td align="center">
                        <?php
                            if($lsv['region']) {
                                echo $lsv['region'] . '(' . $lsv['ip'] . ')';
                            } else {
                                $iparr = explode(",",$lsv['ip']);
                                $IPAddress->qqwry($iparr[0]);
                                $localArea = $IPAddress->replaceArea();
                                $db->query("UPDATE ".DATABASEU.DATATABLE."_buy_account SET region='{$localArea}' WHERE id=" . $lsv['account_id']);
                                echo $localArea . '(' . $iparr[0] . ')';
                            }

                        ?>
                    </td>
                  <td align="center" >
					【<?php
				  echo $audit_arr[$lsv['CS_Flag']];
				  ?>】
				    <br />【<?php
          echo $from_arr[$lsv['LoginFrom']];
          ?>】
				  </td>

                    <td align="center">
                        <?php echo $lsv['AdminUser'] ? $lsv['AdminUser'] : '--'; ?>
                    </td>
                    <td>
                        【<a href="open_contact.php?id=<?php echo $lsv['CompanyID']; ?>">查看回访</a>】
                    </td>
                </tr>
<? } }else{?>

     			 <tr>
       				 <td colspan="9" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>
              </table>

              <!--<table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="100" align="center"><?php /*echo count($list_data);*/?></td>
					 <td align="right"  height="30" >

					 </td>
     			 </tr>
              </table>-->

              <table width="100%" border="0" cellspacing="0" cellpadding="0">

                  <tr>
                      <td width="4%"  height="30" ></td>

                      <td  class="sublink"></td>

                      <td width="50%" align="right"><? echo $page->ShowLink('open.php');?></td>

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