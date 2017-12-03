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
<script type="text/javascript">
            $(function() {
            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });
			$("#tree").treeview({

				collapsed: true,

				animated: "medium",

				control:"#sidetreecontrol",

				persist: "location"

			});
			
			//$(document).delegate("body", "click", function(e) {  
				//closewindowui();
           // });

            $("#bdate").datepicker({changeMonth: true,	changeYear: true});
            $("#edate").datepicker({changeMonth: true,	changeYear: true});

           $('#SubBytton').click(function(){
               var searchName = $('#sercherName').val();
               var bdate = $('#bdate').val();
               var edate = $('#edate').val();
               location.href='credit_info.php?searchname='+searchName+'&bdate='+bdate+'&edate='+edate;
           }); 
		});



</script>
<link rel="stylesheet" type="text/css" href="css/credit.css"/>
<link rel="stylesheet" type="text/css" href="css/icon.css"/>


</head>



<body>

<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>        
    <div id="bodycontent">
        <div class="lineblank"></div>
        
	<div id="searchline">
        	<div class="leftdiv">
        	 <div class="location"><strong>&nbsp;&nbsp;当前位置：</strong><a href="credit_info.php">账期详情</a> </div>
   	        </div>
        </div>
    	<fieldset class="credit_info">
    		<legend>账期概览</legend>
                <?php 
                    $CreditComSumSql = "select count(CompanyID) as CountID from ".DATABASEU.DATATABLE."_order_company where CompanyCredit=1 ";
                    $CreditComSum = $db->get_row($CreditComSumSql);
                ?>
    		<p>开通医统账期的商业公司：<?php echo $CreditComSum['CountID']?>个</p>
                <?php 
                    $CreditMainSql = "select count(ID) as MainID from ".DATABASEU.DATATABLE."_credit_main where CreditStatus='open' ";
                    $CreditMainCount = $db->get_row($CreditMainSql);
                ?>
    		<p>开通医统账期的终端：<?php echo $CreditMainCount['MainID']?>个</p>
                <?php 
                //获取总授信额度 和 总消费额度
                $sqlAmount="SELECT  sum(Amount) as Amount,ROUND(sum(Amount-ResidueAmount),2) as HuanAmount FROM  ".DATABASEU.DATATABLE."_credit_main";
                $sqlAmountSum = $db->get_row($sqlAmount);
                //获取总还款金额
                $HuanAmountSql = "SELECT ROUND(sum( OrderTotal + Interest + OverdueFine),2) AS HuanAmount FROM ".DATABASEU.DATATABLE."_credit_detail where Type = 'return' OR CreditStatus='return'" ;
                $HuanAmountSqlSum = $db->get_row($HuanAmountSql);    
                if(empty($HuanAmountSqlSum['HuanAmount'])){
                    $HuanAmountSqlSum['HuanAmount'] = 0;
                }
                ?>
    		<ul class="money_detail">
				<li class="money_total">
					<i></i>
	                <div id="money_total" class="open"></div>
					<span>总授信额度</span>
				</li>
				<li class="money_return">
					<i></i>			
	                <div id="money_return" class="open"></div>
					<span>总待还款金额</span>
				</li>
				<li class="money_returned">
					<i></i>
	                <div id="money_returned" class="open"></div>
					<span>总已还款金额</span>
				</li>
			</ul>
			<!--<br class="clear"/>-->
    	</fieldset>
    	<fieldset class="credit_info">
    		<legend>客户列表</legend>
    		<div class="search_way">
                    <p>搜索：<input type="text" id="sercherName" value='<?php if(!empty($_GET['searchname'])){ echo $_GET['searchname'];}?>'/></p>
    			<p>统计时间：
                        <label>&nbsp;&nbsp; <input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<?php if(!empty($_GET['bdate'])){ echo $_GET['bdate'];}?>" /></label>
                        --
    			<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<?php if(!empty($_GET['edate'])){ echo $_GET['edate'];}?>" /> </label></p>
                        <input type="submit" value="搜索" id="SubBytton"/>
    		</div>
    		<p class="client">
    			<span class="client_inter pass_active">产生利息客户列表</span>
    			<span class="client_fee">产生滞纳金客户列表</span>
    		</p>
    		<table width="98%" border="0" cellpadding="0" cellspacing="0" class="inter_table">
    			<thead>
    				<tr>
    					<td width="4%">序号</td>
    					<td width="32%">终端名称</td>
    					<td width="28%">商业公司名称</td>
    					<td width="14%">订单SN</td>
    					<td width="9%">订单金额</td>
    					<td width="8%">利息</td>
    					<td width="8%">滞纳金</td>
    				</tr>
    			</thead>
                    <?php 
                    $searchname=!empty($_GET['searchname'])?$_GET['searchname']:'';
                    $bdate=!empty($_GET['bdate'])?$_GET['bdate']:'';
                    $edate=!empty($_GET['edate'])?$_GET['edate']:'';
                    if(!empty($_GET['searchname'])){
                        $searchnamesql = " and client.ClientCompanyName like '%".$searchname."%' or com.CompanyName like '%".$searchname."%'";
                    }else{
                        $searchnamesql ='';
                    }
                    if(!empty($_GET['bdate'])){
                        $bdatesql = " and left(d.RecordDate,10) >='".$bdate."'";
                    }else{
                        $bdatesql='';
                    }
                    if(!empty($_GET['edate'])){
                        $edatesql = " and left(d.RecordDate,10) <='".$edate."'";
                    }else{
                        $edatesql='';
                    }
                    //利息人数
                    //总条数
                    $Interestsqlsum =$db->get_row( "SELECT count(d.ID) as allrow from ".DATABASEU.DATATABLE."_credit_detail AS d  LEFT JOIN ".DATATABLE."_order_client AS client ON d.ClientID = client.ClientID LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS com ON d.CompanyID = com.CompanyID LEFT JOIN ".DATATABLE."_order_orderinfo AS ord ON d.OrderID = ord.OrderID WHERE d.Interest >0 ".$bdatesql.$edatesql.$searchnamesql);
                    $Interestsql = "SELECT d.ID,d.OrderTotal,d.Interest,d.OverdueFine,client.ClientCompanyName,com.CompanyName,ord.OrderSN from ".DATABASEU.DATATABLE."_credit_detail AS d  LEFT JOIN ".DATATABLE."_order_client AS client ON d.ClientID = client.ClientID LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS com ON d.CompanyID = com.CompanyID LEFT JOIN ".DATATABLE."_order_orderinfo AS ord ON d.OrderID = ord.OrderID WHERE d.Interest >0".$bdatesql.$edatesql.$searchnamesql;
                    $page = new ShowPage;
                    $page->PageSize = 50;
                    $page->Total = $Interestsqlsum['allrow'];
                    $page->LinkAry = array("kw"=>$in['kw'],"searchname"=>$in['searchname'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);
                    $list_data = $db->get_results($Interestsql." ".$page->OffSet());
                    //滞纳金
                    $Interestsqlsum2 = "SELECT count(d.ID) as allrow from ".DATABASEU.DATATABLE."_credit_detail AS d  LEFT JOIN ".DATATABLE."_order_client AS client ON d.ClientID = client.ClientID LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS com ON d.CompanyID = com.CompanyID LEFT JOIN ".DATATABLE."_order_orderinfo AS ord ON d.OrderID = ord.OrderID WHERE d.OverdueFine >0".$bdatesql.$edatesql.$searchnamesql;
                    $Interestsqlsum3 = $db->get_row($Interestsqlsum2);
                    $Interestsql2 = "SELECT d.ID,d.OrderTotal,d.Interest,d.OverdueFine,client.ClientCompanyName,com.CompanyName,ord.OrderSN from ".DATABASEU.DATATABLE."_credit_detail AS d  LEFT JOIN ".DATATABLE."_order_client AS client ON d.ClientID = client.ClientID LEFT JOIN ".DATABASEU.DATATABLE."_order_company AS com ON d.CompanyID = com.CompanyID LEFT JOIN ".DATATABLE."_order_orderinfo AS ord ON d.OrderID = ord.OrderID WHERE d.OverdueFine >0".$bdatesql.$edatesql.$searchnamesql;
                    $page1 = new ShowPage;
                    $page1->PageSize = 50;
                    $page1->Total = $Interestsqlsum3['allrow'];
                    $page1->LinkAry = array("kw"=>$in['kw'],"searchname"=>$in['searchname'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);
                    $list_data2 = $db->get_results($Interestsql2." ".$page1->OffSet());
                    ?>
    			<tbody>
                            <?php foreach ($list_data as $k => $v) { ?>
    				<tr>
    					<td><?php echo $v['ID']?></td>
    					<td><?php echo $v['ClientCompanyName']?></td>
    					<td><?php echo $v['CompanyName']?> </td>
    					<td><?php echo $v['OrderSN']?></td>
    					<td><?php echo $v['OrderTotal']?></td>
    					<td><?php echo $v['Interest']?></td>
    					<td><?php echo $v['OverdueFine']?></td>
    				</tr>
                                <tr>
                                    <td></td>
                                    <td><? echo $page->ShowLink('credit_info.php');?></td>
                                </tr>
                            <?php }?>
    			</tbody>
    		</table>
    		<table width="98%" border="0" cellpadding="0" cellspacing="0" class="fee_table hide">
    			<thead>
    				<tr>
    					<td width="4%">序号</td>
    					<td width="32%">终端名称</td>
    					<td width="28%">商业公司名称</td>
    					<td width="14%">订单SN</td>
    					<td width="9%">订单金额</td>
    					<td width="8%">利息</td>
    					<td width="8%">滞纳金</td>
    				</tr>
    			</thead>
    			<tbody>
                            <?php foreach ($list_data2 as $k => $v){?>
    				<tr>
    					<td><?php echo $v['ID']?></td>
    					<td><?php echo $v['ClientCompanyName']?></td>
    					<td><?php echo $v['CompanyName']?> </td>
    					<td><?php echo $v['OrderSN']?></td>
    					<td><?php echo $v['OrderTotal']?></td>
    					<td><?php echo $v['Interest']?></td>
    					<td><?php echo $v['OverdueFine']?></td>
    				</tr>
                            <?php }?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $page1->ShowLink('credit_info.php');?></td>
                                </tr>
    			</tbody>
    		</table>
    	</fieldset>
   	</div>
    <?php include_once ("bottom.php");?>
</body>

<script type="text/javascript">
	//利息滞纳金选择
	function choose(node1,node2,node3){
		var node1 = $('.'+node1);
		var node2 = $('.'+node2);
		var node3 = $('.'+node3);
		
		$('.client span').removeClass('pass_active');
		node1.addClass('pass_active');
		
		node2.addClass('hide');
		node3.removeClass('hide');
	}
	$('.client_inter').click(function(){
		choose('client_inter','fee_table','inter_table');
	});
	$('.client_fee').click(function(){
		choose('client_fee','inter_table','fee_table');
	});
	
	
	
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

/*开通账期信息*/
	//总额度
var option1 = {
        value: 100,//百分比,必填。总额度，100或0
        name: '￥<?php  if(empty($sqlAmountSum['Amount'])){ echo '0';}else{ echo $sqlAmountSum['Amount']; }?>',//必填
        backgroundColor: null,
        color: ['#01a157','#f49400'],
        fontSize: 16,
        domEle: document.getElementById("money_total")//必填
    },percentPie1 = new PercentPie(option1);
    percentPie1.init();
    
    //待还款
    var option2 = {
        value: 100,//百分比,必填
        name: '￥<?php if(empty($sqlAmountSum['HuanAmount'])){echo'0';}else{ echo $sqlAmountSum['HuanAmount'];}  ?>',//必填
        backgroundColor: null,
        color: ['#f49400', 'rgba(236,118,26,0.3)'],
        fontSize: 16,
        domEle: document.getElementById("money_return")//必填
    },percentPie2 = new PercentPie(option2);
    percentPie2.init();

    //已还款
    var option3 = {
        value: 100,//百分比,必填
        name: '￥<?php echo $HuanAmountSqlSum['HuanAmount']?>',//必填
        backgroundColor: null,
        color: ['#28b7a9','rgba(40,183,169,0.3)'],
        fontSize: 16,
        domEle: document.getElementById("money_returned")//必填
    },percentPie3 = new PercentPie(option3);
    percentPie3.init();
</script>
</html>
