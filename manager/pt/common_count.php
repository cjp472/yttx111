<?php 
$menu_flag = "common_count";
include_once ("header.php");
if($_SESSION['uinfo']['userid'] == 12403){ header("location: visit.php");}
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
<script type="text/javascript" src="js/manager.js?v=<? echo VERID;?>"></script>
<style type="">
.bottomlinebold{
	padding-right:2px;
}
</style>
<script type="text/javascript">
    $(function() {
    	$("#bdate").datepicker({changeMonth: true,	changeYear: true});
    	$("#edate").datepicker({changeMonth: true,	changeYear: true});
    });
</script>
</head>
<body>
<?php include_once ("top.php");?> 

<?php include_once ("inc/son_menu_bar.php");?>
        
    <div id="bodycontent">

    	<div class="lineblank"></div>

    	<div id="searchline">

        	<div class="leftdiv">

        	  <form id="FormSearch" name="FormSearch" method="get" action="common_count.php">

        	    <label>

        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" style="width: 110px;" name="kw" id="kw" value="<?php echo $in['kw']?>" class="inputline" />

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
				<select id="uid" name="uid"  style="width:105px;" class="select2">
				  <option value="" >⊙ 医统客服</option>
				  <?php 
					$n = 0;		
					$accarr = $db->get_results("SELECT u.UserID,u.UserName,u.UserTrueName FROM (SELECT DISTINCT CreateUID FROM ".DATABASEU.DATATABLE."_order_company_visit) v INNER JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUid=u.UserID ");

					foreach($accarr as $accvar)
					{
						$n++;
						$industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];

						if($in['uid'] == $accvar['UserID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$accvar['UserID'].'" '.$smsg.' title="'.$accvar['UserName'].'"  >'.$accvar['UserTrueName'].'</option>';
					}
				?>
			    </select>
				<label>
        	      <select id="uid2" name="is_reach"  style="width:100px;" class="select2">
        	        <option value="" >⊙ 是否达标</option>
                     <option value="T" <?php if($in['is_reach']=="T"){echo "selected='selected'";}?>>是</option>
                      <option value="F" <?php if($in['is_reach']=="F"){echo "selected='selected'";}?> >否</option>
      	        </select>
        	      <select id="date_field" name="date_field"  style="width:95px;" class="select2">
                    <option value="" >⊙ 时间区间</option>
					<option value="end_date" <?php if($in['date_field'] == 'end_date') echo 'selected="selected"';?> >到期时间</option>
					<option value="begin_date" <?php if($in['date_field'] == 'begin_date') echo 'selected="selected"';?> >注册时间</option>
          <option value="CS_OpenDate" <?php if($in['date_field'] == 'CS_OpenDate') echo 'selected="selected"';?> >开通时间</option>

				  </select>
                  
                  
                  <select id="uflag" name="uflag"  style="width:85px;" class="select2">
                  <option value="" >使用状态</option>
					<option value="F" <?php if($in['uflag'] == 'F') echo "selected='selected'"?>>未用</option>
    				<option value="T" <?php if($in['uflag'] == 'T') echo "selected='selected'"?>>使用</option>
    				<option value="L" <?php if($in['uflag'] == 'L') echo "selected='selected'"?>>失联</option>
				  </select>
                  
       	        </label>
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:70px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" />-</label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:70px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />
       	        <label>
       	          <input type="checkbox" name="is_teeny" value="T" <?php if($in['is_teeny']=="T"){echo "checked='checked'";}?>/>天力
				</label>
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
        	if(empty($in['num']))
        	{
        	    if(!empty($in['iid']))  
        	    {
        	        $sqlmsg .= " and c.CompanyIndustry=".$in['iid']." "; 
        	    }
        	    else
        	    {
        	        $in['iid'] = '';
        	    }
        	    
        	    if(!empty($in['aid']))
        	    {
        	        if(empty($areainfoselected['AreaParent']))
        	        {
        	            $sqlmsg .= " and ( c.CompanyArea=".$in['aid']." or c.CompanyArea in (SELECT AreaID FROM ".DATABASEU.DATATABLE."_order_city where  AreaParent=".$in['aid']." ORDER BY AreaID ASC) ) ";
        	        }else
        	        {
        	            $sqlmsg .= " and c.CompanyArea=".$in['aid']." ";
        	        }
        	    }
        	    else
        	    {
        	        $in['aid'] = '';
        	    }
        	    if(!empty($in['gid']))  $sqlmsg .= " and c.CompanyAgent=".$in['gid']." "; else $in['gid'] = '';
        	    if($in['date_field'] == 'begin_date')
        	    {
        	        $datefield = 's.CS_BeginDate';
        	    }elseif($in['date_field'] == 'CS_OpenDate'){
                $datefield = 's.CS_OpenDate';
              }else{
        	        $datefield = 's.CS_EndDate';
        	    }

              if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
              if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";

        	    if(!empty($in['kw'])) 
        	    {
        	        $sqlmsg .= " AND (";
        	        $likeArr = array();
        	        foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail') as $lk) {
        	            $likeArr[] = " {$lk} like '%".$in['kw']."%'";
        	        }
        	        $sqlmsg .= implode(" OR " , $likeArr);
        	        $sqlmsg .= ")";
        	    }
        	}
			
			if(!empty($in['uid'])){
				$sqlmsg .= " and v.create_uid=".$in['uid']." ";
			}

			if(!empty($in['is_reach'])){
				$sqlmsg .= " and v.is_reach='".$in['is_reach']."' ";
			}
            
             if(!empty($in['uflag'])){
    			$sqlmsg .= " and v.UseFlag='".$in['uflag']."' ";
        	 }
        	
        	//搜索天力精算的客户 addby lxc 20160608
        	if(!empty($in['is_teeny'])){
        		$sqlmsg .= " and c.CompanyAgent= 10 ";
        	}

          if(!empty($in['IsCharge'])){
            $sqlmsg .= " and s.IsCharge = '".$in['IsCharge']."'";
          }

        	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_common_count v inner join ".DATABASEU.DATATABLE."_order_company c ON v.company_id = c.CompanyID left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0' and s.CS_Flag='T' ".$sqlmsg);
    
        	$page = new ShowPage;
        	$page->PageSize = 50;
        	$page->Total = $InfoDataNum['allrow'];
        	$page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],"gid"=>$in['gid'],"date_field"=>$in['date_field'],"bdate"=>$in['bdate'],"edate"=>$in['edate'],"is_reach"=>$in['is_reach'],"uflag"=>$in['uflag']);
        	
        	$sql = "SELECT c.CompanyAgent,c.CompanyID,c.CompanyName,c.CompanyContact,c.CompanyPhone,c.BusinessLicense,s.CS_Number,s.CS_BeginDate,s.CS_EndDate,s.CS_OpenDate,v.* FROM ".DATABASEU.DATATABLE."_common_count v inner join ".DATABASEU.DATATABLE."_order_company c ON v.company_id=c.CompanyID left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company WHERE c.CompanyFlag='0' and s.CS_Flag='T' " .$sqlmsg. " ORDER BY c.CompanyID DESC";
        	$list_data = $db->get_results($sql." ".$page->OffSet());
        ?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('common_count.php');?></td>
                    </tr>
                 </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="7%" class="bottomlinebold">编号</td>
                  <td width="19%" class="bottomlinebold">公司名称<br />系统名称</td>
                  <td width="6%" class="bottomlinebold">品类数<br />商品数</td>
                  <td width="6%" class="bottomlinebold">许可数<br />经销商数</td>
                  <td width="7%" class="bottomlinebold">总订单量<br />总交易额</td>
                  <td width="10%" class="bottomlinebold">近30天订单量<br />近30天交易额</td>
                  <td width="12%"class="bottomlinebold">近30天活跃用户<br />近30天有订单的用户</td>
                  <td width="8%" class="bottomlinebold">开通时间<br />到期时间</td>
                  <td width="7%" class="bottomlinebold">医统客服</td>
                  <td width="6%" class="bottomlinebold">是否达标</td>
                  <td width="8%" class="bottomlinebold">回访提醒日期</td>
                  <td width="4%" class="bottomlinebold">
                    回访记录条数
                  </td>
                </tr>
     		 </thead> 
      		<tbody>

            <?php
            	if(!empty($list_data))
            	{
            		foreach($list_data as $key=>$lsv)
            		{
            ?>
                        <tr id="line_<? echo $lsv['CompanyID'];?>"  class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">

                          <td title="更新时间：<? echo substr($lsv['update_date'],5,11);?>">10<? echo $lsv['CompanyID'];?><br />
                          <span style="color:#999;" title="注册时间"><?php echo $lsv['CS_BeginDate'];?></span>
                          </td>
        
                          <td >
	                          <a target="_blank" href="company_visit_contact.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" >
	                          	<? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'].(($lsv['CompanyAgent'] == '10') ? ' <font color="red">天力精算</font>':'');?>
	                          </a>
                          </td>
				          <td ><? echo $lsv['site'].'<br />'.$lsv['goods'];?></td>
                          <td ><strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];?><br /><? echo $lsv['client_num'];?></strong></td>
                          <td ><? echo $lsv['order_num'].'<br />'.number_format($lsv['order_total'],2,'.',',');?></td>
                          <td ><? echo $lsv['order_30'].'<br />'.number_format($lsv['order_total_30'],2,'.',',');?></td>

                          <td ><? echo $lsv['client_login_30'].'<br />'.$lsv['client_order_30'].' , '.$lsv['client_order'];?></td>
                          <td title="注册时间：<?php echo $lsv['CS_BeginDate'];?>">
                              <?php
                               if(empty($lsv['CS_OpenDate'])) echo $lsv['CS_BeginDate']; else  echo $lsv['CS_OpenDate'];
                				  $timsgu = strtotime($lsv['CS_EndDate']);
                				  if($timsgu - time() < 30*24*60*60){
                					echo " <br />- <font color=red>".$lsv['CS_EndDate']."</font>";
                				  }else{
                					echo ' <br />- '.$lsv['CS_EndDate'];
                				  }
            				  ?>
                          </td>
                          <td align="center"><? $tempuserdata = $db->get_row("SELECT UserName,UserTrueName FROM ".DATABASEU.DATATABLE."_order_user where UserID={$lsv['create_uid']}"); echo !$tempuserdata['UserTrueName']?"无":$tempuserdata['UserTrueName'];?></td>
                          <td align="center"><? echo $is_reach=$lsv['is_reach']=="F"?"未达标":"已达标"?></td>
                          <td align="center"><? echo $return_time=!$lsv['return_time']?"无":date("Y-m-d H:m:s",$lsv['return_time'])?></td>
                          <td align="center"><a href="company_visit_contact.php?ID=<?php echo $lsv['CompanyID'];?>" target="_blank">(<?php echo $lsv['isvisit'];?>)</a></td>
                        </tr>
                        <? }?>
                         <tr>
       				 <td colspan="15" height="30" align="center">符合记录的条数 <font color="#FF0000"><? echo $InfoDataNum['allrow']?></font>条　</td>
       			 </tr>
            <? }else{?>
     			 <tr>
       				 <td colspan="15" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
            <? }?>
 				</tbody>
              </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('common_count.php');?></td>
                  </tr>
      </table>
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