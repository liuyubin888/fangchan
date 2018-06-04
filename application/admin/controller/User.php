<?php
namespace app\admin\controller;
use app\admin\controller\Base;  
class Admin extends Base
{
    public function adminList()
    {
        return $this->fetch('admin/admin-list');
    }

    public function adminAdd()
    {
        return $this->fetch('admin/admin-add');
    }

}
