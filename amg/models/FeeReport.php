<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 5/11/2019
 * Time: 5:03 PM
 */




class FeeReport extends BaseModel
{

    protected function getTableName()
    {
        // TODO: Implement getTableName() method.
    }

    public function getSumOfDeposit($param = array()){
        $pr = $this->getPrefix();

        $sql = "SELECT
    `jb_fee_deposits`.`bank`
    , SUM(`jb_fee_paid`.`fees`) - SUM(`jb_fee_paid`.`discount`) AS fees
    , `jb_fee_deposits`.`date`
    , `jb_users`.`name`
    , `jb_fee_deposits`.`user_id`
FROM
    `jb_fee_deposits`
    INNER JOIN `jb_fee_paid` 
        ON (`jb_fee_deposits`.`id` = `jb_fee_paid`.`deposit_id`)
        INNER JOIN `jb_users` 
        ON (`jb_fee_deposits`.`user_id` = `jb_users`.`id`)
      WHERE 1 ";

        if(!empty($param['user'])){
            $sql .= " AND `jb_fee_deposits`.`user_id` = " . $param['user'];
        }

        if(!empty($param['branch'])){
            $sql .= " AND `jb_fee_deposits`.`gl_branch_id` = " . $param['branch'];
        }


        if (!empty($param['start_date']) && !empty($param['end_date'])) {
            $sql .= " AND `" . $pr . "fee_deposits`.`date` BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        }

        $sql .= " GROUP BY `jb_fee_deposits`.`date`, `jb_fee_deposits`.`user_id`";

        //echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);
        return $res;
    }

    public function getSumOfColeection($param = array()){
        $pr = $this->getPrefix();

        $sql = "SELECT
    `jb_fee_invoice`.`invoice_id`
    , SUM(`jb_fee_paid`.`fees`) - SUM(`jb_fee_paid`.`discount`) AS fees
    , `jb_fee_invoice`.`recp_date`
    , `jb_users`.`name`
    , `jb_fee_invoice`.`created_user_id`
FROM
    `jb_fee_invoice`
    INNER JOIN `jb_fee_paid` 
        ON (`jb_fee_invoice`.`id` = `jb_fee_paid`.`invoice_id`)
    INNER JOIN `jb_users` 
        ON (`jb_fee_invoice`.`created_user_id` = `jb_users`.`id`)
      WHERE 1 ";

        if(!empty($param['user'])){
            $sql .= " AND `jb_fee_invoice`.`created_user_id` = " . $param['user'];
        }

        if(!empty($param['branch'])){
            $sql .= " AND `jb_fee_paid`.`branch_id` = " . $param['branch'];
        }


        if (!empty($param['start_date']) && !empty($param['end_date'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`recp_date` BETWEEN '" . $param['start_date'] . "' AND '" . $param['end_date'] . "'";
        }

        $sql .= " GROUP BY `jb_fee_invoice`.`recp_date`, `jb_fee_invoice`.`created_user_id`";


        $res = $this->getResults($sql);
        return $res;
    }

    public function BranchStudent($branch,$session){
        $sql = "SELECT
    COUNT(`jb_students`.`id`) AS `tot`
    , `jb_students`.`class_id`
    , `jb_classes`.`title` AS `class_title`
FROM
    `jb_students`
    INNER JOIN `jb_classes` 
        ON (`jb_students`.`class_id` = `jb_classes`.`id`)
 WHERE 1";
        $sql .= " AND jb_students.branch_id = $branch";
        $sql .= " AND jb_students.session_id = $session";
        $sql .= " AND jb_students.student_status = 'current'";
        $sql .= " AND jb_classes.published = 1";
        $sql .= " GROUP BY `jb_students`.`class_id`";

        $res = $this->getResults($sql);

        //echo '<pre>'; print_r($sql); echo '</pre>';
        return $res;
    }

    public function findSessionByDate($date){
        $sql = "SELECT * FROM jb_sessions WHERE '$date' BETWEEN jb_sessions.start_date AND jb_sessions.end_date";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function getBranchFees($param = array())
    {
        $pr = $this->getPrefix();

        $sql = "SELECT
            `jb_fee_structure`.`fees`
            ,`jb_fee_structure`.`class_id`
            ,`jb_fee_structure`.`fee_type_id`
        FROM
            `jb_fee_structure`
            INNER JOIN `jb_classes`
                ON (`jb_fee_structure`.`class_id` = `jb_classes`.`id`)
        WHERE 1 AND `jb_classes`.`class_type` = '" . $this->stuStatus("current") . "'";


        if (
            empty($param['branch'])
            || empty($param['session'])
            || empty($param['fee_type'])
            || !is_array($param['fee_type'])
        ) {
            return false;
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`branch_id` = " . $param['branch'];
        }
        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`class_id` = " . $param['class'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`session_id` = " . $param['session'];
        }

        if (!empty($param['fee_type'])) {
            if(is_array($param['fee_type'])){
                $tool = $this->toolObj();
                $i = 0;
                $sql .= " AND (";
                foreach ($param['fee_type'] as $key){

                    if($i>0){
                        $sql .= " OR ";
                    }
                    $sql .= "`" . $pr . "fee_structure`.`fee_type_id` = " . $tool->GetExplodedInt($key);
                    $i++;
                }
                $sql .= ")";
            }
            //$sql .= $this->orAnd("`" . $pr . "fee_structure`.`fee_type_id`", $param['fee_type']);
        }

        $sql .= " GROUP BY `jb_fee_structure`.`class_id`, `jb_fee_structure`.`fee_type_id`";



        $res = $this->getResults($sql);


        return $res;

    }

    public function GetDiscounts($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_fee_discounts`.`type_id`
    , `jb_students`.`class_id`
    , SUM(`jb_fee_discounts`.`amount`) AS discount_amount
FROM
    `jb_fee_discounts`
    INNER JOIN `jb_students` 
        ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`)
WHERE 1
    AND `jb_students`.`student_status` = 'current'
";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }
        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        $sql .= " GROUP BY `jb_fee_discounts`.`type_id`, `jb_students`.`class_id`";

        $res = $this->getResults($sql);


        return $res;
    }

    public function getDiscountCount($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT `jb_students`.`class_id` , COUNT(`jb_fee_discounts`.`id`) AS `tot` 
, `jb_fee_discounts`.`type_id` FROM `jb_fee_discounts` 
INNER JOIN `jb_students` ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`) 
WHERE 1 AND `jb_students`.`student_status` = 'current'
    ";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        $sql .= " GROUP BY `jb_students`.`class_id`, jb_fee_discounts.type_id";


        $res = $this->getResults($sql);


        return $res;
    }

    public function getBranchDiscounts($date,$branch,$param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT COUNT(*) AS `tot` 
, `fees` , `discount` , `type_id` , `class_id` 
FROM `jb_fee_paid` WHERE 1 ";

        $sql .= " AND `fee_date` = '$date'";
        $sql .= " AND `branch_id` = $branch";


        if (!empty($param['fee_type'])) {
            if(is_array($param['fee_type'])){
                $tool = $this->toolObj();
                $i = 0;
                $sql .= " AND (";
                foreach ($param['fee_type'] as $key){

                    if($i>0){
                        $sql .= " OR ";
                    }
                    $sql .= "`" . $pr . "fee_paid`.`type_id` = " . $tool->GetExplodedInt($key);
                    $i++;
                }
                $sql .= ")";
            }
            //$sql .= $this->orAnd("`" . $pr . "fee_structure`.`fee_type_id`", $param['fee_type']);
        }


        if (!empty($param['session'])) {
            $sql .= " AND `session_id` = " . $param['session'];
        }


        $sql .= "
GROUP BY (`fees` - `discount`), `class_id`,  `type_id`
ORDER BY class_id";

        //echo '<pre>'; print_r($sql); echo '</pre>';


        $res = $this->getResults($sql);


        return $res;
    }




    public function checkBankInvoice($stuId,$date){

        $sql = "SELECT id FROM jb_fee_invoice WHERE 1";
        $sql .= " AND student_id = $stuId";
        $sql .= " AND fee_month = '$date'";
        $sql .= " AND invoice_status = 'bank'";

        $res = $this->getSingle($sql);
        if(empty($res)){
            return "-1";
        }

        return $res['id'];

    }

    public function checkPendingDues($stuId){
        $sql = "SELECT student_id FROM jb_fee_paid WHERE 1";
        $sql .= " AND student_id = $stuId";
        $sql .= " AND paid_status = 'pending'";
        $res = $this->getSingle($sql);
        if(empty($res)){
            return "-1";
        }

        return $res['student_id'];

    }



}
