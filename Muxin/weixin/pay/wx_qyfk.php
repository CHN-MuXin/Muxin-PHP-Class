<?php  
//微信商家付款类
//沐心 2016/12/28

namespace Muxin\weixin\pay;

class wx_qyfk
{//微信商家打款类

	//私有变量 private
	private $appid,$mchid,$device_info,$ip,$key;

	//受保护 protected

	//公用变量 
	public $qingqiu,$url;


	function __construct($appid,$mchid,$key,$cert_pem,$key_pem,$url,$ip='',$device_info=null)
	{
		$this->appid=$appid;
		//公众号 appid
		$this->mchid=$mchid; 
		//微信支付商户号
		$this->key=$key;     
		//微信支付api安全密钥
		$this->url=$url;					
		//请求页面 URL
		$this->ip=$ip;						
		//调用接口机器IP地址
		$this->device_info=$device_info;	
		//微信支付分配的终端设备号

        //证书路径
		$this->apiclient_cert	= $cert_pem;
		$this->apiclient_key	= $key_pem;

        //初始化数据结构
        $this->qingqiu=array(
			
			'mch_appid'			=>	$this->appid ,
			//公众号 appid  必须

			'mchid'				=>	$this->mchid ,
			//商户号   必须

			'device_info'		=>	$this->device_info  ,
			//微信支付分配的终端设备号  非必须

			'nonce_str'			=>null ,  
			//随机字符串 不长于 32位     必须

			'sign'				=>	null  ,
			//签名 使用 get_wx_sign 获取   必须

			'partner_trade_no'	=>	null  ,
			//商户订单号，需保持唯一  必须

			'openid'			=>	null  ,
			//商户appid 下 某用户的 openid  必须

			'check_name'		=>	'NO_CHECK'  ,
			//效验用户名选项  必须
			// NO_CHECK：不校验真实姓名 
			//FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账） 
			//OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）

			're_user_name'		=>	null , 
			//收款人真实姓名 如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名

			'amount'			=>	null  ,
			//金额 单位为分   必须

			'desc'				=>	null  ,
			//企业付款描述信息 必须

			'spbill_create_ip'	=>	$this->ip     
			//IP地址 调用接口的机器Ip地址   必须
		);

	}
	function __destruct()
	{
		return true;
	}

	//获取微信签名字符串
	//1 所有非空参数数组
	//2 KEY
	function get_wx_sign(){
		//获取数据
		//$arr=$this->qingqiu;
		//SORT_ASC - 按照上升顺序排序 
		//SORT_DESC - 按照下降顺序排序 
		//排序类型标志： 
		//◦ SORT_REGULAR - 将项目按照通常方法比较 
		//◦ SORT_NUMERIC - 将项目按照数值比较 
		//◦ SORT_STRING - 将项目按照字符串比较 

		//删除所有值为空的项目
		foreach($this->qingqiu as $k=>$v)
		{
			if (empty($v))
			{
				unset($this->qingqiu[$k]);
			}
		}
		

        //设置金额为字符串避免键值转换时丢参数
		$money=$this->qingqiu['amount'];
        $this->qingqiu['amount']='amount';


		//交换数组的 键值
		$this->qingqiu = array_flip($this->qingqiu);

		//进行自然排序
		natsort($this->qingqiu);

		//交换数组的键值
		$this->qingqiu=array_flip($this->qingqiu);
        
		//将金额返回
        $this->qingqiu['amount']=$money;


  
		//组装字符串 
		$s='';
		$i=0;
		foreach($this->qingqiu as $k=>$v)
		{
			$i++;
			$s.= $i == 1 ? $k.'='.$v : '&'.$k.'='.$v;
		}

		//插入KEY
		$s.='&key='.$this->key;
	
        //echo $s.'<br>';

		//进行 MD5编码 并转换为大写
		return strtoupper(MD5($s));
	}

	//获取微信 随机符串
	//参数1 位数
	public function get_nonce_str($len=32){

		$s='';
		for ($i=0;$i < $len ; $i++ )
		{
			srand();
			$sjs=rand(10,100);
			
			srand();
			if ( $sjs < 25 )
			{//随机抽取 数字
				$s.=rand(0,9);
			}
			else if ($sjs < 60)
			{//随机抽取 大写字母
		
				$s.=chr(rand(65,90));

			}
			else 
			{//随机抽取 小写字母
				$s.=chr(rand(97,122));
			}
		}
		return $s;
	}

	//获取微信 XML
	//成功返回 XML字符串 失败返回 false
	public function get_wx_xml(){
        //生成签名
		$this->qingqiu['sign']=$this->get_wx_sign();

		//获取数据
		$arr=$this->qingqiu;

		//删除所有值为空的项目
		$xml='';

		foreach($arr as $k=>$v)
		{
			$xml .= "<{$k}>{$v}</{$k}>\r\n";
		}
		 
		if (empty($xml))
		{
			return false;
		}

		return '<xml>'.$xml.'</xml>';
	}

   

	private function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
	{
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

		//以下两种方式需选择一种

		//第一种方法，cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT,$this->apiclient_cert);
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY,$this->apiclient_key);

		//第二种方式，两个文件合成一个.pem文件
		//curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/api/weixin/all.pem');

		if( count($aHeader) >= 1 ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		}

		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
			//echo "call faild, errorCode:$error\n"; 
			curl_close($ch);
			return false;
		}
	}
	//xml 转数组
	public function xml_array($xml){

		$p = xml_parser_create(); //建立 xml解析器
		xml_parse_into_struct($p, $xml, $vals, $index);//解析xml
		xml_parser_free($p);//关闭解析器

		//echo '<pre>';
		//echo "Index array\n";
		//print_r($index);
		//echo "\nVals array\n";
		//print_r($vals);
		//echo '</pre>';

		$arr=array();
		foreach($index as $k=>$v)
		{
			if ($k=='XML')
			{
				continue;
			}
			$arr[$k]=$vals[$v[0]]['value'];

		}
		return $arr;
	}
    //验证返回数据
	public function yanzheng($arr){

		if ( !isset($arr['RETURN_CODE']) || !$arr['RETURN_CODE'] == 'SUCCESS')
		{
			return false;
		}
		if ( !isset($arr['RESULT_CODE']) || !$arr['RESULT_CODE'] == 'SUCCESS')
		{
			return false;
		}
		if ( !isset($arr['PAYMENT_NO']) || !$arr['PAYMENT_NO'] != '')
		{
			return false;
		}
		//if ( !isset($arr['MCH_APPID']) || !$arr['MCH_APPID'] == $this->qingqiu['mch_appid'])
		//{
		//	return false;
		//}
		//if ( !isset($arr['MCHID']) || !$arr['MCHID'] == $this->qingqiu['mchid'])
		//{
		//	return false;
		//}
		if ( !isset($arr['NONCE_STR']) || !$arr['NONCE_STR'] == $this->qingqiu['nonce_str'])
		{
			return false;
		}
		if ( !isset($arr['PARTNER_TRADE_NO']) || !$arr['PARTNER_TRADE_NO'] == $this->qingqiu['partner_trade_no'])
		{
			return false;
		}

		return true;
	}



	//打款给用户
	//1 当前公众号下 用户openid
	//2 给改用户打款的金额
	//3 商家订单号 需要保持唯一
	//4 商家打款描述
	//5 校验用户姓名选项
	//6 收款用户真实姓名
	//成功返回 订单号 和时间  失败返回false
	//array(payment_no=订单号,payment_time=时间);
	public function dakuan($openid,$money,$o_id,$desc,$check_name='NO_CHECK',$user_name=''){
		//处理传入的参数
		//用户openid 
		$this->qingqiu['openid']=$openid;
		//需要打款的金额 由于微信是以分做单位需要*100
		$this->qingqiu['amount']=$money*100;
		//商户订单号 需要保持唯一
		$this->qingqiu['partner_trade_no']=$o_id;
		//企业付款描述信息
		$this->qingqiu['desc']=$desc;
		$this->qingqiu['check_name']=$check_name;
		$this->qingqiu['re_user_name']=$user_name;
		$this->qingqiu['nonce_str']=$this->get_nonce_str();
 
		$xml=$this->get_wx_xml();
		
		//echo $xml;
		//发送付款请求
		$r_xml=$this->curl_post_ssl($this->url,$xml);
		//file_put_contents($_SERVER["DOCUMENT_ROOT"].'mobile/data/dk_xml.xml',$r_xml."\r\n",FILE_APPEND);
		$arr=$this->xml_array($r_xml);
		//file_put_contents($_SERVER["DOCUMENT_ROOT"].'mobile/data/dk_log.txt',json_encode ($arr)."\r\n",FILE_APPEND);
        //验证返回数据
		if ($this->yanzheng($arr))
		{
	
			//file_put_contents(getcwd().'/Application/Runtime/Logs/ok-dklog-'.date('Y-m-d').'.txt',$r_xml."\r\n",FILE_APPEND);
			return $arr;
		}else {
			//打款款失败记录
			//file_put_contents(getcwd().'/Application/Runtime/Logs/err-dklog-'.date('Y-m-d').'.txt',$r_xml."\r\n",FILE_APPEND);
			echo $r_xml;
			return false;
		}
	}
}
