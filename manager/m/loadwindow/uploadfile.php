<?  header('Content-Type: text/html; charset=utf-8'."\n"); ?>
<script type="text/javascript">
$(document).ready(function(){
	$("#example3").jqUploader({background:	"FFFFDF",barColor:	"FF00FF"});
});
</script>
<link rel="stylesheet" href="../../plugin/jqUploader/style.css" />
  <form enctype="multipart/form-data" action="../plugin/jqUploader/flash_upload.php" method="POST" class="a_form">
    <fieldset>
    <legend>上传图片：</legend>
    <ol>
      <li id="example2">
        <label for="example2_field">请先选择您要上传的图片:</label>
        <input name="myFile2" id="example2_field"  type="file" />
      </li>
    </ol>
    </fieldset>
    <input type="submit"  name="submit" value="Upload File" />
  </form>
  