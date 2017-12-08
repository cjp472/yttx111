<?php
// +----------------------------------------------------------------------
// | Describe: 接口控制器
// +----------------------------------------------------------------------
// | Author: seekfor <seekfor@gmail.com>
// +----------------------------------------------------------------------
// | Date: 2015-03-19
// +----------------------------------------------------------------------
class controller{

	var $fp = array('+','/','=','_');
	var $rp = array('-','|','DHB',' ');
	
	/**
    * 检查版本，升级，升级提示
	*@param array $param(inStr) 字符串
	*@return array $rdata(rStatus,error,sKey) 状态，提示信息，key
    *@author seekfor
    */
	public function  getVersion($param){
		global $log;
		//$log->logInfo('server', $_SERVER);
		//临时不更新
		$rdata['rStatus'] = 101;
		$rdata['error']   = '请输入设备类型';
		return $rdata;

		if(empty($param['source'])){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '请输入设备类型';
		}else{
			include_once ("./version.php");
			$rdata['rStatus'] = 100;
			$rdata['version'] = $varsionArr[$param['source']];
			$rdata['notice']  = $varsionArr['Notice'];
			$rdata['inAudit'] = $varsionArr['InAudit'];
			$rdata['error']   = '';
		}
		return $rdata;
	}
	
	/**
    * 生成sha1
	*@param array $param(inStr) 字符串
	*@return array $rdata(rStatus,error,sKey) 状态，提示信息，key
    *@author seekfor
    */
	public function  getSha1($param){
		
		$rdata['rStr'] = sha1($param['inStr']);
		return $rdata;
	}
	

	/**
    * 验证，获取Key
	*@param array $param(SerialNumber,Password) 用户名,密码
	*@return array $rdata(rStatus,error,sKey) 状态，提示信息，key
    *@author seekfor
    */
	public function  getTokenValue($param){
		global $db,$log;
		//通过微信获取
		$loginType = 'Mobile';
		$sqltemp = '';
		if(!empty($param['openId'])){
			$wsql = "select WeiXinID,UserID,UserType,CompanyID from ".DB_DATABASEU.DATATABLE."_order_weixin where WeiXinID = '".$param['openId']."' and UserType='C' order by ID desc limit 0,1";
			$winfo = $db->get_row($wsql);
			if(!empty($winfo['CompanyID'])) $sqltemp = " and ClientCompany=".$winfo['CompanyID']." ";
			//独立微信
			if(!empty($param['wid'])){
				$comnameinfo = $db->get_row ("select CompanyName,CompanySigned,CompanyPrefix from ".DB_DATABASEU.DATATABLE."_order_company  where CompanyID=".intval($param['wid'])." limit 0,1" );
				$rdata['rData']   = $comnameinfo;
				
			}
			if(empty($winfo['WeiXinID'])){
				
				$rdata['rStatus'] = 101;
				$rdata['error']   = '请先绑定微信账号';
				return $rdata;
			}else{
				$sqlru = "select ClientID,ClientCompany as CompanyID,ClientName,ClientFlag,TokenValue from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientID = ".$winfo['UserID']." ".$sqltemp." limit 0,1";
			}
			$loginType = 'WeiXin';
			if($param['weixin'] == 'qy') $loginType = 'qyWeiXin';
		}else{
			$loginType = $param['loginFrom'];
			if(!empty($param['wid'])){
				$sqltemp = " and ClientCompany=".intval($param['wid'])." ";
			}
			
			$param['Username'] = trim($param['Username']);
			$param['Password'] = trim($param['Password']);
			//通过帐号密码
			if(empty($param['Username']) || empty($param['Password'])){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '帐号密码不能为空';
				return $rdata;
			}
			
			if(!is_filename($param['Username']) || strlen($param['Username']) < 3 || strlen($param['Username']) > 30){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '请输入正确的帐号';
				return $rdata;
			}
			if(!is_filename($param['Password']) || strlen($param['Password']) < 3 || strlen($param['Password']) > 32){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '请输入正确的密码';
				return $rdata;
			}
			
			$param['Username'] = strtolower($param['Username']);
			$param['Password'] = strtolower($param['Password']);
			
			if(is_phone($param['Username'])){
				$loginwh = " ClientMobile = '".$param['Username']."' ";
			}else{
				$loginwh = " ClientName = '".$param['Username']."' ";
			}
			$sqlru = "select ClientID,ClientCompany as CompanyID,ClientName,ClientFlag,TokenValue from ".DB_DATABASEU.DATATABLE."_order_dealers where ".$loginwh." and ClientPassword = '".$param['Password']."' ".$sqltemp." limit 0,1";			
		}
		//$rdata['sqlu'] = $sqlru;
		$ruinfo = $db->get_row($sqlru);
		if(empty($ruinfo['ClientID'])){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '帐号密码不匹配';
		}elseif($ruinfo['ClientFlag'] == '1'){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '您的帐号已禁用，请联系BMB平台';
		}elseif($ruinfo['ClientFlag'] == '9'){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '帐号审核中，请耐心等待!';
		}else{
			//删除缓存
			//if(!empty($ruinfo['TokenValue'])) store_cache($ruinfo['TokenValue'],'');	
			
			//公司表验证
			$ucinfo = $db->get_row("select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyContact,CompanyMobile,CompanyLogo,CompanyFlag,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$ruinfo['CompanyID']." limit 0,1");
			$tmpArr['CompanyID'] 		= $ucinfo['CompanyID'];
			$tmpArr['CompanyName'] 		= $ucinfo['CompanyName'];
			$tmpArr['CompanySigned'] 	= $ucinfo['CompanySigned'];
			
			$csinfo = $db->get_row("select CS_ID,CS_Company,CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber from ".DB_DATABASEU.DATATABLE."_order_cs where CS_Company = ".$ruinfo['CompanyID']." limit 0,1");
			if($ucinfo['CompanyFlag'] == "1"){
				$rdata['rStatus'] = 101;
				$rdata['error']   = '此药商已锁定，请联系BMB平台';
			}
// 			elseif(time() > (strtotime($csinfo['CS_EndDate'])+60*60*24)){
// 				$rdata['rStatus'] = 101;
// 				$rdata['error']   = '此供货商帐号已到期，暂停使用，请与供货商联系';
// 			}
			else{
				if(empty($ucinfo['CompanyDatabase'])) $sdatabase = DB_DATABASE.'.'; else $sdatabase = DB_DATABASE."_".$ucinfo['CompanyDatabase'].'.';
				
				$cinfo = $db->get_row("select ClientName,ClientCompanyName,ClientTrueName from ".$sdatabase.DATATABLE."_order_client where ClientID = ".$ruinfo['ClientID']." limit 0,1");
				//$tmpArr['sqlc'] = "select ClientName,ClientCompanyName,ClientTrueName from ".$sdatabase.DATATABLE."_order_client where ClientID = ".$ruinfo['ClientID']." limit 0,1";
				if(!empty($param['backClientID'])) $tmpArr['ClientID'] 			= $ruinfo ['ClientID'];				
				$tmpArr['ClientName'] 			= $cinfo['ClientName'];
				$tmpArr['ClientCompanyName'] 	= $cinfo['ClientCompanyName'];
				$tmpArr['ClientTrueName'] 		= $cinfo['ClientTrueName'];
				
				$rdata['rStatus'] = 100;
				$rdata['error']   = '';
				if(empty($param['ip'])) $param['ip'] = RealIp();
				$token = md5 ( API_KEY.$ruinfo['ID'].$ruinfo['ClientName'].time());
				$db->query ( "update ".DB_DATABASEU.DATATABLE."_order_dealers set LoginCount=LoginCount+1,LoginDate=".time().",LoginIP='".$param['ip']."', TokenValue='".$token."' where ClientID=" . $ruinfo ['ClientID'] . " limit 1" );
				//$log->logInfo('getTokenValue upsql', "update ".DB_DATABASEU.DATATABLE."_order_dealers set LoginCount=LoginCount+1,LoginDate=".time().",LoginIP='".$param['ip']."', TokenValue='".$token."' where ClientID=" . $ruinfo ['ClientID'] . " limit 1");
				$rdata['sKey']   = $token;
				$rdata['rData']  = $tmpArr;
				
				$db->query("insert into ".DB_DATABASEU.DATATABLE."_order_login_client_log(LoginCompany,LoginClient,LoginName,LoginIP,LoginDate,LoginUrl) values(".$ruinfo['CompanyID'].",".$ruinfo['ClientID'].",'".$ruinfo['ClientName']."','".$param['ip']."',".time().",'".$loginType."')");
				
			}
		}
		//$log->logInfo('getTokenValue return', $rdata);
		return $rdata;
	}
	
	/**
	 * @desc 体验登录
	 * @param array $param (industry)
	 * @return array $rdata
	 */
	public function getTiYan($param){
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
				$cinfo = $db->get_row("select ClientID,ClientName,ClientPassword from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientCompany=".$companyInfo['CompanyID']." and ClientFlag = 0 order by ClientID asc limit 0,1 ");
			}

			if(empty($cinfo['ClientID']) ) {
				$rdata['rStatus'] = 101;
				$rdata['error']   = '行业数据找不到，请联系客服!';
			}else{
				$tmpParam['loginFrom'] = $param['loginFrom'];
				$tmpParam['Clientid']  = $cinfo['ClientID'];
				$tmpParam['Username']  = $cinfo['ClientName'];
				$tmpParam['Password']  = $cinfo['ClientPassword'];
				
				$backData = self::getTokenValue($tmpParam);
				// 标识这个公司已经使用
				if($backData['rStatus']  == 100){
					$db->query("update ".DB_DATABASEU.DATATABLE."_order_company set IsUse = 1, LoginIP = '{$param['ip']}' where CompanyID = {$companyInfo['CompanyID']} ");
				}
				//$backData['cinfo'] = $cinfo;
				return $backData;
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

				$cinfo = $db->get_row ( "select d.ClientID,c.CompanyID,c.CompanyName,c.CompanySigned,c.CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_dealers d inner join ".DB_DATABASEU.DATATABLE."_order_company c ON d.ClientCompany=c.CompanyID where d.TokenValue='".$param."' limit 0,1" );
				if(empty($cinfo['CompanyID'] )) {
					$rdata['rStatus'] = 119;
					$rdata['error']   = '登录超时，请重新登录！';
				}else{
					$rdata['rStatus'] 		= 100;
					$rdata['ClientID']    	= $cinfo['ClientID'];
					$rdata['CompanyID']   	= $cinfo['CompanyID'];
					$rdata['CompanyName']   = $cinfo['CompanyName'];
					$rdata['CompanySigned']   = $cinfo['CompanySigned'];
					if(empty($cinfo['CompanyDatabase'])) $rdata['Database'] = DB_DATABASE.'.'; else $rdata['Database'] = DB_DATABASE."_".$cinfo['CompanyDatabase'].'.';
				
					$clientInfo = $db->get_row("select ClientID,ClientLevel,ClientName,ClientCompanyName,ClientNO,ClientTrueName,ClientShield,ClientSetPrice,ClientPercent,ClientBrandPercent,ClientPay,ClientConsignment from ".$rdata['Database'].DATATABLE."_order_client where ClientID=".intval($cinfo['ClientID'])." limit 0,1");
					if(empty($clientInfo['ClientSetPrice'])) $clientInfo['ClientSetPrice'] = 'Price1';
					$rdata['ClientInfo']	= $clientInfo;
					
					store_cache($param,$rdata);					
				}
				return $rdata;
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
            $rdata['error'] = '参数错误';
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
     * @desc 发送短信
     * @param $param (CompanyID)
     * @return array
     */
    public function setSendSms($param){
    	global $db,$log;
    	$rdata = array();

    	if(empty($param['CompanyID'])){
    		$rdata['rStatus'] = 110;
    		$rdata['error'] = '参数错误';
    	}else{
    		$statusCode = false;
    		//配置项
    		$valuearr = self::getproductset('sms',$param);
    		if(!empty($valuearr) && in_array($param['sid'], $valuearr))
    		{ 
    			if(!empty($valuearr['Mobile']['FinancePhone']) && $param['sid'] == "3") $sendphone = $valuearr['Mobile']['FinancePhone'];
    			if(!empty($valuearr['Mobile']['LibaryPhone']) && $param['sid'] == "6")  $sendphone = $valuearr['Mobile']['LibaryPhone'];
    			if(empty($sendphone))
    			{
    				if(!empty($valuearr['Mobile']['MainPhone']))
    				{
    					$sendphone = $valuearr['Mobile']['MainPhone'];
    				}
    			}  
    			//号码不为空
                if(!empty($sendphone)) {
                    $phone_list = array();
                    foreach(explode(",",$sendphone) as $phone) {
                        if(is_phone($phone)) {
                            $phone_list[] = $phone;
                        }
                    }

                    if(!empty($phone_list)) {
                        include_once ("./WebService/include/Client.php");
                        include_once ("./soap.inc.php");
                        $cs_sql = "SELECT CS_SmsNumber FROM ".DB_DATABASEU.DATATABLE."_order_cs WHERE CS_Company=".$param['CompanyID'];
                        $cs_num = (int) $db->get_var($cs_sql);

                        $client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
                        $client->setOutgoingEncoding("UTF-8");
                        $client->login();
                        foreach($phone_list as $phone) {
                            if($cs_num <= 0) {
                                break;
                            }
                            $mobilearr    = array($phone);
                            $statusCode     = $client->sendSMS($mobilearr,$param['message']);
                            $insql = "insert into ".$param['Database'].DATATABLE."_order_sms_post(PostCompany,PostUser,PostClient,PostDate,PostPhone,PostContent,PostFlag) values(".$param['CompanyID'].",'".$param['ClientInfo']['ClientName']."',".$param['ClientID'].",".time().",'".$phone."','".$param['message']."','".$statusCode."')";
                            $isu = $db->query($insql);
                            if($isu) $db->query("update ".DB_DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber-1 where CS_Company=".$param['CompanyID']."");
                            $cs_num--;
                        }
                    }

                }
    			if(false && !empty($sendphone) && is_phone($sendphone)){//老代码
    				$cs_sql = "SELECT CS_Number,CS_BeginDate,CS_EndDate,CS_SmsNumber,CS_UpDate,CS_UpdateTime FROM ".DB_DATABASEU.DATATABLE."_order_cs WHERE CS_Company=".$param['CompanyID'];
    				$csInfo = $db->get_row($cs_sql);
    				
    				if($csInfo['CS_SmsNumber'] > 0){
    				
    					include_once ("./WebService/include/Client.php");
    					include_once ("./soap.inc.php");
    					
    					$client = new Client($gwUrl,$serialNumber,$password,$sessionKey,$proxyhost,$proxyport,$proxyusername,$proxypassword,$connectTimeOut,$readTimeOut);
    					$client->setOutgoingEncoding("UTF-8");
    					
    					$mobilearr[]    = $sendphone;
    					$statusCode2    = $client->login();
    					$statusCode     = $client->sendSMS($mobilearr,$param['message']);
    					$insql = "insert into ".$param['Database'].DATATABLE."_order_sms_post(PostCompany,PostUser,PostClient,PostDate,PostPhone,PostContent,PostFlag) values(".$param['CompanyID'].",'".$param['ClientInfo']['ClientName']."',".$param['ClientID'].",".time().",'".$sendphone."','".$param['message']."','".$statusCode."')";	
    					$isu = $db->query($insql);
    					if($isu) $db->query("update ".DB_DATABASEU.DATATABLE."_order_cs set CS_SmsNumber=CS_SmsNumber-1 where CS_Company=".$param['CompanyID']."");
						
    					//$log->logInfo('sms return',$insql);
	
    				}	
    			}
    		}    		
    		
    		if($statusCode !== 0){
    			$rdata['rStatus']	= 101;
    			$rdata['error']		= '发送不成功';
    		}else{
    			$rdata['rStatus']	= 100;
    			$rdata['error']		= '发送成功';
    		}
    	}
    	//$log->logInfo('getCsInfo return',$rdata);
    	return $rdata;
    }

    
    /**
     * 获取商品列表
     *@param array $param(sKey,parentId,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getGoodsSort($param){
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
    			 
    			$sqlnoid = '';
    			if(!empty($cidarr['ClientInfo']['ClientShield'])) $sqlnoid .= " and SiteID NOT IN (".$cidarr['ClientInfo']['ClientShield'].")";
    			if(isset($param['parentId'])) $sqlnoid .= " and ParentID=".intval($param['parentId'])." ";

    			$sql_l  = "select SiteID,ParentID,SiteName from ".$sdatabase.DATATABLE."_order_site where  CompanyID=".$cidarr['CompanyID']." ".$sqlnoid." order by ParentID asc, SiteOrder desc,SiteID asc ";
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
     * 获取商品品牌
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getGoodsBrand($param){
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
     * 获取商品列表
     *@param array $param(sKey,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getGoodsList($param){
    	global $db,$log;   
    	include (SITE_ROOT_PATH."/arr_data.php");    	
    
    	if (empty ( $param['sKey'] )){
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
    		//$log->logInfo('getGoodsListCid', $cidarr);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];
    			if(!empty($cidarr['ClientInfo']['ClientBrandPercent'])) $brandPercent = unserialize($cidarr['ClientInfo']['ClientBrandPercent']);
    			
    			$smsg = $orderbymsg = '';
    			
    			if(empty($param['orderBy']) || $param['orderBy'] == "0"){
    				$orderbymsg = " order by i.OrderID DESC, i.ID DESC";
    			}elseif($param['orderBy'] == "1"){
    				$orderbymsg = " order by i.Price2 DESC";
    			}elseif($param['orderBy'] == "2"){
    				$orderbymsg = " order by i.Price2 ASC";
    			}elseif($param['orderBy'] == "3"){
    				$orderbymsg = " order by i.ID ASC";
    			}elseif($param['orderBy'] == "4"){
    				$orderbymsg = " order by i.Count DESC, i.ID DESC";
    			}
    			
    			if(!empty($param['commendId'])){
    				if($param['commendId'] == 10){
    					$smsg .= " and exists (select FavContent from ".$sdatabase.DATATABLE."_order_fav where FavContent=i.ID and FavCompany=".$cid." and FavClient=".$cidarr['ClientID'].") ";
    				}elseif($param['commendId'] == 11){
    					$smsg .= " and exists (select ContentID from ".$sdatabase.DATATABLE."_order_cart where ContentID=i.ID and CompanyID=".$cid." and ClientID=".$cidarr['ClientID'].") ";
    				}else{	
    					$smsg .= " and i.CommendID = ".intval($param['commendId'])." ";	
    				}
    			}
    			
    			if(!empty($param['brandId'])) $smsg .= " and i.BrandID = ".intval($param['brandId'])." ";
    			
    			$trimC = trim($param['kw']);
    			$kwn   = str_replace(' ','%', $param['kw']);
    			if(strlen($trimC)){//存在搜索关键字时
        			if(strpos($kwn,'%')){
        				$temsql = '';
        				$kwnarr = explode('%',$kwn);
        				foreach($kwnarr as $v){
        					if(!empty($temsql)){
        						$temsql .= " AND ";
        					} 
        					$temsql .= " i.Name like '%".$v."%' ";
        				}
        				$smsg  .= " AND ((".$temsql.") OR (i.Pinyi like '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%'))";
        			}else{
        				$smsg  .= " and (i.Name like '%".$kwn."%' OR i.Pinyi like '%".$kwn."%' OR i.Coding like '%".$kwn."%' OR i.Barcode like '%".$kwn."%') ";
        			}   	
    			}
    			
    			if(empty($param['siteId'])) $param['siteId'] = '';
    			$sidsqlmsg = self::getShieldSite($param['siteId'], $cidarr); //屏蔽分类、商品
    			if($sidsqlmsg != "empty") $smsg .= $sidsqlmsg;

    			$sql_c = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_content_index i
    					LEFT JOIN ".$sdatabase.DATATABLE."_order_site s ON i.SiteID=s.SiteID
    							where i.CompanyID=".$cid." and i.FlagID=0 ".$smsg;
    			$sql  = "select i.ID,i.CommendID,i.Count,i.SiteID,i.BrandID,i.Name,i.Coding,i.Barcode,i.Price1,i.Price2,i.Price3,i.Units,i.Casing,i.Picture,i.Color,i.Specification,b.BrandName,(case when (n.OrderNumber<0) then 0 else n.OrderNumber end) as OrderNumber
    							from ".$sdatabase.DATATABLE."_order_content_index  i
    							LEFT JOIN ".$sdatabase.DATATABLE."_order_site s ON i.SiteID=s.SiteID
    							LEFT JOIN ".$sdatabase.DATATABLE."_order_brand b ON i.BrandID=b.BrandID
								LEFT JOIN ".$sdatabase.DATATABLE."_order_number n ON i.ID=n.ContentID
    							where i.CompanyID=".$cid." and i.FlagID=0 ".$smsg." ".$orderbymsg;
    			//and n.CompanyID =".$cid."
   			   	//$log->logInfo('getGoodsListCid_sql', $sql);			
    			$sql .= " limit ".$param['begin'].",".intval($param['step']);
    			
    			$countData = $db->get_row($sql_c);
    			$listData  = $db->get_results ( $sql );
//     			$rdata['rSql'] = $sql;
    
    			if($countData['allrow'] < 1) {
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '没有符合条件的数据';
    			}else{
    			    for($i=0;$i<count($listData);$i++){
//     			    	$listData[$i]['Name'] = htmlspecialchars($listData[$i]['Name']);
    			    	
    			    	$listData[$i]['Name'] = htmlspecialchars_decode($listData[$i]['Name']);
    			    	$listData[$i]['Name'] = str_replace(array('<', '>'), array('＜', '＞'), $listData[$i]['Name']);
    			    	
    			    	$listData[$i]['Coding'] = html_entity_decode($listData[$i]['Coding'], ENT_QUOTES,'UTF-8');
						$listData[$i]['CommendName'] = $producttypearr[$listData[$i]['CommendID']];
						if(!empty($listData[$i]['Picture']) && $cid == "1" && substr($listData[$i]['Picture'],0,1)!="1") $listData[$i]['Picture'] = "1/".$listData[$i]['Picture'];
						if(!empty($listData[$i]['Picture'])) $listData[$i]['Picture'] = RESOURCE_PATH.$listData[$i]['Picture'];
						$idarr[] = $listData[$i]['ID'];
						if(empty($cidarr['ClientInfo']['ClientSetPrice'])) $cidarr['ClientInfo']['ClientSetPrice'] = 'Price1';
						$listData[$i]['Price'] = $listData[$i][$cidarr['ClientInfo']['ClientSetPrice']];
						
						$listData[$i]['Pencent'] = '10.0';
						if($listData[$i]['CommendID'] == "2"){
							$listData[$i]['Pencent'] = '10.0';
						}else{
							if(!empty($listData[$i]['BrandID']) && !empty($brandPercent[$listData[$i]['BrandID']])){
								$listData[$i]['Pencent'] = $brandPercent[$listData[$i]['BrandID']];
							}else{
								$listData[$i]['Pencent'] = $cidarr['ClientInfo']['ClientPercent'];
							}
						}						
						
						$price3 = setprice3($listData[$i]['Price3'], $cidarr['ClientID'], $cidarr['ClientInfo']['ClientLevel']);
						if(!empty($price3)){
							$listData[$i]['Price'] = $price3;
						}else{
							$listData[$i]['Price'] = $listData[$i]['Price'] * $listData[$i]['Pencent'] / 10;
						}
						if(!empty($listData[$i]['Color']) || !empty($listData[$i]['Specification']))
						{
							$listData[$i]['cs'] = "Y";
						}else{
							$listData[$i]['cs'] = "N";
						}
						unset($listData[$i]['Price1'],$listData[$i]['Price2'],$listData[$i]['Price3']);
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


    
    /**
     * 获取商品明细
     *@param array $param(sKey,contentId) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getGoodsContent($param){
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
    			$cid		= $cidarr['CompanyID'];
    			$sdatabase  = $cidarr['Database'];

    			if(!empty($cidarr['ClientInfo']['ClientBrandPercent'])) $brandPercent = unserialize($cidarr['ClientInfo']['ClientBrandPercent']);
    			$id 		= intval($param['contentId']);    			 
    			$smsg = '';     			

    			$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='product' limit 0,1";
    			$result	= $db->get_row($sql_l);
    			if(!empty($result['SetValue'])) $valuearr = unserialize($result['SetValue']);
    			if(!empty($valuearr)) $setarr = $valuearr; else $setarr = null;
    			
    			$tmp['price1Name'] = $setarr['product_price']['price1_name'] ? $setarr['product_price']['price1_name'] : "价格一";
    			$tmp['price2Name'] = $setarr['product_price']['price2_name'] ? $setarr['product_price']['price2_name'] : "价格二";
    			
    		    if(!empty($setarr['product_number'])){
    				$pn  = $setarr['product_number'];
	    		}else{
	    			$pn  = 'off';
	    		}
	    		if(!empty($setarr['product_negative'])){
	    			$png  = $setarr['product_negative'];
	    		}else{
	    			$png  = 'off';
	    		}
	    		if(!empty($setarr['product_number_show']))
	    		{
	    			$pns  = $setarr['product_number_show'];
	    		}else{
	    			$pns  = 'off';
	    		}
	    		if($pn == 'on' && $png == 'off') $rSet['controller'] = 'Y'; else $rSet['controller'] = 'N';
	    		if($pn == 'on' && $pns == 'on')  $rSet['show'] = 'Y'; else $rSet['show'] = 'N';
    			//屏蔽ID
    			$shielddata = $db->get_row("select count(*) as crow from ".$sdatabase.DATATABLE."_order_shield where CompanyID=".$cid." and ClientID=".$cidarr['ClientID']." and ContentID=".$id);
    			if(!empty($shielddata['crow']))
    			{
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '没有符合条件的数据';
    			}else{
    				
    				$sql  = "select ID,CommendID,BrandID,Name,Coding,Barcode,Units,Casing,Picture,Color,Specification,Model,Price1,Price2,Price3 from ".$sdatabase.DATATABLE."_order_content_index where ID=".$id." and CompanyID=".$cid." and FlagID=0 ";
    				$sql .= " limit 1";
    				$result['index'] = $db->get_row($sql);	
    				
    				//查询出品牌
    				$brSql = "select BrandName,BrandID from ".$sdatabase.DATATABLE."_order_brand where CompanyID=".$cid." and BrandID=".$result['index']['BrandID'];
    				$brand = $db->get_row($brSql);
    				
    				$result['index']['BrandName'] = $brand['BrandName'];
    				
    			    $result['index']['Name'] = htmlspecialchars_decode($result['index']['Name']);
    				$result['index']['Name'] = str_replace(array('<', '>'), array('＜', '＞'), $result['index']['Name']);
    				//$rdata['rSql'] = $sql;
    				if(empty($result['index'])){
    					$rdata['rStatus'] = 101;
    					$rdata['error']   = '没有符合条件的数据';
    					return $rdata;				
    				}else{

    					$sql_c   = "select Content,Package,FieldContent from ".$sdatabase.DATATABLE."_order_content_1 where ContentIndexID = ".$result['index']['ID']." and CompanyID=".$cid." limit 0,1";
    					$result['content'] = $db->get_row($sql_c);
    					
    					if(!empty($result['content']['Content'])){
    						$result['content']['Coding'] = html_entity_decode($result['content']['Coding'], ENT_QUOTES,'UTF-8');
    						$result['content']['Content'] = html_entity_decode($result['content']['Content'], ENT_QUOTES,'UTF-8');
    						$result['content']['Content'] = _striptext($result['content']['Content']); //格式化内容
    						$result['content']['Content'] = htmlentities($result['content']['Content'], ENT_QUOTES,'UTF-8');
    					}
    					$pencent = '10.0';
    					if($result['index']['CommendID'] == "2"){
    						$pencent = '10.0';
    					}else{
    						if(!empty($result['index']['BrandID']) && !empty($brandPercent[$result['index']['BrandID']])){
    							$pencent = $brandPercent[$result['index']['BrandID']];
    						}else{
    							$pencent = $cidarr['ClientInfo']['ClientPercent'];
    						}
    					}
    					if(empty($cidarr['ClientInfo']['ClientSetPrice'])) $cidarr['ClientInfo']['ClientSetPrice'] = 'Price1';
    					$result['index']['Price'] = $result['index'][$cidarr['ClientInfo']['ClientSetPrice']];;
    					
    					$price3 = setprice3($result['index']['Price3'], $cidarr['ClientID'], $cidarr['ClientInfo']['ClientLevel']);
    					if(!empty($price3)){
    						$result['index']['Price'] = $price3;
    					}else{
    						$result['index']['Price'] = $result['index']['Price'] * $pencent / 10;
    					}
    					if($setarr['product_price']['price1_show'] == 'on'){
    						$result['index']['price_name']  = $tmp['price1Name'];
    						$result['index']['show_price'] = $result['index']['Price1'];
    					}elseif($setarr['product_price']['price2_show'] == 'on'){
    						$result['index']['price_name']  = $tmp['price2Name'];
    						$result['index']['show_price'] = $result['index']['Price2'];
    					}
    					
    					if(!empty($result['index']['Picture'])){
    						$result['index']['Picture'] 	= RESOURCE_PATH.$result['index']['Picture'];
    						$result['index']['PictureBig'] 	= str_replace("thumb_","img_",$result['index']['Picture']);
    					}
    					
    					if(!empty($result['content']['FieldContent'])){
    						
    						$farr = unserialize($result['content']['FieldContent']);
    						$setfield = self::getproductset('field',$cidarr);

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
    					
    					$sql_r = "select Name,Path from ".$sdatabase.DATATABLE."_order_resource where CompanyID=".$cid." and IndexID = ".$result['index']['ID']." order by OrderID asc";
    					$pdata = $db->get_results($sql_r);
    					if(!empty($pdata)){    
    						foreach($pdata as $v){
    							$result['content']['PicArray'][] = RESOURCE_PATH.$v['Path']."img_".$v['Name'];
    						}
    						
    						$result['index']['PictureBig'] = RESOURCE_PATH.$pdata[0]['Path']."img_".$pdata[0]['Name'];
    					}
    					
    					if(empty($result['content']['PicArray']) && $result['index']['PictureBig']) $result['content']['PicArray'][] = $result['index']['PictureBig'];
    					
    					//添加默认图片
    					if(empty($result['content']['PicArray'])) $result['content']['PicArray'][] = '../img/default_pic.png';
    					
    					unset($result['index']['Price1'],$result['index']['Price2'],$result['index']['Price3']);

    					//库存
    					if($pn == 'on'){
    						$sqlnumber = "select ContentID,OrderNumber,ContentNumber from ".$sdatabase.DATATABLE."_order_number where CompanyID=".$cid." and ContentID = ".$result['index']['ID']." limit 1";
    						$numberarr	= $db->get_row($sqlnumber);
    						if(empty($numberarr['OrderNumber']) || $numberarr['OrderNumber'] < 0) $result['index']['allLibrary'] = 0; else $result['index']['allLibrary'] = $numberarr['OrderNumber'];
    						//子库存
    						if(!empty($result['index']['Color']) || !empty($result['index']['Specification'])){
    							$sql   = "select ContentID,ContentColor,ContentSpec,OrderNumber,ContentNumber from ".$sdatabase.DATATABLE."_order_inventory_number where  CompanyID=".$cid." and ContentID=".intval($result['index']['ID']);
    							$list_data = $db->get_results($sql);
    							foreach($list_data as $lv){
    								$lv['ContentColor'] = base64_decode(str_replace($this->rp,$this->fp,$lv['ContentColor']));
    								$lv['ContentSpec']  = base64_decode(str_replace($this->rp,$this->fp,$lv['ContentSpec']));
    								if($lv['ContentColor'] == '统一') $lv['ContentColor']  = '';
    								if($lv['ContentSpec']  == '统一')  $lv['ContentSpec']  = '';
    								$lkey = md5($result['index']['ID'].$lv['ContentColor'].$lv['ContentSpec']);
    								$result['index']['library'][$lkey] = $lv['OrderNumber'];
    							}	
    						}
    					}    					
    					$result['index']['controllerLibrary'] = $rSet['controller'];
    					$result['index']['showLibrary'] = $rSet['show'];    
    					
    					if(!empty($result['content'])) $contentData = array_merge($result['index'], $result['content']); else $contentData = $result['index'];
    					
    					$sql_fav = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_fav where FavContent=".$result['index']['ID']." and FavClient=".$cidarr['ClientID']." and FavCompany = ".$cid."";
    					$fav	= $db->get_row($sql_fav);
    					if($fav['allrow'] > 0 ){
    						$contentData['isFav'] = 'Y';
    					}else{
    						$contentData['isFav'] = 'N';
    					}
    					
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

    //产品设置
	protected function getproductset($ty='field',$cidarr)
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
    
    //屏蔽商品分类
    protected function getShieldSite($siteid,$cidarr)
    {
    	global $db,$log;  
    	$smsg = '';
    	$sidsqlmsg = '';
    
    	if(!empty($cidarr['ClientInfo']['ClientShield']))
    	{
    		$nsmsg = '';
    		$nomsg = '';
    		$sitepdata = $db->get_col("select SiteNO from ".$cidarr['Database'].DATATABLE."_order_site where CompanyID=".$cidarr['CompanyID']." and (SiteID IN (".$cidarr['ClientInfo']['ClientShield'].") )");
    		if(!empty($sitepdata))
    		{
    			foreach($sitepdata as $var)
    			{
    				if(empty($nomsg)) $nomsg = " SiteNO LIKE '".$var."%' "; else $nomsg .= " or SiteNO LIKE '".$var."%' ";
    			}
    			$sitesdata2 = $db->get_col("select SiteID from ".$cidarr['Database'].DATATABLE."_order_site where CompanyID=".$cidarr['CompanyID']." and (".$nomsg.")");
    			if(!empty($sitesdata2))  $nsmsg = ",".implode(",",$sitesdata2).",";
    		}
    		if(!empty($nsmsg)) $sidsqlmsg = " and instr('".$nsmsg."', concat(',', i.SiteID, ',') ) = 0 ";  //$sidsqlmsg = " and i.SiteID NOT IN (".$nsmsg.")";
    	}
    
    	if(!empty($siteid))
    	{
    		if(!empty($nsmsg))
    		{
    			$notinsarr = explode(",",$nsmsg);
    			if(in_array($siteid,$notinsarr))
    			{
    				return 'empty';
    			}
    		}
    		$sortinfo = $db->get_row("SELECT SiteID,ParentID,SiteNO,SiteName FROM ".$cidarr['Database'].DATATABLE."_order_site where CompanyID=".$cidarr['CompanyID']." and SiteID=".$siteid." limit 0,1");
    		//$smsg  = " and s.SiteNO like '".$sortinfo['SiteNO']."%' ";
    		$smsg  = " and instr(s.SiteNO,'".$sortinfo['SiteNO']."') > 0 ";
    	}else{
    		$smsg = $sidsqlmsg;
    	}
    
    	//屏蔽商品
    	$shielddata = $db->get_col("select ContentID from ".$cidarr['Database'].DATATABLE."_order_shield where CompanyID=".$cidarr['CompanyID']." and ClientID=".$cidarr['ClientID']);
    	if(!empty($shielddata))
    	{
    		$shieldmsg = ",".implode(",",$shielddata).",";
    		//$smsg  .= " and i.ID NOT IN ( select ContentID from ".DATATABLE."_order_shield where CompanyID=".$cid." and ClientID=".$_SESSION['cc']['cid']." ) ";
    		$smsg .= " and instr('".$shieldmsg."', concat(',', i.ID, ',') ) = 0";
    	}
    	return $smsg;
    } 
    
	/**
    * 获取订单列表
	*@param array $param(sKey,begin,step) key,起始值，步长
	*@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
    *@author seekfor
    */
	public function getOrderList($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");

		//$log->logInfo('getOrderList', $param);

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
				$smg = '';
				
				if(!empty($param['isCollect']))  $smg .= ' and OrderCollect = 1 ';
				if(isset($param['orderStatus'])  && $param['orderStatus'] != '' ) $smg .= " and OrderStatus = ".intval($param['orderStatus'])." ";
				if(isset($param['orderSendStatus']) && $param['orderSendStatus'] != '') $smg .= " and OrderSendStatus = ".intval($param['orderSendStatus'])." ";				
				if(isset($param['orderPayStatus'])  && $param['orderPayStatus'] != '') $smg .= " and OrderPayStatus = ".intval($param['orderPayStatus'])." ";
				
				if(!empty($param['beginDate']))  $smg .= ' and OrderDate > '.strtotime($param['beginDate'].'00:00:00').' ';
				if(!empty($param['endDate']))    $smg .= ' and OrderDate < '.strtotime($param['endDate'].'23:59:59').' ';
				
				$countData  = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']." ".$smg." ");
								
				$sql  = "select OrderID,OrderSN,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderRemark,OrderTotal,OrderStatus,OrderDate from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']."  ".$smg." ";
				$sql .= " order by OrderID desc ";
				$sql .= " limit ".$param['begin'].",".intval($param['step']);
				
				$oinfo  = $db->get_results ( $sql );
			    if($param['debug']){
                	$rdata['rSql'] = $sql;
            	}
				
				if(empty($oinfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '没有符合条件的数据';
				}else{
                    foreach($oinfo as $key=>$val){
						$oinfo[$key]['OrderSendStatus'] = $send_status_arr[$val['OrderSendStatus']];
						$oinfo[$key]['OrderPayStatus']  = $pay_status_arr[$val['OrderPayStatus']];
                        $oinfo[$key]['OrderStatus']		= $order_status_arr[$val['OrderStatus']];

						$oinfo[$key]['OrderSendType']	= $senttypearr[$val['OrderSendType']];
                        $oinfo[$key]['OrderPayType']	= $paytypearr[$val['OrderPayType']];
                    }
					$rdata['rStatus']	= 100;
					$rdata['rAllTotal'] = $countData['allrow'];
                    $rdata['rTotal']	= count($oinfo);
					$rdata['rData']		= $oinfo;
				}
			}
		}
		//$log->logInfo('getOrderList return', $rdata);
		return $rdata;
	}
	
	/**
	 * 获取待付款订单列表
	 *@param array $param(sKey,begin,step) key,起始值，步长
	 *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
	 *@author seekfor
	 */
	public function getPayOrderList($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");
	
		//$log->logInfo('getPayOrderList', $param);
	
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
				$smg = '';
	
				$smg .= ' and (OrderPayStatus=0 or OrderPayStatus=1 or OrderPayStatus=3) and OrderStatus < 5 ';
				$sql  = "select OrderID,OrderSN,OrderPayStatus,OrderTotal,OrderIntegral,OrderStatus from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']." ".$smg." ";
				$sql .= " limit ".$param['begin'].",".intval($param['step']);
	
				$oinfo  = $db->get_results ( $sql );
				if($param['debug']){
					$rdata['rSql'] = $sql;
				}
	
				if(empty($oinfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '没有符合条件的数据';
				}else{
					foreach($oinfo as $key=>$val){
						$oinfo[$key]['OrderPayStatus']  = $pay_status_arr[$val['OrderPayStatus']];
						$oinfo[$key]['OrderStatus']		= $order_status_arr[$val['OrderStatus']];
					}
					$rdata['rStatus']	= 100;
					$rdata['rTotal']	= count($oinfo);
					$rdata['rData']		= $oinfo;
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
	public function getOrderContent($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");
		//$log->logInfo('getOrderContent', $param);

		if (empty ( $param['sKey'] ) || empty($param['orderId']))
		{
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误';
		}else{
			$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
			if($cidarr['rStatus'] != "100"){
				return $cidarr;
			}else{
				$cid    = $cidarr['CompanyID'];
				$sdatabase = $cidarr['Database'];
				//取单头
				$sql    = "select OrderID,OrderSN,OrderSendType,OrderSendStatus,OrderPayType,OrderPayStatus,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,InvoiceType,InvoiceTax,DeliveryDate,OrderRemark,OrderTotal,OrderIntegral,OrderStatus,OrderDate,OrderType,OrderSaler,OrderFrom from ".$sdatabase.DATATABLE."_order_orderinfo  where  OrderID=".intval($param['orderId'])." and  OrderCompany=".$cid." and OrderUserID=".$cidarr['ClientID']." limit 0,1";
				$oinfo  = $db->get_row ( $sql );
				
					$oinfo['OrderSendStatusName'] 	= $send_status_arr[$oinfo['OrderSendStatus']];
					$oinfo['OrderPayStatusName']  	= $pay_status_arr[$oinfo['OrderPayStatus']];
                    $oinfo['OrderStatusName']		= $order_status_arr[$oinfo['OrderStatus']];

					$oinfo['OrderSendType']		= $senttypearr[$oinfo['OrderSendType']];
                    $oinfo['OrderPayType']		= $paytypearr[$oinfo['OrderPayType']];                    
                    
                    $oinfo['InvoiceType']		= $invoicetypearr[$oinfo['InvoiceType']];
                    $oinfo['OrderType']			= $oinfo['OrderType']=='C'?'客户':'管理员';
                    $oinfo['OrderFrom']			= $oinfo['OrderFrom']=='Compute'?'电脑':'手机';
                    //$rdata['rSql'] = $sql;

				//取明细
				$sqlc   = "select Name,Coding,Units,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent,'c' as conType from ".$sdatabase.DATATABLE."_view_index_cart where OrderID=".$oinfo['OrderID']." and CompanyID=".$cid." ";
				$cinfo  = $db->get_results ( $sqlc );
				$infoall = $cinfo;
                $sqlg   = "select Name,Coding,Units,ContentColor,ContentSpecification,ContentPrice,ContentNumber,'g' as conType from ".$sdatabase.DATATABLE."_view_index_gifts where OrderID=".$oinfo['OrderID']." and CompanyID=".$cid." ";
                $ginfo  = $db->get_results($sqlg);
                
                $submitLog = $db->get_results("select ID,Date,Status,Content from ".$sdatabase.DATATABLE."_order_ordersubmit where CompanyID=".$cid." and OrderID=".$oinfo['OrderID']." ");// tubo 2015-12-18修改，去掉Name
                
			    if(!empty($ginfo)){
                	for($i=0;$i<count($ginfo);$i++){
                		$infoall[] = $ginfo[$i];
                	}	
                }
				if(empty($oinfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '数据为空';
				}else{
					$rdata['rStatus'] = 100;
                    $rdata['rTotal']  = count($infoall);
					$rdata['rData']['header']   = $oinfo;
					$rdata['rData']['body']   	= $infoall;// $cinfo;
					$rdata['rData']['log']   	= $submitLog;
				}
			}
		}
		//$log->logInfo('getOrderContent return', $rdata);
		return $rdata;
	}

	
	/**
	 * 获取发货单列表
	 *@param array $param(sKey,begin,step) key,起始值，步长
	 *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
	 *@author seekfor
	 */
	public function getConsignmentList($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");	
		//$log->logInfo('getConsignmentList', $param);
	
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
				
				$smg = '';				
				if(isset($param['flagId']) && $param['flagId'] != '') $smg = " and ConsignmentFlag = ".intval($param['flagId'])." ";
				
				$orderbymsg = " order by ConsignmentID desc ";
				$sql_c 	= "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_consignment where ConsignmentCompany=".$cid." and ConsignmentClient=".$cidarr['ClientID']."  ".$smsg." ";
				$sql 	= "select ConsignmentID,ConsignmentOrder,ConsignmentNO,ConsignmentMan,ConsignmentDate,InceptAddress,ConsignmentFlag from ".$sdatabase.DATATABLE."_order_consignment where ConsignmentCompany=".$cid." and ConsignmentClient=".$cidarr['ClientID']." ".$smsg." ".$orderbymsg;
				$sql   .= " limit ".$param['begin'].",".intval($param['step']);
				$countData  = $db->get_row($sql_c);
				$oinfo  = $db->get_results ( $sql );
				if($param['debug']){
					$rdata['rSql'] = $sql;
				}
	
				if(empty($oinfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '没有符合条件的数据';
				}else{
					foreach($oinfo as $key=>$val){
						$oinfo[$key]['ConsignmentFlag'] = $incept_arr[$val['ConsignmentFlag']];
					}
					$rdata['rStatus']	= 100;
					$rdata['rAllTotal'] = $countData['allrow'];
					$rdata['rTotal']	= count($oinfo);
					$rdata['rData']		= $oinfo;
				}
			}
		}
		//$log->logInfo('getConsignmentList return', $rdata);
		return $rdata;
	}	
	

	
	/**
	 * 获取发货单明细
	 *@param array $param(sKey,consignmentId) key,订单号
	 *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
	 *@author seekfor
	 */
	public function getConsignmentContent($param){
		global $db,$log;
		include (SITE_ROOT_PATH."/arr_data.php");
		//$log->logInfo('getConsignmentContent', $param);
	
		if (empty ( $param['sKey'] ) || empty($param['consignmentId']))
		{
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误';
		}else{
			$cidarr = $this->getCompanyInfo($param['sKey']); //取公司ID,Database
			if($cidarr['rStatus'] != "100"){
				return $cidarr;
			}else{
				$cid    = $cidarr['CompanyID'];
				$sdatabase = $cidarr['Database'];
				//取单头
				$sql    = "select * from ".$sdatabase.DATATABLE."_order_consignment  where  ConsignmentID=".intval($param['consignmentId'])." and  ConsignmentCompany=".$cid." and ConsignmentClient=".$cidarr['ClientID']." limit 0,1";
				$coninfo  = $db->get_row ( $sql );
				$id = $coninfo['ConsignmentID'];
				$coninfo['ConsignmentFlagName'] 	= $incept_arr[$coninfo['ConsignmentFlag']];
				$coninfo['ConsignmentMoneyType'] 	= $pay_send_arr[$coninfo['ConsignmentMoneyType']];

				$sql_c = "select LogisticsName,LogisticsPinyi from ".$sdatabase.DATATABLE."_order_logistics where LogisticsCompany=".$cid." and LogisticsID=".$coninfo['ConsignmentLogistics']." order by LogisticsID asc limit 0,1";
				$loginfo	= $db->get_row($sql_c);
				$coninfo['LogisticsName'] 	= $loginfo['LogisticsName'];
				$coninfo['LogisticsCode'] 	= $loginfo['LogisticsPinyi'];				
				unset($coninfo['ConsignmentLogistics']);
				//$sql_e = "select * from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." and OrderSN='".$coninfo['ConsignmentOrder']."' and OrderUserID=".$cidarr['ClientID']." limit 0,1";
				//$orderinfo	= $db->get_row($sql_e);
				
				$sql_cart = "select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,l.ContentNumber,i.Coding,i.Casing,i.Units,'c' as conType from ".$sdatabase.DATATABLE."_order_cart c inner join ".$sdatabase.DATATABLE."_order_out_library l on c.ID=l.CartID left join ".$sdatabase.DATATABLE."_order_content_index i ON c.ContentID=i.ID where c.CompanyID=".$cid." and l.ConsignmentID=".$id." and l.ConType='c' order by i.SiteID asc,c.ID asc";
				$result['cartinfo']	   = $db->get_results($sql_cart);
				
				$sql_cart_g = "select c.ID,c.ContentID,c.ContentName,c.ContentColor,c.ContentSpecification,l.ContentNumber,i.Coding,i.Casing,i.Units,'g' as conType from ".$sdatabase.DATATABLE."_order_cart_gifts c inner join ".$sdatabase.DATATABLE."_order_out_library l on c.ID=l.CartID left join ".$sdatabase.DATATABLE."_order_content_index i ON c.ContentID=i.ID where c.CompanyID=".$cid." and l.ConsignmentID=".$id." and l.ConType='g' order by i.SiteID asc,c.ID asc";
				$result['cartinfog']   = $db->get_results($sql_cart_g);
				$infoall = $result['cartinfo'];
				for($i=0;$i<count($result['cartinfog']);$i++){
					$infoall[] = $result['cartinfog'][$i];
				}
				
				if(empty($coninfo)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '数据为空';
				}else{
					$rdata['rStatus'] = 100;
					$rdata['rTotal'] = count($infoall);
					$rdata['rData']['header']   = $coninfo;
					$rdata['rData']['body']   	= $infoall;
				}
			}
		}
		//$log->logInfo('getOrderContent return', $rdata);
		return $rdata;
	}	
	
	
	/**
	 * 获取发货单明细
	 *@param array $param(sKey,consignmentId) key,订单号
	 *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
	 *@author seekfor
	 */
	public function getKuaidi($param){
		global $db,$log;
	
		if (empty ( $param['sKey'] )){
			$rdata['rStatus'] = 110;
			$rdata['error']   = '参数错误';
		}elseif(empty($param['kuaidiNo']) || empty($param['kuaidiCode'])){
			$rdata['rStatus'] = 101;
			$rdata['error']   = '快递单号 / 快递公司代码不能为空';
		}else{
				$wl = getExpressDelivery($param['kuaidiCode'],$param['kuaidiNo']);
	
				if(empty($wl)) {
					$rdata['rStatus'] = 101;
					$rdata['error']   = '数据为空';
				}else{
					$rdata['rStatus'] = 100;
					$rdata['rData']	= $wl;
				}

		}
		return $rdata;
	}
   

    /**
     * @desc 获取退货单列表
     * @param array $param(sKey,begin,step)
     * @return array $rdata(rStatus,error,rData) 状态,提示,数据
     */
    public function getReturnList($param){
        global $db,$log;
        $rdata = array();
        include (SITE_ROOT_PATH."/arr_data.php");

        //$log->logInfo('getReturnList',$param);
        if(empty($param['sKey'])){
            $rdata['rStatus'] 	= 110;
            $rdata['error'] 	= '参数错误';
        }else{
            $cidarr = $this->getCompanyInfo($param['sKey']);
            if($cidarr['rStatus']==101){
                return $cidarr;
            }
            $cid = $cidarr['CompanyID'];
            $sdatabase = $cidarr['Database'];
            
            $smg = '';
            if(isset($param['statusId']) && $param['statusId'] != '') $smg = " and ReturnStatus = ".intval($param['statusId'])." ";      
            
            $sql_c 	= "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_returninfo where ReturnCompany=".$cid." and ReturnClient=".$cidarr['ClientID']."  ".$smsg." ";
            $countData  = $db->get_row($sql_c);
            
            $sql  = "SELECT ReturnID,ReturnSN,ReturnSendAbout,ReturnAbout,ReturnProductW,ReturnProductB,ReturnTotal,ReturnDate,ReturnStatus FROM ".$sdatabase.DATATABLE."_order_returninfo WHERE ReturnCompany=".$cid." and ReturnClient=".$cidarr['ClientID']."  ".$smsg." order by ReturnID desc ";
            $sql .= " Limit ".$param['begin'].",".$param['step'];
            
            $rinfo = $db->get_results($sql);
            if(empty($rinfo)){
                $rdata['rStatus'] 	= 101;
                $rdata['error'] 	= '数据为空';
            }else{
            	foreach($rinfo as $key=>$val){
            		$rinfo[$key]['ReturnStatus'] = $return_status_arr[$val['ReturnStatus']];
            	}
                $rdata['rStatus'] 	= 100;
                $rdata['rAllTotal'] = $countData['allrow'];
                $rdata['rTotal'] 	= count($rinfo);
                $rdata['rData'] 	= $rinfo;
            }
        }
        //$log->logInfo('getReturnList return',$rdata);
        return $rdata;
    }

    /**
     * @desc 获取退货单详细
     * @param array $param(sKey,returnSN)
     * @return array $rdata(rStatus,error,rData)
     */
    public function getReturnContent($param){
        global $db,$log;
        //$log->logInfo("getReturnContent",$param);

        if(empty($param['sKey']) || empty($param['returnId'])){
            $rdata['rStatus'] 	= 110;
            $rdata['error'] 	= '参数错误';
        }else{
            $param['returnId'] = intval($param['returnId']);
            $cidarr = $this->getCompanyInfo($param['sKey']);
            if($cidarr['rStatus'] != 100){
                return $cidarr;
            }
			$cid		= $cidarr['CompanyID'];
			$sdatabase  = $cidarr['Database'];
            //获取退货单基本信息
            $hsql  = "SELECT ReturnID,ReturnOrder,ReturnSN,ReturnSendAbout,ReturnProductW,ReturnProductB,ReturnAbout,ReturnDate,ReturnType,ReturnTotal
                      FROM ".$sdatabase.DATATABLE."_order_returninfo 
                      WHERE  ReturnID=".$param['returnId']." and ReturnCompany=".$cid." and ReturnClient=".$cidarr['ClientID']." Limit 0,1";
            $hdata = $db->get_row($hsql);
            //$rdata['rSql'] = $hsql;
            $submitLog = $db->get_results("select ID,Date,Status,Content from ".$sdatabase.DATATABLE."_order_returnsubmit where CompanyID=".$cid." and OrderID=".$hdata['ReturnID']." "); // tubo 2015-12-18修改，去掉Name
            if(empty($hdata)){
                $rdata['rStatus'] 	= 101;
                $rdata['error'] 	= '数据为空';
            }else{
                $rdata['rData']['header'] 	= $hdata;

                //获取退货单详细信息 退货数据转换为负数
                $isql = "SELECT i.ID,i.Coding,i.Name,r.ContentColor,r.ContentSpecification,r.ContentPrice,r.ContentNumber
                     FROM ".$sdatabase.DATATABLE."_order_cart_return  r
                     LEFT JOIN ".$sdatabase.DATATABLE."_order_content_index i
                     ON r.ContentID = i.ID
                     WHERE r.ReturnID=".$hdata['ReturnID']."
                     AND r.CompanyID=".$cid."  ";
                $idata = $db->get_results($isql);
                
                $rdata['rTotal'] = count($idata);
                $rdata['rData']['body'] = $idata;
                $rdata['rData']['log']  = $submitLog;                
                $rdata['rStatus'] = 100;
                //$rdata['rSql'] = $isql;

            }
        }        
        return $rdata;
    }
    
    /**
     * 获取订单起订金额，优先商业全局->单个终端
     * @param array $param
     */
    public function get_OrderAmount($param = array()){
    	global $db,$log;
    
    	if (empty($param)){
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误!';
    		return $rdata;
    	}else{
    		
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid = $cidarr['CompanyID'];
    		$sdatabase = $cidarr['Database'];
    		
    		$sql_l  = "select OrderAmount from ".$sdatabase.DATATABLE."_order_client where ClientID=".$cidarr['ClientID']." and ClientCompany = ".$cid." limit 0,1";
    		$result	= $db->get_var($sql_l);
    		
    		if(empty($result)){
    			$sql_l  = "select OrderAmount from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID = ".$cid." limit 0,1";
    			$result	= $db->get_var($sql_l);
    		}
    		
    		$rdata['rStatus'] 	= 100;
    		$rdata['rData']  	= floatval($result);
    		
    		return $rdata;
    	}
    }

    
    /**
     * @desc 款项 (平台中已确认到账的付款单传递给ERP接口
     * @param array $param (sKey,body)
     * @return array $rdata
     */
    public function getFinanceList($param){
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
            $cid = $cidarr['CompanyID'];
            $sdatabase = $cidarr['Database'];

            $smg = '';           
            if(isset($param['flagId']) && $param['flagId'] != '') $smg = " and FinanceFlag = ".intval($param['flagId'])." ";
            
            $sql_c 	= "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_finance where FinanceCompany=".$cid." and FinanceClient=".$cidarr['ClientID']."  ".$smsg." ";
            $countData  = $db->get_row($sql_c);
            
            $sql  = "SELECT FinanceID,FinanceOrder,FinanceTotal,FinanceToDate,FinanceUpDate,FinanceDate,FinanceFlag
                    FROM ".$sdatabase.DATATABLE."_order_finance
                    WHERE FinanceCompany={$cid} and FinanceClient =".$cidarr['ClientID']."  ".$smsg."  order by FinanceID desc ";
            $sql .= " limit ".$param['begin'].",".intval($param['step']);
            $list = $db->get_results($sql);
			//$rdata['rSql'] = $sql;
            if(empty($list)){
                $rdata['rStatus'] 	= 101;
                $rdata['error'] 	= '数据为空';
            }else{
            	foreach($list as $key=>$val){
            		$list[$key]['FinanceFlag'] = $finance_arr[$val['FinanceFlag']];
            	}
            	$rdata['rStatus'] 	= 100;
            	$rdata['rAllTotal'] = $countData['allrow'];
                $rdata['rTotal'] 	= count($list);
                $rdata['rData']  	= $list;
            }
        }

        return $rdata;
    }

    /**
     * @desc 获取款项详细
     * @param array $param (sKey,financeID)
     * @return array $rdata
     */
    public function getFinanceContent($param){
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
            $cid = $cidarr['CompanyID'];
            $sdatabase = $cidarr['Database'];
            
            $sql = "SELECT f.FinanceID,f.FinanceOrder,f.FinanceTotal,f.FinanceAbout,f.FinanceUpDate,f.FinanceFlag,f.FinanceType,f.FinanceFrom
                    ,a.AccountsBank,a.AccountsNO,a.AccountsName
                    FROM ".$sdatabase.DATATABLE."_order_finance AS f LEFT JOIN ".$sdatabase.DATATABLE."_order_accounts AS a ON a.AccountsID = f.FinanceAccounts 
                    WHERE f.FinanceID=".intval($param['financeId'])." and f.FinanceCompany=".$cid." LIMIT 0,1 ";
            $single = $db->get_row($sql);

            if($single){
            	$single['FinanceFlagName'] = $finance_arr[$single['FinanceFlag']];
            	$ft = $single['FinanceType'];
            	if($single['FinanceType'] == 'O') $single['FinanceType'] = '在线支付'; elseif($single['FinanceType'] == 'Y') $single['FinanceType'] = '银行转帐'; else $single['FinanceType'] = '银行转帐';
            	
            	if($single['FinanceFrom'] == 'yijifu'){//使用了易极付快捷支付方式
            		$single['AccountsBank']  = '易极付在线收款账户';
            	}
            	
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
     * @desc 获取收货地址
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getAddress($param){
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
    
			$sql  = "select AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag from ".$sdatabase.DATATABLE."_order_address where CompanyID = ".$cid." and AddressClient=".$cidarr['ClientID']." order by AddressID asc ";       
			$sql .= " limit ".$param['begin'].",".intval($param['step']);
			$result	= $db->get_results($sql);
    
    		if(!empty($result)){
    			foreach($result as $v){
    				if($v['AddressFlag'] == 1){
    					$setDefault = true;
    					continue; 
    				}else{
    					$setDefault = false;
    				}
    			}
    			if(!$setDefault) $result[0]['AddressFlag'] = 1;
    			
    			$rdata['rTotal']  = count($result);    			
    			$rdata['rData']   = $result;
    			$rdata['rStatus'] = 100;
    		}else{
    			$clientInfo = $db->get_row("select ClientID,ClientCompanyName,ClientTrueName,ClientPhone,ClientMobile,ClientAdd from ".$sdatabase.DATATABLE."_order_client where ClientID=".$cidarr['ClientID']." limit 0,1");

    			if(!empty($clientInfo['ClientTrueName']) && !empty($clientInfo['ClientAdd'])){
    				if(empty($clientInfo['ClientPhone'])) $clientInfo['ClientPhone'] = $clientInfo['ClientMobile'];
    				$sql_l  = "insert into ".$sdatabase.DATATABLE."_order_address(CompanyID,AddressClient,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressDate) values(".$cid.", ".$cidarr['ClientID'].", '".$clientInfo['ClientCompanyName']."', '".$clientInfo['ClientTrueName']."', '".$clientInfo['ClientPhone']."', '".$clientInfo['ClientAdd']."', ".time().")";
    				$isu = $db->query($sql_l);

    				if($isu){
    					$sql  = "select AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag from ".$sdatabase.DATATABLE."_order_address where CompanyID = ".$cid." and AddressClient=".$cidarr['ClientID']." order by AddressID asc ";
    					$sql .= " limit ".$param['begin'].",".intval($param['step']);
    					$result	= $db->get_results($sql);
    					 
    					if(!empty($result)){
    						foreach($result as $v){
    							if($v['AddressFlag'] == 1){
    								$setDefault = true;
    								continue;
    							}else{
    								$setDefault = false;
    							}
    						}
    						if(!$setDefault) $result[0]['AddressFlag'] = 1;
    			
    						$rdata['rTotal']  = count($result);
    						$rdata['rData']   = $result;
    						$rdata['rStatus'] = 100;
    					}else{
    						$rdata['rStatus'] = 101;
    						$rdata['error']   = '数据不存在!';
    					}
    				}else{
    					$rdata['rStatus'] = 101;
    					$rdata['error']   = '数据不存在!';
    				}
    			}else{
    				$rdata['rStatus'] = 101;
    				$rdata['error']   = '数据不存在!';
    			}
    		}
    	}
    
    	return $rdata;
    }

    /**
     * @desc 获取下单所需配置
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getSetOrder($param){
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
    		/**
    		$cinfo = $db->get_row("select ClientFlag from ".DB_DATABASEU.DATATABLE."_order_dealer where ClientID = ".$cidarr['ClientID']." limit 0,1");
    		if($cinfo['ClientFlag'] == "8"){
    			$rdata['rStatus'] 	= 110;
    			$rdata['error'] 	= '您的帐号还处于待审核状态，暂不能下单，请联系供应商审核!';
    			
    			return $rdata;
    		}
    		**/
    		//验证当前时间是否允许下单
//    		$cResult = $this->check_ordertime(array('companyid' => intval($cidarr['CompanyID'])));
//    		if(!$cResult['status']){
//    			$rdata['rStatus'] 	= 101;
//    			$rdata['error'] 	= $cResult['rmsg'];
//    			return $rdata;
//    		}
    
    		$setinfo = $db->get_results("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cid." and (SetName='send' OR SetName='pay' OR SetName='product') ");
    		if(!empty($setinfo))
    		{
    			foreach($setinfo as $v){
    				if(!empty($v['SetValue'])){
    					$setTypeArr[$v['SetName']] = unserialize($v['SetValue']);
    				}
    			}
    		}
    		//配送方式
    		$sql_l  = "select TypeID,TypeName,TypeAbout from ".DB_DATABASEU.DATATABLE."_order_sendtype order by TypeID asc limit 0,10";
    		$result	= $db->get_results($sql_l);
    		if(!empty($setTypeArr['send'])){
    			foreach($result as $var){
    				if(in_array($var['TypeID'], $setTypeArr['send'])) $typeArr['send'][] = $var;
    			}	
    		}else{
    			$typeArr['send'] = $result;
    		}
    		//结算方式
    		$sql_l  = "select TypeID,TypeName,TypeAbout from ".DB_DATABASEU.DATATABLE."_order_paytype  where TypeClose=0 order by TypeID asc limit 0,10";
    		$result	= $db->get_results($sql_l);
    		if(!empty($setTypeArr['pay'])){
    			foreach($result as $var){
    				if(in_array($var['TypeID'], $setTypeArr['pay'])) $typeArr['pay'][] = $var;
    			}
    		}else{
    			$typeArr['pay'] = $result;
    		}
    		//begin tubo 增加在线支付的几个判断
    		$NetGetWay    = new NetGetWay();
			$yijifuInfo   = $NetGetWay->showGetway('yijifu', $cid, '', true);
			if(!empty($yijifuInfo)){
				$yijifutype['TypeID']    = '9';
				$yijifutype['TypeName']  = '快捷支付';
				$yijifutype['TypeAbout'] = '快捷支付';
				$typeArr['pay'][] = $yijifutype;
			}
			$allinpayInfo = $NetGetWay->showGetway('allinpay', $cid, '', true);
			if(!empty($allinpayInfo)){
				$allinpaytype['TypeID']    = '10';
				$allinpaytype['TypeName']  = '网银支付';
				$allinpaytype['TypeAbout'] = '网银支付';
				$typeArr['pay'][] = $allinpaytype;
			}
			$sql_l  = "select AccountsID,AccountsNO,AccountsName from ".$sdatabase.DATATABLE."_order_accounts where AccountsCompany=".$cid." and AliPhone='T' and PayPartnerID!='' and PayKey!='' limit 0,1 ";
			$alipayInfo	= $db->get_row($sql_l);
			if(!empty($alipayInfo)){
				$alipaytype['TypeID']    = '11';
				$alipaytype['TypeName']  = '支付宝支付';
				$alipaytype['TypeAbout'] = '支付宝支付';
				$typeArr['pay'][] = $alipaytype;
			}
			
    		//end 2015-11-23    		
    		$typeArr['deliveryTime'] 			= $setTypeArr['product']['delivery_time'];
    		$typeArr['invoice']['invoice_p'] 	= $setTypeArr['product']['invoice_p'];
    		$typeArr['invoice']['invoice_p_tax'] 	= $setTypeArr['product']['invoice_p_tax'];
    		$typeArr['invoice']['invoice_z'] 		= $setTypeArr['product']['invoice_z'];
    		$typeArr['invoice']['invoice_z_tax'] 	= $setTypeArr['product']['invoice_z_tax'];
    		
    		if($typeArr){
    			$rdata['rData']   = $typeArr;
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '数据不存在!';
    		}
    	}
    
    	return $rdata;
    }
    
    
    /**
     * @desc 获取配送方式
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getSendType($param){
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
    
    		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cid." and SetName='send' limit 0,1");
			if(!empty($setinfo['SetValue']))
			{	
				$valuearr   = unserialize($setinfo['SetValue']);
				$valuemsg = implode(",", $valuearr);
				$sql_l  = "select TypeID,TypeName,TypeAbout from ".DB_DATABASEU.DATATABLE."_order_sendtype where TypeID in (".$valuemsg.") order by TypeID asc limit 0,10";
				$result	= $db->get_results($sql_l);
			}
			if(empty($result))
			{
				$sql_l  = "select TypeID,TypeName,TypeAbout from ".DB_DATABASEU.DATATABLE."_order_sendtype order by TypeID asc limit 0,10";
				$result	= $db->get_results($sql_l);
			}
	    
    		if($result){
    			$rdata['rTotal']  = count($result);
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
     * @desc 获取结算方式
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getPayType($param){
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
    
    		$setinfo = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cid." and SetName='pay' limit 0,1");
    		if(!empty($setinfo['SetValue']))
    		{
    			$valuearr   = unserialize($setinfo['SetValue']);
    			$valuemsg = implode(",", $valuearr);
    			$sql_l  = "select TypeID,TypeName,TypeAbout from ".DB_DATABASEU.DATATABLE."_order_paytype where TypeID in (".$valuemsg.") order by TypeID asc limit 0,10";
    			$result	= $db->get_results($sql_l);
    		}
    		if(empty($result))
    		{
    			$sql_l  = "select TypeID,TypeName,TypeAbout from ".DB_DATABASEU.DATATABLE."_order_paytype order by TypeID asc limit 0,10";
    			$result	= $db->get_results($sql_l);
    		}
    	  
    		if($result){
    			$rdata['rTotal']  = count($result);
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
     * @desc 获取收款帐号
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getAccounts($param){
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
    
			$result = $db->get_results("SELECT AccountsID,AccountsBank,AccountsNO,AccountsName,AccountsType FROM ".$sdatabase.DATATABLE."_order_accounts where AccountsCompany = ".$cid." order by AccountsID asc ");
    		 
    		if($result){
    			$rdata['rTotal']  = count($result);
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
     * @desc 获取我的余额
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getAmount($param){
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
    
	        //收款单
	        $sqlunion  = " and FinanceClient = ".$cidarr['ClientID']." ";
	        $statsql2  = "SELECT sum(FinanceTotal) as Ftotal from ".$sdatabase.DATATABLE."_order_finance where FinanceCompany=".$cid." ".$sqlunion." and FinanceFlag=2 and (FinanceType='Z' OR FinanceType='O') ";
	        $statdata2 = $db->get_row($statsql2);

	        //其他款项
	        $sqlunion  = " and ClientID = ".$cidarr['ClientID']." ";
	        $statsql4  = "SELECT sum(ExpenseTotal) as Ftotal from ".$sdatabase.DATATABLE."_order_expense where CompanyID=".$cid." ".$sqlunion." and FlagID = '2' ";
	        $statdata4 = $db->get_row($statsql4);
	
	        //订单金额
	        $sqlunion  = " and OrderUserID   = ".$cidarr['ClientID']." ";
	        $statsqlt  = "SELECT sum(OrderIntegral) as Ftotal from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany=".$cid." ".$sqlunion." and OrderStatus!=8 and OrderStatus!=9 ";
	        $statdatat = $db->get_row($statsqlt);
	        
	        //退货金额
	        $sqlunion   = " and ReturnClient  = ".$cidarr['ClientID']." ";
	        $statsqlt1  = "SELECT sum(ReturnTotal) as Ftotal from ".$sdatabase.DATATABLE."_order_returninfo where ReturnCompany=".$cid." ".$sqlunion." and (ReturnStatus=3 or ReturnStatus=5) ";
	        $statdata1  = $db->get_row($statsqlt1);
	
	        $begintotal = $statdata2['Ftotal'] - $statdatat['Ftotal'] + $statdata4['Ftotal'] + $statdata1['Ftotal'];
	
	        $begintotal = floatval($begintotal);
	        $begintotal = sprintf("%.2f",round($begintotal,2));        

	        $cvalue = $db->get_row("select sum(PointValue) as pv from ".$sdatabase.DATATABLE."_order_point where PointCompany=".$cid." and PointClient=".$cidarr['ClientID']." ");
	        	        

    		$rdata['rAmount'] = $begintotal;
    		$rdata['rPoint']  = $cvalue['pv']; 			
    		$rdata['error']   = '';
    		$rdata['rStatus'] = 100;

    	}
    
    	return $rdata;
    }    
    
    /**
     * @desc 获取我的积分
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getPoint($param){
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
    
    		$cvalue = $db->get_row("select sum(PointValue) as pv from ".$sdatabase.DATATABLE."_order_point where PointCompany=".$cid." and PointClient=".$cidarr['ClientID']." ");
    
    		if($cvalue){
    			$rdata['rPoint']  = $cvalue['pv'];
    			$rdata['error']   = '';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 100;
    			$rdata['rPoint']  = 0;
    		}
    	}
    
    	return $rdata;
    }
    
    /**
     * @desc 获取我的资料
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getMyInfo($param){
    	global $db,$log;

    	include_once (SITE_ROOT_PATH."/yijifuMessage.php");
    
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
    
			$cinfo = $db->get_row("select ClientName,ClientCompanyName,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,AccountName,BankName,BankAccount,InvoiceHeader,TaxpayerNumber from ".$sdatabase.DATATABLE."_order_client where ClientID = ".$cidarr['ClientID']." limit 0,1");
				
    		if($cinfo){
    			$cominfo = $db->get_row ( "select CompanyName,CompanySigned,CompanyPrefix from ".DB_DATABASEU.DATATABLE."_order_company where CompanyID =".$cid." limit 0,1" );
    			
    			//查询是否已开通易极付 by wanjun
				$sql_yjf = "select YapiuserName from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=".$cid." and ClientID=".$cidarr['ClientID']." limit 1";
				$resultYJF = $db->get_row($sql_yjf);
		
    			$rdata['rData']   = $cinfo;
    			$rdata['rData']['CompanyName']   = $cominfo['CompanyName'];
    			$rdata['rData']['CompanySigned'] = $cominfo['CompanySigned'];
    			$rdata['rData']['CompanyPrefix'] = $cominfo['CompanyPrefix'];
    			//yijifu温馨提示信息  2016-01-26 tubo
    			$rdata['rData']['yijifuMessage'] = YJF_MESSAGE;
    			//end
    			$rdata['rData']['YapiuserName'] =  $resultYJF['YapiuserName'] ? $resultYJF['YapiuserName'] : '';	//添加易极付开户账号
    			
    			$rdata['error']   = '';
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']  = '数据为空！';
    		}
    	}
    
    	return $rdata;
    }

    /**
     * @desc 获取留言
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getForum($param){
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
    		    
			$sql_c 	= "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_forum where CompanyID=".$cid." and ClientID=".$cidarr['ClientID']." and PID=0 ".$smsg.""; 
			$sql	= "select ID,Name,Title,Content,Date,Reply,ReplyDate from ".$sdatabase.DATATABLE."_order_forum where CompanyID=".$cid." and ClientID=".$cidarr['ClientID']." and PID=0 ".$smsg." Order by ReplyDate DESC,ID DESC "; 
			$sql   .= " limit ".$param['begin'].",".intval($param['step']);
			$countData  = $db->get_row($sql_c);
			$result  = $db->get_results ( $sql );
    		if($result){
    			foreach($result as $v){
    				$idArr[] = $v['ID'];
    			}
    			$isMsg = ','.implode(",",$idArr).',';
    			$sql_son = "select ID,PID,Name,Content,Date from ".$sdatabase.DATATABLE."_order_forum where CompanyID=".$cid."  and instr('".$isMsg."', concat(',', PID, ',') ) > 0 Order by PID DESC, ID ASC ";

    			$result_son  = $db->get_results ( $sql_son );
    			if(!empty($result_son))
				{
					for($i=0;$i<count($result);$i++)
					{
						foreach($result_son as $rvar)
						{
							if($result[$i]['ID'] == $rvar['PID']) $result[$i]['replyData'][] = $rvar;
						}
					}
				}    			
    			$rdata['rAllTotal'] = $countData['allrow'];
    			$rdata['rTotal']  = count($result);
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
     * @desc 获取信息栏目
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getInfoSort($param){
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
    		
    		$sql = "SELECT SortID,SortName FROM ".$sdatabase.DATATABLE."_order_sort where SortCompany=".$cid." order by SortOrder DESC,SortID ASC ";
    		$result = $db->get_results($sql);
    		$result[] = array('SortID'=>0,'SortName'=>'公告信息');
    		
    		if($result){
    			$rdata['rTotal']  = count($result);
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
     * @desc 获取信息
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getInfoList($param){
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
    		
    		if(empty($param['sortId'])) $param['sortId'] = 0; else $param['sortId'] = intval($param['sortId']);
    		$smsg = " and ArticleSort = ".$param['sortId']." ";
    		
    		$sql_c  = "select count(*) as allrow from ".$sdatabase.DATATABLE."_order_article where ArticleCompany=".$cid." ".$smsg." and ArticleFlag=0 ";
    		$countData  = $db->get_row($sql_c);
    		
    		$sql  = "select ArticleID,ArticleTitle,ArticleDate from ".$sdatabase.DATATABLE."_order_article where ArticleCompany=".$cid." ".$smsg." and ArticleFlag=0 order by ArticleOrder desc,ArticleID DESC";
			$sql .= " limit ".$param['begin'].",".intval($param['step']);
    		$result	= $db->get_results($sql);
    
    		if($result){
    			$rdata['rAllTotal'] = $countData['allrow'];
    			$rdata['rTotal']  = count($result);
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
     * @desc 获取信息明细
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getInfoContent($param){
    	global $db,$log;
    
    	if(empty($param['sKey']) || empty($param['articleId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    
    		$smsg = " and ArticleID = ".$param['articleId']." ";
    
    		$sql_c  = "select ArticleID,ArticleTitle,ArticleColor,ArticlePicture,ArticleContent,ArticleCount,ArticleDate from ".$sdatabase.DATATABLE."_order_article where ArticleCompany=".$cid." ".$smsg." and ArticleFlag=0 limit 0,1";
    		$result  = $db->get_row($sql_c);    
    
    		if($result){
    			if(!empty($result['ArticlePicture'])) $result['ArticlePicture'] = RESOURCE_PATH.$result['ArticlePicture'];
    			if(!empty($result['ArticleContent'])){
    				$result['ArticleContent'] = html_entity_decode($result['ArticleContent'], ENT_QUOTES,'UTF-8');
    				$result['ArticleContent'] = _striptext($result['ArticleContent']); //格式化内容
    				$result['ArticleContent'] = htmlentities($result['ArticleContent'], ENT_QUOTES,'UTF-8');
    			}			
    			
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
     * @desc 获取联系方式
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getContactTools($param){
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
       
			$sql_tool   = "SELECT ToolType,ToolName,ToolNO FROM ".$sdatabase.DATATABLE."_order_tool where ToolCompany=".$cid." order by ToolID ASC limit 0,10";
			$sql_contact  = "SELECT ContactName,ContactValue FROM ".$sdatabase.DATATABLE."_order_contact where ContactCompany=".$cid." order by ContactID ASC limit 0,10";
			$result['tools']	= $db->get_results($sql_tool);
			$result['contact']	= $db->get_results($sql_contact);   		
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
     * @desc 获取广告图片
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getPicture($param){
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
    		 
			$sql_l  = "SELECT ArticleID,ArticleName,ArticleLink,ArticlePicture FROM ".$sdatabase.DATATABLE."_order_xd where ArticleCompany=".$cid." and ArticleSort=1  order by ArticleOrder DESC, ArticleID DESC";
			$sql_l .= " Limit ".$param['begin'].",".$param['step'];
			$result	= $db->get_results($sql_l);
    		if($result){
    			foreach($result as $key=>$var){
    				$result[$key]['ArticlePicture'] = RESOURCE_PATH.$result[$key]['ArticlePicture'];
    			}
    			$rdata['rTotal']  = count($result);
    			$rdata['rData']   = $result;
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
    public function setOrder($param){
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
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		
    		$oid = intval($param['orderId']);
    		 
			if($param['action'] == 'cancel'){
				$sql_l  = "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderStatus=8 where OrderID=".$oid." and OrderCompany = ".$cid." and OrderUserID=".$cidarr['ClientID']." and OrderStatus=0 ";
				$sqlin 	= "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['ClientInfo']['ClientName']."', '".$cidarr['ClientInfo']['ClientTrueName']."',".time().", '客户取消订单', '".$param['content']."')";

				$resultStatus		= $db->query($sql_l);
				if($resultStatus){
					$inStatus	= $db->query($sqlin);
					self::cancelOrder($param,$cidarr);					
				}
			}else{
				$sqlin 	= "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$oid.", '".$cidarr['ClientInfo']['ClientName']."', '".$cidarr['ClientInfo']['ClientTrueName']."',".time().", '客户留言', '".$param['content']."')";	
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
    	
    	$setinfo  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='product' limit 0,1");
    	if(!empty($setinfo['SetValue'])) $valuearr = unserialize($setinfo['SetValue']);
    	
    	if(!empty($valuearr['product_number']) && $valuearr['product_number']=="on")
    	{
    		$sql     = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".$sdatabase.DATATABLE."_order_cart where OrderID=".$oid." and CompanyID=".$cidarr['CompanyID']." and ClientID = ".$cidarr['ClientID']." ";
    		$data_c = $db->get_results($sql);
   	
    		$tykey = str_replace($this->fp,$this->rp,base64_encode("统一"));
    		foreach($data_c as $dvar)
    		{
    			$db->query("update ".$sdatabase.DATATABLE."_order_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$cidarr['CompanyID']." and ContentID=".$dvar['ContentID']." limit 1");
    				
    			if(!empty($dvar['ContentColor']) || !empty($dvar['ContentSpecification']))
    			{
    				if(empty($dvar['ContentColor'])) $keycolor = $tykey; else $keycolor = str_replace($this->fp,$this->rp,base64_encode($dvar['ContentColor']));
    				if(empty($dvar['ContentSpecification'])) $keyspec = $tykey; else $keyspec= str_replace($this->fp,$this->rp,base64_encode($dvar['ContentSpecification']));
    				$db->query("update ".$sdatabase.DATATABLE."_order_inventory_number set OrderNumber=OrderNumber+".$dvar['ContentNumber']." where CompanyID=".$cidarr['CompanyID']." and ContentID=".$dvar['ContentID']." and ContentColor='".$keycolor."' and ContentSpec='".$keyspec."' limit 1");
    			}
    			$dnumber = intval("-".$dvar['ContentNumber']);
    			$db->query("insert into ".$sdatabase.DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$cidarr['CompanyID']},{$dvar['ContentID']},{$oid},{$dnumber},'cancel')");
    		}
    	}
    	
    }
    
    /**
     * @desc 订单操作
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function setConsignment($param){
    	global $db,$log;
    	$log->logInfo('setConsignment', $param);
    
    	if(empty($param['sKey']) || empty($param['consignmentId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    
    		$conid = intval($param['consignmentId']);    		 
    		if($param['action'] == 'confirm'){
    			$sql_l   = "update ".$sdatabase.DATATABLE."_order_consignment set ConsignmentFlag=1 where ConsignmentID=".$conid." and ConsignmentCompany=".$cidarr['CompanyID']." and ConsignmentClient=".$cidarr['ClientID']." and ConsignmentFlag=0";
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
     * @desc 确认收货相关操作
     * @param array $param (sKey)，array $cidarr
     * @return array $rdata
     */
    protected function confirmIncept($param,$cidarr){
    	global $db,$log;
    	$sdatabase 	= $cidarr['Database'];
    	$conid 		= intval($param['consignmentId']);

    		$cinfo  = $db->get_row("SELECT ConsignmentID,ConsignmentOrder FROM ".$sdatabase.DATATABLE."_order_consignment where ConsignmentCompany = ".$cidarr['CompanyID']." and ConsignmentID=".$conid." limit 0,1");
			
			if(!empty($cinfo['ConsignmentOrder']))
			{
				$upinfo   = $db->get_row("SELECT OrderID,OrderSN,OrderStatus,OrderSendStatus FROM ".$sdatabase.DATATABLE."_order_orderinfo where OrderSN = '".$cinfo['ConsignmentOrder']."' and OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']." limit 0,1");

				$sendline = $db->get_row("select count(*) as allrow from ".$sdatabase.DATATABLE."_order_cart where ContentSend < ContentNumber and CompanyID = ".$cidarr['CompanyID']." and ClientID=".$cidarr['ClientID']." and OrderID=".$upinfo['OrderID']."");
				if(!empty($sendline['allrow']) && $sendline['allrow'] > 0)
				{
					$upsql =  "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderSendStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']."";
				}else{
					$upsql =  "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderSendStatus=4 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']."";
				}
				
				$upsql2 =  "update ".$sdatabase.DATATABLE."_order_orderinfo set OrderStatus=3 where OrderID = ".$upinfo['OrderID']." and OrderCompany=".$cidarr['CompanyID']." and OrderUserID=".$cidarr['ClientID']." and OrderStatus < 3";
				$db->query($upsql2);
				if($db->query($upsql))
				{	
					$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Date,Status,Content) values(".$cidarr['CompanyID'].", ".$upinfo['OrderID'].", '".$cidarr['ClientInfo']['ClientName']."',".time().", '客户确认收货', '移动端操作')";
					$db->query($sqlin);
				}
			}    	 
    }
    
    /**
     * @desc 收藏商品
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function setContentFav($param){
    	global $db,$log;
    
    	if(empty($param['sKey']) || empty($param['contentId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];

    		if($param['action'] == 'remove'){
    			$sql_l = "delete from ".$sdatabase.DATATABLE."_order_fav where FavContent=".intval($param['contentId'])." and FavClient=".$cidarr['ClientID']." and FavCompany = ".$cid."";    				
    			$resultStatus	= $db->query($sql_l);
    		}else{
    			$idarr = explode(",", $param['contentId']);

    			foreach($idarr as $iv){
    				$iv = intval($iv);
    				if(!empty($iv)){
    					$sql_l = "insert into ".$sdatabase.DATATABLE."_order_fav(FavCompany,FavClient,FavContent) values(".$cid.",".$cidarr['ClientID'].",".$iv.")";
    					$resultStatus = $db->query($sql_l);
    					$resultStatus = true;
    				}
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
     * @desc 提交留言
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function submitForum($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['forumTitle']) || empty($param['forumContent'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '标题 / 内容 不能为空!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		
    		if(empty($param['forumName'])) $param['forumName'] = $cidarr['ClientInfo']['ClientTrueName'];
    		
    		//$param = $input->_htmlentities($param);
    		$sql_l = "insert into ".$sdatabase.DATATABLE."_order_forum(CompanyID,ClientID,PID,User,Name,Title,Content,Date,IP,ReplyDate) values(".$cid.",".$cidarr['ClientID'].",0,'".$cidarr['ClientInfo']['ClientName']."', '".$param['forumName']."', '".$param['forumTitle']."', '".$param['forumContent']."', ".time().", '".RealIp()."', ".time().")";
    		$resultStatus	= $db->query($sql_l);
    		
    		if($resultStatus){
    			$rdata['insertId'] = @mysql_insert_id();
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
     * @desc 回复留言
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function replyForum($param){
    	global $db,$log;
    
    	if(empty($param['sKey']) || empty($param['parentId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['replyContent'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '内容 不能为空!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		
    		if(empty($param['replyName'])) $param['replyName'] = $cidarr['ClientInfo']['ClientTrueName'];
    
    		$sql_l = "insert into ".$sdatabase.DATATABLE."_order_forum(CompanyID,ClientID,PID,User,Name,Content,Date,IP) values(".$cid.",".$cidarr['ClientID'].",".$param['parentId'].", '".$cidarr['ClientInfo']['ClientName']."', '".$param['replyName']."', '".$param['replyContent']."', ".time().", '".RealIp()."')";
    		$resultStatus	= $db->query($sql_l);

    		if($resultStatus){
    			$sqlu = "update ".$sdatabase.DATATABLE."_order_forum set Reply=Reply+1, ReplyDate=".time()." where ID=".$param['parentId'];
    			$db->query($sqlu);
    			
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
     * @desc 新增收货地址
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function setAddress($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		if($param['action'] == 'add' || $param['action'] == 'edit'){
    			if(empty($param['addressContact']) || empty($param['addressPhone']) || empty($param['addressAddress'])){
    				$rdata['rStatus'] 	= 101;
    				$rdata['error'] 	= '联系人 / 联系电话 / 详细地址 不能为空!';
    				return $rdata;
    			}
    		}elseif($param['action'] == 'edit' || $param['action'] == 'del'){
    			if(empty($param['addressId'])){
    				$rdata['rStatus'] 	= 101;
    				$rdata['error'] 	= '未指定修改数据';
    				return $rdata;
    			}
    		}
    		    		
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		
    		if($param['action'] == 'add'){
    			$sql_l  = "insert into ".$sdatabase.DATATABLE."_order_address(CompanyID,AddressClient,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressDate) values(".$cid.", ".$cidarr['ClientID'].", '".$param['addressCompany']."', '".$param['addressContact']."', '".$param['addressPhone']."', '".$param['addressAddress']."', ".time().")";
    			
    		}elseif($param['action'] == 'edit'){
    			if(empty($param['addressId'])){
    				$rdata['rStatus'] 	= 110;
    				$rdata['error'] 	= '参数错误!';
    				return $rdata;
    			}
    			$sql_l  = "update ".$sdatabase.DATATABLE."_order_address set AddressCompany='".$param['addressCompany']."',  AddressContact='".$param['addressContact']."', AddressPhone='".$param['addressPhone']."', AddressAddress='".$param['addressAddress']."' where CompanyID=".$cid." and AddressClient=".$cidarr['ClientID']." and AddressID=".$param['addressId']." ";
    		}elseif($param['action'] == 'del'){
    			$sql_l  = "delete from ".$sdatabase.DATATABLE."_order_address  where CompanyID=".$cid." and AddressClient=".$cidarr['ClientID']." and AddressID=".$param['addressId']." ";
    		}elseif($param['action'] == 'default'){
    			$sql_l  = "update ".$sdatabase.DATATABLE."_order_address set AddressFlag=1 where AddressID=".$param['addressId']." and CompanyID=".$cid." and AddressClient=".$cidarr['ClientID']." ";
    			$db->query("update ".$sdatabase.DATATABLE."_order_address set AddressFlag=0 where CompanyID=".$cid." and AddressClient=".$cidarr['ClientID']." ");
    		}

			$resultStatus	= $db->query($sql_l);
    		if($resultStatus){ 
    			if($param['action'] == 'add') $rdata['insertId'] = @mysql_insert_id();  			 
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
    public function setPassword($param){
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

    		$opass = strtolower(trim($param['oldPassword']));
    		$npass = strtolower(trim($param['newPassword']));
    		if(!is_filename($opass) || strlen($opass) < 3 || strlen($opass) > 18){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '请输入合法的原密码！(3-18位数字、字母和下划线)';
    			return $rdata;
    		}

    		if(!is_filename($npass) || strlen($npass) < 3 || strlen($npass) > 18){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '请输入合法的新密码！(3-18位数字、字母和下划线)';
    			return $rdata;
    		}
    		$sql_l = "update ".DB_DATABASEU.DATATABLE."_order_dealers set ClientPassword='".$npass."' where ClientID=".$cidarr['ClientID']." and ClientCompany=".$cid." and ClientPassword='".$opass."' limit 1";
    		$resultStatus	= $db->query($sql_l);

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
     * @desc 登出
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function setLoginOut($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    
    		$sql_l = "update ".DB_DATABASEU.DATATABLE."_order_dealers set TokenValue='' where TokenValue='".$param['sKey']."'  limit 1" ;
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
     * @desc 取消微信绑定
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function setRemoveWerxin($param){
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
    		
    		$sql_l = "delete from ".DB_DATABASEU.DATATABLE."_order_weixin where WeiXinID='".$param['openId']."' and UserID=".$cidarr['ClientID']." and UserType='C' limit 1" ;
    		$resultStatus	= $db->query($sql_l);
    
    		if($resultStatus){
    			$sql_l = "update ".DB_DATABASEU.DATATABLE."_order_dealers set TokenValue='' where TokenValue='".$param['sKey']."' limit 1" ;
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
     * @desc 提交订单
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function submitOrder($param){
		global $db,$log;
		$log->logInfo('submitOrder', $param);		
    	$orderTotal = 0;
    	    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$isAllow = denyRepeatSubmit($param,'submitOrder');
    		if($isAllow){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '请不要重复提交！';
    			return $rdata;
    		}
    		
    		if(empty($param['cartItems'])){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '没有可提交的数据';
    			return $rdata;
    		}
    		
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];  

    		$cinfo = $db->get_row("select ClientFlag from ".DB_DATABASEU.DATATABLE."_order_dealers where ClientID = ".$cidarr['ClientID']." limit 0,1");
    		//$log->logInfo('submitOrder cinfo', $cinfo);
    		if(empty($cinfo) || $cinfo['ClientFlag'] == "1" || $cinfo['ClientFlag'] == "8" || $cinfo['ClientFlag'] == "9"){
    			$rdata['rStatus'] 	= 101;
    			if($cinfo['ClientFlag'] == "8" || $cinfo['ClientFlag'] == "9"){
    				$rdata['error'] 	= '您的帐号还处于待审核状态，暂不能下单，请联系供应商审核!';
    			}else{
    				$rdata['error'] 	= '您的帐号已禁用，暂不能下单，请联系供应商审核!';
    			}
    			
    			return $rdata;
    		}
    		
    		//验证资质效期
    		$cinfo = $db->get_row("select GsmpValidity,LicenceValidity from ".$sdatabase.DATATABLE."_order_client where ClientID = ".$cidarr['ClientID']." limit 0,1");
    		$time = time();
    		if(!empty($cinfo['GsmpValidity']) && $cinfo['GsmpValidity'] != '1970-01-01'){
    			$gsmpValidity = strtotime($cinfo['GsmpValidity'].' 23:59:59');
    			if($time > $gsmpValidity){//资质效期失效
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '您的GSP证书已过期，不能进行采购';
    				return $rdata;
    			}
    		}
    		if(!empty($cinfo['LicenceValidity']) && $cinfo['LicenceValidity'] != '1970-01-01'){
    			$licenceValidity = strtotime($cinfo['LicenceValidity'].' 23:59:59');
    			if($time > $licenceValidity){//资质效期失效
    				$rdata['rStatus']	= 101;
    				$rdata['error']		= '您的许可证已过期，不能进行采购';
    				return $rdata;
    			}
    		}
    		
    		
    		//验证当前时间是否允许下单。需要移动到[getSetOrder方法]
    		$cResult = $this->check_ordertime(array('companyid' => intval($cidarr['CompanyID'])));
    		if(!$cResult['status']){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= $cResult['rmsg'];
    			return $rdata;
    		}
    		//$log->logInfo('submitOrder cResult', $cResult);

    		//库存设置
    		$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='product' limit 0,1";
    		$result	= $db->get_row($sql_l);
    		if(!empty($result['SetValue'])) $valuearr = unserialize($result['SetValue']);
    		if(!empty($valuearr)) $setarr = $valuearr; else $setarr = null;
    		if(!empty($setarr['product_number'])){
    			$setLibrary['pn']  = $setarr['product_number'];
    		}else{
    			$setLibrary['pn']  = 'off';
    		}
    		if(!empty($setarr['product_negative'])){
    			$setLibrary['png']  = $setarr['product_negative'];
    		}else{
    			$setLibrary['png']  = 'off';
    		}    		
    		
    		foreach ($param['cartItems'] as $key=>$var){
    			if(empty($var['number'])){
    				unset($param['cartItems'][$key]);
    			 	continue; //数量为0时丢掉
    			}
    			$inpa[] = $var['contentId'];
    			
    			if(!empty($var['color']) || !empty($var['spec'])){

    				if(empty($var['color'])) $var['encolor'] = str_replace($this->fp,$this->rp,base64_encode("统一")); else $var['encolor'] = str_replace($this->fp,$this->rp,base64_encode($var['color']));
    				if(empty($var['spec'])) $var['enspec']   = str_replace($this->fp,$this->rp,base64_encode("统一")); else $var['enspec'] = str_replace($this->fp,$this->rp,base64_encode($var['spec']));
    				$param['cartItems'][$key]['cskey']   = md5($var['contentId'].$var['encolor'].$var['enspec']);
    				$param['cartItems'][$key]['encolor'] = $var['encolor'];
    				$param['cartItems'][$key]['enspec']  = $var['enspec'];
    				
    			}else{
    				$param['cartItems'][$key]['cskey'] = '';
    			}
    		}
    		//$log->logInfo('submitOrder2', $param);
    		//查询提交的商品名称，价格
   			$goodsArr = self::listCartGoods($param['cartItems'], $setLibrary, $cidarr);
   			//$log->logInfo('submitOrder goodsArr',$goodsArr);
   			//库存检查
   			foreach ($param['cartItems'] as $key=>$var){
   				if($setLibrary['pn'] == 'on' && $setLibrary['png'] == 'off'){
   					if($var['number'] > $goodsArr['all'][$var['contentId']]['OrderNumber']){
   						denyRepeatSubmit($var,'submitOrder'); //删除
   						$rdata['rStatus'] = 101;
   						$rdata['error']   = '商品：“'.$goodsArr['all'][$var['contentId']]['Name'].'” 库存不够！';
   						return $rdata;
   					}
   					if(!empty($var['cskey']) &&  $var['number'] > $goodsArr['cosp'][$var['cskey']]){
   						denyRepeatSubmit($var,'submitOrder');//删除
   						$rdata['rStatus'] = 101;
   						$rdata['error']   = '商品：“'.$goodsArr['all'][$var['contentId']]['Name'].'('.$var['color'].' / '.$var['spec'].')” 库存不够！';
   						return $rdata;
   					}			
   				}

   				if(empty($goodsArr['all'][$var['contentId']]['ID'])) continue; //商品不存在
   				$cartTempArr[$key] = $var;
   				$cartTempArr[$key]['encolor'] = $var['encolor'];
   				$cartTempArr[$key]['enspec']  = $var['enspec'];
   				$cartTempArr[$key]['ID']      = $goodsArr['all'][$var['contentId']]['ID'];
   				$cartTempArr[$key]['name']    = $goodsArr['all'][$var['contentId']]['Name'];
   				$cartTempArr[$key]['price']   = $goodsArr['all'][$var['contentId']]['Price'];
   				$cartTempArr[$key]['pencent'] = $goodsArr['all'][$var['contentId']]['Pencent'];
   				$orderTotal = $orderTotal + ($var['number'] * $cartTempArr[$key]['price'] * $cartTempArr[$key]['pencent'] / 10);
   				if(strpos($goodsArr['all'][$var['contentId']]['Color'],$var['color']) === false){
   					$cartTempArr[$key]['color'] = '';
   				}
   				if(strpos($goodsArr['all'][$var['contentId']]['Specification'],$var['spec']) === false){
   					$cartTempArr[$key]['spec'] = '';
   				}
   			}
   			//$log->logInfo('submitOrder3', $cartTempArr);
   			$rdata['rStatus'] = 101;
   			$rdata['error']   = '提交不成功！';
   			
   			if(!empty($cartTempArr)){
   				$orderTotal = sprintf("%01.2f", round($orderTotal,2));
   				
   				if(empty($param['payType'])) $param['payType'] = 0;
   				if(empty($param['sendType'])) $param['sendType'] = 0;
   				if(!empty($setarr['audit_type']) && $setarr['audit_type']=="on") $autidstatus = 'F'; else $autidstatus = 'T';
   				//税点
   				if(empty($param['invoiceType'])){ $param['invoiceType'] = 'N';}
   				if($param['invoiceType'] == 'P'){
   					$invoiceTax = $setarr['invoice_p_tax'];
   				}elseif($param['invoiceType'] == 'Z'){
   					$invoiceTax = $setarr['invoice_z_tax'];
   				}else{
   					$invoiceTax = 0;
   				}

                $totalPure = $orderTotal;
                $stair_count = get_stair($db,$totalPure,$cid); //以不含税价计算优惠

                $orderTotal = $orderTotal - $stair_count;
   				$orderTotal = $orderTotal + ($orderTotal * $invoiceTax / 100); //含税总价
				$orderTotal = sprintf("%01.2f", round($orderTotal,2));
				
				$orderSpecial = 'F';
            	if($stair_count > 0) {
                	$orderSpecial = 'T';
            	}
            	
            	
   				//生成订单号
   				$osn = $db->get_row("SELECT OrderID,OrderSN from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany = ".$cid." and OrderSN<>'' order by OrderID desc limit 0,1");

   				if(empty($osn['OrderSN']))
   				{
   					$OrderSN = date("Ymd")."-".mt_rand(1999,5999);
   				}else{
   					//$nextid	 = intval(substr($osn['OrderSN'],strpos($osn['OrderSN'], '-')+1))+1;
   					//$OrderSN = date("Ymd")."-".$nextid;
					
					$today   = date("Ymd");
   					$nowDate = substr($osn['OrderSN'], 0, 8);
   					$nextid	 = intval(substr($osn['OrderSN'], strpos($osn['OrderSN'], '-')+1))+1;
   					$OrderSN = $nowDate == $today ? (date("Ymd")."-".$nextid) : (date("Ymd")."-".mt_rand(1999,5999));
   				}

   				if(!empty($param['addressId'])){
   					$sqla  = "select AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag from ".$sdatabase.DATATABLE."_order_address where AddressID = ".intval($param['addressId'])." and CompanyID = ".$cid." and AddressClient=".$cidarr['ClientID']." order by AddressID asc limit 0,1";
   				}else{
   					$sqla  = "select AddressID,AddressCompany,AddressContact,AddressPhone,AddressAddress,AddressFlag from ".$sdatabase.DATATABLE."_order_address where  CompanyID = ".$cid." and AddressClient=".$cidarr['ClientID']."  order by AddressFlag desc, AddressID asc limit 0,1";	
   				}
   				$addinfo	= $db->get_row($sqla);
   				if(!empty($addinfo)){
   					$param['addressCompany'] = $addinfo['AddressCompany'];
   					$param['addressContact'] = $addinfo['AddressContact'];
   					$param['addressPhone']   = $addinfo['AddressPhone'];
   					$param['addressAddress'] = $addinfo['AddressAddress'];
   				}
   				
   				//生成头表
   				if(empty($param['orderFrom'])) $param['orderFrom'] = 'Mobile';
   				if($param['orderType'] != 'M' && $param['orderType'] != 'S') $param['orderType'] = 'C';
   				$sql_l  = "insert into ".$sdatabase.DATATABLE."_order_orderinfo(OrderSN,OrderCompany,OrderUserID,OrderSendType,OrderPayType,OrderReceiveCompany,OrderReceiveName,OrderReceivePhone,OrderReceiveAdd,InvoiceType,InvoiceTax,DeliveryDate,OrderRemark,OrderTotal,OrderDate,OrderType,OrderSaler,OrderFrom,OrderSpecial) values('".$OrderSN."',".$cid.", '".$cidarr['ClientID']."', ".$param['sendType'].",".$param['payType'].",'".$param['addressCompany']."','".$param['addressContact']."','".$param['addressPhone']."','".$param['addressAddress']."','".$param['invoiceType']."','".$invoiceTax."','".$param['deliveryDate']."','".$param['orderRemark']."','".$orderTotal."',".time().",'".$param['orderType']."','".$autidstatus."','".$param['orderFrom']."','{$orderSpecial}')";
   				$isIn = $db->query($sql_l);
   				$osnid = $db->get_row("SELECT OrderID,OrderSN,OrderTotal from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany = ".$cid." and OrderUserID=".$cidarr['ClientID']." and OrderSN='".$OrderSN."' order by OrderID desc limit 0,1");

   				//生成明细
   				if(!empty($osnid['OrderID']))
   				{
   					$oid = $osnid['OrderID'];
   					if(!empty($param['invoiceType']) && $param['invoiceType'] != 'N'){
   						if(!empty($param['invoiceHeader'])){
   							$db->query("insert into ".$sdatabase.DATATABLE."_order_invoice(OrderID,CompanyID,ClientID,InvoiceType,AccountName,BankName,BankAccount,InvoiceHeader,InvocieContent,TaxpayerNumber,InvoiceDate) values(".$oid.",".$cid.",".$cidarr['ClientID'].",'".$param['invoiceType']."','".$param['accountName']."','".$param['bankName']."','".$param['bankAccount']."','".$param['invoiceHeader']."','".$param['invocieContent']."','".$param['taxpayerNumber']."',".time().")");
   						}
   					}
   					
   					foreach($cartTempArr as $k=>$v){
   						if(!empty($v['ID'])){
   							$addInset[] = "(
   							    ".$oid.",
   							    ".$cid.",
   							    ".$cidarr['ClientID'].",
   							    ".$v['ID'].",
								'".$v['name']."', 
								'".$v['color']."', 
								'".$v['spec']."',
								'".$v['price']."',
								".$v['number'].",
								'".$v['pencent']."'
								)";
   						}
   					}
   					//tubo begin 增加 保证cart里面加入的金额和订单金额一致  2016-04-06
   					$sql = "insert into ".$sdatabase.DATATABLE."_order_cart(OrderID,CompanyID,ClientID,ContentID,ContentName,ContentColor,ContentSpecification,ContentPrice,ContentNumber,ContentPercent) values ".implode(",", $addInset);
   					$isIncart = $db->query($sql);
   					
   					if($isIncart){
   						foreach($cartTempArr as $k=>$v){
   							if(!empty($v['ID'])){
	   							if($setLibrary['pn'] == 'on'){
	   								if(!empty($v['cskey'])){
	   									$isTure = $db->query("update ".$sdatabase.DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$v['number']." where CompanyID=".$cid." and ContentID=".$v['ID']." and ContentColor='".$v['encolor']."' and ContentSpec='".$v['enspec']."' limit 1");
	   									//$log->logInfo("sql1","update ".$sdatabase.DATATABLE."_order_inventory_number set OrderNumber=OrderNumber-".$v['number']." where CompanyID=".$cid." and ContentID=".$v['ID']." and ContentColor='".$v['encolor']."' and ContentSpec='".$v['enspec']."' limit 1");
	
	   									$db->query("update ".$sdatabase.DATATABLE."_order_number set OrderNumber=(select sum(OrderNumber) from ".$sdatabase.DATATABLE."_order_inventory_number where CompanyID=".$cid." and ContentID=".$v['ID']." ) where CompanyID=".$cid." and ContentID=".$v['ID']." limit 1");
	   								}else{
	   									$db->query("update ".$sdatabase.DATATABLE."_order_number set OrderNumber=OrderNumber-".$v['number']." where CompanyID=".$cid." and ContentID=".$v['ID']." limit 1");
	   									//$log->logInfo("update ".$sdatabase.DATATABLE."_order_number set OrderNumber=OrderNumber-".$v['number']." where CompanyID=".$cid." and ContentID=".$v['ID']." limit 1");
	   								}
	   								$db->query("insert into ".$sdatabase.DATATABLE."_order_onumber_log(CompanyID,ContentID,OrderID,Number,Action) values({$cid},{$v['ID']},{$oid},{$v['number']},'order')");
	   							}
   							}
   						}
   						
   						$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$osnid['OrderID'].", 'client', '终端',".time().", '提交订单', '通过移动设备提交订单')";
   						
		                if($stair_count > 0) {
	                    	$stair_amount = get_stair($db,$totalPure,$cid,'amount');//满足的满省条件
	                    	$submit_content = '订单满 ¥' . $stair_amount . ' 省 ¥' . $stair_count . " ，金额： ¥" . $orderTotal;
	                    	$sqlin = "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$osnid['OrderID'].", 'client', '药店',".time().", '订单满省', '{$submit_content}')";
	                    	$db->query($sqlin);
	               	    }
	   					
	   					if(!empty($param['userId'])){
	   						$udata = $db->get_row("select UserID,UserName,UserTrueName,UserFlag,UserType from ".DB_DATABASEU.DATATABLE."_order_user where UserID = ".intval($param['userId'])." and UserCompany = ".$cid." limit 0,1");
	   						if($param['orderType'] == 'S') $cmsg = '业务员代下订单'; else $cmsg = '管理员代下订单';
	   						if(!empty($udata)){
	   							$sqlin 	= "insert into ".$sdatabase.DATATABLE."_order_ordersubmit(CompanyID,OrderID,AdminUser,Name,Date,Status,Content) values(".$cid.", ".$osnid['OrderID'].", '".$udata['UserName']."', '".$udata['UserTrueName']."',".time().", '".$cmsg."', '-')";
	   							$db->query($sqlin);
	   						}
	   					}
	   					//发短信
			            $cidarr['sid'] = "1";
			            $cidarr['message'] = "【".$cidarr['CompanySigned']."】您有一个新订单:NO.".$OrderSN.",来自:".$cidarr['CompanyName']."(".$cidarr['ClientInfo']['ClientCompanyName'].")金额为:".$orderTotal." 元,请尽快登录医统平台处理。退订回复TD";
			            self::setSendSms($cidarr);
	   					
	   				   	$rdata['rData']	  = $osnid;		
		    			$rdata['error']   = '执行成功';
		    			$rdata['rStatus'] = 100;
   					}else{
   						$db->query("delete from ".$sdatabase.DATATABLE."_order_orderinfo where OrderCompany = ".$cid." and OrderUserID=".$cidarr['ClientID']." and OrderID=".$osnid['OrderID']);
   						
   						$rdata['error']   = '提交订单失败！';
   						$rdata['rStatus'] = 101;
   					}
   				}				
   			}else{
   				$rdata['error']   = '没有可提交的商品！';
   				$rdata['rStatus'] = 101;	
   			}  			
    	}    
    	return $rdata;
    }   

    //列出购物车商品信息
    protected function listCartGoods($cartData,$setArr,$cidarr){
    	global $db,$log;
    	$dataCsArr = array();
    	
    	$cid 		= $cidarr['CompanyID'];
    	$sdatabase 	= $cidarr['Database'];    	
    	
    	foreach ($cartData as $key=>$var){
    		$idArr[] = $var['contentId'];
    		$cartArr[$var['contentId']] = $var;
    	}
    	$idStr = ",".implode(",",$idArr).",";    	
    	
    	if($setArr['pn']=="on"){
    		$result['ison'] = 'on';
    		$sql_l 	 = "SELECT i.ID,i.CommendID,i.BrandID,i.Name,i.Coding,i.Price1,i.Price2,i.Price3,i.Units,i.Color,i.Specification,n.OrderNumber FROM ".$sdatabase.DATATABLE."_order_content_index i left join ".$sdatabase.DATATABLE."_order_number n on i.ID=n.ContentID where instr('".$idStr."', concat(',',i.ID,',') ) > 0 and i.CompanyID = ".$cid ." and i.FlagID=0 ";
    
    		$sql     = "select ContentID,ContentColor,ContentSpec,(case when (OrderNumber<0) then 0 else OrderNumber end) as OrderNumber from ".$sdatabase.DATATABLE."_order_inventory_number where  CompanyID=".$cid ." and instr('".$idStr."',concat(',', ContentID, ',') ) > 0";
    		$data_cs = $db->get_results($sql);
    		if(!empty($data_cs)){
    			foreach($data_cs as $v){

    				$key = md5($v['ContentID'].$v['ContentColor'].$v['ContentSpec']);
    				$dataCsArr[$key] = $v['OrderNumber'];	
    				
    			}    			
    		}
    	}else{
    		$result['ison'] = 'off';
    		$sql_l  = "select ID,CommendID,BrandID,Name,Coding,Price1,Price2,Price3,Units,Color,Specification from ".$sdatabase.DATATABLE."_order_content_index where CompanyID=".$cid ." and instr('".$idStr."', concat(',', ID, ',') ) > 0 and FlagID=0 ";
    	}
    	$datat = $db->get_results($sql_l);
    	/****
    	//整包装出货
    	$sql_c = "select ContentIndexID,Package from ".$sdatabase.DATATABLE."_order_content_1 where CompanyID=".$cid ." and instr('".$idStr."',ContentIndexID+',') > 0  ";
    	$datac = $db->get_results($sql_c);
    	if(!empty($datac))
    	{
    		foreach($datac as $cvar)
    		{
    			if(empty($cvar['Package'])) $cvar['Package'] = 0;
    			$result['package'][$cvar['ContentIndexID']]  =  $cvar['Package'];
    		}
    	}
    	**/
    	if(!empty($cidarr['ClientInfo']['ClientBrandPercent'])) $brandPercent = unserialize($cidarr['ClientInfo']['ClientBrandPercent']);
    	
		//获取订购价格
		for($i=0;$i<count($datat);$i++)
		{
			$datat[$i]['Price']      = $datat[$i][$cidarr['ClientInfo']['ClientSetPrice']];
			if($datat[$i]['CommendID'] == "2")
			{
				$datat[$i]['Pencent'] = '10.0';
			}else{				
				if(!empty($datat[$i]['BrandID']) && !empty($brandPercent[$datat[$i]['BrandID']]))
				{
					$datat[$i]['Pencent'] = $brandPercent[$datat[$i]['BrandID']];
				}else{
					$datat[$i]['Pencent'] = $cidarr['ClientInfo']['ClientPercent'];
				}
			}
			$price3 = setprice3($datat[$i]['Price3'], $cidarr['ClientID'], $cidarr['ClientInfo']['ClientLevel']);
			if(!empty($price3))
			{
				$datat[$i]['Price']   = $price3;
				$datat[$i]['Pencent'] = '10.0';
			}
			$dataGoods[$datat[$i]['ID']] = $datat[$i];
		}

		$result['all']  = $dataGoods;
		$result['cosp'] = $dataCsArr;
    
    	return $result;
    	unset($result,$dataGoods,$data_cs,$datat);
    } 

    //验证是否开户 by ltc
    public function check_pay_account($param = array())
    {
    	global $db;
    	if(empty($param['sKey'])){
    		die(json_encode(array('rStatus' => '101', 'error' => 'skey值不能为空','rData'=>$param)));
    	}
    
    	$sql_l ="select ClientID,ClientCompany from ".DB_DATABASEU.DATATABLE."_order_dealers where TokenValue ='".$param['sKey']."'";
    	$clientInfo = $db->get_row($sql_l);
    	include_once ("./class/client.php");
    	$client= new client();
    	$client_info=$client->clientinfo($clientInfo);
    	//药店/诊所是否开户
    	$ySet = new YOpenApiSet();
    	$myinfo = $ySet->getSignInfo($clientInfo['ClientID']);
    	//$myinfo = "";
    	if(empty($myinfo)){
    			
    		die(json_encode(array('rStatus' => '102', 'error' => '药店/诊所未开户', 'rData'=>array('phone'=>$client_info['ClientMobile']))));
    	}
    
    	die(json_encode(array('rStatus' => '100', 'error' => '已开户')));
    
    }
    
    
    //获取银行提示信息 by ltc
    public function get_pay_notice()
    {
    	global $db;
    	$now=time();
    	$bank_notice = $db->get_row("select title,content,start_date,end_date  from ".DATATABLE."_pay_notice where start_date <='".$now."' and end_date >='".$now."' and type=1 order by addtime limit 1 ");
    	if($bank_notice){
    		die(json_encode(array('rStatus' => 100, 'error' => '存在银行提示信息!','rData'=>$bank_notice)));
    	}else{
    		die(json_encode(array('rStatus' => 101, 'error' => '没有提示信息!','rData'=>'')));
    	}
    
    	unset($bank_notice);
    }
    //易极付手机号码自动开户 by ltc
    public function onlinepay($param = array()){
    
    	global $db;
    	if(empty($param['sKey'])){
    		die(json_encode(array('rStatus' => '101', 'message' => 'skey值不能为空','rData'=>$param)));
    	}
    
    	$sql_l ="select ClientID,ClientCompany from ".DB_DATABASEU.DATATABLE."_order_dealers where TokenValue ='".$param['sKey']."'";
    	$clientInfo = $db->get_row($sql_l);
    
    
    	$NetGetWay = new NetGetWay();
    	$accinfo = $NetGetWay->showGetway('yijifu', $clientInfo['ClientCompany']);
    
    	//供应商是否开户
    	if(empty($accinfo)){
    			
    		die(json_encode(array('rStatus' => '101', 'message' => '请在商业完成开户后进行支付')));
    	}
    
    	//药店/诊所是否开户
    	$ySet = new YOpenApiSet();
    	$myinfo = $ySet->getSignInfo($clientInfo['ClientID']);
    	//$myinfo="";
    
    	//修改用户的手机号码
    	include_once ("./class/client.php");
    	$client= new client();
    	$client_infos=$client->clientinfo($clientInfo);
    
    	if(empty($myinfo)){
    			
    		if($client_infos['ClientMobile'] != $param['phone'] && !empty($param['phone']) && $client_infos['ClientMobile'] != ''){
    				
    			$client_infos['ClientMobile']=$param['phone'];
    			$res=$client->edit_user_phone($client_infos);
    
    			if(!$res){
    				echo json_encode(array('rStatus' => '102', 'message' => '药店/诊所手机号码修改失败！'));exit;
    			}
    				
    		}
    	}
    
    	//var_dump($clientInfo);exit;
    	if(empty($myinfo)){//还没开户，执行开户操作
    		//maxy 2017-12-09 原来的地址不正确，修改正确
    		include_once ("../global/module/ClientInfo.module.php");
    		include_once ("../global/class/YopenApiFront.class");
    		$front = new YopenApiFront($clientInfo['ClientID'],$clientInfo['ClientCompany']);
    		$aynResponse = $front->ppmNewRuleRegisterUser($clientInfo);
    		if($aynResponse['status'] == 'error') die(json_encode($aynResponse));
    
    		$myinfo = $ySet->getSignInfo($clientInfo['ClientID']);
    	}
    	//处理完毕，前往收银台
    	echo json_encode(array('rStatus' => '100', 'message' => '系统即将前往支付'));
    	exit;
    }
    
    /**
     * @desc 提交付款
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function submitFinance($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['financeToDate']) || empty($param['financeTotal']) || empty($param['financeAccounts'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '转款日期  / 金额  / 收款帐号  不能为空!';
    	}else{
    		$isAllow = denyRepeatSubmit($param,'submitFinance');
    		if($isAllow){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '此数据已经提交过了，不要重复提交！';
    			return $rdata;
    		}
    		
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    
    		$financeordermsg = '';
    		if($param['financeYufu'] == 'Y')
    		{
    			$financeordermsg = '0';
    		}else{
    			if(!empty($param['financeOrder']))
    			{
    				$financeordermsg = is_array($param['financeOrder']) ? implode(",", $param['financeOrder']) : $param['financeOrder'];
    			}
    		}
    		if(empty($param['financeDevice'])) $param['financeDevice'] = 'Api';
    		$param['financeTotal'] = abs(floatval($param['financeTotal']));
    		$sql_l  = "insert into ".$sdatabase.DATATABLE."_order_finance(FinanceCompany,FinanceClient,FinanceOrder,FinanceAccounts,FinanceTotal,FinancePicture,FinanceAbout,FinanceToDate,FinanceDate,FinanceUser, FinanceDevice) values(".$cid.", ".$cidarr['ClientID'].", '".$financeordermsg."', ".$param['financeAccounts'].", '".$param['financeTotal']."', '', '".$param['financeAbout']."', '".$param['financeToDate']."', ".time().",'".$cidarr['ClientInfo']['ClientTrueName']."', '".$param['financeDevice']."')";
    		$status	= $db->query($sql_l);
    		
    		if(!empty($param['financeOrder']) && empty($param['financeYufu']))
    		{
    			foreach($param['financeOrder'] as $ovar)
    			{
    				if(!empty($ovar)) $db->query("update ".$sdatabase.DATATABLE."_order_orderinfo set OrderPayStatus=1 where OrderSN = '".$ovar."' and OrderCompany=".$cid." and OrderUserID=".$cidarr['ClientID']." and OrderPayStatus=0 ");
    			}
    		}    		

    		if($status){    			
    			//发短信
    			$cidarr['sid'] = "3";
    			$cidarr['message'] = "【".$cidarr['CompanySigned']."】药店:".$cidarr['ClientInfo']['ClientTrueName']."-".$cidarr['ClientInfo']['ClientCompanyName']."于".date('Y-m-d')."转入一笔金额为:".$param['financeTotal']."元的款项,请注意查收.退订回复TD";
    			self::setSendSms($cidarr);    			
    			
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
     * @desc 复制订单
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getCopyOrder($param){
    	global $db,$log;
    
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['orderId'])){
    			$rdata['rStatus'] 	= 101;
    			$rdata['error'] 	= '请选择您要复制的订单!';
    	}else{
    
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$sdatabase 	= $cidarr['Database'];
    		$status = false;
    		
    		$sql = "select ContentID,ContentColor,ContentSpecification,ContentNumber from ".$sdatabase.DATATABLE."_order_cart where OrderID=".$param['orderId']." and CompanyID=".$cid."";
    		$cartData = $db->get_results($sql);
    		if(!empty($cartData)){
	    		foreach($cartData as $var){
	    			$kid = make_kid($var['ContentID'], $var['ContentColor'], $var['ContentSpecification']);
	    			$cartTempArr[$kid] = $var['ContentNumber'];
	    		}
    			 
    			//库存设置
    			$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='product' limit 0,1";
    			$result	= $db->get_row($sql_l);
    			if(!empty($result['SetValue'])) $valuearr = unserialize($result['SetValue']);
    			if(!empty($valuearr)) $setarr = $valuearr; else $setarr = null;
    			if(!empty($setarr['product_number'])){
    				$setLibrary['pn']  = $setarr['product_number'];
    			}else{
    				$setLibrary['pn']  = 'off';
    			}
    			if(!empty($setarr['product_negative'])){
    				$setLibrary['png']  = $setarr['product_negative'];
    			}else{
    				$setLibrary['png']  = 'off';
    			}
    			if($setLibrary['pn'] == 'on' && $setLibrary['png'] == 'off') $controller = 'true'; else $controller = 'false';
    			 
    			$goodsArr = self::listCartCacheGoods($cartTempArr, $setLibrary, $cidarr);
    			$k = 0;
    			foreach($cartTempArr as $key=>$v){
    
    				$pos_color = strpos($key, "_p_");
    				$pos_spec  = strpos($key, "_s_");
    				$color	= '';
    				$spec	= '';
    
    				if(empty($pos_color) && empty($pos_spec)){
    					$id = $key;
    				}else if(!empty($pos_color)){
    					$id	    = substr($key, 0, $pos_color);
    					if(empty($pos_spec)){
    						$color	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_color+3)));
    					}else{
    						$color	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_color+3,$pos_spec-$pos_color-3)));
    						$spec	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_spec+3)));
    					}
    				}else if(!empty($pos_spec)){
    					$id		= substr($key, 0, $pos_spec);
    					$spec	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_spec+3)));
    				}
    
    				$cartArr[$k]['id'] 		= $id;
    				$cartArr[$k]['code'] 	= $goodsArr['all'][$id]['Coding'];
    				$cartArr[$k]['name'] 	= $goodsArr['all'][$id]['Name'];
    				$cartArr[$k]['num'] 	= $v;
    				$cartArr[$k]['compare'] = md5($id.$color.$spec);
    				if(empty($pos_color) && empty($pos_spec)){
    					$cartArr[$k]['stock'] 	= $goodsArr['all'][$id]['OrderNumber'] ? $goodsArr['all'][$id]['OrderNumber'] : 0;
    				}else{
    					$cartArr[$k]['stock'] 	= $goodsArr['cosp'][$cartArr[$k]['compare']] ? $goodsArr['cosp'][$cartArr[$k]['compare']] : 0;
    				}
    				$cartArr[$k]['isStock'] = $controller;
    				$cartArr[$k]['pack'] 	= $goodsArr['all'][$id]['Package'] ? $goodsArr['all'][$id]['Package'] : 0;
    				$cartArr[$k]['price'] 	= $goodsArr['all'][$id]['Price'];
    				$cartArr[$k]['color'] 	= $color;
    				$cartArr[$k]['specify'] = $spec;
    				$cartArr[$k]['pic'] 	= $goodsArr['all'][$id]['Picture'];
    				$cartArr[$k]['units'] 	= $goodsArr['all'][$id]['Units'];
    				$k++;
    			}
    
    			$rdata['rTotal']  = count($cartArr);
    			$rdata['rData']   = $cartArr;
    			$rdata['error']   = '';
    			$rdata['rStatus'] = 100;
    			 
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '没有数据';
    		}
    	}
    
    	return $rdata;
    }    
   

    /**
     * @desc 提交购物车缓存
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function submitCartCache($param){
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
    		$status = false;
    		
    		if($param['action'] == 'clear'){
    			$cartTempMsg = '';
    			$status = write_cart_cache($cidarr,$cartTempMsg,"w");
    		}else{
    			$cartArr = json_decode($param['cartData'],true);
    			if(!empty($cartArr)){
    				foreach($cartArr as $v){
    					$kid = make_kid($v['id'], $v['color'], $v['specify']);
    					$cartTempArr[$kid] = $v['num'];
    				}
    				$cartTempMsg = serialize($cartTempArr);
    				$status = write_cart_cache($cidarr,$cartTempMsg,"w");
    			}
    		}
    
    		if($status === false){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}else{
    			$rdata['error']   = '执行成功';
    			$rdata['rStatus'] = 100;
    		}
    	}
    
    	return $rdata;
    } 

    
    /**
     * @desc 返回购物车缓存数据
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function getCartCache($param){
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
    		$status = false;

    		$cartTempMsg = read_cart_cache($cidarr,"r");
    		if(!empty($cartTempMsg)){
    			$cartTempArr = unserialize($cartTempMsg);
    			
    			//库存设置
    			$sql_l  = "SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$cidarr['CompanyID']." and SetName='product' limit 0,1";
    			$result	= $db->get_row($sql_l);
    			if(!empty($result['SetValue'])) $valuearr = unserialize($result['SetValue']);
    			if(!empty($valuearr)) $setarr = $valuearr; else $setarr = null;
    			if(!empty($setarr['product_number'])){
    				$setLibrary['pn']  = $setarr['product_number'];
    			}else{
    				$setLibrary['pn']  = 'off';
    			}
    			if(!empty($setarr['product_negative'])){
    				$setLibrary['png']  = $setarr['product_negative'];
    			}else{
    				$setLibrary['png']  = 'off';
    			}
    			if(!empty($setarr['product_number_show']))
	    		{
	    			$pns  = $setarr['product_number_show'];
	    		}else{
	    			$pns  = 'off';
	    		}
    			if($setLibrary['pn'] == 'on' && $setLibrary['png'] == 'off') $controller = 'true'; else $controller = 'false';
    			if($setLibrary['pn'] == 'on' && $pns == 'on')  $rdata['isShow'] = 'Y'; else $rdata['isShow'] = 'N';
    			
    			$goodsArr = self::listCartCacheGoods($cartTempArr, $setLibrary, $cidarr);

    			$k = 0;
    			foreach($cartTempArr as $key=>$v){   				
    				
    				$pos_color = strpos($key, "_p_");
    				$pos_spec  = strpos($key, "_s_");
    				$color	= '';
    				$spec	= '';
    				
    				if(empty($pos_color) && empty($pos_spec)){
    					$id = $key;
    				}else if(!empty($pos_color)){
    					$id	    = substr($key, 0, $pos_color);
    					if(empty($pos_spec)){
    						$color	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_color+3)));
    					}else{
    						$color	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_color+3, $pos_spec-$pos_color-3)));
    						$spec	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_spec+3)));
    					}
    				}else if(!empty($pos_spec)){
    					$id		= substr($key, 0, $pos_spec);
    					$spec	= base64_decode(str_replace($this->rp,$this->fp,substr($key, $pos_spec+3)));
    				}    				
    				if(empty($goodsArr['all'][$id]['Name'])) continue;
    				
    				$cartArr[$k]['id'] 		= $id;
    				$cartArr[$k]['code'] 	= $goodsArr['all'][$id]['Coding'];
//     				$cartArr[$k]['name'] 	= htmlspecialchars($goodsArr['all'][$id]['Name']);
    				
    				
    				$cartArr[$k]['name'] = htmlspecialchars_decode($goodsArr['all'][$id]['Name']);
    				$cartArr[$k]['name'] = str_replace(array('<', '>'), array('＜', '＞'), $cartArr[$k]['name']);
    				
    				
    				$cartArr[$k]['num'] 	= $v;
    				$cartArr[$k]['compare'] = md5($id.$color.$spec);
    				if(empty($pos_color) && empty($pos_spec)){
    					$cartArr[$k]['stock'] 	= $goodsArr['all'][$id]['OrderNumber'] ? $goodsArr['all'][$id]['OrderNumber'] : 0; 
    				}else{
    					$cartArr[$k]['stock'] 	= $goodsArr['cosp'][$cartArr[$k]['compare']] ? $goodsArr['cosp'][$cartArr[$k]['compare']] : 0;
    				}   				
    				$cartArr[$k]['isStock'] = $controller;
    				$cartArr[$k]['pack'] 	= $goodsArr['all'][$id]['Package'] ? $goodsArr['all'][$id]['Package'] : 0;
    				$cartArr[$k]['price'] 	= $goodsArr['all'][$id]['Price'];
    				$cartArr[$k]['color'] 	= $color;
    				$cartArr[$k]['specify'] = $spec;  				
    				$cartArr[$k]['pic'] 	= $goodsArr['all'][$id]['Picture'];
    				$cartArr[$k]['units'] 	= $goodsArr['all'][$id]['Units'];
    				
    				$k++;
    			}    			

    			$rdata['rTotal']  = count($cartArr);
    			$rdata['rData']   = $cartArr;
    			$rdata['error']   = '';
    			$rdata['rStatus'] = 100;
    			
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '没有数据';
    		}
    	}
    
    	return $rdata;
    }  

    
    //列出缓存购物车商品信息
    protected function listCartCacheGoods($cartData,$setArr,$cidarr){
    	global $db,$log;
    	$dataCsArr = array();
    	 
    	$cid 		= $cidarr['CompanyID'];
    	$sdatabase 	= $cidarr['Database'];
    	 
    	foreach ($cartData as $key=>$var){
    		$pos = strpos($key, "_");
    		if($pos > 0){ 
    			$idArr[] = substr($key, 0,$pos);
    		}else{
    			$idArr[] = $key;
    		}
    	}
    	$idTempArr = array_unique($idArr);    	 
    	$idStr = ",".implode(",",$idTempArr).",";
    	 
    	if($setArr['pn']=="on"){
    		$result['ison'] = 'on';
    		$sql_l 	 = "SELECT i.ID,i.BrandID,i.Name,i.Coding,i.Price1,i.Price2,i.Price3,i.Units,i.Picture,(case when (n.OrderNumber <0) then 0 else n.OrderNumber end) as OrderNumber FROM ".$sdatabase.DATATABLE."_order_content_index i left join ".$sdatabase.DATATABLE."_order_number n on i.ID=n.ContentID where instr('".$idStr."', concat(',', i.ID, ',' ) ) > 0 and i.CompanyID = ".$cid ." and i.FlagID=0 ";
    
    		$sql     = "select ContentID,ContentColor,ContentSpec,OrderNumber from ".$sdatabase.DATATABLE."_order_inventory_number where  CompanyID=".$cid ." and instr('".$idStr."', concat(',', ContentID, ',') ) > 0";
    		$data_cs = $db->get_results($sql);
    		if(!empty($data_cs)){
    			foreach($data_cs as $lv){
    				$lv['ContentColor'] = base64_decode(str_replace($this->rp,$this->fp,$lv['ContentColor']));
    				$lv['ContentSpec']  = base64_decode(str_replace($this->rp,$this->fp,$lv['ContentSpec']));
    				if($lv['ContentColor'] == '统一') $lv['ContentColor'] = '';
    				if($lv['ContentSpec'] == '统一')  $lv['ContentSpec']  = '';
    				$lkey = md5($lv['ContentID'].$lv['ContentColor'].$lv['ContentSpec']);
    				$dataCsArr[$lkey] = $lv['OrderNumber'];
    			}
    		}
    	}else{
    		$result['ison'] = 'off';
    		$sql_l  = "select ID,CommendID,BrandID,Name,Coding,Price1,Price2,Price3,Units,Picture from ".$sdatabase.DATATABLE."_order_content_index where CompanyID=".$cid ." and instr('".$idStr."', concat(',', ID, ',' )) > 0 and FlagID=0 ";
    	}
    	$datat = $db->get_results($sql_l);
    	if(!empty($cidarr['ClientInfo']['ClientBrandPercent'])) $brandPercent = unserialize($cidarr['ClientInfo']['ClientBrandPercent']);
    	 
    	//获取订购价格
    	for($i=0;$i<count($datat);$i++)
    	{
    		$datat[$i]['Price']      = $datat[$i][$cidarr['ClientInfo']['ClientSetPrice']];
    		if($datat[$i]['CommendID'] == "2"){
    			$datat[$i]['Pencent'] = '10.0';
    		}else{
	    		if(!empty($datat[$i]['BrandID']) && !empty($brandPercent[$datat[$i]['BrandID']])){
	    			$datat[$i]['Pencent'] = $brandPercent[$datat[$i]['BrandID']];
	    		}else{
	    			$datat[$i]['Pencent'] = $cidarr['ClientInfo']['ClientPercent'];
	    		}
			}
			$price3 = setprice3($datat[$i]['Price3'], $cidarr['ClientID'], $cidarr['ClientInfo']['ClientLevel']);
    		if(!empty($price3)){
    			$datat[$i]['Price']   = $price3;
    			$datat[$i]['Pencent'] = '10.0';
			}else{
				$datat[$i]['Price'] = $datat[$i]['Price'] * $datat[$i]['Pencent'] / 10;
			}
			if(!empty($datat[$i]['Picture'])) $datat[$i]['Picture'] = RESOURCE_PATH.$datat[$i]['Picture'];
    		$dataGoods[$datat[$i]['ID']] = $datat[$i];
    	}

    	//整包装出货
    	$sql_c = "select ContentIndexID,Package from ".$sdatabase.DATATABLE."_order_content_1 where CompanyID=".$cid ." and instr('".$idStr."', concat(',', ContentIndexID, ',') ) > 0  ";
    	$datac = $db->get_results($sql_c);
    	if(!empty($datac)){
    		foreach($datac as $cvar){
    			if(empty($cvar['Package'])) $cvar['Package'] = 0;
    			$dataGoods[$cvar['ContentIndexID']]['Package']  =  $cvar['Package'];
    		}
    	}    	
    	
    	$result['all']  = $dataGoods;
    	$result['cosp'] = $dataCsArr;
    
    	return $result;
    	unset($result,$dataGoods,$data_cs,$datat);
    }
    
    /**
     * @desc 提交反馈信息
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function submitFeedback($param){
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
    
    		$sql_l  = "insert into ".DB_DATABASEU.DATATABLE."_common_feedback(CompanyID,ClientID,FeedbackType,ClientName,Contact,Content,CreateDate) values(".$cid.", ".$cidarr['ClientID'].", '".$param['feedbackType']."','".$cidarr['ClientInfo']['ClientTrueName']."', '".$param['contact']."', '".$param['content']."', ".time().")";
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
     * 获取网关信息
     *@param array $param(sKey,parentId,begin,step) key,起始值，步长
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getGetWay($param){
    	global $db,$log;
    
    	if (empty ( $param['sKey'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']); //取公司ID,Database
    		$sdatabase 	= $cidarr['Database'];
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}else{
   				$sql_l  = "select AccountsID,AccountsNO,AccountsName from ".$sdatabase.DATATABLE."_order_accounts where AccountsCompany=".$cidarr['CompanyID']." and AliPhone='T' and PayPartnerID!='' and PayKey!='' limit 0,1 ";
    			$result	= $db->get_row($sql_l);
    			if(empty($result)){
    				$rdata['AliData']	= '';
    			}else{
    				$rdata['AliData']	= $result;
    			}
    			
    			$sql_l  = "select SignNO,SignAccount,AccountType,IsDefault from ".DB_DATABASEU.DATATABLE."_order_getway where CompanyID=".$cidarr['CompanyID']." and GetWay='yijifu' and Status='T'  order by GetWayID asc";
    			$result	= $db->get_results($sql_l);  
    			if(empty($result)){
    				if(empty($rdata['AliData'])){
    					$rdata['rStatus']	= 101;
    					$rdata['error']		= '无符合条件数据';
    				}else{
    					$rdata['rStatus']	= 100;
    				}
    			}else{
    				$count = 0;
    				foreach ($result as $key => $var){
    					if($var['IsDefault'] == 'Y'){
    						$count = 1;
    						break;
    					}
    				}
    				if (empty($count)){
    					$result[0]['IsDefault'] = 'Y';
    				}
    				
    				$sql_l  = "select YapiUserId,ApiID,YapiIsact from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=".$cidarr['CompanyID']." and ClientID=".$cidarr['ClientID']." limit 0,1 ";
    				$openApiData = $db->get_row($sql_l);  

    				if(empty($openApiData)){
    					$rdata['openapi'] = 0;
    					$rdata['openapiIscat'] = 0;
    				}else{
    					//tubo 新增当开户未激活时，去易极付对接确认是否是未激活
    					if(empty($openApiData['YapiIsact'])){
    						$cid		= $cidarr['CompanyID'];
			    			$clientid	= $cidarr['ClientID'];
			
			    			$openApi    = new YopenApiFront($clientid,$cid);
			    			$send['userId']			   = $openApiData['YapiUserId'];
			    			
			    			$returnarr = $openApi->setGetway()->userInfo($send);
    				        
    						if(!empty($returnarr)){
			    				$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
								$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区
								
								if(($returnarr['resultCode'] == 'EXECUTE_SUCCESS')){
								 	$logType = 'logInfo';
								}else{
									$logType = 'logError';
								}
								$logMsg = "【".$returnarr['resultMessage']."】【Service：".$returnarr['service']."】：".http_build_query($returnarr);
								
								//获取映射关系
								$YOpenApiSet 	= new YOpenApiSet();
								$dhbOrder		= $YOpenApiSet->getMap($returnarr['orderNo']);
				    			
				    			//初始化
								$YOpenApiDo = new YOpenApiDo($returnarr, $dhbOrder['CompanyID']);
								$kLog->logInfo($logMsg);
								if($returnarr['userStatus'] == 'NORMAL'){
									$service = $returnarr['service'];
									$returnarr['dhbUserid'] = $clientid;
									$returnarr['ClientCompany'] = $cid;
									$YOpenApiDo->$service($returnarr);
									$sql_l  = "select ApiID,YapiIsact from ".DB_DATABASEU.DATATABLE."_yjf_openapi where ClientCompany=".$cidarr['CompanyID']." and ClientID=".$cidarr['ClientID']." limit 0,1 ";
    								$openApiData = $db->get_row($sql_l);  
								}
			    			}
    					}
    					//tubo 结束
    					$rdata['openapi'] = 1;
    					$rdata['openapiIscat'] = $openApiData['YapiIsact'];	
    				}
    				
    				$rdata['rStatus']	= 100;
    				$rdata['rTotal'] 	= count($result);
    				$rdata['rData']		= $result;    				
    			}		
    		}
    	}
    	return $rdata;
    } 
    
    /**
     * 获取注册所需信息
     *@param array $param(sKey,wid,parentId) key,
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getRegiester($param){
    	global $db,$log;
    
    	if (empty ( $param['wid'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		$cidarr = $db->get_row ( "select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company  where CompanyID='".intval($param['wid'])."' limit 0,1" );
    		$cid 	= $cidarr['CompanyID']; 
    		if(empty($cidarr['CompanyDatabase'])) $sdatabase = DB_DATABASE.'.'; else $sdatabase = DB_DATABASE."_".$cidarr['CompanyDatabase'].'.';
    		
    		$setarr = self::getproductset('product',$cidarr);
    		if(empty($setarr['regiester_type']) || $setarr['regiester_type']  != "on"){
    			$rdata['rStatus'] = 110;
    			$rdata['error']   = '未开放注册，请联系供应商！';
    		}
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
    			$rdata['CompanyName']     = $cidarr['CompanyName'];
    			$rdata['CompanySigned']   = $cidarr['CompanySigned']; 
    			$rdata['CompanyPrefix']   = $cidarr['CompanyPrefix'];
    		}
    	}
    	return $rdata;
    }

    /**
     * 验证帐号唯一
     *@param array $param(sKey,wid,parentId) key,
     *@return array $rdata(rStatus,error,rData) 状态，提示信息，数据
     *@author seekfor
     */
    public function getOnlyName($param){
    	global $db,$log;
    
    	if (empty ( $param['onlyClientName'] ))
    	{
    		$rdata['rStatus'] = 110;
    		$rdata['error']   = '参数错误';
    	}else{
    		if(!is_filename($param['onlyClientName']) || strlen($param['onlyClientName']) < 1 || strlen($param['onlyClientName']) > 30){  //tubo 由原来18位增加到30位 2016-05-04
    			$rdata['error'] 	= '请填写正确的帐号（必需用数字字母和下划线组成）！';
    			$rdata['rStatus']	= 101;
    			return $rdata;
    		}

    		$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DB_DATABASEU.DATATABLE."_order_dealers where ClientName='".$param['onlyClientName']."' limit 0,1");
    		
    		if($clientinfo['orwname'] > 0 ){
    			$rdata['rStatus']	= 101;
    			$rdata['error'] 	= '此帐号(“'.$param['onlyClientName'].'”)已存在，请换名再试！';
    		}else{
    			$rdata['rStatus']	= 100;
    			$rdata['error'] 	= '此帐号可用';
    		}
    	}
    	return $rdata;
    }

    
    /**
     * @desc 提交加盟信息
     * @param array $param (sKey)
     * @return array $rdata
     */
    public function submitRegiesterClient($param){
    	global $db,$log;
    	//$log->logInfo('submitRegiesterClient in', $param);
    
    	if(empty($param['wid'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['clientName']) || empty($param['clientPassword'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '请填写帐号、密码、单位名称！';
    	}elseif(empty($param['clientMobile']) || empty($param['clientTrueName']) || empty($param['clientCompanyName'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '请填写手现号、联系人、单位名称！';	
    	}else{
    		$rdata['rStatus'] 	= 101;
    		$cidarr = $db->get_row ( "select CompanyID,CompanyName,CompanySigned,CompanyPrefix,CompanyDatabase from ".DB_DATABASEU.DATATABLE."_order_company  where CompanyID='".intval($param['wid'])."' limit 0,1" );
    		$cid 	= $cidarr['CompanyID']; 
    		if(empty($cidarr['CompanyDatabase'])) $sdatabase = DB_DATABASE.'.'; else $sdatabase = DB_DATABASE."_".$cidarr['CompanyDatabase'].'.';
    		
    		$setarr = self::getproductset('product',$cidarr);
    		$client_flag = isset($setarr['regiester_type_status']) ? $setarr['regiester_type_status'] : 9;
 		
    		$param['clientName']	  = strtolower($param['clientName']);
    		$param['clientPassword']  = strtolower($param['clientPassword']);
    		if(!is_filename($param['clientName']) || strlen($param['clientName']) < 3 || strlen($cidarr['CompanyPrefix']."-".$param['clientName']) > 30){
    			$rdata['error'] 	= '请填写正确的帐号（必需用数字字母和下划线组成）！';
    			return $rdata;
    		}
    		
    		if(!is_filename($param['clientPassword']) || strlen($param['clientPassword']) < 3 || strlen($param['clientPassword']) > 18 ){
    			$rdata['error'] 	= '请填写正确的密码（必需用数字字母和下划线组成）！';
    			return $rdata;
    		}    		
    		$param['clientName'] = $cidarr['CompanyPrefix']."-".$param['clientName'];
    		if(empty($param['ClientPercent'])) $param['ClientPercent'] = '10.0';
    		$clientinfo = $db->get_row("SELECT count(*) as orwname FROM ".DB_DATABASEU.DATATABLE."_order_dealers where ClientName='".$param['clientName']."' limit 0,1");
    		
    		if($clientinfo['orwname'] > 0 ){
    			$rdata['error'] 	= '此帐号已存在，请换名再试！';
    			return $rdata;
    		}
    		$dmobile = '';
    		if(!empty($param['clientMobile']))
    		{
    			if(!is_phone($param['clientMobile'])){
    				$rdata['error'] 	= '请输入正确的手机号码!';
    				return $rdata;
    			}else{
    				$clientminfo = $db->get_row("SELECT count(*) as orwname FROM ".DB_DATABASEU.DATATABLE."_order_dealers where ClientMobile='".$param['clientMobile']."' limit 0,1");
    				if($clientminfo['orwname'] > 0){
    					$dmobile = '';
    				}else{
    					$dmobile = $param['clientMobile'];
    				}
    			}
    		}
    		
    		$isAllow = denyRepeatSubmit($param,'submitRegiesterClient');
    		if($isAllow){
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '此数据已经提交过了，不要重复提交！';
    			return $rdata;
    		}    		
    		$param['clientFlag'] = $client_flag;
    		
    	    $upsql = "insert into ".DB_DATABASEU.DATATABLE."_order_dealers(ClientCompany,ClientName,ClientPassword,ClientMobile,LoginDate,ClientFlag) values(".$cid.", '".$param['clientName']."', '".$param['clientPassword']."','".$dmobile."',".time().",".intval($param['clientFlag']).")";
    		//$log->logInfo('submitClient add u', $upsql);
    		if($db->query($upsql)){
    				$inid    =  $db->insert_id;
    				$insql	 = "insert into ".$sdatabase.DATATABLE."_order_client(
    					ClientID,ClientCompany,ClientLevel,ClientArea,ClientName,ClientCompanyName,ClientCompanyPinyi,ClientNO,ClientTrueName,ClientEmail,ClientPhone,ClientFax,ClientMobile,ClientAdd,ClientAbout,ClientDate,
    					ClientShield,ClientSetPrice,ClientPercent,ClientFlag)
    					values(".$inid.",".$cid.",'',".$param['clientArea'].", '".$param['clientName']."', '".$param['clientCompanyName']."', '', '', '".$param['clientTrueName']."', '".$param['clientEmail']."', '".$param['clientPhone']."', '".$param['clientFax']."', '".$param['clientMobile']."', '".$param['clientAdd']."', '".$param['clientAbout']."',".time().", '','Price1', '10.0', ".intval($param['clientFlag']).")";
    				$status = $db->query($insql);
    				//$log->logInfo('submitRegiesterClient add', $insql);
    		} 
    		if($status){
    			//$rdata['insertId'] = $inid;
    			if($param['clientFlag'] == "9"){
    				$param['isLogin'] = 'F';
    				$rdata['error']   = '您已成功注册，管理员会尽快审核，审核完成即可登录！';
    			}else{
    				$param['isLogin'] = 'T';
    				$rdata['error']   = '您已成功注册，现在去看看！';
    			}
    			
    			$rdata['rStatus'] = 100;
    		}else{
    			$rdata['rStatus'] = 101;
    			$rdata['error']   = '执行不成功';
    		}
    	}
    
    	return $rdata;
    }
       
    
	/**以下为微信接口部分**/    

    /**
     * @desc 获取token
     * @param array $param ()
     * @return array $rdata
     */
    public function weixinGetToken(){
    	
    	$token = get_token();
    	
    	$rdata['rData']   = $token;
    	$rdata['error']   = '执行成功';
    	$rdata['rStatus'] = 100;
    	
    	return $rdata;
    }  

    /**
     * @desc 获取用户基本信息
     * @param array $param openId：微信号
     * @return array $rdata
     */
    public function weixinGetBaseInfo($param){
    	 
    	$token = get_token();
    	if(!empty($param['openId']) && !empty($token)){
    		$get_url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$param['openId'].'&lang=zh_CN';
    		$rTmpData = curl_get_data($get_url);
    		$rdata['rData']   = $rTmpData;
    		$rdata['error']   = '执行成功';
    		$rdata['rStatus'] = 100;
    	}else{
    		$rdata['rData']   = '';
    		$rdata['error']   = '执行不成功';
    		$rdata['rStatus'] = 101;
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
     * @desc 获取企业的openid
     * @param array $param ()
     * @return array $rdata
	 * @ 2016.3.1 新增
     */
    protected function weixinqyGetOpenId($param){
		global $db;
    	if(empty($param['code'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    		return $rdata;
    	}
    	
    	$wid = intval($param['wid']);

        $temptoken = $db->get_row("select CorpID,Permanent_code from ".DB_DATABASEU.DATATABLE."_order_weixinqy where CompanyID=".$wid."");

		$tempsuite_ticket=file(WEB_TM_URL."data/ticket.txt");
		$tempsuite_access_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_suite_token",json_encode(array('suite_id'=>Suite_id,'suite_secret'=>Suite_secret,'suite_ticket'=>trim($tempsuite_ticket[0])))); //获取应用套件令牌
	     $suite_access_token=$tempsuite_access_token['suite_access_token'];
         //$log->logInfo('suite_access_token return',$tempsuite_access_token);

		 $tempaccess_token=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/service/get_corp_token?suite_access_token=".$suite_access_token,json_encode(array('suite_id'=>Suite_id,'auth_corpid'=>$temptoken['CorpID'],'permanent_code'=>$temptoken['Permanent_code']))); //通过永久授权码获取ACCESS_TOKEN
	    //$log->logInfo('tempaccess_token return',$tempaccess_token);


    	$getUrl ='https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$tempaccess_token['access_token'].'&code='.$param['code'];
		$getdata = curl_get_data($getUrl);// 获取企业用户userid

		if(!empty($getdata['UserId'])){//获取到userid 转换成openid
			$tempopenid=curl_post_data("https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=".$tempaccess_token['access_token'],json_encode(array('userid'=>$getdata['UserId'])));//转换openid
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
     * @desc 绑定帐号
     * @param array $param ()
     * @return array $rdata
     */
    public function weixinBindAccount($param){
    	global $db,$log;
    	$openId = '';
    	$log->logInfo('weixinBindAccount_test', $param);
    	if(empty($param['openId'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}elseif(empty($param['Username']) || empty($param['Password'])){
    		$rdata['rStatus'] 	= 101;
    		$rdata['error'] 	= '帐号、密码 不能为空!';
    	}else{
    		//$baseInfo = self::weixinGetBaseInfo($param); 
    		$openId = $param['openId'];
    		unset($param['openId']);
    		
    		$param['backClientID'] = 1; //返回ClientID
    		$loginData = self::getTokenValue($param);
			if($loginData['rStatus'] == '100'){
				//微信帐号
				if(empty($baseInfo['nickname'])) $baseInfo['nickname'] = '';				
				if(!empty($openId)){
					$insql = "insert into ".DB_DATABASEU.DATATABLE."_order_weixin(WeiXinID,UserID,UserType,NickName,CompanyID) values('".$openId."',".$loginData['rData']['ClientID'].",'C', '".$baseInfo['nickname']."',".$loginData['rData']['CompanyID'].")";
					$db->query($insql);
					//$log->logInfo('weixinBindAccount2', $insql);
				}
			}
	    	$rdata   = $loginData;
    	}
    	return $rdata;
    }    
    
    /**
     * @desc 微信授权获取skey
     * @param array $param ()
     * @return array $rdata
     */
    public function weixinGetTokenValue($param){
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
    		$rdata = self::getTokenValue($param);
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
    public function weixinqyGetTokenValue($param){
    	 
    	if(empty($param['code'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{  
    		if(empty($param['openId'])){
    			$rOpendId = self::weixinqyGetOpenId($param);//更换企业接口获取openid
    			if($rOpendId['rStatus'] != '100'){
    				return $rOpendId;
    			}else{
    				$param['openId'] = $rOpendId['rData']['openid'];
    			}	
    		}
    		$param['weixin'] = 'qy';
    		$rdata = self::getTokenValue($param);
    	}
    	$rdata['openId'] = $param['openId'];
    	$rdata['appUrl'] = WEB_API_URL.'api.php';
    	
    	return $rdata;
    }
    
    /**
     * @desc 微信JSSDK配置信息
     * @param array $param ()
     * @return array $rdata
     */
    public function weixinGetConfig($param){
    	global $log;
    	
    	if(empty($param['sKey'])){
    		$rdata['rStatus'] 	= 110;
    		$rdata['error'] 	= '参数错误!';
    	}else{
    		$cidarr = self::getCompanyInfo($param['sKey']);
    		if($cidarr['rStatus'] != 100){
    			return $cidarr;
    		}
    		$cid 		= $cidarr['CompanyID'];
    		$conf_file  = SITE_ROOT_PATH."/wx/wx_".$cid.".php";
    		if(file_exists($conf_file)){
    			include ($conf_file);
    		}else{
    			include (SITE_ROOT_PATH."/wx/wx_0.php");
    		}
    		include (SITE_ROOT_PATH."/class/jssdk.php");
    		$jssdk = new JSSDK();
    		$signPackage = $jssdk->getSignPackage();
    		if(empty($signPackage['appId'])){
    			$rdata['rData']		= $signPackage;
    			$rdata['error']   	= '获取不成功';
    			$rdata['rStatus'] 	= 101;
    		}else{
	    		$rdata['rData']		= $signPackage;
	    		$rdata['error']   	= '执行成功';
	    		$rdata['rStatus'] 	= 100;
    		}
    	}
    	
    	return $rdata;
    }  

    /**
     * 根据系统配置，检查当前时间是否允许下单
     * @param array 入参数组array('companyid')
     * @author wanjun
     * @return bool
     */
    private function check_ordertime($param = array()){
    	global $db;
    	
    	//读取订单时间配置
    	$productset  = $db->get_row("SELECT SetID,SetName,SetValue FROM ".DB_DATABASEU.DATATABLE."_order_companyset where SetCompany = ".$param['companyid']." and SetName='product' limit 1");
    	if(!empty($productset['SetValue'])) $productset = unserialize($productset['SetValue']);
    	
    	//设置当前订货时间
    $currWeek = array(
        1 => "周一",
        2 => "周二",
        3 => "周三",
        4 => "周四",
        5 => "周五",
        6 => "周六",
        7 => "周日",
    );
    	
    $currWeekMsg = "每".$currWeek[$productset['ordertime']['date_start']]." ".$productset['ordertime']['time_start'];
    $currWeekMsg .= " 到 每".$currWeek[$productset['ordertime']['date_end']]." ".$productset['ordertime']['time_end'];


    $nextWeekMsg = "每".$currWeek[$productset['ordertime']['date_start']]." ".$productset['ordertime']['time_start'];
    $nextWeekMsg .= " 到 次".$currWeek[$productset['ordertime']['date_end']]." ".$productset['ordertime']['time_end'];

    $rmsg = $productset['ordertime']['date_start'] > $productset['ordertime']['date_end'] ?  $nextWeekMsg : $currWeekMsg;

    $rmsg = "请在以下时间段提交订单：".$rmsg."。若急需订购，请联系供应商！";

    if(!empty($productset) && isset($productset)){

        $ordertime = $productset['ordertime'];
        if(empty($ordertime) || $ordertime['time_show'] == 'off')
            return array('status' => true, 'rmsg' => $rmsg);
        else
        {
            $dateS = $ordertime['date_start'];
            $dateE = $ordertime['date_end'];
            $timeS = $ordertime['time_start'];
            $timeE = $ordertime['time_end'];

            $weekarray = array("7","1","2","3","4","5","6");
            $nowweekday = $weekarray[date("w")];
            $nowtime = date('H:i',time());

            if($dateE >= $dateS){
                if($dateE == $dateS && $nowweekday == $dateE && $nowtime >= $timeS && $nowtime <= $timeE){//逻辑太混乱，简单增加同一天的限制 tubo 2016-06-12
                    $return = true;}
                else if($nowweekday == $dateS && $dateE > $dateS && $nowtime >= $timeS )
                    $return = true;
                else if($nowweekday == $dateE && $dateE > $dateS && $nowtime <= $timeE)
                    $return = true;
                else if($nowweekday > $dateS && $nowweekday < $dateE)
                    $return = true;
                else
                    $return = false;

                //本组返回
                return array('status' => $return, 'rmsg' => $rmsg);
            }else //跨周
            {
                if($nowweekday == $dateS && $nowtime >= $timeS )
                    $return = true;
                else if($nowweekday == $dateE && $nowtime <= $timeE)
                    $return = true;
                else if($nowweekday <=7 && $nowweekday > $dateS)
                    $return = true;
                else if($nowweekday < $dateE)
                    $return = true;
                else
                    $return = false;

                //本组返回
                return array('status' => $return, 'rmsg' => $rmsg);
            }
        }
    }else
        return array('status' => true, 'rmsg' => $rmsg);
    }//END check_ordertime()
    
    
     /**
     * 存储体验联系人资料
     *
     * @param unknown_type $param
     */
    public function storeLinkMan($param = array()){
    	global $db,$log;
    	
		//$log->logInfo('experience linkman info in', $param);
    
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

    
}//EOC controller
?>