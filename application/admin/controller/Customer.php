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

        if(!empty($list)){
            foreach ($list as $key => &$value) {
                $share_identification = $value['share_identification'];
                $share_num = $customerObj->where(['share_user_identification'=>$share_identification])->count();
                $value['share_num'] = $share_num?$share_num:'0';
            }
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


    //联系结果页面
    public function showContact()
    {
        $title = '联系结果备注';
        $id = Request::instance()->param('id');
        if(!$id || !is_numeric($id)){
            $this->error('参数有误');
        }
        $CustomerObj = new CustomerModel();
        $CustomerInfo = $CustomerObj->where(['id'=>$id])->find();
        if(empty($CustomerInfo)){
            $this->error('数据不存在');
        }
        $contact_date = $CustomerInfo['contact_date']?$CustomerInfo['contact_date']:'';
        $contact_name = $CustomerInfo['contact_name']?$CustomerInfo['contact_name']:'';
        $assign = [
            'title'=>$title,
            'id'=>$id,
            'maxDate'=>date('Y-m-d',time()),
            'contact'=>['contact_date'=>$contact_date,'contact_name'=>$contact_name],
        ];
        $this->assign($assign);
        return $this->fetch('Customer/contact');
    }

    //奖品发放页面
    public function showReceive()
    {
        $title = '发放奖品';
        $id = Request::instance()->param('id');
        if(!$id || !is_numeric($id)){
            $this->error('参数有误');
        }
        $CustomerObj = new CustomerModel();
        $CustomerInfo = $CustomerObj->where(['id'=>$id])->find();
        if(empty($CustomerInfo)){
            $this->error('数据不存在');
        }
        $prize_grant_date = $CustomerInfo['prize_grant_date']?$CustomerInfo['prize_grant_date']:'';
        $prize_name = $CustomerInfo['prize_name']?$CustomerInfo['prize_name']:'';
        $assign = [
            'title'=>$title,
            'id'=>$id,
            'maxDate'=>date('Y-m-d',time()),
            'contact'=>['prize_grant_date'=>$prize_grant_date,'prize_name'=>$prize_name],
        ];
        $this->assign($assign);
        return $this->fetch('Customer/receive');
    }


    public function editContact(){ //更改联系人状态
       
        $id = Request::instance()->param('customer_id');
        $contact_name = Request::instance()->param('contact_name');
        $contact_date = Request::instance()->param('contact_date');
        if(empty($id) || !is_numeric($id) || !$contact_name || !$contact_date){
            return json(array('err_code'=>-10001,'err_msg'=>'参数错误'));
        }
        $customerObj = new CustomerModel();
        $userInfo = $customerObj->where(['id'=>$id])->find();
        if(empty($userInfo)){
            return json(array('err_code'=>-10001,'err_msg'=>'数据不存在'));
        }
        $data = [
            'contact_status'=>2,
            'contact_name'=>$contact_name,
            'contact_date'=>$contact_date,
        ];
        $ret = $customerObj->where(['id'=>$id])->update($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }else{
            return json(array('err_code'=>'-10001','err_msg'=>'操作失败'));
        }
    }

    public function editReceive(){ //更改发放奖品状态
       
        $id = Request::instance()->param('customer_id');
        $prize_name = Request::instance()->param('prize_name');
        $prize_grant_date = Request::instance()->param('prize_grant_date');
        if(empty($id) || !is_numeric($id) || !$prize_grant_date || !$prize_name){
            return json(array('err_code'=>-10001,'err_msg'=>'参数错误'));
        }
        $customerObj = new CustomerModel();
        $userInfo = $customerObj->where(['id'=>$id])->find();
        if(empty($userInfo)){
            return json(array('err_code'=>-10001,'err_msg'=>'数据不存在'));
        }
        $data = [
            'receive_status'=>2,
            'prize_grant_date'=>$prize_grant_date,
            'prize_name'=>$prize_name,
        ];
        $ret = $customerObj->where(['id'=>$id])->update($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }else{
            return json(array('err_code'=>'-10001','err_msg'=>'操作失败'));
        }
    }
}
