<?php
//namespace aes;
/**
 * 极客之家 高端PHP - AES加密解密封装类
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
class Aes
{
    private $secrect_key;

    public function __construct($secrect_key)
    {
        $this->secrect_key = $secrect_key;
    }

    /**
     * 加密
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function encrypt($str)
    {
        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = $this->createIv($cipher);
        if (mcrypt_generic_init($cipher, $this->pad2Length($this->secrect_key, 16), $iv) != -1){
            // PHP pads with NULL bytes if $content is not a multiple of the block size..    
            $cipherText = mcrypt_generic($cipher, $this->pad2Length($str, 16));
            mcrypt_generic_deinit($cipher);
            mcrypt_module_close($cipher);
            return bin2hex($cipherText);
        }
    }

    /**
     * 解密
     * @param  [type] $str [description]
     * @return [type]      [description]
     */
    public function decrypt($str)
    {
        $padkey = $this->pad2Length($this->secrect_key, 16);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = $this->createIv($td);
        if (mcrypt_generic_init($td, $padkey, $iv) != -1){    
            $p_t = mdecrypt_generic($td, $this->hexToStr($str));
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);
               
            return $this->trimEnd($p_t);  
        }    
    }

    /**
     * IV自动生成
     * @param  [type] $td [description]
     * @return [type]     [description]
     */
    private function createIv($td)
    {
        $iv_size = mcrypt_enc_get_iv_size($td); 
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        return $iv;
    }

    /**
     * 将$text补足$padlen倍数的长度
     * @param  [type] $text   [description]
     * @param  [type] $padlen [description]
     * @return [type]         [description]
     */
    private function pad2Length($text, $padlen)
    {
        $len = strlen($text)%$padlen;
        $res = $text;
        $span = $padlen-$len;
        for ($i=0; $i<$span; $i++) {
            $res .= chr($span);
        }
        return $res;
    }

    /**
     * 将解密后多余的长度去掉(因为在加密的时候 补充长度满足block_size的长度)
     * @param  [type] $text [description]
     * @return [type]       [description]
     */
    private function trimEnd($text){    
        $len = strlen($text);
        $c = $text[$len-1];
        if(ord($c) <$len){
            for($i=$len-ord($c); $i<$len; $i++) {
                if($text[$i] != $c){
                    return $text;
                }
            }
            return substr($text, 0, $len-ord($c));
        }
        return $text;
    }

    /**
     * 16进制的转为2进制字符串
     * @param  [type] $hex [description]
     * @return [type]      [description]
     */
    private function hexToStr($hex){
        $bin="";
        for($i=0; $i<strlen($hex)-1; $i+=2) {
            $bin.=chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $bin;
    }
}
