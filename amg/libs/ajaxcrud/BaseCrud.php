<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/9/18
 * Time: 12:21 PM
 */
define('EXECUTING_SCRIPT', "");
require_once __DIR__ . DIRECTORY_SEPARATOR . "crudConst.php";
require_once ajaxcrudextra . "dbcrud.php";

require_once ajaxcrudextra . "Utils.php";
require_once ajaxcrudextra . "paging.php";
include_once ajaxcrudextra . "TransLabelsClass.php";

class BaseCrud{
    protected $lang;
    protected $db_table;
    protected $fields = array();
    protected $field_count;
    protected $db_table_pk;
    protected $item;
    protected $db_table_fk_array = array();
    protected $nullFields = array();
    protected $tablePrefix;
    protected $transObj;

    /**
     * @var Utils $utils
     */
    protected $utils;

    protected $amgBundle;
    protected $amgPage;



    public function __construct()
    {
        global $tpl;
        $lang = $this->lang;
        $this->utils = new Utils();
        $transClass = TransLabelsClass::getInstance();
        $this->transObj = $transClass;
        $this->amgBundle = $tpl->getBundle();
        $this->lang = Tools::getLang();

        $transClass->setLang($lang);

    }


    /**
     * @return mixed
     */
    public function getDbTable()
    {
        return $this->db_table;
    }

    /**
     * @param mixed $db_table
     */
    public function setDbTable($db_table)
    {
        $this->db_table = $db_table;
        $this->fields 			= $this->getFields($db_table);
        $this->field_count 		= count($this->fields);
        $this->display_fields   = $this->fields;
        $this->add_fields       = $this->fields;
    }


    protected function getFields($table){
        $query = "SHOW COLUMNS FROM $table";
        $rs = q($query);

        //print_r($rs);
        $fields = array();
        foreach ($rs as $r){
            //r sub0 is the name of the field (hey ... it works)
            $fields[] = $r[0];
            $this->field_datatype[$r[0]] = $r[1];
        }

        if (count($fields) > 0){
            return $fields;
        }

        return false;
    }


    /**
     * @return mixed
     */
    protected function getDbTablePk()
    {
        return $this->db_table_pk;
    }

    /**
     * @param mixed $db_table_pk
     */
    public function setDbTablePk($db_table_pk)
    {
        $this->db_table_pk = $db_table_pk;
    }


    /**
     * @return string
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param string $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }


    /**
     * @return array
     */
    public function getNullFields()
    {
        return $this->nullFields;
    }

    /**
     * @param array $nullFields
     */
    public function setNullFields($nullFields)
    {
        $this->nullFields = $nullFields;
    }

    /**
     * @return mixed
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * @param mixed $tablePrefix
     */
    public function setTablePrefix($tablePrefix)
    {
        $this->tablePrefix = $tablePrefix;
    }


}
