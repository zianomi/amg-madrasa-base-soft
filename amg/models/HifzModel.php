<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 4/5/2017
 * Time: 10:18 AM
 */
class HifzModel extends BaseModel
{

    protected function getTableName(){}


    public function CheckCompleteStudent($param = array()){
        $sql = "SELECT * FROM `jb_hifz_completion` WHERE student_id = " . $param['id'];
        $row = $this->getSingle($sql);
        return $row;
    }

    public function hifzCompleteStudents($param = array()){
        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_students`.`doa`
            , `jb_students`.`branch_id`
            , `jb_students`.`class_id`
            , `jb_students`.`section_id`
            , `jb_students`.`session_id`
            , `jb_branches`.`title`
            , `jb_classes`.`title`
            , `jb_sections`.`title`
            , `jb_student_parents`.`amergency_mobile`
            , `jb_student_parents`.`home_fone`
            , `jb_student_profile`.`current_address`
            , `jb_student_profile`.`postcode`
            , `jb_student_profile`.`block`
            , `jb_student_profile`.`sreet`
            , `jb_student_profile`.`date_of_birth`
        FROM
            `jb_students`
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_student_profile`
                ON (`jb_students`.`id` = `jb_student_profile`.`student_id`)
            INNER JOIN `jb_student_parents`
                ON (`jb_students`.`id` = `jb_student_parents`.`student_id`)    
            WHERE 1   ";


        if(!empty($param['id'])){
            $sql .= " AND `jb_students`.`id` = " . $param['id'];
        }

        $row = $this->getSingle($sql);
        return $row;
    }


    public function insertProgress($data)
    {
        $pr = $this->getPrefix();
        $table = $pr . "hifz_completion";
        $columns = $this->getTableCols($table);



        $newData = array();

        $data = $this->setInsert($data);

        unset($columns[0]);

        //echo '<pre>'; print_r($columns); echo '</pre>';die('CALL');

        foreach($columns as $key => $val){
            $newData[$val] = $data[$val];
        }

        $this->insert($table, $this->setInsert($newData));
    }


    public function updateProgress($data,$id)
    {
        $pr = $this->getPrefix();
        $table = $pr . "hifz_completion";
        $columns = $this->getTableCols($table);

        $newData = array();

        foreach($columns as $key => $val){
            if($val == 'created_user_id' || $val == 'created'){
                continue;
            }
            $newData[$val] = $data[$val];
        }



        unset($newData['id']);


        $newData = $this->setUpdated($newData);

        $this->update($table, $newData,array("id" => $id));
    }


    function printProgress($id){

        $sql = "SELECT *, `jb_branches`.title AS branch_title
        FROM
            `jb_hifz_completion`
            INNER JOIN `jb_students`
                ON (`jb_hifz_completion`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_student_profile`
                ON (`jb_students`.`id` = `jb_student_profile`.`student_id`)
            INNER JOIN `jb_student_parents`
                ON (`jb_students`.`id` = `jb_student_parents`.`student_id`)
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`) WHERE  jb_hifz_completion.student_id = $id;";
        $row = $this->getSingle($sql);
        return $row;
    }

    public function HifzRanks(){
        $sql = "SELECT *
            FROM
                jb_hifz_ranks WHERE 1 = 1";

        $row = $this->getResults($sql);
        return $row;
    }

    public function quranStructure(){
        $sql = "SELECT * FROM jb_quran WHERE 1 = 1";

        $row = $this->getResults($sql);
        return $row;
    }

    public function insertHifzRecord($data)
    {
        $table = "jb_hifz_record";
        return $this->insert($table, $this->setInsert($data));
    }

    public function updateHifzRecord($data,$id){
        $pr = $this->getPrefix();
        $table = $pr . "hifz_record";
        $newData = $this->setUpdated($data);
        $this->update($table, $newData,array("id" => $id));
    }


    function viewIDHifzProgres($param = array()){
        $pr = $this->getPrefix();
        /*$sql = "SELECT h.`id`, `student_id`, `quran_id`, `date`, `start_date`, MAX(`end_date`) AS end_date,`q`.`title`
                         ,`q`.`total_days`
                         ,`q`.`lines_perday` FROM `jb_hifz_record` h
                         INNER JOIN `jb_quran` q
                     ON (h.`quran_id` = `q`.`id`)
                         WHERE 1";*/


        $sql = "SELECT
            `jb_hifz_record`.`student_id`
            , `jb_hifz_record`.`date`
            , MIN(`jb_hifz_record`.`start_date`) AS start_date
            , MAX(`jb_hifz_record`.`end_date`) AS end_date
            , `jb_hifz_record`.`page_number`
            , `jb_hifz_record`.`line_number`
            , `jb_quran`.`title`
            , `jb_hifz_statndards`.`required_pages`
            , `jb_hifz_statndards`.`required_lines`
            , `jb_hifz_statndards`.`required_lines_perday` AS lines_perday
              , `jb_hifz_statndards`.`total_days`
        FROM
            `jb_hifz_record`
            INNER JOIN `jb_hifz_statndards`
                ON (`jb_hifz_record`.`quran_id` = `jb_hifz_statndards`.`quran_id`)
                AND (`jb_hifz_record`.`hifz_year_id` = `jb_hifz_statndards`.`hifz_year_id`)
            INNER JOIN `jb_quran`
                ON (`jb_hifz_statndards`.`quran_id` = `jb_quran`.`id`) WHERE 1";



        if(!empty($param['id'])){
            $sql .= " AND jb_hifz_record.`student_id` = " . $param['id'];
        }



        $sql .= " GROUP BY jb_hifz_record.`quran_id`";


        $row = $this->getResults($sql);
        return $row;

    }

    public function insertSyllabus($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_students`.`branch_id`
            , `jb_students`.`class_id`
            , `jb_students`.`section_id`
            , `jb_students`.`session_id`
        FROM
            `jb_students` WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
        }

        $sql .= " AND `jb_students`.`id` NOT IN ";

        $sql .= "(";
        $sql .= " SELECT student_id FROM jb_hifz_stu_data WHERE date = '" . $param['date'] . "'";
        $sql .= ")";

        $row = $this->getResults($sql);
        return $row;
    }


    public function insertStuData($data = array()){

       $tableName = "jb_hifz_stu_data";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if(!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    function getCurrentMonthSyllabus($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_quran`.`id`
            , `jb_quran`.`title`
        FROM
            `jb_quran`
            INNER JOIN `jb_hifz_statndards`
                ON (`jb_quran`.`id` = `jb_hifz_statndards`.`quran_id`)
        WHERE  1 ";
        if(!empty($param['year'])){
            $sql .= " AND hifz_year_id = " . $param['year'];
        }
        if(!empty($param['class'])){
            $sql .= " AND class_id = " . $param['class'];
        }


        $sql .= " GROUP BY `jb_hifz_statndards`.`quran_id`";
        $row = $this->getResults($sql);
        return $row;
    }

    public function HifzStuData($param = array()){
        $sql = "SELECT
            `jb_hifz_stu_data`.`page_number`
            , `jb_hifz_stu_data`.`line_number`
            , `jb_hifz_stu_data`.`date`
            , `jb_quran`.`title` AS para
            , `jb_hifz_years`.`title` AS hifz_year
        FROM
            `jb_hifz_stu_data`
            INNER JOIN `jb_quran`
                ON (`jb_hifz_stu_data`.`quran_id` = `jb_quran`.`id`)
            INNER JOIN `jb_hifz_years`
                ON (`jb_hifz_stu_data`.`hifz_year_id` = `jb_hifz_years`.`id`) WHERE 1 ";

        if(!empty($param['id'])){
            $sql .= " AND `jb_hifz_stu_data`.`student_id` = " . $param['id'];
        }
        if(!empty($param['start']) && !empty($param['end'])){
            $start = $param['start'];
            $end = $param['end'];
            $sql .= " AND `jb_hifz_stu_data`.`date` BETWEEN '$start' AND '$end'";
        }

        //echo '<pre>';print_r($sql );echo '</pre>';

        $sql .= " GROUP BY YEAR(`jb_hifz_stu_data`.`date`), MONTH(`jb_hifz_stu_data`.`date`)";

        $row = $this->getResults($sql);
        return $row;
    }


    function HifzKefyatBetween($number) {

        $number = floor($number);

        if (($number) >= 9500){
            $htm =  'بہترین';
        }
        elseif(($number) < 9500 && ($number) >= 9000){
            $htm = 'بہتر';
        }
        elseif($number >= 8500 && $number < 9000){
            $htm = 'مناسب';
        }
        elseif(($number) < 8500 && ($number) >= 8000){
            $htm = 'اوسط';
        }
        elseif(($number) < 8000 && ($number) >= 7500){
            $htm = 'اوسط';
        }

        elseif(($number) < 7500){
            $htm = 'اوسط';
        }

        return $htm;

    }


    public function getCompletionReport($param = array()){
        $sql = "SELECT
    `jb_hifz_completion`.`student_id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_hifz_completion`.`start_date_hifz`
    , `jb_hifz_completion`.`end_date_hifz`
    , `jb_zones`.`title` AS `zone_title`
FROM
    `jb_hifz_completion`
    INNER JOIN `jb_students` 
        ON (`jb_hifz_completion`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_hifz_completion`.`branch` = `jb_branches`.`id`)
    INNER JOIN `jb_zones` 
        ON (`jb_branches`.`zone_id` = `jb_zones`.`id`) WHERE 1";

        if(!empty($param['branch'])){
            $sql .= " AND `jb_hifz_completion`.`branch` = " . $param['branch'];
        }

        if(!empty($param['zone'])){
            $sql .= " AND `jb_zones`.`id` = " . $param['zone'];
        }

        if(!empty($param['date']) && !empty($param['to_date'])){
            $sql .= " AND `jb_hifz_completion`.`end_date_hifz` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        $row = $this->getResults($sql);
        return $row;
    }




}
