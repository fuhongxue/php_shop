<?php

	//验证码类
	class Captcha{
		//属性
		private $width;				//验证码图片宽度
		private $height;			//验证码图片高度
		private $length;			//验证码长度
		private $lines;				//干扰线数量
		private $pixels;			//干扰点数量
		private $color;				//各种颜色配置
		/*
			$color = array(
				'bg_min' => 200,
				'bg_max' => 255
			
			)
		*/
		private $font;				//字体大小
		private $string;			//目标字符串

		/*
		 * 构造方法
		 * @param1 array $arr，一个数组，里面包含了几乎所有的属性
		*/
		public function __construct($arr = array()){
			//初始化属性
			$this->width	= isset($arr['width']) ? $arr['width'] : 146;
			$this->height	= isset($arr['height']) ? $arr['height'] : 20;
			$this->length	= isset($arr['length']) ? $arr['length'] : 4;
			$this->lines	= isset($arr['lines']) ? $arr['lines'] : 5;
			$this->pixels	= isset($arr['pixels']) ? $arr['pixels'] : 200;
			$this->font		= isset($arr['font']) ? $arr['font'] : 5;
			$this->color['bg_min'] = isset($arr['bg_min']) ? $arr['bg_min'] : 200;
			$this->color['bg_max'] = isset($arr['bg_max']) ? $arr['bg_max'] : 255;
			$this->color['font_min'] = isset($arr['font_min']) ? $arr['font_min'] : 0;
			$this->color['font_max'] = isset($arr['font_max']) ? $arr['font_max'] : 100;
			$this->color['line_min'] = isset($arr['line_min']) ? $arr['line_min'] : 100;
			$this->color['line_max'] = isset($arr['line_max']) ? $arr['line_max'] : 150;
			$this->color['pixe_min'] = isset($arr['pixe_min']) ? $arr['pixe_min'] : 150;
			$this->color['pixe_max'] = isset($arr['pixe_max']) ? $arr['pixe_max'] : 200;
			$this->string	= isset($arr['string']) ? $arr['string'] : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
		}

		/*
		 * 获得验证码图片
		*/
		public function generate(){
			//1.	创建画布
			$im = imagecreatetruecolor($this->width,$this->height);

			//2.	背景颜色
			//分配
			$bg_color = imagecolorallocate($im,mt_rand($this->color['bg_min'],$this->color['bg_max']),mt_rand($this->color['bg_min'],$this->color['bg_max']),mt_rand($this->color['bg_min'],$this->color['bg_max']));
			//填充
			imagefill($im,0,0,$bg_color);

			//3.	获取验证码
			$captcha = $this->getCaptchaString();
			//分配颜色
			$str_color = imagecolorallocate($im,mt_rand($this->color['font_min'],$this->color['font_max']),mt_rand($this->color['font_min'],$this->color['font_max']),mt_rand($this->color['font_min'],$this->color['font_max']));

			//将验证码写入到图片
			//计算x和y坐标
			imagestring($im,$this->font,ceil($this->width / 2) - 20,ceil($this->height / 2) - 10,$captcha,$str_color);

			//4.	增加干扰线
			for($i = 0;$i < $this->lines;$i++){
				//分配颜色
				$line_color = imagecolorallocate($im,mt_rand($this->color['line_min'],$this->color['line_max']),mt_rand($this->color['line_min'],$this->color['line_max']),mt_rand($this->color['line_min'],$this->color['line_max']));

				//写入线段
				imageline($im,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$line_color);
			}

			//5.	增加干扰点
			for($i = 0;$i < $this->pixels;$i++){
				//分配颜色
				$pixel_color = imagecolorallocate($im,mt_rand($this->color['pixe_min'],$this->color['pixe_max']),mt_rand($this->color['pixe_min'],$this->color['pixe_max']),mt_rand($this->color['pixe_min'],$this->color['pixe_max']));

				//写入线段
				imagesetpixel($im,mt_rand(0,$this->width),mt_rand(0,$this->height),$pixel_color);
			}

			//6.	保存输出
			imagepng($im);

			//7.	释放资源
			imagedestroy($im);
		}

		/*
		 * 获取验证码字符串
		 * @return string，获得的随机字符串
		*/
		private function getCaptchaString(){
			//获取随机长度
			$captchaStr = '';
			for($i = 0;$i < $this->length;$i++){
				//获取随机字符串
				$captchaStr .= $this->string[mt_rand(0,strlen($this->string) - 1)];
			}

			//将生成的验证码存放到session中
			$_SESSION['captcha'] = $captchaStr;

			//返回
			return $captchaStr;
		}

		/*
		 * 验证用户提交的验证码
		 * @param1 string $captcha，用户提交的验证码
		 * @return bool，成功返回true，失败返回FALSE
		*/
		public static function checkCaptcha($captcha){
			//验证码不区分大小写
			return (strtolower($captcha) === strtolower($_SESSION['captcha']));
		}
	}
