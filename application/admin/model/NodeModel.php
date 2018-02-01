<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class NodeModel extends Model
{

    protected $name = "auth_rule";


    /**
     * [getNodeInfo 获取节点数据]
     * @author [jonny] [980218641@qq.com]
     */
    public function getNodeInfo($id)
    {
        $result = $this->field('id,title,pid')->select();
        $str = "";
        $role = new UserType();
        $rule = $role->getRuleById($id);

        if(!empty($rule)){
            $rule = explode(',', $rule);
        }
        foreach($result as $key=>$vo){
            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['pid'] . '", "name":"' . $vo['title'].'"';

            if(!empty($rule) && in_array($vo['id'], $rule)){
                $str .= ' ,"checked":1';
            }

            $str .= '},';
        }

        return "[" . substr($str, 0, -1) . "]";
    }


    /**
     * [getMenu 根据节点数据获取对应的菜单]
     * @author [jonny] [980218641@qq.com]
     */
    public function getMenu($nodeStr = '')
    {

        //超级管理员没有节点数组
        $where = empty($nodeStr) ? 'status = 1' : 'status = 1 and id in('.$nodeStr.')';
        $result = Db::name('auth_rule')->where($where)->order('sort')->select();
        // print_r($result);die;
        // if(config('template')['theme_name'] == "default"){
            $new_result=array();
            foreach($result as $k=>$v){
                $new_result[$k]['id']=$v['id'];
                $new_result[$k]['pid']=$v['pid'];
                $new_result[$k]['name']=$v['name'];
                $new_result[$k]['title']=$v['title'];
                $new_result[$k]['icon']=$v['css'];

                $new_result[$k]['spread']=false;
                // $new_result[$k]['href']=$v['href'];
            }
            $menu = getMenuList($new_result);
            // print_r($menu);die;
        // }else{
            // $menu = prepareMenu($result);
        // }

        return $menu;
    }
}