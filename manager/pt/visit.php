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
<link rel="stylesheet" href="css/jquery.treeview.css" />
<link rel="stylesheet" href="css/showpage.css" />
<link type="text/css" href="../plugin/jquery-ui/development-bundle/themes/base/ui.all.css" rel="stylesheet" />
<script src="../scripts/jquery.min.js" type="text/javascript"></script>
<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>
<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<style>
    a:hover{cursor:pointer;}
</style>
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

        	  <form id="FormSearch" name="FormSearch" method="get" action="visit.php">

        	    <label>

        	      &nbsp;&nbsp;<strong>搜索</strong>： <input type="text" name="kw" id="kw" value="<?php echo $in['kw'];?>" class="inputline" />

       	        </label>
				<label>
				<select id="uid" name="uid"  style="width:165px;" class="select2">
				<option value="" >⊙ 订货宝客服</option>
				<?php 
					$n = 0;		
					$accarr = $db->get_results("SELECT u.UserID,u.UserName,u.UserTrueName FROM (SELECT DISTINCT CreateUID FROM ".DATABASEU.DATATABLE."_order_company_visit) v INNER JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUid=u.UserID ");

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
				<select id="uflag" name="uflag"  style="width:135px;" class="select2">
    				<option value="" >⊙ 状态</option>
    				<option value="F" <?php if($in['uflag'] == 'F') echo "selected='selected'"?>>未用</option>
    				<option value="T" <?php if($in['uflag'] == 'T') echo "selected='selected'"?>>使用</option>
    				<option value="L" <?php if($in['uflag'] == 'L') echo "selected='selected'"?>>失联</option>
				</select>
				</label>				

        	    <label>
        	      <select id="date_field" name="date_field"  style="width:105px;" class="select2">
                  <option value="visit_date" <?php if($in['date_field'] == 'visit_date') echo 'selected="selected"';?> >回访时间</option>
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
                <input type="button" class="mainbtn" onclick="location.href='visit_contact.php'" value="新增记录" title="新增回访记录信息" />
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
    				$sqlmsg .= " and v.UseFlag='".$in['uflag']."' ";
        		}else{
        			$in['uflag'] = '';
        		}
        		if($in['date_field'] == 'begin_date')
        	    {
        	        $datefield = 'cs.CS_BeginDate';
        	    }
        	    elseif($in['date_field'] == 'visit_date')
                {
                    $datefield = 'v.RecordDate';
                }else{
        	        $datefield = 'cs.CS_EndDate';
        	    }
        	    if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
        	    if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";
        
        		if(!empty($in['kw']))  {
        		    $sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "c.BusinessLicense like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactName like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactJob like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.ContactPhone like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.VisitGeneral like '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.RecordDate like binary '%".$in['kw']."%' or ";
        		    $sqlmsg .= "v.VisitContent like '%".$in['kw']."%')";
        		}
        	}

        	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_company_visit v inner join ".DATABASEU.DATATABLE."_order_company c ON v.CompanyID=c.CompanyID where v.CreateUID is not null".$sqlmsg);
        	
        	$page = new ShowPage;
        	$page->PageSize = 50;
        	$page->Total = $InfoDataNum['allrow'];
        	$page->LinkAry = array("kw"=>$in['kw'],"uid"=>$in['uid'],"uflag"=>$in['uflag'],"date_field"=>$in['date_field'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);
        	
        	$sql = "SELECT c.CompanyName,c.BusinessLicense,u.UserTrueName,v.*,cs.CS_BeginDate,cs.CS_EndDate FROM ".DATABASEU.DATATABLE."_order_company_visit v inner join ".DATABASEU.DATATABLE."_order_company c ON v.CompanyID=c.CompanyID LEFT JOIN ".DATABASEU.DATATABLE."_order_cs AS cs ON c.CompanyID=cs.CS_Company LEFT JOIN ".DATABASEU.DATATABLE."_order_user u ON v.CreateUID=u.UserID WHERE v.CreateUID is not null" .$sqlmsg. " ORDER BY CreateDate DESC";
        	$list_data = $db->get_results($sql." ".$page->OffSet());
        ?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
			     <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <thead>
                <tr>
                  <td width="4%" class="bottomlinebold">编号</td>
                  <td width="17%" class="bottomlinebold">公司名称/系统名称</td>
		          <td width="8%" class="bottomlinebold">回访方式/时间</td>
                  <td width="13%" class="bottomlinebold">联系电话<br />回访简情</td>
                  <td width="40%" class="bottomlinebold">回访详情</td>
                  <td width="6%" class="bottomlinebold">到期时间</td>
                  <td width="5%" class="bottomlinebold">使用状态</td>
                  <td width="4%" class="bottomlinebold">操作人</td>
                  <td width="3%" class="bottomlinebold">操作</td>
                </tr>
     		   </thead> 
      		<tbody>

            <?php
            	if(!empty($list_data))
            	{
            		foreach($list_data as $key=>$lsv)
            		{
            
            ?>

                <tr id="line_<? echo $lsv['CompanyID'];?>" title="<? echo $lsv['VisitContent'];?>" class="bottomline" <?php if($lsv['UseFlag'] =='F') { echo "style='background-color:rgba(253, 251, 210, 0.7);'"; } elseif ($lsv['UseFlag'] =='L'){ echo "style='background-color:rgba(253, 199, 189,0.7);'"; }?>>
				
                  <td ><? echo $key+1;?></td>

                  <td ><a href="company_visit_contact.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['CompanyName'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?></a></td>
				  <td >
                    <? 
                        if($lsv['ContactType'] =='QQ') echo "QQ";
                        else if($lsv['ContactType'] =='Email') echo "邮件";
                        else echo "电话";
                        echo "<br/>".$lsv['RecordDate'];
                    ?>
                  </td>
                  <td class="lookOver" data-cm="<? echo $lsv['CompanyID'];?>"><? echo $lsv['ContactPhone'];?><br /><? echo $lsv['VisitGeneral'];?></td>
                  <td ><span class="lookOver"><? echo $lsv['VisitContent'];?></span></td> 
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
                  <td align="center" >
                    <? if($lsv['UseFlag'] =='F')echo "未用";
                       else if($lsv['UseFlag'] =='L') echo "失联";
                       else echo "使用";
                    ?>
                  </td>
                  <td ><? echo $lsv['UserTrueName'];?></td>
                  <td ><a href="visit_contact.php?vid=<? echo $lsv['ID'];?>" target="_blank">[编辑]</a></td>
                </tr>
                <? }?>
                <tr>
       				 <td colspan="6" height="30" align="center">符合记录的条数 <font color="#FF0000"><? echo $InfoDataNum['allrow']?></font> 条</td>
       			 </tr>
                <?  }else{?>

     			 <tr>
       				 <td colspan="6" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
             <? }?>
 				</tbody>
              </table>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
                        <td width="4%"  height="30" ></td>
    
                        <td  align="right"><? echo $page->ShowLink('visit.php');?></td>
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