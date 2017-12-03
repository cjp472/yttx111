<?php
$menu_flag = "ty";
include_once ("header.php");

if(empty($in['ID'])) exit('非法路径!');
$cinfo = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_visit where ID=".intval($in['ID'])." limit 0,1");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/function.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>        

    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="manager.php">
        	    <label>
        	      <strong>&nbsp;&nbsp;<? echo $cinfo['CompanyName'];?></strong>
       	        </label>
   	          </form>
   	        </div>       

			<div class="location"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <? echo $cinfo['CompanyName'];?></div>
        </div>  	

        <div class="line2"></div>
        <div class="bline">
        <div id="sortright" style="width:100%; margin:8px;">
		   <fieldset title="" class="fieldsetstyle">

			<legend>回访信息</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">记录时间：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['RecordDate'];?>&nbsp;</td>
                </tr>  
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">联系人：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['ContactName'];?>&nbsp;</td>
                </tr> 
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">联系人职务：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['ContactJob'];?>&nbsp;</td>
                </tr> 
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">联系电话：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['ContactPhone'];?>&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">回访人：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['VisitName'];?></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">回访简情：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['VisitGeneral'];?></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">记录内容：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['VisitContent'];?></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="10%"><div align="right">使用状态：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;
                    <? if($cinfo['UseFlag'] =='T')echo "使用";
                       else if($cinfo['UseFlag'] =='L') echo "失联";
                       else echo "没用";
                    ?>
                  </td>
                </tr>
          </table>
          </fieldset> 
       	  </div>
        </div>
        <br style="clear:both;" />

    </div>   

<?php include_once ("bottom.php");?>
 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
         <div id="windowForm6">
		<div class="windowHeader" >
			<h3 id="windowtitle" style="width:540px">时间线</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div> 
</body>
</html>
<?php
 	function ShowTreeMenuList($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				if($var['AreaParent'] == "0")
				{
					$frontMsg  .= '<li><a href="manager.php?aid='.$var['AreaID'].'"  ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="manager.php?aid='.$var['AreaID'].'"  >'.$var['AreaName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenuList($resultdata,$var['AreaID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>