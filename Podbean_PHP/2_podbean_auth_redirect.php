<?php

/*
 * Created on Sep 4, 2022 1:55:56 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 * 
 */
require_once 'autoload.inc';

$url = $_SERVER['REQUEST_URI'];

/* get code from podbean URL */
$code = get_podbean_code_from_url($url);
if (!empty($code)) {
    /**
     *  Write token to file
     */
    $obj = new Podbean\MyPodbeanAuth();
    $obj->getAccessTokenByCode($code);
} else {
    var_dump($_SERVER['REQUEST_URI']);
}
