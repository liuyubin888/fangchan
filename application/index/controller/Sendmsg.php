<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Session;
use Qcloud\Sms\SmsSingleSender;
use Qcloud\Sms\SmsMultiSender;
use Qcloud\Sms\SmsVoiceVerifyCodeSender;
use Qcloud\Sms\SmsVoicePromptSender;
use Qcloud\Sms\SmsStatusPuller;
use Qcloud\Sms\SmsMobileStatusPuller;
class Sendmsg extends Controller
{
    public function sendSms()
    {
        $mobile = Request::instance()->param('mobile');
        $res = preg_match('/0?(13|14|15|18|17)[0-9]{9}$/', $mobile);
        if(!$res || empty($mobile)){
            return json(array('err_code'=>'-10001','err_msg'=>'手机号码格式错误'));
        }
        return json(array('err_code'=>'0','err_msg'=>'success'));
        $code = rand(100000, 999999);
        $expire = time()+60;
        //操作验证码发送
        $res = $this->sendmsg($code,$mobile);
        
        if($res['result'] == '0'){
            $codeSessionData = array(
                'data'=>$code,
                'expire'=>$expire,
                'mobile'=>$mobile, //防止前端获取了验证码之后，又填写另一个手机号码
            );
            Session::set('msg_code',$codeSessionData);
            return json(array('err_code'=>'0','err_msg'=>'success'));
        }else{
            return json(array('err_code'=>$res['result'],'err_msg'=>$res['errmsg']));
        }
        
    }

    //发送短信
    private function sendmsg($code,$phoneNumber){
        // 短信应用SDK AppID
        $appid = 1400093860; // 1400开头

        // 短信应用SDK AppKey
        $appkey = "1d490819a170e8c55db631349095c6a7";

        // 短信模板ID，需要在短信应用中申请
        $templateId = 132546;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

        //如需发送海外短信，同样可以使用此接口，只需将国家码"86"改写成对应国家码号。
        $ssender = new SmsSingleSender($appid, $appkey);

        //params {1}为您的登录验证码，请于{2}分钟内填写。如非本人操作，请忽略本短信。
        $params = [$code,'1'];
        
        $result = $ssender->sendWithParam("86", $phoneNumber, $templateId,
            $params);  // 签名参数未提供或者为空时，会使用默认签名发送短信
        return json_decode($result,1);

    }
}
