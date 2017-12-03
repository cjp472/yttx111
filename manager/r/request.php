<?php
$menu_flag = "manager";
include_once ("header.php");
include_once ("../class/letter.class.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link href="css/showpage.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/request.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
	$(function() {
		//$("#data_EndDate").datepicker();
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
        	  <form id="FormSearch" name="FormSearch" method="post" action="request.php">
        	    <label>
        	      &nbsp;&nbsp;名称/联系人/电话： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
				<label>&nbsp;&nbsp;
				<select name="stype" id="stype" class="selectline">
						<option value="CompanyName" selected="selected">客户名称</option>
						<option value="Contact"> 联系人 </option>
						<option value="QQ"> QQ </option>
						<option value="Mobile"> 手机 </option>
						<option value="Email"> 邮箱 </option>
					</select>
					</label>

				<label>&nbsp;&nbsp;
				<select name="scz" id="scz" class="selectline">
				<?php
					$optionmsg = '';
					foreach($kfarr as $key=>$val){
						if($in['scz'] == $key) $smsg = ' selected="selected" '; else $smsg = '';
						$optionmsg .= '<option value="'.$key.'" '.$smsg.' >'.$val.'</option>';
					}
					echo $optionmsg;
				?>
					</select>
					</label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>            
			<div class="location"><strong>当前位置：</strong><a href="request.php">试用管理</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
 
		<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">行号</td>
                  <td class="bottomlinebold">客户名称</td>				  
                  <td width="10%" class="bottomlinebold">联系人</td>
				  <td width="10%" class="bottomlinebold">QQ</td>
				  <td width="10%" class="bottomlinebold">联系电话</td>
				  <td width="12%" class="bottomlinebold">试用帐号</td>
                  <td width="10%" class="bottomlinebold" align="right">开通/到期时间</td>
				  <td width="8%" class="bottomlinebold" align="right">操作员</td>
				  <td width="8%" class="bottomlinebold" align="center">发送状态</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		<tbody>
<?
	$sqlmsg = '';
	if(!empty($in['stype']) && !empty($in['kw']))  $sqlmsg .= " and s.".$in['stype']." like '%".$in['kw']."%'  ";
	if($in['scz'] == 'all') $in['scz'] = '';
	if(!empty($in['scz']))  $sqlmsg .= " and s.AddUser = '".$in['scz']."' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c right join ".DATABASEU.DATATABLE."_order_request s on c.CompanyID=s.CompanyID where c.CompanyFlag='0'  ".$sqlmsg."  ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],"scz"=>$in['scz']);        
	
	$datasql   = "SELECT c.CompanySigned,s.* FROM ".DATABASEU.DATATABLE."_order_company c right join ".DATABASEU.DATATABLE."_order_request s on c.CompanyID=s.CompanyID where c.CompanyFlag='0'  ".$sqlmsg."  ORDER BY s.ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{				
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>

                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $n++;?></td>
                  <td ><a href="request_content.php?ID=<? echo $lsv['ID'];?>" target="_blank"><? echo $lsv['CompanyName'];?></a></td>
				  <td ><? echo $lsv['Contact'];?>&nbsp;</td>
                  <td ><? echo $lsv['QQ'];?>&nbsp;</td>
				  <td ><? 
				  if(!empty($lsv['Mobile'])) echo $lsv['Mobile'];
				  if(!empty($lsv['Phone'])) echo '<br />'.$lsv['Phone'];
				  ?></td>
				  <td><a href="do_login.php?m=admintologin&companyid=<? echo $lsv['CompanyID'];?>"><? echo $lsv['CompanySigned'];?></a></td>

                  <td class="TitleNUM"><? 
				  echo date("Y-m-d",$lsv['RequestDate']);
				  $timsgu = strtotime($lsv['EndDate'].' 23:59:59');
				  if($timsgu > time()){
					echo "<font color=green><br />√ ".$lsv['EndDate']."</font>";
				  }else{					
					echo "<font color=red><br />/ ".$lsv['EndDate']."</font>";
				  }				  
				  ?></td>
				  <td align="right"><?php echo $kfarr[$lsv['AddUser']];?></td>
                  <td align="center"><?				  
				  if($lsv['SendFlag'] == 'T'){
					echo "<font color=green>√ </font>";
				  }else{					
					echo "<font color=red>X </font>";
				  }				  
				  ?></td> 
                  <td align="center">
				    [<a href="request_add.php?ID=<? echo $lsv['ID'];?>" >延期</a>]&nbsp;&nbsp;
					[<a href="request_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" >查看</a>]				  
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="12" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>
       			     <td  align="right"><? echo $page->ShowLink('request.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>            
        	</div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
	
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>