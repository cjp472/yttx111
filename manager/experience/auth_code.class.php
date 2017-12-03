<?php
    include_once ("../common.php");

    class AuthCode
    {
        public $table;
        public $sms_table;
        public $db;
        public $host;

        function AuthCode(){
            $this->table = DATABASEU . DATATABLE . '_auth_code';
            
            $this->db = dbconnect::dataconnect()->getdb();
            $this->host = 'http://m.dhb.hk/weixin/record_auth_code.php';
        }

        function get_random_code()
        {
            for($i=0; $i<6; $i++){
                if($i == 0){
                    $rand_arr[$i] = rand(1, 9);
                }else{
                    $rand_arr[$i] = rand(0, 9);
                }
            }

            return implode('', $rand_arr);   
        }


        function update_auth_code($old_auth_code, $new_auth_code){
            $this->db->query("UPDATE " . $this->table . " SET code='" . $new_auth_code . "' , create_at='" . date('Y-m-d H:i:s') . "' WHERE code='" . $old_auth_code . "'");
        }

        function insert_auth_code($auth_code, $session_id){
            $this->db->query("INSERT INTO " . $this->table . "(code, create_at, session_id) VALUES('" . $auth_code . "', '" . date('Y-m-d H:i:s') . "', '" . $session_id . "')");
        }

        function select_auth_code($auth_code){
            return $this->db->get_row("SELECT id, create_at, code, session_id FROM " . $this->table . " WHERE code=" . mysql_escape_string($auth_code));
        }

        function delete_auth_code($id){
            $this->db->query("DELETE FROM " . $this->table . " WHERE id=" . $id);
        }

        function get_auth_code_by_session($session_id){
            return $this->db->get_row("SELECT code FROM " . $this->table . " WHERE session_id='" . $session_id . "'");
        }
        
        function record_in_sms($ip, $auth_code){
            return file_get_contents($this->host . '?ip=' . $ip . '&auth_code=' . $auth_code);
        }
    }
?>