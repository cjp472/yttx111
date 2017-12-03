<?php 
$menu_flag = "product";
$pope	   = "pope_view";
include_once ("header.php");

unset($_SESSION['file_upinfo']);
$_SESSION['file_upinfo'] = null;

if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}

$producttypearr = array(
		'0'			=>  '',		
		'1'			=>  '<font color=purple>[荐]</font>',
		'2'			=>  '<font color=#ff6600>[特]</font>',
		'3'			=>  '<font color=#35ce00>[新]</font>',
		'4'			=>  '<font color=#ff0000>[热]</font>',
		'8'			=>  '<font color=#000000>[赠]</font>',
		'9'			=>  '<font color=#00b2fc>[缺]</font>'
 	 );

setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/core.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/events.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/css.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/coordinates.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/drag.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/dragsort.js"></script>
<script language="JavaScript" type="text/javascript" src="../plugin/tool-man/cookies.js"></script>

<script type="text/javascript">
		var dragsort = ToolMan.dragsort();
		var junkdrawer = ToolMan.junkdrawer();

		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		});

		function saveImgOrder(item) {
			var group = item.toolManDragGroup;
			var list = group.element.parentNode;
			var idtemp = list.getAttribute("id");

			if (!idtemp){
				return;
			}else{
				group.register('dragend', function() {
					var id = idtemp.substring(8);

					if(id){
						$.post("do_product.php",
							{m:"set_list_img_order", idname:id,'sortdata':junkdrawer.serializeList(list)},
							function(data){
								$('#listpic_'+id+' li').each(function(n){
									/* 2015/07/01 修正一下BUG */
									var sSayYesNo = $(this).find('input:radio[name=DefautlImg_'+id+']').is(':checked') ? 'Y' : 'N';
									var changeNum = n+1;
									var sNumID = id+"_"+changeNum;

									$(this).attr('id','mu_img_'+sNumID);
									$(this).find('input:radio[name=DefautlImg_'+id+']').attr('onclick',"setdefault_mul_img('"+sNumID+"','"+sSayYesNo+"')").val(sNumID);
									$(this).find('.thumbimg_dd_div').attr('onclick',"remove_mul_img('"+sNumID+"','"+sSayYesNo+"')");
								});

								/*$.growlUI(data);*/
								$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
							}
						);
					}
				});
			}
		}
		
		function Jtrim(str){ 
			return str.replace(/^\s*|\s*$/g,"");  
		} 

		function upload_mu_img(fildname)
		{	
			$('#windowContent').html('<iframe src="../plugin/SWFUpload/upimg.php" width="500" marginwidth="0" height="440" marginheight="0" align="middle" frameborder="0" scrolling="no"></iframe>');
			$("#windowtitle").html('上传图片');
			var client_with=$(window).width();
			var left=(client_with-500)/2;
			$.blockUI({ 
				message: $('#windowForm'),
				css:{ 
		                width: '500px',height:'470px',top:'2%',left:left+"px"
		            }			
				});
			$('#windowForm').css("width","500px");
		    $('#set_filename').val(fildname);
			$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
		}
		
		function set_mu_img(arrUploadinfo)
		{
			var sethtml_img_list = $('#set_filename').val();
			$.post("do_product.php",
					{m:"set_list_mu_img", idname:sethtml_img_list,'updata':JSON.stringify(arrUploadinfo)},
					function(data){
						data = Jtrim(data);
						if(data != ""){
							$("#"+sethtml_img_list).append(data);

							junkdrawer.restoreListOrder(sethtml_img_list);
							dragsort.makeListSortable(document.getElementById(sethtml_img_list),saveImgOrder);
						}	
					}	
				);
			$.unblockUI();
		}

		function remove_mul_img(arrkey,df)
		{
			var img_id = "mu_img_"+arrkey;
			if(confirm('确定要删除该图片吗?'))
			{	
				$.post("do_product.php",
					{m:"remove_mul_img", rkey: arrkey, dkey: df},
					function(data){
						data = Jtrim(data);
						if(data == "ok")
						{
							 $('#'+img_id).remove();
							 $.growlUI("删除成功!");
						}else{
							$.growlUI(data);
						}
						$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
					}		
				);
			}
			$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 	
		}

		function setdefault_mul_img(arrkey,df)
		{		
			$.post("do_product.php",
				{m:"setdefault_mul_img", rkey: arrkey, dkey: df},
				function(data){
					$.growlUI(data);
					$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
				}		
			);		
			$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 	
		}
		
		function closewindowui()
		{
			$.unblockUI();
		}
		
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="product_picture.php">
        		<tr>
					<td width="150" align="center"><strong>名称/编号/拼音码/条码：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline" /></td>
					<td width="140"><input type="checkbox" name="show_no_picture" id="show_no_picture" value="no" <?php if(!empty($in['show_no_picture'])) echo 'checked="checked"';?>  />只显示无图的商品</td>
        			<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " /></td>
					
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="product_picture.php">批量传图</a> &#8250;&#8250; <a href=""><? if(empty($sortinfo)) echo "所有商品"; else echo $sortinfo['SiteName'];?></a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	  <div id="sortleft">
<!-- tree --> 
<div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增商品 " class="button_2" onclick="javascript:window.location.href='product_add.php'" /></div>
<hr style="clear:both;" />

<div id="sidetree"> 
<div class="treeheader">&nbsp;<strong>商品分类</strong></div>  	  
<div id="sidetreecontrol"><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
		echo ShowTreeMenu($sortarr,0);
	?>	
</ul>
 </div>
<!-- tree -->
       	  </div>

        <div id="sortright">
			<div class="warning">
				上传的图片可以进行排序，鼠标左键按住不放，进行拖动交换位置！
			</div>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
          <input type="hidden" name="set_filename" id="set_filename" value="" />
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
				  <td width="28%" class="bottomlinebold">编号/名称</td>
                  <td class="bottomlinebold">商品图片</td>                  

                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['sid'])) $sqlmsg .= " and SiteNO like '".$sortinfo['SiteNO']."%' ";
	if(!empty($in['show_no_picture'])) $sqlmsg .= " and (Picture = '' or Picture is null) ";
	if(!empty($in['kw']))  $sqlmsg .= " and (Name like '%".$in['kw']."%' OR CONCAT(Pinyi, Coding, Barcode) like '%".$in['kw']."%') ";
	//yangmm 2017-11-28 代理商只能看到自己商品的信息
	$userid=$_SESSION['uinfo']['userid'];
	$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
	if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$userid." ";
	if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";	
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_view_index_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0 ".$sqlmsg."  ");
	$datasql   = "SELECT ID,SiteID,SiteName,OrderID,CommendID,Name,Coding,Price1,Price2,Units,Picture FROM ".DATATABLE."_view_index_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0  ".$sqlmsg." ORDER BY OrderID DESC, ID DESC";
	
	$page = new ShowPage;
    $page->PageSize = 30;
    $page->Total   = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid'], "show_no_picture"=>$in['show_no_picture']);        
	
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		foreach($list_data as $v)
		{
			$indexidarr[] = $v['ID'];
		}
		$indexidmsg = implode(",",$indexidarr);
		$ressql   = "select IndexID,Name,Path,OrderID from ".DATATABLE."_order_resource where CompanyID = ".$_SESSION['uinfo']['ucompany']." and IndexID IN (".$indexidmsg.") order by IndexID desc,OrderID asc";
		$pic_data = $db->get_results($ressql);

		if(!empty($pic_data)){
			foreach($pic_data as $pv)
			{
				$picarr[$pv['IndexID']][$pv['OrderID']] = $pv['Path'].'thumb_'.$pv['Name'];			
			}
		}

		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline"  >
  				  <td >
					<a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" class="no"><?php echo $n++;?></a>&nbsp;<br />                	
					<?php echo $lsv['Coding'];?>&nbsp;<br />
                  	<a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" ><? echo $producttypearr[$lsv['CommendID']];?> <? echo $lsv['Name'];?></a><br />
                  	<img src="img/up_img.jpg" onClick="upload_mu_img('listpic_<?php echo $lsv['ID']; ?>');" title="上传商品图片" style="cursor: pointer; padding-top:8px;" />
                  </td>
				  <td >

<script type="text/javascript">
$(function() {
	junkdrawer.restoreListOrder("listpic_<?php echo $lsv['ID']; ?>");
	dragsort.makeListSortable(document.getElementById("listpic_<?php echo $lsv['ID']; ?>"),saveImgOrder);
});
</script>

				  	<ul class="listpic_thumbimg" id="listpic_<?php echo $lsv['ID']; ?>">
				  	<?php
				  	if(!empty($picarr[$lsv['ID']]))
				  	{ 
				  		$out_thumb_msg = '';
				  		foreach($picarr[$lsv['ID']] as $pk => $pv)
				  		{
							if($lsv['Picture'] == $pv)
				  			{
				  				$smsg = 'checked="checked"';
				  				$isdefault = "Y";
				  			} else {
				  				$smsg = '';
				  				$isdefault = "N";
				  			}
				  			$upkey = $lsv['ID'].'_'.$pk;
				  			$out_thumb_msg .= '<li id="mu_img_'.$upkey.'"><a href_="'.RESOURCE_URL.str_replace("thumb_","img_",$pv).'" target="_blank"><img src="'.RESOURCE_URL.$pv.'"  width="70" height="70" border="0" /></a><br /><div class="checkbox thumbimg_dd_left" title="设为列表页默认图片"><input name="DefautlImg_'.$lsv['ID'].'" type="radio" value="'.$upkey.'" '.$smsg.' onclick="setdefault_mul_img(\''.$upkey.'\',\''.$isdefault.'\')" />默认</div><div class="thumbimg_dd_div" onclick="remove_mul_img(\''.$upkey.'\',\''.$isdefault.'\')" title="删除图片">X</div></li>';
				  		}
				  		echo $out_thumb_msg;
				  	}
				  	?>
				  	</ul>
				  </td>                  

                </tr>
<?php } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合条件的商品！</td>
       			 </tr>
<?php }?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('product_picture.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
    <div id="windowForm">
		<div class="windowHeader">
			<h3 id="windowtitle">上传图片</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">  

		</div>
	</div>
</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				if($var['ParentID']=="0")
				{
					$frontMsg  .= '<li><a href="product_picture.php?sid='.$var['SiteID'].'"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="product_picture.php?sid='.$var['SiteID'].'">'.$var['SiteName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>