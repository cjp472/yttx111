<?php 
$menu_flag = "manager";
include_once ("header.php");


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name='robots' content='noindex,nofollow' />

<title><? echo SITE_NAME;?> - 管理平台</title>

<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="css/showpage.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>



<body>

<?php include_once ("top.php");?>

    

<?php include_once ("inc/son_menu_bar.php");?>

        

    <div id="bodycontent">

    	<div class="lineblank"></div>

    	<div id="searchline">

        	<div class="leftdiv">

        	  <form id="FormSearch" name="FormSearch" method="get" action="regiester.php">

        	    <label>

        	      &nbsp;&nbsp;名称/联系人/电话： <input type="text" name="kw" id="kw" class="inputline" />

       	        </label>

				<label>

       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>

   	          </form>

   	        </div>

            

			<div class="location"><strong><a href="/m/company_regiest.php">客户注册</a> </strong></div>

        </div>

    	

        <div class="line2"></div>

        <div class="bline">



<?php

	$sqlmsg = '';

	if(!empty($in['kw']))  $sqlmsg .= " and (CompanyName like '%".$in['kw']."%' or CompanyPhone like '%".$in['kw']."%' or CompanyContact like '%".$in['kw']."%' ) ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_reg where CompanyFlag='0' ".$sqlmsg."  ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw']);        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_company_reg where CompanyFlag='0' ".$sqlmsg."  ORDER BY CompanyID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());			
?>




          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >


			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>

                <tr>

                  <td width="5%" class="bottomlinebold">行号</td>
				  <td width="6%" class="bottomlinebold">所在地区</td>
				  <td width="10%" class="bottomlinebold">所属行业</td>

                  <td class="bottomlinebold">公司名称</td>
				  <td width="20%" class="bottomlinebold">系统名称</td>

				  <td width="8%" class="bottomlinebold">英文简称</td>

                  <td width="8%" class="bottomlinebold">联系人</td>

                  <td width="10%" class="bottomlinebold" >联系电话</td>

                  <td width="10%" class="bottomlinebold" >提交时间</td>

                  <td width="6%" class="bottomlinebold" align="center">管理</td>

                </tr>

     		 </thead> 

      		<tbody>

<?php
	$accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
	foreach($accarr as $accvar)
	{
		$industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];
	}

	$sortarr = $db->get_results("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_common_city  ORDER BY AreaParent asc,AreaID ASC ");

	foreach($sortarr as $areavar)
	{
		$areaarr[$areavar['AreaID']] = $areavar['AreaName'];
	}


	$i = 0;

	if(!empty($list_data))

	{		

		foreach($list_data as $lsv)

		{
			$i++;
?>


                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)">

                  <td ><? echo $i;?></td>
				  <td ><? echo $areaarr[$lsv['CompanyArea']];?></td>
				  <td ><? echo $industryarr[$lsv['CompanyIndustry']];?></td>

                  <td ><? echo $lsv['BusinessLicense'];?></td>
				  <td ><? echo $lsv['CompanyName'];?></td>


                  <td ><? echo $lsv['CompanyPrefix'];?></td>
                  <td ><? echo $lsv['CompanyContact'];?></td>
                  <td ><? echo $lsv['CompanyPhone'];?></td>

                  <td ><? echo date('y-m-d H:i',$lsv['CompanyDate']);?></td>

                  <td align="center">				   

					<a href="company_add.php?ID=<? echo $lsv['CompanyID'];?>" ><img src="img/icon_edit.gif" border="0" title="开通" class="img" /></a>&nbsp;&nbsp;

					<a href="javascript:void(0);" onclick="do_delete_reg('<? echo $lsv['CompanyID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>

				  

				  </td>

                </tr>

<? } }else{?>

     			 <tr>

       				 <td colspan="12" height="30" align="center">暂无符合此条件的内容!</td>

       			 </tr>

<? }?>

 				</tbody>

                

              </table>

                 <table width="100%" border="0" cellspacing="0" cellpadding="0">

     			 <tr>

       				 <td width="4%"  height="30" ></td>



   			       <td  class="sublink"></td>

       			     <td width="50%" align="right"><? echo $page->ShowLink('regiester.php');?></td>

     			 </tr>

              </table>

              <INPUT TYPE="hidden" name="referer" value ="" >



              </form>


        </div>
        <br style="clear:both;" />
    </div>
 
<?php include_once ("bottom.php");?>

<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>