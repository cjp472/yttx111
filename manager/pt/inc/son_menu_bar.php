<?php
$locationurl = basename($_SERVER['SCRIPT_FILENAME']);
$rarr = array(".php",".shtml","_add","_edit");
$locationurl = str_replace($rarr,"",$locationurl);

/**
if(in_array($_SESSION['uinfo']['userid'],$kfArr)){
	$son_menu	 = array(
				"manager"			=> array(
					"company"			=> "客户管理",
					"feedback"			=> "客户反馈",	
					"use"				=> "基本数据",
					"use_order"			=> "活跃情况",															
				),
			);
}elseif(in_array($_SESSION['uinfo']['userid'],$adminArr)){
	$son_menu	 = array(
				"manager"			=> array(
					"manager"			=> "客户管理",
					"regiester"			=> "注册管理",
					"feedback"			=> "客户反馈",	
					"use"				=> "基本数据",
					"use_order"			=> "活跃情况",										
					"agent"				=> "代理商",
					"finance_log"		=> "财务管理",	
				),
			);
}elseif(in_array($_SESSION['uinfo']['userid'],$topAdminArr)){
	$son_menu	 = array(
				"manager"			=> array(
					"manager"			=> "客户管理",
					"regiester"			=> "注册管理",
					"feedback"			=> "客户反馈",	
					"analysis"			=> "数据分析",
					"use"				=> "基本数据",
					"use_order"			=> "活跃情况",
					"company_log"		=> "跟踪日志",					
					"manager_user_log"	=> "登陆日志",
					"error_log"			=> "错误日志",					
					"execution_admin_log"	=> "操作日志",
					//"industry"			=> "行业管理",
					//"area"				=> "地区管理",
					"agent"				=> "代理商",
					"finance_log"		=> "财务管理",					
				),
			);
}
**/
	$agenttypearr = array(
		'p'	=> '普通代理',
		'q'	=> '区域代理',
		'b'	=> '包销代理'
	);
	//数据库个数
	//$databasearr = array(0,1,2,3,4,5,6,7,8);
$db_cnt = $db->get_var("SELECT CompanyDatabase as Cnt FROM ".DATABASEU.DATATABLE."_order_company ORDER BY CompanyID DESC LIMIT 1");
$databasearr = range(0,(int)$db_cnt);
?>
<div class="bodyline" style="height:25px;"></div>

<div class="bodyline" style="height:32px;">
	<div class="leftdiv" style=" margin-top:8px; padding-left:12px;">
		<span><h4><?php echo $_SESSION['uc']['CompanyName'];?></h4></span>
		<span valign="bottom">&nbsp;&nbsp;</span>&nbsp;&nbsp;<span>[<a href="../m/do_login.php?m=logout">退出</a>]</span>
	</div>
    <div class="rightdiv">
       	  <span class="leftdiv"><img src="img/menu2_left.jpg" /></span>
            <span id="menu2">
			<ul>
			<?php
			if(!empty($son_menu[$menu_flag])){
				foreach($son_menu[$menu_flag] as $key=>$var)
				{
					if($key == $locationurl) $classStyle = " class =\"current2\" "; else $classStyle = "";
					echo '<li '.$classStyle.'><a href="'.$key.'.php">'.$var.'</a></li>';
				}
			} else {
                echo '<li class="current2"><a href="javascript:;" onclick="window.location.reload();">'.$menu_arr[$menu_flag].'</a></li>';
            }
			?>
			</ul>
		</span>
          <span><img src="img/menu2_right.jpg" /></span>
     </div>
</div>    
    
<div class="bodyline" id="position" style="height:70px; background-image:url(img/bodyline_bg.jpg);">
	<div class="leftdiv"><img src="img/blue_left.jpg" /></div>
	<div class="leftdiv"><h1><? echo $menu_arr[$menu_flag];?></h1></div>
	<div class="rightdiv" style="color:#ffffff; padding-right:20px; padding-top:40px;"><?php echo $son_description[$menu_flag];?></div>
</div>