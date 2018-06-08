<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Request;
use think\Log;
use app\admin\model\CustomerModel;
class Weixin extends Controller
{

    public function valid()

    {

        $echoStr = $_GET["echostr"];
        $Token = 'UMFLfQP77qZN37XX7Fxmuk70pgxUx077';
        Log::init([
		    'type'  =>  'File',
		    'path'  =>  APP_PATH.'logs/'
		]);
		Log::write($_GET);
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }

    }

    public function responseMsg()

    {

        //get post data, May be due to the different environments

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];


        //extract post data

        if (!empty($postStr)){


                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

                $fromUsername = $postObj->FromUserName;

                $toUsername = $postObj->ToUserName;

                $keyword = trim($postObj->Content);

                $time = time();

                $textTpl = "<xml>

                            <ToUserName><![CDATA[%s]]></ToUserName>

                            <FromUserName><![CDATA[%s]]></FromUserName>

                            <CreateTime>%s</CreateTime>

                            <MsgType><![CDATA[%s]]></MsgType>

                            <Content><![CDATA[%s]]></Content>

                            <FuncFlag>0</FuncFlag>

                            </xml>";

                if(!empty( $keyword ))

                {
                    $msgType = "text";

                    $contentStr = "Welcome to wechat world!";

                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                    echo $resultStr;

                }else{

                    echo "Input something...";

                }


        }else {

            echo "";

            exit;

        }

    }


    private function checkSignature()

    {

        $signature = $_GET["signature"];

        $timestamp = $_GET["timestamp"];

        $nonce = $_GET["nonce"];


        $token = TOKEN;

        $tmpArr = array($token, $timestamp, $nonce);

        sort($tmpArr);

        $tmpStr = implode( $tmpArr );

        $tmpStr = sha1( $tmpStr );


        if( $tmpStr == $signature ){

            return true;

        }else{

            return false;

        }

    }

}