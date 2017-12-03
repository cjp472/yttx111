<?php
    $company_id = $_SESSION['uinfo']['ucompany'];
    $cs_flag = $db->get_var("SELECT CS_Flag FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$company_id} LIMIT 1");
    
	if($cs_flag != 'T' && !isset($in['isin'])) {
?>
<script src="../scripts/layer/layer.js" type="text/javascript"></script>
<script type="text/javascript">


var validHtml = '<div class="layui-layer-content" style="height: 143px;line-height: 28px;font-size: 16px;text-align: center;margin: 0 15px;">';
	validHtml +='<img src="../images/wenjian.jpg" style="width: 50px;display: block;margin: 0 auto;margin-top: 10px;">';
<?php if($cs_flag == 'W'){?>
	validHtml += '为便于您更好的使用系统<br />请现在前往 ›› <a href="company_upload.php">上传企业资质文件</a>';
<?php }else if($cs_flag == 'F'){?>
	validHtml = validHtml.replace('wenjian.jpg', 'cha.jpg');
	validHtml += '您的资料审核未通过<br />请现在前往 ›› <a href="company_upload.php">更新企业资质文件</a>';
<?php }else{?>
	validHtml += '我们已收到您的资料，请耐心等待审核';
<?php }?>
	validHtml += '</div>';
	
layer.open({
	type : 1,
  	title: '提示信息',
  	area: ['390px', '170px'],
  	content: validHtml,
  	cancel: function(){
  	  	
  	  <?php echo (strpos($_SERVER['SCRIPT_NAME'], 'client.php') === false && strpos($_SERVER['SCRIPT_NAME'], 'user_made.php') === false) ? '' : "window.location = 'home.php?isin';"; ?>
  	}
});  
</script>
<?php } ?>