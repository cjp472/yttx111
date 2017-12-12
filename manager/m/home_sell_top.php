        <li style="height:45%">
            <div id="app-sell" style="width:100%">
                <template style="width:100%">
                    <el-tabs v-model="activeName" @tab-click="handleClick" style="position:relative;width:100%">

                        <el-tab-pane name="first" disabled="false">
                            <span slot="label" style="display:block;margin-top:7px;font-size:16px;width:130px;color:#666"><i class="iconfont icon-Performance" style="font-size:24px;color:#639DE0;margin-right:10px;"></i>业绩简报</span>
                        </el-tab-pane>
                        <el-tab-pane label="7天" name="second" style="width:100%">
                            <div id="main1" style="width:100%;height:310px;margin:0 auto;"></div>
                        </el-tab-pane>
                        <el-tab-pane label="30天" name="third" style="width:100%">
                            <div id="main2" style="height:310px;margin:0 auto;"></div>
                        </el-tab-pane>
                        <el-tab-pane label="本月" name="fourth" style="width:100%">
                            <div id="main3" style="height:310px;margin:0 auto;"></div>
                            </el-tab-pane>
                    </el-tabs>
                </template>
            </div>
        </li>
<script>
    var Main = {
	        data() {
	        return {
	            activeName: 'second'
	        };
	    },
	    methods: {
	        handleClick(tab, event) {
              console.log(tab._uid);
              if(tab._uid===14){
                 myChartSev.setOption(optionSN);
              }else if(tab._uid===15){
                 myChartThird.setOption(optionThird);
              }else if(tab._uid===16){
                 myChartMon.setOption(optionMonth);
              }
           }
	    }
    };
    var Ctor = Vue.extend(Main)
    new Ctor().$mount('#app-sell')
        var chartFirstWidth = $('#main1').width();
        $('#main2').css('width', chartFirstWidth);
        $('#main3').css('width', chartFirstWidth);
</script>
<?php
//时间计算，修正1秒误差
$secPer = 86400;
$today  = strtotime(date('Y-m-d')." 23:59:59") + 1;
$yestodayStart = $today - $secPer * 2;
$yestodayEnd   = $today - $secPer - 1;

    //首页商品销量区分商业公司和代理商 wkk
    $user_flag = trim($_SESSION['uinfo']['userflag']);
    $userid=$_SESSION['uinfo']['userid'];
    if($user_flag == '2'){
        $nearSev = $today - $secPer * 7;
        $yejiSev = "SELECT
                      COUNT(1) AS total,
                                  SUM(OrderTotal) AS Tmoney,
                                  FROM_UNIXTIME(OrderDate, '%m.%d') AS Tdate
                                FROM
                                  ".DATATABLE."_order_orderinfo
                                WHERE OrderCompany=".$_SESSION['uc']['CompanyID']."
                                AND OrderStatus NOT IN(8,9)
                                AND OrderDate>".$nearSev." AND OrderDate<".($today-1)."
                                GROUP BY Tdate
                                LIMIT 7 ";
        $yejiSevInfo = $db->get_results($yejiSev);
        $yejiSevLine = $yejiSevName = $yejiSevCount = array();
        foreach($yejiSevInfo as $sev){
                $yejiSevLine[]  = $sev['Tmoney'];
                $yejiSevCount[] = $sev['total'];
                $yejiSevName[]  = "'".$sev['Tdate']."'";
        }
        
        print_r($yejiSev);
        
    }else{
//最近7天
$nearSev = $today - $secPer * 7;
$yejiSev = "SELECT
              COUNT(1) AS total,
			  SUM(OrderTotal) AS Tmoney,
			  FROM_UNIXTIME(OrderDate, '%m.%d') AS Tdate
			FROM
			  ".DATATABLE."_order_orderinfo
			WHERE OrderCompany=".$_SESSION['uc']['CompanyID']."
    			AND OrderStatus NOT IN(8,9)
    			AND OrderDate>".$nearSev." AND OrderDate<".($today-1)."
			GROUP BY Tdate
			LIMIT 7 ";
$yejiSevInfo = $db->get_results($yejiSev);
$yejiSevLine = $yejiSevName = $yejiSevCount = array();
foreach($yejiSevInfo as $sev){
	$yejiSevLine[]  = $sev['Tmoney'];
	$yejiSevCount[] = $sev['total'];
	$yejiSevName[]  = "'".$sev['Tdate']."'";
}

//最近30天
$thirdStart = $today - $secPer * 30;
$yejiThird = "SELECT
                COUNT(1) AS total,
				SUM(OrderTotal) AS Tmoney,
			    FROM_UNIXTIME(OrderDate, '%m.%d') AS Tdate
			  FROM
				".DATATABLE."_order_orderinfo
			  WHERE OrderCompany=".$_SESSION['uc']['CompanyID']."
					AND OrderStatus NOT IN(8,9)
			        AND OrderDate>".$thirdStart." AND OrderDate<".($today-1)."
			  GROUP BY Tdate
			  LIMIT 30 ";
$yejiThirdInfo = $db->get_results($yejiThird);
$yejiThirdLine = $yejiThirdName = $yejiThirdCount = array();
foreach($yejiThirdInfo as $sev){
	$yejiThirdLine[]  = $sev['Tmoney'];
	$yejiThirdCount[] = $sev['total'];
	$yejiThirdName[]  = "'".$sev['Tdate']."'";
}

//本月业绩
$yejiMonth = "SELECT
                COUNT(1) AS total,
				SUM(OrderTotal) AS Tmoney,
				FROM_UNIXTIME(OrderDate, '%m.%d') AS Tdate
			  FROM
				".DATATABLE."_order_orderinfo
			  WHERE OrderCompany=".$_SESSION['uc']['CompanyID']."
				    AND OrderStatus NOT IN(8,9)
				    AND FROM_UNIXTIME(OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')
			  GROUP BY Tdate
			  LIMIT 30 ";
$yejiMonthInfo = $db->get_results($yejiMonth);
$yejiMonthLine = $yejiMonthName = $yejiMonthCount = array();
foreach($yejiMonthInfo as $sev){
	$yejiMonthLine[]  = $sev['Tmoney'];
	$yejiMonthCount[] = $sev['total'];
	$yejiMonthName[]  = "'".$sev['Tdate']."'";
}
}

?>
<script>
    var myChartSev= echarts.init(document.getElementById('main1'), 'macarons');
    var myChartThird= echarts.init(document.getElementById('main2'), 'macarons');
    var myChartMon= echarts.init(document.getElementById('main3'), 'macarons');

    <?php if($yejiSevName){?>
        var optionSN = {
        	    title: {
        	        text: '订单金额(单位：元)',
        	   
        	     },
        	     tooltip: {
        	         trigger: 'axis'
        	     },
        	     legend: {
        	         data:['订单量','销售额']
        	     },
        	     grid: {
        	         left: '3%',
        	         right: '4%',
        	         bottom: '3%',
        	         containLabel: true
        	     },
        	     toolbox: {
        	         show: true,
        	         feature: {
						  dataView: {readOnly: false},
        	              magicType: {type: ['line', 'bar']},
        	              restore: {},
        	              saveAsImage: {}
        	         }
        	     },
        	     xAxis: {
        	         type: 'category',
        	         boundaryGap: false,
        	         data: [<?php echo implode(",", $yejiSevName)?>]
        	     },
        	     yAxis: {
        	         type: 'value'
        	     },
        	     series: [
        	         {
        	             name:'订单量',
        	             type:'line',
        	             stack: '总量',
        	             data:[<?php echo implode(',', $yejiSevCount)?>],
						 markPoint: {
						data: [
							{type: 'max', name: '最大值'},
							{type: 'min', name: '最小值'}
						]
					},
        	         },
        	         {
        	             name:'销售额',
        	             type:'line',
        	             stack: '总量',
        	             data:[<?php echo implode(',', $yejiSevLine)?>]
        	         }
        	     ]
        	 };
    		myChartSev.setOption(optionSN);

    <?php }?>

    <?php if($yejiThirdName){?>
        var optionThird = {
        	    title: {
        	        text: '订单金额(单位：元)',
        	   
        	     },
        	     tooltip: {
        	         trigger: 'axis'
        	     },
        	     legend: {
        	         data:['订单量','销售额']
        	     },
        	     grid: {
        	         left: '3%',
        	         right: '4%',
        	         bottom: '3%',
        	         containLabel: true
        	     },
        	     toolbox: {
        	         show: true,
        	         feature: {
        	              magicType: {type: ['line', 'bar']},
        	              restore: {},
        	              saveAsImage: {}
        	         }
        	     },
        	     xAxis: {
        	         type: 'category',
        	         boundaryGap: false,
        	         data: [<?php echo implode(",", $yejiThirdName)?>]
        	     },
        	     yAxis: {
        	         type: 'value'
        	     },
        	     series: [
        	         {
        	             name:'订单量',
        	             type:'line',
        	             stack: '总量',
        	             data:[<?php echo implode(',', $yejiThirdCount)?>]
        	         },
        	         {
        	             name:'销售额',
        	             type:'line',
        	             stack: '总量',
        	             data:[<?php echo implode(',', $yejiThirdLine)?>]
        	         }
        	     ]
        	 };

    <?php }?>

    <?php if($yejiMonthLine){?>
        var optionMonth = {
        	    title: {
        	        text: '订单金额(单位：元)',
        	   
        	     },
        	     tooltip: {
        	         trigger: 'axis'
        	     },
        	     legend: {
        	         data:['订单量','销售额']
        	     },
        	     grid: {
        	         left: '3%',
        	         right: '4%',
        	         bottom: '3%',
        	         containLabel: true
        	     },
        	     toolbox: {
        	         show: true,
        	         feature: {
        	              magicType: {type: ['line', 'bar']},
        	              restore: {},
        	              saveAsImage: {}
        	         }
        	     },
        	     xAxis: {
        	         type: 'category',
        	         boundaryGap: false,
        	         data: [<?php echo implode(",", $yejiMonthName)?>]
        	     },
        	     yAxis: {
        	         type: 'value'
        	     },
        	     series: [
        	         {
        	             name:'订单量',
        	             type:'line',
        	             stack: '总量',
        	             data:[<?php echo implode(',', $yejiMonthCount)?>]
        	         },
        	         {
        	             name:'销售额',
        	             type:'line',
        	             stack: '总量',
        	             data:[<?php echo implode(',', $yejiMonthLine)?>]
        	         }
        	     ]
        	 };
    <?php }?>

</script>

