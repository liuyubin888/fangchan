<?php
namespace app\admin\controller;
use app\admin\controller\Base;  
use think\Session;

class Index extends Base
{

    public function index()
    {
    	
    	$admin_user = Session::get('admin_user');
        $this->assign('admin_user',$admin_user);
        $title = '房产管理后台';
        $this->assign('title',$title);
        return $this->fetch('index/index');

    }

    public function welcome()
    {
    	
        $title = '我的桌面';
        $this->assign('title',$title);
        return $this->fetch('index/welcome');
    }
}
