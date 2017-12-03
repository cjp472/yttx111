<?php 
$menu_flag = "consignment";
$pope	   = "pope_view";
include_once ("header.php");
include_once ("arr_data.php");
include_once ("arr_kuaidi.php");
$fromurl = '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link rel="stylesheet" href="css/printf.css" rel='stylesheet' />
<script language="javascript" src="../plugin/printf/LodopFuncs_11.js"></script>
<object id="LODOP_OB" classid="clsid:2105C259-1E0C-4534-8141-A753534CB4CA" width=0 height=0> 
	<embed id="LODOP_EM" type="application/x-print-lodop" width=0 height=0 pluginspage="../plugin/printf/install_lodop32.exe"></embed>
</object>

<?php
if(!intval($in['ID']))
{
	Error::AlertJs('参数错误!');
}
$cinfo = $db->get_row("SELECT * FROM ".DATATABLE."_order_consignment where ConsignmentCompany=".$_SESSION['uinfo']['ucompany']." and ConsignmentID=".intval($in['ID'])." limit 0,1");
$linfo = $db->get_row("SELECT LogisticsName,LogisticsPinyi FROM ".DATATABLE."_order_logistics where LogisticsCompany=".$_SESSION['uinfo']['ucompany']." and LogisticsID=".$cinfo['ConsignmentLogistics']." limit 0,1");

$cominfo = $db->get_row("SELECT CompanyName,CompanyContact,CompanyPhone,CompanyFax,CompanyAddress FROM ".DATABASEU.DATATABLE."_order_company where CompanyID = ".$_SESSION['uinfo']['ucompany']."  limit 0,1");

$countinfo = $db->get_row("select sum(ContentNumber) as num from ".DATATABLE."_order_out_library where ConsignmentID=".$cinfo['ConsignmentID']."");

$arr_print_kuaidi_v = array_values($arr_print_kuaidi);
if(empty($cinfo['ConsignmentLogistics']) || empty($linfo['LogisticsPinyi']) || !in_array($linfo['LogisticsPinyi'],$arr_print_kuaidi)){
	Error::AlertJs('找不到对应的快递公司打印模板，请与医统天下联系!');
}

$strNJPM = '';
$strNJSL = '';
if(($_SESSION['uinfo']['ucompany'] == '10051') && ($linfo['LogisticsPinyi'] == 'annengwuliu'))
{
	$cartdata = $db->get_results("select c.CompanyID,l.ContentNumber,i.Model from ".DATATABLE."_order_cart c inner join ".DATATABLE."_order_out_library l on c.ID=l.CartID left join ".DATATABLE."_order_content_index i on c.ContentID=i.ID where c.CompanyID=".$_SESSION['uinfo']['ucompany']." and l.ConsignmentID=".intval($in['ID'])." and l.ConType='c' order by i.SiteID asc,c.ID asc limit 5");
	
	$strNJPM .= '型号：\n';
	$strNJSL .= '数量：\n';
	foreach($cartdata as $ckey=>$cvar)
	{
		$strNJPM .= $cvar['Model'].'\n';
		$strNJSL .= $cvar['ContentNumber'].'\n';
	}
}				
?>

</head>

<body style="width:980px; margin:0 auto;"> 
<script language="javascript" type="text/javascript"> 
	var LODOP; //声明为全局变量
	
	CreatePrintPage('<?php echo $linfo['LogisticsPinyi']; ?>','快递单打印','<?php echo $cominfo['CompanyContact'];?>','<?php echo $cominfo['CompanyName'];?>','<?php echo $cominfo['CompanyAddress'];?>','<?php echo $cinfo['InceptMan'];?>','<?php echo $cinfo['InceptCompany'];?>','<?php echo $cinfo['InceptAddress'];?>','<? echo $strNJPM;?>','<? echo $strNJSL;?>','<?php echo $cominfo['CompanyPhone'];?>','<?php echo $cinfo['InceptPhone'];?>','<?php echo $cinfo['InceptArea'];?>');

	function CreatePrintPage(strType,strPName,strJJRXM,strJJRDW,strJJRDZ,strSJRXM,strSJRDW,strSJRDZ,strNJPM,strNJSL,strJJRDH,strSJRDH,strSJCS) {
		LODOP=getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM'));  
		//LODOP.PRINT_INIT(strPName);        //打印任务名
		if(strType == 'zhongtiewuliu'){
			LODOP.SET_PRINT_PAGESIZE(1,2200,1900,strPName);
		}else{
			LODOP.SET_PRINT_PAGESIZE(1,2400,1450,strPName);
		}
		LODOP.SET_PRINT_STYLE("FontSize",12);
		LODOP.SET_PRINT_STYLE("FontName",'微软雅黑');
		switch(strType) {
 			case 'shunfeng':
				//顺丰
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0004.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(144,292,150,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(145,96,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(168,87,300,35,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(295,292,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(296,95,240,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(323,82,250,35,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(433,49,164,40,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(441,296,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(225,160,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(375,171,150,20,strSJRDH);      //收件人电话
				
				break;

			case 'yunda':
				//韵达
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0002.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(90,119,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(119,119,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(142,120,300,40,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(87,423,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(116,421,269,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(142,421,274,45,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(320,166,149,19,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(320,332,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(190,233,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(190,561,134,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(87,566,150,20,strSJCS);      //收件城市
				break;	
			case 'yunda2':
				//韵达
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0002.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(90,119,150,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(119,119,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(142,120,328,40,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(87,423,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(116,421,269,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(142,421,274,45,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(320,166,149,19,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(320,332,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(190,233,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(190,561,134,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(87,566,150,20,strSJCS);      //收件城市
				break;	

			case 'shentong':
				//申通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0013.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(224,138,240,20,strJJRXM);    //寄件人姓名
				LODOP.ADD_PRINT_TEXT(188,126,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(146,120,328,40,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(219,441,150,20,strSJRXM);    //收件人姓名
				LODOP.ADD_PRINT_TEXT(188,432,269,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(140,420,221,39,strSJRDZ);     //收件人详细地址
				//LODOP.ADD_PRINT_TEXT(468,366,70,21,"货物");
				//LODOP.ADD_PRINT_TEXT(225,708,40,20,strNJSL);
				LODOP.ADD_PRINT_TEXT(257,137,150,20,strJJRDH);    //寄件人电话
				LODOP.ADD_PRINT_TEXT(256,449,134,20,strSJRDH);     //收件人电话
				break;

			case 'yuantong':
				//圆通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0024.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(95,123,150,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(123,122,240,20,strJJRDW);     //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(184,63,308,35,strJJRDZ);      //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(107,478,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(134,478,244,25,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(189,427,308,25,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(283,78,123,26,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(288,287,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(220,155,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(218,526,150,20,strSJRDH);      //收件人电话
				break;

			case 'zhongtong':
				//中通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0031.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(100,133,150,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(192,133,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(132,135,320,55,strJJRDZ);    //寄件人的详细地址

				LODOP.ADD_PRINT_TEXT(103,488,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(196,492,244,25,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(134,490,244,50,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(283,143,108,26,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(288,287,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(224,135,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(390,85,150,20,'<?php echo date("Y  m  d  H");?>');     //日期
				LODOP.ADD_PRINT_TEXT(227,496,150,20,strSJRDH);      //收件人电话
				break;

			case 'huitongkuaidi':
				//汇通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0037.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(100,138,150,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(132,138,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(161,153,320,55,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(99,503,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(129,501,244,25,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(200,452,244,50,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(283,143,108,26,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(288,287,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(255,126,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(252,469,150,20,strSJRDH);      //收件人电话
				break;

			case 'zhaijisong':
				//宅急送
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0053.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(143,133,150,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(208,113,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(174,82,320,25,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(279,136,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(335,114,148,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(309,84,314,25,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(439,71,119,26,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(414,112,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(233,110,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(368,109,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(483,66,150,20,strSJCS);      //收件城市
				break;

			case 'ems':
				//EMS
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0066.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(123,133,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(152,191,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(194,78,320,40,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(124,489,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(149,537,228,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(194,429,314,45,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(316,120,119,26,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(322,322,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(125,298,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(123,664,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(241,477,150,20,strSJCS);      //收件城市
				break;

			case 'tiantian':
				//海航天天
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0050.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(125,137,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(158,151,244,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(188,105,320,55,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(124,489,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(158,504,243,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(194,429,314,45,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(2120,207,119,26,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(287,367,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(249,129,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(252,489,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(123,684,150,20,strSJCS);      //收件城市
				break;


			case 'guotongkuaidi':
				//国通快递
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0086.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(201,120,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(169,122,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(114,114,320,50,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(202,480,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(169,480,148,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(118,475,274,45,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(304,270,130,21,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(328,241,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(230,164,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(229,519,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(394,86,150,20,strSJCS);      //收件城市
				break;

			case 'youshuwuliu':
				//优速物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0092.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(241,98,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(150,107,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(178,97,320,50,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(242,318,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(148,324,225,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(179,318,239,45,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(290,132,90,21,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(298,239,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(229,197,140,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(232,450,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(112,448,150,20,strSJCS);      //收件城市
				break;

			case 'suer':
				//速尔物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0101.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(109,119,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(141,120,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(169,120,400,30,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(210,109,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(241,116,225,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(299,71,414,30,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(349,81,90,21,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(349,176,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(107,255,140,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(211,295,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(272,207,150,20,strSJCS);      //收件城市
				break;

			case 'ganzhongnengda':
				//港中能达
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0150.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(230,120,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(121,126,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(164,120,320,55,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(234,392,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(124,394,225,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(161,393,229,60,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(267,137,90,21,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(265,291,40,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(231,241,140,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(235,502,150,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(123,671,150,20,strSJCS);      //收件城市
				break;
			
			case 'rufengda':
				//凡客如风达
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0245.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(111,119,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(145,138,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(184,163,300,35,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(295,119,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(331,124,269,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(371,97,198,42,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(187,353,149,19,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(165,573,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(233,99,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(416,99,134,20,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(123,671,150,20,strSJCS);      //收件城市
				break;
			case 'quanfengkuaidi':
				//全峰快递
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0060.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(111,147,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(145,138,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(176,119,300,35,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(277,149,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(308,126,269,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(339,123,198,42,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(178,468,108,23,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(98,583,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(238,113,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(399,115,134,20,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(123,671,150,20,strSJCS);      //收件城市
				break;
			case 'debangwuliu':
				//德邦物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0289.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(93,150,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(93,244,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(149,119,300,35,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(223,151,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(223,242,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(277,112,276,27,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(48,619,108,23,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(93,676,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(121,136,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(250,136,134,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'youzhengguonei':
				//邮政国内
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0079.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(355,138,140,20,strJJRXM);       //寄件人姓名
				//LODOP.ADD_PRINT_TEXT(93,244,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(309,119,270,35,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(250,132,150,20,strSJRXM);      //收件人姓名
				//LODOP.ADD_PRINT_TEXT(223,242,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(195,164,248,26,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(178,426,108,23,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(179,551,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(355,289,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(250,288,134,20,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'dhl':
				//DHL
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0172.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(155,290,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(213,154,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(248,119,300,52,strJJRDZ);    //寄件人的详细地址
				//LODOP.ADD_PRINT_TEXT(250,132,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(363,92,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(402,104,248,26,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(298,408,108,23,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(299,567,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(312,232,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(469,140,134,20,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'datianwuliu':
				//大田物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0240.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(135,131,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(160,127,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(200,119,320,52,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(136,506,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(160,510,240,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(196,444,320,32,strSJRDZ);    //收件人详细地址
				//LODOP.ADD_PRINT_TEXT(298,408,108,23,strNJPM);      //内件品名
				//LODOP.ADD_PRINT_TEXT(299,567,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(137,232,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(136,614,150,23,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'eyoubao':
				//e邮宝
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0065.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(122,131,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(160,127,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(192,76,300,52,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(124,489,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(158,448,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(193,439,272,51,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(318,74,108,23,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(321,327,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(121,291,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(123,650,150,23,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'kuaijiesudi':
				//快捷速递
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0112.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(284,287,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(138,125,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(202,76,300,69,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(283,561,65,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(138,404,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(203,355,263,68,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(265,632,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(271,709,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(283,108,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(281,374,150,23,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'longbanwuliu':
				//龙邦物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0109.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(81,136,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(144,125,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(174,96,300,49,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(245,136,65,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(308,128,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(341,92,259,58,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(107,436,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(109,549,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(111,242,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(278,238,150,23,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(177,657,150,20,strSJCS);      //收件城市
				break;
			case 'lianbangkuaidi':
				//联邦快递（国内）
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0164.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(101,146,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(150,135,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(173,124,300,49,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(238,154,65,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(288,131,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(314,123,256,48,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(399,138,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(376,144,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(124,127,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(262,126,150,23,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(177,657,150,20,strSJCS);      //收件城市
				break;
			case 'lianhaowuliu':
				//联昊通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0106.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(254,302,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(139,119,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(183,91,300,49,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(421,300,65,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(308,118,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(348,97,256,48,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(470,130,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(141,390,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(257,60,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(422,58,150,23,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(177,657,150,20,strSJCS);      //收件城市
				break;
			case 'quanyikuaidi':
				//全一快递
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0159.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(244,126,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(149,136,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(182,130,300,49,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(385,129,140,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(283,140,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(319,114,260,59,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(155,460,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(161,555,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(243,285,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(385,294,150,23,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(78,705,150,20,strSJCS);      //收件城市
				break;
			case 'quanritongkuaidi':
				//全日通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0125.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(92,360,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(91,123,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(123,119,314,51,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(210,363,65,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(210,114,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(249,118,310,67,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(122,562,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(121,484,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(177,302,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(324,301,150,24,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(78,705,150,20,strSJCS);      //收件城市
				break;	
			case 'xinbangwuliu':
				//新邦物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0156.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(103,138,140,20,strJJRXM);       //寄件人姓名
				//LODOP.ADD_PRINT_TEXT(91,123,240,20,strJJRDW);    //寄件人单位名称
				//LODOP.ADD_PRINT_TEXT(123,119,314,51,strJJRDZ);    //寄件人的详细地址
				//LODOP.ADD_PRINT_TEXT(210,363,65,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(149,145,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(196,136,290,41,strSJRDZ);    //收件人详细地址
				//LODOP.ADD_PRINT_TEXT(122,562,63,34,strNJPM);      //内件品名
				//LODOP.ADD_PRINT_TEXT(121,484,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(126,138,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(173,177,150,24,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(78,705,150,20,strSJCS);      //收件城市
				break;	
			case 'zhongtiewuliu':
				//中铁快运
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0191.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(411,140,140,20,strJJRXM);       //寄件人姓名
				//LODOP.ADD_PRINT_TEXT(91,123,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(432,132,330,22,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(491,135,140,20,strSJRXM);      //收件人姓名
				//LODOP.ADD_PRINT_TEXT(210,114,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(509,130,332,20,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(230,49,60,19,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(232,153,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(453,331,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(529,326,150,24,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(78,705,150,20,strSJCS);      //收件城市
				break;	
			case 'zhongyouwuliu':
				//中邮物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0423.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(143,156,140,20,strJJRXM);       //寄件人姓名
				//LODOP.ADD_PRINT_TEXT(91,123,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(210,75,342,63,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(108,507,140,20,strSJRXM);      //收件人姓名
				//LODOP.ADD_PRINT_TEXT(210,114,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(165,450,310,67,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(373,90,63,34,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(374,276,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(285,219,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(251,581,150,24,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(78,705,150,20,strSJCS);      //收件城市
				break;	
			case 'annengwuliu':
				//安能物流
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0424.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(113,112,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(86,112,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(137,115,300,52,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(218,115,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(193,132,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(240,110,248,26,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(300,100,200,150,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(300,260,200,150,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(113,263,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(216,265,134,20,strSJRDH);      //收件人电话
				//LODOP.ADD_PRINT_TEXT(48,743,150,20,strSJCS);      //收件城市
				break;
			case 'baishihuitong':
				//百世汇通
				LODOP.ADD_PRINT_SETUP_BKIMG("<img border='0' src='<?php echo $fromurl;?>/m/img/kuaidi/0425.jpg' />");
		  //背景图片
				LODOP.ADD_PRINT_TEXT(105,120,140,20,strJJRXM);       //寄件人姓名
				LODOP.ADD_PRINT_TEXT(135,120,240,20,strJJRDW);    //寄件人单位名称
				LODOP.ADD_PRINT_TEXT(165,150,300,52,strJJRDZ);    //寄件人的详细地址
				LODOP.ADD_PRINT_TEXT(105,500,150,20,strSJRXM);      //收件人姓名
				LODOP.ADD_PRINT_TEXT(133,500,168,20,strSJRDW);    //收件人单位名称
				LODOP.ADD_PRINT_TEXT(165,530,248,26,strSJRDZ);    //收件人详细地址
				LODOP.ADD_PRINT_TEXT(315,60,108,23,strNJPM);      //内件品名
				LODOP.ADD_PRINT_TEXT(315,180,35,20,strNJSL);     //内件数量
				LODOP.ADD_PRINT_TEXT(240,115,150,20,strJJRDH);     //寄件人电话
				LODOP.ADD_PRINT_TEXT(240,500,134,20,strSJRDH);      //收件人电话
				LODOP.ADD_PRINT_TEXT(105,650,150,20,strSJCS);      //收件城市
				break;
		}
		LODOP.SET_SHOW_MODE("BKIMG_IN_PREVIEW",1);

		LODOP.PREVIEW();
	};



</script>

<iframe name="exe_iframe" id="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe> 
</body>
</html>