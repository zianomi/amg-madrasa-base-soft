<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 7/20/2018
 * Time: 12:01 AM
 */

define("KEY","'e2XOqaQwkvrYHH2Y^&-my-key-&^'");
define("CHIPER","'^&-my-key-&^'e2XOqaQwkvrYHH2Y");
define("CHIPER_OPT","AES-256-CBC");

class FeeExportModel extends BaseModel
{

    private $branch = array();
    private $session;

    protected function getTableName()
    {
        // TODO: Implement getTableName() method.
    }

    /**
     * @return array
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param array $branch
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
    }



    /**
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @param mixed $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    public function getBranches(){
        $sql = "SELECT `id`,`eng_name`,`branch_fone`,`branch_date` FROM jb_branches WHERE 1";
        $branchIds = $this->getBranch();
        $sql .= " AND " . $this->makeOrQuery("id",$branchIds);
        $res = $this->getResults($sql);
        return $res;
    }


    public function sessionClasses(){
        $branch = $this->getBranch();
        $session = $this->getSession();
        $sql = "SELECT
    `jb_classes`.`id`
    , `jb_classes`.`eng_name`
FROM
    `jb_session_classes`
    INNER JOIN `jb_classes` 
        ON (`jb_session_classes`.`class_id` = `jb_classes`.`id`)
WHERE 1 AND `jb_session_classes`.`session_id` = $session
     ";
        $sql .= " AND " . $this->makeOrQuery("`jb_session_classes`.`branch_id`",$branch);
        $sql .= " GROUP BY jb_session_classes.class_id";
        $res = $this->getResults($sql);
        return $res;
    }


    public function sessionSections($classIds){
        $branch = $this->getBranch();
        $session = $this->getSession();
        $sql = "SELECT
    `jb_sections`.`id`
    , `jb_sections`.`title`
FROM
    `jb_session_sections`
    INNER JOIN `jb_sections` 
        ON (`jb_session_sections`.`section_id` = `jb_sections`.`id`)
WHERE  1 
    AND `jb_session_sections`.`session_id` = $session
    AND `jb_session_sections`.`class_id`  IN ($classIds)";

        $sql .= " AND " . $this->makeOrQuery("`jb_session_sections`.`branch_id`",$branch);
        $sql .= " GROUP BY jb_session_sections.section_id";
        $res = $this->getResults($sql);
        return $res;
    }


    public function branchStudents(){
        $branch = $this->getBranch();
        $session = $this->getSession();
        $sql = "SELECT
    `jb_students`.`eng_name`
    , `jb_students`.`eng_fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
    , `jb_students`.`branch_id`
    , `jb_students`.`class_id`
    , `jb_students`.`section_id`
     , `jb_students`.`id`
FROM
    `jb_fee_paid`
    INNER JOIN `jb_students` 
        ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`)
WHERE 1";
        $sql .= " AND `jb_fee_paid`.`session_id` = $session";
        $sql .= " AND `jb_students`.`student_status` = 'current'";
        $sql .= " AND `jb_fee_paid`.`paid_status` = 'pending'";

        $sql .= " AND " . $this->makeOrQuery("`jb_fee_paid`.`branch_id`",$branch);
        $sql .= " AND " . $this->makeOrQuery("`jb_students`.`branch_id`",$branch);
        $sql .= " GROUP BY jb_students.id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getUnpaidData(){
        $branch = $this->getBranch();
        $session = $this->getSession();

        $sql = "SELECT jb_fee_paid.*, jb_students.id AS student_table_id FROM jb_fee_paid ";
        $sql .= "INNER JOIN jb_students ON jb_fee_paid.student_id = jb_students.id";
        $sql .= " WHERE 1 AND jb_students.session_id = $session ";

        $sql .= " AND jb_fee_paid.paid_status = 'pending'";

        $sql .= " AND " . $this->makeOrQuery("`jb_students`.`branch_id`",$branch);

        $res = $this->getResults($sql);
        return $res;
    }

    public function getFeeTypes(){
        $sql = "SELECT * FROM jb_fee_type WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function getSettings($userId){
        $sql = "SELECT
    `jb_branches`.`id`
    , `jb_branches`.`eng_name`
    , `jb_branches`.`branch_fone`
    , `jb_branches`.`branch_date`
    , `jb_branch_operators`.`user_id`
    , `jb_users`.`name`
FROM
    `jb_branches`
    INNER JOIN `jb_branch_operators` 
        ON (`jb_branches`.`id` = `jb_branch_operators`.`branch_id`)
    INNER JOIN `jb_users` 
        ON (`jb_branch_operators`.`user_id` = `jb_users`.`id`)";

        $sql .= " WHERE `jb_branch_operators`.`user_id` = $userId";
        $sql .= " GROUP BY `jb_branch_operators`.`user_id`, `jb_branches`.`id`";
        $sql .= " LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }


    public function getCurrentSession(){
        $sql = "SELECT * FROM jb_sessions WHERE published = 1";
        $sql .= " ORDER BY end_date DESC";
        $sql .= " LIMIT 1";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function getBranchOperators(){
        $branch = $this->getBranch();
        $sql = "SELECT jb_users.name, jb_branch_operators.user_id, 
jb_branch_operators.branch_id FROM jb_users 
INNER JOIN jb_branch_operators ON jb_branch_operators.user_id = jb_users.id
WHERE 1
";

        $sql .= " AND " . $this->makeOrQuery("jb_branch_operators.branch_id",$branch);
        $res = $this->getResults($sql);
        return $res;
    }

    public function getUser(){
        $sql = "SELECT id, name, username, password FROM jb_users WHERE published = 1";
        $sql .= " AND id = " . $this->getUserId();
        $res = $this->getSingle($sql);
        return $res;
    }

    public function checkAlreadyPaid($id){

         $checkPaid = array(
             'id' => $id,
             'paid_status' => "paid"
         );
         return $this->exists( 'jb_fee_paid', 'id', $checkPaid );
    }

    public function insertOfflineInvoice($data)
    {
        $table = "jb_fee_invoice_offline";
        $this->insert($table, ($data));
        return $this->lastid();
    }

    public function insertPaidFailed($data)
    {
        $table = "jb_paid_failed";
        $this->insert($table, ($data));
        return $this->lastid();
    }



    public function getOfflineInvoice($invoiceNumber){
        $sql = "SELECT
    `jb_fee_invoice_offline`.`offline_invoice_number`
    , `jb_fee_invoice`.`invoice_id`
    , `jb_fee_invoice`.`recp_date`
    , `jb_fee_invoice_offline`.`id`
    , `jb_users`.`name`
    , `jb_fee_invoice`.`student_id`
FROM
    `jb_fee_invoice_offline`
    INNER JOIN `jb_fee_invoice` 
        ON (`jb_fee_invoice_offline`.`invoice_id` = `jb_fee_invoice`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_fee_invoice`.`created_user_id` = `jb_users`.`id`)";

        $sql .= " WHERE 1";
        $sql .= " AND `offline_invoice_number` = '$invoiceNumber'";
        $sql .= " LIMIT 1";

        $res = $this->getSingle($sql);
        return $res;
    }

    public function checkFailedPaid($id){
        $sql = "SELECT
    `jb_paid_failed`.`offline_failed_id`
    , `jb_fee_invoice`.`invoice_id`
    , `jb_users`.`name`
    , `jb_fee_invoice`.`recp_date`
FROM
    `jb_paid_failed`
    INNER JOIN `jb_fee_paid` 
        ON (`jb_paid_failed`.`paid_failed_id` = `jb_fee_paid`.`id`)
    INNER JOIN `jb_fee_invoice` 
        ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_fee_paid`.`created_user_id` = `jb_users`.`id`)";

        $sql .= " WHERE 1";
        $sql .= " AND `jb_paid_failed`.`offline_failed_id` = $id";
        $sql .= " LIMIT 1";

        $res = $this->getSingle($sql);
        return $res;
    }


}