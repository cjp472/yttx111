<?php 

//++++++++++++++++++++++++++++++++++++++
//      易极付异步通知页面
//
//++++++++++++++++++++++++++++++++++++++++

include_once ('common.php');
include_once ("global.config.php");

$in = array_merge($_GET, $_POST);

if(empty($in['orderNo'])){
    exit;
}
$db     = dbconnect::dataconnect()->getdb();
$log    = KLogger::instance(LOG_PATH, KLogger::INFO);
$kLog = KLogger::instance(YOPENAPI_LOG_PATH);
$kLog->setDateFormat('Y-m-d G:i:s.u P');//年月日时分秒毫秒 时区

if(($in['resultCode'] == 'PAY_SUCCESS')||($in['resultCode'] == 'EXECUTE_PROCESSING')||($in['resultCode'] == 'EXECUTE_SUCCESS')){
    $logType = 'logInfo';
}else{
    $logType = 'logError';
}
$logMsg     = "【异步通知：".$in['resultMessage']."】【Service：".$in['service']."】：".http_build_query($in);

//获取映射关系
$YOpenApiSet    = new YOpenApiSet();
$dhbOrder       = $YOpenApiSet->getMap($in['orderNo']);

//备份异步通知数据，供系统背景通知使用
$backendSyn = $in;

//准备验签
$sign   = $in['sign'];
unset($in['sign']);

if(is_bool($in['success'])){
    if($in['success']){
        $in['success'] = 'true';
    }else{
        $in['success'] = 'false';
    }
}

if(is_bool($in['executeStatus'])){
    if($in['executeStatus']){
        $in['executeStatus'] = 'true';
    }else{
        $in['executeStatus'] = 'false';
    }
}

//初始化
$YOpenApiDo = new YOpenApiDo($backendSyn, $dhbOrder['CompanyID']);
$kLog->logInfo($logMsg);
//成功状态[这里可优化]  此处状态需要优化，需要把支付失败考虑进来，支付失败需要生成付款单
if(($in['resultCode'] == 'PAY_SUCCESS')||($in['resultCode'] == 'EXECUTE_PROCESSING')||($in['resultCode'] == 'EXECUTE_SUCCESS')||(isset($in['tradeStatus']))){

    //本地验签字符
    ksort($in);
    $YOpenApiDo->setGetway()->sign($in);
    
    //验签
    if($sign == $YOpenApiDo->commonPost['sign']){
        //自动执行
        $service = $in['service'];  
        if(empty($service)){
            $service = $in['tradeAction'];  
        }
        $YOpenApiDo->$service($in);
        echo 'success';
    }else{
        //同步跳转
        $status = array(
                'service'       => $in['service'],
                'verify_Result' => '验签失败',
                'dhbPayOrder'   => trim($dhbOrder['DHBOrderNO'], ","),
                'showInfo'      => $in['resultMessage'] ? $in['resultMessage'] : '验签失败',
                'pay_Result'    => $in['resultMessage'] ? $in['resultMessage'] : '验签失败',
                'merchantId'    => $in['partnerId'] ? $in['partnerId'] : '-',
                'orderNo'       => $in['tradeNo'] ? $in['tradeNo'] : $in['orderNo'],
                'orderDatetime' => $in['notifyTime'] ? $in['notifyTime'] : date('Y-m-d H:i:s'),
                'payAmount'     => $in['service'] == 'commonTradePay' ? $in['payAmount'] / 100 : $in['amount'],
                'amountIn'      => $in['amountIn'],
                'payDatetime'   => date('Y-m-d H:i:s')
        );
    
        $kLog->logError('【验签失败】'.$logMsg);
        $YOpenApiDo->goToUrl($status);
    }
    
}else{//失败状态
    $kLog->logError('resultCode 非法！');
    
    //本地验签字符
    ksort($in);
    $YOpenApiDo->setGetway()->sign($in);
    
    //验签
    if($sign == $YOpenApiDo->commonPost['sign']){
        $service = $in['service'];  
        $YOpenApiDo->$service($in);
        //同步跳转
        $status = array(
                'service'       => $in['service'],
                'verify_Result' => '本次操作失败',
                'dhbPayOrder'   => trim($dhbOrder['DHBOrderNO'], ","),
                'showInfo'      => $in['resultMessage'] ? $in['resultMessage'] : '本次操作失败',
                'pay_Result'    => $in['resultMessage'] ? $in['resultMessage'] : '本次操作失败',
                'merchantId'    => $in['partnerId'] ? $in['partnerId'] : '-',
                'orderNo'       => $in['tradeNo'] ? $in['tradeNo'] : $in['orderNo'],
                'orderDatetime' => $in['notifyTime'] ? $in['notifyTime'] : date('Y-m-d H:i:s'),
                'payAmount'     => $in['service'] == 'commonTradePay' ? $in['payAmount'] / 100 : $in['amount'],
                'amountIn'      => $in['amountIn'],
                'payDatetime'   => date('Y-m-d H:i:s')
        );
    
        $YOpenApiDo->goToUrl($status);
        echo 'success';
    }

}

?>