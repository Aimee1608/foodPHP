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
class SecureModel extends Model
{
    protected $table = '';

    
	/**
	 * 信息检测	//无效信息，返回1,默认返回0
	 * @param [type] $str [description]
	 */
	public function Ckencstr($str=null){ 
		//return 12;
		//首先解密，->匹配 son,qin,不存在，无效 ->拼接，检测session 是否存在，存在为无效信息
		//引用解密插件  
		//$str = '00def73ab839923d5b3073eb88220cfefb6d7ac428dc5ed20c8be2b1d4276792';
		if(!$str)
		{ //字符串不能为空,空为密钥错误
			return array("start"=>1005,"msg"=>"token值验证失败");
		} 
		$key = "o0o0o0o0o7ujm*IK<9o.00ew00O0O";// 密钥  
		$aes = new \aes\Aes($key);  
		$bfarr = $aes->decrypt($str);
		$keybfarr = str_replace(".",'',$bfarr);
		$keybfarr = str_replace("-",'',$keybfarr);
		
		//检测key值是否合法
		$endarr = explode("-",$bfarr); 
		if($endarr){ //查询有无 'son' , 'qin'
			$son = in_array('son',$endarr);
			$qin = in_array('qin',$endarr);
			if(!$son || !$qin){  // 密钥错误
				return array("start"=>1005,"msg"=>"token值验证失败");
			} 
		} 

		//检测该秘钥是否使用
		if($bfarr){
			$thes = Cache::get($keybfarr);
			// echo $thes;die; 
			if($thes){ //密钥错误
				return array("start"=>1006,"msg"=>"请刷新页面,请勿重复提交,密钥失效");
			}else{
				Cache::set($keybfarr,"qinlh",3); 
			}   
		} 
		return 1;
	}


    /**
     * 判断是否是手机
     * @return boolean [description]
     */
	function isMobile() { 
		  // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		  if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
		    return true;
		  } 
		  // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		  if (isset($_SERVER['HTTP_VIA'])) { 
		    // 找不到为flase,否则为true
		    return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		  } 
		  // 脑残法，判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
		  if (isset($_SERVER['HTTP_USER_AGENT'])) {
		    $clientkeywords = array('nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile','MicroMessenger'); 
		    // 从HTTP_USER_AGENT中查找手机浏览器的关键字
		    if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
		      return true;
		    } 
		  } 
		  // 协议法，因为有可能不准确，放到最后判断
		  if (isset ($_SERVER['HTTP_ACCEPT'])) { 
		    // 如果只支持wml并且不支持html那一定是移动设备
		    // 如果支持wml和html但是wml在html之前则是移动设备
		    if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
		      return true;
		    } 
		  } 
		  return false;
	}

	/**
	 * 判断是否是微信内置浏览器
	 * @return boolean [description]
	 */
	function isWeixin() 
	{ 
		  if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) 
		  { 
		    return true; 
		  } 
		  else 
		  {
		    return false; 
		  }
	}

	/**
	* 函数名称: getIP
	* 函数功能: 取得手机IP
	* 输入参数: none
	* 函数返回值: 成功返回string
	* 其它说明: 说明
	*/
	function getIP()
	{
		$ip=getenv('REMOTE_ADDR');
		$ip_ = getenv('HTTP_X_FORWARDED_FOR');
		if (($ip_ != "") && ($ip_ != "unknown"))
		{
			$ip=$ip_;
		}
		return $ip;
	}
	
	/**
	 * 检测当前ip
	 * @param  [type] $strip [description]
	 * @return [type]        [description]
	 */
	function secureIp($strip,$isMobile)
	{
		if($strip){
			if($strip['start'] == 1)
			{
				$time = time();//当前时间戳
				$times = $time - $strip['time'];
				if($times > 6*3600){
					DB::name("ipmobile")->where("ip",$isMobile)->update(['ipnum'=>0,"start"=>0]);
					return true;
				}
				exit(json_encode(array("start"=>1005,"msg"=>"当前ip注册频繁，请稍后重试","data"=>'')));
			}
			else
			{
				if($strip['ipnum'] > 5)//请求次数不能连续超过5次
				{
					DB::name("ipmobile")->where("ip",$isMobile)->update(["start"=>1]);
					exit(json_encode(array("start"=>1005,"msg"=>"当前ip注册频繁，请稍后重试","data"=>'')));
				}
				else
				{
					//更新访问ip次数
					DB::name("ipmobile")->where("ip",$isMobile)->update(["ipnum"=>$strip['ipnum'] + 1,"time"=>time()]);
					return true;
				}
			}
		}
		else
		{
			//当前ip没有记录 添加ip
			DB::name("ipmobile")->insert(['ip'=>$isMobile,'ipnum'=>1,"start"=>0,"time"=>time()]);
			return true;
		}
	}


	
}