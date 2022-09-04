<?php

/**
 * Created on Oct 29, 2020 7:35:08 AM
 * Created by @author Trong Nguyen trong.nguyenbt@gmail.com
 */
define("CLIENT_DETECT_DIR", __DIR__);

/**
 * Get Podbean redirect URL
 */
function get_podbean_redirect_url_from_dir() {
    $dir = CLIENT_DETECT_DIR;
    fix_dir_seperator($dir);
    $webRoot = $_SERVER['DOCUMENT_ROOT'];
    fix_dir_seperator($webRoot);
    $host = $_SERVER['HTTP_HOST'];
    $scheme = $_SERVER['REQUEST_SCHEME'];
    $path = str_ireplace($webRoot, "", $dir);
    return $scheme . "://" . $host . $path . "/2_podbean_auth_redirect.php";
}

/**
 * change \ to / in dir
 * @param type $dir
 */
function fix_dir_seperator(&$dir) {
    $dir = str_ireplace("\\", "/", $dir);
    $dir = str_ireplace("//", "/", $dir);
    $dir = rtrim($dir, "/");
}

/**
 * get code from podbean URL
 * @param type $url
 */
function get_podbean_code_from_url($url) {
    $path = parse_url($url);
    $query = $path['query'];
    print_data($query);
    parse_str($query, $query);
    $code = $query['code'];
    return $code;
}

/**
 * Run curl in GET method
 * @param type $url
 * @param type $data
 * @param type $headers
 * @return type
 */
function runCurl_GET_Episode($url, $data = NULL, $headers = NULL) {
    /**
     * Find ? on query
     */
    $sep = "&";
    if (stripos("?", $url) === false) {
        $sep = "?";
    }
    $url = $url . $sep . http_build_query($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if (empty($headers)) {
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );
    } else {
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);

    if (curl_error($ch)) {
        trigger_error('Curl Error:' . curl_error($ch));
    }
    curl_close($ch);
    return $response;
}

/**
 * Run curl in POST method
 * @param type $url
 * @param type $data
 * @param type $headers
 * @return type
 */
function runCurlPost($url, $data = NULL, $headers = NULL) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);

    if (curl_error($ch)) {
        trigger_error('Curl Error:' . curl_error($ch));
    }

    curl_close($ch);
    return $response;
}

/**
 * Write vbu config to files
 * @param type $data
 */
function write_config_file_module($data, $fileName, $dir = "") {
    $dir = CLIENT_DETECT_DIR . "/";
    $filePath = $dir . "$fileName";
    if (is_array($data)) {
        $data = json_encode($data);
    }

    writeToFile_at_begin($filePath, $data, 'w');
}

/**
 * Read vbu config data
 * @return type
 */
function read_config_file_module($fileName, $dir = CLIENT_DETECT_DIR) {
    $filePath = $dir . "/$fileName";
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $data = json_decode($data, TRUE);
        return $data;
    }
}

/**
 * trim text by word
 * @param type $text
 * @param type $maxLength
 */
function trim_by_words($text, $maxLength) {
    $text = trim_white_spaces($text);
    $words = explode(" ", $text);
    $trimWord = "";
    foreach ($words as $value) {
        $trimWord .= " " . $value;
        $len = strlen($trimWord);
        if ($len > $maxLength) {
            return $trimWord;
        }
    }
}

/**
 * Remove  whitespace(space, tab or newline)
 * @param type $str
 * @return type
 */
function trim_white_spaces($str) {
    $result = preg_replace('/\s+/', ' ', $str);
    return $result;
}

/**
 * Remove  whitespace only (Keep tab or newline)
 * @param type $str
 * @return type
 */
function trim_white_spaces_keep_tab_and_new_line($str) {
    $result = preg_replace(array('/\s{2, }/', '/[\t\n]/'), ' ', $str);
    return $result;
}

/**
 * List folder or files in directory
 * @param type $dir
 * @return type
 */
function bkgd_list_folders($dir, $getFolder = TRUE) {
    print_data("Scanning dir: $dir ........");
    clearstatcache();
    $dir = str_ireplace("\\", "/", $dir);
    $isReable = (int) is_dir($dir);
    print_data("Is dir: $isReable ");
    $res = @scandir($dir, SCANDIR_SORT_ASCENDING);
    $files = array();
    $dirs = array();
    $errors = "";
    if (empty($res)) {
        print_data("Nothing in  dir: $dir ");
        return;
    }
    foreach ($res as $value) {
        if (in_array($value, array(".", ".."))) {
            continue;
        }
        $fullValue = $dir . "/$value";
        $fullPathValue = $fullValue;
        print "full value: $fullPathValue " . PHP_EOL;
        try {
            if ($getFolder) {
                if (@is_dir($fullPathValue)) {
                    $dirs[] = $fullValue;
                }
            } else {
                if (@is_file($fullPathValue)) {
                    $files[] = $fullValue;
                }
            }
        } catch (Exception $exc) {
            $errors = $exc->getMessage();
        }
    }

    return array("Dirs" => $dirs, "Files" => $files, "Error" => $errors);
}

/**
 * Write vbu config to files
 * @param type $data
 */
function write_config_file($data, $fileName = "running.json") {
    $filePath = CLIENT_DETECT_DIR . "/$fileName";
    $data = json_encode($data);
    // write only
    writeToFile($filePath, $data, 'w');
}

/**
 * Read vbu config data
 * @return type
 */
function read_config_file($fileName = "running.json") {
    $filePath = CLIENT_DETECT_DIR . "/$fileName";
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        $data = json_decode($data, TRUE);
        return $data;
    }
}

/**
 * Check detect is running or not
 * @return type
 */
function vbu_is_detect_running() {
    $data = read_config_file();
    if ($data) {
        return $data['run'];
    }
}

if (!function_exists("print_data")) {

    /**
     * Print out data
     * @param type $data
     */
    function print_data($data, $new_line = true) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === 'attached_picture') {
                    continue;
                }
                if (is_array($value)) {
                    if (count($value)) {
                        if (is_string($key)) {
                            print_out_data_while_excute(make_bold_text($key) . " : ", false);
                        }
                        print_data($value, false);
                    }
                } else {
                    if (!empty($value)) {
                        if (is_string($key)) {
                            print_out_data_while_excute(make_bold_text($key) . " : " . $value, $new_line);
                        } else {
                            print_out_data_while_excute($value, true);
                        }
                    }
                }
            }
        } else {
            print_out_data_while_excute($data, true);
        }
    }

}
if (!function_exists("print_out_data_while_excute")) {

    /**
     * Print out Data while code is excute
     * @param type $data
     */
    function print_out_data_while_excute($data, $newline = true) {

        if (ob_get_level() == 0) {
            ob_start();
        }
// Print result

        if ($newline) {
            echo $data . "<br>";
        } else {
            echo $data;
        }
        ob_flush();
        flush();
        ob_end_flush();
    }

}
if (!function_exists("make_bold_text")) {

    function make_bold_text($text) {
        return "<b>$text</b>";
    }

}
if (!function_exists("system_log")) {

    /**
     * Write Log
     * @param type $content
     * @param type $location
     * @param type $line
     */
    function system_log($content, $location = "", $line = "", $file = "") {
        if (empty($file)) {
            $logs = CLIENT_DETECT_DIR . "/logs.log";
        } else {
            $logs = $file;
        }
        if (!empty($content)) {
            $refix = date("Y-m-d H:i:s");
            if (!empty($location)) {
                $refix .= ", $location, ";
            }
            if (!empty($line)) {
                $refix .= ", $line";
            }
            writeToFile($logs, "\n" . $refix . ": " . $content);
        }
    }

}
if (!function_exists("writeToFile")) {

    /**
     *  Write data to file
     * @param type $filePath
     * @param type $data
     * @param type $mode
     * @return boolean
     */
    function writeToFile($filePath, $data, $mode = 'a+') {
        try {
            $file = @fopen($filePath, $mode);
        } catch (Exception $exc) {
            return FALSE;
        }
        if ($file) {
            $re = @ fwrite($file, $data);
            @fclose($file);
            return $re > 0;
        }
        return FALSE;
    }

}

if (!function_exists("writeToFile_at_begin")) {

    /**
     * Ghi đè nội dung của File
     * @param type $filePath
     * @param type $data
     * @param type $mode
     * @return type
     */
    function writeToFile_at_begin($filePath, $data, $mode = 'w') {
        return writeToFile($filePath, $data, $mode);
    }

}

if (!function_exists("writeToFile")) {

    /**
     *  Write data to file
     * @param type $filePath
     * @param type $data
     * @param type $mode mặc định là ghi nối vào cuối file
     * @return boolean
     */
    function writeToFile($filePath, $data, $mode = 'a+') {
        try {
            $file = @ fopen($filePath, $mode);
        } catch (Exception $exc) {
            return FALSE;
        }
        if ($file) {
            $re = @ fwrite($file, $data);

            @fclose($file);

            return $re > 0;
        }

        return FALSE;
    }

}