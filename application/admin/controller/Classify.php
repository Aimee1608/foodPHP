<?php
/**
 * 极客之家 高端PHP - 菜单分类模块
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
use app\admin\model\ClassifyModel;
class Classify extends Base
{
	/**
	 * [class_list] 分类列表
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
    public function class_list()
    {
        $class = new ClassifyModel();
        $data = $class->ClassData();
        $this->assign('ClassData',$data);
        return $this->fetch();
    }

    /**
     * [class_add] 添加分类
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function class_add()
    {
    	$class = new ClassifyModel();
    	if(request()->isAjax())
        {
        	$data = input("param.");
        	$flag = $class->ClassAdd($data);
        	return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    	}
    	$ClassData = $class->ClassData();
    	$this->assign("ClassData",$ClassData);
        return $this->fetch();
    }

    /**
     * [class_update] 修改分类
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function class_update()
    {
        $id = input("param.id");
        $class = new ClassifyModel();
        if(request()->isAjax())
        {
            $data = input("param.");
            $flag = $class->ClassEdit($data);
            return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
        }
        $ClassData = DB::name("class")->where("id",$id)->find();
        $this->assign("ClassData",$ClassData);
        $this->assign("c_id",$id);
        return $this->fetch();
    }

     /**
     * [class_status 分类状态]
     * @return [type] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function class_status()
    {
        $id = input('param.id');
        $status = Db::name('class')->where(array('id'=>$id))->value('start');//判断当前状态
        if($status==1)
        {
            $flag = Db::name('class')->where(array('id'=>$id))->setField(['start'=>0]);
            return json(['code' => 1, 'data' => $flag['data'], 'msg' => '已禁止']);
        }
        else
        {
            $flag = Db::name('class')->where(array('id'=>$id))->setField(['start'=>1]);
            return json(['code' => 0, 'data' => $flag['data'], 'msg' => '已开启']);
        }
    
    }

    /**
     * [roleDel 删除分类]
     * @return [type] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function del_class()
    {
        $id = input('param.id');
        $class = new ClassifyModel();
        $flag = $class->delMenu($id);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

}