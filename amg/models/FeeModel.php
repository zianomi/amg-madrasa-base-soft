<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 4/8/2017
 * Time: 12:45 PM
 */
class FeeModel extends BaseModel
{

    protected function getTableName()
    {
    }

    public function getFeeTypes()
    {
        $sql = "SELECT * FROM jb_fee_type WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GerPaidByByInvoiceId($id){
        $sql = "SELECT * FROM jb_fee_paid WHERE invoice_id = $id";
        $res = $this->getResults($sql);
        return $res;
    }

    public function insertCancel($id){
        $table = "jb_fee_cancel_invoices";
        $data = $this->GerPaidByByInvoiceId($id);
        $vals = array();
        $userId = $this->getUserId();
        foreach ($data as $row){
            $rowId = $row['id'];
            $vals[] = array("NULL",$userId,$id,$rowId,date("Y-m-d"));
        }
        $cols = $this->getTableCols($table);
        return $this->insert_multi($table,$cols,$vals,false);
    }

    public function cancelInvoice($id){
        $sql = "UPDATE jb_fee_paid SET ";
        $sql .= " updated_user_id = NULL";
        $sql .= " ,updated = NULL";
        $sql .= " ,deposit_id = NULL";
        $sql .= " ,invoice_id = NULL";
        $sql .= " ,paid_status = '" . $this->paidStatus("pending") . "'";
        $sql .= " WHERE invoice_id = $id";

        $sql2 = "UPDATE `jb_fee_invoice` SET `invoice_status` = '" . $this->invoiceStatus("cancelled") . "'";
        $sql2 .= " WHERE `jb_fee_invoice`.`id` = $id";


        $this->query($sql2);
        return $this->query($sql);
    }

    public function cancelChalan($session,$branch,$type,$date){
        $sql = "DELETE FROM `jb_fee_paid` WHERE `paid_status` = '" . $this->invoiceStatus("pending") . "'";
        $sql .= " AND `session_id` = $session";
        $sql .= " AND `branch_id` = $branch";
        $sql .= " AND `type_id` = $type";
        $sql .= " AND `fee_date` = '$date'";
        return $this->query($sql);
    }

    public function invoiceStatus($key)
    {
        $stuStatus = array();
        $stuStatus['pending'] = 'pending';
        $stuStatus['paid'] = 'paid';
        $stuStatus['cancelled'] = 'cancelled';
        return $stuStatus[$key];
    }

    public function paidStatus($key)
    {
        $stuStatus = array();
        $stuStatus['pending'] = 'pending';
        $stuStatus['paid'] = 'paid';
        $stuStatus['exempt'] = 'exempt';
        $stuStatus['delayed'] = 'delayed';
        $stuStatus['advanced'] = 'advanced';
        $stuStatus['bank'] = 'bank';
        return $stuStatus[$key];
    }

    public function timeStatus($key)
    {
        $stuStatus = array();
        $stuStatus['on_time'] = 'on_time';
        $stuStatus['delayed'] = 'delayed';
        return $stuStatus[$key];
    }

    public function insertFeeStructure($data = array())
    {

        $pr = $this->getPrefix();
        $tableName = $pr . "fee_structure";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function getFeeStructure($param = array())
    {
        $pr = $this->getPrefix();
        $tableName = $pr . "fee_structure";
        $sql = "SELECT * FROM " . $tableName . " WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`class_id` = " . $param['class'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`session_id` = " . $param['session'];
        }

        if (!empty($param['type'])) {
            $sql .= " AND `" . $pr . "fee_structure`.`fee_type_id` = " . $param['type'];
        }

        $res = $this->getResults($sql);
        return $res;
    }

    public function discountsList($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_fee_discounts`.`amount`
            , `jb_fee_discounts`.`refrence`
            , `jb_fee_discounts`.`id` AS discount_primary_id
            , `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_branches`.`title` branch_title
            , `jb_students`.`branch_id`
            , `jb_students`.`class_id`
            , `jb_classes`.`title` class_title
            , `jb_students`.`section_id`
            , `jb_sections`.`title` section_title
            , `jb_students`.`session_id`
            , `jb_sessions`.`title` session_title
            , `jb_fee_discounts`.`type_id`
            , `jb_fee_discounts`.`path`
            , `jb_fee_discounts`.`image`
            , `jb_fee_type`.`title`
            , `jb_discount_refrence`.`title` AS ref_title
            , `jb_fee_structure`.`fees` AS total_fees
        FROM
            `jb_fee_discounts`
            INNER JOIN `jb_students`
                ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_discounts`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_students`.`section_id` = `jb_sections`.`id`)
            INNER JOIN `jb_sessions`
                ON (`jb_students`.`session_id` = `jb_sessions`.`id`)
            INNER JOIN `jb_discount_refrence`
                ON (`jb_fee_discounts`.`refrence` = `jb_discount_refrence`.`id`)
            INNER JOIN `jb_fee_structure`
                ON  (`jb_fee_structure`.`fee_type_id` = `jb_fee_discounts`.`type_id`)   
            WHERE 1";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
            $sql .= " AND `" . $pr . "fee_structure`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
            $sql .= " AND `" . $pr . "fee_structure`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
            $sql .= " AND `" . $pr . "fee_structure`.`session_id` = " . $param['session'];
        }

        if (!empty($param['type'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`type_id` = " . $param['type'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`student_id` = " . $param['id'];
        }


        if (!empty($param['refrence'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`refrence` = " . $param['refrence'];
        }

        $sql .= " GROUP BY `jb_fee_structure`.`fee_type_id`, `jb_students`.`id`";

        $sql .= " ORDER BY `jb_students`.`id` ASC";

        if (!empty($param['limit'])) {
            $sql .= " LIMIT " . $param['limit'];
        }

        $res = $this->getResults($sql);
        return $res;
    }

    public function insertDiscount($data)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_discounts";
        $res = $this->insert($table, $this->setInsert($data));
        return $res;
    }

    public function insertDiscountRequest($data)
    {
        $pr = $this->getPrefix();
        $table = $pr . "discount_requests";
        $res = $this->insert($table, ($data));
        return $res;
    }

    public function updateDiscount($id, $updateData)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_discounts";
        $update_where = array('id' => $id);
        $this->update($table, $this->setUpdated($updateData), $update_where, 1);
    }

    public function updateDiscountCol($id, $updateData)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_paid";
        $update_where = array('id' => $id);
        $this->update($table, ($updateData), $update_where, 1);
    }

    public function getDiscountByID($id)
    {
        $sql = "SELECT
            `jb_fee_discounts`.`id`
            , `jb_fee_discounts`.`student_id`
            , `jb_fee_discounts`.`amount`
            , `jb_fee_discounts`.`type_id`
            , `jb_fee_discounts`.`refrence`
            , `jb_students`.`branch_id`
            , `jb_students`.`class_id`
            , `jb_students`.`session_id`
        FROM
            `jb_fee_discounts`
            INNER JOIN `jb_students`
                ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`) WHERE `jb_fee_discounts`.`id` = $id";

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

    public function insertSingleChalan($data)
    {
        $table = "jb_fee_paid";
        return $this->insert($table, $this->setInsert($data));
    }

    public function invoiceStudents($branch, $session,$param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `id`
            ,`class_id`
            ,`section_id`
        FROM
            `jb_students` WHERE student_status = '" . $this->stuStatus("current") . "'";

        $sql .= " AND branch_id = $branch";
        $sql .= " AND session_id = $session";
        if(!empty($param['class'])){
            $sql .= " AND class_id = " . $param['class'];
        }
       /* $sql .= " AND id NOT IN ";
        $sql .= "(";
        $sql .= "SELECT student_id FROM jb_fee_invoice WHERE invoice_date = '$date'";
        $sql .= ")";*/

        $res = $this->getResults($sql);
        return $res;
    }

    public function insertInvoices($data = array())
    {

        $pr = $this->getPrefix();
        $tableName = $pr . "fee_invoice";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function updateInvoice($branch, $date)
    {

        $sql = "UPDATE `jb_fee_invoice` SET `invoice_status` = '" . $this->invoiceStatus("cancelled") . "'";
        $sql .= "WHERE `jb_fee_invoice`.`branch_id` = $branch AND `jb_fee_invoice`.`invoice_status` = '" . $this->invoiceStatus("pending") . "'";
        $sql .= " AND invoice_date < '$date'";

        return $this->query($sql);
    }

    public function updatePaidData()
    {
        $table = $this->getPrefix() . "fee_paid";
        /*$update = array("fee_date" => $date);
        $update_where = array( "paid_status" =>  $this->invoiceStatus("pending"));
        return $this->update( $table, $update, $update_where );
        */
        $pending = $this->invoiceStatus("pending");
        $sql = "UPDATE `jb_fee_paid` p
            	INNER JOIN `jb_fee_invoice` i
            		ON p.`student_id` = i.`student_id`
            SET p.`invoice_id` = i.id
            WHERE p.`student_id` = i.`student_id`
        	AND i.invoice_status = '$pending'
        	AND p.paid_status = '$pending'
        ";
        return $this->query($sql);
    }

    public function getCurrentMonthInvoice($date, $branch)
    {
        $pr = $this->getPrefix();
        $tableName = $pr . "fee_invoice";
        $sql = "SELECT id, invoice_id AS invoice_table_id, student_id, due_date FROM " . $tableName . " WHERE 1";
        $sql .= " AND branch_id = $branch";
        $sql .= " AND invoice_date = '$date'";
        $res = $this->getResults($sql);
        return $res;
    }

    public function insertFees($data = array())
    {

        $pr = $this->getPrefix();
        $tableName = $pr . "fee_paid";

        $columns = array("id"
            ,"student_id"
            ,"type_id"
            ,"branch_id"
            ,"class_id"
            ,"section_id"
            ,"session_id"
            ,"fees"
            ,"discount"
            ,"paid_status"
            ,"fee_date"
            ,"due_date"
            ,"created_user_id"
            ,"created"
        );

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }


    public function insertAdvancedFees($data = array())
    {

        $pr = $this->getPrefix();
        $tableName = $pr . "fee_paid";

        //$columns = $this->getTableCols($tableName);

        $columns = array(
            "student_id"
            ,"type_id"
            ,"branch_id"
            ,"class_id"
            ,"section_id"
            ,"session_id"
            ,"fees"
            ,"discount"
            ,"paid_status"
            ,"fee_date"
            ,"due_date"
            ,"invoice_id"
            ,"created_user_id"
            ,"updated_user_id"
            ,"created"
            ,"updated"

        );

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function discountedStudents($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_fee_discounts`.`amount`
            , `jb_fee_discounts`.`type_id`
            , `jb_students`.`id`
        FROM
            `jb_fee_discounts`
            INNER JOIN `jb_students` 
                ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`)
        WHERE  1";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }
        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['fee_types'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`type_id` IN (" . implode(",",$param['fee_types']) . ")";
        }

        if (!empty($param['student_id'])) {
            $sql .= " AND `" . $pr . "students`.`id` = " . $param['student_id'];
        }

        $res = $this->getResults($sql);
        return $res;
    }

    public function generatedInvoiceStudents($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_students`.`id`
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`branch_id`
            , `jb_students`.`class_id`
            , `jb_students`.`section_id`
            , `jb_students`.`session_id`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_students`
                ON (`jb_students`.`id` = `jb_fee_paid`.`student_id`) WHERE  1 ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`session_id` = " . $param['session'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`section_id` = " . $param['section'];
        }

        if (!empty($param['fee_date'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`fee_date` = '" . $param['fee_date'] . "'";
        }

        if (!empty($param['fee_type'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`type_id` = '" . $param['fee_type'] . "'";
        }

        if (!empty($param['paid_status'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`paid_status` = '" . $param['paid_status'] . "'";
        }

        $sql .= " GROUP BY `jb_students`.`id`";

        $res = $this->getResults($sql);
        return $res;
    }


    public function countRecords($branch,$date){
        $sqlStu = "SELECT COUNT(*) AS tot FROM jb_students WHERE branch_id = $branch AND student_status = 'current'";
        $resStu = $this->getSingle($sqlStu);
        $totalStudenst = $resStu['tot'];


        //$sqlInv = "SELECT COUNT(*) AS tot FROM jb_fee_invoice WHERE branch_id = $branch AND invoice_date = '$date'";
        //$resInv = $this->getSingle($sqlInv);
        //$totalInv = $resInv['tot'];

        $sqlPaid = "SELECT COUNT(*) AS tot FROM jb_fee_paid WHERE branch_id = $branch AND fee_date = '$date'";
        $resPaid = $this->getSingle($sqlPaid);
        $totalPaid = $resPaid['tot'];
        //return array("totstu" => $totalStudenst, "totinv" => $totalInv, "totpaid" => $totalPaid);
        return array("totstu" => $totalStudenst, "totpaid" => $totalPaid);
    }

    public function deleteChalan($branch,$date){

        $where = array( 'branch_id' => $branch, 'invoice_date' => $date );
        //$this->delete( 'jb_fee_invoice', $where );
        $where2 = array( 'branch_id' => $branch, 'fee_date' => $date );
        $this->delete( 'jb_fee_paid', $where2 );

    }


    public function insertSingleInvoice($data)
    {
        $table = "jb_fee_invoice";
        $this->insert($table, ($data));
        return $this->lastid();
    }

    public function insertDeposit($data)
    {
        $table = "jb_fee_deposits";
        $this->insert($table, ($data));
        return $this->lastid();
    }

    public function updateDeposit($depositId,$ids)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_paid";
        //$update_where = array('invoice_id' => $id, "student_id" => $student_id);
        $sql = "UPDATE $table SET deposit_id = $depositId WHERE id IN ($ids)";
        return $this->query($sql);
    }

    public function markDepositReadByGl($id)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_deposits";
        $updateData['is_gl_read'] = 1;
        $update_where = array('id' => $id);
        return $this->update($table, $updateData, $update_where, 1);

    }

    public function viewInvoices($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_fee_paid`.`type_id`
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_type`.`title`
             , `jb_fee_paid`.`student_id`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_students`
                ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`) WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['invoice_date'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`fee_date` = '" . $param['invoice_date'] . "'";
        }


        if (!empty($param['fee_type'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`type_id` = '" . $param['fee_type'] . "'";
        }



        $res = $this->getResults($sql);
        return $res;
    }

    public function getDiscountStudentFee($id,$typeId)
    {
        //$sql = "SELECT fees FROM jb_fee_structure WHERE branch_id = $branch AND class_id = $class AND fee_type_id = $type AND session_id = $session";

        $sql = "SELECT
            `jb_fee_structure`.`fees`
        FROM
            `jb_fee_structure`
            INNER JOIN `jb_students` 
                ON (`jb_fee_structure`.`branch_id` = `jb_students`.`branch_id`) 
                AND (`jb_fee_structure`.`class_id` = `jb_students`.`class_id`) 
                AND (`jb_fee_structure`.`session_id` = `jb_students`.`session_id`)
        WHERE 1 ";

        $sql .= " AND `jb_students`.`id` = $id";
        $sql .= " AND `jb_fee_structure`.`fee_type_id` = $typeId";



        $res = $this->getSingle($sql);
        return $res['fees'];
    }

    public function getPaidTable($id)
    {
        /*$sql = "SELECT
            `jb_fee_paid`.`student_id`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_paid`.`type_id`
            , `jb_fee_paid`.`invoice_id`
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`paid_status`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_type`.`title`
            , `jb_fee_type`.`title_en`
            , `jb_fee_type`.`duration_type`
            , `jb_fee_invoice`.`invoice_date`
            , `jb_fee_invoice`.`due_date`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_fee_invoice`
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`invoice_id`) WHERE 1";*/



        $sql = "SELECT
                    `jb_fee_paid`.`student_id`
                    , `jb_fee_paid`.`id` AS paid_id
                    , `jb_fee_paid`.`type_id`
                    , `jb_fee_paid`.`invoice_id`
                    , `jb_fee_paid`.`fees`
                    , `jb_fee_paid`.`discount`
                    , `jb_fee_paid`.`paid_status`
                    , `jb_fee_paid`.`fee_date`
                    , `jb_fee_paid`.`due_date`
                    , `jb_fee_type`.`title`
                    , `jb_fee_type`.`title_en`
                    , `jb_fee_type`.`duration_type`

                FROM
                    `jb_fee_paid`
                    INNER JOIN `jb_fee_type`
                        ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
                    WHERE 1";



        $sql .= " AND  `jb_fee_paid`.`student_id` = $id";
        $sql .= " AND `jb_fee_paid`.`paid_status` = '" . $this->paidStatus("pending") . "'";
        //$sql .= " AND `jb_fee_invoice`.`invoice_status` = '" . $this->paidStatus("pending") . "'";


        $sql .= " ORDER BY `jb_fee_paid`.`fee_date` ASC";



        $res = $this->getResults($sql);
        return $res;
    }

    public function getIdHistory($id)
        {
            $sql = "SELECT
                `jb_fee_paid`.`student_id`
                , `jb_fee_paid`.`id` AS paid_id
                , `jb_fee_paid`.`type_id`
                , `jb_fee_paid`.`invoice_id` AS paid_invoice_id
                , `jb_fee_paid`.`fees`
                , `jb_fee_paid`.`discount`
                , `jb_fee_paid`.`paid_status`
                , `jb_fee_paid`.`fee_date`
                , `jb_fee_paid`.`due_date`
                , `jb_fee_type`.`title`
                , `jb_fee_type`.`title_en`
                , `jb_fee_type`.`duration_type`
                , `jb_fee_invoice`.`recp_date`
                , `jb_fee_invoice`.`created_user_id`
                , `jb_fee_invoice`.`id` invoice_db_id
                , `jb_fee_invoice`.`invoice_id` invoice_number
            FROM
                `jb_fee_paid`
                INNER JOIN `jb_fee_type`
                    ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
                INNER JOIN `jb_fee_invoice`
                    ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`) WHERE 1";

            $sql .= " AND  `jb_fee_paid`.`student_id` = $id";
            $sql .= " AND `jb_fee_paid`.`paid_status` <> '" . $this->paidStatus("pending") . "'";
            $sql .= " AND `jb_fee_invoice`.`invoice_status` = '" . $this->paidStatus("paid") . "'";

            $sql .= " ORDER BY jb_fee_invoice.recp_date DESC";


            $res = $this->getResults($sql);
            return $res;
        }


    public function getIdHistoryEdit($id,$session)
    {
    $sql = "SELECT
            `jb_fee_paid`.`student_id`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_paid`.`type_id`
            , `jb_fee_paid`.`invoice_id` AS paid_invoice_id
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`paid_status`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_paid`.`due_date`
            , `jb_fee_type`.`title`
            , `jb_fee_type`.`title_en`
            , `jb_fee_type`.`duration_type`
            , `jb_fee_invoice`.`recp_date`
            , `jb_fee_invoice`.`created_user_id`
            , `jb_fee_invoice`.`id` invoice_db_id
            , `jb_fee_invoice`.`invoice_id` invoice_number
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_fee_invoice`
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`) WHERE 1";

    $sql .= " AND  `jb_fee_paid`.`student_id` = $id";
    $sql .= " AND  `jb_fee_paid`.`session_id` = $session";
    //$sql .= " AND `jb_fee_paid`.`paid_status` <> '" . $this->paidStatus("pending") . "'";
    //$sql .= " AND `jb_fee_invoice`.`invoice_status` = '" . $this->paidStatus("paid") . "'";

    $sql .= " ORDER BY jb_fee_invoice.recp_date DESC";


    $res = $this->getResults($sql);
    return $res;
}

    public function cashierBranch($id){
        $sql = "SELECT
            `jb_branch_operators`.`id`
            , `jb_branches`.`eng_name`
        FROM
            `jb_branch_operators`
            LEFT JOIN `jb_branches` 
                ON (`jb_branch_operators`.`branch_id` = `jb_branches`.`id`) WHERE 1";
        $sql .= " AND  `jb_branch_operators`.`user_id` = $id LIMIT 1";
        $res = $this->getSingle($sql);
        return @$res['eng_name'];

    }

    public function branchOperator($branch){
        $sql = "SELECT
    `jb_users`.`id`
    , `jb_users`.`name`
FROM
    `jb_users`
    INNER JOIN `jb_branch_operators` 
        ON (`jb_users`.`id` = `jb_branch_operators`.`user_id`)
WHERE (`jb_branch_operators`.`branch_id` = $branch)";
        $res = $this->getSingle($sql);
        return $res;
    }


    public function updateDiscuntPaid($id, $discount,$invoiceId,$student_id)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_paid";
        //$update_where = array('invoice_id' => $id, "student_id" => $student_id);
        $update_where = array("id" => $id, "student_id" => $student_id);
        $dateTime = date("Y-m-d H:i:s");
        $user = $this->getUserId();
        $updateData['discount'] = $discount;
        $updateData['invoice_id'] = $invoiceId;
        $updateData['paid_status'] = $this->paidStatus("paid");
        $updateData['updated_user_id'] = $user;
        $updateData['updated'] = $dateTime;

        $this->update($table, $updateData, $update_where);
    }

    public function updatePaidReceive($id, $invoiceId, $timeStatus,$student_id)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_paid";
        //$update_where = array('invoice_id' => $id, "student_id" => $student_id);
        $update_where = array("id" => $id, "student_id" => $student_id);
        $dateTime = date("Y-m-d H:i:s");
        $user = $this->getUserId();
        $updateData['invoice_id'] = $invoiceId;
        $updateData['paid_status'] = $timeStatus;
        $updateData['updated_user_id'] = $user;
        $updateData['updated'] = $dateTime;

        $this->update($table, $updateData, $update_where);
    }

    public function cashierData($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            SUM(`jb_fee_paid`.`fees`) AS `fees`
            , SUM(`jb_fee_paid`.`discount`) AS `discount`
            , `jb_fee_invoice`.`recp_date`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_invoice` 
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`) WHERE 1";

        $sql .= " AND `jb_fee_invoice`.`created_user_id` = " . $this->getUserId();
        $sql .= " AND `jb_fee_paid`.`paid_status` <> '" .  $this->paidStatus("pending") . "'";

        if (!empty($param['rcp_start_date']) && !empty($param['rcp_end_date'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`recp_date` BETWEEN '" . $param['rcp_start_date'] . "' AND '" . $param['rcp_end_date'] . "'";
        }

        $sql .= " GROUP BY recp_date ";
        $sql .= " ORDER BY recp_date ASC";
        $res = $this->getResults($sql);
        return $res;
    }

    public function cashierCollections($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            SUM(`jb_fee_paid`.`fees`) AS `fees`
            , SUM(`jb_fee_paid`.`discount`) AS `discount`
            , `jb_fee_invoice`.`recp_date`
            , `jb_fee_invoice`.`created_user_id` AS `recp_user_id`
            , `jb_users`.`name`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_invoice` 
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
            INNER JOIN `jb_users` 
                ON (`jb_fee_invoice`.`created_user_id` = `jb_users`.`id`) ";

        $sql .= " WHERE 1";
        //$sql .= " AND recp_user_id = " . $this->getUserId();
        if (!empty($param['rcp_start_date']) && !empty($param['rcp_end_date'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`recp_date` BETWEEN '" . $param['rcp_start_date'] . "' AND '" . $param['rcp_end_date'] . "'";
        }

        $sql .= " AND  `jb_fee_paid`.`paid_status` <> '" . $this->paidStatus("pending") . "'";
        $sql .= " GROUP BY recp_date, recp_user_id ";
        $sql .= " ORDER BY recp_date ASC";
        $res = $this->getResults($sql);
        return $res;
    }

    public function updateInvoiceReciep($id)
    {
        $pr = $this->getPrefix();
        $table = $pr . "fee_invoice";
        //$dateTime = date("Y-m-d H:i:s");
        //$user = $this->getUserId();
        $updateData['invoice_status'] = $this->paidStatus("paid");
        //$updateData['updated_user_id'] = $user;
        //$updateData['updated'] = $dateTime;
        $update_where = array('invoice_id' => $id);
        $this->update($table, $updateData, $update_where, 1);

    }



    public function recpData($param = array())
    {
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_fee_paid`.`id`
            , `jb_fee_paid`.`student_id`
            , `jb_fee_paid`.`type_id`
            , `jb_fee_paid`.`invoice_id`
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_invoice`.`recp_date`
            , `jb_fee_invoice`.`invoice_id` AS invoice_number
            , `jb_fee_type`.`title_en` AS title_en
            , `jb_fee_type`.`duration_type` AS duration_type
            , `jb_fee_paid`.`student_id`
            , `jb_students`.`eng_name` name
            , `jb_students`.`gender`
            , `jb_students`.`eng_fname` father_name
            , `jb_students`.`grnumber`
            , `jb_students`.`branch_id`
            , `jb_branches`.`eng_name` branch_title
            , `jb_branches`.`branch_fone`
            , `jb_classes`.`eng_name` class_title
            , `jb_sections`.`title` section_title
            , `jb_users`.`name` username
            , `jb_users`.`id` userid
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_invoice`
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_students`
                ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_branches`
                ON (`jb_fee_paid`.`branch_id` = `jb_branches`.`id`)
            INNER JOIN `jb_classes`
                ON (`jb_fee_paid`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections`
                ON (`jb_fee_paid`.`section_id` = `jb_sections`.`id`) 
            INNER JOIN `jb_users`
                ON (`jb_fee_invoice`.`created_user_id` = `jb_users`.`id`)
                
                WHERE 1";

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`student_id` = '" . $param['id'] . "'";
        }


        if (!empty($param['invoice'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`id` = " . $param['invoice'];
        }


        if (!empty($param['invoice_number'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`invoice_id` = '" . $param['invoice_number'] . "'";
        }

        if (!empty($param['date'])) {
            $date = $param['date'];
            $sql .= " AND `" . $pr . "fee_invoice`.`recp_date` = '$date' AND invoice_status = '" . $this->paidStatus("paid") . "'";
        }

        if (!empty($param['status'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`invoice_status` = '" . $param['status'] . "'";
        }


        if (!empty($param['month'])) {
            if($this->checkDateFormat($param['month'])){
                $sql .= " AND `" . $pr . "fee_invoice`.`invoice_date` = '" . $param['month'] . "'";
            }
        }


        $res = $this->getResults($sql);

        return $res;
    }


    public function deletePaidId($id){
        $where = array( 'id' => $id);
        $this->delete( 'jb_fee_paid', $where, 1 );
    }

    public function paidAndDefaulterList($param = array())
    {
        $pr = $this->getPrefix();

        $sql = "SELECT
            `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`invoice_id`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_paid`.`type_id`
            , `jb_fee_type`.`title`
            , `jb_fee_type`.`title_en`
            , `jb_fee_paid`.`student_id`
            , `jb_fee_paid`.`id` AS fee_paid_id_primary
            , `jb_students`.`name`
            , `jb_students`.`gender`
            , `jb_students`.`fname`
            , `jb_students`.`grnumber`
            , `jb_fee_type`.`duration_type`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_paid`.`paid_status`
            , `jb_classes`.`title` AS class_title
            , `jb_sections`.`title` AS section_title
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type` 
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_students` 
                ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`) 
            INNER JOIN `jb_classes` 
                ON (`jb_fee_paid`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections` 
                ON (`jb_fee_paid`.`section_id` = `jb_sections`.`id`)    
                WHERE 1";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`session_id` = " . $param['session'];
        }

        /*if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`class_id` = " . $param['class'];
        }*/



        if (!empty($param['class'])) {

            if(is_array($param['class'])){
                $sql .= " AND " . $this->makeOrQuery("`" . $pr . "fee_paid`.`class_id`",$param['class']);
            }
            else{
                $sql .= " AND `" . $pr . "fee_paid`.`class_id` = " . $param['class'];
            }

        }



        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`section_id` = " . $param['section'];
        }

        if (!empty($param['student_id'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`student_id` = " . $param['student_id'];
        }


        if (!empty($param['fee_start_date']) && !empty($param['fee_end_date'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`fee_date` BETWEEN '" . $param['fee_start_date'] . "' AND '" . $param['fee_end_date'] . "'";
        }


        if (!empty($param['paid_status'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`paid_status` = '" . $param['paid_status'] . "'";
        }

        if (!empty($param['not_paid_status'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`paid_status` <> '" . $param['not_paid_status'] . "'";
        }

        if (!empty($param['stu_status'])) {
            $sql .= " AND `" . $pr . "students`.`student_status` = '" . $param['stu_status'] . "'";
        }


        if (!empty($param['deposit_id'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`deposit_id` = " . $param['deposit_id'];
        }

        if (!empty($param['feeType'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`type_id` = " . $param['feeType'];
        }

        $sql .= " ORDER BY `jb_fee_paid`.`class_id`, `jb_fee_paid`.`section_id`, `jb_fee_paid`.`student_id`";



        $res = $this->getResults($sql);

        return $res;


    }


    public function GetPaidDataSummary($param = array()){
        $sql = "SELECT fees, discount, student_id, branch_id, paid_status  FROM `jb_fee_paid`";
        $sql .= " WHERE 1 ";
        if(!empty($param['date'])){
            $sql .= " AND fee_date = '".$param['date']."'";
        }

        if(!empty($param['type'])){
            $sql .= " AND type_id = " . $param['type'];
        }

        if(!empty($param['branch'])){
            $sql .= " AND branch_id = " . $param['branch'];
        }


        $sql .= " GROUP BY student_id";

        $res = $this->getResults($sql);
        return $res;
    }

    public function GetStudentDiscountRef($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_fee_discounts`.`student_id`
            , `jb_fee_discounts`.`refrence`
        FROM
            `jb_fee_discounts`
            INNER JOIN `jb_students` 
                ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`)
        WHERE  1 ";
        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }
        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetPaidData($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
            `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_invoice`.`invoice_id`
            , `jb_fee_paid`.`student_id`
            , `jb_students`.`name`
            , `jb_students`.`fname`
            , `jb_students`.`gender`
            , `jb_students`.`grnumber`
            , `jb_fee_paid`.`type_id`
            , `jb_classes`.`title` AS `class_title`
            , `jb_fee_type`.`title_en`
            , `jb_fee_type`.`duration_type`
            , `jb_fee_paid`.`paid_status`
            , `jb_fee_invoice`.`recp_date`
            , `jb_fee_paid`.`fee_date`
            , `jb_sections`.`title` AS section_title
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_invoice` 
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
            INNER JOIN `jb_students` 
                ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`)
            INNER JOIN `jb_classes` 
                ON (`jb_students`.`class_id` = `jb_classes`.`id`)
            INNER JOIN `jb_sections` 
                ON (`jb_students`.`section_id` = `jb_sections`.`id`) 
            INNER JOIN `jb_fee_type` 
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`) WHERE 1";

        if (!empty($param['paid_status'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`paid_status` = '" . $param['paid_status'] . "'";
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`branch_id` = " . $param['branch'];
        }

        /*if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`session_id` = " . $param['session'];
        }*/

        if (!empty($param['class'])) {

            if(is_array($param['class'])){
                $sql .= " AND " . $this->makeOrQuery("`" . $pr . "fee_paid`.`class_id`",$param['class']);
            }
            else{
                $sql .= " AND `" . $pr . "fee_paid`.`class_id` = " . $param['class'];
            }

        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`section_id` = " . $param['section'];
        }

        if (!empty($param['recp_start_date']) && !empty($param['recp_end_date'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`recp_date` BETWEEN '" . $param['recp_start_date'] . "' AND '" . $param['recp_end_date'] . "'";
        }


        if (!empty($param['for_deposit'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`deposit_id` IS NULL";
        }

        if (!empty($param['depositer'])) {
            $sql .= " AND `" . $pr . "fee_invoice`.`created_user_id` = " . $param['depositer'];
        }

        if (!empty($param['feeType'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`type_id` = " . $param['feeType'];
        }



        if (!empty($param['class'])) {

            if (is_array($param['class'])) {
                $sql .= " ORDER BY `" . $pr . "fee_paid`.`class_id`, `" . $pr . "students`.`id`";
            }
        }

        $res = $this->getResults($sql);

        return $res;

    }

    public function convertNumberToWord($num = false)
    {
        $num = str_replace(array(',', ' '), '', trim($num));
        if (!$num) {
            return false;
        }
        $num = (int)$num;
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
                'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
        );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
                'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
                'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
        );
        $num_length = strlen($num);
        $levels = (int)(($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int)($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');
            $tens = (int)($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int)($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . (($levels && ( int )($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }

    public function seePendingInvoince($id){

     $checkInvoice = array(
         'paid_status' => $this->paidStatus("pending"),
         'student_id' => $id
      );
     return $this->exists( 'jb_fee_paid', 'student_id', $checkInvoice );
    }


    public function genInvoiceNumber($date,$stuid){
        $idDate = date("ym", strtotime($date));
        $idPlusMonth = $idDate . $stuid;

        $totalThisMonthEntry = $this->countThisMonthInvlice($date,$stuid);
        $newNumber = $totalThisMonthEntry + 1;

        $idLen = strlen($stuid);
        $numLen = strlen($newNumber);
        $totalLen = ($idLen + 4 + $numLen);

        $totalZeros = 14 - $totalLen;
        $zerosStrings = "";
        for ($i=0; $i < $totalZeros;$i++){
            $zerosStrings .= "A";
        }
        $invoiceNumber = $idPlusMonth . $zerosStrings . $newNumber;

        //echo '<pre>'; print_r($invoiceNumber); echo '</pre>';
        return $invoiceNumber;
    }

    public function zakatFees($param = array()){
        $sql = "SELECT
            `jb_fee_type`.`title_en`
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`fee_date`
            , `jb_fee_type`.`duration_type`
             , `jb_fee_paid`.`due_date`
             , `jb_fee_paid`.`id` AS `paid_id`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type` 
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`) WHERE 1";
        $sql .= " AND student_id = " . $param['id'];
        $sql .= " AND paid_status = '" . $this->paidStatus("exempt") . "'";
        $res = $this->getResults($sql);
        return $res;
    }

    public function countThisMonthInvlice($date,$id){
        $sql = "SELECT COUNT(*) AS tot FROM jb_fee_invoice WHERE fee_month = '$date' AND student_id = $id";
        $res = $this->getSingle($sql);
        return $res['tot'];
    }

    public function setAdvancedInvoiceDueDate($date){
        return date("Y-m-10", strtotime($date));
    }

    public function notDepositedFee($session,$branch){
        $paid = $this->paidStatus("paid");
        /*$sql = "SELECT
            `jb_fee_paid`.`type_id`
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`deposit_id`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_invoice`.`invoice_id`
            , `jb_fee_invoice`.`created_user_id`
            , `jb_gl_module_codes`.`gl_module_id`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_invoice`
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
            INNER JOIN `jb_gl_module_codes`
                ON (`jb_fee_paid`.`class_id` = `jb_gl_module_codes`.`class_id`)
        WHERE  1 ";
        $sql .= " AND `jb_fee_paid`.`paid_status` = '$paid'";
        $sql .= " AND `jb_fee_paid`.`deposit_id` IS NULL ";
        $sql .= " AND `jb_fee_paid`.`session_id` = $session ";
        $sql .= " AND `jb_fee_paid`.`branch_id` = $branch ";
        $sql .= " AND `jb_fee_invoice`.`created_user_id` = " . $this->getUserId();
        $sql .= " AND `jb_gl_module_codes`.`gl_module_id` = $glModule ";*/



        $sql = "SELECT
            `jb_fee_paid`.`type_id`
            , `jb_fee_paid`.`fees`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`deposit_id`
            , `jb_fee_paid`.`class_id`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_invoice`.`invoice_id`
            , `jb_fee_invoice`.`created_user_id`
            , `jb_students`.`id` AS student_id
            , `jb_students`.`name` AS student_name
                , `jb_students`.`fname` AS student_fname
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_invoice` 
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
            INNER JOIN `jb_students` 
        ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`)
        WHERE  1 ";
        $sql .= " AND `jb_fee_paid`.`paid_status` = '$paid'";
        $sql .= " AND `jb_fee_paid`.`deposit_id` = 0 ";
        $sql .= " AND `jb_fee_paid`.`session_id` = $session ";
        $sql .= " AND `jb_fee_paid`.`branch_id` = $branch ";
        $sql .= " AND `jb_fee_invoice`.`created_user_id` = " . $this->getUserId();





        $res = $this->getResults($sql);
        return $res;

    }

    public function GetGlModules(){
        $sql = "SELECT * FROM `jb_gl_modules` WHERE published = 1";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetGlModuleCodes(){
        $sql = "SELECT * FROM `jb_gl_module_codes` WHERE 1";
        $res = $this->getResults($sql);
        return $res;
    }


    public function unreadGlDeposits($zoneId){
        /*$sql = "SELECT
            `jb_fee_paid`.`fees` AS `orign_fee`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_type`.`gl_code` AS `gl_fee_type_id`
            , `jb_fee_deposits`.`bank`
            , `jb_fee_deposits`.`account_title`
            , `jb_fee_deposits`.`account_number`
            , `jb_fee_deposits`.`deposit_number`
            , `jb_fee_deposits`.`is_gl_read`
            , `jb_fee_invoice`.`invoice_id`
            , `jb_fee_deposits`.`id` AS `deposit_id`
            , `jb_fee_deposits`.`created`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_fee_deposits`
                ON (`jb_fee_paid`.`deposit_id` = `jb_fee_deposits`.`id`)
            INNER JOIN `jb_fee_invoice`
                ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
        WHERE (`jb_fee_deposits`.`is_gl_read` =0);";


        */


        /*$sql = "SELECT
    `jb_fee_deposits`.`id` AS `deposit_id`
    , `jb_fee_deposits`.`bank`
    , `jb_fee_deposits`.`bank_short_name`
    , `jb_fee_deposits`.`bank_code`
    , `jb_fee_deposits`.`bank_gl_code`
    , `jb_fee_deposits`.`account_title`
    , `jb_fee_deposits`.`account_number`
    , `jb_fee_deposits`.`deposit_number`
    , `jb_fee_deposits`.`gl_module_code`
    , `jb_fee_deposits`.`gl_branch_code`
    , `jb_fee_deposits`.`created`
    , `jb_fee_deposits`.`gl_branch_id` AS `fee_soft_branch_id`
    , `jb_branches`.`title` AS `fee_soft_branch_title`
    , `jb_zones`.`id` AS `fee_soft_zone_id`
    , `jb_zones`.`title` AS `fee_soft_zone_title`
FROM
    `jb_fee_deposits`
    INNER JOIN `jb_branches`
        ON (`jb_fee_deposits`.`gl_branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_zones`
        ON (`jb_branches`.`zone_id` = `jb_zones`.`id`) WHERE 1
        AND jb_fee_deposits.is_gl_read = 0
        AND jb_zones.id = $zoneId
        AND `jb_fee_deposits`.`created` > '2018-02-12 00:00:00'";*/


        $sql = "SELECT
            `jb_fee_paid`.`fees` AS `orign_fee`
            , `jb_fee_paid`.`discount`
            , `jb_fee_paid`.`id` AS paid_id
            , `jb_fee_type`.`gl_code` AS `gl_fee_type_id`
            , `jb_fee_deposits`.`bank`
            , `jb_fee_deposits`.`bank_short_name`
            , `jb_fee_deposits`.`bank_code`
            , `jb_fee_deposits`.`bank_gl_code`
            , `jb_fee_deposits`.`account_title`
            , `jb_fee_deposits`.`account_number`
            , `jb_fee_deposits`.`deposit_number`
            , `jb_fee_deposits`.`gl_module_code`
            , `jb_fee_deposits`.`gl_branch_code`
            , `jb_fee_deposits`.`is_gl_read`
            , `jb_fee_deposits`.`id` AS `deposit_id`
            , `jb_fee_deposits`.`created`
        FROM
            `jb_fee_paid`
            INNER JOIN `jb_fee_type`
                ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
            INNER JOIN `jb_fee_deposits`
                ON (`jb_fee_paid`.`deposit_id` = `jb_fee_deposits`.`id`)
        WHERE 1 ";

        $sql .= " AND `jb_fee_deposits`.`is_gl_read` = 0 AND `jb_fee_deposits`.`created` > '2018-02-12 00:00:00' ";

        if(!empty($zoneId)){
            $sql .= " AND `jb_fee_deposits`.`gl_zone_id` = $zoneId";
        }


        $res = $this->getResults($sql);

        //echo '<pre>'; print_r($sql); echo '</pre>';

        return $res;
    }

    public function getGlPaidDataByDepositId($depositId){
        $sql = "SELECT
    `jb_fee_paid`.`fees` AS `orign_fee`
    , `jb_fee_paid`.`discount`
    , `jb_fee_paid`.`id` AS `paid_id`
    , `jb_fee_type`.`gl_code` AS `gl_fee_type_id`
FROM
    `jb_fee_paid`
    INNER JOIN `jb_fee_type` 
        ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`) WHERE `jb_fee_paid`.`deposit_id` = $depositId";

        //echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);

        return $res;
    }

    public function getStudentFees($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_student_fees`.`id`
    , `jb_student_fees`.`fees`
    , `jb_student_fees`.`discount`
    , `jb_student_fees`.`student_id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`grnumber`
    ,`jb_fee_type`.`title`
    ,`jb_student_fees`.`type_id`
FROM
    `jb_student_fees`
    INNER JOIN `jb_fee_type` 
        ON (`jb_student_fees`.`type_id` = `jb_fee_type`.`id`)
    INNER JOIN `jb_students` 
        ON (`jb_student_fees`.`student_id` = `jb_students`.`id`) WHERE 1 ";

        /*if (!empty($param['type'])) {
            $sql .= " AND `" . $pr . "student_fees`.`type_id` = " . $param['type'];
        }*/

        if (!empty($param['student_id'])) {
            $sql .= " AND `" . $pr . "student_fees`.`student_id` = " . $param['student_id'];
        }

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



        if (!empty($param['fee_type'])) {

            if(is_array($param['fee_type'])){

                if($param['fee_type'][0] > 0){
                    $tool = $this->toolObj();
                    $i = 0;
                    $sql .= " AND (";
                    foreach ($param['fee_type'] as $key){


                        if($i>0){
                            $sql .= " OR ";
                        }
                        $sql .= "`" . $pr . "student_fees`.`type_id` = " . $tool->GetExplodedInt($key);
                        $i++;
                    }
                    $sql .= ")";
                }



            }
        }

        $sql .= " ORDER BY id DESC";

        $res = $this->getResults($sql);
        return $res;
    }

    public function getBankCopy($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_fee_paid`.`id`
    , `jb_fee_paid`.`student_id`
    , `jb_fee_paid`.`type_id`
    , `jb_fee_paid`.`fees`
    , `jb_fee_paid`.`discount`
    , `jb_fee_paid`.`fee_date`
    , `jb_fee_paid`.`paid_status`
    , `jb_fee_paid`.`invoice_id`
    , `jb_fee_type`.`title_en`
    , `jb_fee_type`.`duration_type`
    , `jb_students`.`eng_name` AS `name`
    , `jb_students`.`eng_fname` AS `father_name`
    , `jb_students`.`gender`
     , `jb_students`.`grnumber`
    , `jb_fee_paid`.`branch_id`
    , `jb_branches`.`eng_name` AS `branch_title`
    , `jb_branches`.`branch_fone`
    , `jb_classes`.`eng_name` AS `class_title`
    , `jb_fee_paid`.`class_id`
    , `jb_sections`.`title` AS `section_title`
    
FROM
    `jb_fee_paid`
    INNER JOIN `jb_students` 
        ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_fee_paid`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_fee_paid`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sessions` 
        ON (`jb_fee_paid`.`session_id` = `jb_sessions`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_fee_paid`.`section_id` = `jb_sections`.`id`)
    INNER JOIN `jb_fee_type` 
        ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)   
        
        WHERE 1";


        if (!empty($param['student_id'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`student_id` = " . $param['student_id'];
        }

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`class_id` = " . $param['class'];
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`session_id` = " . $param['session'];
        }

        if (!empty($param['invoice'])) {
            $sql .= " AND `" . $pr . "fee_paid`.`invoice_id` = " . $param['invoice'];
        }

        $sql .= " AND `" . $pr . "fee_paid`.`paid_status` = '" . $this->paidStatus("pending") . "'" ;
        $sql .= " AND `" . $pr . "students`.`student_status` = 'current'" ;

        //$sql .= " GROUP BY `" . $pr . "fee_paid`.`student_id`, `" . $pr . "fee_paid`.`type_id`, `" . $pr . "fee_paid`.`fee_date`";

        //echo '<pre>'; print_r($sql); echo '</pre>';
        $res = $this->getResults($sql);
        return $res;
    }

    public function insertStudentFees($data)
    {
        $pr = $this->getPrefix();
        $table = $pr . "student_fees";
        return $this->insert($table, ($data));

    }

    public function removeDefinedDiscount($studentId,$typeId){
        $pr = $this->getPrefix();
        //$whereColumn = "id";
        $table = $pr .  "fee_discounts";

        $where = array( "student_id" => $studentId, "type_id" => $typeId);

        if(!empty($studentId) && !empty($typeId)){
            $this->delete( $table, $where, 1 );
        }
    }

    public function getBranchBanck($branchId){
        $sql = "SELECT `jb_branch_banks`.`branch_bank`
    , `jb_branch_banks`.`branch_bank_account_title` AS `branch_bank_title`
    , `jb_branch_banks`.`branch_bank_ac_number` AS `branch_bank_ac_number`
    , `jb_branch_banks`.`branch_bank_code`
    , `jb_branch_banks`.`branch_bank_phone` FROM jb_branch_banks WHERE 1";
        $sql .= " AND `jb_branch_banks`.`branch_id` = $branchId";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function insertBankInvoiceLog($data = array())
    {

        $tableName = "jb_fee_bank_invoice_log";

        $columns = $this->getTableCols($tableName);

        $ids = $this->insert_multi($tableName, $columns, $data);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function removeStudentFees($id){
        $pr = $this->getPrefix();
        $whereColumn = "id";
        $table = $pr .  "student_fees";

        $where = array( $whereColumn => $id);

        if(!empty($id)){
            $this->delete( $table, $where, 1 );
        }

    }


//////////////////////////////////////////////////////////////
    public function genAdvanceFeeStructure($branch,$class,$session){
        $sql = "SELECT
            `jb_fee_type`.`title`
            , `jb_fee_type`.`title_en`
            , `jb_fee_type`.`id` AS type_id
            , `jb_fee_structure`.`class_id`
            , `jb_fee_structure`.`fees`
        FROM
            `jb_fee_structure`
            INNER JOIN `jb_fee_type` 
                ON (`jb_fee_structure`.`fee_type_id` = `jb_fee_type`.`id`)
        WHERE (`jb_fee_structure`.`branch_id` = $branch
            AND `jb_fee_structure`.`class_id` = $class
            AND `jb_fee_structure`.`session_id` = $session)";
        $res = $this->getResults($sql);
        return $res;
    }


    public function getPaidStudentData($param = array()){
        $id = $param['id'];
        $start = $param['start'];
        $end = $param['end'];
        $sql = "SELECT
            `type_id`
            , `fees`
            , `discount`
            , `fee_date`
        FROM
            `jb_fee_paid`
        WHERE (`paid_status` <> '" . $this->paidStatus("pending") . "'
            AND `student_id` = $id AND fee_date BETWEEN '$start' AND '$end')";
        $res = $this->getResults($sql);
        return $res;
    }


    public function getPendingInvoice($id){
        $sql = "SELECT * FROM jb_fee_invoice WHERE student_id = $id AND invoice_status = '" . $this->paidStatus("pending") . "'";


        $res = $this->getSingle($sql);
        return $res;
    }

    public function removeDiscount($id){
        $whereColumn = "id";
        $table = "jb_fee_discounts";

        $where = array( $whereColumn => $id);

        if(!empty($id)){
            $this->delete( $table, $where, 1 );
        }

    }


    public function removeRequest($id){
        $whereColumn = "id";
        $table = "jb_discount_requests";

        $where = array( $whereColumn => $id);

        if(!empty($id)){
            $this->delete( $table, $where, 1 );
        }

    }



    public function discountBranchSummary($date,$branch){
        $sql = "SELECT COUNT(*) AS tot, `fees`, `discount` FROM `jb_fee_paid` WHERE 1 AND `fee_date` = '$date' ";
        $sql .= "AND branch_id = $branch AND type_id = 1 ";

        $sql .= "GROUP BY (`discount` - `fees`)";
        $sql .= "ORDER BY discount DESC ";
        $res = $this->getResults($sql);
        return $res;
    }

    public function GetBranchBank($branch,$module){
        $sql = "SELECT
            `jb_branch_banks`.`branch_bank`
            , `jb_branch_banks`.`branch_bank_ac_number`
            , `jb_branch_banks`.`branch_bank_account_title`
            , `jb_branch_banks`.`branch_bank_short_name`
            , `jb_branch_banks`.`branch_bank_code`
            , `jb_branch_banks`.`bank_gl_code`
            , `jb_gl_module_codes`.`gl_module_code`
            , `jb_gl_branch_codes`.`glcode` AS `branch_gl_code`
        FROM
            `jb_branch_banks`
            INNER JOIN `jb_gl_module_codes` 
                ON (`jb_gl_module_codes`.`gl_module_id` = `jb_branch_banks`.`module_id`)
            INNER JOIN `jb_gl_branch_codes` 
                ON (`jb_gl_branch_codes`.`branch_id` = `jb_branch_banks`.`branch_id`)
         WHERE 1";

        $sql .= " AND jb_branch_banks.branch_id = $branch AND jb_branch_banks.module_id = $module";
        $sql .= " GROUP BY `jb_branch_banks`.`branch_id`, `jb_branch_banks`.`module_id`";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function GetDepositAmount($deposit){
        $sql = "SELECT (SUM(fees) - SUM(discount)) AS tot FROM `jb_fee_paid` WHERE deposit_id = $deposit";
        $res = $this->getSingle($sql);
        return $res['tot'];
    }

    public function GetDepositDetail($deposit){
        $sql = "SELECT * FROM `jb_fee_deposits` WHERE id = $deposit";
        $res = $this->getSingle($sql);
        return $res;
    }




    public function dailyStatement($date,$user){
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_fee_paid`.`fees`
    , `jb_fee_paid`.`discount`
    , `jb_fee_paid`.`fee_date`
    , `jb_fee_type`.`title` AS `type_title`
    , `jb_fee_type`.`id` AS `type_id`
    , `jb_fee_type`.`duration_type` AS `duration_type`
    , `jb_fee_invoice`.`id` AS invoice_id
    , `jb_fee_invoice`.`invoice_id` AS invoice_number
    , `jb_students`.`id`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
    , `jb_classes`.`id` AS `class_id`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
    , `jb_fee_invoice`.`recp_date`
FROM
    `jb_fee_paid`
    INNER JOIN `jb_fee_invoice` 
        ON (`jb_fee_paid`.`invoice_id` = `jb_fee_invoice`.`id`)
    INNER JOIN `jb_fee_type` 
        ON (`jb_fee_paid`.`type_id` = `jb_fee_type`.`id`)
    INNER JOIN `jb_students` 
        ON (`jb_fee_paid`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_fee_paid`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_fee_paid`.`section_id` = `jb_sections`.`id`)
WHERE `jb_fee_invoice`.`created_user_id` = $user
    
    
    AND `jb_fee_invoice`.`recp_date` = '$date'
    AND `jb_fee_paid`.`paid_status` = 'paid'
    AND `jb_fee_invoice`.`invoice_status` = 'paid'
     ";

        //AND `jb_fee_paid`.`branch_id` = $branch

    /*if (!empty($param['class'])) {

        if(is_array($param['class'])){
            $sql .= " AND " . $this->makeOrQuery("`" . $pr . "fee_paid`.`class_id`",$param['class']);
        }
        else{
            $sql .= " AND `" . $pr . "fee_paid`.`class_id` = " . $param['class'];
        }

    }*/


        //AND `jb_fee_paid`.`updated_user_id` = $user

        $res = $this->getResults($sql);

        return $res;
    }

    public function getGlModuleCodeAndName(){
        $sql = "SELECT
    `jb_gl_modules`.`title`
    ,`jb_gl_modules`.`id`
    , `jb_gl_module_codes`.`class_id`
    , `jb_gl_module_codes`.`gl_module_code`
FROM
    `jb_gl_modules`
    INNER JOIN `jb_gl_module_codes` 
        ON (`jb_gl_modules`.`id` = `jb_gl_module_codes`.`gl_module_id`)";

        $res = $this->getResults($sql);

        return $res;
    }


    public function discountRequests($param = array()){

        $sql = "SELECT
  `jb_discount_requests`.`id`
    , `jb_discount_requests`.`student_id`
    , `jb_discount_requests`.`type_id`
    , `jb_discount_requests`.`user_id`
    , `jb_discount_requests`.`amount`
    , `jb_discount_requests`.`path`
    , `jb_discount_requests`.`image`
    , `jb_fee_type`.`title` AS `fee_type_title`
    , `jb_users`.`name` AS `user_name`
    , `jb_discount_requests`.`student_id`
FROM
    `jb_discount_requests`
    INNER JOIN `jb_fee_type` 
        ON (`jb_discount_requests`.`type_id` = `jb_fee_type`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_discount_requests`.`user_id` = `jb_users`.`id`)
WHERE 1";

        if(!empty($param['student_id'])){
            $studentId = $param['student_id'];
            $sql .= " AND `jb_discount_requests`.`student_id` = $studentId";
        }

        if(!empty($param['id'])){
            $id = $param['id'];
            $sql .= " AND `jb_discount_requests`.`id` = $id";
        }

        $res = $this->getResults($sql);
        return $res;
    }

    public function approveRequestList($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_discount_requests`.`id`
    , `jb_discount_requests`.`student_id`
    , `jb_discount_requests`.`type_id`
    , `jb_discount_requests`.`refrence`
    , `jb_discount_requests`.`user_id`
    , `jb_discount_requests`.`amount`
    , `jb_discount_requests`.`path`
    , `jb_discount_requests`.`image`
    , `jb_fee_type`.`title` AS `type_title`
    , `jb_discount_refrence`.`title` AS `ref_title`
    , `jb_users`.`name` AS `user_name`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_branches`.`title` AS `branch_title`
    , `jb_classes`.`title` AS `class_title`
    , `jb_sections`.`title` AS `section_title`
FROM
    `jb_discount_requests`
    INNER JOIN `jb_fee_type` 
        ON (`jb_discount_requests`.`type_id` = `jb_fee_type`.`id`)
    INNER JOIN `jb_discount_refrence` 
        ON (`jb_discount_requests`.`refrence` = `jb_discount_refrence`.`id`)
    INNER JOIN `jb_users` 
        ON (`jb_discount_requests`.`user_id` = `jb_users`.`id`)
    INNER JOIN `jb_students` 
        ON (`jb_discount_requests`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_branches` 
        ON (`jb_students`.`branch_id` = `jb_branches`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_students`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_students`.`section_id` = `jb_sections`.`id`) WHERE 1
 ";


        if (!empty($param['zone'])) {
            $sql .= " AND `" . $pr . "branches`.`zone_id` = " . $param['zone'];
        }

        $sql .= " GROUP BY `jb_discount_requests`.`student_id`, `jb_discount_requests`.`type_id`";

        ///echo '<pre>'; print_r($sql); echo '</pre>';
        $res = $this->getResults($sql);
        return $res;
    }

    public function getSingleRequest($id){
        $sql = "SELECT * FROM `jb_discount_requests` WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res;
    }

    public function checkExistingDiscount($studentId,$typeId){

        $check = array(
          'student_id' => $studentId,
          'type_id' => $typeId
        );
        return $this->exists( 'jb_fee_discounts', 'id', $check );
    }

    public function approveDiscount($studentId,$amount,$type,$refrence,$requestUserId,$path,$image){
        $userId = $this->getUserId();
        $dateTime = date("Y-m-d H:i:s");
        $sql = "INSERT INTO `jb_fee_discounts`";
        $sql .= "(`id`,`student_id`,`amount`,`type_id`,`refrence`,`request_user_id`,`path`,`image`,";
        $sql .= "`created_user_id`,`updated_user_id`,`created`,`updated`) VALUES";
        $sql .= "(NULL,$studentId,$amount,$type,$refrence,$requestUserId,'$path','$image',$userId,$userId,'$dateTime','$dateTime')";
        $sql .= " ON DUPLICATE KEY UPDATE updated_user_id = " . $userId;
        $sql .= ", updated='$dateTime'";
        $sql .= ", amount=$amount";
        $sql .= ", request_user_id=$requestUserId";
        $sql .= ", path='$path'";
        $sql .= ", image='$image'";
        return $this->link->query($sql);
    }

    public function newDisCountList($param = array()){
        $pr = $this->getPrefix();
        $sql = "SELECT
    `jb_fee_discounts`.`student_id`
    , `jb_fee_discounts`.`amount`
    , `jb_fee_discounts`.`type_id`
    , `jb_fee_discounts`.`refrence`
    , `jb_fee_discounts`.`request_user_id`
    , `jb_fee_discounts`.`path`
    , `jb_fee_discounts`.`image`
    , `jb_students`.`name`
    , `jb_students`.`fname`
    , `jb_students`.`gender`
    , `jb_students`.`grnumber`
    , `jb_fee_type`.`title` AS `type_title`
    , `jb_discount_refrence`.`title` AS `ref_title`
    , `jb_students`.`class_id`
    , `jb_classes`.`title` AS `class_title`
    , `jb_students`.`section_id`
    , `jb_sections`.`title` AS `section_title`
FROM
    `jb_fee_discounts`
    INNER JOIN `jb_students` 
        ON (`jb_fee_discounts`.`student_id` = `jb_students`.`id`)
    INNER JOIN `jb_fee_type` 
        ON (`jb_fee_discounts`.`type_id` = `jb_fee_type`.`id`)
    INNER JOIN `jb_discount_refrence` 
        ON (`jb_fee_discounts`.`refrence` = `jb_discount_refrence`.`id`)
    INNER JOIN `jb_classes` 
        ON (`jb_students`.`class_id` = `jb_classes`.`id`)
    INNER JOIN `jb_sections` 
        ON (`jb_students`.`section_id` = `jb_sections`.`id`)
WHERE 1";

        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "students`.`branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "students`.`class_id` = " . $param['class'];
        }

        if (!empty($param['classes'])) {

            if(is_array($param['classes'])){

                if($param['classes'][0] > 0){
                    $tool = $this->toolObj();
                    $i = 0;
                    $sql .= " AND (";
                    foreach ($param['classes'] as $key){


                        if($i>0){
                            $sql .= " OR ";
                        }
                        $sql .= "`" . $pr . "students`.`class_id` = " . $tool->GetExplodedInt($key);
                        $i++;
                    }
                    $sql .= ")";
                }



            }
        }

        if (!empty($param['section'])) {
            $sql .= " AND `" . $pr . "students`.`section_id` = " . $param['section'];
        }

        if (!empty($param['session'])) {
            $sql .= " AND `" . $pr . "students`.`session_id` = " . $param['session'];
        }

        if (!empty($param['type'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`type_id` = " . $param['type'];
        }

        if (!empty($param['id'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`student_id` = " . $param['id'];
        }


        if (!empty($param['refrence'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`refrence` = " . $param['refrence'];
        }

        if (!empty($param['fee_type'])) {
            $sql .= " AND `" . $pr . "fee_discounts`.`type_id` = " . $param['fee_type'];
        }

        $sql .= " AND `" . $pr . "students`.`student_status` = '" . $this->stuStatus("current") . "'";
        $sql .= " ORDER BY `jb_students`.`id` ASC";

        if (!empty($param['limit'])) {
            $sql .= " LIMIT " . $param['limit'];
        }

        //echo '<pre>'; print_r($sql); echo '</pre>';

        $res = $this->getResults($sql);
        return $res;
    }


    public function depositReport($param = array()){
        $pr = $this->getPrefix();

        $sql = "SELECT
    `jb_fee_deposits`.`bank`
    , `jb_fee_deposits`.`id`
    , `jb_fee_deposits`.`account_title`
    , `jb_fee_deposits`.`account_number`
    , `jb_fee_deposits`.`deposit_number`
    , `jb_fee_deposits`.`is_gl_read`
    , `jb_users`.`name` AS `user_name`
FROM
    `jb_fee_deposits`
    INNER JOIN `jb_users` 
        ON (`jb_fee_deposits`.`user_id` = `jb_users`.`id`)
     WHERE 1 ";


        if (!empty($param['branch'])) {
            $sql .= " AND `" . $pr . "fee_deposits`.`gl_branch_id` = " . $param['branch'];
        }

        if (!empty($param['class'])) {
            $sql .= " AND `" . $pr . "fee_deposits`.`gl_modue_id` = " . $param['class'];
        }

        if (!empty($param['user'])) {
            $sql .= " AND `" . $pr . "fee_deposits`.`user_id` = " . $param['user'];
        }

        if (!empty($param['date']) && !empty($param['to_date'])) {
            $sql .= " AND `" . $pr . "fee_deposits`.`created` BETWEEN '" . $param['date'] . " 00:00:00' AND '" . $param['to_date'] . " 23:59:59'";
        }

        $res = $this->getResults($sql);
        return $res;
    }


    public function getZoneIdByBranchId($id){
        $sql = "SELECT zone_id FROM jb_branches WHERE id = $id";
        $res = $this->getSingle($sql);
        return $res['zone_id'];
    }
}
