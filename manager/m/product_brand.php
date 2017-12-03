<?php
$menu_flag = "product";
$pope	   = "pope_view";
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
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/product.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
#windowForm{
	width:700px;
}

</style>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="product_brand.php">
        		<tr>
        			<td align="center" width="80"><strong>品牌搜索：</strong></td>
					<td width="120"><input name="kw" id="kw" class="inputline" value="" onfocus="this.select();" type="text"></td>
					<td width="60"><input class="mainbtn" id="searchbutton" value="搜 索" type="submit"> </td>
					<td align="right"><div class="location"><strong>当前位置：</strong><a href="product.php">商品品牌</a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">


        <div style="width:96%; margin:2px auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
          		<div class="font12h">新增品牌类型:</div>
          	  <table width="70%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="bold" width="10%">品牌编号:</td>
                  <td width="24%" ><input type="text" name="data_BrandNO" id="data_BrandNO" value="" /></td>
				  <td class="bold" width="10%" >品牌名称:</td>  
				  <td width="24%"><input type="text" name="data_BrandName" id="data_BrandName" value="" /></td>         
                  <td ><input type="button" name="savebutton" id="savebutton" value="保 存" class="button_2" onclick="do_save_brand();" /> </td>
                </tr>
              </table>        
          
          <hr />
          
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">行号</td>
                  <td width="8%" class="bottomlinebold">首页推荐</td>
                  <td width="10%" class="bottomlinebold">品牌编号</td>
				  <td width="10%" class="bottomlinebold">品牌Logo</td> 
				  <td class="bottomlinebold">品牌名称</td> 
				  <td width="18%" class="bottomlinebold">拼音码</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php
	
	$condition = $in['kw'] ? " and BrandName like '%".trim($in['kw'])."%'" : "";
	
	$datasql = "SELECT BrandID,BrandNO,BrandName,BrandPinYin,Logo,IsIndex FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$condition." Order by BrandID Desc";
	$InfoSql = "SELECT count(*) as allrow FROM ".DATATABLE."_order_brand where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$condition."  Order by BrandID Desc";
	$InfoDataNum = $db->get_row($InfoSql);
	
	$page = new ShowPage();
	$page->PageSize = 50;
	$page->Total = $InfoDataNum['allrow'];
	$page->LinkAry = array("kw"=>$in['kw']);
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	if(!empty($list_data))
	{
	 $n=1;
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['BrandID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td ><? echo $n++;?></td>
                  <td ><?php if($lsv['IsIndex']) echo '<span style="color:#01a157;">√</span>'; else echo '<span>X</span>';?></td>
                  <td ><? echo $lsv['BrandNO'];?></td>
				  <td >
				  	<?php if($lsv['Logo']){?>
                  		<img src="<?php echo RESOURCE_URL.$lsv['Logo'];?>" width="60" height="24" />
                  	<?php }else{?>
                  		&nbsp;&nbsp;请上传
                  	<?php }?>
				  </td> 
				  <td ><strong><? echo $lsv['BrandName'];?></strong></td> 
				  <td ><? echo $lsv['BrandPinYin'];?></td> 				  
                  <td align="center">
					<a href="javascript:;" onclick="set_edit_bill('<?php echo $lsv['BrandID']; ?>','<?php echo $lsv['BrandNO']; ?>','<?php echo $lsv['BrandName']; ?>')" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;
					<a href="javascript:void(0);" onclick="do_delete_bill('<? echo $lsv['BrandID'];?>');" ><span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a>
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
       			     <td align="right" height="50"><? echo $page->ShowLink('product_brand.php');?></td>
	     		 </tr>
             </table>
              	  
              <a name="editbill"></a>
              <div id="edit_brand"  style="display:none;">
               <div class="font12h">修改品牌类型:</div>
               	  <INPUT TYPE="hidden" name="update_id" id="update_id" value ="" >
	          	  <table width="70%" border="0" cellspacing="0" cellpadding="0">
	                <tr>
	                  <td class="bold" width="10%">品牌编号:</td>
	                  <td width="24%" ><input type="text" name="edit_BrandNO" id="edit_BrandNO" value="" /></td>
					  <td class="bold" width="10%" >品牌名称:</td>  
					  <td width="24%"><input type="text" name="edit_BrandName" id="edit_BrandName" value="" /></td>         
	                  <td ><input type="button" name="editbutton" id="editbutton" value="保 存" class="button_2" onclick="do_edit_brand();" /> </td>
	                </tr>
	              </table>  
              </div>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

<div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">维护品牌档案</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent"></div>
	</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
    
</body>
</html>