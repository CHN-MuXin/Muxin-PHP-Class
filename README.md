***

沐心 PHP 模块简介
---


因为经常开发项目的需求，很多项目又不能二次开发，所以才想把一些常用的功能封装起来以加快开发速度。



## 联系我


QQ：285169134


Email：a@ph233.cn


***


<h2 id="menu">项目目录</h2>


* [目录结构](#Dir_description)
* [微信类](#wx_class)
	* [微信公众号类](#wx_mp)
		* [微信公众号后台服务器类](#wx_mp_server)
		* [微信公众号模板消息类](#wx_mp_mbxx)
	* [微信支付类](#wx_pay)
		* [微信支付回调类](#wx_pay_qyfk)
		* [企业付款类](#wx_pay_qyfk)
* [支付宝类](#alipay_class)
	* [支付宝回调类](#alipay_class)
* [其它类](#other_class)
	* [天气类](#other_weather_class)
	* [邮件类 PHPMailer （第三方）](#other_PHPMailer_class)
	* [二维码生成 phpQRcode （第三方）](#other_phpQRcode_class)


***


<h3 id="Dir_description">- 目录结构</h3>


##### 普通项目


```txt

├─ 项目目录

│ ├─ index.php

│ ├─ Muxin

│ │ ├─ auto.php

│ │ ├─ ... 其它文件或目录


```


普通项目要在 index.php 文件或者其它项目文件引入 auto.php 以实现自动加载。



例：
**index.php**
```php
<?php


	//引用自动加载
	include("./Muxin/auto.php");
	
	...
	...
?>
```


ThinkPHP 可以直接使用自动加载机制实例化或者继承类。


##### ThinkPHP 3.x 项目



```txt

├─ 项目目录

│ ├─ index.php

│ ├─ ThinkPHP

│ │ ├─ Library

│ │ │ ├─ Muxin

│ │ │ │ ├─ ... 其它文件或目录


```


##### ThinkPHP 5.x 项目



```txt

├─ 项目目录

│ ├─ index.php

│ ├─ ThinkPHP

│ ├─ extend

│ │ ├─ Muxin

│ │ │ ├─ ... 其它文件或目录


```


***


<h3 id="wx_class"></h3>
<h3 id="wx_mp"></h3>
<h3 id="wx_mp_server">- 微信公众号后台服务器</h3>


**index.php**
```php
<?php

	//继承公众号类实现自己的功能
	class test extends \Muxin\weixin\mp\server\wx_mp_server{
		//重载 接收文本消息
		public function msg_text($data){
			//服务器下发的信息
			$开发者 = $data->ToUserName;
			$用户ID = $data->FromUserName;
			$信息创建时间 = $data->CreateTime;
			$信息内容 = $data->Content;
			$信息ID = $data->MsgId;
			
			/*
				在这里添加您的代码
			
			*/
			
			
			//回复给用户的信息
			$回复内容='您给我发了信息：'.$信息内容;

			//调用回复文本消息函数回复用户信息
			$this->ret_text($用户ID,$开发者,$回复内容);
		}

		//重载 接收用户关注事件
		public function event_subscribe($data){
			$开发者ID = $data->ToUserName;
			$用户ID = $data->FromUserName;
			$信息创建时间 = $data->CreateTime;
			$事件KEY值 = $data->EventKey;
			$二维码ticket = $data->Ticket;
			
			/*
				您的代码
			*/
			
			
			$ret_text = "感谢您的关注";
			$this->ret_text($开发者ID,$用户ID,$ret_text);

		}
		
		//重载 接收用户取消关注事件
		public function event_unsubscribe($data){
			$开发者ID = $data->ToUserName;
			$用户ID = $data->FromUserName;
			$信息创建时间 = $data->CreateTime;
			
			/*
				您的代码
			*/
		}
		
	}
	

	//配置公众号参数
	$config=array(
		'APPID'=>'公众号的APPID',
		'AppSecret'=>'公众号的AppSecret',
		'TOKEN'=>'公众号TOKEN',
		'encodingAesKey'=>'公众号encodingAesKey',
	);
	
	//实例化业务对象
	$t = new test($config['APPID'],$config['TOKEN'],$config['encodingAesKey'],1);		

?>

```


[返回目录](#menu)


***


<h3 id="wx_mp_mbxx">- 微信公众号模板消息类</h3>


**index.php**
```php
<?php

	//创建公众号参数
	$arr=array(
	
	);
	
	//实例化模板信息类
	$test=new \Muxin\weixin\mp\wx_mbxx($arr);

	//初始化模板信息格式
	$msg=array(
	
	);

	//发送信息
	$ret=$wx_dk->test($msg);
	
?>

```


[返回目录](#menu)


***


<h3 id="wx_pay"></h3>
<h3 id="wx_pay_qyfk">- 微信企业付款类</h3>


**index.php**
```php
<?php

	//创建参数
	$wx_config=array(
		'appid'=>'wx91667d0976219999',	//微信公众号appid
		'partnerid'=>'1377329999',		//微信支付商户号
		'partnerkey'=>'key',			//微信支付api安全密钥
		'cert'=>'my/apiclient_cert.pem',//微信支付证书
		'key'=>'my/apiclient_key.pem',	//微信支付证书密钥
		'url'=>'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
		'ip'=>'127.0.0.1',				//用户ip  (动态获取用户IP)
	);
	
	//实例化企业付款类
	$wx_dk=new \Muxin\weixin\pay\wx_qyfk(
		$wx_config['appid'],
		$wx_config['partnerid'],
		$wx_config['partnerkey'],
		$wx_config['cert'],
		$wx_config['key'],
		$wx_config['url'],
		$wx_config['ip']  
	);


	//初始化打款信息
	$openid='用户的OPENID';
	//付款金额(不小于1)
	$money=1;
	//订单号
	$o_id='test'.time();
	//付款详情
	$info='付款说明';

	//开始打款
	$ret=$wx_dk->dakuan($openid,$money,$o_id,$info);
	//打印结果
	var_dump($ret);
		
	/*
	//成功结果示例
	Array
	(
		[RETURN_CODE] => SUCCESS
		[RETURN_MSG] => 
		[MCH_APPID] => wx91667d0976219999
		[MCHID] => 1377329999
		[DEVICE_INFO] => 
		[NONCE_STR] => ffDx0ZeUOyb2oWhk26RHglxnfMgqi7tS
		[RESULT_CODE] => SUCCESS
		[PARTNER_TRADE_NO] => test1482901333
		[PAYMENT_NO] => 1000018301201612285710545447
		[PAYMENT_TIME] => 2016-12-28 13:02:16
	)
	*/

?>

```


[返回目录](#menu)


***


<h3 id="other_class"></h3>
<h3 id="other_weather_class">- 天气查询类</h3>


**index.php**
```php
<?php
	
	//实例化天气类
	$test = new \Muxin\weather\weather();
	//设置要查询的城市或者包含城市名的字符串
	$test->setCity("我要查北京市的天气");
	//获取 json格式的天气
	var_dump($test->getJson());
	
	//获取 已格式化当天天气
	var_dump($test->get_weather());
	//获取 已格式化一周天气
	var_dump($test->get_7days_weather());	

	//get_weather 和 get_7days_weather 方法可以带一个参数，参数和setCity 相同

?>

```


[返回目录](#menu)



***


<h3 id="other_PHPMailer_class">- PHPMailer 邮件类（第三方）</h3>


[PHPMailer 原项目 GitHub 地址](https://github.com/PHPMailer/PHPMailer)



```php

<?php

		//实例化邮件类
		$mail = new \Muxin\PHPMailer\PHPMailer();
		
		//设置语言版本 第一个参数语言名称 第二个参数语言包路径 默认使用类目录下的
		$mail->SetLanguage('zh_cn');

		// 设置邮件程序使用 SMTP
		$mail->IsSMTP();    
		
		//指定主服务器和备用服务器
		$mail->Host = "smtp.xxxx.com;smtp1.xxxx.com";
		
		//打开SMTP身份验证
		$mail->SMTPAuth = true;
		
		// SMTP 用户名
		$mail->Username = "youName";
		
		// SMTP 密码
		$mail->Password = "youPassWord";

		// 发信人邮箱
		$mail->From = "name@xxxx.com";
		
		// 来自  比如该邮件来自 XX 客户端 XX 网站
		$mail->FromName = "邮件来自";
		
		
		// 添加收件人地址 带收件人名称
		$mail->AddAddress("test@xx.com", "收件人称呼");
		
		// 添加收件人地址 名称是可选的
		//$mail->AddAddress("ellen@example.com");                  
		
		// 添加回复邮件地址 跟回复邮件名称  收件人点击回复时回复这个邮件 不添加为发件邮箱
		$mail->AddReplyTo("rep@xxxx.com", "回复此邮件");

		// 将自动换行设置为50个字符
		$mail->WordWrap = 50;                                 
		
		// 添加附件
		//$mail->AddAttachment("/var/tmp/file.tar.gz");         
		
		// 附件可以指定名称
		//$mail->AddAttachment("/tmp/image.jpg", "new.jpg");    
		
		// 将电子邮件格式设置为HTML
		$mail->IsHTML(true);                                  

		$mail->Subject = "邮件主题";
		
		$mail->Body    = "这是邮件正文 <b>以粗体显示!</b>";
		
		//$mail->AltBody = "这是非 HTML 格式的纯文本正文。";
		

		if(!$mail->Send()){
		   echo "邮件无法发送. <p>";
		   echo "邮件错误: " . $mail->ErrorInfo;
		   exit;
		}else{
			echo "信息已发送";
		}
	
?>
```


[返回目录](#menu)


***



<h3 id="other_phpQRcode_class">- 二维码生成 phpQRcode（第三方）</h3>


[phpQRcode 原项目 GitHub 地址](https://github.com/t0k4rt/phpqrcode)



```php

<?php

	// 直接输出二维码
	Muxin\Qrcode\Qrcode::png('二维码值');

	// 保存二维码文件然后通过 HTML 输出
	Muxin\Qrcode\Qrcode::png('二维码值','二维码路径.png');
	echo '<img src="二维码路径.png" />'; 

	// 使用 Svg 输出二维码
	$svgCode = \Muxin\Qrcode\QRcode::svg('二维码值'); 
	echo $svgCode;

	// 使用 SVG 配置输出

	// Configuring SVG 
	$dataText   = 'PHP QR Code :)'; 
	$svgTagId   = 'id-of-svg'; 
	$saveToFile = false; 
	$imageWidth = 250; // px 
	// SVG file format support 
	$svgCode = QRcode::svg($dataText, $svgTagId, $saveToFile, QR_ECLEVEL_L, $imageWidth);
	
	
	
	/*    **** 常用格式
		//电话
		tel:(86)18988888888
		
		//短信
		sms:(86)18988888888
		
		//电子邮件
		mailto:email@email.com
		
		//电子邮件带内容
		$email = 'john.doe@example.com'; 
		$subject = 'question'; 
		$body = 'please write your question here'; 
		$codeContents = 'mailto:'.$email.'?subject='.urlencode($subject).'&body='.urlencode($body); 
		
		//Skeype 通话
		$skypeUserName = 'echo123'; 
		// we building raw data 
		$codeContents = 'skype:'.urlencode($skypeUserName).'?call'; 
     
		//名片 添加手机通讯录
	    // here our data 
		$name = 'John Doe'; 
		$phone = '(049)012-345-678'; 
		 
		// we building raw data 
		$codeContents  = 'BEGIN:VCARD'."\n"; 
		$codeContents .= 'FN:'.$name."\n"; 
		$codeContents .= 'TEL;WORK;VOICE:'.$phone."\n"; 
		$codeContents .= 'END:VCARD'; 	
		
		//名片 详细 添加手机通讯录
		// here our data 
		$name         = 'John Doe'; 
		$sortName     = 'Doe;John'; 
		$phone        = '(049)012-345-678'; 
		$phonePrivate = '(049)012-345-987'; 
		$phoneCell    = '(049)888-123-123'; 
		$orgName      = 'My Company Inc.'; 
		$email        = 'john.doe@example.com'; 
		// if not used - leave blank! 
		$addressLabel     = 'Our Office'; 
		$addressPobox     = ''; 
		$addressExt       = 'Suite 123'; 
		$addressStreet    = '7th Avenue'; 
		$addressTown      = 'New York'; 
		$addressRegion    = 'NY'; 
		$addressPostCode  = '91921-1234'; 
		$addressCountry   = 'USA'; 
		// we building raw data 
		$codeContents  = 'BEGIN:VCARD'."\n"; 
		$codeContents .= 'VERSION:2.1'."\n"; 
		$codeContents .= 'N:'.$sortName."\n"; 
		$codeContents .= 'FN:'.$name."\n"; 
		$codeContents .= 'ORG:'.$orgName."\n"; 
		$codeContents .= 'TEL;WORK;VOICE:'.$phone."\n"; 
		$codeContents .= 'TEL;HOME;VOICE:'.$phonePrivate."\n"; 
		$codeContents .= 'TEL;TYPE=cell:'.$phoneCell."\n"; 
		$codeContents .= 'ADR;TYPE=work;'. 
			'LABEL="'.$addressLabel.'":' 
			.$addressPobox.';' 
			.$addressExt.';' 
			.$addressStreet.';' 
			.$addressTown.';' 
			.$addressPostCode.';' 
			.$addressCountry 
		."\n"; 
		$codeContents .= 'EMAIL:'.$email."\n"; 
		$codeContents .= 'END:VCARD'; 		
		
		
		
		//名片 照片 添加手机通讯录
		// here our data 
		$name = 'John Doe'; 
		$phone = '(049)012-345-678'; 
		// WARNING! here jpeg file is only 40x40, grayscale, 50% quality! 
		// with bigger images it will simply be TOO MUCH DATA for QR Code to handle! 
		$avatarJpegFileName = 'avatar.jpg'; 
		// we building raw data 
		$codeContents  = 'BEGIN:VCARD'."\n"; 
		$codeContents .= 'FN:'.$name."\n"; 
		$codeContents .= 'TEL;WORK;VOICE:'.$phone."\n"; 
		$codeContents .= 'PHOTO;JPEG;ENCODING=BASE64:'.base64_encode(file_get_contents($avatarJpegFileName))."\n"; 
		$codeContents .= 'END:VCARD'; 		
		
	*/
?>
```


[返回目录](#menu)


***

