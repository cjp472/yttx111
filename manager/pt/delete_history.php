<?php 
$menu_flag = "delete";
include_once ("header.php");
if(!in_array($_SESSION['uinfo']['userid'],array(1))) exit('非法路径!!!');

setcookie("backurl", $_SERVER['REQUEST_URI']);
$cid = intval($in['cid']);

$csql   = "SELECT CompanyID,CompanyName,CompanyContact,CompanyMobile,BusinessLicense,CompanyDatabase FROM ".DATABASEU.DATATABLE."_order_company where CompanyID=".$cid." ORDER BY CompanyID ASC limit 0,1";
$cominfo = $db->get_row($csql);
if(!empty($cominfo['CompanyDatabase']))
{
  $sdbname = DB_DATABASE.'_'.$cominfo['CompanyDatabase'].".";
}else{
  $sdbname = DB_DATABASE.'.';
}

if(empty($cominfo['CompanyID'])) exit('参数错误！');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="js/function.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="">
    .radio input{width:auto;}
    .inputstyle .delBtn{width:72px;height:24px;padding:0px;}
</style>
<script type="text/javascript">		
    $(function(){
        $("#SDate").datepicker({changeMonth: true,	changeYear: true});
        $("#EDate").datepicker({changeMonth: true,	changeYear: true});
    });


  function delete_mul_data()
  {
    if(confirm('确认删除吗?'))
    {
      var cid = "<?php echo $cid;?>";
      $.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
      $.post("do_delete.php?m=delete_mul_data", $("#FormData").serialize(),
        function(data){
          if(data == "ok"){
            $.blockUI({ message: "<p>删除成功!</p>" });
            window.location.reload();
            $('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
          }else{
            $.blockUI({ message: "<p>"+data+"</p>" });
            $('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
          }       
        }   
      );
      $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
      //window.setTimeout($.unblockUI, 1500);
    }
  }

  function delete_sigle_data(data_type)
  {
    if(confirm('确认删除吗?'))
    {
      var cid = "<?php echo $cid;?>";
      $.blockUI({ message: "<p>正在执行，请稍后...</p>" }); 
      $.post("do_delete.php?m=delete_sigle_data&data_type="+data_type, $("#FormData").serialize(),
        function(data){
          if(data == "ok"){
            $.blockUI({ message: "<p>删除成功!</p>" });
            //window.location.reload();
            $("#"+data_type+'_button').val('已删除');
            $("#"+data_type+'_button').attr("disabled","disabled");
            $('.blockOverlay').attr('title','点击继续!').click($.unblockUI);
          }else{
            $.blockUI({ message: "<p>"+data+"</p>" });
            $('.blockOverlay').attr('title','点击返回!').click($.unblockUI);
          }       
        }   
      );
      $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
      //window.setTimeout($.unblockUI, 1500);
    }
  }

</script>
</head>
<body>
<?php include_once ("top.php");?>

<?php include_once ("inc/son_menu_bar.php");?>
     
    <div id="bodycontent">
    <form id="FormData" name="FormData" method="post" > 
                <input type="hidden" name="cid" id="cid2"  value="<? if(!empty($cid)) echo $cid;?>"   />
        <div class="bline">
            <fieldset  class="fieldsetstyle">		
			<legend>业务数据</legend>
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">                 
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">客户：</div></td>
                  <td width="60%">
                      <div style="font-size:14px; color:#cc0000;">
                      <?php
                      echo $cominfo['CompanyID'].'、'.$cominfo['CompanyName'].' ('.$cominfo['BusinessLicense'].')<br />';
                      echo $cominfo['CompanyContact'].' ：'.$cominfo['CompanyMobile'].'<br />';
                      ?>

                      </div>
                        
                  </td>
                  <td bgcolor="#FFFFFF"></td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">选择要删除的数据：</div></td>
                  <td>
                    
                    <input type="checkbox" name="OrderType[]"  value="order" style="width:20px; " /> 订单
                    <input type="checkbox" name="OrderType[]"  value="return" style="width:20px; margin-left:30px;" /> 退单
                    <input type="checkbox" name="OrderType[]"  value="consignment" style="width:20px; margin-left:30px;" /> 发货单
                    <input type="checkbox" name="OrderType[]"  value="finance" style="width:20px; margin-left:30px;" /> 收款单
                    <input type="checkbox" name="OrderType[]"  value="finance" style="width:20px; margin-left:30px;" /> 其他款项
                    <input type="checkbox" name="OrderType[]"  value="invertory" style="width:20px; margin-left:30px;" /> 入库单(全部)

                  </td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">选择要删除的数据时间范围：</div></td>
                  <td>
                    <input name="SDate" id="SDate" value="" style="width:186px"/>&nbsp; 
                                                             到  
                    <input name="EDate" id="EDate" value="" style="width:186px"/>&nbsp; 
                  </td>
                  <td bgcolor="#FFFFFF">不选择时间默认删除所有该单据的数据</td>
                </tr>
                
            </table>
            <div align="center"><input name="backid" type="button" class="button_3" id="backid" value="删除" onclick="delete_mul_data();" /></div>
           </fieldset> 
           
           <br style="clear:both;" />
           
            <fieldset class="fieldsetstyle">
			       <legend>基础资料</legend>
              <table width="98%" border="0" cellpadding="8" cellspacing="1" bgcolor="#ccc" class="inputstyle">             
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">库存：</div></td>
                  <td bgcolor="#FFFFFF"></td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delStorage" id="library_button" value="清空" class="button_3 delBtn" onclick="delete_sigle_data('library');" />
                  </td>
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">商品：</div></td>
                  <td width="30%" bgcolor="#FFFFFF">
                    <label class="radio"><input type="radio" name="Products" value="All" />全部</label>&nbsp;&nbsp;
                    <label class="radio"><input type="radio" name="Products" value="Recycle" />下架</label>&nbsp;&nbsp;
                  </td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delProducts" id="goods_button" value="删除" class="button_3 delBtn" onclick="delete_sigle_data('goods');" />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">商品分类：</div></td>
                  <td bgcolor="#FFFFFF"></td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delPCategory" id="category_button" value="删除" class="button_3 delBtn" onclick="delete_sigle_data('category');" />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户：</div></td>
                  <td bgcolor="#FFFFFF">
                    <label class="radio"><input type="radio" name="Client" value="All" />全部</label>&nbsp;&nbsp;
                    <label class="radio"><input type="radio" name="Client" value="Recycle" />回收站</label>&nbsp;&nbsp;
                  </td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delClient" id="client_button" value="删除" class="button_3 delBtn" onclick="delete_sigle_data('client');" />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户地区：</div></td>
                  <td bgcolor="#FFFFFF"></td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delCArea" id="area_button" value="删除" class="button_3 delBtn" onclick="delete_sigle_data('area');" />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">信息：</div></td>
                  <td bgcolor="#FFFFFF">
                    <label class="radio"><input type="radio" name="Info" value="All" />全部</label>&nbsp;&nbsp;
                    <label class="radio"><input type="radio" name="Info" value="Recycle" />回收站</label>&nbsp;&nbsp;
                  </td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delInfo" id="infomation_button" value="删除" class="button_3 delBtn" onclick="delete_sigle_data('infomation');" />
                  </td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">信息分类：</div></td>
                  <td bgcolor="#FFFFFF"></td>
                  <td bgcolor="#FFFFFF">
                    <input type="button" name="delICategory" id="sort_button" value="删除" class="button_3 delBtn" onclick="delete_sigle_data('sort');" />
                  </td>
                </tr>
              </table>
			</fieldset>
       	</div>
</form>
        <br style="clear:both;" />
    </div>
 
<?php include_once ("bottom.php");?>

</body>
</html>