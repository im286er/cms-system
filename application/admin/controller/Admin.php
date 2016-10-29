<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Admin as AdminModel;
use think\Request;

class Admin extends Controller{
	public function index(){
		$data=[
		'status'=>['neq','-1'],
		];
		$admins = AdminModel::where($data)->order('admin_id')->paginate(3);

		$page = $admins->render();
		$this->assign('admins',$admins);
		$this->assign('page',$page);
		return $this->fetch();
	}

	public function add(Request $request){

		if($request->post()){

		$data =$request->post();
 		if(!isset($data['username']) || !$data['username']){
 			return show(0,'用户名不能为空');
 		}
 		if(!isset($data['password']) || !$data['password']){
 			return show(0,'密码不能为空');
 		}
 		if(!isset($data['realname']) || !$data['realname']){
 			return show(0,'真实姓名不能为空');
 		}
 		$names = AdminModel::where('username',$data['username']) ->find();
 		if($names['username'] || $names['status']==-1){
 			return show(0,'该用户已存在');
 		}
 	 	$admins = new AdminModel;
 	 	$data['password'] = md5($request->post('password'));
		$id = $admins->allowField(true)->save($data);
		if($id){
			return show(1,'新增成功');
		}
		return show(0, '新增失败');
		}else{
		return $this->fetch();
	}
}

public  function  setStatus(Request $request){
      	if($request->post()){

      		$id = $request->post('id');
      		$status =$request->post('status');
      	if(!$id){
      		return show(0, 'ID不存在');
      	}
      	$res = AdminModel::get($id)->allowField(true)->save($request->post());
      	if($res){
      		return show(1,'操作成功');
      	}else{
      		return show(0,'操作失败');
      	}
      }
      return show(0,'没有提交的内容');
  }

  public function personal(Request $request){
  	$admin = AdminModel::get($request->session('adminUser.admin_id'));
  	$this->assign('vo', $admin);
  	return $this->fetch();
  }

   public function save(Request $request){
   	 $data['realname']= $request->post('realname');
   	  $data['email']= $request->post('email');
   	  $res = AdminModel::get( $request->post('admin_id'))->allowField(true)->save($data);
	if($res){
      		return show(1,'更新成功');
      	}else{
      		return show(0,'更新失败');
      	}
   }

}