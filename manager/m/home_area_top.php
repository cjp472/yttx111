<?php 

//时间计算，修正1秒误差
$secPer = 86400;
$today  = strtotime(date('Y-m-d')." 23:59:59") + 1;
$yestodayStart = $today - $secPer * 2;
$yestodayEnd   = $today - $secPer - 1;
//获取当前登录用户的类型 和ID wkk
$user_flag = trim($_SESSION['uinfo']['userflag']);
$userid=$_SESSION['uinfo']['userid'];
if($user_flag == '2'){
           		$subsql = "SELECT DISTINCT o.OrderID AS allow FROM "
			.DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
			where OrderCompany = ".$_SESSION['uinfo']['ucompany']." and c.AgentID= ".$userid."";
    //昨天
    $areaYesSql = "SELECT 
                                      SUM(o.OrderTotal) AS total,
                                      a.AreaName 
                                    FROM
                                      rsung_order_orderinfo AS o 
                                      LEFT JOIN ".DATATABLE."_order_client AS c 
                                        ON o.OrderUserID=c.ClientID 
                                      LEFT JOIN ".DATATABLE."_order_area AS a 
                                        ON c.ClientArea=a.AreaID 
                                    WHERE o.OrderCompany=".$_SESSION['uc']['CompanyID']." 
                                              AND o.OrderStatus NOT IN (8, 9)
                                              and o.OrderID in(".$subsql.")
                                          AND o.OrderDate>".$yestodayStart." AND o.OrderDate<".$yestodayEnd."
                                    GROUP BY a.AreaID
                                    ORDER BY total DESC
                                    LIMIT 10";
    $areaYesInfo = $db->get_results($areaYesSql);

    //7天
    $nearSev = $today - $secPer * 7;
    $areaSevSql = "SELECT
                                      SUM(o.OrderTotal) AS total,
                                      a.AreaName
                                    FROM
                                      rsung_order_orderinfo AS o
                                      LEFT JOIN ".DATATABLE."_order_client AS c
                                        ON o.OrderUserID = c.ClientID
                                      LEFT JOIN ".DATATABLE."_order_area AS a
                                        ON c.ClientArea = a.AreaID
                                    WHERE o.OrderCompany = ".$_SESSION['uc']['CompanyID']."
                                              AND o.OrderStatus NOT IN (8, 9)
                                              and o.OrderID in(".$subsql.")
                                              AND o.OrderDate>".$nearSev." AND o.OrderDate<".($today-1)."
                                    GROUP BY a.AreaID
                                    ORDER BY total DESC
                                    LIMIT 10";
    $areaSevInfo = $db->get_results($areaSevSql);

    //本月
    $areaMonthSql = "SELECT
                                      SUM(o.OrderTotal) AS total,
                                      a.AreaName
                                    FROM
                                      rsung_order_orderinfo AS o
                                      LEFT JOIN ".DATATABLE."_order_client AS c
                                        ON o.OrderUserID = c.ClientID
                                      LEFT JOIN ".DATATABLE."_order_area AS a
                                        ON c.ClientArea = a.AreaID
                                    WHERE o.OrderCompany = ".$_SESSION['uc']['CompanyID']."
                                              AND o.OrderStatus NOT IN (8, 9)
                                              and o.OrderID in(".$subsql.")
                                              AND FROM_UNIXTIME(o.OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')
                                    GROUP BY a.AreaID
                                    ORDER BY total DESC
                                    LIMIT 10";
    $areaMonthInfo = $db->get_results($areaMonthSql);
}else{
    //昨天
    $areaYesSql = "SELECT 
                                      SUM(o.OrderTotal) AS total,
                                      a.AreaName 
                                    FROM
                                      rsung_order_orderinfo AS o 
                                      LEFT JOIN ".DATATABLE."_order_client AS c 
                                        ON o.OrderUserID=c.ClientID 
                                      LEFT JOIN ".DATATABLE."_order_area AS a 
                                        ON c.ClientArea=a.AreaID 
                                    WHERE o.OrderCompany=".$_SESSION['uc']['CompanyID']." 
                                              AND o.OrderStatus NOT IN (8, 9)
                                          AND o.OrderDate>".$yestodayStart." AND o.OrderDate<".$yestodayEnd."
                                    GROUP BY a.AreaID
                                    ORDER BY total DESC
                                    LIMIT 10";
    $areaYesInfo = $db->get_results($areaYesSql);

    //7天
    $nearSev = $today - $secPer * 7;
    $areaSevSql = "SELECT
                                      SUM(o.OrderTotal) AS total,
                                      a.AreaName
                                    FROM
                                      rsung_order_orderinfo AS o
                                      LEFT JOIN ".DATATABLE."_order_client AS c
                                        ON o.OrderUserID = c.ClientID
                                      LEFT JOIN ".DATATABLE."_order_area AS a
                                        ON c.ClientArea = a.AreaID
                                    WHERE o.OrderCompany = ".$_SESSION['uc']['CompanyID']."
                                              AND o.OrderStatus NOT IN (8, 9)
                                              AND o.OrderDate>".$nearSev." AND o.OrderDate<".($today-1)."
                                    GROUP BY a.AreaID
                                    ORDER BY total DESC
                                    LIMIT 10";
    $areaSevInfo = $db->get_results($areaSevSql);

    //本月
    $areaMonthSql = "SELECT
                                      SUM(o.OrderTotal) AS total,
                                      a.AreaName
                                    FROM
                                      rsung_order_orderinfo AS o
                                      LEFT JOIN ".DATATABLE."_order_client AS c
                                        ON o.OrderUserID = c.ClientID
                                      LEFT JOIN ".DATATABLE."_order_area AS a
                                        ON c.ClientArea = a.AreaID
                                    WHERE o.OrderCompany = ".$_SESSION['uc']['CompanyID']."
                                              AND o.OrderStatus NOT IN (8, 9)
                                              AND FROM_UNIXTIME(o.OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')
                                    GROUP BY a.AreaID
                                    ORDER BY total DESC
                                    LIMIT 10";
    $areaMonthInfo = $db->get_results($areaMonthSql);
}
unset($yestodayStart, $yestodayEnd, $nearSev);
?>
  <li style="height:45%">
            <div id="app-list2" style="width:100%">
                <template>
                    <el-tabs v-model="activeName" @tab-click="handleClick" style="width:100%">
                        <el-tab-pane style="width:98%" name="first" disabled="false" style="float:left;">
                            <span slot="label" style="display:block;margin-top:7px;font-size:16px;color:#666"><i class="iconfont icon-diqu" style="font-size:28px;color:#f75959;margin-right:10px;"></i>地区采购分布</span>

            </div>
            </el-tab-pane>

            <el-tab-pane label="昨天" name="second" style="">
                <table style="width:36%;margin-left:3%;float:left;">
                    <tr>
                        <th>地区</th>
                        <th>采购额</th>
                    </tr>
                    <?php
	                    $forAreaYesJSPie = array();//昨天图表统计 名称+数据
	                    $forAreaYesJSPieName = array();//昨天图表统计 名称
	                    if(empty($areaYesInfo)){
                    ?>
                    <tr>
                        <td colspan="2" align="center">暂无数据...</td>
                    </tr>
                    <?php
						}else{
                        foreach($areaYesInfo as $toV){
                            $toV['AreaName'] = trim($toV['AreaName']);
                            $forAreaYesJSPieName[] = "'".$toV['AreaName']."'";
                            $forAreaYesJSPie[] = "{value:".$toV['total'].", name:'".$toV['AreaName']."'}";
                    ?>
                    <tr>
                        <td class="mo" title="<?php echo $toV['AreaName'];?>"><?php echo $toV['AreaName'];?></td>
                        <td >￥<?php echo number_format($toV['total'], 2, '.', '');?></td>
                    </tr>
                    <?php
						}
					}
					?>
                </table>
                <div id="round1" style="width:60%;height:320px;float:right;margin-top:-6px;"></div>
            </el-tab-pane>
            <el-tab-pane label="7天" name="third">
                <table style="width:36%;margin-left:3%;float:left;" >
                    <tr>
                        <th>地区</th>
                        <th>采购额</th>
                    </tr>
                    <?php
	                  $forAreaSevJSPieName	= array();//7天日图表统计 名称
	                  $forAreaSevJSPie		= array();//7天图表统计 名称+数据
	                  if(empty($areaSevInfo)){
                    ?>
                    <tr>
                        <td colspan="2" align="center">暂无数据...</td>
                    </tr>
                    <?php
						}else{
                          foreach($areaSevInfo as $sv){
                            $sv['AreaName'] = trim($sv['AreaName']);
                            $forAreaSevJSPieName[] = "'".$sv['AreaName']."'";
                            $forAreaSevJSPie[] = "{value:".$sv['total'].", name:'".$sv['AreaName']."'}";
                    ?>
                    <tr>
                        <td class="mo"><?php echo $sv['AreaName'];?></td>
                        <td >￥<?php echo number_format($sv['total'], 2, '.', '');?></td>
                    </tr>
                    <?php
						}
					}
					?>
                </table>
                <div id="round2" style="height:320px;float:right;margin-top:-6px;margin-left:0px;"></div>
            </el-tab-pane>
            <el-tab-pane label="本月" name="fourth">
                <table style="width:36%;margin-left:3%;float:left;">
                    <tr>
                        <th>地区</th>
                        <th>采购额</th>
                    </tr>
                    <?php 
	                    $forareaMonthJSPie		= array();//本月图表统计 名称+数据
	                    $forareaMonthJSPieName	= array();//本月图表统计 名称
	                    if(empty($areaMonthInfo)){
                    ?>
                    <tr>
                        <td colspan="2" align="center">暂无数据...</td>
                    </tr>
                    <?php
						}else{
                           foreach($areaMonthInfo as $mV){
                             $mV['AreaName'] = trim($mV['AreaName']);
                             $forareaMonthJSPieName[] = "'".$mV['AreaName']."'";
                             $forareaMonthJSPie[] = "{value:".$mV['total'].", name:'".$mV['AreaName']."'}";
                    ?>
                    <tr>
                        <td class="mo"><?php echo $mV['AreaName'];?></td>
                        <td >￥<?php echo number_format($mV['total'], 2, '.', '');?></td>
                    </tr>
                    <?php
						}
					}
				    ?>
                </table>
                <div id="round3" style="height:320px;float:right;margin-top:-6px;"></div>
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
           if(tab._uid===30){
        	   round.setOption(optionAreaYes);
           }else if(tab._uid===31){
           		ro.setOption(optionAreaSev);
           }else if(tab._uid===32){
           		r.setOption(optionAreaMonth);
           }
      }
    }
    };

    var Ctor = Vue.extend(Main)
    new Ctor().$mount('#app-list2')
    var chartFirstWidth = $('#round1').width();
        $('#round2').css('width', chartFirstWidth);
        $('#round3').css('width', chartFirstWidth);
</script>

<script type="text/javascript">
    // 基于准备好的dom，初始化echarts实例
    
    // 指定图表的配置项和数据
    //本月
    <?php if($forAreaYesJSPie){?>
    var round = echarts.init(document.getElementById('round1'), 'macarons');
    var optionAreaYes = {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br />{b}: ￥{c} ({d}%)"
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
            data:[<?php echo implode(',', $forAreaYesJSPieName);?>]
        },
        series: [
            {
                name:'采购地区',
                type:'pie',
                radius: ['40%', '60%'],
                center:['50%', '40%'],
                avoidLabelOverlap: false,
                label: {
                    normal: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        show: true,
                        textStyle: {
                            fontSize: '20',
                            fontWeight: 'bold',
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data:[<?php echo implode(',', $forAreaYesJSPie);?>]
            }
        ]
    };

    // 使用刚指定的配置项和数据显示图表。
    round.setOption(optionAreaYes);
    <?php }?>

  	//7天
    <?php if($forAreaSevJSPie){?>
    var ro= echarts.init(document.getElementById('round2'), 'macarons');
    var optionAreaSev = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br />{b}: ￥{c} ({d}%)"
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
                data:[<?php echo implode(',', $forAreaSevJSPieName);?>]
            },
            series: [
                {
                    name:'采购地区',
                    type:'pie',
                    radius: ['40%', '60%'],
                    center:['50%', '40%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '20',
                                fontWeight: 'bold',
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data:[<?php echo implode(',', $forAreaSevJSPie);?>]
                }
            ]
        };
    <?php }?>

    //本月
    <?php if($forareaMonthJSPie){?>
    var r = echarts.init(document.getElementById('round3'), 'macarons');
    var optionAreaMonth = {
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br />{b}: ￥{c} ({d}%)"
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
                data:[<?php echo implode(',', $forareaMonthJSPieName);?>]
            },
            series: [
                {
                    name:'采购地区',
                    type:'pie',
                    radius: ['40%', '60%'],
                    center:['50%', '40%'],
                    avoidLabelOverlap: false,
                    label: {
                        normal: {
                            show: false,
                            position: 'center'
                        },
                        emphasis: {
                            show: true,
                            textStyle: {
                                fontSize: '20',
                                fontWeight: 'bold',
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data:[<?php echo implode(',', $forareaMonthJSPie);?>]
                }
            ]
        };
    <?php }?>
    </script>
 <?php 
 unset(
 		$areaYesInfo, 
 		$areaSevInfo, 
 		$areaMonthInfo, 
 		$forareaMonthJSPie, 
 		$forareaMonthJSPieName, 
 		$forAreaSevJSPie, 
 		$forAreaSevJSPieName, 
 		$forAreaYesJSPie, 
 		$forAreaYesJSPieName
 	);
 
 ?>