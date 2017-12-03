<?php
    include_once ("../common.php");
    include_once ("auth_code.class.php");    

    $authCode = new AuthCode();
    $row = $authCode->get_auth_code_by_session(session_id());

    define('TIME', 30);

    $new_auth_code = $authCode->get_random_code();
    if(count($row) > 0){
        //2. 从DB中更新$old_auth_code
        $authCode->update_auth_code($row['code'], $new_auth_code);
    }else{
        //2. 从DB中插入$new_auth_code
        $authCode->insert_auth_code($new_auth_code, session_id());
    }

    $output = array(
                    'time' => TIME,
                    'auth_code' => $new_auth_code
                );

    echo json_encode($output);
    exit;
?>