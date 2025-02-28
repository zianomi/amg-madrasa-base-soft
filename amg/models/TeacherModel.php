<?php

/**
 * Created by PhpStorm.
 * User: fahad
 * Date: 2/25/2017
 * Time: 3:17 PM
 */
class TeacherModel extends BaseModel
{

    protected function getTableName()
    {
    }

    public function getTeachers()
    {
        $sql = "SELECT
            `jb_users`.`name`
            , `jb_users`.`id`
            , `jb_users`.`user_img`
            , `jb_users`.`published`
        FROM
            `jb_users`
            INNER JOIN `jb_user_groups`
                ON (`jb_users`.`group_id` = `jb_user_groups`.`id`)
            INNER JOIN `jb_group_types`
                ON (`jb_user_groups`.`type_id` = `jb_group_types`.`id`)
        WHERE 1 AND (`jb_group_types`.`group_key` ='teachers') 
        AND `jb_users`.`published` = 1 ";

        //echo '<pre>';print_r($sql );echo '</pre>';
        $res = $this->getResults($sql);
        return $res;
    }

    public function removeStaffBranches($id)
    {
        $tableName = $this->getPrefix() . "staff_branches";
        $this->query("DELETE FROM $tableName WHERE staff_id = $id");
    }

    public function insertStaffBranches($data = array())
    {
        $tableName = $this->getPrefix() . "staff_branches";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids);
        }
        return $return;
    }

    public function removeStaffDesignations($id)
    {
        $tableName = $this->getPrefix() . "staff_designation";
        $this->query("DELETE FROM $tableName WHERE staff_id = $id");
    }


    public function insertStaffDesignations($data = array())
    {
        $tableName = $this->getPrefix() . "staff_designation";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids);
        }
        return $return;
    }


    public function removeStaffManagers($id)
    {
        $tableName = $this->getPrefix() . "staff_managers";
        $this->query("DELETE FROM $tableName WHERE staff_id = $id");
    }

    public function insertStaffManagers($data = array())
    {
        $tableName = $this->getPrefix() . "staff_managers";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids);
        }
        return $return;
    }

    public function userBranches($id)
    {
        $sql = "SELECT
            `branch_id`
        FROM
            `jb_staff_branches` WHERE 1";
        $sql .= " AND staff_id = $id";

        $res = $this->getResults($sql);
        return $res;
    }

    public function branchUsers($branchIds, $stafId)
    {
        $sql = "SELECT
            `jb_users`.`id`
            , `jb_users`.`name` AS title
        FROM
            `jb_users`
            INNER JOIN `jb_staff_branches`
                ON (`jb_users`.`id` = `jb_staff_branches`.`staff_id`) WHERE 1";
        if (!empty($branchIds)) {
            $sql .= " AND `jb_staff_branches`.`branch_id` IN ($branchIds)";
        }

        $sql .= " AND `jb_users`.`id` <> $stafId";
        $sql .= " GROUP BY `jb_users`.`id`";

        $res = $this->getResults($sql);
        return $res;
    }

    public function getUserDetail($id)
    {
        $sql = "SELECT * FROM jb_users WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function removeTeacherClasses($id)
    {
        $tableName = $this->getPrefix() . "teacher_classes";
        $this->query("DELETE FROM $tableName WHERE teacher_id = $id");

    }

    public function insertTeacherClasses($data = array())
    {
        $tableName = $this->getPrefix() . "teacher_classes";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function getTeacherClasses($id)
    {
        $sql = "SELECT * FROM jb_teacher_classes WHERE teacher_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function removeTeacherSubjects($id)
    {
        $tableName = $this->getPrefix() . "teacher_subjects";
        $this->query("DELETE FROM $tableName WHERE teacher_id = $id");

    }

    public function insertTeacherSubjects($data = array())
    {
        $tableName = $this->getPrefix() . "teacher_subjects";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function getTeacherSubjects($id)
    {
        $sql = "SELECT * FROM jb_teacher_subjects WHERE teacher_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getTeacherClassSubjects($id)
    {
        $sql = "SELECT
            `jb_subjects`.`id`
            , `jb_subjects`.`title`
        FROM
            `jb_subjects`
            INNER JOIN `jb_teacher_classes`
                ON (`jb_subjects`.`class_id` = `jb_teacher_classes`.`class_id`)
        WHERE (`jb_teacher_classes`.`teacher_id` =$id)";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getAtdName($key)
    {
        $param = $this->atdParams();
        return $param[$key];
    }

    public function atdParams()
    {
        $atd = array();
        $atd[1] = "Present";
        $atd[2] = "Absent";
        $atd[3] = "Leave";
        $atd[4] = "Late";

        return $atd;
    }

    public function getAttendance($params = array())
    {

        $sql = "SELECT attendance, date, entry_time, exit_time, jb_users.id, jb_users.name AS staff_title
                FROM jb_staff_attendance
                INNER JOIN jb_users ON jb_users.id = jb_staff_attendance.staff_id
                WHERE 1 ";

        if (!empty($params['user'])) {
            $sql .= " jb_users.id = " . $params['user'];
        }

        if (!empty($params['id'])) {
            $sql .= " AND jb_staff_attendance.id = " . $params['id'];
        }

        if (!empty($params['branch'])) {
            $sql .= " AND jb_staff_attendance.branch_id = " . $params['branch'];
        }

        if (!empty($params['session'])) {
            $sql .= " AND jb_staff_attendance.session_id = " . $params['session'];
        }


        if (!empty($params['date'])) {
            $sql .= " AND jb_staff_attendance.date = '" . $params['date'] . "'";
        }

        $sql .= " ORDER BY jb_staff_attendance.id DESC";


        $res = $this->getResults($sql);

        return $res;

    }

    public function checkStaffAtd($staffid, $date)
    {

        $check_atd = array(
                'staff_id' => $staffid,
                'date' => $date
        );
        if ($this->exists('jb_staff_attendance', 'staff_id', $check_atd)) {
            return true;
        }

        return false;

    }

    public function removeSyllabus($id)
    {
        $whereColumn = "id";
        $table = "jb_syllabus";
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

    public function removeSessionSyllabus($id)
    {
        $whereColumn = "id";
        $table = "jb_session_syllabus";
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

    public function removePeriod($id)
    {

        $whereColumn = "id";
        $table = "jb_periods";
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

    public function inserAtd($data)
    {
        $table = "jb_staff_attendance";
        $this->insert($table, $data);
        return $this->lastid();
    }

    public function getClassSubjects($id)
    {
        $sql = "SELECT id,title from jb_subjects WHERE class_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }


    public function periodHtmlOutput($session, $branch, $class, $section)
    {
        $params = array("session" => $session, "branch" => $branch, "class" => $class, "section" => $section);
        $periodData = $this->getPeriodData($params);

        $periodsSubjects = $this->GetPeriodSubjects($params);
        $teacherPeriodsArr = array();

        $html = '';

        foreach ($periodsSubjects as $periodsSubject) {
            $teacherPeriodsArr[$periodsSubject['staff_id']][$periodsSubject['period_id']][] = $periodsSubject;
        }

        foreach ($periodData as $periodByclass) {
            $html .= "<tr id='" . $periodByclass['period_id'] . "'>";

            $html .= "<td>" . $periodByclass['branch_title'] . "</td>";
            $html .= "<td>" . $periodByclass['session_title'] . "</td>";
            $html .= "<td>" . $periodByclass['class_title'] . "</td>";
            $html .= "<td>" . $periodByclass['section_title'] . "</td>";
            $html .= "<td>" . $periodByclass['staff_title'] . "</td>";


            $stringSubjects = "";

            if (isset($teacherPeriodsArr[$periodByclass['staff_id']][$periodByclass['period_id']])) {
                $i = 0;
                foreach ($teacherPeriodsArr[$periodByclass['staff_id']][$periodByclass['period_id']] as $periodsRow) {
                    $i++;
                    if ($i > 1) {
                        $stringSubjects .= "<br />" . $periodsRow['title'];
                    } else {
                        $stringSubjects .= $periodsRow['title'];
                    }
                }

            }

            $html .= "<td>$stringSubjects</td>";
            $html .= '<td><span class="label label-success">' . date('H:i:A', strtotime($periodByclass['start_time'])) . "</span></td>";
            $html .= '<td><span class="label label-success">' . date('H:i:A', strtotime($periodByclass['end_time'])) . "</span></td>";
            $html .= '<td class="delete" data-id="' . $periodByclass['period_id'] . '"><button class="btn btn-danger">Delete<button</td>';

            $html .= "</tr>";
        }

        return $html;
    }


    public function checkPeriod($session, $staff, $section, $startTime, $endTime)
    {
        $sql = "SELECT staff_id FROM jb_periods WHERE 1 ";
        //$sql .= " AND staff_id = $staff";
        $sql .= " AND session_id = $session";
        //$sql .= " AND section_id = $section";
        $sql .= " AND (section_id = $section OR staff_id = $staff) ";
        $sql .= " AND (
                  `start_time` BETWEEN '$startTime' AND '$endTime'
                  OR `end_time` BETWEEN '$startTime' AND '$endTime'
                  OR '$startTime' BETWEEN `start_time` AND `end_time`
                  OR '$endTime' BETWEEN `start_time` AND `end_time`
                  ) ";
        $res = $this->link->query($sql);
        if ($res->num_rows > 0) {
            return true;
        }
        return false;
    }


    public function insertPeriod($data)
    {
        $table = "jb_periods";
        $this->insert($table, $this->setInsert($data));
        return $this->lastid();
    }


    public function insertPeriodSubjects($data = array())
    {

        $tableName = $this->getPrefix() . "period_subjects";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }


    public function insertSessionSyllabus($data)
    {
        $table = "jb_session_syllabus";
        $this->insert($table, $data);
        return $this->lastid();
    }


    public function GetSessionSubjectChapters($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_session_syllabus`.`date`
            , `jb_session_syllabus`.`id`
            , `jb_sessions`.`title` session_title
            , `jb_syllabus`.`title`
            , `jb_subjects`.`title` subject_title
            , `jb_session_syllabus`.`syllabus_id`
             , `jb_syllabus`.`start_page_no`
          , `jb_syllabus`.`end_page_no`
        FROM
            `jb_session_syllabus`
            INNER JOIN `jb_syllabus`
                ON (`jb_session_syllabus`.`syllabus_id` = `jb_syllabus`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_session_syllabus`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_subjects`
                ON (`jb_syllabus`.`subject_id` = `jb_subjects`.`id`)
        WHERE 1";
        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "session_syllabus`.`session_id` = " . $param['session'];
        }
        if (!empty($param['subject'])) {
            $sql .= " AND `" . $pr . "syllabus`.`subject_id` = " . $param['subject'];
        }

        $res = $this->getResults($sql);
        return $res;
    }


    public function inserSyllabus($data)
    {
        $table = "jb_syllabus";
        $this->insert($table, $data);
        return $this->lastid();
    }


    public function GetSyllabus($param = array())
    {

        $pr = $this->getPrefix();
        $sql = "
    SELECT
        `" . $pr . "subjects`.`title` AS `subject_title`
        ,`" . $pr . "syllabus`.`id`
        ,`" . $pr . "syllabus`.`title`
        ,`" . $pr . "syllabus`.`start_page_no`
        ,`" . $pr . "syllabus`.`end_page_no`";
        $sql .= "
        FROM
        `" . $pr . "syllabus`
        INNER JOIN `" . $pr . "subjects`
                ON (`" . $pr . "syllabus`.`subject_id` = `" . $pr . "subjects`.`id`)
        WHERE 1
    ";

        if (!empty($param['subject'])) {
            $sql .= " AND `" . $pr . "syllabus`.`subject_id` = " . $param['subject'];
        }
        $res = $this->getResults($sql);
        return $res;


    }


    public function insertStaffAtd($data = array())
    {
        $tableName = $this->getPrefix() . "staff_attendance";
        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }


    public function getBranchStaff($branch, $entry_date)
    {
        $sql = "SELECT jb_staff_branches.staff_id AS id,
                        jb_users.name AS title
                        FROM jb_staff_branches
                        INNER JOIN jb_users ON jb_staff_branches.staff_id = jb_users.id
                        WHERE jb_staff_branches.branch_id = $branch";

        $sql .= " AND jb_staff_branches.staff_id NOT IN ";
        $sql .= "(";
        $sql .= "SELECT jb_staff_attendance.staff_id FROM jb_staff_attendance WHERE jb_staff_attendance.date = '" . $entry_date . "'";
        $sql .= ")";

        $res = $this->getResults($sql);

        return $res;
    }


    public function staffAttendance($params = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT u.name AS staff_title, u.id
        , SUM(CASE WHEN attendance = 1 THEN 1 ELSE 0 END) AS present_staff
        , SUM(CASE WHEN attendance = 2 THEN 1 ELSE 0 END) AS absent_staff
        , SUM(CASE WHEN attendance = 3 THEN 1 ELSE 0 END) AS leave_staff
        , SUM(CASE WHEN attendance = 4 THEN 1 ELSE 0 END) AS late_staff
        FROM `" . $pr . "staff_attendance` INNER JOIN " . $pr . "users u ON staff_id = u.id
        WHERE 1";

        if (!empty($params['session'])) {
            $sql .= " AND session_id = " . $params['session'];
        }
        if (!empty($params['branch'])) {
            $sql .= " AND branch_id = " . $params['branch'];
        }
        if (!empty($params['date']) && !empty($params['to_date'])) {
            $sql .= " AND date between '" . $params['date'] . "' AND '" . $params['to_date'] . "'";
        }
        $sql .= " GROUP BY staff_id";
        $res = $this->getResults($sql);
        return $res;

    }


    public function getSingleStaffAttand($params = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "staff_attendance`  WHERE 1";
        if(!empty($params['date']) && !empty($params['to_date'])){
            $sql .= " AND date between '" . $params['date'] . "' AND '" . $params['to_date'] . "'";
        }

        if(!empty($params['staff'])){
            $sql .= " AND staff_id = " . $params['staff'];
        }

        $res = $this->getResults($sql);
       return $res;
    }


    public function periodsTeacher($params = array()){
        $sql = "SELECT
            `jb_users`.`id`
            , `jb_users`.`name` AS title
            , `jb_staff_branches`.`branch_id`
        FROM
            `jb_users`
            INNER JOIN `jb_teacher_classes`
                ON (`jb_users`.`id` = `jb_teacher_classes`.`teacher_id`)
            INNER JOIN `jb_user_groups`
                ON (`jb_user_groups`.`id` = `jb_users`.`group_id`)
            INNER JOIN `jb_staff_branches`
                ON (`jb_users`.`id` = `jb_staff_branches`.`staff_id`)
            INNER JOIN `jb_group_types`
                ON (`jb_group_types`.`id` = `jb_user_groups`.`type_id`)
        WHERE 1 AND `jb_users`.`published` = 1";

        if(!empty($params['staff'])){
            $sql .= " AND `jb_users`.`id` = " . $params['staff'];
        }

        if(!empty($params['branch'])){
            $sql .= " AND `jb_staff_branches`.`branch_id` = " . $params['branch'];
        }

        if(!empty($params['class'])){
            $sql .= " AND `jb_teacher_classes`.`class_id` = " . $params['class'];
        }

        if(!empty($params['groupy_type'])){
            $sql .= " AND `jb_group_types`.`group_key` = '" . $params['groupy_type'] . "'";
        }

        $res = $this->getResults($sql);
       return $res;
    }


    public function getPeriodData($params = array()){
        $sql = "SELECT
            `jb_branches`.`title` branch_title
            , `jb_classes`.`title` class_title
            , `jb_sessions`.`title` session_title
            , `jb_sections`.`title` section_title
            , `jb_users`.`name` staff_title
            , `jb_periods`.`staff_id`
            , `jb_periods`.`start_time`
            , `jb_periods`.`end_time`
            , `jb_periods`.`id` AS period_id
        FROM
            `jb_periods`
            INNER JOIN `jb_branches`
                ON (`jb_periods`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_periods`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_periods`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_periods`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_users`
                ON (`jb_periods`.`staff_id` = `jb_users`.`id`) WHERE 1";

        if(!empty($params['branch'])){
            $sql .= " AND jb_periods.branch_id = " . $params['branch'];
        }

        if(!empty($params['session'])){
            $sql .= " AND jb_periods.session_id = " . $params['session'];
        }

        if(!empty($params['class'])){
            $sql .= " AND jb_periods.class_id = " . $params['class'];
        }

        if(!empty($params['section'])){
            $sql .= " AND jb_periods.section_id = " . $params['section'];
        }

        $res = $this->getResults($sql);
       return $res;
    }


    public function GetPeriodSubjects($params = array()){
        $sql = "SELECT
            `jb_subjects`.`id`
            , `jb_subjects`.`title`
            , `jb_periods`.`staff_id`
            , `jb_periods`.`id` AS period_id
        FROM
            `jb_periods`
            INNER JOIN `jb_period_subjects`
                ON (`jb_periods`.`id` = `jb_period_subjects`.`period_id`)
            INNER JOIN `jb_subjects`
                ON (`jb_period_subjects`.`subject_id` = `jb_subjects`.`id`)
        WHERE  1";

        if(!empty($params['branch'])){
            $sql .= " AND `jb_periods`.`branch_id` = " . $params['branch'];
        }

        if(!empty($params['class'])){
            $sql .= " AND `jb_periods`.`class_id` = " . $params['class'];
        }

        if(!empty($params['session'])){
            $sql .= " AND `jb_periods`.`session_id` = " . $params['session'];
        }

        if(!empty($params['section'])){
            $sql .= " AND `jb_periods`.`section_id` = " . $params['section'];
        }

        $res = $this->getResults($sql);
        return $res;
    }


    public function periodReport($param = array())
        {

            $pr = $this->getPrefix();
            $sql = "
        SELECT
            `" . $pr . "periods`.`id`
            ,`" . $pr . "sessions`.`title` AS `session_title`
            , `" . $pr . "branches`.`title` AS `branch_title`
            , `" . $pr . "classes`.`title` AS `class_title`
            ,  `" . $pr . "sections`.`title` AS `section_title`
            ,  `" . $pr . "users`.`name` AS `teacher_title`
            , `" . $pr . "periods`.`start_time`
            , `" . $pr . "periods`.`end_time`
            , `" . $pr . "periods`.`staff_id`
            , `" . $pr . "periods`.`id` AS period_id
            ";
            if (!empty($param['fields'])) {
                $sql .= " " . $param['fields'];
            }

            $sql .= "
        FROM
            `" . $pr . "periods`
            INNER JOIN `" . $pr . "sessions`
                ON (`" . $pr . "periods`.`session_id` = `" . $pr . "sessions`.`id`)
            INNER JOIN `" . $pr . "branches`
                ON (`" . $pr . "periods`.`branch_id` = `" . $pr . "branches`.`id`)
            INNER JOIN `" . $pr . "classes`
                ON (`" . $pr . "periods`.`class_id` = `" . $pr . "classes`.`id`)
            INNER JOIN `" . $pr . "sections`
                    ON (`" . $pr . "periods`.`section_id` = `" . $pr . "sections`.`id`)
            INNER JOIN `" . $pr . "users`
                    ON (`" . $pr . "periods`.`staff_id` = `" . $pr . "users`.`id`)
            WHERE 1
        ";

            if (!empty($param['branch'])) {
                $sql .= " AND `" . $pr . "periods`.`branch_id` = " . $param['branch'];
            }

            if (!empty($param['class'])) {
                $sql .= " AND `" . $pr . "periods`.`class_id` = " . $param['class'];
            }

            if (!empty($param['section'])) {
                $sql .= " AND `" . $pr . "periods`.`section_id` = " . $param['section'];
            }

            if (!empty($param['session'])) {
                $sql .= " AND `" . $pr . "periods`.`session_id` = " . $param['session'];
            }

            $res = $this->getResults($sql);
            return $res;
        }


    public function GetSubjectChapters($id){
        $sql = "SELECT * FROM jb_syllabus WHERE subject_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }




}
