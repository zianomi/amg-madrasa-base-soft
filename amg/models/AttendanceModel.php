<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 3/31/2017
 * Time: 7:58 PM
 */
class AttendanceModel extends BaseModel
{

    protected function getTableName()
    {
        $tableName = $this->getPrefix() . "daily_attand";
        return $tableName;
    }

    public function insertSchoolDay($data = array())
    {

        $tableName = $this->getPrefix() . "attand_date_log";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    function attandPaaram($sel = "")
    {
        $htmld = "";
        $selected = "";
        $htmld .= "<option value=''></option>";

        $two = ($sel == 2) ? $selected = ' selected="selected"' : '';
        $three = ($sel == 3) ? $selected = ' selected="selected"' : '';
        $four = ($sel == 4) ? $selected = ' selected="selected"' : '';

        if ($this->getLang() == "ur") {

            $htmld .= "<option value='2'" . $two . ">غیر حاضر</option>";
            $htmld .= "<option value='3'" . $three . ">رخصت</option>";
            $htmld .= "<option value='4'" . $four . ">متآخر</option>";
        } else {
            $htmld .= "<option value='2'" . $two . ">Absent</option>";
            $htmld .= "<option value='3'" . $three . ">Leave</option>";
            $htmld .= "<option value='4'" . $four . ">Late</option>";
        }

        return $htmld;
    }


    public function insertIdAttand($data)
    {
        $table = $this->getTableName();
        $res = $this->insert($table, $this->setInsert($data));
        return $res;
    }

    public function checkClassAttand($param = array())
    {

        $branch = $param['branch'];
        $class = $param['class'];
        $section = $param['section'];
        $session = $param['session'];
        $date = $param['date'];

        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
        FROM
            `jb_students` WHERE 1";

        $sql .= " AND `jb_students`.`id` NOT IN ";

        $sql .= "(";
        $sql .= " SELECT student_id FROM jb_daily_attand WHERE date = '$date'";
        $sql .= ")";

        $sql .= " AND jb_students.branch_id = $branch";
        $sql .= " AND jb_students.class_id = $class";
        $sql .= " AND jb_students.section_id = $section";
        $sql .= " AND jb_students.session_id = $session";

        $res = $this->getResults($sql);

        return $res;
    }


    public function insertClassAttand($data = array())
    {

        $tableName = $this->getTableName();

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function branchStudent($id, $branch)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `" . $pr . "students`.`id`
            , `" . $pr . "students`.`name`
            , `" . $pr . "students`.`gender`
            , `" . $pr . "students`.`fname`
            , `" . $pr . "students`.`branch_id`
            , `" . $pr . "students`.`class_id`
            , `" . $pr . "students`.`section_id`
            , `" . $pr . "students`.`session_id`
            , `" . $pr . "login_user_branches`.`user_id`
        FROM
            `" . $pr . "login_user_branches`
            INNER JOIN `" . $pr . "students`
                ON (`" . $pr . "login_user_branches`.`branch_id` = `" . $pr . "students`.`branch_id`) WHERE 1 ";
        $sql .= " AND `" . $pr . "students`.`id` = $id";
        $sql .= " AND `" . $pr . "login_user_branches`.`user_id` = " . $this->getUserId();

        $sql .= " AND `" . $pr . "students`.`student_status` = '" . $this->stuStatus("current") . "'";
        $sql .= " AND `" . $pr . "students`.`branch_id` = $branch";

        $res = $this->getSingle($sql);


        return $res;
    }

    public function atdStudentReport($id, $date, $date2)
    {
        $tableName = $this->getTableName();
        $sql = "SELECT id, attand, date FROM " . $tableName . " WHERE student_id = $id";

        $sql .= " AND date BETWEEN '$date' AND '$date2'";

        $res = $this->getResults($sql);
        return $res;

    }

    public function ReturnAtdName($str)
    {

        if ($this->getLang() == "ur") {
            switch ($str) {
                case 2:
                    $str = 'غیر حاضر';
                    break;
                case 3:
                    $str = 'رخصت';
                    break;
                case 4:
                    $str = 'متآخر';
                    break;
            }
        } else {
            switch ($str) {
                case 2:
                    $str = 'Absent';
                    break;
                case 3:
                    $str = 'Leave';
                    break;
                case 4:
                    $str = 'Late';
                    break;
            }
        }

        return $str;
    }


    public function GetAttandEdit($name = "atd", $sel = "")
    {


        $htmld = '<select name="' . $name . '" id="atd" class="validate[required]">';
        $htmld .= "<option value=''></option>";

        switch ($sel) {
            case '2':
                $two = ' selected';
                $three = '';
                $four = '';
                break;

            case '3':
                $three = ' selected';
                $two = '';
                $four = '';
                break;

            case '4':
                $four = ' selected';
                $two = '';
                $three = '';
                break;

            default:
                $two = '';
                $three = '';
                $four = '';
        }
        $htmld .= "<option value='-1'>حاضر</option>";
        $htmld .= "<option value='2' " . $two . ">غیر حاضر</option>";
        $htmld .= "<option value='3' " . $three . ">رخصت</option>";
        $htmld .= "<option value='4' " . $four . ">متآخر</option>";
        $htmld .= "</select>";
        return $htmld;
    }


    public function removeAtd($id)
    {
        $whereColumn = "id";
        $table = $this->getTableName();

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            $this->delete($table, $where, 1);
        }

    }

    public function updateAttand($id, $update)
    {
        $update_where = array('id' => $id);
        $this->update($this->getTableName(), $update, $update_where, 1);
    }


    public function countNumberOfAttanbdDays($date, $to_date, $branch, $class)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT COUNT(*) AS tot FROM `" . $pr . "attand_date_log` WHERE date BETWEEN '$date' AND '$to_date'";
        $sql .= " AND branch_id = $branch AND class_id = $class";

        $row = $this->getSingle($sql);
        return $row['tot'];
    }

    public function countSchoolDays($date, $to_date, $branch)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT COUNT(*) AS tot, class_id FROM `" . $pr . "attand_date_log` WHERE date BETWEEN '$date' AND '$to_date'";
        $sql .= " AND branch_id = $branch";
        $sql .= " GROUP BY class_id";

        $res = $this->getResults($sql);
        $data = array();
        foreach ($res as $row) {
            $data[$row['class_id']] = $row['tot'];
        }
        return $data;
    }

    public function classAttandReport($param = array())
    {
        $pr = $this->getPrefix();
        //$sql = "SELECT student_id AS id, name, gender, fname, attand, date FROM " . $pr . "daily_attand ";

        //$sql .= " INNER JOIN " . $pr . "students ON " . $pr . "students.id = student_id";

        $sql = "SELECT
        `jb_daily_attand`.`student_id` AS `id`
        , `jb_daily_attand`.`attand`
        , `jb_daily_attand`.`date`
        , `jb_classes`.`title` AS `class_title`
        , `jb_sections`.`title` AS `section_title`
        , `jb_students`.`name`
        , `jb_students`.`fname`
        , `jb_students`.`gender`
    FROM
        `jb_daily_attand`
        INNER JOIN `jb_classes` 
            ON (`jb_daily_attand`.`class_id` = `jb_classes`.`id`)
        INNER JOIN `jb_sections` 
            ON (`jb_daily_attand`.`section_id` = `jb_sections`.`id`)
        INNER JOIN `jb_students` 
            ON (`jb_daily_attand`.`student_id` = `jb_students`.`id`) WHERE 1";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $date = $param['date'];
            $date2 = $param['to_date'];
            $sql .= " AND `" . $pr . "daily_attand`.`date` BETWEEN '$date' and '$date2'";
        }

        return $this->getResults($sql);

    }


    function classAttandReportSUM($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "
        SELECT
            `" . $pr . "daily_attand`.`student_id`
            , `" . $pr . "daily_attand`.`class_id`
            , `" . $pr . "students`.`name`
            , `" . $pr . "students`.`gender`
            , `" . $pr . "students`.`fname`
            , `" . $pr . "branches`.`title` branch_title
            , `" . $pr . "classes`.`title` class_title
            , `" . $pr . "sections`.`title` section_title
            , `" . $pr . "sessions`.`title` session_title
            , SUM(if (`" . $pr . "daily_attand`.`attand` = 2, 1, 0)) AS absent
            , SUM(if (`" . $pr . "daily_attand`.`attand` = 3, 1, 0)) AS leaves
            , SUM(if (`" . $pr . "daily_attand`.`attand` = 4, 1, 0)) AS late
        FROM
            `" . $pr . "daily_attand`
            INNER JOIN `" . $pr . "students`
                ON (`" . $pr . "daily_attand`.`student_id` = `" . $pr . "students`.`id`)
            INNER JOIN `" . $pr . "branches`
                ON (`" . $pr . "daily_attand`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `" . $pr . "classes`
                ON (`" . $pr . "daily_attand`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `" . $pr . "sections`
                ON (`" . $pr . "daily_attand`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `" . $pr . "sessions`
                ON (`" . $pr . "daily_attand`.`session_id` = `jb_sessions`.`id`)
        ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`session_id` = " . $param['session'];
        }


        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }


        $sql .= " GROUP BY `" . $pr . "daily_attand`.`student_id` ";


        $res = $this->getResults($sql);
        return $res;

    }


    function attandByPercent($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "
    SELECT
        `" . $pr . "daily_attand`.`student_id`
        , `" . $pr . "daily_attand`.`date`
        , `" . $pr . "daily_attand`.`class_id`
        , `" . $pr . "branches`.`title` branch_title
        , `" . $pr . "classes`.`title` class_title
        , `" . $pr . "sections`.`title` section_title
        , `" . $pr . "sessions`.`title` session_title
        ,SUM(if (`attand` = 2, 1, 0)) AS absent
        ,SUM(if (`attand` = 3, 1, 0)) AS leaves
        ,SUM(if (`attand` = 4, 1, 0)) AS late
        , `" . $pr . "students`.`name`
        , `" . $pr . "students`.`gender`
        , `" . $pr . "students`.`fname`
        , `" . $pr . "student_parents`.`father_mobile`
    FROM
        `" . $pr . "daily_attand`
        INNER JOIN `" . $pr . "students`
            ON (`" . $pr . "students`.`id` = `" . $pr . "daily_attand`.`student_id`)
        INNER JOIN `jb_student_profile`
            ON (`jb_students`.`id` = `jb_student_profile`.`student_id`)
        INNER JOIN `jb_student_parents`
            ON (`jb_students`.`id` = `jb_student_parents`.`student_id`)
        INNER JOIN `jb_branches`
            ON (`" . $pr . "daily_attand`.`branch_id` = `jb_branches`.`id`)
        INNER JOIN `jb_classes`
            ON (`" . $pr . "daily_attand`.`class_id` = `jb_classes`.`id`)
        INNER JOIN `jb_sections`
            ON (`" . $pr . "daily_attand`.`section_id` = `jb_sections`.`id`)
        INNER JOIN `jb_sessions`
            ON (`" . $pr . "daily_attand`.`session_id` = `jb_sessions`.`id`)
WHERE 1
        ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`session_id` = " . $param['session'];
        }


        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }


        $sql .= " GROUP BY `" . $pr . "daily_attand`.`student_id`, `" . $pr . "daily_attand`.`attand`";

        switch ($param['type']) {
            case 'grl':
                $count_type = " (absent + leaves + late) ";
                break;
            case 'gr':
                $count_type = " (absent + leaves) ";
                break;
            case 'g':
                $count_type = " absent ";
                break;
        }


        if (!empty($param['count'])) {
            $sql .= " HAVING $count_type " . $param['equality'] . " " . $param['count'];
        }



        $res = $this->getResults($sql);
        return $res;


    }

    public function stuAttand($param = array())
    {

        $sql = "SELECT COUNT(*) AS tot,
                `student_id`, `date`,
                SUM(if (`jb_daily_attand`.`attand` = 2, 1, 0)) AS absent
               ,SUM(if (`jb_daily_attand`.`attand` = 3, 1, 0)) AS rukhsat
               FROM `jb_daily_attand` WHERE 1  ";

        if (!empty($param['id'])) {
            $sql .= " AND `student_id` = " . $param['id'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `session_id` = " . $param['session'];
        }

        if (!empty($param['start']) && !empty($param['end'])) {
            $start = $param['start'];
            $end = $param['end'];
            $sql .= " AND date BETWEEN '$start' AND '$end'";
        }

        $sql .= " GROUP BY `student_id`, YEAR(`date`), MONTH(`date`)";

        $res = $this->getResults($sql);
        return $res;

    }

    public function StuTotalAttand($param = array())
    {

        $sql = "SELECT COUNT(*) AS tot, `date` FROM `jb_attand_date_log` WHERE 1 = 1 ";

        if (!empty($param['branch'])) {
            $sql .= " AND branch_id = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND class_id = " . $param['class'];
        }

        if (!empty($param['start']) && !empty($param['end'])) {
            $start = $param['start'];
            $end = $param['end'];
            $sql .= " AND date BETWEEN '$start' AND '$end'";
        }


        $sql .= " GROUP BY YEAR(`date`), MONTH(`date`)";


        $res = $this->getResults($sql);
        return $res;

    }

    function IDTotalAttandBetweenDate($id, $date, $date2)
    {

        $sql = "SELECT";
        $sql .= "
    SUM(if (`attand` = 2, 1, 0)) as absent,
    SUM(if (`attand` = 3, 1, 0)) as rukhsat,
    SUM(if (`attand` = 4, 1, 0)) as takheer
    FROM jb_daily_attand ";
        $sql .= " WHERE student_id = $id AND date BETWEEN '$date' AND '$date2'  LIMIT 1";

        $row = $this->getSingle($sql);
        return $row;

    }

    public function dayLogsReport($param = array())
    {
        $sql = "SELECT
            
             `jb_branches`.`title` AS `branch_title`
            , `jb_classes`.`title` AS `class_title`
            , `jb_sessions`.`title` AS `session_title`
            , `jb_attand_date_log`.`date`
            , `jb_attand_date_log`.`id`
        FROM
            `jb_attand_date_log`
            INNER JOIN `jb_branches` 
                ON (`jb_attand_date_log`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes` 
                ON (`jb_attand_date_log`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sessions` 
                ON (`jb_attand_date_log`.`session_id` = `jb_sessions`.`id`) WHERE 1";
        if (!empty($param['branch'])) {
            $sql .= " AND `jb_attand_date_log`.`branch_id` = " . $param['branch'];
        }
        if (!empty($param['class'])) {
            $sql .= " AND `jb_attand_date_log`.`class_id` = " . $param['class'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `jb_attand_date_log`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `jb_attand_date_log`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }
        $res = $this->getResults($sql);
        return $res;
    }


    public function GetAbsentStudents($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_student_parents`.`father_mobile`
    ,`jb_students`.`name`
    ,`jb_students`.`fname`,
    ,`jb_students`.`id`
FROM
    `jb_students`
    INNER JOIN `jb_student_parents` 
        ON (`jb_students`.`id` = `jb_student_parents`.`student_id`)
    INNER JOIN `jb_daily_attand` 
        ON (`jb_daily_attand`.`student_id` = `jb_students`.`id`)
WHERE 1 ";

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`date` = '" . $param['date'] . "'";
        }

        $sql .= " AND  (`jb_daily_attand`.`attand` = 2 OR `jb_daily_attand`.`attand` = 3)";

        return $this->getResults($sql);
    }


    public function GetAbsentStudentsByDate($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT student_id, attand, name, fname FROM jb_daily_attand 
        INNER JOIN jb_students ON jb_students.id = jb_daily_attand.student_id                 
                          WHERE 1 ";

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`session_id` = " . $param['session'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "daily_attand`.`date` = '" . $param['date'] . "'";
        }

        $sql .= " AND  `jb_daily_attand`.`attand` = 2 ";


        return $this->getResults($sql);
    }

    public function deleteDay($id)
    {
        $sql = "SELECT branch_id, class_id, session_id, date FROM jb_attand_date_log WHERE id = $id";
        $res = $this->getSingle($sql);
        extract($res);

        $this->query("DELETE FROM jb_daily_attand WHERE branch_id = $branch_id, class_id = $class_id, session_id = $session_id, date = '$date'");
        $this->query("DELETE FROM jb_attand_date_log WHERE id = $id");
    }

    public function branchStudentsAtd()
    {
        $sql = "SELECT
        COUNT(*) AS tot
        ,`jb_students`.`branch_id`
        , `jb_branches`.`title` AS `branch_name`
    FROM
        `jb_students`
        INNER JOIN `jb_branches` 
            ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
    WHERE (`jb_students`.`student_status`  = 'current'
        AND `jb_branches`.`published`  = 1) 
        GROUP BY `jb_students`.`branch_id`
        ";

        return $this->getResults($sql);
    }

    public function countBranchClasses($param = array())
    {

        $sql = "SELECT COUNT(*) AS tot, branch_id  FROM jb_attand_date_log WHERE 1";

        if (!empty($param['date'])) {
            $sql .= " AND date = '" . $param['date'] . "'";
        }

        $sql .= " GROUP BY branch_id";


        return $this->getResults($sql);

    }

    public function countBranchesAttend($param = array())
    {
        $sql = "SELECT
        branch_id,
        COUNT(CASE WHEN attand = 2 THEN 1 END) AS absent_count,
        COUNT(CASE WHEN attand = 3 THEN 1 END) AS leave_count,
        COUNT(CASE WHEN attand = 4 THEN 1 END) AS late_count
    FROM
        jb_daily_attand WHERE 1
    ";

        if (!empty($param['date'])) {
            $sql .= " AND date = '" . $param['date'] . "'";
        }

        $sql .= " GROUP BY `branch_id`";



        return $this->getResults($sql);

    }


    function generateAttendanceMessage($name, $fname,$branch) {
        // Format the provided date
        //$formattedDate = date("F d, Y", strtotime($date));











        $msg = "Respected Parent/Guardian,";
        $msg .= "\n";
        $msg .= "Assalamu Alaikum,";
        $msg .= "\n";
        $msg .= "We noticed that your child: ";



        $msg .= "{$name} / {$fname} ";

        $msg .= "was absent today.";
        $msg .= "Regular attendance is crucial for student’s academic progress. Kindly ensure their presence in upcoming classes.";
        $msg .= "\n";
        $msg .= "If there is any reason for the absence, please inform the school.";
        $msg .= "\n";
        $msg .= "Regards,";
        $msg .= "\n";
        $msg .= "Al Badar School";
        $msg .= "\n";
        $msg .= $branch;



        /*if ($date < date("Y-m-d")) {
            if ($attendanceStatus == 2) {
                $message .= "was absent on {$formattedDate}.";
            } else {
                $message .= "was on leave on {$formattedDate}.";
            }
        } elseif ($date == date("Y-m-d")) {
            if ($attendanceStatus == 2) {
                $message .= "is absent today, {$formattedDate}.";
            } else {
                $message .= "is on leave today, {$formattedDate}.";
            }
        } else {
            $message .= "is absent for {$formattedDate}.";
        }*/


        return $msg;
    }


}
