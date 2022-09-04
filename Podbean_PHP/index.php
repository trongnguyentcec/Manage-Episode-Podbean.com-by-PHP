<?php

/*
 * Created on Sep 4, 2022 2:44:36 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 * 
 */
require_once 'autoload.inc';
$url = get_podbean_redirect_url_from_dir();
echo "Get: Redirect URI (Development) & Redirect URI (Production):<br> ";
echo "$url<br> ";


