<?php 
$menu_flag = "system";
include_once ("header.php");
//if(empty($in['selectid']) || $in['selectid']=="undefined")
//{
//	$in['selectid'] = '';
//}
////查询
$cinfo = $db->get_col("select ID FROM  ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany']." and AgentID !=0 ");
$sidarr1 = explode(";",$in['selectid']); 
$sidarr = array_merge_recursive($sidarr1, $cinfo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/showpage.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript">
function Jtrim(str){ 
	return str.replace(/^\s*|\s*$/g,"");  
} 
function sub_add_client()
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_saler.php?m=sub_add_goods", $("#MainForm").serialize(),
			function(data){
				data.backtype = Jtrim(data.backtype);
				if(data.backtype == "ok")
				{
					$.blockUI({ message: "<p>提交成功!</p>" });
					parent.set_add_client(data.htmldata);
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					window.setTimeout($.unblockUI, 1000); 

				}else{
					$.blockUI({ message: "<p>"+data.backtype+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);

				}	
		},"json");
}


function CheckAll(form)
{
	for(var i=0;i<form.elements.length;i++)
	{
		var e = form.elements[i];
		if (e.name != 'chkall' && e.name !='copy')       e.checked = form.chkall.checked; 
	}
}

</script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}
form{margin:0; padding:0;}
thead tr td{font-weight:bold;}
.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
.leftlevel{width:100%; height:360px; overflow:auto;}
.leftlevel ul{width:100%;  clear:both; list-style-type:none; list-style:none; margin:0; padding:2px;}
.leftlevel dt{width:100%; font-weight:bold; margin-left:2px; height:28px; clear:both; color:#333333;}
li{width:100%; height:24px; clear:both; list-style-type:none; list-style:none;}
.locationli{background:#277DB7; color:#ffffff; padding:2px;}
-->
</style>
</head>

<body> 
		<table width="98%" border="0" cellpadding="4" cellspacing="0" bgcolor="#ffffff"> 
		<tr>
		<td valign="top">
              <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <form id="FormSearch" name="FormSearch" method="post" action="system_area_goods.php">
				 <input name="selectid" type="hidden" id="selectid" value="<? echo $in['selectid'];?>"  />
				 <tr>
       				 <td width="12%" align="center"><strong>药品名称：</strong></td>
					 <td width="43%"  height="30" ><input type="text" name="kw" id="kw" style="width:200px;" /></td> 
					 <td width="35%">
					<select name="aid" id="aid" style="width:180px;">
                    <option value="0">⊙ 请选择药品分类</option>
                    <?php 
                                        	
                                        $sortarr = $db->get_results("SELECT SiteID,CompanyID,ParentID,SiteName  FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteID ASC ");
					echo ShowTreeMenu($sortarr,0,0);
                                        ?>
                  </select>
					 </td>
   			         <td > &nbsp;<input name="searchbutton" type="submit" class="bluebtn" id="searchbutton" value="搜 索" /></td>
     			 </tr>
				 </form>
              </table>

			<form id="MainForm" name="MainForm" method="post" action="" target="" >		
        	  <table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">               
               <thead>
                <tr>
                  <td width="10%"  bgcolor="#efefef" align="center">选择</td>
                  <td bgcolor="#efefef" >药品名称</td>	
                </tr>
     		 </thead>      		
      		<tbody>
<?php
        $sqlmsg = '';
	if(!empty($in['kw']))
	{
		$in['kw'] = trim($in['kw']);
		$sqlmsg .= " and Name like binary '%%".$in['kw']."%%' ";
	}else{
		$in['kw'] = '';
	}
	if(!empty($in['aid']))
	{		
		$sqlmsg .= " and SiteID = ".$in['aid'];
	}
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany'].$sqlmsg);
	$page = new ShowPage;
        $page->PageSize = 10;
        $page->Total    = $InfoDataNum['allrow'];
        $page->LinkAry  = array("kw"=>$in['kw'],"sid"=>$in['sid'],"aid"=>$in['aid'],"selectid"=>$in['ID']);        
	$datasql   = "SELECT ID,CompanyID,SiteID,BrandID,Name FROM ".DATATABLE."_order_content_index where CompanyID=".$_SESSION['uinfo']['ucompany'].$sqlmsg;
        $list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $k=>$lsv)
		 {
			 $lsv['Name'] = str_replace('"',"“",$lsv['Name']);
?>
                <tr id="line_<? echo $lsv['ID'];?>"  >
                    <!--<? if (in_array($lsv['ID'], $sidarr)) {	echo 'checked="checked"  disabled="disabled"';}?>-->
				   <td bgcolor="#FFFFFF" height="22" align="center"><input name="selectclient[]" value="<? echo $lsv['ID'];?>" type="checkbox"  <? if (in_array($lsv['ID'], $sidarr)) {	echo 'checked="checked"  disabled="disabled"';}?>/></td>
                  <td bgcolor="#FFFFFF" ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" > <? echo $lsv['Name'];?></a></td>
              </tr>
<?php } }else{ ?>
     		  <tr>
       				 <td height="30" colspan="3" align="center" bgcolor="#FFFFFF">无符合条件的内容!</td>
   			  </tr>
<?php }?>
 				</tbody>                
              </table>

		      <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="10%" align="center"  height="28" class="selectinput" >&nbsp;<input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="14%" >全选/取消</td>
   			       <td ><input name="b4" id="b4" type="button" value=" 提 交 " onClick="sub_add_client();" class="bluebtn" title="提交您选择被屏蔽的药店"  />
				   <input name="b4" id="b4" type="button" value=" 返 回 " onClick="parent.closewindowui();" class="bluebtn" />
				   </td>
     			 </tr>
              </table>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="left" height="30"><? echo $page->ShowLink2('system_area_goods.php');?></td>
     			 </tr>
          </table>
			</td>
		</tr>
		</table>
            </form>      
</body>
</html>
<?php



 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";
		
		if($var['ParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("&nbsp;&nbsp;", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	
				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
       
?>