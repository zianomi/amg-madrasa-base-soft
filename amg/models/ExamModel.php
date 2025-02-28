<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 4/1/2017
 * Time: 8:10 PM
 */
class ExamModel extends BaseModel
{

    protected function getTableName()
    {
        $pr = $this->getPrefix();
        return $pr . "results";
    }

    public function examDateLogs($param = array())
    {

        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_exam_date_log`.`id`
            , `jb_branches`.`title` AS branch_title
            , `jb_branches`.`id` AS branch_id
             , `jb_classes`.`id` AS class_id
             , `jb_classes`.`title` AS class_title
             , `jb_sessions`.`title` AS session_title
             , `jb_sessions`.`start_date` AS session_start_date
             , `jb_sessions`.`end_date` AS session_end_date
             , `jb_exam_names`.`title` AS exam_title
            ,`jb_exam_date_log`.`exam_start_date`
            , `jb_exam_date_log`.`exam_end_date`
            , `jb_exam_date_log`.`attand_start_date`
            , `jb_exam_date_log`.`attand_end_date`
            , `jb_exam_date_log`.`year`
        FROM
            `jb_exam_date_log`
            INNER JOIN `jb_branches`
                ON (`jb_exam_date_log`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_classes`.`id` = `jb_exam_date_log`.`class_id`)
            INNER JOIN `jb_sessions`
                ON (`jb_exam_date_log`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_exam_names`
                ON (`jb_exam_date_log`.`exam_id` = `jb_exam_names`.`id`) WHERE 1 ";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "exam_date_log`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "exam_date_log`.`class_id` = " . $param['class'];
        }


        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "exam_date_log`.`session_id` = " . $param['session'];
        }


        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "exam_date_log`.`exam_id` = " . $param['exam'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "exam_date_log`.`id` = " . $param['id'];
        }


        return $this->getResults($sql);
    }

    public function getExamNames()
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "exam_names` WHERE published = 1 ORDER BY position ";
        $res = $this->get_results($sql);
        return $res;
    }

    public function examDateLogInsert($data = array())
    {

        $tableName = $this->getPrefix() . "exam_date_log";

        $columns = $this->getTableCols($tableName);
        unset($columns[0]);


        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function examSubjectInsert($dateLogid, $data = array())
    {
        $tableName = $this->getPrefix() . "exam_subjects";
        $tableSubject = $this->getPrefix() . "exam_subjects";
        $subjectWhereColumn = "exam_date_log_id";
        $whereSubject = array($subjectWhereColumn => $dateLogid);
        if (!empty($dateLogid)) {
            $this->delete($tableSubject, $whereSubject);
        }

        $ins = $this->insertBulk($tableName, $data, false);
        return $ins;
    }

    public function studentPublishedResults($id)
    {
        $sql = "SELECT
    `student_id`
    , `session_id`
    , `branch_id`
    , `class_id`
    , `section_id`
    , `exam_id`
FROM
    `jb_student_published_result`
WHERE (`student_id`  = $id);";

        return $this->getResults($sql);
    }

    public function getExamClasses($session, $branch, $exam)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `" . $pr . "classes`.`id`
            ,`" . $pr . "classes`.`title`
        FROM
            `" . $pr . "classes`
            INNER JOIN `" . $pr . "session_classes`
                ON (`" . $pr . "classes`.`id` = `" . $pr . "session_classes`.`class_id`) WHERE 1";

        $sql .= " AND `" . $pr . "session_classes`.`session_id` = $session";
        $sql .= " AND `" . $pr . "session_classes`.`branch_id` = $branch";
        $sql .= " AND `" . $pr . "classes`.`published`  = 1";

        $sql .= " AND `" . $pr . "classes`.`id` NOT IN";

        $sql .= " (SELECT class_id FROM " . $pr . "exam_date_log WHERE branch_id = $branch AND session_id = $session AND exam_id = $exam)";

        $sql .= " GROUP BY `" . $pr . "classes`.`id` ORDER BY `" . $pr . "classes`.`position` ASC";




        $res = $this->get_results($sql);
        return $res;
    }

    public function HideShowDropDown($name, $class = "", $val = "")
    {

        if ($val == 'hide') {
            $hide = 'selected="selected"';
        } else {
            $hide = '';
        }

        if ($val == 'show') {
            $show = 'selected="selected"';
        } else {
            $show = '';
        }

        $html = '<select name="' . $name . '" class="' . $class . '">
               <option value=""></option>
               <option value="hide" ' . $hide . '>Hide</option>
               <option value="show" ' . $show . '>Show</option>
           </select>';

        return $html;
    }

    public function insertClassAllNumbers($data = array())
    {

        $tableName = $this->getTableName();

        $columns = $this->getTableCols($tableName);

        unset($columns[0]);




        $ids = $this->insertMulti($tableName, $columns, $data);

        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }


    public function ExamIDNumbers($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_subjects`.`id`
            , `jb_subjects`.`title`
            , `jb_exam_subjects`.`subject_numbers` AS numbers
        FROM
            `jb_subjects`
            INNER JOIN `jb_exam_subjects`
                ON (`jb_subjects`.`id` = `jb_exam_subjects`.`subject_id`) WHERE 1 ";

        $sql .= " AND `jb_exam_subjects`.`exam_date_log_id` = " . $param['date_id'];

        $sql .= " AND `jb_subjects`.`id` NOT IN ";

        $sql .= " (";

        $sql .= "SELECT subject_id FROM jb_results WHERE 1 AND student_id = " . $param['stuid'];

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "results`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "results`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }

        //$sql .= " AND date = '" . $param['date'] . "'";

        $sql .= " AND `" . $pr . "results`.`exam_id` = " . $param['exam'];

        $sql .= ")";

        $sql .= " ORDER BY `jb_subjects`.`position` ASC";



        $res = $this->getResults($sql);

        return $res;
    }


    public function IdProgress($id)
    {
        $sql = "SELECT id, student_id, progress FROM " . PR . "idprogress WHERE student_id = $id";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function ResultProgress($lang = 'ur')
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM " . $pr . "exam_progress_bar WHERE lang = '$lang' ORDER BY id";
        $res = $this->getResults($sql);

        return $res;
    }


    public function insertProgress($data)
    {
        $table = $this->getPrefix() . "idprogress";

        $this->insert($table, $data);
    }

    public function updateProgress($id, $update)
    {
        $table = $this->getPrefix() . "idprogress";
        $update_where = array('id' => $id);
        $this->update($table, $update, $update_where, 1);
    }


    public function examAllClassStudents($param = array())
    {

        $pr = $this->getPrefix();

        $stuStatus = $this->stuStatus("current");

        $sql = "SELECT
        `" . $pr . "students`.`id`
        , `" . $pr . "students`.`name`
        , `" . $pr . "students`.`fname`
        , `" . $pr . "students`.`grnumber`
        FROM
        `" . $pr . "students` WHERE 1 ";


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

        $sql .= " AND `" . $pr . "students`.`student_status` = '$stuStatus'";



        return $this->getResults($sql);
    }


    public function examSubjects($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_exam_subjects`.`exam_date_log_id`
            , `jb_subjects`.`title`
            , `jb_exam_subjects`.`subject_id`
            , `jb_exam_subjects`.`subject_numbers` AS numbers
            
        FROM
            `jb_exam_subjects`
            INNER JOIN `jb_subjects`
                ON (`jb_exam_subjects`.`subject_id` = `jb_subjects`.`id`)
        WHERE 1 AND `exam_date_log_id` = $id
        ORDER BY `jb_subjects`.`position` ASC
        ";


        $res = $this->getResults($sql);


        return $res;
    }

    public function ExamSubjectNumbers($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT subject_id, student_id, numbers FROM jb_results WHERE 1 ";

        if (!empty($param['id'])) {
            $sql .= " AND student_id = " . $param['id'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "results`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "results`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }

        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "results`.`exam_id` = " . $param['exam'];
        }

        if (!empty($param['subject'])) {
            $sql .= " AND `" . $pr . "results`.`subject_id` = " . $param['subject'];
        }


        $res = $this->getResults($sql);

        return $res;
    }


    public function examMainSubjects($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_exam_subjects`.`exam_date_log_id`
            , `jb_subjects`.`title`
            , `jb_exam_subjects`.`subject_id`
            , `jb_subjects`.`numbers`
        FROM
            `jb_exam_subjects`
            INNER JOIN `jb_subjects`
                ON (`jb_exam_subjects`.`subject_id` = `jb_subjects`.`id`)
        WHERE 1 AND `exam_date_log_id` = $id
        ORDER BY `jb_subjects`.`position` ASC
        ";


        $res = $this->getResults($sql);


        return $res;
    }



    public function examSubject($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_subjects`.`id`
            , `jb_subjects`.`title`
            , `jb_exam_subjects`.`subject_numbers` AS numbers
        FROM
            `jb_subjects`
            INNER JOIN `jb_exam_subjects`
                ON (`jb_subjects`.`id` = `jb_exam_subjects`.`subject_id`) WHERE 1 ";

        $sql .= " AND `jb_subjects`.`subject_type` = 'exam'";
        $sql .= " AND `jb_exam_subjects`.`exam_date_log_id` = " . $param['date_id'];

        /*$sql .= " AND `jb_subjects`.`id` NOT IN ";

        $sql .= " (";

        $sql .= "SELECT subject_id FROM jb_results WHERE 1 ";

        if(!empty($param['branch'])){
            $sql .= " AND `".$pr."results`.`branch_id` = " . $param['branch'];
        }

        if(!empty($param['class'])){
            $sql .= " AND `".$pr."results`.`class_id` = " . $param['class'];
        }

        if(!empty($param['section'])){
            $sql .= " AND `".$pr."results`.`section_id` = " . $param['section'];
        }

        if(!empty($param['session'])){
            $sql .= " AND `".$pr."results`.`session_id` = " . $param['session'];
        }

        if(!empty($param['exam'])){
            $sql .= " AND `".$pr."results`.`exam_id` = " . $param['exam'];
        }

        $sql .=  ")";*/

        $res = $this->getResults($sql);

        return $res;
    }

    public function subjectNumberBySubjectId($id)
    {
        $sql = "SELECT numbers FROM jb_subjects WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res['numbers'];
    }



    public function examNames()
    {
        $sql = "SELECT id, title FROM jb_exam_names WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function ExamHeadings()
    {
        $heading_keys = array(
            'hifz' => "حفظ",
            "hifz_islamyat" => "اسلامیات",
            "qaida" => "قاعدہ",
            "qaida_islamyat" => "اسلامیات وخوشخطی",
            "roza_deenyat" => "دینیات",
            "roza_urdu" => "اردو",
            "roza_english" => "انگریزی",
            "roza_hisab" => "نمبر رائٹنگ+کلرنگ"
        );

        return $heading_keys;
    }

    public function SelectIDresult($id, $exam, $session)
    {
        $sql = "
            SELECT
                `jb_results`.`id`
                , `jb_results`.`student_id`
                , `jb_results`.`numbers` exam_numbers
                , `jb_results`.`date` exam_date
                , `jb_results`.`exam_id` exam_exam
                , `jb_results`.`section_id` section_id
                , `jb_results`.`class_id` class_id
                , `jb_results`.`branch_id` branch_id
                , `jb_results`.`session_id` session_id
                , `jb_subjects`.`id` exam_subjectid
                , `jb_subjects`.`title` subject_name
                , `jb_subjects`.`numbers` subject_number
                , `jb_subjects`.`subject_group` subject_group
                , `jb_subjects`.`compulsory_sub` main_sub
            FROM
                `jb_results`
                INNER JOIN `jb_subjects`
                    ON (`jb_results`.`subject_id` = `jb_subjects`.`id`) WHERE 1
            ";
        $sql .= "

                AND `jb_results`.`exam_id` IN ($exam)
                AND `jb_results`.session_id = $session
                AND `jb_results`.`student_id` = $id
            ";
        $sql .= " GROUP BY `jb_results`.`subject_id`, YEAR(`jb_results`.`date`), `jb_results`.`exam_id`, `jb_results`.`student_id`";

        //echo '<pre>'; print_r($sql); echo '</pre>';
        $res = $this->getResults($sql);
        return $res;
    }


    function RankFunction($exam, $session, $branch, $class, $section)
    {
        $pr = $this->getPrefix();

        $sql = "SELECT student_id exam_stuid, SUM(" . $pr . "results.numbers) AS marks_student,
              subject_group
             FROM " . $pr . "results INNER JOIN " . PR . "subjects ON " . $pr . "results.subject_id  = " . $pr . "subjects.id WHERE exam_id = $exam

             AND " . $pr . "results.session_id = $session
             AND " . $pr . "results.branch_id = $branch
             AND " . $pr . "results.class_id = $class
             AND " . $pr . "results.section_id = $section

             AND compulsory_sub = 0

            GROUP BY student_id ORDER BY marks_student DESC";


        $res = $this->getResults($sql);
        return $res;
    }


    public function getExamDateLog($param = array())
    {

        $sql = "SELECT * FROM `jb_exam_date_log` WHERE 1 ";

        if (!empty($param['exam'])) {
            $sql .= " AND exam_id = " . $param['exam'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND branch_id = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND class_id = " . $param['class'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND session_id = " . $param['session'];
        }

        $row = $this->getSingle($sql);
        return $row;
    }


    function numberBetween($number)
    {

        $number = floor($number);

        if (($number) >= 9500) {
            $htm = 'ممتاز مع الشرف';
        } elseif (($number) < 9500 && ($number) >= 9000) {
            $htm = 'ممتاز';
        } elseif ($number >= 8500 && $number < 9000) {
            $htm = 'جید جدا';
        } elseif (($number) < 8500 && ($number) >= 8000) {
            $htm = 'جید';
        } elseif (($number) < 8000 && ($number) >= 7500) {
            $htm = 'مقبول';
        } elseif (($number) < 7500) {
            $htm = 'راسب';
        }

        return $htm;
    }

    function KefyatBetween($number)
    {

        $number = floor($number);

        if (($number) >= 9500) {
            $htm = 'ماشاء اللہ بہترین ہیں';
        } elseif (($number) < 9500 && ($number) >= 9000) {
            $htm = 'بہتر ہیں محنت جاری رکھیں';
        } elseif ($number >= 8500 && $number < 9000) {
            $htm = 'بہتر ہیں مزید محنت کی ضرورت ہے';
        } elseif (($number) < 8500 && ($number) >= 8000) {
            $htm = 'متوسط!  مزید محنت کی ضرورت ہے';
        } elseif (($number) < 8000 && ($number) >= 7500) {
            $htm = ' توجہ طلب!  سخت محنت کی ضرورت ہے';
        } elseif (($number) < 7500) {
            $htm = 'سخت محنت و توجہ کی ضرورت ہے';
        }

        return $htm;
    }


    function GetKefyatId($number)
    {

        $number = floor($number);

        if (($number) >= 9500) {
            $htm = 1;
        } elseif (($number) < 9500 && ($number) >= 9000) {
            $htm = 2;
        } elseif ($number >= 8500 && $number < 9000) {
            $htm = 3;
        } elseif (($number) < 8500 && ($number) >= 8000) {
            $htm = 4;
        } elseif (($number) < 8000 && ($number) >= 7500) {
            $htm = 5;
        } elseif (($number) < 7500) {
            $htm = 5;
        }

        return $htm;
    }


    public function updateNumber($id, $number)
    {

        $table = $this->getPrefix() . "results";

        $update = array('numbers' => $number);
        $update_where = array('id' => $id);


        if ($this->update($table, $update, $update_where, 1)) {
            return true;
        }
        return $this->getError();
    }

    function handelFloat($floatVal)
    {
        return str_replace(".", "", $floatVal);
    }

    public function getSubjectGroups($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `jb_subject_groups` WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "subject_groups`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "subject_groups`.`class_id` = " . $param['class'];
        }



        /*if(!empty($param['exam'])){
            $sql .= " AND `".$pr."subject_groups`.`exam_id` = " . $param['exam'];
        }

        echo '<pre>'; print_r($sql); echo '</pre>';*/

        $res = $this->getResults($sql);

        return $res;
    }

    public function result($param = array())
    {
        $pr = $this->getPrefix();

        $sql = "SELECT
            `jb_results`.`numbers`
             , `jb_results`.`subject_id`
            , `jb_branches`.`title` AS `branch_title`
            , `jb_classes`.`title` AS `class_title`
            , `jb_sections`.`title` AS `section_title`
            , `jb_sessions`.`title` AS `session_title`
            , `jb_exam_names`.`title` AS `exam_title`
            , `jb_subjects`.`title` AS `subject_title`
            , `jb_results`.`subject_numbers` AS `subject_numbers`
            , `jb_subjects`.`subject_group_id`
            , `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_results`.`numbers` AS `student_numbers`
            , `jb_results`.`id` AS `row_id`
        FROM
            `jb_results`
            INNER JOIN `jb_branches`
                ON (`jb_results`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_results`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_results`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_results`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_exam_names`
                ON (`jb_results`.`exam_id` = `jb_exam_names`.`id`)
            INNER JOIN `jb_subjects`
                ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
            INNER JOIN `jb_students`
                ON (`jb_results`.`student_id` = `jb_students`.`id`) WHERE 1

        ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "results`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "results`.`section_id` = " . $param['section'];
        }


        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }


        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "results`.`exam_id` = " . $param['exam'];
        }


        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "results`.`student_id` = " . $param['id'];
        }

        $sql .= " GROUP BY `" . $pr . "results`.`student_id`, `" . $pr . "results`.`subject_id`";


        $sql .= " ORDER BY `jb_subjects`.`position`, `jb_students`.`id` ASC";


        $res = $this->getResults($sql);

        return $res;
    }

    public function examSummary($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            SUM(`jb_results`.`numbers`) AS `obtain_numbers`
            , `jb_results`.`branch_id`
            , `jb_results`.`class_id`
            , `jb_branches`.`title` branch_title
            , `jb_classes`.`title` class_title
            , SUM(`jb_subjects`.`numbers`) AS `total_numbers`
        FROM
            `jb_subjects`,
            `jb_results`
            INNER JOIN `jb_branches`
                ON (`jb_results`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_results`.`class_id` = `jb_classes`.`id`)";

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }


        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "results`.`exam_id` = " . $param['exam'];
        }
        $sql .= " GROUP BY `" . $pr . "results`.`student_id`";

        $res = $this->getResults($sql);

        return $res;
    }

    public function examSummaryData($session, $exam)
    {
        $sql = "SELECT t.branch_id, b.title,
        SUM(CASE WHEN percent >= 95 THEN 1 ELSE 0 END) `mumtaz_ma_sharf`,
        SUM(CASE WHEN percent < 95 AND percent >= 90 THEN 1 ELSE 0 END) `mumtaz`,
        SUM(CASE WHEN percent < 90 AND percent >= 85 THEN 1 ELSE 0 END) `jayyad_jiddan`,
        SUM(CASE WHEN percent < 85 AND percent >= 80 THEN 1 ELSE 0 END) `jayyad`,
        SUM(CASE WHEN percent < 80 AND percent >= 75 THEN 1 ELSE 0 END) `maqbool`,
        SUM(CASE WHEN percent < 75 THEN 1 ELSE 0 END) `rasib`,
        COUNT(t.branch_id) AS tot
         FROM (
        SELECT
        student_id,branch_id,
            (SUM(`jb_results`.`numbers`)/SUM(`jb_subjects`.`numbers`)) * 100 percent
        FROM
            `jb_results`
            INNER JOIN `jb_subjects`
                ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
        WHERE (`jb_results`.`session_id` = $session
            AND `jb_results`.`exam_id` = $exam)
          GROUP BY student_id
          ) t
          INNER JOIN jb_branch_short_names b ON t.branch_id = b.branch_id AND b.lang_id = " . $this->getLangId() . "
         GROUP BY t.branch_id";

        $res = $this->getResults($sql);

        return $res;
    }



    public function CheckRankCriteria($template, $rank, $totalStudentinClass, $percentage)
    {
        $rankName = '';


        if ($template == 'roza') {

            if ($percentage >= 7500) {
                if ($rank == 1) {
                    $rankName = '<span class="fonts">اول</span>';
                } elseif ($rank == 2) {
                    $rankName = '<span class="fonts">دوم</span>';
                } elseif ($rank == 3) {
                    $rankName = '<span class="fonts">سوم</span>';
                } else {
                    if ($rank > $totalStudentinClass) {
                        //$rankName = $totalStudentinClass;
                        $rankName = $totalStudentinClass . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                    } else {
                        //$rankName = $rank;
                        $rankName = $rank . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                    }
                }
            } else {
                $rankName = '-';
            }
        } else {

            if ($percentage >= 7500) {
                if ($rank == 1) {
                    if ($percentage >= 9500) {
                        $rankName = '<span class="fonts">اول</span>';
                    } else {
                        $rankName = $rank . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                        ;
                    }
                } elseif ($rank == 2) {
                    if ($percentage >= 9500) {
                        $rankName = '<span class="fonts">دوم</span>';
                    } else {
                        $rankName = $rank . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                        ;
                    }
                } elseif ($rank == 3) {
                    if ($percentage >= 9500) {
                        $rankName = '<span class="fonts">سوم</span>';
                    } else {
                        $rankName = $rank . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                        ;
                    }
                } else {
                    if ($rank > $totalStudentinClass) {
                        //$rankName = $totalStudentinClass;
                        $rankName = $totalStudentinClass . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                    } else {
                        //$rankName = $rank;
                        $rankName = $rank . '<span style="vertical-align: text-top; font-size:10px;">th</span>/' . $totalStudentinClass;
                    }
                }
            } else {
                $rankName = '-';
            }
        }
        return $rankName;
    }





    public function getClassSubjectsByClassId($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT id, title, class_id FROM " . $pr . "subjects WHERE 1 AND published = 1 AND subject_type = 'exam' AND class_id = $id ORDER BY position ASC";
        $res = $this->getResults($sql);
        echo '<pre>';
        print_r($sql);
        echo '</pre>';
        return $res;
    }


    public function getExamClassSubjectsByClassId($id, $branch)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT id, title, class_id FROM " . $pr . "subjects WHERE 1 ";

        $sql .= " AND published = 1 AND class_id = $id AND subject_type = 'exam'";
        $sql .= " AND branch_id = $branch";

        $sql .= " ORDER BY position ASC";



        $res = $this->getResults($sql);
        return $res;
    }

    public function studentExamSyllabus($param = array())
    {
        $sql = "SELECT * FROM jb_exam_syllabus_students WHERE 1";
        if (!empty($param['student'])) {
            $student = $param['student'];
            $sql .= " AND student_id = $student";
        }
        if (!empty($param['branch'])) {
            $branch = $param['branch'];
            $sql .= " AND branch_id = $branch";
        }
        if (!empty($param['class'])) {
            $class = $param['class'];
            $sql .= " AND class_id = $class";
        }
        if (!empty($param['section'])) {
            $section = $param['section'];
            $sql .= " AND section_id = $section";
        }

        if (!empty($param['session'])) {
            $session = $param['session'];
            $sql .= " AND session_id = $session";
        }

        if (!empty($param['exam'])) {
            $exam = $param['exam'];
            $sql .= " AND exam_id = $exam";
        }
        $res = $this->getResults($sql);
        return $res;
    }


    public function insertExamSyllabus($data)
    {
        $table = $this->getPrefix() . "exam_syllabus_students";
        $this->insert($table, $this->setInsert($data));
    }

    public function updateExamSyllabus($id, $update)
    {
        $table = $this->getPrefix() . "exam_syllabus_students";
        $update_where = array('id' => $id);
        $this->update($table, $update, $update_where, 1);
    }


    public function insertClassAllSyllabus($data = array())
    {

        $tableName = $this->getPrefix() . "exam_syllabus_students";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }


    public function deleteExamData($where)
    {
        //$where = array( 'branch_id' => $param['branch'], 'class_id' => $param['class'], 'section_id' => $param['section'], 'session_id' => $param['session'], 'exam_id' => $param['exam']);
        //$this->delete( 'jb_exam_syllabus_students', $where);
        return $this->delete('jb_results', $where);
    }

    public function ClassResultStudents($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_results`.`student_id`
            , `jb_students`.`name`
            , `jb_students`.`fname`
            , `jb_students`.`gender`
            , `jb_students`.`grnumber`
            , `jb_results`.`branch_id`
            , `jb_results`.`class_id`
            , `jb_results`.`section_id`
            , `jb_results`.`session_id`
        FROM
            `jb_results`
            INNER JOIN `jb_students`
                ON (`jb_results`.`student_id` = `jb_students`.`id`)
        WHERE 1
        ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "results`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "results`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }

        $sql .= " GROUP BY `jb_results`.`student_id`";

        $res = $this->getResults($sql);
        return $res;
    }


    public function getHifzProgressRes($student_id, $session_id, $exam_id)
    {
        $sql = "SELECT
            SUM(`jb_subjects`.`numbers`) AS `subject_numbers`
            , SUM(`jb_results`.`numbers`) AS `obtain_numbers`
            , `jb_results`.`session_id`
            , `jb_results`.`exam_id`
            , `jb_branches`.`title`
            , `jb_results`.`branch_id`
        FROM
            `jb_results`
            INNER JOIN `jb_subjects`
                ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_results`.`branch_id` = `jb_branches`.`id`)
        WHERE (`jb_results`.`student_id` = $student_id
            AND `jb_results`.`class_id` =4
            AND `jb_results`.`session_id` = $session_id
            AND `jb_results`.`exam_id` = $exam_id
            AND `jb_subjects`.`compulsory_sub` = 0
            AND `jb_subjects`.`subject_type` = 1
            )
        GROUP BY `jb_results`.`session_id`, `jb_results`.`exam_id`
        ORDER BY `jb_results`.`session_id` DESC
        LIMIT 1
        ";

        $res = $this->getSingle($sql);
        return $res;
    }




    /////////////////////////////////////////////////////////////////////////////////////////////
    function GetIdSyllabus($id, $examname, $date, $to_date)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT id, required, current FROM " . $pr . "exam_syllabus_students WHERE student_id = $id AND exam_id = $examname ";
        $sql .= " AND date BETWEEN '$date' AND '$to_date' LIMIT 1";


        $res = $this->getSingle($sql);
        return $res;
    }

    /* function GetExamLabel($class,$exam_id,$lang = 'ur'){
            $pr = $this->getPrefix();
            $sql = "SELECT * FROM ".$pr."exam_labels WHERE 1 AND lang = '$lang'
             AND class_id = $class AND exam_id = $exam_id LIMIT 1
            ";
            $res = $this->getSingle($sql);
            return $res;
        }*/

    /////////////////////////////////////////////////////////////////////////////////////////////


    public function removeDateLog($id)
    {
        $whereColumn = "id";
        $table = "jb_exam_date_log";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            return $this->delete($table, $where, 1);
        }
    }

    public function removeResult($id)
    {
        $whereColumn = "id";
        $table = "jb_results";

        $where = array($whereColumn => $id);

        if (!empty($id)) {
            return $this->delete($table, $where, 1);
        }
    }


    public function GetStudentExamDetail($param = array())
    {
        $sql = "SELECT
    `jb_results`.`student_id`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
    , `jb_results`.`branch_id`
    , `jb_results`.`class_id`
    , `jb_results`.`section_id`
    , `jb_results`.`session_id`
FROM
    `jb_results`
    INNER JOIN `jb_branches`
        ON (`jb_results`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes`
        ON (`jb_results`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sessions`
        ON (`jb_results`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_sections`
        ON (`jb_results`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_students`
        ON (`jb_results`.`student_id` = `jb_students`.`id`)
WHERE 1";

        if (!empty($param['student'])) {
            $sql .= " AND `jb_results`.`student_id`  = " . $param['student'];
        }
        if (!empty($param['session'])) {
            $sql .= " AND `jb_results`.`session_id`  = " . $param['session'];
        }
        if (!empty($param['exam'])) {
            $sql .= " AND `jb_results`.`exam_id`  = " . $param['exam'];
        }

        $sql .= " LIMIT 1";

        $res = $this->getSingle($sql);

        return $res;
    }


    public function getFormulaName($id)
    {
        $sql = "SELECT title
FROM jb_exam_grade_formulas WHERE 1 AND id = $id";
        $res = $this->getSingle($sql);

        //$examNames = $this->getExamNames($res['branch_id']);
        return $res['title'];
    }

    public function getGradeFormulas()
    {
        $sql = "SELECT *
FROM jb_exam_grade_formulas WHERE 1";
        $res = $this->getResults($sql);

        return $res;
    }

    public function getClassFormulas($branch)
    {
        $sql = "SELECT *
FROM jb_exam_class_formula WHERE 1 AND branch_id = $branch";
        $res = $this->getResults($sql);

        return $res;
    }

    public function insertClassFormula($data = array())
    {

        $tableName = $this->getPrefix() . "exam_class_formula";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data, false);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function deleteGradeFormula($branch)
    {
        $where = array('branch_id' => $branch);
        $this->delete('jb_exam_class_formula', $where);
    }

    public function examNumberInput($name, $maxNumbers, $value = 0)
    {
        $html = '<input type="number"';
        $html .= ' placeholder="0.00"';
        $html .= ' required';
        $html .= ' name=';
        $html .= $name;
        $html .= ' min="0"';
        $html .= ' onkeyup="';
        $html .= 'CheckValue(this,';
        $html .= $maxNumbers;
        $html .= ')"';
        $html .= ' value="' . $value . '"';
        $html .= ' step="0.01"';
        $html .= ' title="Numbers"';
        $html .= ' pattern="^\d+(?:\.\d{1,2})?$"';
        $html .= ' onblur="this.parentNode.parentNode.style.backgroundColor=';
        $html .= '/^\d+(?:\.\d{1,2})?$/.test(this.value)?';
        $html .= '\'inherit\':';
        $html .= '\'red\'';
        $html .= '">';

        return $html;
    }


    public function insertPublishedResult($data = array())
    {


        $tableName = $this->getPrefix() . "student_published_result";

        $columns = $this->getTableCols($tableName);
        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $ids . " inserted");
        }
        return $return;
    }


    public function removePublishedResult($session, $branch, $class, $section, $exam)
    {
        $tableName = $this->getPrefix() . "student_published_result";

        $where['session_id'] = $session;
        $where['branch_id'] = $branch;
        $where['class_id'] = $class;
        $where['section_id'] = $section;
        $where['exam_id'] = $exam;




        return $this->delete($tableName, $where);
    }

    public function insertUnPublished()
    {
        $sql = "SELECT student_id, branch_id, class_id, section_id, session_id, exam_id FROM jb_results WHERE 1";
        $sql .= " GROUP BY student_id, session_id, exam_id";
        $res = $this->getResults($sql);

        $user = $this->getUserId();
        $created = date("Y-m-d H:i:s");
        $i = 0;

        foreach ($res as $row) {
            $i++;
            $student = $row['student_id'];
            $branch = $row['branch_id'];
            $class = $row['class_id'];
            $section = $row['section_id'];
            $session = $row['session_id'];
            $exam = $row['exam_id'];
            $ins = "INSERT IGNORE INTO `jb_student_published_result` (
  `id`,
  `student_id`,
  `session_id`,
  `branch_id`,
  `class_id`,
  `section_id`,
  `exam_id`,
  `created_user_id`,
  `created`) 
VALUES
  (
    NULL,
    '$student',
    '$session',
    '$branch',
    '$class',
    '$section',
    '$exam',
    '$user',
    '$created'
  ) ;

";

            echo '<pre>';
            print_r($ins);
            echo '</pre>';
        }
    }

    public function getPublishedResult($param = array())
    {
        $tableName = $this->getPrefix() . "student_published_result";
        $sql = "SELECT student_id FROM $tableName WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND branch_id = " . $param['branch'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND session_id = " . $param['session'];
        }

        if (!empty($param['exam'])) {
            $sql .= " AND exam_id = " . $param['exam'];
        }



        return $this->getResults($sql);
    }


    public function getExamSubjects($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_results`.`branch_id`
    , `jb_results`.`class_id`
    , `jb_results`.`section_id`
    , `jb_results`.`session_id`
    , `jb_results`.`exam_id`
    , `jb_results`.`subject_id`
    , `jb_subjects`.`title`
FROM
    `jb_results`
    INNER JOIN `jb_subjects` 
        ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
 WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "results`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "results`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }

        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "results`.`exam_id` = " . $param['exam'];
        }


        if (!empty($param['subject_type'])) {
            $sql .= " AND `" . $pr . "subjects`.`subject_type` = '" . $param['subject_type'] . "'";
        }


        $sql .= " GROUP BY `jb_results`.`subject_id`";




        $res = $this->getResults($sql);
        return $res;
    }

    public function subjectNumbersById($dateLogId)
    {
        $sql = "SELECT subject_id, subject_numbers FROM jb_exam_subjects WHERE 1 AND exam_date_log_id = $dateLogId";
        $res = $this->getResults($sql);
        $rows = array();
        foreach ($res as $row) {
            $rows[$row['subject_id']] = $row['subject_numbers'];
        }

        return $rows;
    }

    public function subjectExamNumberBySubjectId($subject_id, $examId)
    {
        $sql = "SELECT subject_numbers AS numbers FROM jb_exam_subjects
INNER JOIN jb_exam_date_log edl on jb_exam_subjects.exam_date_log_id = edl.id
WHERE jb_exam_subjects.subject_id = $subject_id AND edl.exam_id = $examId
";

        $res = $this->getSingle($sql);
        return $res['numbers'];
    }


    public function examSubjectsByClassBranch($session, $exam, $branch, $class)
    {

        /*$sql = "SELECT
            `jb_exam_subjects`.`exam_date_log_id`
            , `jb_subjects`.`title`
            , `jb_exam_subjects`.`subject_id`
            , `jb_exam_subjects`.`subject_numbers` AS numbers

        FROM
            `jb_exam_subjects`
            INNER JOIN `jb_subjects`
                ON (`jb_exam_subjects`.`subject_id` = `jb_subjects`.`id`)
        WHERE 1 AND `jb_subjects`.`branch_id` = $branch
        AND `jb_subjects`.`class_id` = $class
        ";*/

        $sql = "SELECT 
        `jb_exam_subjects`.`exam_date_log_id`
            , `jb_subjects`.`title`
            , `jb_exam_subjects`.`subject_id`
            , `jb_exam_subjects`.`subject_numbers` AS numbers
            FROM
            `jb_exam_subjects`
            INNER JOIN `jb_exam_date_log` ON 
        `jb_exam_subjects`.`exam_date_log_id` = jb_exam_date_log.id
        INNER JOIN `jb_subjects`
                ON (`jb_exam_subjects`.`subject_id` = `jb_subjects`.`id`)
        WHERE 1 AND `jb_exam_date_log`.`branch_id` = $branch
        AND `jb_exam_date_log`.`class_id` = $class
        AND `jb_exam_date_log`.`session_id` = $session
        AND `jb_exam_date_log`.`exam_id` = $exam
        ";


        $res = $this->getResults($sql);
        $rows = array();
        foreach ($res as $row) {
            $rows[$row['subject_id']] = $row['numbers'];
        }

        return $rows;
    }

    public function updateNumberAsPerLog($session, $exam)
    {
        $sql = "UPDATE jb_results res JOIN jb_exam_subjects es 
ON res.subject_id = es.subject_id
JOIN jb_exam_date_log log
ON es.exam_date_log_id = log.id AND es.subject_id = res.subject_id
 SET res.subject_numbers = es.subject_numbers
 WHERE log.session_id = $session
 AND log.exam_id = $exam
 AND res.session_id = $session
 AND res.exam_id = $exam";

        return $this->link->query($sql);
    }

    public function getNumbersForTransfer($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_results`.`student_id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
FROM
    `jb_results`
    INNER JOIN `jb_students` 
        ON (`jb_results`.`student_id` = `jb_students`.`id`) WHERE 1";

        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "results`.`exam_id` = " . $param['exam'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "results`.`session_id` = " . $param['session'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "results`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "results`.`section_id` = " . $param['section'];
        }

        return $this->getResults($sql);
    }

    public function transferNumbers($param = array())
    {

        if (empty($param)) {
            return false;
        }

        if (empty($param['exam'])) {
            return false;
        }

        if (empty($param['session'])) {
            return false;
        }

        if (empty($param['branch'])) {
            return false;
        }

        if (empty($param['class'])) {
            return false;
        }

        if (empty($param['section'])) {
            return false;
        }

        if (empty($param['old_exam'])) {
            return false;
        }

        if (empty($param['old_session'])) {
            return false;
        }

        if (empty($param['old_branch'])) {
            return false;
        }

        if (empty($param['old_class'])) {
            return false;
        }

        if (empty($param['old_section'])) {
            return false;
        }




        if (empty($param['ids'])) {
            return false;
        }

        if (!is_array($param['ids'])) {
            return false;
        }

        $exam = $param['exam'];
        $old_exam = $param['old_exam'];
        $session = $param['session'];
        $old_session = $param['old_session'];
        $branch = $param['branch'];
        $old_branch = $param['old_branch'];
        $class = $param['class'];
        $old_class = $param['old_class'];
        $section = $param['section'];
        $old_section = $param['old_section'];

        $sql = "UPDATE `jb_results` SET ";
        $sql .= " `branch_id` = $branch";
        $sql .= " ,`class_id` = $class";
        $sql .= " ,`section_id` = $section";
        $sql .= " ,`session_id` = $session";
        $sql .= " ,`exam_id` = $exam";

        $sql .= " WHERE `exam_id` = $old_exam";
        $sql .= " AND `session_id` = $old_session";
        $sql .= " AND `branch_id` = $old_branch";
        $sql .= " AND `class_id` = $old_class";
        $sql .= " AND `section_id` = $old_section";
        $sql .= " AND `student_id` IN ( " . implode(",", $param['ids']) . ")";

        return $this->link->query($sql);
    }

    public function examPapers($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_session_papers`.`id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_exam_names`.`title` AS `exam_title`
    , `jb_session_papers`.`subject`
    , `jb_session_papers`.`paper_link`
FROM
    `jb_session_papers`
    INNER JOIN `jb_sessions` 
        ON (`jb_session_papers`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_session_papers`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_exam_names` 
        ON (`jb_session_papers`.`exam_id` = `jb_exam_names`.`id`)
     WHERE 1";


        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "session_papers`.`session_id` = " . $param['session'];
        }


        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "session_papers`.`class_id` = " . $param['class'];
        }


        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "session_papers`.`exam_id` = " . $param['exam'];
        }

        if (!empty($param['subject'])) {
            $sql .= " AND `" . $pr . "session_papers`.`subject_id` = " . $param['subject'];
        }


        return $this->getResults($sql);
    }

    public function examSyllabus($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_session_syllabus`.`id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_exam_names`.`title` AS `exam_title`
    , `jb_session_syllabus`.`subject`
    , `jb_session_syllabus`.`paper_link`
FROM
    `jb_session_syllabus`
    INNER JOIN `jb_sessions` 
        ON (`jb_session_syllabus`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_session_syllabus`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_exam_names` 
        ON (`jb_session_syllabus`.`exam_id` = `jb_exam_names`.`id`)
     WHERE 1";


        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "session_syllabus`.`session_id` = " . $param['session'];
        }


        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "session_syllabus`.`class_id` = " . $param['class'];
        }


        if (!empty($param['exam'])) {
            $sql .= " AND `" . $pr . "session_syllabus`.`exam_id` = " . $param['exam'];
        }

        if (!empty($param['subject'])) {
            $sql .= " AND `" . $pr . "session_syllabus`.`subject_id` = " . $param['subject'];
        }

        return $this->getResults($sql);
    }

    public function policies($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_session_policies`.`id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_session_policies`.`paper_link`
    , `jb_session_policies`.`subject`
    , `jb_session_policies`.`date`
FROM
    `jb_session_policies`
    INNER JOIN `jb_sessions` 
        ON (`jb_session_policies`.`session_id` = `jb_sessions`.`id`)
     WHERE 1";


        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "session_policies`.`session_id` = " . $param['session'];
        }

        return $this->getResults($sql);
    }


    public function schedules($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_session_schedules`.`id`
    , `jb_sessions`.`title` AS `session_title`
    , `jb_session_schedules`.`paper_link`
    , `jb_session_schedules`.`subject`
    , `jb_session_schedules`.`date`
FROM
    `jb_session_schedules`
    INNER JOIN `jb_sessions` 
        ON (`jb_session_schedules`.`session_id` = `jb_sessions`.`id`)
     WHERE 1";


        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "session_schedules`.`session_id` = " . $param['session'];
        }

        return $this->getResults($sql);
    }

    public function insertPaper($data)
    {
        $table = $this->getPrefix() . "session_papers";

        return $this->insert($table, $data);
    }

    public function insertSyllabus($data)
    {
        $table = $this->getPrefix() . "session_syllabus";

        return $this->insert($table, $data);
    }

    public function insertPolicy($data)
    {
        $table = $this->getPrefix() . "session_policies";

        return $this->insert($table, $data);
    }

    public function insertSchedule($data)
    {
        $table = $this->getPrefix() . "session_schedules";

        return $this->insert($table, $data);
    }

    public function getPaper($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "session_papers` WHERE id = $id";
        return $this->getSingle($sql);
    }

    public function getPlanRow($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "lession_session` WHERE id = $id";
        return $this->getSingle($sql);
    }

    public function getSyllabus($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "session_syllabus` WHERE id = $id";
        return $this->getSingle($sql);
    }

    public function getPolicy($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "session_policies` WHERE id = $id";
        return $this->getSingle($sql);
    }

    public function getSchedule($id)
    {
        $pr = $this->getPrefix();
        $sql = "SELECT * FROM `" . $pr . "session_schedules` WHERE id = $id";
        return $this->getSingle($sql);
    }

    public function deletePaper($id)
    {

        $where = array('id' => $id);
        $this->delete("jb_session_papers", $where, 1);
    }

    public function deleteSyllabus($id)
    {

        $where = array('id' => $id);
        $this->delete("jb_session_syllabus", $where, 1);
    }

    public function deletePolicy($id)
    {

        $where = array('id' => $id);
        $this->delete("jb_session_policies", $where, 1);
    }

    public function deleteSchedule($id)
    {

        $where = array('id' => $id);
        $this->delete("jb_session_schedules", $where, 1);
    }

    public function getReportSubjects()
    {
        $sql = "SELECT * FROM `jb_subject_reports` WHERE published = 1";
        return $this->getResults($sql);
    }

    public function getSubjectReport($param = array())
    {
        $sql = "SELECT
    SUM(`jb_results`.`subject_numbers`) AS `total_subject_numbers`
    , SUM(`jb_results`.`numbers`) AS `total_obtain_numbers`
    , `jb_results`.`section_id`
    , `jb_subjects`.`report_subject_id`
    , `jb_results`.`exam_id`
    , `jb_results`.`class_id`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
FROM
    `jb_results`
    INNER JOIN `jb_subjects` 
        ON (`jb_results`.`subject_id` = `jb_subjects`.`id`)
    
    INNER JOIN `jb_classes` 
        ON (`jb_results`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_results`.`section_id` = `jb_sections`.`id`)
WHERE 1
";
        if (!empty($param['session'])) {
            $sql .= " AND `jb_results`.`session_id` = " . $param['session'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `jb_results`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['exam'])) {
            //$sql .= " AND `jb_results`.`exam_id` IN (" . implode(",", $param['exam']) . ")";

            $sql .= " AND `jb_results`.`exam_id` = " . $param['exam'];
        }

        //$sql .= " GROUP BY `jb_results`.`exam_id`, `jb_results`.`class_id`, `jb_subjects`.`report_subject_id` ";
        $sql .= " GROUP BY `jb_results`.`exam_id`, `jb_results`.`class_id`, `jb_results`.`section_id`, `jb_subjects`.`report_subject_id` ";
        $sql .= " ORDER BY jb_classes.position, jb_sections.position ASC ";

        //echo '<pre>'; print_r($sql); echo '</pre>';
        return $this->getResults($sql);
    }


    public function getExamPercentages($param = array())
    {

        if (empty($param['exam'])) {
            return array();
        }

        if (empty($param['session'])) {
            return array();
        }



        $exam = $param['exam'];
        $session = $param['session'];


        $sql = "SELECT
branch_id,
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 1 AND 30 THEN 1 ELSE 0 END) AS '1_10',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 31 AND 40 THEN 1 ELSE 0 END) AS '31_40',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 41 AND 50 THEN 1 ELSE 0 END) AS '41_50',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 51 AND 60 THEN 1 ELSE 0 END) AS '51_60',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 61 AND 70 THEN 1 ELSE 0 END) AS '61_70',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 71 AND 80 THEN 1 ELSE 0 END) AS '71_80',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 81 AND 90 THEN 1 ELSE 0 END) AS '81_90',
SUM(CASE WHEN ((numbers / subject_numbers) * 100) BETWEEN 91 AND 100 THEN 1 ELSE 0 END) AS '91_100'
FROM (
SELECT DISTINCT student_id, branch_id, numbers, subject_numbers
FROM jb_results
WHERE session_id = $session AND exam_id = $exam
) AS distinct_results
GROUP BY branch_id;";



        $sql = "SELECT branch_id, (numbers / subject_numbers) * 100 AS percentage 
        FROM jb_results 
        WHERE session_id = $session AND exam_id = $exam 
        GROUP BY branch_id, student_id;
        ";



        return $this->getResults($sql);
    }

    public function getBranchNames()
    {
        $sql = "SELECT id, title FROM jb_branches";
        $data = array();
        $res = $this->getResults($sql);
        foreach ($res as $row) {
            $data[$row["id"]] = array("id" => $row["id"], "title" => $row['title']);
        }
        return $data;
    }

    public function getParentSubs($param = array())
    {
        $sql = "SELECT id, title FROM jb_subject_groups WHERE published = 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `jb_subject_groups`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `jb_subject_groups`.`class_id` = " . $param['class'];
        }


        return $this->getResults($sql);

    }
}
