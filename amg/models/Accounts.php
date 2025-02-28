<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 9/14/2018
 * Time: 12:39 PM
 */




class Accounts extends BaseModel{

    const BANK_VOUCHER = 1;
    const CASH_VOUCHER = 2;
    const GEN_VOUCHER = 3;

    protected function getTableName(){}


    public function insertChartOfAccount($data){
        $prefix = $this->getPrefix();
        $table = "ac_chart_of_accounts";
        $tableName = $prefix . $table;

        if($this->insert( $tableName, $data )){
            return $this->lastid();
        }
        else{
            return $this->getError();
        }
    }

    public function getTypeByPage($page){
        $arr = array(
                "cashvoucher" => self::CASH_VOUCHER
               ,"bankvoucher" => self::BANK_VOUCHER
               ,"generalvoucher" => self::GEN_VOUCHER
        );

        return $arr[$page];
    }

    public function countChild($parent){
        $prefix = $this->getPrefix();
        $table = "ac_chart_of_accounts";
        $tableName = $prefix . $table;
        $sql = "SELECT COUNT(*) AS tot FROM `$tableName` WHERE parent_id = $parent AND is_root <> 1";

        $res = $this->getSingle($sql);
        return $res['tot'];
    }





    public function makeCode($parent){
        $totalChilds = $this->countChild($parent);

        if(empty($parent)){
            $code = "";
        }
        else{
            $codeRes = $this->getChartOfAccount(array("id" => $parent));
            $code = $codeRes['code'];
        }




        $plusOneLevel = $totalChilds + 1;

        $newCode = $code . $plusOneLevel;
        /*$newCode = $totalLevels + 1;
        $numLen = strlen($newCode);
        $levelLen = strlen($level);
        $totalLen = ($levelLen +  $numLen);
        $totalZeros = 8 - $totalLen;
        $zerosStrings = "";
        for ($i=0; $i < $totalZeros;$i++){
            $zerosStrings .= "0";
        }

        $code = $level . $zerosStrings . $newCode;*/

        return $newCode;
    }

    public function findChildrenByParent($id){
        $prefix = $this->getPrefix();
        $table = "ac_chart_of_accounts";
        $tableName = $prefix . $table;
        $sql = "SELECT level FROM `$tableName` WHERE 1 AND id = $id AND is_root <> 1";
        $res = $this->getSingle($sql);
        return $res['level'];
    }

    public function getChartOfAccount($param = array()){
        $prefix = $this->getPrefix();
        $table = "ac_chart_of_accounts";
        $tableName = $prefix . $table;
        $sql = "SELECT * FROM `$tableName` WHERE 1 ";

        if(!empty($param['id'])){
            $sql .= " AND id = " . $param['id'];
        }

        $sql .= " AND is_root <> 1";

        $res = $this->getSingle($sql);
        return $res;
    }

    public function getSettings($param = array()){
        $prefix = $this->getPrefix();
        $table = "ac_settings";
        $tableName = $prefix . $table;
        $sql = "SELECT * FROM `$tableName` WHERE 1";

        if(!empty($param['key'])){
            $sql .= " AND setting_key = '" . $param['key'] . "'";
        }

        if(!empty($param['key'])){
            $res = $this->getSingle($sql);
            return $res['setting_value'];
        }
        else{
            $res = $this->getResults($sql);
            return $res;
        }


    }

    public function getChartOfAccounts(){
        $prefix = $this->getPrefix();
        $table = "ac_chart_of_accounts";
        $tableName = $prefix . $table;
        $sql = "SELECT id, title AS text, code, level, parent_id FROM `$tableName` WHERE published = 1";

        //$sql .= " AND is_root <> 1";

        $sql .= " ORDER BY id, code";
        $res = $this->getResults($sql);
        return $res;
    }

    public function updateData($table,$update,$updateWhere){
         return $this->update( $table, $update, $updateWhere, 1 );
    }

    public function voucherNumber($date){
        $totalVoucher = $this->countTodayVouchers($date);
        $startNumber = date("ymd");
        $pluOneVoucher = $totalVoucher + 1;


        $numLen = strlen($pluOneVoucher);
        $startLen = strlen($startNumber);

        $totalLen = ($startLen +  $numLen);

        $totalZeros = 12 - $totalLen;
        $zerosStrings = "";
        for ($i=0; $i < $totalZeros;$i++){
            $zerosStrings .= "0";
        }

        $code = $startNumber . $zerosStrings . $pluOneVoucher;

        return $code;

    }

    public function insertVoucher($date,$type,$desc){
        $prefix = $this->getPrefix();
        $table = "ac_vouchers";
        $tableName = $prefix . $table;

        $data['code'] = $this->voucherNumber($date);
        $data['type_id'] = $type;
        $data['date'] = $date;
        $data['description'] = $desc;

        if($this->insert( $tableName, $this->setInsert($data) )){
            return $this->lastid();
        }
        else{
            return $this->getError();
        }
    }

    public function insertVoucherDetail($data){
        $pr = $this->getPrefix();
        $tableName = $pr . "ac_voucher_detail";

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


    public function countTodayVouchers($date){
        $sql = "SELECT COUNT(*) AS tot FROM jb_ac_vouchers WHERE 1 AND date = '$date'";
        $res = $this->getSingle($sql);
        return $res['tot'];
    }

    public function makeVoucherDetailArr($voucherId,$dataDetail){
        $vals = array();
        foreach ($dataDetail as $data){
            $vals[] = $this->setInsertDefaultValues(array($voucherId,$data['account_id'],$data['debit'],$data['credit']));
        }

        return $vals;
    }

    public function insertVoucherTransation($date,$type,$desc,$dataDetail){
        $res1 = false;
        $res2 = false;
        $this->query("START TRANSACTION");
        $lastId = $this->insertVoucher($date,$type,$desc);
        if(is_numeric($lastId)){
            $res1 = true;
            $vals = $this->makeVoucherDetailArr($lastId,$dataDetail);
            $resIns = $this->insertVoucherDetail($vals);
            if($resIns['status']){
                $res2 = true;
            }
        }

        if($res1 && $res2){
            $this->query("COMMIT;");
            return true;
        }
        else{
            $this->query("ROLLBACK;");
            return false;
        }

    }


}