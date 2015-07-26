<?php

	//字符集设置
	header("Content-type:text/html;charset=utf-8");

	//定义目录常量
	//系统根目录
	define('HOME_ROOT', str_replace('\\','/',substr(__DIR__, 0,strpos(__DIR__, '\admin\includes'))));
	//前台公共目录
	define('HOME_INC',HOME_ROOT.'/includes');
	define('HOME_CONF', HOME_ROOT.'/conf');
	//后台根目录
	define('ADMIN_ROOT',HOME_ROOT.'/admin');
	define('ADMIN_INC',ADMIN_ROOT.'/includes');
	define('ADMIN_TEMP',ADMIN_ROOT.'/templates');
	define('ADMIN_UPL',ADMIN_ROOT.'/uploads');

	//定义url常量
	define('__ADMIN__', 'http://www.shop.com/admin');

	//定义系统错误控制
	@ini_set('error_reporting',        E_ALL);
	@ini_set('display_errors',        1);

	//加载公共函数
	include_once ADMIN_INC.'/functions.php';

	//加载配置文件
	$config = include_once HOME_CONF.'/config.php';

	@session_start();

	$script_name = basename($_SERVER['SCRIPT_NAME']);

	if($script_name == 'privilege.php'  && ($act == 'login' || $act == 'captcha' || $act == 'signin')){

	}else{
		//验证用户身份
		if(!isset($_SESSION['user'])){
			//判断浏览器是否携带了cookie
			if (isset($_COOKIE['user_id'])) {
				//有cookie
				//帮助用户进行登录
				$admin = new Admin();

				//调用Admin类的方法，通过ID获取用户信息
				if($user = $admin->getUserInfoById($_COOKIE['user_id'])){
					//得到用户信息
					//将用户信息写入session
					$_SESSION['user'] = $user;

					//更新用户信息
					$admin->updateLoginInfo($user['a_id']);
				}else{
					admin_redirect('privilege.php','保存的用户信息已经失效，请重新登录',2);
				}
			}else{	
				//没有cookie，也没有session
				admin_redirect('privilege.php','请先登录',2);
			}

		}
	}



