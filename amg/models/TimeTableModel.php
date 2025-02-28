<?php

class TimeTableModel extends BaseModel
{


    public function getTimeTableTable($param = array()){
        $sql = "SELECT
    `jb_timetable_structure`.`start_time`
    , `jb_timetable_structure`.`end_time`
    , `jb_timetable_structure`.`weekday_id`
    , `jb_timetable_structure`.`period_name_id`
    , `jb_timetable_structure`.`timetable_id`
    , `jb_timetable_structure`.`id` AS structure_id
    , `jb_timetable_week_days`.`title` AS `week_day_title`
    , `jb_timetable_period_name`.`title` AS `period_name_title`
FROM
    `jb_timetable_structure`
    INNER JOIN `jb_timetable_class_sections` 
        ON (`jb_timetable_structure`.`timetable_id` = `jb_timetable_class_sections`.`timetable_id`)
    INNER JOIN `jb_timetable_week_days` 
        ON (`jb_timetable_structure`.`weekday_id` = `jb_timetable_week_days`.`id`)
    INNER JOIN `jb_timetable_period_name` 
        ON (`jb_timetable_structure`.`period_name_id` = `jb_timetable_period_name`.`id`)";

        $sql .= " WHERE 1";


        /*if(!empty($param['branch'])){
            $sql .= " AND `jb_timetable_structure`.`company_id` = " . $this->getCompany();
        }*/

        if(!empty($param['branch'])){
            $sql .= " AND `jb_timetable_class_sections`.`branch_id` = " . $param['branch'];
        }


        if(!empty($param['session'])){
            $sql .= " AND `jb_timetable_class_sections`.`session_id` = " . $param['session'];
        }


        if(!empty($param['class'])){
            $sql .= " AND `jb_timetable_class_sections`.`class_id` = " . $param['class'];
        }



        if(!empty($param['section'])){
            $sql .= " AND `jb_timetable_class_sections`.`section_id` = " . $param['section'];
        }




        return $this->getResults($sql);
    }



    public function checkAvailableTeachers($param = array()){


        $sql = "SELECT
    `jb_teacher_subjects`.`teacher_id`
    , `jb_teacher_subjects`.`subject_id`
    , `jb_subjects`.`title` AS `subject_title`
    , `jb_users`.`name`
FROM
    `jb_teacher_subjects`
    INNER JOIN `jb_subjects` 
        ON (`jb_teacher_subjects`.`subject_id` = `jb_subjects`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_teacher_subjects`.`teacher_id` = `jb_users`.`id`)
WHERE 1 ";

        //$sql .= " AND `jb_users`.`company_id` = " . $this->getCompany();

        if(!empty($param['branch'])){
            $sql .= " AND `jb_subjects`.`branch_id` = " . $param['branch'];
        }

        if(!empty($param['class'])){
            $sql .= " AND `jb_subjects`.`class_id` = " . $param['class'];
        }

        $session = !empty($param['session'])   ? $param['session'] : "";
        $weekdayId = !empty($param['weekday'])   ? $param['weekday'] : "";
        $startTime = !empty($param['start_time'])   ? $param['start_time'] : "";
        $endTime = !empty($param['end_time'])   ? $param['end_time'] : "";

        if(empty($param['allow_duplicate'])){
            $sql .= " AND teacher_id NOT IN
        (SELECT teacher_id FROM jb_timetable_for_session WHERE weekday_id = $weekdayId
         AND session_id = $session
         AND
        (
            `start_time` BETWEEN '$startTime' AND '$endTime'
            OR `end_time` BETWEEN '$startTime' AND '$endTime'
            OR '$startTime' BETWEEN `start_time` AND `end_time`
            OR '$endTime' BETWEEN `start_time` AND `end_time`
        ) )";
        }

        $sql .= " GROUP BY teacher_id";



        return $this->getResults($sql);
    }


    public function insertTimetableForSession($data,$dataSubs){

        if(empty($data)){
            exit;
        }

        $cols = array('branch_id','session_id','class_id','section_id','teacher_id','timetable_structure_id','timetable_id','weekday_id','period_name_id','start_time','end_time');

        if(!empty($dataSubs)){
            $colSubs = array('branch_id','session_id','class_id','section_id','teacher_id','subject_id', 'timetable_structure_id');
            $this->insert_multi( 'jb_timetable_session_subjects', $colSubs, $dataSubs,false );
        }

        return $this->insert_multi( 'jb_timetable_for_session', $cols, $data,false );
    }


    public function getTimetableForSession($param = array()){
        $sql = "SELECT
    `jb_timetable_for_session`.`timetable_structure_id`
    , `jb_timetable_for_session`.`teacher_id`
    , `jb_timetable_for_session`.`weekday_id`
    , `jb_timetable_for_session`.`period_name_id`
    , `jb_users`.`name`
    , `jb_users`.`id` AS staff_id
FROM
    `jb_timetable_for_session`
    INNER JOIN `jb_users` 
        ON (`jb_timetable_for_session`.`teacher_id` = `jb_users`.`id`) WHERE 1";

        if(!empty($param['class'])){
            $sql .= " AND `jb_timetable_for_session`.`class_id` = " . $param['class'];
        }

        if(!empty($param['session'])){
            $sql .= " AND `jb_timetable_for_session`.`session_id` = " . $param['session'];
        }

        if(!empty($param['section'])){
            $sql .= " AND `jb_timetable_for_session`.`section_id` = " . $param['section'];
        }





        return $this->getResults($sql);
    }


    public function getTimetableSessionSubjects($param = array()){
        $sql = "SELECT
    `jb_timetable_session_subjects`.`timetable_structure_id`
    , `jb_timetable_session_subjects`.`teacher_id`
    , `jb_timetable_session_subjects`.`subject_id`
    , `jb_timetable_structure`.`weekday_id`
    , `jb_timetable_structure`.`period_name_id`
    , `jb_subjects`.`title` AS subject_title
   
  
FROM
    `jb_timetable_session_subjects`
    INNER JOIN `jb_timetable_structure` 
        ON (`jb_timetable_session_subjects`.`timetable_structure_id` = `jb_timetable_structure`.`id`)
    INNER JOIN `jb_subjects` 
        ON (`jb_timetable_session_subjects`.`subject_id` = `jb_subjects`.`id`) WHERE 1";

        if(!empty($param['class'])){
            $sql .= " AND `jb_timetable_session_subjects`.`class_id` = " . $param['class'];
        }

        if(!empty($param['session'])){
            $sql .= " AND `jb_timetable_session_subjects`.`session_id` = " . $param['session'];
        }

        if(!empty($param['section'])){
            $sql .= " AND `jb_timetable_session_subjects`.`section_id` = " . $param['section'];
        }


        return $this->getResults($sql);
    }


    public function deleteTimetableForSession($branch,$class,$section,$session,$teacher,$structure){
        $where['branch_id'] = $branch;
        $where['session_id'] = $session;
        $where['class_id'] = $class;
        $where['section_id'] = $section;
        $where['teacher_id'] = $teacher;
        $where['timetable_structure_id'] = $structure;

        $this->delete( 'jb_timetable_session_subjects', $where );


        return $this->delete( 'jb_timetable_for_session', $where, 1 );
    }

    protected function getTableName()
    {
        // TODO: Implement getTableName() method.
    }
}
