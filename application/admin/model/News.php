<?php
namespace app\admin\model;

use think\Model;

class News extends Model
{
	public function maxcount(){
		$data = [ 'status'=>1];
		$count = $this->where($data)->count();
                          return $count;
	}

}