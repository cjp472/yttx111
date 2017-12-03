<?php 
    $menu_flag = "sale_count";
    include_once ("header.php");
    
    if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');
    
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
            	    $sqlmsg .= " and cs.CS_SaleUID = ".$in['kw'];
            	}
            	
            	//$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c LEFT JOIN ".DATABASEU.DATATABLE."_order_cs cs ON c.companyid = cs.CS_Company WHERE cs.CS_SaleUID is not null AND cs.CS_Flag = 'T' ".$sqlmsg);
            	 
            	//$page = new ShowPage;
            	//$page->PageSize = 10;
            	//$page->Total = $InfoDataNum['allrow'];
            	//$page->LinkAry = array("kw"=>$in['kw']);
            
            	if($in['m'] == 'showFree')
            	{
            	    $datasql   = "SELECT CS_Company,CS_Number,CS_SaleUID FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company NOT IN(
                            		SELECT DISTINCT o.company_id FROM ".DATABASEU.DATATABLE."_buy_order o
                            		INNER JOIN ".DATABASEU.DATATABLE."_buy_stream s
                            		ON o.order_no = s.order_no
                            		WHERE o.TYPE = 'product' AND s.status = 'T'
                            	)  AND CS_Flag ='T' AND CS_SaleUID = ".$in['kw']." ORDER BY CS_Company DESC";
            	}
            	else if($in['m'] == 'showPay')
            	{
            	     $datasql = "SELECT cs.CS_SaleUID,PayOrder.* FROM (
                                	SELECT s.stream_no,s.order_no,s.company_id,o.type FROM ".DATABASEU.DATATABLE."_buy_order o 
                                	INNER JOIN ".DATABASEU.DATATABLE."_buy_stream s 
                                	ON o.order_no = s.order_no 
                                	WHERE o.TYPE IN ('product','weixin','erp') AND s.status = 'T'
                                ) AS PayOrder LEFT JOIN ".DATABASEU.DATATABLE."_order_cs cs 
            	                ON cs.CS_Company = PayOrder.company_id WHERE cs.CS_Flag ='T' ".$sqlmsg." ORDER BY PayOrder.order_no DESC";
            	}  
            	else if($in['m'] == 'showTotal')
            	{
            	    $datasql = "SELECT total.*,s.CS_SaleUID FROM (
                                	SELECT s.stream_no,s.order_no,s.amount,s.company_id,o.type FROM ".DATABASEU.DATATABLE."_buy_stream s 
                                	INNER JOIN ".DATABASEU.DATATABLE."_buy_order o 
                                	ON s.order_no = o.order_no 
                                	WHERE o.type <> 'renewals' AND s.status = 'T' 
                                ) AS total LEFT JOIN ".DATABASEU.DATATABLE."_order_cs s ON s.CS_Company = total.company_id 
                    		    WHERE s.CS_Flag ='T' AND s.CS_SaleUID = ".$in['kw']." ORDER BY total.order_no DESC";
            	}
            	
            	$list_data = $db->get_results($datasql);//." ".$page->OffSet()
            	
            ?>
        
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <table width="100%" border="0" cellpadding="0" cellspacing="0">               
                    
                    <?php if($in['m'] == 'showFree') {?>
                    <thead>
                        <tr>
                          <td  style="padding-left: 10px;"class="bottomlinebold">公司ID</td> 
                          <td  style="padding-left: 10px;"class="bottomlinebold">用户数</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">销售员ID</td>
                        </tr>
                    </thead> 
                    <tbody>
                        <?php
                    		foreach($list_data as $lsv)
                    		{
                        ?>
                                <tr class="bottomline">
                                    <td style="padding-left: 10px;">10<? echo $lsv['CS_Company'];?></td>
                                    <td ><? echo $lsv['CS_Number'];?></td>
                                    <td ><? echo $lsv['CS_SaleUID'];?></td>
                                </tr>
                        <?      } ?>
                    </tbody>
                     <?php } else if($in['m'] == 'showPay'){?>
                     <thead>
                     <tr>
                          <td  style="padding-left: 10px;"class="bottomlinebold">公司ID</td> 
                          <td  style="padding-left: 10px;"class="bottomlinebold">订单号</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">订单类型</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">销售员ID</td>
                        </tr>
                      </thead> 
                      <tbody>
                        <?php
                    		foreach($list_data as $lsv)
                    		{
                        ?>
                                <tr class="bottomline">
                                    <td style="padding-left: 10px;">10<? echo $lsv['CS_Company'];?></td>
                                    <td ><? echo $lsv['order_no'];?></td>
                                    <td ><? echo $lsv['type'];?></td>
                                    <td ><? echo $lsv['CS_SaleUID'];?></td>
                                </tr>
                        <?  } ?>
                    </tbody>
                      <?php } else if($in['m'] == 'showTotal'){?>
                     <thead>
                     <tr>
                          <td  style="padding-left: 10px;"class="bottomlinebold">公司ID</td> 
                          <td  style="padding-left: 10px;"class="bottomlinebold">订单号</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">订单类型</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">金额</td>
                          <td  style="padding-left: 10px;"class="bottomlinebold">销售员ID</td>
                        </tr>
                      </thead> 
                      <tbody>
                        <?php
                    		foreach($list_data as $lsv)
                    		{
                        ?>
                                <tr class="bottomline">
                                    <td style="padding-left: 10px;">10<? echo $lsv['company_id'];?></td>
                                    <td ><? echo $lsv['order_no'];?></td>
                                    <td ><? echo $lsv['type'];?></td>
                                    <td ><? echo $lsv['amount'];?></td>
                                    <td ><? echo $lsv['CS_SaleUID'];?></td>
                                </tr>
                        <?  } ?>
                        </tbody>
                      <?php }?>
                </table>
                <br style="clear:both;" />
                <!-- <table width="100%" border="0" cellspacing="0" cellpadding="0" style="padding-right: 5px;">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('sale_client.php');?></td>
                    </tr>
                 </table> -->
            </form>
        </div>
    <br style="clear:both;" />
    </div>
</body>
</html>