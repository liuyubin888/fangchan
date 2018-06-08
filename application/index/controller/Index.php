<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Request;
use Weixin\Swechat\SwechatObj;
use Qcloud\Sms\SmsSingleSender;
use app\admin\model\CustomerModel;
class Index extends Controller
{
    //展示首页
    public function index()
    {
        $index_logined = Session::get('index_logined'); //是否登录
        if($index_logined){
            $index_customer_id = Session::get('index_customer_id'); //客户ID
            $index_customer_mobile = Session::get('index_customer_mobile'); //客户手机号码 
            $index_share_identification = Session::get('index_share_identification'); //个人唯一分享标识
        }else{
            $index_logined = '';
            $index_customer_id = '';
            $index_customer_mobile = '';
            $index_share_identification = '';
        }
        
        $weixin = new SwechatObj();
        $JssdkConfig = $weixin->getJssdkConfig();
        $sucde = Request::instance()->param('sucde'); //介绍人标识
        $sucde = $sucde?$sucde:'';
        $shareData = array(
            'title' => "推荐购房拿大奖，登记买房折扣多",
            'desc' => "粤房汇网，周年庆典，购房折扣最高达7折",
            'imgUrl' => "__PUBLIC__/index/images/sharelogo.jpg",
            'link' => url('index/index',array('sucde'=>$index_share_identification)),
        );
        $assign = array(
            'sucde'=>$sucde,
            'JssdkConfig'=>$JssdkConfig,
            'shareData'=>$shareData,
        );
        $this->assign($assign);
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
        $code = Request::instance()->param('code'); //前端验证码
        $save_code_data = Session::get('msg_code'); //服务端保存验证码数据，包含过期时间
        $save_code = $save_code_data['data']; //服务端验证码
        $code_expire = $save_code_data['expire']; //服务端验证码过期时间
        $save_mobile = $save_code_data['mobile']; //防止前端获取了验证码之后，又填写另一个手机号码
        $mobile = Request::instance()->param('mobile'); //参与人手机号码
        $name = Request::instance()->param('name'); //姓名
        $unique_id = $this->createIdentifi(); //唯一标识

        if($code_expire<time()){ //验证码过期了
            Session::delete('msg_code');
            return json(array('err_code'=>'-10004','err_msg'=>'请重新获取验证码'));
        }

        if(empty($code) || strlen($code) != 6 || !is_numeric($code) || $code != $save_code){
            return json(array('err_code'=>'-10004','err_msg'=>'验证码有误'));
        }
        
        $res = preg_match('/0?(13|14|15|18|17)[0-9]{9}$/', $mobile);
        
        if(!$res || empty($mobile)){
            return json(array('err_code'=>'-10001','err_msg'=>'手机号码格式错误'));
        }

        if($mobile != $save_mobile){
            return json(array('err_code'=>'-10001','err_msg'=>'修改手机号码需重新获取验证码'));
        }

        $customerObj = new CustomerModel();
        $Customer = $customerObj->where(['mobile'=>$mobile])->find();

        if(!empty($Customer)){ //已经存在的用户，直接登录，无需重复保存
            $this->loginInfo($Customer);
            return json(array('err_code'=>'0','err_msg'=>'success'));
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
        
        $insertGetId = $customerObj->data($data)->save()->insertGetId();
        if($ret){
            Session::set('index_logined',true);
            Session::set('index_customer_id',$insertGetId);
            Session::set('index_customer_mobile',$mobile);
            Session::set('index_share_identification',$unique_id);
            return json(array('err_code'=>'0','err_msg'=>'success'));
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

    //生成登录态
    private function loginInfo($Customer){
        Session::set('index_logined',true);
        Session::set('index_customer_id',$Customer['id']);
        Session::set('index_customer_mobile',$Customer['mobile']);
        Session::set('index_share_identification',$Customer['share_identification']);
    }
    
}
