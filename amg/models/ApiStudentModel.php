<?php

class ApiStudentModel extends BaseModel
{

    const PENDING = 'pending';
    const PAID = 'paid';

    public function insertDevice($id, $device, $os)
    {

        $date = date("Y-m-d");

        $sql = "REPLACE INTO `jb_devices` (`id`, `student_id`, `device_token`, `date`, `os`) VALUES (NULL, $id, '$device', '$date', '$os')";
        if ($this->query($sql)) {
            return true;
        }

        return false;
    }

    public function insertQuery($id, $query)
    {
        $param['student_id'] = $id;
        $param['query_desc'] = $query;
        $param['date'] = date("Y-m-d");
        $param['is_replied'] = 0;
        if ($this->insert("jb_quries", $param)) {
            return true;
        }

        return false;
    }

    public function getPaidData($id)
    {
        $paid = self::PAID;

        $sql = "SELECT
    `jb_fee_invoice`.`invoice_id`
    , `jb_fee_invoice`.`recp_date`
    , `jb_fee_invoice`.`student_id`
    , `jb_fee_invoice`.`id`
    , SUM(`jb_fee_paid`.`fees`) - SUM(`jb_fee_paid`.`discount`) AS fees
FROM
    `jb_fee_invoice`
    INNER JOIN `jb_fee_paid` 
        ON (`jb_fee_invoice`.`id` = `jb_fee_paid`.`invoice_id`)
WHERE (`jb_fee_invoice`.`student_id`  = $id
    AND `jb_fee_invoice`.`invoice_status`  = '$paid'
    AND `jb_fee_paid`.`paid_status`  = '$paid')";
        $sql .= " GROUP BY `jb_fee_invoice`.`id`";
        $sql .= " ORDER BY `jb_fee_invoice`.`recp_date` DESC";
        $sql .= " LIMIT 25";



        return $this->getResults($sql);
    }

    public function getUnpaidFees($id)
    {
        $pending = self::PENDING;
        $sql = "SELECT (`jb_fee_paid`.`fees` - `jb_fee_paid`.`discount`) AS fees
    , `jb_fee_paid`.`fee_date`
    , `jb_fee_type`.`title` AS `type_title`
    , `jb_fee_paid`.`student_id`
    , `jb_fee_paid`.`id`
FROM
    `jb_fee_paid`
    INNER JOIN `jb_fee_type` 
        ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
WHERE (`jb_fee_paid`.`student_id`  = $id AND `jb_fee_paid`.`paid_status`  = '$pending' )";


        return $this->getResults($sql);
    }

    public function getContact($branch)
    {
        $sql = "SELECT
    `id`
    , `title`
    , `branch_fone`
    , `branch_address`
    , `email`
    , `latitude`
    , `longitude`
FROM
    `jb_branches`
WHERE (`id`  = $branch);";

        return $this->getSingle($sql);
    }


    public function getNotifications($id, $page)
    {
        $sql = "SELECT
    *
FROM
    `jb_notifications`
WHERE (`student_id`  = $id) ORDER BY date DESC";

        $sql .= " LIMIT $page, 50";

        //echo $sql;
        //exit;

        return $this->getResultsOrArr($sql);
    }

    public function getHomeworkDetail($id)
    {
        /*$sql = "SELECT
    `jb_teacher_home_works`.`id`
    , `jb_teacher_home_works`.`date`
    , `jb_teacher_home_works`.`description`
    , `jb_lession_session`.`chapter`
    , `jb_lession_session`.`topic`
    , `jb_lession_session`.`subtopic`
    , `jb_exam_names`.`title` AS `exam_name`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_teacher_home_works`
    INNER JOIN `jb_lession_session`
        ON (`jb_teacher_home_works`.`lesson_id` = `jb_lession_session`.`id`)
    INNER JOIN `jb_exam_names`
        ON (`jb_lession_session`.`exam_id` = `jb_exam_names`.`id`)
    INNER JOIN `jb_subjects`
        ON (`jb_lession_session`.`subject_id` = `jb_subjects`.`id`)
WHERE (`jb_teacher_home_works`.`id`  = $id)";

        $sql .= " LIMIT 1";*/

        //echo $sql;
        //exit;

        $sql = "SELECT hw.id, hw.title, hw.description, s.title AS subject_title, hw.submit_date
FROM `jb_class_home_works` hw
INNER JOIN jb_subjects s
ON hw.subject_id = s.id
WHERE hw.id = $id";


        return $this->getSingle($sql);
    }

    public function getHomeworkListing($param = array())
    {
        /*$session = $param['session'] ?: 0;
        $section = $param['section'] ?: 0;

        if (
            empty($session)
            || empty($section)
        ) {
            return array();
        }

        $sql = "SELECT
    `jb_teacher_home_works`.`id`
    , `jb_teacher_home_works`.`date`
    , `jb_teacher_home_works`.`description`
    , `jb_lession_session`.`chapter`
    , `jb_subjects`.`title`
    , `jb_teacher_home_works`.`description`
    , `jb_lession_session`.`subtopic`
FROM
    `jb_teacher_home_works`
    INNER JOIN `jb_lession_session` 
        ON (`jb_teacher_home_works`.`lesson_id` = `jb_lession_session`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_lession_session`.`subject_id` = `jb_subjects`.`id`)
WHERE (`jb_teacher_home_works`.`session_id`  = $session
    AND `jb_teacher_home_works`.`section_id`  = $section
    AND `jb_lession_session`.`session_id`  = $session) LIMIT 100;";

        //echo $sql;
        //exit;

        return $this->getResults($sql);*/

        $baseDate = date('Y-m-d');
        $tenDaysBefore = date('Y-m-d', strtotime('-5 days', strtotime($baseDate)));
        $fifteenDaysAfter = date('Y-m-d', strtotime('+7 days', strtotime($baseDate)));

        $sql = "SELECT hw.id, hw.title, s.title AS subject_title, hw.submit_date, hw.description
FROM `jb_class_home_works` hw
INNER JOIN jb_subject_groups s
ON hw.subject_id = s.id
WHERE 1";
        if (!empty($param['branch'])) {
            $sql .= " AND hw.`branch_id` =  " . $param['branch'];
        }
        if (!empty($param['class'])) {
            $sql .= " AND hw.`class_id` =  " . $param['class'];
        }
        if (!empty($param['section'])) {
            $sql .= " AND hw.`section_id` =  " . $param['section'];
        }
        if (!empty($param['session'])) {
            $sql .= " AND hw.`session_id` =  " . $param['session'];
        }
        if (!empty($param['subject'])) {
            $sql .= " AND hw.`subject_id` =  " . $param['subject'];
        }
        if (!empty($param['date'])) {
            $sql .= " AND hw.submit_date = '" . $param['date'] . "'";
        }
        else{
            $sql .= " AND hw.submit_date BETWEEN '$tenDaysBefore' AND '$fifteenDaysAfter'";
        }

        $sql .= " ORDER BY hw.submit_date DESC";

        //echo '<pre>'; print_r($sql); echo '</pre>';die("");


        return $this->getResults($sql);
    }



    public function getSyllabusLessons($param = array())
    {

        //$id = $param['id'] ?: 0;
        $subject = $param['subject'] ?: 0;
        $class = $param['class'] ?: 0;
        $session = $param['session'] ?: 0;
        $branch = $param['branch'] ?: 0;
        $section = $param['section'] ?: 0;



        if (
            empty($subject)
            || empty($class)
            || empty($session)
            || empty($branch)
            || empty($section)
        ) {
            return array();
        }




        $sql = "SELECT
    `jb_lession_session`.`id`
    , `jb_lession_session`.`chapter`
    , `jb_lession_session`.`topic`
    , `jb_lession_session`.`subtopic`
    , `jb_lession_session`.`exam_id`
    , `jb_exam_names`.`title` AS `exam_title`
    , `jb_teacher_lession_session`.`current_status`
    , `jb_lession_session`.`week_id` AS week
FROM
    `jb_lession_session`
    INNER JOIN `jb_exam_names` 
        ON (`jb_lession_session`.`exam_id` = `jb_exam_names`.`id`)
    LEFT JOIN `jb_teacher_lession_session` 
        ON (`jb_teacher_lession_session`.`lesson_id` = `jb_lession_session`.`id`
            AND `jb_teacher_lession_session`.`class_id` = $class
            AND `jb_teacher_lession_session`.`section_id` = $section
            ) WHERE 1";

        $sql .= " AND `jb_lession_session`.`branch_id` = $branch";
        $sql .= " AND `jb_lession_session`.`session_id` = $session";
        $sql .= " AND `jb_lession_session`.`subject_id` = $subject";

        //echo $sql;
        //exit;

        return $this->getResultsOrArr($sql);
    }
    public function getSyllabusSubjects($param = array())
    {
        $id = $param['id'] ?: 0;
        $branch = $param['branch'] ?: 0;
        $session = $param['session'] ?: 0;
        $class = $param['class'] ?: 0;
        $section = $param['section'] ?: 0;

        if (
            empty($id)
            || empty($session)
            || empty($branch)
            || empty($class)
            || empty($section)
        ) {
            return array();
        }
        $sql = "SELECT
    `jb_timetable_session_subjects`.`subject_id` AS `id`
    , `jb_subjects`.`title`
FROM
    `jb_timetable_session_subjects`
    INNER JOIN `jb_subjects` 
        ON (`jb_timetable_session_subjects`.`subject_id` = `jb_subjects`.`id`)
WHERE (`jb_timetable_session_subjects`.`branch_id`  = $branch
    AND `jb_timetable_session_subjects`.`session_id`  = $session
    AND `jb_timetable_session_subjects`.`class_id`  = $class
    AND `jb_timetable_session_subjects`.`section_id`  = $section)";

        $sql .= " GROUP BY `jb_timetable_session_subjects`.`subject_id`";

        echo $sql;
        exit;

        return $this->getResults($sql);
    }

    public function getTimeTable($param = array())
    {

        $id = $param['id'] ?: 0;
        $branch = $param['branch'] ?: 0;
        $session = $param['session'] ?: 0;
        $class = $param['class'] ?: 0;
        $section = $param['section'] ?: 0;

        if (
            empty($id)
            || empty($session)
            || empty($branch)
            || empty($class)
            || empty($section)
        ) {
            return array();
        }

        /*$sql = "SELECT
    `jb_timetable_for_session`.`id`
    , `jb_timetable_for_session`.`start_time`
    , `jb_timetable_for_session`.`end_time`
    , `jb_staffs`.`name` AS teacher_name
    , `jb_timetable_for_session`.`weekday_id`
    , `jb_timetable_for_session`.`period_name_id`
    , `jb_timetable_for_session`.`timetable_structure_id`
    , `jb_timetable_period_name`.`title` AS `period_name`
FROM
    `jb_timetable_for_session`
    INNER JOIN `jb_staffs`
        ON (`jb_timetable_for_session`.`teacher_id` = `jb_staffs`.`id`)
    INNER JOIN `jb_timetable_period_name`
        ON (`jb_timetable_for_session`.`period_name_id` = `jb_timetable_period_name`.`id`)
WHERE (`jb_timetable_for_session`.`branch_id`  = $branch
    AND `jb_timetable_for_session`.`session_id`  = $session
    AND `jb_timetable_for_session`.`class_id`  = $class
    AND `jb_timetable_for_session`.`section_id`  = $section)";*/



        $sql = "SELECT
    `jb_timetable_for_session`.`id`
    ,`jb_timetable_for_session`.`start_time`
    , `jb_timetable_for_session`.`end_time`
    , `jb_timetable_for_session`.`period_name_id`
    , `jb_timetable_for_session`.`weekday_id`
    , `jb_timetable_session_subjects`.`subject_id`
    , `jb_timetable_period_name`.`title` AS `period_name`
    , `jb_staffs`.`name` AS `teacher_name`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_timetable_for_session`
    INNER JOIN `jb_timetable_session_subjects` 
        ON (`jb_timetable_for_session`.`timetable_structure_id` = `jb_timetable_session_subjects`.`id`)
    INNER JOIN `jb_timetable_period_name` 
        ON (`jb_timetable_for_session`.`period_name_id` = `jb_timetable_period_name`.`id`)
    INNER JOIN `jb_staffs` 
        ON (`jb_timetable_for_session`.`teacher_id` = `jb_staffs`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_timetable_session_subjects`.`subject_id` = `jb_subjects`.`id`)
WHERE (`jb_timetable_for_session`.`branch_id`  = $branch
    AND `jb_timetable_for_session`.`session_id`  = $session
    AND `jb_timetable_for_session`.`class_id`  = $class
    AND `jb_timetable_for_session`.`section_id`  = $section)
GROUP BY `jb_timetable_for_session`.`period_name_id`, `jb_timetable_for_session`.`weekday_id`, `jb_timetable_session_subjects`.`subject_id`";



        return $this->getResults($sql);
    }

    public function getMonthlyTest($id, $session)
    {
        $sql = "SELECT
    SUM(`sub_number`) AS `subject_numbers`
    , SUM(`passing_marks`) AS `passing_marks`
    , SUM(`number`) AS `obtained_marks`
    , `date`
    , `id`
FROM
    `jb_monthly_test`
WHERE (`student_id`  = $id
    AND `session_id`  = $session)";

        $sql .= " GROUP BY MONTH(date)";
        $sql .= " ORDER BY date ASC";
        //echo $sql;
        //exit;
        return $this->getResults($sql);
    }

    public function getMonthlyTestSessions($id)
    {
        $sql = "SELECT
    `jb_monthly_test`.`session_id` AS `id`
    , `jb_sessions`.`title`
    , `jb_monthly_test`.`student_id`
FROM
    `jb_monthly_test`
    INNER JOIN `jb_sessions` 
        ON (`jb_monthly_test`.`session_id` = `jb_sessions`.`id`)
WHERE (`jb_monthly_test`.`student_id`  = $id)";

        $sql .= " GROUP BY `jb_monthly_test`.`session_id`";


        return $this->getResults($sql);
    }

    public function getStudentResults($id, $session, $exam)
    {
        $sql = "SELECT
    `jb_results`.`student_id`
    , `jb_results`.`subject_numbers`
    
    , `jb_results`.`numbers`
    , `jb_results`.`id`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_results`
    INNER JOIN `jb_subjects` 
        ON (`jb_results`.`subject_id` = `jb_subjects`.`id`) WHERE 1";

        $sql .= " AND `jb_results`.`student_id`  = $id";
        $sql .= " AND `jb_results`.`session_id`  = $session";
        $sql .= " AND `jb_results`.`exam_id`  = $exam";



        return $this->getResults($sql);
    }

    public function getExamSessions($id)
    {
        $sql = "SELECT
    `jb_results`.`exam_id`
    , `jb_results`.`session_id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_exam_names`.`title` AS `exam_title`
FROM
    `jb_results`
    INNER JOIN `jb_sessions` 
        ON (`jb_results`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_exam_names` 
        ON (`jb_results`.`exam_id` = `jb_exam_names`.`id`)
WHERE (`jb_results`.`student_id`  = $id)
GROUP BY `jb_results`.`exam_id`, `jb_results`.`session_id`";

        $sql .= " ORDER BY `jb_sessions`.`end_date`, `jb_exam_names`.`position` DESC";

        return $this->getResults($sql);
    }

    public function getLogs($id)
    {
        $sql = "SELECT
    `jb_notes`.`id`
     , `jb_notes`.`date`
     , `jb_notes`.`desc`
     , `jb_notesubcats`.`title`
     , `jb_notecats`.`title` AS note_cat
FROM
    `jb_notes`
    INNER JOIN `jb_notesubcats` 
        ON (`jb_notes`.`note_sub_cat_id` = `jb_notesubcats`.`id`)
    INNER JOIN `jb_notecats` 
        ON (`jb_notesubcats`.`note_cat_id` = `jb_notecats`.`id`)
WHERE (`jb_notes`.`student_id`  = $id)";


        $sql .= " LIMIT 250";

        //echo $sql;
        //exit;

        return $this->getResults($sql);
    }

    public function getAttendance($id, $date, $endDate)
    {
        $sql = "SELECT
    `student_id`
    , `date`
    , `attand`
FROM
    `jb_daily_attand` WHERE 1";

        $sql .= " AND student_id = $id";
        $sql .= " AND date BETWEEN '$date' AND '$endDate'";

        return $this->getResults($sql);
    }

    public function getProfile($id)
    {
        $sql = "SELECT
    `jb_students`.`id`
    , `jb_students`.`name`
    , `jb_students`.`fname` AS `father_name`
    , `jb_students`.`grnumber` AS gr_number
    
    , `jb_branches`.`title` AS `branch_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_student_parents`.`mother_name`
    , `jb_student_parents`.`mother_mobile`
    , `jb_student_parents`.`father_mobile`
    , `jb_student_parents`.`father_email`
    , `jb_student_profile`.`current_address`
FROM
    `jb_students`
    INNER JOIN `jb_branches` 
        ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_students`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sessions` 
        ON (`jb_students`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_students`.`section_id` = `jb_sections`.`id`)
    LEFT JOIN `jb_student_parents` 
        ON (`jb_student_parents`.`student_id` = `jb_students`.`id`)
    LEFT JOIN `jb_student_profile` 
        ON (`jb_student_profile`.`student_id` = `jb_students`.`id`) WHERE 1";

        $sql .= " AND `jb_students`.`id` = $id";
        $sql .= " LIMIT 1";



        return $this->getSingle($sql);
    }

    public function login($userName)
    {
        $userName = intval($userName);
        if(!is_numeric($userName)){
            return false;
        }
        $sql = "SELECT
    crd.`student_id`
    ,crd.`password`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`grnumber`
    
    , `jb_students`.`branch_id`
    , `jb_students`.`class_id`
    , `jb_students`.`section_id`
    , `jb_students`.`session_id`
FROM
    `jb_student_credentials` crd
    INNER JOIN `jb_students` 
        ON (crd.`student_id` = `jb_students`.`id`)
WHERE 1";

        $sql .= " AND crd.student_id = $userName";

        /*if($filterBy == "username"){
            $sql .= " AND crd.student_id = '$userName'";
        }

        if($filterBy == "id"){
            $sql .= " AND crd.student_id = '$userName'";
        }*/

        $sql .= " AND crd.published = 1 AND jb_students.student_status = 'current'";
        $sql .= " LIMIT 1";

        //echo $sql;

        return $this->getSingle($sql);
    }


    public function updateNotification($id)
    {
        $table = 'jb_notifications';
        $update = array("is_read" => 1);
        $where = array("id" => $id);
        return $this->update($table, $update, $where, 1);
    }


    protected function getTableName()
    {
    }
}
