<?php
//该供应商是否开通易极付
$NetGetWay = new NetGetWay();
$netInfo = $NetGetWay->showGetway('yijifu', $_SESSION['ucc']['CompanyID'], '', true);

//当前经销商在易极付是否已开户
$myYJF = array();
$YOpenApiSet = new YOpenApiSet();
$myYJF = $YOpenApiSet->getSignInfo(intval($_SESSION['cc']['cid']));

//当前经销商已绑定的银行卡数量
$signbank = new Signbank();
$signNum= $signbank->getSignBankNum(intval($_SESSION['ucc']['CompanyID']), intval($_SESSION['cc']['cid']));


//当前药店是否开通账期

$cid = $_SESSION['cc']['cid'];
$CompanyID = $_SESSION['ucc']['CompanyID'];
$PwdSetSel = $signbank->PwdSetSel($cid,$CompanyID);

//当前经销商是否开通账期

$companyCredit = $signbank->CompanyCredit($CompanyID);

?>