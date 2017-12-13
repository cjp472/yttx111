<?php 

//时间计算，修正1秒误差
$secPer = 86400;
$today  = strtotime(date('Y-m-d')." 23:59:59") + 1;
$yestodayStart = $today - $secPer * 2;
$yestodayEnd   = $today - $secPer - 1;

//ymm 2017-12-13                判断是否为代理商，代理商只能看到自己所管辖商品相关的药店
//昨日
$user_flag = trim($_SESSION['uinfo']['userflag']);
if ($user_flag == '2')
{       
    $subsql = "SELECT DISTINCT o.OrderID FROM "
        .DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
        where c.AgentID= ".$_SESSION['uinfo']['userid']." AND o.OrderCompany=".$_SESSION['uc']['CompanyID']." AND o.OrderStatus NOT IN (8, 9) AND o.OrderDate>".$yestodayStart." AND o.OrderDate<".$yestodayEnd."";
    $clientYesSql = "SELECT c.ClientCompanyName,o.OrderID FROM "
    .DATATABLE."_order_orderinfo o RIGHT JOIN ".DATATABLE."_order_client c on o.OrderUserID=c.ClientID
    where OrderID in (".$subsql.")";
}else{ //管理员和商业公司可以看到所有订单

     $clientYesSql = "SELECT c.ClientCompanyName,o.OrderID FROM "
    .DATATABLE."_order_orderinfo o RIGHT JOIN ".DATATABLE."_order_client c on o.OrderUserID=c.ClientID where c.ClientCompany=".$_SESSION['uc']['CompanyID']." AND o.OrderStatus NOT IN (8, 9) AND o.OrderDate>".$yestodayStart." AND o.OrderDate<".$yestodayEnd."";
}
$clientYes = $db->get_results($clientYesSql);
//处理结果集把OrderID分割成字符串
foreach ($clientYes as $key => $val) {
    $clientYesInfo['ClientCompanyName'][$val['ClientCompanyName']][]=$val['OrderID'];
}
foreach ($clientYesInfo['ClientCompanyName'] as $key => $val) {
    $clientYesInfo['ClientCompanyName'][$key]['OrderID']=implode(',',$val);
}

//ymm 2017-12-13                判断是否为代理商，代理商只能看到自己所管辖商品相关的药店
//7天
$nearSev = $today - $secPer * 7;
if ($user_flag == '2')
{       
    $subsql = "SELECT DISTINCT o.OrderID FROM "
        .DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
        where c.AgentID= ".$_SESSION['uinfo']['userid']." AND o.OrderCompany=".$_SESSION['uc']['CompanyID']." AND o.OrderStatus NOT IN (8, 9) AND o.OrderDate>".$nearSev." AND o.OrderDate<".($today-1)."";
    $clientSevSql = "SELECT c.ClientCompanyName,o.OrderID FROM "
    .DATATABLE."_order_orderinfo o RIGHT JOIN ".DATATABLE."_order_client c on o.OrderUserID=c.ClientID
    where OrderID in (".$subsql.")";
}else{ //管理员和商业公司可以看到所有订单
     $clientSevSql = "SELECT c.ClientCompanyName,o.OrderID FROM "
    .DATATABLE."_order_orderinfo o RIGHT JOIN ".DATATABLE."_order_client c on o.OrderUserID=c.ClientID where c.ClientCompany=".$_SESSION['uc']['CompanyID']." AND o.OrderStatus NOT IN (8, 9) AND o.OrderDate>".$nearSev." AND o.OrderDate<".($today-1)."";
}
$clientSev = $db->get_results($clientSevSql);
//处理结果集把OrderID分割成字符串
foreach ($clientSev as $key => $val) {
    $clientSevInfo['ClientCompanyName'][$val['ClientCompanyName']][]=$val['OrderID'];
}
foreach ($clientSevInfo['ClientCompanyName'] as $key => $val) {
    $clientSevInfo['ClientCompanyName'][$key]['OrderID']=implode(',',$val);
}

//ymm 2017-12-13                判断是否为代理商，代理商只能看到自己所管辖商品相关的药店
//本月
$thirdStart = $today - $secPer * 30;
if ($user_flag == '2'){   
    $subsql = "SELECT DISTINCT o.OrderID FROM "
        .DATATABLE."_order_orderinfo o LEFT JOIN ".DATATABLE."_view_index_cart c ON o.OrderID=c.OrderID 
        where c.AgentID= ".$_SESSION['uinfo']['userid']." AND o.OrderCompany=".$_SESSION['uc']['CompanyID']." AND o.OrderStatus NOT IN (8, 9) AND FROM_UNIXTIME(o.OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
    $clientMonthSql = "SELECT c.ClientCompanyName,o.OrderID FROM ".DATATABLE."_order_orderinfo o RIGHT JOIN ".DATATABLE."_order_client c on o.OrderUserID=c.ClientID
    where OrderID in (".$subsql.")";
}else{ //管理员和商业公司可以看到所有订单
    $clientMonthSql = "SELECT c.ClientCompanyName,o.OrderID FROM ".DATATABLE."_order_orderinfo o RIGHT JOIN ".DATATABLE."_order_client c on o.OrderUserID=c.ClientID where c.ClientCompany=".$_SESSION['uc']['CompanyID']." AND o.OrderStatus NOT IN (8, 9) AND FROM_UNIXTIME(o.OrderDate,'%Y%m')=DATE_FORMAT(CURDATE(),'%Y%m')";
}
$clientMonth = $db->get_results($clientMonthSql);
//处理结果集把OrderID分割成字符串
foreach ($clientMonth as $key => $val) {
    $clientMonthInfo['ClientCompanyName'][$val['ClientCompanyName']][]=$val['OrderID'];
}
foreach ($clientMonthInfo['ClientCompanyName'] as $key => $val) {
    $clientMonthInfo['ClientCompanyName'][$key]['OrderID']=implode(',',$val);
}

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
                            	foreach($clientYesInfo['ClientCompanyName'] as $ktoV=>$toV){
                            		$ktoV = trim($ktoV);
                            	                            ?>
                                <tr>
                                    <td class="mo" title="<?php echo $ktoV;?>"><?php echo $ktoV;?></td>
                                    <td >￥<?php
                                    //2017-12-13 ymm 判断当前登录的人的身份如果是代理商就查询出对应的订单信息
                                    $user_flag = trim($_SESSION['uinfo']['userflag']);
                                    if ($user_flag == '2'){
                                    $sql1 = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID in (".$toV['OrderID'].") and AgentID=".$_SESSION['uinfo']['userid']." order by SiteID asc, BrandID asc, ID asc";
                                    }
                                    else //管理员和商业公司可以看到所有订单
                                    {
                                    $sql1 = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID in (".$toV['OrderID'].") order by SiteID asc, BrandID asc, ID asc";
                                    }
                                    $yestotal=$db->get_results($sql1);
                                    $yes_total=0;
                                    //2017-12-13 ymm 算出负责的订单总金额
                                    foreach ($yestotal as $key => $cvar) {
                                    $yes_total+=$cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
                                    }
                                     $clientYesLine[] = "{value:".$yes_total.", name:'".$ktoV."'}"; 

                                     echo number_format($yes_total, 2, '.', '');?></td>
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
                            	foreach($clientSevInfo['ClientCompanyName'] as $ksv=>$sv){
                            		$ksv = trim($ksv);
                            		$clientSevName[] = "'".$ksv."'";
                            ?>
                                <tr>
                                    <td class="mo"><?php echo $ksv;?></td>
                                    <td >￥<?php 
                                    //2017-12-13 ymm 判断当前登录的人的身份如果是代理商就查询出对应的订单信息
                                    $user_flag = trim($_SESSION['uinfo']['userflag']);
                                    if ($user_flag == '2'){
                                    $sev_sql = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID in (".$sv['OrderID'].") and AgentID=".$_SESSION['uinfo']['userid']." order by SiteID asc, BrandID asc, ID asc";
                                    }
                                    else //管理员和商业公司可以看到所有订单
                                    {
                                    $sev_sql = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID in (".$sv['OrderID'].") order by SiteID asc, BrandID asc, ID asc";
                                    }
                                    $sevtotal=$db->get_results($sev_sql);
                                    $sev_total=0;
                                    //2017-12-13 ymm 算出负责的订单总金额
                                    foreach ($sevtotal as $key => $cvar) {
                                    $sev_total+=$cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
                                    }
                                    $clientSevLine[] = "{value:".$sev_total.", name:'".$ksv."'}";
                                    echo number_format($sev_total, 2, '.', '');?></td>
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
                            	foreach($clientMonthInfo['ClientCompanyName'] as $mk=>$mV){
                            		$mk = trim($mk);
                            		$clientMonthName[] = "'".$mk."'";
                            ?>
                                <tr>
                                    <td class="mo"><?php echo $mk;?></td>
                                    <td >￥<?php
                                    //2017-12-13 ymm 判断当前登录的人的身份如果是代理商就查询出对应的订单信息
                                    $user_flag = trim($_SESSION['uinfo']['userflag']);
                                    if ($user_flag == '2'){
                                    $mon_sql = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID in (".$mV['OrderID'].") and AgentID=".$_SESSION['uinfo']['userid']." order by SiteID asc, BrandID asc, ID asc";
                                    }
                                    else //管理员和商业公司可以看到所有订单
                                    {
                                    $mon_sql = "select ContentPrice,ContentNumber,ContentPercent from ".DATATABLE."_view_index_cart where CompanyID=".$_SESSION['uinfo']['ucompany']." and OrderID in (".$mV['OrderID'].") order by SiteID asc, BrandID asc, ID asc";
                                    }
                                    $montotal=$db->get_results($mon_sql);
                                    $mon_total=0;
                                    //2017-12-13 ymm 算出负责的订单总金额
                                    foreach ($montotal as $key => $cvar) {
                                    $mon_total+=$cvar['ContentNumber']*$cvar['ContentPrice']*$cvar['ContentPercent']/10;
                                    }
                                    $clientMonthLine[] = "{value:".$mon_total.", name:'".$mk."'}";
                                    echo number_format($mon_total, 2, '.', '');?></td>
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

