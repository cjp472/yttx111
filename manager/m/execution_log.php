<?php 
$menu_flag = "system";
include_once ("header.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="js/order.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
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
        	  <form id="FormSearch" name="FormSearch" method="get" action="execution_log.php">
        	    <label>
        	      &nbsp;&nbsp;<strong>动作：</strong> <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
				<label>
					<select id="cname" name="cname" onchange="javascript:submit()" style="width:145px;">
					<option value="" >⊙ 所有帐号</option>
					<?php 
						$sortarr = $db->get_results("SELECT UserID,UserName,UserTrueName FROM ".DATABASEU.DATATABLE."_order_user where UserCompany=".$_SESSION['uinfo']['ucompany']." ORDER BY UserID ASC");
						foreach($sortarr as $areavar)
						{
							$n++;
							if($in['cname'] == $areavar['UserName']) $smsg = 'selected="selected"'; else $smsg ="";
							//$clientarr[$areavar['UserID']] = $areavar['UserTrueName'];
							echo '<option value="'.$areavar['UserName'].'" '.$smsg.' title="'.$areavar['UserTrueName'].'" sp="'.preg_replace('/([^a-zA-Z]+)/i','',$areavar['UserName']).'" >'.$areavar['UserName'].' - '.$areavar['UserTrueName'].'</option>';
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
            
			<div class="location"><strong>当前位置：</strong><a href="execution_log.php">操作日志</a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?
	$sqlmsg = ' and ExecutionUser!="seekfor" and ExecutionUser!="knight" ';
	if(!empty($in['cname'])) $sqlmsg .= " and ExecutionUser='".$in['cname']."' ";
	if(!empty($in['kw']))    $sqlmsg .= " and ExecutionAction like binary '%%".$in['kw']."%%' ";
	if(!empty($in['bdate'])) $sqlmsg .= ' and ExecutionDate > '.strtotime($in['bdate'].'00:00:00').' ';
	if(!empty($in['edate'])) $sqlmsg .= ' and ExecutionDate < '.strtotime($in['edate'].'00:00:00').' ';

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_execution where ExecutionCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"cname"=>$in['cname'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT ExecutionID,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionDate FROM ".DATATABLE."_order_execution where ExecutionCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ORDER BY ExecutionID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="15%" class="bottomlinebold">&nbsp;行号</td>
                  <td width="20%" class="bottomlinebold">帐号</td>				  
				  <td width="20%" class="bottomlinebold">动作</td>
                  <td width="28%" class="bottomlinebold">操作时间</td>

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
               <tr id="line_<? echo $lsv['ExecutionID'];?>"  class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td >&nbsp;<? echo $n++;?></td>
                  <td ><? echo $lsv['ExecutionUser'];?></td>
				  <td ><? echo $lsv['ExecutionAction'];?></td>
				  <td class="TitleNUM2"><? echo date("Y-m-d H:i",$lsv['ExecutionDate']);?></td>
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
       			     <td  align="right"><? echo $page->ShowLink('execution_log.php');?></td>
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