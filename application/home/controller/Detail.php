<?php
namespace app\home\controller;
use app\home\controller\Common;
use app\admin\model\NewsContent as NewsContentModel  ;
use app\admin\model\PositionContent as PositionContentModel  ;
use app\admin\model\News as NewsModel  ;
use think\Request;
use think\Db;

class Detail  extends Common{
	public function index(){
		$request = Request::instance();
		$id = intval($request->get('id'));
		if(!$id || $id<0){
			return $this->errorto('ID不存在');
		}
	$news= Db::name('news')->where('news_id',$id) ->find();
	if(!$news || $news['status']!=1){
		return $this->errorto('ID不存在或者资讯被关闭');
	}
	$count=intval($news['count']) + 1;
	Db::name('news')->where('news_id',$id) ->update(['count'=>$count]);
	$content = NewsContentModel ::where('news_id',$id)->find();
	$news['content']=htmlspecialchars_decode($content['content']);
	$rankNews = NewsModel::where(['status'=>1])->limit(10)->order('count desc,news_id')->select();
	$advNews = PositionContentModel::where(['status'=>1,'position_id'=>5])->order('listorder desc')->limit(2)->select();
	$this->assign('result',[
		'rankNews'=>$rankNews,
		'advNews'=>$advNews,
		'catId'=>$news['catid'],
		'news'=>$news,
		]);
	return $this->fetch('detail/index');
	}

	public function viewsee(){

		if(!getLoginUsername()){
			$this->errorto('你没有权限访问该页面');
		}

		return $this->index();
	}

}