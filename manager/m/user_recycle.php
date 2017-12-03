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

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/system.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="user_recycle.php">
        	    <label>
        	      &nbsp;&nbsp;帐号/姓名/电话： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>

       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="user.php">帐号管理</a> </div>
            
            
        </div>

    	
        <div class="line2"></div>
        <div class="bline">

        	<div >
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="8%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">帐号</td>
				  <td width="14%" class="bottomlinebold">姓名</td>
                  <td width="16%" class="bottomlinebold">部门职位</td>
                  <td width="8%" class="bottomlinebold" >登陆次数</td>

                  <td width="16%" class="bottomlinebold" align="right">最近登陆</td>
                  <td width="15%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$sqlmsg = '';

	if(!empty($in['kw']))  $sqlmsg .= " and (UserName like binary '%%".$in['kw']."%%' or UserTrueName like binary '%%".$in['kw']."%%' or UserPhone like '%%".$in['kw']."%%' ) ";
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_user where UserCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and UserFlag='1' and UserType='M' ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_user where UserCompany = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." and UserFlag='1'  and UserType='M' ORDER BY UserID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n=1;
	if(!empty($list_data))
	{
     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['UserID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['UserID'];?>" value="<? echo $lsv['UserID'];?>" /></td>
                  <td ><? echo $n++;?></td>

                  <td ><a href="#"><? echo $lsv['UserName'];?></a></td>
				  <td ><? echo $lsv['UserTrueName'];?>&nbsp;</td>
                  <td class="TitleNUM2"><? echo $lsv['UserPhone'];?>&nbsp;</td>

                  <td ><? echo $lsv['UserLogin'];?>&nbsp;</td>

                  <td class="TitleNUM"><? if(!empty($lsv['UserLoginDate'])) echo date("y-m-d H:i",$lsv['UserLoginDate']);?>&nbsp;</td>
                  
                  <td align="right">
					<a href="javascript:void(0);" onclick="do_restore('<? echo $lsv['UserID'];?>')" >恢复</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="do_quite_delete('<? echo $lsv['UserID'];?>');" >彻底删除</a>				  
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
       			     <td width="50%" align="right"><? echo $page->ShowLink('user_recycle.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    

 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
    
</body>
</html>