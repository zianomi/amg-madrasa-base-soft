<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 11/10/2017
 * Time: 12:03 PM
 */

class PushNotification
{
    private $title;
    private $message;
    private $image;
    // push message payload
    private $data;

    private $isBackground;

    function __construct() {

    }
    
    
    public function setTitle($title) {
        $this->title = $title;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setImage($imageUrl) {
        $this->image = $imageUrl;
    }

    public function setPayload($data) {
        $this->data = $data;
    }

    public function setIsBackground($isBackground) {
        $this->isBackground = $isBackground;
    }

    public function getPush() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['is_background'] = $this->isBackground;
        $res['data']['message'] = $this->message;
        $res['data']['image'] = $this->image;
        $res['data']['payload'] = $this->data;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        return $res;
    }

}