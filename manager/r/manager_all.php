<?php 
$menu_flag = "manager";
include_once ("header.php");

$arrIndustryOption = array();
$arrTempOption = $db->get_row("select *from ".DATABASEU.DATATABLE."_ty_option where Name='industry' ");
if(!empty($arrTempOption['Value'])){
	$arrIndustryOption = json_decode($arrTempOption['Value'],true);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
    

    <div id="bodycontent">
    	<div class="lineblank"></div>        

		<div id="searchline">
        	<div class="leftdiv">
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="manager_all.php">管理入口</a></div>
          </div>
         
        </div>

    	
        <div class="line2"></div>
        
        <div class="warning">
			注：这里为客户信息相关管理入口地址。
		</div>
        
        <div class="bline" >
       
<div >
             <table width="100%" cellspacing="0" cellpadding="0" border="0">
               <thead>
                <tr>
                  <td  class="bottomlinebold">管理项目</td>
				  <td  class="bottomlinebold">说明</td>
                  <td width="120px" class="bottomlinebold">入口地址</td>
                </tr>
     		 </thead>      		
      		<tbody>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
                  <td>客户管理</td>
				  <td>管理客户信息。</td>
                  <td><a href="manager.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
                  <td>客户反馈</td>
				  <td>客户反馈信息展示。</td>
                  <td><a href="feedback.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>试用管理</td>
				  <td>管理试用记录。</td>
                  <td><a href="request.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>开通试用</td>
				  <td>开通试用记录。</td>
                  <td><a href="request_add.php">访问</a></td>
                </tr>
				<?php if(DHB_RUNTIME_MODE !== 'experience'):?>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>客户订单</td>
				  <td>客户订单确认开通。</td>
                  <td><a href="company_order.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>支付信息</td>
				  <td>查看支付信息。</td>
                  <td><a href="company_stream.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>开票申请</td>
				  <td>查看开票申请信息。</td>
                  <td><a href="company_invoice.php">访问</a></td>
                </tr>
				<?php endif;?>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>登录日志</td>
				  <td>查看登录日志。</td>
                  <td><a href="manager_user_log.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>错误日志</td>
				  <td>查看错误日志。</td>
                  <td><a href="error_log.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>操作日志</td>
				  <td>查看操作日志。</td>
                  <td><a href="execution_admin_log.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>行业管理</td>
				  <td>管理行业信息。</td>
                  <td><a href="industry.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>地区管理</td>
				  <td>管理地区信息。</td>
                  <td><a href="area.php">访问</a></td>
                </tr>
 				</tbody>                
              </table>
       	  </div>
              
          </div>    
        <br style="clear:both;" />

    </div>
    
    <? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>