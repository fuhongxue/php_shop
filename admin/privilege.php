<?php

	//后台权限控制

	//获取用户当前的动作请求
	$act = isset($_POST['act']) ? $_POST['act'] : (isset($_GET['act']) ? $_GET['act'] : 'login');

	//加载公共文件
	include_once 'includes/init.php';

	//判断用户请求动作
	if($act == 'login'){
		//用户是想得到登陆界面进行登录
		include_once ADMIN_TEMP.'/login.html';
	}elseif ($act == 'signin') {
		//用户已经做了登录操作，在提交用户信息进行验证
		$username = isset($_POST['username']) ? $_POST['username'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$captcha = isset($_POST['captcha']) ? $_POST['captcha'] : '';

		//合法性验证
		//验证码合法性验证
		if(empty($captcha)){
			admin_redirect('privilege.php','必须填写验证码',2);
		}

		if(empty($username) || empty($password)){
			//用户信息不完整
			admin_redirect('privilege.php','用户名或者密码都不能为空',2);
		}

		//验证码有效性验证
		if(!Captcha::checkCaptcha($captcha)){
			admin_redirect('privilege.php','验证码错误',2);
		}


		//验证用户有效性
		$admin = new Admin();

		if ($user = $admin->checkByUsernameAndPassword($username,$password) ){
			//将数据保存到文件
			//file_put_contents('user.txt', serialize($user));

			//将用户信息保存到session
			$_SESSION['user'] = $user;

			//判断用户是否记住用户信息
			if(isset($_POST['remember'])){
				//用户选择了保存用户信息
				//设置cookie，记住用户的信息，把用户信息放到浏览器
				setcookie('user_id',$user['a_id'],time()+7*3600*24);
			}

			$admin->updateLoginInfo($user['a_id']);

			//登录成功进入到系统首页
			admin_redirect("index.php?name={$user['a_username']}&time={$user['a_last_log_time']}",'登录成功',2);
		}else{
			//验证失败，用户名或者密码错误
			admin_redirect('privilege.php','用户名或者密码错误',2);
		}

	}elseif ($act == 'logout') {
		//用户退出系统
		//清空session和销毁session文件两种方式

		//销毁session
		session_destroy();

		if(isset($_COOKIE['user_id'])){
			//删除cookie
			setcookie('user_id','',1);
		}

		admin_redirect('privilege.php?act=login','退出成功',2);
	}elseif ($act == 'captcha') {
		//用户要获取验证码图片
		$captcha = new Captcha();

		//告知浏览器为图片处理
		header('Content-type:image/png');
		//生成验证码图片
		$captcha->generate();
	}


	
