<?php 
$menu_flag = "common_count";
include_once ("header.php");

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
<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
<style>
        .allArea{
            width: 150px;
            height: 35px;
        }
        .nowState{
            float: right;
            margin-right: 15px;
            line-height: 40px;
            font-weight: bold;
        }
        .clientInfo tr td{
            width: auto;
            text-align: center;
            height: 40px;
            line-height: 40px;
            border-bottom: 1px  dashed darkgray;
        }
        .clientInfo thead tr td{
            border-bottom:2px  solid #CCCCCC;
        }
        .lookOver{
            cursor: pointer;
        }
        .moreDetail{
            overflow: hidden;
        }
        .DetailBox{
            width: 90%;
            margin: auto;
            padding-bottom: 30px;
        }
        .detailTitle{
            list-style: none;
            overflow: hidden;
            width: 100%;
        }
        .detailTitle li{
            float: left;
            width: 14%;
            text-align: center;
            font-size: 13px;
            color: #333333;
        	width:150px;
        }
        .detailInfo{
            border-top:2px solid #cccccc ;
            border-bottom:2px solid #cccccc ;
        }
        .detailInfo td {
            height: 40px;
            line-height: 40px;
            border-bottom: 1px dashed #cccccc;
        }
        .packUp{
            float: right;
            cursor: pointer;
        }
            .use{
                display: block;
                width: 50px;
                height: auto;
                background: blue;
                color: #ffffff;
                margin: auto;
            }
            .notUse{
                display: block;
                width: 50px;
                height: auto;
                background: #FF9933;
                color: #ffffff;
                margin: auto;
            }
        .LossOnline{
            display: block;
            width: 50px;
            height: auto;
            background: red;
            color: #ffffff;
            margin: auto;
        }
    </style>
<script type="text/javascript">
$(function() {
	$("#bdate").datepicker({changeMonth: true,	changeYear: true});
	$("#edate").datepicker({changeMonth: true,	changeYear: true});
});

//点击查看按钮
$(function(){
    $('body').on('click','.lookOver',function(){
        var company = $(this).attr('data-cm');
        var infos = $(this).parent().next().find('.DetailBox');
        $.post("do_use.php",
        		{m:"get_feedback", CompanyID: company},
        		function(data){
        			var rs = eval('(' + data + ')');
            		var str = "<ul class='detailTitle'><li>记录时间</li> <li>联系人</li><li>联系人职务</li><li>回访记录简情</li> <li>使用状态</li><li>详情查看</li></ul>";
            		if(rs !== null && rs.length > 0)
            		{
						str += "<div class='detailInfo'>";
						for(var i=0;i<rs.length;i++){
							str += "<ul class='detailTitle'>";
							str += "<li>" + ((rs[i].RecordDate == null)?"&nbsp;":rs[i].RecordDate) +"</li>";
							str += "<li>" + ((rs[i].ContactName == '')?"&nbsp;":rs[i].ContactName) +"</li>";
							str += "<li>" + ((rs[i].ContactJob == '')? "&nbsp;":rs[i].ContactJob) +"</li>";
							str += "<li>" + ((rs[i].VisitGeneral == '')? "&nbsp;":rs[i].VisitGeneral) +"</li>";
							if(rs[i].UseFlag == 'T')
								str += "<li><span  class='use'>使用</span></li>";
							else if(rs[i].UseFlag == 'L')
								str += "<li><span  class='LossOnline'>失联</span></li>";
							else
								str += "<li><span  class='notUse'>没用</span></li>";
							str += "<li><a target='_blank' href='use_detail.php?ID=" + rs[i].ID + "&CID="+ rs[i].CompanyID +"'>详情</a></li>";
							str += "</ul>";
						}
						str += "</div>";
                	}
            		else{
            			str += "<div class='detailInfo'>";
            			str += "<div style='text-align:center;'>暂无数据</div>";
            			str += "</div>";
                	}
            		
            		str += "<div class='packUp'>↑【收起】</div>";
            		infos.html(str);	
            		infos.css('display','block');	
        		}		
        	);
    		
    });

    $('body').on('click','.packUp',function(){
        $(this).parent().css('display','none');
    });
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

   	        </div>
        </div>

        <div class="line2"></div>

        <div class="bline">
<?php
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
		if(!empty($in['gid']))  $sqlmsg .= " and c.CompanyAgent=".$in['gid']." "; else $in['gid'] = '';
		if($in['date_field'] == 'begin_date'){
			$datefield = 's.CS_BeginDate';
		}else{
			$datefield = 's.CS_EndDate';
		}
		if(!empty($in['bdate'])) $sqlmsg .= " and ".$datefield." >= '".$in['bdate']."' ";
		if(!empty($in['edate'])) $sqlmsg .= " and ".$datefield." <= '".$in['edate']."' ";

		if(!empty($in['kw']))  $sqlmsg .= " and CONCAT(c.BusinessLicense,c.IdentificationNumber,c.CompanyName,c.CompanySigned,c.CompanyContact,c.CompanyMobile,c.CompanyPhone,c.CompanyEmail) like '%".$in['kw']."%' ";


			$databasearr1 = $databasearr;
			if(!isset($in['d']) || $in['d'] == '') $in['d'] = array_pop($databasearr1);
			$in['d'] = intval($in['d']); 
			$sqlmsg .= " and c.CompanyDatabase=".$in['d']." ";

			if(empty($in['d'])){
				$sdatabase = DB_DATABASE.'.';
			}else{
				$sdatabase = DB_DATABASE.'_'.$in['d'].'.';
			}


		$datasql   = "SELECT c.*,s.* FROM ".DATABASEU.DATATABLE."_order_company c left join ".DATABASEU.DATATABLE."_order_cs s on c.CompanyID=s.CS_Company  where c.CompanyFlag='0' and s.CS_Flag='T'  ".$sqlmsg."  ORDER BY c.CompanyID DESC";
		$list_data = $db->get_results($datasql);			
?>

          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold"></td>
					 <td align="right"  height="30" class="bottomlinebold">
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="use.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
				 }
				 ?>
					 </td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>
                <tr>
                  <td width="6%" class="bottomlinebold">编号</td>
                  <td class="bottomlinebold">公司名称/系统名称</td>
				  <td width="8%" class="bottomlinebold">品类</td>
				  <td width="8%" class="bottomlinebold">商品</td>
				  <td width="8%" class="bottomlinebold">经销商</td>
				  <td width="10%" class="bottomlinebold">许可经销商</td>
				  <td width="7%" class="bottomlinebold">管理员</td>                  
				  <td width="9%" class="bottomlinebold">短信数</td>
				  <td width="16%" class="bottomlinebold" align="left">开通/到期时间</td>
				  <td width="8%" class="bottomlinebold">详细情况</td>
                  <td width="6%"class="bottomlinebold">操作</td>
                </tr>
     		 </thead> 

      		<tbody>

<?php
	if(!empty($list_data))
	{
		//商品
		$sqlgoods = "select CompanyID,count(*) as num from ".$sdatabase.DATATABLE."_order_content_index group by CompanyID ";
		$goodscount = $db->get_results($sqlgoods);
		foreach($goodscount as $v){
			$goodsarr[$v['CompanyID']] = $v['num'];
		}
		
		//分类
		$sqlsort = "select CompanyID,count(*) as num from ".$sdatabase.DATATABLE."_order_site group by CompanyID ";
		$sortcount = $db->get_results($sqlsort);
		foreach($sortcount as $v){

			$sitearr[$v['CompanyID']] = $v['num'];
		}

		//经销商
		$sqlclient = "select ClientCompany as CompanyID,count(*) as num from ".$sdatabase.DATATABLE."_order_client where ClientFlag='0' group by ClientCompany ";
		$clientcount = $db->get_results($sqlclient);
		foreach($clientcount as $v){

			$clientarr[$v['CompanyID']] = $v['num'];
		}

		//管理员
		$sqluser = "select UserCompany as CompanyID,count(*) as num from ".DATABASEU.DATATABLE."_order_user group by UserCompany ";
		$usercount = $db->get_results($sqluser);
		foreach($usercount as $v){

			$userarr[$v['CompanyID']] = $v['num'];
		}

		foreach($list_data as $lsv)
		{
?>
                <tr id="line_<? echo $lsv['CompanyID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >
                  <td >10<? echo $lsv['CompanyID'];?></td>
                  <td ><a href="use_content.php?ID=<? echo $lsv['CompanyID']; ?>"  title="<?php echo $lsv['IdentificationNumber'];?>" target="_blank"><? echo $lsv['BusinessLicense'].'<br />'.$lsv['CompanyName'];?> <span style="color:red;"><?  echo ' '.$yunType[$lsv['CompanyType']];?></span></a></td>
				  <td><?php if(!empty($sitearr[$lsv['CompanyID']])) echo $sitearr[$lsv['CompanyID']]; else echo '0';?></td>

				  <td ><?php if(!empty($goodsarr[$lsv['CompanyID']])) echo $goodsarr[$lsv['CompanyID']]; else echo '0';?></td>
				  <td ><?php if(!empty($clientarr[$lsv['CompanyID']])) echo $clientarr[$lsv['CompanyID']]; else echo '0';?></td>
				  <td ><strong><? if($lsv['CS_Number']=='10000') echo '<font color=red>无限</font>'; else echo $lsv['CS_Number'];?> </strong></td>
                  <td ><?php echo $userarr[$lsv['CompanyID']];?></td>
				  <td ><strong><? echo $lsv['CS_SmsNumber'];?> 条 </strong></td>                  
                  <td ><? echo $lsv['CS_BeginDate'];?>
                  <?php
				  $timsgu = strtotime($lsv['CS_EndDate']);
				  if($timsgu - time() < 30*24*60*60){
					echo " - <font color=red>".$lsv['CS_EndDate']."</font>";
				  }else{
					echo ' - '.$lsv['CS_EndDate'];
				  }
				  ?>
                  </td>
                  <td class="lookOver" data-cm="<? echo $lsv['CompanyID'];?>">查看</td>
                  <td >
                      [<a target="_blank" href="use_contact.php?id=<?php echo $lsv['CompanyID']; ?>">回访记录</a>]
                  </td>
                </tr>
                <tr class='moreDetail'><td colspan="10"><div class="DetailBox"  style="display: none">sd</div></td></tr>
<? } }else{?>

     			 <tr>
       				 <td colspan="8" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>
              </table>

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="100" align="center"><?php echo count($list_data);?></td>
					 <td align="right"  height="30" >
				 <?php
				 foreach($databasearr as $key=>$var){
					echo '<a href="use.php?d='.$key.'">['.$var.']</a>&nbsp;&nbsp;';
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