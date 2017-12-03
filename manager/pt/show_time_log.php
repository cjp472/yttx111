<?php 
$menu_flag = "manager";
include_once ("header.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<title>时间线</title>
<style type="text/css">
	body{ background-color:#f3f3f3; margin:0; padding:0;}
	a ,body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, form, fieldset, input,textarea, p, a, blockquote, th,table,td,tr,h1{ font-size:12px; font-family:"微软雅黑",Arial, Helvetica, sans-serif; color:#343434;}
	.content1{ width:555px; margin:0 auto; margin-top:25px; position:relative; height:auto;}
	.insert_record{ width:400px; height:50px; border:1px solid #cbcbcb; border-radius:5px; font-size:12px; float:left;}
	.insert_btn{ width:104px; height:37px; border:1px solid #0e86dc; border-radius:5px; background-color:#51b2f7; color:#fff; font-family:"微软雅黑"; margin-left:25px; cursor: pointer; margin-top:10px;float:left;}
	.content1 dl dt{ width:100%; float:left}
	.clock{ width:36px; height:36px; background-image:url(img/clock_img.png); background-position:0 0; background-repeat:no-repeat; overflow:hidden; float:left; margin-left:5px;}
	.years{ float:left; font-size:14px; font-weight:bold; color:#3ec1ec; line-height:36px; height:36px; width:59px;overflow:hidden;}
	.mounth{ float:left; font-size:14px; font-weight:bold; color:#3ec1ec; line-height:36px; margin-left:30px;_margin-left:15px;height:36px; width:35px; overflow:hidden; display:block}
	.dian{ width:14px; height:14px; background-image:url(img/clock_img.png); background-position:-12px -38px; background-repeat:no-repeat; overflow:hidden; float:left; margin-top:11px;margin-left:10px;}
	.mounth_info{ width:100%;}
	.mounth_left{ width:100px; float:left;overflow:hidden}
	.mounth_right{ width:449px; float:left;border-left:1px solid #51b2f7; margin-left:-19px; padding-left:19px;overflow:hidden}
	.mounth_right ul{ list-style:none; margin-left:-30px;*margin-left:10px; margin-bottom:55px;}
	.mounth_right ul li{ width:420px; border:1px solid #cbcbcb; border-radius:5px; background-color:#fff; min-height:75px; height:auto; margin-bottom:15px;}
	.mounth_right ul li h1{ font-size:12px; font-weight:bold; color:#3ec1ec; padding-left:10px; line-height:20px; height:20px; display:block; margin-top:3px; margin-bottom:3px;}
	.mounth_right ul li p{ line-height:20px; margin-left:10px; margin-top:0px; height:auto; display:block; width:449px; overflow:hidden}
	.chaozuo{ color:#adadad; margin-left:5px;}
	.times{ color:#adadad; margin-left:5px;}
    
	
</style>
<script type="text/javascript">
//代理商
function do_save_log()
{
	if($('#Content').val()=="")
	{
		$.blockUI({ message: "<p>请输入内容!</p>" });
	}else{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_manager.php?m=save_company_log",$("#formrecord").serialize(),
			function(data){
				if(data == "ok"){
					$.blockUI({ message: "<p>保存成功!</p>" });
					window.setTimeout(window.location.reload(), 3000);
					$('.blockOverlay').attr('title','点击返回!').click(window.location.reload());
				}else{
					$.blockUI({ message: "<p>"+data+"</p>" });
					$('.blockOverlay').attr('title','点击返回修改!').click($.unblockUI);
				}				
			}		
		);
	}
	$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
	window.setTimeout($.unblockUI, 5000);
}
</script>
</head>

<body>
<!--添加记录-->
	<div class="content1">
    	<form id="formrecord" name="formrecord" method="post" action="do_manager.php?m=save_company_log">
		<input name="CompanyID" id="CompanyID" type="hidden" value="<?php echo $in['ID'];?>" />
		<textarea name="Content" id="Content" type="text" class="insert_record" ></textarea>
        <input name="submit" type="button"  value="添加记录" onclick="do_save_log();" class="insert_btn" />
		</form>
    </div>
<!--时间轴-->
<?php
$sql = "select YEAR(FROM_UNIXTIME(CreateDate)) as Ydate,MONTH(FROM_UNIXTIME(CreateDate)) as Mdate,CreateDate,CreateUser,Content from ".DATABASEU.DATATABLE."_order_company_log where CompanyID=".intval($in['ID'])." order by LogID desc limit 0,1000";
$data = $db->get_results($sql);

foreach($data as $v){
	$ydata[$v['Ydate']][$v['Mdate']][] = $v;
}

?>
    <div class="content1" >
    	<?php
		$yy = date('Y');
		foreach($ydata as $yk=>$yv)
		{			
		?>
		<dl >
			<dt style="margin-top:10px;"><div class="years"><?php echo $yk;?>年 ·</div><div class="clock"></div></dt>

			<dt>
            <?php 
			foreach($yv as $mk=>$mv)
			{

			?>
            	<div class="mounth_info">
                	<div class="mounth_left">
                    	<div class="mounth"><?php echo $mk;?>月</div><div class="dian"></div>
                    </div>
                    <div class="mounth_right">
                    	<ul>
                        	<?php
							foreach($mv as $dk=>$dv)
							{
							?>
							<li>
                            	<h1><?php echo date("d 日 H:i",$dv['CreateDate']);?><span class="chaozuo"> <?php echo $dv['CreateUser'];?> </span> </h1>
                                <p>
                                
								<?php echo nl2br($dv['Content']);?>
                                </p>
                            	
                            </li>
							<? }?>
                        </ul>                    	
                    </div>               
                </div>
			<?php }?>

               
                </div>
            </dt>
            <dt>
            	
            </dt>
        </dl>
		<?php }?>
	</div>
</body>
</html>