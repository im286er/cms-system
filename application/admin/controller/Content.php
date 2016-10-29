<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Menu as MenuModel;
use app\admin\model\News as NewsModel;
use app\admin\model\Position as PositionModel;
use app\admin\model\NewsContent ;
use app\admin\model\PositionContent ;
use think\Request;



class Content extends Controller
{
 public function index(Request $request){
 	$data = array();
//进行查询操作的查询条件
 	if($request->param('catid')){
 	$data['catid'] =$request->param('catid');
 	$this->assign('catid',$data['catid']);
 	}else{
 	$this->assign('catid',-1);
 	}
 	if($request->param('title')){
 	$data['title'] = array('like', '%'.$request->param('title').'%');
 	$this->assign('testtitle',$request->param('title'));
 	}else{
 		$this->assign('testtitle',null);
 	}
 	$data['status']=['neq',-1];
 	$param = $request->param();
 	$content = NewsModel::where($data)->order('listorder desc ,news_id desc')->paginate(3,false,
 		array('query' => $param)
 		);
 	$pages=$content->render();
 	$positions =PositionModel::where('status',1) ->select();
 	$this->assign('positions',$positions);
 	$this->assign('pages',$pages);
 	$this->assign('content',$content);
 	$webSiteMenu = $this->getBarMenu();
 	$this->assign('webSiteMenu',$webSiteMenu );
  	return $this->fetch();
    }


// 获取前端项目名称
    public function getBarMenu(){
    	$data = [
		'status' =>['neq', -1],
		'type' => 0,
		];
	$res = MenuModel::where($data)->order('listorder desc, menu_id desc')->select();
 	return  $res ;
    }

public function add(Request $request){
	if($_POST){
		if(!isset($_POST['title']) || !$_POST['title']){
			return show(0, '标题不存在');
		}
		if(!isset($_POST['small_title']) || !$_POST['small_title']) {
			return show(0, '短标题不存在');
		}
		if(!isset($_POST['catid']) || !$_POST['catid']){
			return show(0, '文章栏目不存在');
		}
		if(!isset($_POST['keywords']) || !$_POST['keywords']){
			return show(0, '关键字不存在');
		}
		if(!isset($_POST['content']) || !$_POST['content'] ){
			return show(0, 'content不存在');
		}
		if( $request->post('news_id')){
			return  $this->update($_POST);
		}
		$data = $_POST;
		$data['create_time'] = time();
		$data['username'] = getLoginUsername();
		$news = new NewsModel;
		 $news->allowField(true)->save($data);
		 //获取新增数据主键
		 $newsId =  $news->news_id;
		if($newsId){
			$newsContentData['content'] = htmlspecialchars($_POST['content']);
			$newsContentData['news_id'] = $newsId;
			$newsContentData['create_time'] = time();
			$content = new NewsContent;
			$cId = $content->allowField(true)->save($newsContentData);
			if($cId){
				return show(1,'新增成功' );
			}else{
				return show(1,'主表插入成功，附表插入失败' );
			}

		}else{
			return show(0,'新增失败');
		}


	}else{


	$titleFontColor = config('TITLE_FONT_COLOR');
	$copyFrom = config('COPY_FROM');
	$this->assign('webSiteMenu', $this->getBarMenu());
	$this->assign('titleFontColor', $titleFontColor);
	$this->assign('copyFrom', $copyFrom);
    	return $this->fetch();
    }
}

public function edit(Request $request){
	$newsId = $request->param('id');

	if(!$newsId){
		$this->redirect('/admin/content');
	}
	$news = NewsModel::get($newsId );

	if(!$news){
		$this->redirect('/admin/content');
	}
	$newsContent = NewsContent::get(['news_id'=>$newsId]);
	if($newsContent ){
		$news['content'] = $newsContent['content'];
	}

	$webSiteMenu = $this->getBarMenu();
	$this->assign('webSiteMenu', $webSiteMenu);
	$this->assign('titleFontColor',  config('TITLE_FONT_COLOR'));
	$this->assign('copyFrom', config('COPY_FROM'));
	$this->assign('news', $news);

	return $this->fetch();

}

public function update($data){
	$newsId = $data['news_id'];
	//更新操作不用传主键
	unset($data['news_id']);
	$data['update_time'] = time();
	if(isset($data['content']) && $data['content']){
	$data['content']=htmlspecialchars($data['content']);
	}
	try{
	if(!$newsId && !is_numeric($newsId)){
		throw_exception('ID不合法');
	}
	if(!$data && !is_array($data)){
		throw_exception('更新数据不合法');
	}
	$id = NewsModel::get($newsId)->allowField(true)->save($data);
	$conId = NewsContent ::get(['news_id'=>$newsId])->allowField(true)->save($data);
	if($id===false  && $conId===false  ){
	return show(0,'更新失败');
	}
	return show(1,'更新成功');
	}catch(Exception $e){
		return show(0, $e->getmessage());
	}

	if($id  && $conId  ){
	return show(1,'更新成功');
	}
}

      public  function  setStatus(){
      	if($_POST){
      		$id = $_POST['id'];
      		$status = $_POST['status'];
      	if(!$id){
      		return show(0, 'ID不存在');
      	}
      	$res = NewsModel::get($id)->allowField(true)->save($_POST);
      	if($res){
      		return show(1,'操作成功');
      	}else{
      		return show(0,'操作失败');
      	}
      }
      return show(0,'没有提交的内容');
  }

  public function listorder(){

  	$listorder = $_POST['listorder'] ;
  	//获取前一页的跳转地址
  	$jumpUrl = $_SERVER['HTTP_REFERER'];
  	$errors = array();
	if(isset($listorder)){
    		try{
    		foreach ($listorder as $newsId => $v) {
    			if(!isset($newsId) || !is_numeric($newsId)){
    				throw_exception('ID不合法');
    			}
    			$data['listorder']=$v;
 			$res=NewsModel::get($newsId)->allowField(true)->save($data);
 			if($res===false){
 				$errors[] = $newsId;
 			}
    		}}catch(Exception $e){
    			return show(0, $e->getMessage(), array('jump_url' => $jumpUrl)) ;
    		}
    		if($errors!=null){
    			return show(0, '排序失败-'.implode(',',$errors), array('jump_url' => $jumpUrl)) ;
    				}
    		    	return show(1, '排序成功', array('jump_url' => $jumpUrl)) ;

    	}
    		return show(0, '排序数据失败', array('jump_url' => $jumpUrl)) ;

  }

public function push(Request $request){
	//试用$jumpUrl = $request->param('HTTP_REFERER');
	$jumpUrl = $request->server('HTTP_REFERER');
	$positionId = intval($request->param('position_id'));
	$posts= $request->post();
	$newsId=$posts['push'];

	if(!$newsId || !is_array($newsId)){
		return show(0,'	请选择推荐的文章ID进行推荐');
	}
	if(!$positionId ){
		return show(0,'	没有选择推荐位');
	}
	try{
	if(!is_array($newsId)){
		throw_exception('参数不合法');
	}
	//查询语言 in 表示 在这几个值之中
	$data = [
	'news_id' => ['in', implode(',', $newsId)],
	];

	$news = NewsModel ::where($data)->select();
	if(!$news){
		return show(0, '没有相关内容');
	}

	foreach ($news as $new){
		$table = [
			'position_id' => $positionId,
			'title' => $new['title'],
			'thumb' => $new['thumb'],
			'news_id' => $new['news_id'],
			'status' =>1,
			'create_time' => $new['create_time'],
			];
		//create方法可以传入数组或者标准对象
		$position = PositionContent::create($table );
	}
	}catch(Exception $e){
		return show(0, $e->getMessage());
	}
	return show(1, '推荐成功', ['jump_url' => $jumpUrl]);


}

}