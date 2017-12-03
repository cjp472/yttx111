<?
/**
 * 基于ID生成唯一编码
 *
 * @author http://www.jb51.net/article/51705.htm
 * @remark dyhb 
 * @date 2015/05/06
 * @example 
 * <
 *    $code = new Code();
 *
 *    $num  = 3422234;
 *    $num2 = 56723238;
 *    
 *    var_dump($num);
 *    echo '<br/>';
 *    var_dump($num2);
 *    
 *    echo '<br/><br/><br/>';
 *    
 *    var_dump($num_ = $code->encodeID($num,5));
 *    echo '<br/>';
 *    var_dump($num2_ = $code->encodeID($num2,5));
 *    
 *    echo '<br/><br/><br/>';
 *    
 *    var_dump($code->decodeID($num_));
 *    echo '<br/>';
 *    var_dump($code->decodeID($num2_));
 * >
 * 
 */

if(!function_exists('bcmod')){
	/** 
	 * my_bcmod - get modulus (substitute for bcmod) 
	 * string my_bcmod ( string left_operand, int modulus ) 
	 * left_operand can be really big, but be carefull with modulus :( 
	 * by Andrius Baranauskas and Laurynas Butkus :) Vilnius, Lithuania 
	 **/ 
	function bcmod( $x, $y ) 
	{ 
	    // how many numbers to take at once? carefull not to exceed (int) 
	    $take = 5;      
	    $mod = ''; 

	    do 
	    { 
	        $a = (int)$mod.substr( $x, 0, $take ); 
	        $x = substr( $x, $take ); 
	        $mod = $a % $y;     
	    }  
	    while ( strlen($x) ); 

	    return (int)$mod; 
	}
}

if(!function_exists('bcdiv')){ 
	function bcdiv( $first, $second, $scale = 0 )
	{
	    $res = $first / $second;
	    
		$end = substr($res,strpos($res,'.')+1,$scale);

		if(strlen($end)<$scale) { 

			$end .= str_repeat('0',$scale-strlen($end));
		}

		$res = substr($res,0,strpos($res,'.')).'.'.$end;
		$res = rtrim($res,'.');
	    return $res;
	}
}

class Code { 
    //密码字典 
    private $dic = array( 
        0=>'0',    1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8',     
        9=>'9', 10=>'A',  11=>'B', 12=>'C', 13=>'D', 14=>'E', 15=>'F',  16=>'G',  17=>'H',     
        18=>'I',19=>'J',  20=>'K', 21=>'L',  22=>'M',  23=>'N', 24=>'O', 25=>'P', 26=>'Q',     
    27=>'R',28=>'S',  29=>'T',  30=>'U', 31=>'V',  32=>'W',  33=>'X', 34=>'Y', 35=>'Z' 
    ); 
 
 
    public function encodeID($int, $format=8) { 
        $dics = $this->dic; 
        $dnum = 36; //进制数 
        $arr = array (); 
        $loop = true; 
        while ($loop) { 
            $arr[] = $dics[bcmod($int, $dnum)]; 
            $int = bcdiv($int, $dnum, 0); 
            if ($int == '0') { 
                $loop = false; 
            } 
        } 
        if (count($arr) < $format) 
            $arr = array_pad($arr, $format, $dics[0]); 
 
        return implode('', array_reverse($arr)); 
    } 
 
    public function decodeID($ids) { 
        $dics = $this->dic; 
        $dnum = 36; //进制数 
        //键值交换 
        $dedic = array_flip($dics); 
        //去零 
        $id = ltrim($ids, $dics[0]); 
        //反转 
        $id = strrev($id); 
        $v = 0; 
        for ($i = 0, $j = strlen($id); $i < $j; $i++) { 
            $v = bcadd(bcmul($dedic[$id { 
                $i } 
            ], bcpow($dnum, $i, 0), 0), $v, 0); 
        } 
        return $v; 
    } 
 
}
?>