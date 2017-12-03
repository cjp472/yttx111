<?php 
$menu_flag = "manager";
include_once ("header.php");

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

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
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="experience.php">体验入口</a></div>
          </div>
         
        </div>

    	
        <div class="line2"></div>
        
        <div class="warning">
			注：这里为后台体验系统相关管理入口地址。
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
                  <td>行业管理</td>
				  <td>下面的“配置行业”和“创建公司”的入口地址。</td>
                  <td><a href="experience_industry.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
                  <td>配置行业</td>
				  <td>与“创建公司”关联。</td>
                  <td><a href="experience_industry_option.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>创建公司</td>
				  <td>与“配置行业”关联。</td>
                  <td><a href="experience_company.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>行业公司使用情况</td>
				  <td>目前行业的公司使用的情况。</td>
                  <td><a href="experience_count.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>体验数据还原</td>
				  <td>整理重置体验数据。</td>
                  <td><a href="experience_reset.php">访问</a></td>
                </tr>
                <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>体验联系人信息</td>
				  <td>查看体验联系人信息。</td>
                  <td><a href="experience_contact.php">访问</a></td>
                </tr>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				  <td>体验数据分析</td>
				  <td>查看体验一些分析数据。</td>
                  <td><a href="experience_analyze.php">访问</a></td>
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