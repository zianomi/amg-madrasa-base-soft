<?php

class ApiTeacherModel extends BaseModel
{

    const STARTED = "STARTED";
    const COMPLETED = "COMPLETED";
    const CANCELLED = "CANCELLED";
    const PENDING = "PENDING";
    const TEACHER_HOME_WORKS = "`jb_teacher_home_works`";



    public function login($userName, $filterBy = "username")
    {
        $sql = "SELECT id, name, username, password  FROM `jb_users` WHERE 1";

        if ($filterBy == "username") {
            $sql .= " AND username = '$userName'";
        }

        if ($filterBy == "id") {
            $sql .= " AND id = '$userName'";
        }

        $sql .= " AND published = 1 ";
        $sql .= " AND user_type = 'teacher'";
        $sql .= " LIMIT 1";

        return $this->getSingle($sql);
    }



    public function getTeacherBranches($id): array
    {
        $sql = "SELECT
        `jb_user_branches`.`branch_id` AS `id`
        , `jb_branches`.`title`
    FROM
        `jb_user_branches`
        INNER JOIN `jb_branches` 
            ON (`jb_user_branches`.`branch_id` = `jb_branches`.`id`) WHERE `jb_user_branches`.`user_id` = $id";
        return $this->getResults($sql);
    }

    public function getConfig()
    {
        $sql = "SELECT id, title FROM `jb_sessions` WHERE 1";
        $sql .= " AND published = 1 ORDER BY end_date DESC LIMIT 1";

        $rowSession = $this->getSingle($sql);

        $data['session_id'] = 0;
        $data['session_title'] = "";

        if (!empty($rowSession)) {
            $data['session_id'] = $rowSession['id'];
            $data['session_title'] = $rowSession['title'];
        }

        return $data;

    }



    public function getTeacherBooks($teacher)
    {
        $sql = "SELECT
        `jb_teacher_subjects`.`subject_id` AS `id`
        , `jb_subject_groups`.`title`
        , `jb_subject_groups`.`class_id`
        , `jb_classes`.`title` AS `class_title`
    FROM
        `jb_teacher_subjects`
        INNER JOIN `jb_subject_groups` 
            ON (`jb_teacher_subjects`.`subject_id` = `jb_subject_groups`.`id`)
        INNER JOIN `jb_classes` 
            ON (`jb_subject_groups`.`class_id` = `jb_classes`.`id`)
    WHERE (`jb_teacher_subjects`.`teacher_id`  = $teacher)
    GROUP BY `id`;";

        return $this->getResults($sql);
    }




    protected function getTableName()
    {
    }
}