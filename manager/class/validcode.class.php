<?php
class validcode
{
/****** ����Session ID ******/
/*
�������ƣ�create_sess_id()
�������ã������Ը�����ĻỰID
��    ����$len: ��Ҫ�Ự�ַ����ĳ��ȣ�Ĭ��Ϊ32λ����Ҫ����16λ
�� �� ֵ�����ػỰID
*/
function create_sess_id($len=32)
{
// У���ύ�ĳ����Ƿ�Ϸ�
 if( !is_numeric($len) || ($len>32) || ($len<16)) { return; }
// ��ȡ��ǰʱ���΢��
 list($u, $s) = explode(' ', microtime());
 $time = (float)$u + (float)$s;
// ����һ�������
 $rand_num = rand(100000000, 999999999);
 $rand_num = rand($rand_num, $time);
 mt_srand($rand_num);
 $rand_num = mt_rand();
// ����SessionID
 $sess_id = md5( md5($time). md5($rand_num) );
// ��ȡָ����Ҫ���ȵ�SessionID
 $sess_id = substr($sess_id, 0, $len);
 return $rand_num;
}


/****** ����У���� ******/

/*
�������ƣ�create_check_code()
�������ã������Ը������У����
��    ����$len: ��ҪУ����ĳ���, �벻Ҫ����16λ,ȱʡΪ4λ
�� �� ֵ������ָ�����ȵ�У����
*/
function create_check_code($len=4)
{
 if ( !is_numeric($len) || ($len>6) || ($len<1)) { return; }

 $check_code = substr($this->create_sess_id(), 0, $len );
 return strtoupper($check_code);
}


/******  ����У�����ͼƬ ******/


/*
�������ƣ�create_check_image()
�������ã�����һ��У�����ͼƬ
��    ����$check_code: У�����ַ�����һ����create_check_code()���������
�� �� ֵ�����ظ�ͼƬ
*/
function create_check_image( $check_code )
{
 // ����һ��ͼƬ
 $im = imagecreate(60,20);
 $black = ImageColorAllocate($im, 4,66,93);  // ������ɫ
 $white = ImageColorAllocate($im, 255,255,255);  // ǰ����ɫ
 $gray = ImageColorAllocate($im, 200,200,200);
 imagefill($im,68,30,$gray);

 // ����λ������֤�����ͼƬ
 imagestring($im, 5, 8, 3, $check_code, $white);
 // �����������
 for($i=0;$i<200;$i++)
 {
     $randcolor = ImageColorallocate($im,rand(0,255),rand(0,255),rand(0,255));
     imagesetpixel($im, rand()%70 , rand()%30 , $randcolor);
 }
 // ���ͼ��
 Header("Content-type: image/PNG");
 ImagePNG($im);
 ImageDestroy($im);
}

}

?>