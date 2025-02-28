<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 10/16/2017
 * Time: 10:40 AM
 */
class AdminModel extends BaseModel
{

    protected function getTableName(){}



    public function GetOvealAllCountStudents(){
        $sql = "SELECT COUNT(*) AS `tot` , `student_status` FROM `jb_students` GROUP BY `student_status`;";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetCurrentStudents(){
        $sql = "SELECT COUNT(*) AS `tot`, `jb_students`.`branch_id`, `jb_branch_short_names`.`title` FROM `jb_students`";
        $sql .= " INNER JOIN `jb_branches`
                        ON (`jb_students`.`branch_id` = `jb_branches`.`id`)";
        $sql .= " INNER JOIN `jb_branch_short_names`
                        ON (`jb_branch_short_names`.`branch_id` = `jb_branches`.`id`)";
        $sql .= " WHERE `student_status` = 'current' AND `jb_branch_short_names`.`lang_id` =3";
        $sql .= " GROUP BY `jb_students`.`branch_id`";

        $res = $this->getResults($sql);
        return $res;
    }

    public function GetStudentCountByBranch($branchId = ""){
        $sql = "SELECT

            `jb_branch_short_names`.`title`
            ,SUM(if (`jb_students`.`student_status` = 'current', 1, 0)) AS current_students
            ,SUM(if (`jb_students`.`student_status` = 'terminated', 1, 0)) AS terminated_students
            ,SUM(if (`jb_students`.`student_status` = 'completed', 1, 0)) AS completed_students
        FROM
            `jb_students`
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_branch_short_names`
                ON (`jb_branch_short_names`.`branch_id` = `jb_branches`.`id`)
        WHERE `jb_branch_short_names`.`lang_id` = 3 ";

        if(!empty($branchId)){
            $sql .= " AND `jb_branch_short_names`.`branch_id` = $branchId";
        }
       $sql .= " GROUP BY `jb_students`.`branch_id`";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetAttandByBranch($data){
        $sql = "SELECT
            COUNT(*) AS `tot`
            , `branch_id`
        FROM
            `jb_daily_attand`
        WHERE 1 AND (`date` = '$data') AND (`attand` = 2 OR attand = 3)
        GROUP BY `branch_id`";

        $res = $this->getResults($sql);
        return $res;
    }

    public function GetAppSettings(){
        $sql = "SELECT * FROM `jb_data_settings` WHERE 1 AND data_key = 'admin_app'";
        $res = $this->getSingle($sql);
        $data = unserialize($res['data_val']);
        return $data;
    }

    public function GetUniqueExams(){
        $sql = "SELECT
            `jb_results`.`exam_id`
            , `jb_sessions`.`title`
            , `jb_sessions`.`id` AS session_id
        FROM
            `jb_results`
            INNER JOIN `jb_sessions`
                ON (`jb_results`.`session_id` = `jb_sessions`.`id`)
        GROUP BY `jb_results`.`exam_id`, `jb_results`.`session_id`
        ORDER BY `jb_results`.`date` DESC";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetFeeStructure($session){
        $sql = "SELECT
            `jb_fee_type`.`title_en` AS fee_title
            ,`jb_fee_type`.`id` AS fee_type_id
            , `jb_branch_short_names`.`title` AS branch_title
            , `jb_fee_structure`.`session_id`
            , `jb_fee_structure`.`fees`
            , `jb_fee_structure`.`class_id`
            , `jb_fee_structure`.`branch_id`
            , `jb_classes`.`eng_name` AS class_title
        FROM
            `jb_fee_structure`
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_structure`.`fee_type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_fee_structure`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_branch_short_names`
                ON (`jb_branch_short_names`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_fee_structure`.`class_id` = `jb_classes`.`id`)
        WHERE `jb_branch_short_names`.`lang_id` = 3
            AND `jb_fee_structure`.`session_id` = $session
            AND `jb_fee_type`.`id` IN (1,2)
        GROUP BY `jb_fee_type`.`id`, `jb_fee_structure`.`branch_id`, `jb_fee_structure`.`class_id`";

        $res = $this->getResults($sql);
        return $res;
    }

    public function countStudentsByBranchClass($sessionId = 5){
        $sql = "SELECT
            `jb_students`.`class_id`
            , `jb_students`.`branch_id`
            , COUNT(`jb_students`.`id`) AS `tot`

        FROM
            `jb_students`
            WHERE 1 AND `session_id` = $sessionId AND `student_status` = 'current'
        GROUP BY `jb_students`.`class_id`, `jb_students`.`branch_id`";
        $res = $this->getResults($sql);
        return $res;
    }

    public function studentDiscounts(){
        $sql = "SELECT
            `jb_fee_discounts`.`amount`
            , `jb_students`.`class_id`
            , `jb_students`.`branch_id`
        FROM
            `jb_fee_discounts`
            INNER JOIN `jb_students`
                ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`)
        WHERE (`jb_fee_discounts`.`type_id` = 1)
        GROUP BY `jb_students`.`id`";

        $res = $this->getResults($sql);
        return $res;
    }

    public function GetFeeTypes(){
        $sql = "SELECT
            `title_en`
            , `id`
        FROM
            `jb_fee_type`
        WHERE (`published` =1)
        ORDER BY `position` ASC";

        $res = $this->getResults($sql);
        return $res;
    }

    public function feePaidDetail($date){
        $staus = "pending";
        $sql = "SELECT (SUM(fees)-SUM(discount)) AS fees, branch_id FROM `jb_fee_paid` WHERE fee_date = '$date' AND `paid_status` <> '$staus' AND type_id = 1 GROUP BY branch_id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function feePendingDetail($date){
        $staus = "pending";
        $sql = "SELECT (SUM(fees)-SUM(discount)) AS fees, branch_id FROM `jb_fee_paid` WHERE fee_date = '$date' AND `paid_status` = '$staus' AND type_id = 1 GROUP BY branch_id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetPaidData($date){
        $sql = "SELECT fees, discount, jb_fee_paid.branch_id, title, paid_status  FROM `jb_fee_paid`";
        $sql .= " INNER JOIN jb_branch_short_names";
        $sql .= " ON (`jb_branch_short_names`.`branch_id` = `jb_fee_paid`.`branch_id`)";
        $sql .= " WHERE 1 AND jb_branch_short_names.lang_id = 3";
        $sql .= " AND fee_date = '$date'";
        $sql .= " AND type_id = 1";
        $sql .= " GROUP BY student_id";
        //echo '<pre>';print_r($sql );echo '</pre>';
        $res = $this->getResults($sql);
        return $res;
    }
	
	public function BranchList(){
		$sql = "SELECT id, title, branch_address FROM jb_branches WHERE published = 1";
		$res = $this->getResults($sql);
		return $res;
	}

	public function GetBranchDetail($id){
		$sql = "SELECT
               `jb_zones`.`title` AS `zone_title`
               , `jb_branches`.`id`
               , `jb_branches`.`title` AS `branch_title`
               , `jb_branches`.`branch_code`
               , `jb_branches`.`branch_address`
               , `jb_branches`.`branch_nazim`
               , `jb_branches`.`branch_fone`
               , `jb_branches`.`branch_date`
               , `jb_branch_app_detail`.`thumb_image`
               , `jb_branch_app_detail`.`banner_image`
               , `jb_branch_app_detail`.`latitude`
               , `jb_branch_app_detail`.`longtitude`
           FROM
               `jb_branches`
               INNER JOIN `jb_zones`
                   ON (`jb_branches`.`zone_id` = `jb_zones`.`id`)
               LEFT JOIN `jb_branch_app_detail`
                   ON (`jb_branches`.`id` = `jb_branch_app_detail`.`branch_id`)";

		$sql .= " WHERE `jb_branches`.`id` = $id";
		$res = $this->getSingle($sql);
		return $res;
	}

	public function AdminLogin($user,$pwd){
	    $sql = "SELECT
	        `jb_users`.`id`
	        , `jb_users`.`name`
	        , `jb_users`.`username`
	        , `jb_users`.`phone_number`
	    FROM
	        `jb_users`
	        INNER JOIN `jb_user_groups` 
	            ON (`jb_users`.`group_id` = `jb_user_groups`.`id`)
	        INNER JOIN `jb_group_types` 
	            ON (`jb_user_groups`.`type_id` = `jb_group_types`.`id`)
	    WHERE 1 ";
	    $sql .= "AND (`jb_group_types`.`group_key` ='super_admin') ";
	    $sql .= "AND `jb_users`.`username` = '$user' AND password = '$pwd' LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }


    public function AdminUsers($param = array()){
        $sql = "SELECT
            `jb_users`.`id`
            , `jb_users`.`name`
            , `jb_users`.`username`
            , `jb_users`.`phone_number`
        FROM
            `jb_users`
            INNER JOIN `jb_user_groups` 
                ON (`jb_users`.`group_id` = `jb_user_groups`.`id`)
            INNER JOIN `jb_group_types` 
                ON (`jb_user_groups`.`type_id` = `jb_group_types`.`id`)
        WHERE 1 ";
        if(!empty($param['user'])){
            $sql .= " AND `jb_users`.`id` = " . $param['user'];
        }
        $sql .= " AND (`jb_group_types`.`group_key` ='super_admin') ";

        $res = $this->getResults($sql);
        return $res;
    }

    public function insertDeviceToken($userId,$deviceToken){
        $table = "`jb_user_device_tokens`";
        $sql = "INSERT INTO $table (`user_id`, `device_token`) VALUES ($userId, '$deviceToken')";
        $sql .= " ON DUPLICATE KEY UPDATE `device_token` = '$deviceToken'";
        return $this->query($sql);
    }

    public function BranchFeeStructure($branchId,$sessionId){
        $sql = "SELECT
             `jb_fee_type`.`title`
             , `jb_classes`.`title` AS `class_title`
           ,SUM(if (`jb_fee_structure`.`fee_type_id` = 1, fees, 0)) AS monthly
           ,SUM(if (`jb_fee_structure`.`fee_type_id` = 2, fees, 0)) AS yearly
        FROM
            `jb_fee_structure`
            INNER JOIN `jb_fee_type` 
                ON (`jb_fee_structure`.`fee_type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_classes` 
                    ON (`jb_fee_structure`.`class_id` = `jb_classes`.`id`)
        WHERE (`jb_fee_structure`.`branch_id` = $branchId
            AND `jb_fee_structure`.`session_id` = $sessionId)";
        $sql .= "AND `jb_fee_type`.`id` IN (1,2) ";
        $sql .= "GROUP BY `jb_fee_structure`.`class_id`";
        $res = $this->getResults($sql);
        return $res;
    }

    public function insertMessage($data)
    {
        $table = "jb_user_messages";
        return $this->insert($table, $data);
    }

    public function messagesList(){
        $sql = "SELECT
            `jb_user_messages`.`message`
            , `jb_user_messages`.`message_type`
            , `jb_user_messages`.`created`
            , `jb_users`.`name`
        FROM
            `jb_user_messages`
            INNER JOIN `jb_users` 
                ON (`jb_user_messages`.`user_id` = `jb_users`.`id`)
        ORDER BY `jb_user_messages`.`created` DESC LIMIT 200";
        $res = $this->getResults($sql);
        return $res;
    }

    public function deviceIds($userId = ""){
        $sql = "SELECT device_token FROM jb_user_device_tokens WHERE 1";
        if(!empty($userId)){
            $sql .= " AND user_id = $userId";
        }
        $res = $this->getResults($sql);
        $registrationIds = array();
        foreach ($res as $row){
            $registrationIds[] = $row['device_token'];
        }
        return $registrationIds;
    }


}