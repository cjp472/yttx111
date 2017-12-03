<?php 
$menu_flag = "finance";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");

setcookie("backurl", $_SERVER['REQUEST_URI']);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/yijifu.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        		<tr>
					<td aling="right"><div class="location"><strong>当前位置：</strong><a href="yijifu.php">支付管理</a></div></td>
				</tr>
			 </table>      
        </div>
    	

        <div class="bline">
         
         <?php 
            //读取开户类型
            $NetGetWay = new NetGetWay();
			$netInfo = $NetGetWay->showGetway('yijifu', $_SESSION['uc']['CompanyID'], '', true);
			
			$myType = array();
			$getWay = array();
			foreach($netInfo as $yval){
			    $myType[] = $yval['AccountType'];
			    $getWay[$yval['AccountType']] = $yval['SignNO'];
			}
			$myType = array_unique($myType);
         ?>
       <div style="margin-top:10px;">

			<strong>我的默认收款账户 :</strong>
			<?php if(empty($netInfo)){?>
			您还未开通快捷支付！请根据下面的操作指引选择需要的开户类型
			<?php }else{?>
    			<?php foreach($netInfo as $yval){?>
    			<label>
    				<input type="radio" data-name="<?php echo $yval['SignNO'];?>" name="mydefault" value="<?php echo $yval['AccountType'];?>" <?php if($yval['IsDefault']=='Y'){echo 'checked=checked';}?> />
    				<?php echo $getway_account_type[$yval['AccountType']];?>【<?php echo $yval['SignNO'];?>】
    			</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    			<?php }?>
    			<?php if(count($netInfo) > 1){?>
			( <span class="red">*</span>&nbsp;请指定一个默认的收款账户)
				<?php }?>
			<?php }?><!-- 如果已开户了 -->
      </div>

      <div style="margin-top:10px;">
      <img src="img/lt.png" style="width:100.15%;margin-left:-2px;">
      </div>

        <script type="text/javascript">
        	$("input[name='mydefault']").bind("click", function(){
        		var _this = $(this);
        		var myDefault = _this.val(),
        			dName = _this.attr('data-name');
    			$.post('do_finance.php', 
    	    		{
						m : 'setMyDefault',
						myDefault : myDefault,
						dName : dName
        			}, function(msg){
            			
            		});
        	});
        
        </script>
         
         <!-- 开通快捷支付 START -->
         <div style="    width: 100%;
                         height: 350px;">

        <ul class="company-set" style="">


                <div class="tips">
                    <p class="tips-title">【企业账户】</p>
                    <p>企业账户是以公司名称开立的银行结算账户作为快捷支付交易的收款账户，便于公司公对公的收款交易使用。</p>
                    <?php if(in_array('company', $myType)){?>
                    	<a href="YJFopenApi.php?type=wallet&actype=company" target="_balnk" class="btn btn-bule">管理账户</a>
                    <?php }else{?>
                    	<a href="YJFopenApi.php?type=openPaymentAccount&user=ENTERPRISE" target="_balnk" class="btn btn-bule">立即开通</a>
                    	<?php if(!empty($in['message'])){?>
                    	<span style="font-size:16px;color:red;">（<?php echo $in['message']?>）</span>
                    	<?php }?>
                    <?php }?>
                </div>
            </li>

                <div class="image-ico">
                    <img src="img/yuan.jpg" alt="" style="width:284px">
                </div>
                <div class="tips">
                    <p class="tips-title" style="text-align:right;">【个人账户】</p>
                    <p>个人账户是以个人名字开立的银行账户作为快捷支付交易的收款账户，便于法人个体或非法人个体快速管理收款账户。</p>
                    <?php if(in_array('personal', $myType)){?>
                    	<a href="YJFopenApi.php?type=wallet&actype=personal" target="_balnk" class="btn btn-green">管理账户</a>
                    <?php }else{?>
                    	<a href="YJFopenApi.php?type=openPaymentAccount&user=PERSONAL" target="_balnk" class="btn btn-green">立即开通</a>
                    	<?php if(!empty($in['message'])){?>
                    	<span style="font-size:16px;color:red;">（<?php echo $in['message']?>）</span>
                    	<?php }?>
                    <?php }?>
                </div>
            </li>
        </ul>

        </div>








    <!-- 开通快捷支付 END -->
                  
        
       
            
			

       	  </div>
        <br style="clear:both;" />
    </div>
    

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>  
</body>
</html>