<?php 
$menu_flag = "manager";
include_once ("header.php");

if(DHB_RUNTIME_MODE !== 'experience'){
	exit('not experience error!');
}

$arrIndustryOption = array();
$arrTempOption = $db->get_row("select *from ".DATABASEU.DATATABLE."_ty_option where Name='industry' ");
if(!empty($arrTempOption['Value'])){
	$arrIndustryOption = json_decode($arrTempOption['Value'],true);
}

// 读取所有的行业
$arrAllIndustrys = array();
$arrTemps = $db->get_results( " select IndustryID,IndustryName from ".DATABASEU.DATATABLE."_order_industry " );
if(is_array($arrTemps)){
	foreach($arrTemps as $var){
		$arrAllIndustrys[$var['IndustryID']] = $var['IndustryName'];
	}
}

$arrIndustryUsedatas = array();

$arrUse = array();
$arrNoUser = array();

$arrTemps2 = $db->get_results("select count(*) as allrow,CompanyIndustry,IsUse from ".DATABASEU.DATATABLE."_order_company where IsSystem='0' and CompanyFlag = '0' group by CompanyIndustry,IsUse ");

if(is_array($arrTemps2)){
	foreach($arrTemps2 as $val){
		if($val['IsUse']=='1'){
			$arrUse[$val['CompanyIndustry']] = $val['allrow'];
		}else{
			$arrNoUser[$val['CompanyIndustry']] = $val['allrow'];
		}
	}
}

if(is_array($arrAllIndustrys)){
	foreach($arrAllIndustrys as $key=>$val){
		$nUser = intval(isset( $arrUse[$key] ) ? $arrUse[$key] : 0);
		$nNoUser =intval( isset( $arrNoUser[$key] ) ? $arrNoUser[$key] : 0);
		$nTotal = $nUser+$nNoUser;
		$nBl = 0;
		if($nTotal>0){
			$nBl = (round($nUser/$nTotal,2)*100).'%';
		}

		$arrIndustryUsedatas[$key]=array(
			'id' => $key,
			'name' => $val,
			'total' => $nTotal,
			'use' => $nUser,
			'no_use' => $nNoUser,
			'bl' => $nBl,
		);
	}
}

$nEndLimitNum = 40;
$nEndBlLimit = 80;

function getNouser($nNoUser){
	global $nEndLimitNum;
	return $nNoUser < $nEndLimitNum ? 'style="color:red;font-weight:bold;font-size:16px;"' : '';
}

function makeColor($nColor){
	global $nEndBlLimit;
	$nColor = intval(rtrim($nColor,'%'));
	$sColor = '';
	if($nColor>=0 && $nColor<=10){
		$sColor = '#04ad5c';
	}elseif($nColor>10 && $nColor<=30){
		$sColor = '#17d278';
	}elseif($nColor>30 && $nColor<=50){
		$sColor = '#54f2a6';
	}elseif($nColor>50 && $nColor<=70){
		$sColor = '#fc8e22';
	}elseif($nColor>70 && $nColor<=90){
		$sColor = '#f17b7b';
	}elseif($nColor>90){
		$sColor = '#fc0c0c';
	}

	return 'style="color:'.$sColor.';font-weight:bold;font-size:14px;"';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>
    

    <div id="bodycontent">
    	<div class="lineblank"></div>        

		<div id="searchline">
        	<div class="locationl"><a name="editname"></a><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250;<a href="experience.php">体验入口</a>&#8250;&#8250; <a href="experience_count.php">体验公司使用情况</a></div>
        </div>

    	
        <div class="line2"></div>
        
        <div class="warning">
			注：这里为后台体验系统行业的公司占用数据。
		</div>
        
        <div class="bline" >
       
<div >
             <table width="100%" cellspacing="0" cellpadding="0" border="0">
               <thead>
                <tr>
                  <td width="100px" class="bottomlinebold">行业ID</td>
				  <td  class="bottomlinebold">行业名字</td>
                  <td width="170px" class="bottomlinebold">总数量</td>
				  <td width="170px" class="bottomlinebold">已使用数量</td>
				  <td width="170px" class="bottomlinebold">剩余数量</td>
				  <td width="170px" class="bottomlinebold">使用率</td>
				  <td width="80px" class="bottomlinebold">增加</td>
				  <td width="80px" class="bottomlinebold">还原</td>
                </tr>
     		 </thead>      		
      		<tbody>
				<?php $nTotal1 = $nTotal2 = $nTotal3 = 0;?>
				<?php if(is_array($arrIndustryUsedatas)):?>
				<?php foreach($arrIndustryUsedatas as $val):?>
				<?php 
					$nTotal1 += $val['total'];
					$nTotal2 += $val['use'];
					$nTotal3 += $val['no_use'];
				?>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				    <td><?php echo $val['id'];?></td>
				    <td><?php echo $val['name'];?></td>
				    <td><?php echo $val['total'];?></td>
				    <td><?php echo $val['use'];?></td>
				    <td <?php echo getNouser($val['no_use']);?>><?php echo $val['no_use'];?></td>
				    <td <?php echo makeColor($val['bl']);?>><?php echo $val['bl'];?></td>
				    <td><a href="experience_company.php?industry=<?php echo $val['id'];?>" target="_blank">前往</a></td>
					<td><a href="experience_reset.php?industry=<?php echo $val['id'];?>" target="_blank">前往</a></td>
                </tr>
				<?php endforeach;?>
				<?php endif;?>
				<tr onmouseout="outStyle(this)" onmouseover="inStyle(this)" class="bottomline" style="">
				    <td><b>数据统计</b></td>
				    <td></td>
				    <td><b><?php echo $nTotal1;?></b></td>
				    <td><b><?php echo $nTotal2;?></b></td>
				    <td><b><?php echo $nTotal3;?></b></td>
				    <td></td>
				    <td></td>
					<td></td>
                </tr>
 				</tbody>                
              </table>
       	  </div>
              
          </div>    
        <br style="clear:both;" />

    </div>
    
    <? include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>