<?php 
$menu_flag = "agent";
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

<script src="../scripts/jquery.cookie.js" type="text/javascript"></script>

<script src="../scripts/jquery.treeview.js" type="text/javascript"></script>

<script src="../scripts/jquery.blockUI.js" type="text/javascript"></script>

<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.core.js"></script>
<script type="text/javascript" src="../plugin/jquery-ui/development-bundle/ui/ui.datepicker.js"></script>

<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>

</head>



<body>

<?php include_once ("top.php");?>

    

<?php include_once ("inc/son_menu_bar.php");?>

        

    <div id="bodycontent">

    	<div class="lineblank"></div>

    	<div id="searchline">

        	<div class="leftdiv">

        	  <form id="FormSearch" name="FormSearch" method="get" action="agent.php">

        	    <label>

        	      &nbsp;&nbsp;名称/联系人/电话： <input type="text" name="kw" id="kw" class="inputline" />

       	        </label>

        	    <label>
        	      &nbsp;&nbsp;填加时间： 
       	        </label>
				<label>&nbsp;&nbsp;<input type="text" name="bdate" id="bdate" class="inputline" style="width:80px;" value="<? if(!empty($in['bdate'])) echo $in['bdate'];?>" /> - </label>
				<label>&nbsp;&nbsp;<input type="text" name="edate" id="edate" class="inputline" style="width:80px;" value="<? if(!empty($in['edate'])) echo $in['edate'];?>" />

       	        </label>
				<label>

       	          <input name="searchbutton" type="submit" class="mainbtn" id="searchbutton" value=" 搜 索 " />

   	            </label>

   	          </form>

   	        </div>

            

			<div class="location"><input type="button" name="newbutton" id="newbutton" value=" 新增代理商 " class="button_2" onclick="javascript:window.location.href='agent_add.php'" /> </div>

        </div>

    	

        <div class="line2"></div>

        <div class="bline">



<?php

	$sqlmsg = '';
	if(!empty($in['aid']))
	{
		if(empty($areainfoselected['AreaParent']))
		{
			$sqlmsg .= " and ( AgentArea=".$in['aid']." or AgentArea in (SELECT AreaID FROM ".DATABASEU.DATATABLE."_order_city where  AreaParent=".$in['aid']." ORDER BY AreaID ASC) ) ";
		}else{
			$sqlmsg .= " and AgentArea=".$in['aid']." ";
		}
	}else{
		$in['aid'] = '';
	}
	if(!empty($in['bdate'])) $sqlmsg .= " and CreateDate >= '".$in['bdate']."' ";
	if(!empty($in['edate'])) $sqlmsg .= " and CreateDate <= '".$in['edate']."' ";

	if(!empty($in['kw']))  $sqlmsg .= " and concat(AgentName,AgentContact,AgentPhone,AgentAddress,AgentQQ,AgentEmail ) like '%".$in['kw']."%' ";

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_order_agent where 1=1 ".$sqlmsg."  ");
	$page = new ShowPage;
    $page->PageSize = 100;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],"aid"=>$in['aid'],"bdate"=>$in['bdate'],"edate"=>$in['edate']);        
	
	$datasql   = "SELECT * FROM ".DATABASEU.DATATABLE."_order_agent where 1=1 ".$sqlmsg." ORDER BY AgentID DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());			
?>




          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >

              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>

   			       <td  class="sublink"></td>
       			     <td width="50%" align="right"><? echo $page->ShowLink('manager.php');?></td>
     			 </tr>
              </table>
			  <table width="100%" border="0" cellspacing="0" cellpadding="0">

               <thead>

                <tr>

                  <td width="5%" class="bottomlinebold">编号</td>

                  <td class="bottomlinebold">名称</td>

				  <td width="10%" class="bottomlinebold">联系人</td>

                  <td width="12%" class="bottomlinebold">联系方式</td>

                  <td width="16%" class="bottomlinebold" align="right">合同时间</td>

                  <td width="12%" class="bottomlinebold" align="right">创建时间</td>
                  <td width="10%" class="bottomlinebold" align="center">类型</td>				  

                  <td width="8%" class="bottomlinebold" align="center">管理</td>

                </tr>

     		 </thead> 

      		<tbody>

<?php



	if(!empty($list_data))

	{		
	

		foreach($list_data as $lsv)

		{
?>

                <tr id="line_<? echo $lsv['AgentID'];?>" class="bottomline"  onmouseover="inStyle(this)"  onmouseout="outStyle(this)"   >



                  <td ><? echo $lsv['AgentID'];?></td>

                  <td ><? echo $lsv['AgentName'];?></td>

				  <td >&nbsp;<? echo $lsv['AgentContact'];?></td>

                  <td ><? echo $lsv['AgentPhone'];?></td>

                  <td class="TitleNUM"><? echo $lsv['AgentBegin'].' - '.$lsv['AgentEnd'];?></td>

                  <td class="TitleNUM"><? 

				  echo date("Y-m-d",$lsv['CreateDate']);			  

				  ?></td>

                  <td align="center">[<?
				  if($lsv['AgentType']=='b') echo '<font color=red>包销</font>'; elseif($lsv['AgentType']=='q') echo '<font color=green>区域</font>'; else echo '普通';
					?>]</td>

                  <td align="center" >
					<a href="agent_add.php?ID=<? echo $lsv['AgentID'];?>" ><img src="img/icon_edit.gif" border="0" title="修改" class="img" /></a>&nbsp;&nbsp;

					<a href="javascript:void(0);" onclick="do_delete_agent('<? echo $lsv['AgentID'];?>');" ><img src="img/icon_delete.gif" border="0" class="img" title="删除" /></a>				  

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

       				 <td width="4%"  height="30" ></td>



   			       <td  class="sublink"></td>

       			     <td width="50%" align="right"><? echo $page->ShowLink('agent.php');?></td>

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
 	function ShowTreeMenuList($resultdata,$p_id) 
	{
		$frontMsg  = "";				
		foreach($resultdata as $key => $var)
		{
			if($var['AreaParent'] == $p_id)
			{
				if($var['AreaParent'] == "0")
				{
					$frontMsg  .= '<li><a href="agent.php?aid='.$var['AreaID'].'"  ><strong>'.$var['AreaName'].'</strong></a>';
				}	
				else
				{
					$frontMsg  .= '<li><a href="agent.php?aid='.$var['AreaID'].'"  >'.$var['AreaName'].'</a>';
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