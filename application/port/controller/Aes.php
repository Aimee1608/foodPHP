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

	public function fetch_test()
	{
		Config::set('cache',array('type'=>'memcache'));
		// $cache = Cache::getInstance('memcache',array('expire'=>60));
		Cache::set('key',array('name'=>'秦林慧','password'=>123456789),3600);
		$name = Cache::get('key');//读取缓存
		p($name);
		if($_FILES)
		{
			$file = request()->file('file');
			$info = $file->move(ROOT_PATH . 'public' . DS . 'upload/testfile');
		}
		else
		{
		  echo "empty";
		}
	}

	public function ffmpeg()
	{
		$type = 'way';
		$real_file = "wxfile://store_f0d1a1daf739ce0c003cae6f153ab994a953788ac44e7a756c267abe4d0f12c0.silk";
		$cmd = "ffmpeg/converter.sh $real_file $type";
		exec($cmd,$out);
		// $new_filename = str_replace('silk', 'mp3', $filename);
  //       $command = "sh /mntdir/silk_decoder/converter_beta.sh ".$file1." mp3";
  //       exec($command);
	}

	/*
     	递归查询所有分类
      */
     function getAll($node,$pid=0,$leave=0){
     	static $result;
     	foreach ($node as $key => $val) {
     		if($val['pid']==$pid){
     			$val['leave']=$leave;
     			$result[]=$val;
     			$this->getAll($node,$val['node_id'],$leave+1);
     		}
     	}
     	//print_r($result);die;
     	return $result;
     }
}
