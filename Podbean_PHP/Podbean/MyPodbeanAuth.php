<?php

namespace Podbean;

 

/**
 *  My PobBean Authorize 
 * Created on Jul 4, 2022 7:28:09 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 */
class MyPodbeanAuth extends BasePodBean {

    /**
     * Podbean client ID
     */
    protected $client_id = "";

    /**
     * Podbean client secret
     */
    protected $client_secret = "";

    /**
     * Podbean redirect URI
     */
    protected $redirect_uri = "";

    /**
     * Store your access token & refresh token
     */
    protected $tokenFile = "my-podbean-token.json";

    

}
