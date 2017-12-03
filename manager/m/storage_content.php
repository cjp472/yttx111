<?php 
$menu_flag = "inventory";
$pope	   = "pope_view";
include_once ("header.php");

if(!intval($in['ID']))
{
	exit('非法操作!');
} 

$info = $db->get_row("SELECT StorageID,StorageSN,StorageProduct,StorageAttn,StorageAbout,StorageUser,StorageDate FROM ".DATATABLE."_order_storage where CompanyID = ".$_SESSION['uinfo']['ucompany']." and StorageID=".intval($in['ID'])." ORDER BY StorageID ASC limit 0,1");
if(empty($info['StorageID'])) exit('此入库单不存在，或已经删除!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/inventory.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>      
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
         <input type="hidden" name="set_filename" id="set_filename" value="" />
         <input type="hidden" name="update_id" id="update_id" value="<? echo $productinfo['ID'];?>" />
		<div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="storage.php">入库单</a> &#8250;&#8250; <a href="#">入库单详细</a> </div>
   	        </div>            
            <div class="rightdiv sublink" style="padding-right:20px; padding-top:4px;"><ul></ul></div>            
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >
			<fieldset  class="fieldsetstyle">
			<legend>入库单:</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="15%" bgcolor="#F0F0F0"><div align="right">入库单号：</div></td>
                  <td width="35%" bgcolor="#FFFFFF"><label><strong><? echo $info['StorageSN'];?></strong></label></td>
                  <td width="15%" bgcolor="#F0F0F0"><div align="right">下单时间：</div></td>
                  <td width="35%" bgcolor="#FFFFFF"><? echo date("Y-m-d H:i:s",$info['StorageDate']); ?></td>
                </tr>               
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">经办人：</div></td>
                  <td bgcolor="#FFFFFF"><label><? echo $info['StorageAttn'];?></label></td>
                  <td bgcolor="#F0F0F0"><div align="right">操作员：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $info['StorageUser']; ?>  </td>
                </tr>
                <tr>
                  <td  bgcolor="#F0F0F0"><div align="right">备注说明：</div></td>
                  <td  bgcolor="#FFFFFF" colspan="3"><label>
                    <? echo nl2br($info['StorageAbout']);?></label></td>
                </tr>               
              </table>
		</fieldset>
	<?php
		$sql = "select s.ContentNumber,i.ID,i.Name,i.Coding,i.Barcode,i.Casing,i.Units,i.Color,i.Specification from ".DATATABLE."_order_storage_number s left join ".DATATABLE."_order_content_index i on s.ContentID=i.ID where s.StorageID=".$info['StorageID']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$list_data_total = $db->get_results($sql);
		
		$sqlrow = "select ContentID from ".DATATABLE."_order_storage_number_cs where StorageID = ".$info['StorageID']." and CompanyID=".$_SESSION['uinfo']['ucompany']." ";
		$listrow = $db->get_col($sqlrow);
		if(!empty($listrow))
		{			
			$sqlcs = "select s.ContentColor,s.ContentSpec,s.ContentNumber,i.ID,i.Name,i.Coding,i.Barcode,i.Casing,i.Units,i.Color,i.Specification from ".DATATABLE."_order_storage_number_cs s left join ".DATATABLE."_order_content_index i on s.ContentID=i.ID where s.StorageID=".$info['StorageID']." and s.CompanyID=".$_SESSION['uinfo']['ucompany']." ";
			$list_data = $db->get_results($sqlcs);
			foreach($list_data_total as $v)
			{
				if(!in_array($v['ID'],$listrow)) $list_data[] = $v;
			}
		}else{
			$list_data = $list_data_total;
			unset($list_data_total);
		}

	?>
		<fieldset  class="fieldsetstyle">
			<legend>入库明细：</legend>
        	 <table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
               <thead>
                <tr>
                  <td width="5%" class="bottomlinebold">&nbsp;行号</td>
				  <td width="10%" class="bottomlinebold" >&nbsp;编号</td>
                  <td class="bottomlinebold">&nbsp;商品名称</td>
				  <td width="12%" class="bottomlinebold" >&nbsp;条码</td>
				  <?php if(!empty($listrow)) echo '<td width="10%" class="bottomlinebold" >&nbsp;颜色</td><td width="10%" class="bottomlinebold" >&nbsp;规格</td>';?>
				  <td width="12%" class="bottomlinebold" >&nbsp;包装</td>                  
				  <td width="12%" class="bottomlinebold" align="right" >入库数</td>
				  <td width="6%" class="bottomlinebold" align="center">单位</td>
                </tr>
     		   </thead>
      		<tbody>
			<?php			
			$n = 1;
			foreach($list_data as $lsv)
			{				
			?>
                <tr id="line_<? echo $lsv['ID'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
                  <td >&nbsp;<? echo $n++;?></td>
				  <td >&nbsp;<? echo $lsv['Coding'];?></td>
                  <td ><a href="product_content.php?ID=<? echo $lsv['ID'];?>" target="_blank" ><? echo $lsv['Name'];?></a></td>
				  <td >&nbsp;<? echo $lsv['Barcode'];?></td>
                  <?php 
				  if(!empty($listrow)){
					$color = base64_decode(str_replace($rp,$fp,$lsv['ContentColor']));
					if($color == '统一') $color = '';
					$spec  = base64_decode(str_replace($rp,$fp,$lsv['ContentSpec']));
					if($spec == '统一') $spec = '';
					echo '<td  >&nbsp;'.$color.'</td><td  >&nbsp;'.$spec.'</td>';
				  }
				  ?>				  
				  <td >&nbsp;<? echo $lsv['Casing'];?></td>                                  
				  <td align="right"><? echo $lsv['ContentNumber'];?>&nbsp;</td>
				  <td align="center">&nbsp;<? echo $lsv['Units'];?></td> 
                </tr>
			<? }?>
			</tbody>
			</table>
            </fieldset>
        </div>
         <INPUT TYPE="hidden" name="referer" value ="" >
        </form>
		<div class="line" align='right'>		
			<input type="button" value="打印入库单" class="bluebtn" name="printbtn" id="print_confirmbtn" onclick="javascript:window.open( 'print.php?u=print_storage&ID=<? echo $info['StorageID'];?>','_blank');" />&nbsp;&nbsp;
			<input type="button" value="导出入库单" class="greenbtn" name="excelprintbtn" id="excel_confirmbtn" onclick="javascript:window.open( 'storage_content_excel.php?ID=<? echo $info['StorageID'];?>','exe_iframe');" />&nbsp;&nbsp;	&nbsp;&nbsp;	&nbsp;&nbsp;
		</div>
        <br style="clear:both;" />
    </div>
    
<?php include_once ("bottom_content.php");?>

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