<?php

$de = '\'rgrgretertret';
$de = "<a href='test'>Test</a>";
echo str_replace(array('"', "'"), array('', ''), $de);

echo '<br />';
echo $de;
echo '<br />';
echo str_replace('\\', '', $de);

exit;


//phpinfo();

      
function encrypt($str, $key){  
    $block = mcrypt_get_block_size('des', 'ecb');  
    $pad = $block - (strlen($str) % $block);  
    $str .= str_repeat(chr($pad), $pad);  
  
    return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);  
}  
  
function decrypt($str, $key){    
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);  
  
    $block = mcrypt_get_block_size('des', 'ecb');  
    $pad = ord($str[($len = strlen($str)) - 1]);  
    return substr($str, 0, strlen($str) - $pad);  
}  

///////////////////////////////////////////////////////////////////////////////////////////////////////////
class CryptDes {

     var $key;

     var $iv;

     function CryptDes($key, $iv){

        $this->key = $key;

        $this->iv = $iv;

     }

     

     function encrypt($input){

         $size = mcrypt_get_block_size(MCRYPT_DES,MCRYPT_MODE_CBC); //3DES加密将MCRYPT_DES改为MCRYPT_3DES

         $input = $this->pkcs5_pad($input, $size); //如果采用PaddingPKCS7，请更换成PaddingPKCS7方法。

         $key = str_pad($this->key,8,'0'); //3DES加密将8改为24

         $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, '');

         if( $this->iv == '' )

         {

             $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

         }

         else

         {

             $iv = $this->iv;

         }

         @mcrypt_generic_init($td, $key, $iv);

         $data = mcrypt_generic($td, $input);

         mcrypt_generic_deinit($td);

         mcrypt_module_close($td);

         $data = base64_encode($data);//如需转换二进制可改成  bin2hex 转换

         return $data;

     }

 

     function decrypt($encrypted){

         $encrypted = base64_decode($encrypted); //如需转换二进制可改成  bin2hex 转换

         $key = str_pad($this->key,8,'0'); //3DES加密将8改为24

         $td = mcrypt_module_open(MCRYPT_DES,'',MCRYPT_MODE_CBC,'');//3DES加密将MCRYPT_DES改为MCRYPT_3DES

          if( $this->iv == '' )

         {

             $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

         }

         else

         {

             $iv = $this->iv;

         }

         $ks = mcrypt_enc_get_key_size($td);

         @mcrypt_generic_init($td, $key, $iv);

         $decrypted = mdecrypt_generic($td, $encrypted);

         mcrypt_generic_deinit($td);

         mcrypt_module_close($td);

         $y=$this->pkcs5_unpad($decrypted);

         return $y;

     }

 

     function pkcs5_pad ($text, $blocksize) {

         $pad = $blocksize - (strlen($text) % $blocksize);

         return $text . str_repeat(chr($pad), $pad);

     }

 

     function pkcs5_unpad($text){

         $pad = ord($text{strlen($text)-1});

         if ($pad > strlen($text)) {

             return false;

         }

         if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){

             return false;

         }

         return substr($text, 0, -1 * $pad);

     }

 

     function PaddingPKCS7($data) {

         $block_size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);//3DES加密将MCRYPT_DES改为MCRYPT_3DES

         $padding_char = $block_size - (strlen($data) % $block_size);

         $data .= str_repeat(chr($padding_char),$padding_char);

         return $data;

     }

}

 





//消息体
$strOld = '<?xml version="1.0" encoding="UTF-8"?>
<BODY>
  <CORPNAME>测试集团有限公司</CORPNAME>
  <CORPACCOUNT>57812</CORPACCOUNT>
  <PRODUCTCODE>1061234501</PRODUCTCODE>
  <SUBSCRIBERID>2015918123</SUBSCRIBERID>
  <ACCESSNUMBER>20150918</ACCESSNUMBER>
  <PARAMLIST />
  <CORPINFOLIST>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_DESC</CORPINFOCODE>
      <CORPINFOVALUE>刘康</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_CMSTAFFNO</CORPINFOCODE>
      <CORPINFOVALUE>liukang</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_RANK</CORPINFOCODE>
      <CORPINFOVALUE>A</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_TYPE</CORPINFOCODE>
      <CORPINFOVALUE>0</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_LINKMOBILE</CORPINFOCODE>
      <CORPINFOVALUE>15671660031</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_ADDR</CORPINFOCODE>
      <CORPINFOVALUE>湖北武汉金银湖联通路1号</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_BUREAUID</CORPINFOCODE>
      <CORPINFOVALUE>HBCU</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_AREACODE</CORPINFOCODE>
      <CORPINFOVALUE>71</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_FAX</CORPINFOCODE>
      <CORPINFOVALUE>50735555</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_HEADPHONE</CORPINFOCODE>
      <CORPINFOVALUE>15671660031</CORPINFOVALUE>
    </CORPINFOMAP>
    <CORPINFOMAP>
      <CORPINFOCODE>CORP_LINKMAN</CORPINFOCODE>
      <CORPINFOVALUE>段建雄</CORPINFOVALUE>
    </CORPINFOMAP>
  </CORPINFOLIST>
  <LICENSE>99999</LICENSE>
  <OPTYPE>1</OPTYPE>
  <OPNOTE>http://unicom.dhb.net.cn/soap/wsdl/CorpBindReq.wsdl|502020600004838875</OPNOTE>
</BODY>';

//加密后
$strNew = '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';


//$des = decrypt($strNew, 'F3D78C2F9EFEE867');

//$des = new CryptDes("12345678qwe","12345678");//（秘钥向量，混淆向量）
$des = new CryptDes("12345678qwe","12345678");//（秘钥向量，混淆向量）

echo $ret = $des->encrypt($strOld);//加密字符串
echo '<br />===<br />';
echo $des->decrypt($ret);


debug($des);


      


?>