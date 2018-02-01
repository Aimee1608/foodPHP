<?php
/**
 * 极客之家 高端PHP - 前台用户审核模块
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
class AuditModel extends Model
{
    protected $table = 'veriety';

    /**
     * [AuditPass]  [审核通过的数据]
     * @return [type] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function AuditPass($pageId,$audit_start,$uid)
    {
      $map = [];
  		$map['audit_start'] = $audit_start;
      $map['uid'] = $uid;
  		if($pageId && $pageId > 1)
  		{
  			   $map['id'] = ["<",$pageId];
  		}
      $data = DB::name("veriety")
                ->limit(10)
                ->where($map)
                ->field("id,img,name,inventory,author,collect,user_like,like,class_id,time,audit_start")
                ->order('id desc')
                ->select();
        foreach ($data as $key => $value) {
    			$data[$key]['inventory'] = unserialize($value['inventory']);
    			$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
    			$data[$key]['time'] = date("Y-m-d",$value['time']);//时间
    		}

        if($data == array())
    		{//没有跟多数据了
    			return ['code'=>1003,'data'=>'','msg'=>'Query Data Empty'];
    		}

        if(false == $data)
    		{//查询数据错误
    			return ['code'=>1004,'data'=>'','msg'=>'Query Data Error'];
    		}

    		return ['code'=>1001,'data'=>$data,'msg'=>'Query Data Success'];
    }

}
