<?php
$menu_flag = "open";
include_once ("header.php");
//$data_info = $db->get_row("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_data WHERE CompanyID={$cid} LIMIT 1");
//$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");
if(empty($in['ID'])) exit('非法路径!');
$company_id = $in['ID'];
$user_id = $in['UID'];
$ucSql = "SELECT c.*,m.* FROM ".DATATABLE."_order_client AS c LEFT JOIN ".DATABASEU.DATATABLE."_three_sides_merchant AS m ON c.ClientCompany=m.CompanyID AND c.ClientID=m.MerchantID WHERE c.ClientCompany=".$company_id." and c.ClientID=".$user_id." limit 1";
$result = $db->get_row($ucSql);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.treeview.css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<script type="text/javascript">
/******tree****/
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
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
</script>
</head>

<body>
<?php include_once ("top.php");?>
<?php include_once ("inc/son_menu_bar.php");?>        

    <div id="bodycontent">
    	<div class="lineblank"></div>
    	<div id="searchline">
        	<div class="leftdiv">
        	  <form id="FormSearch" name="FormSearch" method="post" action="manager.php">
        	    <label>
        	      <strong>&nbsp;&nbsp;<? echo $cinfo['CompanyName'];?></strong>
       	        </label>
   	          </form>
   	        </div>       

			<div class="location"><strong>当前位置：</strong><a href="#">客户管理</a> &#8250;&#8250; <? echo $cinfo['CompanyName'];?></div>
        </div>  	

        <div class="line2"></div>
        <div class="bline">
       	  <div id="sortleft">

		  <hr style="clear:both;" />
<!-- tree --> 

<div id="sidetree"> 

<hr style="clear:both;" />
<div class="leftlist">
<div >
<strong><img src="css/images/home.gif" alt="地区"  />&nbsp&nbsp<a href="manager.php">行业分类</a></strong></div>
<ul>
	<?php 
		$accarr = $db->get_results("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_common_industry ORDER BY IndustryID ASC ");
		foreach($accarr as $accvar)
		{
			if($in['iid'] == $accvar['IndustryID']) $smsg = 'class="locationli"'; else $smsg ="";
			echo '<li><a href="manager.php?iid='.$accvar['IndustryID'].'" '.$smsg.' > - '.$accvar['IndustryName'].'</a></li>';
		}
	?>
</ul>
<br style="clear:both;" />
</div>
       	  </div>



        </div>
          <div id="sortright">
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >



          <br style="clear:both;" />
          <fieldset title="法人资料" class="fieldsetstyle">
              <legend>法人资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                  <tr>
                      <td width="15%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                      <td width="35%" bgcolor="#FFFFFF"><label>
                              <? echo $result['BusinessName'];?>
                          </label></td>
                  </tr>
                  <tr>
                      <td width="15%" bgcolor="#F0F0F0"><div align="right">姓名：</div></td>
                      <td width="35%" bgcolor="#FFFFFF"><label>
                              <? echo $result['TureUserName'];?>
                          </label></td>
                  </tr>
                  <tr>
                      <td width="15%" bgcolor="#F0F0F0"><div align="right">手机号：</div></td>
                      <td width="35%" bgcolor="#FFFFFF"><? echo $result['UserPhone'];?></td>
                  </tr>
                  <tr>
                      <td width="15%" bgcolor="#F0F0F0"><div align="right">身份证号：</div></td>
                      <td width="35%" bgcolor="#FFFFFF"><? echo $result['IDCard'];?></td>
                  </tr>
                  <tr>
                      <td width="15%" bgcolor="#F0F0F0"><div align="right">身份证：</div></td>
                      <td width="70%" bgcolor="#FFFFFF">
                          <?php    
                            $IDCardImg = explode(',',$result['IDCardImg']); 
                            if(count($IDCardImg)>0){
                                foreach ( $IDCardImg as $v) {
                                    echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$v).'" title="点击查看大图" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$v).'" border="0" height="150px"  width="200px" style="float:left;margin-left:20px" />';                                    
                                }
                            }

                      ?></td>
                  </tr>
              </table>

          </fieldset>

          <br style="clear:both;" />
                <fieldset class="fieldsetstyle">
                    <legend>认证信息</legend>
                    <div >
                        <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                        	<tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">药企名称：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                   <label> 
                                        <?php echo $result['ClientCompanyName']; ?>
                                   </label>
                                </td>
                                <td width="29%" bgcolor="#FFFFFF"></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <label> 
                                        <?php echo $result['BusinessCard']; ?>
                                    </label>
                                </td>
                                <td width="29%" bgcolor="#FFFFFF"></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right"></div></td>
                                <td colspan="2" bgcolor="#FFFFFF">
                                    <?php if(!empty($result['BusinessCardImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$result['BusinessCardImg']).'" title="点击查看大图" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$result['BusinessCardImg']).'" border="0" height="150px" />';?>
                                </td>
                            </tr>
                            <!-- 
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">身份证号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <?php echo $result['IDCard']; ?>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">&nbsp;</div></td>
                                <td colspan="2" bgcolor="#FFFFFF">                               
                                    <? if(!empty($result['IDCardImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$result['IDCardImg']).'" title="点击查看大图" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$result['IDCardImg']).'" border="0" height="150px"  />';?>
                                </td>
                                <td></td>
                            </tr>
                             -->
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">药品经营许可证号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <label> 
                                        <?php echo $result['IDLicenceCard']; ?>
                                    </label>
                                </td>
                                <td width="29%" bgcolor="#FFFFFF"></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">
                                <?php 
//                                 if($result['CompanyType'] == V ){
//                                         echo'药品生产许可证：';          
//                                 }elseif($result['CompanyType'] == S){
//                                         echo'药品经营许可证：';          
//                                 }?>
                                </div></td>
                                <td colspan="2" bgcolor="#FFFFFF">                               
                                    <?php if(!empty($result['IDLicenceImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$result['IDLicenceImg']).'" title="点击查看大图" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$result['IDLicenceImg']).'" border="0" height="150px"  />';?>
                                </td>
                                <td></td>
                            </tr>
                             <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">GSP认证号码：</div></td>
                                <td width="55%" bgcolor="#FFFFFF" colspan="2">
                                    <label> 
                                        <?php echo $result['GPCard']; ?>
                                    </label>
                                </td>
                                <td width="29%" bgcolor="#FFFFFF"></td>
                            </tr>
                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">
                                   </div></td>
                                <td colspan="2" bgcolor="#FFFFFF">                               
                                    <?php if(!empty($result['GPImg'])) echo '<a href="'.RESOURCE_URL.str_replace("thumb_","img_",$result['GPImg']).'" title="点击查看大图" target="_blank"><img src="'.RESOURCE_URL.str_replace("thumb_","img_",$result['GPImg']).'" border="0" height="150px"  />';?>
                                </td>
                                <td></td>
                            </tr>
<!--                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">医统天下合作协议</div></td>
                                <td colspan="3" bgcolor="#FFFFFF">                               
                                    <?php 
				                   		$select = "select * from ".DATABASEU.DATATABLE."_three_sides where CompanyID=".$result['ClientCompany']." and ClientID=".$result['ClientID']." and MuserID=0 and FlieType='YTClient' order by tid asc";
				                   		$resultYT = $db->get_results($select);
				                   		$tmpArr = array();
				                   ?>
				                   <ul class="three-silds-box" id="three_silds_box">
				                   	<?php 
				                   			for($i=0;$i<count($resultYT);$i++){
				                   				$tmpArr[] = array('filepath' => $resultYT[$i]['FilePath'], 'filename' => $resultYT[$i]['FileName']);
				                   ?>
				                   	<li><img src="<?php echo RESOURCE_URL.$resultYT[$i]['FilePath']."".$resultYT[$i]['FileName'];?>" onclick="window.open(this.src);" title="点击查看大图" alt="点击查看大图" /></li>
				                   	<?php }?>
				                   </ul>
                                </td>
                                <td></td>
                            </tr>-->
<!--                            <tr>
                                <td width="16%" bgcolor="#F0F0F0"><div align="right">产业园自有合作协议</div></td>
                                <td colspan="3" bgcolor="#FFFFFF">                               
                                    <?php 
				                   		$select = "select * from ".DATABASEU.DATATABLE."_three_sides where CompanyID=".$result['ClientCompany']." and ClientID=".$result['ClientID']." and MuserID=0 and FlieType='OtherClient' order by tid asc";
				                   		$resultMer = $db->get_results($select);
				                   		$tmpArr = array();
				                   ?>
				                   <ul class="three-silds-box" id="three_merchant_box">
				                   	<?php 
				                   			for($i=0;$i<count($resultMer);$i++){
				                   				$tmpArr[] = array('filepath' => $resultMer[$i]['FilePath'], 'filename' => $resultMer[$i]['FileName']);
				                   ?>
				                   	<li><img src="<?php echo RESOURCE_URL.$resultMer[$i]['FilePath']."".$resultMer[$i]['FileName'];?>" onclick="window.open(this.src);" title="点击查看大图" alt="点击查看大图" /></li>
				                   	<?php }?>
				                   </ul>
                                </td>
                                <td></td>
                            </tr>-->
                        </table>
                    </div>
                </fieldset>


          <br style="clear:both;" />
          <br style="clear:both;"/>

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
					$frontMsg  .= '<li><a href="manager.php?aid='.$var['AreaID'].'"  ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="manager.php?aid='.$var['AreaID'].'"  >'.$var['AreaName'].'</a>';
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