<style>
.layui-layer-content a{ 
color:#01a157 !important; 
}
</style>
<? $client_id = $_SESSION['cc']['cid'];
    $company_id = $_SESSION['cc']['ccompany'];
    $cs_flag = $db->get_var("SELECT C_Flag FROM ".DATATABLE."_order_client WHERE ClientCompany=$company_id and ClientID=$client_id LIMIT 1");
    
if($cs_flag != 'T' && !isset($in['isin'])) {
 ?>
<script src="template/js/jquery.1.11.js" type="text/javascript"></script>
<script src="../manager/scripts/layer/layer.js" type="text/javascript"></script>
<script type="text/javascript">

var validHtml = '<div class="layui-layer-content" style="height: 143px;line-height: 28px;font-size: 16px;text-align: center;margin: 0 15px;">';
validHtml +='<img src="../manager/images/wenjian.jpg" style="width: 50px;display: block;margin: 0 auto;margin-top: 10px;">';
<? if($cs_flag == 'W' || $cs_flag ==""){  ?>
validHtml += '为便于您更好的使用系统<br />请现在前往 ›› <a href="my.php?m=qualification">上传资质文件</a>';
<? }else if($cs_flag == 'F'){  ?>
validHtml = validHtml.replace('wenjian.jpg', 'cha.jpg');
validHtml += '您的资料审核未通过<br />请现在前往 ›› <a href="my.php?m=qualification">更新企业资质文件</a>';
<? }else{  ?>
validHtml += '我们已收到您的资料，请耐心等待审核';
<? }  ?>
validHtml += '</div>';

layer.open({
type : 1,
  	title: '提示信息',
  	area: ['390px', '170px'],
  	content: validHtml,
  	cancel: function(){
  	  	
  
<? echo (strpos($_SERVER['SCRIPT_NAME'], 'cart.php') === false) ? '' : "window.location = 'home.php?isin';";  ?>
  		
  	}
});  
</script>
<? }  ?>
