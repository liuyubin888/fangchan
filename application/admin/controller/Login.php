<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\AdminModel;
use think\Request;
use think\Session;
class Login extends Controller
{
    public function index()
    {
        // 登录验证
		if (Session::get('logined') || Session::get('logined') === true || Session::get('admin_id')) {
			$this->redirect('Admin/index/index');
		}else{
            return $this->fetch('Login/index');
        }
        
    }

    /** 
     * 验证登录
     * @param string 
     * @return
     */
    public function checkin() {
        if (!Request::instance()->isAjax()){
            return json(array('err_code'=>10002,'err_msg'=>'请求方式错误'));
        }
        $code = Request::instance()->param('code');
        $user = Request::instance()->param('user');
        $pwd = Request::instance()->param('pwd');
        if(!$this->check_verify($code)){
            // return json(array('err_code'=>10001,'err_msg'=>'验证码不正确'));
        }

        if(empty($user)){
            return json(array('err_code'=>10002,'err_msg'=>'用户名不能为空'));
        }

        if(empty($pwd)){
            return json(array('err_code'=>10003,'err_msg'=>'密码不能为空'));
        }

        $Admin = new AdminModel();
        $userInfo = $Admin->getUser($user);
        if(empty($userInfo)){
            return json(array('err_code'=>10004,'err_msg'=>'用户名或密码错误'));
        }
        
        // 密码校验
    	if (md5($pwd . $userInfo['salt']) != $userInfo['password']) {
            return json(array('err_code'=>10004,'err_msg'=>'用户名或密码错误'));
        }
        $Admin->where('id',$userInfo['id'])->update(array('lastvisit'=>date('Y-m-d H:i:s',time()))); //更新最后登录时间
        Session::set('logined',true);
        Session::set('admin_id',$userInfo['id']);
        Session::set('admin_user',$userInfo['username']);

        return json(array('err_code'=>0,'err_msg'=>'登录成功'));
    }

    /** 
     * 验证码的验证
     * @param string $code
     * @return 
     */
    private function check_verify($code){
        $captcha = new \think\captcha\Captcha();
        return $captcha->check($code);
    }

    /** 
     * 退出登录
     * @param 
     * @return 
     */
    public function logout(){
        Session::set('logined',false);
        Session::set('admin_id','');
        Session::set('admin_user','');
        $this->redirect('Admin/Login/index');
    }
}
