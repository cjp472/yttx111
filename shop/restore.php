<?php
include_once ('common.php');

$db         = dbconnect::dataconnect()->getdb();

//定义数据库数组
$databaseName = array(
		'db_dhb_test',
		'db_dhb_test_1',
		'db_dhb_test_2',
		'db_dhb_test_3',
		'db_dhb_test_4',
		'db_dhb_test_5',
		'db_dhb_test_6'
);


$insertSqlHead = "insert into ".DATABASEU.DATATABLE."_yjf_openapi(ClientCompany,ClientID,YapiUserId,YapiuserName,YapiUserType,CreateTime) values";

$insertData = array();

$total = 0;
foreach ($databaseName as $dbn){
	
	//处理第一个库
	$csql = "select ClientID,ClientCompany,YapiUserId,YapiuserName from ".$dbn.".".DATATABLE."_order_client where YapiUserId!=''";
	$yopenApiInfo = $db->get_results($csql);

	$length = count($yopenApiInfo);
	if(!$length) continue;	//没有数据就直接跳过
	
	echo '当前数据库：'.$dbn;
	echo '<br />';
	echo '共有数据：'.$length;
	echo '<br />';
	
	//准备写入数据
	for($j = 0; $j < $length; $j++){
		$insertData[] = "(".$yopenApiInfo[$j]['ClientCompany'].",".$yopenApiInfo[$j]['ClientID'].",'".$yopenApiInfo[$j]['YapiUserId']."','".$yopenApiInfo[$j]['YapiuserName']."','P','".date('Y-m-d H:i:s')."')";
	}
}

$insertSqlBody = $insertSqlHead . implode(",", $insertData);

echo $insertSqlBody;
exit;
//写入
$db->query($insertSqlBody);


?>