<?php
    include_once ("../common.php");
    include_once ("auth_code.class.php");

    $authCode = new AuthCode();

    $code = $_POST['code'];
    $row = $authCode->select_auth_code($code);
    if(count($row) > 0){
        $time = $row['create_at'];

        $authCode->delete_auth_code($row['id']);
        //是否超时30分钟
        if(date('Y-m-d H:i:s', strtotime('+30 minute', strtotime($time))) < date('Y-m-d H:i:s', time())){
            echo json_encode(array('is_ok' => 0, 'err_msg' => '验证码过期'));
        }else{
            //设置验证成功24小时内不用登录
            $_SESSION['is_auth'] = 1;

            $authCode->record_in_sms(RealIp(), $row['code']);
            echo json_encode(array('is_ok' => 1));
        }
       
    }else{
        //登录失败
        echo  json_encode(array('is_ok' => 0, 'err_msg' => '验证失败'));
    }
    exit;
?>