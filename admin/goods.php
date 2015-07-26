<?php
	
	//处理商品操作
	//获取用户的请求动作，用户请求当中携带的参数
	$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'list';

	//加载公共文件
	include_once 'includes/init.php';

	//判断用户的动作
	if($act == 'list'){
		//查看所有商品信息
		$page = isset($_GET['page']) ? $_GET['page'] : 1;

		//获得所有的商品信息
		$goods = new Goods();
		$lists = $goods->getAllGoods($page);

		$pagecounts = $goods->getPageCounts();
		//获取分页信息
		$page = Page::show('goods.php?act=list',$pagecounts,$page);

		//加载模板文件
		include_once ADMIN_TEMP . '/goods_list.html';
	}elseif ($act == 'remove') {
		//商品加入到回收站
		$g_id = isset($_GET['id']) ? $_GET['id'] : 0;

		//验证数据合法性
		if($g_id == 0){
			//没有选中要删除的商品
			admin_redirect('goods.php?act=list','没有选中要删除的商品',3);
		};


		//移除商品
		$goods = new Goods();
		if($goods->removeGoodsById($g_id)){
			//加入回收站成功
			admin_redirect('goods.php?act=trash','商品加入回收站成功！',2);
		}else{
			//失败
			admin_redirect('goods.php?act=list','商品加入回收站失败！',3);
		}
	}elseif ($act == 'trash') {
		//商品回收站显示数据
		//获取当前页码
		$page = isset($_GET['page']) ? $_GET['page'] : 1;

		//获取数据，只获取已经加入到回收站的商品数据（g_is_delete = 1）
		$goods = new Goods();
		$lists = $goods->getAllGoods($page,1);

		//获取所有记录数
		$pagecounts = $goods->getPageCounts(1);

		//加载分页数据
		$page  = Page::show('goods.php?act=trash',$pagecounts,$page);

		//加载模板文件
		include_once ADMIN_TEMP . '/goods_trash.html';
	}elseif ($act == 'restore') {
		//商品加入到回收站
		$g_id = isset($_GET['id']) ? $_GET['id'] : 0;

		//验证数据合法性
		if($g_id == 0){
			//没有选中要还原的商品
			admin_redirect('goods.php?act=trash','没有选中要还原的商品',3);
		}

		//直接还原商品
		$goods = new Goods();
		if($goods->restoreGoodsById($g_id)){
			//成功
			admin_redirect('goods.php?act=list','商品还原成功！',2);
		}else{
			//失败
			admin_redirect('goods.php?act=trash','商品还原失败！',3);
		}
	}elseif ($act == 'delete') {
		$g_id = isset($_GET['id']) ? $_GET['id'] : 0;

		if($g_id == 0){
			//没有选中要还原的商品
			admin_redirect('goods.php?act=trash','没有选中要还原的商品',3);
		}

		//直接还原商品
		$goods = new Goods();
		if($goods->deleteGoodsById($g_id)){
			//成功
			admin_redirect('goods.php?act=trash','商品删除成功！',2);
		}else{
			//失败
			admin_redirect('goods.php?act=trash','商品删除失败！',3);
		}

	}elseif ($act == 'add') {
		
		//获取所有商品分类
		$category = new Category();
		//调用方法获取
		$categories = $category->getAllCategories();

		//添加模板
		include_once ADMIN_TEMP . '/goods_add.html';
	}elseif ($act == 'insert') {
		//接收数据
		$goodsinfo['g_name'] = isset($_POST['goods_name']) ? $_POST['goods_name'] : '';
		$goodsinfo['g_sn'] = isset($_POST['goods_sn']) ? $_POST['goods_sn'] : '';
		$goodsinfo['c_id'] = isset($_POST['category_id']) ? $_POST['category_id'] : 0;
		$goodsinfo['g_price'] = isset($_POST['shop_price']) ? $_POST['shop_price'] : 0;
		$goodsinfo['g_desc'] = isset($_POST['goods_desc']) ? $_POST['goods_desc'] : '';
		$goodsinfo['g_inv'] = isset($_POST['goods_number']) ? $_POST['goods_number'] : 0;
		$goodsinfo['g_is_pro'] = isset($_POST['is_promote']) ? $_POST['is_promote'] : 0;
		$goodsinfo['g_is_new'] = isset($_POST['is_new']) ? $_POST['is_new'] : 0;
		$goodsinfo['g_is_hot'] = isset($_POST['is_hot']) ? $_POST['is_hot'] : 0;
		$goodsinfo['g_is_sale'] = isset($_POST['is_on_sale']) ? $_POST['is_on_sale'] : 0;
		$goodsinfo['g_sort'] = isset($_POST['sort_order']) ? $_POST['sort_order'] : 50;
		//图片信息是需要服务器接收文件处理后被赋值
		$goodsinfo['g_img'] = '';
		$goodsinfo['g_thumb_img'] = '';
		$goodsinfo['g_water_img'] = '';
		$goodsinfo['g_is_delete'] = 0;			//默认商品添加就是正常商品

		//合法性验证：名称，分类ID
		if(empty($goodsinfo['g_name'])){
			//商品名称为空
			admin_redirect('goods.php?act=add','商品名称不能为空！',3);
		}

		if(strlen($goodsinfo['g_name']) > 60){
			//超长
			admin_redirect('goods.php?act=add','商品名称过长，只能最多输入20个字符！',3);
		}

		//商品分类id验证
		if($goodsinfo['c_id'] == 0){
			//没有选择分类
			admin_redirect('goods.php?act=add','没有选择商品分类！',3);
		}


		//应该对所有传进来的数据类型进行验证，尤其是数值类型。

		//验证数据有效性。
		//货号验证
		$goods = new Goods();
		if($goodsinfo['g_sn']){
			//货号存在，验证货号是否唯一
			if($goods->checkSn($goodsinfo['g_sn'])){
				//货号存在
				admin_redirect('goods.php?act=add',"当前货号 {$goodsinfo['g_sn']} 已经存在！",3);
			}
		}else{
			//货号不存在，自动增长货号
			$goodsinfo['g_sn'] = $goods->createAutoSn();
		}

		//接收图片并处理，不管图片是否上传成功，都不会影响整个商品记录的插入
		if($path = Upload::uploadSingle($_FILES['goods_img'],$config['goods_img_upload'],$config['goods_img_upload_max'])){
			//上传成功，将上传文件的相对路径存放到数据对应的字段下
			$goodsinfo['g_img'] = $path;
		}else{
			//上传失败，获取错误信息
			$error = Upload::$errorInfo;
		}

		//进行缩略图制作
		$image = new Image();
		if($thumb_path = $image->createThumb($goodsinfo['g_img'])){
			//成功
			$goodsinfo['g_thumb_img'] = $thumb_path;
		}


		//制作水印
		if($water_path = $image->createWatermark($goodsinfo['g_img'])){
			//成功
			$goodsinfo['g_water_img'] = $water_path;
		}	

		//插入到数据库
		if($goods->insertGoods($goodsinfo)){
			//插入成功
			//需要判断文件上传情况
			if(isset($error)){
				//文件上传失败
				admin_redirect('goods.php?act=list','新增商品成功！但是文件上传失败，失败原因是：' . $error,2);
			}else{
				//文件上传成功
				admin_redirect('goods.php?act=list','新增商品成功！',2);
			}
		}else{
			//插入失败
			admin_redirect('goods.php?act=add','商品新增失败！',3);
		}
	}
