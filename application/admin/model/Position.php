<?php
namespace app\admin\model;

use think\Model;

class Position extends Model
{
	public function Pmaxcount(){
		$data = [ 'status'=>1];
		$count = $this->where($data)->count();
                          return $count;
	}

}