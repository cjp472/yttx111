<?php 
    $menu_flag = "sale_count";
    include_once ("header.php");
    
       if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr) && !in_array($_SESSION['uinfo']['userid'],$sqArr)) exit('非法路径!');
    
    $accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
    
    foreach($accarr as $accvar)
    {
        $industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];
    }
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
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
</head>
<body>    
    <div id="bodycontent" style="width:100%">
        <div class="bline" style="width:100%; height:460px; overflow:auto;">
            <?php
            
            	$sqlmsg = '';
            	if(!empty($in['kw']))
            	{
            	    $sqlmsg .= " and cs.CS_SaleUID = '".$in['kw']."' ";
            	}
            	
            	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs cs ON c.companyid = cs.CS_Company WHERE cs.CS_SaleUID is not null AND cs.CS_Flag = 'T' ".$sqlmsg);
            	 
            	$page = new ShowPage;
            	$page->PageSize = 10;
            	$page->Total = $InfoDataNum['allrow'];
            	$page->LinkAry = array("kw"=>$in['kw']);
            
            	$datasql   = "SELECT c.*,cs.* FROM ".DATABASEU.DATATABLE."_order_company c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs cs ON c.companyid = cs.CS_Company WHERE cs.CS_SaleUID is not null AND cs.CS_Flag = 'T' ".$sqlmsg;
            	$list_data = $db->get_results($datasql." ".$page->OffSet());
            ?>
        
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <table width="100%" border="0" cellpadding="0" cellspacing="0">               
                    <thead>
                        <tr>
                          <td width="15%" class="bottomlinebold" style="padding-left: 10px;">编号</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">公司名称</td> 
                          <td  style="padding-left: 10px;"class="bottomlinebold">地区/行业</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">版本</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold" align="left">开通/到期时间</td>
                        </tr>
                    </thead> 
                
                    <tbody>
                    
                        <?php
                        	if(!empty($list_data))
                        	{
                        		foreach($list_data as $lsv)
                        		{
                        ?>
                                    <tr id="line_<? echo $lsv['CompanyID'];?>" title="<? echo $lsv['Remark']; ?>" class="bottomline">
                                        <td style="padding-left: 10px;">10<? echo $lsv['CompanyID'];?></td>
                                        <td ><? echo $lsv['CompanyName'];?></td>
                                        <td ><? echo $industryarr[$lsv['CompanyIndustry']].'<br />'.$areaarr[$lsv['CompanyArea']];?></td>
                                        <td ><strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];?></strong></td>
                                        <td ><? 
                                            echo $lsv['CS_OpenDate'];
                                            $timsgu = strtotime($lsv['CS_EndDate']);
                                            
                                            if($timsgu - time() < 30*24*60*60){
                                            
                                                echo " - <font color=red>".$lsv['CS_EndDate']."</font>";
                                            
                                            }else{
                                            
                                                echo ' - '.$lsv['CS_EndDate'];
                                            
                                            }
                                        ?></td>
                                    </tr>
                        <?      } 
                        	}
                            else
                            {
                        ?>
                                <tr>
                                    <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
                                </tr>
                        <? }?>
                    </tbody>
                </table>
                <br style="clear:both;" />
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-right: 5px;">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('sale_client.php');?></td>
                    </tr>
                 </table>
            </form>
        </div>
    <br style="clear:both;" />
    </div>
</body>
</html>