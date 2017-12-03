<?php
    $menu_flag = "sale_count";
    include_once ("header.php");
    setcookie("backurl", $_SERVER['REQUEST_URI']);
    
    if(!in_array($_SESSION['uinfo']['userid'],$allAdminArr)) exit('非法路径!');
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
<script src="js/sale.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>

<body>
    <?php include_once ("top.php");?> 
    <?php include_once ("inc/son_menu_bar.php");?>      

    <div id="bodycontent">
    	<div class="lineblank"></div>
    
        <div id="searchline">
        	<div class="leftdiv">
        
        	  <form id="FormSearch" name="FormSearch" method="get" action="sale.php">
        	    <label>
        	        &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" value="<? echo $in['kw']?>" class="inputline" />
                </label>
        		<label>
        		<select id="iid" name="iid"  style="width:165px;" class="select2">
                    <option value="" >⊙ 所有部门</option>
                    <?php 
                    	foreach($sale_depart as $key => $accvar)
                    	{
                    		if($in['iid'] == $key) $smsg = 'selected="selected"'; else $smsg ="";
                    
                    		echo '<option value="'.$key.'" '.$smsg.' title="'.$accvar.'"  >'.$accvar.'</option>';
                    	}
                    ?>
                </select>
                </label>				
                <label>                    
                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />       
                </label>
              </form>
            </div>
            <div class="location">
                <input type="button" class="button_2" onclick="addSale();" value="新增销售人员" title="新增销售人员信息" />
            </div>
        </div>    	
    
        <div class="line2"></div>
    
        <div class="bline">
            <?php
            
            	$sqlmsg = '';
            	if(empty($in['num'])){
            		if(!empty($in['iid']))  $sqlmsg .= " and s.SaleDepartment = '".$in['iid']."' "; else $in['iid'] = '';

            		//if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' ";
                    if(!empty($in['kw'])) {
                        $sqlmsg .= " AND (";
                        $likeArr = array();
                        foreach(explode(',', 's.SaleName,s.SalePhone,u.UserTrueName') as $lk) {
                            $likeArr[] = " {$lk} like '%".$in['kw']."%'";
                        }
                        $sqlmsg .= implode(" OR " , $likeArr);
                        $sqlmsg .= ")";
                    }
            	}
            	
            	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_sale s where s.CreateUID is not null".$sqlmsg);
            	 
            	$page = new ShowPage;
            	$page->PageSize = 50;
            	$page->Total = $InfoDataNum['allrow'];
            	$page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid']);
            
            	$datasql   = "SELECT s.*,u.UserTrueName FROM ".DATABASEU.DATATABLE."_order_sale as s left join ".DATABASEU.DATATABLE."_order_user as u ON s.UpdateUID = u.UserID where s.SaleFlag = 'T' ".$sqlmsg."  ORDER BY s.ID DESC";
            	$list_data = $db->get_results($datasql." ".$page->OffSet());
            ?>
        
            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
                <table width="100%" border="0" cellspacing="0" cellpadding="0">               
                    <thead>
                        <tr>
                          <td width="6%" class="bottomlinebold">编号</td>
                          <td width="10%" class="bottomlinebold">姓名</td>
                          <td width="10%" class="bottomlinebold">部门</td>
                          <td width="15%" class="bottomlinebold">电话</td>
                          <td class="bottomlinebold">个性化地址</td>
                          <td width="10%" class="bottomlinebold">操作人</td>
                          <td width="13%" class="bottomlinebold">操作时间</td>
                          <td width="8%" class="bottomlinebold" >操作</td>  
                        </tr>
                    </thead> 
                
                    <tbody>
                    
                        <?php
                        	if(!empty($list_data))
                        	{
                        		foreach($list_data as $lsv)
                        		{
                        ?>
                    
                                    <tr id="line_<? echo $lsv['ID'];?>" title="<? echo $lsv['Remark']; ?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
                                        <td >10<? echo $lsv['ID'];?></td>
                                        <td ><? echo $lsv['SaleName'];?></td>
                                        <td><?php echo $sale_depart[$lsv['SaleDepartment']]; ?></td>                                    
                                        <td ><? echo $lsv['SalePhone'];?></td>
                                        <td >
                                            <?php $param = encodeParam("type=seller&id=".$lsv['ID']."&name=".$lsv['SaleName']);echo ESPECIAL_URL."?a=".$param;?>
                                            <a target="_blank" href="createWeixin.php?text=<? echo $param;?>&size=400&date=<? echo random(10);?>">[二维码]</a>
                                        </td>
                                        <td ><? echo $lsv['UserTrueName'];?></td>
                                        <td ><? echo date("Y-m-d H:i",$lsv['UpdateDate']);?></td>                                    
                                        <td >
                                            <? echo "<a class='showSale' href='javascript:;' data-sid='".$lsv['ID']."' data-name='".$lsv['SaleName']."' data-dept='".$lsv['SaleDepartment']."' data-phone='".$lsv['SalePhone']."' data-remark='".$lsv['Remark']."'>[编辑]</a>";?>                                            
                                            &nbsp;&nbsp;
                                            <a href='javascript:;' class='delSale' data-sid='<?echo $lsv['ID'] ?>'>[删除]</a>
                                        </td>
                                    </tr>
                        <?      } 
                        	}
                            else
                            {
                        ?>
                                <tr>
                                    <td colspan="7" height="30" align="center">暂无符合此条件的内容!</td>
                                </tr>
                        <? }?>
                    </tbody>
                </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('sale.php');?></td>
                    </tr>
                 </table>
            
            </form>
        
        </div>
    
    <br style="clear:both;" />
    </div>

    <?php include_once ("bottom.php");?>

    <iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <div id="windowForm6">
        <div class="windowHeader" >
        	<h3 id="windowtitle" style="width:540px"></h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent">
                                正在载入数据...       
        </div>
    </div> 
    <div id="windowForm">
        <div class="windowHeader">
            <h3 id="windowtitle">销售人员信息</h3>
            <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
        </div>
        <div id="windowContent2">
            <form id="open_sale_fm">
                <input name="m" value="set_sale_info" type="hidden"/>
                <input name="ID" type="hidden" value=""/>
                <table width="100%">
                    <tr class="bottomline">
                        <td width="24%" align="right">姓名：</td>
                        <td align="left">
                            <input style="width:60%" name="SaleName" id="SaleName" value="" type="text"/>
                            <span data-serial></span>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">部门：</td>
                        <td align="left">
                            <select style="width:60%" name="SaleDepartment">
                                <?php
                                    foreach($sale_depart as $kval => $ver){
                                        echo "<option value='".$kval."'>".$ver."</option>";
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">电话：</td>
                        <td align="left">
                            <input style="width:60%" name="SalePhone" id="SalePhone" value="" type="text"/>
                            <span data-serial></span>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td align="right">备注：</td>
                        <td align="left">
                            <textarea style="width:60%" name="Remark" id="Remark"  rows="5"></textarea>
                        </td>
                    </tr>
                    <tr class="bottomline">
                        <td colspan="2">
                            <input class="button_1 btn_sale_submit" type="button" value="提交"/>
                            <input class="button_3" onclick="closewindowui();" title="关闭" type="button" value="关闭"/>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <div class="dhbBox" style="display: none; left: 50%; top: 50%; margin-left: -110px; margin-top: -122.5px; position: fixed; height: 220px; width: 220px;">
        <div class="dhbClose" style="top: 8px; right: 20px;">X</div>
        <div class="dhbFrameDiv">
            <div style="height: 220px; width: 220px; display: block;" class="dhbCreateWeixin">
            </div>
        </div>
    </div>
    <link href="../plugin/qrcode/createWeixin.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../plugin/qrcode/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="../plugin/qrcode/jquery.qrcode-0.12.0.js"></script>
    <script type="text/javascript" src="./js/function.js"></script>
    <script type="text/javascript">
        $(function(){
            $(".dhbClose").click(
                function(){
                    $(".dhbBox").hide();
                }
            );
        });
    </script>
</html>