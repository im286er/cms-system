<?php
namespace app\home\controller;
use app\home\controller\Common;
use app\admin\model\News as NewsModel  ;
use app\admin\model\PositionContent as PositionContentModel  ;
class Index extends Common
{
    public function index()
    {
    	$rankNews = NewsModel::where(['status'=>1])->limit(10)->order('count desc,news_id')->select();
    	//首页大图数据
            $topPicNews = PositionContentModel::where(['status'=>1,'position_id'=>2])->limit(1)->select();
            //首页三小图
            $topSmailNews = PositionContentModel::where(['status'=>1,'position_id'=>3])->order('listorder desc')->limit(3)->select();
            $listNews = NewsModel::where(['status'=>1,'thumb'=>['neq','']])->order('listorder desc')->limit(5)->select();
            $advNews = PositionContentModel::where(['status'=>1,'position_id'=>5])->order('listorder desc')->limit(2)->select();

            $this->assign('result',['topPicNews'=>$topPicNews,
            			           'topSmailNews'=>$topSmailNews,
            			           'listNews'=>$listNews,
            			           'advNews'=>$advNews,
            			           'rankNews'=>$rankNews,
            			           'catId'=>0,
            			           	] );
             return $this->fetch();
    }



}
