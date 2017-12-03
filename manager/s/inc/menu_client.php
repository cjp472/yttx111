<?php
$locationurl = basename($_SERVER['SCRIPT_FILENAME']);
$rarr = array(".php",".php");
$locationurl = str_replace($rarr,"",$locationurl);
$son_menu	 = array(
					"client_content"	=> "基本资料",
					"client_finance"	=> "应收款",
					"client_point"		=> "积分管理",
					"client_point_log"	=> "积分记录",
					"client_address"	=> "收货地址",
					"client_toplog"		=> "登录日志",

				);
?>
<div id="searchline" >
        	   <span id="menu2" style=" height:32px; float:left;">
            	<ul>
                  	<?php
					foreach($son_menu as $key=>$var){
						if($key == $locationurl) $classStyle = " class =\"current2\" "; else $classStyle = "";
						echo '<li '.$classStyle.'><a href="'.$key.'.php?ID='.$in['ID'].'">'.$var.'</a></li>';
					}
					?>
                </ul>
            </span>                       
</div>