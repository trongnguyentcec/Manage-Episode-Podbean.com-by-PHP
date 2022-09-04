<?php

namespace Podbean;

use function trim_by_words;

/**
 *  Episode Base
 * Created on Jul 5, 2022 10:31:19 AM
 * Created by @author Trong Nguyen <trong.nguyenbt@gmail.com> trong.nguyenbt@gmail.com
 */
class EpisodeBase {

    /**
     * Trạng thái Episode là Draff
     */
    public static $STATUS_DRAFT = "draft";

    /**
     * Trạng thái Episode là published
     */
    public static $STATUS_PUBLISHED = "publish";

    /**
     * node ID
     */
    protected $nid;
    public $title;
    public $content;
//    public $status = "publish";

    /**
     * tránh public khi media chưa upload xong
     */
    public $status = "publish";
    public $type = "public";
    public $apple_episode_type = "full";
    /*
     * If an episode is within a season use this tag.Where season is a non-zero integer (1, 2, 3,etc.) representing your season number.
     */
    public $season_number;

    /**
     * If all your episodes have numbers and you would like to be ordered based on them use this tag for each one.
     */
    public $episode_number;

    /**
     * Local Mp3
     */
    public $localMp3;

    /**
     * Local Image
     */
    public $localImage;

    /**
     * temporary remote Mp3
     */
    public $tempRemoteMp3;

    /**
     *  temporary remote Image
     */
    public $tempRemoteImage;

    /**
     * update episode fields
     */
    protected $updateFields = array();

    /**
     * trim title by words 
     * Title của Episode không vượt quá 200 ký tự
     * @param string $title
     */
    public static function trimTitle(&$title) {
        $len = strlen($title);
        /* trim title nếu vượt quá 200 */
        if ($len > 200) {
            $title = trim_by_words($title, 196) . " ...";
        }
    }

    /**
     * Init Episode base
     * @param type $title
     * @param type $content
     * @param type $mp3
     * @param type $image
     * @param type $season_number
     * @param type $episode_number
     */
    public function __construct($title = "", $content = "", $mp3 = "", $image = "", $season_number = "", $episode_number = "") {
        /**
         * trim title
         */
        static::trimTitle($title);
        $this->setTitle($title);
        $this->setContent($content);
        $this->episode_number = $episode_number;
        $this->season_number = $season_number;
        $this->localImage = $image;
        $this->localMp3 = $mp3;
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    public function updateData_add($key, $value) {
        $this->updateFields[$key] = $value;
    }

    /**
     * convert to array
     * @return array
     */
    public function convertToArray() {
        $data = array(
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'type' => $this->type,
            'apple_episode_type' => $this->apple_episode_type,
        );
        if (!empty($data['season_number'])) {
            $data['season_number'] = $this->season_number;
        }
        if (!empty($data['episode_number'])) {
            $data['episode_number'] = $this->episode_number;
        }

        return $data;
    }

    /**
     * Reset update fields
     */
    public function resetUpdateFields() {
        $this->updateFields = array();
    }

    public function getUpdateFields() {
        return $this->updateFields;
    }

    public function setUpdateFields($updateFields): void {
        $this->updateFields = $updateFields;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getType() {
        return $this->type;
    }

    public function getSeason_number() {
        return $this->season_number;
    }

    public function getEpisode_number() {
        return $this->episode_number;
    }

    public function setTitle($title): void {
        static::trimTitle($title);
        $this->title = $title;
        $this->updateData_add("title", $title);
    }

    public function setContent($content): void {
        $this->content = $content;
        $this->updateData_add("content", $content);
    }

    public function setStatus($status): void {
        $this->status = $status;
        $this->updateData_add("status", $status);
    }

    public function setType($type): void {
        $this->type = $type;
        $this->updateData_add("type", $type);
    }

    public function setSeason_number($season_number): void {
        $this->season_number = $season_number;
        $this->updateData_add("season_number", $season_number);
    }

    public function setEpisode_number($episode_number): void {
        $this->episode_number = $episode_number;
        $this->updateData_add("episode_number", $episode_number);
    }

    public function getLocalMp3() {
        return $this->localMp3;
    }

    public function getLocalImage() {
        return $this->localImage;
    }

    public function setLocalMp3($localMp3): void {
        $this->localMp3 = $localMp3;
    }

    public function setLocalImage($localImage): void {
        $this->localImage = $localImage;
    }

    public function getTempRemoteMp3() {
        return $this->tempRemoteMp3;
    }

    public function getTempRemoteImage() {
        return $this->tempRemoteImage;
    }

    public function setTempRemoteMp3($tempRemoteMp3): void {
        $this->tempRemoteMp3 = $tempRemoteMp3;
    }

    public function setTempRemoteImage($tempRemoteImage): void {
        $this->tempRemoteImage = $tempRemoteImage;
    }

    public function getNid() {
        return $this->nid;
    }

    public function getApple_episode_type() {
        return $this->apple_episode_type;
    }

    public function setNid($nid): void {
        $this->nid = $nid;
    }

    public function setApple_episode_type($apple_episode_type): void {
        $this->apple_episode_type = $apple_episode_type;
        $this->updateData_add("apple_episode_type", $apple_episode_type);
    }

}
