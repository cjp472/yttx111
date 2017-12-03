<?php 
$menu_flag = "infomation";
$pope	   = "pope_view";
include_once ("header.php"); 

if(!intval($in['ID']))
{
	exit('非法操作!');
}else{	 
	$info = $db->get_row("SELECT * FROM ".DATATABLE."_order_article where ArticleCompany=".$_SESSION['uinfo']['ucompany']." and ArticleID=".intval($in['ID'])." limit 0,1");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo SITE_NAME;?> - 管理平台</title>
<link href="css/main.css?v=<? echo VERID;?>" rel="stylesheet" type="text/css" />
</head>

<body>
  <?php include_once ("top.php");?>
    <div id="bodycontent">
    	<div class="lineblank"></div>
         <form id="MainForm" name="MainForm" enctype="multipart/form-data" method="post" target="exe_iframe"  action="">
			<input type="hidden" name="set_filename" id="set_filename" value="" />
			<input type="hidden" name="update_id" id="update_id" value="<? echo $info['ArticleID'];?>" />
		   <div id="searchline">
        	<div class="leftdiv width300">
        	 <div class="locationl"><strong>当前位置：</strong><a href="#">信息管理</a>  &#8250;&#8250; <a href="#">查看详细信息</a> </div>
   	        </div>            
			
        </div>
    	
        <div class="line2"></div>
        <div class="bline" >        

			<fieldset  class="fieldsetstyle">
		<legend>基本资料</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle">
                 <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">所属栏目：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                    <? 
					if(empty($info['ArticleSort']))
					{
						echo "公告信息";
					}else{
						$sinfo = $db->get_row("SELECT SortID,SortName FROM ".DATATABLE."_order_sort where SortCompany=".$_SESSION['uinfo']['ucompany']." and SortID=".$info['ArticleSort']." order by SortOrder DESC,SortID ASC limit 0,1");	
						echo $sinfo['SortName'];
					}
					?>
                  <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF" class="red">&nbsp;</td>
                </tr>               
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">标题：</div></td>
                  <td width="55%" bgcolor="#FFFFFF"><label>
                   <? echo $info['ArticleTitle'];?>
                    <span class="red">*</span></label></td>
                  <td width="29%" bgcolor="#FFFFFF">&nbsp;</td>
                </tr>
                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">作者：</div></td>
                  <td bgcolor="#FFFFFF"><? echo $info['ArticleAuthor'];?></td>
                  <td bgcolor="#FFFFFF">&nbsp;</td>
                </tr>

                <tr>
                  <td bgcolor="#F0F0F0"><div align="right">文件：</div></td>
                  <td bgcolor="#FFFFFF">
				  <?				  
				  if(!empty($info['ArticlePicture']))
				  {
					  $sExtension = substr( $info['ArticlePicture'], ( strrpos($info['ArticlePicture'], '.') + 1 ) );
					  if($sExtension=="jpg" || $sExtension=="png" || $sExtension=="gif")
					  {
						 echo '<div style="width:700px; height:auto; overflow:hidden;"><a href="'.RESOURCE_URL.$info['ArticlePicture'].'" target="_blank"><img src="'.RESOURCE_URL.$info['ArticlePicture'].'" alt="'.$info['ArticleFileName'].'" border="0" onload="javascript:if(this.width>600) this.style.width=600;" /></a></div>';
					  }else{
						 echo '<a href="'.RESOURCE_URL.$info['ArticlePicture'].'" target="_blank">文件下载↓ 【'.$info['ArticleFileName'].'】</a>';
					  }
				  }
				  ?>
				  </td>
                  <td bgcolor="#FFFFFF" id="data_ArticlePicture_text" >&nbsp;</td>
                </tr>
              </table>
	</fieldset>
    
	<fieldset class="fieldsetstyle">
		<legend>正文内容</legend>
			<?php

				echo $stringContent = html_entity_decode($info['ArticleContent'], ENT_QUOTES,'UTF-8');

			?>
            </fieldset>            
            
			<fieldset class="fieldsetstyle">
			<legend>设置</legend>
              <table width="98%" border="0" cellpadding="4" cellspacing="1" bgcolor="#FFFFFF" class="inputstyle" >                            
                <tr>
                  <td width="16%" bgcolor="#F0F0F0"><div align="right">排序权重：</div></td>
                  <td width="55%"><label>
                    <? echo $info['ArticleOrder'];?>
                 </label></td>
                  <td width="29%"> (排序依据，数字大的靠前)</td>
                </tr>
              </table>
           </fieldset>    
			<br style="clear:both;" />
            <div class="rightdiv sublink" style="padding-right:20px;"><ul><li><a href="javascript:void(0);" onclick="window.close(true);">关 闭 </a></li></ul></div>
        </div>
              
			  <INPUT TYPE="hidden" name="referer" value ="" >
              </form>
        <br style="clear:both;" />

    </div>
    
<? include_once ("bottom_content.php");?>
</body>
</html>