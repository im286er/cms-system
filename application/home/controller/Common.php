<?php
namespace app\home\controller;


use think\Controller;

class Common  extends controller {


	public function errorto($message=''){
   	$message=$message ? $message : '系统发生错误';
   	$result['catId']=0;
   	$this->assign('result',$result);
   	$this->assign('message',$message);
   	return $this->fetch('index/errorto');
   }


}