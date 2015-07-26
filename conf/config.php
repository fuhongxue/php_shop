<?php

	//配置文件
	return array(
		'mysql' => array(
			//数据库连接配置选项
			'host' => 'localhost',
			'port' => '3306',
			'user' => 'root',
			'pass' => '1',
			'prefix' => 'sh_',
			'dbname' => 'shop',
			'charset' => 'utf8'
		),

		//后台商品每页显示的数量
		'admin_goods_pagecounts' => '5',

		//后台商品上传允许上传的MIME类型
		'goods_img_upload' => array(
			'image/gif',
			'image/pgif',
			'image/png',
			'image/jpg',
			'image/jpeg',
			'image/pjpeg',
		),

		'goods_img_upload_max' => 1000000,	//默认1M


		//缩略图配置
		'goods_img_thumb_width' => 100,
		'goods_img_thumb_height' => 100,

		//水印图片
		'goods_img_water' => ADMIN_ROOT . '/images/water.jpg',
	);
