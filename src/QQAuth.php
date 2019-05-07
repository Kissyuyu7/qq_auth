<?php
/**
 * kissyuyu7@live.cn
 * QQ互联网页授权
 */

namespace kissyuyu7;

class QQAuth
{
    private $_appId;        //AppId
    private $_appKey;       //AppKey
    private $_callbackUrl;  //回调地址

    public function __construct($config){
        $this->_appId           = $config['appId'];
        $this->_appKey          = $config['appKey'];
        $this->_callbackUrl     = $config['callbackUrl'];
    }

    /**
     * 获取QQ登录唤起地址
     * @param string $state (验证部分 可选)
     * @param bool $isMobile (是否是手机端)
     * @return string (唤起地址)
     */
    public function getAuthPageUrl($state = '',$isMobile = false){
        $param = [
            'response_type' => 'code',
            'client_id'     => $this->_appId,
            'redirect_uri'  => $this->_callbackUrl,
            'state'         => $state,
            'scope'         => '',
            'display'       => $isMobile?'mobile':'',
        ];
        return 'https://graph.qq.com/oauth2.0/authorize?'.http_build_query($param);
    }

    /**
     * 通过授权码 获取用户信息
     * @param $authCode (登录授权码)
     * @return bool|array
     */
    public function getUserInfoByAuthCode($authCode){
        if ($accessToken = $this->_getAccessTokenByAuthCode($authCode)) {
            $url = "https://graph.qq.com/oauth2.0/me?access_token={$accessToken}";
            $rel = file_get_contents($url);
            $rel = json_decode(substr($rel,9,-3));
            $openId = $rel->openid;    //openId
            $url = "https://graph.qq.com/user/get_user_info?access_token={$accessToken}&oauth_consumer_key={$this->_appId}&openid={$openId}";
            $rel = file_get_contents($url);
            return [
                'userInfo' => json_decode($rel),
                'openId'   => $openId
            ];
        }else{
            return false;
        }
    }

    //-------------------------------------------------------------------------

    /**
     * 通过授权码 获取accessToken
     * @param $authCode (用户授权码)
     * @return string|bool (accessToken | false)
     */
    private function _getAccessTokenByAuthCode($authCode){
        $param = [
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->_appId,
            'client_secret' => $this->_appKey,
            'code'          => $authCode,
            'redirect_uri'  => $this->_callbackUrl
        ];
        $url = 'https://graph.qq.com/oauth2.0/token?'.http_build_query($param);
        $rel = file_get_contents($url);
        parse_str($rel,$rel);
        if (isset($rel['access_token'])){
            return $rel['access_token'];    //access_token
        }
        return false;
    }
}