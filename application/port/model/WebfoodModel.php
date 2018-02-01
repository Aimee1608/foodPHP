<?php
/**
 * 极客之家 高端PHP - 安全检验模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\model;
use think\Model;
use think\Db;
use think\Config;
use think\Session;
use think\Cache;
use think\Loader;
class WebfoodModel extends Model
{
    protected $table = 'veriety';

    /**
     * [GreensAdd] 用户前台添加入库菜单数据
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function WebfoodAdd($data)
    {
        try{
            $DataArray = [
                     'name'      =>  $data['name']
                    ,'uid'    =>  $data['uid']
                    ,'describe'  =>  $data['describe']
                    ,'author'  =>  $data['author'] //作者
                    ,'img'  =>  $data['img']
                    ,'class_id'  =>  $data['class_id']
                    ,'complexity'  =>  $data['complexity'] //难易程度
                    ,'handle_time'  =>  $data['handle_time']
                    ,'inventory'  =>  $data['inventory']   //食材
                    ,'step'  =>  $data['step']  //操作步骤
                    ,'thumbnail'  =>  $data['thumbnail']  //图片操作步骤
                    ,'tip'  =>  $data['tip']
                    ,'time'  =>  time()  //小窍门
                ];
            $result = DB::name("veriety")->insert($DataArray);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '菜单添加成功'];
            }
        }
        catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [foodDel] 删除菜单
     * @param  [string] $id [项目ID]
     * @param  [string] $uid [用户ID]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function foodDel($id,$uid,$openid)
    {
        try{
            $base  = new BaseModel;
            $food = new FoodModel();
            $name = DB::name($this->table)->where("id",$id)->field("name")->find()['name'];
            //要把所有图片都删掉
            //1. 先查询封面图片
            $imgurl = DB::name($this->table)->where(["id"=>$id,"uid"=>$uid])->field("img,thumbnail")->find();
            // p($imgurl);
            //先删除封面图片
            @$base->DelWormFile($imgurl['img']);

            //再删除图片步骤图片集
            $thumbnail = explode(",",unserialize($imgurl['thumbnail']));
            foreach ($thumbnail as $key => $val) {
                @$base->DelWormFile($val);
            }

            @$greensName = DB::name($this->table)->where("id",$id)->field("name")->find()['name'];
            $res = DB::name($this->table)->where("id",$id)->delete();
            $nickname = $food->UserOpenName($openid);
            if(false == $res)
            {
                writelog($uid,$nickname,"用户【".$nickname."】删除"."菜单【".$greensName."】",2);
                return ['code' => 1004, 'data' => '', 'msg' => '删除菜单失败'];
            }
            //将用户关于这件商品收藏数据删除
            @DB::name("collect")->where("f_id",$id)->delete();
            $nickname = DB::name("user")->where("uid",$uid)->field("nickname")->find()['nickname'];
            writelog($uid,$nickname,"用户【".$nickname."】删除"."菜单【".$greensName."】",1);
            return ['code' => 1001, 'data' => '', 'msg' => '删除菜单成功'];
        }catch( PDOException $e ){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}
