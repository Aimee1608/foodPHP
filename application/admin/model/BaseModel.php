<?php
/**
 * 极客之家 高端PHP - 公用控制器
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\model;
use think\Db;
use think\Model;
class BaseModel extends Model
{
    protected $name = 'table';
    

    /**
     * 分页 没有两表联查
     * @param  [type] $table [表名]
     * @param  [type] $where [查询条件]
     * @param  [type] $where [当前页]
     * @param  [type] $ID [自增ID]
     * @return [type] array  
     */
    public function GetPage($table,$where,$page,$id)
    {
        try{
            $limits = 15;// 获取总条数
            $count = Db::name($table)->where($where)->count();//计算总页面
            $allpage = intval(ceil($count / $limits));
            $lists = Db::name($table)->where($where)->page($page, $limits)->order(''.$id.' desc')->select();
            if(false == $lists)
            {
                return ['code'=>-1,'data'=>'','msg'=>'查询分页数据失败'];
            }
            return ['count'=>$count,'allpage'=>$allpage,'lists'=>$lists];

        }catch( PDOException $e){
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 移动文件到正式目录
     * @param [type] $file [临时文件路径]
     * @param [type] $file [临时文件路径]
     */
    public function MoveFile($file,$url,$test_url='')
    {
        try{
            //新文件地址
            $NewFile = "upload/".$file."/".date('Ymd',time())."/";
            // echo $NewFile;die;
            if(!file_exists($NewFile))
            {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($NewFile);
                chmod($NewFile,0777);
            }
            if(!empty($test_url))
            {
                @unlink(substr($test_url,1)); //删除旧文件目录
            }

            $new_file = $NewFile.basename($url);

            @rename(substr($url,1),$new_file); //移动到新目录  第一个参数为临时文件  第二个参数为新文件
            @unlink($url); //删除临时目录下的的文件
            return ['code' => 1, 'data' => "/".$new_file, 'msg' => 'Move File Success'];
        }catch( PDOException $e){
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 删除旧文件
     * @param [type] $url [文件路径]
     * @param [type] [临时文件路径]
     */
    public function DelWormFile($url='')
    {
        try{
            if(!empty($url))
            {
                @unlink(substr($url,1)); //删除旧文件目录
            }
            return;
        }catch( PDOException $e){
            return ['code' => -1, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
      * 递归查询所有分类
      * @param  [type]  $data  [所有数据]
      * @param  integer $pid   [父ID]
      * @param  integer $leave [自定义参数]
      * @return [type]         [返回处理好的数组]
      */
     function getAll($data,$pid=0,$leave=0)
     {
        static $result;
        foreach ($data as $key => $val) 
        {
            if($val['pid'] == $pid)
            {
                $val['leave'] = $leave;
                $result[] = $val;
                $this->getAll($data,$val['class_id'],$leave+1);
            }
        }
        //print_r($result);die;
        return $result;
     }
}