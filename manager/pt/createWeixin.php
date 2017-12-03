<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>二维码生成</title>
</head>
<style type="text/css">
    .createWeixin {
        /*position: absolute;*/
        /*width: 1000px;*/
        /*height: 1000px;*/
        /*top: 300px;*/
        /*left:300px;*/
        margin-top:200px;
    }

</style>
<body>
<div id="createWeixin" class="createWeixin" align="center"></div>
</body>
<script type="text/javascript" src="../scripts/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../plugin/qrcode/jquery.qrcode-0.12.0.js"></script>
<script type="text/javascript" src="js/function.js"></script>
<script type="text/javascript">
    $(function(){
        var value= window.location.href;
        var param = $_GET(value,'shtml?');
        var size = param.size;
        var text = param.text;
        $("#createWeixin").qrcode({
            // render method: 'canvas', 'image' or 'div'
            render: 'image',
            "size": size,
            "color": "#3a3",
            "text": text
        });
    });
</script>
</html>