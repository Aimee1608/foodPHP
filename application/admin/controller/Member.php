<?php
/**
 * 极客之家 高端PHP - 会员模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\controller;

use think\Db;
use app\admin\model\MemberModel;
use app\admin\model\LevelModel;
class Member extends Base
{
    /*
     * 会员列表
     */
    public function index(){
        $username = input('username');
        $email = input('email');
        $map = [];
        if($username && $username !== ""){
            $map['username'] = ['like',"%" . $username . "%"];
        }

        if($email && $email !== ""){
            $map['email'] = ['like',"%" . $email . "%"];
        }

        $Nowpage = input('get.page') ? input('get.page'):1;

        $limits = 10;// 获取总条数
        $start = $limits * ($Nowpage - 1);
        $count = Db::name('user')->where($map)->count();//计算总页面
        $allpage = ceil($count / $limits);
        $member = new MemberModel();
        $lists = $member->getMemberByWhere($map, $start, $limits);
        // p($lists);
        // foreach($lists as $k=>$v){
        //     $lists[$k]['register_time']=date('Y-m-d H:i:s',$v['register_time']);
        //    // $lists[$k]['update_time']=date('Y-m-d H:i:s',$v['update_time']);
        // }
        $this->assign('Nowpage', $Nowpage); //当前页
        $this->assign('allpage', $allpage); //总页数
        $this->assign('count', $count);
       // $this->assign('val', $key);
        if(input('get.page')){
            //$article->getlastsql();
            return json($lists);
        }
        return $this->fetch();
    }

    /*
     * 添加会员
     */
    public function add_user(){
        if(request()->isAjax()){
            $param = input('post.');
            $param['password']=md5($param['password']);
            $param['register_time']=time();
            $member = new MemberModel();
            $flag = $member->insertMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $level_list=Db::name('user_leval')->select();//会员等级列表
        $this->assign('level_list',$level_list);
        return $this->fetch();
    }

    /*
     * 编辑会员
     */
    public function edit_user(){
        $member = new MemberModel();
        if(request()->isAjax()){

            $param = input('post.');
            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5($param['password']);
            }
            $flag = $member->updateMember($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');

        $level_list=Db::name('user_leval')->select();//会员等级列表
        $this->assign('level_list',$level_list);
        $this->assign('member',$member->getOneMember($id));
        return $this->fetch();
    }

    /*
     * 删除会员
     */
    public function del_user(){
        $id = input('param.id');
        $member = new MemberModel();
        $flag = $member->delMember($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }


    /*
     * 更改会员状态
     */
    public function member_state(){
        $id=input('param.id');
        $status = Db::name('user')->where(array('uid'=>$id))->value('status');//判断当前状态情况
        if($status == 1)
        {
            $flag = Db::name('user')->where(array('uid'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('user')->where(array('uid'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    }

    /**
     * [administrators_start]  [管理员状态]
     * @return [type] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function administrators_start(){
        $id = input('param.id');
        $status = Db::name('user')->where(array('uid'=>$id))->value('admin_start');//判断当前状态情况
        if($status == 1)
        {
            $flag = Db::name('user')->where(array('uid'=>$id))->setField(['admin_start'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('user')->where(array('uid'=>$id))->setField(['admin_start'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    }


    /*
     * 检测会员名是否存在
     */
    public function checkUsername(){
        $username=input('param.username');
        $id=input('param.id');
        $member=new MemberModel();
        $results=$member->getUserId(array('id'=>$id,'username'=>$username));
        if($results){
          return  json(0);
        }else{
            $res=$member->getUserId(array('username'=>$username));
            if($res){
                return  json(1);
            }else{
                return  json(0);
            }
        }
    }
    /*
     * 检测邮箱是否存在
     */
    public function checkEmail(){

        $email=input('param.email');
        $id=input('param.id');
        $member=new MemberModel();
        $results=$member->getUserId(array('id'=>$id,'email'=>$email));
        if($results){
            return  json(0);
        }else{
            $res=$member->getUserId(array('email'=>$email));
            if($res){
                return  json(1);
            }else{
                return  json(0);
            }
        }
    }

    /*
     * 会员等级列表
     */
    public function index_leval(){
        $leval=new LevelModel();
        $leval_list=$leval->getLevalList();
        $this->assign('list',$leval_list);

        return $this->fetch();
    }

    /*
     * 添加会员等级
     */
    public function add_leval(){

         if(request()->isAjax()){

             $param = input('post.');
             $leval = new LevelModel();
             $flag = $leval->addMemberLeavl($param);
             return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
         }

        return $this->fetch();
    }

    /*
     * 编辑会员等级
     */
    public function edit_leval(){
        $leval = new LevelModel();

        if(request()->isAjax()){

            $param = input('post.');
            $flag = $leval->editMemberLeavl($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $leval_list=$leval->getLevelInfo();
        $this->assign('leval',$leval_list);
        return $this->fetch();
    }
    /*
     * 删除会员等级
     */
    public function del_leval(){

        $id = input('param.id');
        $leval = new LevelModel();
        $flag = $leval->delMemberLeval($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /*
     * 更改会员等级状态
     */
    public function user_leave(){
        $id=input('param.id');
        $status = Db::name('user_leval')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('user_leval')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('user_leval')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    }
}
