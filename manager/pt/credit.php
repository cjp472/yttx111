<?php 
$menu_flag = "credit_client";
include_once ("header.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name='robots' content='noindex,nofollow' />

<title><? echo SITE_NAME;?> - 管理平台</title>

<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="css/jquery.treeview.css" />

<link rel="stylesheet" href="css/showpage.css" />

<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>

<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>

<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>

<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

<script src="js/echarts.js" type="text/javascript" charset="utf-8"></script>

<link rel="stylesheet" type="text/css" href="css/credit.css"/>
<link rel="stylesheet" type="text/css" href="css/icon.css"/>
</head>



<body>

<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>        
<?php 
// 查询买家信息
$ClientID = $in['id'];
$CompanyID = $in['cpid'];
if(empty($ClientID)){
    echo'请求出错';die;
}else{
    if($in['m'] == OneApprove){
        print_r(1);die;
    }
    $ClientID = $in['id'];
    $CreditSql = "select C.ClientCompanyName,B.*, M.* from ".DATABASEU.DATATABLE."_credit_main AS M LEFT JOIN ".DATATABLE."_order_client AS C ON M.ClientID = C.ClientID LEFT JOIN ".DATABASEU.DATATABLE."_credit_body AS B ON M.ClientID = B.ClientID AND M.CompanyID = B.CompanyID WHERE M.ClientID='".$ClientID."' and M.CompanyID ='".$CompanyID."'";
    $CreditData = $db ->get_row($CreditSql);   
    $last_month = date('Y-m', strtotime('last month'));
    $last['first'] = $last_month . '-01';
    $last['end'] = date('Y-m-d', strtotime("$last_month +1 month -1 day +23 hours +59 minutes +59 seconds"));    
    $CreditDetSql = "select sum(OrderTotal+Interest+OverdueFine) as TotalSum from ".DATABASEU.DATATABLE."_credit_detail where  CompanyID=".$CompanyID."  and ClientID = ".$ClientID." and Type = 'out' and  left(RecordDate,10)>='". $last['first']."' and left(RecordDate,10)<='".$last['end']."'";
    $CreditDetSum = $db->get_row($CreditDetSql);
    $CreditDetSqla = $db->get_row("select PayDate from ".DATABASEU.DATATABLE."_credit_detail where CompanyID=".$CompanyID."  and ClientID = ".$ClientID." and Type = 'out' ORDER BY ID DESC");
    if(empty($CreditDetSqla['PayDate'])){
        $CreditDetSqla['PayDate'] = '--';
    }else{
        $CreditDetSqla['PayDate'] = date('Y-m-d', strtotime($CreditDetSqla['PayDate']));
    }
    if(empty($CreditData)){
        $CreditData['OpenDate'] = '--';
                
    }else{
        $CreditData['OpenDate'] = date('Y-m-d', strtotime($CreditData['OpenDate']));
    }
}
?>
	<span class="company_name">【<?php echo $CreditData['ClientCompanyName']?>】</span>
    <div id="bodycontent">
        <div class="credit">
        	
            <ul class="credit-detail">
				<li class="total">
					<i></i>
	                <?php if($CreditData[CreditStatus] == closed || $CreditData[CreditStatus]==''|| $CreditData[CreditStatus]== normal || $CreditData[CreditStatus]== one){?>
	                <div class="notopen border1" id="credit_total"></div>
	                <?php }else{?>
	                <div id="credit_total" class="open"></div>
	                <?php }?>
					<span>总额度</span>
				</li>
				<li class="used">
					<i></i>			
	                <?php if($CreditData[CreditStatus] == closed || $CreditData[CreditStatus]==''|| $CreditData[CreditStatus]== normal || $CreditData[CreditStatus]== one){?>
	                <div class="notopen border2" id="credit_used"></div>
	                <?php }else{?>
	                <div id="credit_used" class="open"></div>
	                <?php }?>
					<span>已用</span>
				</li>
				<li class="unused">
					<i></i>
	                <?php if($CreditData[CreditStatus] == closed || $CreditData[CreditStatus]=='' || $CreditData[CreditStatus]== normal || $CreditData[CreditStatus]== one){?>
	                <div class="notopen border3" id="credit_unused"></div>
	                <?php }else{?>
	                <div id="credit_unused" class="open"></div>
	                <?php }?>
					<span>余额</span>
				</li>
			</ul>
			<div class="check_choose clear hide except">
				<p class="choose_title">
					<span class="pass_title1">通过</span>
					<span class="pass_title2 pass_active">未通过</span>
				</p>
				<div>
					<div class="pass1">
						<input type="text" value="1、资质正常" title = "1、资质正常" readonly="true" id="oneOption1" onclick="chooseReason('oneOption1','OneContent')"/>
	                	<input type="text" value="2、优质客户" title="2、优质客户" readonly="true" id="oneOption2" onclick="chooseReason('oneOption2','OneContent')"/>
	                	<input type="text" value="3、采购较符合预期" title="3、采购较符合预期" readonly="true" id="oneOption3" onclick="chooseReason('oneOption3','OneContent')"/>
	                	<input type="text" value="其他" title="其他" readonly="true" id="oneOption4" onclick="chooseReason('oneOption4','OneContent')"/>
					</div>
					<div class="unpass1 hide">
						<input type="text" value="1、资质不正常，公章不清晰" title="1、资质不正常，公章不清晰" readonly="true" id="oneOption5" onclick="chooseReason('oneOption5','OneContent')"/>
	                	<input type="text" value="2、采购不符合预期" title="2、采购不符合预期" readonly="true" id="oneOption6" onclick="chooseReason('oneOption6','OneContent')"/>
	                	<input type="text" value="其他" title="其他" readonly="true" id="oneOption7" onclick="chooseReason('oneOption7','OneContent')"/>
					</div>
                </div>
           	</div>
           	<div class="check_choose1 clear hide except">
				<p class="choose_title1">
					<span class="pass_title3">通过</span>
					<span class="pass_title4 pass_active">未通过</span>
				</p>
				<div>
					<div class="pass2">	
	                	<input type="text" value="授信金额符合要求" title="授信金额符合要求" readonly="true" id="twoOption1" onclick="chooseReason('twoOption1','TwoContent')"/>
	                	<input type="text" value="其他" title="其他" readonly="true" id="twoOption2" onclick="chooseReason('twoOption2','TwoContent')"/>
					</div>
					<div class="unpass2 hide">
						<input type="text" value="1、资质存在异常" title = "1、资质存在异常" readonly="true" id="twoOption3" onclick="chooseReason('twoOption3','TwoContent')"/>
	                	<input type="text" value="2、金额不符合授信要求" title = "2、金额不符合授信要求" readonly="true" id="twoOption4" onclick="chooseReason('twoOption4','TwoContent')"/>
	                	<input type="text" value="其他" title="其他" readonly="true" id="twoOption5" onclick="chooseReason('twoOption5','TwoContent')"/>
					</div>
                </div>
           	</div>
		<div class="credit_open">
			<div class="credit_open_left">
				<p class="credit_honor">
					<span>信用：</span>
                    <i class="check">良好</i>
					<i>一般</i>
					<i>坏账风险</i>
				</p>
				<p class="credit_date">
					<span>开通日期：<?php echo  $CreditData['OpenDate']; ?></span>
					<span class="data_use">首次使用日期：<?php echo $CreditDetSqla['PayDate']; ?></span>
				</p>
				<div class="credit_sure">
					<div class="check_number">
                        <?php if($CreditData[CreditStatus] == open ){?>
                        <p>一级审核员：<span id="SOneApprove"><?php echo $CreditData['OneApprove']?></span></p>
                        <?php }else{?>
                            <p>一级审核员：<span id="OneApprove"><?php echo $_SESSION['uinfo']['username']; ?></span></p>
                        <?php }?>
                            <?php if($CreditData[CreditStatus] == open ){?>
                            <p>授信额度：<input type="text" value='<?php echo $CreditData['Amount']?>' disabled='ture'/></p>
                            <?php }else{?>
                            <p>授信额度：<input type="text" value='<?php echo $CreditData['Amount']?>' id="Amount"/></p>
                            <?php }?>
                            <?php if($CreditData[CreditStatus] == open ){?>
                            <div class="check_text">
                            	<span>审核描述：</span><textarea  cols="30" rows="10"  disabled='ture' class="checked_text"><?php echo $CreditData['OneContent']?></textarea>
                            </div>
                            <?php }else{?>
                             <div class="check_text except">
                            	<span>审核描述：</span>
                            	<input name="" id="OneContent" readonly="true" unselectable='on' onfocus="this.blur()" value="<?php echo $CreditData['OneContent']?>"  onclick="check('one1','one2','check_choose')">
                            	<i class="iconfont icon-xiala one1" onclick="check('one1','one2','check_choose')"></i>
								<i class="iconfont icon-xiala-copy one2 hide" onclick="check('one1','one2','check_choose')"></i>
                            </div>
                            <?php }?>
					</div>
					
					<div class="check_button">
                        <?php  if($CreditData[CreditStatus] != open ){?>           
                        <p onclick="OneApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)" class="open_button open_button1">开通账期</p>
                        <p onclick="WeiApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)" class="not_button not_button1">未通过</p>
                        <?php }else{?>
                        <p onclick="CloseApprove(<?php echo $ClientID?>,<?php echo $CompanyID?>)" class="close_button">关闭账期</p>
                        <?php }?>
					</div>
				</div>
				<br class="clear"/>
				
			</div>
			
			<div class="credit_open_right">
				<p>近5日待还款金额：￥<?php echo MoneyFormat::MoneyOfFenToYuan(round($CreditDetSum['TotalSum'],2))?><a href="">通知</a><a href="credit_trade.php?id=<?php echo $ClientID?>&type=Upmonth&cpid=<?php echo $CompanyID?>">查看</a></p>
				<div  class="credit_sure">
					<div class="check_number">
                        <p>二级审核员：             <?php if($CreditData[CreditStatus] == open ){?>    
                                                        <span><?php echo $CreditData['TwoApprove']; ?></span></p>
                                                    <?php }else{?>
                                                        <span id="TwoApprove"><?php echo $_SESSION['uinfo']['username']; ?></span></p>
                                                    <?php }?>
                                        
                                    <p>授信额度：<input type="text"  value='<?php echo $CreditData['Amount']?>' disabled='ture'/></p>
                                    <?php if($CreditData[CreditStatus] == open ){?>
                                    <div class="check_text">
                                        <span>审核描述：</span><textarea name="" id="" cols="30" rows="10" value="" disabled='ture' class="checked_text"><?php echo $CreditData['TwoContent']?></textarea>
                                    </div>
                                    <?php }else{?>
                                    <div class="check_text except">
                                        <span>审核描述：</span>
                                    <?php  if($CreditData[CreditStatus] == one ){?> 
                                    	<input name="" id="TwoContent" value="<?php echo $CreditData['TwoContent']?>" readonly='ture' unselectable='on' onfocus="this.blur()" onclick="check('two1','two2','check_choose1')">
                                        <i class="iconfont icon-xiala two1" onclick="check('two1','two2','check_choose1')"></i>
										<i class="iconfont icon-xiala-copy two2 hide"  onclick="check('two1','two2','check_choose1')"></i>
									<?php }else{?>
										<input name="" id="TwoContent" value="<?php echo $CreditData['TwoContent']?>" disabled='ture'>
										<i class="iconfont icon-xiala two1"></i>
										<i class="iconfont icon-xiala-copy two2 hide"></i>
									<?php }?>
                                    </div>
                                    <?php }?>

					</div>
					<div class="check_button">
                        <?php  if($CreditData[CreditStatus] != open ){?>  
                        <?php  if($CreditData[CreditStatus] == one ){?>  
                        <p onclick="TwoApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)" class="open_button open_button2">确认开通</p>
                        <p onclick="TwoWeiApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)" class="not_button not_button2">未通过</p>
                          <?php }?>  
                        <?php }?>
                    </div>
				</div>
				<br class="clear"/>
				
			</div>
			
		</div>
		
		<br class="clear"/>
		<br class="clear"/>
		<p class="credit_footer clear">
            <a href="credit_data.php?id=<?php echo $ClientID?>&cpid=<?php echo $CompanyID;?>">查看资料</a>
            <a href="credit_trade.php?id=<?php echo $ClientID;?>&cpid=<?php echo $CompanyID;?>">账期对账</a>
		</p>
		<br class="clear"/>
   	</div>
</div> 
    <?php include_once ("bottom.php");?>
</body>

<script type="text/javascript">
	function check(node1,node2,node3){
		var one1        = $('.'+node1);
		var one2        = $('.'+node2);
		var check_choose= $('.'+node3);
		
		if(one1.hasClass('hide')){
			one1.removeClass('hide');
			one2.addClass('hide');
			check_choose.addClass('hide');
		}else{
			one1.addClass('hide');
			one2.removeClass('hide');
			check_choose.removeClass('hide');
		};
	};
	$('body').click(function(e){
		$('.check_choose,.check_choose1').addClass('hide');
		
		$('.one1,.two1').removeClass('hide');
		$('.one2,.two2').addClass('hide');

	});
	$('.except').click(function(e){
		e.stopPropagation();
	});
	//通过未通过
	function choose(node1,node2,node3){
		var node1 = $('.'+node1);
		var node2 = $('.'+node2);
		var node3 = $('.'+node3);

		if(node1.parent().hasClass('choose_title')){
			$('.choose_title span').removeClass('pass_active');
		}else{
			$('.choose_title1 span').removeClass('pass_active');
		};
		node1.addClass('pass_active');
		
		node2.removeClass('hide');
		node3.addClass('hide');
	}
	$('.pass_title1').click(function(){
		choose('pass_title2','pass1','unpass1');
	});
	$('.pass_title2').click(function(){
		choose('pass_title1','unpass1','pass1');
	});
	$('.pass_title3').click(function(){
		choose('pass_title4','pass2','unpass2');
	});
	$('.pass_title4').click(function(){
		choose('pass_title3','unpass2','pass2');
	});
	//通过未通过原因
	function chooseReason(node1,node2){
		var TwoContent= $('#'+node2);
		
		if($("#"+node1).val() == '其他'){
			TwoContent.val("");
			TwoContent.removeAttr("readonly").focus();
		}else{
			TwoContent.val($("#"+node1).val());
			TwoContent.attr('title',$("#"+node1).val()).attr('readonly','true');
		};
		
		if($("#"+node1).parent().hasClass('pass1') || $("#"+node1).parent().hasClass('unpass1')){
			$('.check_choose,.one2').addClass('hide');
			$('.one1').removeClass('hide');
			if(!$('.pass_title1').hasClass('pass_active')){
				$('.open_button1').removeAttr('disabled','true').removeClass('global-gray').attr('onclick','OneApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)');
				$('.not_button1').attr('disabled','true').addClass('global-gray').removeAttr('onclick');
				
			}else{
				$('.not_button1').removeAttr('disabled','true').removeClass('global-gray').attr('onclick','WeiApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)');
				$('.open_button1').attr('disabled','true').addClass('global-gray').removeAttr('onclick');
				
			}
		}else{
			$('.check_choose1,.two2').addClass('hide');
			$('.two1').removeClass('hide');
			if(!$('.pass_title3').hasClass('pass_active')){
				$('.open_button2').removeAttr('disabled','true').removeClass('global-gray').attr('onclick','TwoApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)');
				$('.not_button2').attr('disabled','true').addClass('global-gray').removeAttr('onclick');
			}else{
				$('.not_button2').removeAttr('disabled','true').removeClass('global-gray').attr('onclick','TwoWeiApprove(<?php echo $ClientID?>,<?php echo $CompanyID ?>)');
				$('.open_button2').attr('disabled','true').addClass('global-gray').removeAttr('onclick');
			
			}
		}
		

	};
	
	
	
        function OneApprove(a,b){
            var Amount = $('#Amount').val();
            var OneApprove = $('#OneApprove').html();
                if(Amount == ''){
                    alert('授信金额不能为空');
                    return false;
                }
                if(isNaN(Amount)){
                    alert(Amount +"不是数字");return false;
                }
                if(Amount<=0){
                    alert('不能小于等于零');return false;
                }
                var OneContentOne = $('#OneContent').val();
                if(OneContentOne==''){
                   alert('审核描述不能为空');return false; 
                }
            $.post("do_manager.php?m=OneApprove",{ClientID:a,Amount:Amount,OneApprove:OneApprove,OneContentOne:OneContentOne,CompanyID:b},
            function(result){
                if(result == 'ok'){
                	alert("操作成功")
                	function myrefresh(){
   						window.location.reload();
					}
					setTimeout(myrefresh(),1000); //指定1秒刷新一次
     
                }else{
                	alert(result);
                }
            });
        }
        function TwoApprove(b,a){
            var TwoApprove = $('#TwoApprove').html();
            var TwoContent = $('#TwoContent').val();
            if(TwoContent == ''){
                alert('审核描述不能为空');return false;
            }
            $.post("do_manager.php?m=TwoApprove",{ClientID:b,TwoApprove:TwoApprove,TwoContent:TwoContent,CompanyID:a},
            function(result){
                if(result == 'ok'){
                	alert("操作成功")
                	function myrefresh(){
   						window.location.reload();
   						window.sessionStorage.setItem("success2", "success2");
					}
					setTimeout(myrefresh(),1000); //指定1秒刷新一次
     
                }else{
                	alert(result);
                }
            });
        }
        function CloseApprove(c,f){
            var SOneApprove = $('#SOneApprove').val();
            $.post("do_manager.php?m=CloseApprove",{ClientID:c,SOneApprove:SOneApprove,CompanyID:f},
            function(result){
                if(result == 'ok'){
                	alert("操作成功")
                	function myrefresh(){
   						window.location.reload();
					}
					setTimeout(myrefresh(),1000); //指定1秒刷新一次
     
                }else{
                	alert(result);
                }
            });
        }
        function WeiApprove(d,e){
           
            var OneContent = $('#OneContent').val();
            var OneApprove = $('#OneApprove').html();  //一级审核人
            var Amount = $('#Amount').val(); //金额           
            if(isNaN(Amount)){
                alert(Amount +"不是数字");return false;
            }
            if(Amount == ''){
                Amount = 0;
            }
            if(OneContent == ''){
                alert('请填写审核描述');return false;
            }
            $.post("do_manager.php?m=OneWeiApprove",{ClientID:d,OneContent:OneContent,OneApprove:OneApprove,Amount:Amount,CompanyID:e},
                function(result){
                    if(result == 'ok'){
	                	alert("操作成功")
	                	function myrefresh(){
	   						window.location.reload();
						}
						setTimeout(myrefresh(),1000); //指定1秒刷新一次
	     
	                }else{
	                	alert(result);
	                }
                });
            
        }
        function TwoWeiApprove(e,a){
            var TwoContent = $('#TwoContent').val();
            var TwoApprove = $('#TwoApprove').html();
            if(TwoContent==''){
                alert('请填写审核描述');return false;
            }
            $.post("do_manager.php?m=TwoWeiApprove",{ClientID:e,TwoContent:TwoContent,TwoApprove:TwoApprove,CompanyID:a},
                function(result){
                    if(result == 'ok'){
                	alert("操作成功")
                	function myrefresh(){
   						window.location.reload();
					}
					setTimeout(myrefresh(),1000); //指定1秒刷新一次
     
                }else{
                	alert(result);
                }
            });
        }
	function PercentPie(option){
	    this.backgroundColor = option.backgroundColor||'#fff';
	    this.color           = option.color||['#38a8da','#d4effa'];
	    this.fontSize        = option.fontSize||12;
	    this.domEle          = option.domEle;
	    this.value           = option.value;
	    this.name            = option.name;
	    this.title           = option.title;
	}
	/*环形图*/
	PercentPie.prototype.init = function(){
	    var _that = this;
	    var option = {
	        backgroundColor:_that.backgroundColor,
	        color:_that.color,
	        title: {
	            text: _that.title,
	            top:'3%',
	            left:'1%',
	            textStyle:{
	                color: '#333',
	                fontStyle: 'normal',
	                fontWeight: 'bold',
	                fontFamily: 'sans-serif',
	                fontSize: 16,
	            }
	        },
	        series: [{
	            name: '来源',
	            type: 'pie',
	            radius: ['100%', '88%'],
	            avoidLabelOverlap: false,
	            hoverAnimation:false,
	            label: {
	                normal: {
	                    show: false,
	                    position: 'center',
	                    textStyle: {
	                    		color: "#333",
	                        fontSize: _that.fontSize,
	                        fontWeight: 'bold'
	                    },
	                    formatter:_that.name
	                }
	            },
	            data: [{
	                    value: _that.value,
	                    name: _that.name,
	                    label:{
	                        normal:{
	                            show:true
	                        }
	                    }
	                 },
	                {
	                    value: 100-_that.value,
	                    name: ''
	                }
	            ]
	        }]
	    };
	
	    echarts.init(_that.domEle).setOption(option);
	};
   <?php  if ($CreditData['CreditStatus'] != open){ ?>
/*未开通*/
    var option1 = {
        value:0,//百分比,必填。总额度，100或0
        name:'未开通',//必填
        backgroundColor:null,
        color:['#01a157','rgba(1,161,87,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_total")//必填
    },percentPie1 = new PercentPie(option1);
    percentPie1.init();
    
  //已用
    var option2 = {
        value:0,//百分比,必填
        name:'未开通',//必填
        backgroundColor:null,
        color:['#f49400','rgba(236,118,26,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_used")//必填
    },percentPie2 = new PercentPie(option2);
    percentPie2.init();

    //剩余
    var option3 = {
        value:0,//百分比,必填
        name:'未开通',//必填
        backgroundColor:null,
        color:['#28b7a9','rgba(40,183,169,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_unused")//必填
    },percentPie3 = new PercentPie(option3);
    percentPie3.init();    
   <?php }else{?>
/*已开通*/
var option1 = {
        value:100,//百分比,必填。总额度，100或0
        name:'￥<?php echo $CreditData['Amount']?>',//必填
        backgroundColor:null,
        color:['#01a157','#f49400'],
        fontSize:16,
        domEle:document.getElementById("credit_total")//必填
    },percentPie1 = new PercentPie(option1);
    percentPie1.init();
    
    //已用
    var option2 = {
        value:<?php echo ($CreditData['Amount']-$CreditData['ResidueAmount'])/$CreditData['Amount'] *100?>,//百分比,必填
        name:'￥<?php echo round($CreditData['Amount']-$CreditData['ResidueAmount'],2)?>',//必填
        backgroundColor:null,
        color:['#f49400', 'rgba(236,118,26,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_used")//必填
    },percentPie2 = new PercentPie(option2);
    percentPie2.init();

    //剩余
    var option3 = {
        value:<?php echo $CreditData['ResidueAmount']/$CreditData['Amount'] *100?>,//百分比,必填
        name:'￥<?php echo $CreditData['ResidueAmount']?>',//必填
        backgroundColor:null,
        color:['#28b7a9','rgba(40,183,169,0.3)'],
        fontSize:16,
        domEle:document.getElementById("credit_unused")//必填
    },percentPie3 = new PercentPie(option3);
    percentPie3.init();

<?php }?>
    
    //账号开通？
   
</script>
</html>
