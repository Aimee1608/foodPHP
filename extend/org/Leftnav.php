<?php
/**
 * 极客之家 高端PHP - 公共一些方法
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace org;

class leftnav{

	/**
	 * [rule]  递归处理函数 查询权限菜单
	 * @param  [string] [接受信息描述]
	 * @return [type] [返回参数描述]
	 * @author [qinlh] [WeChat QinLinHui0706]
	 */
	static public function rule($cate ,$lefthtml = '— — ', $pid=0 ,$lvl=0, $leftpin=0 ){
		$arr=array();
		foreach ($cate as $v){
			if($v['pid']==$pid){
				$v['lvl']=$lvl + 1;
				$v['leftpin']=$leftpin + $lvl*20;//左边距
				$v['lefthtml']=str_repeat($lefthtml,$lvl);
				//$v['lefthtml']='<span style="display:inline-block;width:24px;"></span>'.$lefthtml;//str_repeat($lefthtml,$lvl);
				$arr[]=$v;
				$arr= array_merge($arr,self::rule($cate,$lefthtml,$v['id'] ,$lvl+1, $leftpin+15));
			}
		}
		return $arr;
	}
	
}


?>