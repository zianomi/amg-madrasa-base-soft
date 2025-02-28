<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 9/21/2017
 * Time: 10:38 PM
 */


class BaseWs
{

    private $student = 0;
    private $parentsId = 0;
    private $session = 0;
    private $branch = 0;
    private $class = 0;
    private $section = 0;



    /**
     * @return int
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * @param int $student
     */
    public function setStudent($student)
    {
        $this->student = $student;
    }

    /**
     * @return int
     */
    public function getParentsId()
    {
        return $this->parentsId;
    }

    /**
     * @param int $parentsId
     */
    public function setParentsId($parentsId)
    {
        $this->parentsId = $parentsId;
    }

    /**
     * @return int
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param int $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return int
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param int $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }

    /**
     * @return int
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param int $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return int
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param int $section
     */
    public function setSection($section)
    {
        $this->section = $section;
    }



    public function GetModel(){
        return new WsModel();
    }

    public function GetWebApiKeyPushNotification(){
        $stringKey = "AIzaSyC5__O4rsmjiwzgWfmGbdu6sda3wMSGMJE";
        return $stringKey;
    }

    public function GetCacheDir(){
        return CACHE . DRS . "ws" . DRS;
    }

    public function StudentGetCacheDir(){
        return $this->GetCacheDir() . $this->getStudent() . DRS;
    }


    public function getSuccResponse($data){
        $respons = array();
        $respons['status'] = "true";
        $respons['statusCode'] = 200;
        $respons['statusMessage'] = "success";
        //$respons['data'] = array_merge($this->GetMainStudentsData(),$data);
        $respons['data'] = $data;
        header('Content-type: application/json');
        return json_encode($respons);
    }


    public function getErrorResponse($errors,$msg = ""){
        $respons = array();
        $respons['status'] = "false";
        $respons['statusCode'] = 404;
        $respons['statusMessage'] = "fail";
        $respons['errors'] = $errors;
        $respons['msg'] = $msg;
        header('Content-type: application/json');
        return json_encode($respons);
    }

    public function GetCurrentMonthStartDate(){
        $year = date("Y");
        $month = date("m");
        return $year . "-" . $month . "-01";
    }

    public function GetCurrentMonthEndDate(){
        $year = date("Y");
        $month = date("m");
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        return $year . "-" . $month . "-" . $day;
    }

    public function GetSessionMonthEndDate($date){
        $dateArr = explode("-",$date);
        $day = cal_days_in_month(CAL_GREGORIAN, $dateArr[1],$dateArr[0]);
        return $dateArr[0] . "-" . $dateArr[1] . "-" . $day;
    }

    public function GetSessions(){
        $dir = $this->GetCacheDir();
        $file = $dir . "sessions";
        if (file_exists($file) && (filemtime($file) > (time() - 3600 ))){
            return unserialize(file_get_contents($file));
        }
        $set = new SettingModel();
        $allSessions = $set->allSessions();

        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($allSessions),LOCK_EX);
        unset($set);

        return $allSessions;

    }


    public function GetParentAppsettings(){
        $dir = $this->GetCacheDir();
        $file = $dir . "parents_app_settings";
        if (file_exists($file) && (filemtime($file) > (time() - 3600 ))){
            return unserialize(file_get_contents($file));
        }
        $set = new SettingModel();
        $serializeData = $set->GetDataSettings($set->DataKeys("parents_app"));
        $data = unserialize($serializeData);

        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($data),LOCK_EX);
        unset($set);

        return $data;

    }

    public function GetSessionMonths(){
        $dir = $this->GetCacheDir();
        $file = $dir . "session_months";
        if (file_exists($file) && (filemtime($file) > (time() - 60 ))){
            return unserialize(file_get_contents($file));
        }
        $set = new SettingModel();
        $sessionData = $set->GetCurrentSession();
        $sessionStartDate = $sessionData['start_date'];
        $sessionEndDate = $sessionData['end_date'];
        $start = strtotime($sessionStartDate);
        $end = strtotime($sessionEndDate);
        $currentdate = $start;
        $dateArr = array();
        while($currentdate < $end) {
            $cur_date = date('F, Y', $currentdate);
            $sqlFormatedDate = date('Y-m-d', $currentdate);
            $currentdate = strtotime('+1 month', $currentdate);
            $dateArr[$sqlFormatedDate] = $cur_date;
        }
        $data =  $dateArr;
        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($dateArr),LOCK_EX);
        unset($set);
        return $data;
    }


    public function GetCurrentSessionMonths(){
        $dir = $this->GetCacheDir();
        $file = $dir . "current_session_months";
        if (file_exists($file) && (filemtime($file) > (time() - 60 ))){
            return unserialize(file_get_contents($file));
        }
        $set = new SettingModel();
        $sessionData = $set->GetCurrentSession();
        $sessionStartDate = $sessionData['start_date'];
        $sessionEndDate = date("Y-m-d");
        $start = strtotime($sessionStartDate);
        $end = strtotime($sessionEndDate);
        $currentdate = $start;
        $dateArr = array();
        while($currentdate < $end) {
            $cur_date = date('F, Y', $currentdate);
            $sqlFormatedDate = date('Y-m-d', $currentdate);
            $currentdate = strtotime('+1 month', $currentdate);
            $dateArr[] = array("month" => $sqlFormatedDate, "month_label" => $cur_date);
        }
        $data =  $dateArr;
        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($dateArr),LOCK_EX);
        unset($set);
        return $data;
    }



    public function GetMainStudentsData(){
        $dir = $this->StudentGetCacheDir();
        $file = $dir . "student_detail";
        if (file_exists($file) && (filemtime($file) > (time() - 86400 ))){
            $data = unserialize(file_get_contents($file));
        }
        else{
            Tools::getModel("WsModel");
            $ws = new WsModel();
            $data = $ws->StudentData($this->getStudent());
            if(!file_exists($dir)){
                mkdir($dir,0777,true);
            }
            file_put_contents($file,serialize($data),LOCK_EX);
            unset($ws);
        }

        $branch = $data['branch_id'];
        $class = $data['class_id'];
        $section = $data['section_id'];
        $session = $data['session_id'];
        $this->setSession($session);
        $this->setSection($section);
        $this->setClass($class);
        $this->setBranch($branch);

        return array("student_detail" => $data);
    }



    public function sessionToken(){
        return "jkEFGHIJMNOabcdefopqrstuvwxyz!@#ghiKLlmn$%&*()RSTUVWXYZ12ABCD345";
    }

    public function GlToken(){
        return "!@#ghiKLlmn$%&*()RSTUVWXYZ12ABCD345jkEFGHIJMNOabcdefopqrstuvwxyz";
    }



    public function GetAdminAppsettings(){
        $dir = $this->GetCacheDir();
        $file = $dir . "admin_app";
        if (file_exists($file) && (filemtime($file) > (time() - 3600 ))){
            return unserialize(file_get_contents($file));
        }
		
		
        $set = new SettingModel();
        $serializeData = $set->GetDataSettings($set->DataKeys("admin_app"));
        $data = unserialize($serializeData);



        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($data),LOCK_EX);
        unset($set);

        return $data;

    }

    public function sendPushNotification($title,$message,$push_type,$include_image,$regId){
        Tools::getLib("Firebase");
        Tools::getLib("PushNotification");
        $firebase = new Firebase();
        $push = new PushNotification();
        $firebase->setFirebaseApiKey($this->GetWebApiKeyPushNotification());

        $payload = array();
        $payload['team'] = 'Amgdevs';
        $payload['score'] = '5.6';
        $push->setTitle($title);
        $push->setMessage($message);
        //if ($include_image) {
        $push->setImage($include_image);
        //} else {
            //$push->setImage('');
        //}
        $push->setIsBackground(FALSE);
        $push->setPayload($payload);


        $json = '';
        $response = '';

        if ($push_type == 'topic') {
            $json = $push->getPush();
            $response = $firebase->sendToTopic('global', $json);
        } else if ($push_type == 'individual') {
            $json = $push->getPush();
            $response = $firebase->send($regId, $json);
        }

        else if ($push_type == 'multiple') {
            $json = $push->getPush();
            $response = $firebase->sendMultiple($regId, $json);
        }

        return $response;
    }


    public function errorResponse($data)
    {

        header('Content-type: application/json');
        http_response_code(422);
        return json_encode(array("error" => $data));
    }


    public function successResponse($data)
    {
        ob_clean();
        header_remove();
        header("Content-type: application/json; charset=utf-8");
        http_response_code(200);
        return json_encode($data);
    }
}
