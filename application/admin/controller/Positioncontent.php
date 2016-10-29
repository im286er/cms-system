<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Position as PositionModel;
use app\admin\model\PositionContent as PositionContentModel  ;
use app\admin\model\News as NewsModel;
use think\Request;

class Positioncontent extends Controller{
	public function index(Request $request){
		$data['status']=['neq',-1];

		$webSiteMenu = $this->getBarMenu();
		if($request->param('positionId')){
 		$data['position_id'] =$request->param('positionId');
 		$this->assign('positionId',$data['position_id']);
 		}else{
 		$data['position_id'] = $webSiteMenu[0]['id'];
 		$this->assign('positionId', $data['position_id']);
 		}
 		if($request->param('title')){
 		$data['title'] =trim(['like', '%'.$request->param('title').'%']);
 		$this->assign('testtitle',$request->param('title'));
 		}else{
 		$this->assign('testtitle',null);
 		}

		$param = $request->param();
		$positions = PositionContentModel::where($data)->order('listorder desc ,id desc')->paginate(3,false,
 		array('query' => $param)
 		);
 		$page = $positions->render();
		$this->assign('pages',$page);
		$this->assign('positions', $positions);
		$this->assign('webSiteMenu',$webSiteMenu);

		return $this->fetch();
	}

	public function getBarMenu(){
    	$data = [
		'status' =>['neq', -1],
		];
	$res = PositionModel::where($data)->order('id desc')->select();
 	return  $res ;
    }

    public function listorder(Request $request){
  	$listorder = $request->post('listorder/a');

  	//获取前一页的跳转地址

  	$jumpUrl = $request->server('HTTP_REFERER');
  	$errors = array();
	if(isset($listorder)){
    		try{
    		foreach ($listorder as $positionId => $v) {
    			if(!isset($positionId) || !is_numeric($positionId)){
    				throw_exception('ID不合法');
    			}
    			$data['listorder']=$v;
 			$res=PositionContentModel::get($positionId)->allowField(true)->save($data);
 			if($res===false){
 				$errors[] = $positionId;
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

  public function add(Request $request){
  	if( $request->post()){
  		$data =  $request->post();
		if(!isset($data['position_id']) || !$data['position_id']){
		return show(0,'推荐位ID不能为空');
		}
		if(!isset($data['title']) || !$data['title']){
		return show(0,'标题不能为空');
		}
		if(!isset($data['url']) && !$data['news_id']){
		return show(0,'url和文章id不能同时为空');
		}

		if(!isset($data['thumb']) || !$data['thumb']){
			if($data['news_id']){
				$res = NewsModel::get($data['news_id']);
				if($res){
					$data['thumb'] = $res->thumb;
				}
			}else{
				return show(0, '图片不能为空');
			}
		}
		if(!isset($data['status']) || !in_array($data['status'],[-1,0,1])){
		return show(0,'状态不能为空');
		}
		if(!$data['news_id']){
			$data['news_id'] = null;
		}

		if($request->post('id')){
			$positionEdit = $request->post();
			return $this->update($positionEdit );
		}
		$data['create_time'] = time();
		$position = new PositionContentModel;
		$id = $position->allowField(true)->save($data);
		if($id){
			return show(1,'新增成功');
		}
		return show(0, '新增失败');
  	}else{
  	$position=$this->getBarMenu();
  	$this->assign('position', $position);
  	return $this->fetch();
  	}
  }
public function update($data){
	$positionId = $data['id'];
	//更新操作不用传主键
	unset($data['id']);
	$data['update_time'] = time();
	try{
	if(!$positionId && !is_numeric($positionId)){
		throw_exception('ID不合法');
	}
	if(!$data && !is_array($data)){
		throw_exception('更新数据不合法');
	}
	$id = PositionContentModel::get($positionId)->allowField(true)->save($data);
	if($id===false ){
	return show(0,'更新失败');
	}
	return show(1,'更新成功');
	}catch(Exception $e){
		return show(0, $e->getmessage());
	}
}

  public function edit(Request $request){
  	$id = $request->get();
  	$position = PositionContentModel::get($id);

  	$positionName=$this->getBarMenu();
  	$this->assign('position', $positionName);
  	$this->assign('positioncontent', $position);
  	return $this->fetch();
  }

   public  function  setStatus(){
      	if($_POST){
      		$id = $_POST['id'];
      		$status = $_POST['status'];
      	if(!$id){
      		return show(0, 'ID不存在');
      	}
      	$res = PositionContentModel::get($id)->allowField(true)->save($_POST);
      	if($res){
      		return show(1,'操作成功');
      	}else{
      		return show(0,'操作失败');
      	}
      }
      return show(0,'没有提交的内容');
  }


}