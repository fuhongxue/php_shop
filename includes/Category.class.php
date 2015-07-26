<?php

	class Category extends DB{
		//属性
		protected $table = 'category';

		/*
		 * 获取所有的商品分类
		 * @param1 int $stop_id，需要终止查询的商品分类的ID
		 * @return mixed，成功返回二维数组，失败返回FALSE
		*/
		public function getAllCategories($stop_id = 0){
			//组织sql
			$sql = "select * from {$this->getTableName()} order by c_sort";

			$categories = $this->db_getAll($sql);

			//调用无限级分类进行处理
			return  $this->noLimitCategory($categories,0,0,$stop_id);
		}

		/*
		 * 无限级分类
		 * @param1 array $categories，需要进行无限级分类的数组
		 * @param2 int $parent_id，当前需要查询的顶级分类的id，默认为0，表示顶级分类
		 * @param3 int $level，默认0，表示是第一层
		 * @param4 int $stop_id，默认0，表示获取全部
		 * @return array，返回一个已经进行分类的数组
		*/
		private function noLimitCategory($categories,$parent_id = 0,$level = 0,$stop_id = 0){
			//定义一个静态数组用于保存每次遍历得到的结果
			static $res = array();
			
			//遍历数组进行数据判断
			foreach($categories as $value){
				//判断数据的父分类ID
				if($value['c_parent_id'] == $parent_id){
					if($value['c_id'] != $stop_id){
						//将当前层级遍历得到的记录都加上一个level元素
						$value['level'] = $level;

						//是要找的类容
						$res[] = $value;

						//递归点：当前分类有可能有子分类
						$this->noLimitCategory($categories,$value['c_id'],$level + 1,$stop_id);
					}
				}
			}

			//返回最终的结果
			return $res;
		}

	/*
	 * 通过父级ID和分类名字验证数据有效性
	 * @param1 int $c_parent_id，父级ID
	 * @param2 string $c_name，当前分类的名称
	 * @return boolean，有返回FALSE，没有返回true
	*/
	public function getCategoryByParentIdAndName($c_parent_id,$c_name){
		//组织SQL语句
		$sql = "select * from {$this->getTableName()} where c_parent_id = '{$c_parent_id}' and c_name = '{$c_name}' limit 1";

		//调用父类方法
		return $this->db_getRow($sql) ? FALSE : TRUE;
	}

	/*
	 * 插入商品分类
	 * @param1 string $c_name，商品分类名称
	 * @param2 int $c_parent_id，父级分类ID
	 * @param3 int $c_sort，分类排序
	 * @return mixed，成功返回新增ID，失败返回FALSE
	*/
	public function insertCategory($c_name,$c_parent_id,$c_sort){
		//组织SQL
		$sql = "insert into {$this->getTableName()} values(null,'{$c_name}',default,'{$c_sort}','{$c_parent_id}')";

		//调用父类方法
		return $this->db_insert($sql);
	}

	/*
	 * 判断商品分类是否可以被删除
	 * @param1 int $c_id，要判断的商品分类ID
	 * @return mixed，成功返回true，失败返回原因
	*/
	public function isDelete($c_id){
		//当前商品分类不是末级分类不能删除
		$sql = "select * from {$this->getTableName()} where c_parent_id = '{$c_id}'";
		//执行
		if($this->db_getRow($sql)){
			//有子分类不能被删除
			return '不是末级分类';
		}else{
			//没有子分类
			//需要判断当前分类是否有商品
			$sql = "select * from {$this->getTableName()} where c_id = '{$c_id}' and c_inv > 0";

			//执行
			return $this->db_getRow($sql) ? '当前商品分类有商品' : true;
		}
	}


	/*
	 * 删除商品分类
	 * @param1 int $c_id，要删除的商品分类的ID
	 * @return bool，成功返回true，失败返回FALSE
	*/
	public function deleteCategory($c_id){
		//组织SQL
		$sql = "delete from {$this->getTableName()} where c_id = '{$c_id}' limit 1";

		//执行
		return $this->db_delete($sql);
	}

	/*
	 * 通过商品分类的ID获取商品分类信息
	 * @param1 int $c_id，商品分类的id
	 * @return mixed，成功返回商品分类信息，失败返回FALSE
	*/
	public function getCategoryById($c_id){
		//组织SQL
		$sql = "select * from {$this->getTableName()} where c_id = '{$c_id}' limit 1";

		//执行
		return $this->db_getRow($sql);
	}

	/*
	 * 更新商品分类信息
	 * param1 int $c_id，要更新的商品分类id
	 * param2 string $c_name，要更新的商品分类名称
	 * param3 int $c_parent_idid，要更新的商品分类父级id
	 * param4 int $c_sort，要更新的商品分类的排序
	 * @return boolean，成功返回true，失败返回FALSE
	*/
	public function updateCategory($c_id,$c_name,$c_parent_id,$c_sort){
		//组织SQL
		$sql = "update {$this->getTableName()} set c_name='{$c_name}',c_parent_id='{$c_parent_id}',c_sort = '{$c_sort}' where c_id = '{$c_id}'";

		//执行
		return $this->db_update($sql);
	}
}

