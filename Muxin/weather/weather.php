<?php
//天气类
namespace Muxin\weather;
class weather{
	private $city='北京';
	//获取JSON格式的天气信息
	public function getJson(){
		$url='http://wthrcdn.etouch.cn/weather_mini?city='.$this->city;
		return file_get_contents('compress.zlib://'.$url);
	}
	
	//获取XML格式的天气信息
	public function getXml(){
		$url='http://wthrcdn.etouch.cn/WeatherApi?city='.$this->city;
		return file_get_contents($url);
	} 
	
	//设置当前城市
	public function setCity($str){
		if(!empty($str)){
			$city=$this->get_City($str);
			if($city){
				$this->city=$city;
			}
		}
	}
	//提取字符串中的城市名或地区名
	public function get_City($name){
		include(__DIR__.'/diqu.php');
		foreach($info as $v)
		{
			if(substr_count($name,$v) > 0){
				return $v;
			}
			//精确查找没用 则执行模糊匹配
			$vb=str_replace(array("省","市","县","自治区","特别行政区"),"",$v);
			if(substr_count($name,$vb) > 0){
				return $v;
			}
		}
		unset($info);
		return false;
	}

	//获取当天天气
	public function get_weather($city=''){
		if(!empty($city)){
			$this->setCity($city);
		}		
		$data=json_decode($this->getJson(),true);
		if($data['desc'] == 'OK' || $data['desc'] == 'ok'){
			$tq=$data['data']['forecast'][0];
			$tq['fengli']=	$this->zz($tq['fengli'],'/\<\!\[CDATA\[([\S]+)\]\]\>/');
	
			$text="{$tq['date']} {$data['data']['city']} 天气 {$tq['type']}\r\n";
			$text.= "{$tq['low']} ~ {$tq['high']}\r\n";
			$text.= "{$tq['fengxiang']} {$tq['fengli']}";
		}else{
			$text='天气查询失败！';
		}
		return $text;
	}
	
	//获取最近七天天气
	public function get_7days_weather($city=''){
		if(!empty($city)){
			$this->setCity($city);
		}
			
		$data=json_decode($this->getJson(),true);
		if($data['desc'] == 'OK' || $data['desc'] == 'ok'){
			$text="{$data['data']['city']} 最近天气如下\r\n";
			foreach($data['data']['forecast'] as $tq){
				$tq['fengli']=	$this->zz($tq['fengli'],'/\<\!\[CDATA\[([\S]+)\]\]\>/');
				$text.="{$tq['date']}  {$tq['type']}\r\n";
				$text.= "{$tq['low']} ~ {$tq['high']}\r\n";
				$text.= "{$tq['fengxiang']} {$tq['fengli']}\r\n";
				$text.= "------------------------------\r\n";
			}
		}else{
			$text='天气查询失败！';
		}
		return $text;
	}
	public function zz($a,$b){
		$arr=array();
		preg_match($b, $a, $arr);
		//echo $arr[1];
		//print_r($arr);
		if (count($arr) == 2)		
			$data=$arr[1];
		else
			$data='';
		
		return $data;
	}
	
}
	
	/*

	http://wthrcdn.etouch.cn/weather_mini?city=北京
	通过城市名字获得天气数据，json数据
	http://wthrcdn.etouch.cn/weather_mini?citykey=101010100
	通过城市id获得天气数据，json数据


	http://wthrcdn.etouch.cn/WeatherApi?citykey=101010100
	通过城市id获得天气数据，xml文件数据,
	当错误时会有<error>节点
	http://wthrcdn.etouch.cn/WeatherApi?city=北京
	通过城市名字获得天气数据，xml文件数据
	*/

?>