<?php 
$menu_flag = "company_log";
include_once ("header.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
        
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="company_log.php<? if(!empty($in['cid'])) echo '?cid='.$in['cid'];?>">
        	    <label>
        	      &nbsp;&nbsp;搜索： <input type="text" name="kw" id="kw" class="inputline" />
       	        </label>
				<label>
				<select id="cid" name="cid" onchange="javascript:submit()" style="width:245px;" class="select2">
				<option value="" >⊙ 所有客户</option>
				<?php 
					$n = 0;
					$sortarr = $db->get_results("SELECT CompanyID,CompanyName,CompanySigned FROM ".DATABASEU.DATATABLE."_order_company where CompanyFlag='0' ORDER BY CompanyID DESC");
					foreach($sortarr as $areavar)
					{
						$n++;
						$comarr[$areavar['CompanyID']] = $areavar['CompanyName'];
						if($in['cid'] == $areavar['CompanyID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$areavar['CompanyID'].'" '.$smsg.' title="'.$areavar['CompanyName'].'"  >'.$areavar['CompanyID'].' 、 '.$areavar['CompanySigned'].'</option>';
					}
				?>
				</select>
				</label>
       	        <label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="company_log.php">跟踪日志</a></div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?
	$sqlmsg = '';
	if(!empty($in['cid'])) $sqlmsg .= " and CompanyID=".$in['cid']." ";
	if(!empty($in['kw']))  $sqlmsg .= " and Content like '%".$in['kw']."%'  ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_log where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"cid"=>$in['cid']);        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_company_log where 1=1 ".$sqlmsg." ORDER BY LogID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());

?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">编号</td>
				  <td width="24%" class="bottomlinebold">客户</td>
                 			  
				  <td  class="bottomlinebold">内容</td>
				   <td width="12%" class="bottomlinebold">操作帐号</td>	
                  <td width="15%" class="bottomlinebold">操作时间</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?

if(!empty($list_data))
{
     foreach($list_data as $lsv)
	 {
?>
               <tr id="line_<? echo $lsv['LogID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><? echo $lsv['LogID'];?></td>
				  <td ><? echo '<a href="manager_company.php?ID='.$lsv['CompanyID'].'" target="_blank">'.$comarr[$lsv['CompanyID']].'</a>';?></td>
                  
				  <td ><? echo nl2br($lsv['Content']);?></td>
				  <td ><? echo $lsv['CreateUser'];?></td>
				  <td class="TitleNUM2"><? echo date("y-m-d H:i",$lsv['CreateDate']);?></td>
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
       			     <td  align="right"><? echo $page->ShowLink('company_log.php');?></td>
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