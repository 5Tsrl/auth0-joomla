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
    protected $curl;


    public function __construct($domain, $clientId, $clientSecret, $redirectURL) {

        $this->domain = $domain;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURL = $redirectURL;
        $this->http = new JHttp();
        $this->curl = curl_init();

    }

    public function getAccessToken ($code) {

        $body = array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectURL,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );

        $headers = array(
            "content-type: application/json"
            //'content-type' => 'application/x-www-form-urlencoded'
        );

        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->domain . '/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $headers,
          ));
        $response = curl_exec($this->curl);

        // $response = $this->http->post($this->domain . '/oauth/token', $body, $headers);

        $data = json_decode( $response );

        if (isset($data->access_token)) {
            return $data->access_token;
        }

        throw new Exception($data->error_description);
    }

    public function getUserInfo($accessToken) {

        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->domain . '/userinfo/?access_token=' . $accessToken,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            // CURLOPT_POSTFIELDS => json_encode($body),
            // CURLOPT_HTTPHEADER => $headers,
          ));
        $response = curl_exec($this->curl);

        // $userData = $this->http->get($this->domain . '/userinfo/?access_token=' . $accessToken);
        $userData = $response;
        $userInfo = json_decode( $userData );

        return $userInfo;

    }



    public function getToken($grantType = 'client_credentials')
    {   
        $body = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => $grantType,
            'audience' => $this->domain.'/api/v2/',
        );
        $headers = array(
            "content-type: application/json"
        );
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $this->domain . '/oauth/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => $headers,
          ));
        $response = curl_exec($this->curl);
        // $response = $this->http->post($this->domain . '/oauth/token', json_encode($body, JSON_UNESCAPED_SLASHES), $headers);
        // die( $response );

        $data = json_decode( $response );

        if (isset($data->access_token)) {
            return $data->access_token;
        }

        throw new Exception($data ? $data->error_description : 'Invalid headers');
    }

}
