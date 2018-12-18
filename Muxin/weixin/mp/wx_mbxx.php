<?php 
// 沐心
// 微信模板消息类

namespace Muxin\weixin\mp;

define('IN_ECS', true);


class wx_mbxx{
	//参数1 模板消息内容 参数2 用户ID
	public static function send_msg($arr,$u_id=0){
		global $db,$ecs;
		require_once(__DIR__. '/../../weixin/wechat.class.php');
		//@ini_set('display_errors', true);
		$weixinconfig =$GLOBALS['db']->getRow( "SELECT * FROM " . $GLOBALS['ecs']->table('weixin_config') . " WHERE `id` = 1" );
		$weixin = new core_lib_wechat($weixinconfig);
		$url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$weixin->checkAuth();
		if(empty($u_id))
			return false;
		$sql='SELECT wu.fake_id FROM '.$ecs->table('weixin_user').' AS wu,'.$ecs->table('users').' AS u WHERE wu.ecuid=u.user_id AND wu.isfollow = 1 AND u.user_id='.$u_id;
		$arr['touser']=$db->getOne($sql);
		if(empty($arr['touser']))
			return false;
		$re=self::curl_post_ssl($url,json_encode($arr));
		if(!$re)
			return false;
		$re=json_decode($re,true);
		if( $re['errmsg'] == 'ok' && $re['errcode'] == 0 )
			return true;			
		else
			return false;
	}
	private static function curl_post_ssl($url, $vars, $second=30,$ca=false){
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL,$url);
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		}
		if($ca){
			//以下两种方式需选择一种
			//第一种方法，cert 与 key 分别属于两个.pem文件
			//默认格式为PEM，可以注释
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT,$ca->apiclient_cert);
			//默认格式为PEM，可以注释
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY,$ca->apiclient_key);
			//第二种方式，两个文件合成一个.pem文件
			//curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/api/weixin/all.pem');			
		}

		if( count($aHeader) >= 1 )
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		$Status = curl_getinfo($ch);
		curl_close($ch);
		if( intval($Status["http_code"])==200 )
			return $data;
		else
			return false;	
	}
}


// 给免单中奖者发送模板信息
// 用户ID 订单号 全免/半免 期号
function send_mbxx($u_id,$order_sn,$text,$qs){

	
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='PziaqKbt0qTLlVzmLMYEzgan9ZR3ju-DRj3MMX8Mhlg';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='WZLlm2YTsgdZX3kBI39wo3J_CbXDpailP49RAVRLB4A';//运营版 模板ID
	else
		return false;
		
	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	'http://'.$_SERVER['HTTP_HOST'].'/mobile/miandan.php?act=look&id='.$qs,//点击消息跳转URL
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
			//键
			'first'	=>	array(
				'value'	=>	'尊敬的用户您好，恭喜您的订单获得'.$text.'单，金额已经退回到您的账户，请在账户余额中查看。', //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(
				'value'	=>	$order_sn, //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(
				'value'	=>	'4000-987-998', //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(
				'value'	=>	'点击查看免单结果', //值
				'color'	=>	'#ff0000'  //颜色代码

			)
		)
	);

	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);
}



// 发送接单模板提示
// 用户ID  //商家名称  //订单编号  //点击跳转链接
function send_mbxx_jd($u_id,$su_name,$order_sn,$url){
	//引入模板消息文件
	//include_once(__DIR__. '/../api/weixin/weixin_mbxx.php');	
	
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='K2e2HcjYAyrVXk7UdZSWgYoJoY9UMON4mdQosbGYaU0';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='NyU4S2PzpV2h7SeRF-fxgqMxjvPLTgnmA_kd_gsZ9xE';//运营版 模板ID
	else
		return false;
		
	
	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	'http://'.$_SERVER['HTTP_HOST'].'/mobile/'.$url,//点击消息跳转URL
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
		
			//键
			'first'	=>	array(
				'value'	=>	'客官您好， “'.$su_name.'” 已在为您准备菜品，请稍候！', //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(
				'value'	=>	$order_sn, //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(
				'value'	=>	date('Y-m-d H:i:s'), //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(
				'value'	=>	'点击详情核对菜单。抢免单进度稍后微信通知您。', //值
				'color'	=>	'#ff0000'  //颜色代码 

			)
		)
	);

	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);
}



//	发送用户参加免单信息
// 用户ID 订单号 全免/半免 期号
function send_mbxx_cjmd($u_id,$order_sn,$qs){

	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='PziaqKbt0qTLlVzmLMYEzgan9ZR3ju-DRj3MMX8Mhlg';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='WZLlm2YTsgdZX3kBI39wo3J_CbXDpailP49RAVRLB4A';//运营版 模板ID
	else
		return false;
		
	
	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	'http://'.$_SERVER['HTTP_HOST'].'/mobile/miandan.php?act=look&id='.$qs,//点击消息跳转URL
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
			//键
			'first'	=>	array(
				'value'	=>	'尊敬的用户您好,您的订单已成功参加第'.$qs.'期免单。', //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(
				'value'	=>	$order_sn, //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(
				'value'	=>	'4000-987-998', //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(
				'value'	=>	'>>点击关注免单结果', //值
				'color'	=>	'#ff0000'  //颜色代码

			)
		)
	);

	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);
}


//send_mbxx_ddwc(181,array('url'=>'http://kf.wodiyou.com','first'=>'您的订单已完成','order'=>'201712150001','time'=>'2017-12-15 10：33','remark'=>'>>点击查看订单'));
// 订单完成模板
// 用户id ,参数数组
function send_mbxx_ddwc($u_id,$data){
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='g-qujKypofmSgameDTYgGSvn2PDtsJfOnu2bqKM19Yw';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='R2b5jkKFUhbDkk_nsrWOqHDVjzm9Koj4bQ0S8S74AhQ';//运营版 模板ID
	else
		return false;
		
	
	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	$data['url'],//点击消息跳转URL 必须是带http的全路径
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
			//键
			'first'	=>	array(//头部信息
				'value'	=>	$data['first'], //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(// 订单编号
				'value'	=>	$data['order'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(//订单完成时间
				'value'	=>	$data['time'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(//底部信息
				'value'	=>	$data['remark'], //值
				'color'	=>	'#ff0000'  //颜色代码
			)
		)
	);

	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);	
}



//send_mbxx_tkxx(181,array('url'=>'http://kf.wodiyou.com','first'=>'您在某商家的订单有退款','order'=>'201712150001','rmb'=>'10.00','num'=>'1','text'=>'这里是退款说明','remark'=>'>>点击查看订单'));
//退款模板消息 
// 用户id ,参数数组
function send_mbxx_tkxx($u_id,$data){
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='3LlSOq3scEn4L1Kci2WkIoMbU23NrP5BUCwYCuhfSZ4';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='yr8qctXoeZLGMcV8Ok9BB5C0Jr_JnfcOeI9MGJH0mXk';//运营版 模板ID
	else
		return false;

	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	$data['url'],//点击消息跳转URL 必须是带http的全路径
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
			//头部消息
			'first'	=>	array(
				'value'	=>	$data['first'], //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(//订单编号
				'value'	=>	$data['order'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(//退款金额
				'value'	=>	$data['rmb'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword3'	=>	array(//退货数量
				'value'	=>	$data['num'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword4'	=>	array(//退款退货原因
				'value'	=>	$data['text'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(//尾部消息
				'value'	=>	$data['remark'], //值
				'color'	=>	'#ff0000'  //颜色代码
			)
		)
	);
	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);	
}

//  send_mbxx_fxtz(857,array('url'=>'http://www.wodiyou.com/mobile/user.php?act=account_detail','first'=>'您的订单获得奖励金','order'=>'201712150001','rmb'=>'￥10 + 10U金','remark'=>'>>点击查看详情'));
// 返现到账通知
// 用户id ,参数数组
function send_mbxx_fxtz($u_id,$data){
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='xlKJhZiCbjEw_BHubZc_OiaQE174Z9Wxbyvbb7H4sfk';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='JPTn1I9AMXkusldRqKZtdNe6V7wwI51KCoYD43ge_LU';//运营版 模板ID
	else
		return false;

	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	$data['url'],//点击消息跳转URL 必须是带http的全路径
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
			//头部消息
			'first'	=>	array(
				'value'	=>	$data['first'], //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'order'	=>	array(//订单编号
				'value'	=>	$data['order'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'money'	=>	array(//退款金额
				'value'	=>	$data['rmb'], //金额
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(//尾部消息
				'value'	=>	$data['remark'], //值
				'color'	=>	'#ff0000'  //颜色代码
			)
		)
	);
	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);	
}

//  send_mbxx_zjbd(857,array('url'=>'http://www.wodiyou.com/mobile/user.php?act=account_detail','first'=>'您的账户有变动','keyword1'=>'2017-12-16 16：00','keyword2'=>'￥10 U金10','keyword3'=>'￥10 U金10','remark'=>'>>点击查看详情'));
// 返现到账通知
// 用户id ,参数数组
function send_mbxx_zjbd($u_id,$data){
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='AL86rAticYu91iU4p6Tym8t7dkzkoAovzmRh1hM1v2Y';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='_d_Fy3LVOmuLmEONPOhBcSIsh85lvl0mmsacq6f34o8';//运营版 模板ID
	else
		return false;

	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	$data['url'],//点击消息跳转URL 必须是带http的全路径
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
			//头部消息
			'first'	=>	array(
				'value'	=>	$data['first'], //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(//变动时间
				'value'	=>	$data['keyword1'], //变动时间
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(//变动金额
				'value'	=>	$data['keyword2'], //变动时间
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword3'	=>	array(//帐户余额
				'value'	=>	$data['keyword3'], //变动时间
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(//尾部消息
				'value'	=>	$data['remark'], //值
				'color'	=>	'#ff0000'  //颜色代码
			)
		)
	);
	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);	
}

// 新接单模板提示
//send_mbxx_zjbd(857,array('url'=>'http://www.wodiyou.com/mobile/user.php?act=account_detail','first'=>'您的账户有变动','order_sn'=>'201802030001','time'=>'2017-12-16 16：00','remark'=>'>>点击查看详情'));
// 用户ID  //商家名称  //订单编号  //点击跳转链接
function send_mbxx_jdtx($u_id,$data){
	//引入模板消息文件
	//include_once(__DIR__. '/../api/weixin/weixin_mbxx.php');	
	
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='K2e2HcjYAyrVXk7UdZSWgYoJoY9UMON4mdQosbGYaU0';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='NyU4S2PzpV2h7SeRF-fxgqMxjvPLTgnmA_kd_gsZ9xE';//运营版 模板ID
	else
		return false;
		
	
	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	$data['url'],//点击消息跳转URL
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
		
			//键
			'first'	=>	array(
				'value'	=>	$data['first'], //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(
				'value'	=>	$data['order_sn'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(
				'value'	=>	$data['time'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(
				'value'	=>	$data['remark'], //值
				'color'	=>	'#ff0000'  //颜色代码 

			)
		)
	);

	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);
}


// 新接单模板提示
//send_mbxx_new_order(857,array('url'=>'http://www.wodiyou.com/mobile/user.php?act=account_detail','first'=>'您有新订单','order_sn'=>'201802030001','time'=>'2017-12-16 16：00','remark'=>'>>点击查看详情'));
// 用户ID  //商家名称  //订单编号  //点击跳转链接
function send_mbxx_new_order($u_id,$data){
	//引入模板消息文件
	//include_once(__DIR__. '/../api/weixin/weixin_mbxx.php');	
	
	if($_SERVER['HTTP_HOST'] == 'kf.wodiyou.com')
		$tem_id='_UE1hurfjHAjPg8Ia5w020r5EIAE-7xssnaOLXyz6cc';//开发版 模板ID
	else if($_SERVER['HTTP_HOST'] == 'www.wodiyou.com')
		$tem_id='Ymbq_8nZyj7uJTDBOqtYUTmkHEFpz7uqP6v8yX0urpI';//运营版 模板ID
	else
		return false;
	$arr=array(
		'template_id'	=>	$tem_id,//模板ID
		'url'			=>	$data['url'],//点击消息跳转URL
		'topcolor'		=>	'#ff0000',//标题颜色代码
		//数据组
		'data'	=>	array(
		
			//键
			'first'	=>	array(
				'value'	=>	$data['first'], //值
				'color'	=>	'#ff0000'  //颜色代码

			),
			'keyword1'	=>	array(
				'value'	=>	$data['order_sn'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'keyword2'	=>	array(
				'value'	=>	$data['time'], //值
				'color'	=>	'#0785f1'  //颜色代码

			),
			'remark'	=>	array(
				'value'	=>	$data['remark'], //值
				'color'	=>	'#ff0000'  //颜色代码 

			)
		)
	);
	wx_mbxx::send_msg($arr,$u_id);
	unset($arr);
}


?>