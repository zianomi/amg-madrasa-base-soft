<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 2/2/2018
 * Time: 7:36 PM
 */
class ProfileModel extends BaseModel{

    protected function getTableName()
    {
    }

    public function GetNotes($id){
        $sql = "SELECT
            `jb_notes`.`date`
            , `jb_notes`.`desc`
            , `jb_notesubcats`.`title` AS sub_cat_title
            , `jb_classes`.`title` AS class_title
            , `jb_classes`.`id` AS class_id
            , `jb_branches`.`title` AS branch_title
            , `jb_notecats`.`id`
        FROM
            `jb_notes`
            INNER JOIN `jb_classes`
                ON (`jb_notes`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_notes`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_notesubcats`
                ON (`jb_notes`.`note_sub_cat_id` = `jb_notesubcats`.`id`)
            INNER JOIN `jb_notecats`
                ON (`jb_notesubcats`.`note_cat_id` = `jb_notecats`.`id`) WHERE student_id = $id ORDER BY date ASC";


        $res = $this->getResults($sql);
       return $res;
    }

    public function GetExam($id){
        $sql = "SELECT
            `jb_subjects`.`title` AS subject_title
            , `jb_results`.`subject_numbers` AS subject_numbers
            , `jb_results`.`id` AS result_table_id
            , `jb_results`.`branch_id`
            , `jb_subjects`.`id` AS subject_id
            , `jb_branches`.`title` AS branch_title
            , `jb_classes`.`title` AS class_title
            , `jb_classes`.`id` AS class_id
            , `jb_results`.`numbers`
            , `jb_results`.`date`
            , `jb_results`.`session_id`
            , `jb_exam_names`.`id` AS exam_id
            , `jb_exam_names`.`title` AS exam_title
            , `jb_sections`.`id` AS section_id
            , `jb_sections`.`title` AS section_title
        FROM
            `jb_results`
            INNER JOIN `jb_subjects`
                ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_results`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_results`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_exam_names`
                ON (`jb_results`.`exam_id` = `jb_exam_names`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_results`.`section_id` = `jb_sections`.`id`)
            WHERE jb_results.student_id = $id ORDER BY `jb_results`.`exam_id`, date ASC";



        $res = $this->getResults($sql);
       return $res;
    }

    public function GetAttand($id){
        $sql = "SELECT
          `jb_attand`.`total_attandance`
          , `jb_attand`.`total_absent`
          , `jb_attand`.`total_leave`
          , `jb_attand`.`date`
          , `jb_classes`.`id` AS class_id
          , `jb_classes`.`title` AS class_title
          , `jb_branches`.`title` AS branch_title
          , `jb_sections`.`title` AS section_title
          , `jb_sessions`.`title` AS session_title

      FROM
          `jb_attand`
          INNER JOIN `jb_classes`
              ON (`jb_attand`.`class_id` = `jb_classes`.`id`)
          INNER JOIN `jb_branches`
              ON (`jb_attand`.`branch_id` = `jb_branches`.`id`)
          INNER JOIN `jb_sections`
            ON (`jb_attand`.`section_id` = `jb_sections`.`id`)
          INNER JOIN `jb_sessions`
            ON (`jb_attand`.`session_id` = `jb_sessions`.`id`)
          WHERE student_id = $id ORDER BY date ASC";

       // echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);
       return $res;

    }
}
