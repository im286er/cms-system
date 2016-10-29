<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\Cache;

class Basic extends Controller{
public function index(){

	$data = ['title', 'keywords', 'description'];
	foreach($data as $value ){
		$vol[$value] = Cache::get($value);
	}
	$this->assign('vol', $vol);
	return $this->fetch();
}

public function add(Request $request){
	if($request->post()){
		if(!$request->post('title')){
			return show(0,'站点信息不能为空');
		}
		if(!$request->post('keywords')){
			return show(0,'站点关键字不能为空');
		}
		if(!$request->post('description')){
			return show(0,'站点描述不能为空');
		}
		$data = ['title', 'keywords', 'description'];
		foreach($data as $value ){
		Cache::set($value,$request->post($value));
		}
		return show(1,'保存成功');

	}else{
		return show(0,'没有提交的数据');
	}
}

}