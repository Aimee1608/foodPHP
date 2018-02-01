<?php
/**
 * 极客之家 高端PHP - 广告管理Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\model;
use think\Model;
use think\Db;

class AdModel extends Model
{
    protected $name = 'ad';

    /**
     * 查询焦点图列表
     */
    public function focus_map()
    {
        try{
            $data = DB::name("focus")->select();
            foreach ($data as $key => $value) {
                $data[$key]['show_time'] = $value['start_date']." 至 ".$value['end_date']; 
            }
            return $data;
         }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 插入信息
     * @param $param
     */
    public function add_focus($param)
    {
        try{
            if($param['status'] == 'on'){$param['status'] = 0;}
            //这里要移动图片位置到正式上传目录 生成正式路径
            $base  = new BaseModel;
            $param['img'] = $base->MoveFile("focus",$param['img'])['data'];
            $result = DB::name("focus")->insert($param);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        }catch( PDOException $e){
            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑信息
     * @param $param
     */
    public function edit_focus($param)
    {
        try{
            if($param['status'] == 'on'){$param['status'] = 0;}
            //这里要移动图片位置到正式上传目录 生成正式路径
            $base  = new BaseModel;
            $img = $base->MoveFile("focus",$param['img'])['data'];
            $UpdatteArr = [
                        'title' => $param['title'],
                        'img' => $img,//大图
                        'link_url' => $param['link_url'],
                        'order_by' => $param['order_by'],
                        'start_date' => $param['start_date'],
                        'end_date' => $param['end_date'],
                        'time' => time(),
                        'status' => $param['status']
                    ];
            //修改数据之前先删除原来项目文件
            $imgurl = DB::name("focus")->where("f_id",$param['f_id'])->field("img,img_smail")->find();
            $result = DB::name("focus")->where("f_id",$param['f_id'])->update($UpdatteArr);
            if(false === $result){
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                @unlink(substr($imgurl['img'],1)); //删除原来文件
                return ['code' => 1, 'data' => '', 'msg' => '编辑成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 根据id获取一条信息
     * @param $id
     */
    public function getOne($id)
    {
        return DB::name("focus")->where('f_id', $id)->find();
    }


    /**
     * 删除信息
     * @param $id
     */
    public function del_focus($id)
    {
        try{
            //将垃圾图片删除
            @unlink(substr(DB::name("focus")->where("f_id",$id)->field("img")->find()['img'],1));
            DB::name("focus")->where("f_id",$id)->delete();
            return ['code' => 1, 'data' => '', 'msg' => '删除成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

}
