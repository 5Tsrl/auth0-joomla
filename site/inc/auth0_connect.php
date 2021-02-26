<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
defined('_JEXEC') or die('Restricted access');

class Auth0Connect {

    protected $domain;
    protected $clientId;
    protected $clientSecret;
    protected $redirectURL;
    protected $http;

    public function __construct($domain, $clientId, $clientSecret, $redirectURL) {

        $this->domain = $domain;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURL = $redirectURL;
        $this->http = new JHttp();

    }

    public function getAccessToken ($code) {

        $body = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'audience' => $this->domain . '/api/v2/',
            'grant_type' => 'authorization_code',
            'code' => $code,
            'content-type' => 'content-type: application/json'
        );

        $response = $this->http->post($this->domain . '/oauth/token', $body);

        $data = json_decode( $response->body );

        if (isset($data->access_token)) {
            return $data->access_token;
        }

        throw new Exception($data->error_description);
    }

    public function getUserInfo($accessToken) {

        $body = array(
            "authorization" => "Bearer ".$accessToken,
            'audience' => $this->domain . '/api/v2/',
            "cache-control" => "no-cache",
            "content-type" => "application/json; charset=utf-8"
        );

        //$userData = $this->http->get($this->domain . '/userinfo/?access_token=' . $accessToken);
        $userData = $this->http->get($this->domain . '/api/v2/users', $body);
        $userInfo = json_decode( $userData->body );

        return $userInfo;

    }



    public function getToken($grantType = 'client_credentials')
    {
        $body = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'audience' => $this->domain . '/api/v2/',
            'grant_type' => $grantType,
            'content-type' => 'content-type: application/json'
        );

        $response = $this->http->post($this->domain . '/oauth/token', $body);

        $data = json_decode( $response->body );

        if (isset($data->access_token)) {
            return $data->access_token;
        }

        throw new Exception($data ? $data->error_description : 'Invalid headers');
    }

}
