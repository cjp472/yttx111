<?php 
    $menu_flag = "common_count";
    include_once ("header.php");
    
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
            $datefield = 'cs.CS_BeginDate';
        }
        else
        {
            $datefield = 'cs.CS_EndDate';
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
    $btime = date("Y-m-d",strtotime("-30 day")). ' 00:00:00';
    $etime = date("Y-m-d",time()).' 23:59:59';
    $monthmsg = " AND ReturnDateTime >= ".date("YmdHis",strtotime($btime))." AND ReturnDateTime <= ".date("YmdHis",strtotime($etime));

    
    /** 因以company+getway表为主表关联其他表，所以只需COUNT主表的条数即可 **/
    $InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_getway Account INNER JOIN (
                                    SELECT c.CompanyID FROM ".DATABASEU.DATATABLE."_order_company c 
                                    INNER JOIN ".DATABASEU.DATATABLE."_order_cs cs 
                                    ON cs.CS_Company = c.CompanyID 
                                    where c.CompanyFlag='0' and cs.CS_Flag='T' ".$sqlmsg."
                                ) AS CInfo ON Account.companyid = CInfo.CompanyID WHERE Account.STATUS='T' AND Account.IsDefault='Y'");
    $page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],"gid"=>$in['gid'],"date_field"=>$in['date_field'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);

    $sql = "SELECT CInfo.CompanyName,CInfo.CompanyID,CInfo.BusinessLicense,Account.SignAccount,Main.OpenCNT,PayCNT.PayCNT,PayTotal.PayMoney,PayMonth.PayMonth 
            FROM (-- 取开通快捷账号的供应商及账号
            	SELECT companyid,SignAccount FROM ".DATABASEU.DATATABLE."_order_getway WHERE STATUS='T' AND IsDefault='Y'
            )AS Account
            LEFT JOIN (-- 取供应商开通数
            	SELECT ClientCompany,COUNT(*) AS OpenCNT FROM ".DATABASEU.DATATABLE."_yjf_openapi GROUP BY ClientCompany
            )AS Main ON Main.ClientCompany = Account.companyid
            LEFT JOIN (-- 取总交易额
            	SELECT companyid,COUNT(PayID) AS PayCNT,SUM(PayMoney) AS PayMoney FROM ".DATABASEU.DATATABLE."_order_netpay WHERE getway = 'yijifu' AND (PayResult = '1' OR ErrorCode LIKE 'EXECUTE_SU%' OR ErrorCode LIKE 'PAY_SUCCES%') GROUP BY companyid
            )AS PayTotal ON Account.companyid = PayTotal.companyid
	        LEFT JOIN (-- 取订单金额大于50的交易笔数
            	SELECT companyid,COUNT(PayID) AS PayCNT FROM ".DATABASEU.DATATABLE."_order_netpay WHERE getway = 'yijifu' AND (PayResult = '1' OR ErrorCode LIKE 'EXECUTE_SU%' OR ErrorCode LIKE 'PAY_SUCCES%') AND PayMoney >= 50 GROUP BY companyid
            )AS PayCNT ON Account.companyid = PayCNT.companyid
            LEFT JOIN (-- 取近30天交易额
            	SELECT companyid,SUM(PayMoney) AS PayMonth FROM ".DATABASEU.DATATABLE."_order_netpay 
            	WHERE getway = 'yijifu' AND (PayResult = '1' OR ErrorCode LIKE 'EXECUTE_SU%' OR ErrorCode LIKE 'PAY_SUCCES%') ".$monthmsg."   
            	GROUP BY companyid
            )AS PayMonth ON Account.companyid = PayMonth.companyid
            INNER JOIN (-- 取供应商信息
            	SELECT c.CompanyName,c.CompanyID,c.BusinessLicense FROM ".DATABASEU.DATATABLE."_order_company c 
        	    INNER JOIN ".DATABASEU.DATATABLE."_order_cs cs ON c.companyid= cs.CS_Company 
    	        WHERE c.CompanyFlag='0' AND cs.CS_Flag = 'T' ".$sqlmsg ."
            ) AS CInfo ON Account.companyid = CInfo.CompanyID
             ORDER BY Account.CompanyID DESC";
    
    $list_data = $db->get_results($sql." ".$page->OffSet());

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

        	  <form id="FormSearch" name="FormSearch" method="get" action="pay_count.php">

        	    <label>

        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" value="<?php echo $in['kw']?>" class="inputline" />

       	        </label>
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
       	          <input type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
        </div>
        <div class="line2"></div>

        <div class="bline">
        
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('pay_count.php');?></td>
                    </tr>
                 </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">公司名称/系统名称</td>
				  <td width="15%" class="bottomlinebold">快捷账号</td>
				  <td width="10%" class="bottomlinebold">经销商开通数量</td>
				  <td width="10%" class="bottomlinebold">交易笔数</td>
                  <td width="10%" class="bottomlinebold">总交易额</td>
                  <td width="10%" class="bottomlinebold">近30天交易额</td>
                </tr>
     		 </thead> 
      		<tbody>

            <?php
            $kfs = $jys = $jye = $jye30 = 0;
            	if(!empty($list_data))
            	{
            		foreach($list_data as $key=>$lsv)
            		{
            ?>
                        <tr id="line_<? echo $lsv['CompanyID'];?>" title="<? echo $lsv['VisitContent'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
        				
                          <td >10<? echo $lsv['CompanyID'];?></td>
        
                          <td ><a target="_blank" href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['CompanyName'];?>" ><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?></a></td>
        				  <td ><? echo $lsv['SignAccount'];?></td>
        				  <td ><? echo intval($lsv['OpenCNT']);?></td>
        				  <td ><? echo floatval($lsv['PayCNT']);?></td>
        				  <td ><? echo number_format($lsv['PayMoney'],2,'.',',');?></td>
                          <td ><? echo number_format($lsv['PayMonth'],2,'.',',');?></td>
                          
                        </tr>
            <? 
            $kfs = $kfs + $lsv['OpenCNT'];
            $jys = $jys + $lsv['PayCNT'];
            $jye = $jye + $lsv['PayMoney'];
            $jye30 = $jye30 + $lsv['PayMonth'];
            
            } 
            $jyen = number_format($jye,2,'.',',');
            $jye30n = number_format($jye30,2,'.',',');
            }else{?>
     			 <tr>
       				 <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
            <? }?>
            <tr id="line_end"  class="bottomline" >
                        
                          <td ></td>
        
                          <td >合计：</td>
                          <td ></td>
                          <td ><? echo intval($kfs);?></td>
                          <td ><? echo $jys;?></td>
                          <td ><? echo $jyen;?></td>
                          <td ><? echo $jye30n;?></td>
                          
                        </tr>
 				</tbody>
              </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('pay_count.php');?></td>
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