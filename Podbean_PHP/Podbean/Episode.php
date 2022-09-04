<?php

namespace Podbean;

/**
 *  Episode PodBean:
 * add new episode by using:
 *  remote_media_url Your Mp3 url (from your web)
 *  remote_logo_url Your Image url (from your web)
 * Created on Jul 4, 2022 4:22:29 PM
 * Created by @author Trong Nguyen trong.nguyenbt@gmail.com
 * @see https://developers.podbean.com/
 * @see https://developers.podbean.com/podbean-api-docs/
 */
class Episode extends EpisodeBase {

    /**
     * Your Mp3 url (from your web), Ex: https://myweb.com/mp3-files/my-mp3.mp3
     */
    public $remote_media_url;

    /**
     * Your Image url (from your web), Ex: https://myweb.com/images/my-image.jpg
     */
    public $remote_logo_url;

    /**
     * PodBean Episode (không upload trước Mp3 & hình)
     * @param type $title
     * @param type $content
     * @param type $mp3
     * @param type $image
     * @param type $season_number
     * @param type $episode_number
     */
    public function __construct($title = "", $content = "", $mp3 = "", $image = "", $season_number = "", $episode_number = "") {
        parent::__construct($title, $content, $mp3, $image, $season_number, $episode_number);
        $this->remote_logo_url = $image;
        $this->remote_media_url = $mp3;
    }

    /**
     * get blank Episode instance
     * @return Podbean\Episode
     */
    public static function getInstance() {
        $obj = new Episode("", "");
        return $obj;
    }

    /**
     * Convert to array
     */
    public function convertToArray() {
        $data = parent::convertToArray();
        $data['remote_logo_url'] = $this->getRemote_logo_url();
        $data['remote_media_url'] = $this->getRemote_media_url();
        return $data;
    }

    public function getRemote_media_url() {
        return $this->remote_media_url;
    }

    public function getRemote_logo_url() {
        return $this->remote_logo_url;
    }

    public function setRemote_media_url($remote_media_url): void {
        $this->remote_media_url = $remote_media_url;
        $this->updateData_add("remote_media_url", $remote_media_url);
    }

    public function setRemote_logo_url($remote_logo_url): void {
        $this->remote_logo_url = $remote_logo_url;
        $this->updateData_add("remote_logo_url", $remote_logo_url);
    }

}
