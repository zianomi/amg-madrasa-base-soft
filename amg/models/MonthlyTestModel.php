<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 4/6/2017
 * Time: 12:01 AM
 */
require __DIR__ . DIRECTORY_SEPARATOR . "ExamModel.php";
class MonthlyTestModel extends ExamModel
{
    protected function getTableName(){
        $pr = $this->getPrefix();
        $table = $pr . "monthly_test";
        return $table;
    }

    /*public function testSubjects($class){
        $pr = $this->getPrefix();
        $sql = "SELECT *
        FROM
            `jb_subjects`
                WHERE 1 AND `jb_subjects`.`class_id` = $class
                AND `jb_subjects`.`subject_type` = 2
        ORDER BY `jb_subjects`.`position` ASC";

        $res = $this->getResults($sql);

        return $res;

    }*/

    public function testAssignedSubjects($class){
        $pr = $this->getPrefix();
        $sql = "SELECT
            *
        FROM
            `jb_monthly_test_subjects`
                WHERE 1 AND `jb_monthly_test_subjects`.`class_id` = $class";

        $res = $this->getResults($sql);

        return $res;
    }

    public function testSubjectInsert($class,$data = array()){
        $tableName = $this->getPrefix()."monthly_test_subjects";

        $subjectWhereColumn = "class_id";
        $whereSubject = array( $subjectWhereColumn => $class);
        if(!empty($class)){
            $this->delete( $tableName, $whereSubject );
        }

        $ins = $this->insertBulk($tableName,$data,false);


        return $ins;
    }

    public function monthlyTestSubjects($class){


        $sql = "SELECT
            *
        FROM
            `jb_monthly_test_subjects`
            
        WHERE 1 AND published = 1";

        $sql .= " AND class_id = $class";
        $sql .= " AND published = 1";
        $sql .= "
        ORDER BY `jb_monthly_test_subjects`.`position` ASC";

        $res = $this->getResults($sql);

        return $res;
    }

    public function getExamNumberSum($id,$start,$end){
        $sql = "SELECT SUM(`jb_results`.`numbers`) AS total_obtained
            , `jb_results`.`exam_id`
            , SUM(`jb_results`.`subject_numbers`) AS total_numbers
            , `jb_exam_names`.`title` AS exam_title
        FROM
            `jb_results`
            INNER JOIN `jb_subjects` 
                ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
            INNER JOIN `jb_exam_names` 
                ON (`jb_results`.`exam_id` = `jb_exam_names`.`id`)
        WHERE (`jb_results`.`student_id` = $id
           
            AND `jb_results`.`date` BETWEEN '$start' AND '$end')";

        $sql .= " GROUP BY `jb_results`.`exam_id`";

        return $this->getResults($sql);



    }

    public function insertClassNumbers($data = array())
    {

        $tableName = $this->getTableName();

        $columns = $this->getTableCols($tableName);
        unset($columns[0]);

        $ids = $this->insert_multi($tableName, $columns, $data,false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: " . $this->link->error);
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    /*public function insertSyllabus($data = array())
    {

        $tableName = "jb_hifz_student_syllabus";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data,false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }*/

    public function getIdTotal($id,$session){
        $sql = "SELECT
            SUM(`jb_subjects`.`numbers`) AS `subject_numbers`
            , SUM(`jb_monthly_test`.`number`) AS `obtain_numbers`
        FROM
            `jb_monthly_test`
            INNER JOIN `jb_subjects` 
                ON (`jb_monthly_test`.`subject_id` = `jb_subjects`.`id`)
        WHERE (`jb_monthly_test`.`student_id` =$id
            AND `jb_monthly_test`.`session_id` =$session)";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function IDReport($param = array()){

        $sql = "SELECT
                    `jb_monthly_test`.`number`
                    , `jb_monthly_test_subjects`.`title` subject_name
                    , `jb_monthly_test`.`sub_number` subject_numbers
                    , `jb_monthly_test`.`date`
                    , `jb_monthly_test`.`subject_id`
                FROM
                    `jb_monthly_test`
                    INNER JOIN `jb_monthly_test_subjects`
                        ON (`jb_monthly_test`.`subject_id` = `jb_monthly_test_subjects`.`id`) WHERE 1";

        $sql .= " AND jb_monthly_test.student_id = " . $param['student_id'];

        $sql .= " AND jb_monthly_test.date BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] ."'";

        if(!empty($param['session'])){
            $sql .= " AND jb_monthly_test.session_id = " . $param['session'];
        }


        //echo '<pre>'; print_r($sql); echo '</pre>';


        $res = $this->getResults($sql);

        return $res;
    }

    public function classReport($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_monthly_test`.`number`
            , `jb_monthly_test`.`date`
            , `jb_monthly_test`.`student_id` AS id
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_monthly_test`.`subject_id`
            , `jb_monthly_test_subjects`.`title` AS subject_name
            , `jb_monthly_test`.`sub_number` AS subject_number
        FROM
            `jb_monthly_test`
            INNER JOIN `jb_students`
                ON (`jb_monthly_test`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_monthly_test_subjects`
                ON (`jb_monthly_test`.`subject_id` = `jb_monthly_test_subjects`.`id`) WHERE 1";

        $sql .= " AND jb_monthly_test.date BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] ."'";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`session_id` = " . $param['session'];
        }

        //echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);

        return $res;
    }

    public function getCurrentMonthNumber($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT student_id, subject_id, number FROM jb_monthly_test WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`session_id` = " . $param['session'];
        }

        if (!empty($param['student'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`student_id` = " . $param['student'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "monthly_test`.`date` = '" . $param['date'] ."'";
        }

        //echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);

        $data = array();

        foreach ($res as $row){
            $data[$row['student_id']][$row['subject_id']] = $row['number'];
        }

        return $data;
    }

    /*public function getCurrentMonthSyllabus($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT required, current, student_id, date FROM jb_hifz_student_syllabus WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`date` = '" . $param['date'] ."'";
        }


        if (!empty($param['from_date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`date` BETWEEN '" . $param['from_date'] . "' AND '"  . $param['to_date'] ."'";
        }


        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`student_id` = " . $param['id'];
        }


        $res = $this->getResults($sql);

        $data = array();

        foreach ($res as $row){
            $data[$row['student_id']] = array("required" => $row['required'], "current" => $row['current']);
        }

        return $data;
    }*/

    public function getStudentSyllabus($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT required, current, student_id, date FROM jb_hifz_student_syllabus WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`date` = '" . $param['date'] ."'";
        }


        if (!empty($param['from_date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`date` BETWEEN '" . $param['from_date'] . "' AND '"  . $param['to_date'] ."'";
        }


        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "hifz_student_syllabus`.`student_id` = " . $param['id'];
        }


        $res = $this->getResults($sql);

        $data = array();

        foreach ($res as $row){
            $dateArr = explode("-",$row['date']);
            $data[$dateArr[0]][$dateArr[1]] = array("required" => $row['required'], "current" => $row['current']);
        }

        return $data;
    }

    public function deleteData($param = array()){
        $where = array( 'branch_id' => $param['branch'], 'class_id' => $param['class'], 'section_id' => $param['section'], 'session_id' => $param['session'], 'date' => $param['date']);
        //$this->delete( 'jb_hifz_student_syllabus', $where);
        if(!empty($param['branch'])){
            $this->delete( 'jb_monthly_test', $where);
        }

    }

    public function deleteIDData($param = array()){
        $where = array('id' => $param['id'], 'branch_id' => $param['branch'], 'class_id' => $param['class'], 'section_id' => $param['section'], 'session_id' => $param['session'], 'date' => $param['date']);
        //$this->delete( 'jb_hifz_student_syllabus', $where);
        if(!empty($param['branch']) && !empty($param['id'])){
            $this->delete( 'jb_monthly_test', $where);
        }

    }


    public function monthlyTestStudentDetail($param = array()){
        $sql = "SELECT
    `jb_monthly_test`.`student_id`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_monthly_test`.`branch_id`
    , `jb_monthly_test`.`class_id`
    , `jb_monthly_test`.`section_id`
    , `jb_monthly_test`.`session_id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
    , `jb_student_profile`.`block`
    , `jb_student_profile`.`date_of_birth`
FROM
    `jb_monthly_test`
    INNER JOIN `jb_branches` 
        ON (`jb_monthly_test`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_monthly_test`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_monthly_test`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_sessions` 
        ON (`jb_monthly_test`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_students` 
        ON (`jb_monthly_test`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_student_profile` 
        ON (`jb_student_profile`.`student_id` = `jb_monthly_test`.`student_id`) WHERE 1";


        if (!empty($param['start']) && !empty($param['end'])) {
            $start = $param['start'];
            $end = $param['end'];
            $sql .= " AND jb_monthly_test.date BETWEEN '$start' AND '$end'";
        }

        if (!empty($param['student'])) {
            $sql .= " AND jb_monthly_test.student_id = " . $param['student'];
        }

        $sql .= " LIMIT 1";

        $res = $this->getSingle($sql);

        return $res;

    }

}
