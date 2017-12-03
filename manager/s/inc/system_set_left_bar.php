<?php
/**
 * 根据 URL 地址识别当前栏目
 * */

$baseName = basename($_SERVER['SCRIPT_FILENAME']);

// style class is locationli
$urlLink = array(
				"0" => array(
					"url" => "system.php",
					"name" => "公司信息"					
				), 
				"1" => array(
					"url" => "producttype_set.php",
					"name" => "模式设置"					
				),
                /*
                "12"=> array(
                    "url"=>"erpinterface_set.php",
                    "name"=>"ERP接口设置",
                ),*/
				"2" => array(
					"url" => "productfield_set.php",
					"name" => "商品字段设置"					
				), 
				"3" => array(
					"url" => "printfield_set.php",
					"name" => "打印设置"					
				), 
				"31" => array(
					"url" => "templateset.php",
					"name" => "品牌Logo设置"					
				), 
				"4" => array(
					"url" => "pointset.php",
					"name" => "积分设置"					
				),
				"5" => array(
					"url" => "smsset.php",
					"name" => "短信通知设置"					
				),
				"6" => array(
					"url" => "typeset.php",
					"name" => "发货方式设置"					
				),
				"7" => array(
					"url" => "typeset.php#paylocation",
					"name" => "收款方式设置"					
				),
				"8" => array(
					"url" => "client_area.php",
					"name" => "药店地区设置"					
				),
				"9" => array(
					"url" => "client_level.php",
					"name" => "药店级别设置"					
				),
				"10" => array(
					"url" => "accounts.php",
					"name" => "收款账号设置"
				),
				"11" => array(
					"url" => "expense_bill.php",
					"name" => "其他款项类型设置"					
				)
			);
?>

<ul>
<?php
foreach($urlLink as $k => $link) {
    $classStyle = strpos($link['url'], $baseName) === 0 ? " class =\"locationli\" " : "";
    if ($k == 12) {
        $cnt = $db->get_var("SELECT COUNT(*) as Cnt FROM " . DATABASEU . DATATABLE . "_api_serial WHERE CompanyID= " . $_SESSION['uc']['CompanyID'] . " AND Status='T' LIMIT 1");
        if ($cnt == 0) {
            continue;
        }
    }
    echo "<li><a href='{$link['url']}' {$classStyle} >{$link['name']}</a></li>";
}
?>
</ul>