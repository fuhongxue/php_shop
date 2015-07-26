<?php

	class Page{
		const PAGE_ALL = 0;		//默认使用全部分页内容
		const PAGE_STR = 1;		//
		const PAGE_CLI = 2;		//
		const PAGE_SEL = 3;		//
		const PAGE_STR_CLI = 4;		//
		const PAGE_STR_SEL = 5;		//
		const PAGE_CLI_SEL = 6;		//
		/*
		 * 分页方法
		 * @param1 string $basename，请求的脚本文件
		 * @param2 int $counts，总记录数
		 * @param3 int $page，当前页码
		 * @return string，具有分页点击a标签的字符串
		 *
		 * 示例：总共多少条记录，每页显示多少条记录，当前是第几页，<a>首页</a>，<a>前一页</a>，后一页，末页
		*/
		public static function show($basename,$counts,$page=1,$chose =0){
			//计算出总页数
			$pagesize = $GLOBALS['config']["admin_goods_pagecounts"];
			$pageCounts = ceil($counts / $pagesize);

			if($pageCounts == 0) return '';

			//计算上一页和下一页
			$prev = ($page == 1) ? $page : ($page - 1);
			$next = ($page == $pageCounts) ? $pageCounts : ($page + 1);

			//使用定界符来拼凑数据
			$str = <<<ENDF
				<span id="str_page">
				总共有{$counts}条数据，每页显示{$pagesize}条，当前是第{$page}页&nbsp;&nbsp;
				<a href="{$basename}&page=1">首页</a> 
				<a href="{$basename}&page={$prev}">上一页</a> 
				<a href="{$basename}&page={$next}">下一页</a> 
				<a href="{$basename}&page={$pageCounts}">末页</a>&nbsp;&nbsp; </span>
ENDF;

			//增加一个类似点击按钮，1,2,3,4,5
			//需求：1,2,3...---->1 ... 3,4,5 ...------->1 ... 5,6,7 ...
			$click = '<span id="click_page">';
			for ($i=1; $i <= $pageCounts; $i++) { 
				if($page > 2){
					if($i == 1)$click .= "<a href='{$basename}&page={$i}'>$i</a>&nbsp;&nbsp;...";
					else{
						if($page == $i){
							$click .= "<a href='{$basename}&page={$i}'>{$i}</a>&nbsp;&nbsp;";
							$temp = $i + 1;
							if ($temp <= $pageCounts) {
								$click .= "<a href='{$basename}&page={$temp}'>{$temp}</a>&nbsp;&nbsp;";
							}
							$temp = $i + 2;
							if ($temp <= $pageCounts) {
								$click .= "<a href='{$basename}&page={$temp}'>{$temp}</a>&nbsp;&nbsp;";
							}
						}
					}
				}else{
					//1,2,3 ...
					//点击是前2页
					//判断$pageCountss是否有三页
					$click .= "<a href='{$basename}&page=1'>1</a>&nbsp;&nbsp;";
					//判断是否有第二页
					if($pageCounts >= 2){
						$click .= "<a href='{$basename}&page=2'>2</a>&nbsp;&nbsp;";

						//判断是否有第三页
						if($pageCounts >= 3){
							$click .= "<a href='{$basename}&page=3'>3</a>&nbsp;&nbsp;";
						}
					}
					break;
				}

			}
			if($page <= $pageCounts - 3){
				$click .= '...</span>';
			}else{
				$click .= '</span>';
			}

			//select 下拉框分页
			$select = "<span id='select_page'>&nbsp&nbsp<select onchange=\"location.href='{$basename}&page='+this.value\">";
			//循环遍历
			for ($i=1; $i <= $pageCounts; $i++) { 
				if($i == $page){
					$select .= "<option value='{$i}' selected='selected'>$i</option>";		
				}else{
					$select .= "<option value='{$i}'>$i</option>";		
				}
			}

			$select .= '</select></span>';


			//返回当前拼凑好的分页
			//判断用户需求，来进行选择性返回
			switch($chose){
				case Page::PAGE_ALL:
				default:
					return $str . $click . $select;
				case Page::PAGE_STR:
					return $str;
				case Page::PAGE_CLI:
					return $click;
				case Page::PAGE_SEL:
					return $select;
				case Page::PAGE_CLI:
					return $str . $click;
				case Page::PAGE_STR_SEL:
					return $str . $select;
				case Page::PAGE_cl:
					return $click . $select;
			}
		}
	}
