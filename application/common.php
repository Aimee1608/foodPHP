<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//




/**
 * 高端PHP - 自定义函数
 *
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */

/**
 * 打印函数 打印关于变量的易于理解的信息。
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
if (! function_exists('p')) 
{
	function p($var)
	{   
	 	echo "<pre>";   
	  	print_r($var);   
	    exit;
	}
}

/**
 * 打印函数 打印关于变量的详细信息。
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
if (! function_exists('dd'))
{
	function dd($var)
	{    
		echo "<pre>";    
		var_dump($var);   
		exit;
	}
}

/**
 * echo 打印函数 输出一个或者多个字符串
 * @param  [type] $val [description]
 * @return [type]      [description]
 */
if (! function_exists('e'))
{
	function e($val)
	{ 
		echo "<pre>";    
		echo $val;
		exit;
	}
}