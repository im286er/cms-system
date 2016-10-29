<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
	public function  getLastLoginUser(){
		$time = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$data=[
		'status'=>1,
		'lastlogintime' =>['gt',$time],
		];
		$res=$this->where($data)->count();
		return $res;
	}


}