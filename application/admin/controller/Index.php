<?php
/**
 * 极客之家 高端PHP - 首页模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {

        return $this->fetch();
    }


    /**
     * [indexPage 后台首页]
     * @return [type] [description]
     * @author [jonny] [980218641@qq.com]
     */
    public function indexPage()
    {
        $info = array(
            'web_server' => $_SERVER['SERVER_SOFTWARE'],
            'onload'     => ini_get('upload_max_filesize'),
            'think_v'    => THINK_VERSION,
            'phpversion' => phpversion(),
        );

        $this->assign('info',$info);
        return $this->fetch('main');
    }
    public function center(){
        return $this->fetch();
    }
}
