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
        	 <div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="experience.php">体验入口</a>&#8250;&#8250; <a href="experience_industry.php">创建公司</a></div>
          </div>
         
        </div>

    	
        <div class="line2"></div>
        
        <div class="warning">
			注：这里将根据行业数据进行批量生成对应的行业的公司。
		</div>
        
        <div class="bline" >
       
<div id="sortleft">

 <form id="MainForm" name="MainForm" method="post" target=""  action="">
             <table width="100%" cellspacing="0" cellpadding="0" border="0">
               <thead>
                <tr>
                  <td  class="bottomlinebold">行业</td>
                  <td width="50px" class="bottomlinebold">数量</td>
                </tr>
     		 </thead>      		
      		<tbody>
		      	<?php 
		      	$sortarrTemp = $db->get_results("SELECT IndustryID,IndustryName,IndustryAbout,IndustryOrder FROM ".DATABASEU.DATATABLE."_order_industry ORDER BY IndustryID ASC ");
				if($sortarrTemp){
					foreach($sortarrTemp as $val){
						$sortarr[$val['IndustryID']] = $val;
					}
				}
				?>
				<?php if(!empty($in['industry']) && array_key_exists($in['industry'], $arrIndustryOption)):?>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" industry="<?php echo $in['industry'];?>" id="line_<?php echo $in['industry'];?>" style="">
                  <td><?php echo $sortarr[$in['industry']]['IndustryName'];?></td>
                  <td><input type="text" style="width:40px;" name="num_value[<?php echo $sortarr[$in['industry']]['IndustryID'];?>]" value="<?php echo array_key_exists($in['industry'], $arrIndustryOption) ? $arrIndustryOption[$in['industry']] : 10;?>" /></td>
                </tr>
				<?php else:?>
				<?php foreach($sortarr as $svar): ?>
				<?php if(array_key_exists($svar['IndustryID'], $arrIndustryOption)):?>
               <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" industry="<?php echo $svar['IndustryID'];?>" id="line_<?php echo $svar['IndustryID'];?>" style="">
                  <td><?php echo $svar['IndustryName'];?>&nbsp;&nbsp;<a style="width:30px;color:red;font-size:14px;cursor:pointer;" onclick="deleteItemHy('<?php echo $svar['IndustryID'];?>');">[x]</a></td>
                  <td><input type="text" style="width:40px;" name="num_value[<?php echo $svar['IndustryID'];?>]" value="<?php echo array_key_exists($svar['IndustryID'], $arrIndustryOption) ? $arrIndustryOption[$svar['IndustryID']] : 10;?>" /></td>
                </tr>
                <?php endif;?>
                <?php endforeach; ?>
				<?php endif;?>
 				</tbody>                
              </table>
            <div style="padding-right:10px;margin-top:80px;">
				<input id="make_company_button" type="button" class="button_1" id="make_company_button" value="生成公司" onclick="do_make_company();" />
				<input type="button" onclick="clearMessage();" value="清理消息" id="savedelid" class="button_3">
			</div>
              <INPUT TYPE="hidden" name="referer" value ="" >
      </form>
       	  </div>
<div id="sortright">
	<div style="font-size:25px;color:#999;margin-bottom:10px;">生成进度消息框</div>
	<div id="message-box" style="width:95%; height:600px; overflow:auto; border:2px solid #ccc;padding:10px;">
		系统已准备就绪...<br/>
	</div>
 </div>
              
          </div>    
        <br style="clear:both;" />

    </div>
    
    <? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>