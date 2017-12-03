<?
/**
 * Class 错误处理
 *
 * @author seekfor seekfor@gmail.com
 */

class Error {

	function Back($msg)
	{
		$delay = 3;
		switch(Error_Display)
		{
			case "js":
			{
				echo '<script language="javascript">
				 alert("'.$msg.'");
				 history.go(-1);
				 </script>
				 ';
				exit();
			}
			case "html":
			{
				echo '<meta http-equiv="refresh" content="'.$delay.';url=javascript:history.go(-1)">
				<link href="template/css/main.css" rel="stylesheet" type="text/css">
				<body bgcolor="#efefef"><br><br>
				<div align=center>
					<div  align=center class="warning">
						<div align=left width=80%> 
      						 <span class=content14_r><img src="template/img/s_warn.png">&nbsp;  系统提示：'.$msg.'</span><br /><br /><span >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;如果您的浏览器没有自动跳转  <a href="javascript:history.back(1)">按这里返回</a> </span><br>
						</div>
     				</div>
				</div>
			 </body>';
				exit();
			}
		}
	}

	function Jump($msg, $tourl)
	{
		$delay = 1;
		switch(Error_Display)
		{
			case "js":
			{
				echo '<script language="javascript"> 
				alert("系统提示： '. $msg . '"); 
				location.href="'.$tourl.'"; 
				</script>
				';
				exit();
			}
			case "html":
			{

				echo '<meta http-equiv="refresh" content="'.$delay.';url='.$tourl.'">
				<link href="template/css/main.css" rel="stylesheet" type="text/css">
<body bgcolor="#efefef"><br><br>
<div align=center>
	<div  align=center class="warning">
			<div align=left width=80%> 
       <span class=content14_r><img src="template/img/s_warn.png">&nbsp;  系统提示： '.$msg.'</span><br><br><span class=content14>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   如果您的浏览器没有自动跳转  <a href="'.$tourl.'">按这里返回</a></span> <br>
			</div>
     </div>
</div>
</body>';
				exit();
			}
		}
	}


	function GoToUrl($tourl)
	{
		header("Location:".$tourl."");
		exit;
	}
	
	function JumpJs($msg, $tourl)
	{
		
				echo '<script language="javascript"> 
				alert("系统提示： '. $msg . '"); 
				location.href="'.$tourl.'"; 
				</script>
				';
				exit();
	}

	function AlertJs($msg)
	{
		
				echo '<script language="javascript"> 
				alert("系统提示： '. $msg . '"); 
				</script>
				';
				exit();
	}
	
	function Alert($msg, $frame)
	{
		
		echo '<script language="javascript">
			alert(\'' . $msg . '\');
			top.window.location.reload();
			</script>
		';
	exit();
	}
	
	function AlertParent($msg, $frame)
	{
		
		echo '<script language="javascript">
			//alert(\'' . $msg . '\');
			parent.' . $frame . '.location.reload();
			</script>
		';
	exit();
	}
	
	function Confirm($msg, $tourl)
	{
		
		echo '<script language="javascript">
			if(confirm(" ' . $msg . '")){
			window.location="' . $tourl . '";
							}else {
								
							}
							</script>
		';
		exit();
	}	

	function Halt($msg, $mode = 0, $setFunction='')
	{
		
		if($mode == 1)
		{
			echo '<script language="javascript">
				alert(\'' . $msg . '\');
				'.$setFunction.'
			</script>
			';
		exit();
		}
		
		echo '<script language="javascript">
				alert(\'' . $msg . '\');
				'.$setFunction.'
				history.go(-1);
			</script>
			';
		exit();
	}

	function AlertSet($setFunction='')
	{
		
		echo '<script language="javascript">
			'.$setFunction.'
			</script>
		';
	exit();
	}

	function outAdmin($msg, $tourl)
	{
		
		echo '<script language="javascript">
			alert(\'' . $msg . '\');
			top.location.href="'.$tourl.'";
			</script>
		';
	exit();
	}
	
}
?>