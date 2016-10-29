<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
use think\Controller;
use think\Request;
// 应用公共文件
function show($status, $message, $data=array()){
	$result=array(
		'status'=>$status,
		'message'=>$message,
		'data'=>$data,
		);
	exit(json_encode($result));
}

function getBarMenu1(){
    	$data = [
		'status' =>['neq', -1],
		'type' => 0,
		];
	$res = Db::name('menu')->where($data)->order('listorder desc, menu_id desc')->select();
 	return  $res ;
    }

    function getLoginUsername(){
	$request = Request::instance();
	return  $request->session('adminUser.username') ? $request->session('adminUser.username') :'' ;
}



