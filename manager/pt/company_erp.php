<?php
$menu_flag = "manager";
include_once ("header.php");
// $erp_version = include_once("inc/erp_version.php");
$erp_company = include_once("inc/erp_company.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);


if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');

if(!isset($in['status'])) {
    $in['status'] = 'T';
}

?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name='robots' content='noindex,nofollow' />
        <title><? echo SITE_NAME;?> - 管理平台</title>
        <link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="css/jquery.treeview.css" />
        <link rel="stylesheet" href="css/showpage.css" />
        <link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
        <script src="../scripts/jquery.min.js" type="text/javascript"></script>
        <script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
        <script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
        <script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
        <script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
        <script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

        <script type="text/javascript">
            $(function() {

                $("#tree").treeview({
                    collapsed: true,
                    animated: "medium",
                    control:"#sidetreecontrol",
                    persist: "location"
                });
                $("#bdate").datepicker({changeMonth: true,	changeYear: true});
                $("#edate").datepicker({changeMonth: true,	changeYear: true});
            })

            function show_change_log(cid,cname)
            {
                $("#windowtitle").html(cname+' - 时间线');
                $('#windowContent').html('<iframe src="show_change_log.php?ID='+cid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
                $.blockUI({
                    message: $('#windowForm6'),
                    css:{
                        width: '620px',height:'580px',top:'8%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
            }

            function show_pay(cid,cname)
            {
                $("#windowtitle").html(cname+' - 在线支付');
                $('#windowContent').html('<iframe src="show_pay.php?ID='+cid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
                $.blockUI({
                    message: $('#windowForm6'),
                    css:{
                        width: '620px',height:'580px',top:'8%'
                    }
                });
                $('.blockOverlay').attr('title','点击返回').click($.unblockUI);
            }
        </script>
    </head>



    <body>
    <?php include_once ("top.php");?>
    <?php include_once ("inc/son_menu_bar.php");?>

    <div id="bodycontent">
    <div class="lineblank"></div>

    <div id="searchline">
        <div class="leftdiv">

            <form id="FormSearch" name="FormSearch" method="get" action="company_erp.php">
                <input name="d" type="hidden" value="<?php echo $in['d']; ?>"/>
                <label>
                    &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" class="inputline" />
                </label>
                <label style="display:none;">
                    <select id="iid" name="iid"  style="width:165px;" class="select2">
                        <option value="" >⊙ 所有行业</option>
                        <?php
                        $n = 0;
                        $accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry where ORDER BY IndustryID ASC ");

                        foreach($accarr as $accvar)
                        {
                            $n++;
                            $industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];

                            if($in['iid'] == $accvar['IndustryID']) $smsg = 'selected="selected"'; else $smsg ="";

                            echo '<option value="'.$accvar['IndustryID'].'" '.$smsg.' title="'.$accvar['IndustryName'].'"  >'.$accvar['IndustryName'].'</option>';
                        }
                        ?>
                    </select>
                </label>
                <label>
                    <select id="aid" name="aid"  style="width:135px;" class="select2">
                        <option value="" >⊙ 所有地区</option>
                        <?php
                        $n = 0;
                        $sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city  ORDER BY AreaParent asc,AreaID ASC ");
                        foreach($sortarr as $areavar)
                        {
                            $n++;
                            if($areavar['AreaID']==$in['aid']) $areainfoselected = $areavar;
                            $areaarr[$areavar['AreaID']] = $areavar['AreaName'];
                        }
                        echo ShowTreeMenu($sortarr,0,$in['aid'],1);
                        ?>
                    </select>
                </label>
                <label>
                    <select id="status" name="status"  style="width:135px;" class="select2">
                        <option value="" >⊙ ERP开通状态</option>
                        <option value="T" <?php echo $in['status'] == 'T' ? "selected='selected'" : ""; ?>>已开通</option>
                        <option value="F" <?php echo $in['status'] == 'F' ? "selected='selected'" : ""; ?>>未开通</option>
                    </select>
                </label>
                <label>
                    <select id="version" name="version"  style="width:135px;" class="select2">
                        <option value="" >⊙ ERP类型</option>
                        <?php
                        
                        foreach($erp_company['yunup']['support'] as $ver){
                            echo "<option value='".$ver."' ".($ver == $in['version'] ? "selected='selected'" : "").">".$ver."</option>";
                        }
                        ?>
                    </select>
                </label>
                <label>
                    <select id="develop" name="develop"  style="width:135px;" class="select2">
                        <option value="" >⊙ 接口开发方</option>
                        <option value="YTTX" <?php echo $in['develop'] == 'YTTX' ? "selected='selected'" : ""; ?>>医统天下</option>
                        <option value="OTHER" <?php echo $in['develop'] == 'OTHER' ? "selected='selected'" : ""; ?>>第三方开发</option>
                    </select>
                </label>

                <!--<label>
                    <select id="date_field" name="date_field"  style="width:105px;" class="select2">
                        <option value="end_date" <?php /*if($in['date_field'] == 'end_date') echo 'selected="selected"';*/?> >到期时间</option>
                        <option value="begin_date" <?php /*if($in['date_field'] == 'begin_date') echo 'selected="selected"';*/?> >开通时间</option>
                    </select>
                </label>
                <label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<?/* if(!empty($in['bdate'])) echo $in['bdate'];*/?>" /> - </label>
                <label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<?/* if(!empty($in['edate'])) echo $in['edate'];*/?>" />

                </label>-->
                <label>

                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

                </label>
            </form>
        </div>
    </div>

    <div class="line2"></div>

    <div class="bline">
        <?php

        $sqlmsg = '';
        if(empty($in['num'])){
            if(!empty($in['iid']))  $sqlmsg .= " and c.CompanyIndustry=".$in['iid']." "; else $in['iid'] = '';
            if(!empty($in['aid']))
            {
                if(empty($areainfoselected['AreaParent']))
                {
                    $sqlmsg .= " and ( c.CompanyArea=".$in['aid']." or c.CompanyArea in (SELECT AreaID FROM ".DATABASEU.DATATABLE."_order_city where  AreaParent=".$in['aid']." ORDER BY AreaID ASC) ) ";
                }else{
                    $sqlmsg .= " and c.CompanyArea=".$in['aid']." ";
                }
            }else{
                $in['aid'] = '';
            }
            if(!empty($in['gid']))  $sqlmsg .= " and c.CompanyAgent=".$in['gid']." "; else $in['gid'] = '';
            if($in['date_field'] == 'begin_date'){
                $datefield = 's.CS_BeginDate';
            }else{
                $datefield = 's.CS_EndDate';
            }
            if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
            if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";

            //if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' ";
            if(!empty($in['kw'])) {
                $sqlmsg .= " AND (";
                $likeArr = array();
                foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail') as $lk) {
                    $likeArr[] = " {$lk} like '%".$in['kw']."%'";
                }
                $sqlmsg .= implode(" OR " , $likeArr);
                $sqlmsg .= ")";
            }

            if($in['status'] == 'T') {
                $sqlmsg .= " AND l.SerialNumber <> '' ";
            } else if($in['status'] == 'F') {
                $sqlmsg .= " AND l.SerialNumber IS NULL ";
            }

            if(!empty($in['version'])) {
                $sqlmsg .= " AND l.Version='{$in['version']}' ";
            }

            if(!empty($in['develop'])) {
                $sqlmsg .= " AND l.Develop='{$in['develop']}' ";
            }

        }

        //$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0'  ".$sqlmsg."  ");
        $data_cnt = $db->get_var("SELECT COUNT(*) AS Total FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_api_serial as l ON l.CompanyID=c.CompanyID where c.CompanyFlag='0' and s.CS_Flag='T' ".$sqlmsg);
        $page = new ShowPage;
        $page->PageSize = 100;
        $page->Total = (int)$data_cnt;
        $page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],'gid' => $in['gid'] , 'date_field' => $in['date_field'] , 'bdate' => $in['bdate'] , 'edate' => $in['edate'],"status"=>$in['status'],"version"=>$in['verison'],'develop' => $in['develop']);

        //$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0' ".$sqlmsg." ORDER BY c.CompanyID DESC limit 0,500";
//         $datasql   = "SELECT c.*,s.*,l.SerialNumber,l.Password,l.Status,l.Version,l.TransferCheck,l.RunStatus,l.Develop,l.TransStart FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_api_serial as l ON l.CompanyID=c.CompanyID where c.CompanyFlag='0' and s.CS_Flag='T' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
        $datasql   = "SELECT c.*,s.*,l.SerialNumber,l.Password,l.Status,l.Version,l.TransferCheck,l.RunStatus,l.Develop,l.TransStart,l.isPrice FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_api_serial as l ON l.CompanyID=c.CompanyID where c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
        $list_data = $db->get_results($datasql . ' ' . $page->OffSet());
        ?>




        <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td class="bottomlinebold">
                        <!--<input type="button" name="newbutton" id="newbutton" value=" 新增客户 " class="button_2" onclick="javascript:window.location.href='company_add.php'" />&nbsp;&nbsp;&nbsp;&nbsp;[<a href="manager.php?num=more">MORE</a>]-->
                    </td>
                    <td align="right"  height="30" class="bottomlinebold">
                        <? echo $page->ShowLink('company_erp.php');?>
                    </td>
                </tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">

                <thead>
                <tr>
                    <td width="6%" class="bottomlinebold">编号</td>
                    <td class="bottomlinebold">公司名称/系统名称</td>
                    <td class="bottomlinebold" width="8%">ERP类型</td>
                    <td class="bottomlinebold" width="25%">序列号</td>
                    <td class="bottomlinebold" width="7%">接口开发方</td>
                    <td width="15%" class="bottomlinebold">地区/行业</td>
                    <td width="11%" class="bottomlinebold">联系人/联系方式</td>
                    <td width="5%" class="bottomlinebold">状态</td>
                    <td width="8%" class="bottomlinebold" align="center">管理</td>
                </tr>
                </thead>

                <tbody>

                <?php
                if(!empty($list_data))
                {
                    foreach($list_data as $lsv)
                    {
                        /**
                        if($_GET['m'] == 'add'){
                        if(!(file_exists (RESOURCE_PATH.$lsv['CompanyID'])))
                        {
                        _mkdir(RESOURCE_PATH,$lsv['CompanyID']);
                        echo RESOURCE_PATH.$lsv['CompanyID'];
                        }
                        }
                         **/
                        ?>

                        <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
                            <td >10<? echo $lsv['CompanyID'];?></td>
                            <td ><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?>
                            <span style="color:red;"><?  echo ' '.$yunType[$lsv['CompanyType']];?></span>
                            </a></td>

                            <td>
                                <?php echo $lsv['Version']; ?>
                            </td>
                            <td>
                                <?php echo $lsv['SerialNumber']; ?> <br/>
                                <?php echo $lsv['Password']; ?>
                            </td>
                            <td>
                                <?php if($lsv['Develop'] == 'YTTX') { echo "医统天下"; 
                                 } else if($lsv['Develop'] == 'TEENY') { echo "天力精算"; 
                                } else if($lsv['Develop'] == 'OTHER') { echo "第三方"; } ?>                    
                            </td>
                            <td><?php
                                echo $industryarr[$lsv['CompanyIndustry']].'<br />'.$areaarr[$lsv['CompanyArea']];
                                ?></td>

                            <td ><? echo $lsv['CompanyContact'].'<br />'.$lsv['CompanyMobile'];?></td>
                            <td>
                                <?php if($lsv['SerialNumber']) {
                                    echo $lsv['Status'] == 'T' ? '[已开通]' : '[未开通]';
                                    echo "<br/>";
                                    echo $lsv['RunStatus'] == 'T' ? '[运行中]' : '[未运行]';
                                }?>
                            </td>
                            <td align="center" title="<?php echo $lsv['CompanyRemark'];?>">
                                <?php
                                if(empty($lsv['SerialNumber'])){
                                    echo "&nbsp;&nbsp;[<a href='javascript:;' class='showErp' data-company='".$lsv['CompanyID']."' data-cp='".$lsv['CompanyType']."' title='开通ERP接口'>开通</a>]";
                                }else{
                                    echo "&nbsp;&nbsp;[<a href='javascript:;' id='show".$lsv['CompanyID']."' class='showErp' data-serial='".$lsv['SerialNumber']."' data-password='".$lsv['Password']."' data-version='".$lsv['Version']."' data-status='".$lsv['Status']."' data-run-status='".$lsv['RunStatus']."' data-transfer-check='".$lsv['TransferCheck']."' data-trans-start='".($lsv['TransStart'] == 0 ? '' : date('Y-m-d',$lsv['TransStart']))."' data-company='".$lsv['CompanyID']."' data-develop='".$lsv['Develop']."' data-cp='".$lsv['CompanyType']."' data-isprice = '".$lsv['isPrice']."' title='查看ERP接口信息'>查看".($lsv['Status']=='F' ? "-<font color='red'>已停用</font>" : "")."</a>]";
                                    echo "&nbsp;&nbsp;[<a href='javascript:void(0)' onclick=\"show_change_log('".$lsv['CompanyID']."','".$lsv['CompanyName']."');\" >日志</a>]";
                                }
                                ?>
                            </td>
                        </tr>
                    <? } }else{?>

                    <tr>
                        <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
                    </tr>
                <? }?>
                </tbody>
            </table>

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="100" align="center"><?php echo count($list_data);?></td>
                    <td align="right"  height="30" >
                        <? echo $page->ShowLink('company_erp.php');?>
                    </td>
                </tr>
            </table>

            <INPUT TYPE="hidden" name="referer" value ="" >



        </form>

    </div>

    <br style="clear:both;" />
    </div>

    <?php include_once ("bottom.php");?>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>

    <div id="windowForm6">
		<div class="windowHeader" >
			<h3 id="windowtitle" style="width:540px">时间线</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div> 
	
    <div id="windowForm">
        <div class="windowHeader">
            <h3 id="windowtitle">ERP接口信息</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent2">
            <form id="open_erp_fm">
                <input name="m" value="set_erp_info" type="hidden"/>
                <input name="company" type="hidden" value=""/>
                <table width="100%">
                    <tr class="bottomline">
                        <td width="24%" align="right">序列号：</td>
                        <td align="left">
                            <input name="serial" value="" type="hidden"/>
                            <span data-serial></span>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">密码：</td>
                        <td align="left">
                            <input name="password" type="hidden" value=""/>
                            <span data-password></span>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">合作方：</td>
                        <td align="left">
                        
                            <select name="cpCompany" id="cpCompany">
                                <option value="">请选择</option>
                                <?php
                                foreach($erp_company as $ck=>$cp){
                                    echo "<option value='".$ck."'>".$cp['name']."</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">类型：</td>
                        <td align="left">
                            <select name="version" id="erp-version">
                                <option value="">请选择</option>
                                <?php
                                foreach($erp_version as $ver){
                                    echo "<option value='".$ver."'>".$ver."</option>";
                                }
                                ?>
                            </select><input type="text" name="erp-other" id="erp-other" style="display: none" /> （请先指定合作方）
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">是否开通：</td>
                        <td align="left">
                            <label><input type="radio" name="status" value="F" />关闭</label>
                            <label><input type="radio" name="status" value="T" />开通</label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">是否运行：</td>
                        <td align="left">
                            <label><input type="radio" name="isOpen" value="F" />关闭</label>
                            <label><input type="radio" name="isOpen" value="T" />运行</label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">接口开发方：</td>
                        <td align="left">
                            <label><input type="radio" name="develop" value="YTTX" />医统天下</label>
                            <label><input type="radio" name="develop" value="OTHER" />第三方开发</label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">更新商品价格：</td>
                        <td align="left">
                            <label><input type="radio" name="isPrice" value="T" />是</label>
                            <label><input type="radio" name="isPrice" value="F" />否</label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">订单传输开始时间：</td>
                        <td align="left">
                            <label>
                                <input type="text" id="begin_date" name="transStart" />
                            </label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">订单传递：</td>
                        <td align="left">
                            <label><input type="radio" name="transferCheck" value="F" />无需审核</label>
                            <label><input type="radio" name="transferCheck" value="T" />需要审核</label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">变更原因：</td>
                        <td align="left">
                            <label>
                                <textarea rows="3" id="changeReason" name="changeReason" cols="50"></textarea>
                            </label><br/>
                            <label style="color: green;">* 开通/运行状态发生变化时请填写变更原因</label>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td colspan="2">
                            <input class="button_1 btn_erp_submit" type="button" value="提交"/>
                            <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    <script type="text/javascript">
    	var erpJson   = $.parseJSON('<?=json_encode($erp_company)?>');
    	window.cpType = '';
    	
        $(function(){
			var cpCompanyType  = $("select[name='cpCompany']");
			var versionCompany = $("select#erp-version");
			var erpOther = $('#erp-other');
			
            //选择合作方
            cpCompanyType.live('change', function(){
                var _this = $(this);
                var cpType = $(this).data('cp');

    			if(_this.val() == ''){
    				versionCompany.html('<option value="">请选择</option>');
    				return false;
    			}
                    
                cpType = _this.val();
                var _vhtml = '';
                if(erpJson[cpType] != undefined){
              		for(var i in erpJson[cpType]['support']){
    					_vhtml += "<option value='"+erpJson[cpType]['support'][i]+"'>"+erpJson[cpType]['support'][i]+"</option>";
                  	}
              		versionCompany.html(_vhtml);
                }

                alert(versionCompany.val());
                //如果是 其他 类型，就增加填充的
                if(versionCompany.val() == '其他'){
                	erpOther.show();
                }
          		
            });
            alert(versionCompany.val());
            $(".btn_erp_submit").click(function(){
                //验证变更
                var compnyid = $("#windowContent2").find("input[name='company']").val();
                var statusVal = $("#show"+compnyid).data('status');
                var cpCompany = $("#cpCompany").val();
                var run_status = $("#show"+compnyid).data('run-status');
                var newStatus = $("input[name='status']:checked").val();
                var newRunStatus = $("input[name='isOpen']:checked").val();
                var newSetPrice = $("input[name='isPrice']:checked").val();
                var changeinfo = $("#changeReason").val();

                if(cpCompany == ''){
                	alert('请指定合作方!');
                	return false;
                }
                if((statusVal != newStatus) || (run_status != newRunStatus))
                {
                    if(changeinfo == '')
                    {
                        alert('您更改了开通/运行状态，请填写变更原因!');
                    	return;
                    }
                }
            	//提交ERP接口信息
                var fm = $("#open_erp_fm");
                $.post("do_manager.php",fm.serialize(),function(data){
                    data = Jtrim(data);
                    if(data == 'ok') {
                        $.blockUI({
                            message : '<p>操作成功!</p>'
                        });
                        setTimeout(function(){
                            window.location.reload();
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>'+data+'</p>'
                        });
                        window.setTimeout($.unblockUI, 2000);
                    }
                },'text');
            });

            /**
             * @desc 显示接口信息
             */
            $(".showErp").click(function(){
                var serial = $(this).data('serial');
                var cpType = $(this).data('cp');
                var password = $(this).data('password');
                var version = $(this).data('version');
                var statusVal = $(this).data('status') || 'T';
                var run_status = $(this).data('run-status');
                var transfer_check = $(this).data('transfer-check');
                var trans_start = $(this).data('trans-start');
                var develop = $(this).data('develop') || 'DHB';
                var ct = $("#windowContent2");
                var status = $(this).data('status') == 'T';
                var isPrice = $(this).data('isprice');
                ct.find("input[name='company']").val($(this).data('company'));


                $('option', cpCompanyType).removeAttr('selected').each(function(){
              		if($(this).val() == cpType) {
                  		$(this).attr('selected', 'selected');
                  		return false;
              		}
                });
                var _vhtml = '';
                if(erpJson[cpType] != undefined){
              		for(var i in erpJson[cpType]['support']){
						_vhtml += "<option value='"+erpJson[cpType]['support'][i]+"'>"+erpJson[cpType]['support'][i]+"</option>";
                  	}
              		versionCompany.html(_vhtml);
                }
                
                if(serial){
                    ct.find("span[data-serial]").html(serial);
                    ct.find("input[name='serial']").val(serial);
                    ct.find("span[data-password]").html(password);
                    ct.find("input[name='password']").val(password);
                    ct.find("input[name='status'][value='"+statusVal+"']").attr('checked','checked');
                    ct.find("input[name='isOpen'][value='"+run_status+"']").attr('checked','checked');
                    ct.find("input[name='transferCheck'][value='"+transfer_check+"']").attr('checked','checked');
                    ct.find("input[name='transStart']").val(trans_start);
                    ct.find("input[name='develop'][value='"+develop+"']").attr('checked','checked');
                    ct.find("input[name='isPrice'][value='"+isPrice+"']").attr('checked','checked');
                    if(status){
                        $(".dredgeErp,.startErp").hide();
                        $(".stopErp").show();
                    }else{
                        $(".dredgeErp,.stopErp").hide();
                        $(".startErp").show();
                    }
                    $("select[name='version']").val($(this).data('version'));
                    $("#changeReason").val('');

                }else{
                    $.post("do_manager.php?m=buildErp",function(json){
                        serial = json.serial;
                        password = json.password;
                        ct.find("span[data-serial]").html(serial);
                        ct.find("input[name='serial']").val(serial);
                        ct.find("span[data-password]").html(password);
                        ct.find("input[name='password']").val(password);
                        ct.find("input[name='status'][value='T']").attr('checked','checked');
                        ct.find("input[name='isOpen'][value='F']").attr('checked','checked');
                        ct.find("input[name='transferCheck'][value='T']").attr('checked','checked');
                        ct.find("input[name='develop'][value='DHB']").attr('checked','checked');
                        ct.find("input[name='isPrice'][value='"+isPrice+"']").attr('checked','checked');
                        $("select[name='version']").val('');
                        $("#changeReason").val('');
                        $(".startErp,.stopErp").hide();
                        $(".dredgeErp").show();
                    },'json');
                }

                $("#begin_date").datepicker({changeMonth: true,	changeYear: true});
                $("#ui-datepicker-div").css({'zIndex' : 10001});
                $.blockUI({
                    message : $("#windowForm"),
                    css: {
                        top: '10%',left:'30%'
                    }
                });
            });


        });
    </script>

    </body>
    </html>
<?php
function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1)
{
    $frontMsg  = "";
    $frontTitleMsg = "┠-";
    $selectmsg = "";

    if($var['AreaParent']=="0") $layer = 1; else $layer++;

    foreach($resultdata as $key => $var)
    {
        if($var['AreaParent'] == $p_id)
        {
            $repeatMsg = str_repeat(" -+- ", $layer-2);
            if($var['AreaID'] == $s_id) $selectmsg = "selected"; else $selectmsg = "";

            $frontMsg  .= "<option value='".$var['AreaID']."' ".$selectmsg." >".$frontTitleMsg.$repeatMsg.$var['AreaName']."</option>";

            $frontMsg2  = "";
            $frontMsg2 .= ShowTreeMenu($resultdata,$var['AreaID'],$s_id,$layer);
            $frontMsg  .= $frontMsg2;
        }
    }
    return $frontMsg;
}
?>