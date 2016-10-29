<?php
namespace app\admin\controller;
use app\admin\model\Position as PositionModel;
use think\Controller;
use think\Request;

class Position extends Controller{
	public function index(Request $request){
	$data['status']=['neq',-1];
	$param = $request->param();
	$positions = PositionModel::where($data)->order('id desc')->paginate(2,false,array('query' => $param));
	$page = $positions->render();
	$this->assign('positions',$positions);
 	$this->assign('page',$page);
	return $this->fetch();
	}

	public function add(Request $request){
		if($request->param()){
			$data['name'] = $request->param('name');
			$data['description'] = $request->param('description');
			$data['status'] = $request->param('status');
			$data['create_time'] = time();
			if(!isset($data['name']) || !$data['name']){
 				return show(0,'推荐位名称不能为空');
 			}
 			if(!isset($data['description']) || !$data['description']){
 				return show(0,'描述不能为空');
 			}
 			if(!isset($data['status'])){
 				return show(0,'状态值不能为空');
 			}
 			if($request->param('position_id')){
 				return $this->update($request->param());
 			}
 			$PositionModel = new PositionModel;
 			$res=$PositionModel->allowField(true)->save($data);
 			if($res===false){
 				return show(0,'提交失败');
 			}
 				return show(1,'提交成功');
		}else{
		return $this->fetch();
		}
	}

	public function edit(Request $request){
		$positionId = $request->param('id');
		if(!$positionId){
			$this->redirect('/admin/position');
		}
		$position = PositionModel::get($positionId);
		if(!$position){
		$this->redirect('/admin/position');
		}
    		$this->assign('position', $position);
		return $this->fetch();
	}

	public function update($data){
	$positionId = $data['position_id'];
	//更新操作不用传主键
	unset($data['position_id']);
	$data['update_time'] = time();
	try{
	if(!$positionId && !is_numeric($positionId)){
		throw_exception('ID不合法');
	}
	if(!$data && !is_array($data)){
		throw_exception('更新数据不合法');
	}
	$id = PositionModel::get($positionId)->allowField(true)->save($data);
	if($id===false){
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

	public function setStatus(Request $request){
	if($request->param()){
      		$id = $request->param('id');
      		$status = $request->param('status');
      	if(!$id){
      		return show(0, 'ID不存在');
      	}
      	$res = PositionModel::get($id)->allowField(true)->save($request->param());
      	if($res){
      		return show(1,'操作成功');
      	}else{
      		return show(0,'操作失败');
      	}
      	}
     	 return show(0,'没有提交的内容');
	}

}