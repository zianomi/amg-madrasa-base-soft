<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 2/13/2017
 * Time: 11:50 AM
 */
class NotesModel extends BaseModel
{


    protected function getTableName()
   {
       return $this->getPrefix() . "notes";
   }




    public function insertNote($data)
    {
        $table = $this->getTableName();
        $this->insert($table, $this->setInsert($data));
    }


    public function NotesReport($param = array())
    {
        $pr = $this->getPrefix();

        $sql = "SELECT
        `" . $pr . "students`.`id`
        , `" . $pr . "students`.`name`
        , `" . $pr . "students`.`fname`
        , `" . $pr . "branches`.`title` AS `branch_title`
        , `" . $pr . "classes`.`title` AS `class_title`
        ,  `" . $pr . "sections`.`title` AS `section_title`
        ,  `" . $pr . "sessions`.`title` AS `session_title`
        , `" . $pr . "notes`.`id` AS note_id
        , `" . $pr . "notes`.`date`
        , `" . $pr . "notes`.`desc`
        , `" . $pr . "notesubcats`.`title` AS `cat`
        , `" . $pr . "notecats`.`title` AS `sub_cat`
    FROM
        `" . $pr . "notes`
        INNER JOIN `" . $pr . "students`
            ON (`" . $pr . "students`.`id` = `" . $pr . "notes`.`student_id`)
        INNER JOIN `" . $pr . "notesubcats`
            ON (`" . $pr . "notes`.`note_sub_cat_id` = `" . $pr . "notesubcats`.`id`)
        INNER JOIN `" . $pr . "branches`
            ON (`" . $pr . "branches`.`id` = `" . $pr . "notes`.`branch_id`)
        INNER JOIN `" . $pr . "classes`
            ON (`" . $pr . "classes`.`id` = `" . $pr . "notes`.`class_id`)
        INNER JOIN `" . $pr . "sections`
            ON (`" . $pr . "students`.`section_id` = `" . $pr . "sections`.`id`)
        INNER JOIN `" . $pr . "sessions`
            ON (`" . $pr . "sessions`.`id` = `" . $pr . "students`.`session_id`)
        INNER JOIN `" . $pr . "notecats`
            ON (`" . $pr . "notesubcats`.`note_cat_id` = `" . $pr . "notecats`.`id`) WHERE 1 ";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "notes`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "notes`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "notes`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "notes`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "notes`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        if (!empty($param['cat'])) {
            $sql .= " AND `" . $pr . "notecats`.`id` = " . $param['cat'];
        }

        if (!empty($param['sub_cat'])) {
            $sql .= " AND `" . $pr . "notes`.`note_sub_cat` = " . $param['sub_cat'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "notes`.`student_id` = " . $param['id'];
        }

        if (!empty($param['today'])) {
            $sql .= " AND date(`created`) = '" . date("Y-m-d") . "'";
        }



        $res = $this->getResults($sql);

        return $res;

    }


    function MoreNotes($param = array()){
        $pr = $this->getPrefix();

        $sql = "SELECT

            `".$pr."students`.`id`
            , `".$pr."students`.`name`
            , `".$pr."students`.`fname`
            , `" . $pr . "branches`.`title` AS `branch_title`
            , `" . $pr . "classes`.`title` AS `class_title`
            ,  `" . $pr . "sections`.`title` AS `section_title`
            ,  `" . $pr . "sessions`.`title` AS `session_title`
            , `" . $pr . "notes`.`id` AS `note_id`
            ,COUNT(`".$pr."notes`.`id`) AS counts

        FROM
            `".$pr."students`
            INNER JOIN `".$pr."notes`
                ON (`".$pr."students`.`id` = `".$pr."notes`.`student_id`)
            INNER JOIN `".$pr."notesubcats`
                ON (`".$pr."notes`.`note_sub_cat_id` = `".$pr."notesubcats`.`id`)
            INNER JOIN `".$pr."branches`
                ON (`".$pr."branches`.`id` = `".$pr."notes`.`branch_id`)
            INNER JOIN `".$pr."classes`
                ON (`".$pr."classes`.`id` = `".$pr."notes`.`class_id`)
            INNER JOIN `" . $pr . "sections`
                ON (`" . $pr . "students`.`section_id` = `" . $pr . "sections`.`id`)
            INNER JOIN `" . $pr . "sessions`
                ON (`" . $pr . "sessions`.`id` = `" . $pr . "students`.`session_id`)
            INNER JOIN `".$pr."notecats`
                ON (`".$pr."notesubcats`.`note_cat_id` = `".$pr."notecats`.`id`) WHERE 1 = 1 ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "notes`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "notes`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "notes`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "notes`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "notes`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        if (!empty($param['cat'])) {
            $sql .= " AND `" . $pr . "notecats`.`id` = " . $param['cat'];
        }

        if (!empty($param['sub_cat'])) {
            $sql .= " AND `" . $pr . "notes`.`note_sub_cat` = " . $param['sub_cat'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "notes`.`student_id` = " . $param['id'];
        }


        if(!empty($param['count'])){
            $sql .= " GROUP BY `".$pr."notes`.`student_id` HAVING counts >= " . $param['count'];
        }

        $res = $this->getResults($sql);

        return $res;

    }


    public function removeNote($id){
        $whereColumn = "id";
        $table = $this->getTableName();

        $where = array( $whereColumn => $id);

        if(!empty($id)){
            $this->delete( $table, $where, 1 );
        }

    }



}