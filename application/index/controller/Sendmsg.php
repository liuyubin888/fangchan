<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
class Sendmsg extends Controller
{
    public function sendSms()
    {
        $mobile = Request::instance()->param('mobile');
        $res = preg_match('/0?(13|14|15|18|17)[0-9]{9}$/', $mobile);
        if($res || empty($mobile)){
            return json(array('err_code'=>'-10001','err_msg'=>'手机号码格式错误'));
        }
        $code = rand(100000, 999999);
        $msg = '您正在申请粤房汇登记，验证码为'.$code.'，请在100秒内完成验证，如非本人操作，请忽略本条信息。';
        
        //操作验证码发送
    }
}
