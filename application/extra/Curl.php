<?php
namespace HttpGet\Extract\Curl;
class Curl{
    /**
     * curl get方式获取数据
     *
     * @param $url string 访问的URL
     * @return string
     */
    public static function curlGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;

    }

    /**
     * curl post方式获取数据
     *
     * @param $url string 访问的URL
     * @param $params string post提交的参数（json字符串）
     * @return $data string 结果
     */
    public static function curlPost($url, $params){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT,10);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;

    }
}
