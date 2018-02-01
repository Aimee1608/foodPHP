<?php

return [

    'url_route_on' => true,
    'url_route_must'  =>  false,
    //模板参数替换
    'view_replace_str' => array(
        '__CSS__' => '/public/static/admin/css',
        '__JS__'  => '/public/static/admin/js',
        '__IMG__' => '/public/static/admin/images',
		'__CS__'  =>'/public/static/css',
        '__JSS__'  =>'/public/static/js',
        '__IMAGE__'  =>'/public/static/images',
        '__PLUG__'  =>'/public/static/plugins',
        '__js__'   =>'/public/statisc/js',
        '__css__'   =>'/public/statisc/css',
        '__img__'   =>'/public/statisc/img',
        '__lay__'   =>'/public/statisc/layui',
        '__font__'   =>'/public/statisc/Font-Awesome/css',
    ),
    // 'template' => [
    //     // 模板引擎类型 支持 php think 支持扩展
    //     // 'view_path'    => './application/admin/view/default/',
    //     'view_path'    => './template/admin/',
    //     //'theme_name'   =>'default',
    //     'theme_name'   =>'default',
    // ],
];
