<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 3/24/2017
 * Time: 3:49 PM
 */
class StudentsModel extends BaseModel
{

    protected function getTableName()
    {
    }

    public function classTypeIds($key)
    {
        $types = array();
        $types['completed'] = 10;
        $types['terminated'] = 11;

        return $types[$key];
    }

    public function StudentdSearchWithProfile($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "
    SELECT
        `" . $pr . "students`.`id`
        ,`" . $pr . "students`.`grnumber`
         , `" . $pr . "students`.`name`
        , `" . $pr . "students`.`fname`
        , `" . $pr . "students`.`gender`
        , `" . $pr . "students`.`student_status`
        , `" . $pr . "branches`.`title` AS `branch_title`
        , `" . $pr . "classes`.`title` AS `class_title`
        ,  `" . $pr . "sections`.`title` AS `section_title`
        ,  `" . $pr . "sessions`.`title` AS `session_title`
        ";
        if (!empty($param['fields'])) {
            $sql .= " " . $param['fields'];
        }

        $sql .= "
    FROM
        `" . $pr . "students`
        LEFT JOIN `" . $pr . "student_profile`
            ON (`" . $pr . "student_profile`.`student_id` = `" . $pr . "students`.`id`)
        LEFT JOIN `" . $pr . "student_parents`
            ON (`" . $pr . "student_parents`.`student_id` = `" . $pr . "students`.`id`)
        INNER JOIN `" . $pr . "classes`
            ON (`" . $pr . "students`.`class_id` = `" . $pr . "classes`.`id`)
        INNER JOIN `" . $pr . "branches`
            ON (`" . $pr . "students`.`branch_id` = `" . $pr . "branches`.`id`)
        INNER JOIN `" . $pr . "sections`
                ON (`" . $pr . "students`.`section_id` = `" . $pr . "sections`.`id`)
        INNER JOIN `" . $pr . "sessions`
                ON (`" . $pr . "sessions`.`id` = `" . $pr . "students`.`session_id`)
        
        WHERE 1
    ";


        //INNER JOIN `" . $pr . "login_user_branches`
        //                ON (`" . $pr . "students`.`branch_id` = `" . $pr . "login_user_branches`.`branch_id`)
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
            if (is_numeric($param['id'])) {
                $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
            } else {
                $sql .= " AND `" . $pr . "students`.`name` LIKE '%" . $param['id'] . "%'";
            }
        }

        if (!empty($param['gr'])) {
            $sql .= " AND `" . $pr . "students`.`grnumber` = '" . $param['gr'] . "'";
        }

        if (!empty($param['key_word'])) {
            $sql .= " AND " . str_replace(",", "", $param['fields']) . " LIKE '%" . $param['key_word'] . "%'";
        }

        $sql .= " AND  ( `" . $pr . "students`.`student_status` = 'current' OR `" . $pr . "students`.`student_status` = 'blocked')";
        //$sql .= " AND `" . $pr . "login_user_branches`.`user_id` = " . $this->getUserId();


        //echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);

        return $res;


    }


    public function StudentdCustomSearch($param = array(), $cols = array())
    {

        $pr = $this->getPrefix();
        $sql = "
       SELECT `" . $pr . "students`.`id` AS student_id
               , `" . $pr . "students`.`fname`
               , `" . $pr . "students`.`name`
               , `" . $pr . "branches`.`title` AS `branch_title`
               , `" . $pr . "classes`.`title` AS `class_title`
               ,  `" . $pr . "sections`.`title` AS `section_title`
              ";

        if (!empty($param['key_word']) && !empty($param['fields'])) {
            if (in_array($param['fields'], ($cols))) {
                $sql .= "," . $param['fields'];
            }
        }


        $sql .= "
       FROM
           `" . $pr . "students`
           LEFT JOIN `" . $pr . "student_profile`
            ON (`" . $pr . "student_profile`.`student_id` = `" . $pr . "students`.`id`)
        LEFT JOIN `" . $pr . "student_parents`
            ON (`" . $pr . "student_parents`.`id` = `" . $pr . "student_profile`.`parents_id`)
        INNER JOIN `" . $pr . "classes`
            ON (`" . $pr . "students`.`class_id` = `" . $pr . "classes`.`id`)
        INNER JOIN `" . $pr . "branches`
            ON (`" . $pr . "students`.`branch_id` = `" . $pr . "branches`.`id`)
        INNER JOIN `" . $pr . "sections`
                ON (`" . $pr . "students`.`section_id` = `" . $pr . "sections`.`id`)
        INNER JOIN `" . $pr . "sessions`
                ON (`" . $pr . "sessions`.`id` = `" . $pr . "students`.`session_id`)
        INNER JOIN `" . $pr . "login_user_branches`
                ON (`" . $pr . "students`.`branch_id` = `" . $pr . "login_user_branches`.`branch_id`)
           WHERE 1
       ";

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



        if (!empty($param['key_word']) && !empty($param['fields'])) {

            if (in_array($param['fields'], ($cols))) {

                $sql .= " AND " . $param['fields'] . " LIKE '%" . $param['key_word'] . "%'";
            }

        }

        if (!empty($param['admission_date'])) {
            $sql .= " AND `" . $pr . "students`.`created` > '" . $param['admission_date'] . "'";
        }


        $sql .= " ORDER BY `" . $pr . "classes`.`position`, `" . $pr . "sections`.`position` ASC";


        $res = $this->getResults($sql);

        return $res;


    }


    public function SelectStudenProfiletById($student_id)
    {

        $sql = "SELECT *, `jb_student_profile`.`id` AS profile_id, `jb_student_parents`.`id` AS parents_id
    FROM
        `jb_students`
        INNER JOIN `jb_student_profile`
            ON (`jb_students`.`id` = `jb_student_profile`.`student_id`)
        INNER JOIN `jb_student_parents`
            ON (`jb_student_parents`.`student_id` = `jb_students`.`id`)
    WHERE jb_students.id = $student_id";





        $res = $this->getSingle($sql);

        return $res;
    }


    public function newAdmissions()
    {

        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_branches`.`title` AS branch_title
            , `jb_classes`.`title` AS class_title
            , `jb_sections`.`title` AS section_title
        FROM
            `jb_students`
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`) WHERE 1 ORDER BY `jb_students`.`id` DESC";

        $sql .= " LIMIT 0,100";

        $res = $this->getResults($sql);

        return $res;
    }


    public function getClassIdByType($type)
    {
        $sql = "SELECT id FROM jb_classes WHERE class_type = '$type' LIMIT 1";
        $res = $this->getSingle($sql);
        return $res['id'];
    }

    public function insertTerminated($data)
    {
        $id = $data['student_id'];
        if (!empty($id)) {
            $classId = $this->getClassIdByType($this->stuStatus("terminated"));
            $table = $this->getPrefix() . "terminated";
            $stuTable = $this->getPrefix() . "students";
            $this->insert($table, $this->setInsert($this->filter($data)));
            $update = array('student_status' => $this->stuStatus("terminated"), "class_id" => $classId);
            $update_where = array('id' => $id);
            $this->update($stuTable, $update, $update_where, 1);
        }
    }

    public function getTerminatedStudents($param = array())
    {
        $pr = $this->getPrefix();
        $teminatedTable = $pr . "terminated";
        $studentTable = $pr . "students";
        $sql = "SELECT $studentTable.id ";
        $sql .= ",$studentTable.grnumber ";
        $sql .= ",$studentTable.name ";
        $sql .= ",$studentTable.fname ";
        $sql .= ",$teminatedTable.date ";
        $sql .= ",$teminatedTable.reason ";
        $sql .= ",jb_branches.title AS branch_titles ";

        $sql .= " FROM $teminatedTable INNER JOIN " . $pr . "students ON $teminatedTable.student_id = $studentTable.id";
        $sql .= " INNER JOIN " . $pr . "branches ON $studentTable.branch_id = jb_branches.id";
        if (!empty($param['id'])) {
            $sql .= " AND $teminatedTable.student_id = " . $param['id'];
        }
        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND $teminatedTable.date BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        if (!empty($param['branch'])) {
            $sql .= " AND $studentTable.branch_id = " . $param['branch'];
        }


        $sql = "SELECT
    `jb_terminated`.`date`
    , `jb_terminated`.`reason`
    , `jb_students`.`id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`grnumber`
    , `jb_branches`.`title` AS `branch_titles`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_sessions`.`title` AS `session_title`
FROM
    `jb_terminated`
    INNER JOIN `jb_students` 
        ON (`jb_terminated`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_terminated`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_terminated`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_terminated`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_sessions` 
        ON (`jb_terminated`.`session_id` = `jb_sessions`.`id`) WHERE 1";


        if (!empty($param['id'])) {
            $sql .= " AND jb_terminated.student_id = " . $param['id'];
        }
        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND jb_terminated.date BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        if (!empty($param['branch'])) {
            $sql .= " AND jb_terminated.branch_id = " . $param['branch'];
        }


        $res = $this->getResults($sql);
        return $res;
    }


    public function getCompletedStudents($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT ";
        $sql .= "jb_students.id ";
        $sql .= ",jb_students.name ";
        $sql .= ",jb_students.fname ";
        $sql .= ",jb_completed.date ";
        $sql .= ",jb_completed.note ";
        $sql .= ",jb_completed.roll_number ";
        $sql .= ",jb_completed.grade ";
        $sql .= ",jb_completed.numbers ";
        $sql .= ",jb_completed.certificate_number ";
        $sql .= ",jb_branches.title AS branch_title ";
        $sql .= " FROM " . $pr . "completed INNER JOIN " . $pr . "students ON student_id = " . $pr . "students.id";
        $sql .= " INNER JOIN jb_branches ON jb_students.branch_id = jb_branches.id ";
        $sql .= " WHERE 1 ";


        $sql = "SELECT
    `jb_students`.`id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_completed`.`date`
    , `jb_completed`.`note`
    , `jb_completed`.`roll_number`
    , `jb_completed`.`grade`
    , `jb_completed`.`numbers`
    , `jb_completed`.`certificate_number`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_sessions`.`title` AS `session_title`
FROM
    `jb_completed`
    INNER JOIN `jb_students` 
        ON (`jb_completed`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_completed`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_completed`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_completed`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_sessions` 
        ON (`jb_completed`.`session_id` = `jb_sessions`.`id`) WHERE 1";

        if (!empty($param['id'])) {
            $sql .= " AND jb_completed.student_id = " . $param['id'];
        }
        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND jb_completed.date BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        if (!empty($param['branch'])) {
            $sql .= " AND jb_completed.branch_id = " . $param['branch'];
        }

        $res = $this->getResults($sql);
        return $res;
    }


    public function insertCompleted($data)
    {
        $id = $data['student_id'];
        if (!empty($id)) {
            $classId = $this->getClassIdByType($this->stuStatus("completed"));
            $table = $this->getPrefix() . "completed";
            $stuTable = $this->getPrefix() . "students";
            $this->insert($table, $this->setInsert($data));
            $update = array('student_status' => $this->stuStatus("completed"), "class_id" => $classId);
            $update_where = array('id' => $id);
            $this->update($stuTable, $update, $update_where, 1);
        }
    }

    public function checkReturn($type, $id)
    {
        if ($type == $this->stuStatus("completed")) {
            $table = $this->getPrefix() . "completed";
        } else {
            $table = $this->getPrefix() . "terminated";
        }
        $sql = "SELECT * FROM " . $table . " WHERE student_id = $id";

        $res = $this->getSingle($sql);
        return $res;
    }

    public function insertReturn($data, $type, $dataId)
    {
        $table = $this->getPrefix() . "returntudent_log";
        $this->insert($table, $this->setInsert($data));
        if ($type == $this->stuStatus("completed")) {
            $tableStudent = $this->getPrefix() . "completed";
        } else {
            $tableStudent = $this->getPrefix() . "terminated";
        }
        $where = array("id" => $dataId);
        if (!empty($dataId)) {
            $this->delete($tableStudent, $where, 1);
        }

    }



    public function updateStudentStatus($id)
    {


        $sql = "UPDATE `jb_students` s
        	INNER JOIN `jb_classes` c
        		ON s.`class_id` = c.`id`
        SET s.`student_status` = c.class_type
        WHERE s.id = $id AND s.`class_id` = c.`id`;";

        return $this->query($sql);
    }

    public function inserTransfers($vals)
    {
        $tableName = $this->getPrefix() . "transfer_history";
        $columns = $this->getTableCols($tableName);
        $this->insert_multi($tableName, $columns, $vals, false);
    }

    public function returnTransferLog($id, $branch, $class, $section, $session)
    {
        $sql = "SELECT * FROM jb_transfer_history WHERE student_id = $id LIMIT 1";
        $res = $this->getSingle($sql);
        $paramTransfer['student_id'] = $id;
        $paramTransfer['old_branch'] = $res['current_branch'];
        $paramTransfer['old_class'] = $res['current_class'];
        $paramTransfer['old_section'] = $res['current_section'];
        $paramTransfer['old_session'] = $res['current_session'];
        $paramTransfer['current_branch'] = $branch;
        $paramTransfer['current_class'] = $class;
        $paramTransfer['current_section'] = $section;
        $paramTransfer['current_session'] = $session;
        $this->logTransferDetail($paramTransfer);
    }


    public function logTransferDetail($param = array())
    {

        $data = array();
        $date = date("Y-m-d");
        $table = $this->getPrefix() . "transfer_history";
        $old_branch = $param['old_branch'];
        $old_class = $param['old_class'];
        $old_section = $param['old_section'];
        $old_session = $param['old_session'];
        $current_branch = $param['current_branch'];
        $current_class = $param['current_class'];
        $current_section = $param['current_section'];
        $current_session = $param['current_session'];

        $data['student_id'] = $param['student_id'];
        $data['old_branch'] = $old_branch;
        $data['old_class'] = $old_class;
        $data['old_section'] = $old_section;
        $data['old_session'] = $old_session;
        $data['current_branch'] = $current_branch;
        $data['current_class'] = $current_class;
        $data['current_section'] = $current_section;
        $data['current_session'] = $current_session;
        $data['date'] = $date;


        if (

            $data['old_branch'] != $data['current_branch']
            || $data['old_class'] != $data['current_class']
            || $data['old_section'] != $data['current_section']
            || $data['old_session'] != $data['current_session']

        ) {
            if ($this->insert($table, $this->setInsert($data))) {
                return true;
            }
        }


        return false;
    }


    public function transferStudents($branch, $class, $section, $session, $transfer_ids)
    {
        $table = $this->getPrefix() . "students";
        $sql = "UPDATE `" . $table . "` SET `branch_id` = " . $branch . ", `class_id` = " . $class . ", `section_id` = " . $section . ", `session_id` = " . $session . "
             WHERE `" . $table . "`.`id` IN ($transfer_ids)";
        $res = $this->query($sql);
        return $res;
    }


    public function TransferHistory($param = array())
    {

        $sql = "
        SELECT
            `jb_transfer_history`.`date`
            , `so`.`id`
            , `so`.`name`
            , `so`.`gender`
            , `so`.`fname`
            , `so`.`grnumber`
            , `so`.`branch_id`
            , `bo`.`title` bo_title
            , `co`.`title` co_title
            , `sco`.`title` sco_title
            , `bc`.`title` bc_title
            , `cc`.`title` cc_title
            , `sc`.`title` sc_title
        FROM
            `jb_transfer_history`
            INNER JOIN `jb_students` so
                ON (`jb_transfer_history`.`student_id` = `so`.`id`)
            INNER JOIN `jb_branches` bo
                ON (`jb_transfer_history`.`old_branch` = `bo`.`id`)
            INNER JOIN `jb_classes` co
                ON (`jb_transfer_history`.`old_class` = `co`.`id`)
            INNER JOIN `jb_sections` sco
                ON (`jb_transfer_history`.`old_section` = `sco`.`id`)
            INNER JOIN `jb_branches` bc
                    ON (`jb_transfer_history`.`current_branch` = `bc`.`id`)
            INNER JOIN `jb_classes` cc
                ON (`jb_transfer_history`.`current_class` = `cc`.`id`)
            INNER JOIN `jb_sections` sc
                ON (`jb_transfer_history`.`current_section` = `sc`.`id`)
        ";

        if (!empty($param['id'])) {
            $sql .= " AND `jb_transfer_history`.`student_id` = " . $param['id'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `jb_transfer_history`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }



        $res = $this->getResults($sql);

        return $res;
    }

    public function studentSearch($param = array())
    {
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
            , `jb_branches`.`title` branch_title
            , `jb_classes`.`title` class_title
            , `jb_sections`.`title` section_title
            , `jb_sessions`.`title` session_title
        FROM
            `jb_students`
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`) WHERE 1 ";


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

        if (!empty($param['status'])) {
            $sql .= " AND `" . $pr . "students`.`student_status` = '" . $param['status'] . "'";
        }

        if (!empty($param['gr'])) {
            $sql .= " AND `" . $pr . "students`.`grnumber` = '" . $param['gr'] . "'";
        }


        if (!empty($param['id'])) {
            if (is_numeric($param['id'])) {
                $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
            } else {
                $sql .= " AND `" . $pr . "students`.`name` LIKE '%" . $param['id'] . "%'";
            }
        }



        $res = $this->getResults($sql);

        return $res;
    }



    public function allStudentSearch($param = array())
    {
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
            , `jb_branches`.`title` branch_title
            , `jb_classes`.`title` class_title
            , `jb_sections`.`title` section_title
            , `jb_sessions`.`title` session_title
        FROM
            `jb_students`
            LEFT JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            LEFT JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            LEFT JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            LEFT JOIN `jb_sessions`
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`)";

        $sql .= " WHERE 1";
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

        if (!empty($param['status'])) {
            $sql .= " AND `" . $pr . "students`.`student_status` = '" . $param['status'] . "'";
        }

        if (!empty($param['gr'])) {
            $sql .= " AND `" . $pr . "students`.`grnumber` = '" . $param['gr'] . "'";
        }


        if (!empty($param['id'])) {
            if (is_numeric($param['id'])) {
                $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
            } else {
                $sql .= " AND `" . $pr . "students`.`name` LIKE '%" . $param['id'] . "%'";
            }
        }





        $res = $this->getResults($sql);

        return $res;
    }



    public function studentAllCols($param = array())
    {
        $pr = $this->getPrefix();

        if (!empty($param["fields"])) {
            $fields = implode(",", $param["fields"]);
        } else {
            $fields = "*";
        }
        $sql = "SELECT $fields
        FROM
            `jb_students`
            INNER JOIN `jb_student_profile`
                ON (`jb_students`.`id` = `jb_student_profile`.`student_id`)
            INNER JOIN `jb_student_parents`
                ON (`jb_students`.`id` = `jb_student_parents`.`student_id`)
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`) WHERE 1 ";


        if (!empty($param['id'])) {
            if (is_numeric($param['id'])) {
                $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
            } else {
                $sql .= " AND `" . $pr . "students`.`name` LIKE '%" . $param['id'] . "%'";
            }
        }
        $res = $this->getResults($sql);



        return $res;
    }

    public function studentCount($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            COUNT(`jb_students`.`id`) AS tot
        FROM
            `jb_students`
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`)";


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

        if (!empty($param['status'])) {
            $sql .= " AND `" . $pr . "students`.`student_status` = '" . $param['status'] . "'";
        }


        if (!empty($param['id'])) {
            if (is_numeric($param['id'])) {
                $sql .= " AND `" . $pr . "students`.`id` = " . $param['id'];
            } else {
                $sql .= " AND `" . $pr . "students`.`name` LIKE '%" . $param['id'] . "%'";
            }
        }

        $res = $this->getSingle($sql);

        return $res['tot'];
    }


    public function checkparents($param = array())
    {

        $pr = $this->getPrefix();
        $sql = "SELECT
        `" . $pr . "students`.`fname`
        , `" . $pr . "student_parents`.`id`
        ";

        $sql .= "
    FROM
        `" . $pr . "students`
        INNER JOIN " . $pr . "student_profile
                ON jb_student_profile.student_id = jb_students.id
        INNER JOIN " . $pr . "student_parents
                ON jb_student_profile.parents_id = jb_student_parents.id
        WHERE 1 ";

        if (!empty($param['father_name'])) {
            $sql .= " AND `" . $pr . "students`.`fname` = '" . $param['father_name'] . "'";
        }

        if (!empty($param['father_nic'])) {
            $sql .= " AND `" . $pr . "student_parents`.`father_nic` = " . $param['father_nic'];
        }

        if (!empty($param['father_mobile'])) {
            $sql .= " AND `" . $pr . "student_parents`.`father_mobile` = " . $param['father_mobile'];
        }
        $res = $this->getSingle($sql);
        return $res;
    }


    public function prepareStudentData($data)
    {
        $table = "jb_students";
        $columns = $this->getTableCols($table);

        $ins = array();
        $ins['branch_id'] = $_POST['branch'];
        $ins['session_id'] = $_POST['session'];
        $ins['class_id'] = $_POST['class'];
        $ins['section_id'] = $_POST['section'];
        $ins['doa'] = $_POST['doa'];
        $ins['student_status'] = $this->stuStatus("current");
        foreach ($columns as $key) {
            if (isset($data[$key])) {
                $ins[$key] = $data[$key];
            }
        }
        $ins = $this->sortArrayByArray($ins, $columns);

        return $ins;
    }




    public function addStudents($data)
    {
        $table = "jb_students";
        $ins = $this->prepareStudentData($data);
        $this->insert($table, $this->setInsert($ins));
        return $this->lastid();
    }



    public function prepareProfileData($data)
    {
        $table = "jb_student_profile";
        $columns = $this->getTableCols($table);
        $ins = array();
        foreach ($columns as $key) {
            if (isset($data[$key])) {
                $ins[$key] = $data[$key];
            }
        }

        return $ins;
    }


    public function insertProfile($data)
    {
        $table = "jb_student_profile";
        $ins = $this->prepareProfileData($data);
        $this->insert($table, $this->setInsert($ins));
        return $this->lastid();
    }

    public function prepareParentsData($data)
    {
        $table = "jb_student_parents";
        $columns = $this->getTableCols($table);
        $ins = array();
        foreach ($columns as $key) {
            if (isset($data[$key])) {
                $ins[$key] = $data[$key];
            }
        }
        $ins['username'] = "";
        $ins['password'] = "";
        $ins = $this->sortArrayByArray($ins, $columns);
        return $ins;
    }

    public function insertParents($data)
    {
        $table = "jb_student_parents";
        $ins = $this->prepareParentsData($data);
        $this->insert($table, $this->setInsert($ins));
        return $this->lastid();
    }

    public function updateStudent($data, $id)
    {
        $table = "jb_students";
        $update_where = array('id' => $id);
        $updatedData = $this->prepareStudentData($data);
        $this->update($table, $this->setUpdated($updatedData), $update_where, 1);
    }

    public function updateProfile($data, $id)
    {
        $table = "jb_student_profile";
        $update_where = array('id' => $id);
        $updatedData = $this->prepareProfileData($data);
        unset($data['student_id']);
        unset($data['parents_id']);

        $this->update($table, $this->setUpdated($updatedData), $update_where, 1);
    }

    public function updateParents($data, $id)
    {
        $table = "jb_student_parents";
        $update_where = array('id' => $id);
        $updatedData = $this->prepareParentsData($data);
        //unset($data['student_id']);
        $this->update($table, $this->setUpdated($updatedData), $update_where, 1);
    }


    public function RozaStudentDates($id)
    {

        $sql = "SELECT
            `jb_students`.`doa`
            , `jb_student_profile`.`date_of_birth`
        FROM
            `jb_student_profile`
            INNER JOIN `jb_students`
                ON (`jb_student_profile`.`student_id` = `jb_students`.`id`) WHERE `jb_students`.`id` = $id LIMIT 1";

        $row = $this->getSingle($sql);

        return $row;

    }


    public function studentsCount($session)
    {
        $sql = "SELECT
    COUNT(`jb_students`.`id`) AS `tot`
    , `jb_branches`.`id` AS `branch_id`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_branches`.`total_students` AS `total_students`
    , `jb_branches`.`zone_id` AS `zone_id`
    , `jb_zones`.`title` AS `zone_title`
FROM
    `jb_students`
    INNER JOIN `jb_branches` 
        ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_zones` 
        ON (`jb_branches`.`zone_id` = `jb_zones`.`id`)
        WHERE `jb_branches`.`published` = 1 AND `jb_students`.`student_status` = 'current'
        AND `jb_students`.`session_id` = $session
GROUP BY `branch_id`";

        $res = $this->getResults($sql);

        return $res;
    }

    public function branchOperators()
    {
        $sql = "SELECT
    `jb_users`.`name`
    , `jb_branch_operators`.`branch_id`
FROM
    `jb_users`
    INNER JOIN `jb_branch_operators` 
        ON (`jb_users`.`id` = `jb_branch_operators`.`user_id`);";
        $res = $this->getResults($sql);

        return $res;
    }

    public function operatorBranchStudent($branch)
    {
        $user = $this->getUserId();
        $sql = "SELECT * FROM `jb_login_user_branches` WHERE branch_id = $branch AND user_id = $user";
        $res = $this->getSingle($sql);
        return $res;

    }

    public function countZoneStudents($zone)
    {
        $sql = "SELECT
    COUNT(`jb_students`.`id`) AS `tot`
    , `jb_students`.`branch_id`
    , `jb_students`.`class_id`
    , `jb_students`.`gender`
FROM
    `jb_students`
    INNER JOIN `jb_branches` 
        ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
WHERE (`jb_students`.`student_status` = 'current'
    AND `jb_branches`.`zone_id` = $zone)
GROUP BY `jb_students`.`branch_id`, `jb_students`.`class_id`, `jb_students`.`gender`";

        $res = $this->getResults($sql);

        return $res;
    }

    public function countTermintedStudents($start, $end)
    {
        $sql = "SELECT
    COUNT(`jb_terminated`.`student_id`) AS `tot`
    , `jb_terminated`.`date`
    , `jb_transfer_history`.`old_branch`
    , `jb_transfer_history`.`old_class`
FROM
    `jb_terminated`
    INNER JOIN `jb_transfer_history` 
        ON (`jb_terminated`.`student_id` = `jb_transfer_history`.`student_id`) WHERE 1";

        $sql .= " AND `jb_terminated`.`date` BETWEEN '$start' AND '$end'";

        $res = $this->getResults($sql);
        return $res;
    }

    public function countHifzCompletion($start, $end)
    {
        $sql = "SELECT
    COUNT(`jb_hifz_completion`.`student_id`) AS `tot`
    , `jb_students`.`branch_id`
    , `jb_students`.`gender`
FROM
    `jb_hifz_completion`
    INNER JOIN `jb_students` 
        ON (`jb_hifz_completion`.`student_id` = `jb_students`.`id`) WHERE 1";

        $sql .= " AND `jb_hifz_completion`.`end_date_hifz` BETWEEN '$start' AND '$end'";

        $res = $this->getResults($sql);
        return $res;
    }

    public function blockStudentToggle($status, $id)
    {

        $update = array('student_status' => $status);
        $update_where = array('id' => $id);
        $this->update('jb_students', $update, $update_where, 1);

    }

    public function removeStudentCredentials($id)
    {
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr . "student_credentials";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            return $this->delete($table, $where, 1);
        }

        return false;

    }


    public function getCredentials($param = array())
    {

        $isLimit = true;

        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_student_credentials`.`id`
    , `jb_student_credentials`.`student_id`
    , `jb_student_credentials`.`password`
    , `jb_student_credentials`.`published`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
FROM
    `jb_student_credentials`
    INNER JOIN `jb_students` 
        ON (`jb_student_credentials`.`student_id` = `jb_students`.`id`) WHERE 1";



        if (!empty($param['session'])) {
            $isLimit = false;
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['branch'])) {
            $isLimit = false;
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $isLimit = false;
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $isLimit = false;
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['id'])) {
            $isLimit = false;
            $sql .= " AND `" . $pr . "student_credentials`.`section_id` = " . $param['id'];
        }

        $sql .= " ORDER BY `jb_student_credentials`.`student_id`";

        if ($isLimit) {
            $sql .= " LIMIT 250";
        }


        return $this->getResults($sql);

    }


    public function insertCredentials($data): bool
    {
        $table = $this->getPrefix() . "student_credentials";
        return $this->insert($table, $data);

    }

    /*public function studentLastTransferLog($start,$end){
        $sql = "SELECT
    MAX(`date`) AS `date`
    , `student_id`
    , `old_branch`
    , `old_class`
FROM
    `jb_transfer_history` WHERE `jb_transfer_history`.`date` BETWEEN '$start' AND '$end'
GROUP BY `student_id`
HAVING (`date` BETWEEN '$start' AND '$end')";

        $res = $this->getResults($sql);
        return $res;
    }*/

}
