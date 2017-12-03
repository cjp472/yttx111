<?php 
$menu_flag = "forum";
$pope	   = "pope_view";
include_once ("header.php");

	if(empty($in['cname']))
	{
		$cidmsg = '';
	}else{
		$cidmsg = '&cname='.$in['cname'];
	}
	if(empty($in['ty']))
	{
		$tymsg = '';
	}else{
		$tymsg = '&ty='.$in['ty'];
	}
setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link href="css/showpage.css" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/forum.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
        
		<div id="searchline">
        	<div class="rightdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="forum.php">客服</a> &#8250;&#8250; <a href="forum.php">留言管理</a> </div>
   	        </div>       
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >

       	<div id="sortleft">         
<!-- tree --> 
<div class="leftlist"> 
<div >
<strong><span class="iconfont icon-suoyouleibie" style="color:#33a676;font-size:14px;"></span>&nbsp;&nbsp;<a href="forum.php" title="所有留言">留言管理</a></strong></div>
<ul>
					<li><a href="forum.php?ty=replyed<? echo $cidmsg;?>" <? if($in['ty'] == "replyed") echo 'class="locationli"';?> > &#8250;&#8250; 已回复留言</a></li>
					<li><a href="forum.php?ty=noreply<? echo $cidmsg;?>" <? if($in['ty'] == "noreply") echo 'class="locationli"';?> > &#8250;&#8250; 未回复留言</a></li>
</ul>
<hr style="clear:both;" />
<div >
<strong><a href="forum.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="forum.php" method="get">
				<select id="cname" name="cname" onchange="javascript:submit()" class="select2" style="width:85%;">
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
		$sortarr = $db->get_results("SELECT ClientID,ClientName,ClientCompanyName,ClientCompanyPinyi FROM ".DATATABLE."_order_client where ClientCompany = ".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 ORDER BY ClientCompanyPinyi ASC ");
		foreach($sortarr as $areavar)
		{
			$n++;
			if($in['cname'] == $areavar['ClientName'])  $smsg = 'selected="selected"'; else $smsg ="";
			$clientarr[$areavar['ClientID']] = $areavar['ClientCompanyName'];
			echo '<option value="'.$areavar['ClientID'].'" '.$smsg.' title="'.$areavar['ClientCompanyName'].'"  >'.substr($areavar['ClientCompanyPinyi'],0,1).' - '.$areavar['ClientCompanyName'].'</option>';
		}
	?>
				</select>
				</form>
</ul>


</div>
<!-- tree -->   
       	  </div>

		<div id="sortright">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>

                  <td width="8%" class="bottomlinebold">行号</td>
                  <td  class="bottomlinebold">标题</td>
                  <td width="20%" class="bottomlinebold">留言人</td>
                  <td width="14%" class="bottomlinebold" >发布时间</td>
                  <td width="6%" class="bottomlinebold" >回复</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';
		if($in['ty']=='replyed')
		{
			$sqlmsg .= ' and Reply!=0 ';
		}elseif($in['ty']=='noreply'){
			$sqlmsg .= ' and Reply=0 ';
		}
	if(empty($in['cname']))
	{
		$in['cname'] = '';;
	}else{
		$sqlmsg .= " and User='".$in['cname']."' ";
	}

	if(!empty($in['kw']))  $sqlmsg .= " and (Title like '%".$in['kw']."%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_forum where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and PID=0 ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"ty"=>$in['ty']);        
	
	$datasql   = "SELECT ID,Title,Name,User,Date,Reply FROM ".DATATABLE."_order_forum where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and PID=0 ORDER BY ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">

                  <td ><? echo $n++;?></td>
				  <td ><a href="forum_edit.php?ID=<? echo $lsv['ID'];?>"><? echo $lsv['Title'];?></a></td>
                  <td ><? echo $clientarr[$lsv['User']]."<br />".$lsv['Name']."";?>&nbsp;</td>
                  <td ><? echo date("Y-m-d H:i",$lsv['Date']);?></td>                  
                  <td class="font12"> <? echo $lsv['Reply'];?></td>
                  <td align="center"><a href="forum_edit.php?ID=<? echo $lsv['ID'];?>" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_all('<? echo $lsv['ID'];?>');" ><span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 5px;"></span></a></td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合条件的信息!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('forum.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
<!-- rightbody end -->
        </div>              
          </div>
              
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>