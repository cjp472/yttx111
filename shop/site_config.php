<?
/**
 * 数据库连接等基础配置信息
 *
 * < 如果当前目录没有site_config.php 文件，请复制一份本文件，重命名为site_config.php. >
 *
 * @date 2015/08/05
 */

$Site_Config = array (
  "sitename"	=> "医统天下 BMB订货系统",
  "siteurl"		=> "YITONG111.COM",
  "e_mail"		=> "wangdan@yitong111.com",
  "resourcepath"=> "/resource/",
  "resourceurl" => "http://res.yitong111.com/",  
  
  "upfile"		=>
  array (
    "img"		=> "gif|jpg|png|bmp",
    "flash"		=> "swf",
    "annex"		=> "fla|psd|cdr|rar|zip|rm|rmvb|avi|mp3|asf|txt|chm|pdf|pdg|doc|ppt|xls|mdb|docx|xlsx|jpg|gif|png|bmp",
    "units"		=> "个|本|根|支|双|盒|把|箱|包|提|件|付|组|对|条|辆|部|台|张|份|米|尺|卷|瓶|克|束|公斤",
  ),
 
  "database"	=>
  array (
	"host"		=> "172.19.224.205:3306",
    "dataname"	=> "etong_db_live",
    "username"  => "ftp-etong",
    "password"  => "yttx123456",
    "datatable" => "rsung",
    )
);
