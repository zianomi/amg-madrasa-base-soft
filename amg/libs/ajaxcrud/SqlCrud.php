<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/9/18
 * Time: 3:16 PM
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . "BaseCrud.php";
class SqlCrud extends BaseCrud {


    protected $joinWhereClause = array();
    protected $joinSortItems = array();
    protected $sql_order_by;
    protected $limit; // limit of rows to display on one page. defaults to 50
    protected $sql_limit;
    protected $amgExternalJoins = "";
    protected $display_fields = array();
    protected $sql_where_clause;
    protected $sql_where_clauses = array(); //array used IF there is more than one where clause
    protected $row_count;
    protected $num_where_clauses;
    protected $amgCrudFilterMode = false;
    protected $amgExternalFields = "";

    public function __construct()
    {
        parent::__construct();

        $this->limit = 10;
        $this->num_where_clauses = 0;
    }


    /**
     * @return array
     */
    protected function getJoinWhereClause()
    {
        return $this->joinWhereClause;
    }

    /**
     * @param array $joinWhereClause
     */
    protected function setJoinWhereClause($joinWhereClause)
    {
        $this->joinWhereClause = $joinWhereClause;
    }

    /**
     * @return array
     */
    protected function getJoinSortItems()
    {
        return $this->joinSortItems;
    }

    /**
     * @param array $joinSortItems
     */
    protected function setJoinSortItems($joinSortItems)
    {
        $this->joinSortItems = $joinSortItems;
    }

    public function addOrderBy($sql_order_by){
        $this->sql_order_by = " " . $sql_order_by;
    }

    public function setLimit($limit){
        $this->limit = $limit;
    }


    /**
     * @return string
     */
    protected function getAmgExternalJoins()
    {
        return $this->amgExternalJoins;
    }

    /**
     * @param string $amgExternalJoins
     */
    protected function setAmgExternalJoins($amgExternalJoins)
    {
        $this->amgExternalJoins = $amgExternalJoins;
    }

    /**
     * @return boolean
     */
    public function isAmgCrudFilterMode()
    {
        return $this->amgCrudFilterMode;
    }

    /**
     * @param boolean $amgCrudFilterMode
     */
    public function setAmgCrudFilterMode($amgCrudFilterMode)
    {
        $this->amgCrudFilterMode = $amgCrudFilterMode;
    }

    /**
     * @return string
     */
    public function getAmgExternalFields()
    {
        return $this->amgExternalFields;
    }

    /**
     * @param string $amgExternalFields
     */
    public function setAmgExternalFields($amgExternalFields)
    {
        $this->amgExternalFields = $amgExternalFields;
    }

    protected function getNumRows(){

        $sql = "SELECT COUNT(*) FROM " . $this->db_table . $this->getAmgExternalJoins() . $this->sql_where_clause;
        $numRows = q1($sql);
        return $numRows;
    }

    function createQueryHelper()
    {
        $amgJoinFieldsArr = $this->db_table_fk_array;
        $amgJoinTables = $this->category_table_array;
        $amgJoinTableIds = $this->category_table_pk_array;
        $amgJoinTableCols = $this->category_field_array;
        $amgJoinWhereCouse = $this->category_whereclause_array;
        $amgJoinSortCouse = $this->category_sort_field_array;

//echo '<pre>';print_r($this->category_sort_field_array );echo '</pre>';


        if (!empty($amgJoinFieldsArr)) {
            $amgJoinSql = "";
            $amgJoinFields = "";

            for ($amgI = 0; $amgI < count($amgJoinTables); $amgI++) {
                $amgJoinFields .= "," . $amgJoinTables[$amgI] . "." . $amgJoinTableCols[$amgI] . " AS " . $amgJoinTables[$amgI] . "_" . $amgJoinTableCols[$amgI];
                $amgJoinSql .= " LEFT JOIN " . $amgJoinTables[$amgI] . " ON ";
                $amgJoinSql .= $this->db_table . "." . $amgJoinFieldsArr[$amgI] . " = " . $amgJoinTables[$amgI] . "." . $amgJoinTableIds[$amgI];
            }
            if(!empty($amgJoinWhereCouse)){
                $this->setJoinWhereClause($amgJoinWhereCouse);
            }

            if(!empty($amgJoinSortCouse)){
                $this->setJoinSortItems($amgJoinSortCouse);
            }
            $this->setAmgExternalFields($amgJoinFields);
            $this->setAmgExternalJoins($amgJoinSql);



        } else {
            $this->setAmgExternalFields("");
            $this->setAmgExternalJoins("");
        }


    }


    protected function getTotalRowCount(){
        if(!empty($this->db_table_fk_array)){
            $this->createQueryHelper();
        }
        $count = q1("SELECT COUNT(*) FROM " . $this->db_table . $this->getAmgExternalJoins() . $this->sql_where_clause);

        return $count;
    }

    //DEPRECATED - use insertRowsReturned instead for realtime updating with ajax
    protected function getRowCount(){
        if ($_SESSION['row_count'] == ""){
            $count = $this->getNumRows();
        }
        else{
            $count = $_SESSION['row_count'];
        }
        //return $count;
        return  $count;
    }



    function addWhereClause($sql_where_clause){
        $this->num_where_clauses++;
        $this->sql_where_clauses[] = $sql_where_clause;

        if ($this->num_where_clauses <= 1){
            $this->sql_where_clause = " " . $sql_where_clause;
        }
        else{
            //chain multiple together
            $whereClause = ""; //start the clause now chain to it
            $count = 0;
            foreach($this->sql_where_clauses as $where_clause){
                if ($count > 0){
                    //$where_clause = str_replace("WHERE", "AND", $where_clause);
                    $where_clause = preg_replace('/WHERE/', 'AND', $where_clause, 1); // Only replace the FIRST instance; the magic is in the optional fourth parameter [Limit] (this is important because of sub queries which uses a second WHERE statement)
                }
                $whereClause .= " $where_clause";
                $count++;
            }

            $this->sql_where_clause = " $whereClause";
        }

        $_SESSION['ajaxcrud_where_clause'][$this->db_table] = $this->sql_where_clause;


    }




}
