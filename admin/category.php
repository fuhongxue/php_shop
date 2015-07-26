<?php

	//商品分类处理
	// $act = isset($_GET['act']) ? $_GET['act'] : 'list';
	//数据有可能post也有可能来自get
	$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'list';

	//加载公共文件
	include_once 'includes/init.php';

	//判断用户动作，处理
	if($act == 'list'){
		//显示商品分类列表
		$category = new Category();
		//调用方法获取
		$categories = $category->getAllCategories();

		//加载显示模板
		include_once ADMIN_TEMP . '/category_list.html';
	}elseif($act == 'add'){
		//新增商品分类
		//获取所有商品分类
		$category = new Category();
		//调用方法获取
		$categories = $category->getAllCategories();


		//加载显示模板
		include_once ADMIN_TEMP . '/category_add.html';
	}elseif ($act == 'insert') {
		//用户提交新增数据
		//获取用户提交的数据
		$c_name = isset($_POST['category_name']) ? $_POST['category_name'] : '';
		$c_parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
		$c_sort = isset($_POST['sort_order']) ? $_POST['sort_order'] : 50;

		//数据合法性验证
		if (empty($c_name)) {
			//商品分类的名字不能为空
			admin_redirect('category.php?act=add','商品分类名字不能为空',2);
		}

		//判断数据是否合法
		if(!is_numeric($c_sort)){
			admin_redirect('category.php?act=add','排序字段只能为整数',2);
		}

		//判断数据的长度是否合法
		if (strlen($c_name) > 20) {
			admin_redirect('category.php?act=add','商品分类名称超过指定长度，长度不能超过20个字符',2);
		}

		//验证数据的有效性：同一级别（一个父级分类下）不允许同名
		$category = new Category();

		if($category->getCategoryByParentIdAndName($c_parent_id,$c_name)){
			//数据没有重复
			if($category->insertCategory($c_name,$c_parent_id,$c_sort)){
				//插入成功
				admin_redirect('category.php','插入成功！',2);
			}else{
				//插入失败
				admin_redirect('category.php?act=add','插入失败！',3);
			}
		}else{
			//已经存在数据
			admin_redirect('category.php?act=add','当前商品分类已经存在！',3);
		}
	}elseif ($act == 'delete') {
		$c_id = isset($_GET['id']) ? $_GET['id'] : '';

		if ($c_id == 0) {
			admin_redirect('category.php?act=add','没有选中要删除的商品分类',2);
		}

		//验证商品分类是否可以被删除
		$category = new Category();
		$res = $category->isDelete($c_id);

		//判断结果
		if($res === true){
			//可以删除
			if($category->deleteCategory($c_id)){
				//删除成功
				admin_redirect('category.php','删除成功！',1);
			}else{
				//删除失败
				admin_redirect('category.php','删除失败！',3);
			}
		}else{
			//不能删除
			admin_redirect('category.php','不能删除，原因是：'  . $res,3);
		}
	}elseif ($act == 'edit') {
		//编辑商品分类
		//获取商品分类id
		$c_id = isset($_GET['id']) ? $_GET['id'] : 0;

		//判断数据合法性
		if($c_id == 0){
			//用户没有传入ID
			admin_redirect('category.php','没有选中要编辑的商品分类！',3);
		}

		//获取商品分类信息
		$category = new Category();
		if(!$cat = $category->getCategoryById($c_id)){
			//没有获取到数据
			admin_redirect('category.php','当前商品分类编辑失败！',3);
		}

		//获取所有的商品分类
		$categories = $category->getAllCategories($c_id);

		include_once ADMIN_TEMP . '/category_edit.html';
	}elseif ($act == 'update') {

		//更新商品分类
		//接收商品分类数据
		$c_name = isset($_POST['category_name']) ? $_POST['category_name'] : '';
		$c_parent_id = isset($_POST['parent_id']) ? $_POST['parent_id'] : 0;
		$c_sort	= isset($_POST['sort_order']) ? $_POST['sort_order'] : 50;
		$c_id = isset($_POST['c_id']) ? $_POST['c_id'] : 0;

		//数据合法性验证
		if($c_id == 0){
			//没有要更新的数据
			admin_redirect('category.php','没有要更新的商品分类信息！',3);
		}

		if(empty($c_name)){
			//商品分类的名字不能为空
			admin_redirect('category.php?act=edit&id='.$c_id,'商品分类名字不能为空！',3);
		}

		//判断数据是否合法
		if(!is_numeric($c_sort)){
			//数据不合法
			admin_redirect('category.php?act=edit&id='.$c_id,'排序字段只能为整数！',3);
		}

		//判断数据的长度是否合法
		if(strlen($c_name) > 60){
			//数据长度不合法
			admin_redirect('category.php?act=edit&id='.$c_id,'商品分类名称超过指定长度，长度不能超过20个字符！',3);
		}

		//数据更新
		$category = new Category();
		if($category->updateCategory($c_id,$c_name,$c_parent_id,$c_sort)){
			//成功
			admin_redirect('category.php','更新成功！',2);
		}else{
			//失败
			admin_redirect('category.php?act=edit&id='.$c_id,'更新失败！',3);
		}
	}
