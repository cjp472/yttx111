<?php 
include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
include_once (SITE_ROOT_PATH."/module/statdata.php");

$input	=	new Input;
$in		=	$input->parse_incoming();
	
$order_status_arr = array(
	'0'			=>  '待审核',
	'1'			=>  '备货中',
	'2'			=>  '已开票出库',
	'3'			=>  '已收货',
	'5'         =>  '已付款',
	'7'         =>  '已完成',
	'8'         =>  '客户取消',
	'9'         =>  '管理员取消'
 );

if($in['m']=="show_list")
{	
	$db = dbconnect::dataconnect()->getdb();
	if($in['stype'] == "day")
	{
		$headersmg = '<table width="98%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td height="28" align="left" width="28%" class="bottomlinebold">订单号</td>
				  <td align="left" class="bottomlinebold">&nbsp;下单时间</td>
                  <td align="right" width="20%" class="bottomlinebold">订单金额&nbsp;&nbsp;</td>
				  <td align="right" width="15%">订单状态</td>
                </tr>
     		 </thead>			 
			 <tbody>';

		$statsql  = "SELECT OrderID,OrderSN,OrderTotal,OrderDate,OrderStatus from ".DATATABLE."_order_orderinfo where OrderCompany=".$_SESSION['cc']['ccompany']." and OrderUserID = ".$_SESSION['cc']['cid']." and left(OrderSN,8)='".$in['did']."' and OrderStatus!=8 and OrderStatus!=9 order by OrderID asc limit 0,1000";
		$statdata = $db->get_results($statsql);
		$totalm = 0;
		if(!empty($statdata))
		{			
			foreach($statdata as $var)
			{
				$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left">'.$var['OrderSN'].'</td>
				  <td align="left">&nbsp;'.date("Y-m-d H:i",$var['OrderDate']).'</td>
                  <td align="right" class="TitleNUM">¥ '.$var['OrderTotal'].'</td>
				  <td align="right">'.$order_status_arr[$var['OrderStatus']].'</td>
			 </tr>';
			 $totalm = $totalm + $var['OrderTotal'];
			}
		}
		$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td ><strong>合计：</strong></td>
				  <td align="left" ><strong>&nbsp;'.count($statdata).'个</strong></td>
                  <td class="TitleNUM"><strong>¥ '.$totalm.'</strong></td>
				  <td>&nbsp;</td>
			 </tr>
			 </tbody>
			</table>';
		echo $headersmg;
	}
	if($in['stype'] == "rday")
	{
		$headersmg = '<table width="98%" border="0" cellspacing="0" cellpadding="0">               
               <thead>
                <tr>
                  <td height="28" align="left" width="28%" class="bottomlinebold">退货号</td>
				  <td align="left" class="bottomlinebold">&nbsp;退货时间</td>
                  <td align="right" width="20%" class="bottomlinebold">退货单金额&nbsp;&nbsp;</td>
                </tr>
     		 </thead>			 
			 <tbody>';

		$statsql  = "SELECT ReturnID,ReturnSN,ReturnTotal,ReturnDate from ".DATATABLE."_order_returninfo where ReturnCompany=".$_SESSION['cc']['ccompany']." and ReturnClient = ".$_SESSION['cc']['cid']." and left(ReturnSN,9)='R".$in['did']."' and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) order by ReturnID asc limit 0,1000";
		$statdata = $db->get_results($statsql);
		$totalm = 0;
		if(!empty($statdata))
		{			
			foreach($statdata as $var)
			{
				$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left">'.$var['ReturnSN'].'</td>
				  <td align="left">&nbsp;'.date("Y-m-d H:i",$var['ReturnDate']).'</td>
                  <td class="TitleNUM">¥ '.$var['ReturnTotal'].'</td>
			 </tr>';
			 $totalm = $totalm + $var['ReturnTotal'];
			}
		}
		$headersmg .= '<tr class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				  <td align="left"><strong>合计：</strong></td>
				  <td align="left"><strong>&nbsp;'.count($statdata).'个</strong></td>
                  <td  class="TitleNUM"><strong>¥ '.$totalm.'</strong></td>
			 </tr>
			 </tbody>
			</table>';
		echo $headersmg;
	}

}elseif($in['m']=="year"){
	
	$rmsg = '';
	if(empty($in['y'])) $in['y'] = date("Y");
	for($i=2010;$i<(date("Y")+1);$i++)
	{
		$ylist[] = $i;
	}
	$rdata   = statdata::statorder_y($in);
		
	$total0  = 0;
	$total1  = 0;
	$rarr = null;
	if(!empty($rdata['order0']))
	{
		foreach($rdata['order0'] as $rvar)
		{
			$rarr[$rvar['ODate']] = $rvar['totalnumber'];
			$total0 = $total0 + $rvar['OTotal'];
			$total1 = $total1 + $rvar['totalnumber'];
		}
	}
	$statdata = $rdata['order'];

	if(!empty($statdata))
	{
	
		$totalm = 0;
		$totaln = 0;
		foreach($statdata as $var)
		{
				$carr[] = "'".$var['ODate']."'";
				$tarr[] = $var['OTotal'];
				$narr[] = $var['totalnumber'];

			$totalm = $totalm + $var['OTotal'];
			$totaln = $totaln + $var['totalnumber'];
		}
			$carrmsg = implode(",",$carr);
			$tarrmsg = implode(",",$tarr);
			$narrmsg = implode(",",$narr);
	}

	include template("statistics_y");

}elseif($in['m']=="month"){

	if(empty($in['y'])) $in['y'] = date("Y");
	if(empty($in['mon'])) $in['mon'] = date("m");

	$ylist = array(2010,2011,2012,2013,2014,2015,2016,2017,2018,2019,2020,2021,2022,2023,2024,2025);
	$mlist = array(1,2,3,4,5,6,7,8,9,10,11,12);

	$rdata   = statdata::statorder_m($in);
	$total0  = 0;
	$total1  = 0;
	$rarr = null;
	if(!empty($rdata['order0']))
	{
		foreach($rdata['order0'] as $rvar)
		{
			$rarr[$rvar['ODate']] = $rvar['totalnumber'];
			$total0 = $total0 + $rvar['OTotal'];
			$total1 = $total1 + $rvar['totalnumber'];
		}
	}
	$statdata = $rdata['order'];
	if(!empty($statdata))
	{	
		$totalm = 0;
		$totaln = 0;
		foreach($statdata as $var)
		{
				$carr[] = "'".$var['ODate']."'";
				$tarr[] = $var['OTotal'];
				$narr[] = $var['totalnumber'];

			$totalm = $totalm + $var['OTotal'];
			$totaln = $totaln + $var['totalnumber'];
		}
			$carrmsg = implode(",",$carr);
			$tarrmsg = implode(",",$tarr);
			$narrmsg = implode(",",$narr);
	}

	include template("statistics_m");
	
}elseif($in['m']=="day"){

	if(empty($in['cordate'])) $in['cordate'] = date("Y-m-d");

	$statdata   = statdata::statorder_d($in);
	if(!empty($statdata))
	{		
		$totalm = 0;
		$totaln = 0;
		foreach($statdata as $var)
		{
			if(empty($snmsg))
			{
				$snmsg = "'".$var['OrderSN']."'";
				$tmsg    = $var['OrderTotal'];
			}else{
				$snmsg .= ","."'".$var['OrderSN']."'";
				$tmsg   .= ",".$var['OrderTotal'];
			}
			$totalm = $totalm + $var['OrderTotal'];
		}
	}
	include template("statistics_d");
	
}elseif($in['m']=="finance"){
	
	$rmsg = '';
	if(empty($in['begindate'])) $in['begindate'] = '';
	if(empty($in['enddate']))   $in['enddate']   = '';
	if(!empty($in['begindate']) && empty($in['enddate'])) $in['enddate'] = date("Y-m-d");
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		$rmsg = '<p>注意：时间跨度不能超过一年!</p>';
	}else{
		$statdata   = statdata::statorder_finance($in);
		if(!empty($statdata))
		{
			$finactpencent[1]['p'] = round($statdata['y']/$statdata['yin']*100,2);
			$finactpencent[1]['n'] = '已确认到帐';

			$finactpencent[2]['p'] = round($statdata['w']/$statdata['yin']*100,2);
			$finactpencent[2]['n'] = '未付款金额';

			$finactpencent[3]['p'] = round($statdata['t']/$statdata['yin']*100,2);
			$finactpencent[3]['n'] = '在途付款金额';
		}
	}
	include template("statistics_finance");
	
}elseif($in['m']=="product"){

	$rmsg = '';
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate']   = date("Y-m-d");
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		$rmsg = '<p>注意：时间跨度不能超过一年!</p>';
	}else{
		
		$rdata   = statdata::statorder_product($in);
		$n=1;
		$totalr = 0;
		$totalm = 0;
		$totalamount = 0;
		$returndata = null;
		$statdata = $rdata['order'];
		if(!empty($rdata['return']))
		{
			foreach($rdata['return'] as $rvar)
			{
				$returndata[$rvar['ContentID']] = $rvar['cnum'];
			}
		}
		for($i=0;$i<count($statdata);$i++)
		{
			$statdata[$i]['onum'] = $statdata[$i]['cnum'];
			$totalm = $totalm + $statdata[$i]['onum'];
			if(!empty($returndata[$statdata[$i]['ContentID']]))
			{
				$statdata[$i]['rnum'] = $returndata[$statdata[$i]['ContentID']];				
				$totalr = $totalr + $statdata[$i]['rnum'];
			}
			$statdata[$i]['cnum'] = $statdata[$i]['onum'] - $statdata[$i]['rnum'];
			if(empty($statdata[$i]['rnum'])) $statdata[$i]['rnum'] = 0;
			if(empty($statdata[$i]['cnum'])) $statdata[$i]['cnum'] = 0;

			$totalamount = $totalamount + $statdata[$i]['amount'];
		}
		$total = $totalm - $totalr;

	}
	include template("statistics_product");	

}elseif($in['m']=="productcs"){

	if($in['stype']=="color") $typename = '颜色'; else $typename = '规格';
	$rmsgt = '';
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate']   = date("Y-m-d");
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		$rmsgt = '<p>注意：时间跨度不能超过一年!</p>';
	}else{	

		$rdata   = statdata::statorder_product_cs($in);
		$pinfo   = $rdata['pinfo'];
		$statdata =  $rdata['order'];

		$n=1;
		$totalr  = 0;
		$totalm = 0;

		for($i=0;$i<count($statdata);$i++)
		{
			$statdata[$i]['rnum']   = 0;
			$statdata[$i]['onum'] = $statdata[$i]['cnum'];
			if(!empty($rdata['return']))
			{
				foreach($rdata['return'] as $var)
				{
					if($var['ContentCS']==$statdata[$i]['ContentCS'])
					{
						$statdata[$i]['rnum'] = $var['rnum'];
					}
				}
			}
			$statdata[$i]['cnum'] = $statdata[$i]['onum'] - $statdata[$i]['rnum'];
			if(empty($statdata[$i]['rnum'])) $statdata[$i]['rnum'] = 0;
			if(empty($statdata[$i]['cnum'])) $statdata[$i]['cnum'] = 0;
			
			$totalr   = $totalr + $statdata[$i]['rnum'];
			$totalm = $totalm + $statdata[$i]['onum'];

			if(empty($cmsg))
			{
				$cmsg = "'".$statdata[$i]['ContentCS']."'";
				$pmsg = $statdata[$i]['cnum'];
				$rmsg  = $statdata[$i]['rnum'];
			}else{
				$cmsg .= ",'".$statdata[$i]['ContentCS']."'";
				$pmsg .= ",".$statdata[$i]['cnum'];
				$rmsg  .= ",".$statdata[$i]['rnum'];
			}
		}
		$total = $totalm - $totalr;
	}
	include template("statistics_product_cs");	

}elseif($in['m']=="productall"){

	$rmsgt = '';
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate']   = date("Y-m-d");
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		$rmsgt = '<p>注意：时间跨度不能超过一年!</p>';
	}else{	

		$rdata    = statdata::statorder_product_all($in);
		$pinfo    = $rdata['pinfo'];
		$n=1;
		$totalr = $totalm = 0;
		$rarr = null;
		if(!empty($rdata['return']))
		{
			foreach($rdata['return'] as $rvar)
			{
				$kid = commondata::make_kid($rvar['ContentID'], $rvar['ContentColor'], $rvar['ContentSpecification']);
				if(empty($rarr[$kid])) $rarr[$kid] = 0;
				$rarr[$kid] = $rarr[$kid] + $rvar['ContentNumber'];
			}
		}

		for($i=0;$i<count($rdata['order']);$i++)
		{			
			$kid = commondata::make_kid($rdata['order'][$i]['ContentID'], $rdata['order'][$i]['ContentColor'], $rdata['order'][$i]['ContentSpecification']);
			if(empty($oarr[$kid]['onum']))
			{
				$oarr[$kid] = $rdata['order'][$i];
				$oarr[$kid]['onum'] = 0;
			}
			$oarr[$kid]['onum'] = $oarr[$kid]['onum'] + $rdata['order'][$i]['ContentNumber'];
		}

		foreach($oarr as $key=>$var)
		{
			$statdata[$key] = $var;
			if(empty($rarr[$key])) $rarr[$key] = 0;
			$statdata[$key]['rnum'] = $rarr[$key];
			$statdata[$key]['cnum'] = $statdata[$key]['onum'] - $statdata[$key]['rnum'];
			$totalr = $totalr + $statdata[$key]['rnum'];
			$totalm = $totalm + $statdata[$key]['onum'];
							
			if(empty($cmsg))
			{
				$cmsg = "'".$var['ContentColor']."/".$var['ContentSpecification']."'";
				$pmsg = $statdata[$key]['cnum'];
				$rmsg  = $statdata[$key]['rnum'];
			}else{
				$cmsg .= ",'".$var['ContentColor']."/".$var['ContentSpecification']."'";
				$pmsg .= ",".$statdata[$key]['cnum'];
				$rmsg  .= ",".$statdata[$key]['rnum'];
			}

		}
		$total = $totalm - $totalr;
	}
	include template("statistics_product_all");

}elseif($in['m']=="return"){

	$rmsg = '';
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate']   = date("Y-m-d");
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		$rmsg = '<p>注意：时间跨度不能超过一年!</p>';
	}else{
		$statdata   = statdata::statreturn($in);

		for($i=0;$i<count($statdata);$i++)
		{
			$statdata[$i]['ODate'] = substr($statdata[$i]['ODate'],1);
		}

		if(!empty($statdata))
		{	
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				if(empty($snmsg))
				{
					$snmsg  = "'".$var['ODate']."'";
					$tmsg    = $var['OTotal'];
					$nmsg   =  $var['totalnumber'];
				}else{
					$snmsg .= ","."'".$var['ODate']."'";
					$tmsg   .= ",".$var['OTotal'];
					$nmsg   .= ",".$var['totalnumber'];
				}
				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
			}
		}
	}
	include template("statistics_return");	

}else{

	$rmsg = '';
	if(empty($in['begindate'])) $in['begindate'] = date('Y-m-d',strtotime('-1 months'));
	if(empty($in['enddate']))   $in['enddate']   = date("Y-m-d");
	if((strtotime($in['enddate']) - strtotime($in['begindate'])) / (60*60*24) > 365 )
	{
		$rmsg = '<p>注意：时间跨度不能超过一年!</p>';
	}else{
		$rdata   = statdata::statorder($in);
		$total0  = 0;
		$total1  = 0;
		if(!empty($rdata['order0']))
		{
			foreach($rdata['order0'] as $rvar)
			{
				$rarr[$rvar['ODate']] = $rvar['totalnumber'];
				$total0 = $total0 + $rvar['OTotal'];
				$total1 = $total1 + $rvar['totalnumber'];
			}
		}
		$statdata = $rdata['order'];
		if(!empty($statdata))
		{		
			$totalm = 0;
			$totaln = 0;
			foreach($statdata as $var)
			{
				$carr[] = "'".$var['ODate']."'";
				$tarr[] = $var['OTotal'];
				$narr[] = $var['totalnumber'];

				$totalm = $totalm + $var['OTotal'];
				$totaln = $totaln + $var['totalnumber'];
			}
			$carrmsg = implode(",",$carr);
			$tarrmsg = implode(",",$tarr);
			$narrmsg = implode(",",$narr);
		}
	}

	include template("statistics");	
//END
}


?>