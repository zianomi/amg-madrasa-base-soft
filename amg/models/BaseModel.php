<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "MysqliDb.php";
abstract class BaseModel extends MysqliDb
{


    protected $db;
    protected $tool;
    protected $trans = array();

    public function __construct()
    {
        global $tool;
        try {

            parent::__construct();
            $this->db = new MysqliDb();
            $this->tool = $tool;
        } catch (mysqli_sql_exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getTrans($transKey)
    {
        $data = $this->tool->getTransData();
        if (isset($data[$transKey])) {
            return $data[$transKey];
        }
        return $transKey;
    }

    /**
     * @param array $trans
     */
    public function setTrans($trans)
    {
        $this->trans = $trans;
    }





    public function getResults($sql)
    {

        $res = $this->db->get_results($sql);

        return $res;
    }

    public function getResultsOrArr($sql)
    {

        $res = $this->db->get_results($sql);

        if (!$res) {
            return array();
        }

        return $res;
    }



    public function getTableCols($table)
    {

        $sql = "DESCRIBE $table";
        $res = $this->getResults($sql);
        $data = array();
        foreach ($res as $row) {
            $data[] = $row;
        }

        $colums = array();
        foreach ($data as $key => $val) {

            if ($val['Field'] == "updated_user_id" || $val['Field'] == "updated") {
                continue;
            }
            $colums[$key] = $val['Field'];
        }

        return $colums;
    }

    public function getUserId()
    {
        return Tools::getUserId();
    }

    public function getError()
    {
        return $this->link->error;
    }


    protected function getDb()
    {
        $db = new AmgDb();
        return $db;
    }

    public function getPrefix()
    {
        return PR;
    }

    protected function getLangId()
    {
        return Tools::getLangId();
    }

    protected function getLang()
    {
        return Tools::getLang();
    }

    public function setInsertDefaultValues($data)
    {
        $date = date("Y-m-d H:i:s");
        //$vals = array(self::getUserId(),"NULL","$date","NULL");
        $vals = array(self::getUserId(), "$date");
        return array_merge($data, $vals);
    }


    public function getSingle($sql)
    {

        $res = $this->db->get_results($sql);

        return isset($res[0]) ? $res[0] : "";
    }

    public function setInsert($data)
    {
        $created = date("Y-m-d H:i:s");
        $userId = $this->getUserId();

        $data["created_user_id"] = $userId;
        $data["created"] = $created;
        return $data;
    }

    public function setUpdated($data)
    {
        $updated = date("Y-m-d H:i:s");
        $userId = $this->getUserId();

        $data["updated_user_id"] = $userId;
        $data["updated"] = $updated;
        return $data;
    }


    public function GetSqlMonth($date)
    {

        $date_arr = explode("-", $date);

        $month = $date_arr[1];

        $year = $date_arr[0];

        $start = $year . '-' . $month . '-01';

        $ends = date('m-t', mktime(0, 0, 0, $month, 1));

        $end = $year . '-' . $ends;


        return array("start" => $start, "end" => $end);
    }

    function GetSqlYear($date)
    {

        $date_arr = explode("-", $date);

        $year = $date_arr[0];

        $start = $year . '-' . '01-01';

        $end = ($year) . '-' . '12-31';

        return array("start" => $start, "end" => $end);
    }

    public function stuStatus($key)
    {
        $stuStatus = array();
        $stuStatus['current'] = 'current';
        $stuStatus['completed'] = 'completed';
        $stuStatus['dependent'] = 'dependent';
        $stuStatus['terminated'] = 'terminated';

        return $stuStatus[$key];
    }

    public function toolObj()
    {
        global $tool;
        return $tool;
    }

    public function Message($type, $msg)
    {
        $tool = $this->toolObj();
        return $tool->Message($type, $msg);
    }

    public function checkDateFormat($date)
    {
        $tool = $this->toolObj();

        return $tool->checkDateFormat($date);
    }

    public function insertBulk($table, $data = array(), $duplicate = true)
    {


        $columns = $this->getTableCols($table);


        $ids = $this->insert_multi($table, $columns, $data, $duplicate);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    public function insertBulkWithoutId($table, $data = array(), $duplicate = true)
    {


        $columns = $this->getTableCols($table);

        unset($columns[0]);


        $ids = $this->insert_multi($table, $columns, $data, $duplicate);
        if (!$ids) {
            $return = array("status" => false, "msg" => "insert failed: ");
        } else {
            $return = array("status" => true, "msg" => $this->Message("succ", $ids . " inserted"));
        }
        return $return;
    }

    protected abstract function getTableName();

    protected function orAnd($col, $arrays)
    {
        $tool = $this->toolObj();
        $i = 0;
        $string = "";
        foreach ($arrays as $array) {

            if ($i == 0) {
                $con = " AND";
            } else {
                $con = " OR";
            }

            $i++;

            $string .= $con . " " . $col . " = " . $tool->GetExplodedInt($array);
        }

        return $string;
    }



    public function sortArrayByArray(array $array, array $orderArray)
    {
        $tool = $this->toolObj();
        return $tool->sortArrayByArray($array, $orderArray);
    }


    public function makeOrQuery($name, $ids = array())
    {
        $sql = "(";
        $i = 0;
        foreach ($ids as $key) {

            if ($i != 0) {
                $sql .= " OR ";
            }
            $sql .= " " . $name . " = " . $key;
            $i++;
        }
        $sql .= ")";
        return $sql;
    }


    public function getAllTableCols($table)
    {

        $sql = "DESCRIBE $table";
        $res = $this->getResults($sql);
        $data = array();
        foreach ($res as $row) {
            $data[] = $row;
        }

        $colums = array();
        foreach ($data as $key => $val) {
            $colums[$key] = $val['Field'];
        }

        return $colums;
    }
}
