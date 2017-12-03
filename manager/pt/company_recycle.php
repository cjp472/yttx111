<?php 
include_once ("header.php");
$menu_flag = "manager";
if($_SESSION['uinfo']['userid'] != "1") exit('非法路径!');

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
        	      &nbsp;&nbsp;名称/联系人/电话： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>

       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <a href="company_add.php">新增客户</a> </div>            
            
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	  <div id="sortleft">
<!-- tree --> 
<div class="leftlist">
<div >&nbsp;<br />
<strong><img src="css/images/home.gif" alt="地区"  />&nbsp&nbsp<a href="company_recycle.php">行业分类</a></strong></div>
<ul>
	<?php 
		$accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_order_industry ORDER BY IndustryID ASC ");
		foreach($accarr as $accvar)
		{
			if($in['iid'] == $accvar['IndustryID']) $smsg = 'class="locationli"'; else $smsg ="";
			echo '<li><a href="company_recycle.php?iid='.$accvar['IndustryID'].'" '.$smsg.' > - '.$accvar['IndustryName'].'</a></li>';
		}
	?>
</ul>
<br style="clear:both;" />
</div>
<hr style="clear:both;" />
<div id="sidetree"> 
<div class="treeheader">&nbsp;
<strong><a href="company_recycle.php">地区分类</a></strong></div>  	  
<div id="sidetreecontrol"><img src="css/images/home.gif" alt="地区"  />&nbsp;&nbsp;[ <a href="?#">关闭</a> | <a href="?#">展开</a> ]</div>
<ul id="tree">
	<?php 
		$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName,AreaAbout FROM ".DATABASEU.DATATABLE."_order_city  ORDER BY AreaParent asc,AreaOrder DESC,AreaID ASC ");
		echo ShowTreeMenu($sortarr,0);

		foreach($sortarr as $areavar)
		{
			$areaarr[$areavar['AreaID']] = $areavar['AreaName'];
		}
	?>
	
</ul>
</div>
<!-- tree --> 

       	  </div>


        <div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="8%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">公司名称</td>
				  <td width="8%" class="bottomlinebold">用户数</td>
                  <td width="12%" class="bottomlinebold">联系方式</td>
                  <td width="12%" class="bottomlinebold" align="right">开通时间</td>
                  <td width="12%" class="bottomlinebold" align="right">到期时间</td>
                  <td width="16%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['iid']))  $sqlmsg .= " and c.CompanyIndustry=".$in['iid']." "; else $in['iid'] = '';
	if(!empty($in['aid']))  $sqlmsg .= " and c.CompanyArea=".$in['aid']." "; else $in['aid'] = '';

	if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like binary '%%".$in['kw']."%%' or c.CompanyPhone like binary '%%".$in['kw']."%%' or c.CompanyContact like '%%".$in['kw']."%%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='1' ".$sqlmsg."  ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid']);        
	
	$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='1' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	if(!empty($list_data))
	{
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['CompanyID'];?>" value="<? echo $lsv['CompanyID'];?>" /></td>
                  <td ><? echo $lsv['CompanyID'];?></td>

                  <td ><a href="#"><? echo $lsv['CompanyName'];?></a></td>
				  <td >&nbsp;<strong><? echo $lsv['CS_Number'];?></strong></td>
                  <td class="TitleNUM2"><? echo $lsv['CompanyContact'];?></td>

                  <td class="TitleNUM"><? echo $lsv['CS_BeginDate'];?></td>

                  <td class="TitleNUM"><? 
				  $timsgu = strtotime($lsv['CS_EndDate']);
				  if($timsgu - time() < 30*24*60*60){
					echo "<font color=red>".$lsv['CS_EndDate']."</font>";
				  }else{
					echo $lsv['CS_EndDate'];
				  }				  
				  ?></td>
                  
                  <td align="center">
						<a href="javascript:void(0);" onclick="do_restore('<? echo $lsv['CompanyID'];?>');" >还原</a>&nbsp;|&nbsp;
					<a href="javascript:void(0);" onclick="do_quite_delete('<? echo $lsv['CompanyID'];?>');" >彻底删除</a>
				  
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>
                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>

   			       <td  class="sublink"></td>
       			     <td width="50%" align="right"><? echo $page->ShowLink('company_recycle.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
    
</body>
</html>
<?
 	function ShowTreeMenu($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParentID'] == $p_id)
			{
				if($var['AreaParentID']=="0")
				{
					$frontMsg  .= '<li><a href="company_recycle.php?aid='.$var['AreaID'].'"><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="company_recycle.php?aid='.$var['AreaID'].'">'.$var['AreaName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>