<?php
     isLogin($menu_flag,$pope);

	 //isLogin
    function isLogin($mo='',$tf='')
	{
		if(empty($_SESSION['uinfo']['userid']) || empty($_SESSION['uc']['CompanyID']))
		{
			session_unset();
			session_destroy();
			$_SESSION['eMsg'] = "请先登陆！";
		 	Error::outAdmin('登陆超时或您的帐号在别的地方登陆了，请重新登陆！','../index.php');
        }		
		if($_SESSION['uinfo']['usertype']!="M") Error::GoToUrl('../s/order.php');
		if($_SESSION['uinfo']['userflag']!="9")
		{
			if($mo=="system")
			{
				Error::Back('对不起，您没有此项操作权限！');
			}else{
				if(!empty($mo) && !empty($tf))
				{
					if($_SESSION['up'][$mo][$tf] != 'Y') Error::Back('对不起，您没有此项操作权限！');
				}
			}
		}
	}

/**
 * 检查是否有权限
 * @param string $mo
 * @param string $tf
 * @param string $relation
 * @return bool
 */
    function is_allow_access($mo='',$tf='',$relation="and") {
        $result = false;
        $flag = $_SESSION['uinfo']['userflag'];
        if($flag == 9) {
            return true;
        }
        if(is_array($tf)) {
            $tmp = true;
            foreach($tf as $act) {
                if($relation == 'and' && $_SESSION['up'][$mo][$act] != 'Y') {
                    $tmp = false;
                    break;
                } else if($relation == 'or' && $_SESSION['up'][$mo][$act] == 'Y') {
                    $tmp = true;
                    break;
                }
            }
            $result = $tmp;
        } else {
            $result = $_SESSION['up'][$mo][$tf] == 'Y';
        }
        return $result;
    }
?>
