<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\News;
use app\admin\model\Position;
use app\admin\model\Admin as AdminModel;

class Index extends Controller
{
 public function index(){
 	$news=new News();
 	$count['newsCount']=$news->maxcount();
 	$positions=new Position();
 	$count['positionCount'] = $positions->Pmaxcount();
 	$login=new AdminModel();
 	$count['loginCount'] =  $login->getLastLoginUser();
 	$this->assign('count',$count);
    	return $this->fetch();
    }
}