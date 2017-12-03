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
        	<div class="leftdiv"> </div>            
			<div class="location"><strong>当前位置：</strong><a href="system.php">系统设置</a> &#8250;&#8250; <a href="#">费用类型</a>  </div>           
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
       	<div id="sortleft">
         
<!-- tree --> 
<div class="leftlist"> 



<div >
<strong>系统设置</strong></div>
<!-- 系统设置菜单开始 -->
<?php include_once("inc/system_set_left_bar.php")  ;?>
<!-- 系统设置菜单结束 -->
<br style="clear:both;" />
</div>
<!-- tree -->  
       	  </div>

          <div id="sortright">
          	<form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
          		<div class="font12h">新增其他款项类型:</div>
          	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td class="bold" width="10%">其他款项编号:</td>
                  <td width="24%" ><input type="text" name="data_BillNO" id="data_BillNO" value="" /></td>
				  <td class="bold" width="10%" >其他款项名称:</td>  
				  <td width="24%"><input type="text" name="data_BillName" id="data_BillName" value="" /></td>         
                  <td ><input type="button" name="savebutton" id="savebutton" value="保 存" class="button_2" onclick="do_save_bill();" /> </td>
                </tr>
              </table>        
          
          <hr />
          
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">行号</td>
                  <td width="24%" class="bottomlinebold">其他款项编号</td>
				  <td  class="bottomlinebold">其他款项名称</td>           
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php
	
	$datasql   = "SELECT * FROM ".DATATABLE."_order_expense_bill where CompanyID = ".$_SESSION['uinfo']['ucompany']."  Order by BillID Desc";
	$list_data = $db->get_results($datasql);
	if(!empty($list_data))
	{
	 $n=1;
     foreach($list_data as $lsv)
	 {
?>
                <tr id="line_<? echo $lsv['BillID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['BillNO'];?><?php if($lsv['BillNO']=='otherbill'){?>&nbsp;[<span class="green">默认</span>]<?php }?></td>
				  <td ><? echo $lsv['BillName'];?></td>  
                  <td align="center">
                  	<?php if($lsv['BillNO']=='otherbill'){?>
						[<span class="green">默认</span>]
					  <?php }else{?>
					  <a href="#editbill" onclick="set_edit_bill('<?php echo $lsv['BillID']; ?>','<?php echo $lsv['BillNO']; ?>','<?php echo $lsv['BillName']; ?>')" ><span class="iconfont icon-xiugaixinxi" style="color:#666;position: relative;top: 3px;"></span></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_bill('<? echo $lsv['BillID'];?>');" >
					  <span class="iconfont icon-dacha01" style="color:#666;font-size:19px;position: relative;top: 4px;"></span></a>
					  <?php }?>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <a name="editbill"></a>
              <div id="edit_bill"  style="display:none;">
               <div class="font12h">修改其他款项类型:</div>
               	  <INPUT TYPE="hidden" name="update_id" id="update_id" value ="" >
	          	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
	                <tr>
	                  <td class="bold" width="10%">费用编号:</td>
	                  <td width="24%" ><input type="text" name="edit_BillNO" id="edit_BillNO" value="" /></td>
					  <td class="bold" width="10%" >费用名称:</td>  
					  <td width="24%"><input type="text" name="edit_BillName" id="edit_BillName" value="" /></td>         
	                  <td ><input type="button" name="editbutton" id="editbutton" value="保 存" class="button_2" onclick="do_edit_bill();" /> </td>
	                </tr>
	              </table>  
              </div>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>


        </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>