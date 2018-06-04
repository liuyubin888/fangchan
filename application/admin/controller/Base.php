<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;

class Base extends Controller
{
	
    public function _initialize()
    {
        // 登录验证
		if (!Session::get('logined') || Session::get('logined') !== true || !Session::get('admin_id')) {
			$this->redirect('Admin/Login/index');
		}
    }
}
