<?php

namespace Podbean;

use function mime_content_type;
use function print_data;

/**
 * CRUD Podbean Episode
 * Created on Jul 4, 2022 4:27:44 PM
 * Created by @author Trong Nguyen trong.nguyenbt@gmail.com
 */
class BasePodBean extends BasePodBeanAuth {

    /**
     * Add episode to Podbean using remote URLs (Image & Mp3)
     * @param \Podbean\Episode $episode
     * @return type
     */
    public function add_remote_Episode(Episode $episode) {
        if (empty($episode)) {
            print_data('Error: empty Episode ');
            return;
        }
        /* Get Episode URL */
        $url = $this->getAdd_Episode_Full_URL();
        /* Get Data from Episode */
        $data = $episode->convertToArray();
        /* Run Add new Episode */
        $res = $this->runCurlPost_CRUD_Episode($url, $data);
        /* decode results */
        if (is_string($res)) {
            $res = json_decode($res, true);
        }
        /**
         * Check Podbean return errors
         */
        if (isset($res['error'])) {
            print_data('Error: update Episode. ');
            var_dump($res);
            return;
        }

        return $res;
    }

    /**
     * Read episode data by ID
     * @param type $episodeID
     */
    public function readEpisode($episodeID) {
        /* Get Episode Edit URL with params */
        $url = $this->getUpdateEpisodeFullURL($episodeID);
        $response = runCurl_GET_Episode($url);
        return $response;
    }

    /**
     * Delete Episode
     * @param type $episodeID
     * @return type
     */
    public function deleteEpisode($episodeID) {

        if (empty($episodeID)) {
            print_data("Error: Không có Episode ID = $episodeID");
            return;
        }
        /* Get Episode Delete URL with params */
        $url = $this->getDeleteEpisodeFullURL($episodeID);
        /**
         * Xóa luôn File
         */
        $data = array(
            'delete_media_fileoptional' => 'yes',
        );
        /* Run Add new Episode */

        $res = $this->runCurlPost_CRUD_Episode($url, $data);
        if (is_string($res)) {
            $data = json_decode($res, true);
            $res = isset($data['msg']);
        }


        return $res;
    }

    /**
     * Change Episode status to publish
     * @param type $media
     * @param type $episodeID
     * @return type
     */
    public function updateEpisode_Status($media, $episodeID = "", $status = "publish") {
        if (empty($media)) {
            print_data("Error: No media  ");
            return;
        }
        /**
         * Get Episode Field
         */
        if (empty($episodeID)) {
            $episodeData = get_episode_data_from_node($media);
            $episodeID = $episodeData['id'];
        }

        if (empty($episodeID)) {
            print_data("Error: No Episode ID = $episodeID");
            return;
        }
        $episode = new Episode();
        /**
         * load node if not exists
         */
        if (is_array($media)) {
            $nid = $media['nid'];
            $media = node_load($nid);
        }
        $episode->getUpdateEpisode_from_nodeMedia($media, $status);
        /* Get Episode Edit URL with params */
        $url = $this->getUpdateEpisodeFullURL($episodeID);
        /* Get updating Data from Episode */
        $data = $episode->getUpdateFields();
        if (empty($data)) {
            print_data("Error: No update data= $episodeID");
            var_dump($episode);
            return;
        }
        /* Run update Episode */

        $res = $this->runCurlPost_CRUD_Episode($url, $data);

        return $res;
    }

    /**
     * Update episode
     * @param Episode $episode
     */
    public function updateEpisode($episodeID, Episode $episode) {

        if (empty($episodeID)) {
            print_data("Error: No Episode ID = $episodeID");
            return;
        }
        if (empty($episode)) {
            print_data('Error: No Episode data');
            return;
        }
        /* Get Episode Edit URL with params */
        $url = $this->getUpdateEpisodeFullURL($episodeID);
        /* Get Data from Episode */
        $data = $episode->getUpdateFields();
        /* Run Add new Episode */

        $res = $this->runCurlPost_CRUD_Episode($url, $data);

        /* decode results */
        if (is_string($res)) {
            $res = json_decode($res, true);
        }
        if (isset($res['error'])) {
            print_data('Error: update Episode. ');
            var_dump($res);
            return $res;
        }


        return $res;
    }

    /**
     * Run curl in POST method
     * @param type $url
     * @param type $data
     * @param type $headers
     * @return type
     */
    public function runCurlPost_CRUD_Episode($url, $data = NULL) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $response = curl_exec($ch);

        print_data("<br> --- RESPONSES INFORMATION: ");
        var_dump($response);
        print_data("<br> --- # END RESPONSES INFORMATION ");

        if (curl_error($ch)) {
            trigger_error('Curl Error:' . curl_error($ch));
        }
        curl_close($ch);
        /**
         * print out responses
         */
        return $response;
    }

    /**
     * add File size and File Path to data
     * @param type $filePath file Path on server not File URL
     * @param type $data
     */
    public function addFile_Type_Size($filePath, &$data) {
        $content_type = mime_content_type($filePath);
        $size = filesize($filePath);

        $data['filesize'] = $size;
        $data['content_type'] = $content_type;
        /* Add token */
        $data['access_token'] = $this->getAccessToken();
    }

    /**
     * convert File URL to File Path
     * @param type $fileUrl
     * @return type
     */
    public function convertURL_to_FilePath($fileUrl) {
        $pathInfo = parse_url($fileUrl);
        $filePath = $pathInfo['path'];
        $filePath = ltrim($filePath, "/");
        $filePath = rtrim($filePath, "/");

        return $filePath;
    }

    /**
     * Set Podbean configs
     * @param type $data
     */
    protected function setPodbeanConfigs(&$data) {
        /**
         * Set Access token 
         */
        $data['access_token'] = $this->getAccessToken();
        $data['client_id'] = $this->getClient_id();
        $data['client_secret'] = $this->getClient_secret();
    }

    /**
     * get update episode URL  
     * @return string
     */
    public function getUpdateEpisodeFullURL($episodeID) {
        $url = $this->getUpdateEpsiodeURL($episodeID);
        /* build full params */
        $urlFull = $this->buildEpisodeFullURL_for_CRUD($url);
        return $urlFull;
    }

    /**
     * get delete episode  URL
     * @return string
     */
    public function getDeleteEpisodeFullURL($episodeID) {
        /**
         * @see https://developers.podbean.com/podbean-api-docs/#api-Episode-Delete_Episode
         */
        $url = $this->getUpdateEpsiodeURL($episodeID);
        $url = trim($url);
        $url .= "/delete";
        /* build full params */
        $urlFull = $this->buildEpisodeFullURL_for_CRUD($url);
        return $urlFull;
    }

    /**
     * get add Episode URL
     * @return string
     */
    public function getAdd_Episode_Full_URL() {
        $url = $this->getEpisodeURL();
        /* build full params */
        $urlFull = $this->buildEpisodeFullURL_for_CRUD($url);
        return $urlFull;
    }

    /**
     * get Authorize URL
     * @return string
     */
    public function buildEpisodeFullURL_for_CRUD($url) {
        $params = $this->getURL_Params();
        $url .= "?" . http_build_query($params);
        return $url;
    }

    /**
     * get Params of URL on GET & POST
     */
    protected function getURL_Params() {
        $params = array(
            'client_id' => $this->getClient_id(),
            'access_token' => $this->getAccessToken(),
//            'client_secret' => $this->getClient_secret(),
//            'scope' => $this->getPermissionString(),
        );
        return $params;
    }

}
