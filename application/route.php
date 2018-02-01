<?php
use think\Route;
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];
Route::rule('FlowAdmin','/admin/index/index');//默认登录首页	
// Route::rule('ShowDealerData','/Flow/public/index.php/port/Userftlotter/ShowDealerData');//查询福特经销商数据
// Route::rule('RandomBroker','/Flow/public/index.php/port/Userftlotter/RandomBroker');//查询福特对应媒体渠道三个经纪人信息
// Route::rule('DealerDataVert','/Flow/public/index.php/port/Userftlotter/DealerDataVert');//查询福特对应经销商经纪人数据
// Route::rule('UserLotter','/Flow/public/index.php/port/Userftlotter/UserLotter');//福特用户留资