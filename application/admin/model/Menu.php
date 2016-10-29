<?php
namespace app\admin\model;
use think\Request;
use think\Model;

class Menu extends Model
{
    protected function getStatusAttr($status)
    {
       if($status==0){
       	$str='关闭';
       }elseif($status==1){
       	$str='正常';
       }elseif($status==-1){
       	$str='删除';
       }
       return $str;

    }
    protected function getTypeAttr($type){
    	return $type ==1?'后台菜单' :'前端导航';
    }


}