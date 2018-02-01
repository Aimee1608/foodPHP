<?php
/**
 * 极客之家 高端PHP - 菜单Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\model;
use think\Db;
use think\Model;
class GreensModel extends Model
{
	protected $table = 'veriety';

    /**
     * [greens_list] 查询所有菜单数据
     * @param  [string] $page [当前页]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function greens_list($page)
    {
        try {
            $BasePage  = new BaseModel;
            $table = "veriety";
            $where = 1;
						$limits = 15;// 每页显示条数
            $count = Db::name($table)->where($where)->count();//计算总页面
            $allpage = intval(ceil($count / $limits));
						$lists = DB::name($table)
											->alias("v")
											->join("food_user u","v.uid=u.uid")
											->where($where)
											->field("u.nickname,u.truename,v.id,v.name,v.img,v.describe,v.time,v.recommend,v.like,v.collect,v.status,v.audit_start")
											->page($page, $limits)
											->order('id desc')
											->select();
						foreach ($lists as $key => $val) {
								$lists[$key]['time'] = date("Y-m-d H:i:s",$val['time']);
						}
// p($lists);
						return ['count'=>$count,'allpage'=>$allpage,'lists'=>$lists];
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

	/**
	 * [ClassData] 查询分类数据
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function ClassData($class_id='')
	{
		try {
            $data = DB::name("class")->where("start",1)->select();
            $classId = explode(",",$class_id);
            // p($labelId);
            foreach ($data as $key => $val)
            {

                if(in_array($val['id'],$classId))
                {
                    $data[$key]['num'] = 1;
                }
                else
                {
                    $data[$key]['num'] = 0;
                }
            }
            // p($data);
            $nav = new \org\Leftnav;
            return $nav::rule($data);
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
	}

	/**
	 * [AdministratorList] 查询所有超级管理员
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function AdministratorList()
	{
		try {
          	return DB::name("user")->where("admin_start",1)->field("uid,nickname,openid")->select();
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
	}

    /**
     * [GreensAdd] 添加入库菜单数据
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function GreensAdd($data)
    {
        try{
            $DataArray = [
                     'name'      =>  $data['name']
                    ,'describe'  =>  $data['describe']
                    ,'author'  =>  $data['author'] //作者
                    ,'uid'  =>  $data['uid'] //作者 uid
                    ,'img'  =>  $data['img']['data']
                    ,'class_id'  =>  $data['class_id']
                    ,'complexity'  =>  $data['complexity'] //难易程度
                    ,'handle_time'  =>  $data['handle_time']
                    ,'inventory'  =>  $data['inventory']   //食材
                    ,'step'  =>  $data['step']  //操作步骤
                    ,'thumbnail'  =>  $data['thumbnail']  //图片操作步骤
                    ,'tip'  =>  $data['tip']
                    ,'time'  =>  time()  //小窍门
                    ,'thumbnail'  =>  $data['thumbnail']
                    ,'status'   =>   0  // 默认不发布
                ];
            $result = DB::name($this->table)->insert($DataArray);
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
     * [GreenSave] 修改菜单数据
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function GreensSave($data,$id)
    {
        try{
            $DataArray = [
                     'name'      =>  $data['name']
                    ,'describe'  =>  $data['describe']
                    ,'img'  =>      $data['img']
                    ,'class_id'  =>  $data['class_id']
                    ,'complexity'  =>  $data['complexity'] //难易程度
                    ,'handle_time'  =>  $data['handle_time']
                    ,'inventory'  =>  $data['inventory']   //食材
                    ,'step'  =>  $data['step']  //操作步骤
                    ,'thumbnail'  =>  $data['thumbnail']  //图片操作步骤
                    ,'tip'  =>  $data['tip']
                    ,'time'  =>  time()  //小窍门
                    ,'thumbnail'  =>  $data['thumbnail']
                    ,'status'   =>   $data['status']=='on' ? 1 : 0
                ];
            $result = DB::name($this->table)->where("id",$id)->update($DataArray);
            if(false === $result){
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '菜单修改成功'];
            }
        }
        catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [foodDel] 删除菜单
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function foodDel($id)
    {
        try{
            $base  = new BaseModel;
            //要把所有图片都删掉
            //1. 先查询封面图片
            $imgurl = DB::name($this->table)->where("id",$id)->field("img,thumbnail")->find();

            //先删除封面图片
            @$base->DelWormFile($imgurl['img']);

            //再删除图片步骤图片集
            $thumbnail = explode(",",unserialize($imgurl['thumbnail']));
            foreach ($thumbnail as $key => $val) {
                @$base->DelWormFile($val);
            }
						//删除菜单数据
            DB::name($this->table)->where("id",$id)->delete();
						//删除收藏列表所有关于这条数据
						@DB::name("collect")->where("f_id",$id)->delete();
            writelog(session('admin_uid'),session('admin_username'),'用户【'.session('admin_username').'】删除菜单成功',1);
            return ['code' => 1, 'data' => '', 'msg' => '删除菜单成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 查询单前ID图片路径
     * @param [type] $file [临时文件路径]
     * @param [type] $file [临时文件路径]
     */
     function TestfileFind($id='')
     {
       try{
          return DB::name($this->table)->where("id",$id)->field("id,img,thumbnail")->find();
        }catch( PDOException $e){
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
     }
}
