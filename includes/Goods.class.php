<?php

	//商品处理类
	class Goods extends DB{
		//属性
		protected $table = 'goods';
		protected $fields;

		/*
		 * 获取所有商品信息
		 * @param1 int $page，当前要获取的页码
		 * @param2 int $type，商品的类型（正常还是回收站）
		 * @return mixed，成功返回商品数组（二维），失败返回FALSE
		*/
		public function getAllGoods($page,$type = 0){
			//求出limit所需要的起始位置和长度
			$length = $GLOBALS['config']['admin_goods_pagecounts'];
			$start  = ($page - 1) * $length;

			//组织SQL
			$sql = "select * from {$this->getTableName()} where g_is_delete = '{$type}' limit {$start},{$length}";

			//执行
			return $this->db_getAll($sql);
		}

		/*
		 * 获取当前商品的所有记录
		 * @param1 int $type，商品的类型，默认为0表示正常数据
		 * @return 返回当前商品的所有记录
		*/
		public function getPageCounts($type = 0){
			$sql = "select count(*) pagecounts from {$this->getTableName()} where g_is_delete = '{$type}'";
			$res = $this->db_getRow($sql);
			//返回
			return $res ? $res['pagecounts'] : false; 
		}

		/*
		 * 移动商品到回收站
		 * @param1 int $g_id，要移动的商品的ID
		 * @return Boolean，成功返回TRUE，失败返回FALSE
		*/
		public function removeGoodsById($g_id){
			//组织SQL
			$sql = "update {$this->getTableName()} set g_is_delete = 1 where g_id = '{$g_id}'";

			//执行SQL
			return $this->db_update($sql);
		}

		/*
		 * 还原商品
		 * @param1 int $g_id，要还原的商品的ID
		 * @return Boolean，成功返回TRUE，失败返回FALSE
		*/
		public function restoreGoodsById($g_id){
			//组织SQL
			$sql = "update {$this->getTableName()} set g_is_delete = 0 where g_id = '{$g_id}'";

			//执行SQL
			return $this->db_update($sql);
		}
		/*
		 * 还原商品
		 * @param1 int $g_id，要还原的商品的ID
		 * @return Boolean，成功返回TRUE，失败返回FALSE
		*/
		public function deleteGoodsById($g_id){
			//组织SQL
			$sql = "update {$this->getTableName()} set g_is_delete = -1 where g_id = '{$g_id}'";

			//执行
			return $this->db_update($sql);
		}

		/*
		 * 验证货号
		 * @param1 string $g_sn，要验证的货号
		 * @return array 直接使用父类的返回值，没有数据返回空数组
		*/
		public function checkSn($g_sn){
			//防SQL注入
			$g_sn = addslashes($g_sn);

			//组织SQL
			$sql = "select g_id from {$this->getTableName()} where g_sn = '{$g_sn}' limit 1";

			//执行
			return  $this->db_getRow($sql);
		}

		/*
		 * 自动生成新的货号、
		 * @return string，新生成的货号
		*/
		public function createAutoSn(){
			//1.获取到当前最大的货号
			$sql = "select g_sn from {$this->getTableName()} order by g_sn desc limit 1";

			//2.获得结果
			$old_sn = $this->db_getRow($sql)['g_sn'];

			//3.截取货号
			$num = substr($old_sn,5);

			//4.实现自增
			$num = (integer)$num;		//强制转换
			$num++;

			//5.拼凑货号，暂不考虑数据超过大小的问题
			return 'GOODS' . str_pad($num,5,'0',STR_PAD_LEFT);
		}

		/*
		 * 插入数据
		 * @param1 array $goodsinfo，要插入的数据的数组
		 * @return，成功返回自增ID，失败返回FALSE
		*/
		public function insertGoods($goodsinfo){
			//拼接SQL语句
			$sql = "insert into {$this->getTableName()}";

			//回调函数
			function addQuote($n){
				return "'" . $n . "'";
			}

			//给所有的数组元素添加单引号
			//遍历$goodsinfo，将得到的每一个元素的值调用addQuote方法，并把值传进去，最后把返回的结果重新赋值给元素下标对应的值
			$goodsinfo = array_map('addQuote',$goodsinfo);

			//遍历字段
			$fields = $values = '';
			foreach($goodsinfo as $key => $value){
				//拼凑字段和值列表
				$fields .= $key . ',';
				$values .= $value . ',';
			}

			//去除最右边的都好
			$fields = rtrim($fields,',');
			$values = rtrim($values,',');

			//拼凑SQL语句
			$sql .= " ({$fields}) values ({$values})";
			//echo $sql;exit;

			//执行
			return $this->db_insert($sql);
		}
	}
