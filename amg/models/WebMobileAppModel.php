<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/26/2017
 * Time: 12:55 PM
 */

class WebMobileAppModel extends BaseModel
{

    protected function getTableName()
    {
    }

    public function MeetingAttendees($key){
        $ret[1] = "Mother";
        $ret[2] = "Father";
        $ret[3] = "Parents";
        return $ret[$key];
    }

    public function GetCirculars($params = array()){

        $sql = "SELECT
            `jb_circulars`.`short_desc`
            , `jb_circulars`.`id`
            , `jb_circulars`.`date`
            , `jb_branches`.`title` AS branch_title
            , `jb_sessions`.`title` AS session_title
        FROM
            `jb_circulars`
            INNER JOIN `jb_branches`
                ON (`jb_circulars`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_circulars`.`session_id` = `jb_sessions`.`id`) WHERE 1 ";

        if (!empty($params['branch'])) {
            $sql .= " AND `jb_circulars`.`branch_id` = " . $params['branch'];
        }

        if (!empty($params['session'])) {
            $sql .= " AND `jb_circulars`.`session_id` = " . $params['session'];
        }

        if (!empty($params['date']) && empty($params['to_date'])) {
            $sql .= " AND `jb_circulars`.`date` = '" . $params['date'] . "'";
        }

        if (!empty($params['date']) && !empty($params['to_date'])) {
            $sql .= " AND `jb_circulars`.`date` BETWEEN '" . $params['date'] . "' AND '" . $params['to_date'] . "'";
        }

        $sql .= " ORDER BY `jb_circulars`.`date` DESC";


        if (!empty($params['limit'])) {
            $sql .= " LIMIT " . $params['limit'];
        }

        $res = $this->getResults($sql);
        return $res;

    }

    public function inserCicular($data)
    {
        $table = "jb_circulars";
        $this->insert($table, $this->setInsert($data));
        return $this->lastid();
    }

    public function removeCicular($id)
    {
        $whereColumn = "id";
        $table = "jb_circulars";
        $msg = array();
        $where = array($whereColumn => $id);
        if (!empty($id)) {
            if ($this->delete($table, $where, 1)) {
                $msg["status"] = true;
                $msg["msg"] = "OK";
            } else {
                $msg["status"] = false;
                $msg["msg"] = $this->link->error;
            }
        }
        return $msg;
    }

    public function GetMeetings($params = array()){

        $sql = "SELECT
            `jb_meetings`.`date`
            , `jb_meetings`.`id` AS meeting_id
            , `jb_meetings`.`short_desc`
            , `jb_meetings`.`attendees`
            , `jb_meetings`.`time`
            , `jb_meetings`.`student_id` AS id
            , `jb_sessions`.`title` AS `session_title`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_branches`.`title` AS `branch_title`
        FROM
            `jb_meetings`
            INNER JOIN `jb_sessions`
                ON (`jb_meetings`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_students`
                ON (`jb_meetings`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`) WHERE 1";



        if (!empty($params['branch'])) {
            $sql .= " AND `jb_branches`.`id` = " . $params['branch'];
        }


        if (!empty($params['id'])) {
            $sql .= " AND `jb_meetings`.`student_id` = " . $params['id'];
        }

        if (!empty($params['session'])) {
            $sql .= " AND `jb_meetings`.`session_id` = " . $params['session'];
        }

        if (!empty($params['date']) && empty($params['to_date'])) {
            $sql .= " AND `jb_meetings`.`date` = '" . $params['date'] . "'";
        }

        if (!empty($params['date']) && !empty($params['to_date'])) {
            $sql .= " AND `jb_meetings`.`date` BETWEEN '" . $params['date'] . "' AND '" . $params['to_date'] . "'";
        }

        $sql .= " ORDER BY `jb_meetings`.`date` DESC";

        //$sql .= " GROUP BY ";


        if (!empty($params['limit'])) {
            $sql .= " LIMIT " . $params['limit'];
        }

        $res = $this->getResults($sql);
        return $res;
    }

    public function insertMeetings($data = array())
    {

        $tableName = $this->getPrefix() . "meetings";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;

        /*$table = "jb_meetings";
        $this->insert($table, $this->setInsert($data));
        return $this->lastid();*/
    }

    public function removeMeeting($id)
    {
        $whereColumn = "id";
        $table = "jb_meetings";
        $msg = array();
        $where = array($whereColumn => $id);
        if (!empty($id)) {
            if ($this->delete($table, $where, 1)) {
                $msg["status"] = true;
                $msg["msg"] = "OK";
            } else {
                $msg["status"] = false;
                $msg["msg"] = $this->link->error;
            }
        }
        return $msg;
    }


    public function GetEvents($params = array()){

        $sql = "SELECT jb_events.id
                      ,jb_events.title
                      ,jb_events.image
                      ,jb_events.path
                      ,jb_events.date
                       FROM `jb_events`
                        WHERE 1";

        if (!empty($params['date']) && empty($params['to_date'])) {
            $sql .= " AND `jb_events`.`date` = '" . $params['date'] . "'";
        }

        if (!empty($params['date']) && !empty($params['to_date'])) {
            $sql .= " AND `jb_events`.`date` BETWEEN '" . $params['date'] . "' AND '" . $params['to_date'] . "'";
        }

        $sql .= " ORDER BY `jb_events`.`date` DESC";

        if (!empty($params['limit'])) {
            $sql .= " LIMIT " . $params['limit'];
        }


        $res = $this->getResults($sql);

        return $res;

    }

    public function removeEvent($id)
    {
        $whereColumn = "id";
        $table = "jb_events";
        $msg = array();
        $where = array($whereColumn => $id);
        if (!empty($id)) {
            if ($this->delete($table, $where, 1)) {
                $msg["status"] = true;
                $msg["msg"] = "OK";
            } else {
                $msg["status"] = false;
                $msg["msg"] = $this->link->error;
            }
        }
        return $msg;
    }

    public function inserEvent($data)
    {
        $table = "jb_events";
        $this->insert($table, $this->setInsert($data));
        return $this->lastid();
    }

    public function inserEventGallery($data)
    {
        $table = "jb_events_gallery";
        $this->insert($table, ($data));
        return $this->lastid();
    }


    public function GetEventGallery($id){
        $sql = "SELECT * FROM `jb_events_gallery` WHERE event_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetEvent($id){
        $sql = "SELECT * FROM `jb_events` WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res;
    }


    public function removeGalleryImage($id)
    {
        $whereColumn = "id";
        $table = "jb_events_gallery";
        $msg = array();
        $where = array($whereColumn => $id);
        if (!empty($id)) {
            if ($this->delete($table, $where, 1)) {
                $msg["status"] = true;
                $msg["msg"] = "OK";
            } else {
                $msg["status"] = false;
                $msg["msg"] = $this->link->error;
            }
        }
        return $msg;
    }

    public function GetTimeTableSyllabusList($params = array()){
        $sql = "SELECT
            `jb_subjects`.`id` AS `subject_id`
            , `jb_subjects`.`title` AS `subject_title`
            , `jb_periods`.`start_time`
            , `jb_periods`.`end_time`
            , `jb_classes`.`id` AS `class_id`
            , `jb_classes`.`title` AS `class_title`
            , `jb_sections`.`id` AS `section_id`
            , `jb_sections`.`title` AS `section_title`
        FROM
            `jb_period_subjects`
            INNER JOIN `jb_subjects` 
                ON (`jb_period_subjects`.`subject_id` = `jb_subjects`.`id`)
            INNER JOIN `jb_periods` 
                ON (`jb_period_subjects`.`period_id` = `jb_periods`.`id`)
            INNER JOIN `jb_classes` 
                ON (`jb_subjects`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections` 
                ON (`jb_periods`.`section_id` = `jb_sections`.`id`)
        WHERE  1";

        if (!empty($params['class'])) {
            $sql .= " AND `jb_subjects`.`class_id` = " . $params['class'];
        }

        if (!empty($params['section'])) {
            $sql .= " AND `jb_periods`.`section_id` = " . $params['section'];
        }

        if (!empty($params['session'])) {
            $sql .= " AND `jb_periods`.`session_id` = " . $params['session'];
        }

        $sql .= " GROUP BY `subject_id`";
        $sql .= " ORDER BY `jb_periods`.`start_time`, `jb_periods`.`end_time` ASC";

        $res = $this->getResults($sql);
        return $res;
    }

    public function GetSubjectSyllabusList($id,$params=array()){
        $sql = "SELECT
            `jb_syllabus`.`title`
            , `jb_syllabus`.`start_page_no`
            , `jb_syllabus`.`end_page_no`
            , `jb_session_syllabus`.`date`
        FROM
            `jb_session_syllabus`
            INNER JOIN `jb_syllabus` 
                ON (`jb_session_syllabus`.`syllabus_id` = `jb_syllabus`.`id`)
        WHERE 1";
        $sql .= " AND `jb_syllabus`.`subject_id` = $id";
        if (!empty($params['limit'])) {
            $sql .= " LIMIT " . $params['limit'];
        }
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetCompletedSyllabusList($subjectId){
        $sql = "SELECT
            `jb_teacher_syllabus`.`date` AS `teacher_completed_date`
            , `jb_teacher_syllabus`.`teacher_id`
            , `jb_session_syllabus`.`date` AS `session_syllabus_date`
            , `jb_syllabus`.`title`
            , `jb_syllabus`.`start_page_no`
            , `jb_syllabus`.`end_page_no`
            , `jb_syllabus`.`subject_id`
        FROM
            `jb_teacher_syllabus`
            INNER JOIN `jb_session_syllabus` 
                ON (`jb_teacher_syllabus`.`session_syllabus_id` = `jb_session_syllabus`.`syllabus_id`)
            INNER JOIN `jb_syllabus` 
                ON (`jb_session_syllabus`.`syllabus_id` = `jb_syllabus`.`id`)
        WHERE 1";

        $sql .= " AND `jb_syllabus`.`subject_id` = $subjectId";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetHomeWorks($params=array()){
        $sql = "SELECT
            `jb_home_works`.`date`
            , `jb_home_works`.`short_desc`
            , `jb_periods`.`start_time`
            , `jb_periods`.`end_time`
            , `jb_subjects`.`id` AS `subject_id`
            , `jb_subjects`.`title` AS `subject_title`
        FROM
            `jb_home_works`
            INNER JOIN `jb_periods` 
                ON (`jb_home_works`.`section_id` = `jb_periods`.`section_id`)
            INNER JOIN `jb_period_subjects` 
                ON (`jb_home_works`.`subject_id` = `jb_period_subjects`.`subject_id`)
            INNER JOIN `jb_subjects` 
                ON (`jb_home_works`.`subject_id` = `jb_subjects`.`id`) 
                AND (`jb_periods`.`id` = `jb_period_subjects`.`period_id`) 
                AND (`jb_period_subjects`.`subject_id` = `jb_subjects`.`id`)
        WHERE 1";

        if (!empty($params['section'])) {
            $sql .= " AND `jb_home_works`.`section_id` =  " . $params['section'];
        }
        if (!empty($params['date'])) {
            $sql .= " AND date = '" . $params['date'] . "'";
        }
        $sql .= " GROUP BY `jb_home_works`.`subject_id`";
        $res = $this->getResults($sql);
        return $res;
    }

    public function InsertLeaverRequest($data){
        $pr = $this->getPrefix();
        $table = $pr . "leave_requests";
        if($this->insert($table, $data)){
            return true;
        }
        return false;
    }

    public function InsertSuggestions($data){
        $pr = $this->getPrefix();
        $table = $pr . "suggestions";
        if($this->insert($table, $data)){
            return true;
        }
        return false;
    }

    public function checkLogin($nic, $pwd){
        $sql = "SELECT * FROM ".$this->getPrefix()."student_parents WHERE father_nic = '$nic' AND password = '$pwd' LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function ForgotPassword($nic,$email){
        $sql = "SELECT * FROM ".$this->getPrefix()."student_parents WHERE father_nic = '$nic' AND father_email = '$email' LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function UpdateProfile($id,$newPwd){
        $update_where = array('id' => $id);
        $this->update('jb_student_parents', array("password" => $newPwd), $update_where, 1);
    }

    public function checkProfile($id,$oldPwd){
        $checkParents = array(
             'id' => $id,
             'password' => $oldPwd
        );
        $exists = $this->exists( 'jb_student_parents', 'id', $checkParents );
        if($exists){
            return true;
        }
        return false;
    }

    public function checkFirstTimeParents($fatherNic,$motherNic,$fatherFone){
        $checkParents = array(
             'father_nic' => $fatherNic,
             'mother_nic' => $motherNic,
             'father_mobile' => $fatherFone
        );
        $exists = $this->exists( 'jb_student_parents', 'id', $checkParents );
        if($exists){
            return true;
        }
        return false;
    }

    public function FindParentsChildren($parentId){

        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`fname`
            , `jb_students`.`gender`
            , `jb_students`.`grnumber`
            , `jb_student_profile`.`path`
            , `jb_student_profile`.`image`
            , `jb_branches`.`title` AS `branch_title`
            , `jb_classes`.`title` AS `class_title`
            , `jb_sections`.`title` AS `section_title`
            , `jb_sessions`.`title` AS `session_title`
        FROM
            `jb_student_profile`
            INNER JOIN `jb_students`
                ON (`jb_student_profile`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`)
        WHERE 1";

        $sql .= " AND `jb_students`.`parents_id` = $parentId";
        $sql .= " AND `jb_students`.`student_status` = 'current'";

        $res = $this->getResults($sql);
        return $res;
    }





}