<?php 
$menu_flag = "notice_list";
include_once ("header.php");
$erp_version = include_once("inc/erp_version.php");
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
				
function show_time_log(cid,cname)
{
	$("#windowtitle").html(cname+' - 时间线');
	$('#windowContent').html('<iframe src="show_time_log.php?ID='+cid+'" width="100%" marginwidth="0" height="550" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
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

        	  <form id="FormSearch" name="FormSearch" method="get" action="notice_list.php">
				<label>&nbsp;&nbsp;添加日期：</label>
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

       	        </label>
					<?php
					$type= empty($in['ty']) ? 1 : $in['ty'] ;
					$sql="select * from ".DATATABLE."_pay_notice_type order by id asc";
					$notice_type=$db->get_results($sql);
					//var_dump($notice_type);exit;
					?>
						&nbsp;&nbsp;
						<select name="ty">
							<?php
							if(!empty($notice_type)){
								foreach($notice_type as $key=>$value){
							?>
							<option <?php if( $type==$value['id']) echo "selected='selected'"?> value="<?php echo $value['id'];?>"><?php echo $value['name'];?></option>
							<?php
								}
							}
							?>
						</select>
				<label>
				
				</label>
				
				<label>

                  <input name="d" type="hidden" value="<?php echo $in['d']; ?>"/>
       	          <input type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>
   	          </form>
   	        </div>
        </div>    	

        <div class="line2"></div>

        <div class="bline">
<?php
	$search_str="";
	
	if(isset($_GET['bdate']) && $_GET['bdate'] !='' ){
		$search_str.=" and addtime>= '".strtotime($_GET['bdate'].' 00:00:00')."'";
	}

	if(isset($_GET['edate']) && $_GET['edate'] !='' ){
		$search_str.=" and addtime <= '".strtotime($_GET['edate'].' 23:59:59')."'";
	}
	
    $InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_pay_notice where type=".$type." ".$search_str);
	$page = new ShowPage;
    $page->PageSize = 10;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("bdate"=>$_GET['bdate'],"edate"=>$_GET['edate'],"ty"=>$in['ty']);        
	$datasql  = "SELECT * FROM ".DATATABLE."_pay_notice where type=".$type." ".$search_str." order by addtime desc";
	
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
?>




          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr align="right">
       				 <td class="bottomlinebold"><input type="button" name="newbutton" id="newbutton" value=" 新增消息 " class="button_2" onclick="javascript:window.location.href='pay_notice.php'" />&nbsp;&nbsp;&nbsp;&nbsp;</td>
					
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">ID</td>
                  <td class="bottomlinebold">提示信息</td>
				  <td width="15%" class="bottomlinebold">开始时间</td>
                  <td width="15%" class="bottomlinebold" align="left">结束时间</td>  
				  <td width="15%" class="bottomlinebold" align="left">添加时间</td>  
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

                <tr id="line_<?php echo $lsv['id'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
	<?php 
	if(in_array($_SESSION['uinfo']['userid'],$allAdminArr)){				
	?>
                   <td><? echo $lsv['id'];?> </td>
				   <td><? echo mb_substr($lsv['title'],0,20,'UTF-8');?> </td>
				   <td> <?php echo empty($lsv['start_date'])? "—" : date("Y-m-d H:i:s",$lsv['start_date']); ?></td>
				   <td><? echo empty($lsv['end_date'])? "—" : date("Y-m-d H:i:s",$lsv['end_date']);?> </td>
				   <td><? echo empty($lsv['addtime'])? "—" : date("Y-m-d H:i:s",$lsv['addtime']);?> </td>
                  <td align="right" title="<?php echo $lsv['CompanyRemark'];?>">

					<a href="edit_pay_notice.php?ID=<? echo $lsv['id'];?>" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;
					<?php
					if(in_array($_SESSION['uinfo']['userid'],$topAdminArr)){
					?>
					<a href="javascript:void(0);" onclick="do_delete_pay_notice('<? echo $lsv['id'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>&nbsp;&nbsp;&nbsp;
					<?php }  ?>
				  </td>
                </tr>
	<?  }else{?>

     			 <tr>
       				 <td colspan="5" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
	<? } } }?>
 				</tbody>
              </table>
			 <table width="100%" border="0" cellspacing="0" cellpadding="0">
			 <tr>
				 <td width="4%"  height="30" ></td>
				 
				 <td  align="right"><? echo $page->ShowLink('notice_list.php');?></td>
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
                <td align="right">版本：</td>
                <td align="left">
                    <select name="version">
                        <option value="">请选择</option>
                        <?php
                            foreach($erp_version as $ver){
                                echo "<option value='".$ver."'>".$ver."</option>";
                            }
                        ?>
                    </select>
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
                <td align="right">接口开发方：</td>
                <td align="left">
                    <label><input type="radio" name="develop" value="DHB" />订货宝开发</label>
                    <label><input type="radio" name="develop" value="OTHER" />第三方开发</label>
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
<?php 
if($_SESSION['uinfo']['userid'] == "1"){
?>
<script type="text/javascript">
/*
     $.post("do_sms1.php?m=shownumber1",$("#alipayment").serialize(),
		function(data){
			if(data < 3000) alert('系统通知短信数：'+data);
		});

	 $.post("do_sms1.php?m=shownumber3",$("#alipayment").serialize(),
		function(data){
			if(data < 1000) alert('校验码短信数：'+data);
		});
*/
</script>
<?php }?>
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