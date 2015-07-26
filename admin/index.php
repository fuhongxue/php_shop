<?php

	//获取用户动作
	$act = isset($_GET['act']) ? $_GET['act'] : 'index';

	//判断用户是否登录
	include_once 'includes/init.php';


	//判断动作
	if($act == 'index'){
		//接收数据（privilege.php传过来）
		// $user = $_GET['name'];
		// $time = $_GET['time'];
		//加载首页模板文件
		include_once ADMIN_TEMP.'/index.html';
	}elseif($act == 'top'){
		//接收数据（index.php?act=top传过来）
		// $user = $_GET['user'];
		// $time = $_GET['time'];

		//从文件读取数据
		// $user = unserialize(file_get_contents('user.txt'));

		//从session中获取用户信息
		// session_start();

		//加载头部文件
		include_once ADMIN_TEMP.'/top.html';
	}elseif($act == 'menu'){
		//加载菜单文件
		include_once ADMIN_TEMP.'/menu.html';
	}elseif($act == 'drag'){
		//加载滚动条文件
		include_once ADMIN_TEMP.'/drag.html';
	}elseif($act == 'main'){
		//加载主体文件
		include_once ADMIN_TEMP.'/main.html';
	}

