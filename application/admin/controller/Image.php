<?php
/*
图片相关功能
 */
namespace app\admin\controller;
use think\Controller;
use think\Request;

class Image extends Controller
{
public function ajaxuploadimage(Request $request){
	        $file = $request->file('file');
	        $path = ROOT_PATH . 'public' . DS . 'uploads' ;
	        $info = $file->move($path);
	         $info->getSaveName();
	        $res = '/uploads/'. $info->getSaveName();
	        if($res===false){
	        return show(0,'上传失败','');
	        }else{
	        return show(1,'上传成功',$res);
	    }

}

public function kindupload(Request $request){
	   $file = $request->file('imgFile');
	   $path = ROOT_PATH . 'public' . DS . 'uploads' ;
	    $info = $file->move($path);
	    $res = '/uploads/'. $info->getSaveName();
	     if($res===false){
	        return showKind(1, '上传失败');
	        }else{
	        return showKind(0,$res);
	    }



}

}