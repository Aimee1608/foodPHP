<?php
namespace jssdk;
/**
 * 极客之家 高端PHP - 微信公众号接口权限验证类
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2017-3-13 13:51:52
 */
class Jssdk
{
    private $AppID;//用户AppId
    private $AppSecret;//用户appserver

    public function __construct($AppID,$AppSecret)
    {
        $this->appId = $AppID;
        $this->appServer = $AppSecret;
    }

    /**
     * 获取参数
     * @return [type] [description]
     */
    function getSignPackage() {
      $jsapiTicket = $this->getJsApiTicket();
      if(!isset($jsapiTicket) || empty($jsapiTicket)){
          return array('start'=>0,'message'=>'jsapi_ticket失败');
      }
      // 注意 URL 一定要动态获取，不能 hardcode.
      $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      $Purl = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
      
      $url = (!empty($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : "";//获取来源网站

       if (empty($url)) {
          $url = $Purl;
        }

      $timestamp = time();
      $nonceStr = $this->createNonceStr();
      if(!isset($nonceStr) || empty($nonceStr)){
          return array('start'=>0,'message'=>'签名获取失败');
      }
      // 这里参数的顺序要按照 key 值 ASCII 码升序排序
      $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

      $signature = sha1($string);

      return array(
        "appId"     => $this->appId,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string
      );
      
    }

    /**
   * 签名算法实现
   * @param  integer $length [description]
   * @return [type]          [description]
   */
    function createNonceStr($length = 16) {
      $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
      $str = "";
      for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
      }
      return $str;
    }

    /**
     * jsapi_ticket生成
     * @return [type] [description]
     */
    function getJsApiTicket() {
      // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
      $data = json_decode($this->get_php_file("../extend/jssdk/jsapi_ticket.php"));
      if (empty($data) || $data->expire_time < time()) {
        $accessToken = $this->getAccessToken();
        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
        // $res = json_decode(httpGet($url));
        $res = json_decode(file_get_contents($url));
        // print_r($res);die;
        $ticket = $res->ticket;
        if ($ticket) {
          $data->expire_time = time() + 7000;
          $data->jsapi_ticket = $ticket;
          $this->set_php_file("../extend/jssdk/jsapi_ticket.php", json_encode($data));
        }
      } else {
        $ticket = $data->jsapi_ticket;
      }

      return $ticket;
    }

    /**
     * access_token生成
     * @return [type] [description]
     */
    function getAccessToken() {
      // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
      $data = json_decode($this->get_php_file("../extend/jssdk/access_token.php"));
      if (empty($data) || $data->expire_time < time()) {
        // 如果是企业号用以下URL获取access_token
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appId."&secret=".$this->appServer;
        // $res = json_decode(httpGet($url));
        $res = json_decode(file_get_contents($url));
        $access_token = $res->access_token;
        if ($access_token) {
          $data->expire_time = time() + 7000;
          $data->access_token = $access_token;
          $this->set_php_file("../extend/jssdk/access_token.php", json_encode($data));
        }
      } else {
        $access_token = $data->access_token;
      }
      return $access_token;
    }

    /**
     * 请求
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    function httpGet($url) {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_TIMEOUT, 500);
      // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
      // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($curl, CURLOPT_URL, $url);

      $res = curl_exec($curl);
      curl_close($curl);

      return $res;
    }

    /**
     * 读取文件值
     * @param  [type] $filename [description]
     * @return [type]           [description]
     */
    function get_php_file($filename) {
      return trim(substr(file_get_contents($filename), 15));
    }

    /**
     * 写入文件值
     * @param [type] $filename [description]
     * @param [type] $content  [description]
     */
    function set_php_file($filename, $content) {
      $fp = fopen($filename, "w");
      fwrite($fp, "<?php exit();?>" . $content);
      fclose($fp);
    }

     
}