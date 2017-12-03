<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

setcookie("backurl", $_SERVER['REQUEST_URI']);

//初始起止时间
$startDate	= date('Y-m-d', strtotime('-30 days'));
$endtDate	= date('Y-m-d');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/showpage.css" />
<link rel="stylesheet" href="css/jquery.pagination.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script src="js/finance.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script src="js/jquery.pagination.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script src="js/jquery.execute.hidden.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript">
	$(function(){
		$("#sdate, #edate").datepicker();

		//初始化载入
//		getPayWithdraw();
		
		//点击载入
		$("#to-show-draw").bind('click', function(){
			getPayWithdraw();
		});
	});
</script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        		<tr>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="yijifu.php">快捷支付</a></div></td>
				</tr>
			 </table>      
        </div>
    	
        <div class="line2"></div>
        <div class="bline">
         <fieldset>     
			<legend><strong>快捷支付菜单</strong></legend>
            <table width="98%" cellspacing="1" cellpadding="4" border="0" bgcolor="#FFFFFF">          
				<tbody>
					<tr>
                 		<td align="left">
	                 		<span totar="YJFopenApi.php?type=changeEmail" class="font-weight hand merchant_action">修改邮箱</span>
	                 		&nbsp;&nbsp;&nbsp;&nbsp;<span class="font-color-gray">|</span>&nbsp;&nbsp;&nbsp;&nbsp;
	                 		<span totar="YJFopenApi.php?type=changePWD" class="font-weight hand merchant_action">修改支付密码</span>
	                 		&nbsp;&nbsp;&nbsp;&nbsp;<span class="font-color-gray">|</span>&nbsp;&nbsp;&nbsp;&nbsp;
	                 		<span totar="YJFopenApi.php?type=getPWD" class="font-weight hand merchant_action">找回支付密码</span>
	                 		&nbsp;&nbsp;&nbsp;&nbsp;<span class="font-color-gray">|</span>&nbsp;&nbsp;&nbsp;&nbsp;
	                 		<span totar="YJFopenApi.php?type=changeMobile" class="font-weight hand merchant_action">修改绑定手机</span>
	                 		<a href="" target="_blank" id="merchant_action_target"><span></span></a>
	                 		<script type="text/javascript">
							  	$(function(){
							  		$("span.merchant_action").bind('click', function(){
							  			var val = $(this).attr('totar'),
							  				$accountType = $("#accountType"),
							  				acVal = $accountType.val();
							  				
							  				$("#merchant_action_target").attr("href", val+'&actype='+acVal).find('span').trigger('click');
							  		});
							  	});
						  </script>
                 		</td>
                    </tr>
            	</tbody>
            </table>
        </fieldset>
                  
        
        <fieldset>     
			<legend><strong>提现查询</strong></legend>
            <table width="98%" cellspacing="1" cellpadding="4" border="0" bgcolor="#FFFFFF">          
				<tbody><tr>
                  <td width="80" align="center"><strong>账户类型：</strong></td>
                  <td width="100">
	                  	<?php 
						$NetGetWay = new NetGetWay();
						$netInfo = $NetGetWay->showGetway('yijifu', $_SESSION['uc']['CompanyID'], '', true);
						?>
						<select id="accountType" name="accountType">
							<?php foreach($netInfo as $nval){
									$selected = $nval['IsDefault'] == 'Y' ? ' selected="selected" ' : '';
									echo '<option value="'.$nval['SignNO'].'" '.$selected.'>'.$getway_account_type[$nval['AccountType']].'</option>';				
								}
							?>
						</select>
                  </td>
                  <td width="60" align="center">
                  	<strong>时间：</strong>从 </td>
                  <td width="100">
					<input type="text" id="sdate" name="sdate" class="inputline" value="<?=$startDate?>" />
                  </td>
				  <td width="20">到</td>
				  <td width="100">
				  	<input type="text" id="edate" name="edate" class="inputline" value="<?=$endtDate?>" />
				  </td>
				  <td>
				  	<a class="mainbtn" id="to-show-draw" style="text-decoration:none;padding:4px 19px;color:#fff;">查询记录</a>
       				<a class="redbtn" id="set-submit-draw" style="text-decoration:none;padding:4px 19px" target="_blank" href="#" >我要提现</a>
				  </td>
				  <td align="right">
				  
				  </td>
                </tr>
            </tbody></table>
        </fieldset>
            <script type="text/javascript">
				$(function(){
					var $acType = $('#accountType'),
						$setDraw = $('#set-submit-draw');
					
					$('#accountType').bind('change',function(){
						var sval = $(this).val();
						if(sval){
							$setDraw.attr('href', 'YJFopenApi.php?type=getMoney&acType='+sval);
						}else{
							alert('请选择账户类型');
							return false;
						}
					});

					//初始化
					$setDraw.attr('href', 'YJFopenApi.php?type=getMoney&acType='+($acType.val()));
				});
			</script>
            
			
			<fieldset>
			<legend><strong>往来对账数据</strong></legend>
			<!-- 记录头显示 -->   
			<table class="clint-info-credit" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="80" align="right" class="right">总记录数：</td>
					<td width="180">总计 <span id="total-count">-</span> 笔交易</td>
					<td width="100" align="right" class="right">本金总金额(元)：</td>
					<td width="180">¥ <span id="total-amounts">-</span></td>
					<td width="100" align="right" class="right">总金额(元)：</td>
					<td width="180">¥ <span id="total-amountsIn">-</span></td>
					<td width="100" align="right" class="right">总手续费(元)：</td>
					<td>¥ <span id="total-charges">-</span></td>
				</tr>
			</table> 
			<div style="height:1px;"></div>
			<!-- 记录体显示 -->  
        	  <table width="98%" cellspacing="0" cellpadding="0" border="0" align="center">
               <thead>
                <tr>
				  <td width="4%" class="bottomlinebold">行号</td>
                  <td width="8%" class="bottomlinebold">状态</td>
                  <td width="10%" class="bottomlinebold">提现时间</td>
                  <td class="bottomlinebold">资金账户名</td>
				  <td width="8%" class="bottomlinebold">银行名称</td>
                  <td width="12%" class="bottomlinebold">银行卡号</td>
				  <td width="9%" class="bottomlinebold">提现总金额(元)</td>
                  <td width="9%" class="bottomlinebold">提现金额(元)</td>
				  <td width="9%" class="bottomlinebold">提现手续费(元)</td>
				  <td width="14%" class="bottomlinebold">提现流水号</td>
                </tr>
     		</thead>      		
      		 <tbody id="log-draw">
				<td colspan="10" align="center"><b>请指定查询时间</b></td>
			</tbody>                
             </table>
       	  </fieldset>

			
			<!-- 分页 -->
			<p class="pagination yopenapi-page" id="yopenapi-page"></p>

       	  </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>