<?php
include_once ("../../common.php");
//上传证件专用
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>jqUploader</title>
    <link rel="stylesheet" type="text/css" media="screen" href="style.css"/>
    <script type="text/javascript" src="jquery-1.2.1.min.js"></script>
    <script type="text/javascript" src="jquery.flash.js"></script>
    <script type="text/javascript" src="jquery.jqUploader.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){

            $("#example3").jqUploader({
                afterScript:"../../m/do_upload.php?m=upload_certify",
                background:	"FFFFDF",
                barColor:	"64A9F6",
                allowedExt:     "*.gif; *.jpg; *.jpeg; *.png;",
                allowedExtDescr: "图片 (*.gif; *.jpg; *.jpeg; *.png)"
            });

        });
    </script>
</head>
<body>
<form enctype="multipart/form-data" action="flash_upload.php?PHPSESSID=<?php echo session_id(); ?>" method="POST" class="a_form">
    <fieldset>
        <legend>上传图片</legend>
        <ol>
            <li id="example3">
                <label for="example3_field">请选择您要上传的文件:</label>
                <input name="myFile3" id="example3_field"  type="file" />
            </li>
        </ol>
    </fieldset>
</form>
</body>
</html>