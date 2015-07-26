<?php
	
	//封装一个DB类，用来专门操作数据库
	class DB{
		// 属性
		private $host;
		private $port;
		private $user;
		private $pass;
		private $dbname;
		private $charset;
		private $prefix; //表前缀
		private $link;
		//构造方法，初始化对象的属性
		/*
		 * @param1 array $arr,默认为空，里面是一个关联数组,里面有7个元素
		 * array('host'==>'localhost','port'==>'3306');
		 */
		public function __construct($arr = array()){
			$this->host = isset($arr['host']) ? $arr['host'] : $GLOBALS['config']['mysql']['host'];
			$this->port = isset($arr['port']) ? $arr['port'] : $GLOBALS['config']['mysql']['port'];
			$this->user = isset($arr['user']) ? $arr['user'] : $GLOBALS['config']['mysql']['user'];
			$this->pass = isset($arr['pass']) ? $arr['pass'] : $GLOBALS['config']['mysql']['pass'];
			$this->dbname = isset($arr['dbname']) ? $arr['dbname'] : $GLOBALS['config']['mysql']['dbname'];
			$this->charset = isset($arr['charset']) ? $arr['charset'] : $GLOBALS['config']['mysql']['charset'];
			$this->prefix = isset($arr['prefix']) ? $arr['prefix'] : $GLOBALS['config']['mysql']['prefix'];

			//连接数据库
			$this->connect();

			//设置字符集
			$this->setCharset();

			//选择数据库
			$this->setDbname();

			//获取当前表的所有字段信息
			$this->getFields();
		}

		/*
		 * 连接数据库
		 */
		private function  connect(){
			//mysql扩展连接
			$this->link = mysql_connect($this->host.':'.$this->port,$this->user,$this->pass);

			if(!$this->link){
				//结果出错
				echo '数据库连接错误：<br/>';
				echo "错误编号".mysql_errno().'<br/>';
				echo "错误内容".mysql_error().'<br/>';
				exit;
			}
		}

		/*
		 * 设置字符集
		*/
		private function setCharset(){
			$this->db_query("set names {$this->charset}");
		}

		/*
		 * 选择数据库
		*/
		private function setDbname(){
			$this->db_query("use {$this->dbname}");
		}

		/*
		 * 增加数据
		 * @param1 string $sql,要执行插入语句
		 * @return boolean,成功返回的是自动增长的id，失败返回false
		*/
		public function db_insert($sql){
			//发送数据
			$this->db_query($sql);

			//成功返回自增id
			return mysql_affected_rows() ? mysql_insert_id() : FALSE;
		}

		/*
		 * 删除数据
		 * @param1 string $sql,要执行的删除语句
		 * @return Boolean,成功返回受影响的行数,失败返回FALSE
		*/
		public function db_delete($sql){
			$this->db_query($sql);

			return mysql_affected_rows() ? mysql_affected_rows() : FALSE;
		}

		/*
		 * 更新数据
		 * @param1 string $sql,要执行的更新语句
		 * @return Boolean,成功返回受影响的行数,失败返回FALSE
		*/
		public function db_update($sql){
			$this->db_query($sql);

			return mysql_affected_rows() ? mysql_affected_rows() : FALSE;
		}

		/*
		 * 查询：查询一条记录
		 * @param1 string $sql,要查询的sql语句
		 * @return mixed, 成功返回数组，失败返回FALSE
		*/
		public function db_getRow($sql){
			$res = $this->db_query($sql);

			return mysql_num_rows($res) ? mysql_fetch_assoc($res) : array();
		}

		/*
		 * 查询：查询多条记录
		 * @param1 string $sql,要查询的sql语句
		 * @return mixed, 成功返回二维数组，失败返回FALSE
		*/
		public function db_getAll($sql){
			$res = $this->db_query($sql);

			$list = array();
			if(mysql_num_rows($res)){
				while ($row = mysql_fetch_assoc($res)) {
					$list[] = $row;
				}
			}

			return $list;
		}

		/*
		 * mysql_query错误处理
		 * @param1 string $sql,需要执行的sql语句
		 * @return mixed，只要语句不出错，全部返回
		*/
		private function db_query($sql){

			$res = mysql_query($sql);

			if(!$res){
				//结果出错
				echo '语句出现错误：<br/>';
				echo "错误编号".mysql_errno().'<br/>';
				echo "错误内容".mysql_error().'<br/>';
				exit;
			}

			return $res;
		}

		
		/*
		 * 获取完整的表名
		*/
		protected function getTableName(){
			return $this->prefix.$this->table;
		}


		/*
		 * 获取字段信息
		*/
		private function getFields(){
			//组织SQL语句
			$sql = "desc {$this->getTableName()}";

			//执行
			$res = $this->db_getAll($sql);

			//遍历二维数组
			foreach($res as $field){
				$this->fields[] = $field['Field'];
				//判断当前字段是否是主键
				if($field['Key'] == 'PRI'){
					$this->fields['_PK'] = $field['Field'];
				}
			}
		}
	}

