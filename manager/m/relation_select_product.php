<?php 
$menu_flag = "product";
$pope	   = "pope_form";
include_once ("header.php");
$sidarr = null;
if(empty($in['selectid']) || $in['selectid']=="undefined")
{
	$in['selectid'] = '';
}
$sidarr = explode(";",$in['selectid']);
if(empty($in['sid']))
{
	$sortinfo = null;
	$in['sid'] = 0;
}else{	 
	$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteName,SiteNO FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." and SiteID=".intval($in['sid'])." limit 0,1");
}
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

function sub_add_relation()
{
		$.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
		$.post("do_product.php?m=sub_add_relation", $("#MainForm").serialize(),
			function(data){
				data.backtype = Jtrim(data.backtype);
				if(data.backtype == "ok")
				{
					$.blockUI({ message: "<p>提交成功!</p>" });
					parent.set_add_relation(data.htmldata);
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					window.setTimeout($.unblockUI, 1000); 
					$('#b4').attr("disabled","");
				}else{
					$.blockUI({ message: "<p>"+data.backtype+"</p>" });
					$('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
					$('#b4').attr("disabled","");
				}	
		},"json");
}
</script>
<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}
td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: "微软雅黑", Arial, Helvetica, sans-serif, "宋体"; color:#333;}
thead tr td{font-weight:bold;}
.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#f0f0f0; background:url(./img/f1s.jpg); }

.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
.TitleNUM{font-size:12px; font-family: 'Lucida Grande', Verdana, sans-serif; color:#000}
.blockUI p{margin:4px; padding:8px 20px; font-size:14px; font-weight:bold}
.growlUI {  }
.growlUI h1, div.growlUI h2 {
	color: white; padding: 5px 5px 5px 15px; text-align: left; font-size:14px;
}
-->
</style>
</head>

<body> 

	<table width="100%" border="0" cellspacing="0" cellpadding="4">
          <form id="forms" name="forms" method="get" action="">
		  <tr>
            <td width="7%" nowrap="nowrap"><strong>&nbsp;快速查询：</strong></td>
            <td width="17%" height="24" nowrap="nowrap">
              <label>
                <input type="text" name="kw" id="kw" size="20" value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();" />
              </label>           
            </td>
            <td width="30%" nowrap="nowrap">            
				 <select name="sid" id="sid" style="width:185px; height:22px; margin:1px 2px;">
                    <option value="">⊙ 所有商品分类</option>
                    <? 
					$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName,SitePinyi FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
					echo ShowTreeMenu($sortarr,0,$in['sid'],1);
					?>
                  </select>                  
            </td>
            <td width="46%"><label>
              <input name="button3" type="submit" class="bluebtn" id="button3" value=" 查询 " />
            </label></td>            
          </tr>
		  </form>
        </table>
	<div style="width:100%; height:400px; overflow:auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="" >
			  <input type="hidden" name="selectid" id="selectid" value="<? if(!empty($in['selectid'])) echo $in['selectid'];?>" />
        	  <table width="98%" border="0" cellpadding="2" cellspacing="1" bgcolor="#CCCCCC">               
               <thead>
                <tr>
				  <td width="5%" bgcolor="#efefef" align="center" height="22" >&nbsp;</td>
                  <td width="5%" bgcolor="#efefef" align="center" height="22" >行号</td>
                  <td width="18%" bgcolor="#efefef" >编号</td>
				  <td bgcolor="#efefef" >名称</td>                  				  
                  <td width="16%" align="center" bgcolor="#efefef" >价格1(元)</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php
	$sqlmsg = '';
	if(!empty($in['sid'])) $sqlmsg .= " and SiteNO like '".$sortinfo['SiteNO']."%' ";
	if(!empty($in['kw']))  $sqlmsg .= " and (Name like '%".$in['kw']."%' OR CONCAT(Pinyi, Coding, Barcode) like '%".$in['kw']."%') ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_view_index_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0 ".$sqlmsg."  ");
	$page = new ShowPage;
    $page->PageSize = 11;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"sid"=>$in['sid']);        
	
	$datasql   = "SELECT ID,SiteID,SiteName,OrderID,CommendID,Name,Coding,Barcode,Price1,Price2,Units FROM ".DATATABLE."_view_index_site where CompanyID = ".$_SESSION['uinfo']['ucompany']." and FlagID=0  ".$sqlmsg." ORDER BY OrderID DESC, ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		 {
?>
                <tr id="line_<? echo $lsv['ID'];?>"  >
				  <td align="center" bgcolor="#FFFFFF"><input name="selectrelation[]" value="<? echo $lsv['ID'];?>" type="checkbox" <? if (in_array($lsv['ID'], $sidarr)) {echo 'checked="checked"  disabled="disabled"';}?> /></td>
                  <td height="22" align="center" bgcolor="#FFFFFF" > <? echo $n++;?></td>
				  <td bgcolor="#FFFFFF" ><? echo $lsv['Coding'];?></td>
                  <td bgcolor="#FFFFFF" ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" > <? echo $lsv['Name'];?></a></td>
                  <td class="TitleNUM" bgcolor="#FFFFFF" align="right">¥ <? echo $lsv['Price1']; ?>&nbsp;</td>
              </tr>
			<? } }else{?>
						  <tr>
								 <td height="30" colspan="7" align="center" bgcolor="#FFFFFF">无符合条件的商品!</td>
						  </tr>
			<? }?>
 				</tbody>                
              </table>
                 <table width="96%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
					 <td width="60"><input name="b4" id="b4" type="button" value=" 提 交 " onClick="sub_add_relation();" class="bluebtn" title="提交您选择商品"  /></td>
       			     <td align="right" height="30"><? echo $page->ShowLink('relation_select_product.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>

</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "┠";
		$selectmsg = "";
		
		if($var['ParentID']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				$repeatMsg = str_repeat("-+-", $layer-1);
				if($var['SiteID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";
				
				$frontMsg  .= "<option value='".$var['SiteID']."' ".$selectmsg." > ".$frontTitleMsg.$repeatMsg.$var['SiteName']."</option>";	

				$frontMsg2  = "";
				$frontMsg2 .= ShowTreeMenu($resultdata,$var['SiteID'],$s_id,$layer);
				$frontMsg  .= $frontMsg2;
			}
		}		
		return $frontMsg;
	}
?>
