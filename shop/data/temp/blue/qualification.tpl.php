<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$_SESSION['ucc']['CompanyName']?> - <?=SITE_NAME?></title>
<meta name='robots' content='noindex,nofollow' />
<link rel="shortcut icon" href="/favicon.ico" />

<link href="<?=CONF_PATH_IMG?>css/base.css?v=<?=VERID?>" type="text/css" rel="stylesheet" />
<script src="template/js/jquery.js" type="text/javascript"></script>
<script src="template/js/my.js?v=<?=VERID?>" type="text/javascript"></script>
<script src="template/js/jquery.blockUI.js" type="text/javascript"></script>
 <!--<script src="template/js/system.js?v=<?=VERID?>89" type="text/javascript"></script>-->  
<script type="text/javascript">var resourceurl = '<?=RESOURCE_URL?>'</script>
<style>
ul.three-silds-box li{
float:left;
width:202px;
clear:none;
cursor:pointer;
border:1px solid #dbdbdb;
margin: 2px;
}
ul.three-silds-box li img{
width:200px;
height:150px;
}
.approve-notice {
    float: none;
    clear: both;
    color: #dd1f1f;
    border: 1px dashed #ff9f36;
    padding: 0 10px;
    font-size: 14px;
}

</style>
</head>

<body>
<? include template('header'); ?>
<div id="main">
<div id="location">当前位置：<a href="home.php">首页</a> / <a href="my.php?m=qualification">企业资质</a></div>
<div class="main_left">
<div class="fenlei_bg_tit"><span class="iconfont icon-wenjian" style="font-size: 15px;color: white;margin-left: 10px"></span>    我的医药账户</div>
  <div class="news_info">
  <!-- 载入菜单 -->
  
<? include template('my_profile_menu'); ?>
  </div>
<div class="fenlei_bottom" style="width: 223px;height: 9px;border-left: 1px solid #D6D6D6;border-right: 1px solid #D6D6D6;border-bottom: 1px solid #D6D6D6"></div>

</div>

<div class="main_right">
 
<div class="right_product_tit">
<div class="xs_0">  <span class="iconfont icon-changfangxing" style="color: #FFB135;font-size:16px;margin-left: -10px;"></span>   企业资质</div>
</div>

<div class="right_product_main">
<div class="list_line">
<div class="num">
<? if($result['data']['C_Flag'] == 'F') { ?>
<p class="approve-notice"><b>因以下原因，您需要再次提交资料</b>：<br /><?=$result['data']['Notice']?></p>
<br />
<? } ?>
<fieldset class="fieldsetstyle">
  <legend></legend>
  <input type="hidden" name="set_filename" id="set_filename" value="" />
  <table width="100%" border="0" align="center" cellpadding="8" cellspacing="0">
      
    <tr>
        
      <td width="30%" class="pay_right">企业名称：</td>
      <td width="70%" class="pay_right_one">
          <strong ><?=$result['CompanyName']?></strong>
          <input type='text' id='BusinessName' value="<?=$result['CompanyName']?>" style="display:none">
      </td>
    </tr>
    <tr>
        <td class="pay_right" valign="top">法人信息：    
            </td>
        <td>
            <span><font style="letter-spacing:15px">姓名</font>：
                
<? if($result['data']['TureUserName']=='') { ?>
                    <input  type="text"  style="border:1px solid #E5E5E5;height: 20px;padding-left:8px;width: 168px;color:#615f5f;font-weight:bold;" name="TureUserName" id="TureUserName" value="<?=$result['Cdata']['ClientTrueName']?>"></span></br>
                
<? } else { ?>
                    
<? if($result['data']['C_Flag'] != 'T') { ?>
                        <input  type="text"  style="border:1px solid #E5E5E5;height: 20px;padding-left:8px;width: 168px;color:#615f5f;font-weight:bold;" name="TureUserName" id="TureUserName" value="<?=$result['data']['TureUserName']?>"></span></br>
                    
<? } else { ?>
                         &nbsp;<?=$result['data']['TureUserName']?></br>
                    
<? } ?>
                
<? } ?>
    
            <span><font style="letter-spacing:15px">手机</font>：
                
<? if($result['data']['UserPhone']=="") { ?>
                    <input  type="text"  style="border:1px solid #E5E5E5 ;height: 20px;padding-left:8px;width: 168px;color:#615f5f;" onkeyup="this.value=this.value.replace(/[^0-9-]+/,'');"  name="UserPhone" id="UserPhone" value="<?=$result['Cdata']['ClientMobile']?>"></span></br>
                
<? } else { ?>
                    
<? if($result['data']['C_Flag'] != 'T') { ?>
                        <input  type="text"  style="border:1px solid #E5E5E5 ;height: 20px;padding-left:8px;width: 168px;color:#615f5f;" onkeyup="this.value=this.value.replace(/[^0-9-]+/,'');"  name="UserPhone" id="UserPhone" value="<?=$result['data']['UserPhone']?>"></span></br>
                    
<? } else { ?>
                        <?=$result['data']['UserPhone']?></br>
                    
<? } ?>
                
<? } ?>
        
            <span><font style="letter-spacing:0.5px">身份证号</font>：
                    
<? if($result['data']['C_Flag'] != 'T') { ?>
                    <input type="text"    style="border:1px solid #E5E5E5;height: 20px;padding-left:8px;width: 168px;color:#615f5f;margin-right:135px" name="IDCard" id ="IDCard" value="<?=$result['data']['IDCard']?>"></span>
<? if($result['data']['C_Flag'] != 'T') { ?>
<input type="button" style="width:85px; height:26px; font-size:12px;float: right;" onClick="upload_mu_img('three_client_box');" class="download-a" value=" 上传身份证照片 ">
<? } else { ?>
<font color="#00AA00" style="float: right;">证件通过</font> 
<? } ?>
</br>
                    
<? } else { ?>
                    <?=$result['data']['IDCard']?></span>
<? if($result['data']['C_Flag'] != 'T') { ?>
<input type="button" style="width:85px; height:26px; font-size:12px;float: right;" onClick="upload_mu_img('three_client_box');" class="download-a" value="上传身份证照片">
<? } else { ?>
<font color="#00AA00" style="float: right;">证件通过</font> 
<? } ?>
</br>
                    
<? } ?>
  
        </td>   
    </tr>
  <fieldset class="fieldsetstyle">
      <input type="hidden" id="set_three_filename" value="" />
    <tr>
      <td class="pay_right" valign="top">
          身份证件 ：
<? if($result['data']['C_Flag'] != 'T') { ?>
<span style="color: red">*</span><br /><span style="font-size:12px;color: red"class="red">(请上传清晰、正确的身份证照片)</span>
<? } ?>
      </td>
      <td class="pay_right_one" valign="top" colspan="2" width="70%" >
      	<ul class="three-silds-box" id="three_client_box" style='width:99%;height:auto;clear:both;'>
            
<? if($result['data']['IDCardImg']) { ?>
            
<? if(is_array($IDCatdImg)) { foreach($IDCatdImg as $skey => $svar) { ?>
               <li>
               	<img src="<?=RESOURCE_PATH?><?=$IDCatdImg[$skey]?>" />              
               </li>
            
<? } } ?>
           
<? } else { ?>
            <li><img src="./images/default_zj.jpg" /></li>
            
<? } ?>
         
        </ul>	
          <input type='hidden' id='hiddenmer'>
      </td>
    </tr>
  </fieldset>
    <!--  非三证合一 -->
    <tr class="CardCheckdis">
      <td class="pay_right">营业执照号码：</td>
      <td class="pay_right_one">
          
<? if($result['data']['C_Flag'] != 'T') { ?>
                <input name="BusinessCard"  style="border:1px solid #E5E5E5;height: 25px;padding-left:8px;color:#615f5f;" id="BusinessCard" type="text" size="50" value="<?=$result['data']['BusinessCard']?>"  />
          
<? } else { ?>
                <?=$result['data']['BusinessCard']?>
          
<? } ?>
      
<? if($result['data']['C_Flag'] != 'T') { ?>
<input name="bt_Picture" type="button" class="bluebtn"  value="上传营业执照" title="上 传" style="width:85px; height:26px; font-size:12px;float: right;" onclick="upload_file_certify('BusinessCardImg')">
<? } else { ?>
<font color="#00AA00" style="float: right;">证件通过</font> 
<? } ?>
           
      </td>
    </tr>
    <tr class="CardCheckdis" >
      <td class="pay_right" valign="top">营业执照图 ：
<? if($result['data']['C_Flag'] != 'T') { ?>
<span style="color: red">*</span><br /><span style="font-size:12px;color: red"class="red">(请签字盖章上传，务必清晰)</span>
<? } ?>
</td>      
        <td class="pay_right_one">
            
<? if($result['data']['C_Flag'] != 'T') { ?>
                <input name="BusinessCardImg" id="BusinessCardImg" type="text" size="50" value="<?=$result['data']['BusinessCardImg']?>"  style="visibility:hidden;" />
            
<? } else { ?>
                <input name="BusinessCardImg" id="BusinessCardImg" type="text" size="50" value="<?=$result['data']['BusinessCardImg']?>"  style="visibility:hidden;" />
            
<? } ?>
            <!--<input name="bt_Picture" type="button" class="bluebtn"  value="上传图片" title="上 传" style="width:85px; height:26px; font-size:12px;" onclick="upload_file_certify('BusinessCardImg')">-->
            <div id="BusinessCardImg_text">
            
<? if($result['data']['BusinessCardImg']) { ?>
            	<a href="<?=RESOURCE_PATH?><?=$result['data']['BusinessCardImg']?>" target="_blank" >
            	<img src="<?=RESOURCE_PATH?><?=$result['data']['BusinessCardImg']?>" border="0" height="150" />
            </a>
            
<? } else { ?>
         	<img src="./images/default_zj.jpg" border="0" height="150" />
            
<? } ?>
            </div>
        </td>
    </tr>
    <tr class="CardCheckdis">
      <td class="pay_right">GSP执照号码：</td>
      <td class="pay_right_one">
        
<? if($result['data']['C_Flag'] != 'T') { ?>
            <input name="GPCard"  style="border:1px solid #E5E5E5;height: 25px;padding-left:8px;color:#615f5f;" id="GPCard" type="text" size="50" value="<?=$result['data']['GPCard']?>"  />
        
<? } else { ?>
            <?=$result['data']['GPCard']?>
       
<? } ?>
       
<? if($result['data']['C_Flag'] != 'T') { ?>
<input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('GPImg');" value="上传GSP认证" title="上 传" style="width:85px; height:26px; font-size:12px;float: right;">
<? } else { ?>
<font color="#00AA00" style="float: right;">证件通过</font> 
<? } ?>
           
      </td>
    </tr>
    <tr class="CardCheckdis" >
      <td class="pay_right" valign="top">GSP认证：
<? if($result['data']['C_Flag'] != 'T') { ?>
<br /><span style="font-size:12px;color: red"class="red">(请签字盖章上传，务必清晰<br />诊所可以不上传该项)</span>
<? } ?>
</td>
      <td class="pay_right_one">
          <input name="GPImg" type="text" id="GPImg" size="50" value="<?=$result['data']['GPImg']?>" style="visibility:hidden;" />
            <div id="GPImg_text">
            
<? if($result['data']['GPImg']) { ?>
            
            	<a href="<?=RESOURCE_PATH?><?=$result['data']['GPImg']?>" target="_blank" >
            	<img src="<?=RESOURCE_PATH?><?=$result['data']['GPImg']?>" border="0" height="150" />
            </a>
        
<? } else { ?>
         	<img src="./images/default_zj.jpg" border="0" height="150" />
            
<? } ?>
            </div>
       </td>
    </tr>
    <tr class="CardCheckdis">
      <td class="pay_right">药品经营许可证号码：</td>
      <td class="pay_right_one" vaign="top">
        
<? if($result['data']['C_Flag'] != 'T') { ?>
            <input name="IDLicenceCard"  style="border:1px solid #E5E5E5;height: 25px;padding-left:8px;color:#615f5f;" id="IDLicenceCard" type="text" size="50" value="<?=$result['data']['IDLicenceCard']?>"  />
        
<? } else { ?>
            <?=$result['data']['IDLicenceCard']?>
        
<? } ?>
      
<? if($result['data']['C_Flag'] != 'T') { ?>
<input name="bt_Picture" type="button" class="bluebtn"  onClick="upload_file_certify('IDLicenceImg');" value="上传药品经营证" title="上 传" style="width:85px; height:26px; font-size:12px;float: right;">
<? } else { ?>
<font color="#00AA00" style="float: right;">证件通过</font> 
<? } ?>
           
      </td>
    </tr>
    <tr class="CardCheckdis" >
      <td class="pay_right" valign="top">药品经营许可证 ：
<? if($result['data']['C_Flag'] != 'T') { ?>
<span style="color: red">*</span><br /><span style="font-size:12px;color: red"class="red">(请签字盖章上传，务必清晰)</span>
<? } ?>
</td>
      <td class="pay_right_one">
          
<? if($result['data']['C_Flag'] != 'T') { ?>
               <input name="IDLicenceImg" id="IDLicenceImg" type="text" size="50" value="<?=$result['data']['IDLicenceImg']?>" style="visibility:hidden;" />
          
<? } else { ?>
                <input name="IDLicenceImg" readonly="readonly" id="IDLicenceImg" type="text" size="50" value="<?=$result['data']['IDLicenceImg']?>" style="visibility:hidden;" />
          
<? } ?>
          <div id="IDLicenceImg_text">
            
<? if($result['data']['IDLicenceImg']) { ?>
            	<a href="<?=RESOURCE_PATH?><?=$result['data']['IDLicenceImg']?>" target="_blank" >
            	<img src="<?=RESOURCE_PATH?><?=$result['data']['IDLicenceImg']?>" border="0" height="150" />
            </a>
            
<? } else { ?>
         	<img src="./images/default_zj.jpg" border="0" height="150" />
            
<? } ?>
            </div>
      </td>
    </tr>
     <!--  非三证合一 完 -->
</div>
    <tr>
      <td class="pay_right">&nbsp;</td>
      <td class="pay_right_one">
          <div>
            
         
          </div>
      </td>
    </tr>
    <tr>
      <td >&nbsp;</td>
        
<? if($result['data']['C_Flag'] != 'T') { ?>
      <td class="pay_right_one"><input type="button" value="保存资料" class="button_4" name="confirmbtn" id="confirmbtn"  onclick="buttonComit()"/></td>
        
<? } else { ?>
        <td></td>
        
<? } ?>
    </tr>

  </table>
  </fieldset>
  <br class="clear" />
  <!-- 第三方合同暂时不需要 2017-8-16 -->
  <div>
  	说明：<br />
  		&nbsp;&nbsp;&nbsp;&nbsp;1、医统天下收集的各项资料仅用于平台和金融认证
  </div>

</div>
<br class="clear" />
</div>		

       </div>
</div>
</div>
<? include template('bottom'); ?>
<div id="windowForm">
    <div class="windowHeader">
        <h3 id="windowtitle">上传图片</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent"></div>
</div>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>
<script>   
    function upload_file_certify(fildname)
    {
 
//        $('#windowContent').html('<iframe src="http://www.yitong111.com/taxetong/shop/plugin/jqUploader/upload_certify.php" width="500" marginwidth="0" height="280" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
        $('#windowContent').html('<iframe src="./plugin/jqUploaderM/upload_certify.php" width="500" marginwidth="0" height="280" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
        $.blockUI({
            message: $('#windowForm'),
            css:{
                width: '540px',top:'20%'
            }
        });
        $('#set_filename').val(fildname);
        $('.blockOverlay').attr('title','点击解除封锁').click($.unblockUI);
    }
    
   function upload_mu_img(fildname){	
  
$('#windowContent').html('<iframe src="./plugin/SWFUpload/upimg.php" width="500" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');	
                $("#windowtitle").html('证件上传');
var client_with=$(window).width();
var left=(client_with-500)/2;
$.blockUI({ 
message: $('#windowForm'),
css:{ 
                width: '540px',height:'470px',top:'5%',left:left+"px"
            }
});
//$('#windowForm').css("width","500px");
        $('#set_three_filename').val(fildname);
$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
}
        
    function set_mu_img(arrUploadinfo){

var threeSid = $('#set_three_filename').val();
var upLi = '',
tsBox = $('#'+threeSid),
inputBox = $('#'+threeSid+'_yt');
inputBox.val(JSON.stringify(arrUploadinfo));



if(arrUploadinfo.length){
for(var i in arrUploadinfo){
upLi += '<li>';
upLi += '<img src="'+(arrUploadinfo[i]['realpath'].replace('thumb_', 'img_'))+'" onclick="window.open(this.src);" title="点击查看大图" alt="点击查看大图" />';
upLi += '</li>';
}
console.log(upLi);
tsBox.html(upLi);
}

$('#set_three_filename').val('');
$.unblockUI();
}

function do_threeside_upload() {
        $('#editthreeinfo').attr("disabled","disabled");

      //验证输入
        var bs_three_yitong = $("#three_silds_box_yt").val();
        var bs_three_merchant = $("#three_merchant_box_yt").val();
        
$.post("do_system.php",
{m:"set_three_mu_img",'upThreeData':bs_three_yitong, 'upMerchantData':bs_three_merchant},
function(data){
data = Jtrim(data);
            if(data == 'ok') {
                $.blockUI({
                    message : '<p>资料上传成功!</p>'
                });
                window.location.reload();
            } else {
                $.blockUI({
                    message : '<p>数据保存失败,请重试!</p>'
                });
                $('#editthreeinfo').removeAttr("disabled","disabled");
            }
}	
);
}
        function buttonComit(){
        var filename1  = new Array();
        var count1=$('#hiddenmer').prev('ul').children('li').length;//医统数量
        for(n=0;n<count1;n++)
        {
            if($('#hiddenmer').prev('ul').children('li').eq(n).children('img').attr('src')=='./images/default_zj.jpg')
            {
                $.growlUI('请上身份证传图片!');
                $('#hiddenmer').prev('ul').children('li').eq(n).children('img').attr('src')==' ';
            }else{
                filename1.push($('#hiddenmer').prev('ul').children('li').eq(n).children('img').attr('src'));   //非医统
            }
        }
            if(filename1.length == 0){
               $.growlUI('请上传身份证件图片!');
            }else if($("#BusinessCard").val()==""){
                $.growlUI('营业执照号码不能为空!');
            }else if($("#BusinessCardImg").val()==""){
                $.growlUI('请上传营业执照图!');
            }else if($("#IDLicenceImg").val()==""){
                $.growlUI('请上传药品经营许可证图片!');
            }else if($("#IDLicenceCard").val()==""){
                $.growlUI('药品经营许可证号码 不能为空!');
            }else if($("#ClientTrueName").val()==""){
            $.growlUI('企业名称 不能为空');
            }else if($("#IDCardImg").val()==""){
                $.growlUI('请上传身份证图片!');
            }else if($("#TureUserName").val()==""){
                $.growlUI('姓名 不能为空!');
            }else if($("#UserPhone").val()==""){
                $.growlUI('手机 不能为空!');
            }else if($("#IDCard").val()==""){
                $.growlUI('身份证号 不能为空!');
            }else{
$.post("my.php",
{m:"addQualification", BusinessName: $('#BusinessName').val(),TureUserName:$('#TureUserName').val(),UserPhone:$('#UserPhone').val(),IDCard:$('#IDCard').val(),BusinessCard:$('#BusinessCard').val(),BusinessCardImg:$('#BusinessCardImg').val(),GPImg: $('#GPImg').val(),IDLicenceImg:$('#IDLicenceImg').val(),IDCardImg:filename1,SanBusinessCard:$('#SanBusinessCard').val(),SanBusinessCardImg:$('#SanBusinessCardImg').val(),GPCard:$('#GPCard').val(),IDLicenceCard:$('#IDLicenceCard').val()},
function(data){
if(data == "ok"){						
$.growlUI('提交成功，正在载入页面...');
                                        setTimeout(a, 500);					
                                        function a(){  
                                            window.location.reload();  
                                        } 
}else{
$.growlUI(data);
}
}			
);
}
window.setTimeout("hideshow('tip')",20000);
        }
        
//        function commitfile()
//        {
////            var val=$('img').attr('src');
//            var filename  = new Array();
//            var filename1  = new Array();
//            var count=$('#hiddenbox').prev('ul').children('li').length;//医统数量
//            for(i=0;i<count;i++)
//            {
//                if($('#hiddenbox').prev('ul').children('li').eq(i).children('img').attr('src')=='./images/default_zj.jpg')
//                {
//                     $.growlUI('请上传图片!');
//                     $('#hiddenbox').prev('ul').children('li').eq(i).children('img').attr('src')==' ';
//                }else{
//                    filename.push($('#hiddenbox').prev('ul').children('li').eq(i).children('img').attr('src'));   //医统
//                }
//                
//            }
//            //非医统数量
//             var count1=$('#hiddenmer').prev('ul').children('li').length;//医统数量
//             for(n=0;n<count1;n++)
//            {
//                if($('#hiddenmer').prev('ul').children('li').eq(n).children('img').attr('src')=='./images/default_zj.jpg')
//                {
//                     $.growlUI('请上传图片!');
//                     $('#hiddenmer').prev('ul').children('li').eq(n).children('img').attr('src')==' ';
//                }else{
//                    filename1.push($('#hiddenmer').prev('ul').children('li').eq(n).children('img').attr('src'));   //非医统
//                }
//             }
//           if(filename.length == 0){
//               $.growlUI('请上传医统天下合作协议!');
//           }else if(filename1.length == 0){
//               $.growlUI('商业公司自有合作协议不能为空!');
//           }else if($('#hiddenbox').prev('ul').children('li').eq(0).children('img').attr('src')!=' '&&$('#hiddenmer').prev('ul').children('li').eq(0).children('img').attr('src')!=' '){
//               		$.post("my.php",
//			{m:"addSide", FileName: filename,FileName1:filename1},
//			function(data){
//				if(data == "ok"){						
//					$.growlUI('提交成功，正在载入页面...');
//					alert('操作成功!');
//					window.location.reload();
//				}else{
//					$.growlUI(data);
//				}
//			}			
//			);
//           }
//        }
</script>