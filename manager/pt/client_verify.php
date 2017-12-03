<?php 
$menu_flag = "manager";
include_once ("header.php");
$erp_version = include_once("inc/erp_version.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);
$in['status'] = $in['status'] ? $in['status']: 'A';
$in['CreditStatus'] = $in['CreditStatus']?$in['CreditStatus']:'quan';
$currentCompanyID = intval($in['id']);


$sqlmsg = '';

/*
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
*/
if(!empty($in['bdate'])) $sqlmsg .= " and u.UserDate >= '".strtotime($in['bdate'] . '00:00:00')."' ";
if(!empty($in['edate'])) $sqlmsg .= " and u.UserDate <= '".strtotime($in['edate'] . '23:59:59')."' ";

//if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or d.BusinessName like '%".$in['kw']."%' or c.CompanyContact like '%".$in['kw']."%' ) ";
if(!empty($in['kw'])) {
    $sqlmsg .= " AND (";
    $likeArr = array();
    foreach(explode(',', 'c.ClientCompanyName,c.ClientTrueName') as $lk) {
        $likeArr[] = " {$lk} like '%".$in['kw']."%'";
    }
    $sqlmsg .= implode(" OR " , $likeArr);
    $sqlmsg .= ")";
}

if($in['status'] != 'A') {
    $sqlmsg .= " and u.C_Flag='".$in['status']."' ";
}

if($in['CreditStatus'] =='open'){
    $sqlmsg .= " and main.CreditStatus='open' ";
}
if($in['CreditStatus'] =='write'){
    $sqlmsg .= " and main.CreditStatus!='open' ";
}

$begin_company = 1;// 改为600
$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATATABLE."_order_client AS c LEFT JOIN ".DATABASEU.DATATABLE."_three_sides_merchant AS m ON c.ClientCompany=m.CompanyID AND c.ClientID=m.MerchantID WHERE c.ClientCompany=".$currentCompanyID." ".$sqlmsg);// AND c.ClientID=m.MerchantID 
$page = new ShowPage;
$page->PageSize = 50;
$page->Total = $InfoDataNum['allrow'];
$page->LinkAry = array("kw"=>$in['kw'],"aid"=>$in['aid'],"id"=>$in['id'],"status"=>$in['status'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);

//读取信息
$ucSql = "SELECT c.*,m.*,main.CreditStatus FROM ".DATATABLE."_order_client AS c LEFT JOIN ".DATABASEU.DATATABLE."_three_sides_merchant AS m ON c.ClientCompany = m.CompanyID AND c.ClientID = m.MerchantID  left join ".DATABASEU.DATATABLE."_credit_main as main on  c.ClientCompany = main.CompanyID AND c.ClientID = main.ClientID  WHERE c.ClientCompany=".$currentCompanyID." ".$sqlmsg." ORDER BY CONVERT(c.C_Flag USING gbk)  ";
//print_r($ucSql);die;
$list_data = $db->get_results($ucSql." ".$page->OffSet());
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
            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });
			$("#tree").treeview({

				collapsed: true,

				animated: "medium",

				control:"#sidetreecontrol",

				persist: "location"

			});
			
			//$(document).delegate("body", "click", function(e) {  
				//closewindowui();
           // });

            $("#bdate").datepicker({changeMonth: true,	changeYear: true});
            $("#edate").datepicker({changeMonth: true,	changeYear: true});

		});



</script>

</head>



<body>

<?php include_once ("top.php");?>

    

<?php include_once ("inc/son_menu_bar.php");?>

        

    <div id="bodycontent">

    	<div class="lineblank"></div>

    	<div id="searchline">

        	<div class="leftdiv">

        	  <form id="FormSearch" name="FormSearch" method="get" action="client_verify.php">
				<input type="hidden" name="id" value="<?php echo $in['id'];?>" />
        	    <label>

        	      &nbsp;&nbsp;名称/联系人： <input type="text" name="kw" value="<?php echo $in['kw']; ?>" id="kw" class="inputline" />

       	        </label>
                  <label>&nbsp;&nbsp;注册时间： <input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
                  <label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" /> </label>
        	    <label>
        	      &nbsp;&nbsp;审核状态： 
       	        </label>
				<select name="status" id="status">
					<option value="A">全部</option>
					<option value="T" <?php if($in['status']=='T') { echo "selected='selected'"; } ?> >通过</option>
					<option value="F" <?php if($in['status']=='F') { echo "selected='selected'"; } ?> >不通过</option>
					<option value="D" <?php if($in['status']=='D') { echo "selected='selected'"; } ?> >待审</option>
					<option value="W" <?php if($in['status']=='W') { echo "selected='selected'"; } ?> >待传</option>
				</select>
       	       
				<label>
        	      &nbsp;&nbsp;账期选择： 
       	        </label>
				<select name="CreditStatus" id="status">
					<option value="A">全部</option>
					<option value="open"  <?php if($in['CreditStatus']=='open') { echo "selected='selected'"; } ?>>已开通</option>
					<option value="write"  <?php if($in['CreditStatus']=='write') { echo "selected='selected'"; } ?>>待审核</option>
				</select>
       	    
				<label>

       	          <input name="" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>
                  <label style="margin-left:50px;"></label>
                  <label><strong>待审：</strong><?php echo (int)$calc_num['D']; ?> </label>
                  <label><strong>待传：</strong><?php echo (int)$calc_num['W']; ?> </label>
                  <label><strong>通过：</strong><?php echo (int)$calc_num['T']; ?> </label>
                  <label><strong>未通过：</strong><?php echo (int)$calc_num['F']; ?> </label>

   	          </form>

   	        </div>

			<div class="location"><strong>当前位置：</strong><a href="merchant_verify.php">药企审核</a> </div>

        </div>

        <div class="line2"></div>

        <div class="bline">

        <div id="sortright" style="width:1174px;">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>

   			       <td  class="sublink"></td>
       			     <td width="50%" align="right"><? echo $page->ShowLink('client_verify.php');?></td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>

                <tr>

                  <td width="5%" class="bottomlinebold">编号</td>
                  
                  
                  <td class="bottomlinebold" width="39%">药店名称</td>
                                   
                   <td width="10%" class="bottomlinebold">联系人</td>
					
                  <td width="20%" class="bottomlinebold">手机号</td>                 
                  <td width="10%" class="bottomlinebold">建立日期</td>
					<td width="8%" class="bottomlinebold" align="center">账期</td>
                    <td width="8%" class="bottomlinebold" style="text-align:left;">审核</td>

                </tr>

     		 </thead> 

      		<tbody>

<?php

	if(!empty($list_data))
	{

		$num = 0;
		foreach($list_data as $lsv)
		{ ?>


                <tr id="line_<? echo $lsv['ClientID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >

                  <td><? echo ++$num;  ?></td>
                  <td ><?php 
                    echo' <a href="client_detail.php?ID='.$lsv['ClientCompany'].'&UID='.$lsv['ClientID'].'" target="_blank" >'.$lsv['ClientCompanyName'].'</a>';
                      ?>
                  </td>
                  
    
                   <td ><? echo $lsv['ClientTrueName'];?></td>
				  
                  <td><? echo $lsv['ClientMobile']; ?>/<? echo $lsv['ClientPhone']; ?></td>
                  
                    <td ><? if(!empty($lsv['ClientDate'])) echo date("y-m-d H:i",$lsv['ClientDate']);?></td>
				  
                    <td align="center">
                        <?php 
                        $CompanyID = $lsv['ClientCompany'];;
                        $CreditApplySql = "select * from ".DATABASEU.DATATABLE."_credit_apply where CompanyID=".$CompanyID." and ClientID=".$lsv['ClientID']."";
                        $CreditApplySel = $db->get_row($CreditApplySql);
//                        print_r($CreditApplySql);
                            if(!empty($CreditApplySel)){  ?>
                            <?php 
                                $CreditMainSql = "select CreditStatus from ".DATABASEU.DATATABLE."_credit_main where CompanyID=".$CompanyID." and ClientID=".$lsv['ClientID']."";
                                $CreditMainSel = $db->get_row($CreditMainSql);
                                if($CreditMainSel['CreditStatus'] == 'open'){
                            ?>
                                    <a href="credit.php?id=<?php echo $lsv['ClientID']?>&cpid=<?php echo $lsv['ClientCompany']?>" onclick="Approve()">查看</a>
                                <?php }else{?>
                                    <a href="credit.php?id=<?php echo $lsv['ClientID']?>&cpid=<?php echo $lsv['ClientCompany']?>" onclick="Approve()">审核</a>
                                <?php }?>
                            <?php }else{ ?>
<!--                                    <a href="credit.php?id=<?php echo $lsv['ClientID']?>" onclick="Approve()">客户未申请</a>-->
                                        客户未申请
                            <?php }?>
                        
                    </td>
                    <td style="text-align:left;" class="TitleNUM">
                        <?php
                            switch($lsv['C_Flag']) {
                                case 'T': echo "通过"; break;
                                case 'F': echo "不通过"; break;
                                case '': echo "未上传"; break;
                                case 'W': echo "未上传"; break;
                                case 'D':
                                    echo "待审";
                                    echo '-' . '<a href="client_check.php?ID='.$lsv['ClientCompany'].'&UID='.$lsv['ClientID'].'">去审核</a>';
                                    break;
                                default:break;
                            }
                        ?>
                    </td>

                </tr>

<? } }else{?>

     			 <tr>

       				 <td colspan="6" height="30" align="center">暂无符合此条件的内容!</td>

       			 </tr>

<? }?>

 				</tbody>

              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">

     			 <tr>
       				 <td width="4%"  height="30" ></td>

   			       <td  class="sublink"></td>

       			     <td width="50%" align="right"><? echo $page->ShowLink('merchant_verify.php');?></td>

     			 </tr>

              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >

              </form>

       	  </div>
        </div>
        <br style="clear:both;" />
    </div>
 
<?php include_once ("bottom.php");?>

<iframe style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
<div id="windowForm" style="width:800px; height:100%; position: fixed; top: 0%; left: 25%; background-color:#fff;">
    <div class="windowHeader">
        <h3 id="windowtitle">查看图片</h3>
        <div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
    </div>
    <div id="windowContent" >
		<img id="showimg" name="showimg" src="" />
    </div>
</div>

</body>
</html>
<?php
 	function ShowTreeMenuList($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				if($var['AreaParent'] == "0")
				{
					$frontMsg  .= '<li><a href="merchant_verify.php?aid='.$var['AreaID'].'"  ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="merchant_verify.php?aid='.$var['AreaID'].'"  >'.$var['AreaName'].'</a>';
				}	

					$frontMsg2 = "";
					$frontMsg2 .= ShowTreeMenuList($resultdata,$var['AreaID']);
					if(!empty($frontMsg2)) $frontMsg .= '<ul>'.$frontMsg2.'</ul>';
					$frontMsg .= '</li>';
			}
		}		
		return $frontMsg;
	}
?>