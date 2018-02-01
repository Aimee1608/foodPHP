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
class FoodModel extends Model
{
    protected $table = '';

    /**
     * [UserOpenid] 获取用户openid
     * @param  [string] $openid [用户openid]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function UserOpenid($openid)
    {
    	try{
           	$uid = DB::name("user")->where("openid",$openid)->field("uid,openid")->find()['uid'];
            if(!isset($uid) || empty($uid))
            {
                return json_encode(['code'=>1010,'data'=>'','msg'=>'User Nonentity']);
            }
            return $uid;
        }
        catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * [UserOpenid] 获取用户openid
     * @param  [string] $openid [用户openid]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function UserOpenName($openid)
    {
    	try{
           	$uid = DB::name("user")->where("openid",$openid)->field("uid,openid,nickname")->find()['nickname'];
            if(!isset($uid) || empty($uid))
            {
                return json_encode(['code'=>1010,'data'=>'','msg'=>'User Nonentity']);
            }
            return $uid;
        }
        catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
	 * [UserCollect] 用户收藏
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function UserCollect($openid,$f_id)
	{
		$uid = $this->UserOpenid($openid);
		$str = DB::name("collect")->where(['uid'=>$uid,'f_id'=>$f_id,'status'=>0])->find();
		$veriety = DB::name("veriety")->where("id",$f_id)->field("collect")->find()['collect'];
		if($str)
		{
			//已经收藏
			$res = DB::name("collect")->where(['uid'=>$uid,'f_id'=>$f_id])->update(['status'=>1]);
			//修改菜单收藏量
			DB::name("veriety")->where("id",$f_id)->field("collect")->update(['collect'=>$veriety - 1]);
			if(false == $res)
			{
				return ['code'=>1004,'data'=>'','msg'=>'Delete Data Error'];
			}
			return ['code'=>1001,'data'=>0,'msg'=>'Collect Data Success'];
		}
		else
		{
			//未收藏  查询单前用户是否有数据
			$strtus = DB::name("collect")->where(['uid'=>$uid,'f_id'=>$f_id])->find();
			if($strtus)
			{//如果有数据就修改为收藏状态
				$res = DB::name("collect")->where(['uid'=>$uid,'f_id'=>$f_id])->update(['status'=>0]);
				//修改菜单收藏量
				DB::name("veriety")->where("id",$f_id)->field("collect")->update(['collect'=>$veriety + 1]);
			}
			else
			{// 添加一条收藏数据
				$res = DB::name("collect")->insert(['uid'=>$uid,'f_id'=>$f_id,'time'=>time(),'status'=>0]);
				//修改菜单收藏量
				DB::name("veriety")->where("id",$f_id)->field("collect")->update(['collect'=>$veriety + 1]);
			}

			if(false == $res)
			{
				return ['code'=>1004,'data'=>'','msg'=>'Add Data Error'];
			}
			return ['code'=>1001,'data'=>1,'msg'=>'Collect Data Success'];
		}
	}

    /**
     * [UserLike] 用户点赞
     * @param  [string] $openid [用户openid]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function UserLike($openid,$f_id)
    {
    	$uid = $this->UserOpenid($openid);
		//用户已经点赞、取消点赞
		$data = DB::name("veriety")->where("id",$f_id)->field("id,user_like,like")->find();
		$res = explode(",",$data['user_like']);
		if(in_array($uid,$res))
		{
			//如果存在开始取消用户点赞 重新组合数组
			foreach ($res as $key => $val)
			{
				if($val == $uid)
				{
					unset($res[$key]);
				}
			}
			$new_res = trim(implode(",", $res),',');
			$flog = DB::name("veriety")->where("id",$f_id)->update(['user_like'=>$new_res,'like'=>$data['like'] - 1]);
			if(false == $flog)
			{
				return ['code'=>1004,'data'=>0,'msg'=>'Database Error'];
			}
			return ['code'=>1001,'data'=>'','msg'=>'Update Success'];
		}
		else
		{
			//用户未点赞、添加用户点赞
			$no_res = $data['user_like'];
			$new_res = trim(",".$no_res.",".$uid,',');
			$flog = DB::name("veriety")->where("id",$f_id)->update(['user_like'=>$new_res,'like'=>$data['like'] + 1]);
			if(false == $flog)
			{
				return ['code'=>1004,'data'=>'','msg'=>'Database Error'];
			}
			return ['code'=>1001,'data'=>1,'msg'=>'Update Success'];
		}
    }
}
