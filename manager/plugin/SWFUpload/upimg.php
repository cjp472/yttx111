<?php
include_once ("../../common.php");
if(empty($_SESSION['uinfo']['userid']) || empty($_SESSION['uc']['CompanyID'])) exit('请重新<a href="/" target="top">登陆</a>!');

unset($_SESSION["file_info"]);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>多文件上传 </title>
<link href="./css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./swfupload/swfupload.js?v=06291"></script>
<script type="text/javascript" src="./js/handlers.js?v=06291"></script>
<script type="text/javascript">
		var swfu;
		window.onload = function () {
			swfu = new SWFUpload({
				// Backend Settings
				upload_url: "upload.php",
				post_params: {"PHPSESSID": "<?php echo session_id(); ?>"},

				// File Upload Settings
				file_size_limit : "8 MB",	// 2MB
				file_types : "*.jpg",
				file_types_description : "JPG Images",
				file_upload_limit : "0",

				// Event Handler Settings - these functions as defined in Handlers.js
				//  The handlers are not part of SWFUpload but are part of my website and control how
				//  my website reacts to the SWFUpload events.
				file_queue_error_handler : fileQueueError,
				file_dialog_complete_handler : fileDialogComplete,
				upload_progress_handler : uploadProgress,
				upload_error_handler : uploadError,
				upload_success_handler : uploadSuccess,
				upload_complete_handler : uploadComplete,

				// Button Settings
				button_image_url : "images/SmallSpyGlassWithTransperancy_17x18.png",
				button_placeholder_id : "spanButtonPlaceholder",
				button_width: 150,
				button_height: 18,
				button_text : '<span class="button"> 选择您要上传图片 (JPG)</span>',
				button_text_style : '.button { font-family: "微软雅黑", Arial, Helvetica, sans-serif, "宋体"; font-size: 12pt; } .buttonSmall { font-size: 12pt; }',
				button_text_top_padding: 0,
				button_text_left_padding: 18,
				button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
				button_cursor: SWFUpload.CURSOR.HAND,
				
				// Flash Settings
				flash_url : "./swfupload/swfupload.swf",

				custom_settings : {
					upload_target : "divFileProgressContainer"
				},
				
				// Debug Settings
				debug: false
			});
		};
	</script>
<script type="text/javascript">	
	function goNextImg() {
		parent.set_mu_img(arrUploadinfo);
		parent.closewindowui();
	}
</script>
</head>
<body>
<div id="content">

	<?php
	if( !function_exists("imagecopyresampled") ){
		?>
	<div class="message">
		<h4><strong>注:</strong> </h4>
		<p>您的系统不支持上传图片所需组件，请与系统管理员联系.</p>
	</div>
	<?php
	} else {
	?>
	<form>
		<div style="display: inline; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px; margin-top:8px;">
			<span id="spanButtonPlaceholder"></span>
		</div>
		<div style="display: inline; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px; margin-top:8px; margin-left:12px; ">
			<span id="spanButtonPlaceholder3"> ...  </span>			
		</div><br />
		<p>（注：按住"Crtl"键, 可以一次选择多张图片,<br />建议图片尺寸宽度在800像素以内，大小在2M以内）</p>
	</form>
	<?php
	}
	?>
	<div id="divFileProgressContainer" style="height: 75px;"></div>
	<div id="thumbnails" style="overflow:auto; width:468px; height:300px;"></div>
</div>
</body>
</html>