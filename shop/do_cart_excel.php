<?php

include_once ('common.php');
include_once (SITE_ROOT_PATH."/class/islogin.php");
if(!empty($_FILES)){
	
	require_once 'plugin/PHPExcel/Classes/PHPExcel.php';
	require_once 'plugin/PHPExcel/Classes/PHPExcel/IOFactory.php';
	require_once 'plugin/PHPExcel/Classes/PHPExcel/Reader/Excel5.php';
	$file=$_FILES['file'];
	$sExtension=end(explode('.',$file['name']));
	if($sExtension!="xls") exit(json_encode(array('status'=>0,'msg'=>'只能导入EXCEL文件(扩展名：xls)')));
	$filename=$file['tmp_name'];
	$objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format 
	$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
	$sheet = $objPHPExcel->getSheet(0); 
	$highestRow = $sheet->getHighestRow(); // 取得总行数 
	$highestColumn = $sheet->getHighestColumn(); // 取得总列数
	$k = 0; 
	 
	
	//循环读取excel文件,读取一条,插入一条
	//$content_arr=array();
	for($j=3;$j<=$highestRow;$j++)
	{
	 
		$a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
		$b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值
		$c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取C列的值
		$d = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();//获取D列的值
		
		if(!empty($c)){
			$sql = "select ID from ".DATATABLE."_order_content_index where Coding='".$c."' and CompanyID='".$_SESSION['cc']['ccompany']."'";
			
			$conid=$db->get_var($sql);
			$d=$d <0 ? 0 : $d ;
			if(!empty($conid)) $_SESSION['cartitems'][$conid]= isset($_SESSION['cartitems'][$conid]) ? ($_SESSION['cartitems'][$conid] + (int)$d) : (int)$d;
		}
		
		//echo $a.'-'.$b.'-'.$c.'-'.$d."<br/>";
	 
	}
	exit(json_encode(array('status'=>1,'msg'=>'导入成功！')));

}else{
	exit(json_encode(array('status'=>0,'msg'=>'导入失败！')));
}
?>