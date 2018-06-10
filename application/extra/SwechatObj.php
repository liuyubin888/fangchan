<?php
namespace Weixin\Swechat;
use HttpGet\Extract\Curl\Curl;
use Extracts\CacheMysql\CacheMysql;
class SwechatObj {
    public $AppSecret = '83ad245aab4a191364c14538f8b07e52';
    public $AppID = 'wx059d4aff52465d02'; //正确APPID
    public $Token = 'UMFLfQP77qZN37XX7Fxmuk70pgxUx077';
    public $timestamp = '';
    public $result = array('err_code'=>'0','err_msg'=>'success');
    

    /**
     * 构造函数
     *
     * @param string $appid  sdkappid
     * @param string $appkey sdkappid对应的appkey
     */
    public function __construct()
    {
        $this->timestamp = time();
    }
    
    //获取AccessToKen
    public function getAccessToken(){
        
        $cachekey = "accesstoken";

        $cache = CacheMysql::cache_read($cachekey);
        if(!empty($cache) && !empty($cache['token']) && $cache['expire'] > $this->timestamp){ //有缓存，直接返回缓存AccessToken
            $this->result['data'] = $cache['token'];
            return $this->result;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->AppID.'&secret='.$this->AppSecret;
        $content = Curl::curlGet($url);
        $token = @json_decode($content, true);
        if(isset($token['errcode']) && $token['errcode'] != '0') { //请求错误
            $this->result['err_code'] = $token['errcode'];
            $this->result['err_msg'] = $token['errmsg'].'---Obtain AccessToKen Fail';
            return $this->result;
        }
        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = $this->timestamp + $token['expires_in'] - 200;
        CacheMysql::cache_write($cachekey, $record);
        $this->result['data'] = $record['token'];
        return $this->result;
    }


    public function getJsApiTicket(){
        $cachekey = "jsticket";
        $cache = CacheMysql::cache_read($cachekey);
        if(!empty($cache) && !empty($cache['ticket']) && $cache['expire'] > $this->timestamp) {
            $this->result['data'] = $cache['ticket'];
            return $this->result;
        }
        $access_token = $this->getAccessToken();
        if($access_token['err_code'] != '0'){
            return $access_token;
        }
        $access_token = $access_token['data'];
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
        
        $content = Curl::curlGet($url);
        
        $result = @json_decode($content, true);
        if(empty($result) || intval(($result['errcode'])) != 0 || $result['errmsg'] != 'ok') {
            $this->result['err_code'] = $result['errcode'];
            $this->result['err_msg'] = $result['errmsg'].'---Obtain JsApiTicket Fail';
            return $this->result;
        }
        $record = array();
        $record['ticket'] = $result['ticket'];
        $record['expire'] = $this->timestamp + $result['expires_in'] - 200;
        CacheMysql::cache_write($cachekey, $record);
        $this->result['data'] = $record['ticket'];
        return $this->result;
    }

    public function getJssdkConfig($link){
        $jsapiTicket = $this->getJsApiTicket();
        if($jsapiTicket['err_code'] != '0'){
            return $jsapiTicket;
        }
        $jsapiTicket = $jsapiTicket['data'];
        $nonceStr = random(16);
        $timestamp = $this->timestamp;

        $string1 = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$link}";
        $signature = sha1($string1);
        $config = array(
            "appId"     => $this->AppID,
            "nonceStr"  => $nonceStr,
            "timestamp" => "$timestamp",
            "signature" => $signature,
        );
        return $config;
    }
}