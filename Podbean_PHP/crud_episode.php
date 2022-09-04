<?php

/*
 * Created on Sep 4, 2022 3:12:06 PM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 * 
 */
require_once 'autoload.inc';

/**
 * Publish episode to Podbean
 */
function add_episode() {
    /**
     * @see https://developers.podbean.com/podbean-api-docs/#api-Episode-Publish_New_Episode
     */
    $title = "Episode Title";
    $content = "Episode description";
    /**
     * Your Mp3 url (from your web), Ex: https://myweb.com/mp3-files/my-mp3.mp3
     */
    $mp3 = "https://myweb.com/mp3-files/my-mp3.mp3";
    /**
     * Your Image url (from your web), Ex: https://myweb.com/images/my-image.jpg
     */
    $image = "https://myweb.com/images/my-image.jpg";
    $episode = new Podbean\Episode($title, $content, $mp3, $image);
    /**
     * Set more fields ...
     */
    /**
     * Allowed values: "public", "premium", "private"
     */
//    $episode->setType("public");
    /**
     * Allowed values: "publish", "draft"
     */
//    $episode->setStatus("publish");
    $MyPodbeanAuth = new Podbean\MyPodbeanAuth();
    $response = $MyPodbeanAuth->add_remote_Episode($episode);
    /**
     * to get Epsiode fields ...
     */
    $episode_id = $response['episode']['id'];
    $media_url = $response['episode']['media_url'];
    /**
     * Save Response to database ....
     */
    var_dump($response);
    return $response;
}

/**
 * Update episode data
 * @see https://developers.podbean.com/podbean-api-docs/#api-Episode-Update_Episode
 * @param type $episodeID episode id @see $response['episode']['id']
 * @param type $title
 * @param type $content
 * @param type $type Allowed values: "public", "premium", "private"
 * @param type $status Allowed values: "publish", "draft"
 * @param type $remote_media_url
 * @param type $remote_logo_url
 */
function edit_episode($episodeID, $title, $content, $type, $status, $remote_media_url = "", $remote_logo_url = "") {

    /**
     * Podbean APIs are stupid, 
     * update one field, user have to update required fields!!!
     * 1. Set Title & Content
     */
    $episode = new Podbean\Episode($title, $content);
    /**
     * 2. Set type: Allowed values: "public", "premium", "private"
     */
    $episode->setType($type);
    /**
     * 3. Set status: Allowed values: "publish", "draft"
     */
    $episode->setStatus($status);
    /**
     * Set remote Mp3
     */
    if (!empty($remote_media_url)) {
        $episode->setRemote_media_url($remote_media_url);
    }
    /**
     * Set remote image
     */
    if (!empty($remote_logo_url)) {
        $episode->setRemote_logo_url($remote_logo_url);
    }
    /**
     * Update Episode
     */
    $MyPodbeanAuth = new Podbean\MyPodbeanAuth();
    $response = $MyPodbeanAuth->updateEpisode($episodeID, $episode);
    /**
     * to get Epsiode fields ...
     */
    $episode_id = $response['episode']['id'];
    $media_url = $response['episode']['media_url'];
    /**
     * Save Response to database ....
     */
    var_dump($response);
    return $response;
}

/**
 * Delete episode
 * @see https://developers.podbean.com/podbean-api-docs/#api-Episode-Delete_Episode
 * @param type $episodeID
 * @return type
 */
function delete_episode($episodeID) {
    /**
     * Delete episode
     */
    $MyPodbeanAuth = new Podbean\MyPodbeanAuth();
    $res = $MyPodbeanAuth->deleteEpisode($episodeID);
    return $res;
}
