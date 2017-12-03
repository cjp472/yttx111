<?php 
$menu_flag = "common_count";
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
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script type="text/javascript" src="js/manager.js?v=<? echo VERID;?>"></script>
<script type="text/javascript">
    $(function() {
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

        	  <form id="FormSearch" name="FormSearch" method="get" action="contacts.php">

        	    <label>

        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" value="<?php echo $in['kw']?>" class="inputline" />

       	        </label>
				<label>
				<select id="uid" name="uid"  style="width:165px;" class="select2">
				<option value="" >⊙ 操作人</option>
				<?php 
					$n = 0;		
					$accarr = $db->get_results("SELECT u.UserID,u.UserName,u.UserTrueName FROM (SELECT DISTINCT CreateUID FROM ".DATABASEU.DATATABLE."_order_company_contact) v INNER JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUID=u.UserID ");

					foreach($accarr as $accvar)
					{
						$n++;
						$industryarr[$accvar['IndustryID']] = $accvar['IndustryName'];

						if($in['uid'] == $accvar['UserID']) $smsg = 'selected="selected"'; else $smsg ="";

						echo '<option value="'.$accvar['UserID'].'" '.$smsg.' title="'.$accvar['UserName'].'"  >'.$accvar['UserTrueName'].'</option>';
					}
				?>
				</select>
				</label>				

        	    <label>
        	      <select id="date_field" name="date_field"  style="width:105px;" class="select2">
					<option value="end_date" <?php if($in['date_field'] == 'end_date') echo 'selected="selected"';?> >到期时间</option>
					<option value="begin_date" <?php if($in['date_field'] == 'begin_date') echo 'selected="selected"';?> >开通时间</option>
				  </select>
       	        </label>
       	        
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />
       	        </label>
				<label>
       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />
   	            </label>
   	          </form>
   	        </div>
   	        <div class="location">
                <!--<strong>当前位置：</strong><a href="company_order.php">客户订单</a>-->
                <input type="button" class="mainbtn" onclick="location.href='contacts_edit.php'" value="新增联系人" title="新增联系人信息" />
            </div>
        </div>
        <div class="line2"></div>

        <div class="bline">
        <?php    
        	$sqlmsg = '';
        	if(empty($in['num'])){
        		if(!empty($in['uid']))  $sqlmsg .= " and v.CreateUID=".$in['uid']." "; else $in['uid'] = '';
        		if(!empty($in['uflag']))
        		{
    				$sqlmsg .= " and v.UseFlag=".$in['uflag']." ";
        		}else{
        			$in['uflag'] = '';
        		}
        		if($in['date_field'] == 'begin_date')
        	    {
        	        $datefield = 'cs.CS_BeginDate';
        	    }
        	    else
        	    {
        	        $datefield = 'cs.CS_EndDate';
        	    }
        	    if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
        	    if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";
        
        		if(!empty($in['kw']))  {
        		    $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "c.BusinessLicense like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactName like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactJob like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactMobile like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactPhone like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactQQ like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactEmail like '%".$in['kw']."%')";
        		}
        	}

        	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_contact v inner join ".DATABASEU.DATATABLE."_order_company c ON v.CompanyID=c.CompanyID where 1=1".$sqlmsg);
    
        	$page = new ShowPage;
        	$page->PageSize = 50;
        	$page->Total = $InfoDataNum['allrow'];
        	$page->LinkAry = array("kw"=>$in['kw'],"uid"=>$in['uid'],"uflag"=>$in['uflag'],"date_field"=>$in['date_field'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);
        	
        	$sql = "SELECT c.CompanyName,c.BusinessLicense,u.UserTrueName,v.*,cs.CS_BeginDate,cs.CS_EndDate FROM ".DATABASEU.DATATABLE."_order_company_contact v inner join ".DATABASEU.DATATABLE."_order_company c ON v.CompanyID=c.CompanyID LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS cs ON c.CompanyID=cs.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUID=u.UserID WHERE 1=1" .$sqlmsg. " ORDER BY CreateDate DESC";
        	$list_data = $db->get_results($sql." ".$page->OffSet());
        ?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
             
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>
                <tr>
                  <td width="4%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">公司名称/系统名称</td>
				  <td width="12%" class="bottomlinebold">联系人/联系人职务</td>
                  <td width="12%" class="bottomlinebold">手机/电话</td>
                  <td width="15%" class="bottomlinebold">QQ/邮箱</td>
                  <td width="10%" class="bottomlinebold">开通/到期时间</td>
                  <td width="6%"class="bottomlinebold">操作人</td>
                  <td width="7%"class="bottomlinebold">操作时间</td>
                  <td width="5%"class="bottomlinebold">操作</td>
                </tr>
     		 </thead> 
      		<tbody>

            <?php
            	if(!empty($list_data))
            	{
            		foreach($list_data as $key=>$lsv)
            		{
            
            ?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" title="<? echo $lsv['VisitContent'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)">
				
                  <td ><? echo $key+1;?></td>

                  <td ><a href="manager_company.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?></a></td>
				  <td ><? echo $lsv['ContactName'].'<br/>'.$lsv['ContactJob'];?></td>
				  <td ><? echo $lsv['ContactMobile'].'<br/>'.$lsv['ContactPhone'];?></td>
                  <td ><? echo $lsv['ContactQQ'].'<br/>'.$lsv['ContactEmail'];?></td>
                  <td >
                      <?php
                          echo $lsv['CS_BeginDate'];
        				  $timsgu = strtotime($lsv['CS_EndDate']);
        				  if($timsgu - time() < 30*24*60*60){
        					echo "<br/> <font color=red>".$lsv['CS_EndDate']."</font>";
        				  }else{
        					echo '<br/>'.$lsv['CS_EndDate'];
        				  }
    				  ?>
                  </td>  
                  <td ><? echo $lsv['UserTrueName'];?></td>
                  <td ><? echo date('Y-m-d H:i',$lsv['CreateDate']);?></td>
                  <td ><a href="contacts_edit.php?ID=<?php echo $lsv['ID']?>">[编辑]</a></td>
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
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('contacts.php');?></td>
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
			<h3 id="windowtitle" style="width:540px">时间线</h3>
			<div class="windowClose"><div class="close-form" onclick="closewindowui()" title="关闭" >x</div></div>
		</div>
		<div id="windowContent">
        正在载入数据...       
        </div>
	</div> 
</body>
</html>