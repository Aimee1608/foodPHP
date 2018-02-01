<?php

namespace app\admin\controller;
use app\admin\model\NodeModel;
use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        if(!session('admin_uid')){
            $this->redirect(url('login/index'));
        }
        
        $auth = new \com\Auth();
    
        $module     = strtolower(request()->module());
        $controller = strtolower(request()->controller());
        $action     = strtolower(request()->action());
        $url        = $module."/".$controller."/".$action;
        //跳过检测以及主页权限
        if(session('admin_uid')!=1){
            if(!in_array($url, ['admin/index/index','admin/index/center','admin/index/indexpage'])){
                if(!$auth->check($url,session('admin_uid'))){
                    //$this->error('抱歉，您没有操作权限');
                    // $ErrorData = array("start"=>1003,"msg"=>"抱歉，您没有操作权限");
                    // $this->assign('ErrorData' , $ErrorData);  //  data为参数，$list为输出的内容
                   echo "<script>alert('抱歉，您没有操作权限');</script>";
                   exit;
                }
            }
        }

        $node = new NodeModel();
        //if(config('template')['theme_name'] == "new"){
        $menu_list=json_encode($node->getMenu(session('rule')));
       // }else{
            //$menu_list=$node->getMenu(session('rule'));
        //}


        $this->assign([
            'username' => session('admin_username'),
            // 'menu' => $menu_list,
            'rolename' => session('rolename')
        ]);

    }
}