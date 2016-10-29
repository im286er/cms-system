<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Menu as MenuModel;
use think\Request;

class Menu extends Controller
{
 public function index(Request $request){

	$data=[];
	$res = $request->param('type');
	if(isset($res)){
 		$data['type'] = $res;
		$this->assign('type', $data['type']);
	}else{
		$this->assign('type', -1);
	}

	$pageSize= $request->param('p')?$request->param('p'):3;
	$param = $request->param();
 	$menus = MenuModel::where($data)->where('status', 'in', [0,1])->order('listorder desc, menu_id desc')->paginate($pageSize,false,
 		['query' => $param]
 		);
 	$page = $menus->render();
 	$this->assign('menus',$menus);
 	$this->assign('page',$page);
 	return $this->fetch();

    }

   public function add(){
    	if($_POST){
 		if(!isset($_POST['name']) || !$_POST['name']){
 			return show(0,'菜单名不能为空');
 		}
 		if(!isset($_POST['m']) || !$_POST['m']){
 			return show(0,'模块名不能为空');
 		}
 		if(!isset($_POST['c']) || !$_POST['c']){
 			return show(0,'控制器不能为空');
 		}
 		if(!isset($_POST['f']) || !$_POST['f']){
 			return show(0,'方法名不能为空');
 		}

		if(isset($_POST['menu_id'])){
		    return    $this->update($_POST);
		}

 		$menu = new MenuModel;
 		$res=$menu->save(input('post.'));
 		if(isset($res)){
 			return show(1,'新增成功',$menu);
 		}
 			return show(0,'新增失败',$menu);
 	}
 	else{
	return $this->fetch();
 		}

    }

    public function edit(Request $request){
    	$menuId = $request->param('id');
    	$menu = MenuModel::get($menuId);
    	$this->assign('menu', $menu);
    	return $this->fetch();
    }

    public function update($data){
    	$menuId = $data['menu_id'];
    	unset($data['menu_id']);
    	try{
    	if(!$menuId || !is_numeric($menuId)){
    		throw_exception("ID不合法");
    	    }
    	if(!$data || !is_array($data)){
    		throw_exception("更新的数据不合法");
    	    }
    	$menu = MenuModel::get($menuId);
 	$res=$menu->isUpdate()->save($data);
 	if($res===false){
 		return show(0,'更新失败',$menu);
 	}
 		return show(1,'更新成功',$menu);
 	}catch(Exception $e){
 		return show(0, $e->getMessage());
 	}
    }

    public function setStatus(){
    	try{
    	    if(isset($_POST)){
    		$id = $_POST['id'];
    		$status =$_POST['status'];
		if(!is_numeric($id)  || !$id){
    			throw_exception("ID不合法");
    		}
		if(!is_numeric($status) || !$status){
    			throw_exception("状态不合法");
    		}
    		$data['status'] =$status;
    		$menu = MenuModel::get($id);
 		$res=$menu->isUpdate()->save($data);
 		if(isset($res)){
 			return show(1,'操作成功');
 	    }else{
 		return show(0,'操作失败');
    		}
	    }
   	 }catch(Exception $e){
    	    return show (0,$e->getMessage());
    	}
    	return show(0, '没有提交的数据');
    }

    public function listorder(){
    	$listorder = $_POST['listorder'];
    	$jumpUrl = $_SERVER['HTTP_REFERER'];
    	$errors = array();
    	if(isset($listorder)){
    		try{
    		foreach ($listorder as $menuId => $v) {
    			if(!isset($menuId) || !is_numeric($menuId)){
    				throw_exception('ID不合法');
    			}
    			$data['listorder']=$v;
    			$menu = MenuModel::get($menuId);
 			$res=$menu->isUpdate()->save($data);
 			if($res===false){
 				$errors[] = $menuId;
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
}