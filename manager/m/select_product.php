<?php include_once ("header.php");?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script language="javascript">
<!--
function setTargetNodeID(targetNodeID,NodeName)
{
	document.form1.targetNodeID.value = targetNodeID;
	document.form1.targetNodeName.value = NodeName;
	return false;
}

function returnNodeID()
{
	if(document.form1.targetNodeID.value == '') {
		alert("请选一个分类!");
		return false;
	} else {
        var rv = document.form1.targetNodeID.value;
        window.parent.apply_move(rv);
        window.parent.cancel_muledit();
	}
}

/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})
//-->
</script>

<style type="text/css">
<!--
a{text-decoration:none; color:#277DB7; }
a:hover{text-decoration:underline; color:#cc0000}

td,div,p{color:#333333; font-size:12px; line-height:150%;}
body { margin:0; padding:0; font-size:12px; background-color:#ffffff; font-family: 'Lucida Grande', Verdana, sans-serif; color:#333;}

.redbtn {
     background:url(./img/f1.jpg);  color: #FFF;  border:#f45c0d 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.redbtn:hover {color:#ffffff; background:url(./img/f1s.jpg); }


.bluebtn {
   background:url(./img/f2.jpg);  color: #FFF;  border:#0774bc 1px solid;  font-weight: bold;    font-size: 12px;  margin:0 8px 0 5px;  line-height:20px;  cursor: pointer;	height:24px;
}
.bluebtn:hover {  color:#f0f0f0;  background:url(./img/f2s.jpg);}
-->
</style>
</head>

<body bgcolor=#efefef STYLE="margin:3pt;padding:0pt;border: 1px buttonhighlight;" >
<table width="92%" border="0" cellspacing="4" align="center">
  <form name="form1" method="post" action="">
    <tr> 
      <td colspan="3" align="right">
	  	<table width="100%" border="0" cellspacing="0">
          <tr>
            <td width="100%" align="left" ><strong>&nbsp;请选择目标分类:</strong> </td>
          </tr>
        </table>
        
      </td>
    </tr>
    <tr> 
      <td colspan="2" align="center">
	<div style="width:100%; height:240px; overflow:auto; bgcolor:#ffffff; text-align:left;">
<!-- tree --> 

<div id="sidetree"> 
 	  
<div id="sidetreecontrol"><img src="css/images/home.gif" alt="分类"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT SiteID,ParentID,SiteName FROM ".DATATABLE."_order_site where CompanyID=".$_SESSION['uinfo']['ucompany']." ORDER BY SiteOrder DESC,SiteID ASC ");
		echo ShowTreeMenu($sortarr,0);
	?>	
</ul>
 </div>
<!-- tree --> 
<?
 	function ShowTreeMenu($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['ParentID'] == $p_id)
			{
				if($var['ParentID']=="0")
				{
					$frontMsg  .= '<li><a href="#" onclick="setTargetNodeID(\''.$var['SiteID'].'\',\''.$var['SiteName'].'\');"><strong>'.$var['SiteName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="#" onclick="setTargetNodeID(\''.$var['SiteID'].'\',\''.$var['SiteName'].'\');">'.$var['SiteName'].'</a>';
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
	
	</div>			
				</td>
    </tr>

	<tr> 
      <td  ><input name="targetNodeID" type="hidden" ><input name="targetNodeName" id="targetNodeName" type="text" size="12" > </td>
      <td  align=right> <input name="Submit" type="button" class="redbtn" onclick="returnNodeID();" value=" 确定 "> 
      &nbsp;&nbsp;<input name="Submit2" type="button" class="bluebtn" onclick='window.parent.cancel_muledit();' value=" 取消 "></td>
    </tr>

  </form>
</table>
</body>
</html>