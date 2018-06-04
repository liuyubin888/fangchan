<?php
namespace app\admin\controller;
use app\admin\controller\Base;  
use app\admin\model\AdminModel;
use think\Request;
class Admin extends Base
{

    public function adminList()
    {
        $title = '管理员列表';
        $this->assign('title',$title);
        return $this->fetch('admin/admin-list');
    }

    public function adminAdd()
    {
        $title = '添加管理员';
        $this->assign('title',$title);
        return $this->fetch('admin/admin-add');
    }

    public function adminEdit()
    {
        $title = '编辑管理员';
        $id = $username = Request::instance()->param('id');
        if(!$id || !is_numeric($id)){
            $this->error('参数有误');
        }
        $Admin = new AdminModel();
        $adminInfo = $Admin->where(['id'=>$id])->find();
        if(empty($adminInfo)){
            $this->error('数据不存在');
        }
        $assign = [
            'title'=>$title,
            'adminInfo'=>$adminInfo,
        ];
        $this->assign($assign);
        return $this->fetch('admin/admin-edit');
    }


    public function getAdminList(){
        
        $CurrentPage = Request::instance()->param('CurrentPage');
        $PageSize = Request::instance()->param('PageSize');
        $username = Request::instance()->param('username');
        $Admin = new AdminModel();
        $where = null;
        if(!empty($username)){
            $where = ['username',$username];
        }
        $list = $Admin->where($where)->page($CurrentPage,$PageSize)->select();
        
        $TotalCount = $Admin->count();
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

    public function addAdmin(){
       
        $username = Request::instance()->param('username');
        $password = Request::instance()->param('password');
        if(empty($username) || empty($password)){
            return json(array('err_code'=>10003,'err_msg'=>'参数错误'));
        }
        $salt = random(6);
        $status = Request::instance()->param('status');
        $groupid = Request::instance()->param('groupid');
        $password = md5($password . $salt);
        $Admin = new AdminModel();
        $userInfo = $Admin->where(['username'=>$username])->find();
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
        $ret = $Admin->addUser($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }
    }

    public function editAdmin(){
       
        $username = Request::instance()->param('username');
        $password = Request::instance()->param('password');
        $id = Request::instance()->param('admin_id');
        if(empty($username) || empty($id)){
            return json(array('err_code'=>-10001,'err_msg'=>'参数错误'));
        }
        if(!empty($password)){
            $salt = random(6);
            $password = md5($password . $salt);
        }
        $Admin = new AdminModel();
        $userInfo = $Admin->where(['id'=>$id])->find();
        $name_is_existence = $Admin->where(['username'=>$username])->find();
        if(empty($userInfo)){
            return json(array('err_code'=>-10001,'err_msg'=>'数据不存在'));
        }
        if(!empty($name_is_existence)){
            return json(array('err_code'=>-10001,'err_msg'=>'用户名重复'));
        }
        $data = [
            'username'=>$username,
        ];
        if(!empty($password)){
            $data['password'] = $password;
            $data['salt'] = $salt;
        }
        $ret = $Admin->where(['id'=>$id])->update($data);
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }
    }

    public function deleteAdmin(){
        $ids = $_GET['id'];
        if(empty($ids)){
            return json(array('err_code'=>10003,'err_msg'=>'参数错误'));
        }
        $Admin = new AdminModel();
        foreach ($ids as $id) {
            if(!empty($id) && is_numeric($id)){
                $ret = $Admin->where('id',$id)->delete();
            }
        }
        if($ret){
            return json(array('err_code'=>'0','err_msg'=>'操作成功'));
        }
    }
}
