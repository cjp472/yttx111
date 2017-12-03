<?php
$menu_flag = "open";
include_once ("header.php");
include_once ("../class/ip2location.class.php");
$erp_version = include_once("inc/erp_version.php");

setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<link rel="stylesheet" href="css/showpage.css" />

<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="js/generalize.js?v=<? echo VERID;?>" type="text/javascript"></script>
</head>

<body>
<?php include_once ("top.php");?>
    
<?php include_once ("inc/son_menu_bar.php");?>
<?php

            $sql_t = "select generalizeType from ".DATABASEU.DATATABLE."_order_generalize where generalizeType <> 'seller' ";
            $sql_type = $db->get_results($sql_t);
            $typeArr = array();
            foreach($sql_type as $data){
                if(!in_array($data['generalizeType'],$typeArr)){
                    $typeArr[] = $data['generalizeType'];
                }
            }
            if(!isset($in['startTime']) && !isset($in['endTime'])){
                $in['startTime'] = date('Y-m-01',time());
                $in['endTime'] = date('Y-m-t',time());
            }
            
            $wheres = '';
    if(!empty($in['ttype'])){
    $wheres .= " and generalizeType = '".$in['ttype']."' ";
    }
    //修改  增加查询条件        2015-12-1 by dumhao---------start-------------
    if(!empty($in['gzName'])){
        $wheres .= " and g.generalizeName like '%".$in['gzName']."%'";
    }
    //-------------------------------------------------End-------------
    $whereType = '';
    if(!empty($in['startTime'])){
        $in['startTime'] = strtotime($in['startTime']."00:00:00");
        $whereType .= " and handle >= ".intval($in['startTime']);
    }
    if(!empty($in['endTime'])){
        $in['endTime'] = strtotime($in['endTime']."23:59:59");
        $whereType .= " and handle <= ".intval($in['endTime']);
    }
    $datasql   = "SELECT g.generalizeID,t.companyNum,g.generalizeNo,g.generalizeName,g.generalizeType,g.generalizeUrl FROM ".DATABASEU.DATATABLE."_order_generalize as g left join (select num,count(*) as companyNum from ".DATABASEU.DATATABLE."_order_type where id>0 ".$whereType." group by num) as t on t.num = g.generalizeNo  where g.generalizeType <> 'seller' ".$wheres." Order by g.generalizeID Desc";
    $list_data = $db->get_results($datasql);    
    foreach($list_data as $i){
        $arrId .=$i['generalizeNo'].',';
    }
    $arrId =rtrim($arrId, ",");
    if(isset($in['startTime']) && is_int($in['startTime'])){
        $in['startTime'] = date('Y-m-d',$in['startTime']);
        $in['endTime'] = date('Y-m-d',$in['endTime']);
    }
    //修改  增加注册统计查询  2015-12-1 by dumhao---------start-------------
    $countSql = "SELECT 
                   COUNT(g.generalizeID) AS geCount,
                   SUM(t.companyNum) AS companyNum
                 FROM
                   ".DATABASEU.DATATABLE."_order_generalize AS g 
                   LEFT JOIN 
                     (SELECT 
                       num,
                       COUNT(*) AS companyNum 
                     FROM
                       ".DATABASEU.DATATABLE."_order_type
                      where id>0 ".$whereType." group by num
                     ) AS t
                     ON t.num = g.generalizeNo 
                 WHERE g.generalizeType <> 'seller' ".$wheres." 
                 ORDER BY g.generalizeID DESC";
    $countInfo = $db->get_results($countSql);
    //增加   增加收费用户统计 2015-12-1 by dumhao
    $regSql ="SELECT 
              COUNT(CS_Number) AS CCount 
            FROM
              ".DATABASEU.DATATABLE."_order_cs 
            WHERE CS_Company IN 
              (SELECT 
                company_id 
              FROM
                ".DATABASEU.DATATABLE."_order_type 
              WHERE num IN (".$arrId.") ".$whereType."
              GROUP BY company_id) 
              AND CS_Number = 10000";
         $countNum = $db->get_results($regSql);
    //-------------------------------------------------End-------------
?>
    <div id="bodycontent">    
    	<div class="lineblank"></div>
    	<div id="searchline">

            
			<table width="98%" border="0" cellspacing="0" cellpadding="0" align="center" >
        	  <form id="FormSearch" name="FormSearch" method="get" action="generalize.php">
        		<tr>
					<td align="left">
                        <div class="leftdiv">
                                <label>
                                    &nbsp;&nbsp;类型：
                                </label>
                                <label>
                                    <select name="ttype" id="ttype">
                                        <option value="">请选择..</option>
                                        <?php foreach($typeArr as $type){ ?>
                                            <option value="<?php echo $type;?>" <? if($type == $in['ttype']) echo "selected = selected";?>><?php echo $type;?></option>
                                        <? }?>
                                    </select>
                                </label>
                            <label>&nbsp;&nbsp;时间：
                                <input type="text" name="startTime" id="startTime" class="inputline" style="width:80px;" value="<? if(!empty($in['startTime'])) echo $in['startTime'];?>" /> -
                            </label>
                            <label>&nbsp;
                                <input type="text" name="endTime" id="endTime" class="inputline" style="width:80px;" value="<? if(!empty($in['endTime'])) echo $in['endTime'];?>" />
                            </label>
                                <label>
                                                            推广名称：<input type="text" name="gzName" id="gzName" style="width:80px;height:23px;" value="" />
                                </label>
                                <label>
                                    <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
                                </label>
                        </div>
                    </td>
					<td align="right"><div class="location"><strong>当前位置：</strong><a href="generalize.php">推广关系</a></div></td>
				</tr>
   	          </form>
			 </table>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

        <div style="width:96%; margin:2px auto;">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
          		<div class="font12h">新增推广关系:</div>
          	  <table width="92%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="4%" class="bold">编号:</td>
                  <td width="16%"><input type="text" name="data_generalizeNo" id="data_generalizeNo" value="" /></td>
				  <td width="6%" class="bold" >推广名称:</td>
				  <td width="16%"><input type="text" name="data_generalizeName" id="data_generalizeName" value="" /></td>
                  <td width="4%" class="bold" >类型:</td>
                  <td width="10%" ><input style="width: 90%" type="text" id="data_generalizeType" name="data_generalizeType"/></td>
                  <td width="4%" class="bold" >地址:</td>
                  <td width="15%" ><input style="width: 90%" type="text" id="data_url" name="data_url"/></td>
                  <td ><input type="button" name="savebutton" id="savebutton" value="保 存" class="button_2" onclick="do_save_generalize();" style="margin-top:0px;" />&nbsp;&nbsp;(注：seller类型为销售专有) </td>
                </tr>
              </table>        
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                     <td class="bottomlinebold">
                  <!--修改信息统计  2015-12-01 by dumhao  start------->
                         <label><strong>统计来源</strong></label>
                         <label><strong>共<font color=red> <?php echo !empty($countInfo[0]['geCount'])?$countInfo[0]['geCount']:0;?> </font> 条</strong></label>
                             <label><strong> 访问：<font id="accessCount" color=red> 0 </font> 次</strong></label>
                             <label><strong> 注册：<font color=red> <?php echo !empty($countInfo[0]['companyNum'])?$countInfo[0]['companyNum']:0;?> </font> 人 </strong></label>
                             <label><strong> 收费用户：<font color=red> <?php echo !empty($countNum[0]['CCount'])?$countNum[0]['CCount']:0;?> </font> 人 </strong></label>
                             <label><strong> 免费用户：<font color=red> <?php echo ($countInfo[0]['companyNum']-$countNum[0]['CCount']);?> </font> 人 </strong></label>
                  <!---------------------------------  end------->          
                     </td>
                     <td align="right"  height="30" class="bottomlinebold">
                     </td>
                 </tr>
              </table>
                
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">行号</td>
                  <td width="8%" class="bottomlinebold">编号</td>82
				  <td width="15%" class="bottomlinebold">推广名称</td>67
				  <td width="10%" class="bottomlinebold">类型</td>57
				  <td width="10%" class="bottomlinebold">访问次数</td>
				  <td width="10%" class="bottomlinebold">注册次数</td>
				  <td width="25%" class="bottomlinebold">个性化地址</td>
				  <td width="9%" class="bottomlinebold" style="text-align:center;">用户详情</td>
                  <td width="10%" class="bottomlinebold" align="center">管理</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?php
    
	if(!empty($list_data))
	{
	 $n=1;
     $hasGno = ''; //当前页的编号数据

     foreach($list_data as $lsv)
	 {
         $hasGno = $hasGno.$lsv['generalizeNo'].',';
?>
                <tr id="line_<? echo $lsv['generalizeID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)" >
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['generalizeNo'];?></td>
				  <td ><strong><? echo $lsv['generalizeName'];?></strong></td>
				  <td ><strong><? echo $lsv['generalizeType'];?></strong></td>
				  <td ><strong><span id="relation-<?php echo $lsv['generalizeID'];?>"><img src="img/loader.gif"/></span></strong></td>
				  <td ><strong><? if(empty($lsv['companyNum'])){echo '0';}else{echo $lsv['companyNum'];};?></strong></td>
				  <td >
                      <? echo $lsv['generalizeUrl'];?>
                      <a target="_blank" href="createWeixin.php?text=<? echo $lsv['generalizeUrl'];?>&size=400&date=<? echo random(10);?>">[二维码]</a>
                  </td>
                  <td style="text-align:center;">
                      <a href="open.php?findText=<? echo $lsv['generalizeType'];?>">查看</a></td>
                  <td align="center">
					<a href="#editbill" onclick="set_edit_bill('<?php echo $lsv['generalizeID']; ?>','<?php echo $lsv['generalizeNo']; ?>','<?php echo $lsv['generalizeName']; ?>','<?php echo $lsv['generalizeType']; ?>','<?php echo $lsv['generalizeUrl']; ?>')" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;<a href="javascript:void(0);" onclick="do_delete_bill('<? echo $lsv['generalizeID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>
				  </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <a name="editbill"></a>
              <div id="edit_generalize"  style="display:none;">
               <div class="font12h">修改推广关系:</div>
               	  <INPUT TYPE="hidden" name="update_id" id="update_id" value ="" >
	          	  <table width="80%" border="0" cellspacing="0" cellpadding="0">
	                <tr>
	                  <td class="bold" width="4%">编号:</td>
	                  <td width="16%" ><input type="text" name="edit_GeneralizeNO" id="edit_GeneralizeNO" value="" /></td>
					  <td class="bold" width="6%" >推广名称:</td>
					  <td width="16%"><input type="text" name="edit_GeneralizeName" id="edit_GeneralizeName" value="" /></td>
                      <td width="4%" class="bold" >类型:</td>
                      <td width="10%" ><input style="width: 90%" type="text" id="edit_GeneralizeType" name="edit_GeneralizeType"/></td>
                      <td width="4%" class="bold" >地址:</td>
                      <td width="15%" ><input style="width: 90%" type="text" id="edit_Url" name="edit_Url"/></td>
	                  <td ><input type="button" name="editbutton" id="editbutton" value="保 存" class="button_2" onclick="do_edit_generalize();" /> </td>
	                </tr>
	              </table>  
              </div>

              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>    
    
</body>
</html>
<script src="js/function.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function(){
        $("#startTime").datepicker({changeMonth: true,	changeYear: true});
        $("#endTime").datepicker({changeMonth: true,	changeYear: true});
        var hasGn = "<?php echo $hasGno;?>";
        $.post(
            "do_generalize.php",
            {
                m:"generalize_select_generalize", //请求方法
                url:"<?php echo DHB_HK_URL;?>/?f=count_generalize", //请求第三方数据源地址
                p:Math.random(),
                s:hasGn,
                startTime:<?php echo strtotime($in['startTime']);?>,
                endTime:<?php echo strtotime($in['endTime']);?>
            },
            function(result){
                $("span[id^='relation-']").text(0);
                if(result != null){
                    var icount = 0;//dumhao 2015-12-1
                    var rsLength = result.length;
                    for(var i = 0;i < rsLength; i++) {
                        icount = icount + Number(result[i].count);//dumhao 2015-12-1
                        $("#relation-"+result[i].generalizeID).text(result[i].count);
                    }
                    $("#accessCount").text(icount);//dumhao 2015-12-1
                }
            },'json');
    });
</script>