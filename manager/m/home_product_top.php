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
            $sqlmsg = " AND i.AgentID= ".$userid."";
        }
	$productYesSql = "SELECT
					COUNT(1) AS cnt,c.ContentID,i.Name,i.Model,i.Units,SUM(c.ContentNumber) AS total,
					SUM((c.ContentNumber * c.ContentPercent * c.ContentPrice)/10) AS money
				FROM
					".DATATABLE."_order_orderinfo AS o
				LEFT JOIN rsung_order_cart AS c
					ON o.OrderID=c.OrderID
				LEFT JOIN ".DATATABLE."_order_content_index AS i
					ON i.CompanyID=c.CompanyID
					AND i.ID=c.ContentID
				WHERE
					c.CompanyID=".$_SESSION['uc']['CompanyID']."
					AND o.OrderStatus NOT IN (8, 9) 
    				AND o.OrderDate>".$yestodayStart." AND o.OrderDate<".$yestodayEnd."".$sqlmsg."
				GROUP BY c.ContentID
				ORDER BY total DESC
				LIMIT 10";
      $productYesInfo = $db->get_results($productYesSql);

      //最近七日
      $nearSev = $today - $secPer * 7;
      $productSevSql = "SELECT
			            COUNT(1) AS cnt,c.ContentID,i.Name,i.Model,i.Units,SUM(c.ContentNumber) AS total,
			       SUM((c.ContentNumber * c.ContentPercent * c.ContentPrice)/10) AS money
			       FROM
			             ".DATATABLE."_order_orderinfo AS o
			       LEFT JOIN rsung_order_cart AS c
			             ON o.OrderID=c.OrderID
			       LEFT JOIN ".DATATABLE."_order_content_index AS i
			             ON i.CompanyID=c.CompanyID
			             AND i.ID=c.ContentID
			       WHERE
			             c.CompanyID=".$_SESSION['uc']['CompanyID']."
			             AND o.OrderStatus NOT IN (8, 9) 
			             AND o.OrderDate>".$nearSev." AND o.OrderDate<".($today-1)."".$sqlmsg."
			       GROUP BY c.ContentID
			       ORDER BY total DESC
			       LIMIT 10";
       $productSevInfo = $db->get_results($productSevSql);

       //本月
       $productMonthSql = "SELECT
			            COUNT(1) AS cnt,c.ContentID,i.Name,i.Model,i.Units,SUM(c.ContentNumber) AS total,
			            SUM((c.ContentNumber * c.ContentPercent * c.ContentPrice)/10) AS money
			        FROM
			            ".DATATABLE."_order_orderinfo AS o
			        LEFT JOIN rsung_order_cart AS c
			            ON o.OrderID=c.OrderID
			        LEFT JOIN ".DATATABLE."_order_content_index AS i
			            ON i.CompanyID=c.CompanyID
			            AND i.ID=c.ContentID
			        WHERE
			            c.CompanyID=".$_SESSION['uc']['CompanyID']."
			            AND o.OrderStatus NOT IN (8, 9) 
			            AND FROM_UNIXTIME(o.OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')".$sqlmsg."
			        GROUP BY c.ContentID
			        ORDER BY total DESC
			        LIMIT 10";
      $productMonthInfo = $db->get_results($productMonthSql);

                ?>
 <li style="height:45%">
            <div id="app-list" style="width:100%">
                <template>
                    <el-tabs v-model="activeName" @tab-click="handleClick" style="width:100%">
                        <el-tab-pane style="width:98%" name="first" disabled="false" style="float:left;">
                            <span slot="label" style="display:block;margin-top:7px;font-size:16px;color:#666"><i class="iconfont icon-tongji" style="font-size:24px;color:#906fe1;margin-right:10px;"></i>商品销量排行</span>
                            <div id="chart" style="width:220px;height:370px;position:absolute;left:60%;top:-14%;">
                            </div>
                        </el-tab-pane>

                        <el-tab-pane label="昨天" name="second" style="">
                            <table style="width:60%;margin-left:3%;float:left;">
                                <tr>
                                    <th style="width:100px;">名称</th>
                                    <th style="width:80px;height:27px;">规格</th>
                                    <th style="width:80px;height:27px;">订单数</th>
                                    <th style="width:62px;">金额</th>
                                </tr>
                                <?php
	                            $forProductYesJSPie = array();//今日图表统计 名称+数据
	                            $forProductYesJSPieName = array();//今日图表统计 名称
	                            if(empty($productYesInfo)){
                            ?>
                                <tr>
                                    <td colspan="4" align="center">暂无数据</td>
                                </tr>
                                <?php
								}else{
                            	foreach($productYesInfo as $toV){
                            		$toV['Name'] = trim($toV['Name']);
                            		$forProductYesJSPieName[] = "'".$toV['Name']."'";
                            		$forProductYesJSPie[]     = "{value:".$toV['money'].", name:'".$toV['Name']."'}";
                            ?>
                                <tr>
                                    <td class="mo" title="<?php echo $toV['Name'];?>"><?php echo $toV['Name'];?></td>
                                    <td class="over" style=""><?php echo $toV['Model'];?></td>
                                    <td class="" style=""><?php echo $toV['cnt'];?> 个</td>
                                    <td >￥<?php echo number_format($toV['money'], 2, '.', '');?></td>
                                </tr>
                                <?php
								}
							}
							?>
                            </table>
                            <div id="chart1" style="width:36%;height:370px;float:right;"></div>
                        </el-tab-pane>
                        <el-tab-pane label="7天" name="third" style="width:100%">
                            <table style="width:60%;margin-left:3%;float:left;" >
                                <tr>
                                    <th style="width:100px;">名称</th>
                                    <th style="width:80px;height:27px;">规格</th>
                                    <th style="width:80px;height:27px;">订单数</th>
                                    <th style="width:62px;">金额</th>
                                </tr>
                                <?php
		                            $forProductSevJSPieName = array();	//7天图表统计 名称
		                            $forproductSevJSPie 	= array();	//7天图表统计 名称+数据
		                            if(empty($productSevInfo)){
                                ?>
                                <tr>
                                    <td colspan="4" align="center">暂无数据</td>
                                </tr>
                                <?php
								}else{
                            	foreach($productSevInfo as $sv){
                            		$sv['Name'] = trim($sv['Name']);
                            		$forProductSevJSPieName[] = "'".$sv['Name']."'";
                            		$forproductSevJSPie[]     = "{value:".$sv['money'].", name:'".$sv['Name']."'}";
                            ?>
                                <tr>
                                    <td class="mo"><?php echo $sv['Name'];?></td>
                                    <td class="over" style=""><?php echo $sv['Model'];?></td>
                                    <td class="" style=""><?php echo $sv['cnt'];?> 个</td>
                                    <td >￥<?php echo number_format($sv['money'], 2, '.', '');?></td>
                                </tr>
                                <?php
								}
							}
							?>
                            </table>
                            <div id="chart2" style="height:370px;float:right;"></div>
                        </el-tab-pane>
                        <el-tab-pane label="本月" name="fourth">
                            <table style="width:60%;margin-left:3%;float:left;">
                                <tr>
                                   <th style="width:100px;">名称</th>
                                   <th style="width:80px;height:27px;">规格</th>
                                   <th style="width:80px;height:27px;">订单数</th>
                                   <th style="width:62px;">金额</th>
                                </tr>
                                <?php
	                            $forProductMonthJSPie     = array();	//本月图表统计 名称+数据
	                            $forProductMonthJSPieName = array();	//本月图表统计 名称
	                            if(empty($productMonthInfo)){
                            ?>
                                <tr>
                                    <td colspan="4" align="center">暂无数据</td>
                                </tr>
                                <?php
								}else{
                            	foreach($productMonthInfo as $mV){
                            		$mV['Name'] = trim($mV['Name']);
                            		$forProductMonthJSPieName[] = "'".$mV['Name']."'";
                            		$forProductMonthJSPie[] = "{value:".$mV['money'].", name:'".$mV['Name']."'}";
                            ?>
                                <tr>
                                    <td class="mo"><?php echo $mV['Name'];?></td>
                                    <td class="over" style=""><?php echo $mV['Model'];?></td>
                                    <td class="" style=""><?php echo $mV['cnt'];?> 个</td>
                                    <td >￥<?php echo number_format($mV['money'], 2, '.', '');?></td>
                                </tr>
                                <?php
								}
							}
							?>
                            </table>
                            <div id="chart3" style="width:195px;height:370px;float:right;"></div>
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
           if(tab._uid===6){
              	myChartToday.setOption(optionProductYes);
           }else if(tab._uid===7){
				myChartSevenday.setOption(optionProductSev);
           }else if(tab._uid===8){
              	myChartMonth.setOption(optionProductMonth);
           }
     }

    }
    };
    var Ctor = Vue.extend(Main)
    new Ctor().$mount('#app-list')
    
    var chartFirstWidth = $('#chart1').width();
    	$('#chart2').css('width', chartFirstWidth);
    	$('#chart3').css('width', chartFirstWidth);

</script>
<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var myChartToday    = echarts.init(document.getElementById('chart1'), 'macarons');
    var myChartSevenday = echarts.init(document.getElementById('chart2'), 'macarons');
    var myChartMonth    = echarts.init(document.getElementById('chart3'), 'macarons');


    // 指定图表的配置项和数据
    //今日数据
    <?php if($forProductYesJSPie){?>
        var optionProductYes = {
            title : {
                text: '销量前10名',
                subtext: '单位：元',
                x:'center',
                textStyle:{
                    color : '#666666'
                }
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            toolbox: {
      	         show: true,
      	         feature: {
      	              saveAsImage: {}
      	         }
      	     },
            series : [
                {
                    name: '商品名称',
                    type: 'pie',
                    radius : '85%',
                    center: ['50%', '50%'],
                    data:[
                    <?php echo implode(',', $forProductYesJSPie)?>
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            label: {
                normal: {
                    show : false
                }
            },
        }
    ]
    };
	myChartToday.setOption(optionProductYes);
    <?php } ?>

    //最近七日数据
    <?php if($forproductSevJSPie){?>
        var optionProductSev = {
            title : {
                text: '销量前10名',
                subtext: '单位：元',
                x:'center',
                textStyle:{
                    color : '#666666'
                }
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            toolbox: {
      	         show: true,
      	         feature: {
      	              saveAsImage: {}
      	         }
      	    },
            series : [
                {
                    name: '商品名称',
                    type: 'pie',
                    radius : '85%',
                    center: ['50%', '50%'],
                    data:[
                    <?php echo implode(',', $forproductSevJSPie)?>
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            label: {
                normal: {
                    show : false
                }
            },
        }
    ]
    };

    <?php } ?>

    //本月数据
    <?php if($forProductMonthJSPie){?>
        var optionProductMonth = {
            title : {
                text: '销量前10名',
                subtext: '单位：元',
                x:'center',
                textStyle:{
                    color : '#666666'
                }
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            toolbox: {
   	         show: true,
   	         feature: {
   	              saveAsImage: {}
   	         }
   	     	},
            series : [
                {
                    name: '商品名称',
                    type: 'pie',
                    radius : '85%',
                    center: ['50%', '50%'],
                    data:[
                    <?php echo implode(',', $forProductMonthJSPie)?>
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            },
            label: {
                normal: {
                    show : false
                }
            },
        }
    ]
    };

    <?php } ?>


    // 使用刚指定的配置项和数据显示图表。

</script>

