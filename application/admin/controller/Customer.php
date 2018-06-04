<?php
namespace app\admin\controller;
use app\admin\controller\Base;  
use app\admin\model\CustomerModel;
use think\Request;
class customer extends Base
{

    public function customerList()
    {
        $title = '会员列表';
        $this->assign('title',$title);
        return $this->fetch('customer/customer-list');
    }

    public function getCustomerList(){
        
        $CurrentPage = Request::instance()->param('CurrentPage');
        $PageSize = Request::instance()->param('PageSize');
        $mobile = Request::instance()->param('mobile'); //手机号码
        $share_identification = Request::instance()->param('share_identification'); //分享标识
        $share_user_identification = Request::instance()->param('share_user_identification'); //分享标识
        $contact_status = Request::instance()->param('contact_status'); //联系状态
        $receive_status = Request::instance()->param('receive_status'); //发放奖品状态
        $customerObj = new CustomerModel();
        $where = array();
        if(!empty($mobile)){
            $where['mobile'] = $mobile;
        }
        if(!empty($share_identification)){
            $where['share_identification'] = $share_identification;
        }
        if(!empty($share_user_identification)){
            $where['share_user_identification'] = $share_user_identification;
        }
        if(!empty($contact_status)){
            $where['contact_status'] = $contact_status;
        }
        if(!empty($receive_status)){
            $where['receive_status'] = $receive_status;
        }
        $list = $customerObj->where($where)->page($CurrentPage,$PageSize)->select();
        
        $TotalCount = $customerObj->count();
        $TotalPage = ceil($TotalCount / $PageSize); //总共页数
        if($list){
            $list = collection($list)->toArray();
        }else{
            $list = array();
        }
        $pageInfo = array(
            'PageSize' => $PageSize,
            'TotalCount' => $TotalCount,
            'TotalPage' => $TotalPage,
            'CurrentPage' => $CurrentPage,
        );
        $return = array(
            'err_code' => '0',
            'err_msg' => 'success',
            'dataList' => $list,
            'pageInfo' => $pageInfo,

        );
        return json($return);
    }

    public function addcustomer(){
       
        $username = Request::instance()->param('username');
        $password = Request::instance()->param('password');
        if(empty($username) || empty($password)){
            return json(array('err_code'=>10003,'err_msg'=>'参数错误'));
        }
        $salt = random(6);
        $status = Request::instance()->param('status');
        $groupid = Request::instance()->param('groupid');
        $password = md5($password . $salt);
        $customerObj = new CustomerModel();
        $userInfo = $customerObj->where(['username'=>$username])->find();
        if(!empty($userInfo)){
            return json(array('err_code'=>10003,'err_msg'=>'该用户名已存在'));
        }
        $data = [
            'username'=>$username,
            'password'=>$password,
            'salt'=>$salt,
            'created'=>date('Y-m-d H:i:s',time()),
            'lastvisit'=>date('Y-m-d H:i:s',time()),
            'status'=>1,
            'groupid'=>1,
        ];
        $ret = $customerObj->addUser($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }
    }

    public function editcontact(){ //更改联系人状态
       
        $id = Request::instance()->param('customer_id');
        if(empty($id) || !is_numeric($id)){
            return json(array('err_code'=>-10001,'err_msg'=>'参数错误'));
        }
        $customerObj = new CustomerModel();
        $userInfo = $customerObj->where(['id'=>$id])->find();
        if(empty($userInfo)){
            return json(array('err_code'=>-10001,'err_msg'=>'数据不存在'));
        }
        $data = [
            'contact_status'=>2,
        ];
        $ret = $customerObj->where(['id'=>$id])->update($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }else{
            return json(array('err_code'=>'-10001','err_msg'=>'操作失败'));
        }
    }

    public function editreceive(){ //更改发放奖品状态
       
        $id = Request::instance()->param('customer_id');
        if(empty($id) || !is_numeric($id)){
            return json(array('err_code'=>-10001,'err_msg'=>'参数错误'));
        }
        $customerObj = new CustomerModel();
        $userInfo = $customerObj->where(['id'=>$id])->find();
        if(empty($userInfo)){
            return json(array('err_code'=>-10001,'err_msg'=>'数据不存在'));
        }
        $data = [
            'receive_status'=>2,
        ];
        $ret = $customerObj->where(['id'=>$id])->update($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }else{
            return json(array('err_code'=>'-10001','err_msg'=>'操作失败'));
        }
    }
}
