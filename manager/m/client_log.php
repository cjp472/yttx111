<?php 
$menu_flag = "client";
$pope	   = "pope_audit";
include_once ("header.php");
include_once ("../class/ip2location.class.php");

if(empty($in['cid']))
{
	$cinfo = null;
	$in['cid'] = 0;
}else{	 
	$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['cid'])."  limit 0,1");
}
	$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#bdate").datepicker();
		$("#edate").datepicker();
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="get" action="client_log.php">
        	    <label>
        	      <strong>&nbsp;&nbsp;帐号/IP：</strong> <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
				<label>
					<select id="cid" name="cid" onchange="javascript:submit()" style="width:145px;" class="select2">
					<option value="" >⊙ 所有药店</option>
					<?php 

						foreach($clientdata as $areavar)
						{
							$n++;
							if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
							$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
							echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$areavar['ClientCompanyPinyi']).'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
						}
					?>
					</select>
				</label>

				<label>
				&nbsp;&nbsp;起止时间：
				<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />
       	        </label>
				<label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="client_log.php">登陆日志</a> &#8250;&#8250; <a href="product.php"><? if(empty($cinfo)) echo "所有药店"; else echo $cinfo['ClientCompanyName'];?></a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?
	$sqlmsg = '';
	if(!empty($in['cid']))
	{
		$sqlmsg .= ' and LoginClient='.$in['cid'].' ';
	}
	if(!empty($in['kw']))  $sqlmsg .= " and (LoginName like binary '%%".$in['kw']."%%' or LoginIP like binary '%%".$in['kw']."%%' ) ";
	if(!empty($in['bdate'])) $sqlmsg .= ' and LoginDate > '.strtotime($in['bdate'].'00:00:00').' ';
	if(!empty($in['edate'])) $sqlmsg .= ' and LoginDate < '.strtotime($in['edate'].'00:00:00').' ';

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_login_client_log where LoginCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT LoginID,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl FROM ".DATABASEU.DATATABLE."_order_login_client_log where LoginCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY LoginID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td width="24%" class="bottomlinebold">药店</td>
				  <td width="15%" class="bottomlinebold">帐号</td>
				  <td width="22%" class="bottomlinebold">登陆地址/IP</td>
				  <td width="14%" class="bottomlinebold">登陆时间</td>
                  <td class="bottomlinebold">登陆地址</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n=1;
if(!empty($list_data))
{
   if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
   $IPAddress = new IPAddress();
   foreach($list_data as $lsv)
   {
		$iparr = explode(",",$lsv['LoginIP']);
		$IPAddress->qqwry($iparr[0]);
		$localArea = $IPAddress->replaceArea();
?>
               <tr id="line_<? echo $lsv['LoginID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['LoginClient'];?>" target="_blank"><? echo $clientarr[$lsv['LoginClient']];?></a></td>
				  <td ><? echo $lsv['LoginName'];?></td>
				  <td ><? echo $localArea." (".$iparr[0].")";?></td>
				  <td class="TitleNUM2"><? echo date("Y-m-d H:i",$lsv['LoginDate']);?></td>
                  <td class="TitleNUM2"><? if(strrpos($lsv['LoginUrl'],'?')) echo substr($lsv['LoginUrl'],0,strrpos($lsv['LoginUrl'],'?')); else echo $lsv['LoginUrl']; ?></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="9" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td  align="right"><? echo $page->ShowLink('client_log.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>