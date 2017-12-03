<?php 
$menu_flag = "product";
$pope	   = "pope_view";
include_once ("header.php");

$clientdata = $db->get_results("select ClientID,ClientCompanyName,ClientCompanyPinyi from ".DATATABLE."_order_client where ClientCompany=".$_SESSION['uinfo']['ucompany']." and ClientFlag=0 order by ClientCompanyPinyi asc");

 	$sqlmsg1 = '';
	$sqlmsg2 = '';
	if(empty($in['cid']))
	{
		$in['cid'] = '';
	}else{
		$sqlmsg1 =" and ClientID = ".intval($in['cid'])." ";
		$sqlmsg2 =" and n.ClientID = ".intval($in['cid'])." ";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/product.js?v=2<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
<div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
  
   	        </div>
            
			<div class="location"><strong>当前位置：</strong><a href="product.php">商品管理</a> &#8250;&#8250; <a href="product_notice.php">到货通知</a></div>          
            
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	  <div id="sortleft">
<!-- tree --> 
<div class="leftlist">
<hr style="clear:both;" />
<div ><strong><a href="order.php">药店</a></strong></div>
<ul>
				<form name="changetypeform" id="changetypeform" action="product_notice.php" method="get">
				<select id="cid" name="cid" onchange="javascript:submit()" style=" width:85%;" class="select2">
				<option value="" >⊙ 所有药店</option>
	<?php 
		$n = 0;
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
          <form id="MainForm" name="MainForm" method="get" action="" target="exe_iframe" >
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold"><label>
                 &nbsp;
                  </label></td>
                  <td width="6%" class="bottomlinebold">行号</td>
				  <td width="14%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">名称</td>
                  
                  <td width="24%" class="bottomlinebold" >药店</td>
                  <td width="12%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead> 
      		
      		<tbody>
<?
	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_notice where CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg1." ");
	$page = new ShowPage;
    $page->PageSize = 20;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("cid"=>$in['cid']);        
	
	$datasql   = "SELECT i.ID,i.SiteID,i.Name,i.Coding,i.Price1,i.Price2,i.Units,n.ID as nID,n.ClientID,n.Mobile,n.Flag FROM ".DATATABLE."_order_content_index i left join ".DATATABLE."_order_notice n on i.ID=n.ProductID  where i.CompanyID = ".$_SESSION['uinfo']['ucompany']." and n.CompanyID = ".$_SESSION['uinfo']['ucompany']." ".$sqlmsg2." and i.FlagID=0 ORDER BY n.Flag desc,n.ID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	$n = 1;
	if(!empty($list_data))
	{
		if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td ><input type="checkbox" name="selectedID[]" id="select_<? echo $lsv['nID'];?>" value="<? echo $lsv['nID'];?>" <? if($lsv['Flag']=="1") echo 'disabled="disabled"';?> /></td>
                  <td ><? echo $n++;?></td>
				 <td ><? echo $lsv['Coding'];?>&nbsp;</td>
                  <td ><div style="overflow:hidden; width:98%; height:20px; line-height:20px;"><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" title="<? echo $lsv['Name'];?>" ><? echo $producttypearr[$lsv['CommendID']];?> <? echo $lsv['Name'];?></a></div></td>                  
                  <td > <? echo $clientarr[$lsv['ClientID']];?>&nbsp;</td>
                  <td align="center" id="set_notice_<?php echo $lsv['ID'];?>">
				  <? if($lsv['Flag']=="1"){?>
					<font color=gray>已通知</font>
				  <? }else{?>
					<a href="javascript:void(0);" onclick="do_notice_message(<? echo $lsv['ID'];?>);" >通知</a>&nbsp;|&nbsp;<a href="javascript:void(0);" onclick="do_notice_delete('<? echo $lsv['nID'];?>');" >删除</a>
				  <? }?>				  
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合条件的商品!</td>
       			 </tr>
<? }?>
 				</tbody>
                
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" align="center" ><input id="chkall" type="checkbox" name="chkall" value="on" onClick="CheckAll(this.form)" title="全选"></td>
   			       <td width="12%" >&nbsp;全选/取消</td>
				   <td  class="sublink"><ul><li><a href="javascript:void(0);" onclick="notice_going('notice','<? echo $in['cid'];?>')" >批量通知</a></li><li><a href="javascript:void(0);" onclick="notice_going('del','<? echo $in['cid'];?>')" >批量删除</a></li></ul></td>
       			     
     			 </tr>
              </table>
                 <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td align="right"><? echo $page->ShowLink('product_notice.php');?></td>
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