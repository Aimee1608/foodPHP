<?php
/**
 * 极客之家 高端PHP - 数据列表
 * 微信公众号h5移动站 - 接口数据
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\web\controller;
use think\Controller;
use think\Config;
use think\Db;
use think\Session;
use think\Cache;
use app\port\model\IndexModel;
class Index extends Controller
{

	/**
	 * 查询首页焦点图
	 * @return [json] [description]
	 */
	public function focus()
	{
		$res = DB::name("focus")->where("status",1)->order("order_by asc")->limit(5)->select();
		foreach ($res as $key => $value) 
		{
			$res[$key]['img'] = 'https://h5php.xingyuanauto.com/Flow/public'.$value['img'];
			$res[$key]['img_smail'] = 'https://h5php.xingyuanauto.com/Flow/public'.$value['img_smail'];
		}
		if(false == $res)
		{
			return json_encode(['errcode'=>1,'data'=>'','msg'=>'Query data error']);
		}
		return json_encode(['code'=>1,'data'=>$res,'msg'=>'Query data success']);
	}

	/**
	 * 查询数据分类
	 * @return [json] [description]
	 */
	public function class_list()
	{
		$res = DB::name("project_class")->where("status",1)->order("order_by asc")->limit(4)->select();
		// p($res);
		if(false == $res)
		{
			return json_encode(['errcode'=>-1,'data'=>'','msg'=>'Query Data Error']);
		}
		return json_encode(['code'=>1,'data'=>$res,'msg'=>'Query Data Success']);
	}

	/**
	 * 查询所有项目数据  
	 * 更多数据、模糊搜索、条件查询
	 * @return [type] [description]
	 */
	public function show_list()
	{
		// p($_SERVER);die;
		$class_id = input('get.class_id') ? input('get.class_id') : 0;
		$pageId = input('get.pageId') ? input('get.pageId') : 1;
		$project_name = input('get.project_name') ? input('get.project_name') : '';
		// $project_name = "标";
		// $pageId = 26;
		// $class_id = 2;
		$map = [];
		$map['start'] = 1;
		if($pageId && $pageId > 1)
		{
			$map['id'] = ["<",$pageId];
		}
		if($class_id > 0)
		{
			// $map['class_id'] = $class_id;
			$map['class_id'] = ['like',"%" . $class_id . "%"];//模糊陪陪，由于一个项目有可能对应多个分类
		}

		if($project_name && $project_name !== "")
		{//模糊搜索是标题数据
            $map['project_name'] = ['like',"%" . $project_name . "%"];
        }

		$data = DB::name("project")
					->where($map)
					->limit(5)
					->order("id desc")
					->field("id,project_name,project_img,describe,start,label_id,clicks,class_id,time")
					->select();
		//对数据的一些处理
		foreach ($data as $key => $val) 
		{//查询对应标签
			$data[$key]['label_id'] = DB::name("project_label")->where("b_id","in",$val['label_id'])->field("b_id,label_name")->select();
			$data[$key]['project_img'] = $_SERVER['HTTP_HOST']."/Flow/public".$val['project_img'];//对应图片地址
			$data[$key]['describe'] = mb_strlen($val['describe'], 'utf-8') > 9 ? mb_substr($val['describe'], 0, 9, 'utf-8').'....' : $val['describe'];//描述字节控制
			$data[$key]['time'] = date("Y-m-d",strtotime($val['time']));
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
	 * 用户点击项目列表
	 * @return [type] [description]
	 */
	public function click_url()
	{
		$id = input('get.id') ? input('get.id') : 0;//接受链接地址id
		if($id == 0)
		{//获取ID错误
			return json_encode(['code'=>-1,'data'=>'','msg'=>'String Id Error']);
		}

		//修改项目点击量
		@$num = DB::name("project")->where("id",$id)->field("clicks")->find()['clicks'] + 1;
		// echo $num;die;
		@DB::name("project")->where("id",$id)->update(['clicks'=>$num]);

		//查询项目对应链接地址
		$url = DB::name("project")->where("id",$id)->field("line_url")->find()['line_url'];
		if(false == $url)
		{//url地址获取错误
			return json_encode(['code'=>-1,'data'=>'','msg'=>'Query Url Error']);
		}

		return json_encode(['code'=>1,'data'=>$url,'msg'=>'Query Url Success']);
	}
}