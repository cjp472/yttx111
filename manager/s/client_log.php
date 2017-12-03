<?php 
$menu_flag = "client";
$pope	   = "pope_view";
include_once ("header.php");


if(empty($in['cid']))
{
	$cinfo = null;
	$in['cid'] = 0;
}else{	 
	$cinfo = $db->get_row("SELECT ClientID,ClientName,ClientCompanyName,ClientTrueName FROM ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientID=".intval($in['cid'])."  limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/client.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<? include_once ("top.php");?>
    
<div class="bodyline" style="height:25px;"></div>

    
    

        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="client_log.php">
        	    <label>
        	      &nbsp;&nbsp;用户/IP： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>

       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="client_log.php">登陆日志 &#8250;&#8250; <? if(empty($cinfo)) echo "所有药店"; else echo $cinfo['ClientCompanyName'];?></a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 

<div class="leftlist"> 
<div >
<strong>药店</strong></div>  	  
<ul>
	<form name="changetypeform" id="changetypeform" action="client_log.php" method="get">
	<select id="cid" name="cid" onchange="javascript:submit()" style="width:160px !important; width:145px;" class="select2">
	<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		$clientdata = $db->get_results("select c.ClientID,c.ClientCompanyName,c.ClientCompanyPinyi from ".DATATABLE."_order_client c left join ".DATATABLE."_order_salerclient s ON c.ClientID=s.ClientID  where c.ClientCompany=".$_SESSION['uinfo']['ucompany']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." and s.SalerID=".$_SESSION['uinfo']['userid']." and c.ClientFlag=0 order by c.ClientCompanyPinyi asc");
		foreach($clientdata as $areavar)
		{
			$n++;
			if($in['cid'] == $areavar['ClientID']) $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];

			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$areavar['ClientCompanyPinyi']).'" >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
	</select>
	</form>
</ul>

 </div>
<!-- tree -->   
       	  </div>
          <div id="sortright">
<?
	$sqlmsg = "";
	if(!empty($in['cid']))
	{
		$sclientidarr = explode(",",$_SESSION['uinfo']['clientidmsg']);
		if (in_array($in['cid'], $sclientidarr))
		{
			$sqlmsg .= ' and LoginClient='.$in['cid'].' ';
		}else{
			$sqlmsg .= ' and LoginClient=0 ';
		}
	}else{
		$sqlmsg .= " and LoginClient in (".$_SESSION['uinfo']['clientidmsg'].") ";
	}
	if(!empty($in['kw']))  $sqlmsg .= " and (LoginName like binary '%%".$in['kw']."%%' or LoginIP like binary '%%".$in['kw']."%%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_login_client_log where LoginCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid']);        
	
	$datasql   = "SELECT LoginID,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl FROM ".DATABASEU.DATATABLE."_order_login_client_log where LoginCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY LoginID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">行号</td>
                  <td width="18%" class="bottomlinebold">药店(帐号)</td>
				  <td width="18%" class="bottomlinebold">登陆IP</td>
				  <td width="22%" class="bottomlinebold">登陆时间</td>
                  <td class="bottomlinebold">登陆地址</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n=1;
if(!empty($list_data))
{
   if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
   foreach($list_data as $lsv)
   {
?>
               <tr id="line_<? echo $lsv['LoginID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><a href="client_content.php?ID=<? echo $lsv['LoginClient'];?>" target="_blank"><? echo $lsv['LoginName'];?></a></td>
				  <td ><? 	$iparr = explode(",", $lsv['LoginIP']);
	 echo $iparr[0];?></td>
				  <td class="TitleNUM2"><? echo date("Y-m-d H:i",$lsv['LoginDate']);?></td>
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
       			     <td  align="right"><? echo $page->ShowLink('client_log.php');?></td>
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