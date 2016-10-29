<?php
namespace app\home\controller;
use app\home\controller\Common;
use app\admin\model\Menu as MenuModel  ;
use app\admin\model\News as NewsModel  ;
use app\admin\model\PositionContent as PositionContentModel  ;
use think\Request;
use think\Db;

class Cat  extends Common {
	public function index(Request $request){
		$id = intval($request->get('id'));
		if(!$id){
			return $this->errorto('ID不存在');
		}
		$nav= Db::name('menu')->where('menu_id',$id) ->find();
		if(!$nav || $nav['status']!=1){
			return $this->errorto('栏目ID不存在或者状态不为正常');
		}
		$advNews = PositionContentModel::where(['status'=>1,'position_id'=>5])->order('listorder desc')->limit(2)->select();
		$rankNews = NewsModel::where(['status'=>1])->limit(10)->order('count desc,news_id')->select();
		$param = $request->param();
		$listNews=NewsModel::where(['catid'=>$id])->order('count desc,news_id')->paginate(3,false,['query' => $param]);
 		$page = $listNews->render();
		 $this->assign('result',[
            			           'advNews'=>$advNews,
            			           'rankNews'=>$rankNews,
            			           'catId'=>$id,
            			           'listNews'=>$listNews,
            			           	] );
		 $this->assign('page',$page);
		return $this->fetch();
	}

}
