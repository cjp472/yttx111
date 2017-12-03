<?php
$menu_flag = "manager";
include_once ("header.php");

if(empty($in['ID'])) exit('非法路径!');
$cid = intval($in['ID']);
$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})
</script>
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
       	  <div id="sortleft">
		  <div class="linebutton"><input type="button" name="newbutton" id="newbutton" value=" 新增客户 " class="redbtn" onclick="javascript:window.location.href='company_add.php'" /></div>
		  <hr style="clear:both;" />
<!-- tree --> 

<div id="sidetree"> 
<div class="treeheader">
<strong><a href="manager.php">地区分类</a></strong></div>  	  
<div id="sidetreecontrol"><img src="css/images/home.gif" alt="地区"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
	    $areainfoselected = null;
		$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName,AreaAbout FROM ".DATABASEU.DATATABLE."_common_city  ORDER BY AreaParent asc,AreaOrder DESC,AreaID ASC ");
		echo ShowTreeMenuList($sortarr,0);

		foreach($sortarr as $areavar)
		{
			if($areavar['AreaID']==$in['aid']) $areainfoselected = $areavar;
			$areaarr[$areavar['AreaID']] = $areavar['AreaName'];
		}
	?>
	
</ul>
</div>
<!-- tree --> 
<hr style="clear:both;" />
<div class="leftlist">
<div >
<strong><img src="css/images/home.gif" alt="地区"  />&nbsp&nbsp<a href="manager.php">行业分类</a></strong></div>
<ul>
	<?php 
		$accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
		foreach($accarr as $accvar)
		{
			if($in['iid'] == $accvar['IndustryID']) $smsg = 'class="locationli"'; else $smsg ="";
			echo '<li><a href="manager.php?iid='.$accvar['IndustryID'].'" '.$smsg.' > - '.$accvar['IndustryName'].'</a></li>';
		}
	?>
</ul>
<br style="clear:both;" />
</div>
       	  </div>

        <div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			<fieldset  class="fieldsetstyle">
			<legend>属性资料</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                  <td width="55%"><label>
                    <? 
					$areainfo = $db->get_row("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city where AreaID=".$cinfo['CompanyArea']." ORDER BY AreaID ASC ");
					echo $areainfo['AreaName'];
					?>
                    </label></td>
                  <td width="29%"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                  <td>
                    <? 
					$industryinto = $db->get_row("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry where IndustryID=".$cinfo['CompanyIndustry']." ORDER BY IndustryID ASC limit 0,1");
					echo $industryinto['IndustryName']." ";
					?>
					</td>
                  <td></td>
                </tr>
            </table>
           </fieldset>             

            <br style="clear:both;" />
            <fieldset title="基本资料" class="fieldsetstyle">
		<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">           
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司全称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $cinfo['CompanyName'];?>
					</label></td>
                  <td width="29%" bgcolor="#FFFFFF">公司名或店名</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanySigned'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPrefix'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyCity'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyContact'];?></td>

                  <td bgcolor="#FFFFFF">可以写多个</td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyMobile'];?>

                  </td>

                  <td bgcolor="#FFFFFF">&nbsp;</td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPhone'];?>

                  </td>

                  <td bgcolor="#FFFFFF">&nbsp;</td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyFax'];?></td>

                  <td bgcolor="#FFFFFF">&nbsp;</td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyAddress'];?></td>

                  <td bgcolor="#FFFFFF">&nbsp;</td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyEmail'];?>&nbsp;</td>

                  <td bgcolor="#FFFFFF">&nbsp;</td>

                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyWeb'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;请输入以“http://”开头的完整网址</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyUrl'];?>&nbsp;</td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>

                  <td bgcolor="#FFFFFF"><? echo nl2br($cinfo['CompanyRemark']);?>&nbsp;</td>

                  <td bgcolor="#FFFFFF">&nbsp;</td>

                </tr>

              </table>

			</fieldset>



			<br style="clear:both;" />

		   <fieldset title="设置" class="fieldsetstyle">

			<legend>设置</legend>

            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">用户数：</div></td>

                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['CS_Number'];?>&nbsp;</td>

                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(限制客户经销商的个数)</td>

                </tr>  

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">开通时间：</div></td>

                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_BeginDate'];?>&nbsp;</td>

                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(客户交费开通日期)</td>

                </tr> 

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">到期时间：</div></td>

                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_EndDate'];?>&nbsp;</td>

                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>

                </tr> 

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">最近更新时间：</div></td>

                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_UpDate'];?>&nbsp;</td>

                  <td bgcolor="#FFFFFF" width="29%">&nbsp;(更新续费日期)</td>

                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">短信条数：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['CS_SmsNumber'];?></td>
                  <td bgcolor="#FFFFFF" width="29%">&nbsp;</td>
                </tr>

          </table>

          </fieldset>  

              <INPUT TYPE="hidden" name="referer" value ="" >

          </form>

       	  </div>

        </div>

        <br style="clear:both;" />

    </div>   

<?php include_once ("bottom.php");?>
 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
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