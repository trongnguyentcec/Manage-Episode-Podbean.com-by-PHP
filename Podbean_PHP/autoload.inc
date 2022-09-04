<?php

/*
 * Created on Sep 4, 2022 12:22:48 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 * 
 */
spl_autoload_register("podbean_php_autoload", true);

require_once 'podbean.function.inc';
/**


 *  auto load Podbean PHP classes
 */

/**
 * Auto load data
 * @param type $class_name
 */
function podbean_php_autoload($class_name) {
    $root = getcwd();
    $dir = $root . "/";
    fix_dir_seperator($class_name);
    $file = $dir . trim($class_name) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        print_data("Not Found class: $class_name: at $file");
        $file = $dir . strtolower($class_name . '.php');
        if (file_exists($file)) {
            require_once $file;
        } else {
            print_data("Not Found class: $class_name: at $file");
        }
    }
}
