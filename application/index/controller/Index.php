<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Request;
use app\admin\model\CustomerModel;
class Index extends Controller
{
    //展示首页
    public function index()
    {
        
        $sucde = Request::instance()->param('sucde'); //介绍人标识
        $sucde = $sucde?$sucde:'';
        $this->assign('sucde',$sucde);
        return $this->fetch('index/index');
    }

    //点击参与活动验证登录态
    public function participateActive(){ 
        // 登录验证
		if (!Session::get('index_customer_id') || Session::get('index_logined') !== true || !Session::get('index_customer_mobile')) { //已登录
			return json(array('err_code'=>'0','err_msg'=>'success'));
		}else{ //未登录
            return json(array('err_code'=>'1','err_msg'=>'success'));
        }
    }

    //登记手机号码 
    public function gradeMobile(){
        $sucde = Request::instance()->param('sucde'); //介绍人标识
        $sucde = $sucde?$sucde:'';
        $code = Request::instance()->param('code'); //验证码
        $save_code = Session::get('msg_code'); //服务端验证码
        $mobile = Request::instance()->param('mobile'); //参与人手机号码
        $name = Request::instance()->param('name'); //姓名
        $unique_id = $this->createIdentifi(); //唯一标识
        $res = preg_match('/0?(13|14|15|18|17)[0-9]{9}$/', $mobile);
        if(empty($code) || strlen($code) != 6 || !is_numeric($code) || $code != $save_code){
            return json(array('err_code'=>'-10004','err_msg'=>'验证码有误'));
        }
        if($res || empty($mobile)){
            return json(array('err_code'=>'-10001','err_msg'=>'手机号码格式错误'));
        }
        if(empty($name)){
            return json(array('err_code'=>'-10002','err_msg'=>'请填写姓名'));
        }
        if(empty($unique_id)){
            return json(array('err_code'=>'-10003','err_msg'=>'网络错误'));
        }
        $data = [
            'share_user_identification' => $sucde,
            'mobile' => $mobile,
            'name' => $name,
            'share_identification' => $unique_id,
            'created' => date('Y-m-d H:i:s',time()),
        ];
        $customerObj = new CustomerModel();
        $ret = $customerObj->data($data)->save();
        if($ret){
            Session::set('index_logined',true);
            Session::set('index_customer_id',$userInfo);
            Session::set('index_customer_mobile',$mobile);
        }
    }

    //生成唯一标识
    private function createIdentifi(){
        $ret = true;
        $customerObj = new CustomerModel();
        $unique_id = ''; //唯一ID
        while($ret){
            $unique_id = random(12); //随机生成唯一ID
            $unique_id_is_existence = $customerObj->where(['share_identification'=>$unique_id])->find(); //确保不重复
            if(!$unique_id_is_existence){
                $ret = false;
            }
        }
        return $unique_id;
    }
}
