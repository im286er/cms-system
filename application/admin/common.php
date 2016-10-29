<?php
use think\Db;
use think\Request;


 function getAdminMenus(){
	$data = array(
		'status' => array('neq',-1),
		'type' => 1,
		);
	$result=Db::name('menu')->where($data)->order('listorder desc, menu_id desc')->select();
	return 	$result;
}

function getAdminMenuUrl($nav){
	$url ='/admin/'.$nav['c'].'/'.$nav['f'];
	return $url;
}

function getActive($navc){
	$request = Request::instance();
	$c = strtolower($request->controller());
	if(strtolower($navc)==$c){
		return 'class="active" ';
	}
	return '';
}

function showKind($status,$data){
	header('Content-type:application/json;charset=UTF-8');
	if($status==0){
		exit(json_encode(array('error'=>0, 'url'=>$data)));
	}
	exit(json_encode(array('error'=>1, 'message'=>'上传失败')));
}


function getCatName($navs,$catid){
	foreach($navs as $nav){
		$navList[$nav['menu_id']] = $nav['name'];
	}
	//$nav['menu_id']与$catid在表中news和menu表中是对应的关系

	return isset($navList[$catid]) ?  $navList[$catid] : '';
}
function getCopyFromById($copyfrom){

	$copyFroms= config('COPY_FROM');
	//$copyFrom[$id] ? $copyFrom[$id] : ''


	return $copyFroms[$copyfrom] ? $copyFroms[$copyfrom]  :'';
}

function isThumb($thumb){
	if($thumb){
		return '<span style="color:red">有</ span>';
	}
	return '无';
}

function status($status){
       if($status==0){
       	$str='关闭';
       }elseif($status==1){
       	$str='正常';
       }elseif($status==-1){
       	$str='删除';
       }
        return $str;
}


