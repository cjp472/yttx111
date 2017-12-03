<?php
$menu_flag = "common_count";
include_once ("header.php");

if(empty($in['ID'])) exit('非法路径!');
$cid = intval($in['ID']);
$cinfo = $db->get_row("SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company where CompanyID=".intval($in['ID'])." limit 0,1");
$contacts = $db->get_results("SELECT * FROM ".DATABASEU.DATATABLE."_order_company_contact where CompanyID=".intval($in['ID'])." order by CreateDate DESC");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name='robots' content='noindex,nofollow' />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script src="js/function.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style>
.add{
    display: inline-block;
     width: 20px;
     height: 20px;
     background: url("img/u84.png") no-repeat 0px 0px;
    cursor: pointer;
    margin-right: 10px;
 }
.del{
    display: inline-block;
    width:20px;
    height: 20px;
    background: url("img/u84.png") no-repeat -20px 0px;
    cursor: pointer;
}
.edit{
    display: inline-block;
    width:20px;
    height: 20px;
    background: url("img/u84.png") no-repeat -37px 0px;
    cursor: pointer;
}
.btn{
    float: right;
    margin-top: -10px;
    margin-right: 10px;
}
</style>
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

			<div class="location"><strong>当前位置：</strong><a href="manager.php">客户管理</a> &#8250;&#8250; <? echo $cinfo['CompanyName'];?></div>
        </div>  	

        <div class="line2"></div>
        <div class="bline">
       	  <div id="sortleft" style="width:45%; padding:8px;">

            <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			
			<fieldset  class="fieldsetstyle">
			<legend>属性资料</legend>
			
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                  <td width="55%"><label>
                    <? 
					$areainfo = $db->get_row("SELECT AreaID,AreaParent,AreaName FROM ".DATABASEU.DATATABLE."_order_city where AreaID=".$cinfo['CompanyArea']." ORDER BY AreaID ASC ");
					echo $areainfo['AreaName'];
					?>
                    </label></td>
                  
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                  <td>
                    <? 
					$industryinto = $db->get_row("SELECT IndustryID,IndustryName FROM ".DATABASEU.DATATABLE."_order_industry where IndustryID=".$cinfo['CompanyIndustry']." ORDER BY IndustryID ASC limit 0,1");
					echo $industryinto['IndustryName']." ";
					?>
					</td>
                 
                </tr>
            </table>
           </fieldset>             

            <br style="clear:both;" />
            <fieldset title="基本资料" class="fieldsetstyle">
		      <legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">           
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">公司名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $cinfo['BusinessLicense'];?>
					</label></td>
                  
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">营业执照号：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $cinfo['IdentificationNumber'];?>
          </label></td>
                  
                </tr>
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">订货系统名称：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? echo $cinfo['CompanyName'];?>
          </label></td>
                  
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">简称：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanySigned'];?></td>
                 
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">帐号前缀：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPrefix'];?></td>
                  
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所在城市：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyCity'];?></td>
                 
                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">联系人：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyContact'];?></td>

                  

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">移动电话：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyMobile'];?>

                  </td>

                 

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">联系电话：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyPhone'];?>

                  </td>

                 

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">传 真：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyFax'];?></td>

                 

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">详细地址：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyAddress'];?></td>

                 

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">E-mail：</div></td>

                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyEmail'];?>&nbsp;</td>



                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">客户网站：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyWeb'];?></td>

                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">订货入口链接：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $cinfo['CompanyUrl'];?>&nbsp;</td>

                </tr>

                <tr>

                  <td bgcolor="#F0F0F0"><div align="right">备 注：</div></td>

                  <td bgcolor="#FFFFFF"><? echo nl2br($cinfo['CompanyRemark']);?>&nbsp;</td>



                </tr>

              </table>

			</fieldset>



			<br style="clear:both;" />

		   <fieldset title="设置" class="fieldsetstyle">

			<legend>设置</legend>

            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">用户数：</div></td>

                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['CS_Number'];?>&nbsp;</td>



                </tr>  

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">开通时间：</div></td>

                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_BeginDate'];?>&nbsp;</td>



                </tr> 

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">到期时间：</div></td>

                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_EndDate'];?>&nbsp;</td>



                </tr> 

                <tr>

                  <td bgcolor="#F0F0F0" width="16%"><div align="right">最近更新时间：</div></td>

                  <td bgcolor="#FFFFFF" width="55%"><? echo $cinfo['CS_UpDate'];?>&nbsp;</td>



                </tr>
                <tr>
                  <td bgcolor="#F0F0F0" width="16%"><div align="right">短信条数：</div></td>
                  <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cinfo['CS_SmsNumber'];?></td>

                </tr>

          </table>

          </fieldset>  

              <INPUT TYPE="hidden" name="referer" value ="" >

          </form>


       	  </div>

        <div id="sortright" style="width:50%; margin:8px;">
         

			<fieldset  class="fieldsetstyle">
			<legend>基础数据</legend>
			
            <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属地区：</div></td>
                  <td width="55%"><label>
                   
                    </label></td>
                  
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">所属行业：</div></td>
                  <td>
                   
					</td>
                 
                </tr>
            </table>
           </fieldset>     

            <br style="clear:both;" />
               
		   <fieldset title="" class="fieldsetstyle">
            <!-- <div class="btn" title="保存"><div class="add" data="<?php echo $in['ID'];?>" id="addcontact" onclick="addcontact();"></div></div> -->
			<legend>新增联系人</legend>
			<form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">联系人：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactName" id="ContactName" value="" /><span style="color:red;">*</span>&nbsp;</td>
                    </tr>  
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">职     务：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactJob" id="ContactJob" value="" style="width:150px;"/>&nbsp;&nbsp;&nbsp;
                          <select id="Job" class="select2" style="width: 150px;" onchange=" document.getElementById('ContactJob').value = this.options[this.selectedIndex].value">
                            <option value="">⊙ 选择职位</option>
                            <option value='经理'>经理</option>
                            <option value='仓库'>仓库</option>
                            <option value='总经理'>总经理</option>
                            <option value='办公室文员'>办公室文员</option>
                            <option value='财务'>财务</option>
                            <option value='网运部经理'>网运部经理</option>
                            <option value='文员'>文员</option>
                            <option value='助理'>助理</option>
                            <option value='新媒体运营'>新媒体运营</option>
                            <option value='客户服务部主管'>客户服务部主管</option>
                            <option value='总务'>总务</option>
                            <option value='业务主管'>业务主管</option>
                            <option value='销售商务'>销售商务</option>
                            <option value='会计'>会计</option>
                          </select>
                      </td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">电     话：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactPhone" id="ContactPhone" value="" />&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">手     机：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactMobile" id="ContactMobile" value="" />&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">Q  Q：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactQQ" id="ContactQQ" value="" />&nbsp;</td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">邮    箱：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<input name="ContactEmail" id="ContactEmail" value="" /></td>
                    </tr>
              </table>
              <div class="rightdiv sublink" style="padding-right:20px; ">
        		<input type="button" id="savecontact" class="button_1" name="savecontact" cdata="<?php echo $in['ID']?>" onclick="addcontact();" value="保存"/>
        		<input name="resetcompanyid" type="button" class="button_3" onclick="resets();" id="resetcompanyid" value="重 置" />
    		  </div>
		  </form>
          </fieldset>  
          <?php if(!empty($contacts)){?>
            <?php foreach($contacts as $cvar){?>
                <fieldset title="" class="fieldsetstyle">
                <div class="btn" >
                    <div class="edit" title="编辑" onclick="editcontact(<?php echo $cvar['ID'];?>);"></div>
                    <div class="del" title="删除" onclick="delcontact(<?php echo $cvar['ID'];?>);"></div>
                </div>
    			<legend>已有联系人</legend>
                <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF"  class="inputstyle">
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">联系人：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cvar['ContactName'];?>&nbsp;</td>
                    </tr>  
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">职     务：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cvar['ContactJob'];?>&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">电     话：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cvar['ContactPhone'];?>&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">手     机：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cvar['ContactMobile'];?>&nbsp;</td>
                    </tr> 
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">Q  Q：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cvar['ContactQQ'];?>&nbsp;</td>
                    </tr>
                    <tr>
                      <td bgcolor="#F0F0F0" width="16%"><div align="right">邮    箱：</div></td>
                      <td bgcolor="#FFFFFF" width="55%">&nbsp;<? echo $cvar['ContactEmail'];?></td>
                    </tr>
              </table>
              </fieldset>
              <?php  } ?>
         <?php  } ?>
       	  </div>
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