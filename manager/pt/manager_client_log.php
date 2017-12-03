<?php 
$menu_flag = "manager";
include_once ("header.php");
include_once ("../class/ip2location.class.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker({changeMonth: true,	changeYear: true});
		$("#edate").datepicker({changeMonth: true,	changeYear: true});
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>

 <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="get" action="manager_client_log.php">
			  <input type="hidden" name="cid" id="cid" class="inputline" value="<? if(!empty($in['cid'])) echo $in['cid'];?>" />
        	    <label>
        	      &nbsp;&nbsp;用户/IP： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
				<label>
				<select id="cid" name="cid" onchange="javascript:submit()" style="width:160px !important; width:145px;">
				<option value="" >⊙ 所有客户</option>
				<?php 
					$n = 0;
					$sortarr = $db->get_results("SELECT CompanyID,CompanyName,CompanySigned FROM ".DATABASEU.DATATABLE."_order_company where CompanyFlag='0' ORDER BY CompanyID DESC");
					foreach($sortarr as $areavar)
					{
						$n++;
						$companyarr[$areavar['CompanyID']] = $areavar['CompanyName'];
						if($in['cid'] == $areavar['CompanyID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$areavar['CompanyID'].'" '.$smsg.' title="'.$areavar['CompanyName'].'"  >'.$areavar['CompanyID'].' 、 '.$areavar['CompanySigned'].'</option>';
					}
				?>
				</select>
				</label>

				&nbsp;&nbsp;起止时间：
				<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="manager_client_log.php">登陆日志-经销商</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 

<div class="leftlist"> 
<div class="treeheader">
<strong>帐号类型</strong></div>  	  

<ul >
	<li><a href="error_log.php?t=c">错误日志-经销商</a></li>
	<li><a href="error_log.php?t=m">错误日志-管理员</a></li>	
	<li>----------------------------</li>
	<li><a href="manager_client_log.php">登陆日志-经销商</a></li>
	<li><a href="manager_user_log.php">登陆日志-管理员</a></li>	
</ul>

<br style="clear:both;" />
<br style="clear:both;" />



 </div>
<!-- tree -->   
       	  </div>
          <div id="sortright">
<?
	$sqlmsg = '';
	if(!empty($in['cid']))
	{
		$sqlmsg .= " and LoginCompany=".$in['cid']." ";
	}
	if(!empty($in['kw']))  $sqlmsg .= " and (LoginName like binary '%".$in['kw']."%' or LoginIP like binary '%".$in['kw']."%' ) ";
	if(!empty($in['bdate'])) $sqlmsg .= ' and LoginDate > '.strtotime($in['bdate'].'00:00:00').' ';
	if(!empty($in['edate'])) $sqlmsg .= ' and LoginDate < '.strtotime($in['edate'].'23:23:59').' ';
	
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_login_client_log where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"t"=>$in['t'],"cid"=>$in['cid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT LoginID,LoginClient,LoginCompany,LoginName,LoginIP,LoginDate,LoginUrl FROM ".DATABASEU.DATATABLE."_order_login_client_log where 1=1 ".$sqlmsg." ORDER BY LoginID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">行号</td>
                  <td width="14%" class="bottomlinebold">帐号</td>
				  <td width="28%" class="bottomlinebold">登陆IP</td>
				  <td width="14%" class="bottomlinebold">登陆时间</td>
                  <td  class="bottomlinebold">登陆地址</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n = 1;
if(!empty($list_data))
{
	$IPAddress = new IPAddress();
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
		$iparr = explode(",",$lsv['LoginIP']);
		$IPAddress->qqwry($iparr[0]);
		$localArea = $IPAddress->replaceArea();
?>
               <tr id="line_<? echo $lsv['LoginID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)" title="<? echo $companyarr[$lsv['LoginCompany']];?>" >
                  <td ><? echo $n++;?></td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['LoginClient'];?>&cid=<? echo $lsv['LoginCompany'];?>" target="_blank"><? echo $lsv['LoginName'];?></a></td>
				  <td ><? echo $localArea.'--'.$lsv['LoginIP'];?></td>
				  <td class="TitleNUM2"><? echo date("y-m-d H:i",$lsv['LoginDate']);?></td>
                  <td class="TitleNUM2"><? echo $lsv['LoginUrl'];?></td>
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
   			         
       			     <td  align="right"><? echo $page->ShowLink('manager_client_log.php');?></td>
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