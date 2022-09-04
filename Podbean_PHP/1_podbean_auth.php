<?php

/*
 * Created on Sep 4, 2022 1:57:01 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 * 
 */
include_once 'autoload.inc';

try {
    /**
     * 1. get Access code for Podbean app
     */
    if (class_exists("Podbean\MyPodbeanAuth")) {
        $obj = new Podbean\MyPodbeanAuth();
        $authorURL = $obj->getFullAuthorizeURL();
        /**
         * Go to Authorize location
         */
        header("Location: " . $authorURL );
    } else {
        print_data("Class not found: Podbean\MyPodbeanAuth");
    }
    exit(0);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}


