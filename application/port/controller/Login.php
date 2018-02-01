<?php
/**
 * 极客之家 高端PHP - 美食小程序用户授权登录
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\port\controller;
use think\Controller;
use think\Request;
use think\Db;
use app\port\model\LoginModel;
use think\Session;
use think\Cookie;
use think\Cache;
use wxBizDataCrypt\wxBizDataCrypt;
class Login extends Controller
{
    public function index()
    {
        // session('session_key','555');
        // $sessionKey = session('session_key');
        // var_dump($sessionKey);die;
        // $time = time();
        return $this->fetch('login');
    }

    /**
     * [sendCode] 调用接口获取登录凭证（code）进而换取用户登录态信息
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public  function sendCodeLogin()
    {
        //这里要配置你的小程序appid和secret
        $appid = "wx772b2ddb98b4de78";
        $secret = "5b9412479cdd99059e26e391e74c2806";
        $code = input("param.code") ? input("param.code") : '';
        $encryptedData = input('param.encryptedData') ? input('param.encryptedData') : '';
        $iv = input('param.iv') ? input('param.iv') : '';
        // $code = "011xdFHU0tuuwU1VceLU00fKHU0xdFHq";
        if(empty($code) || empty($encryptedData) || empty($iv))
        {
            return json_encode(['errCode'=>1002,'errMsg'=>'Parameter Error']);
        }

        //根据 code 或取 session_key
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
        $data = file_get_contents($url);
        $arr = json_decode($data,true);
        // p($arr);
        //请求失败返回
        if(isset($arr['errcode']) && (!isset($arr['openid']) || (!isset($arr['session_key'])))){
            // return (array('code'=>-1,'msg'=>'获取信息失败'));
            return json_encode(['errCode'=>1004,'errMsg'=>'获取信息失败']);
        }
        //查询用户是否存在
        $map['openid'] = $arr['openid'];
        $UserDataAll = DB::name('user')->where($map)->field('openid,uid,admin_start')->find();
    		//获取PHPSESSID
    		$PHPSESSID = $this->getRandomString(27);
        if(false == $UserDataAll)
        { //用户不存在
            //开始获取用户信息
            $result = import("wxBizDataCrypt",EXTEND_PATH.'wxBizDataCrypt');
            $pc = new \wxBizDataCrypt($appid, $arr['session_key']); //解密 session_key
            $errCode = $pc->decryptData($encryptedData, $iv, $data ); //返回用户信息
            if ($errCode == 0) {
                $data = json_decode($data, true);
                // p($data);
                // return $data;
                // session('myinfo', $data);
                // Cache::set('myinfo'.$openid,$data); //存储用户信息

                $UserDataAll = array( //用户信息
                        'openid'   =>     $arr['openid'],
                        'token'   =>      'gh_6d3bf5d72981',
                        'nickname' => $data['nickName'],
                        'sex' => $data['gender'],
                        'city' => $data['city'],
                        'province' => $data['province'],
                        'country' => $data['country'],
                        'headimgurl' => $data['avatarUrl'],
                        'reg_time'   =>   time(),
                        'last_login_time' => time(),  //最近授权更新时间
                        'admin_start' => 0 //默认不是管理员
                    );

                //开始添加用户信息
                DB::name('user')->insert($UserDataAll);
                $userId = DB::name('user')->getLastInsID();
                if($userId >= 0)
                {
          					// return $PHPSESSID;
          					$returnData = [
          						'openid' => $arr['openid'],
          						'PHPSESSID'=>$PHPSESSID,
          						'isManager' => $UserDataAll['admin_start'] //是否为管理员
          					];
                    return json_encode(['code'=>1001,'data'=>$returnData,'msg'=>'Success Ok']);
                }
                else
                {
                    return json_encode(['errCode'=>1005,'errMsg'=>'用户信息保存失败']);
                }
            }
            else
            {
                return json_encode(['errCode'=>1008,'errMsg'=>'错误编号：'.$errCode]);
            }
        }
  		else
  		{//已经注册，直接返回
  			// return $PHPSESSID;
  			$returnData = [
  					'openid' => $arr['openid'],
  					'PHPSESSID'=>$PHPSESSID,
  					'isManager' => $UserDataAll['admin_start'] //是否为管理员
  				];
  			return json_encode(['code'=>1001,'data'=>$returnData,'msg'=>'Success Ok']);
  		}
    }

    /**
     * 生成随机session_id
     * @param  [type] $len   [description]
     * @param  [type] $chars [description]
     * @return [type]        [description]
     */
    function getRandomString($len, $chars=null)
    {
        if (is_null($chars)){
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++){
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }


     /**
      * [saveUserInfo] 修改用户数据 保存用户数据
      * @param  [string] [接受信息描述]
      * @return [type] [返回参数描述]
      * @author [qinlh] [WeChat QinLinHui0706]
      */
    // function saveUserInfo(){
    //     $encryptedData = input('param.encryptedData') ? input('param.encryptedData') : '';
    //     $iv = input('param.iv') ? input('param.iv') : '';
    //     $openid = input('param.openid') ? input('param.openid') : '';
    //     // return $iv;
    //     if(empty($encryptedData) || empty($iv) || empty($openid)){
    //         return json_encode(['errCode'=>1002,'errMsg'=>'传递信息不全']);
    //     }

    //     $appid = 'wx772b2ddb98b4de78';  //这里配置你的小程序appid
    //     $sessionKey = Cache::get('session_key'.$openid);//获取会话秘钥
    //     Cache::rm('session_key'.$openid);
    //     $result = import("wxBizDataCrypt",EXTEND_PATH.'wxBizDataCrypt');
    //     $pc = new \wxBizDataCrypt($appid, $sessionKey); //解密 session_key
    //     $errCode = $pc->decryptData($encryptedData, $iv, $data ); //返回用户信息
    //     // return $errCode;
    //     if ($errCode == 0) {
    //         $data = json_decode($data, true);
    //         // return $data;
    //         // session('myinfo', $data);
    //         // Cache::set('myinfo'.$openid,$data); //存储用户信息

    //         $save = array( //用户信息
    //                 'nickname' => $data['nickName'],
    //                 'sex' => $data['gender'],
    //                 'city' => $data['city'],
    //                 'province' => $data['province'],
    //                 'country' => $data['country'],
    //                 'headimgurl' => $data['avatarUrl'],
    //                 'last_login_time' => time()  //最近授权更新时间
    //             );

    //         $uid =  Cache::get('mid'.$openid); //获取用户自增ID
    //         Cache::rm('mid'.$openid);
    //         if(empty($uid)){
    //             return json_encode(['errCode'=>1005,'errMsg'=>'获取用户ID异常']);
    //         }

    //         //开始修改用户信息
    //         $res = DB::name('user')->where("uid",$uid)->update($save);
    //         if(true == $res)
    //         {
    //             return json_encode(['code'=>1001,'data'=>'','msg'=>'Success Ok']);
    //         }
    //         else
    //         {
    //             return json_encode(['errCode'=>1005,'errMsg'=>'用户信息保存失败']);
    //         }

    //     }
    //     else
    //     {
    //         return json_encode(['errCode'=>1008,'errMsg'=>'错误编号：'.$errCode]);
    //     }
    // }

    /**
     * 模拟Popst提交
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    function post_data($url){
        //模拟post请求
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作

        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }
}
