<?php
/**
 * 极客之家 高端PHP - 小程序接口
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use think\Controller;
use think\Config;
use think\Db;
use think\Session;
use think\Cache;
use app\port\model\FoodModel;
class Food extends Controller
{

	/**
	 * 查询首页焦点图
	 * @return [json] [description]
	 */
	public function GetFocus()
	{
		//读取缓存
		if(Cache::get("GetFocusCacheData") == true)
		{
				$data = Cache::get("GetFocusCacheData");
				return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
		}

		$data = DB::name("veriety")->where(['status'=>1,'audit_start'=>1])->order("`like` desc")->limit(5)->field("id,img,like")->select();
		foreach ($data as $key => $value) {
			$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
		}
		// p($data);
		if(false == $data)
		{
				return json_encode(['errcode'=>1004,'data'=>'','msg'=>'Query Data Error']);
		}
		@Cache::set("GetFocusCacheData",$data,86400);
		return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
	}

	/**
	 * 查询菜单所有分类
	 * @return [json] [description]
	 */
	public function class_list()
	{
		//读取缓存
		if(Cache::get("classListCache") == true)
		{
				$res = Cache::get("classListCache");
				return json_encode(['code'=>1001,'data'=>$res,'msg'=>'Query Data Success']);
		}

		$res = DB::name("class")->where(['pid'=>0,'start'=>1])->order("sort asc")->select();
		foreach ($res as $key => $val) {
			$res[$key]['class_names'] = DB::name("class")->where(['pid'=>$val['id'],'start'=>1])->select();
		}
		// p($res);
		if(false == $res)
		{
			return json_encode(['errcode'=>1004,'data'=>'','msg'=>'Query Data Error']);
		}

		@Cache::set("classListCache",$res,86400);
		return json_encode(['code'=>1001,'data'=>$res,'msg'=>'Query Data Success']);
	}

	/**
	 * [ClassData] 根据分类查询数据
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	// public function ClassData()
	// {
	// 	$class_id = input('param.class_id') ? input('param.class_id') : 0;//接受分类ID
	// 	if(!$class_id && empty($class_id))
	// 	{
	// 		return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
	// 	}

	// 	$res = DB::name("veriety")->where("class_id",$class_id)->select();
	// 	if(false == $data)
	// 	{//查询数据错误
	// 		return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
	// 	}

	// 	if($data == array())
	// 	{//没有跟多数据了
	// 		return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
	// 	}

	// 	return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
	// }

	/**
	 * 查询首页最新数据
	 * @return [type] [description]
	 */
	public function IndexShowList()
	{
		$pageId = input('param.pageId') ? input('param.pageId') : 0;

		$map = [];
		$map['v.status'] = 1;
		$map['v.audit_start'] = 1;

		if($pageId && $pageId >= 1)
		{
				$map['id'] = ["<",$pageId];
		}

		$data = DB::name("veriety")
					->alias("v")
					->join("food_user u","v.uid=u.uid")
					->where($map)
					->limit(8)
					->order("v.id desc")
					->field("v.id,v.img,v.name,v.inventory,v.author,v.collect,v.user_like,v.like,v.class_id,v.time,u.nickname,u.headimgurl")
					->select();
		foreach ($data as $key => $value) {
			$data[$key]['inventory'] = unserialize($value['inventory']);
			$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
			$data[$key]['time'] = date("Y-m-d",$value['time']);//时间
		}

		if($data == array())
		{//没有跟多数据了
				return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
		}
		if(false == $data)
		{//查询数据错误
				return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
		}

			return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
	}

	/**
	 * 查询所有菜单数据
	 * 更多数据、模糊搜索、条件查询
	 * @return [type] [description]
	 */
	public function show_list()
	{
		// p($_SERVER);die;
		$class_id = input('param.class_id') ? input('param.class_id') : 0;
		$pageId = input('param.pageId') ? input('param.pageId') : 0;
		$name = input('param.name') ? input('param.name') : '';
		// $project_name = "标";
		// $pageId = 26;
		// $class_id = 2;
		if($class_id == 0 && empty($name))
		{//如果索索添加不满足返回空数组
			return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
		}

		$map = [];
		$map['v.status'] = 1;
		$map['v.audit_start'] = 1;

		if($pageId && $pageId >= 1)
		{
			 $map['v.id'] = ["<",$pageId];
		}
		if($class_id > 0)
		{
			$map['v.class_id'] = $class_id;
			// $map['class_id'] = ['like',"%" . $class_id . "%"];//模糊陪陪，由于一个项目有可能对应多个分类
		}

		if($name && $name !== "")
		{//模糊搜索是标题数据
        $map['v.name'] = ['like',"%" . $name . "%"];
    }

		$data = DB::name("veriety")
					->alias("v")
					->join("food_user u","v.uid=u.uid")
					->where($map)
					->limit(8)
					->order("v.id desc")
					->field("v.id,v.img,v.name,v.inventory,v.author,v.collect,v.user_like,v.like,v.class_id,v.time,u.nickname,u.headimgurl")
					->select();
		foreach ($data as $key => $value) {
			$data[$key]['inventory'] = unserialize($value['inventory']);
			$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
			$data[$key]['time'] = date("Y-m-d",$value['time']);//时间
		}
		// p($data);
		if($data == array())
		{//没有跟多数据了
			return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
		}
		if(false == $data)
		{//查询数据错误
			return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
		}

		return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
	}

	/**
	 * 查询首页今日推荐数据 后台设置控制接口 api
	 * @return [type] [description]
	 */
		// public function Recommend()
		// {
		// 	// p($_SERVER);die;
		// 	// $pageId = input('param.pageId') ? input('param.pageId') : 0;
		// 	// $project_name = "标";
		// 	// $pageId = 26;
		// 	// $class_id = 2;
		// 	$map = [];
		// 	$map['status'] = 1;
		// 	$map['recommend'] = 1;
		// 	$map['audit_start'] = 1;
		// 	$data = DB::name("veriety")
		// 				->where($map)
		// 				->limit(4)
		// 				->order("id desc")
		// 				->field("id,img,name,inventory,author,collect,like,user_like,time")
		// 				->select();
		// 	foreach ($data as $key => $value) {
		// 		$data[$key]['inventory'] = unserialize($value['inventory']);
		// 		$data[$key]['user_like'] = count(explode(",",$value['user_like']));
		// 		$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
		// 		$data[$key]['time'] = date("Y-m-d",$value['time']);//时间
		// 	}
		// 	// p($data);
		// 	if($data == array())
		// 	{//没有跟多数据了
		// 		return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
		// 	}
		// 	if(false == $data)
		// 	{//查询数据错误
		// 		return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
		// 	}
		//
		// 	return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
		// }

		/**
		 * 查询首页今日推荐数据 随机每天更新
		 * @return [type] [description]
		 */
			public function Recommend()
			{
				// $dateStr = strtotime(date('Y-m-d', time()));//获取当天0:0:0:时间戳
				$TodayTime = date("Y-m-d");//今天时间
				$YestTime  = date("Y-m-d",strtotime("-1 day"));//昨天时间

				if(Cache::get($TodayTime."_GetRecommendData") == true)
				{
						//读取到今天缓存的数据，直接展示
						$data = Cache::get($TodayTime."_GetRecommendData");
						// return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
				}
				else
				{
						//开始查询数据
						$map = [];
						$map['status'] = 1;
						// $map['recommend'] = 1;
						$map['audit_start'] = 1;
						$data = DB::name("veriety")
									->where($map)
									->limit(4)
									->order("RAND()")
									->field("id,img,name,inventory,author,collect,like,user_like,time")
									->select();
						foreach ($data as $key => $value) {
							$data[$key]['inventory'] = unserialize($value['inventory']);
							$data[$key]['user_like'] = count(explode(",",$value['user_like']));
							$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
							$data[$key]['time'] = date("Y-m-d",$value['time']);//时间
						}

						//这是昨天缓存的数据，删除缓存数据，重新缓存
						@Cache::rm($YestTime.'_GetRecommendData');//删除昨天的数据
						//今天第一次重新缓存数据
						@Cache::set($TodayTime."_GetRecommendData",$data,86400); //存一天自动删除
				}
				// p($data);
				if($data == array())
				{//没有跟多数据了
					return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
				}
				if(false == $data)
				{//查询数据错误
						return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
				}

				return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Data Success']);
			}


	/**
	 * 用户点击菜单进入详情页
	 * @return [type] [description]
	 */
	public function FoodInfoData()
	{
		$food = new FoodModel();
		$id = input('param.id') ? input('param.id') : 0;//接受菜单ID
		$openid = input("param.openid") ? input("param.openid") : '';//用户openid
		if($id == 0)
		{//获取ID错误
			return json_encode(['code'=>1002,'data'=>'','msg'=>'String Id Error']);
		}

		//修改项目点击量
		@$num = DB::name("veriety")->where("id",$id)->field("click")->find()['click'] + 1;
		// echo $num;die;
		@DB::name("veriety")->where("id",$id)->update(['click'=>$num]);

		//查询项目详情数据
		$data = DB::name("veriety")->where("id",$id)->find();
		$data['img'] = 'https://h5php.xingyuanauto.com/food/public'.$data['img'];
		$data['inventory'] = unserialize($data['inventory']);//清单
		$data['thumbnail'] = explode(",",unserialize($data['thumbnail']));//操作步骤
		$data['step'] = explode("\n",trim($data['step'])); //操作步骤
		//查询对应作者  如果有真实姓名显示真实姓名，否则显示昵称
		$name = DB::name("user")->where("uid",$data['uid'])->field("truename")->find()['truename'];
		if(empty($name))
		{
				$data['author'] = $data['author'];
		}
		else
		{
				$data['author'] = $name;
		}

		foreach ($data['thumbnail'] as $key => $val) {
			$data['thumbnail'][$key] = 'https://h5php.xingyuanauto.com/food/public'.$val;
		}
		// p($data);
		if($openid && $openid !== "")
		{//用户已经登录
           $uid = $food->UserOpenid($openid);
			//查询用户是否点赞
			if(in_array($uid,explode(",",$data['user_like'])))
			{//已经点赞
				$data['user_like_start'] = 1;
			}
			else
			{//未点赞
				$data['user_like_start'] = 0;
			}

			//判读用户是否收藏
			if(DB::name("collect")->where(['uid'=>$uid,'status'=>0,'f_id'=>$id])->find())
			{
				//已经收藏、
				$data['collect_start'] = 1;
			}
			else
			{
				//未收藏、
				$data['collect_start'] = 0;
			}

        }
        else
        {//默认
        	$data['user_like_start'] = 0;
        	$data['collect_start'] = 0;
        }
        // p($data);
		if(false == $data)
		{//url地址获取错误
			return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Url Error']);
		}

		return json_encode(['code'=>1001,'data'=>$data,'msg'=>'Query Url Success']);
	}

	/**
	 * [UserCollect] 用户点击收藏
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function UserCollect()
	{
		$food = new FoodModel();
		$openid = input("param.openid") ? input("param.openid") : '';//用户openid
		$f_id = input("param.f_id") ? input("param.f_id") : '';//项目ID
		$start = input("param.start") ? input("param.start") : 0;//收藏状态 1已收藏  0为收藏
		if(empty($openid) || empty($f_id))
		{
			return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
		}

		$return = $food->UserCollect($openid,$f_id);
		return json_encode(['code'=>$return['code'],'data'=>$return['data'],'msg'=>$return['msg']]);
	}

	/**
	 * [UserLike] 用户点击点赞
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function UserLike()
	{
		$food = new FoodModel();
		$openid = input("param.openid") ? input("param.openid") : '';//用户openid
		$f_id = input("param.f_id") ? input("param.f_id") : '';//项目ID
		if(empty($openid) || empty($f_id))
		{
			return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
		}
		$return = $food->UserLike($openid,$f_id);
		return json_encode(['code'=>$return['code'],'data'=>$return['data'],'msg'=>$return['msg']]);
	}

	/**
	 * [UserCollectData] 查询用户收藏列表
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	public function UserCollectData()
	{
		$food = new FoodModel();
		$openid = input("param.openid") ? input("param.openid") : '';//用户openid
		$pageId = input('param.pageId') ? input('param.pageId') : 0;
		if(empty($openid))
		{
			return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
		}
		$uid = $food->UserOpenid($openid);
		$map['c.status'] = 0;
		$map['c.uid'] = $uid;
		$map['audit_start'] = 1;
		if($pageId && $pageId >= 1)
		{
			$map['collect_id'] = ["<",$pageId];
		}
		$data = DB::name("collect")
					->alias('c')
					->join('food_veriety v','c.f_id = v.id')
					->where($map)
					->limit(10)
					->order("c.collect_id desc")
					->field("v.id,v.img,v.name,v.inventory,v.author,v.collect,v.user_like,v.like,v.time")
					->select();
		foreach ($data as $key => $val) {
			$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$val['img'];
			$data[$key]['inventory'] = unserialize($val['inventory']);//清单
			$data[$key]['time'] = date("Y-m-d",$val['time']);//时间
		}
		//查询当前用户收藏总数
		$CollectCount = DB::name("collect")->where(['uid'=>$uid,'status'=>0])->count();
		// $NewData = ;
		if($data)
		{
			return json_encode(['code'=>1001,'data'=>['dataList' => $data,'count' => $CollectCount],'msg'=>'Query Data Success']);
		}
		else
		{
			return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
		}
	}

//************************************** 未启用 ******************************************


	/**
	 * 查询首页焦点图
	 * @return [json] [description]
	 */
	// public function focus()
	// {
	// 	$res = DB::name("focus")->where("status",1)->order("order_by asc")->limit(5)->select();
	// 	foreach ($res as $key => $value)
	// 	{
	// 		$res[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$value['img'];
	// 	}
	// 	if(false == $res)
	// 	{
	// 		return json_encode(['errcode'=>1004,'data'=>'','msg'=>'Query data error']);
	// 	}
	// 	return json_encode(['code'=>1001,'data'=>$res,'msg'=>'Query data success']);
	// }
}
