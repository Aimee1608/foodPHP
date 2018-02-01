<?php
/**
 * 极客之家 高端PHP - 文件上传模块
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */
namespace app\admin\controller;
use think\Controller;
use think\File;
use think\Request;
class Upload extends Base
{

    /*
     * 上传焦点图图片
     */
    public function uploadAdImage(){
        $file = request()->file('file');
        // p($file);
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/testfile');
        if($info){
            $res['status'] = 1;
            $res['image_name'] = $info->getSaveName();
            return json($res);

        }else{

            $res['status']=0;
            $res['error_info']=$file->getError();
            return json($res);
        }
    }

    /**
     * [uploadProjectImage 添加菜单上传图片]
     * @return [type] [description]
     * @author [qinlh] [15035574759@163.com]
     */
    public function uploadProjectImage()
    {
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/testfile');//先移动到临时文件

        //如果图片大小大于100KB 开始压缩处理
        $file_url = "upload/testfile/".$info->getSaveName();
        $size = round(filesize($file_url) / 1024);
        if($size > 100){
          @$image = new \image\Image($file_url);
          @$image->compressImg($file_url);
        }

        if($info)
        {
            $res['status'] = 1;
            $res['image_name'] = $info->getSaveName();
            return json($res);
        }
        else
        {
            $res['status']=0;
            $res['error_info'] = $file->getError();
            return json($res);
        }
    }


    /**
     * [UploadExcel 上传Excel文件]
     * @return [type] [description]
     * @author [qinlh] [15035574759@163.com]
     */
    public function UploadExcel()
    {
        $file = request()->file('file');
        // print_r($file);die;
        $info = $file->move(ROOT_PATH . 'public' . DS . 'UploadFiles/excel');
        if($info)
        {
            $res['status'] = 1;
            $res['image_name'] = $info->getSaveName();
            return json($res);

        }
        else
        {

            $res['status']=0;
            $res['error_info'] = $file->getError();
            return json($res);
        }
    }

    /**
     * 删除原来上传文件
     * @param [type] $imgurl [源文件]
     */
    public function DelUpdate($imgurl)
    {
        if(strpos($imgurl,"tong.jpg"))
        {
            return true;
        }
        else
        {
            //删除文件
            unlink($imgurl);
            return true;
        }
    }


    /**
     * [zyUploads] 批量上传文件
     * @param  [string] [接受信息描述]
     * @return [type] [返回参数描述]
     * @author [qinlh] [WeChat QinLinHui0706]
     */
    public function zyupload()
    {
            $url = array();
            // 获取临时文件
            for($i=0;$i<count($_FILES['name']['name']);$i++)
            {
                //新文件地址
                $NewFile = "upload/testfile/".date('Ymd',time())."/";
                // echo $NewFile;die;
                if(!file_exists($NewFile))
                {
                    //检查是否有该文件夹，如果没有就创建，并给予最高权限
                    mkdir($NewFile);
                    chmod($NewFile,0777);
                }
                 //$name = $_FILES['name']['name'][$i];
                $name = $_FILES['name']['name'][$i];//文件名称
                $file_name = date("YmdHis").rand(1000,999999).substr($name,strripos($name,'.'));
                // $names = implode(",", $name);
                $path = $_FILES['name']['tmp_name'][$i];//文件位置
                // print_r($path);
                //移动到指定文件
                $res = move_uploaded_file($path,$NewFile.$file_name);

                //如果图片大小大于100KB 开始压缩处理
                $file_url = $NewFile.$file_name;
                $size = round(filesize($file_url) / 1024);
                if($size > 100){
                  @$image = new \image\Image($file_url);
                  @$image->compressImg($file_url);
                }

                $url[] = $NewFile.$file_name;
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









//*********************************************未启用*********************************************//
    /*
     * 编辑器图片上传接口
     */
    public function uploadImage(){
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/editor');
        if($info){
            $res['src']="http://www.93admin.com/public/statisc/img/tong.jpg";
            $result=array('code'=>0,'data'=>$res,'msg'=>'上传成功');

            return json($result);

        }else{

            $result=array('code'=>-1,'data'=>'','msg'=>$file->getError());

            return json($result);
        }
    }


	//图片上传
    public function upload(){
       $file = request()->file('file');
       $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/image/article');
       if($info){
           $res['status']=1;
           $res['image_name']=$info->getSaveName();
           return json($res);
        }else{
           $res['status']=0;
           $res['error_info']=$file->getError();
           return json($res);
        }
    }

    //文件上传
    public function uploadFile(){
        $file = request()->file('file');
        $info = $file->move(ROOT_PATH . 'public' . DS . 'upload/file/down');
        if($info){

            $res['status']=1;
            $res['file_name']=$info->getFilename();
            $res['file_path']="/upload/file/down/".$info->getSaveName();
            return json($res);

        }else{
            $res['status']=0;
            $res['error_info']=$file->getError();
            return json($res);
        }
    }

    //会员头像上传
    public function uploadface(){
       $file = request()->file('file');
       $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/face');
       if($info){
            echo $info->getSaveName();
        }else{
            echo $file->getError();
        }
    }
//*********************************************结束*********************************************//




}
