<?php 
$menu_flag = "manager";
include_once ("header.php");
$erp_version = include_once("inc/erp_version.php");
setcookie("backurl", $_SERVER['REQUEST_URI']);
$in['status'] = $in['status'] ? $in['status']: 'D';


$sqlmsg = '';
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
if(!empty($in['bdate'])) $sqlmsg .= " and c.CompanyDate >= '".strtotime($in['bdate'] . '00:00:00')."' ";
if(!empty($in['edate'])) $sqlmsg .= " and c.CompanyDate <= '".strtotime($in['edate'] . '23:59:59')."' ";

//if(!empty($in['kw']))  $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or d.BusinessName like '%".$in['kw']."%' or c.CompanyContact like '%".$in['kw']."%' ) ";
if(!empty($in['kw'])) {
    $sqlmsg .= " AND (";
    $likeArr = array();
    foreach(explode(',', 'c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail,d.BusinessName,d.BusinessCard,d.IDCard') as $lk) {
        $likeArr[] = " {$lk} like '%".$in['kw']."%'";
    }
    $sqlmsg .= implode(" OR " , $likeArr);
    $sqlmsg .= ")";
}

if($in['status'] != 'A') {
    $sqlmsg .= " and s.CS_Flag='".$in['status']."' ";
}

$begin_company = 1;// 改为600
$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_company_data d on c.CompanyID=d.CompanyID left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company RIGHT JOIN ".DATABASEU.DATATABLE."_buy_account as a ON a.company_id=c.CompanyID where c.CompanyID > {$begin_company} and c.CompanyFlag='0'  ".$sqlmsg."  ");
$page = new ShowPage;
$page->PageSize = 100;
$page->Total = $InfoDataNum['allrow'];
$page->LinkAry = array("kw"=>$in['kw'],"iid"=>$in['iid'],"aid"=>$in['aid'],"status"=>$in['status'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);
$datasql   = "SELECT c.*,d.*,s.CS_Flag,s.CS_Number,c.CompanyID as CID FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_order_company_data as d ON d.CompanyID=c.CompanyID where c.CompanyID > {$begin_company} AND c.CompanyFlag='0' ".$sqlmsg."  ORDER BY c.CompanyID DESC";
$list_data = $db->get_results($datasql." ".$page->OffSet());
$calc_num = $db->get_results("SELECT COUNT(*) AS Total,CS_Flag FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_order_company_data as d ON d.CompanyID=c.CompanyID where c.CompanyID > {$begin_company} AND c.CompanyFlag='0' ".$sqlmsg."  GROUP BY CS_Flag");
$calc_num = array_column($calc_num ? $calc_num : array() , 'Total','CS_Flag');

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

        	  <form id="FormSearch" name="FormSearch" method="get" action="company_verify.php">

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
       	        </label>

				<label>

       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>
                  <label style="margin-left:50px;"></label>
                  <label><strong>待审：</strong><?php echo (int)$calc_num['D']; ?> </label>
                  <label><strong>待传：</strong><?php echo (int)$calc_num['W']; ?> </label>
                  <label><strong>通过：</strong><?php echo (int)$calc_num['T']; ?> </label>
                  <label><strong>未通过：</strong><?php echo (int)$calc_num['F']; ?> </label>

   	          </form>

   	        </div>

			<div class="location"><strong>当前位置：</strong><a href="company_verify.php">客户审核</a> </div>

        </div>

        <div class="line2"></div>

        <div class="bline">

        <div id="sortright" style="width:1174px;">

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>

   			       <td  class="sublink"></td>
       			     <td width="50%" align="right"><? echo $page->ShowLink('company_verify.php');?></td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>

                <tr>

                  <td width="5%" class="bottomlinebold">编号</td>

					<td width="8%" class="bottomlinebold">联系人</td>

                  <td class="bottomlinebold">企业名称</td>
                  
                  <td width="10%" class="bottomlinebold">手机号</td>
                  
                  <td width="25%" class="bottomlinebold">营业执照</td>
                  
                  <td width="15%" class="bottomlinebold">建立日期</td>

                    <td width="10%" class="bottomlinebold" style="text-align:left;">审核</td>

                </tr>

     		 </thead> 

      		<tbody>

<?php

	if(!empty($list_data))
	{

		foreach($list_data as $lsv)
		{ ?>


                <tr id="line_<? echo $lsv['CID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >

                  <td onclick="javascript: window.location.href='do_login.php?m=admintologin&companyid=<? echo $lsv['CID'];?>'">10<? echo $lsv['CID'];  ?></td><? $yunType = array('dhb'=>'','ali'=>'阿里','shuan'=>'曙安'); ?>

				  <td ><? echo $lsv['CompanyContact'];?></td>
				  
                  <td ><a target="_blank" href="manager_company.php?ID=<? echo $lsv['CID']; ?>"  title="<?php echo $lsv['CompanyRemark'];?>"><? echo $lsv['CompanyName'];?><span style="color:red;"><?  echo ' '.$yunType[$lsv['CompanyType']];?></span></a></td>

                  <td><? echo $lsv['CompanyMobile']; ?></td>
                  
                  <td ><? echo $lsv['BusinessCard'];?></td>
                  
				  <td ><? if(!empty($lsv['CompanyDate'])) echo date("y-m-d H:i",$lsv['CompanyDate']);?></td>
				  
                    <td style="text-align:left;" class="TitleNUM">
                        <?php
                            switch($lsv['CS_Flag']) {
                                case 'T': echo "通过"; break;
                                case 'F': echo "不通过"; break;
                                case 'D':
                                    echo "待审";
                                    echo '-' . '<a href="company_check.php?ID='.$lsv['CID'].'">去审核</a>';
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

       			     <td width="50%" align="right"><? echo $page->ShowLink('company_verify.php');?></td>

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
					$frontMsg  .= '<li><a href="company_verify.php?aid='.$var['AreaID'].'"  ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="company_verify.php?aid='.$var['AreaID'].'"  >'.$var['AreaName'].'</a>';
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