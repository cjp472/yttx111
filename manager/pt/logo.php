<?php 
$menu_flag = "manager";
include_once ("../common.php");
$db  = dbconnect::dataconnect()->getdb();
setcookie("backurl", $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>



<body>

        

    <div id="bodycontent">

    	<div class="lineblank"></div>

        <div class="line2"></div>
        <div class="bline">
<?php

// 	$datasql = "select * from ".DATABASEU.DATATABLE."_order_company where CompanyLogo <> '' order by CompanyID DESC limit 0,100 ";
	$datasql = "SELECT c.*,i.IndustryName FROM ".DATABASEU.DATATABLE."_order_company AS c LEFT JOIN ".DATABASEU.DATATABLE."_common_industry AS i ON c.CompanyIndustry = i.IndustryID WHERE CompanyLogo <> '' ORDER BY CompanyID DESC LIMIT 0, 100 ";
	
	$list_data = $db->get_results($datasql);			
?>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>
                <tr>
                  <td width="8%" class="bottomlinebold">编号</td>
                  <td width="24%" class="bottomlinebold">公司名称</td>
                  <td width="24%" class="bottomlinebold">开通时间/行业</td>
				  <td width="24%" class="bottomlinebold">系统名称</td>

				  <td  class="bottomlinebold">LOGO</td>
                </tr>
     		 </thead> 

      		<tbody>

<?php
$i = 0;
	if(!empty($list_data))
	{
		foreach($list_data as $lsv)
		{
			$i++;
?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >

                  <td ><? echo $i;?></td>

                  <td ><? echo $lsv['BusinessLicense'];?></td>
                  <td >
                  	<? echo date('Y-m-d', $lsv['CompanyDate']);?><br />
                  	<? echo $lsv['IndustryName'];?>
                  </td>
				  <td><? 
				  echo $lsv['CompanyName'];
				  echo '<br /><a href="'.$lsv['CompanyWeb'].'" target="_blank">'.$lsv['CompanyWeb'].'</a>';
				  ?></td>

				  <td  ><div style="width:468px; height:90px; overflow:hidden;"><?php echo '<a href="http://'.$lsv['CompanyPrefix'].'.dhb.hk" target="_blank" title="http://'.$lsv['CompanyPrefix'].'.dhb.hk">';?><img src="<?php echo RESOURCE_URL.''.$lsv['CompanyLogo']; ?>"></a></div></td>
                </tr>
<? } }else{?>

     			 <tr>
       				 <td colspan="5" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>
              </table>


              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
       	  </div>

        <br style="clear:both;" />
    </div>
 


<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
</body>
</html>