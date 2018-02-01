<?php
/**
 * 极客之家 高端PHP - 小程序前端接口
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
use org\Verify;
use app\port\model\FoodModel;
use app\port\model\BaseModel;
use app\port\model\WebfoodModel;
class Webfood extends Controller
{
  /**
   * [uploadProjectImage 添加项目上传图片]
   * @return [type] [description]
   * @author [qinlh] [15035574759@163.com]
   */
  public function uploadProjectImage()
  {
      $file = request()->file('file');

      $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/uploads/');//移动到目录

      //如果图片大小大于100KB 开始压缩处理
      $file_url = "upload/uploads/".$info->getSaveName();
      $size = round(filesize($file_url) / 1024);
      if($size > 100){
        @$image = new \image\Image($file_url);
        @$image->compressImg($file_url);
      }

      if($info)
      {
          $res['status'] = 1;
          $res['image_name'] = "/upload/uploads/".$info->getSaveName();
          return json($res);
      }
      else
      {
          $res['status'] = 0;
          $res['error_info'] = $file->getError();
          return json($res);
      }
  }

    /**
     * [zyUploads] 批量上传图片
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function zyupload()
    {
    // print_r($_FILES['name']);
            $url = array();
            // 获取临时文件
            for($i=0;$i<count($_FILES['name']['name']);$i++)
            {
                //新文件地址
                $NewFile = "upload/uploads/".date('Ymd',time())."/";
                // echo $NewFile;die;
                if(!file_exists($NewFile))
                {
                    //检查是否有该文件夹，如果没有就创建，并给予最高权限
                    mkdir($NewFile);
                    chmod($NewFile,0777);
                }
                 //$name = $_FILES['name']['name'][$i];
                $name = $_FILES['name']['name'];//文件名称
                $file_name = date("YmdHis").rand(1000,999999).substr($name,strripos($name,'.'));
                // $names = implode(",", $name);
                $path = $_FILES['name']['tmp_name'];//文件位置
                // return $name;
                //移动到指定文件
                $res = move_uploaded_file($path,$NewFile.$file_name);

                //如果图片大小大于100KB 开始压缩处理
                $file_url = $NewFile.$file_name;
                $size = round(filesize($file_url) / 1024);
                if($size > 100){
                  @$image = new \image\Image($file_url);
                  @$image->compressImg($file_url);
                }

                $url[] = "/".$NewFile.$file_name;
             }
             if(true == $res)
             {
                exit(json_encode(['code'=>1001,'data'=>$url,'msg'=>'Update Success']));
             }
             else
             {
                exit(json_encode(['code'=>1003,'data'=>'','msg'=>'Update Error']));
             }
    }

    /**
     * [unsetImg] [删除图片]
     * @return [type] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function unsetImg()
    {
        $base  = new BaseModel;
        $imgurl = input("param.imgurl");
        if(empty($imgurl))
    		{//如果索索添加不满足返回空数组
    			   return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
    		}
        //开始删除对应图片
        $result = $base->MoveFile("uploads",'/'.$imgurl)['data'];
        if(isset($result['code']) || $result['code'] == -1)
        {
            exit(json_encode(['code'=>-1,'data'=>$url,'msg'=>'Dealer File Error']));
        }
        exit(json_encode(['code'=>1001,'data'=>$url,'msg'=>'Dealer File Success']));

    }

    /**
     * [MenuAddUpload] [用户发布菜单]
     * @return [json] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function MenuAddUpload()
    {
        $webfood  = new WebfoodModel;
        $food = new FoodModel();
        $data = input("param.");
        $data['uid'] = $food->UserOpenid($data['openid']);
        //一个用户一天最多只能发布三个作品
        $piece = 3;
        $DataTime = DB::name("veriety")->where("uid",$data['uid'])->whereTime('time', 'd')->count();
        if($DataTime >= $piece)
        {
            exit(json_encode(['code'=>1009,'ErrMsg'=>'Piece Error']));
        }
        $data['inventory'] = serialize(json_decode($data['inventory'],true));
        $data['thumbnail'] = serialize(implode(",",json_decode($data['thumbnail'],true)));

        $flag = $webfood->WebfoodAdd($data);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [UserMenuCount] [查询当前用户当天发布数量]
     * @return [json] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function UserMenuCount() {
        $food = new FoodModel();
        $openid = input("param.openid") ? input("param.openid") : '';//用户openid
        if(empty($openid))
        {
            return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
        }
        $uid = $food->UserOpenid($openid);
        if(!isset($uid) || empty($uid))
        {
            return json_encode(['code'=>1010,'data'=>'','msg'=>'User Nonentity']);
        }
        $UserPiece = DB::name("veriety")->where("uid",$uid)->whereTime('time', 'd')->count();
        return json_encode(['code'=>1001,'data'=>$UserPiece,'msg'=>'Query Data Success']);
    }

    /**
     * [UserMenuDel] [用户删除对应菜单列表]
     * @return [json] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function UserMenuDel()
    {
        $webfood  = new WebfoodModel;
        $food = new FoodModel();
        $openid = input("param.openid") ? input("param.openid") : '';//用户openid
        $id = input("param.id") ? input("param.id") : '';//菜单ID
        $uid = $food->UserOpenid($openid);
        if(empty($openid) || empty($id))
    		{
    			  return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
    		}
        $flag = $webfood->foodDel($id,$uid,$openid);
        return json(['code' => $flag['code'], 'data' => $flag['data'], 'msg' => $flag['msg']]);
    }

    /**
     * [UserMenuList] [用户菜单列表]
     * @return [json] [description]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function UserMenuList()
    {
        $webfood  = new WebfoodModel;
        $food = new FoodModel();
        $openid = input("param.openid") ? input("param.openid") : '';//用户openid
    		$pageId = input('param.pageId') ? input('param.pageId') : 0;
    		if(empty($openid))
    		{
    			return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
    		}
    		$uid = $food->UserOpenid($openid);
    		$map['uid'] = $uid;
    		$map['audit_start'] = 1; //只显示审核通过的
    		if($pageId && $pageId >= 1)
    		{
    			$map['id'] = ["<",$pageId];
    		}
    		$data = DB::name("veriety")
    					->where($map)
    					->limit(10)
              ->order("id desc")
    					->field("id,img,name,inventory,author,collect,user_like,like,time")
    					->select();
    		foreach ($data as $key => $val) {
    			$data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$val['img'];
    			$data[$key]['inventory'] = unserialize($val['inventory']);//清单
          $data[$key]['time'] = date("Y-m-d",$val['time']);//时间
    		}

    		//查询当前用户发布菜单总数
    		$VerietyCount = DB::name("veriety")->where(['uid'=>$uid,'audit_start'=>1])->count();
    		// p($data);
    		if($data)
    		{
    			return json_encode(['code'=>1001,'data'=>['dataList' => $data,'count' => $VerietyCount],'msg'=>'Query Data Success']);
    		}
    		else
    		{
    			return json_encode(['code'=>1004,'data'=>'','msg'=>'Query Data Error']);
    		}
    }

}
