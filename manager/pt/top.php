	<div id="header2">	
    	<div id="logo"><a href="../m/home.php"><img src="img/logo2.jpg" alt="" title="" height="55" border="0" /></a></div>
    	<ul>
			<?php
			foreach($menu_arr as $km=>$kv)
			{
				if($km == "system" && $_SESSION['uinfo']['userflag']!="9") continue;
				if($menu_flag == $km)
				{ 
					echo '<li class="current">'.$kv.'</li>';
				}else{
					echo '<li ><a href="../pt/'.$km.'.php">'.$kv.'</a></li>';
				}				
			}
 			if(in_array($_SESSION['uinfo']['userid'],array(1))) echo '<li ><a href="delete.php">数据</a></li>';
			?>                      
        </ul>
    </div>