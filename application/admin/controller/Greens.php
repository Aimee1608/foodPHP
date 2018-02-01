<?php
/**
 * 极客之家 高端PHP - 菜单模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\controller;
use think\Controller;
use think\Config;
use think\Db;
use think\Session;
use think\Cache;
use app\admin\model\GreensModel;
use app\admin\model\BaseModel;
class Greens extends Base
{
    /**
     * [greens_list] 菜单列表
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_list()
    {
        $page = input('get.page') ? input('get.page') : 1;//当前页
        $greens = new GreensModel();
        $data = $greens->greens_list($page);

        $count = $data['count'];
        $allpage = $data['allpage'];
        $lists = $data['lists'];
        foreach ($lists as $key => $val) {
            $lists[$key]['describe'] = mb_strlen($val['describe'], 'utf-8') > 10 ? mb_substr($val['describe'], 0, 10, 'utf-8').'....' : $val['describe'];
            if(empty($val['truename']))
            {
                $lists[$key]['truename'] = 0;
            }
        }
        // p($lists);
        $this->assign([
            'count'  => $count,//总条数
            'allpage' => $allpage,//总页面
        ]);
        if(input('get.page')){
            return json($lists);//数据
        }
        return $this->fetch();
    }

    /**
     * [greens_add] 添加菜单
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_add()
    {
    	$greens = new GreensModel();
        if(request()->isAjax())
        {
            $data = input("param.");

            //1. 这里要移动封面图片位置到正式上传目录 生成正式路径
            $base  = new BaseModel;
            $data['img'] = $base->MoveFile("uploads",$data['img']);//最终封面图片
            //2. 在移动步骤图片到正式目录
            $stepImg = explode(",",$data['zyfile']);
            // $new_stepimg = array();
            foreach ($stepImg as $val) {
                $new_stepimg[] = $base->MoveFile("uploads",'/'.$val)['data'];
            }
            $data['thumbnail'] = serialize(implode(",",$new_stepimg));//最终步骤图片地址
            //3. 重新组合食材数组 排列
            foreach($data['food_name'] as $key => $val)
            {
              $inventory[$key]['food_name'] = $data['food_name'][$key];
              $inventory[$key]['food_how'] = $data['food_how'][$key];
            }
            $data['inventory'] = serialize($inventory);
            $data['author'] = DB::name("user")->where("uid",$data['uid'])->field("uid,nickname")->find()['nickname'];
            $flag = $greens->GreensAdd($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
    	    $DataClass = $greens->ClassData(); //菜单分类
          $Administrator = $greens->AdministratorList(); //查询超级管理员
    	    $this->assign("DataClass",$DataClass);
    	    $this->assign("Administrator",$Administrator);
          return $this->fetch();
    }

     /**
     * [greens_update] 修改菜单
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_update()
    {
        $id = input("param.id");
        $greens = new GreensModel();
        $base  = new BaseModel;
        if(request()->isAjax())
        {
            $data = input("param.");
            // p($data);
            //1. 这里要移动封面图片位置到正式上传目录 生成正式路径
            //判断一下如果图片路径为正式路径，不用进行移动文件 【这里图片正式路径为uploads 临时路径为testfile】
            $imgstr = explode("/",$data['img']);
            if(in_array("testfile", $imgstr))
            {
                //如果重新上传了图片，需要将原来图片删除
                //1.首先要获取原来图片路径
                $test_url = $greens->TestfileFind($data['c_id'])['img'];
                // echo $test_url;die;
                //开始移动图片路径位置
                $data['img'] = $base->MoveFile("uploads",$data['img'],$test_url)['data'];//最终封面图片
            }

            if($data['file_start'] == 1)
            {//说明图片步骤重新上传了 1的话
                //2. 在移动步骤图片到正式目录
                $stepImg = explode(",",$data['zyfile']);

                //删除原来图片文件
                $test_url = explode(",",unserialize($greens->TestfileFind($data['c_id'])['thumbnail']));
                foreach ($test_url as $key => $val) {
                   $base->DelWormFile($val);
                }

                foreach ($stepImg as $key => $val) {
                    // print_r($test_url[$key]);
                    $new_stepimg[] = $base->MoveFile("uploads",'/'.$val)['data'];
                }

                $data['thumbnail'] = serialize(implode(",",$new_stepimg));//最终步骤图片地址
            }
            else
            {
                $data['thumbnail'] = serialize($data['zyfile']);//原来步骤图片地址
            }


            //3. 重新组合食材数组 排列
            foreach($data['food_name'] as $key => $val)
            {
              $inventory[$key]['food_name'] = $data['food_name'][$key];
              $inventory[$key]['food_how'] = $data['food_how'][$key];
            }
            $data['inventory'] = serialize($inventory);
            $flag = $greens->GreensSave($data,$data['c_id']);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $DataStr = DB::name("veriety")->where("id",$id)->find();//查询当前修改的数据
        $DataClass = $greens->ClassData();//查询分类数据
        $DataStr['inventory'] = unserialize($DataStr['inventory']);
        $DataStr['thumbnail'] = unserialize($DataStr['thumbnail']);
        $DataStr['thumbnailcount'] = count(explode(",",$DataStr['thumbnail']));
        // p($DataStr);
        $this->assign("DataClass",$DataClass);
        $this->assign("DataStr",$DataStr);
        $this->assign("c_id",$id);//单前菜单ID
        return $this->fetch();
    }

   /**
     * [foodDel] 删除菜单
     * @param  [string] $id [接受菜单id]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function foodDel()
    {
        $id = input('param.id');
        $greens = new GreensModel();
        $flag = $greens->foodDel($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [greens_add] 修改菜单状态
     * @param  [string] $id [接受菜单id]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function food_state(){
        $id = input('param.id');
        $status = Db::name('veriety')->where(array('id'=>$id))->value('status');//判断当前状态情况
        if($status == 1)
        {
            $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已关闭']);
        }
        else
        {
            $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已发布']);
        }
    }

    /**
     * [recommend_status] 修改菜单今日推荐状态
     * @param  [string] $id [接受菜单id]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function recommend_status(){
        $id = input('param.id');
        $status = Db::name('veriety')->where(array('id'=>$id))->value('recommend');//判断当前状态情况
        if($status == 1)
        {
            $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['recommend'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已关闭']);
        }
        else
        {
            $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['recommend'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已推荐']);
        }
    }

    /**
     * [greens_audit] 菜单审核
     * @param  [string] $id [接受菜单id]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_audit()
    {
         $id = input("param.id");
         $greens = new GreensModel();
         $base  = new BaseModel;
         $DataStr = DB::name("veriety")->where("id",$id)->find();//查询当前修改的数据
         $DataClass = $greens->ClassData();//查询分类数据
         $DataStr['inventory'] = unserialize($DataStr['inventory']);
         $DataStr['thumbnail'] = explode(",",unserialize($DataStr['thumbnail']));
         $this->assign("DataClass",$DataClass);
         $this->assign("DataStr",$DataStr);
         $this->assign("c_id",$id);//单前菜单ID
         return $this->fetch();
    }

    /**
     * [greens_adopt] 菜单通过
     * @param  [string] $id [接受菜单id]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_adopt()
    {
        $id = input('param.id');
        $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['audit_start'=>1,'audit_time'=>time()]);
        $greensName = DB::name("veriety")->where("id",$id)->field("name")->find()['name'];
        if(true == $flag)
        {
            writelog(session('admin_uid'),session('admin_username'),"超级管理员【".session('admin_username')."】审核通过"."菜单【".$greensName."】",1);
            exit(json_encode(['code' => 1, 'data' => '', 'msg' => '已通过']));
        }
        else
        {
            writelog(session('admin_uid'),session('admin_username'),"超级管理员【".session('admin_username')."】审核通过"."菜单【".$greensName."】",2);
            return json(['code' => 0, 'data' => '', 'msg' => '操作失败']);
        }
    }

    /**
     * [greens_reject] 菜单驳回
     * @param  [string] $id [接受菜单id]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_reject()
    {
        $id = input('param.id');
        $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['audit_start'=>2,'status'=>0,'recommend'=>0,'audit_time'=>time()]);
        $greensName = DB::name("veriety")->where("id",$id)->field("name")->find()['name'];
        if(true == $flag)
        {
            writelog(session('admin_uid'),session('admin_username'),"审核驳回"."菜单【".$greensName."】",1);
            exit(json_encode(['code' => 1, 'data' => '', 'msg' => '驳回成功']));
        }
        else
        {
            writelog(session('admin_uid'),session('admin_username'),"审核驳回"."菜单【".$greensName."】",2);
            return json(['code' => 0, 'data' => '', 'msg' => '驳回失败']);
        }
    }
}
