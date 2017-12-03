<?php 
$menu_flag = "manager";
include_once ("header.php");

if($_SESSION['uinfo']['userid'] != "1" && $_SESSION['uinfo']['userid'] != "9") exit('非法路径!');

$cid = intval($in['cid']);
$csql   = "SELECT CompanyID,CompanyDatabase FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$cid." ORDER BY CompanyID ASC limit 0,1";
$cominfo = $db->get_row($csql);
if(!empty($cominfo['CompanyDatabase']))
{
	$sdbname = DB_DATABASE.'_'.$cominfo['CompanyDatabase'].".";
}else{
	$sdbname = DB_DATABASE.'.';
}


	$status_arr = array(
		'0'			=>  '上架',
		'1'			=>  '下架'
 	 );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<link rel="stylesheet" href="css/showpage.css" />
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
.tc thead tr td{font-weight:bold; background: #efefef; height:28px; padding:2px;}
.tc tbody tr td{ background: #ffffff; font-weight:bold; height:28px; padding:2px;}
.tcheader{font-weight:bold; background: #efefef; height:25px; padding:2px;}
input{font-weight:bold; font-size:12px;font-family: Verdana, Arial, Helvetica, sans-serif; color:#333333;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}


.button_1{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/anns.jpg) 0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_1:hover {background:url(./img/anns.jpg) 0 -26px no-repeat;}

.button_3{ width:72px; height:24px; line-height:22px; font-weight:bold; color:#fff; border:0; margin:5px 0 0 5px; background:url(./img/dnn5.jpg)  0 0 no-repeat; cursor: pointer;margin-right:10px;}
.button_3:hover {background:url(./img/dnn5.jpg) 0 -26px no-repeat;}

.bottomlinebold{border-bottom:#CCCCCC solid 2px; font-weight:bold; height:32px;}
.bottomlinebold td{border-bottom:#CCCCCC solid 2px; font-weight:bold; height:32px;}
.bottomline td{border-bottom:#CCCCCC dotted 1px; height:auto; padding:4px 0; line-height:28px;}
.bottonline input{border:0;}
.sublink ul{float:left; list-style:none; margin:0; padding:0;}
.sublink ul li{background: #0774bc; border:#0774bc 1px solid;  color: #fff; font-size: 12px; padding:1px; height:20px; line-height:20px; float:left; width:65px; margin:0 4px; text-align:center;}
.sublink ul li a{text-decoration:none; color:#fff; display:block;  font-size:12px;}
.sublink ul li a:hover{text-decoration:none; color:#fff; display:block;  font-size:12px; }

-->
</style>
<script type="text/javascript">
	$(function() {
		$("#begindate").datepicker();
		$("#enddate").datepicker();
	});

	var old_bg="";
	function inStyle(obj)
	{
		old_bg=obj.style.background;
		obj.style.background="#edf3f9";
	}
	function outStyle(obj)
	{
		obj.style.background=old_bg;
	}

	function delete_data(did)
	{
		if(confirm('确认删除吗?'))
		{
			var cid = "<?php echo $cid;?>";
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_delete.php",
				{m:"delete_product", cid: cid, did:did},
				function(data){
					if(data == "ok"){
						var delline = "line_" + did;
						$("#"+delline).hide();
						$.blockUI({ message: "<p>删除成功!</p>" }); 
					}else{
						$.blockUI({ message: "<p>"+data+"</p>" }); 
					}					
				}		
			);
		}else{
			return false;
		}
		window.setTimeout($.unblockUI, 1500); 
	}

	function delete_mul_data()
	{
		if(confirm('确认删除吗?'))
		{
			var cid = "<?php echo $cid;?>";
			$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
			$.post("do_delete.php?m=delete_product", $("#MainForm").serialize(),
				function(data){
					if(data == "ok"){
						$.blockUI({ message: "<p>删除成功!</p>" });
						window.location.reload();
						$('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
					}else{
						$.blockUI({ message: "<p>"+data+"</p>" });
						$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					}				
				}		
			);
			$('.blockOverlay').attr('title','点击返回').click($.unblockUI);
			window.setTimeout($.unblockUI, 1500);
		}
	}
</script>
</head>

<body>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="tc" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="show_product_data.php">			  
        		<input type="hidden" name="cid" id="cid2"  value="<? if(!empty($cid)) echo $cid;?>"   />
				<thead>
				<tr>
					<td width="80" align="center"><strong>商品搜索：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" value="<? if(!empty($in['kw'])) echo $in['kw'];?>"  onfocus="this.select();" style="width:200px;" /></td>
        	        <td width="80">
        	        <select name="stype" id="stype" class="selectline">
						<option value="coding" <?php if($in['stype']=="coding") echo 'selected="selected"'; ?> >编号</option>
						<option value="barcode" <?php if($in['stype']=="barcode") echo 'selected="selected"'; ?> >条码</option>
						<option value="name" <?php if($in['stype']=="name") echo 'selected="selected"'; ?> >名称</option>
						<option value="idsn" <?php if($in['stype']=="idsn") echo 'selected="selected"'; ?> >ID号</option>
					</select>
					</td>
        	        <td width="80">
        	        <select name="ostatus" id="ostatus" class="selectline">
						<option value=""  >状态</option>
						<?php
						foreach($status_arr as $key=>$var)
						{
							if(isset($in['ostatus']) && $in['ostatus']!='')
							{
								if($in['ostatus'] == $key) $smsg = 'selected="selected"'; else $smsg ="";
							}else{
								$smsg =" ";
							}
							echo '<option value="'.$key.'" '.$smsg.' >'.$var.'</option>';
						}
						?>						
					</select>
					</td>

       				<td width="60"><input name="searchbutton" type="submit" class="redbtn" id="searchbutton" value="搜 索" /> </td>
					<td></td>
				</tr>
				</thead>
   	          </form>
			 </table> 
			 

		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			<input type="hidden" name="cid" id="cid"  value="<? if(!empty($cid)) echo $cid;?>"   />
        	 <div style="width:100%; height:590px; overflow:auto; margin:0; padding:0;">
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
				  <td width="8%" class="bottomlinebold" >&nbsp;行号</td>
                  <td width="8%" class="bottomlinebold" >&nbsp;&nbsp;ID</td>
				  <td width="20%" class="bottomlinebold">编号</td>
                  <td  class="bottomlinebold">名称</td>
                  <td width="14%" class="bottomlinebold" >条码</td>
				  <td width="6%" class="bottomlinebold" >状态</td>
                  <td width="6%" class="bottomlinebold" nowrap="nowrap">&nbsp;管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php	
	$pagearr['cid'] = $cid;
	$sqlmsg = '';

	if(!empty($in['kw'])){	
		$in['kw'] = str_replace(' ',',',$in['kw']);
		if($in['stype']=='idsn'){			
			$sqlmsg .=  " AND ID IN(".$in['kw'].")";
		}elseif($in['stype']=='coding'){
			$in['kw'] = str_replace(",","','",$in['kw']);
			$sqlmsg  .=  " AND Coding IN ('".$in['kw']."') ";
		}elseif($in['stype']=='barcode'){
			$in['kw'] = str_replace(",","','",$in['kw']);
			$sqlmsg  .=  " AND Barcode IN ('".$in['kw']."') ";
		}else{
			$in['kw'] = str_replace(",","%",$in['kw']);
			$sqlmsg  .=  " AND Name like '%".$in['kw']."%' ";
		}
	}
	if(isset($in['ostatus']) and $in['ostatus'] != ''){
		$sqlmsg .= ' and FlagID = '.$in['ostatus'].' ';
		$pagearr['ostatus'] = $in['ostatus'];
	}


	$sqlnum = "SELECT count(*) AS allrow FROM ".$sdbname.DATATABLE."_order_content_index where CompanyID = ".$cid." ".$sqlmsg." ";
	$datasql   = "SELECT * FROM ".$sdbname.DATATABLE."_order_content_index where CompanyID = ".$cid." ".$sqlmsg." Order by ID Desc";

	$InfoDataNum = $db->get_row($sqlnum);
	$page        = new ShowPage;
    $page->PageSize = 50;
    $page->Total    = $InfoDataNum['allrow'];
    $page->LinkAry  = $pagearr;        
	
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td >&nbsp;<span class="selectinput"><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['ID'];?>" value="<? echo $lsv['ID'];?>" /></span>&nbsp;<? echo $n++;?></td>
				  <td height="28" >
					<span ><? echo $lsv['ID'];?></span>
				  </td>
				  <td><? echo $lsv['Coding'];?></td>
                  <td >		
						<span><? echo $lsv['Name'];?></span>
				  </td>

                  <td >
				  <?php 
				  echo "<span>".$lsv['Barcode']."</span> ";
				  ?>
					</td>
                  <td >
					<? if(empty($lsv['FlagID'])) echo '上架'; else echo '下架';?>			
				  </td>
				  <td>[<a href="javascript:void(0)" onclick="delete_data('<?php echo $lsv['ID'];?>');">删除</a>]</td>
                </tr>
	<? } }else{?>
     			 <tr>
       				 <td colspan="5" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
	<? }?>
 				</tbody>                
              </table>
			  </div>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			   <td width="4%"   height="30" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="8%" >全选/取消</td>
   			       <td class="sublink"><ul><li><a href="javascript:void(0);" onclick="delete_mul_data();" >删除</a></li>
				   </ul></td>
				   <td  align="right"><? echo $page->ShowLink('show_product_data.php');?></td>
     			 </tr>
              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

</body>
</html>