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
            $this->checkCustomerExistence(); //用户资料不存在，清除session
            $index_customer_id = Session::get('index_customer_id'); //客户ID
            $index_customer_mobile = Session::get('index_customer_mobile'); //客户手机号码 
            $index_share_identification = Session::get('index_share_identification'); //个人唯一分享标识
            $index_customer_num = $this->getCustmoerNum($index_share_identification);
        }else{
            $index_logined = '';
            $index_customer_id = '';
            $index_customer_mobile = '';
            $index_share_identification = '';
            $index_customer_num = '';
        }
        $weixin = new SwechatObj();
        $JssdkConfig = $weixin->getJssdkConfig();
        $sucde = Request::instance()->param('sucde'); //邀请人标识
        $sucde = $sucde?$sucde:'';
        $loginInfo = array(
            'index_customer_id'=>$index_customer_id,
            'index_customer_mobile'=>$index_customer_mobile,
            'index_share_identification'=>$index_share_identification,
            'sucde'=>$sucde,
            'index_customer_num'=>$index_customer_num,
        );

        $link = 'http://'.$_SERVER['HTTP_HOST'].url('index/index/index',array('sucde'=>$index_share_identification));
        $shareData = array(
            'title' => "推荐购房拿大奖，登记买房折扣多",
            'desc' => "粤房汇网，周年庆典，购房折扣最高达7折",
            'imgUrl' => 'http://'.$_SERVER['HTTP_HOST']."/static/index/images/sharelogo.jpg",
            'link' => $link,
        );
        $assign = array(
            'loginInfo'=>$loginInfo,
            'JssdkConfig'=>$JssdkConfig,
            'shareData'=>$shareData,
        );
        $this->assign($assign);
        return $this->fetch('index/index');
    }

    //检查用户SESSION是否存在数据库
    public function checkCustomerExistence(){
        $index_customer_id = Session::get('index_customer_id');
        if($index_customer_id){
            $customerObj = new CustomerModel();
            $customerInfo = $customerObj->where('id',$index_customer_id)->find();
            if(empty($customerInfo)){
                Session::delete('index_logined');
                Session::delete('index_customer_id');
                Session::delete('index_customer_mobile');
                Session::delete('index_share_identification');
            }
        }
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

    //获取邀请人数
    private function getCustmoerNum($share_user_identification){
        $customerObj = new CustomerModel();
        return $customerObj->where('share_user_identification',$share_user_identification)->count();
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
        $customer_name = Request::instance()->param('name'); //姓名
        $unique_id = $this->createIdentifi(); //唯一标识
        $index_mobile_is_existence = Session::get('index_mobile_is_existence'); //服务端保存验证码数据，包含过期时间
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

        if($index_mobile_is_existence['mobile'] != $mobile && $index_mobile_is_existence['is_existence'] == false){
            if(!$customer_name){
                return json(array('err_code'=>'-10001','err_msg'=>'请输入姓名'));
            }
        }
        

        $customerObj = new CustomerModel();
        $Customer = $customerObj->where(['mobile'=>$mobile])->find();

        if(!empty($Customer)){ //已经存在的用户，直接登录，无需重复保存
            $this->loginInfo($Customer);
            return json(array('err_code'=>'0','err_msg'=>'success'));
        }
        if(empty($unique_id)){
            return json(array('err_code'=>'-10003','err_msg'=>'网络错误'));
        }
        $data = [
            'share_user_identification' => $sucde, //介绍人标识 
            'mobile' => $mobile, //手机号码 
            'name' => $customer_name, //姓名
            'share_identification' => $unique_id,//个人唯一标识
            'created' => date('Y-m-d H:i:s',time()),
        ];
        
        $customerObj->data($data)->save();
        $insertId = $customerObj->getLastInsID();
        if($insertId){
            Session::set('index_logined',true);
            Session::set('index_customer_id',$insertId);
            Session::set('index_customer_mobile',$mobile);
            Session::set('index_share_identification',$unique_id);
            return json(array('err_code'=>'0','err_msg'=>'success'));
        }else{
            return json(array('err_code'=>'-10001','err_msg'=>'error'));
        }
    }

    //检测手机号码是否存在
    public function getCustomerInfo(){
        $mobile = Request::instance()->param('mobile'); //手机号码
        $res = preg_match('/0?(13|14|15|18|17)[0-9]{9}$/', $mobile);
        
        if(!$res || empty($mobile)){
            return json(array('err_code'=>'-10001','err_msg'=>'手机号码格式错误'));
        }

        $customerObj = new CustomerModel();
        $Customer = $customerObj->where(['mobile'=>$mobile])->find();

        if(!empty($Customer)){ //用户存在，无需输入姓名
            Session::set('index_mobile_is_existence',array('mobile'=>$mobile,'is_existence'=>true));
            return json(array('err_code'=>'0','err_msg'=>'success','data'=>'1'));
        }else{
            return json(array('err_code'=>'0','err_msg'=>'success','data'=>'2'));
        }

    }


    //获取邀请详情
    public function getInvitationDetailed(){
        $share_identification = Request::instance()->param('share_identification'); //介绍人标识
        
        if( empty($share_identification)){
            return json(array('err_code'=>'-10001','err_msg'=>'参数有误'));
        }

        $customerObj = new CustomerModel();
        $Customer = $customerObj->field('name,mobile,created')->where(['share_user_identification'=>$share_identification])->select();
        if($Customer){
            $Customer = collection($Customer)->toArray();
        }else{
            $Customer = array();
        }
        if(!empty($Customer)){ //用户存在，无需输入姓名
            /*$list = array(
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
                array('name'=>'刘汉滨','mobile'=>'17688445071','created'=>'2018-06-09 21:22:11'),
            );*/
            foreach ($Customer as $key => &$value) {
                $value['created'] = date('Y-m-d',strtotime($value['created']));
            }
            return json(array('err_code'=>'0','err_msg'=>'success','data'=>$Customer));
        }else{
            return json(array('err_code'=>'-10001','err_msg'=>'error'));
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

    //重新登录
    public function logOut(){
        Session::delete('index_logined');
        Session::delete('index_customer_id');
        Session::delete('index_customer_mobile');
        Session::delete('index_share_identification');
        $this->redirect('index/index/index');
    }
    
}
