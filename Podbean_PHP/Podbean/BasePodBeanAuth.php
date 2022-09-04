<?php

namespace Podbean;

use function print_data;
use function read_config_file_module;
use function runCurlPost;
use function write_config_file_module;

/**
 *  PodBean authentication
 * Created on Jul 23, 2022 3:41:21 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 */
class BasePodBeanAuth {

    /**
     * Author URL
     */
    protected $authURL = "https://api.podbean.com/v1/dialog/oauth";

    /**
     * Access token URL
     */
    protected $accessTokenURL = "https://api.podbean.com/v1/oauth/token";

    /**
     * CRUD episode
     */
    protected $episodeURL = "https://api.podbean.com/v1/episodes";

    /**
     * Upload File URL
     * @see 
     */
    protected $uploadFileURL = "https://api.podbean.com/v1/files/uploadAuthorize";
    protected $client_id;
    protected $client_secret;
    protected $redirect_uri;
    protected $response_type = "code";
    protected $tokenFile;
    protected $accessToken;
    protected $accessTokenExpired;

    /**
     * Podcast opermissions
     * @see https://developers.podbean.com/podbean-api-docs/#Permissions
     */
    protected $permissions = array(
        'podcast_read',
        'podcast_update',
        'episode_read',
        'episode_publish',
    );

    /**
     * Create PodBean Authorize 
     * @param type $client_id
     * @param type $redirect_uri
     * @param type $permissions
     */
    public function __construct($client_id = "", $redirect_uri = "", $permissions = array()) {
        if (!empty($client_id)) {
            $this->client_id = $client_id;
        }
        if (!empty($redirect_uri)) {
            $this->redirect_uri = $redirect_uri;
        }
        if (!empty($permissions)) {
            $this->permissions = $permissions;
        }
        /**
         * Refresh access token
         */
        $this->refreshAccessToken();
    }

    /**
     * get Update Episode URL
     * @param type $episodeID
     * @return type
     */
    public function getUpdateEpsiodeURL($episodeID) {
        return $this->getEpisodeURL() . "/" . trim($episodeID);
    }

    /**
     * get Authorize URL
     * @return string
     */
    public function getFullAuthorizeURL() {
        $url = $this->getAuthURL();
        $params = array(
            'client_id' => $this->getClient_id(),
            'redirect_uri' => $this->getRedirect_uri(),
            'scope' => $this->getPermissionString(),
            'response_type' => $this->getResponse_type(),
        );
        $url .= "?" . http_build_query($params);
        return $url;
    }

    /**
     * Refresh access token
     */
    public function refreshAccessToken() {
        $fileName = $this->getTokenFile();
        $data = read_config_file_module($fileName);
        if (empty($data)) {
//            print_data("File: $fileName không tồn tại (func = " . __FUNCTION__);
            return;
        }

        if (!isset($data['access_token'])) {
//            print_data("Cần phải authorize PodBean " . __FUNCTION__);
            unlink($fileName);

            return;
        }
        $expires_in = $data['expires_in'];
        $this->setAccessTokenExpired($expires_in);
        /**
         * Access token is expired?
         */
        $isExpiredToken = $this->isAccessTokenExpired();
        if ($isExpiredToken) {
            $refresh_token = $data['refresh_token'];
            /**
             * update access token to file
             */
            $this->getAccessTokenBy_refreshToken($refresh_token);
        }
        /**
         * Set Access token
         */
        $accessToken = $data['access_token'];
        $this->setAccessToken($accessToken);
    }

    /**
     * get Access token by $refresh_token
     * @param type $refresh_token
     * @return type
     */
    public function getAccessTokenBy_refreshToken($refresh_token) {
        $params = array(
            'refresh_token' => $refresh_token,
            "grant_type" => "refresh_token",
            "redirect_uri" => $this->getRedirect_uri(),
            'client_id' => $this->getClient_id(),
            "client_secret" => $this->getClient_secret(),
        );
        return $this->write_accessToken_toFile($params);
    }

    /**
     * get Access token by code
     * @param type $code
     * @return type
     */
    public function getAccessTokenByCode($code) {
        $params = array(
            'code' => $code,
            "grant_type" => "authorization_code",
            "redirect_uri" => $this->getRedirect_uri(),
            'client_id' => $this->getClient_id(),
            "client_secret" => $this->getClient_secret(),
        );
        return $this->write_accessToken_toFile($params);
    }

    /**
     * Write access token to file
     * @param type $params
     * @return type
     */
    protected function write_accessToken_toFile($params) {
        $url = $this->getAccessTokenURL();
        $dataJson = runCurlPost($url, $params);
        $data = json_decode($dataJson, true);
        if (empty($data)) {
            print_data("ERROR: run fail: $url ");
        }
        $fileName = $this->getTokenFile();
        if (isset($data['access_token'])) {
            $accessToken = $data['access_token'];
            $expires_in = time() + $data['expires_in'];
            $this->setAccessToken($accessToken);
            $this->setAccessTokenExpired($expires_in);
        }
        /**
         * Nếu là Refresh Token thì lưu lại giá trị refresh_token vào File Json
         */
        if (isset($params['refresh_token'])) {
            $data['refresh_token'] = $params['refresh_token'];
        }
        write_config_file_module($data, $fileName);
        return $accessToken;
    }

    /**
     * Check Token is Expired
     * @return type
     */
    public function isAccessTokenExpired() {
        return time() > $this->getAccessTokenExpired() - 3;
    }

    /**
     * get Permissions as String
     * @return type
     */
    public function getPermissionString($permissions = array(), $glue = " ") {
        if (empty($permissions)) {
            $permissions = $this->getPermissions();
        }
        $pers = implode($glue, $permissions);
        return $pers;
    }

    public function getAuthURL() {
        return $this->authURL;
    }

    public function getClient_id() {
        return $this->client_id;
    }

    public function getRedirect_uri() {
        return $this->redirect_uri;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    public function setAuthURL($authURL) {
        $this->authURL = $authURL;
    }

    public function setClient_id($client_id) {
        $this->client_id = $client_id;
    }

    public function setRedirect_uri($redirect_uri) {
        $this->redirect_uri = $redirect_uri;
    }

    public function setPermissions($permissions) {
        $this->permissions = $permissions;
    }

    public function getResponse_type() {
        return $this->response_type;
    }

    public function setResponse_type($response_type) {
        $this->response_type = $response_type;
    }

    public function getAccessTokenURL() {
        return $this->accessTokenURL;
    }

    public function setAccessTokenURL($accessTokenURL) {
        $this->accessTokenURL = $accessTokenURL;
    }

    public function getClient_secret() {
        return $this->client_secret;
    }

    public function setClient_secret($client_secret) {
        $this->client_secret = $client_secret;
    }

    public function getTokenFile() {
        return $this->tokenFile;
    }

    public function setTokenFile($tokenFile) {
        $this->tokenFile = $tokenFile;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function setAccessToken($accessToken) {
        $this->accessToken = $accessToken;
    }

    public function getAccessTokenExpired() {
        return $this->accessTokenExpired;
    }

    public function setAccessTokenExpired($accessTokenExpired): void {
        $this->accessTokenExpired = $accessTokenExpired;
    }

    public function getEpisodeURL() {
        return $this->episodeURL;
    }

    public function setEpisodeURL($episodeURL): void {
        $this->episodeURL = $episodeURL;
    }

    public function getUploadFileURL() {
        return $this->uploadFileURL;
    }

    public function setUploadFileURL($uploadFileURL): void {
        $this->uploadFileURL = $uploadFileURL;
    }

}
