	<div id="header2">	
    	<div id="logo"><a href="../m/home.php"><img src="img/logo2.jpg" alt="订货宝 订货管理系统 (DHB.HK)" title="订货宝 订货管理系统 (DHB.HK)" height="55" border="0" /></a></div>
    	<ul>
			<?php
			foreach($menu_arr as $km=>$kv)
			{
				if($km == "system" && $_SESSION['uinfo']['userflag']!="9") continue;
				//if($km == "statistics" && ($_SESSION['uinfo']['userid'] === "1"  || $_SESSION['uinfo']['userid'] === "3")) continue;
				if($menu_flag == $km)
				{ 
					echo '<li class="current">'.$kv.'</li>';
				}else{
					echo '<li ><a href="../m/'.$km.'.php">'.$kv.'</a></li>';
				}			
			}
			?>                      
        </ul>
    </div>