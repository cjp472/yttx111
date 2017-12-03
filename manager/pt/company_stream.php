<?php 
$menu_flag = "company_order";
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
<script src="js/manager.js?v=<? echo VERID;?>" type="text/javascript"></script>
    <script type="text/javascript">
        $(function(){
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
        	  <form id="FormSearch" name="FormSearch" method="post" action="company_stream.php">
			  
        	    <label>
        	      &nbsp;&nbsp;订单号，公司： <input type="text" name="kw" value="<? echo $in['kw']; ?>" id="kw" class="inputline" />
       	        </label>
                  <label>
                      <select name="status"  style="width:105px;" class="select2">
                          <option value="A">全部</option>
                          <option value="T" <?php if($in['status'] == 'T') echo 'selected="selected"';?> >已到账</option>
                          <option value="F" <?php if($in['status'] == 'F') echo 'selected="selected"';?> >未到账</option>
                      </select>
                  </label>
                  <label>
                      <select id="date_field" name="date_field"  style="width:105px;" class="select2">
                          <option value="record_date" <?php if($in['date_field'] == 'record_date') echo 'selected="selected"';?> >记录时间</option>
                          <option value="to_date" <?php if($in['date_field'] == 'to_date') echo 'selected="selected"';?> >到账时间</option>
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
            
			<div class="location"><strong>当前位置：</strong><a href="company_stream.php">支付信息</a> </div>
        </div>
    	
        <div class="line2"></div>
        <div class="bline">

<?php
	$sqlmsg = '';
	if(!empty($in['kw']))  {
        $kw_field = explode("," , "c.CompanyName,o.order_no,c.CompanyPrefix,c.CompanySigned");
        $linkArr = array();
        foreach($kw_field as $fd) {
            $linkArr[] = " {$fd} like '%{$in['kw']}%' ";
        }
        $sqlmsg .= " AND (".implode("OR" , $linkArr).")";
        //$sqlmsg .= " and (c.CompanyName like '%".$in['kw']."%' or o.order_no like '%".$in['kw']."%' ) ";
    }

    if(!empty($in['status']) && $in['status'] != 'A' ) {
        $sqlmsg .= " AND o.status='{$in['status']}'";
    }

    $tField = $in['date_field'] == 'record_date' ? "time" : "to_time";

    if(!empty($in['bdate'])) {
        $sqlmsg .= " AND {$tField} >= " . strtotime($in['bdate'] . ' 00:00:00');
    }
    if(!empty($in['edate'])) {
        $sqlmsg .= " AND {$tField} <= " . strtotime($in['edate'] . ' 23:59:59');
    }

	$InfoDataNum = $db->get_row("SELECT count(*) AS allrow FROM ".DATABASEU.DATATABLE."_buy_stream o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1 ".$sqlmsg." ");
	$page = new ShowPage;
    $page->PageSize = 50;
    $page->Total = $InfoDataNum['allrow'];
    $page->LinkAry = array("kw"=>$in['kw'],'status' => $in['status'], 'bdate' => $in['bdate'],'edate' => $in['edate'],'date_field' => $in['date_field']);
	
	$datasql   = "SELECT o.*,c.CompanyName FROM ".DATABASEU.DATATABLE."_buy_stream o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1  ".$sqlmsg." ORDER BY o.id DESC";
	$list_data = $db->get_results($datasql." ".$page->OffSet());
	
	//统计支付方式及数量 lxc 2015-12-31
    $calc_data = $db->get_results("SELECT o.pay_away,COUNT(*) CNT FROM ".DATABASEU.DATATABLE."_buy_stream o inner join ".DATABASEU.DATATABLE."_order_company c ON o.company_id=c.CompanyID where 1=1  ".$sqlmsg." GROUP BY o.pay_away");
    $calc_data = array_column($calc_data ? $calc_data : array(),'CNT','pay_away');
    $lineCNT = intval($calc_data['line']);
    $onlineCNT = intval($calc_data['alipay']) + intval($calc_data['allinpay']);
?>
          <form id="MainForm" name="MainForm" method="post" action="" target="exe_iframe" >
             <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td class="bottomlinebold">
                         <label><strong>总计</strong></label>
                         <label><strong> <font color="red"><?php echo array_sum($calc_data); ?></font> 笔订单，</strong></label>
                         <label>其中在线支付&nbsp;<font color="red"><?php echo $onlineCNT; ?></font>&nbsp;笔，&nbsp;</label>
                         <label>线下付款&nbsp;<font color="red"><?php echo $lineCNT; ?></font>&nbsp;笔&nbsp;</label>
       				 </td>
					 <td align="right"  height="30" class="bottomlinebold">
                         <? echo $page->ShowLink('open.php');?>
					 </td>
     			 </tr>
              </table>
              
        	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
               <thead>
                <tr>
                  <td width="3%" class="bottomlinebold">行号</td>
                  <td width="14%" class="bottomlinebold">订单号/支付流水号</td>
                  <td class="bottomlinebold">公司</td>
				  <td width="7%" class="bottomlinebold">支付方式</td>
				  <td width="10%" class="bottomlinebold">转入账号</td>
				  <td align="right" width="8%" class="bottomlinebold">金额(元)</td>
				  <td align="center" width="10%" class="bottomlinebold">支付/到帐时间</td>
                    <td align="center" width="10%" class="bottomlinebold">备注</td>
				  <td width="7%" class="bottomlinebold">操作人</td>
                    <td width="7%" class="bottomlinebold">操作</td>
                </tr>
     		 </thead>      		
      		<tbody>
<?
$n = 1;
if(!empty($list_data))
{

     if(!empty($in['page'])) $n = ($in['page']-1)*$page->PageSize+1;
	 foreach($list_data as $lsv)
	 {

?>
               <tr id="line_<? echo $lsv['id'];?>" class="bottomline" onmouseover="inStyle(this)"  onmouseout="outStyle(this)"  >
                  <td ><? echo $n++;?></td>
                  <td ><? echo $lsv['order_no'].'<br />'.$lsv['stream_no'];?>
                      <?php if(!empty($lsv['trade_no'])) echo $lsv['trade_no'];?>
                  </td>
                  <td ><? echo $lsv['CompanyName'];?></td>
                  <td ><? echo $pay_arr[$lsv['pay_away']];?></td>
                  <td >
                      <? echo $lsv['pay_away'] != 'line' ? '-' : $lsv['trade_no'];?>
                  </td>
                  <td align="right"><? echo '￥'.$lsv['amount'];?></td>
                  <td align="center"><? echo date("y-m-d H:i",$lsv['time']).'<br />';
                  echo $lsv['to_time'] ? date("y-m-d H:i",$lsv['to_time']) : '--';?></td>
                   <td><?php echo $lsv['remark']; ?></td>
                  <td ><? echo $lsv['username'];?></td>
                    <td>
                        <?php if($lsv['status'] == 'F') { ?>
                        <a href="javascript:;" data-ajax-href="do_company.php?m=sure_stream&id=<?php echo $lsv['id']; ?>" data-ajax-confirm="您确定到账了吗?" data-ajax-succ="确认到账成功!" title="确认到账">到账</a>
                            -
                        <a href="javascript:;" data-ajax-href="do_company.php?m=del_stream&id=<?php echo $lsv['id']; ?>" data-ajax-confirm="确认删除该付款信息吗?" data-ajax-succ="付款信息删除成功" title="删除付款">删除</a>
                        <?php } else { ?>
                        --
                        <?php } ?>
                    </td>
                </tr>
<? } }else{?>
     			 <tr>
       				 <td colspan="10" height="30" align="center">暂无符合此条件的内容!</td>
       			 </tr>
<? }?>
 				</tbody>                
              </table>
              <table width="100%" border="0" cellspacing="0" cellpadding="0">
     			 <tr>
       				 <td width="4%"  height="30" ></td>
   			         
       			     <td  align="right"><? echo $page->ShowLink('company_stream.php');?></td>
     			 </tr>
              </table>
              <INPUT TYPE="hidden" name="referer" value ="" >
              </form>

        </div>
        <br style="clear:both;" />
    </div>
    
<? include_once ("bottom.php");?>
 
<iframe name="exe_iframe" style="width:0; height:0;" frameborder="0" scrolling="no"></iframe>
    <script type="text/javascript">
        $(function(){
            $("body").on('click','.blockOverlay',function(){
                $.unblockUI();
            });

            $("a[data-ajax-href]").on('click',function(){
                var _this = $(this);
                var href = _this.attr('data-ajax-href');
                var confirmMsg = _this.attr('data-ajax-confirm');
                var succMsg = _this.attr('data-ajax-succ');
                if(!confirm(confirmMsg)) {
                    return false;
                }
                $.post(href,function(data){
                    data = Jtrim(data);
                    if(data == 'ok') {
                        $.blockUI({
                            message : '<p>'+succMsg+'</p>'
                        });
                        setTimeout(function(){
                            $.unblockUI();
                            location.reload();
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>'+data+'</p>'
                        });
                    }
                },'text');
            });

            $("a[data-del-href]").on('click',function(){
                var href = $(this).attr('data-del-href');
                if(!confirm('确认删除该付款信息吗?')) {
                    return false;
                }
                $.post(href,function(data){
                    data = Jtrim(data);
                    if(data == 'ok') {
                        $.blockUI({
                            message : '<p>付款信息删除成功!</p>'
                        });
                        setTimeout(function(){
                            $.unblockUI();
                            window.location.reload();
                        },710);
                    } else {
                        $.blockUI({
                            message : '<p>'+data+'</p>'
                        });
                    }
                },'text');
                console.log(href);
            });
        });
    </script>
</body>
</html>