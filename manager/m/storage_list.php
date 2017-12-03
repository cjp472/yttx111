<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");

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

<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
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

			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="post" action="storage_list.php">
        		<tr>
					<td width="50" align="center"><strong>商品：</strong></td> 
					<td width="120"><input type="text" name="kw" id="kw" class="inputline"  value="<? if(!empty($in['kw'])) echo $in['kw'];?>" onfocus="this.select();"  /></td>
					<td align="center" width="80">起止时间：</td>
					<td width="220" nowrap="nowrap"><input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /></td>
       				<td width="60"><input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value="搜 索" /></td>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="storage_list.php">入库明细</a></div></td>
				</tr>
   	          </form>
			 </table>  
   	        </div>            
    	
        <div class="line2"></div>
        <div class="bline">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >


		<fieldset  class="fieldsetstyle">
			<legend>商品入库明细数据</legend>

        	 <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >               
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">&nbsp;行号</td>
				  <td width="12%" class="bottomlinebold" >&nbsp;编号</td>	
                  <td  class="bottomlinebold">&nbsp;商品名称</td>                  
				  <td width="14%" class="bottomlinebold" >&nbsp;条码</td>	
				  <td width="12%" class="bottomlinebold" align="right" >数量&nbsp;</td>
                  <td width="8%" class="bottomlinebold" align="center">单位</td>
				  <td width="14%" class="bottomlinebold" align="left">&nbsp;入库单号</td>
				  <td width="6%" class="bottomlinebold" align="center" >详细</td>
                </tr>
     		   </thead> 
      		
      		<tbody>
			<?
			$sqlmsg = '';

			if(!empty($in['kw']))  $sqlmsg .= " and (i.Name like '%".$in['kw']."%' or i.Coding like '%".$in['kw']."%' or i.Pinyi like '%".$in['kw']."%') ";

			if(!empty($in['bdate'])) $sqlmsg .= ' and t.StorageDate > '.strtotime($in['bdate'].'00:00:00').' ';
			if(!empty($in['edate'])) $sqlmsg .= ' and t.StorageDate < '.strtotime($in['edate'].'23:59:59').' ';
				
			//yangmm 2017-11-28 代理商只能看到自己商品的信息
			$userid=$_SESSION['uinfo']['userid'];
			$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DATABASEU.DATATABLE."_order_user where UserID = ".$userid."");
			if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$userid." ";
			if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";		

			if(empty($sqlmsg))
			{
				$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_storage_number where CompanyID = ".$_SESSION['uinfo']['ucompany']." ");
			}else{
				$InfoDataNum = $db->get_row("SELECT count(*) AS allrow from ".DATATABLE."_order_storage_number s  left join ".DATATABLE."_order_storage t on s.StorageID=t.StorageID  left join ".DATATABLE."_order_content_index i on s.ContentID=i.ID  where  s.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." ");
			}
			$page = new ShowPage;
			$page->PageSize = 30;
			$page->Total   = $InfoDataNum['allrow'];
			$page->LinkAry = array("kw"=>$in['kw'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);    

			$datasql = "select t.StorageID,s.ContentNumber,i.ID,i.Name,i.Coding,i.Barcode,i.Units,i.Casing,i.Color,i.Specification,t.StorageID,t.StorageSN from ".DATATABLE."_order_storage_number s inner join ".DATATABLE."_order_storage t on s.StorageID=t.StorageID  left join ".DATATABLE."_order_content_index i on s.ContentID=i.ID where s.CompanyID=".$_SESSION['uinfo']['ucompany']." and i.CompanyID=".$_SESSION['uinfo']['ucompany']." ".$sqlmsg." order by s.StorageID desc ";
			$list_data = $db->get_results($datasql." ".$page->OffSet());
			$n = 1;
			if(!empty($list_data))
			{
				if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
				foreach($list_data as $lsv)
				{		
			?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td height="28">&nbsp;<? echo $n++;?></td>
				   <td >&nbsp;<? echo $lsv['Coding'];?></td>
                  <td ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" ><? echo $lsv['Name'];?></a></td>
                  <td >&nbsp;<? echo $lsv['Barcode'];?></td>
				  <td align="right"><? echo $lsv['ContentNumber'];?>&nbsp;</td>
                  <td align="center">&nbsp;<? echo $lsv['Units'];?></td>                 
				  <td align="left">&nbsp;<a href="storage_content.php?ID=<? echo $lsv['StorageID'];?>" target="_blank" ><? echo $lsv['StorageSN'];?></a></td> 
				  <td align="center">
				  <? if(empty($lsv['Color']) && empty($lsv['Specification'])){ ?>
					&nbsp;
				  <? }else{?>
					[<a href="javascript:void(0)" onclick="showproductnumber('<? echo $lsv['ID'];?>','<? echo $lsv['StorageID'];?>');">明细</a>]
				  <? }?>
				  </td>
                </tr>
			<? }}?>
			</tbody>
			</table>

                 <table width="98%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       			     <td height="40" align="right"><? echo $page->ShowLink('storage_list.php');?></td>
     			 </tr>
              </table>
            </fieldset>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        <br style="clear:both;" />
    </div>
    

 <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
        <div id="windowForm6">
		<div class="windowHeader">
			<h3 id="windowtitle">商品库存详细</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div>
</body>
</html>