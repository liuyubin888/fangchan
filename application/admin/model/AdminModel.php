<?php
namespace app\admin\model;

use think\Model;

class AdminModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'admin';
    /** 
     * 根据用户名查找帐号
     * @param string $user
     * @return 
     */
    public function getUser($user){
        return $this->where('username',$user)->find();
    }

    public function addUser($data){
        $this->data($data);
        return $this->save();
    }
}