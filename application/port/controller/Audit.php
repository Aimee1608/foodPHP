<?php
/**
 * 极客之家 高端PHP - 小程序前台审核控制器
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Audit.php 2017-9-1 13:36:52
 */
namespace app\port\controller;
use think\Controller;
use think\Config;
use think\Db;
use think\Session;
use think\Cache;
use app\port\model\FoodModel;
use app\port\model\BaseModel;
use app\port\model\AuditModel;
use app\port\model\WebfoodModel;
class Audit extends Controller
{

  /**
   * [Administrators]  [判断用户是否为管理员]
   * @return [type] [description]
   * @author [qinlh] [WeChat QinLinHui0706]
   */
  public function Administrators()
  {
      $openid = input("param.openid") ? input("param.openid") : '';//用户openid
      if(empty($openid))
      {//如果索索添加不满足返回空数组
          return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
      }
      $admin_start = DB::name("user")->where("openid",$openid)->field("admin_start")->find()['admin_start'];
      if(false == $admin_start)
      {
          exit(json_encode(['code' => 1004, 'data' => '', 'msg' => '操作失败']));
      }
      if($admin_start == 1)
      {
          exit(json_encode(['code' => 1001, 'Administrators' => 1, 'msg' => 'You are Administrators']));
      }
      else
      {
          exit(json_encode(['code' => 1001, 'Administrators' => 0, 'msg' => 'You are not an administrator']));
      }
  }
  /**
   * [AuditPass]  [查询所有数据 审核中 待审核、未通过]
   * @return [type] [description]
   * @author [qinlh] [WeChat QinLinHui0706]
   */
  public function AuditList()
  {
      $food = new FoodModel();
      $webfood = new WebfoodModel();
      $pageId = input('param.pageId') ? input('param.pageId') : 1;
      $openid = input('param.openid') ? input('param.openid') : '';
      $audit_start = input('param.audit_start') ? input('param.audit_start') : 0; //0 待审核  1已通过  2驳回
      if(empty($openid) || $audit_start === '')
      {//如果索索添加不满足返回空数组
        return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
      }
      $uid = $food->UserOpenId($openid);
      if($audit_start == 2)
      {//如果是未通过审核的菜单，系统默认是从审核日期开始大于三天数据自动删除
        //1. 想获取三天之前时间
        $TridTime = strtotime("-3 day"); //系统时间三天之前的时间
        //2.查询大于三天的数据
        $TridTimeData = DB::name("veriety")->where(['uid'=>$uid,'audit_start'=>2,'audit_time'=>['<',$TridTime]])->field("id")->select();
        //3.删除大于三天驳回的数据
        foreach ($TridTimeData as $key => $val) {
          // @DB::name("veriety")->where("id",$val['id'])->delete();
          @$webfood->foodDel($val['id'],$uid,$openid);
        }
      }
      $audit = new AuditModel();
      $flag = $audit->AuditPass($pageId,$audit_start,$uid);
       if(empty($flag['data'])){
            return json_encode(['code'=>1003,'data'=>'','msg'=>'Query Data Empty']);
       }
      $count = DB::name("veriety")->where(['audit_start'=>$audit_start,'uid'=>$uid])->count();
      $data = [
        'DataArr' => $flag['data'],
        'count' =>  $count
      ];
      return json_encode(['code'=>$flag['code'],'data'=>$data,'msg'=>$flag['msg']]);
  }

  /**
   * [greens_adopt] 菜单通过
   * @param  [string] $id [接受菜单id]
   * @return [type] [返回参数描述]
   * @author [qinlh] [WeChat QinLinHui0706]
   */
  public function greens_adopt()
  {
      $id = input('param.id') ? input('param.id') : '';
      $openid = input("param.openid") ? input("param.openid") : '';//用户openid
      if(empty($id) || empty($openid))
      {//如果索索添加不满足返回空数组
        return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
      }
      $food = new FoodModel();
      $uid = $food->UserOpenId($openid);
      $greensName = DB::name("veriety")->where("id",$id)->field("name")->find()['name'];
      $nickname = $food->UserOpenName($openid);
      $flag = DB::name('veriety')->where(array('id'=>$id))->setField(['audit_start'=>1,'status'=>1,'audit_time'=>time()]);
      if(true == $flag)
      {
          writelog($uid,$nickname,"管理员【".$nickname."】审核通过并发布"."菜单【".$greensName."】",1);
          exit(json_encode(['code' => 1001, 'data' => '', 'msg' => '已通过']));
      }
      else
      {
          writelog($uid,$nickname,"管理员【".$nickname."】审核通过并发布"."菜单【".$greensName."】",2);
          exit(json_encode(['code' => 1004, 'data' => '', 'msg' => '操作失败']));
      }
  }

  /**
   * [greens_reject] 菜单驳回
   * @param  [string] $id [接受菜单id]
   * @return [type] [返回参数描述]
   * @author [qinlh] [WeChat QinLinHui0706]
   */
  public function greens_reject()
  {
      $id = input('param.id') ? input('param.id') : '';
      $openid = input("param.openid") ? input("param.openid") : '';//用户openid
      if(empty($id) || empty($openid))
      {//如果索索添加不满足返回空数组
        return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
      }
      $food = new FoodModel();
      $uid = $food->UserOpenId($openid);
      $greensName = DB::name("veriety")->where("id",$id)->field("name")->find()['name'];
      $nickname = $food->UserOpenName($openid);
      $flag = Db::name('veriety')->where(array('id'=>$id))->setField(['audit_start'=>2,'status'=>0,'recommend'=>0,'audit_time'=>time()]);
      if(true == $flag)
      {
          writelog($uid,$nickname,"管理员【".$nickname."】审核驳回"."菜单【".$greensName."】",1);
          exit(json_encode(['code' => 1001, 'data' => '', 'msg' => '驳回成功']));
      }
      else
      {
          writelog($uid,$nickname,"管理员【".$nickname."】审核驳回"."菜单【".$greensName."】",2);
          exit(json_encode(['code' => 1004, 'data' => '', 'msg' => '驳回失败']));
      }
  }

  /**
   * [UserMenuList] [超级管理员审核用户发布的菜单列表]
   * @return [json] [description]
   * @author [qinlh] [WeChat QinLinHui0706]
   */
  public function AdministratorsAudit()
  {
      $food = new FoodModel();
      $openid = input("param.openid") ? input("param.openid") : '';//用户openid
      $pageId = input('param.pageId') ? input('param.pageId') : 1;
      if(empty($openid))
      {
          return json_encode(['code'=>1002,'data'=>'','msg'=>'Parameter Error']);
      }
      $uid = $food->UserOpenid($openid);
      $map = [];
      $map['audit_start'] = 0;//显示待审核菜单 所有用户
      if($pageId && $pageId > 1)
      {
          $map['id'] = ["<",$pageId];
      }
      $data = DB::name("veriety")
            ->where($map)
            ->limit(10)
            ->order("id desc")
            ->field("id,img,name,inventory,author,collect,user_like,time")
            ->select();
      foreach ($data as $key => $val) {
        $data[$key]['user_like'] = count(explode(",",$val['user_like']));
        $data[$key]['img'] = 'https://h5php.xingyuanauto.com/food/public'.$val['img'];
        $data[$key]['inventory'] = unserialize($val['inventory']);//清单
        $data[$key]['time'] = date("Y-m-d",$val['time']);//时间
      }

      //查询当前用户发布菜单总数
      $VerietyCount = DB::name("veriety")->where(['audit_start'=>0])->count();
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
