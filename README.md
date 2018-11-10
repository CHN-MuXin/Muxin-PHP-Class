***

沐心 PHP 模块简介
---


因为经常开发项目的需求，很多项目又不能二次开发，所以才想把一些常用的功能封装起来以加快开发速度。


***

<h2 id="menu">项目目录</h2>

* [微信类](#wx_class)
	* [微信公众号类](#wx_mp)
		* [微信公众号后台服务器类](#wx_mp_server)
	* [微信支付类](#wx_pay)
		* [微信支付回调类](#wx_pay_qyfk)
		* [企业付款类](#wx_pay_qyfk)
* [支付宝类](#alipay_class)
	* [支付宝回调类](#alipay_class)


***

<h3 id="wx_class"></h3>
<h3 id="wx_mp"></h3>
<h3 id="wx_mp_server">- 微信公众号后台服务器</h3>

**目录结构**

```txt

├─ 项目目录

│ ├─index.php

│ ├─Muxin

│ │ ├─weixin

│ │ │ ├─mp

│ │ │ │ ├─server

│ │ │ │ │ ├─wx_mp_server.class.php

```

**index.php**
```php
<?php
	//引入公众号服务类
	include("Muxin/weixin/mp/server/wx_mp_server.class.php");

	//继承公众号类实现自己的功能
	class test extends wx_mp_server{
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


### ThinkPHP 使用方法

将 Muxin 文件夹复制到 ThinkPHP/Lirary 目录

```php
<?php
	//导入类
	import("Muxin.weixin.mp.server.wx_mp_server");
	
	//继承 wx_mp_server 类
	class test extends \wx_mp_server{
		/*
			您的业务代码
		*/
	}
?>
```


[返回目录](#menu)

***




