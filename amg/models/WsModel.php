<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 9/30/2017
 * Time: 9:34 AM
 */

class WsModel extends BaseModel
{

    protected function getTableName()
    {
    }

    public function GetCurrentSession(){
        $sql = "SELECT * FROM ".$this->getPrefix()."sessions WHERE 1 AND published = 1 ORDER BY id DESC";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function StudentData($id){
        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`fname`
            , `jb_students`.`gender`
            , `jb_students`.`grnumber`
            , `jb_students`.`branch_id`
            , `jb_students`.`class_id`
            , `jb_students`.`section_id`
            , `jb_students`.`session_id`
            , `jb_student_profile`.`date_of_birth`
            , `jb_student_profile`.`current_address`
            , `jb_student_parents`.`father_mobile`
            , `jb_student_parents`.`father_email`
            , `jb_student_parents`.`mother_name`
            , `jb_branches`.`title` AS `branch_title`
            , `jb_classes`.`title` AS `class_title`
            , `jb_sessions`.`title` AS `session_title`
            , `jb_sections`.`title` AS `section_title`
        FROM
            `jb_student_profile`
            INNER JOIN `jb_students` 
                ON (`jb_student_profile`.`student_id` = `jb_students`.`id`)
            LEFT JOIN `jb_student_parents` 
                ON (`jb_students`.`id` = `jb_student_parents`.`student_id`)
            INNER JOIN `jb_branches` 
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes` 
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sessions` 
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_sections` 
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
        WHERE (`jb_students`.`id` = $id)";

        $res = $this->getSingle($sql);
        return $res;
    }




}