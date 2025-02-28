<?php

class AcademicModel extends BaseModel
{

    public function getHomeWorks($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_homeworks`.`path`
    , `jb_homeworks`.`id`
    , `jb_homeworks`.`pdf`
    , `jb_homeworks`.`class_id`
    , `jb_homeworks`.`subject_id`
    , `jb_homeworks`.`title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_homeworks`
    INNER JOIN `jb_classes` 
        ON (`jb_homeworks`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_homeworks`.`subject_id` = `jb_subjects`.`id`) WHERE 1";

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "homeworks`.`class_id` = " . $param['class'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "homeworks`.`date` = '" . $param['date'] . "'";
        }
        return $this->getResults($sql);
    }


    public function getClassHomeWorks($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT jb_class_home_works.id, jb_class_home_works.title, 
       jb_subject_groups.title AS subject_title, jb_class_home_works.date, jb_class_home_works.submit_date, jb_class_home_works.description,
       jb_sections.title AS section_title

FROM `jb_class_home_works` ";
        $sql .= " INNER JOIN `jb_subject_groups` ON (`jb_class_home_works`.`subject_id` = `jb_subject_groups`.`id`) ";
        $sql .= " INNER JOIN `jb_sections` ON (`jb_class_home_works`.`section_id` = `jb_sections`.`id`) ";
        $sql .= " WHERE 1 ";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "class_home_works`.`branch_id` = " . $param['branch'];
        }
        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "class_home_works`.`class_id` = " . $param['class'];
        }
        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "class_home_works`.`section_id` = " . $param['section'];
        }
        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "class_home_works`.`session_id` = " . $param['session'];
        }

        if (!empty($param['date'])) {
            $sql .= " AND `" . $pr . "class_home_works`.`date` = '" . $param['date'] . "'";
        }
        $sql .= " ORDER BY `" . $pr . "class_home_works`.`date` DESC LIMIT 250";
        //echo '<pre>'; print_r($sql); echo '</pre>';
        return $this->getResults($sql);
    }

    public function getSubjects($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `jb_subjects` WHERE 1 AND published = 1 ";

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "subjects`.`class_id` = " . $param['class'];
        }
        $sql .= " ORDER BY position ASC";

        //
        $res = $this->getResults($sql);
        return $res;
    }


    public function insertHomeWork($data)
    {
        return $this->insert('jb_homeworks', $data);
    }

    public function insertTeacherPeriodStructure($data)
    {
        return $this->insert('jb_teacher_timetable_structure', $data);
    }

    public function removeHomeWork($id)
    {
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr . "homeworks";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            $this->delete($table, $where, 1);
        }
    }

    public function getHomeWork($id)
    {
        $pr = $this->getPrefix();
        $table = $pr . "homeworks";
        $sql = "SELECT * FROM $table WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res;
    }

    protected function getTableName()
    {
        // TODO: Implement getTableName() method.
    }

    public function getTimeTables()
    {
        $sql = "SELECT * FROM jb_timetables WHERE 1 AND published = 1";
        $sql .= " ORDER BY position ASC";
        return $this->getResults($sql);
    }



    public function getWeekDays()
    {
        $sql = "SELECT * FROM jb_timetable_week_days WHERE 1 AND published = 1";
        $sql .= " ORDER BY position ASC";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getTimeTableStructure($param = array())
    {
        $sql = "SELECT
    `jb_timetable_period_name`.`title` AS period_name
    , `jb_timetable_structure`.`start_time`
    , `jb_timetable_structure`.`end_time`
    , `jb_timetables`.`title` AS `plan_title`
    , `jb_timetable_structure`.`id`
    , `jb_timetable_week_days`.`title` AS `day_title`
    , `jb_timetable_week_days`.`id` AS `day_id`
FROM
    `jb_timetable_structure`
    INNER JOIN `jb_timetables` 
        ON (`jb_timetable_structure`.`timetable_id` = `jb_timetables`.`id`)
    INNER JOIN `jb_timetable_period_name` 
        ON (`jb_timetable_structure`.`period_name_id` = `jb_timetable_period_name`.`id`)    
    INNER JOIN `jb_timetable_week_days` 
        ON (`jb_timetable_structure`.`weekday_id` = `jb_timetable_week_days`.`id`) WHERE 1 ";

        if (!empty($param['timetableId'])) {
            $sql .= " AND `jb_timetable_structure`.`timetable_id` = " . $param['timetableId'];
        }



        return $this->getResults($sql);

    }


    public function removeTimeTableStructure($id)
    {
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr . "teacher_timetable_structure";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            $this->delete($table, $where, 1);
        }

    }


    public function getSubjectWithClass($branch)
    {
        $sql = "SELECT
    `jb_subjects`.`id`
    , `jb_subjects`.`title`
    , `jb_subjects`.`class_id`
    , `jb_classes`.`title` AS `class_title`
FROM
    `jb_subjects`
    INNER JOIN `jb_classes` 
        ON (`jb_subjects`.`class_id` = `jb_classes`.`id`)
WHERE `jb_subjects`.`published`  = 1 AND `jb_subjects`.`subject_type` = 'exam'
";

        $sql .= " AND jb_subjects.branch_id = $branch";
        $sql .= " ORDER BY `jb_subjects`.`position` ASC";

        return $this->getResults($sql);
    }


    public function getParentSubjectWithClass($branch)
    {
        $sql = "SELECT
    `jb_subject_groups`.`id`
    , `jb_subject_groups`.`title`
    , `jb_subject_groups`.`class_id`
    , `jb_classes`.`title` AS `class_title`
FROM
    `jb_subject_groups`
    INNER JOIN `jb_classes` 
        ON (`jb_subject_groups`.`class_id` = `jb_classes`.`id`)
WHERE `jb_subject_groups`.`published`  = 1
";

        $sql .= " AND jb_subject_groups.branch_id = $branch";
        $sql .= " ORDER BY `jb_subject_groups`.`position` ASC";

        //echo $sql;

        return $this->getResults($sql);
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


    public function removeTeacherSubjects($teacherId)
    {
        $tableName = $this->getPrefix() . "teacher_subjects";
        if (!empty($teacherId)) {
            $sql = "DELETE FROM `$tableName` WHERE teacher_id = $teacherId";
            $this->query($sql);
        }
    }


    public function getTeacherSubjects($teacherId)
    {
        $tableName = $this->getPrefix() . "teacher_subjects";
        $sql = "SELECT * FROM $tableName WHERE teacher_id = $teacherId";
        $res = $this->getResults($sql);
        return $res;
    }


    public function getVideoLessons($param = array())
    {
        $pr = $this->getPrefix();

        $sql = "SELECT
    `jb_video_lessons`.`id`
    , `jb_video_lessons`.`title`
    , `jb_video_lessons`.`date`
    , `jb_video_lessons`.`video_url`
    , `jb_classes`.`title` AS `class_title`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_video_lessons`
    INNER JOIN `jb_classes` 
        ON (`jb_video_lessons`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_video_lessons`.`subject_id` = `jb_subjects`.`id`) WHERE 1";


        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "video_lessons`.`class_id` = " . $param['class'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "video_lessons`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }


        $res = $this->getResults($sql);

        return $res;
    }


    public function insertVideoLesson($data)
    {
        return $this->insert('jb_video_lessons', $data);
    }

    public function getUserTeachers($param = array())
    {
        $sql = "SELECT
    `jb_users`.`id`
    , `jb_users`.`name`
    , `jb_users`.`username`
    , `jb_users`.`password`
    , `jb_users`.`phone_number`
    , `jb_users`.`published`
    , `jb_user_branches`.`branch_id`
FROM
    `jb_users`
    INNER JOIN `jb_user_branches` 
        ON (`jb_users`.`id` = `jb_user_branches`.`user_id`)
WHERE 1";

        if (!empty($param['branch'])) {
            $branch = $param['branch'];
        } else {
            $branch = "";
        }

        if (!empty($branch)) {
            $sql .= " AND branch_id = $branch";
        }

        if (!empty($param['name'])) {
            $name = $param['name'];
            $sql .= " AND `jb_users`.`name` LIKE '%$name%'";
        }


        if (!empty($param['id'])) {
            $id = $param['id'];
            $sql .= " AND `jb_users`.`id` = '$id'";
        }



        $sql .= " AND `jb_users`.`user_type` = 'teacher' ";

        $sql .= " GROUP BY `jb_users`.`id`, `jb_user_branches`.`branch_id`";


        $res = $this->getResults($sql);

        return $res;
    }

    public function getTeacherSubjectsWithClasses($id)
    {
        $sql = "SELECT
    `jb_teacher_subjects`.`subject_id`
    , `jb_classes`.`title` AS `class_title`
    , `jb_subjects`.`class_id`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_teacher_subjects`
    INNER JOIN `jb_subjects` 
        ON (`jb_teacher_subjects`.`subject_id` = `jb_subjects`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_subjects`.`class_id` = `jb_classes`.`id`)
WHERE (`jb_teacher_subjects`.`teacher_id`  = $id)";

        $res = $this->getResults($sql);

        return $res;
    }



    public function teacherSessionSections($session, $teacherId)
    {
        $branches = $this->getTeacherBranches($teacherId);
        $subjects = $this->getTeacherSubjectsWithClasses($teacherId);
        $classes = array();
        foreach ($subjects as $subject) {
            $classes[$subject['class_id']] = array("id" => $subject['class_id'], "title" => $subject['class_title']);
        }

        $classArr = array();
        $branchArr = array();


        foreach ($classes as $class) {
            $classArr[] = $class['id'];

        }

        foreach ($branches as $branch) {
            $branchArr[] = $branch['branch_id'];

        }



        $sql = "SELECT
            `jb_sections`.`id`
            , `jb_sections`.`title`
            , `jb_session_sections`.`class_id`
        FROM
            `jb_session_sections`
            INNER JOIN `jb_sections`
                ON (`jb_session_sections`.`section_id` = `jb_sections`.`id`)
        WHERE 1 AND `jb_session_sections`.`session_id` = $session";



        if (count($classArr) > 0) {
            $sql .= " AND (";
            $i = 0;
            foreach ($classArr as $key) {

                if ($i != 0) {
                    $sql .= " OR ";
                }
                $sql .= " jb_session_sections.class_id = " . $key;
                $i++;
            }
            $sql .= ")";
        }

        //$branchArr[] = 1;
        //$branchArr[] = 2;
        if (count($branchArr) > 0) {
            $sql .= " AND (";
            $i = 0;
            foreach ($branchArr as $key) {

                if ($i != 0) {
                    $sql .= " OR ";
                }
                $sql .= " jb_session_sections.branch_id = " . $key;
                $i++;
            }
            $sql .= ")";
        }









        $sql .= " GROUP BY `jb_sections`.`id`, `jb_session_sections`.`class_id`";
        $sql .= " ORDER BY `jb_sections`.`position` ASC";

        $res = $this->getResults($sql);


        $sectionsArr = array();

        foreach ($res as $row) {
            $sectionsArr[$row['class_id']][] = array("id" => $row['id'], "title" => $row['title']);
        }

        return array("classes" => $classes, "subjects" => $subjects, "sections" => $sectionsArr);



    }


    public function getTeacherBranches($id)
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "user_branches WHERE 1 AND user_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getTeacherSections($id)
    {
        $sql = "SELECT * FROM " . $this->getPrefix() . "teacher_sections WHERE 1 AND teacher_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }



    public function removeTeacherSections($teacherId)
    {
        $tableName = $this->getPrefix() . "teacher_sections";
        if (!empty($teacherId)) {
            $sql = "DELETE FROM `$tableName` WHERE teacher_id = $teacherId";
            $this->query($sql);
        }
    }


    public function insertTeacherSections($data = array())
    {


        $tableName = $this->getPrefix() . "teacher_sections";

        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }

    public function countSessions($param = array())
    {
        $sql = "SELECT
    COUNT(*) AS `tot`
    , `jb_branches`.`title` AS `branch_title`
FROM
    `jb_teacher_assignments`
    INNER JOIN `jb_branches` 
        ON (`jb_teacher_assignments`.`branch_id` = `jb_branches`.`id`) WHERE 1
";

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `jb_teacher_assignments`.`date` BETWEEN '" . $param['date'] . "' AND '" . $param['to_date'] . "'";
        }

        if (!empty($param['branch'])) {
            if (is_array($param['branch'])) {
                $sql .= " AND (";
                $i = 0;
                foreach ($param['branch'] as $key) {

                    if ($i != 0) {
                        $sql .= " OR ";
                    }
                    $sql .= " jb_teacher_assignments.branch_id = " . $key;
                    $i++;
                }
                $sql .= ")";
            } else {
                $sql .= " AND jb_teacher_assignments.branch_id = " . $param['branch'];
            }
        }

        $sql .= " GROUP BY jb_teacher_assignments.branch_id";

        $res = $this->getResults($sql);


        return $res;

    }

    public function getDetailCount($param = array())
    {
        $sql = "SELECT
    `jb_teacher_assignments`.*
    , `jb_branches`.`title` AS `branch_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_teacher_assignments`.`branch_id`
    , `jb_subjects`.`title` AS `subject_title`
    , `jb_teacher_assignments`.`branch_id`
    , `jb_teacher_assignments`.`class_id`
    , `jb_teacher_assignments`.`subject_id`
    , `jb_teacher_assignments`.`teacher_id`
    , `jb_users`.`name`
FROM
    `jb_teacher_assignments`
    INNER JOIN `jb_branches` 
        ON (`jb_teacher_assignments`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_teacher_assignments`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_teacher_assignments`.`subject_id` = `jb_subjects`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_teacher_assignments`.`teacher_id` = `jb_users`.`id`) WHERE 1
";


        if (!empty($param['date'])) {
            $sql .= " AND `jb_teacher_assignments`.`date` = '" . $param['date'] . "'";
        }

        if (!empty($param['branch'])) {
            $sql .= " AND jb_teacher_assignments.branch_id = " . $param['branch'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND jb_teacher_assignments.session_id = " . $param['session'];
        }

        //$sql .= " GROUP BY `jb_teacher_assignments`.`branch_id`, `jb_teacher_assignments`.`class_id`, `jb_teacher_assignments`.`subject_id`";

        $res = $this->getResults($sql);
        return $res;
    }

    public function branchStudentCount($session, $branch)
    {
        $sql = "SELECT
    COUNT(*) AS `tot`
FROM
    `jb_students`
WHERE (`student_status`  = 'current'
    AND `session_id`  = $session
    AND `branch_id`  = $branch) ";
        $res = $this->getSingle($sql);
        return $res['tot'];
    }


    public function blockTeacherToggle($status, $id)
    {

        $update = array('published' => $status);
        $update_where = array('id' => $id);
        $this->update('jb_users', $update, $update_where, 1);

    }

    public function getTeacherAssignements($type = "q", $param = array())
    {

        if ($type == "count") {
            $sql = "SELECT COUNT(*) AS tot FROM jb_teacher_assignments WHERE 1";
        } else {
            $sql = "SELECT
    `jb_teacher_assignments`.`id`
    , `jb_teacher_assignments`.`title`
    , `jb_teacher_assignments`.`date`
    , `jb_teacher_assignments`.`time`
    , `jb_teacher_assignments`.`session_id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_teacher_assignments`.`branch_id`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_teacher_assignments`.`class_id`
    , `jb_classes`.`title` AS `class_title`
    , `jb_teacher_assignments`.`subject_id`
    , `jb_subjects`.`title` AS `subject_title`
    , `jb_teacher_assignments`.`teacher_id`
    , `jb_users`.`name` AS `teacher_title`
FROM
    `jb_teacher_assignments`
    INNER JOIN `jb_sessions` 
        ON (`jb_teacher_assignments`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_teacher_assignments`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_teacher_assignments`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_teacher_assignments`.`subject_id` = `jb_subjects`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_teacher_assignments`.`teacher_id` = `jb_users`.`id`) WHERE 1
";
        }



        if (!empty($param['date'])) {
            $sql .= " AND `jb_teacher_assignments`.`date` = '" . $param['date'] . "'";
        }

        if (!empty($param['session'])) {
            $sql .= " AND jb_teacher_assignments.session_id = " . $param['session'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND jb_teacher_assignments.branch_id = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND jb_teacher_assignments.class_id = " . $param['class'];
        }

        if (!empty($param['subject'])) {
            $sql .= " AND jb_teacher_assignments.subject_id = " . $param['subject'];
        }

        if (!empty($param['teacher'])) {
            $sql .= " AND jb_teacher_assignments.teacher_id = " . $param['teacher'];
        }

        if ($type == "count") {
            $res = $this->getSingle($sql);
            return $res['tot'];
        }

        $sql .= " ORDER BY `jb_teacher_assignments`.`date` DESC";

        if ($type == "q") {
            if (!empty($param['limit'])) {
                $sql .= $param['limit'];
            }
        }


        return $this->getResults($sql);
    }


    public function insertPeriodStructure($data)
    {

        $cols = array('timetable_id', 'weekday_id', 'period_name_id', 'start_time', 'end_time');
        return $this->insert_multi('jb_timetable_structure', $cols, $data, false);
    }

    public function getPeriodName()
    {
        $sql = "SELECT id, title FROM jb_timetable_period_name WHERE 1";
        $sql .= " AND lang_id = " . $this->getLangId();
        $sql .= " ORDER BY position ASC ";
        return $this->getResults($sql);
    }

    public function deleteTimetableClassSections($session, $branch)
    {

        $sql = "DELETE FROM jb_timetable_class_sections WHERE session_id = $session AND branch_id = $branch";
        return $this->query($sql);
    }

    public function insertTimetableClassSections($data)
    {

        $cols = array('branch_id', 'session_id', 'class_id', 'section_id', 'timetable_id');
        return $this->insert_multi('jb_timetable_class_sections', $cols, $data, false);
    }

    public function getSessionSectionsForTimeTable($session, $branch)
    {
        $sql = "SELECT
    `jb_session_sections`.`section_id`
    , `jb_sections`.`title` AS `section_title`
    , `jb_session_sections`.`class_id`
    , `jb_classes`.`title` AS `class_title`
FROM
    `jb_session_sections`
    INNER JOIN `jb_sections` 
        ON (`jb_session_sections`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_session_sections`.`class_id` = `jb_classes`.`id`) WHERE 1";

        $sql .= " AND jb_session_sections.session_id = $session AND jb_session_sections.branch_id = $branch";


        return $this->getResults($sql);
    }

    public function getTimetableClassSections($session, $branch)
    {
        $sql = "SELECT * FROM `jb_timetable_class_sections` WHERE 1";
        $sql .= " AND session_id = $session AND branch_id = $branch";

        return $this->getResults($sql);
    }

    public function getTeacherWithSubjects($param = array())
    {
        $sql = "SELECT
    `jb_teacher_subjects`.`teacher_id`
    , `jb_teacher_subjects`.`subject_id`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_teacher_subjects`
    INNER JOIN `jb_subjects` 
        ON (`jb_teacher_subjects`.`subject_id` = `jb_subjects`.`id`)
WHERE 1 ";

        if (!empty($param['branch'])) {
            $sql .= " AND `jb_subjects`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `jb_subjects`.`class_id` = " . $param['class'];
        }




        return $this->getResults($sql);
    }

    public function listStaff($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_users`.*
    , `jb_ac_designations`.`title` AS `designation_title`
    , `jb_ac_grades`.`title` AS `grade_title`
    , `jb_branches`.`title` AS `branch_title`
FROM
    `jb_staffs`
    INNER JOIN `jb_ac_designations` 
        ON (`jb_staffs`.`designation_id` = `jb_ac_designations`.`id`)
    INNER JOIN `jb_ac_grades` 
        ON (`jb_staffs`.`grade_id` = `jb_ac_grades`.`id`) 
    INNER JOIN `jb_branches` 
        ON (`jb_staffs`.`branch_id` = `jb_branches`.`id`) 

WHERE 1";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "staffs`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['designation'])) {
            $sql .= " AND `" . $pr . "staffs`.`designation_id` = " . $param['designation'];
        }

        if (!empty($param['grade'])) {
            $sql .= " AND `" . $pr . "staffs`.`grade_id` = " . $param['grade'];
        }

        if (!empty($param['gr_number'])) {
            $sql .= " AND `" . $pr . "staffs`.`gr_number` = " . $param['gr_number'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "staffs`.`id` = " . $param['id'];
        }

        if (!empty($param['staff_type'])) {
            $sql .= " AND `" . $pr . "staffs`.`staff_type` = '" . $param['staff_type'] . "'";
        }

        $sql .= " AND `jb_staffs`.company_id = " . $this->getCompany();

        if (empty($param)) {
            $sql .= " LIMIT 250";
        }


        return $this->getResults($sql);
    }

    public function getTeacherTimeTable($param = array())
    {
        $sql = "SELECT
    `time_table_structure_view`.`session_id`
    , `time_table_structure_view`.`timetable_structure_id`
    , `time_table_structure_view`.`period_name_id`
    , `time_table_structure_view`.`weekday_id`
    , `time_table_structure_view`.`start_time`
    , `time_table_structure_view`.`end_time`
    , `time_table_structure_view`.`teacher_id`
     , `time_table_structure_view`.`class_id`
    , `time_table_structure_view`.`section_id`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_timetable_week_days`.`title` AS `week_day_title`
    , `jb_timetable_period_name`.`title` AS `period_name_title`
FROM
    `time_table_structure_view`
    INNER JOIN `jb_classes` 
        ON (`time_table_structure_view`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`time_table_structure_view`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_timetable_week_days` 
        ON (`time_table_structure_view`.`weekday_id` = `jb_timetable_week_days`.`id`)
    INNER JOIN `jb_timetable_period_name` 
        ON (`time_table_structure_view`.`period_name_id` = `jb_timetable_period_name`.`id`)
WHERE 1";

        if (!empty($param['session'])) {
            $sql .= " AND `time_table_structure_view`.`session_id` = " . $param['session'];
        }

        if (!empty($param['teacher'])) {
            $sql .= " AND `time_table_structure_view`.`teacher_id` = " . $param['teacher'];
        }

        //$sql .= " AND `time_table_structure_view`.`company_id` = " . $this->getCompany();

        $sql .= " ORDER BY `time_table_structure_view`.`weekday_id`, `time_table_structure_view`.`period_name_id`";

        return $this->getResults($sql);
    }

    public function teacherTimeTableSubjects($param = array())
    {
        $sql = "SELECT
    `jb_timetable_session_subjects`.`class_id`
    , `jb_timetable_session_subjects`.`section_id`
    , `jb_timetable_session_subjects`.`subject_id`
    , `jb_timetable_session_subjects`.`timetable_structure_id`
    , `jb_subjects`.`title` AS `subject_title`
FROM
    `jb_timetable_session_subjects`
    INNER JOIN `jb_subjects` 
        ON (`jb_timetable_session_subjects`.`subject_id` = `jb_subjects`.`id`)
WHERE 1";

        if (!empty($param['session'])) {
            $sql .= " AND `jb_timetable_session_subjects`.`session_id` = " . $param['session'];
        }

        if (!empty($param['teacher'])) {
            $sql .= " AND `jb_timetable_session_subjects`.`teacher_id` = " . $param['teacher'];
        }


        return $this->getResults($sql);
    }

    public function getDayNameById($name)
    {
        $arr['Sat'] = 1;
        $arr['Sun'] = 2;
        $arr['Mon'] = 3;
        $arr['Tue'] = 4;
        $arr['Wed'] = 5;
        $arr['Thu'] = 6;
        $arr['Fri'] = 7;

        return $arr[$name];
    }


    public function getEnteredFixtures($param = array())
    {
        $sql = "SELECT
    `jb_timetable_fixtures`.`id`
    ,`jb_timetable_fixtures`.`start_time`
    , `jb_timetable_fixtures`.`end_time`
    , `jb_timetable_fixtures`.`teacher_id`
    , `jb_users`.`id` AS gr_number
    , `jb_users`.`name`
    , `jb_timetable_fixtures`.`date`
    , `jb_timetable_period_name`.`title` AS `period_title`
    , `staff_origin`.`name` AS origin_name
FROM
    `jb_timetable_fixtures`
    INNER JOIN `jb_users` 
        ON (`jb_timetable_fixtures`.`teacher_id` = `jb_users`.`id`)
    INNER JOIN `jb_timetable_period_name` 
        ON (`jb_timetable_fixtures`.`period_id` = `jb_timetable_period_name`.`id`)
    INNER JOIN `jb_users` AS `staff_origin`
        ON (`jb_timetable_fixtures`.`original_teacher_id` = `staff_origin`.`id`) WHERE 1";

        if (!empty($param['session'])) {
            $sql .= " AND `jb_timetable_fixtures`.`session_id` = " . $param['session'];
        }
        if (!empty($param['original_teacher'])) {
            $sql .= " AND `jb_timetable_fixtures`.`original_teacher_id` = " . $param['original_teacher'];
        }
        if (!empty($param['date'])) {
            $sql .= " AND `jb_timetable_fixtures`.`date` = '" . $param['date'] . "'";
        }


        return $this->getResults($sql);
    }

    public function removeStaffFixture($id)
    {
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr . "timetable_fixtures";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            $this->delete($table, $where, 1);
        }

    }

    public function availableTeacherForFixture($param = array())
    {
        $sql = "SELECT
    
    `jb_users`.`id`
    , `jb_users`.`name`
    
FROM
     `jb_staffs` 
WHERE 1 ";

        //$sql .= " AND `jb_staffs`.`company_id` = " . $this->getCompany();




        $session = !empty($param['session']) ? $param['session'] : "";
        $weekdayId = !empty($param['weekday']) ? $param['weekday'] : "";
        $startTime = !empty($param['start_time']) ? $param['start_time'] : "";
        $endTime = !empty($param['end_time']) ? $param['end_time'] : "";
        $date = !empty($param['date']) ? $param['date'] : "";


        $sql .= " AND id NOT IN
        (SELECT teacher_id FROM time_table_structure_view WHERE weekday_id = $weekdayId
         AND session_id = $session
         AND
        (
            `start_time` BETWEEN '$startTime' AND '$endTime'
            OR `end_time` BETWEEN '$startTime' AND '$endTime'
            OR '$startTime' BETWEEN `start_time` AND `end_time`
            OR '$endTime' BETWEEN `start_time` AND `end_time`
        ) )";

        $sql .= " AND id NOT IN (";
        $sql .= " SELECT teacher_id FROM jb_timetable_fixtures WHERE date = '$date'";
        $sql .= " AND session_id = $session";
        $sql .= " AND
        (
            `start_time` BETWEEN '$startTime' AND '$endTime'
            OR `end_time` BETWEEN '$startTime' AND '$endTime'
            OR '$startTime' BETWEEN `start_time` AND `end_time`
            OR '$endTime' BETWEEN `start_time` AND `end_time`
        )";
        $sql .= " )";


        $sql .= " GROUP BY id";




        return $this->getResults($sql);
    }


    public function getLessonPlans($param = array())
    {
        $sql = "SELECT
    `jb_lession_session`.`chapter`
    , `jb_lession_session`.`topic`
    , `jb_lession_session`.`subtopic`
    , `jb_lession_session`.`week_id`
    , `jb_lession_session`.`exam_id`
    , `jb_lession_session`.`id`
    , `jb_exam_names`.`title` AS `exam_title`
    , `jb_lession_session`.`subject_id`
    , `jb_subject_groups`.`title` AS `subject_title`
    , `jb_lession_session`.`session_id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_lession_session`.`branch_id`
    , `jb_lession_session`.`pdf_link`
    , `jb_branches`.`title` AS `branch_title`
FROM
    `jb_lession_session`
    INNER JOIN `jb_exam_names` 
        ON (`jb_lession_session`.`exam_id` = `jb_exam_names`.`id`)
    INNER JOIN `jb_subject_groups` 
        ON (`jb_lession_session`.`subject_id` = `jb_subject_groups`.`id`)
    INNER JOIN `jb_sessions` 
        ON (`jb_lession_session`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_lession_session`.`branch_id` = `jb_branches`.`id`) WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `jb_lession_session`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `jb_lession_session`.`session_id` = " . $param['session'];
        }

        if (!empty($param['exam'])) {
            $sql .= " AND `jb_lession_session`.`exam_id` = " . $param['exam'];
        }

        if (!empty($param['subject'])) {
            $sql .= " AND `jb_lession_session`.`subject_id` = " . $param['subject'];
        }


        return $this->getResults($sql);
    }



    public function getSessionWeek($key)
    {
        $arr = $this->getSessionWeeks();

        return $arr[$key];

    }

    public function getSessionWeeks()
    {
        $lang = $this->tool->getLang();


        if ($lang == "ur") {
            $arr[1] = 'پہلا ہفتہ';
            $arr[2] = 'دوسرا ہفتہ';
            $arr[3] = 'تیسرا ہفتہ';
            $arr[4] = 'چوتھا ہفتہ';
            $arr[5] = 'پانچواں ہفتہ';
            $arr[6] = 'چھٹا ہفتہ';
            $arr[7] = 'ساتواں ہفتہ';
            $arr[8] = 'آٹھواں ہفتہ';
            $arr[9] = 'نواں ہفتہ';
            $arr[10] = 'دسواں ہفتہ';
            $arr[11] = 'گیارھواں ہفتہ';
            $arr[12] = 'بارھواں ہفتہ';
            $arr[13] = 'تیرھواں ہفتہ';
            $arr[14] = 'چودھواں ہفتہ';
            $arr[15] = 'پندرھواں ہفتہ';
            $arr[16] = 'سولہواں ہفتہ';
            $arr[17] = 'سترہواں ہفتہ';
            $arr[18] = 'اٹھارہواں ہفتہ';
            $arr[19] = 'انیسواں ہفتہ';
            $arr[20] = 'بیسواں ہفتہ';
            $arr[21] = 'اکیسواں ہفتہ';
            $arr[22] = 'بائیسواں ہفتہ';
            $arr[23] = 'تیئسواں ہفتہ';
            $arr[24] = 'چوبیسواں ہفتہ';


        } else {
            $arr[1] = "1st week";
            $arr[2] = "2nd week";
            $arr[3] = "3rd week";
            $arr[4] = "4th week";
            $arr[5] = "5th week";
            $arr[6] = "6th week";
            $arr[7] = "7th week";
            $arr[8] = "8th week";
            $arr[9] = "9th week";
            $arr[10] = "10th week";
            $arr[11] = "11th week";
            $arr[12] = "12th week";
            $arr[13] = "13th week";
            $arr[14] = "14th week";
            $arr[15] = "15th week";
            $arr[16] = "16th week";
            $arr[17] = "17th week";
            $arr[18] = "18th week";
            $arr[19] = "19th week";
            $arr[20] = "20th week";
            $arr[21] = "21st week";
            $arr[22] = "22nd week";
            $arr[23] = "23rd week";
            $arr[24] = "24th week";

        }

        return $arr;

    }

    public function removeLessonPlan($id)
    {
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr . "lession_session";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            $this->delete($table, $where, 1);
        }

    }


    public function insertSessionLesson($data)
    {
        return $this->insert('jb_lession_session', $data);
    }

    public function insertClassHomeworks($data)
    {
        return $this->insert('jb_class_home_works', $data);
    }

    public function removeClassHomeworks($id)
    {
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr . "class_home_works";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            $this->delete($table, $where, 1);
        }

    }

    public function getTeacherForSubjects($subject)
    {
        $sql = "SELECT
        `jb_teacher_subjects`.`teacher_id` AS `id`
        , `jb_users`.`name` AS `title`
    FROM
        `jb_teacher_subjects`
        INNER JOIN `jb_users` 
            ON (`jb_teacher_subjects`.`teacher_id` = `jb_users`.`id`)
    WHERE (`jb_teacher_subjects`.`subject_id`  = $subject)";

        return $this->getResults($sql);
    }

}
