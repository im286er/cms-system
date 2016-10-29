<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use app\admin\model\Admin;

//登陆模块
class Login extends Controller
{

    public function index()
    {
        if(session('adminUser')){
        	$this->redirect('/admin/index/index');
        }
        return $this->fetch();

    }

//登陆验证功能
    public function check(Request $request){
    	$username= $request->param('username');
    	$password= $request->param('password');
    	if(!trim($username)){
    	  return  show(0,'用户名不能为空');
    	}
    	if(!trim($password)){
    	  return  show(0,'密码不能为空');
    	}

    	$ret = Admin::where('username', $username)->find();
    	if(!$ret || $ret['status']!=1){
    		return show (0, '该用户不存在');
    	}

    	if($ret['password'] !=md5($password)){
    		return show(0,'密码错误');
    	}

             $changetime= new Admin();
             $changetime->save(
                ['lastlogintime'=>time()],
                ['admin_id'=>$ret ['admin_id']]
                );
    	session('adminUser', $ret);

    	return show(1,'登陆成功');
    }
//退出模块
    function loginout(){
    	session('adminUser',null);
    	$this->redirect('/admin/login/index');
    }
}
