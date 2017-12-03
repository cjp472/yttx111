<?
/**
 * Class 图表
 *
 * @author seekfor seekfor@gmail.com
 * @version CMSfor Website Pro 1.2 Tue Oct 10 18:07:59 CST 2006 
 */
class XmlData
{	
	function XmlData()
	{
		global $db;
		$this->db   = & $db;
	}
	
	function MakeXml($key,$dataarr)
	{
		switch($key)
		{
			
			case "3dpie_home":
	if(is_array($dataarr))
	{
		$n=1;
		foreach($dataarr as $dvar)
		{
			$msg1 .='<string >'.$n++.'</string>
			';
			$msg2 .='<number>'.$dvar.'</number>
			';
		}
	}

	$results = '<chart>
	<axis_category shadow="low" size="12" color="000000" alpha="50" orientation="horizontal" font="黑体" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="0" />
	<chart_border top_thickness="0" bottom_thickness="0" left_thickness="0" right_thickness="0" />
	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Number</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">订单统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="105" y="50" width="580" height="180" positive_alpha="0" />
	<chart_type>3d pie</chart_type>
</chart>';
				break;

			case "linebetween":

			$msg1 = '';
			$msg2 = '';

	if(is_array($dataarr))
	{
		foreach($dataarr as $dvar)
		{
			$dkey = substr($dvar['ODate'],4,2)."-".substr($dvar['ODate'],6,2);
			//$dvararr[$dkey] = $dvar;
			$msg1 .='<string>'.$dkey.'</string>
			';
			$msg2 .='<number>'.$dvar['OTotal'].'</number>
			';
		}
	}

	$results = '<chart>
	<axis_category shadow="low" size="12" alpha="80" orientation="horizontal" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="90" size="12" />
	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Total</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">订单统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="65" y="50" width="650" height="180" positive_alpha="0" />
	<series_color>
		<color>768bb3</color>
	</series_color>
</chart>';
				break;


			case "linebetween_return":

			$msg1 = '';
			$msg2 = '';

	if(is_array($dataarr))
	{
		foreach($dataarr as $dvar)
		{

			$dkey = substr($dvar['ODate'],4,2)."-".substr($dvar['ODate'],6,2);
			//$dvararr[$dkey] = $dvar;
			$msg1 .='<string>'.$dkey.'</string>
			';
			$msg2 .='<number>'.$dvar['OTotal'].'</number>
			';
		}

	}

	$results = '<chart>
	<axis_category shadow="low" size="12" alpha="80" orientation="horizontal" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="90" size="12" />

	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Total</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">退货单统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="65" y="50" width="650" height="180" positive_alpha="0" />
	<series_color>
		<color>768bb3</color>
	</series_color>
</chart>';
				break;


			case "lined":

			$msg1 = '';
			$msg2 = '';

	if(is_array($dataarr))
	{
		foreach($dataarr as $dvar)
		{
			$msg1 .='<string>'.$dvar['OrderID'].'</string>
			';
			$msg2 .='<number>'.$dvar['OrderTotal'].'</number>
			';
		}

	}

	$results = '<chart>
	<axis_category shadow="low" size="12" alpha="80" orientation="horizontal" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="90" size="12" />
	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Total</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">日订单统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="65" y="50" width="650" height="180" positive_alpha="0" />
	<series_color>
		<color>768bb3</color>
	</series_color>
</chart>';
				break;


			case "linem":

			$msg1 = '';
			$msg2 = '';
			$msg3 = '';
	if(is_array($dataarr))
	{
		foreach($dataarr as $dvar)
		{
			$dkey = intval(substr($dvar['ODate'],6,2));
			$dvararr[$dkey] = $dvar;
		}
		for($i=1;$i<32;$i++)
		{
			if(empty($dvararr[$i])) $darr[$i] = null; else $darr[$i] = $dvararr[$i];
			$msg1 .='<string>'.$i.'</string>
			';
			if(empty($dvararr[$i]['OTotal'])) $total = 0; else $total = $dvararr[$i]['OTotal'];
			$msg2 .='<number>'.$total.'</number>
			';
		}
	}

	$results = '<chart>
	<axis_category shadow="low" size="12" alpha="80" orientation="horizontal" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="90" size="12" />
	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Total</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">月订单统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="60" y="50" width="620" height="180" positive_alpha="0" />
	<series_color>
		<color>768bb3</color>
	</series_color>
</chart>';
				break;


			case "liney":

			$msg1 = '';
			$msg2 = '';
			$msg3 = '';
	if(is_array($dataarr))
	{
		foreach($dataarr as $dvar)
		{
			$dkey = intval(substr($dvar['ODate'],4,2));
			$dvararr[$dkey] = $dvar;
		}
		for($i=1;$i<13;$i++)
		{
			if(empty($dvararr[$i])) $darr[$i] = null; else $darr[$i] = $dvararr[$i];
			$msg1 .='<string>'.$i.'</string>
			';
			if(empty($dvararr[$i]['OTotal'])) $total = 0; else $total = $dvararr[$i]['OTotal'];
			$msg2 .='<number>'.$total.'</number>
			';
		}
	}

	$results = '<chart>
	<axis_category shadow="low" size="12" alpha="80" orientation="horizontal" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="90" size="12" />
	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Total</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">年订单统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="65" y="50" width="650" height="180" positive_alpha="0" />
	<series_color>
		<color>768bb3</color>
	</series_color>
</chart>';
				break;


			case "3dpie":
	if(is_array($dataarr))
	{
		$n=1;
		foreach($dataarr as $dvar)
		{
			$msg1 .='<string >'.$n++.'</string>
			';
			$msg2 .='<number>'.$dvar['cnum'].'</number>
			';
		}
	}

	$results = '<chart>
	<axis_category shadow="low" size="12" color="000000" alpha="50" orientation="horizontal" font="黑体" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="0" />
	<chart_border top_thickness="0" bottom_thickness="0" left_thickness="0" right_thickness="0" />
	<chart_data>
		<row>
			<null/>
			'.$msg1.'</row>
		<row>
			<string>Number</string>
			'.$msg2.'</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">商品属性统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="105" y="50" width="580" height="180" positive_alpha="0" />
	<chart_type>3d pie</chart_type>
</chart>';
				break;


			case "3dpie_finance":

			$results = '<chart>
	<axis_category shadow="low" size="12" color="000000" alpha="50" orientation="horizontal" font="黑体" />
	<axis_ticks value_ticks="false" category_ticks="false" />
	<axis_value alpha="0" />
	<chart_border top_thickness="0" bottom_thickness="0" left_thickness="0" right_thickness="0" />
	<chart_data>
		<row>
			<null/>
			<string >1</string>
			<string >2</string>
			<string >3</string>
		</row>
		<row>
			<string>Money</string>
			<number>'.$dataarr['y'].'</number>
			<number>'.$dataarr['w'].'</number>
			<number>'.$dataarr['t'].'</number>
		</row>
	</chart_data>
	<draw>
		<text shadow="high" color="cc0000" font="黑体" alpha="90" size="20" x="8" y="6" width="180" height="50" h_align="center">款项统计</text>
	</draw>
	<chart_label shadow="low" alpha="65" size="10" position="inside" as_percentage="true" />
	<chart_pref select="true" drag="false" rotation_x="50" />
	<chart_rect x="105" y="50" width="580" height="180" positive_alpha="0" />
	<chart_type>3d pie</chart_type>
</chart>';
				break;

		}
		$this->WriteCache($key, $results);
		return true;
	}
	

	function WriteCache($key, $cacheData)
	{
		if (!@file_exists ("./resource/".$_SESSION['cc']['ccompany']."/"))
		{
			$this->_mkdir("./resource/".$_SESSION['cc']['ccompany']."/");
		}
		
		$handle = fopen("./resource/".$_SESSION['cc']['ccompany']."/".$key.".xml", "w");
		@flock($handle, 3);
		fwrite($handle, $cacheData);
		fclose($handle);
	}

    function _mkdir ($b_path, $mode=0777)
    {
      if (@mkdir ($b_path, $mode))
      {
		@chmod ($b_path, $mode);
      	return true;
      }
      return false;
    }	

}
?>