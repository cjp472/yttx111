<?php 
$menu_flag = "delete";
include_once ("header.php");
if(!in_array($_SESSION['uinfo']['userid'],array(1))) exit('非法路径!!!');


setcookie("backurl", $_SERVER['REQUEST_URI']);
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
				
function show_data(cid,ctype,cname)
{
	$("#windowtitle").html(cname+'业务数据');
	$("#windowHeader").css("width","1000px");
	$("#windowContent").css("width","1000px");
	$('#windowContent').html('<iframe src="show_'+ctype+'_data.php?cid='+cid+'" width="100%" marginwidth="0" height="680" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '1000px',height:'720px',top:'4%',left:'22%'
            }			
		});
	//$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
}	
			
function show_data_clear(cid,ctype,cname)
{
	$("#windowtitle").html(cname+'');
	$("#windowHeader").css("width","800px");
	$("#windowContent").css("width","800px");
	$('#windowContent').html('<iframe src="show_'+ctype+'_data.php?cid='+cid+'" width="100%" marginwidth="0" height="680" marginheight="0" align="middle" frameborder="0" scrolling="auto"></iframe>');
	$.blockUI({ 
		message: $('#windowForm6'),
		css:{ 
                width: '800px',height:'720px',top:'4%',left:'15%'
            }			
		});
	//$('.blockOverlay').attr('title','点击返回').click($.unblockUI); 
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

        	  <form id="FormSearch" name="FormSearch" method="get" action="delete.php">

        	    <label>

        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" class="inputline" />

       	        </label>
				<label>
				<select id="iid" name="iid"  style="width:155px;" class="select2">
				<option value="" >⊙ 所有行业</option>
				<?php 
					$n = 0;		
					$accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_order_industry ORDER BY IndustryID ASC ");
					foreach($accarr as $accvar)
					{
						$n++;
						$areaarr[$accvar['IndustryID']] = $accvar['IndustryName'];

						if($in['iid'] == $accvar['IndustryID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$accvar['IndustryID'].'" '.$smsg.' title="'.$accvar['IndustryName'].'"  >'.$n.' 、 '.$accvar['IndustryName'].'</option>';
					}
				?>
				</select>
				</label>
				<label>
				<select id="aid" name="aid"  style="width:135px;" class="select2">
				<option value="" >⊙ 所有地区</option>
				<?php 
					$n = 0;		
					$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName,AreaAbout FROM ".DATABASEU.DATATABLE."_order_city  ORDER BY AreaParent asc,AreaOrder DESC,AreaID ASC ");
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
				<select id="gid" name="gid"  style="width:235px;" class="select2">
				<option value="" >⊙ 所属代理商</option>
				<?php 
					$n = 0;		
					$agentdata = $db->get_results("SELECT AgentID,AgentName FROM ".DATABASEU.DATATABLE."_order_agent ORDER BY AgentID ASC ");
					foreach($agentdata as $var)
					{
						$n++;
						$agentarr[$var['AgentID']] = $var['AgentID'];

						if($in['gid'] == $var['AgentID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$var['AgentID'].'" '.$smsg.' title="'.$var['AgentName'].'"  >'.$n.' 、 '.$var['AgentName'].'</option>';
					}
					
				?>
				</select>
				</label>				

        	    <label>
        	      &nbsp;&nbsp;到期时间： 
       	        </label>
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

       	        </label>
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
		if(!empty($in['bdate'])) $sqlmsg .= " and s.CS_EndDate >= '".$in['bdate']."' ";
		if(!empty($in['edate'])) $sqlmsg .= " and s.CS_EndDate <= '".$in['edate']."' ";

        if(!empty($in['kw'])) {
            $sqlmsg .= " AND (";
            $likeArr = array();
            foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail') as $lk) {
                $likeArr[] = " {$lk} like '%".$in['kw']."%'";
            }
            $sqlmsg .= implode(" OR " , $likeArr);
            $sqlmsg .= ") ";
        }

		if(empty($sqlmsg)){
			$databasearr1 = $databasearr;
			if(!isset($in['d']) || $in['d'] == '') $in['d'] = array_pop($databasearr1);
			$in['d'] = intval($in['d']); 
			$sqlmsg = " and c.CompanyDatabase=".$in['d']." ";
		}
	}

	$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID DESC limit 0,500";
	$list_data = $db->get_results($datasql);			
?>




          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold">[<a href="delete.php?num=more">MORE</a>]</td>
					 <td align="right"  height="30" class="bottomlinebold">
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="delete.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
					 </td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>

                <tr>

                  <td width="5%" class="bottomlinebold">编号</td>

                  <td class="bottomlinebold">公司名称</td>
				  <td width="8%" class="bottomlinebold">前缀</td>

				  <td width="6%" class="bottomlinebold">用户数</td>
                  <td width="8%" class="bottomlinebold">联系人</td>

                  <td width="6%" class="bottomlinebold" align="center">订单</td>

                  <td width="6%" class="bottomlinebold" align="center">发货单</td>
                  <td width="6%" class="bottomlinebold" align="center">收款单</td>
                  <td width="6%" class="bottomlinebold" align="center">入库单</td>	
				  <td width="6%" class="bottomlinebold" align="center">退单</td>

                  <td width="6%" class="bottomlinebold" align="center">商品</td>
  				  <td width="12%" class="bottomlinebold" align="center">清理数据</td>
                </tr>

     		 </thead> 

      		<tbody>

<?php



	if(!empty($list_data))

	{

		foreach($list_data as $lsv)

		{


?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >

                  <td onclick="javascript: window.location.href='do_login.php?m=admintologin&companyid=<? echo $lsv['CompanyID'];?>'"><? echo $lsv['CompanyID'];?></td>

                  <td ><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['CompanyRemark'];?>" target="_blank"><? echo $lsv['CompanyName'].'<br />'.$lsv['BusinessLicense'];?></a></td>
				  <td><? echo '<a href="http://'.$lsv['CompanyPrefix'].'.dhb.hk" target="_blank">'.$lsv['CompanyPrefix'].'</a><br />'.$lsv['CompanySigned'];?></td>

				  <td >&nbsp;<strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];?></strong></td>
                  <td ><? echo $lsv['CompanyContact'].'<br />'.$lsv['CompanyMobile'];?></td>

                  <td align="center">[<a href="javascript:void(0)" onclick="show_data('<? echo $lsv['CompanyID'];?>','order','<? echo $lsv['CompanyName'].'-订单';?>');" >订单</a>]</td>

                  <td align="center">[<a href="javascript:void(0)" onclick="show_data('<? echo $lsv['CompanyID'];?>','consignment','<? echo $lsv['CompanyName'].'-发货单';?>');" >发货单</a>]</td>
				  <td align="center" >[<a href="javascript:void(0)" onclick="show_data('<? echo $lsv['CompanyID'];?>','finance','<? echo $lsv['CompanyName'].'-款项';?>');" >款项</a>]</td>

                  <td align="center">[<a href="javascript:void(0)" onclick="show_data('<? echo $lsv['CompanyID'];?>','library','<? echo $lsv['CompanyName'].'-入库单';?>');" >入库单</a>]</td>

                  <td align="center" >
					[<a href="javascript:void(0)" onclick="show_data('<? echo $lsv['CompanyID'];?>','return','<? echo $lsv['CompanyName'].'-退单';?>');" >退单</a>]		  
				  </td>
				  <td align="center" >
					[<a href="javascript:void(0)" onclick="show_data('<? echo $lsv['CompanyID'];?>','product','<? echo $lsv['CompanyName'].'-商品';?>');" >商品</a>]		  
				  </td>

				  <td align="center" >
					[<a href="delete_history.php?cid=<?php echo $lsv['CompanyID'];?>" target="_blank" >清理数据</a>]		  
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
       				 <td align="right"  height="30" >
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="delete.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
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
		<div class="windowHeader" id="windowHeader" >
			<h3 id="windowtitle" style="width:88%">业务数据处理</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div> 

</body>
</html>
<?php
 	function ShowTreeMenu($resultdata,$p_id=0,$s_id=0,$layer=1) 
	{
		$frontMsg  = "";
		$frontTitleMsg = "";
		$selectmsg = "";
		
		if($var['AreaParent']=="0") $layer = 1; else $layer++;
					
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				$repeatMsg = str_repeat(" -- ", $layer-2);
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