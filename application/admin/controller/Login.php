<?php
/**
 * 极客之家 高端PHP - 用户登录
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\controller;
use app\admin\model\UserType;
use think\Controller;
use think\Db;
use org\Verify;
use com\Geetestlib;
use think\Session;

class Login extends Controller
{
    public function test(){
        return $this->fetch('index');
    }

    /**
     * 登录页面
     * @return [type] [description]
     */
    public function index()
    {
        return $this->fetch('login');
    }

    /**
     * 登录操作
     * @return [type] [description]
     */
    public function doLogin()
    {
        $username = input("param.username");
        $password = input("param.password");

        if (config('verify_type') == 1) {
            $code = input("param.code");
        }
        // $result = $this->validate(compact('username', 'password'), 'AdminValidate');
        // print_r($result);die;
        // if(true !== $result){
        //     return json(['code' => -5, 'data' => '', 'msg' => $result]);
        // }
        $verify = new Verify();
        if (config('verify_type') == 1) {
            if (!$code) {
                return json(['code' => -4, 'data' => '', 'msg' => '请输入验证码']);
            }
            if (!$verify->check($code)) {
                return json(['code' => -4, 'data' => '', 'msg' => '验证码错误']);
            }
        }

        $hasUser = Db::name('admin')->where('username', $username)->find();//查询用户
        if(empty($hasUser)){
            return json(['code' => -1, 'data' => '', 'msg' => '管理员不存在']);
        }

        if(md5(md5($password) . config('auth_key')) != $hasUser['password']){
            writelog($hasUser['id'],$username,'用户【'.$username.'】登录失败：密码错误',2);
            return json(['code' => -2, 'data' => '', 'msg' => '账号或密码错误']);
        }

        if(1 != $hasUser['status']){
            writelog($hasUser['id'],$username,'用户【'.$username.'】登录失败：该账号被禁用',2);
            return json(['code' => -6, 'data' => '', 'msg' => '该账号被禁用']);
        }

        if (config('verify_type') != 1) {

            $GtSdk = new Geetestlib(config('gee_id'), config('gee_key'));
            $user_id = session('user_id');
            if (session('gtserver') == 1) {
                $result = $GtSdk->success_validate(input('param.geetest_challenge'), input('param.geetest_validate'), input('param.geetest_seccode'), $user_id);
                //极验服务器状态正常的二次验证接口
                if (!$result) {
                    $this->error('请先拖动验证码到相应位置');
                }
            }else{
                if (!$GtSdk->fail_validate(input('param.geetest_challenge'), input('param.geetest_validate'), input('param.geetest_seccode'))) {
                    //极验服务器状态宕机的二次验证接口
                    $this->error('请先拖动验证码到相应位置');
                }
            }

        }


        //获取该管理员的角色信息
        $user = new UserType();
        $info = $user->getRoleInfo($hasUser['groupid']);
        session('admin_username', $username);
        session('admin_uid', $hasUser['id']);
        session('rolename', $info['title']);  //角色名
        session('rule', $info['rules']);  //角色节点
        session('name', $info['name']);  //角色权限

        //更新管理员状态
        $param = [
            'loginnum' => $hasUser['loginnum'] + 1,
            'last_login_ip' => request()->ip(),
            'last_login_time' => time()
        ];

        Db::name('admin')->where('id', $hasUser['id'])->update($param);
        // echo session('admin_username');die;
        writelog($hasUser['id'],session('admin_username'),'用户【'.session('admin_username').'】登录成功',1);
        return json(['code' => 1, 'data' => url('index/index'), 'msg' => '登录成功！']);
    }

    /**
     * 验证码
     * @return [type] [description]
     */
    public function checkVerify()
    {
        $verify = new Verify();
        $verify->imageH = 32;
        $verify->imageW = 100;
		$verify->codeSet = '0123456789';
        $verify->length = 4;
        $verify->useNoise = false;
        $verify->fontSize = 14;
        return $verify->entry();
    }


    /**
     * 极验验证
     * @return [type] [description]
     */
    public function getVerify(){
        $GtSdk = new Geetestlib(config('gee_id'), config('gee_key'));
        $user_id = "web1";
        $status = $GtSdk->pre_process($user_id);
        session('gtserver',$status);
        session('user_id',$user_id);
        echo $GtSdk->get_response_str();
    }



    /**
     * 退出操作
     * @return [type] [description]
     */
    public function loginOut()
    {
        session(null);
       return json(array('code'=>1));
    }
}
