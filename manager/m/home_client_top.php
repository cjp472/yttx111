<?php 

//时间计算，修正1秒误差
$secPer = 86400;
$today  = strtotime(date('Y-m-d')." 23:59:59") + 1;
$yestodayStart = $today - $secPer * 2;
$yestodayEnd   = $today - $secPer - 1;

//昨天
$clientYesSql = "SELECT 
				  SUM(o.OrderTotal) AS total,
				  c.ClientCompanyName 
				FROM
				  rsung_order_orderinfo AS o 
				  LEFT JOIN rsung_order_client AS c 
				    ON o.OrderUserID = c.ClientID 
				WHERE o.OrderCompany=".$_SESSION['uc']['CompanyID']."
					  AND o.OrderStatus NOT IN (8, 9) 
					  AND o.OrderDate>".$yestodayStart." AND o.OrderDate<".$yestodayEnd."
				GROUP BY c.ClientID 
				ORDER BY total DESC
				LIMIT 10 ";
$clientYesInfo = $db->get_results($clientYesSql);

//7天
$nearSev = $today - $secPer * 7;
$clientSevSql = "SELECT
				  SUM(o.OrderTotal) AS total,
				  c.ClientCompanyName
				FROM
				  rsung_order_orderinfo AS o
				  LEFT JOIN rsung_order_client AS c
				    ON o.OrderUserID = c.ClientID
				WHERE o.OrderCompany=".$_SESSION['uc']['CompanyID']."
					  AND o.OrderStatus NOT IN (8, 9)
					  AND o.OrderDate>".$nearSev." AND o.OrderDate<".($today-1)."
				GROUP BY c.ClientID
				ORDER BY total DESC
				LIMIT 10 ";
$clientSevInfo = $db->get_results($clientSevSql);

//本月
$thirdStart = $today - $secPer * 30;
$clientMonthSql = "SELECT
				  SUM(o.OrderTotal) AS total,
				  c.ClientCompanyName
				FROM
				  rsung_order_orderinfo AS o
				  LEFT JOIN rsung_order_client AS c
				    ON o.OrderUserID = c.ClientID
				WHERE o.OrderCompany=".$_SESSION['uc']['CompanyID']."
					  AND o.OrderStatus NOT IN (8, 9)
					  AND FROM_UNIXTIME(o.OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')
				GROUP BY c.ClientID
				ORDER BY total DESC
				LIMIT 10 ";
$clientMonthInfo = $db->get_results($clientMonthSql);
?>

        <li style="height:45%">
            <div id="app-list1" style="width:100%">
                <template>
                    <el-tabs v-model="activeName" @tab-click="handleClick" style="width:100%">
                        <el-tab-pane style="width:98%" name="first" disabled="false" style="float:left;">
                            <span slot="label" style="display:block;margin-top:7px;font-size:16px;color:#666"><i class="iconfont icon-caigou" style="font-size:21px;color:#349575;margin-right:10px;"></i>客户采购分布</span>
                        </el-tab-pane>

                        <el-tab-pane label="昨天" name="second" style="width:100%">
                            <table style="width:36%;margin-left:3%;float:left;">
                                <tr>
                                    <th style="">药店名称</th>
                                    <th style="width:100px;margin-left:50px;">采购金额</th>
                                </tr>
                                <?php
	                            $clientYesLine = array();//昨日图表统计 名称+数据
	                            $clientYesName = array();//昨日图表统计 名称
	                            if(empty($clientYesInfo)){
                            ?>
                                <tr>
                                    <td colspan="2" align="center">暂无数据...</td>
                                </tr>
                                <?php
								}else{
                            	foreach($clientYesInfo as $toV){
                            		$toV['ClientCompanyName'] = trim($toV['ClientCompanyName']);
                            		$clientYesName[] = "'".$toV['ClientCompanyName']."'";
                            		$clientYesLine[] = "{value:".$toV['total'].", name:'".$toV['ClientCompanyName']."'}";
                            ?>
                                <tr>
                                    <td class="mo" title="<?php echo $toV['ClientCompanyName'];?>"><?php echo $toV['ClientCompanyName'];?></td>
                                    <td >￥<?php echo number_format($toV['total'], 2, '.', '');?></td>
                                </tr>
                                <?php
								}
							}
							?>
                            </table>
                            <div id="got7" style="width:60%;height:320px;float:right;margin-top:-6px;"></div>
                        </el-tab-pane>
                        <el-tab-pane label="7天" name="third" >
                            <table style="width:36%;margin-left:3%;float:left;" >
                                <tr>
                                    <th>药店名称</th>
                                    <th style="width:100px;margin-left:50px;">采购金额</th>
                                </tr>
                                <?php
	                            $clientSevLine = array();//昨日图表统计 名称+数据
	                            $clientSevName = array();//昨日图表统计 名称
	                            if(empty($clientSevInfo)){
                            ?>
                                <tr>
                                    <td colspan="2" align="center">暂无数据...</td>
                                </tr>
                                <?php
								}else{
                            	foreach($clientSevInfo as $sv){
                            		$sv['ClientCompanyName'] = trim($sv['ClientCompanyName']);
                            		$clientSevName[] = "'".$sv['ClientCompanyName']."'";
                            		$clientSevLine[] = "{value:".$sv['total'].", name:'".$sv['ClientCompanyName']."'}";
                            ?>
                                <tr>
                                    <td class="mo"><?php echo $sv['ClientCompanyName'];?></td>
                                    <td >￥<?php echo number_format($sv['total'], 2, '.', '');?></td>
                                </tr>
                                <?php
								}
							}
							?>
                            </table>
                            <div id="got8" style="height:320px;float:right;margin-top:-6px;"></div>
                        </el-tab-pane>
                        <el-tab-pane label="本月" name="fourth">
                            <table style="width:36%;margin-left:3%;float:left;">
                                <tr>
                                    <th>药店名称</th>
                                    <th style="width:100px;margin-left:50px;">采购金额</th>
                                </tr>
                                <?php
	                            $clientMonthLine = array();//本月图表统计 名称+数据
	                            $clientMonthName = array();//本月图表统计 名称
	                            if(empty($clientMonthInfo)){
                            ?>
                                <tr>
                                    <td colspan="2" align="center">暂无数据...</td>
                                </tr>
                                <?php
								}else{
                            	foreach($clientMonthInfo as $mV){
                            		$mV['ClientCompanyName'] = trim($mV['ClientCompanyName']);
                            		$clientMonthName[] = "'".$mV['ClientCompanyName']."'";
                            		$clientMonthLine[] = "{value:".$mV['total'].", name:'".$mV['ClientCompanyName']."'}";
                            ?>
                                <tr>
                                    <td class="mo"><?php echo $mV['ClientCompanyName'];?></td>
                                    <td >￥<?php echo number_format($mV['total'], 2, '.', '');?></td>
                                </tr>
                                <?php
								}
							}
							?>
                            </table>
                            <div id="got9" style="height:320px;float:right;margin-top:-6px;"></div>
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
               		if(tab._uid===22){
               		  got.setOption(optionClientYes);
               		}
               		else if(tab._uid===23){
               	    go.setOption(optionClientSev);
               		}
               		else if(tab._uid===24){
               		    g.setOption(optionClientMonth);
               		}
                     }
            }
            };
            var Ctor = Vue.extend(Main)
            new Ctor().$mount('#app-list1')
            var chartFirstWidth = $('#got7').width();
                $('#got8').css('width', chartFirstWidth);
                $('#got9').css('width', chartFirstWidth);
        </script>
<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    var got = echarts.init(document.getElementById('got7'), 'macarons');
    var go  = echarts.init(document.getElementById('got8'), 'macarons');
    var g   = echarts.init(document.getElementById('got9'), 'macarons');
    
    // 指定图表的配置项和数据
    <?php if($clientYesLine){?>
    var optionClientYes = {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b}: ￥{c} ({d}%)"
        },
        toolbox: {
  	         show: true,
  	         feature: {
  	              saveAsImage: {}
  	         }
  	     	},
        legend: {
        	orient: 'vertical',
            x:'center',
            y: 'bottom',
            align:'left',
            orient:'horizontal',
            data:[<?php echo implode(',', $clientYesName);?>],
            formatter: function (name) {
                return echarts.format.truncateText(name, 66,'8px Microsoft Yahei', '…');
            },
            tooltip: {
                show: true
            }
        },
        series: [
            {
                name:'客户采购',
                type:'pie',
                radius: ['40%', '60%'],
                center:['50%', '35%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: false,
                        textStyle: {
                            fontSize: '12',
                            fontWeight: 'bold'
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[<?php echo implode(',', $clientYesLine)?>]
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    got.setOption(optionClientYes);

    <?php }?>

	<?php if($clientSevLine){?>
        // 指定图表的配置项和数据
        var optionClientSev = {
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b}: ￥{c} ({d}%)"
                },
                toolbox: {
          	         show: true,
          	         feature: {
          	              saveAsImage: {}
          	         }
          	     	},
                legend: {
                	orient: 'vertical',
                    x:'center',
                    y: 'bottom',
                    align:'left',
                    orient:'horizontal',
                    data:[<?php echo implode(',', $clientSevName);?>],
                    formatter: function (name) {
                        return echarts.format.truncateText(name, 66,'8px Microsoft Yahei', '…');
                    },
                    tooltip: {
                        show: true
                    }
                },
                series: [
                    {
                        name:'客户采购',
                        type:'pie',
                        radius: ['40%', '60%'],
                        center:['50%', '35%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: false,
                                textStyle: {
                                    fontSize: '12',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data:[<?php echo implode(',', $clientSevLine)?>]
                    }
                ]
            };

        <?php }?>

        <?php if($clientMonthLine){?>
     		// 指定图表的配置项和数据
          	var optionClientMonth = {
        	        tooltip: {
        	            trigger: 'item',
        	            formatter: "{a} <br/>{b}: ￥{c} ({d}%)"
        	        },
        	        toolbox: {
        	   	         show: true,
        	   	         feature: {
        	   	              saveAsImage: {}
        	   	         }
        	   	     	},
        	        legend: {
        	        	orient: 'vertical',
        	            x:'center',
        	            y: 'bottom',
        	            align:'left',
        	            orient:'horizontal',
        	            data:[<?php echo implode(',', $clientMonthName);?>],
        	            formatter: function (name) {
        	                return echarts.format.truncateText(name, 66,'8px Microsoft Yahei', '…');
        	            },
        	            tooltip: {
        	                show: true
        	            }
        	        },
        	        series: [
        	            {
        	                name:'客户采购',
        	                type:'pie',
        	                radius: ['40%', '60%'],
        	                center:['50%', '35%'],
        	                avoidLabelOverlap: false,
        	                label: {
        	                    normal: {
        	                        show: false,
        	                        position: 'center'
        	                    },
        	                    emphasis: {
        	                        show: false,
        	                        textStyle: {
        	                            fontSize: '12',
        	                            fontWeight: 'bold'
        	                        }
        	                    }
        	                },
        	                labelLine: {
        	                    normal: {
        	                        show: false
        	                    }
        	                },
        	                data:[<?php echo implode(',', $clientMonthLine)?>]
        	            }
        	        ]
        	    };
          
          <?php }?>

</script>

<?php 
unset(
		$yestodayStart,
		$yestodayEnd,
		$nearSev,
		$clientYesLine,
		$clientYesName,
		$clientSevLine,
		$clientSevName,
		$clientMonthLine,
		$clientMonthName,
		$clientYesInfo,
		$clientSevInfo,
		$clientMonthInfo
	);
?>

