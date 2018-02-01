<?php
/**
 * 极客之家 高端PHP - 广告管理模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\controller;
use app\admin\model\AdModel;
use app\admin\model\AdPositionModel;
use think\Db;

class Ad extends Base
{
    //*********************************************广告位*********************************************//
    /**
     * [index_position 广告位列表]
     * @return [type] [description]
     * @author [jonny] [980218641@qq.com]
     */
    public function focus_map()
    {
         $ad = new AdModel();
         $data = $ad->focus_map();
         $this->assign("list",$data);
        return $this->fetch();
    }



    /**
     * [add_position 添加广告位]
     * @return [type] [description]
     * @author [jonny] [980218641@qq.com]
     */
    public function add_focus()
    {
        if(request()->isAjax()){

            $param = input('post.');
            unset($param['file']);
            // p($param);
            $ad = new AdModel();
            $flag = $ad->add_focus($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        return $this->fetch();
    }


    /**
     * [edit_position 编辑广告位]
     * @return [type] [description]
     * @author [jonny] [980218641@qq.com]
     */
    public function edit_focus()
    {
        $ad = new AdModel();
        if(request()->isAjax()){
            $param = input('post.');
            $flag = $ad->edit_focus($param);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }

        $id = input('param.id');
        $this->assign('list',$ad->getOne($id));
        $this->assign('f_id',$id);
        return $this->fetch();
    }


    /**
     * [del_position 删除焦点图]
     * @return [type] [description]
     * @author [jonny] [980218641@qq.com]
     */
    public function del_focus()
    {
        $id = input('param.id');
        $ad = new AdModel();
        $flag = $ad->del_focus($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }



    /**
     * [position_state 广告位状态]
     * @return [type] [description]
     * @author [jonny] [980218641@qq.com]
     */
    public function focus_state()
    {
        $id=input('param.id');
        $status = Db::name('focus')->where(array('f_id'=>$id))->value('status');//判断当前状态情况
        if($status==1)
        {
            $flag = Db::name('focus')->where(array('f_id'=>$id))->setField(['status'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('focus')->where(array('f_id'=>$id))->setField(['status'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }  

}