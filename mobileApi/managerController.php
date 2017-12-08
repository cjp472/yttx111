 <?php
// +----------------------------------------------------------------------
// | Describe: 管理端接口控制器
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2015-05-29
// +----------------------------------------------------------------------
class managerController{

	var $fp = array('+','/','=','_');
	var $rp = array('-','|','DHB',' ');

	/**
    * 验证，获取Key
	*@param array $param(SerialNumber,Password) 用户名,密码
	*@return array $rdata(rStatus,error,sKey) 状态，提示信息，key
    *@author seekfor
    */
	public function  managerTokenValue($param){
		global $db,$log;
		//$log->logInfo('managerTokenValue', $param);
		//通过微信获取
		$loginType = 'Mobile';
		$sqltemp = '';
		if(!empty($param['openId'])){
			$wsql = "select WeiXinID,UserID,UserType,CompanyID from ".DB_DATABASEU.DATATABLE."_order_weixin where WeiXinID = '".$param['openId']."' and UserType='M' order by ID desc limit 0,1";
			$winfo = $db->get_row($wsql);
			if(!empty($winfo['CompanyID'])) $sqltemp = " and UserCompany=".$winfo['CompanyID']." ";
			//独立微信
			if(!empty($param['wid'])){
				$comnameinfo = $db->get_row ( "select CompanyName,CompanySigned,CompanyPrefix from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID=".intval($param['wid'])." limit 0,1" );
				$rdata['rData']   = $comnameinfo;
			}
			if(empty($winfo['WeiXinID'])){
								
				$rdata['rStatus'] = 101;
				$rdata['error']   = '您的微信号还没有与订货系统进行绑定，请先绑定！';
				return $rdata;
			}else{
				$sqlru = "select UserID,UserName,UserCompany as CompanyID,UserTrueName,UserFlag,UserType from ".DB_DATABASEU.DATATABLE."_order_user where UserID = ".$winfo['UserID']." ".$sqltemp." limit 0,1";
			}
			$loginType = 'WeiXin';
			if($param['weixin'] == 'qy') $loginType = 'qyWeiXin';
			
		}elseif(IS_TIYAN == 'T' && !empty($param['Userid'])){
			$sqlru = "select UserID,UserName,UserCompany as CompanyID,UserTrueName,UserFlag,UserType from ".DB_DATABASEU.DATATABLE."_order_user where UserID=".intval($param['Userid'])." and UserName = '".$param['Username']."' limit 0,1";
			$loginType = $param['loginFrom'];
		}else{
			$loginType = $param['loginFrom'];
			//通过帐号密码
			if(empty($param['Username']) || empty($param['Password'])){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '帐号密码不能为空';
				return $rdata;
			}
			
			if(!is_filename($param['Username']) || strlen($param['Username']) < 3 || strlen($param['Username']) > 30){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '请输入合法的帐号！(3-40位数字、字母和下划线)';
				return $rdata;
			}
			if(!is_filename($param['Password']) || strlen($param['Password']) < 3 || strlen($param['Password']) > 32){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '请输入合法的密码！(3-32位数字、字母和下划线)';
				return $rdata;
			}
			
			$param['Username'] = strtolower($param['Username']);
			$param['Password'] = strtolower($param['Password']);

			$psmsg = change_msg($param['Username'],$param['Password']);

			$loginwh = " UserName = '".$param['Username']."' ";

			$sqlru = "select UserID,UserName,UserCompany as CompanyID,UserTrueName,UserFlag,UserType,TokenValue from ".DB_DATABASEU.DATATABLE."_order_user where ".$loginwh." and UserPass = '".$psmsg."' ".$sqltemp." limit 0,1";			
		}
		//$rdata['sqlru'] = $sqlru;
		$ruinfo = $db->get_row($sqlru);
		if(empty($ruinfo['UserID'])){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '帐号密码不匹配';
		}elseif($ruinfo['UserFlag'] == '1'){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '您的帐号已禁用，请与管理员联系';
		}else{
			//删除缓存
			if(!empty($ruinfo['TokenValue'])) store_cache($ruinfo['TokenValue'],'');

			$tmpArr 		= $ruinfo;
			//公司表验证
			$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyLogo,CompanyFlag,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$ruinfo['CompanyID']." limit 0,1");
			$tmpArr['CompanyID'] 		= $ucinfo['CompanyID'];
			$tmpArr['CompanyName'] 		= $ucinfo['CompanyName'];
			$tmpArr['CompanySigned'] 	= $ucinfo['CompanySigned'];
			
			$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DB_DATABASEU.DATATABLE."_order_cs where CS_Company = ".$ruinfo['CompanyID']." limit 0,1");
			if($ucinfo['CompanyFlag'] == "1"){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '此帐号已锁定，暂停使用，请与订货宝联系';
			}
//             elseif(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24)){
// 				$rdata['rStatus'] = 101;
// 				$rdata['error']   = '此帐号已到期，暂停使用，请与订货宝联系';
// 			}
        else{
				if(empty($param['ip'])) $param['ip'] = RealIp();
				$token = md5 ( API_KEY.$ruinfo['UserID'].$ruinfo['UserName'].time());
				//更新token
				$db->query("update ".DB_DATABASEU.DATATABLE."_order_user set UserLoginDate=".time().",UserLoginIP='".$param['ip']."',UserLogin=UserLogin+1, TokenValue='".$token."' where UserID=".$ruinfo['UserID']);
				//日志
				$db->query("insert into ".DB_DATABASEU.DATATABLE."_order_login_user_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(".$ruinfo['CompanyID'].",".$ruinfo['UserID'].",'".$ruinfo['UserName']."','". $param['ip']."',".time().",'".$loginType."')");
				//unset($tmpArr['UserID'],$tmpArr['CompanyID']);
				
				$rdata['rStatus'] = 100;
				$rdata['error']   = '';
				$rdata['sKey']    = $token;
				$rdata['rData']   = $tmpArr;
			}
		}
		//$log->logInfo('managerTokenValue return', $rdata);
		return $rdata;
	}
	
	/**
	 * @desc 体验登录
	 * @param array $param (industry)
	 * @return array $rdata
	 */
	public function managerTiYan($param){
		global $db,$log;
		$param['ip'] = RealIp();
	
		if(empty($param['industry'])){
			$rdata['rStatus'] 	= 110;
			$rdata['error'] 	= '参数错误!';
		}else{
			
			$companyInfo = $db->get_row("select CompanyID from ".DB_DATABASEU.DATATABLE."_order_company where CompanyFlag = '0' and CompanyIndustry = ".intval($param['industry'])." and IsUse = 1 and LoginIP = '{$param['ip']}' and IsSystem=0 order by CompanyID asc limit 0,1 ");
			if(empty($companyInfo['CompanyID'])){
				$companyInfo = $db->get_row("select CompanyID from ".DB_DATABASEU.DATATABLE."_order_company where  CompanyIndustry=".intval($param['industry'])." and CompanyFlag = '0' and IsUse=0 and IsSystem=0 order by CompanyID asc limit 0,1");
			}
			
			if(!empty($companyInfo['CompanyID'])){
				$cinfo = $db->get_row ( "select UserID,UserName,UserTrueName from ".DB_DATABASEU.DATATABLE."_order_user  where UserCompany = ".$companyInfo['CompanyID']." and UserFlag='9' order by UserID asc limit 0,1" );
			}		
			
			if(empty($cinfo['UserID'])) {
				$rdata['rStatus'] = 101;
				$rdata['error']   = '行业数据找不到，请联系客服!';
			}else{
				$tmpParam['loginFrom'] = $param['loginFrom'];
				$tmpParam['Userid']   = $cinfo['UserID'];
				$tmpParam['Username'] = $cinfo['UserName'];

				$backData = self::managerTokenValue($tmpParam);
				// 标识这个公司已经使用
				if($backData['rStatus']  == 100){					
					$db->query("update ".DB_DATABASEU.DATATABLE."_order_company set IsUse = 1, LoginIP = '{$param['ip']}' where CompanyID = {$companyInfo['CompanyID']} ");	
				}
				return $backData;
			}
		}	
		return $rdata;
	}
	
	
	/**
	 * @desc 登出
	 * @param array $param (sKey)
	 * @return array $rdata
	 */
	public function managerLoginOut($param){
		global $db,$log;
	
		if(empty($param['sKey'])){
			$rdata['rStatus'] 	= 110;
			$rdata['error'] 	= '参数错误!';
		}else{
	
			$sql_l = "update ".DB_DATABASEU.DATATABLE."_order_user set TokenValue='' where TokenValue='".$param['sKey']."'  limit 1" ;
			$resultStatus	= $db->query($sql_l);
	
			if($resultStatus){
				$cache_file = buid_dir($param['sKey'],true);//子目录缓存
				if (file_exists($cache_file) ){
					unlink($cache_file);
				}
				$rdata['error']   = '执行成功';
				$rdata['rStatus'] = 100;
			}else{
				$rdata['rStatus'] = 101;
				$rdata['error']   = '执行不成功';
			}
		}
	
		return $rdata;
	}

	/**
    * 验证sKey,获取公司信息
	*@param string skey
	*@return array $rdata(rStatus,error,ClientID,CompanyID,CompanyName,CompanyDatabase) 状态，提示信息，公司ID,数据库
    *@author seekfor
    */
	protected function getCompanyInfo($param){
		global $db,$log;

		if (empty($param)){
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误!';
			return $rdata;
		}else{
			
			$rdata = get_cache($param);
			if(!empty($rdata) && $rdata['rStatus'] == '100'){
				return $rdata;
			}else{

				$cinfo = $db->get_row ( "select d.UserID,d.UserName,d.UserTrueName,d.UserFlag,d.UserType,c.CompanyID,c.CompanyName,c.CompanyPrefix,c.CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_user d inner join ".DB_DATABASEU.DATATABLE."_order_company c ON d.UserCompany=c.CompanyID where d.TokenValue='".$param."' limit 0,1" );
				if(empty($cinfo['CompanyID'] )) {
					$rdata['rStatus'] = 119;
					$rdata['error']   = '登录超时，请重新登录！';
				}else{
					if($cinfo['UserFlag']=="0" || $cinfo['UserFlag']=='2') //wangd 2017-11-30获取权限增加代理商及其客情
					{
						$pope_info = $db->get_results("SELECT pope_module,pope_view,pope_form,pope_audit FROM ".DB_DATABASEU.DATATABLE."_order_pope where pope_company=".$cinfo['CompanyID']." and pope_user=".$cinfo['UserID']." ");
						$popearr = null;
						if(!empty($pope_info))
						{
							foreach($pope_info as $pvar)
							{
								$popearr[$pvar['pope_module']] = $pvar;
							}
						}
						$cinfo['module'] = $popearr;
					}
					//业务权限
					if($cinfo['UserType']=="S")
					{
						if(empty($cinfo['CompanyDatabase'])) $sdatabase = DB_DATABASE.'.'; else $sdatabase = DB_DATABASE."_".$cinfo['CompanyDatabase'].'.';
					
						$c_info = $db->get_col("SELECT ClientID FROM ".$sdatabase.DATATABLE."_order_salerclient where CompanyID=".$cinfo['CompanyID']." and SalerID=".$cinfo['UserID']." ");
						if(!empty($c_info))
						{
							$comma_separated = implode(",", $c_info);
						}
						$cinfo['separated'] = ",".$comma_separated.",";
						
						$cinfo['module']['product']['pope_view'] = 'Y';
						$cinfo['module']['finance']['pope_view'] = 'Y';
					}	
					
					$rdata  =  $cinfo;
					$rdata['rStatus'] = 100;
					if(empty($cinfo['CompanyDatabase'])) $rdata['Database'] = DB_DATABASE.'.'; else $rdata['Database'] = DB_DATABASE."_".$cinfo['CompanyDatabase'].'.';
					
					store_cache($param,$rdata);					
				}
				return $rdata;
			}						
		}		
	}
	
	/**
	 * @desc 验证帐号权限
	 * @param $param：帐号信息,module:模块名称，动作
	 * @return array
	 */
	protected function getModulePope($param,$module){
	
		if($param['UserFlag'] == '9'){
			return true;
		}else{
			if($param['module'][$module['name']][$module['action']] == 'Y'){
				return true;
			}else{
				return false;
			}			
		}
	}

    /**
     * @desc 获取公司账号信息
     * @param $param (CompanyID)
     * @return array
     */
    protected function getCsInfo($param){
        global $db,$log;
        $rdata = array();

        if(empty($param['CompanyID'])){
            $rdata['rStatus'] = 110;
            $rdata['error']   = '参数错误';
        }else{
            $cs_sql = "SELECT CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber,CS_UpDate,CS_UpdateTime FROM ".DB_DATABASEU.DATATABLE."_order_cs WHERE CS_Company=".$param['CompanyID'];
            $csInfo = $db->get_row($cs_sql);
            if($param['debug']){
                $rdata['rSql'] = $cs_sql;
            }
            if(empty($csInfo)){
                $rdata['rStatus']	= 101;
                $rdata['error']		= '数据为空';
            }else{
                $rdata['rStatus']	= 100;
                $rdata['rData']		= $csInfo;
            }
        }
        //$log->logInfo('getCsInfo return',$rdata);
        return $rdata;
    }
    
    
    
    /**
     * 验证sKey,获取用户信息
     *@param string skey
     *@return array $rdata(rStatus,error,ClientID,CompanyID,CompanyName,CompanyDatabase) 状态，提示信息，公司ID,数据库
     *@author seekfor
     */
    public function managerUserInfo($param){
    	global $db,$log;
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		    
    			$cinfo = $db->get_row ( "select d.UserID,d.UserName,d.UserTrueName,d.UserPhone as UserJob,d.UserFlag,d.UserType,c.CompanyID,c.CompanyName,c.CompanySigned,c.CompanyPrefix,c.CompanyContact,c.CompanyMobile,c.CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_user d inner join ".DB_DATABASEU.DATATABLE."_order_company c ON d.UserCompany=c.CompanyID where d.TokenValue='".$param['sKey']."' limit 0,1" );
    	    	if(empty($cinfo)){   	    		
					$rdata['rStatus'] = 119;
					$rdata['error']   = '登录超时，请重新登录！';
    			}else{
    				//管理员
    				if($cinfo['UserFlag'] != "9")
    				{
    					$pope_info = $db->get_results("SELECT pope_module,pope_view,pope_form,pope_audit FROM ".DB_DATABASEU.DATATABLE."_order_pope where pope_company=".$cinfo['CompanyID']." and pope_user=".$cinfo['UserID']." ");
    					$popearr = null;
    					if(!empty($pope_info))
    					{
    						foreach($pope_info as $pvar)
    						{
    							$popearr[$pvar['pope_module']] = $pvar;
    						}
    					}
    					$cinfo['module'] = $popearr;
    				}
    				//业务权限
    				if($cinfo['UserType']=="S")
    				{
    					if(empty($cinfo['CompanyDatabase'])) $sdatabase = DB_DATABASE.'.'; else $sdatabase = DB_DATABASE."_".$ucinfo['CompanyDatabase'].'.';
    					 
    					$c_info = $db->get_col("SELECT ClientID FROM ".$sdatabase.DATATABLE."_order_salerclient where CompanyID=".$cinfo['CompanyID']." and SalerID=".$cinfo['UserID']." ");
    					if(!empty($c_info))
    					{
    						$comma_separated = implode(",", $c_info);
    					}
    					$cinfo['separated'] = ",".$comma_separated.",";
    					 
    					$cinfo['module']['product']['pope_module'] = 'product';
    					$cinfo['module']['product']['pope_view']   = 'Y';
    					$cinfo['module']['finance']['pope_module'] = 'finance';
    					$cinfo['module']['finance']['pope_view']   = 'Y';
    				}
    				
    				$rdata['rStatus']	= 100;
    				$rdata['rData']		= $cinfo;    				
    			}
    	}
    	return $rdata;
    }

        
    /**
     * 获取商品列表
     *@param array $param(sKey,parentId,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerGoodsSort($param){
    	global $db,$log;
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];
    			$smsg = '';
    			if(isset($param['parentId'])) $smsg .= " and ParentID=".intval($param['parentId'])." ";
    			 
    			$sql_l  = "select SiteID,ParentID,SiteName from ".$sdatabase.DATATABLE."_order_site where  CompanyID=".$cidarr['CompanyID']." ".$smsg." order by ParentID asc, SiteOrder desc, SiteID asc ";
    			$result	= $db->get_results($sql_l);  
    			if(empty($result)){
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '无符合条件数据';
    			}else{
    				$rdata['rStatus']	= 100;
    				$rdata['rTotal'] 	= count($result);
    				$rdata['rData']		= $result;    				
    			}		
    		}
    	}
    	return $rdata;
    } 
    
    
    /**
     * 获取商地区列表
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerArea($param){
    	global $db,$log;
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];
    			$smsg = '';
    			if(isset($param['parentId'])) $smsg .= " and AreaParentID = ".intval($param['parentId'])." ";
    
    			$sql_l  = "select AreaID,AreaParentID as ParentID,AreaName,AreaPinyi from ".$sdatabase.DATATABLE."_order_area where AreaCompany=".$cid." ".$smsg." order by AreaParentID asc,AreaID asc ";
    			$result	= $db->get_results($sql_l);
    			if(empty($result)){
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '无符合条件数据';
    			}else{
    				$rdata['rStatus']	= 100;
    				$rdata['rTotal'] 	= count($result);
    				$rdata['rData']		= $result;    
    			}
    		}
    	}
    	//$log->logInfo('getGoodsSort return', $rdata);
    	return $rdata;
    }
    
    /**
     * 获取收款帐号
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerAccounts($param){
    	global $db,$log;
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];
    			$smsg = '';
    
    			$sql_l  = "select AccountsID,AccountsBank,AccountsNO,AccountsName from ".$sdatabase.DATATABLE."_order_accounts where AccountsCompany=".$cid." order by AccountsID asc ";
    			$result	= $db->get_results($sql_l);
    			//$rdata['rsql'] = $sql_l;
    			if(empty($result)){
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '无符合条件数据';
    			}else{
    				$rdata['rStatus']	= 100;
    				$rdata['rTotal'] 	= count($result);
    				$rdata['rData']		= $result;
    
    			}
    		}
    	}
    	//$log->logInfo('getGoodsSort return', $rdata);
    	return $rdata;
    }
    
    
    /**
     * 获取客户等级
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerLevel($param){
    	global $db,$log;
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];
    			$smsg = '';
    			$result = self::getSystemSet('clientlevel',$cidarr);
    			if(empty($result)){
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '无符合条件数据';
    			}else{
    				$rdata['rStatus']	= 100;
    				$rdata['rData']		= $result;
    
    			}
    		}
    	}
    	//$log->logInfo('getGoodsSort return', $rdata);
    	return $rdata;
    }
    
    /**
     * 获取商品品牌
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerGoodsBrand($param){
    	global $db,$log;
    	//$log->logInfo('getGoodsBrand', $param);
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];
    			$smsg = '';
				$sql_l  = "SELECT BrandID,BrandNO,BrandName,BrandPinYin FROM ".$sdatabase.DATATABLE."_order_brand b where CompanyID=".$cid." ";
				if(!empty($param['siteId'])){
    				$sortinfo = $db->get_row("SELECT SiteNO FROM ".$sdatabase.DATATABLE."_order_site where CompanyID=".$cid." and SiteID=".intval($param['siteId'])." limit 0,1");
    				$smsg  = " and exists (select BrandID from ".$sdatabase.DATATABLE."_view_index_site where  BrandID=b.BrandID AND CompanyID=".$cid." and SiteNO like '".$sortinfo['SiteNO']."%' AND BrandID <>0) ";
    			}
    			$sql_l = $sql_l.$smsg." order by BrandID ASC";
    			$result	= $db->get_results($sql_l);
    			//$rdata['rsql'] = $sql_l;
    			if(empty($result)){
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '没有符合条件的数据';
    			}else{
    				$rdata['rStatus']	= 100;
    				$rdata['rTotal'] 	= count($result);
    				$rdata['rData']		= $result;
    
    			}    
    		}
    	}
    	return $rdata;
    }    
    
    /**
     * 获取商品列表
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerGoodsList($param){
    	global $db,$log;   
    	include (SITE_ROOT_PATH."/arr_data.php");    	

    	if (empty ( $param['sKey'] )){
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database

    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			//权限验证
    			$module = array('name'=>'product','action'=>'pope_view');
    			$isallow = self::getModulePope($cidarr,$module);
    			if(!$isallow){
    				$rdata['rStatus'] = 110;
    				$rdata['error']   = '对不起，您没有此项操作权限！';
    				return $rdata;
    			}
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];

    			$smsg = $orderbymsg = '';
    			
    			if(empty($param['orderBy']) || $param['orderBy'] == "0"){
    				$orderbymsg = " order by OrderID DESC, ID DESC";
    			}elseif($param['orderBy'] == "1"){
    				$orderbymsg = " order by Price2 DESC";
    			}elseif($param['orderBy'] == "2"){
    				$orderbymsg = " order by Price2 ASC";
    			}elseif($param['orderBy'] == "3"){
    				$orderbymsg = " order by ID ASC";
    			}elseif($param['orderBy'] == "4"){
    				$orderbymsg = " order by Count DESC, ID DESC";
    			}    			
    			
    			if(isset($param['flagId']) && $param['flagId'] !== "") $smsg .= " and FlagID = ".intval($param['flagId'])." ";
    			if(!empty($param['commendId'])){
    				$smsg .= " and CommendID = ".intval($param['commendId'])." ";	
    			}
    			
    			if(!empty($param['brandId'])) $smsg .= " and BrandID = ".intval($param['brandId'])." ";
    			
    			$kwn = str_replace(' ','%',$param['kw']);
    			if(strpos($kwn,'%')){
    			    $temsql = '';
    			    $kwnarr = explode('%',$kwn);
    			    foreach($kwnarr as $v){
    			        if(!empty($temsql)){
    			            $temsql .= " AND ";
    			        }
    			        $temsql .= " Name like '%".$v."%' ";
    			    }
    			    $smsg  .= " AND ((".$temsql.") OR (Pinyi like '%".$kwn."%' OR Coding like '%".$kwn."%' OR Barcode like '%".$kwn."%' OR Model like '%".$kwn."%'))";
    			}else{
    			    $smsg  .= " AND (Name like '%".$kwn."%' OR Coding like '%".$kwn."%' OR Barcode like '%".$kwn."%' OR Pinyi like '%".$kwn."%' OR Model like '%".$kwn."%') ";
    			}
    			
    			if(!empty($param['siteId'])) $smsg .= self::getShieldSite($param['siteId'], $cidarr); //屏蔽分类、商品


				//wangd 2017-11-30 判断是否为代理商或者其客情，代理商只能看到自己相关的订单
				$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DB_DATABASEU.DATATABLE."_order_user where UserID=".$cidarr['UserID']."");
				if($type['UserType']=='M' && $type['UserFlag']==2)    $smsg .=" AND AgentID= ".$cidarr['UserID']." ";
				if($type['UserType']=='S' && $type['UserFlag']==2)    $smsg .=" AND AgentID= ".$type['UpperID']." ";	

    			$sql_c = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_content_index 
    							where CompanyID=".$cid." ".$smsg;
    			$sql  = "select ID,CommendID,Count,SiteID,BrandID,Name,Coding,Barcode,Price1,Price2,Units,Casing,Picture,Color,Specification,FlagID
    							from ".$sdatabase.DATATABLE."_order_content_index 
    							where CompanyID=".$cid." ".$smsg." ".$orderbymsg;	
   			   			
    			$sql .= " limit ".$param['begin'].",".intval($param['step']);
    			
    			
    			//查询出品牌
    			$brSql = "select BrandName,BrandID from ".$sdatabase.DATATABLE."_order_brand where CompanyID=".$cid;
    			$brand = $db->get_results($brSql);
    			$length = count($brand);
    			$brandInfo = array();
    			for($i = 0; $i < $length; $i++){
    			    $brandInfo[$brand[$i]['BrandID']] = $brand[$i]['BrandName'];
    			}
    			unset($brand);

    			$countData = $db->get_row($sql_c);
    			$listData  = $db->get_results ( $sql );
    			//$rdata['rSql'] = $sql;
    			
    			$setproduct = self::getSystemSet('product',$cidarr);
    			$rdata['price1Name'] = $setproduct['product_price']['price1_name'] ? $setproduct['product_price']['price1_name'] : "价格一";
    			$rdata['price2Name'] = $setproduct['product_price']['price2_name'] ? $setproduct['product_price']['price2_name'] : "价格二";
    			
    			if(empty($countData)) {
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '没有符合条件的数据';
    			}else{
    			    for($i=0;$i<count($listData);$i++){
    			    	$listData[$i]['Coding'] = html_entity_decode($listData[$i]['Coding'], ENT_QUOTES,'UTF-8');
						$listData[$i]['CommendName'] = $producttypearr[$listData[$i]['CommendID']];
						$listData[$i]['BrandName']   = $brandInfo[$listData[$i]['BrandID']];
						$listData[$i]['FlagName']    = $listData[$i]['FlagID']=='1'?'下架' : '上架';
						
						$listData[$i]['Name'] = htmlspecialchars_decode($listData[$i]['Name']);
						$listData[$i]['Name'] = str_replace(array('<', '>'), array('＜', '＞'), $listData[$i]['Name']);
						
						if(!empty($listData[$i]['Picture']) && $cid == "1" && substr($listData[$i]['Picture'],0,1)!="1") $listData[$i]['Picture'] = "1/".$listData[$i]['Picture'];
						if(!empty($listData[$i]['Picture'])) $listData[$i]['Picture'] = RESOURCE_PATH.$listData[$i]['Picture'];
						$idarr[] = $listData[$i]['ID'];						
					}
					
					//wangd 2017-11-30 代理商只能看到自己部分的商品情况
					if($type['UserType']=='M' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$cidarr['UserID']." ";
					if($type['UserType']=='S' && $type['UserFlag']==2)    $sqlmsg .=" AND AgentID= ".$type['UpperID']." ";	
					$sql_a = "select count(*) as allrow,FlagID from ".$sdatabase.DATATABLE."_order_content_index
    							where CompanyID=".$cid.$sqlmsg." group by FlagID";

					$countAudit  = $db->get_results ( $sql_a );

					foreach ($countAudit as $cv){
						if($cv['FlagID'] == '1'){
							$rdata['countUnAudit'] 	= $cv['allrow'];
						}else{
							$rdata['countAudit'] 	= $cv['allrow'];
						}
					}
					
    				$rdata['rStatus']		= 100;
    				$rdata['rAllTotal'] 	= $countData['allrow'];
    				$rdata['rTotal'] 		= count($listData);
    				$rdata['rData']			= $listData;
    			}
    		}
    	}
    	//$log->logInfo('getGoodsList return', $rdata);
    	return $rdata;
    }

    //商品子分类
    protected function getShieldSite($siteid,$cidarr)
    {
    	global $db,$log;
    	$smsg = '';
    	$sidsqlmsg = '';
        
    	if(!empty($siteid))
    	{
    		$sortinfo = $db->get_row("SELECT SiteNO FROM ".$cidarr['Database'].DATATABLE."_order_site where CompanyID=".$cidarr['CompanyID']." and SiteID=".$siteid." limit 0,1");
    		$sortarr = $db->get_col("select SiteID from ".$cidarr['Database'].DATATABLE."_order_site where CompanyID=".$cidarr['CompanyID']." and SiteNO like '".$sortinfo['SiteNO']."%'");
    		if(!empty($sortarr)){
    			$smsg = implode(',',$sortarr);
    			$smsg .= ','.$siteid;
    			$sidsqlmsg = " and SiteID IN('".$smsg."') ";
    		}    		
    	}

    	return $sidsqlmsg;
    }
    
    /**
     * 获取商品明细
     *@param array $param(sKey,contentId) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function managerGoodsContent($param){
    	global $db,$log;
    	include (SITE_ROOT_PATH."/arr_data.php");
    
    	if (empty ( $param['sKey'] ) || empty($param['contentId']))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			//权限验证
    			if($cidarr['UserType'] != 'S'){
    				$module = array('name'=>'product','action'=>'pope_view');
    				$isallow = self::getModulePope($cidarr,$module);
    				if(!$isallow){
    					$rdata['rStatus'] = 110;
    					$rdata['error']   = '对不起，您没有此项操作权限！';
    					return $rdata;
    				}
    			}    			
    			
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];

    			$id 		= intval($param['contentId']);    			 
    			$smsg = '';     			

    			$setproduct = self::getSystemSet('product',$cidarr);
    			$rdata['price1Name'] = $setproduct['product_price']['price1_name'] ? $setproduct['product_price']['price1_name'] : "价格一";
    			$rdata['price2Name'] = $setproduct['product_price']['price2_name'] ? $setproduct['product_price']['price2_name'] : "价格二";
    			
    			if(!empty($shielddata['crow']))
    			{
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '没有符合条件的数据';
    			}else{
    				
    				$sql  = "select ID,CommendID,BrandID,Name,Coding,Barcode,Units,Casing,Picture,Color,Specification,Model,Price1,Price2,FlagID from ".$sdatabase.DATATABLE."_order_content_index where ID=".$id." and CompanyID=".$cid."  ";
    				$sql .= " limit 1";
    				$result['index']	 = $db->get_row($sql);	
    				
    				//查询出品牌
    				$brSql = "select BrandName,BrandID from ".$sdatabase.DATATABLE."_order_brand where CompanyID=".$cid." and BrandID=".$result['index']['BrandID'];
    				$brand = $db->get_row($brSql);
    				
    				$result['index']['BrandName'] = $brand['BrandName'];
    				
    				//$rdata['rSql'] = $sql;
    				if(empty($result['index'])){
    					$rdata['rStatus'] = 101;
    					$rdata['error']   = '没有符合条件的数据';
    					return $rdata;				
    				}else{
    				    $result['index']['Name'] = htmlspecialchars_decode($result['index']['Name']);
    				    $result['index']['Name'] = str_replace(array('<', '>'), array('＜', '＞'), $result['index']['Name']);

    					$sql_c   = "select Content,Package,FieldContent,ContentCreateDate,ContentEditDate from ".$sdatabase.DATATABLE."_order_content_1 where ContentIndexID = ".$result['index']['ID']." and CompanyID=".$cid." limit 0,1";
    					$result['content'] = $db->get_row($sql_c);
    					
    					if(!empty($result['content']['Content'])){
    						$result['content']['Content'] = html_entity_decode($result['content']['Content'], ENT_QUOTES,'UTF-8');
    						$result['content']['Content'] = _striptext($result['content']['Content']); //格式化内容
    						$result['content']['Content'] = htmlentities($result['content']['Content'], ENT_QUOTES,'UTF-8');
    					}
    					
    					if(!empty($result['index']['Picture'])){
    						$result['index']['Picture'] 	= RESOURCE_PATH.$result['index']['Picture'];
    						//$result['index']['PictureBig'] 	= str_replace("thumb_","img_",$result['index']['Picture']);
    					}
    					
    					if(!empty($result['content']['FieldContent'])){
    						
    						$farr = unserialize($result['content']['FieldContent']);
    						$setfield = self::getSystemSet('field',$cidarr);

    						$result['content']['FieldContent'] = '';
    						if(!empty($setfield)){
    							foreach($setfield as $key=>$var){
    								if(!empty($farr[$key])){
    									$result['content']['FieldContent'][$key]['name']  = $var['name'];
    									$result['content']['FieldContent'][$key]['value'] = $farr[$key];    									
    								}
    							}
    						}
    					}
    					
    					//商品的创建时间和修改时间 by wanjun
    					if(empty($result['content']['ContentCreateDate']) && $result['content']['ContentEditDate']){
    						$result['content']['ContentCreateDate'] = $result['content']['ContentEditDate'];
    						$sql_up = "update ".$sdatabase.DATATABLE."_order_content_1 set ContentCreateDate=".$result['content']['ContentEditDate']." where ContentIndexID=".$result['index']['ID']." and CompanyID=".$cid." limit 1";
							$db->query($sql_up);
    					}
    						
    					$result['content']['productTime']['ContentCreateDate']	= $result['content']['ContentCreateDate'] ? date('Y-m-d H:i', $result['content']['ContentCreateDate']) : '';
    					$result['content']['productTime']['ContentEditDate']	= $result['content']['ContentEditDate'] ? date('Y-m-d H:i', $result['content']['ContentEditDate']) : '';
    					
    					//end 商品的创建时间和修改时间
    					
    					$sql_r = "select Name,Path from ".$sdatabase.DATATABLE."_order_resource where CompanyID=".$cid." and IndexID = ".$result['index']['ID']." order by OrderID asc";
    					$pdata = $db->get_results($sql_r);
    					if(!empty($pdata)){    						
    						foreach($pdata as $v){
    							$result['content']['PicArray'][] = RESOURCE_PATH.$v['Path']."img_".$v['Name'];
    						}
    						//$result['index']['PictureBig'] = RESOURCE_PATH.$pdata[0]['Path']."img_".$pdata[0]['Name'];
    					}
  					
    					if(!empty($result['content'])) $contentData = array_merge($result['index'], $result['content']); else $contentData = $result['index'];
    					   					
    					$rdata['rStatus'] = 100;
    					$rdata['error']   = '';
    					$rdata['rData']	  = $contentData;
    				}
    			}
       			     
    			if(empty($result)) {
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '没有符合条件的数据';
    			}
    		}
    	}
    	//$log->logInfo('getGoodsContent return', $rdata);
    	return $rdata;
    }  

    //设置
	protected function getSystemSet($ty='field',$cidarr)
	{	
		global $db,$log; 

		$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='".$ty."' limit 0,1";
		$result	= $db->get_row($sql_l);
		if(!empty($result['SetValue'])) $valuearr = unserialize($result['SetValue']);
		if(!empty($valuearr)) $typemsg = $valuearr; else $typemsg = null;

		//$db->debug();
		return $typemsg;
		unset($result);
	}

    
	/**
    * 获取订单列表
	*@param array $param(sKey,begin,step) key,起始值，步长
	*@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
    *@author seekfor
    */
	public function managerOrderList($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");
		//$log->logInfo('getOrderList', $param);

		if (empty ( $param['sKey'] )){
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误';
		}else{
			$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
			if($cidarr['rStatus'] != 100){
				return $cidarr;
			}else{
				//权限验证
				$module  = array('name'=>'order','action'=>'pope_view');
				$isallow = self::getModulePope($cidarr,$module);
				if(!$isallow){
					$rdata['rStatus'] = 110;
					$rdata['error']   = '对不起，您没有此项操作权限！';
					return $rdata;
				}
				$cid		= $cidarr['CompanyID'];
				$sdatabase  = $cidarr['Database'];
				$smg = $csmg = '';
				$valuearr = self::getSystemSet('product',$cidarr);
				
				//业务员数据范围
				if($cidarr['UserType'] == 'S'){
					$smg .= " and instr('".$cidarr['separated']."', CONCAT(',', o.OrderUserID, ',')) > 0";
					$csmg = " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',')) > 0";	
				}
				
				if(!empty($param['clientId'])) $smg .= " and o.OrderUserID = ".intval($param['clientId'])." ";
				if(isset($param['orderStatus'])  && $param['orderStatus'] !== '' ) $smg .= " and o.OrderStatus = ".intval($param['orderStatus'])." ";
				if(isset($param['orderSendStatus']) && $param['orderSendStatus'] !== '') $smg .= " and o.OrderSendStatus = ".intval($param['orderSendStatus'])." ";				
				if(isset($param['orderPayStatus'])  && $param['orderPayStatus'] !== '') $smg .= " and o.OrderPayStatus = ".intval($param['orderPayStatus'])." ";
				
				if(!empty($param['beginDate']))  $smg .= ' and o.OrderDate > '.strtotime($param['beginDate'].'00:00:00').' ';
				if(!empty($param['endDate']))    $smg .= ' and o.OrderDate < '.strtotime($param['endDate'].'23:59:59').' ';
				
				if(!empty($param['kw']))   $smg .= " and (o.OrderSN like '%".$param['kw']."%' or c.ClientCompanyName like '%".$param['kw']."%' or c.ClientTrueName like '%".$param['kw']."%' ) ";
				
				//wangd 2017-11-30 判断是否为代理商或者其客情，代理商只能看到自己相关的订单
				if($cidarr['UserFlag']==2)
				{
					$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DB_DATABASEU.DATATABLE."_order_user where UserID=".$cidarr['UserID']."");
					if($type['UserType']=='M')    $smg .=" AND AgentID= ".$cidarr['UserID']." ";
					if($type['UserType']=='S')    $smg .=" AND AgentID= ".$type['UpperID']." ";	
					$countData  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo o 
									left join ".$sdatabase.DATATABLE."_order_client c ON o.OrderUserID=c.ClientID  
									left join ".DATATABLE."_view_index_cart t ON o.OrderID=t.OrderID 
									where o.OrderCompany=".$cidarr['CompanyID']." ".$smg." ");
					
					$sql  = "select o.OrderID,o.OrderSN,o.OrderSendType,o.OrderSendStatus,o.OrderPayType,o.OrderPayStatus,o.OrderRemark,o.OrderTotal,o.OrderStatus,o.OrderDate,o.OrderSaler,c.ClientCompanyName,c.ClientTrueName
							from ".$sdatabase.DATATABLE."_order_orderinfo o 
							left join ".$sdatabase.DATATABLE."_order_client c ON o.OrderUserID = c.ClientID 
							left join ".DATATABLE."_view_index_cart t ON o.OrderID=t.OrderID
							where o.OrderCompany=".$cidarr['CompanyID']." ".$smg." ";	

				}
				else
				{
					$countData  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo o 
									left join ".$sdatabase.DATATABLE."_order_client c 
											ON o.OrderUserID=c.ClientID  
									where o.OrderCompany=".$cidarr['CompanyID']." ".$smg." ");
									
					$sql  = "select o.OrderID,o.OrderSN,o.OrderSendType,o.OrderSendStatus,o.OrderPayType,o.OrderPayStatus,o.OrderRemark,o.OrderTotal,o.OrderStatus,o.OrderDate,o.OrderSaler,c.ClientCompanyName,c.ClientTrueName
							from ".$sdatabase.DATATABLE."_order_orderinfo o 
									left join ".$sdatabase.DATATABLE."_order_client c 
											ON o.OrderUserID = c.ClientID 
											where o.OrderCompany=".$cidarr['CompanyID']." ".$smg." ";					
				}

				$sql .= " order by o.OrderID desc ";
				$sql .= " limit ".$param['begin'].",".intval($param['step']);
				
				$oinfo  = $db->get_results ( $sql );

				$smgc = " and OrderStatus = 0 ";
				//wangd 2017-11-30 判断是否为代理商或者其客情，代理商只能看到自己相关的订单
				if($cidarr['UserFlag']==2)
				{
					$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DB_DATABASEU.DATATABLE."_order_user where UserID=".$cidarr['UserID']."");
					if($type['UserType']=='M')    $smgc .=" AND t.AgentID= ".$cidarr['UserID']." ";
					if($type['UserType']=='S')    $smgc .=" AND t.AgentID= ".$type['UpperID']." ";	
					$countAudit  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo o
						left join ".DATATABLE."_view_index_cart t ON o.OrderID=t.OrderID 
						where OrderCompany=".$cidarr['CompanyID']." ".$smgc." ".$csmg." ");
				}
				else
				{
					$countAudit  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo 
						where OrderCompany=".$cidarr['CompanyID']." ".$smgc." ".$csmg." ");
				}

				$smgc = " and OrderPayStatus = 0 ";
				//wangd 2017-11-30 判断是否为代理商或者其客情，代理商只能看到自己相关的订单
				if($cidarr['UserFlag']==2)
				{
					$type = $db->get_row("SELECT UserType,UserFlag,UpperID FROM ".DB_DATABASEU.DATATABLE."_order_user where UserID=".$cidarr['UserID']."");
					if($type['UserType']=='M')    $smgc .=" AND t.AgentID= ".$cidarr['UserID']." ";
					if($type['UserType']=='S')    $smgc .=" AND t.AgentID= ".$type['UpperID']." ";	
					$countPay  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo o
						left join ".DATATABLE."_view_index_cart t ON o.OrderID=t.OrderID 
						where OrderCompany=".$cidarr['CompanyID']." ".$smgc." ".$csmg." ");	
				}
				else
				{
					$countPay  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo
							where OrderCompany=".$cidarr['CompanyID']." ".$smgc." ".$csmg." ");					
				}
				
				if(empty($oinfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '没有符合条件的数据';
				}else{
                    foreach($oinfo as $key=>$val){
						$oinfo[$key]['OrderSendStatusName'] = $send_status_arr[$val['OrderSendStatus']];
						$oinfo[$key]['OrderPayStatusName']  = $pay_status_arr[$val['OrderPayStatus']];
                        $oinfo[$key]['OrderStatusName']		= $order_status_arr[$val['OrderStatus']];
                        
                        if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on")
                        {
                        	if($val['OrderSaler']=="T") $oinfo[$key]['OrderSalerStatusName'] = '已初审'; else $oinfo[$key]['OrderSalerStatusName'] = '待初审';
                        }else{
                			$oinfo[$key]['OrderSalerStatusName'] = '';
                		}

						$oinfo[$key]['OrderSendType']	= $senttypearr[$val['OrderSendType']];
                        $oinfo[$key]['OrderPayType']	= $paytypearr[$val['OrderPayType']];
                    }
					$rdata['rStatus']	= 100;
					$rdata['rAllTotal'] = $countData['allrow'];
                    $rdata['rTotal']	= count($oinfo);
                    $rdata['countAudit'] = $countAudit['allrow'];
                    $rdata['countPay']	 = $countPay['allrow'];
					$rdata['rData']		 = $oinfo;
				}
			}
		}
		//$log->logInfo('getOrderList return', $rdata);
		return $rdata;
	}


	/**
    * 获取订单明细
	*@param array $param(sKey,orderId) key,订单号
	*@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
    *@author seekfor
    */
	public function managerOrderContent($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");
		//$log->logInfo('getOrderContent', $param);

		if (empty ( $param['sKey'] ) || empty($param['orderId']))
		{
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误';
		}else{
			$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database

			//获取供应商主数据,过期提醒
			$cInfo  = $this->getCsInfo($cidarr);
		    $timsgu = (strtotime($cInfo['rData']['CS_EndDate'])+60*60*24);
        	if(time() > $timsgu){
        	    $rdata['rStatus'] = 110;
				$rdata['error']   = '您的账户已到期，请在电脑端升级！';
				return $rdata;
        	}//end if
			
			if($cidarr['rStatus'] != "100"){
				return $cidarr;
			}else{
				//权限验证
				$module = array('name'=>'order','action'=>'pope_view');
				$isallow = self::getModulePope($cidarr,$module);
				if(!$isallow){
					$rdata['rStatus'] = 110;
					$rdata['error']   = '您没有此功能权限！';
					return $rdata;
				}				
				
				$cid    = $cidarr['CompanyID'];
				$sdatabase = $cidarr['Database'];
				
				$valuearr = self::getSystemSet('product',$cidarr);
				
				//业务员数据范围
				if($cidarr['UserType'] == 'S'){
					$smg = " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',')) > 0";	
				}
				
				//取单头
				$sql    = "select OrderID,OrderSN,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,InvoiceType,InvoiceTax,DeliveryDate,OrderRemark,OrderTotal,OrderIntegral,OrderStatus,OrderDate,OrderType,OrderSaler,OrderFrom from ".$sdatabase.DATATABLE."_order_orderinfo  where  OrderID=".intval($param['orderId'])." and OrderCompany=".$cid." ".$smg." limit 0,1";
				$oinfo  = $db->get_row ( $sql );
				if(empty($oinfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '数据为空';
				}else{
				
					$oinfo['OrderSendStatusName'] 	= $send_status_arr[$oinfo['OrderSendStatus']];
					$oinfo['OrderPayStatusName']  	= $pay_status_arr[$oinfo['OrderPayStatus']];
                    $oinfo['OrderStatusName']		= $order_status_arr[$oinfo['OrderStatus']];

					$oinfo['OrderSendType']		= $senttypearr[$oinfo['OrderSendType']];
                    $oinfo['OrderPayType']		= $paytypearr[$oinfo['OrderPayType']];                    
                    
                    $oinfo['InvoiceType']		= $invoicetypearr[$oinfo['InvoiceType']];
                    $oinfo['OrderType']			= $oinfo['OrderType']=='C'?'客户':'管理员';
                    $oinfo['OrderFromName']     = $order_from[$oinfo['OrderFrom']];
                    if(empty($oinfo['OrderFromName'])) $oinfo['OrderFromName']	= '电脑';

					//取明细
					$sqlc   = "select Name,Coding,Units,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent,'c' as conType from ".$sdatabase.DATATABLE."_view_index_cart where OrderID=".$oinfo['OrderID']." and CompanyID=".$cid." ";
					$cinfo  = $db->get_results ( $sqlc );
					$infoall = $cinfo;
                	$sqlg   = "select Name,Coding,Units,ContentColor,ContentSpecification,ContentPrice,ContentNumber,'g' as conType from ".$sdatabase.DATATABLE."_view_index_gifts where OrderID=".$oinfo['OrderID']." and CompanyID=".$cid." ";
                	$ginfo  = $db->get_results($sqlg);
                	//$infoall = $ginfo;
                	$submitLog = $db->get_results("select ID,Name,Date,Status,Content from ".$sdatabase.DATATABLE."_order_ordersubmit where CompanyID=".$cid." and OrderID=".$oinfo['OrderID']." ");
                	if(!empty($ginfo)){
                		for($i=0;$i<count($ginfo);$i++){
                			$infoall[] = $ginfo[$i];
                		}	
                	}
                
                	$datasql   = "SELECT c.ConsignmentNO,c.ConsignmentMan,c.ConsignmentDate,c.ConsignmentFlag,c.InputDate,l.LogisticsName FROM ".$sdatabase.DATATABLE."_order_consignment c left join ".$sdatabase.DATATABLE."_order_logistics l ON c.ConsignmentLogistics=l.LogisticsID where c.ConsignmentOrder='".$oinfo['OrderSN']."' and c.ConsignmentCompany = ".$cid." Order by c.ConsignmentID Desc";
                	$coninfo = $db->get_results($datasql);  
                	for($i=0;$i<count($coninfo);$i++){
                		$coninfo[$i]['ConsignmentFlagName'] = $incept_arr[$coninfo[$i]['ConsignmentFlag']];
                	}  
                	
                	if(!empty($valuearr['audit_type']) && $valuearr['audit_type']=="on")
                	{
                		if($oinfo['OrderSaler']=="T") $oinfo['OrderSalerStatusName'] = '已初审'; else $oinfo['OrderSalerStatusName'] = '待初审';
                	}else{
                		$oinfo['OrderSalerStatusName'] = '';
                	}
                
					$rdata['rStatus'] = 100;
                    $rdata['rTotal']  = count($infoall);
					$rdata['rData']['header']   = $oinfo;
					$rdata['rData']['body']   	= $infoall;// $cinfo;
					$rdata['rData']['consignment']   = $coninfo;
					$rdata['rData']['log']   	= $submitLog;
				}
			}
		}

		return $rdata;
	}

	
	
    
    /**
     * @desc 款项
     * @param array $param (sKey,body)
     * @return array $rdata
     */
    public function managerFinanceList($param){
        global $db,$log;
        include (SITE_ROOT_PATH."/arr_data.php");

        if(empty($param['sKey'])){
            $rdata['rStatus'] = 110;
            $rdata['error'] = '参数错误!';
        }else{
            $cidarr = self::getCompanyInfo($param['sKey']);
            if($cidarr['rStatus'] != 100){
                return $cidarr;
            }
            //权限验证
            $module = array('name'=>'finance','action'=>'pope_view');
            $isallow = self::getModulePope($cidarr,$module);
            if(!$isallow){
            	$rdata['rStatus'] = 110;
            	$rdata['error']   = '对不起，您没有此项操作权限！';
            	return $rdata;
            }
            
            $cid = $cidarr['CompanyID'];
            $sdatabase = $cidarr['Database'];

            $smg = $smgc = '';
            //业务员数据范围 
            if($cidarr['UserType'] == 'S'){
            	$smg .= " and instr('".$cidarr['separated']."', CONCAT(',', f.FinanceClient, ',') ) > 0";
            	$smgc = " and instr('".$cidarr['separated']."', CONCAT(',', FinanceClient, ',')) > 0";
            }            
            if(!empty($param['accountsId']))  $smg .= " and f.FinanceAccounts = ".intval($param['accountsId'])." ";      
            if(isset($param['flagId']) && $param['flagId'] !== ''){
            	$smg .= " and f.FinanceFlag = ".intval($param['flagId'])." ";
            }
            if(!empty($param['beginDate']))  $smg .= ' and f.FinanceDate > '.strtotime($param['beginDate'].'00:00:00').' ';
            if(!empty($param['endDate']))    $smg .= ' and f.FinanceDate < '.strtotime($param['endDate'].'23:59:59').' ';
            
            if(!empty($param['kw']))   $smg .= " and (f.FinanceID like '%".$param['kw']."%' or c.ClientCompanyName like '%".$param['kw']."%' or c.ClientTrueName like '%".$param['kw']."%' ) ";
            
            $sql_c 	= "select count(*) as allrow FROM ".$sdatabase.DATATABLE."_order_finance f
                    left join ".$sdatabase.DATATABLE."_order_client c
                    ON f.FinanceClient=c.ClientID where f.FinanceCompany=".$cid." ".$smg." ";
            $countData  = $db->get_row($sql_c);
            
            $sql  = "SELECT f.FinanceID,f.FinanceOrder,f.FinanceTotal,f.FinanceToDate,f.FinanceUpDate,f.FinanceDate,f.FinanceFlag,c.ClientCompanyName,c.ClientTrueName
                    FROM ".$sdatabase.DATATABLE."_order_finance f
                    left join ".$sdatabase.DATATABLE."_order_client c
                    ON f.FinanceClient=c.ClientID
                    WHERE f.FinanceCompany=".$cid." ".$smg." order by f.FinanceID desc ";
            $sql .= " limit ".$param['begin'].",".intval($param['step']);
            $list = $db->get_results($sql);

            $smsgc  = " and FinanceFlag = 0";
            $sql_c 	= "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_finance where FinanceCompany=".$cid." ".$smsgc." ".$smgc." ";
            $countAudit  = $db->get_row($sql_c);            

            $smsgc  = " and FinanceDate > ".strtotime($param['beginDate'].'00:00:00')." and FinanceDate < ".strtotime($param['beginDate'].'23:59:59')."";
            $sql_c 	= "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_finance where FinanceCompany=".$cid." ".$smsgc." ".$smgc." ";
            $countToday  = $db->get_row($sql_c);
            
            if(empty($list)){
                $rdata['rStatus'] 	= 101;
                $rdata['error'] 	= '没有符合条件的数据';
            }else{
            	foreach($list as $key=>$val){
            		$list[$key]['FinanceFlagName'] = $finance_arr[$val['FinanceFlag']];
            	}
            	$rdata['rStatus'] 	 = 100;
            	$rdata['rAllTotal']  = $countData['allrow'];
                $rdata['rTotal'] 	 = count($list);
                $rdata['countAudit'] = $countAudit['allrow'];            
                $rdata['countToday'] = $countToday['allrow'];
                $rdata['rData']  	 = $list;
            }
        }

        return $rdata;
    }

    /**
     * @desc 获取款项详细
     * @param array $param (sKey,financeID)
     * @return array $rdata
     */
    public function managerFinanceContent($param){
        global $db,$log;
        $rdata = array('rStatus'=>100);
        include (SITE_ROOT_PATH."/arr_data.php");
        
        if(empty($param['sKey'])){
            $rdata['rStatus'] 	= 110;
            $rdata['error'] 	= '参数错误!';
        }elseif(empty($param['financeId'])){
            $rdata['rStatus'] = 101;
            $rdata['error'] = '收款单号不能为空!';
        }else{
            $cidarr = self::getCompanyInfo($param['sKey']);
            if($cidarr['rStatus'] != 100){
                return $cidarr;
            }
            //权限验证
            $module = array('name'=>'finance','action'=>'pope_view');
            $isallow = self::getModulePope($cidarr,$module);
            if(!$isallow){
            	$rdata['rStatus'] = 110;
            	$rdata['error']   = '对不起，您没有此项操作权限！';
            	return $rdata;
            }
            
            $cid = $cidarr['CompanyID'];
            $sdatabase = $cidarr['Database'];
            //业务员数据范围
            if($cidarr['UserType'] == 'S'){
            	$smg = " and instr('".$cidarr['separated']."', CONCAT(',', f.FinanceClient, ',')) > 0";
            }
            $sql = "SELECT f.FinanceID,f.FinanceClient,f.FinanceOrder,f.FinanceTotal,f.FinanceAbout,f.FinanceToDate,f.FinanceUpDate,f.FinanceDate,f.FinanceFlag,f.FinanceType
                    ,a.AccountsBank,a.AccountsNO,a.AccountsName
                    FROM ".$sdatabase.DATATABLE."_order_finance AS f LEFT JOIN ".$sdatabase.DATATABLE."_order_accounts AS a ON a.AccountsID = f.FinanceAccounts 
                    WHERE f.FinanceID=".intval($param['financeId'])." and f.FinanceCompany=".$cid." ".$smg ." LIMIT 0,1 ";
            $single = $db->get_row($sql);

            if($single){
            	$sql	= "SELECT ClientName,ClientCompanyName,ClientTrueName FROM ".$sdatabase.DATATABLE."_order_client where ClientCompany = ".$cid." and ClientID = ".$single['FinanceClient']." ORDER BY ClientID DESC limit 0,1";
            	$clientInfo = $db->get_row($sql);
            	$single['ClientCompanyName'] = $clientInfo['ClientCompanyName'];
            	$single['ClientTrueName'] = $clientInfo['ClientTrueName'];
            	$single['FinanceFlagName'] = $finance_arr[$single['FinanceFlag']];
            	$ft = $single['FinanceType'];
            	if($single['FinanceType'] == 'O') $single['FinanceType'] = '在线支付'; elseif($single['FinanceType'] == 'Y') $single['FinanceType'] = '银行转帐'; else $single['FinanceType'] = '银行转帐';
            	$rdata['rData']   = $single;
            	$rdata['rStatus'] = 100;
            }else{
                $rdata['rStatus'] 	= 101;
                $rdata['error'] 	= '数据不存在!';
            }
        }

        return $rdata;
    }



    /**
     * @desc 获取客户
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerClientList($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'client','action'=>'pope_view');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
			
    		$sqlmsg = $smgc = '';

    		if(isset($param['flagId']) && $param['flagId'] !== '') $sqlmsg .= " and c.ClientFlag=".intval($param['flagId'])." "; else  $sqlmsg .= " and (c.ClientFlag=0 OR c.ClientFlag=9)";
    		
    		if(!empty($param['areaId'])){
    			$sqlmsg .= "and (c.ClientArea in (SELECT AreaID FROM ".$sdatabase.DATATABLE."_order_area where AreaCompany=".$cid." and (AreaParentID=".$param['areaId']." OR AreaID=".$param['areaId'].") ORDER BY AreaID ASC ) or c.ClientArea=".$param['areaId'].") ";
    		}
    		if(!empty($param['levelId'])){
    			$sqlmsg .= " and FIND_IN_SET('".$param['lid']."',c.ClientLevel) ";
    		}
    		if(!empty($param['kw']))  $sqlmsg .= " and CONCAT(c.ClientName, c.ClientCompanyName, c.ClientCompanyPinyi, c.ClientTrueName, c.ClientMobile) like '%".$param['kw']."%' ";
    		  
    		//业务员权限范围
    		if($cidarr['UserType'] == 'S'){
    			$sql_c 	= "SELECT count(*) AS allrow FROM ".$sdatabase.DATATABLE."_order_client c inner join ".$sdatabase.DATATABLE."_order_salerclient s ON c.ClientID = s.ClientID where c.ClientCompany = ".$cid." and s.SalerID=".$cidarr['UserID']."  ".$sqlmsg."";
    			$sql	= "SELECT c.ClientID,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientMobile,c.ClientAdd,c.ClientFlag FROM ".$sdatabase.DATATABLE."_order_client c inner join ".$sdatabase.DATATABLE."_order_salerclient s ON c.ClientID = s.ClientID where c.ClientCompany = ".$cid." and s.SalerID=".$cidarr['UserID']." ".$sqlmsg."  ORDER BY c.ClientID DESC ";
    			$sql_a 	= "SELECT count(*) AS allrow FROM ".$sdatabase.DATATABLE."_order_client c inner join ".$sdatabase.DATATABLE."_order_salerclient s ON c.ClientID = s.ClientID where c.ClientCompany = ".$cid." and c.ClientFlag=9 and s.SalerID=".$cidarr['UserID']."  ";
    		}else{
    			$sql_c 	= "SELECT count(*) AS allrow FROM ".$sdatabase.DATATABLE."_order_client c where c.ClientCompany = ".$cid." ".$sqlmsg."";
    			$sql	= "SELECT c.ClientID,c.ClientName,c.ClientCompanyName,c.ClientTrueName,c.ClientMobile,c.ClientAdd,c.ClientFlag FROM ".$sdatabase.DATATABLE."_order_client c  where c.ClientCompany = ".$cid." ".$sqlmsg."  ORDER BY c.ClientID DESC ";
    			$sql_a 	= "SELECT count(*) AS allrow FROM ".$sdatabase.DATATABLE."_order_client c where c.ClientCompany = ".$cid." and c.ClientFlag=9 ";
    		}
    		
			$sql   .= " limit ".$param['begin'].",".intval($param['step']);
			$countData  = $db->get_row($sql_c);
			$result   = $db->get_results ( $sql );
			
			$countAudit  = $db->get_row($sql_a);
			$rdata['sql'] = $sql;
			
    		if($result){ 			
    			$rdata['rAllTotal']  = $countData['allrow'];
    			$rdata['rTotal']     = count($result);
    			$rdata['countAudit'] = $countAudit['allrow'];
    			$rdata['rData']   = $result;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '没有符合条件的数据!';
    		}
    	}
    
    	return $rdata;
    }   

    
    /**
     * @desc 获取客户明细
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerClientContent($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['clientId'])){
    			$rdata['rStatus'] = 101;
    			$rdata['error'] = '请指定您要查看信息!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'client','action'=>'pope_view');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		//业务员权限范围
    		if($cidarr['UserType'] == 'S'){
    			$c_info = $db->get_row("SELECT count(*) as allrow FROM ".$sdatabase.DATATABLE."_order_salerclient where CompanyID=".$cid." and ClientID=".$param['clientId']." ");
    			if($c_info['allrow'] <= 0)
    			{
    				$rdata['rStatus'] = 110;
    				$rdata['error']   = '对不起，您没有此项操作权限！';
    				return $rdata;
    			}	
    		}
    
			$result = $db->get_row("SELECT ClientID,ClientArea,ClientName,ClientCompanyName,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientFlag,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber FROM ".$sdatabase.DATATABLE."_order_client where ClientCompany=".$cid." and ClientID=".intval($param['clientId'])." limit 0,1");

    		if($result){    			
    			//$dealersinfo = $db->get_row("SELECT ClientID,ClientName,ClientPassword,ClientMobile,LoginIP,LoginDate,LoginCount FROM ".DB_DATABASEU.DATATABLE."_order_dealers  where ClientCompany=".$cid." and ClientID=".intval($param['clientId'])." limit 0,1");
    			$dealersinfo = $db->get_row("SELECT ClientID,ClientName,ClientPassword,LoginIP,LoginDate,LoginCount FROM ".DB_DATABASEU.DATATABLE."_order_dealers  where ClientCompany=".$cid." and ClientID=".intval($param['clientId'])." limit 0,1");
    			$contentData = array_merge($result, $dealersinfo);
    			
    			$sortarrname = $db->get_row("SELECT AreaID,AreaParentID,AreaName,AreaAbout FROM ".$sdatabase.DATATABLE."_order_area where AreaCompany=".$cid." and AreaID=".$result['ClientArea']." ORDER BY AreaID ASC limit 0,1");
    			$contentData['AreaName'] = $sortarrname['AreaName'];
    			unset($contentData['ClientArea']);
    			//begin tubo修改，密码改为*传给前端 2015-11-04
    			$contentData['ClientPassword'] = '******';
    			//end tubo
    			$rdata['rData']   = $contentData;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }
    
    /**
     * @desc 获取首页统计信息
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerHome($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = $sqlf = '';
    		
    		//业务员数据范围
    		if($cidarr['UserType'] == 'S'){
    			$smsg = " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',')) > 0";
    			$sqlf = " and instr('".$cidarr['separated']."', CONCAT(',', FinanceClient, ',')) > 0";

    		}
    		
    		//今天
    		$day  = date("Y-m-d");
    		$sqlunion = " and OrderStatus <> 8 and  OrderStatus <> 9 and FROM_UNIXTIME(OrderDate) between '".$day." 00:00:00' and '".$day." 23:59:59' ";
    		$daysql   = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as totalmoney,count(*) as totalnumber from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$smsg." ".$sqlunion." ";
    		$dayOrder = $db->get_row($daysql);
    		
    		$sqlunion = " and FROM_UNIXTIME(FinanceDate) between '".date("Y-m-d")." 00:00:00' and '".date("Y-m-d")." 23:59:59' ";
    		$dayFinanceSql  = "SELECT sum(FinanceTotal) as Ftotal from ".$sdatabase.DATATABLE."_order_finance where FinanceCompany=".$cid." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ".$sqlf." ".$sqlunion." ";
    		$dayFinance = $db->get_row($dayFinanceSql);

    		//昨天
    		$yestoday = date("Y-m-d",strtotime("-1 day"));
    		$sqlunion = " and OrderStatus <> 8 and  OrderStatus <> 9 and FROM_UNIXTIME(OrderDate) between '".$yestoday." 00:00:00' and '".$yestoday." 23:59:59' ";
    		$yestodaysql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as totalmoney,count(*) as totalnumber from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$smsg." ".$sqlunion." ";
    		$yestodayOrder = $db->get_row($yestodaysql);
    		
    		//本月
    		$month = date('Y-m');
    		$sqlunion  = " and OrderStatus <> 8 and  OrderStatus <> 9 and DATE_FORMAT(FROM_UNIXTIME(OrderDate),'%Y-%m') =  '".$month."'";
    		$monthsql  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as totalmoney,count(*) as totalnumber from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$smsg." ".$sqlunion." ";
    		$monthOrder = $db->get_row($monthsql);
    		
    		$sql0 = "SELECT count(*) as orow FROM ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$smsg."  and OrderStatus=0 limit 0,1";
    		$auditOrder = $db->get_row($sql0);
			if($dayOrder){
				$rdata['rData']['today']['orderTotal']   = $dayOrder['totalmoney']? $dayOrder['totalmoney']:0;
				$rdata['rData']['today']['orderNumber']  = $dayOrder['totalnumber']? $dayOrder['totalnumber'] : 0;
				$rdata['rData']['today']['financeTotal'] = $dayFinance['Ftotal'] ? $dayFinance['Ftotal']: 0;								
				$rdata['rData']['yestoday']['orderTotal']  = $yestodayOrder['totalmoney']? $yestodayOrder['totalmoney']:0;
				$rdata['rData']['yestoday']['orderNumber'] = $yestodayOrder['totalnumber']? $yestodayOrder['totalnumber'] : 0;
				$rdata['rData']['month']['orderTotal']  = $monthOrder['totalmoney']? $monthOrder['totalmoney']:0;
				$rdata['rData']['month']['orderNumber'] = $monthOrder['totalnumber']? $monthOrder['totalnumber']: 0;
				
				$rdata['rData']['auditOrder'] = $auditOrder['orow']? $auditOrder['orow']: 0;
				
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }
       
    
    /**
     * @desc 商品销售量统计
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerGoodsCount($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		
    		//业务员数据范围
    		if($cidarr['UserType'] == 'S'){
    			$smsg = " and instr('".$cidarr['separated']."', CONCAT(',', o.OrderUserID, ',')) > 0";	
    		}
    		
    		if(empty($param['beginDate'])) $param['beginDate'] = date('Y-m').'-01';
    		if(empty($param['endDate'])) $param['endDate']     = date("Y-m-d");
    		

    		$sql_c = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_content_index i inner join 
    				(select c.ContentID from ".$sdatabase.DATATABLE."_order_cart c inner join ".$sdatabase.DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID 
    						where c.CompanyID=".$cid." ".$smsg."
    						and FROM_UNIXTIME(o.OrderDate) between '".$param['beginDate']." 00:00:00' and '".$param['endDate']." 23:59:59' 
    								and o.OrderStatus<>8 and o.OrderStatus<>9 
    								group by c.ContentID) as cart ON i.ID=cart.ContentID where i.CompanyID=".$cid."
    				";
    		$countData = $db->get_row($sql_c);
    		//$rdata['sql'] = $sql_c;
    		//订购商品
    		$sql  = "SELECT c.ContentID,c.ContentName,sum(c.ContentNumber * c.ContentPrice * c.ContentPercent / 10) as OrderTotal,sum(c.ContentNumber) as OrderNumber 
    				 from ".$sdatabase.DATATABLE."_order_cart c inner join ".$sdatabase.DATATABLE."_order_orderinfo o on c.OrderID=o.OrderID 
    						where c.CompanyID=".$cid." ".$smsg."
    						and FROM_UNIXTIME(o.OrderDate) between '".$param['beginDate']." 00:00:00' and '".$param['endDate']." 23:59:59' 
    								and o.OrderStatus<>8 and o.OrderStatus<>9 
    								group by c.ContentID order by OrderNumber desc ";
    		$sql .= " limit ".$param['begin'].",".intval($param['step']);
    		$result = $db->get_results($sql);
			//$rdata['sql'] = $sql;
    		if($result){
    			$rdata['rAllTotal']  = $countData['allrow'];
    			$rdata['rData']   = $result;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }
    
    /**
     * @desc 客户订单统计
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerClientCount($param){
    	global $db,$log;

    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    
    		if(empty($param['beginDate'])) $param['beginDate'] = date('Y-m').'-01';
    		if(empty($param['endDate'])) $param['endDate']     = date("Y-m-d");
    		
    		//业务员权限范围
    		if($cidarr['UserType'] == 'S'){
    			$smsg .= " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',') ) > 0";
    		}

    		$sql_c  = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_client c 
    				   inner join (select OrderUserID from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$smsg."
                                and FROM_UNIXTIME(OrderDate) between '".$param['beginDate']." 00:00:00' and '".$param['endDate']." 23:59:59'
                                and OrderStatus!=8 and OrderStatus!=9  group by OrderUserID) as o ON o.OrderUserID = c.ClientID
                                where c.ClientCompany=".$cid." and c.ClientFlag = 0 ";
    		$countData = $db->get_row($sql_c);
    		
    		$sql  = "SELECT c.ClientID,c.ClientCompanyName,c.ClientTrueName,c.ClientMobile,sum(OrderTotal) as CountTotal,count(*) as CountNumber from ".$sdatabase.DATATABLE."_order_orderinfo o
    				 inner join ".$sdatabase.DATATABLE."_order_client c ON o.OrderUserID = c.ClientID and c.ClientFlag = 0 
                                where c.ClientCompany=".$cid." and o.OrderCompany=".$cid." ".$smsg."
                                and FROM_UNIXTIME(o.OrderDate) between '".$param['beginDate']." 00:00:00' and '".$param['endDate']." 23:59:59'
                                and o.OrderStatus!=8 and o.OrderStatus!=9  group by o.OrderUserID ORDER By countTotal DESC ";
    		$sql .= " limit ".$param['begin'].",".intval($param['step']);
    		$result = $db->get_results($sql);
    		//$rdata['sql'] = $sql_c; 
    		if($result){
    			$rdata['rAllTotal']  = $countData['allrow'];
    			$rdata['rData']   = $result;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    } 
    
    
    /**
     * @desc 月度销量统计
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerMonthCount($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		//业务员权限范围
    		if($cidarr['UserType'] == 'S'){
    			$smsg .= " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',') ) > 0";
    		}
    		if(empty($param['year'])) $param['year'] = date('Y');
    
    		$sql  = "SELECT left(OrderSN,6) as ODate,sum(OrderTotal) as CountTotal,count(*) as CountNumber 
    				from ".$sdatabase.DATATABLE."_order_orderinfo 
    						where OrderCompany=".$cid." ".$smsg." and YEAR(FROM_UNIXTIME(OrderDate))=".$param['year']." and OrderStatus <> 8 and  OrderStatus <> 9
    								group by left(OrderSN,6)";
    		$sql .= " limit ".$param['begin'].",".intval($param['step']);
    		$result = $db->get_results($sql);
    		//$rdata['sql'] = $sql;
    		if($result){
    			$rdata['rData']   = $result;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }
    
    /**
     * @desc 按日销量统计
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerDayCount($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		//业务员权限范围
    		if($cidarr['UserType'] == 'S'){
    			$smsg .= " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',') ) > 0";
    		}
    		
    		if(empty($param['month'])) $param['month'] = date('Y-m');
    
    		$sql  = "SELECT left(OrderSN,8) as ODate,sum(OrderTotal) as CountTotal,count(*) as CountNumber
    				from ".$sdatabase.DATATABLE."_order_orderinfo
    						where OrderCompany=".$cid." ".$smsg." and DATE_FORMAT(FROM_UNIXTIME(OrderDate),'%Y-%m')='".$param['month']."' and OrderStatus <> 8 and  OrderStatus <> 9
    								group by left(OrderSN,8)";
    		$sql .= " limit ".$param['begin'].",".intval($param['step']);
    		$result = $db->get_results($sql);
    		//$rdata['sql'] = $sql;
    		if($result){
    			$rdata['rData']   = $result;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }
    
    /**
     * @desc 应收款
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerFinanceClient($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		//业务员权限范围
    		if($cidarr['UserType'] == 'S'){
    			$smsg .= " and instr('".$cidarr['separated']."', CONCAT(',', ClientID, ',')) > 0";
    			$smsf .= " and instr('".$cidarr['separated']."', CONCAT(',', FinanceClient, ',')) > 0";
    			$smse .= " and instr('".$cidarr['separated']."', CONCAT(',', ClientID, ',')) > 0";
    			$smsr .= " and instr('".$cidarr['separated']."', CONCAT(',', ReturnClient, ',')) > 0";
    			$smso .= " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',')) > 0";

    		}
    		
    		if(!empty($param['kw'])){
    			$param['kw'] = str_replace(' ', '%', $param['kw']);
    			$sqlkw = " and concat(ClientCompanyName,ClientTrueName) like '%".$param['kw']."%' ";
    		}else{
    			$sqlkw = '';
    		}
    		
    		$sql_c  = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_client where ClientCompany=".$cid." ".$smsg." and ClientFlag=0 ".$smsg." ";
    		$countData = $db->get_row($sql_c);
    		
    		$sql  = "select ClientID,ClientCompanyName,ClientTrueName,ClientMobile from ".$sdatabase.DATATABLE."_order_client where ClientCompany=".$cid." and ClientFlag=0 ".$smsg." ".$sqlkw." order by ClientID asc";
    		$sql .= " limit ".$param['begin'].",".intval($param['step']);
    		$result = $db->get_results($sql);    		
    		
    		$statsql2  = "SELECT sum(FinanceTotal) as Ftotal,FinanceClient from ".$sdatabase.DATATABLE."_order_finance where FinanceCompany=".$cid." ".$smsf." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') group by FinanceClient  ";
    		$statdata2 = $db->get_results($statsql2);
    		foreach($statdata2 as $v)
    		{
    			$rdata['finance'][$v['FinanceClient']] = $v['Ftotal'];
    		}
    		
    		$statsql4 = "SELECT sum(ExpenseTotal) as Ftotal,ClientID from ".$sdatabase.DATATABLE."_order_expense where CompanyID=".$cid." ".$smse." and FlagID = '2' group by ClientID ";
    		$statdata4 = $db->get_results($statsql4);
    		foreach($statdata4 as $v)
    		{
    			$rdata['expense'][$v['ClientID']] = $v['Ftotal'];
    		}
    		
    		$statsqlt  = "SELECT sum(OrderTotal) as Ftotal,OrderUserID from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$smso." and OrderStatus!=0 and OrderStatus!=8 and OrderStatus!=9 group by OrderUserID ";
    		$statdatat = $db->get_results($statsqlt);    		
    		if(!empty($statdatat)){
    			foreach($statdatat as $v)
    			{
    				$rdata['order'][$v['OrderUserID']] = $v['Ftotal'];
    			}
    		}
    		
    		$statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal,ReturnClient from ".$sdatabase.DATATABLE."_order_returninfo where ReturnCompany=".$cid." ".$smsr."  and (ReturnStatus=2 or ReturnStatus=3 or ReturnStatus=5) group by ReturnClient";
    		$statdata1 = $db->get_results($statsqlt1);
    		foreach($statdata1 as $v)
    		{
    			$rdata['return'][$v['ReturnClient']] = $v['Ftotal'];
    		}
    		
    		$alltotal = 0;
    		if($result){
    			foreach($result as $key=>$var){
    				$returnArr[$key] = $var;
    				$tall = floatval($rdata['order'][$var['ClientID']]) - floatval($rdata['return'][$var['ClientID']]) - floatval($rdata['expense'][$var['ClientID']]) - floatval($rdata['finance'][$var['ClientID']]);
    				$alltotal = $alltotal + $tall;
    				$returnArr[$key]['Total'] = number_format($tall,2,'.',',');
    			}
    			unset($rdata);
    			$rdata['rAllTotal']   = $countData['allrow'];
    			$rdata['rCountTotal'] = $alltotal;
    			$rdata['rData']   = $returnArr;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }

    /** 以下部分为数据操作部分 **/
    
    
    
    
    /**
     * @desc 订单操作
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSetOrder($param){
    	global $db,$log;
    	$log->logInfo('setOrder', $param);
    	
    	if(empty($param['sKey']) || empty($param['orderId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'order','action'=>'pope_audit');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];    		
    		$oid = intval($param['orderId']);
    		
    		$productSet = self::getSystemSet('product',$cidarr);
    		$loinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderTotal,OrderSaler,OrderPayStatus FROM ".$sdatabase.DATATABLE."_order_orderinfo where OrderID = ".$oid." and OrderCompany=".$cid." limit 0,1");
    		
    		 
			if($param['action'] == 'cancel'){
				$sql_l  = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderStatus=9 where OrderID=".$oid." and OrderCompany = ".$cid." and OrderStatus=0 ";
				$sqlin 	= "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['UserName']."', '".$cidarr['UserTrueName']."',".time().", '管理员取消订单', '".$param['content']."')";

				$resultStatus	= $db->query($sql_l);
				if($resultStatus){
					$inStatus	= $db->query($sqlin);
                    $cidarr['ClientID'] = $loinfo['OrderUserID'];
					self::cancelOrder($param,$cidarr);					
				}
			}elseif($param['action'] == 'audit'){
				
				//未付款不能通过审核
				if($loinfo['OrderPayStatus'] != '2'){
					$rdata['rStatus'] = 101;
					$rdata['error']   = '当前订单未支付完成，不能进行审核操作!';
					return $rdata;
				}
				
				//业务员权限范围
				if($cidarr['UserType'] == 'S'){
					$smsg = " and instr('".$cidarr['separated']."', CONCAT(',', OrderUserID, ',')) > 0";	
				}
				if(!empty($productSet['audit_type']) && $productSet['audit_type']=="on"){
					if($cidarr['UserType'] == 'M' && $loinfo['OrderSaler'] == "F"){
						$rdata['rStatus'] = 101;
						$rdata['error']   = '待业务员初审';
						return $rdata;
					}
					if($cidarr['UserType'] == 'S' && $loinfo['OrderSaler'] == "F"){
						$upsql = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderSaler='T' where OrderID = ".$oid." and OrderCompany=".$cid." and OrderStatus=0 and OrderSaler='F' ";
						$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['UserName']."', '".$cidarr['UserTrueName']."',".time().", '初审订单', '".$param['content']."')";
					}else{
						$upsql = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderStatus=1,OrderSendStatus=1 where OrderID = ".$oid." and OrderCompany=".$cid." and OrderStatus=0 ";
						$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['UserName']."', '".$cidarr['UserTrueName']."',".time().", '审核订单', '".$param['content']."')";
					}
				}else{
					$upsql = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderStatus=1,OrderSendStatus=1 where OrderID = ".$oid." and OrderCompany=".$cid." and OrderStatus=0 ";
					$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['UserName']."', '".$cidarr['UserTrueName']."',".time().", '审核订单', '".$param['content']."')";
				}

				if($db->query($upsql)){
					$db->query($sqlin);				
					
					self::changePoint($cidarr,$param,$loinfo,"jia");				
					$inStatus = true;
				}else{
    				$inStatus = false;
				}
								
			}elseif($param['action'] == 'unaudit'){
				if($cidarr['UserType'] == 'S'){
					$rdata['rStatus'] = 101;
					$rdata['error']   = '您没有此项操作的权限！';
					return $rdata;
				}
				$upsql = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderStatus=0,OrderSendStatus=0 where OrderID = ".$oid." and OrderCompany=".$cid." and OrderStatus=1 ";
				if($db->query($upsql))
				{
					$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['UserName']."', '".$cidarr['UserTrueName']."',".time().", '反审核', '".$param['content']."')";
					$db->query($sqlin);
				
					$loinfo = $db->get_row("SELECT OrderID,OrderSN,OrderUserID,OrderTotal FROM ".$sdatabase.DATATABLE."_order_orderinfo where OrderID = ".$oid." and OrderCompany=".$cid." limit 0,1");
					self::changePoint($cidarr,$param,$loinfo,"jian");
				
					$inStatus = true;
				}else{
					$inStatus = false;
				}
					
			}else{
				$sqlin 	= "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$$cidarr['UserName']."', '".$cidarr['UserTrueName']."',".time().", '留言', '".$param['content']."')";	
				$inStatus	= $db->query($sqlin);
			}			
    		
    		if($inStatus){
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    }   
  
    
    /**
     * @desc 取消订单库存还原
     * @param array $param (sKey)，array $cidarr
     * @return array $rdata
     */
    protected function cancelOrder($param,$cidarr){
    	global $db,$log;
    	$sdatabase 	= $cidarr['Database'];
    	$oid = intval($param['orderId']);
    	
    	$valuearr = self::getSystemSet('product',$cidarr);
    	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
    	{
    		$sql     = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".$sdatabase.DATATABLE."_order_cart where OrderID=".$oid." and CompanyID=".$cidarr['CompanyID']." and ClientID = ".$cidarr['ClientID']." ";
    		$data_c = $db->get_results($sql);
    		$tykey = str_replace($this->fp,$this->rp,base64_encode("统一"));
    		foreach($data_c as $dvar)
    		{
    			$db->query("update ".$sdatabase.DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$cidarr['CompanyID']." and ContentID=".$dvar['ContentID']." limit 1");

    			if(strlen($dvar['ContentColor']) || strlen($dvar['ContentSpecification']))
    			{
    				if(!strlen($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($this->fp,$this->rp,base64_encode($dvar['ContentColor']));
    				if(!strlen($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($this->fp,$this->rp,base64_encode($dvar['ContentSpecification']));
    				$db->query("update ".$sdatabase.DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$cidarr['CompanyID']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");

    			}
    			$dnumber = intval("-".$dvar['ContentNumber']);
    			$db->query("insert into ".$sdatabase.DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$cidarr['CompanyID']},{$dvar['ContentID']},{$oid},{$dnumber},'cancel')");

    		}
    	}

    }
    
    /**
     * @desc 积分处理
     * @param array $param (sKey)，array $cidarr
     * @return array $rdata
     */
    protected function changePoint($cidarr,$param,$ot,$ty)
    {
    	global $db,$log;
    	$sdatabase 	= $cidarr['Database'];

    	$pointarr = self::getSystemSet('point',$cidarr);
    
    	if(!empty($pointarr) && $pointarr['pointtype'] != "1" )
    	{
    		if(!empty($ty) && $ty=="jian")
    		{
    			$db->query("delete from ".$sdatabase.DATATABLE."_order_point where PointCompany=".$cidarr['CompanyID']." and PointOrder='".$ot['OrderSN']."' ");
    		}else{
    			$pointv = 0;
    			if($pointarr['pointtype'] == "2")
    			{
    				if(empty($pointarr['pointpencent'])) $pointarr['pointpencent'] = 1;
    				$pointv = abs(intval($ot['OrderTotal'] * $pointarr['pointpencent']));
    			}else{
    				$sql    = "select c.ContentID,c.ContentNumber,i.ContentPoint from ".$sdatabase.DATATABLE."_order_cart c left join ".$sdatabase.DATATABLE."_order_content_1 i on c.ContentID=i.ContentIndexID where c.OrderID=".$ot['OrderID']." and c.CompanyID=".$cidarr['CompanyID']." and i.CompanyID= ".$cidarr['CompanyID']." and i.ContentPoint !=0 ";
    				$data_c = $db->get_results($sql);
    				foreach($data_c as $cv)
    				{
    					$pointv = $pointv + ($cv['ContentNumber'] * $cv['ContentPoint']);
    				}
    			}
    			$pointv = intval($pointv);
    			if(!empty($pointv)) $db->query("insert into ".$sdatabase.DATATABLE."_order_point(PointCompany,PointClient,PointOrder,PointValue,PointDate,PointUser) value(".$cidarr['CompanyID'].", ".$ot['OrderUserID'].", '".$ot['OrderSN']."', ".$pointv.", ".time().", ".$cidarr['UserID'].")");
    		}
    	}
    }
    
    /**
     * @desc 款项确认
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSetFinance($param){
    	global $db,$log;
    	$log->logInfo('managerSetFinance', $param);
    
    	if(empty($param['sKey']) || empty($param['financeId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'finance','action'=>'pope_audit');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    
    		$fid = intval($param['financeId']);    		 
    		if($param['action'] == 'confirm'){
    			$sql_l   = "update ".$sdatabase.DATATABLE."_order_finance set FinanceUpDate=".time().",FinanceAdmin='".$cidarr['UserName']."', FinanceFlag=2 where FinanceID=".$fid." and FinanceCompany=".$cidarr['CompanyID']." and FinanceFlag=0";
    			$resultStatus	= $db->query($sql_l);

    			if($resultStatus){
    				self::confirmIncept($param,$cidarr);
    			}
    		}
    
    		if($resultStatus){
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    }  

    
    /**
     * @desc 确认收款相关操作
     * @param array $param (sKey)，array $cidarr
     * @return array $rdata
     */
    protected function confirmIncept($param,$cidarr){
    	global $db,$log;
    	$sdatabase 	= $cidarr['Database'];
    	$fid 		= intval($param['financeId']);

    	$cinfo = $db->get_row("SELECT FinanceID,FinanceClient,FinanceOrder,FinanceTotal,FinanceToDate FROM ".$sdatabase.DATATABLE."_order_finance where FinanceID=".$fid." and FinanceCompany = ".$cidarr['CompanyID']." limit 0,1");
		if(!empty($cinfo['FinanceOrder']))
		{
			$ordersn_arr = explode(",", $cinfo['FinanceOrder']);
			$smmsg  = " '".str_replace(",","','",$cinfo['FinanceOrder'])."' ";
			$sqlarr = " and OrderSN IN (".$smmsg.") ";
			$sql_l  = "select OrderID,OrderSN,OrderTotal,OrderIntegral,OrderStatus from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cidarr['CompanyID']." ".$sqlarr." order by OrderID asc ";
			$olist  =  $db->get_results($sql_l);

			if(!empty($olist))
			{
				$chatotal = $cinfo['FinanceTotal'];
				foreach($olist as $osv)
				{
					if(!empty($osv['OrderTotal']))
					{
						$chatotal = $chatotal - $osv['OrderTotal'] + $osv['OrderIntegral'];				
						if($chatotal >= 0)
						{
							$upsql = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderPayStatus=2, OrderIntegral='".$osv['OrderTotal']."' where OrderID = '".$osv['OrderID']."' and OrderCompany=".$cidarr['CompanyID']." limit 1";
							$isup  = $db->query($upsql);
						}else{
							$uptotal = $chatotal + $osv['OrderTotal'];
							$upsql = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderPayStatus=3, OrderIntegral='".$uptotal."' where OrderID = '".$osv['OrderID']."' and OrderCompany=".$cidarr['CompanyID']." limit 1";
							$isup  = $db->query($upsql);
							break;
						}
					}
					$lastosv = $osv['OrderSN'];
				}				
			}
		}   	 
    }
    
    /**
     * @desc 设置商品
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSetGoods($param){
    	global $db,$log;
    
    	if(empty($param['sKey']) || empty($param['contentId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'product','action'=>'pope_audit');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    
    		$param['contentId'] = intval($param['contentId']);
    		if($param['action'] == 'shelve'){
    			$sql_l = "update ".$sdatabase.DATATABLE."_order_content_index set FlagID=0 where ID=".$param['contentId']." and CompanyID = ".$cid."";    				
    		}elseif($param['action'] == 'unshelve'){
    			$sql_l = "update ".$sdatabase.DATATABLE."_order_content_index set FlagID=1 where ID=".$param['contentId']." and CompanyID = ".$cid.""; 
    		}
    		$resultStatus	= $db->query($sql_l);
    
    		if($resultStatus){
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    }  

    /**
     * @desc 审核客户
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSetClient($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['clientId'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '请指定药店!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'client','action'=>'pope_audit');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		   		
    		$deInsql = '';
    		if($param['action'] == 'audit'){
    			$sql_l = "update ".$sdatabase.DATATABLE."_order_client set ClientFlag = 0 where ClientCompany = ".$cid." and ClientID = ".intval($param['clientId'])." limit 1";
    			$deInsql = "update ".DB_DATABASEU.DATATABLE."_order_dealers set ClientFlag=0 where ClientID=".intval($param['clientId'])." and ClientCompany=".$cid." limit 1";
    		}elseif($param['action'] == 'unaudit'){
    			$sql_l = "update ".$sdatabase.DATATABLE."_order_client set ClientFlag = 9 where ClientCompany = ".$cid." and ClientID = ".intval($param['clientId'])." limit 1";
    		}   
    		//$rdata['sql'] = $sql_l; 		
    		$resultStatus	= $db->query($sql_l);
    		
    		if($resultStatus){
    		    if($deInsql) $db->query($deInsql);    //只有审核动作才有
    		    
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    } 

    
    /**
     * @desc 修改密码
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSetPassword($param){
    	global $db,$log;
    	
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{

    		if(empty($param['oldPassword']) || empty($param['newPassword'])){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '原密码和新密码不能为空!';
    			return $rdata;
    		}
    
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];

    		$param['oldPassword'] = strtolower(trim($param['oldPassword']));
    		$param['newPassword'] = strtolower(trim($param['newPassword']));
    		$opass = change_msg($cidarr['UserName'],$param['oldPassword']);
    		$npass = change_msg($cidarr['UserName'],$param['newPassword']);
    		
    		if(!is_filename($param['oldPassword']) || strlen($param['oldPassword']) < 3 || strlen($param['oldPassword']) > 18){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '请输入合法的原密码！(3-18位数字、字母和下划线)';
    			return $rdata;
    		}

    		if(!is_filename($param['newPassword']) || strlen($param['newPassword']) < 3 || strlen($param['newPassword']) > 18){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '请输入合法的新密码！(3-18位数字、字母和下划线)';
    			return $rdata;
    		}
    		$sql_l = "update ".DB_DATABASEU.DATATABLE."_order_user set UserPass='".$npass."' where UserID=".$cidarr['UserID']." and UserCompany=".$cid." and UserPass='".$opass."' limit 1";
    		$resultStatus	= $db->query($sql_l);
    		$log->logInfo('managerSetPassword return', $sql_l);
    		if($resultStatus){
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '原密码不正确';
    		}
    	}
    
    	return $rdata;
    }


    /**
     * @desc 提交反馈信息
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSubmitFeedback($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['feedbackType']) || empty($param['contact']) || empty($param['content'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '反馈类型  联系方式  反馈内容！';
    	}else{
    		$isAllow = denyRepeatSubmit($param,'submitFeedback');
    		if($isAllow){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '此数据已经提交过了，不要重复提交！';
    			return $rdata;
    		}
    
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 	= $cidarr['CompanyID'];
    
    		$sql_l  = "insert into ".DB_DATABASEU.DATATABLE."_common_feedback(CompanyID,ClientID,FeedbackType,ClientName,Contact,Content,CreateDate) values(".$cid.", 0, '".$param['feedbackType']."','".$cidarr['UserTrueName']."', '".$param['contact']."', '".$param['content']."', ".time().")";
    		$status	= $db->query($sql_l);
        	if($param['debug']) $rdata['rsql'] = $sql_l;
    		if($status){
    			$rdata['insertId'] = @mysql_insert_id();
    			$rdata['error']   = '您的反馈我们已收到，会尽快为您查看！';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    } 
    
    
    /**
     * @desc 获取openid
     * @param array $param ()
     * @return array $rdata
     */
    protected function weixinGetOpenId($param){
    	if(empty($param['code'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    		return $rdata;
    	}
    	 
    	$wid = intval($param['wid']);
    	$conf_file  = SITE_ROOT_PATH."/wx/wx_".$wid.".php";
    	if(file_exists($conf_file)){
    		include ($conf_file);
    	}else{
    		include (SITE_ROOT_PATH."/wx/wx_0.php");
    	}
    	 
    	$getUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.APPID.'&secret='.APPSECRET.'&code='.$param['code'].'&grant_type=authorization_code';
    	$getdata = curl_get_data($getUrl);
    
    	if(!empty($getdata['openid'])){
    		$rdata['rData']   = $getdata;
    		$rdata['error']   = '执行成功';
    		$rdata['rStatus'] = 100;
    	}else{
    		$rdata['rData']   = $getdata;
    		$rdata['error']   = '获取 openId不成功！';
    		$rdata['rStatus'] = 119;
    	}
    	return $rdata;
    }
    

 /**
     * @desc 获取openid
     * @param array $param ()
     * @return array $rdata
	 * @2016.3.1新增
     */
    protected function weixinqyGetOpenId($param){
    	global $db,$log;
    	if(empty($param['code'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    		return $rdata;
    	}
    	
    	$wid = intval($param['wid']);

        $temptoken = $db->get_row("select CorpID,Permanent_code from ".DB_DATABASEU.DATATABLE."_order_weixinqy where CompanyID=".$wid."");
		//$log->logInfo('temptoken return',$temptoken);

    	$tempsuite_ticket=file(WEB_TM_URL."data/ticket.txt");
		$tempsuite_access_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token",json_encode(array('suite_id'=>Suite_id,'suite_secret'=>Suite_secret,'suite_ticket'=>trim($tempsuite_ticket[0])))); //获取应用套件令牌
	     $suite_access_token=$tempsuite_access_token['suite_access_token'];
		//$log->logInfo('suite_access_token return',$tempsuite_access_token);

		 $tempaccess_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?suite_access_token=".$suite_access_token,json_encode(array('suite_id'=>Suite_id,'auth_corpid'=>$temptoken['CorpID'],'permanent_code'=>$temptoken['Permanent_code']))); //通过永久授权码获取ACCESS_TOKEN
		//$log->logInfo('tempaccess_token return',$tempaccess_token);

    	$getUrl ='https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$tempaccess_token['access_token'].'&code='.$param['code'];
		$getdata = curl_get_data($getUrl);// 获取企业用户userid


		if(!empty($getdata['UserId'])){//获取到userid 转换成openid
			$tempopenid=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=".$tempaccess_token['access_token'],json_encode(array('userid'=>$getdata['UserId'])));

			//管理端用户更新企业授权userid,openid
			$db->query("update ".DB_DATABASEU.DATATABLE."_order_weixinqy set OpenID='".$tempopenid['openid']."',WxUserID='".$getdata['UserId']."' where CompanyID=".$wid."");

			$rdata['rData']   = $tempopenid;
			$rdata['error']   = '执行成功';
			$rdata['rStatus'] = 100;
		}else{
			$rdata['rData']   = $getdata;
			$rdata['error']   = '获取 openId不成功！';
			$rdata['rStatus'] = 119;
		}
    	return $rdata;
    }


    /**
     * @desc 微信授权获取skey
     * @param array $param ()
     * @return array $rdata
     */
    public function managerWeixinGetTokenValue($param){
    
    	if(empty($param['code'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		if(empty($param['openId'])){
    			$rOpendId = self::weixinGetOpenId($param);
    			if($rOpendId['rStatus'] != '100'){
    				return $rOpendId;
    			}else{
    				$param['openId'] = $rOpendId['rData']['openid'];
    			}
    		}
    		$rdata = self::managerTokenValue($param);
    	}
    	$rdata['openId'] = $param['openId'];
    	$rdata['appUrl'] = WEB_API_URL.'api.php';
    	 
    	return $rdata;
    }
    
      /**
     * @desc 微信企业授权获取skey
     * @param array $param ()
     * @return array $rdata
	 * @2016.3.1 新增
     */
    public function managerWeixinqyGetTokenValue($param){
    	global $log;
    	if(empty($param['code'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		if(empty($param['openId'])){
    			$rOpendId = self::weixinqyGetOpenId($param);
    			if($rOpendId['rStatus'] != '100'){
    				return $rOpendId;
    			}else{
    				$param['openId'] = $rOpendId['rData']['openid'];
    			}
    		}
    		$param['weixin'] = 'qy';
    		$rdata = self::managerTokenValue($param);
    	}
    	$rdata['openId'] = $param['openId'];
    	$rdata['appUrl'] = WEB_API_URL.'api.php';
    	//$log->logInfo('rdata return',$rdata);
    	return $rdata;
    }

    /**
     * @desc 绑定帐号
     * @param array $param ()
     * @return array $rdata
     */
    public function managerWeixinBindAccount($param){
    	global $db,$log;
    	$openId = '';
    	 
    	if(empty($param['openId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['Username']) || empty($param['Password'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '帐号、密码 不能为空!';
    	}else{
    		$openId = $param['openId'];
    		unset($param['openId']);
    
    		$loginData = self::managerTokenValue($param);

    		if($loginData['rStatus'] == '100'){
    			//微信帐号
    			if(empty($baseInfo['nickname'])) $baseInfo['nickname'] = '';
    			if(!empty($openId)){
    				$insql = "insert into ".DB_DATABASEU.DATATABLE."_order_weixin(WeiXinID,UserID,UserType,NickName,CompanyID) values('".$openId."',".$loginData['rData']['UserID'].",'M', '".$baseInfo['nickname']."',".$loginData['rData']['CompanyID'].")";
    				$db->query($insql);
    				//$log->logInfo('managerWeixinBindAccount return', $insql);
    			}
    		}
    		$rdata   = $loginData;
    	}
    	return $rdata;
    }
    
    
    /**
     * 模拟客户登录
     *@param array $param(SerialNumber,Password) 用户名,密码
     *@return array $rdata(rStatus,error,sKey) 状态，提示信息，key
     *@author seekfor
     */
    public function  managerChangeClient($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['clientId'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '请选择您要操作的药店！';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{    		//权限验证
    			$module = array('name'=>'order','action'=>'pope_form');
    			$isallow = self::getModulePope($cidarr,$module);
    			if(!$isallow){
    				$rdata['rStatus'] = 110;
    				$rdata['error']   = '对不起，您没有此项操作权限！';
    				return $rdata;
    			}
    			//业务员数据范围
    			$smg = '';
    			if($cidarr['UserType'] == 'S'){
    				$smg .= " and instr('".$cidarr['separated']."', concat(',',ClientID,',')) > 0";
    			}
    			$cid 		= $cidarr['CompanyID'];
    			$sdatabase 	= $cidarr['Database'];
    		
    			$sqlru = "select ClientID,ClientCompany as CompanyID,ClientName from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientID=".$param['clientId']." and ClientCompany=".$cid." ".$smg." limit 0,1";
    			$ruinfo = $db->get_row($sqlru);
    		}
    	}    
    	
    	if(empty($ruinfo['ClientID'])){
    		$rdata['rStatus'] = 101;
    		$rdata['error']   = '帐号密码不匹配!';
    	}elseif($ruinfo['ClientFlag'] == '1'){
    		$rdata['rStatus'] = 101;
    		$rdata['error']   = '帐号已禁用，请与管理员联系!';
    	}elseif($ruinfo['ClientFlag'] == '9'){
    		$rdata['rStatus'] = 101;
    		$rdata['error']   = '帐号正处于待审核状态!';
    	}else{
    			
    		//公司表验证
    		$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyLogo,CompanyFlag,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$ruinfo['CompanyID']." limit 0,1");
    		$tmpArr['CompanyName'] 		= $ucinfo['CompanyName'];
    		$tmpArr['CompanySigned'] 	= $ucinfo['CompanySigned'];    			

    			
    			$cinfo = $db->get_row("select ClientName,ClientCompanyName,ClientTrueName from ".$sdatabase.DATATABLE."_order_client where ClientID = ".$ruinfo['ClientID']." limit 0,1");
    			if(!empty($param['backClientID'])) $tmpArr['ClientID'] 			= $ruinfo ['ClientID'];
    			$tmpArr['ClientName'] 			= $cinfo['ClientName'];
    			$tmpArr['ClientCompanyName'] 	= $cinfo['ClientCompanyName'];
    			$tmpArr['ClientTrueName'] 		= $cinfo['ClientTrueName'];
    
    			$rdata['rStatus'] = 100;
    			$rdata['error']   = '';
    			if(empty($param['ip'])) $param['ip'] = RealIp();
    			$token = md5 ( API_KEY.$ruinfo['ID'].$ruinfo['ClientName'].time());
    			$db->query ( "update ".DB_DATABASEU.DATATABLE."_order_dealers set LoginCount=LoginCount+1,LoginDate=".time().",LoginIP='".$param['ip']."', TokenValue='".$token."' where ClientID=" . $ruinfo ['ClientID'] . " limit 1" );
    			$rdata['sKey']   = $token;
    			$rdata['rData']  = $tmpArr;
    			
    			
    			$sqlex = "insert into ".$sdatabase.DATATABLE."_order_execution(ExecutionCompany,ExecutionUser,ExecutionLink,ExecutionAction,ExecutionData,ExecutionDate) values(".$cid.", '".$cidarr['UserName']."', 'Mobiel:managerChangeClient','模拟药店登录(".$ruinfo ['ClientID'] .")','-',".time().")";
    			$db->query($sqlex);
    
    	}
    	//$log->logInfo('managerChangeClient return', $rdata);
    	return $rdata;
    }
    
    /**
     * @desc 取消微信绑定
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerRemoveWeixin($param){
    	global $db,$log;
    
    	if(empty($param['sKey']) || empty($param['openId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    
    		$sql_l = "delete from ".DB_DATABASEU.DATATABLE."_order_weixin where WeiXinID='".$param['openId']."' and UserID=".$cidarr['UserID']." and UserType='M' limit 1" ;
    		$resultStatus	= $db->query($sql_l);
    
    		if($resultStatus){
    			$sql_l = "update ".DB_DATABASEU.DATATABLE."_order_user set TokenValue='' where TokenValue='".$param['sKey']."' limit 1" ;
    			$resultStatus	= $db->query($sql_l);
    			 
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    } 

    
    /**
     * @desc 添加药店
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerClientAdd($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'client','action'=>'pope_form');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		
    		//tubo 2015-12-2 提出要限制药店创建
    		$is_erp = erp_is_run($db,$cid);
    		if($is_erp){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = 'ERP 用户请通过接口同步新增药店资料！';
    			return $rdata;
    		}

			$cs_info = $db->get_row("SELECT CS_Flag FROM ".DB_DATABASEU.DATATABLE."_order_cs WHERE CS_Company={$cid} LIMIT 1");
			$client_cnt = $db->get_var("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_order_dealers WHERE ClientCompany={$cid} LIMIT 1");
			if(in_array($cs_info['CS_Flag'],array('W','F')) && $client_cnt >= 1) {
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您的帐号还没有认证，请先从PC端进行实名认证！';
    			return $rdata;
			}

    		$sql_area  = "select AreaID,AreaParentID as ParentID,AreaName,AreaPinyi from ".$sdatabase.DATATABLE."_order_area where AreaCompany=".$cid."  order by AreaParentID asc,AreaID asc ";
    		$areaData  = $db->get_results($sql_area);
			
    		$setproduct = self::getSystemSet('product',$cidarr);
    		$rdata['price1Name'] = $setproduct['product_price']['price1_name'] ? $setproduct['product_price']['price1_name'] : "价格一";
    		$rdata['price2Name'] = $setproduct['product_price']['price2_name'] ? $setproduct['product_price']['price2_name'] : "价格二";
    		
    		$levelData = self::getSystemSet('clientlevel',$cidarr);
    		$levelArr  = $levelData[$levelData['isdefault']];
    		foreach($levelArr as $k=>$v){
    			if($k != 'id' && $k != 'name'){
    				$nLevelArr[$levelArr['id'].'_'.$k] = $v;
    			}
    		}
    		
    		$rdata['rStatus'] = 100;
			$rdata['rData']['area']  = $areaData;
			$rdata['rData']['level'] = $nLevelArr;				
			$rdata['rData']['prefix'] = $cidarr['CompanyPrefix'];
    	}
    
    	return $rdata;
    }
    
    /**
     * @desc 编辑药店
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerClientEdit($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['clientId'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '请选择您要操作的药店！';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		//权限验证
    		$module = array('name'=>'client','action'=>'pope_form');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$smsg = '';
    		
    		$clientinfo  = $db->get_row("SELECT ClientID,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientFlag,ClientSetPrice,ClientPercent FROM ".$sdatabase.DATATABLE."_order_client  where ClientCompany=".$cid." and ClientID=".intval($param['clientId'])." limit 0,1");
    		//$dealersinfo = $db->get_row("SELECT ClientID,ClientName,ClientPassword,ClientMobile,ClientFlag FROM ".DB_DATABASEU.DATATABLE."_order_dealers  where ClientCompany=".$cid." and ClientID=".intval($param['clientId'])." limit 0,1");
    		$dealersinfo = $db->get_row("SELECT ClientID,ClientName,ClientPassword,ClientFlag FROM ".DB_DATABASEU.DATATABLE."_order_dealers  where ClientCompany=".$cid." and ClientID=".intval($param['clientId'])." limit 0,1");
    		$contentData = array_merge($clientinfo, $dealersinfo);
    		$contentData['ClientName'] = str_replace($cidarr['CompanyPrefix']."-","",$contentData['ClientName']);
    		
    		$sql_area  = "select AreaID,AreaParentID as ParentID,AreaName,AreaPinyi from ".$sdatabase.DATATABLE."_order_area where AreaCompany=".$cid."  order by AreaParentID asc,AreaID asc ";
    		$areaData  = $db->get_results($sql_area);
    			
    		$setproduct = self::getSystemSet('product',$cidarr);
    		$rdata['price1Name'] = $setproduct['product_price']['price1_name'] ? $setproduct['product_price']['price1_name'] : "价格一";
    		$rdata['price2Name'] = $setproduct['product_price']['price2_name'] ? $setproduct['product_price']['price2_name'] : "价格二";
    
    	    $levelData = self::getSystemSet('clientlevel',$cidarr);
    		$levelArr  = $levelData[$levelData['isdefault']];
    		foreach($levelArr as $k=>$v){
    			if($k != 'id' && $k != 'name'){
    				$nLevelArr[$levelArr['id'].'_'.$k] = $v;
    			}
    		}

    		if(strpos($clientinfo['ClientLevel'],",")){
    			$clientlevelarr1 = explode(",", $clientinfo['ClientLevel']);
    			$rdata['d'] = $clientlevelarr1;
    			foreach($clientlevelarr1 as $cl){
    				if($levelData['isdefault'] == substr($cl,0,1)){
    					$contentData['ClientLevel'] = $cl;
    				}
    			}
    		}else{
    			if($levelData['isdefault'] != substr($clientinfo['ClientLevel'],0,1)){
    				$contentData['ClientLevel'] = '';
    			}
    		}
    		//begin tubo修改，密码改为*传给前端 2015-11-04
    		$contentData['ClientPassword'] = '******';
    		//end tubo
    		$rdata['rStatus'] = 100;
    		$rdata['rData']['area']  = $areaData;
    		$rdata['rData']['level'] = $nLevelArr;
    		$rdata['rData']['content']   = $contentData;
    		$rdata['rData']['prefix']    = $cidarr['CompanyPrefix'];
    
    	}
    
    	return $rdata;
    }
    
    
    /**
     * @desc 保存客户资料
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSubmitClient($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$log->logInfo('managerSubmitClient in', $param);
   
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 	    = $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		
    		//tubo 2015-12-2 提出要限制药店创建，523不限制
    		/*if($param['action'] == 'add'){
	    		$is_erp = erp_is_run($db,$cid);
	    		if(($is_erp)&&($cid!=523)){
	    			$rdata['rStatus'] = 101;
	    			$rdata['error']   = 'Erp用户请通过接口同步新增药店资料！';
	    			return $rdata;
	    		}
    		}*/
    		//权限验证
    		$module = array('name'=>'client','action'=>'pope_form');
    		$isallow = self::getModulePope($cidarr,$module);
    		if(!$isallow){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '对不起，您没有此项操作权限！';
    			return $rdata;
    		}
    		$rdata['rStatus'] 	= 101;
    		//删除操作
    		if($param['action'] == 'del'){
    			$status = false;
    			$upsql = "update ".$sdatabase.DATATABLE."_order_client set ClientFlag=1 where ClientID = ".$param['clientId']." and ClientCompany=".$cid;
    			 
    			if($db->query($upsql)){
    				$status = $db->query("update ".DB_DATABASEU.DATATABLE."_order_dealers set ClientFlag=1 where ClientID = ".$param['clientId']." and ClientCompany=".$cid);
    			}
    			if($status){
    				$rdata['error']    = '执行成功!';
    				$rdata['rStatus']  = 100;
    			}else{
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '执行不成功!';
    			}  
    			return $rdata;  			
    		}
    		
    		if(empty($param['ClientName']) || empty($param['ClientPassword']) || empty($param['ClientCompanyName'])){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '请填写帐号、密码、单位名称！';
    		}
    		
    		$param['ClientName']	  = strtolower($param['ClientName']);    		
    		$param['ClientPassword']  = strtolower($param['ClientPassword']);
    		if(!is_filename($param['ClientName']) || strlen($param['ClientName']) < 1 || strlen($cidarr['CompanyPrefix']."-".$param['ClientName']) > 30){
    			$rdata['error'] 	= '请填写正确的帐号（必需用数字字母和下划线组成）！';
    			return $rdata;
    		}
    		//begin tubo修改，密码改为*传给前端 2015-11-04
    		if($param['ClientPassword']!='******'){
    		//end tubo  		
	    		if(!is_filename($param['ClientPassword']) || strlen($param['ClientPassword']) < 3 || strlen($param['ClientPassword']) > 18 ){
	    			$rdata['error'] 	= '请填写正确的密码（必需用数字字母和下划线组成）！';
	    			return $rdata;
	    		}
    		}
    		
    		$param['ClientName'] = $cidarr['CompanyPrefix']."-".$param['ClientName'];
    		if(empty($param['ClientPercent'])) $param['ClientPercent'] = '10.0';
    		
    		$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DB_DATABASEU.DATATABLE."_order_dealers where ClientName='".$param['ClientName']."' ".$sqlmsg." limit 0,1");
    		
    		if(($clientinfo['orwname'] > 0 && $param['action'] == 'add') || ($clientinfo['orwname'] > 1 && $param['action'] == 'edit')){
    			$rdata['error'] 	= '此帐号已存在，请换名再试！';
    			return $rdata;
    		}
    		
    		$dmobile = '';
    		if(!empty($param['ClientMobile']))
    		{
    			if(!is_phone($param['ClientMobile'])){
    				$rdata['error'] 	= '请输入正确的手机号码!';
    				return $rdata;
    			}else{
    				$clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DB_DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$param['ClientMobile']."' ".$sqlmsg." limit 0,1");
    				if($clientminfo['orwname'] > 0){
    					$dmobile = '';
    				}else{
    					$dmobile = $param['ClientMobile'];
    				}
    			}
    		}    		
    		
    		$InfoDataNum = $db->get_row("SELECT count(*) AS clientrow FROM ".DB_DATABASEU.DATATABLE."_order_dealers where ClientCompany = ".$cid." and ClientFlag=0 ");

    		if($InfoDataNum['clientrow'] > 20){
    			$csData = self::getCsInfo($cidarr);
    			if($InfoDataNum['clientrow'] >= $csData['rData']['CS_Number']){
    				$rdata['error'] = '您授权用户数已全部用完，请联系开发商增加授权用户!';
    				return $rdata;
    			}
    		}
    		
    		$isAllow = denyRepeatSubmit($param,'submitClient');
    		if($isAllow){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '此数据已经提交过了，不要重复提交！';
    			return $rdata;
    		}
    		$status = false;
    		$param['ClientArea'] = intval($param['ClientArea']);
    		if($param['ClientSetPrice'] != 'Price2') $param['ClientSetPrice'] = 'Price1';
    		
    		if($param['action'] == 'add'){    			
    			$upsql = "insert into ".DB_DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,ClientMobile,LoginDate,ClientFlag) values(".$cid.", '".$param['ClientName']."', '".$param['ClientPassword']."','".$dmobile."',".time().",".intval($param['ClientFlag']).")";
    			//$log->logInfo('submitClient add u', $upsql);
    			if($db->query($upsql)){
    				$inid    =  $db->insert_id;
    				$insql	 = "insert into ".$sdatabase.DATATABLE."_order_client(
    					ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,
    					ClientShield,ClientSetPrice,ClientPercent,ClientFlag)
    					values(".$inid.",".$cid.",'".$param['ClientLevel']."',".$param['ClientArea'].", '".$param['ClientName']."', '".$param['ClientCompanyName']."', '', '".$param['ClientNO']."', '".$param['ClientTrueName']."', '".$param['ClientEmail']."', '".$param['ClientPhone']."', '".$param['ClientFax']."', '".$param['ClientMobile']."', '".$param['ClientAdd']."', '".$param['ClientAbout']."',".time().", '','".$param['ClientSetPrice']."', '".$param['ClientPercent']."', ".intval($param['ClientFlag']).")";
    				$status = $db->query($insql);
    				//$log->logInfo('submitClient add', $insql);
    				if($status){
    					//归属到药店下面
    					$insqls = "insert into ".$sdatabase.DATATABLE."_order_salerclient(CompanyID,SalerID,ClientID) values(".$cid.", ".$cidarr['UserID'].", ".$inid.")";
    					$db->query($insqls);
    					//$log->logInfo('submitClient salerclient', $insqls);
    				} 
    			}    			

    		}elseif($param['action'] == 'edit'){
    			if(!intval($param['clientId'])){
    				$rdata['rStatus'] 	= 101;
    				$rdata['error'] 	= '请指定您要操作的数据！';
    				return $rdata;
    			}
    			//begin tubo修改，密码改为*传给前端 2015-11-04
    			if($param['ClientPassword']!='******'){
    				$insql = "update ".DB_DATABASEU.DATATABLE."_order_dealers set ClientName='".$param['ClientName']."', ClientPassword='".$param['ClientPassword']."', ClientMobile = '".$dmobile."', ClientFlag=".$param['ClientFlag']." where ClientID=".$param['clientId']." and ClientCompany=".$cid;
    			}else{
    				$insql = "update ".DB_DATABASEU.DATATABLE."_order_dealers set ClientName='".$param['ClientName']."', ClientMobile = '".$dmobile."', ClientFlag=".$param['ClientFlag']." where ClientID=".$param['clientId']." and ClientCompany=".$cid;
    			}//end tubo
    			$isu = $db->query($insql);
    			//$log->logInfo('submitClient edit u', $insql);
    			if($isu){
    				$upsql = "update ".$sdatabase.DATATABLE."_order_client set 
    						ClientLevel='".$param['ClientLevel']."', 
    						ClientArea=".$param['ClientArea'].", 
    						ClientName='".$param['ClientName']."', 
    						ClientCompanyName='".$param['ClientCompanyName']."', 
    						ClientNO='".$param['ClientNO']."',  
    						ClientTrueName='".$param['ClientTrueName']."', 
    						ClientEmail='".$param['ClientEmail']."', 
    						ClientPhone='".$param['ClientPhone']."', 
    						ClientFax='".$param['ClientFax']."', 
    						ClientMobile='".$param['ClientMobile']."', 
    						ClientAdd='".$param['ClientAdd']."', 
    						ClientAbout='".$param['ClientAbout']."',
    						ClientSetPrice='".$param['ClientSetPrice']."', 
    						ClientPercent='".$param['ClientPercent']."',
    						ClientFlag=".$param['ClientFlag']." 
    						where ClientID=".$param['clientId']." and ClientCompany=".$cid;
    				$status = $db->query($upsql);
    				//$log->logInfo('submitClient update', $insql);    				
    			}
    		}
    		
    		if($status){
    			$rdata['insertId'] = $inid;
    			$rdata['error']    = '执行成功!';
    			$rdata['rStatus']  = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功!';
    		}
    	}    
    	return $rdata;
    }    
    
/***  以下部份为开通帐号  *****/
    
    /**
     * @desc 获取短信验证码
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerGetSmsCode($param){
    	global $db,$log;
    
    	if(!is_phone($param['mobile'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '请输入正确的手机号!';
    	}elseif(md5($param['mobile']."_DHB") != $param['token']){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$log->logInfo('managerGetSmsCode in', $param);
    		/**
    	    $isAllow = denyRepeatSubmit($param,'managerGetSmsCode');
    		if($isAllow){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '发送太频繁了，不要重复提交！';
    			return $rdata;
    		}
    		*/
    		$param['ip'] = RealIp();
    	    //同一个IP一天只能发送10个手机号,一个手机号一天只能发10条短信
		    $end = time();
		    $begin = strtotime("-1 days" , $end );
		    
		    $valid_data = $db->get_row("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_buy_account WHERE  mobile = '".$param['mobile']."' AND status = 'T' ");
		    if($valid_data['Total'] > 0) {
		    	$rdata['rStatus']  = 101;
                $rdata['isReg'] = 'T';
		    	$rdata['error']    = '此手机号码已注册！'; //fixme 待确定
		    	return $rdata;
		    }

		    $valid_data = $db->get_row("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_buy_sms WHERE ip='".$param['ip']."' AND code <> 0 AND time BETWEEN {$begin} AND {$end} ");
		    if($valid_data[Total] > 20) {
		    	$rdata['rStatus'] = 101;
		    	$rdata['error']    = '您给太多手机进行验证，若需开通更多的订货宝帐号？请致电 028-84191728 联系客服外理'; //fixme 待确定
		    	return $rdata;
		    } 
		    $valid_data = $db->get_row("SELECT COUNT(*) as Total FROM ".DB_DATABASEU.DATATABLE."_buy_sms WHERE mobile = '".$param['mobile']."' AND time BETWEEN {$begin} AND {$end} ");
		    if($valid_data['Total'] > 5) {
		    	$rdata['rStatus']  = 101;
		    	$rdata['error']    = '验证次数过多，您的手机累坏了， 若无法收到验证短信？请致电 028-84191728 联系客服获取校验码'; //fixme 待确定
		    	return $rdata;
		    }    		

    		include_once ("./WebService/include/Client.php");
    		include_once ("./soap2.inc.php");    		
    		
    		$code = rand(100000,999999);
    		$message = str_replace('{CODE}',$code,$sms_config['sms_validate']);
    			
    		$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
    		$client->setOutgoingEncoding("UTF-8");
    			
    		$mobilearr[]    = $param['mobile'];
    		$statusCode2    = $client->login();
    		$statusCode     = $client->sendSMS($mobilearr,$message);   
    		$status = $db->query("INSERT INTO ".DB_DATABASEU. DATATABLE."_buy_sms (mobile,code,message,time,ip,status,is_next) VALUES ('{$param['mobile']}',{$code},'{$message}',".time().",'".$param['ip']."','{$statusCode}','F')");
    		
    		//$rdata['rsm'] = $message;
    		if($statusCode == '0'){
    			$rdata['error']    = '发送成功!';
    			$rdata['rStatus']  = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '发送不成功,请致电：400 6311 682 ';
    		}
    	}
    	return $rdata;
    }
    
    /**
     * @desc 验证短信校验码
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerVerifySmsCode($param){
    	global $db,$log;
    	//$log->logInfo('managerVerifySmsCode in', $param);
    	
    	if(!is_phone($param['mobile'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '请输入正确的手机号!';
    	}elseif(empty($param['smsCode']) || !intval($param['smsCode'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '请输入正确的短信校验码!';
    	}else{
       		
    		$rowData = $db->get_row("select count(*) as allrow from ".DB_DATABASEU. DATATABLE."_buy_sms where mobile = '".$param['mobile']."' and code = ".intval($param['smsCode'])." and is_next = 'F' ");
    		//$rdata['rsql'] = "select count(*) as allrow ".DB_DATABASEU. DATATABLE."_buy_sms where mobile = '".$param['mobile']."' and code = ".intval($param['smsCode'])." ";
    		if($rowData['allrow'] > 0){
    			$status = $db->query("update ".DB_DATABASEU. DATATABLE."_buy_sms set is_next = 'T' where mobile = '{$param['mobile']}' and code = ".intval($param['smsCode'])." ");
    			$rdata['error']    = '验证成功!';
    			$rdata['rStatus']  = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '短信校验码不正确 ';
    		}
    	}
    	return $rdata;
    }


    /**
     * @desc 获取行业信息
     * @param $param
     * @return array $rData
     * @author hxtgirq
     * @since 2015-08-19
     */
    public function managerGetIndustry($param) {
        global $db,$log;
        $rData = array();
        //$log->logInfo("managerIndustry in",$param);
        $industry = $db->get_results("SELECT IndustryID,IndustryName  FROM ".DB_DATABASEU.DATATABLE."_common_industry ORDER BY OrderNum DESC,IndustryID ASC");
        if($industry) {
            $rData['rStatus'] = 100;
            $rData['rData'] = $industry;
        } else {
            $rData['rStatus'] = 101;
            $rData['error'] = "暂无行业信息";
        }
        return $rData;
    }

    /**
     * @desc 提交注册信息
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function managerSubmitCompany($param){
    	global $db,$log;
    	//$log->logInfo('managerSubmitCompany in', $param);
    
    	if(empty($param['username']) || empty($param['confirmPassword'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误 ！';
    	}elseif(empty($param['prefix']) || empty($param['trueName']) || empty($param['platformName'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '参数错误 ！';
    	}elseif(!is_phone($param['mobile'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '参数错误 ！';	
    	}else{
    		
    		$rowData = $db->get_row("select count(*) as allrow ".DB_DATABASEU. DATATABLE."_buy_sms where mobile = '".$param['mobile']."' and is_next = 'T' ");
    		if(empty($rowData['allrow'])){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '非法访问 ！';	
    		}
    		$param['prefix'] = strtolower($param['prefix']);
    		$param['username'] = strtolower($param['username']);
    		$param['confirmPassword'] = strtolower($param['confirmPassword']);
            $param['industry'] = intval($param['industry']);
    		if(!is_prefix($param['prefix'])) {
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '请输入合法的二级域名！(3-10位数字、字母)';
    			return $rdata;
    		}
    		if(!is_filename($param['username']) || strlen($param['username']) < 3 || strlen($param['username']) > 30){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '请输入合法的帐号！(3-30位数字、字母和下划线)';
    			return $rdata;
    		}
    		if(!is_filename($param['confirmPassword']) || strlen($param['confirmPassword']) < 3 || strlen($param['confirmPassword']) > 32){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '请输入合法的密码！(3-32位数字、字母和下划线)';
    			return $rdata;
    		}
    		
    		//再次检测登录账号&独立二级域名是否已占用
    		if(!valid_name($db,$param['username'])) {
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '该账号已被使用,请换名称再试!';
    			return $rdata;
    		}elseif(!valid_prefix($db,$param['prefix'])) {
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '当前域名已被占用,请换名称再试!';
    			return $rdata;
    		}
    		if(empty($param['ip'])) $param['ip'] = RealIp();
    		$bsql = "INSERT INTO ".DB_DATABASEU.DATATABLE."_buy_account (mobile,user_name,passwd,industry,prefix,name,platform_name,time,ip) VALUES ('{$param['mobile']}','{$param['username']}','{$param['confirmPassword']}',{$param['industry']},'{$param['prefix']}','{$param['trueName']}','{$param['platformName']}',".time().",'".$param['ip']."')";
    		$acc_result = $db->query($bsql);
    		$account_id = $db->insert_id;
    		if($acc_result) {
    			$pwd = change_msg($param['username'],$param['confirmPassword']);    		
    		
    			$company_sql = "INSERT INTO ".DB_DATABASEU.DATATABLE."_order_company (CompanyIndustry,CompanyName,CompanyPrefix,CompanyContact,CompanyMobile,CompanyWeb,CompanyDate,CompanyFlag,CompanyDatabase) VALUES ({$param['industry']},'{$param['platformName']}','{$param['prefix']}','{$param['trueName']}','{$param['mobile']}','',".time().",'0',0)";
    			$db->query($company_sql);
    			$company_id = $db->insert_id;    			
    			//当前账号应该使用的数据库
    			$database = floor($company_id / 2000) + 10;
    			$db->query("UPDATE ".DB_DATABASEU.DATATABLE."_order_company SET CompanyDatabase={$database} WHERE CompanyID={$company_id} LIMIT 1");
    			// order_cs 数据插入
				if(empty($param['loginFrom'])) $param['loginFrom'] = 'Mobile';
				$enddate = date('Y-m-d', strtotime('+1 month'));  //免费用户开通两个月
    			$cs_sql = "INSERT INTO ".DB_DATABASEU . DATATABLE."_order_cs (CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_UpDate,CS_UpdateTime,CS_SmsNumber,CS_Flag,LoginFrom) VALUES
    			({$company_id},10000,'".date('Y-m-d')."','".$enddate."','".date('Y-m-d')."',".time().",20,'W','".$param['loginFrom']."')";
    			$db->query($cs_sql);
				//$log->logInfo('managerSubmitCompany cs_sql', $cs_sql);
    			$user_sql = "INSERT INTO ". DB_DATABASEU . DATATABLE."_order_user (UserName,UserPass,UserCompany,UserTrueName,UserPhone,UserMobile,UserDate,UserFlag,UserDataBase,UserType)
    			VALUES ('{$param['username']}','{$pwd}',{$company_id},'{$param['trueName']}','管理员','{$param['mobile']}',".time().",'9',{$database},'M')";
    			$db->query($user_sql);
    			$user_id = $db->insert_id;
    			if(!empty($user_id)){
    				if(!(file_exists (RESOURCE.$company_id)))
    				{
    					_mkdir(RESOURCE,$company_id);
    				}
    				
    				$db->query("UPDATE ".DB_DATABASEU . DATATABLE."_buy_account SET company_id={$company_id}, status='T' WHERE id={$account_id}");
					//初史化数据
					if(empty($database)) $sdatabase = DB_DATABASE.'.'; else $sdatabase = DB_DATABASE."_".$database.'.';
					self::InputDefaultValue($db,$company_id,$param['mobile'],$sdatabase);

    				//发送短信通知供应商账号已开通
    				include_once ("./WebService/include/Client.php");
    				include_once ("./soap2.inc.php");
    				
    				$message = str_replace(array('{ACCOUNT}','{PASSWORD}'),array($param['username'],$param['confirmPassword']),$sms_config['sms_notify']);
    				 
    				$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
    				$client->setOutgoingEncoding("UTF-8");
    				 
    				$mobilearr[]    = $param['mobile'];
    				$statusCode2    = $client->login();
    				$statusCode     = $client->sendSMS($mobilearr,$message);
    				
    				$smsSql = "INSERT INTO ".DB_DATABASEU. DATATABLE."_buy_sms (mobile,type,code,message,time,ip,status,is_next) VALUES ('{$param['mobile']}','notify',0,'{$message}',".time().",'".$param['ip']."','{$statusCode}','F')";
    				$status = $db->query($smsSql);
    				$log->logInfo('managerSubmitCompany smsSql', $smsSql);
    				
    				//通过帐号密码
    				$tmpParam['loginFrom'] = $param['loginFrom'];
    				$tmpParam['Username']  = $param['username'];
    				$tmpParam['Password']  = $param['confirmPassword'];
    				//登录
    				$loginData = self::managerTokenValue($tmpParam);
					//$loginData['error'] = '温馨提示：订货宝手机管理端能够让您方便地在线处理业务。在此之前，请您先从电脑登录完善基础数据！';
					return $loginData;
    			}
    		}    		
    	}
    	return $rdata;
    }   
    
    /**
     * @desc 初史化开通帐号内容
     * @param array $param (sKey)
     * @return array $rdata
     */
    protected function InputDefaultValue($db,$cid,$phone,$sdatabase)
    {
    	$settype = 'product';
    	$valuemsg = 'a:12:{s:15:"checkandapprove";s:2:"on";s:11:"producttype";s:7:"imglist";s:14:"product_number";s:2:"on";s:16:"product_negative";s:3:"off";s:19:"product_number_show";s:3:"off";s:11:"return_type";s:5:"order";s:11:"deduct_type";s:2:"on";s:10:"audit_type";s:3:"off";s:14:"regiester_type";s:2:"on";s:13:"delivery_time";s:1:"N";s:10:"show_money";s:2:"on";s:13:"product_price";a:4:{s:11:"price1_show";s:2:"on";s:11:"price1_name";s:9:"参考价";s:11:"price2_show";s:3:"off";s:11:"price2_name";s:9:"订货价";}}';
    	$isq = $db->query("insert into ".DB_DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");
    
    	$smsarr = Array
    	(
    			0 => 1,
    			1 => 2,
    			2 => 3,
    			3 => 4,
    			4 => 5,
    			5 => 6,
    			6 => 7,
    			7 => 8,
    			9 => 9,
    			'Mobile' => Array
    			(
    					'MainPhone' => $phone,
    					'FinancePhone' => $phone,
    					'FinancePhone' => $phone
    			)
    	);
    	$settype  = 'sms';
    	$valuemsg = serialize($smsarr);
    	$isq = $db->query("insert into ".DB_DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");
    
    	$settype  = 'clientlevel';
    	$valuemsg = 'a:2:{s:1:"A";a:5:{s:7:"level_1";s:15:"一级药店";s:7:"level_2";s:15:"二级药店";s:7:"level_3";s:15:"三级药店";s:2:"id";s:1:"A";s:4:"name";s:15:"药店类型";}s:9:"isdefault";s:1:"A";}';
    	$isq = $db->query("insert into ".DB_DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");
    
    	$settype  = 'printf';
    	$valuemsg = 'a:18:{s:2:"NO";a:3:{s:4:"name";s:6:"序号";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:6:"Coding";a:3:{s:4:"name";s:6:"货号";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:2:"8%";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:2:"8%";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:6:"Price1";a:3:{s:4:"name";s:9:"零售价";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"价格";s:5:"width";s:3:"10%";s:4:"show";s:0:"";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:2:"6%";s:4:"show";s:0:"";}s:12:"PercentPrice";a:3:{s:4:"name";s:9:"批发价";s:5:"width";s:3:"10%";s:4:"show";i:1;}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"12%";s:4:"show";i:1;}s:16:"CompanyInfoPrint";s:1:"2";s:5:"order";a:16:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"8%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"6%";s:4:"show";s:1:"1";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:1:"1";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"PercentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"1";}s:4:"send";a:16:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"4%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"6%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";s:1:"1";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price1";a:3:{s:4:"name";s:7:"价格1";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:6:"Price2";a:3:{s:4:"name";s:7:"价格2";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:14:"ContentPercent";a:3:{s:4:"name";s:6:"折扣";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"PercentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:2:"8%";s:4:"show";s:1:"1";}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"1";}s:6:"return";a:12:{s:2:"NO";a:3:{s:4:"name";s:6:"行号";s:5:"width";s:2:"5%";s:4:"show";s:1:"1";}s:6:"Coding";a:3:{s:4:"name";s:6:"编号";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:11:"ContentName";a:3:{s:4:"name";s:12:"商品名称";s:5:"width";s:0:"";s:4:"show";i:1;}s:7:"Barcode";a:3:{s:4:"name";s:6:"条码";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentColor";a:3:{s:4:"name";s:6:"颜色";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:20:"ContentSpecification";a:3:{s:4:"name";s:6:"规格";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:13:"ContentNumber";a:3:{s:4:"name";s:6:"数量";s:5:"width";s:2:"5%";s:4:"show";i:1;}s:5:"Units";a:3:{s:4:"name";s:6:"单位";s:5:"width";s:2:"5%";s:4:"show";s:0:"";}s:6:"Casing";a:3:{s:4:"name";s:6:"包装";s:5:"width";s:0:"";s:4:"show";s:0:"";}s:12:"ContentPrice";a:3:{s:4:"name";s:6:"单价";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:9:"LineTotal";a:3:{s:4:"name";s:6:"金额";s:5:"width";s:3:"10%";s:4:"show";s:1:"1";}s:16:"CompanyInfoPrint";s:1:"1";}}';
    	$isq = $db->query("insert into ".DB_DATABASEU.DATATABLE."_order_companyset(SetCompany,SetName,SetValue) values(".$cid.",'".$settype."','".$valuemsg."')");
    
    	$insql = "insert into ".$sdatabase.DATATABLE."_order_expense_bill(BillNO,BillName,CompanyID) values
    	('1001','期初',$cid),
    	('1002','返利',$cid),
    	('1003','补差',$cid),
    	('1004','促销费',$cid)";
    	$isq = $db->query($insql);
    	
    	//地区
    	$areasql = "insert into ".$sdatabase.DATATABLE."_order_area (AreaCompany,
			AreaParentID,
			AreaName,
			AreaPinyi,
			AreaAbout) values($cid,0,'通用','TY','')";    	
    	$db->query($areasql);
    	
    	//分类
    	$sitesql = "insert into ".$sdatabase.DATATABLE."_order_site (CompanyID,
			ParentID,
			SiteNO,
			SiteOrder,
			SiteName,
			SitePinyi,
			SiteAdmin,
			Content,
			Disabled) values($cid,0,'0.',0,'通用','TY','System','',0)";
    	$db->query($sitesql);
    	$insert_id = mysql_insert_id();
    	$db->query("update ".$sdatabase.DATATABLE."_order_site set SiteNO = '0.".$insert_id.".' where SiteID=".$insert_id." limit 1");

    }    
    
    /**
     * 存储体验联系人资料
     *
     * @param unknown_type $param
     */
    public function managerStoreLinkMan($param = array()){
    	global $db,$log;
    	
		$log->logInfo('experience linkman info in', $param);
    
		//检验数据
    	if(empty($param['Name']) || empty($param['Phone']) || empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误 ！';
    		return $rdata;
    	}
    	
    	if(strlen($param['Phone']) > 11){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '手机号码不能超过11位！';
    		return $rdata;
    	}
    	
    	//存储数据
    	$ip = RealIp();
	
		$param['Name']  	= strip_tags($param['Name']);
		$param['Phone'] 	= strip_tags($param['Phone']);
		$param['industry']	= intval($param['industry']);

		$companyInfo = $db->get_row("select CompanyID from ".DB_DATABASEU.DATATABLE."_order_company where CompanyFlag='0' and CompanyIndustry=".$param['industry']." and IsUse = 1 and LoginIP = '{$ip}' and IsSystem=0 order by CompanyID asc limit 0,1 ");
			
		if(empty($companyInfo['CompanyID'])){
			$companyInfo = $db->get_row("select CompanyID from ".DB_DATABASEU.DATATABLE."_order_company where  CompanyIndustry=".$param['industry']." and CompanyFlag = '0' and IsUse=0 and IsSystem=0 order by CompanyID asc limit 0,1");
		}
			
		$insql = "insert into ".DB_DATABASEU.DATATABLE."_experience_contact(ContactName,Phone,Date,Status,Remark,CompanyID,IP,Industry)
					values('{$param['Name']}','{$param['Phone']}', ".time().", '0', '','{$companyInfo['CompanyID']}','{$ip}','{$param['industry']}' )";
		
		$status	= $db->query($insql);
    	if($status) {
            $rData['rStatus'] = 100;
            $rData['rData'] = '保存成功';
        } else {
            $rData['rStatus'] = 101;
            $rData['error'] = "保存失败！";
        }
        return $rData;	
    	
    }
 
    /**
     * 建立账号 搜索店铺
     * @param string $param (sKey) 
     * @return array $data
     */
    public function managerSearch($param)
    {
    	global $db,$log;
    	//先查询出当前登陆的人的身份
    	$cinfo = $db->get_row("SELECT UserCompany,UserID,UserFlag,UserType FROM ".DB_DATABASEU.DATATABLE."_order_user where TokenValue = '".$param['sKey']."' limit 0,1");
   		$sqlmsg=' AND ClientFlag!=1'; 
    	$param['search']=trim($param['search']);   
        if ($param['search']!='') {
              $sqlmsg.=" AND ClientCompanyName like '%".$param['search']."%'";
	    }
	    //如果是客情官的话就去查询客情管所管辖的药店之后根据条件搜索
        if($cinfo['UserType']=='S'){
            $salersql="SELECT ClientID FROM  ".DB_DATABASE.".".DATATABLE."_order_salerclient where SalerID=".$cinfo['UserID']."";
            $saler=$db->get_results($salersql);
    	  	foreach ($saler as $key => $val) {
        		$client=$db->get_row("SELECT ClientID,ClientCompanyName,ClientMobile FROM ".DB_DATABASE.'.'.DATATABLE."_order_client where ClientCompany = ".$cinfo['UserCompany']." AND ClientID=".$val['ClientID'].$sqlmsg." AND (C_Flag = 'W' or C_Flag = 'F')");
        		if ($client) {
        			$rData[]=$client;
        		}
        	}
       }else{
       		//如果是商业公司或者管理员或者代理商的话就可以显示所有的药店之后根据条件搜索
	    	$sql="SELECT ClientID,ClientCompanyName,ClientMobile FROM ".DB_DATABASE.'.'.DATATABLE."_order_client where ClientCompany = ".$cinfo['UserCompany'].$sqlmsg." AND (C_Flag = 'W' or C_Flag = 'F')";
	    	$rData=$db->get_results($sql);
	   }
	    	$data['rData']=$rData;
	    	return $data;
    }

 
 	/**
     * 建立账号
     * @param int $tel 手机号
     * @param int $user_id 药店id
     * @return array $msg
     */
 	public function managerEstablish($tel,$user_id=0)
 	{	
 	  	//默认可以点击下一步
 	 	$msg['error'] = 100;
 	  	//判断是否填写店铺信息	
 	  	if ($user_id==0) {
 	  		 $msg['error'] = 101;
 	  		 $msg['data'] = "请先选择店铺信息";
 	    }
 	  	//判断手机号格式是否正确
 	  	if ($tel!='') {
	 	  	if (!preg_match("/^(1(([35][0-9])|(47)|[8][0126789]))\d{8}$/",$tel)) {
		  		 $msg['error'] = 101;
		  		 $msg['data'] = "手机号格式不对";
		 	 }
 	  	}
	    return $msg;	
 	}

 	/**
     * 上传资质
     * @param array $param (sKey)
     * @return array $data
     * @Author yangmm 2017-12-08
     */
    public function managerQualifications($param)
    {   
    	global $db,$log;
		$CompanyID=$db->get_row("SELECT ClientCompany FROM ".DB_DATABASE.".".DATATABLE."_order_client WHERE ClientID=".$param['user_id']."");
	    //文件存放目录
	   	$dir=$_SERVER['DOCUMENT_ROOT']."/resource/".$CompanyID['ClientCompany'];
       	if(!is_dir($dir)){
       		mkdir($dir,0777);
            chmod($dir,0777);
       	}
       	if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $param['param'], $result)){
                $type = $result[2];
                if(in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))){
                	  //唯一的文件名
                	  $file= date("d_His")."_".$this->currentTimeMillis()."".rand(10,99).'.'.$type;
                	  //文件路径
                      $new_file = $dir.'/'.$file;  
                    if(file_put_contents($new_file,base64_decode(str_replace($result[1],'',$param['param'])))){
                    			//入库的图片路径
                    			$path=$CompanyID['ClientCompany'].'/'.$file;  
	                            $data['code']=100;
	                            $data['path']=$path;    
                    }else{

                                $data['code']=103;
                                $data['message']='上传失败';
                    }
                }else{
                     	   //文件类型错误
                            $data['code']=103;
                            $data['message']='上传类型错误';
                }
            }else{
               				//文件错误
                            $data['code']=103;
                            $data['message']='文件错误';
            }
            return $data;
    }

    public function currentTimeMillis()
    {
		list($usec, $sec) = explode(" ",microtime()); 
		return substr($usec, 2, 3); 
    }

	/**
     * 提交开户
     * @param array $param 
     * @return array $rdata
     */
    public function managerSubmitAccount($param)
    {
    	global $db,$log;
    	if (empty($param['IDCard']) || empty($param['BusinessCard']) || empty($param['IDLicenceCard']) || empty($param['GPCard'])) {
    		$rData['codes'] = 101;
    		$rData['rdata'] = "输入框不能为空";
    	}
    	$select = "select CompanyID from ".DB_DATABASEU.DATATABLE."_three_sides_merchant where MerchantID=".$param['ClientID']."";
        $selectID = $db->get_row($select);
        $order_client=$db->get_row("select ClientCompany,ClientCompanyName,ClientTrueName from ".DB_DATABASE.'.'.DATATABLE."_order_client where ClientID = ".$param['ClientID']."");
        // 查询 判断是否已存在    
        if(empty($selectID)){  	
            $addSql = "insert into ".DB_DATABASEU.DATATABLE."_three_sides_merchant (CompanyID,MerchantID,BusinessName,BusinessCard,BusinessCardImg,IDLicenceImg,GPImg,TureUserName,UserPhone,IDCard,IDCardImg,IDLicenceCard,GPCard)values(".$order_client['ClientCompany'].",".$param['ClientID'].",'".$order_client['ClientCompanyName']."','".$param['BusinessCard']."','".$param['BusinessCardImg']."','".$param['IDLicenceImg']."','".$param['GPImg']."','".$order_client['ClientTrueName']."','".$param['ClientName']."',".$param['IDCard'].",'".$param['IDCardImg']."','".$param['IDLicenceCard']."','".$param['GPCard']."')";
        }else{
            $addSql = "update ".DB_DATABASEU.DATATABLE."_three_sides_merchant set BusinessName='".$order_client['ClientCompanyName']."',BusinessCard='".$param['BusinessCard']."',BusinessCardImg='".$param['BusinessCardImg']."',IDLicenceImg ='".$param['IDLicenceImg']."',GPImg='".$param['GPImg']."',TureUserName='".$order_client['ClientTrueName']."',UserPhone='".$param['ClientName']."',IDCard='".$param['IDCard']."',IDCardImg='".$param['IDCardImg']."',IDLicenceCard='".$param['IDLicenceCard']."',GPCard='".$param['GPCard']."' where CompanyID = ".$selectID['CompanyID']." and MerchantID=".$param['ClientID']; 
        }  
        $res=$db->query("UPDATE ".DB_DATABASE.'.'.DATATABLE."_order_client SET C_Flag='D' where ClientID =".$param['ClientID']."");
		$is=$db->query($addSql);
		if ($is&&$res) {
			$rData['codes'] = 100;
			$rData['rdata'] = "开户成功";
		}
		return $rData;
    }

}
?>