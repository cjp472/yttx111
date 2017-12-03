<?php 
    $menu_flag = "sale_count";
    include_once ("header.php");
    setcookie("backurl", $_SERVER['REQUEST_URI']);
    
    if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr) && !in_array($_SESSION['uinfo']['userid'],$sqArr)) exit('非法路径!');
    
    $freemsg = '';
    $paymsg = '';
    $totalmsg = '';
    if(!empty($in['sale']))
        $freemsg .= ' AND CS_SaleUID = '.$in['sale'];
    else 
        $sqlmsg .= ' AND CS_SaleUID IS NOT NULL';
    
    if(!empty($in['bdate']))
    {
        $freemsg .= " AND CS_OpenDate >= '".$in['bdate']."'";
        $paymsg .= " AND s.to_time >= " . strtotime($in['bdate'] . ' 00:00:00');
    }
    
    if(!empty($in['edate']))
    {
        $freemsg .= " AND CS_OpenDate <= '".$in['edate']."'";
        $paymsg .= " AND s.to_time <= " . strtotime($in['edate'] . ' 23:59:59');
    }
    
    /** 默认统计当前月销售数据  **/
    if(!isset($in['bdate']) && !isset($in['edate']))
    {
        $freemsg .= " AND CS_OpenDate >= '".date('Y-m-01',time())."'";
        $paymsg .= " AND s.to_time >= " . strtotime(date('Y-m-01',time()) . ' 00:00:00');
        
        $freemsg .= " AND CS_OpenDate <= '".date('Y-m-t',time())."'";
        $paymsg .= " AND s.to_time <= " . strtotime(date('Y-m-t',time()) . ' 23:59:59');
        
        $in['bdate'] = date('Y-m-01',time());
        $in['edate'] = date('Y-m-t',time());
    }
      
    /** 取在职的销售员 **/
//     $sale = $db->get_results("SELECT s.* FROM ".DATABASEU.DATATABLE."_order_sale as s where s.SaleID > 0 ".$saleflag." ORDER BY s.ID ASC");
    
    /** 取销售员免费单总数  取通过审核且在订单表中不存在开通类型的数据,以支付信息的到账状态为准   **/
    $freeSQL = "SELECT COUNT(CS_ID) AS FreeCNT,CS_SaleUID FROM (
                	SELECT CS_ID,CS_SaleUID FROM ".DATABASEU.DATATABLE."_order_cs WHERE CS_Company NOT IN(
                		SELECT DISTINCT o.company_id FROM ".DATABASEU.DATATABLE."_buy_order o 
            		    INNER JOIN ".DATABASEU.DATATABLE."_buy_stream s 
        		        ON o.order_no = s.order_no 
        		        WHERE o.TYPE = 'product' AND s.status = 'T' ".$paymsg."
                	)  AND CS_Flag ='T' ".$freemsg."
                ) AS FreeOrder GROUP BY CS_SaleUID";
    
    $freeRst = $db->get_results($freeSQL);
    
    /** 取销售员付费单总数  取通过审核且在订单表中存在开通、微信部署、ERP类型的数据,以支付信息的到账状态为准 **/
    $paySQL = "SELECT cs.CS_SaleUID,SUM(PayOrder.PayCNT)AS PayCNT FROM (
                	SELECT s.company_id,COUNT(*) AS PayCNT FROM ".DATABASEU.DATATABLE."_buy_order o 
                	INNER JOIN ".DATABASEU.DATATABLE."_buy_stream s 
                	ON o.order_no = s.order_no 
                	WHERE o.TYPE IN ('product','weixin','erp') AND s.status = 'T' ".$paymsg."
                	GROUP BY s.company_id
                ) AS PayOrder LEFT JOIN ".DATABASEU.DATATABLE."_order_cs cs ON cs.CS_Company = PayOrder.company_id WHERE cs.CS_Flag ='T' GROUP BY cs.CS_SaleUID";
    
    $payRst = $db->get_results($paySQL);
    
    /** 取销售员付费单总金额   取通过审核且在订单表中存在除续费类型的已到账的数据  **/
    $totalSQL = "SELECT s.CS_SaleUID,SUM(total.Total) AS Total FROM (
                	SELECT SUM(s.amount) AS Total,s.company_id FROM ".DATABASEU.DATATABLE."_buy_stream s 
                	INNER JOIN ".DATABASEU.DATATABLE."_buy_order o 
                	ON s.order_no = o.order_no 
                	WHERE o.type <> 'renewals' AND s.status = 'T' ".$paymsg."
                	GROUP BY s.company_id
                ) AS total LEFT JOIN ".DATABASEU.DATATABLE."_order_cs s ON s.CS_Company = total.company_id WHERE s.CS_Flag ='T' GROUP BY s.CS_SaleUID";
    
    $totalRst = $db->get_results($totalSQL);
 
    /** 格式化统计数组 **/
    foreach($freeRst as $free)
    {
        $freeCNT[$free['CS_SaleUID']] = $free['FreeCNT'];
    }
    
    foreach($payRst as $pay)
    {
        $payCNT[$pay['CS_SaleUID']] = $pay['PayCNT'];
    }
    
    foreach($totalRst as $pay)
    {
        $total[$pay['CS_SaleUID']] = $pay['Total'];
    }
    
    /** 取参与统计的在职销售员 **/
    $salesql = "";
    if(!empty($in['sale']))
        $salesql .=" AND ID = ".$in['sale'];
    if(!empty($in['dept']))
        $salesql .=" AND SaleDepartment = ".$in['dept'];

    if(!empty($in['flagval']) && ($in['flagval'] != 'ALL'))
    {
        $salesql .= " AND s.SaleFlag = '{$in['flagval']}' ";
    }
    if(empty($in['flagval']))
        $salesql .= " AND s.SaleFlag = 'T' ";
    
//     if(!empty($salesql))
//     {
        $saleList = $db->get_results("SELECT s.* FROM ".DATABASEU.DATATABLE."_order_sale as s where s.ID > 0 ".$salesql." ORDER BY s.ID ASC");
//     }
//     else 
//         $saleList = $sale;
  
    /** 缓存统计数据 **/
    $_SESSION['SaleCount']['salelist']     = $saleList;
    $_SESSION['SaleCount']['freeCNT']      = $freeCNT;
    $_SESSION['SaleCount']['payCNT']       = $payCNT;
    $_SESSION['SaleCount']['total']        = $total;
    
    
    /** 格式化图表Json **/
    foreach($saleList as $saler)
    {
        $saleIds[] = $saler['ID'];
        $saleChart[]  = "'".$saler['SaleName']."'";
        $freeChart[]  = intval($freeCNT[$saler['ID']]);
        $payChart[]   = intval($payCNT[$saler['ID']]);
        $totalChart[] = intval($total[$saler['ID']]);
    }
$sqlSeller = "select saleId,generalizeNo from ".DATABASEU.DATATABLE."_order_generalize where generalizeType = 'seller'";
$idNos = $db->get_results($sqlSeller);
$idNosArr = array();
foreach($idNos as $idNo){
    $idNosArr[$idNo['saleId']][] = $idNo['generalizeNo'];
}
    $salemsg  = implode(",",$saleChart);
    $freemsg  = implode(",",$freeChart);
    $paymsg   = implode(",",$payChart);
    $totalmsg = implode(",",$totalChart);
$idNosStr = '';
foreach($idNosArr as $id=>$nos){
    $nosStr = implode(',',$nos);
    $idNosStr = $idNosStr.$id.'='.$nosStr.';';
}
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
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script type="text/javascript" src="../plugin/Highcharts/js/highcharts.js"></script>
<script type="text/javascript" src="../plugin/Highcharts/js/modules/exporting.js"></script>

<script src="js/sale.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style type="text/css">
 .analysis{}
 .analysis td{font-size:14px;}

 .sfont18{font-size:18px; color:#cc0000; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
  .sfont20{font-size:20px; color:#FF6600; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
  .sfont24{font-size:24px; color:#009933; font-weight:bold;font-family:Verdana, Arial, Helvetica, sans-serif;}
</style>
<script type="text/javascript">
var chart;
var chart1;
$(function() {
    $("body").on('click','.blockOverlay',function(){
        $.unblockUI();
    });

    $("#bdate").datepicker({changeMonth: true,	changeYear: true});
    $("#edate").datepicker({changeMonth: true,	changeYear: true});

    chart = new Highcharts.Chart({
		chart: {
			renderTo: 'container',
			zoomType: 'xy'
		},
		title: {
			text: "销售数据分析"
		},
		subtitle: {
			text: ''
		},
		xAxis: [{
			labels: {    
		        rotation: -45
		    },
			categories: [<? echo $salemsg;?>]
		}],
		yAxis: [{ // Primary yAxis
			labels: {
				formatter: function() {
					return this.value +'元';
				},
				style: {
					color: '#89A54E'
				}
			},
			title: {
				text: '金额',
				style: {
					color: '#89A54E'
				}
			},
			opposite: true
		},{ // Secondary yAxis
			title: {
				text: '单数',
				style: {
					color: '#4572A7'
				}
			},
			labels: {
				formatter: function() {
					return this.value +' 单';
				},
				style: {
					color: '#4572A7'
				}
			}
		}],
		tooltip: {
			formatter: function() {
				return ''+
					this.x +': '+ this.y +
					(this.series.name == '金额' ? ' 元' : '单');
			}
		},
		legend: {
			layout: 'vertical',
			align: 'left',
			x: 120,
			verticalAlign: 'top',
			y: 100,
			floating: true,
			backgroundColor: '#FFFFFF'
		},
		series: [{
			name: '金额',
			color: '#4572A7',
			type: 'spline',
			data: [<? echo $totalmsg;?>]
		}, {
			name: '免费单',
			color: '#89A54E',
			type: 'column',
			yAxis: 1,
			data: [<? echo $freemsg;?>]
		}, {
			name: '付费单',
			color: '#7CB5EC',
			type: 'column',
			yAxis: 1,
			data: [<? echo $paymsg;?>]
		}]
	});

    ///////////////////////sale_url分享统计start////////////////////////////////

    chart1 = new Highcharts.Chart({
        chart: {
            renderTo: 'sale_url',
            type: 'column',
            margin: [ 50, 50, 100, 80]
        },
        title: {
            text: '地址分享分析'
        },
        xAxis: {
            categories:[<? echo $salemsg;?>],
            labels: {
                rotation: -45,
                align: 'right',
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: '分享次数'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: '被分享了: <b>{point.y} 次</b>',
        },
        series: [{
            name: '分享次数',
            type: 'column',
            data: [
                <?php
                    for($i = 0; $i<count($saleIds);$i++){
                    echo '0,';
                    }
                    ?>
            ],
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                x: 4,
                y: 10,
                style: {
                    fontSize: '13px',
                    fontFamily: 'Verdana, sans-serif',
                    textShadow: '0 0 3px black'
                }
            }
        }]
    });
    ///////////////////////sale_url分享统计end//////////////////////////////////

});
</script>
    <script src="js/function.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
            var object = eval('ids = <?php echo json_encode($saleIds);?>');
            var str = "<?php echo $idNosStr;?>";
            var idsNew = new Array();
            if(ids.length != 0){
                for(var i=0;i<ids.length;i++)
                {
                    idsNew[ids[i]] = 0;
                }
            }
            $.post(
                "do_generalize.php",
                {
                    m:"generalize_select_generalize", //请求方法
                    url:"<?php echo DHB_HK_URL;?>/?f=generalizeNum", //请求第三方数据源地址
                    p:Math.random(),
                    s:str,
                    startTime:"<?php echo $in['bdate'];?>",
                    endTime:"<?php echo $in['edate'];?>"
                },
                function(result){
                    if(result['n'].length != 0){
                        for(var id in idsNew){
                            for(var key in result['n']){  //key是id
                                if(id == key){
                                    idsNew[id] = result['n'][key];
                                }
                            }
                        }
                    }
                    $.ajaxSettings.async = false;
                    var data1 = [];
                    var idsNum = "";
                    if (result['n'].length == 0)
                    {
                        $("#sale_url").css("height","200px");
                        $("#sale_url").html("<div style='text-align:center;margin-top: 100px;'>暂无数据</div>");
                    } else{
                        for(var a in idsNew){
                            idsNum = idsNum + '' +a + ':'+idsNew[a]+',';
                            if(idsNew.hasOwnProperty(a)) {
                                data1.push([idsNew[a]]);
                            }
                        };
                        chart1.series[0].setData(data1);
                    }
                    idsNum = trim(idsNum,',');
                    $("#idNums").val(idsNum);

                },'json');
        });
    </script>
</head>

<body>
    <?php include_once ("top.php");?> 
    <?php include_once ("inc/son_menu_bar.php");?>      

    <div id="bodycontent">
    	<div class="lineblank"></div>
    
        <div id="searchline">
        	<div class="leftdiv">
        
        	  <form id="FormSearch" name="FormSearch" method="get" action="sale_count.php">        	    
        		<label>
        		    &nbsp;&nbsp;<strong>搜索</strong>：
            		<select id="sale" name="sale"  style="width:180px;" class="select2">
                        <option value="" >⊙ 所有销售员</option>
                        <?php 
                        	foreach($sale as $sval)
                        	{
                        		if($in['sale'] == $sval['ID']) $smsg = 'selected="selected"'; else $smsg ="";
                        
                        		echo '<option value="'.$sval['ID'].'" '.$smsg.' title="'.$sval['SaleName'].'"  >'.$sval['SaleName'].'-'.$sale_depart[$sval['SaleDepartment']].'-'.$sval['SalePhone'].'</option>';
                        	}
                        ?>
                    </select>
                </label>
                <label>
                    <select id="dept" name="dept"  style="width:165px;" class="select2">
                        <option value="" >⊙ 所有部门</option>
                        <?php 
                        	foreach($sale_depart as $key => $accvar)
                        	{
                        		if($in['dept'] == $key) $smsg = 'selected="selected"'; else $smsg ="";
                        
                        		echo '<option value="'.$key.'" '.$smsg.' title="'.$accvar.'"  >'.$accvar.'</option>';
                        	}
                        ?>
                    </select>
                </label>
                <select id="flagval" name="flagval"  style="width:165px;" class="select2">
                    <option value="ALL" <?php if($in['flagval'] == 'ALL') echo 'selected="selected"';?>>⊙ 所有状态</option>
                    <option value="T" <?php if($in['flagval'] == 'T' || (empty($in['flagval']))) echo 'selected="selected"';?>>在职</option>
                    <option value="F" <?php if($in['flagval'] == 'F') echo 'selected="selected"';?>>离职</option>
                </select>
                </label>
                <label>&nbsp;&nbsp;统计时间： 
                    <input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - 
                </label>
                <label>&nbsp;&nbsp;
                    <input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /> 
                </label>		
                <label>                    
                    <input name="d" type="hidden" value="<?php echo $in['d']; ?>"/>
                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />       
                </label>
              </form>
            </div>
            <div class="location">
                <form id="export" name="export" method="post" action="do_sale.php">
                    <input type="hidden" name='m' id='m' value='export' />
                    <input type="hidden" name='idNums' id='idNums' value="" />
                    <input type="submit" class="mainbtn" value="导出Excel" title="导出Excel" />
                </form>
            </div>
        </div>    	
    
        <div class="line2"></div>
    
        <div class="bline">
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <fieldset class="fieldsetstyle">
        			<legend>销售统计图</legend>
                     <table width="100%" border="0" cellspacing="0" cellpadding="0" >
             			 <tr>
        					 <td >
        					   <div id="container" ></div>
        					 </td>
             			 </tr>
        			</table>
        			<table width="90%" border="0" cellspacing="0" cellpadding="0" class="analysis"> 
    			     <?php
                        	if(!empty($saleList))
                        	{
                        	    $freeNum = $payNum = $totalNum = 0;
                        	    
                        		foreach($saleList as $lsv)
                        		{
                        		    $freeNum += $freeCNT[$lsv['ID']];
                        		    $payNum += $payCNT[$lsv['ID']];
                        		    $totalNum += $total[$lsv['ID']];
                        		}
                        	}
                        ?> 
        			 <tr  >
                          <td align="right" width="10%">免费单总数：</td>
        				  <td width="10%" class="sfont20" align="right"><? echo $freeNum;?> 单</td>
        				  <td align="right" width="10%">付费单总数：</td>
        				  <td width="10%" class="sfont20" align="right"><? echo $payNum;?> 单</td>
        				  <td align="right" width="10%">金额：</td>
        				  <td class="sfont24" width="18%" align="right"> ¥ <? echo $totalNum;?></td>
        
        			 </tr>
    			 </table>
    			</fieldset>

                <br/>

                <fieldset class="fieldsetstyle">
                    <legend>销售数据分析</legend>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <td width="6%" class="bottomlinebold">编号</td>
                            <td class="bottomlinebold">姓名</td>
                            <td width="15%" class="bottomlinebold">（总）免费单数</td>
                            <td width="15%" class="bottomlinebold">（总）付费单数</td>
                            <td width="20%" class="bottomlinebold">金额</td>
                            <td width="10%" class="bottomlinebold">操作</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(!empty($saleList))
                        {
                            foreach($saleList as $lsv)
                            {
                                ?>
                                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >

                                    <td ><? echo $lsv['ID'];?></td>
                                    <td ><? 
                                        echo $lsv['SaleName'];
                                        if($lsv['SaleFlag'] == 'F')
                                            echo '<font color="red">【离职】</font>';
                                    ?></td>
                                    <td ><a href="javascript:;" onclick="showDetail('showFree','<? echo $lsv['ID'];?>')"><? echo intval($freeCNT[$lsv['ID']]);?></a></td>
                                    <td ><a href="javascript:;" onclick="showDetail('showPay','<? echo $lsv['ID'];?>')"><? echo intval($payCNT[$lsv['ID']]) ;?></a></td>
                                    <td ><a href="javascript:;" onclick="showDetail('showTotal','<? echo $lsv['ID'];?>')"><? echo intval($total[$lsv['ID']]);?></a></td>
                                    <td >
                                        <a href="javascript:;" onclick="showClient('<? echo $lsv['ID'];?>')">[查看客户]</a>
                                    </td>
                                </tr>
                            <?      } ?>
                            <tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
                                <td><b>总计</b></td>
                                <td></td>
                                <td><b><?php echo $freeNum;?> 单</b></td>
                                <td><b><?php echo $payNum;?> 单</b></td>
                                <td><b>￥<?php echo $totalNum;?></b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?	}
                        else
                        {
                            ?>
                            <tr>
                                <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
                            </tr>
                        <? }?>
                        </tbody>
                    </table>
                </fieldset>
    			<br/>

                <fieldset class="fieldsetstyle">
                    <legend>地址分享统计图</legend>
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                        <tr>
                            <td >
                                <div id="sale_url" ></div>
                            </td>
                        </tr>
                    </table>

                </fieldset>


            </form>
        </div>
        <br style="clear:both;" />
    </div>

    <?php include_once ("bottom.php");?>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm6">
        <div class="windowHeader" >
        	<h3 id="windowtitle" style="width:540px"></h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent">
                                正在载入数据...       
        </div>
    </div> 
    <div id="windowForm" style="width:700px">
        <div class="windowHeader">
            <h3 id="windowtitle">销售人员客户信息</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent2">
                                 正在载入数据... 
        </div>
    </div>
</body>
</html>