<?
/**
 * Class dbconnect
 * 
 */
class dbconnect
{
	private static $instance;
	private $db;
	private function __construct(){

// 		if(!empty($_SESSION['ucc']['CompanyDatabase'])) $datasplitname = DB_DATABASE."_".$_SESSION['ucc']['CompanyDatabase']; else $datasplitname = DB_DATABASE;
		// 在线版，改定制，注释上面  在线版数据库配置
		$datasplitname = DB_DATABASE;
		
		$this->db = new ezSQL_mysql(DB_USER,DB_PASSWORD,$datasplitname,DB_HOST);
		$this->db->query("set names 'utf8'");
	}

	public function dataconnect()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new dbconnect() ;
		}
		return self::$instance ;
	}

	public function getdb()
	{
		return $this->db;
	}
}

/***

class dbuserconnect
{
	private static $instance;
	private $db;
	private function __construct(){
		$this->db = new ezSQL_mysql(DB_USER,DB_PASSWORD,DB_DATABASE."_user",DB_HOST);
		$this->db->query("set names 'utf8'");
	}

	public function dataconnect()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new dbuserconnect() ;
		}
		return self::$instance ;
	}

	public function getdb()
	{
		return $this->db;
	}
}
****/
?>