<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 1/12/2017
 * Time: 10:57 AM
 */
class Tools{

    const inst =  null;
    private static $url;
    private static $pageTitle = "Build Pakistan";
    private static $cacheDir;
    private static $vendorDir;
    private static $direction = "ltr";
    private static $lang = "en";
    private static $webUrl;
    private static $userName;
    private static $userImage;
    private static $transData = array();
    private static $transDir;
    private static $modelsDir;
    private static $libsDir;


    /**
     * Call this method to get singleton
     *
     * @return Tools
     */
    public static function Instance()
    {
        if (self::inst === null) {
            $inst = new Tools();
        }
        return $inst;
    }

    /**
     * Private ctor so nobody else can instantiate it
     *
     */
    private function __construct()
    {
    }

    public static function getUserName()
    {
        return $_SESSION['UserName'];
    }

    public static function langArr(){
        return array("ar" => 1, "ur" => 2, "en" => 3);
    }

    public static function getLangId(){
        $langArr = self::langArr();
        return $langArr[self::getLang()];
    }


    /**
     * @return mixed
     */
    public static function getUrl()
    {
        return self::$url;
    }

    /**
     * @param mixed $url
     */
    public static function setUrl($url)
    {
        self::$url = $url;
    }

    /**
     * @return mixed
     */
    public static function getModelsDir()
    {
        return self::$modelsDir;
    }

    /**
     * @param mixed $modelsDir
     */
    public static function setModelsDir($modelsDir)
    {
        self::$modelsDir = $modelsDir;
    }

    /**
     * @return mixed
     */
    public static function getLibsDir()
    {
        return self::$libsDir;
    }

    /**
     * @param mixed $libsDir
     */
    public static function setLibsDir($libsDir)
    {
        self::$libsDir = $libsDir;
    }





    /**
     * @return string
     */
    public static function getPageTitle()
    {
        return self::$pageTitle;
    }

    /**
     * @param string $pageTitle
     */
    public static function setPageTitle($pageTitle)
    {
        self::$pageTitle = $pageTitle;
    }

    /**
     * @return mixed
     */
    public static function getCacheDir()
    {
        return self::$cacheDir;
    }

    /**
     * @param mixed $cacheDir
     */
    public static function setCacheDir($cacheDir)
    {
        self::$cacheDir = $cacheDir;
    }

    /**
     * @return mixed
     */
    public static function getVendorDir()
    {
        return self::$vendorDir;
    }

    /**
     * @param mixed $vendorDir
     */
    public static function setVendorDir($vendorDir)
    {
        self::$vendorDir = $vendorDir;
    }

    /**
     * @return string
     */
    public static function getDirection()
    {
        return self::$direction;
    }

    /**
     * @param string $direction
     */
    public static function setDirection($direction)
    {
        self::$direction = $direction;
    }

    /**
     * @return string
     */
    public static function getLang()
    {
        return self::$lang;
    }

    /**
     * @param string $lang
     */
    public static function setLang($lang)
    {
        self::$lang = $lang;
    }

    /**
     * @return mixed
     */
    public static function getWebUrl()
    {
        return self::$webUrl;
    }

    /**
     * @param mixed $webUrl
     */
    public static function setWebUrl($webUrl)
    {
        self::$webUrl = $webUrl;
    }





    /**
     * @return mixed
     */
    public static function getUserImage()
    {
        return self::$userImage;
    }

    /**
     * @param mixed $userImage
     */
    public static function setUserImage($userImage)
    {
        self::$userImage = $userImage;
    }




    public static function alpha($value)
    {
        //Check if the value is alphabetical
        if(!preg_match('/^([a-z])+$/i', $value))
        {
            return false;
        }

        return true;
    }

    public static function numeric($value)
        {
            //Check if the value is numeric
            if(!preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $value))
            {
                return false;
            }
            return true;
        }






    public static function setDirectionAuto(){
        $lnag = self::getLang();
        switch($lnag){
            case "ur":
            case "ar":
                self::$direction = "rtl";
            break;
            default:
                self::$direction = "ltr";
        }
    }

    /**
     * @return array
     */
    public static function getTransData()
    {
        return self::$transData;
    }

    /**
     * @param array $transData
     */
    public static function setTransData($transData)
    {
        self::$transData = $transData;
    }

    /**
     * @return mixed
     */
    public static function getTransDir()
    {
        return self::$transDir . DIRECTORY_SEPARATOR . self::getLang();
    }

    /**
     * @param mixed $transDir
     */
    public static function setTransDir($transDir)
    {
        self::$transDir = $transDir;
    }








    public static function makeLink($bundle, $phpFile, $pageCode, $action)
    {
       return self::getUrl() . "?menu=" . $bundle . "&page=" . $phpFile . "&code=" . $pageCode . "&action=".$action;
    }

    public static function Redir($bundle, $phpFile, $pageCode, $action){
        $url = self::getUrl() . "?menu=" . $bundle . "&page=" . $phpFile . "&code=" . $pageCode . "&action=".$action;
        header("Location: " . $url);
    }



    public static function extractStringTag($str){
        //preg_match_all("/\{(\w+)\}(.+?)\{\/\\1\}/", $str, $matches);
        preg_match("/\{(\w+)\}(.+?)\{\/\\1\}/", $str, $matches);
        return $matches[2];
    }

    public static function transnoecho($transKey){
        $data = self::getTransData();
        if(isset($data[$transKey])){
            return $data[$transKey];
        }
        return $transKey;

    }

    public static function trans($transKey){
        $data = self::getTransData();
        if(isset($data[$transKey])){
            echo $data[$transKey];
            return;
        }
        echo $transKey;
        return;
    }

    public static function getModel($model){
        require_once self::getModelsDir() . DIRECTORY_SEPARATOR . $model . ".php";
    }

    public static function getLib($libFile){
        require self::getLibsDir() . DIRECTORY_SEPARATOR . $libFile . ".php";
    }

    public static function getInclude($libFile){
        require INCLUDES . DIRECTORY_SEPARATOR . $libFile . ".php";
    }


    public function getMysqlCon(){
        $mysqliConn = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        $mysqliConn->set_charset("utf8");

        return $mysqliConn;
    }

    public function Message($type, $msg){
        switch ($type) {
            case 'info':
                $class = 'alert';
                break;
            case 'alert':
                $class = 'alert alert-error';
                break;
            case 'succ':
                $class = 'alert alert-success';
                break;
        }
        $htm = '<div class="' . $class . '" style="font-size:20px;">';
        $htm .= '<button data-dismiss="alert" class="close" type="button">×</button>'. $msg . '</div>';

        return $htm;

    }


    public function MessageOnly($type, $msg){
        switch ($type) {
            case 'info':
                $class = 'alert';
                break;
            case 'alert':
                $class = 'alert alert-error';
                break;
            case 'succ':
                $class = 'alert alert-success';
                break;
        }
        $htm = '<div class="' . $class . '" style="font-size:20px;">';
        $htm .= $msg . '</div>';

        return $htm;

    }

    public function getUserIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public function intVal($val){
        return intval($val);
    }

    /**
     * @return mixed
     */
    public static function getUserId()
    {
        return isset($_SESSION['UserId']) ? $_SESSION['UserId'] : "";
    }







   public function setInsertDefaultValues($data){
       $date = date("Y-m-d H:i:s");
       //$vals = array(self::getUserId(),"NULL","$date","NULL");
       $vals = array(self::getUserId(),"$date");
       return array_merge($data,$vals);
   }



    public function GetInt($id)
    {
        return intval($id);
    }


    public function GetExplodedInt($int)
    {
        $exp = explode("-", $int);
        return $this->GetInt($exp[0]);
    }

    public function ExplodedInt($int)
    {
        $exp = explode("-", $int);
        return $exp[0];
    }

    public function explodedVal($val,$int){
        $exp = explode("-", $val);
        return $exp[$int];
    }


    public function GetExplodedVar($var)
    {
        $exp = explode("-", $var);
        return htmlspecialchars($exp[1]);
    }

    public function excludedPages(){
        $pages = array(
                "login" => "login"
               ,"" => ""
               ,"logout" => "logout"
              ,"dashboard","404"
        );
        return $pages;
    }


    public function expandDirectories($base_dir) {
          $directories = array();
          foreach(scandir($base_dir) as $file) {
                if($file == '.' || $file == '..') continue;
                $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
                if(is_dir($dir)) {
                    $directories []= $dir;
                    $directories = array_merge($directories, $this->expandDirectories($dir));
                }
          }
          return $directories;
    }


    public function displayErrorArray($errors = array()){
        if(count($errors)>0){
            echo $this->Message("alert",implode("<br />",$errors));
        }
    }

    public static function handleSessionData(){
        $sessionArr = array();
        if(isset($_SESSION['AmgSettingsData'])){
            $sessionArr = $_SESSION['AmgSettingsData'];
        }

        return $sessionArr;
    }

    public static function getPrefix(){
        return PR;
    }



    public function ChangeDateFormat($date)
    {
        $db_date = '';
        $dates = explode("-", $date);
        @$db_date .= $dates[2] . "-" . $dates[1] . "-" . $dates[0];
        return $db_date;
    }


    public function checkDateFormat($date)
    {
        if (preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts)) {
            if (checkdate($parts[2], $parts[3], $parts[1]))
                return true;
            else
                return false;
        } else
            return false;
    }


    public function makeGenderKey($key){
        if($key == 2){
            return "gender_two";
        }

        return "gender_one";
    }


    public function sortArrayByArray(array $array, array $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
                unset($array[$key]);
            }
        }
        return $ordered + $array;
    }



    public function GetCurrentUrl(){
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public function checkYoutubeUrl($url)
    {
        preg_match_all(
            "#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#",
            $url,
            $matches
        );

        if (strlen($matches[0][0]) == 11) {
            return true;
        }

        return false;
    }

    public function getBranchName()
    {
        return isset($_SESSION['branchName']) ? $_SESSION['branchName'] : "";
    }
}
