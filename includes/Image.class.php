<?php
	
	class Image{
		//属性
		private $thumb_width;
		private $thumb_height;
		public $errorinfo;
		private $image_type = array(
			'gif' => 'gif',
			'png' => 'png',
			'jpg' => 'jpeg',
			'jpeg' => 'jpeg'
		);

		public function __construct($width = '',$height = ''){
			//判断用户是否传入参数
			$this->thumb_width = empty($width) ?  $GLOBALS['config']['goods_img_thumb_width'] :  $width ;
			$this->thumb_height = empty($height) ?  $GLOBALS['config']['goods_img_thumb_height'] : $height;
		}

		/*
		 * 根据图片制作缩略图
		 * @param1 string $file，缩略图原图资源
		 * @return mixed，成功返回缩略图路径，失败返回FALSE
		*/
		public function createThumb($file){
			//判断资源是否是文件
			if(!$extension = $this->checkFile($file)) return false;

			//缩略图制作
			//1.	获取原图资源
			//知道使用哪个函数//类似imagecreatefromjpeg
			$imagecreate = 'imagecreatefrom' . $this->image_type[$extension];
			$imagesave   = 'image' . $this->image_type[$extension];

			//利用可变函数获取图片资源
			$src = @$imagecreate($file);

			//创建缩略图资源
			$dst = imagecreatetruecolor($this->thumb_width,$this->thumb_height);

			//填充背景色
			$dst_bg = imagecolorallocate($dst,255,255,255);
			imagefill($dst,0,0,$dst_bg);

			//获取图片信息
			$fileinfo = getimagesize($file);

			//求出原图的宽高比和缩略图的宽高比
			$src_cmp = $fileinfo[0] / $fileinfo[1];					//浮点数
			$dst_cmp = $this->thumb_width / $this->thumb_height;	//浮点数

			//比较宽高比
			if($src_cmp > $dst_cmp){
				//确定宽高
				$width = $this->thumb_width;
				$height = floor($width / $src_cmp);
			}else{
				//确定宽高
				$height = $this->thumb_height;
				$width = floor($height * $src_cmp);
			}

			//求出缩略图中原始图的其实位置
			$dst_x = ceil(($this->thumb_width - $width) / 2);
			$dst_y = ceil(($this->thumb_height - $height) / 2);


			//2.	采样和复制
			if(imagecopyresampled($dst,$src,$dst_x,$dst_y,0,0,$width,$height,$fileinfo[0],$fileinfo[1])){
				//成功，保存图片
				$name = 'thumb_' . basename($file);

				if($imagesave($dst,ADMIN_UPL . '/' . $name)){
					//保存成功
					return './uploads/' . $name;
				}else{
					//失败
					$this->errorinfo = '缩略图保存失败！';
					return FALSE;
				}
			}else{
				//失败
				$this->errorinfo = '缩略图采样失败！';
				return false;
			}
		}

		/*
		 * 创建水印图
		 * @param1 string $file，要创建水印的图片
		 * @param2 int $position，水印图的位置，默认是5，表示右下角 
		 * @param4 int $pct，透明度，默认为30 
		 * @param4 string $water，水印图片，默认值为空，读配置文件
		 * @return string $path，水印图的路径
		*/
		public function createWatermark($file,$position = 5,$pct = 30,$water = ''){
			//判断目标文件是否正确
			if(!$extension = $this->checkFile($file)) return false;

			//确定水印图片
			if(!$water){
				//使用默认的水印图片
				$water = $GLOBALS['config']['goods_img_water'];
			}

			//判断水印图片
			if(!$water_ext = $this->checkFile($water)){
				$this->errorinfo = '水印图片资源不存在';	
				return false;
			}

			//制作水印
			//1.	确定图片资源获取的函数
			$dstcreate = 'imagecreatefrom' . $this->image_type[$extension];
			$watercreate = 'imagecreatefrom' . $this->image_type[$water_ext];
			$dstsave = 'image' . $this->image_type[$extension];

			//2.	获取图片资源
			$dst = @$dstcreate($file);
			$wat = @$watercreate($water);

			//3.	获取图片信息
			$dstinfo = getimagesize($file);
			$watinfo = getimagesize($water);

			//4.	计算水印在原图的坐标
			switch($position){
				case 1:
					//左上角
					$start_x = 0;
					$start_y = 0;
					break;
				case 2:
					//右上角
					$start_x = $dstinfo[0] - $watinfo[0];
					$start_y = 0;
					break;
				case 3:
					//中间位置
					$start_x = floor(($dstinfo[0] - $watinfo[0]) / 2);
					$start_y = floor(($dstinfo[1] - $watinfo[1]) / 2);
					break;
				case 4:
					//左下角
					$start_x = 0;
					$start_y = $dstinfo[1] - $watinfo[1];
					break;
				case 5:
				default:
					$start_x = $dstinfo[0] - $watinfo[0];
					$start_y = $dstinfo[1] - $watinfo[1];
			}

			//5.	采样合并
			if(@imagecopymerge($dst,$wat,$start_x,$start_y,0,0,$watinfo[0],$watinfo[1],$pct)){
				//成功，保存图片返回路径
				$name = 'water_' . basename($file);

				if(@$dstsave($dst,ADMIN_UPL . '/' . $name)){
					//成功
					//销毁资源
					imagedestroy($dst);
					imagedestroy($wat);

					return './uploads/' . $name;
				}else{
					//失败
					$this->errorinfo = '水印图片保存失败！';
				}
			}else{
				//失败
				$this->errorinfo = '水印图片合并失败！';
			}

			//销毁资源，返回FALSE
			@imagedestroy($dst);
			@imagedestroy($wat);
			return false;
		}

		/*
		 * 判断文件是否有效
		 * @param1 string $file，需要判断的文件
		*/
		private function checkFile($file){
			//判断资源是否是文件
			if(!is_file($file)){
				//不是文件
				$this->errorinfo = '不是一个有效的文件！';
				return FALSE;
			}

			//判断文件类型
			//获取文件的后缀名
			$extension = substr($file,strrpos($file,'.')+1); //不要点

			//判断
			if(!array_key_exists($extension,$this->image_type)){
				//不是一张合法的图片
				$this->errorinfo = '不是一个有效的图片！';
				return false;
			}

			//返回正确
			return $extension;
		}
	}
