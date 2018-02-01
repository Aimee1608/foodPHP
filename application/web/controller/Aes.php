<?php
/**
 * 极客之家 高端PHP - 生成秘钥 key
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
use app\port\model\SecureModel;
class Aes extends Controller
{
	/**
	 * 随机获取秘钥 key
	 * @param [type] $key [固定的key]
	 */
	public function GetEncrypt()
	{
		$key = "o0o0o0o0o7ujm*IK<9o.00ew00O0O";// 密钥  
		$aes = new \aes\Aes($key);  

		$ip = $_SERVER["REMOTE_ADDR"];  //获取当前ip
		 
		$time = substr(time(),-5); //获取当前时间戳

		$name1 = "son"; // key
		$name2 = "qin";	//key

		$num = rand(0,9999);
		$arr = array('ip'=>$ip,'tm'=>$time,'no'=>$name1,'tw'=>$name2,'nm'=>$num);
		shuffle($arr); //打乱数组顺序
		$arrstr = implode("-",$arr); 
		return $aes->encrypt($arrstr);//加密
	}

	/**
	 * 微信签名 
	 * @return [type] [description]
	 */
	public function wx_token()
	{
		$AppID = 'wxe3044990214ab2c9';//用户AppId
		$AppSecret = "da46a82a965dafcc75e029672b467981";//用户appserver
		$wx_token = new \jssdk\Jssdk($AppID,$AppSecret); 
		$result = $wx_token->getSignPackage();
		exit(json_encode($result));
	}
}