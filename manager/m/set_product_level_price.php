<?php 
$menu_flag = "product";
$pope	       = "pope_form";
include_once ("header.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">
function save_product_level_price()
{
	$.blockUI({ message: "<p>正在执行，请稍后...</p>" });
	$('#buttonset').attr("disabled","disabled");
	$.post("do_product.php?m=set_save_level_price", $("#MainForm").serialize(),
		function(data){
			if(data != "")
			{					
				parent.set_input_price(data);
			}
		});
	closewindowui();
}
</script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p,strong{color:#333333; font-size:12px; line-height:150%; font-family: "微软雅黑", "宋体",Arial, Helvetica, sans-serif !important; font-family: "宋体",Arial, Helvetica, sans-serif;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:25px; padding:2px;}
.tc tbody tr td{ background: #ffffff; font-weight:bold; height:25px; padding:2px;}
.tcheader{font-weight:bold; background: #efefef; height:25px; padding:2px;}

.button_1{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/anns.jpg) 0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_1:hover {background:url(./img/anns.jpg) 0 -26px no-repeat;}

.button_2{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/dnn5.jpg)  0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_2:hover {background:url(./img/dnn5.jpg) 0 -26px no-repeat;}

input{font-weight:bold; font-size:12px;font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333;}
form{margin:0; padding:0;}
-->
</style>
</head>

<body>
<?
	if(!empty($in['vmsg']))
	{
		$parr = unserialize(urldecode($in['vmsg']));
		if(empty($parr['typeid'])) $parr['typeid'] = 'A';
	}

	$valuearr = get_set_arr('clientlevel');
	if(empty($valuearr))
	{
?>
<div >

          <table width="90%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
                 	<td align="center"><br /><br /><font color="red">注：您还没有设置您的药店级别！<br />请先在系统设置里-><a href="client_level.php" target="_parent">药店级别设置</a>中设置</font></td>
     			 </tr>
          </table>
</div>
<? }else{ ?>

<div >
<?
			$valuearr1 = $valuearr;
			if(count($valuearr1, COUNT_RECURSIVE) == count($valuearr1))
			{
				$levelarr['A']              = $valuearr1;
				$levelarr['A']['id']        = "A";
				$levelarr['A']['name']  = "方式A";
				$levelarr['isdefault']   = "A";

			}else{
				$levelarr = $valuearr1;
			}

		if(empty($in['vmsg']) && !empty($levelarr['isdefault']) && empty($in['id']))
		{
			$valuearr = $levelarr[$levelarr['isdefault']];
			$in['id']    = $levelarr['isdefault'];
		}else{
			if(empty($in['id']))
			{
				if(!empty($parr['typeid']))
				{
					$in['id']    = $parr['typeid'];
					$valuearr = $levelarr[$parr['typeid']];
				}else{
					if(!empty($levelarr['A']))
					{
						$valuearr = $levelarr['A'];
						$in['id']    = 'A';
						$parr['typeid'] = 'A';
					}else{
						$in['id'] = $levelarr['isdefault'];
						$valuearr = $levelarr[$in['id']];
					}
				}
			}else{
				$valuearr = $levelarr[$in['id']];
			}
		}

?>
		<table width="100%" border="0" cellspacing="4" cellpadding="0">
     		<form name="changetypeform" id="changetypeform" action="set_product_level_price.php" method="get">
				<input name="vmsg" id="vmsg" type="hidden" value='<? if(!empty($in['vmsg'])) echo $in['vmsg'];?>' />
				 <tr>
					<td align="left" width="70"><strong>分级方式：</strong></td>
                 	<td align="left">					
					<select name="id" id="id" onchange="javascript:submit();" style="width:170px; margin:2px; padding:2px;">
						<option value="" selected="selected">⊙ 请选择分级方式&nbsp;&nbsp;</option>
						<? 
						  foreach($levelarr as $keys=>$vars)
						  {
							  if($keys=="isdefault") continue;
							  if(!empty($vars['id']))
							  {
									if($in['id'] == $vars['id'])
								    {
										echo '<option value="'.$vars['id'].'" selected="selected">&nbsp;&nbsp; '.$vars['name'].' </option>';
								    }else{
										echo '<option value="'.$vars['id'].'">&nbsp;&nbsp; '.$vars['name'].' </option>';
								    }
							  }
						  }
						?>
					</select>	&nbsp;&nbsp;<font color="red">(注：一个商品只能设置一种等级价格)</font>	
					</td>
     			 </tr></form>
          </table>
          <form id="MainForm" name="MainForm" method="post" action="" target="" >
        	  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC" class="tc">               
                <thead>
				<tr>
				  <td width="12%" class="tcheader" align="center"><strong>ID</strong></td>
				  <td width="30%" class="tcheader">&nbsp;<? if(empty($valuearr['name'])) echo "级别"; else echo $valuearr['name'];?><input type="hidden" name="typeid" id="typeid" value="<? echo $valuearr['id'];?>" /></td>
				  <td  class="tcheader">&nbsp;价格(元)</td>
                </tr>
				</thead>
				<tbody>
				<?
				if(!empty($valuearr))
				{
					foreach($valuearr as $key=>$var)
					{
						if($key=="id" || $key=="name") continue;
						if(!empty($parr[$key])) $pvmsg = 'value="'.$parr[$key].'"'; else $pvmsg ='';
						if($parr['typeid'] != $in['id']) $pvmsg = '';
						echo '<tr><td align="center" class="tcheader">'.substr($key,6).'</td><td >&nbsp;'.$var.'</td><td>&nbsp;<input type="text" name="level_'.substr($key,6).'" id="level_'.substr($key,6).'" onKeypress="if ((event.keyCode < 48 || event.keyCode > 57) && event.keyCode!=46) event.returnValue = false;" maxlength="10" tabindex="'.substr($key,6).'" onfocus="this.select();" style="width:150px;" '.$pvmsg.'  /></td></tr>';
					}
				}
				?>
				</tbody>
              </table>			
              </form>
       	  </div>

          <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
					<td align="center">&nbsp;&nbsp;</td>
					 <td height="35" width="280"><label>
                 	   <input type="button" name="buttonset" id="buttonset" value=" 提 交 " class="button_1" onclick="save_product_level_price();" />
                 	 </label>                 	   
               	     &nbsp;&nbsp;
               	     <label>
               	     <input type="button" name="button2" id="button2" value=" 返 回 " class="button_2"  onclick="parent.closewindowui()" />
               	     </label></td>       			     
     			 </tr>
          </table>
 <? }?>     
</body>
</html>