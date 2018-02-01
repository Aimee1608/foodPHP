<?php
/**
 * 极客之家 高端PHP - 菜单分类Model
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\model;
use think\Db;
use think\Model;
class ClassifyModel extends Model
{
	protected $table = 'class';

	/**
	 * [ClassData] 查询分类数据
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function ClassData()
	{
		try {
            $data = DB::name("class")->select();
            $nav = new \org\Leftnav;
            return $nav::rule($data);//递归处理
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
	}

    /**
     * [ClassAdd] 添加分类
     * @param  [string] $data [接受数据]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function ClassAdd($data)
    {
        try {
            $data['start'] = $data['start'] ? 1 : 0;
            $result = DB::name($this->table)->insert($data);
            if(false === $result){               
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '添加成功'];
            }
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [ClassAdd] 编辑分类
     * @param  [string] $data [接受数据]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function ClassEdit($data)
    {
        try {
            $data['start'] = $data['start'] ? 1 : 0;
            $result = DB::name($this->table)->where("id",$data['c_id'])->update(['class_name'=>$data['class_name'],'sort'=>$data['sort'],'start'=>$data['start']]);
            if(false === $result){               
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                return ['code' => 1, 'data' => '', 'msg' => '修改成功'];
            }
        } catch (Exception $e) {
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

     /**
     * [delMenu 删除分类]
     * @author [jonny] [980218641@qq.com]
     */
    public function delMenu($id)
    {
        try{
            DB::name($this->table)->where("id",$id)->delete();
            writelog(session('admin_uid'),session('admin_username'),'用户【'.session('admin_username').'】删除菜单成功',1);
            return ['code' => 1, 'data' => '', 'msg' => '删除分类成功'];
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}