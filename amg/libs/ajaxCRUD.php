<?php
include_once __DIR__ . DIRECTORY_SEPARATOR . "/AmgSpace.php";
include_once __DIR__ . DIRECTORY_SEPARATOR . "/ajaxcrud/TransLabelsClass.php";
define("ajaxcrudextra",__DIR__ . DIRECTORY_SEPARATOR . "ajaxcrud" . DIRECTORY_SEPARATOR);
include_once ajaxcrudextra . "dbcrud.php";
include_once ajaxcrudextra . "common.php";

class ajaxCRUD{

    protected $amgSpace;
    var $ajaxcrud_root;
    var $ajax_file;
    var $css_file;
    var $css = true; 	//indicates a css spredsheet WILL be used
    var $add = true;    //adding is ok

    var $includeTableHeaders = true; //include table headers (default)
    var $includeJQuery 	= true; //include jquery (default)

    var $allowHeaderInsert = true; //insert the jquery/css files by default [you can insert whereever you want in your script with $yourObject->insertHeader();]

    var $doActionOnShowTable; //boolean var. When true and showTable() is called, doAction() is also called. turn off when you want to only have a table show in certain conditions but CRUD operations can take place on the table "behind the scenes"

    var $item_plural;
	var $item;

	var $db_table;
	var $db_table_pk;
	var $db_main_field;
    var $row_count;

    var $table_html; //the html for the table (to be modified on ADD via ajax)

    var $cellspacing;

    var $showPaging = true;
    var $limit; // limit of rows to display on one page. defaults to 50
    var $sql_limit;

    var $filtered_table = false; //the table is by default unfiltered (eg no 'where clause' on it)
    var $ajaxFilter_fields = array(); //array of fields that can be are filtered by ajax (creates a textbox at the top of the table)
    var $ajaxFilterBoxSize = array(); //array (sub fieldname) holding size of the input box

    //all fields in the table
    var $fields = array();
	var $field_count;

    //field datatypes
    var $field_datatype = array(); //$field_datatype[field] = datatype

    //allow delete of fields | boolean variable set to true by default
    var $delete;

    //defines if the add functionality uses ajax
    var $ajax_add = true;

    //defines if the class allows you to edit all fields (only used to turn off ALL editing completely)
    var $ajax_editing = true;

    //defines if the class allows you to sort all fields
    var $ajax_sorting = true;

    //the fields to be displayed
    var $display_fields = array();

    //the fields to be inputted when adding a new entry (90% time will be all fields). can be changed via the omitAddField method
    var $add_fields = array();
    var $add_form_top = FALSE; //the add form (by default) is below the table. use displayAddFormTop() to bring it to the top

    //the fields which are displayed, but not editable
    var $uneditable_fields = array();

    //the header fields which are displayed, but not sortable (i.e. click to sort)
    var $unsortable_fields = array();

	var $sql_where_clause;
    var $sql_where_clauses = array(); //array used IF there is more than one where clause

	var $sql_order_by;
    var $num_where_clauses;

    var $on_add_specify_primary_key = false;

    //table border - default is off: 0
    var $border;

    var $orientation; //orientation of table (detault is horizontal)

	var $showCSVExport = false;	// indicates whether to show the "Export Table to CSV" button

    //array containing values for a button next to the "go back" button at the bottom. [0] = value [1] = url [2] = extra tags/javascript
    var $bottom_button = array();

    //array with value being the url for the buttom to go to (passing the id) [0] = value [1] = url
    var $row_button = array();

	//array with value being "same" or "new" - specifying the target window for the opening the page. index of array is the button 'id'
    var $addButtonToRowWindowOpen = "";

    ################################################
    #
    # The following are parallel arrays to help in the definition of a defined db relationship
    #
    ################################################

    //values will be the name(s) of the foreign key(s) for a category table
	var $db_table_fk_array = array();

    //values will be the name(s) of the category table(s)
	var $category_table_array = array();

    //values will be the name(s) of the primary key for the category table(s)
	var $category_table_pk_array = array();

    //values will be the name(s) of the field to return in the category table(s)
	var $category_field_array = array();

    //values will be the (optional) name of the field to sort by in the category table(s)
    var $category_sort_field_array = array();

    //values will be the (optional) whereclause for the fk clause
    var $category_whereclause_array = array();

    //for dropdown (to make an empty box). (format: array[field] = true/false)
    var $category_required = array();

    // allowable values for a field. the key is the name of the field
    var $allowed_values = array();

	// "on" and "off" values for a checkbox. the key is the name of the field
    var $checkbox = array();

	// holds the field names of columns that will have a "check all" checkbox
	var $checkboxall = array();

    //values to be set to a particular field when a new row is added. the array is set as $field_name => $add_value
    var $add_values = array();

    //destination folder to be set for a particular field that allows uploading of files. the array is set as $field_name => $destination_folder
    var $file_uploads = array();
    var $file_upload_info = array(); //array[$field_name]['destination_folder'], array[$field_name]['relative_folder'], and array[$field_name]['permittedFileExts']
    var $filename_append_field = "";

    //array dictating that "dropdown" fields do not show dropdown (but text editor) on edit (format: array[field] = true/false);
    //used in defineAllowableValues function
    var $field_no_dropdown = array();

    //array holding the (user-defined) function to format a field with on display (format: array[field] = function_name);
    //used in formatFieldWithFunction function
    var $format_field_with_function 	= array();
    var $validate_delete_with_function 	= ""; //used to determine if a particular row can be deleted or not (user-defined function)

    //used in formatFieldWithFunctionAdvanced function (takes a second param - the id of the row)
    var $format_field_with_function_adv = array();

    var $onAddExecuteCallBackFunction;
    var $onUpdateExecuteCallBackFunction = array(); //this is an array because callback methods for update (unlike add) are field based
    var $onFileUploadExecuteCallBackFunction;
    var $onDeleteFileExecuteCallBackFunction;

    //(if true) put a checkbox before each row
    var $showCheckbox;

    var $loading_image_html;

    var $emptyTableMessage;

	/* these default to english words (e.g. "Add", "Delete" below); but can be
	   changed by setting them via $obj->addText = "A�adir"
	*/
	var $addText, $deleteText, $cancelText, $actionText, $fileDeleteText, $fileEditText; //text values for buttons and other table text
	var $addButtonText; //if you want to replace the entire add button text with a phrase or other text. Added in 8.81
	var $addMessage; //used when onAddExecuteCallBackFunction is leveraged

    var $sort_direction; //used when sorting the table via ajax

    ################################################
    #
    # displayAs array is for linking a particular field to the name that displays for that field
    #
    ################################################

    //the indexes will be the name of the field. the value is the displayed text
    var $displayAs_array = array();

    //height of the textarea for certain fields. the index is the field and the value is the height
    var $textarea_height = array();

    var $textboxWidth = array(); //if defined for regular text input boxes, this will alter how ADD fields are displayed

    //any 'notes' to display next to a field when adding a row
    var $fieldNote = array();

    //a placeholder text to give to the field when ADDing a new row
    var $placeholderText = array();

    //variable used to capture which search fields should be EXACT matches vs approximate match (using LIKE %search%)
    var $exactSearchField = array(); //set by setExactSearchField (and automatically set for fields using defineRelationship and defineAllowableValues)

    //set manually - initial value for a field (when adding a row)
    var $initialFieldValue = array();

	// Array to include css style classes in specified fields
	var $display_field_with_class_style = array();


    var $amgBundle;
    var $amgPage;
    var $lang;

    var $customButton;
    var $customButtonStatus = false;
    var $printButton = true;
    var $amgCsv = false;
    var $transObj;

    var $amgInputDataType;

    var $amgExternalFields = "";
    var $amgExternalJoins = "";

    var $amgCrudFilterMode = false;

    var $showFileDelete = true;

    var $joinSortItems = array();
    var $joinWhereClause = array();
    var $nullFields = array();


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
     * @return boolean
     */
    public function isShowFileDelete()
    {
        return $this->showFileDelete;
    }

    /**
     * @param boolean $showFileDelete
     */
    public function setShowFileDelete($showFileDelete)
    {
        $this->showFileDelete = $showFileDelete;
    }

    /**
     * @return array
     */
    public function getJoinWhereClause()
    {
        return $this->joinWhereClause;
    }

    /**
     * @param array $joinWhereClause
     */
    public function setJoinWhereClause($joinWhereClause)
    {
        $this->joinWhereClause = $joinWhereClause;
    }

    /**
     * @return array
     */
    public function getJoinSortItems()
    {
        return $this->joinSortItems;
    }

    /**
     * @param array $joinSortItems
     */
    public function setJoinSortItems($joinSortItems)
    {
        $this->joinSortItems = $joinSortItems;
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

    /**
     * @return string
     */
    public function getAmgExternalJoins()
    {
        return $this->amgExternalJoins;
    }

    /**
     * @param string $amgExternalJoins
     */
    public function setAmgExternalJoins($amgExternalJoins)
    {
        $this->amgExternalJoins = $amgExternalJoins;
    }





    /**
     * @return array
     */
    public function getAmgInputDataType()
    {
        return $this->amgInputDataType;
    }


    public function setAmgInputDataType($inputField,$amgInputDataType)
    {
        $this->amgInputDataType[$inputField] = $amgInputDataType;
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




    /**
     * @return mixed
     */
    public function getDbTablePk()
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
        $this->item_plural		= "";
    }

    /**
     * @return string
     */
    public function getAjaxcrudRoot()
    {
        return $this->ajaxcrud_root;
    }

    /**
     * @param string $ajaxcrud_root
     */
    public function setAjaxcrudRoot($ajaxcrud_root)
    {
        $this->ajaxcrud_root = $ajaxcrud_root;
        $this->loading_image_html = "<center><br /><br  /><img src=\'" . $this->ajaxcrud_root . "css/loading.gif\'><br /><br /></center>"; //changed via setLoadingImageHTML()
    }


    /**
     * @return boolean
     */
    public function isPrimaryKeyAutoIncrement()
    {
        return $this->primaryKeyAutoIncrement;
    }

    /**
     * @param boolean $primaryKeyAutoIncrement
     */
    public function setPrimaryKeyAutoIncrement($primaryKeyAutoIncrement)
    {
        $this->primaryKeyAutoIncrement = $primaryKeyAutoIncrement;
    }

    /**
     * @return mixed
     */
    public function getAmgBundle()
    {
        return $this->amgBundle;
    }

    /**
     * @param mixed $amgBundle
     */
    public function setAmgBundle($amgBundle)
    {
        $this->amgBundle = $amgBundle;
    }

    /**
     * @return mixed
     */
    public function getAmgPage()
    {
        return $this->amgPage;
    }

    /**
     * @param mixed $amgPage
     */
    public function setAmgPage($amgPage)
    {
        $this->amgPage = $amgPage;
    }

    /**
     * @return mixed
     */
    public function getCustomButton()
    {
        return $this->customButton;
    }

    /**
     * @param mixed $customButton
     */
    public function setCustomButton($customButton)
    {
        $this->customButton = $customButton;
    }

    /**
     * @return boolean
     */
    public function isCustomButtonStatus()
    {
        return $this->customButtonStatus;
    }

    /**
     * @param boolean $customButtonStatus
     */
    public function setCustomButtonStatus($customButtonStatus)
    {
        $this->customButtonStatus = $customButtonStatus;
    }

    /**
     * @return boolean
     */
    public function isPrintButton()
    {
        return $this->printButton;
    }

    /**
     * @param boolean $printButton
     */
    public function setPrintButton($printButton)
    {
        $this->printButton = $printButton;
    }

    /**
     * @return boolean
     */
    public function isAmgCsv()
    {
        return $this->amgCsv;
    }

    /**
     * @param boolean $amgCsv
     */
    public function setAmgCsv($amgCsv)
    {
        $this->amgCsv = $amgCsv;
    }












    // Constructor
    //by default ajaxCRUD assumes all necessary files are in the same dir as the script calling it (eg $ajaxcrud_root = "")
    public function __construct() {


        if($this->amgSpace == null){
            $this->amgSpace = new AmgSpace();

        }
        $this->lang = Tools::getLang();

        $lang = $this->lang;

        $this->transObj = TransLabelsClass::getInstance();
        $this->transObj->setLang($lang);
        $transArr = $this->transObj->transArray();



        //global variable - for allowing multiple ajaxCRUD tables on one page
        global $num_ajaxCRUD_tables_instantiated;
        if ($num_ajaxCRUD_tables_instantiated === "") $num_ajaxCRUD_tables_instantiated = 0;

        global $headerAdded;
        if ($headerAdded === "") $$headerAdded = FALSE;

        $this->showCheckbox     = false;
        //$this->ajaxcrud_root    = $ajaxcrud_root;
        $this->ajax_file        = EXECUTING_SCRIPT;

		//$this->item 			= $item;

		//$this->db_table			= $db_table;
		//$this->db_table_pk		= $db_table_pk;
		//$this->db_table_pk		= $db_table_pk;

		//$this->fields 			= $this->getFields($db_table);
		//$this->field_count 		= count($this->fields);

        //by default paging is turned on; limit is 50
        $this->showPaging       = true;
        $this->limit            = 1000;
        $this->num_where_clauses = 0;

        $this->delete           = true;
        $this->add              = true;

        //assumes the primary key is auto incrementing
        $this->primaryKeyAutoIncrement = true;

        $this->border           = 0;
        $this->css              = true;
        $this->ajax_add         = true;
        $this->orientation 		= 'horizontal';

        $this->addButtonToRowWindowOpen = 'same'; //global window to open pages in - used when adding custom buttons to a row

        $this->doActionOnShowTable = true;


        $this->addText			 = $this->getAddText();
        $this->deleteText		 = $transArr['delete'];
        $this->cancelText		 = $transArr['cancel'];
        $this->actionText		 = $transArr['action'];
        $this->fileEditText	 	 = $transArr['edit']; //added in 8.81 (for file crud)
        $this->fileDeleteText	 = $transArr['del']; //added in 8.81 (for file crud)

        $this->emptyTableMessage = $transArr['no_data_in_this_table_click_add_button_below'];
        $this->addButtonText	 = ""; //when blank, button text defaults to 'Add {Item}' text unless when $addText is set then defaults to '{addText} {Item}'; if set the entire button is replaced with {addButtonText}
        $this->addMessage		 = ""; //when blank, defaults to generic '{Item} added' message

        $this->onAddExecuteCallBackFunction         = '';
        $this->onFileUploadExecuteCallBackFunction  = '';
        $this->onDeleteFileExecuteCallBackFunction  = '';

        //don't allow primary key to be editable
        $this->uneditable_fields[] = $this->db_table_pk;



        //default sort direction
        $this->sort_direction	= "desc";



		/**/

		return true;
	}




    function setAddText($text){
        $this->addText = $text;
    }

    function getAddText(){
        return $this->addText;
    }

	function getNumRows(){

		$sql = "SELECT COUNT(*) FROM " . $this->db_table . $this->getAmgExternalJoins() . $this->sql_where_clause;
		$numRows = q1($sql);
		return $numRows;
	}

	function setAjaxFile($ajax_file){
        $this->ajax_file = $ajax_file;
    }

	function setOrientation($orientation){
        $this->orientation = $orientation;
    }

    function turnOffAjaxADD(){
        $this->ajax_add = false;
    }

    function turnOffAjaxEditing(){
        $this->ajax_editing = false;
        foreach ($this->fields as $field){
			$this->disallowEdit($field);
		}
    }

    function turnOffSorting(){
        $this->ajax_sorting = false;
        foreach ($this->fields as $field){
			$this->disallowSort($field);
		}
    }

    function turnOffPaging($limit = ""){
        $this->showPaging = false;
        if ($limit != ''){
            $this->sql_limit = " LIMIT $limit";
        }
    }

	function disableTableHeaders() {
		$this->includeTableHeaders = false;
	}

	function disableJQuery() {
		$this->includeJQuery = false;
	}


    function setCSSFile($css_file){
        $this->css_file = $css_file;
    }

    function setLoadingImageHTML($html){
        $this->loading_image_html = $html;
    }

    function addTableBorder(){
        $this->border = 1;
    }

    function addAjaxFilterBox($field_name, $textboxSize = 10, $exactSearch = FALSE){
        $this->ajaxFilter_fields[] = $field_name;

        //defaults to size of "10" (unless changed via setAjaxFilterBoxSize)
        $this->setAjaxFilterBoxSize($field_name, $textboxSize);
        if ($exactSearch === TRUE){
        	$this->setExactSearchField($field_name);
        }
    }

    function setAjaxFilterBoxSize($field_name, $size){
        $this->ajaxFilterBoxSize[$field_name] = $size; //this function is deprecated, as of v6.0
    }

    function addAjaxFilterBoxAllFields(){
        //unset($this->ajaxFilter_fields);
        foreach ($this->display_fields as $field){
            $this->addAjaxFilterBox($field);
        }
    }

    function displayAddFormTop(){
    	$this->add_form_top = TRUE;
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



	function addOrderBy($sql_order_by){
		$this->sql_order_by = " " . $sql_order_by;
	}

	/* added in release 6.0 */
	function orderFields($fieldsString){
		/* warning - if you add a field to this list which is not in the database,
		   you may have unintended results */

		//separate fieldsString with ","
		$fieldsString = str_replace(" ", "", $fieldsString); //parse out any spaces
		$fieldsArray = explode(",", $fieldsString);

		foreach($this->display_fields as $d){
			if(!in_array($d,$fieldsArray))
				$fieldsArray[] = $d;
		}

		$this->display_fields = $fieldsArray;
	}

    function formatFieldWithFunction($field, $function_name){
        $this->format_field_with_function[$field] = $function_name;
    }

    function formatFieldWithFunctionAdvanced($field, $function_name){
        $this->format_field_with_function_adv[$field] = $function_name;
    }

    /*	added in R8.7
    	uses a user-defined function which will return true or false re whether THAT ROW
    	is deletable (validateDeleteWithFunction)  or any any field in that row is editable (validateUpdateWithFunction)
    	Example and Documentation: http://ajaxcrud.com/api/index.php?id=validateDeleteWithFunction
    	Example and Documentation: http://ajaxcrud.com/api/index.php?id=validateUpdateWithFunction
    */
    function validateDeleteWithFunction($function_name){
    	$this->validate_delete_with_function = $function_name;
    }

    function validateUpdateWithFunction($function_name){
    	$this->validate_update_with_function = $function_name;
    }

    function defineRelationship($field, $category_table, $category_table_pk, $category_field_name, $category_sort_field = "", $category_required = "1", $where_clause = ""){

        $this->db_table_fk_array[]          = $field;
        $this->category_table_array[]       = $category_table;
        $this->category_table_pk_array[]    = $category_table_pk;
        $this->category_field_array[]       = $category_field_name;
        $this->category_sort_field_array[]  = $category_sort_field;
        $this->category_whereclause_array[] = $where_clause;

        //make the relationship required for the field
        if ($category_required == "1"){
            $this->category_required[$field] = TRUE;
        }

        $this->setExactSearchField($field); //set search field to use exact matching (as of v7.2.1)
    }

    function relationshipFieldOptional(){
        $this->cat_field_required = FALSE;
    }

	function defineAllowableValues($field, $array_values, $onedit_textbox = FALSE){
		//array with the setup [0] = value [1] = display name (both the same)
		$new_array = array();

		foreach($array_values as $array_value){
			if (!is_array($array_value)){
                //a two-dimentential array --> set both the value and dropdown text to be the same
                $new_array[] = array(0=> $array_value, 1=>$array_value);
            }
            else{
                //a 2-dimentential array --> value and dropdown text are different
                $new_array[] = $array_value;
            }
		}

		if ($onedit_textbox != FALSE){
			$this->field_no_dropdown[$field] = TRUE;
		}

		$this->allowed_values[$field] = $new_array;
		$this->setExactSearchField($field); //set search field to use exact matching (as of 7.2.1)
	}

	function defineCheckbox($field, $value_on="1", $value_off="0"){
		$new_array = array($value_on, $value_off);

		$this->checkbox[$field] = $new_array;
	}

	function showCheckboxAll($field, $display_data) {
		$this->checkboxall[$field] = $display_data;
	}

    function displayAs($field, $the_field_name){
        $this->displayAs_array[$field] = $the_field_name;
    }

    function setTextareaHeight($field, $height){
        $this->textarea_height[$field] = $height;
    }

    function setTextboxWidth($field, $width){
        $this->textboxWidth[$field] = $width;
    }

    function setAddFieldNote($field, $caption){
        $this->fieldNote[$field] = $caption;
    }

    /* added in R8.0 */
    function setAddPlaceholderText($field, $placeholder){
        $this->placeholderText[$field] = $placeholder;
    }

	/* added in R7.2.1 */
	function setExactSearchField($field) {
		$this->exactSearchField[$field] = true;
	}

    function setInitialAddFieldValue($field, $value){
        $this->initialFieldValue[$field] = $value;
    }

    function setLimit($limit){
        $this->limit = $limit;
    }

    //DEPRECATED - use insertRowsReturned instead for realtime updating with ajax
    function getRowCount(){
        if ($_SESSION['row_count'] == ""){
        	$count = $this->getNumRows();
        }
        else{
        	$count = $_SESSION['row_count'];
        }
        //return $count;
        return "<span id='" . $this->db_table . "_RowCount'>" . $count . "</span>";
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

    function getTotalRowCount(){
        if(!empty($this->db_table_fk_array)){
            $this->createQueryHelper();
        }
        $count = q1("SELECT COUNT(*) FROM " . $this->db_table . $this->getAmgExternalJoins() . $this->sql_where_clause);

        return $count;
    }

	function omitField($field_name){
        $key = array_search($field_name, $this->display_fields);

        if ($this->fieldInArray($field_name, $this->display_fields)){
            unset($this->display_fields[$key]);
        }
        else{
            $error_msg[] = "Error in your doNotDisplay function call. There is no field named <b>$field_name</b> in the table <b>" . $this->db_table . "</b>";
        }
    }

    function omitAddField($field_name){
        $key = array_search($field_name, $this->add_fields);

        if ($key !== FALSE){
            unset($this->add_fields[$key]);
        }
        else{
            $error_msg[] = "Error in your omitAddField function call. There is no field named <b>$field_name</b> in the table <b>" . $this->db_table . "</b>";
        }
    }

    function omitFieldCompletely($field_name){
        $this->omitField($field_name);
        $this->omitAddField($field_name);
    }

	/* added with R6.0 */
	function showOnly($fieldsString){
		//separate fieldsString with ","
		$fieldsString = str_replace(" ", "", $fieldsString); //parse out any spaces
		$fieldsArray = explode(",", $fieldsString);

        $this->display_fields   = $fieldsArray;
        $this->add_fields       = $fieldsArray;
    }

    function addValueOnInsert($field_name, $insert_value){
        $this->add_values[] = array(0 => $field_name, 1 => $insert_value);
    }

    function onAddExecuteCallBackFunction($function_name){
        $this->onAddExecuteCallBackFunction = $function_name;
        $this->ajax_add = false;
    }

    function onUpdateExecuteCallBackFunction($field_name, $function_name){
        $this->onUpdateExecuteCallBackFunction[$field_name] = $function_name;
    }

    function onFileUploadExecuteCallBackFunction($function_name){
        $this->onFileUploadExecuteCallBackFunction = $function_name;
    }

    function onDeleteFileExecuteCallBackFunction($function_name){
        $this->onDeleteFileExecuteCallBackFunction = $function_name;
    }

    function primaryKeyNotAutoIncrement(){
        $this->primaryKeyAutoIncrement = false;
    }

    //the forth optional param (permittedFileExts) was added in v8.9; it is an ARRAY of permitted file extensions allowed for upload; e.g. array("png", "jpg")
    function setFileUpload($field_name, $destination_folder, $relative_folder = "", $permittedFileExts = "", $amgPath = ""){
        //put values into array
        $this->file_uploads[] = $field_name;
        $this->file_upload_info[$field_name]['destination_folder'] = $destination_folder;
        $this->file_upload_info[$field_name]['relative_folder'] = $relative_folder;
        $this->file_upload_info[$field_name]['amg_path'] = $amgPath;

        //added in v8.9
        if (is_array($permittedFileExts)){
	        $this->file_upload_info[$field_name]['permittedFileExts'] = $permittedFileExts;
	    }

        //the filenames that are saved are not editable
        //$this->disallowEdit($field_name);

        //have to add the row via POST now
        $this->ajax_add = false;
    }

    function appendUploadFilename($append_field){
        $this->filename_append_field = $append_field;
    }

    function omitPrimaryKey(){

        //99% time it'll be in key 0, but just in case do search
        $key = array_search($this->db_table_pk, $this->display_fields);
        unset($this->display_fields[$key]);
    }

	function showCSVExportOption() {
		$this->showCSVExport = true;
	}

	function modifyFieldWithClass($field, $class_name){
        $this->display_field_with_class_style[$field] = $class_name;
    }

    function insertRowsReturned(){
    	$numRows = $this->getNumRows();
    	echo "<span class='" . $this->db_table . "_rowCount'>" . $numRows . "</span>";
    }

    function insertHeader($ajax_file = "ajaxCRUD.inc.php"){

        global $headerAdded;
        $headerAdded = TRUE;

        if ($this->css_file == ''){
            //$this->css_file = 'default.css';
        }

		/* Load Javascript dependencies */
		if ($this->includeJQuery){

				echo "<script type=\"text/javascript\" src=\"" . $this->ajaxcrud_root . "js/ajaxcrud.js\"></script>\n";
				//echo "<script type=\"text/javascript\" src=\"" . $this->ajaxcrud_root . "js/jquery.min.js\"></script>\n";
				//echo "<script type=\"text/javascript\" src=\"http://ajax.microsoft.com/ajax/jquery.validate/1.5.5/jquery.validate.js\"></script>\n";
			    //echo "<script type=\"text/javascript\" src=\"" . $this->ajaxcrud_root . "js/validation.js\"></script>\n";
		}
        echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />\n";
        //echo "<script type=\"text/javascript\" src=\"" . $this->ajaxcrud_root . "js/javascript_functions.js\"></script>\n";
        //echo "<link href=\"" . $this->ajaxcrud_root . "css/" . $this->css_file . "\" rel=\"stylesheet\" type=\"text/css\" media=\"screen\" />\n";



        echo "
            <script>\n
                ajax_file = \"$this->ajax_file\"; \n
                this_page = \"" . $_SERVER['REQUEST_URI'] . "\"\n
                loading_image_html = \"$this->loading_image_html\"; \n

                function validateAddForm(tableName, usePost){
            		var validator = $('#add_form_' + tableName).validate();
            		if (validator.form()){
						if (!usePost){
							setLoadingImage(tableName);
							//var fields = getFormValues(document.getElementById('add_form_' + tableName), '');
							var fields = $('#add_form_' + tableName).serialize();
							fields = fields + '&table=' + tableName;

							alert(fields);
							var req = '" . $this->getThisPage() . "ajaxCrudAction=add&' + fields;
							//validator.resetForm();
							clearForm('add_form_' + tableName);
							sndAddReq(req, tableName);
							return false;
						}
						else{
							//post the form normally (e.g. if using file uploads)
							$('#add_form_' + tableName).submit();
						}

                    }
                    return false;
                }

				$(document).ready(function(){
					$(\"#add_form_{$this->db_table}\").validate();
				});

            </script>\n";
		echo "
			<style>
				/* this will only work when your HTML doctype is in \"strict\" mode.
					In other words - put this in your header:
				   <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
				*/

				.hand_cursor{
					cursor: pointer; /* hand-shaped cursor */
					cursor: hand; /* for IE 5.x */
				}

				.editable:hover, p.editable:hover{
					background-color: #FFFF99;
				}
			</style>\n";

		return true;
	}

    function disallowEdit($field){
        $this->uneditable_fields[] = $field;
    }

    function disallowSort($field){
        $this->unsortable_fields[] = $field;
    }

    function disallowDelete(){
        $this->delete = false;
    }

    function disallowAdd(){
        $this->add = false;
    }

    function addButton($value, $url, $tags = ""){
        $this->bottom_button[] = array(0 => $value, 1 => $url, 2 => $tags);
    }

    function addButtonToRow($value, $url, $attach_params = "", $javascript_tags = "", $windowToOpen = "same"){
        $this->row_button[] = array(0 => $value, 1 => $url, 2 => $attach_params, 3 => $javascript_tags, 4 => $windowToOpen);
    }

    function onAddSpecifyPrimaryKey(){
        $this->on_add_specify_primary_key = true;
    }

    function doCRUDAction(){
        if ($_REQUEST['ajaxCrudAction'] != ''){
            $this->doAction($_REQUEST['ajaxCrudAction']);
        }
    }

	function doAction($action){

		global $error_msg;
		global $report_msg;

		$item = $this->item;

		if ($action == 'delete' && $_REQUEST['id'] != ''){
			$delete_id = $_REQUEST['id'];
            $success = qr("DELETE FROM $this->db_table WHERE $this->db_table_pk = \"$delete_id\"");
			if ($success){
				$report_msg[] = "$item " . $this->deleteText . "d";
			}
			else{
				$error_msg[] = "$item could not be deleted. Please try again.";
			}
		}//action = delete


		if ($action == 'update' && $_REQUEST['field_name'] != "" && $_REQUEST['id'] != ""){

			if ($_REQUEST['table'] == $this->db_table){//added this conditional in v8.5 to account for multiple tables
				$paramName = $_REQUEST['paramName']; //this is the param which the field update value will come by
				$val = addslashes($_REQUEST[$paramName]);
				$pkID = $_REQUEST['id'];

				$field_name = $_REQUEST['field_name'];
				$success = qr("UPDATE $this->db_table SET $field_name  = \"$val\" WHERE $this->db_table_pk = $pkID");

				if ($success){
					$report_msg[] = "$item Updated";
					if ($this->onUpdateExecuteCallBackFunction[$field_name] != ''){
						$updatedRowArray = qr("SELECT * FROM $this->db_table WHERE $this->db_table_pk = $pkID");
						$callBackSuccess = call_user_func($this->onUpdateExecuteCallBackFunction[$field_name], $updatedRowArray);
						if (!$callBackSuccess){
							//commented this out because not all callback functions will return true or false
							//$error_msg[] = "Callback function could not be executed; please check the name of your callback function.";
						}
					}
				}
				else{
					$error_msg[] = "$item could not be updated (likely because no data has changed). Please try again.";
				}
			}
		}

        if ($action == 'upload' && $_REQUEST['field_name'] && $_REQUEST['id'] != '' && is_array($this->file_uploads) && in_array($_REQUEST['field_name'],$this->file_uploads)){
            $update_id      = $_REQUEST['id'];
            $file_field     = $_REQUEST['field_name'];
            $upload_folder  = $this->file_upload_info[$file_field]['destination_folder'];

			$allowedExts = "";
			if (isset($this->file_upload_info[$file_field]['permittedFileExts'])){
				$allowedExts = $this->file_upload_info[$file_field]['permittedFileExts'];
			}

            $success = $this->uploadFile($update_id, $file_field, $upload_folder, $allowedExts);

            if ($success){
                //$report_msg[] =
                $resPosnse = array("status" => "true","msg"=>"File Uploaded");
            }
            else{
                $resPosnse = array("status" => "false","msg"=>"There was an error uploading your file.");
                //$error_msg[] = "There was an error uploading your file.";
            }

            echo json_encode($resPosnse);
            exit;

        }//action = upload



	}//doAction

	// Cleans data up for CSV output
	function escapeCSVValue($value) {
		$value = str_replace('"', '&quot;', $value); // First off escape all " and make them HTML quotes
		if(preg_match('/,/', $value) or preg_match("/\n/", $value)) { // Check if I have any commas or new lines
			return '&quot;'.$value.'&quot;'; // If I have new lines or commas escape them
		} else {
			return $value; // If no new lines or commas just return the value
		}
	}


	// Gathers and returns table data to create a CSV file
    function createCSVOutput($passedParams) {
        $params = unserialize(urldecode($passedParams));

        $display_fields = $params['display_fields'];
        $displayAs_array = $params['displayAs_array'];
        $db_table = $params['db_table'];
        $sql_where_clause = $params['sql_where_clause'];
        $sql_order_by = $params['sql_order_by'];
        $format_field_with_function = $params['format_field_with_function'];
        $format_field_with_function_adv = $params['format_field_with_function_adv'];

        //main table column this will join t other table
        $db_table_fk_array = $params['db_table_fk_array'];
        //Join tables display cols
        $category_field_array = $params['category_field_array'];

        //Join tables name
        $category_table_array = $params['category_table_array'];

        //Join tables id col
        $category_table_pk_array = $params['category_table_pk_array'];

        $headers = "";
        $data = "";
        // Gather table heading data
        $exportTableHeadings = array();
        foreach ($display_fields as $field){
            $field_name = $field;
            if (@$displayAs_array[$field] != ''){
                $field = $displayAs_array[$field];
            }
            $field = $this->escapeCSVValue($field);

            if ($field == "ID") {
                $field = "Id";			// To prevent the SYLK error in Excel
            }

            $exportTableHeadings[] = $field;
        }
        $headers = join(',', $exportTableHeadings) . "\n";

        if(!empty($db_table_fk_array)) {
           $sql = "SELECT " . $db_table . ".*"; //added name for table (t) in case where clauses want to use it (7.2.2)
           $amgJoinSql = "";
           $amgJoinFields = "";

           for ($amgI = 0; $amgI < count($category_table_array); $amgI++) {
               $amgJoinFields .= "," . $category_table_array[$amgI] . "." . $category_field_array[$amgI] . " AS " . $category_table_array[$amgI] . "_" . $category_field_array[$amgI];
               $amgJoinSql .= " LEFT JOIN " . $category_table_array[$amgI] . " ON ";
               $amgJoinSql .= $db_table . "." . $db_table_fk_array[$amgI] . " = " . $category_table_array[$amgI] . "." . $category_table_pk_array[$amgI];
           }


           $sql .= $amgJoinFields;
           $sql .= " FROM " . $db_table;
           $sql .= $amgJoinSql;
        }
        else{
            $sql = "SELECT * FROM " . $db_table . $sql_where_clause . $sql_order_by;
        }




        $rows = q($sql);
        foreach($rows as $row){
            $exportTableData = array();
            foreach($display_fields as $field){
                $cell_value = $row[$field]; 	// retain original data
                $cell_data = $cell_value;




                // Check for user defined formatting functions
                if (isset($format_field_with_function[$field]) && $format_field_with_function[$field] != ''){
                    $cell_data = call_user_func($format_field_with_function[$field], $cell_data);
                }

                if (isset($format_field_with_function_adv[$field]) && $format_field_with_function_adv[$field] != ''){
                    $cell_data = call_user_func($format_field_with_function_adv[$field], $cell_data, $id);
                }

                // Check whether field is a foreign key linking to another table
                $found_category_index = array_search($field, $db_table_fk_array);


                if (is_numeric($found_category_index)) {
                    //this field is a reference to another table's primary key (eg it must be a foreign key)
                    $category_field_name = $category_field_array[$found_category_index];
                    $category_table_name = $category_table_array[$found_category_index];
                    $category_table_pk 	 = $category_table_pk_array[$found_category_index];

                    $joinTableCol = $category_table_name . "_" . $category_field_name;

                    $cell_data = $row[$joinTableCol];



                    /*$selected_dropdown_text = "--"; //in case value is blank
                    if ($cell_data != ""){
                        $selected_dropdown_text = q1("SELECT $category_field_name FROM $category_table_name WHERE $category_table_pk = \"" . $cell_value . "\"");
                        $cell_data = $selected_dropdown_text;
                    }*/


                }

                $exportTableData[] = $this->escapeCSVValue($cell_data);
            }
            $data .= join(',',$exportTableData) . "\n";
        }




        // clean up
        unset($exportTableHeadings);
        unset($exportTableData);

        return $headers.$data;
    }

    //a file must have been "sent"/posted for this to work
    function uploadFile($row_id, $file_field, $resize = "", $allowedExts = array()){
        global $report_msg, $error_msg, $tool;
        //$amgSpace = new AmgSpace();
        $this->amgSpace->setDomainName(DOMAIN_NAME);


        /*@$fileName  = $_FILES[$file_field]['name'];
        @$tmpName   = $_FILES[$file_field]['tmp_name'];
        @$fileSize  = $_FILES[$file_field]['size'];
        @$fileType  = $_FILES[$file_field]['type'];

        if (is_array($allowedExts)){
	        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); //gets file ext (lowercase)
	        if ( !in_array($fileExt, $allowedExts)){
	        	$error_msg[] = "Upload failed. Selected file extention <b>.{$fileExt}</b> was, but this is not an permitted file extension.";
	        	return false;
	        }
	    }

        $new_filename = make_filename_safe($fileName);
        if ($this->filename_append_field != ""){
            if ($_REQUEST[$this->filename_append_field] != ''){
                $new_filename = $_REQUEST[$this->filename_append_field] . "_" . $new_filename;
            }
            else{
                if ($this->filename_append_field == $this->db_table_pk){
                    $new_filename = $row_id . "_" . $new_filename;
                }
                else{
                    $colSelect = $this->db_table . "." . $this->db_table_pk;
                    @$db_value_to_append = q1("SELECT $this->filename_append_field FROM $this->db_table WHERE $this->db_table_pk = $row_id");
                    if ($db_value_to_append != ""){
                        $new_filename = $db_value_to_append . "_" . $new_filename;
                    }
                }

            }
        }

        $newAmgFileName = $this->file_upload_info[$file_field]['amg_path']['path'] . $new_filename;
        $destination = $upload_folder . $newAmgFileName;



        $success = move_uploaded_file ($tmpName, $destination);*/

        $this->amgSpace->setAllowedMimeTypes($allowedExts);
        $this->amgSpace->setFileInputName($file_field);

        if(!empty($resize)){
            $resize = explode(",",$resize);
            if(is_array($resize)){
                $w = isset($resize[0]) ? $resize[0] : 0;
                $h = isset($resize[1]) ? $resize[1] : 0;
                if(!empty($w) && !empty($h)){
                    $this->amgSpace->setImageSize(array($w,$h));
                }
            }
        }

        $fileArr = $this->amgSpace->upload();

        $amgSpace = null;

        $newAmgFileName = $fileArr['value'];

        if ( !empty($fileArr['value']) ){
            $colSelect = $this->db_table . "." . $this->db_table_pk;
            $update_success = qr("UPDATE $this->db_table SET $file_field = \"$newAmgFileName\" WHERE $colSelect = $row_id");

            if ($this->onFileUploadExecuteCallBackFunction != ''){
                $file_info_array = array();
                $file_info_array[id]        = $row_id;
                $file_info_array[field]     = $file_field;
                $file_info_array[fileName]  = $newAmgFileName;
                $file_info_array[fileSize]  = 100;
                $file_info_array[fldType]   = "png";
                call_user_func($this->onFileUploadExecuteCallBackFunction, $file_info_array);
            }

            if ($update_success) return true;
        }
        else{
            $error_msg[] = "There was an error uploading your file. Check permissions of the destination directory (make sure is set to 777).";
        }

        return false;
    }

	function showTable(){


        /*

        if ($this->field_count == 0){
            $error_msg[] = $transArr[$lang]['no_fields_in_this_table'];;
            echo_msg_box();
            exit();
        }*/

        global $error_msg;
        global $report_msg;
        global $warning_msg_displayed;
        global $num_ajaxCRUD_tables_instantiated;
        global $headerAdded;

        $num_ajaxCRUD_tables_instantiated++;


        $transArr = $this->transObj->transArray();;




        /* Filter Table (if there are request parameters)
        */
		$count_filtered = 0;
		$action = "";
		if (isset($_REQUEST['ajaxCrudAction'])){
			$action = $_REQUEST['ajaxCrudAction'];
		}



        foreach ($this->fields as $field){


			//this if condition is so MULTIPLE ajaxCRUD tables can be used on the same page.
			if (@$_REQUEST['table'] == $this->db_table){



				if (isset($_REQUEST[$field]) && $_REQUEST[$field] != '' && ($action != 'add' && $action != 'delete' && $action != 'update' && $action != 'upload' && $action != 'delete_file')){
					$filter_field = $field;
					$filter_value = $_REQUEST[$field];
					if ($this->exactSearchField[$filter_field]){
						//exact search (is set by
						$filter_where_clause = "WHERE $filter_field = \"$filter_value\"";
					}
					else{
						//approximate search (default)
						$filter_where_clause = "WHERE $filter_field LIKE \"%" . $filter_value . "%\"";
					}

                    //die('Call');
					$this->addWhereClause($filter_where_clause);
					$this->filtered_table = true;
					$count_filtered++;
				}
			}
		}
        if ($count_filtered > 0){
            $this->filtered_table;
        }
        else{
            $this->filtered_table = false;
        }


        /* Sort Table
           Note: this cancels out default sorting set by addOrderBy()
        */
        if (isset($_REQUEST['table']) && isset($_REQUEST['sort_field'])){
			if ($this->db_table == $_REQUEST['table'] && $_REQUEST['sort_field'] != ''){
				$sort_field = $_REQUEST['sort_field'];
				$user_sort_order_direction = $_REQUEST['sort_direction'];

				if ($user_sort_order_direction == 'asc'){
					$this->sort_direction = "desc";
				}
				else{
					$this->sort_direction = "asc";
				}
				$sort_sql = " ORDER BY $sort_field $this->sort_direction";
				$this->addOrderBy($sort_sql);
				$this->sorted_table = true;
			}
		}

        //the HTML to display
        $top_html = "";     //top header stuff
        $table_html = "";   //for the html table itself
        $bottom_html = "";
        $add_html = "";     //for the add form
        $add_htmlAMG = "";     //for the add form

        $html = ""; //all combined

        if ( $num_ajaxCRUD_tables_instantiated == 1 && !$headerAdded){
            //pull in the  css and javascript files
            $this->insertHeader($this->ajax_file);


        }



        if ($this->doActionOnShowTable){
            if (isset($_REQUEST['ajaxCrudAction']) && $_REQUEST['ajaxCrudAction'] != ''){

                $this->doAction($_REQUEST['ajaxCrudAction']);
            }
        }

		$item = $this->item;


		//this array is used to populate the dropdown boxes set by defined relationships (to other tables)
		$dropdown_array = array();
		foreach ($this->category_table_array as $key => $category_table){
            $category_field_name = $this->category_field_array[$key];
            $category_table_pk   = $this->category_table_pk_array[$key];

            $order_by = '';
            if ($this->category_sort_field_array[$key] != ''){
                $order_by = " ORDER BY " . $this->category_sort_field_array[$key];
            }

            $whereclause  = '';

            if ($this->category_whereclause_array[$key] != ''){
                $whereclause = ' WHERE 1 ' . $this->category_whereclause_array[$key];
            }

            $newQ = "SELECT $category_table_pk, $category_field_name FROM $category_table $whereclause $order_by";

            $dropdown_array[] = q($newQ);

		}

		$top_html .= "<a name='ajaxCRUD" . $num_ajaxCRUD_tables_instantiated ."' id='ajaxCRUD" . $num_ajaxCRUD_tables_instantiated  ."'></a>\n";



        $top_html .= '<div id="ajaxcrud_succ" class="alert alert-success" style="display:none"></div>';
        $top_html .= '<div id="ajaxcrud_error" class="alert alert-error" style="display:none"></div>';



        $top_html .= '
        <div class="social-box">
            <div class="header">
                <div class="tools"><div class="btn-group">
                    ';


        if($this->isAmgCrudFilterMode()){
           $top_html .= '<button class="btn btn-danger" id="amgAjaxCrudClearFilter" style="margin-left: 10px; margin-right: 10px;" data-toggle="collapse"> Clear Filter <i class="icon-filter"></i></button>';
        }



        if ($this->add){
            $addButtonVal = $this->addText . " " . $item;
            if ($this->addButtonText != ""){
                $addButtonVal = $this->addButtonText;
            }

            //$add_html .= "   <input type=\"button\" value=\"$addButtonVal\" class=\"btn editingSize\" onClick=\"$('#add_form_$this->db_table').slideDown('fast'); x = document.getElementById('add_form_$this->db_table'); t = setTimeout('x.scrollIntoView(false)', 200); \">\n";


            $add_htmlAMG .= "   <button class=\"btn editingSize fonts\" onClick=\"$('#amg_add_form_crud').slideDown('fast'); x = document.getElementById('amg_add_form_crud'); t = setTimeout('x.scrollIntoView(false)', 200); \">$addButtonVal <i class=\"icon-edit\"></i></button>";

        }


        if($this->isPrintButton()){
            $top_html .= '<button class="btn btn-info fonts" data-toggle="collapse" onClick="printSpecial()">' . $transArr['print'] .' <i class="icon-print"></i></button>';
        }



        $top_html .= '


                    <button class="btn btn-success fonts" data-toggle="collapse" data-target="#advanced-search"> '.$transArr['search'].' <i class="icon-filter"></i> </button>';

        $top_html .= $add_htmlAMG;




        if($this->isCustomButtonStatus()){
            $top_html .= $this->getCustomButton();
        }


        if ($this->showCSVExport) {
            if($this->isAmgCsv()){
                $requiredCsvParamsArr =
                        array(
                                "display_fields" => $this->display_fields
                                ,"displayAs_array" => $this->displayAs_array
                                ,"db_table" => $this->db_table
                                ,"sql_where_clause" => $this->sql_where_clause
                                ,"sql_order_by" => $this->sql_order_by
                                ,"format_field_with_function" => $this->format_field_with_function
                                ,"format_field_with_function_adv" => $this->format_field_with_function_adv
                                ,"db_table_fk_array" => $this->db_table_fk_array
                                ,"category_field_array" => $this->category_field_array
                                ,"category_table_array" => $this->category_table_array
                                ,"category_table_pk_array" => $this->category_table_pk_array
                        );

                $serializeParams = urlencode(serialize($requiredCsvParamsArr));
                $top_html .= "<form action=\"\" name=\"CSVExport\" method=\"POST\" style='float:left; margin: 0'>\n";
                $top_html .= "  <input type=\"hidden\" name=\"fileName\" value=\"tableoutput.csv\" />\n";
                $top_html .= "  <input type=\"hidden\" name=\"customAction\" value=\"exportToCSV\" />\n";
                $top_html .= "  <input type=\"hidden\" name=\"amgCsvParam\" value=\"" . $serializeParams . "\" />\n";
                //$top_html .= "	<input type=\"hidden\" id='amg_hidden_export' name=\"tableData\" value=\"" . $this->createCSVOutput($serializeParams) . "\" />\n";
                $top_html .= "  <button type=\"submit\" name=\"submit\" class=\"btn btn-primary fonts\"> " .$transArr['export'] . "<i class=\"icon-arrow-right\"></i></button>";
                $top_html .= "</form>\n";
            }
        }





        $amgTotalFilterFields = count($this->ajaxFilter_fields);


        $top_html .= '</div></div>
            </div>
            <div class="body">
                <div id="jamia_msg">&nbsp;</div>
                <div id="advanced-search" class="collapse">

                    <div align="center"><div class="table-responsive" style=" overflow: auto;">
        ';
        if (!isset($extra_query_params)) $extra_query_params = "";//this is used by certain applications which require extra query params to be passed (not typical)

        if ($amgTotalFilterFields > 0){
            //$top_html .= "<form id=\"" . $this->db_table . "_filter_form\" class=\"ajaxCRUD\">\n";
            $top_html .= "<form id=\"amgAjaxCrudFilterForm\" class=\"ajaxCRUD\" action=''>\n";

            $top_html .= "<table cellspacing='5' align='center'><tr><thead>";

            $selectedFilterField = "";

            foreach ($this->ajaxFilter_fields as $filter_field){
                $display_field = $filter_field;
                if (@$this->displayAs_array[$filter_field] != ''){
                    $display_field = $this->displayAs_array[$filter_field];
                }

                //TODO: this var is used to see if there is a defined relationship with the field (I hate this approach and need to re-architect it!)
                $found_category_index = array_search($filter_field, $this->db_table_fk_array);

                $textbox_size = $this->ajaxFilterBoxSize[$filter_field];

                $filter_value = "";
                if (isset($_REQUEST[$filter_field]) && $_REQUEST[$filter_field] != ''){
                	//$filter_value = $_REQUEST[$filter_field];
                	$filter_value = utf8_encode($_REQUEST[$filter_field]);
                }

                $top_html .= "<th class='fonts'><b>$display_field</b>:";

				//check for valid values (set by defineAllowableValues)
				if (isset($this->allowed_values[$filter_field]) && is_array($this->allowed_values[$filter_field])){
					$top_html .= "<select name=\"$filter_field\">";
					$top_html .= "<option value=\"\" selected='selected'>==Select==</option>\n";
					foreach ($this->allowed_values[$filter_field] as $list){
						if (is_array($list)){
							$list_val = $list[0];
							$list_option = $list[1];
						}
						else{
							$list_val = $list;
							$list_option = $list;
						}

                        $selectFilterField = "";

                        if(isset($_GET[$filter_field])){
                            if(!empty($_GET[$filter_field])){
                                if($_GET[$filter_field] == $list_val){
                                    $selectFilterField =  " selected='selected'";
                                }
                                else{
                                    $selectFilterField = "";
                                }
                            }
                        }


						$top_html .= "<option value=\"$list_val\" $selectFilterField>$list_option</option>\n";
					}
					$top_html .= "</select>\n";
				}
				//check for defined link to another db table (pk/fk relationship) (set by defineRelationship)
				else if (is_numeric($found_category_index)){
					$top_html .= "<select name=\"$filter_field\">";
					$top_html .= "<option value=\"\" selected='selected'>==Select==</option>\n";

					//this field is a reference to another table's primary key (eg it must be a foreign key)
					$category_field_name = $this->category_field_array[$found_category_index];
					$category_table_name = $this->category_table_array[$found_category_index];
					$category_table_pk 	 = $this->category_table_pk_array[$found_category_index];


					//this array is set above (used a few places in the class) - sorry, a bit of repeating code here :-(
					foreach ($dropdown_array[$found_category_index] as $dropdown){
						$dropdown_value = $dropdown[$this->category_table_pk_array[$found_category_index]];
						$dropdown_text = $dropdown[$this->category_field_array[$found_category_index]];

                        $selectFilterField = "";

                        if(isset($_GET[$filter_field])){
                            if(!empty($_GET[$filter_field])){
                                if($_GET[$filter_field] == $dropdown_value){
                                    $selectFilterField =  " selected='selected'";
                                }
                                else{
                                    $selectFilterField = "";
                                }
                            }
                        }

						$top_html .= "<option value=\"$dropdown_value\" $selectFilterField>$dropdown_text</option>\n";
					}



					$top_html .= "</select>\n";
				}
				//check for a checkbox for this field
				else if (isset($this->checkbox[$filter_field]) && is_array($this->checkbox[$filter_field])){
					$values = $this->checkbox[$filter_field];
					$value_on = $values[0];
					$value_off = $values[1];

					$checked = '';
					if (isset($field_value) && $field_value == $value_on) $checked = "checked";

					$top_html .= "<input type=\"checkbox\" name=\"$filter_field\" $checked value=\"$value_on\">";
				}
				//a "regualar" textbox filter box
				else{
					$customClass = "";
					if (isset($this->display_field_with_class_style[$filter_field]) && $this->display_field_with_class_style[$filter_field] != '') {
						$customClass = $this->display_field_with_class_style[$filter_field];
					}

                    $amgTextBoxFilterField = !empty($_REQUEST[$filter_field]) ? $_REQUEST[$filter_field] : "";

                	$top_html .= "<input type=\"text\" class=\"$customClass\" size=\"$textbox_size\" name=\"$filter_field\" value=\"$amgTextBoxFilterField\">";
                }

            }

            $top_html .= "&nbsp;&nbsp;</th>";

            $top_html .= "<th><button type='button' id='amgCrudSearchButton' class='btn btn-primary fonts' style='margin-bottom: 10px;'> Search <i class=\"icon-filter\"></i></button></th>";
            $top_html .= "</tr></thead></table>\n";

            //$top_html .= "<input type='hidden' name='amg_crud_search_table' value='".$this->db_table."'>\n";
            $top_html .= "</form>\n";
            $top_html .= '</div></div></div>';
        }


		#############################################
		#
		# Begin code for displaying database elements
		#
		#############################################

		$select_fields = implode(",", $this->fields);



        if(!empty($this->db_table_fk_array)){
           $this->createQueryHelper();
        }


               //$count = q1("SELECT COUNT(*) FROM " . $this->db_table . $this->getAmgExternalJoins() . $this->sql_where_clause);



       if(!empty($this->db_table_fk_array)) {
           $sql = "SELECT " . $this->db_table . ".*"; //added name for table (t) in case where clauses want to use it (7.2.2)
           $sql .= $this->getAmgExternalFields();
           $sql .= " FROM " . $this->db_table;
           $sql .= $this->getAmgExternalJoins();



       }
       else{
           $sql = "SELECT * FROM " . $this->db_table; //added name for table (t) in case where clauses want to use it (7.2.2)
       }

        $sql .= $this->sql_where_clause;

        $sql .= implode(" ", $this->getJoinWhereClause());

        //echo '<pre>';print_r($this->getJoinSortItems() );echo '</pre>';



        $sql .= $this->sql_order_by;



        if ($this->showPaging){

            $pageid = "";
            if (isset($_REQUEST['pid'])){
            	$pageid        = $_REQUEST['pid'];//Get the pid value
            }
            if(intval($pageid) == 0) $pageid  = 1;
            $Paging        = new paging();
            $Paging->tableName = $this->db_table;

            $total_records = $Paging->myRecordCount($sql);//count records
            $totalpage     = $Paging->processPaging($this->limit,$pageid);
            $rows          = $Paging->startPaging($sql);//get records in the databse
            $links         = $Paging->pageLinks(basename($_SERVER['PHP_SELF']));//1234 links
            unset($Paging);
        }
        else{
            $rows = q($sql . $this->sql_limit);
        }
        //echo $sql;

		//$row_count = count($rows); //count should NOT consider paging
		$row_count = $this->getNumRows();

        $this->row_count = $row_count;
        $_SESSION['row_count'] = $row_count; //DEPRECATED
        //$_SESSION[$this->db_table . '_row_count'] = $row_count;
        $_SESSION['amg_row_count'] = $row_count;

        if ($row_count == 0){
            $report_msg[] = $this->emptyTableMessage;
        }

        #this is an optional function which will allow you to display errors or report messages as desired. comment it out if desired
        //only show the message box if it hasn't been displayed already
        if ($warning_msg_displayed == 0 || $warning_msg_displayed == ''){
            echo_msg_box();
        }

        $top_html .= "<div id='$this->db_table'>\n";

        if ($row_count > 0){

            /*
            commenting out the 'edit item' text at the top; feel free to add back in if you want
            $edit_word = "Edit";
            if ($row_count == 0) $edit_word = "No";
            $top_html .= "<h3>Edit " . $this->item_plural . "</h3>\n";
            */

            //for vertical display, have a little spacing in there
            if ($this->orientation == 'vertical' && $this->cellspacing == ""){
            	$this->cellspacing = 2;
            }



            $table_html .= "<div id=\"printReady\"><table align='center' class='ajaxCRUD table table-bordered table-striped table-hover' style='width:95%;' name='table_" . $this->db_table . "' id='table_" . $this->db_table . "' cellspacing='" . $this->cellspacing . "' border=" . $this->border . ">\n";

			//only show the header (field names) at top for horizontal display (default)
			if ($this->orientation != 'vertical'){



				if ($this->includeTableHeaders){
					$table_html .= "<thead><tr>\n";
					//for an (optional) checkbox
					if ($this->showCheckbox){
						$table_html .= "<th>&nbsp;</th>";
					}



					foreach ($this->display_fields as $field){
						$field_name = $field;
						if (@$this->displayAs_array[$field] != ''){
							$field = $this->displayAs_array[$field];
						}

						if (!$this->fieldInArray($field_name, $this->unsortable_fields)){
							$fieldHeaderHTML = "<a href='javascript:;' onClick=\"changeSort('$this->db_table', '$field_name', '$this->sort_direction');\" >" . $field . "</a>";
						}
						else{
							$fieldHeaderHTML = $field;
						}

						if (array_key_exists($field_name, $this->checkboxall)) {
							$table_html .= "<th class='fonts'><input type=\"checkbox\" name=\"$field_name" . "_checkboxall\" value=\"checkAll\" onClick=\"
								if (this.checked) {
									setAllCheckboxes('$field_name" . "_fieldckbox',false);
								} else {
									setAllCheckboxes('$field_name" . "_fieldckbox',true);
								}
								\">";

							if ($this->checkboxall[$field_name] == true) {
								$table_html .= $fieldHeaderHTML;
							}
							$table_html .= "</th>";
						}
						else {
							$table_html .= "<th class='fonts'>$fieldHeaderHTML</th>";
						}
					}

					if ($this->delete || (count($this->row_button)) > 0){
						$table_html .= "<th class='fonts'>" . $this->actionText . "</th>\n";
					}

					$table_html .= "</tr></thead>\n";
				}
			}

            $count = 0;
            $class = "odd";

            $attach_params = "";

			$valign = "middle";

            foreach ($rows as $row){
                $id = $row[$this->db_table_pk];

				if ($this->orientation == 'vertical'){
					$class = "vertical" . " $class";
					$valign = "middle";
				}



                $table_html .= "<tr class='$class' id=\"" . $this->db_table . "_row_$id\" valign='{$valign}'>\n";


                if ($this->showCheckbox && $this->orientation != 'vertical'){
                    $checkbox_selected = "";

                    if ($id == $_REQUEST[$this->db_table_pk]) $checkbox_selected = " checked";
                    $table_html .= "<td class='fonts'><input type='checkbox' $checkbox_selected onClick=\"window.location ='" . $_SERVER['PHP_SELF'] . "?$this->db_table_pk=$id'\" /></td>\n";
                }

				$canRowBeUpdated = true;
				if (isset($this->validate_update_with_function) && $this->validate_update_with_function != ''){
					$canRowBeUpdated = call_user_func($this->validate_update_with_function, $id);
				}

                foreach($this->display_fields as $field){
                    $cell_data = $row[$field];
                    $cell_value = $cell_data; //retain original value in new variable (before executing callback method)

                $amgTextBoxPassedDataArr =
                        array(
                         "id"=>$id
                        ,"field"=>      $field
                        ,"table"    =>   $this->db_table
                        ,"pkcol"    =>   $this->db_table_pk
                );



                $amgFieldAttributesArr = $this->getAmgInputDataType();



                    $amgInputDataType = " class='amg_textbox_class fonts' data-type='text'";

                if(isset($amgFieldAttributesArr[$field])){
                    $amgFieldAttributes = $amgFieldAttributesArr[$field];
                    foreach ($amgFieldAttributes as $amgFieldAttribute => $amgFieldAttributeVal){

                        if($amgFieldAttributeVal == 'date'){
                            $cell_value = changeDateFormat($cell_data);
                            $amgInputDataType = " class='amg_textbox_class' data-type='date' data-placement='right' data-viewformat='dd-mm-yyyy' data-original='".$cell_value."'";
                        }

                        $amgTextBoxPassedDataArr[$amgFieldAttribute] = $amgFieldAttributeVal;
                    }
                }

                $amgTextBoxPassedDataArr = json_encode($amgTextBoxPassedDataArr);

                    //for adding a button via addButtonToRow
                    if (count($this->row_button) > 0){
                        $attach_params .= "&" . $field . "=" . urlencode($cell_data);
                    }



                    if (isset($this->format_field_with_function[$field]) && $this->format_field_with_function[$field] != ''){
                        $cell_data = call_user_func($this->format_field_with_function[$field], $cell_data);
                    }

                    if (isset($this->format_field_with_function_adv[$field]) && $this->format_field_with_function_adv[$field] != ''){
                        $cell_data = call_user_func($this->format_field_with_function_adv[$field], $cell_data, $id);
                    }

                    //try to find a reference to another table relationship
                    $found_category_index = array_search($field, $this->db_table_fk_array);

					//if orientation is vertical show the field name next to the field
					if ($this->orientation == 'vertical'){
						if ($this->displayAs_array[$field] != ''){
							$fieldName = $this->displayAs_array[$field];
						}
						else{
							$fieldName = $field;
						}
						$table_html .= "<th class='vertical fonts'>$fieldName</th>";
					}

                    //don't allow uneditable fields (which usually includes the primary key) to be editable
                    if ( !$canRowBeUpdated || $this->fieldInArray($field, $this->file_uploads) || ( ($this->fieldInArray($field, $this->uneditable_fields) && (!is_numeric($found_category_index))) ) ){

                        $table_html .= "<td class='fonts'>";

                        $key = array_search($field, $this->display_fields);

                        if ($this->fieldInArray($field, $this->file_uploads) && !$this->fieldInArray($field, $this->uneditable_fields)){

                            //a file exists for this field
                            $file_dest = "";
                            if ($cell_data != ''){
                                $file_link = $this->file_upload_info[$field]['relative_folder'] . $row[$field];
                                $file_dest = $this->file_upload_info[$field]['destination_folder'];

                                //$table_html .= "<span style=\"font-size: 9px;\" id='text_" . $field . $id . "'><a target=\"_new\" href=\"$file_link\">" . $cell_data . "</a> <a style=\"font-size: 9px;\" href=\"javascript:\" onClick=\"document.getElementById('file_$field$id').style.display = ''; document.getElementById('text_$field$id').style.display = 'none'; \">" . $this->fileEditText . "</a> | <a style=\"font-size: 9px;\" href=\"javascript:\" onClick=\"deleteFile('$field', '$id')\">" . $this->fileDeleteText . "</a></span>\n";
                                $table_html .= "<span style=\"font-size: 9px;\" id='text_" . $field . $id . "'>" . $cell_data . "<a style=\"font-size: 9px;\" href=\"javascript:\" onClick=\"document.getElementById('file_$field$id').style.display = 'block'; document.getElementById('text_$field$id').style.display = 'none'; \">" . $this->fileEditText . "</a>";
                                if($this->isShowFileDelete()){
                                    $table_html .= " | <a style=\"font-size: 9px;\" href=\"javascript:\" onClick=\"deleteFile('$field', '$id')\">" . $this->fileDeleteText . "</a></span>\n";
                                }

                                $table_html .= '</span>';

                                $table_html .= "<div id='file_" . $field . $id . "' style='display:none;'>\n";
                                $table_html .= $this->showUploadForm($field, $file_dest, $id);
                                $table_html .= "</div>\n";
                            }
                            else{
                                $table_html .= "<span id='text_" . $field . $id . "'><a style=\"font-size: 9px;\" href=\"javascript:\" onClick=\"document.getElementById('file_$field$id').style.display = 'block'; document.getElementById('text_$field$id').style.display = 'none'; \">Add File</a></span> \n";

                                $table_html .= "<div id='file_" . $field. $id . "' style='display:none;'>\n";
                                $table_html .= $this->showUploadForm($field, $file_dest, $id);
                                $table_html .= "</div>\n";
                            }
                        }
                        else{
                            //added in 6.5. allows defineAllowableValues to work even when in readonly mode
                            if (isset($this->allowed_values[$field]) && is_array($this->allowed_values[$field])){
								foreach ($this->allowed_values[$field] as $list){
									if (is_array($list)){
										$list_val = $list[0];
										$list_option = $list[1];
									}
									else{
										$list_val = $list;
										$list_option = $list;
									}

									//fixed bug in 8.76. cell_value ensures we're looking at original value vs value set by user-defined function
									if ($list_val == $cell_value) $table_html .= $list_option;
								}
                            }
                            else{
                            	$table_html .= stripslashes($cell_data);
                            }
                        }
                    }//if field is not editable
                    else{


                        if (!is_numeric($found_category_index)){

                            //was allowable values for this field defined?
                            if ( (isset($this->allowed_values[$field]) && is_array($this->allowed_values[$field])) && !isset($this->field_no_dropdown[$field]) ){
                                $table_html .= '<td class="fonts" data-type="select" data-pk="'.$id.'" data-url="/post" data-title="Select '.$field.'">';
                                $table_html .= $this->makeAjaxDropdown($id, $field, $cell_value, $this->db_table, $this->db_table_pk, $this->allowed_values[$field]);
                            }
                            else{

                                //if a checkbox
                                if (isset($this->checkbox[$field]) && is_array($this->checkbox[$field])){
                                    $table_html .= '<td class="fonts" data-type="checklist" data-pk="'.$id.'" data-url="/post" data-title="Select allowed value">';
                                    $table_html .= $this->makeAjaxCheckbox($id, $field, $cell_value);
                                }
                                else{
                                    //is an editable field
                                    //if ($cell_data == '') $cell_data = "&nbsp;&nbsp;";

                                    $field_onKeyPress = "";
                                    if ($this->fieldIsInt($this->getFieldDataType($field)) || $this->fieldIsDecimal($this->getFieldDataType($field))){
                                        $field_onKeyPress = "return fn_validateNumeric(event, this, 'n');";
                                        if ($this->fieldIsDecimal($this->getFieldDataType($field))){
                                            $field_onKeyPress = "return fn_validateNumeric(event, this, 'y');";
                                        }
                                    }

                                    if ($this->fieldIsEnum($this->getFieldDataType($field))){
                                        $allowed_enum_values_array = $this->getEnumArray($this->getFieldDataType($field));
                                        $table_html .= '<td class="fonts" data-type="select" data-pk="'.$id.'" data-url="/post" data-title="Select allowed value">';
                                        $table_html .= $this->makeAjaxDropdown($id, $field, $cell_value, $this->db_table, $this->db_table_pk, $allowed_enum_values_array);
                                    }
                                    else{
										//updated logic in 7.1 to enable a textarea to be 'forced' if desired [thanks to dpruitt for code revision]
										$field_length = strlen($row[$field]);
										if(isset($this->textarea_height[$field]) && $this->textarea_height[$field] != '' || $field_length > 51){
											$textarea_height = '';
											if (@$this->textarea_height[$field] != '') $textarea_height = $this->textarea_height[$field];
                                            //$table_html .= '<td class="fonts" data-type="textarea" data-pk="'.$id.'" data-title="Select allowed value">';
											//$table_html .= $this->makeAjaxEditor($id, $field, $cell_value, 'textarea', $textarea_height, $cell_data, $field_onKeyPress);
                                            $table_html .= "<td class='amg_textbox_class fonts' data-type='textarea' data-pk='$amgTextBoxPassedDataArr'>" . $cell_value;
										}
										else{
                                            //if the textbox width was set manually with function setTextboxWidth
                                            if (isset($this->textboxWidth[$field]) && $this->textboxWidth[$field] != ''){
                                            	$field_length = $this->textboxWidth[$field];
                                            }


                                            if (!$this->fieldInArray($field, $this->uneditable_fields)){
                                                $table_html .= "<td ".$amgInputDataType." data-pk='".$amgTextBoxPassedDataArr."'>" . $cell_value;
                                            }
                                            else{
                                                $table_html .= "<td class='fonts'>".$cell_value."</td>";
                                            }



											//$table_html .= $this->makeAjaxEditor($id, $field, $cell_value, 'text', $field_length, $cell_data, $field_onKeyPress);
										}



                                    }
                                }
                            }
                        }
                        else{
                            //this field is a reference to another table's primary key (eg it must be a foreign key)
                            $category_field_name = $this->category_field_array[$found_category_index];
                            $category_table_name = $this->category_table_array[$found_category_index];
                            $category_table_pk 	 = $this->category_table_pk_array[$found_category_index];
                            $amgRefrenceColJoinClName = $category_table_name . "_" . $category_field_name;
                            $amgSorfFieldCat = $this->category_sort_field_array[$found_category_index];
                            $amgWhereFieldCat = $this->category_whereclause_array[$found_category_index];




                            $selected_dropdown_text = "--"; //in case value is blank
                            $selected_dropdown_text = $row[$amgRefrenceColJoinClName];
                            if ($cell_data != ""){
                                //$selected_dropdown_text = q1("SELECT $category_field_name FROM $category_table_name WHERE $category_table_pk = \"" . $cell_value . "\"");
                                //echo "field: $field - $selected_dropdown_text <br />\n";
                            }
                            if (!$this->fieldInArray($field, $this->uneditable_fields)){



                                $amgTextBoxPassedDataArr =
                                        array(
                                         "id"=>$id
                                        ,"field"=>      $field
                                        ,"table"    =>   $this->db_table
                                        ,"pkcol"    =>   $this->db_table_pk
                                );


                                $amgFieldAttributesArr = $this->getAmgInputDataType();


                                if(isset($amgFieldAttributesArr[$field])){
                                    $amgFieldAttributes = $amgFieldAttributesArr[$field];
                                    foreach ($amgFieldAttributes as $amgFieldAttribute => $amgFieldAttributeVal){

                                        $amgTextBoxPassedDataArr[$amgFieldAttribute] = $amgFieldAttributeVal;
                                    }
                                }



                                $amgTextBoxPassedDataArr = json_encode($amgTextBoxPassedDataArr);

                                $table_html .= "<td class='amg_select_crud_class fonts' data-sort='".$amgSorfFieldCat."' data-where='".$amgWhereFieldCat."' data-reftableid='".$category_table_pk."' data-refcol='".$category_field_name."' data-name='".$field."' data-id='".$id."' data-table='".$category_table_name."' data-type='select' data-pk='".$amgTextBoxPassedDataArr."' data-title='Please Select'>" . $selected_dropdown_text;
                                //$table_html .= $this->makeAjaxDropdown($id, $field, $cell_value, $category_table_name, $category_table_pk, $dropdown_array[$found_category_index], $selected_dropdown_text);
                            }
                            else{
                                $table_html .= '<td class="fonts">' . $selected_dropdown_text . '</td>';
                            }
                        }

                    }

                    $table_html .= "</td>\n";
                    if ($this->orientation == 'vertical'){
                    	$table_html .= "</tr><tr class='$class' id=\"" . $this->db_table . "_row_$id\" valign='middle'>\n";
                    }

                }//foreach displayFields


                $customClass = "";

                if (isset($this->display_field_with_class_style[$field]) && $this->display_field_with_class_style[$field] != '') {
                    $customClass = $this->display_field_with_class_style[$field];
                }

                if ($this->delete || (count($this->row_button)) > 0){

					if ($this->orientation == 'vertical'){
						$table_html .= "<th class='vertical'>" . $this->actionText . "</th>";
					}

                    $table_html .= "<td class='fonts'>\n";

                    if ($this->delete){

						$canRowBeDeleted = true;
						if (isset($this->validate_delete_with_function) && $this->validate_delete_with_function != ''){
							$canRowBeDeleted = call_user_func($this->validate_delete_with_function, $id);
						}

                        if ($canRowBeDeleted){
                        	$table_html .= "<input type=\"button\" class=\"btn editingSize\" onClick=\"confirmDelete('$id', '" . $this->db_table . "', '" . $this->db_table_pk ."');\" value=\"" . $this->deleteText . "\" />\n";
                        }
                    }

                    if (count($this->row_button) > 0){
                        foreach ($this->row_button as $button_id => $the_row_button){
                            $value = $the_row_button[0];
                            $url = $the_row_button[1];
                            $attach_param = $the_row_button[2]; //optional param
                            $javascript_onclick_function = $the_row_button[3]; //optional param
                            $window_to_open = $the_row_button[4]; //optional param

                            if ($attach_param == "all"){
                                $attach = "?attachments" . $attach_params;
                            }
                            else{
                                $char = "?";
                                if (stristr($url, "?") !== FALSE){
                                	$char = "&"; //the url already has get parameters; attach the id with it
                                }

                                $getParam = $this->db_table_pk;
								$valueToPass = $id;
                                if ($attach_param != "all" && $attach_param != ""){
                                	$getParam = $attach_param;
									//check to see if the field being passed is a db column
									if ($this->fieldInArray($attach_param, $this->fields)){
										$valueToPass = $row[$attach_param];
									}
                                }
                                $valueToPass = urlencode($valueToPass);
                                $attach = $char . $getParam . "=$valueToPass";
                            }

                            //its most likely a user-defined ajax function
                            if ($javascript_onclick_function != ""){
                                $javascript_for_button = "onClick=\"" . $javascript_onclick_function . "($id);\"";
                            }
                            else{
                                //either button-specific window is 'same' or global (all buttons' window is 'same'
                                if ($window_to_open == "same" && $this->addButtonToRowWindowOpen == "same"){
                                	$javascript_for_button = "onClick=\"location.href='" . $url . $attach . "'\"";
                                }
                                else{
                                	$javascript_for_button = "onClick=\"window.open('" . $url . $attach . "')\"";
                                }
                            }


                            $table_html .= "<input type=\"button\" $javascript_for_button class=\"btn editingSize fonts\" value=\"$value\" />\n";
                        }
                    }

                    $table_html .= "</td>\n";
                }

                $table_html .= "</tr>";

				if ($this->orientation == 'vertical'){
					$table_html .= "<tr><td colspan='2' style='border-top: 4px silver solid;' ></td></tr>\n";
				}


                if($count%2==0){
                    $class="cell_row";
                }
                else{
                    $class="odd";
                }

                $count++;


            }//foreach row

            $table_html .= "</table></div>\n";

            //paging links
            if ($totalpage > 1){
                $table_html .= "$links";
            }

        }//if rows > 0

        //closing div for paging links (if applicable)
        $bottom_html = "</div><br />\n";

		// displaying the export to csv button


        //$add_html .= "<center>\n";
        //now we come to the "add" fields


        //$add_html .= "</center>\n";

		if (count($this->bottom_button) > 0){
			foreach($this->bottom_button as $button){
				$button_value = $button[0];
				$button_url = $button[1];
				$button_tags = $button[2];

				if ($button_tags == ''){
					$tag_stuff = "onClick=\"location.href = '$button_url';\"";
				}
				else{
					$tag_stuff = $button_tags;
				}
				$add_html .= "  <input type=\"button\" value=\"$button_value\" href=\"$button_url\" class=\"btn\" $tag_stuff>\n";
			}
		}

        if ($this->add){
            //$add_html .= "  <input type=\"button\" value=\"Go Back\" class=\"btn\" onClick=\"history.back();\">\n";

            $amgClass = "";
            $formActionURL = $_SERVER['PHP_SELF'];
            if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != ""){
            	//some web applications require posting to the same exact page (with parameters included); this is useful if/when onAddExecuteCallbackFunction is used
            	$formActionURL = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            }

            $add_html .= "<form action=\"" . $formActionURL . "#ajaxCRUD\" id=\"amg_add_form_crud\" method=\"POST\" ENCTYPE=\"multipart/form-data\" style=\"display:none;\">\n";
            //$add_html .= "<br /><h3 align='center'>New <b>$item</b></h3>\n";
            $add_html .= "<br />\n";
            $add_html .= "<table align='center' name='form' class='table-bordered table-striped table-condensed'>\n";

            $applyParamaOnAdd[$field] = array();

            $applyParamaOnAddArr = $this->getAmgInputDataType();

            $nullFields = $this->getNullFields();
            $nullFieldString = "";
            $iazNull=0;
            foreach ($nullFields as $nullField){
                $iazNull++;
                if($iazNull > 1){
                    $nullFieldString .= "," . $nullField;
                }
                else{
                    $nullFieldString .= $nullField;
                }

            }

            $add_html .= '<input type="hidden" name="amg_hidden" value="'.$nullFieldString.'">';
            //for here display ALL 'addable' fields
            foreach($this->add_fields as $field){

                if(isset($applyParamaOnAddArr[$field])){
                    $applyParamaOnAdd[$field] = $applyParamaOnAddArr[$field];
                }

				$add_html .= "<tr>\n";
                if ($field != $this->db_table_pk || $this->on_add_specify_primary_key){
                    $field_value = "";

					$hideOnClick = "";
					$placeholder = "";
					//if a date field, show helping text
					if ($this->fieldIsDate($this->getFieldDataType($field))){
						//$placeholder = "YYYY-mm-dd";
						$placeholder = date("d-m-Y");
						//$hideOnClick = TRUE;
					}

                    //if initial field value for field is set
                    if (isset($this->initialFieldValue[$field]) && $this->initialFieldValue[$field] != ""){
                    	$field_value = $this->initialFieldValue[$field];
                    	//$hideOnClick = TRUE;
                    }

                    //the request (post/get) will overwrite any initial values though
                    if (isset($_REQUEST[$field]) && @$_REQUEST[$field] != '') {
                    	//$field_value = $_REQUEST[$field];  //note: disable because caused problems
                    	//$hideOnClick = FALSE;
                    }

                    if ($hideOnClick){
                    	//$hideOnClick = "onClick = \"this.value = ''\"";
                    }

                    if (@$this->displayAs_array[$field] != ''){
                        $display_field = $this->displayAs_array[$field];
                    }
                    else{
                        $display_field = $field;
                    }

                    $note = "";
                    if (isset($this->fieldNote[$field]) && $this->fieldNote[$field] != ""){
                    	$note = "&nbsp;&nbsp;<i>" . $this->fieldNote[$field] . "</i>";
                    }

                    if (isset($this->placeholderText[$field]) && $this->placeholderText[$field] != ""){
                    	$placeholder = $this->placeholderText[$field];
                    }

                    //if a checkbox
                    if (isset($this->checkbox[$field]) && is_array($this->checkbox[$field])){
                        $values = $this->checkbox[$field];
                        $value_on = $values[0];
                        $value_off = $values[1];
                        $add_html .= "<td class='fonts'>$display_field</td><td class='fonts'>\n";
                        $add_html .= "<input type='checkbox' name=\"$field\" value=\"$value_on\">\n";
                        $add_html .= "$note</td>\n";
                    }
                    else{
                        $found_category_index = array_search($field, $this->db_table_fk_array);
                        if (!is_numeric($found_category_index) && $found_category_index == ''){

                            //it's from a set of predefined allowed values for this field
                            if (isset($this->allowed_values[$field]) && is_array($this->allowed_values[$field])){
                                $add_html .= "<td class='fonts'>$display_field</td><td class='fonts'>\n";
                                $add_html .= "<select name=\"$field\" class='editingSize'>\n";
                                foreach ($this->allowed_values[$field] as $dropdown){
                                    $selected = "";
                                    $dropdown_value = $dropdown[0];
                                    $dropdown_text  = $dropdown[1];
                                    if ($field_value == $dropdown_value) $selected = " selected";
                                    $add_html .= "<option value=\"$dropdown_value\" $selected>$dropdown_text</option>\n";
                                }
                                $add_html .= "</select>$note</td>\n";
                            }
                            else{
                                if ($this->fieldInArray($field, $this->file_uploads)){
                                    //this field is an file upload
                                    $add_html .= "<th>$display_field</th><td class='fonts'><input class=\"editingSize\" type=\"file\" name=\"$field\" size=\"15\">$note</td></tr>\n";
                                    $file_uploads = true;
                                }
                                else{
                                    if ($this->fieldIsEnum($this->getFieldDataType($field))){
                                        $allowed_enum_values_array = $this->getEnumArray($this->getFieldDataType($field));
                                        $add_html .= "<td class='fonts'>$display_field</td><td class='fonts'>\n";
                                        $add_html .= "<select name=\"$field\" class='editingSize'>\n";
                                        foreach ($allowed_enum_values_array as $dropdown){
                                            $dropdown_value = $dropdown;
                                            $dropdown_text  = $dropdown;
                                            if ($field_value == $dropdown_value) $selected = " selected";
                                            $add_html .= "<option value=\"$dropdown_value\" $selected>$dropdown_text</option>\n";
                                        }
                                        $add_html .= "</select>$note</td></tr>\n";
                                    }//if enum field
                                    else{
                                        $field_onKeyPress = "";
                                        if ($this->fieldIsInt($this->getFieldDataType($field)) || $this->fieldIsDecimal($this->getFieldDataType($field))){
                                            $field_onKeyPress = "return fn_validateNumeric(event, this, 'n');";
                                            if ($this->fieldIsDecimal($this->getFieldDataType($field))){
                                                $field_onKeyPress = "return fn_validateNumeric(event, this, 'y');";
                                            }
                                        }
                                        //textarea fields
                                        if (isset($this->textarea_height[$field]) && $this->textarea_height[$field] != ''){
                                            $add_html .= "<td class='fonts'>$display_field</td><td class='fonts'><textarea $hideOnClick onKeyPress=\"$field_onKeyPress\" class=\"editingSize\" name=\"$field\" style='width: 97%; height: " . $this->textarea_height[$field] . "px;'>$field_value</textarea>$note</td></tr>\n";
                                        }
                                        else{
                                            //any ol' text data (generic text box)
                                            $fieldType = "text";
                                            $fieldSize = "";
                                            //change the type of textbox field if a password (HTML 5 compatible)
                                            //if (stristr($field, "password")){
                                            	//$fieldType = "password";
                                            //}
                                            //change the type of textbox field if a password (HTML 5 compatible)
                                            //if (stristr($field, "email")){
                                            	//$fieldType = "email";
                                            //}

                                            if ($this->fieldIsInt($this->getFieldDataType($field)) || $this->fieldIsDecimal($this->getFieldDataType($field))){
                                                $fieldSize = 7;
                                                $fieldType = "text";
                                            }

                                            //if the textbox width was set manually with function setTextboxWidth
                                            if (isset($this->textboxWidth[$field]) && $this->textboxWidth[$field] != ''){
                                            	$fieldSize = $this->textboxWidth[$field];
                                            }

											$customClass = "";
											// Apply custom CSS class to field if applicable
											if (isset($this->display_field_with_class_style[$field]) && $this->display_field_with_class_style[$field] != '') {
												$customClass = $this->display_field_with_class_style[$field];
											}
											$add_html .= "<td class='fonts'>$display_field</td><td class='fonts'><input onKeyPress=\"$field_onKeyPress\" class=\"editingSize $customClass\" type=\"$fieldType\" id=\"$field\" name=\"$field\" size=\"$fieldSize\" maxlength=\"150\" value=\"$field_value\" placeholder=\"$placeholder\" >$note</td></tr>\n";
											$placeholder = "";
                                        }
                                    }//else not enum field
                                }//not an uploaded file
                            }//not a pre-defined value
                        }//not from a foreign/primary key relationship
                        else{
                            //field is from a defined relationship
                            $key = $found_category_index;
                            $add_html .= "<td class='fonts'>$display_field</td><td class='fonts'>\n";
                            $add_html .= "<select name=\"$field\" class='editingSize'>\n";

                            if (@$this->category_required[$field] != TRUE){
                                if ($this->fieldIsInt($this->getFieldDataType($field)) || $this->fieldIsDecimal($this->getFieldDataType($field))){
                                    $add_html .= "<option value='0'>--Select--</option>\n";
                                }
                                else{
                                    $add_html .= "<option value=''>--Select--</option>\n";
                                }
                            }

                            foreach ($dropdown_array[$key] as $dropdown){
                                $selected = "";
                                $dropdown_value = $dropdown[$this->category_table_pk_array[$key]];
                                $dropdown_text  = $dropdown[$this->category_field_array[$key]];
                                if ($field_value == $dropdown_value) $selected = " selected";
                                $add_html .= "<option value=\"$dropdown_value\" $selected>$dropdown_text</option>\n";
                            }
                            $add_html .=  "</select>$note</td></tr>\n";
                        }
                    }//not a checkbox
                }//not the primary pk
            }//foreach

            $add_html .= "</tr><tr><td class='fonts' colspan='2' style='text-align: center'>\n";

			$postForm = "false";
			if (!$this->ajax_add){
				$postForm = "true";
			}
			//$add_html .= "<input class=\"btn editingSize\" type=\"button\" onClick=\"validateAddForm('$this->db_table', $postForm);\" value=\"Save $item\">";
			$add_html .= "<input class=\"btn editingSize\" type='submit' id=\"amg_ajaxcrud_add_button\" value=\"Save $item\">";
			$add_html .= "<input style='' class=\"btn editingSize\" type=\"button\" onClick=\"this.form.reset();$('#amg_add_form_crud').slideUp('slow');\" value=\"" . $this->cancelText . "\">";

            $amgParamsAdding = array(
                "amg_add_params" =>  $applyParamaOnAdd
                ,"fields" =>  $this->fields
                ,"db_table_pk" =>  $this->db_table_pk
                ,"add_values" =>  $this->add_values
                ,"file_uploads" =>  $this->file_uploads
                ,"table" =>  $this->db_table
                ,"on_add_specify_primary_key" =>  $this->on_add_specify_primary_key
                ,"primaryKeyAutoIncrement" =>  $this->primaryKeyAutoIncrement
                ,"onAddExecuteCallBackFunction" =>  $this->onAddExecuteCallBackFunction
                ,"file_upload_info" =>  $this->file_upload_info
                ,"ajaxAction" =>  "add"
            );

            $amgAddData = urlencode(serialize($amgParamsAdding));

            $add_html .= "</td></tr>\n</table>\n";
            //$add_html .= "<input type=\"hidden\" name=\"ajaxAction\" value=\"add\">\n";
            $add_html .= "<input type=\"hidden\" name=\"ajaxCrudAction\" value=\"add\">\n";
            $add_html .= "<input type=\"hidden\" name=\"table\" value=\"$this->db_table\">\n";
            $add_html .= "<input type=\"hidden\" name=\"amg_add_param_keys\" value=\"$amgAddData\">\n";



            if (isset($file_uploads) && $file_uploads){
                $add_html .= "<input type=\"hidden\" name=\"uploads_on\" value=\"true\">\n";
            }

            $add_html .= "</form>\n";

        }//if adding fields is "allowed"

        /*
        THIS IS IMPORTANT
        for ajax retrieval (see top of page)
        */
        //$_SESSION["amg_html_table_data"] = $table_html;

        //$bottom_html .= '<script type="text/javascript">';
        //$bottom_html .= '$(".amg_crud_required").prop(\'required\',true);';
        //$bottom_html .= '</script>';

        $html = $top_html . $table_html . $bottom_html . $add_html;
        if ($this->add_form_top){
        	$html = $add_html . $top_html . $table_html . $bottom_html;
        }

        echo $html;


	}

	function getFields($table){
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

    function getFieldDataType($field_name){
        return isset($this->field_datatype[$field_name]);
    }

    function fieldIsInt($datatype){
        if (stristr($datatype, "int") !== FALSE){
            return true;
        }
        return  false;
    }

    function fieldIsDecimal($datatype){
        if (stristr($datatype, "decimal") !== FALSE || stristr($datatype, "double") !== FALSE){
            return true;
        }
        return  false;

    }

    function fieldIsEnum($datatype){
        if (stristr($datatype, "enum") !== FALSE){
            return true;
        }
        return  false;
    }

	function fieldIsDate($datatype){
		if (stristr($datatype, "date") !== FALSE){
			return true;
		}
		return  false;
	}

    function getEnumArray($datatype){
        $enum = substr($datatype, 5);
        $enum = substr($enum, 0, (strlen($enum) - 1));
        $enum = str_replace("'", "", $enum);
        $enum = str_replace('"', "", $enum);
        $enum_array = explode(",", $enum);

        return ($enum_array);
    }


    function fieldInArray($field, $the_array){

        //try to find index for arrays with array[key] = field_name
        $found_index = array_search($field, $the_array);
        if ($found_index !== FALSE){
            return true;
        }

        //for arrays with array[0] = field_name and array[1] = value
        foreach ($the_array as $the_array_values){
            if(isset($the_array_values[0])){
                $field_name = $the_array_values[0];
                if ($field_name == $field){
                    return true;
                }
            }

        }

        return false;
    }

	function makeAjaxEditor($unique_id, $field_name, $field_value, $type = 'textarea', $fieldSize = "", $field_text = "", $onKeyPress_function = ""){

        $prefield = trim($this->db_table . $field_name . $unique_id);

		$input_name = $type . "_" . $prefield;

        $return_html = "";

		if ($field_text == "") $field_text = $field_value;

		$cell_data = "";
		if (isset($this->format_field_with_function[$field_name]) && $this->format_field_with_function[$field_name] != ''){
			$cell_data = call_user_func($this->format_field_with_function[$field_name], $field_value);
		}
		if (isset($this->format_field_with_function_adv[$field_name]) && $this->format_field_with_function_adv[$field_name] != ''){
			$cell_data = call_user_func($this->format_field_with_function_adv[$field_name], $field_value, $unique_id);
		}

		if ( (strip_tags($cell_data) == "") && $field_value == "") $field_text = "--";

		//for getting rid of the html space, replace with actual no text
		if ($field_value == "&nbsp;&nbsp;") $field_value = "";

		$field_value = stripslashes(htmlspecialchars($field_value));

		$postEditForm = false; //default action is for form NOT to be submitted but processed through ajax
		if (isset($this->onUpdateExecuteCallBackFunction[$field_name]) && $this->onUpdateExecuteCallBackFunction[$field_name] != ''){
			$postEditForm = true; //a callback function is specified for this field; as such it cannot be edited with ajax but must be posted
		}

        $return_html .= "<span class=\"editable hand_cursor\" id=\"" . $prefield ."_show\" onClick=\"
			document.getElementById('" . $prefield . "_edit').style.display = '';
			document.getElementById('" . $prefield . "_show').style.display = 'none';
			document.getElementById('" . $input_name . "').focus();
            \">" . stripslashes($field_text) . "</span>
        <span id=\"" . $prefield ."_edit\" style=\"display: none;\">
            <form style=\"display: inline;\" name=\"form_" . $prefield . "\" id=\"form_" . $prefield . "\" method=\"POST\" onsubmit=\"";

			if ($postEditForm){
				$return_html .= "return true;\">";
			}
			else{
				$return_html .= "
					document.getElementById('" . $prefield . "_edit').style.display='none';
					document.getElementById('" . $prefield . "_save').style.display='';
					var sndValue = document.getElementById('" . $input_name . "').value;
					sndValue = cleanseStrForURIEncode(sndValue);
					var req = '" . $this->ajax_file . "&ajaxAction=update&id=" . $unique_id . "&field=" . $field_name . "&table=" . $this->db_table . "&pk=" . $this->db_table_pk . "&val=' + sndValue;
					sndUpdateReq(req);
					return false;
				\">";
			}

            if ($type == 'text'){
                $customClass = @$this->display_field_with_class_style[$field_name];
                if ($fieldSize == "") $fieldSize = 15;

				if (isset($this->display_field_with_class_style[$field_name]) && $this->display_field_with_class_style[$field_name] != '') {

					$return_html .= "<input ONKEYPRESS=\"$onKeyPress_function\" id=\"$input_name\" name=\"$input_name\" type=\"text\" class=\"editingSize editMode $customClass\" size=\"$fieldSize\" value=\"$field_value\"/>\n";
				}
				else {
					$return_html .= "<input ONKEYPRESS=\"$onKeyPress_function\" id=\"$input_name\" name=\"$input_name\" type=\"text\" class=\"editingSize editMode $customClass\" size=\"$fieldSize\" value=\"$field_value\"/>\n";
				}
			}
			else{
                if ($fieldSize == "") $fieldSize = 80;
                $return_html .= "<textarea ONKEYPRESS=\"$onKeyPress_function\" id=\"$input_name\" name=\"textarea_$prefield\" class=\"editingSize editMode\" style=\"width: 100%; height: " . $fieldSize . "px;\">$field_value</textarea>\n";
                $return_html .= "<br /><input type=\"submit\" class=\"btn editingSize\" value=\"Ok\">\n";
			}

        $return_html .= "
			<input type=\"hidden\" name=\"id\" value=\"$unique_id\">
			<input type=\"hidden\" name=\"field_name\" value=\"$field_name\">
			<input type=\"hidden\" name=\"table\" value=\"$this->db_table\">
			<input type=\"hidden\" name=\"paramName\" value=\"$input_name\">
			<input type=\"hidden\" name=\"action\" value=\"update\">
			<input type=\"button\" class=\"btn editingSize\" value=\"Cancel\" onClick=\"
				document.getElementById('" . $prefield . "_show').style.display = '';
				document.getElementById('" . $prefield . "_edit').style.display = 'none';
			\"/>
			</form>
		</span>
        <span style=\"display: none;\" id=\"" . $prefield . "_save\" class=\"savingAjaxWithBackground\">Saving...</span>";

        return $return_html;

	}//makeAjaxEditor



    function makeAjaxDropdown($unique_id, $field_name, $field_value, $dropdown_table, $dropdown_table_pk, $array_list, $selected_dropdown_text = "NOTHING_ENTERED"){
        $return_html = "";

        $prefield = trim($this->db_table . $field_name . $unique_id);
        $input_name = "dropdown" . "_" . $prefield;

		$cell_data = $field_value;
		if (isset($this->format_field_with_function[$field_name]) && $this->format_field_with_function[$field_name] != ''){
			$cell_data = call_user_func($this->format_field_with_function[$field_name], $field_value);
		}
		if (isset($this->format_field_with_function_adv[$field_name]) && $this->format_field_with_function_adv[$field_name] != ''){
			$cell_data = call_user_func($this->format_field_with_function_adv[$field_name], $field_value, $unique_id);
		}

        if ($selected_dropdown_text == "NOTHING_ENTERED"){

            $selected_dropdown_text = $cell_data;

            foreach ($array_list as $list){
                if (is_array($list)){
                    $list_val = $list[0];
                    $list_option = $list[1];
                }
                else{
                    $list_val = $list;
                    $list_option = $list;
                }

                //if ($list_val == $field_value) $selected_dropdown_text = $list_option;
            }
        }

        $no_text = false;
        if ($selected_dropdown_text == '' || $selected_dropdown_text == '&nbsp;&nbsp;'){
            $no_text = true;
            $selected_dropdown_text = "&nbsp;--&nbsp;";
        }

		$postEditForm = false; //default action is for form NOT to be submitted but processed through ajax
		if (isset($this->onUpdateExecuteCallBackFunction[$field_name]) && $this->onUpdateExecuteCallBackFunction[$field_name] != ''){
			$postEditForm = true; //a callback function is specified for this field; as such it cannot be edited with ajax but must be posted
		}

        $return_html = "<span class=\"editable hand_cursor\" id=\"" . $prefield . "_show\" onClick=\"
			document.getElementById('" . $prefield . "_edit').style.display = '';
			document.getElementById('" . $prefield . "_show').style.display = 'none';
			\">" . $selected_dropdown_text . "</span>

            <span style=\"display: none;\" id=\"" . $prefield . "_edit\">
                <form style=\"display: inline;\" method=\"POST\" name=\"form_" . $prefield . "\" id=\"form_" . $prefield . "\">
                <select class=\"editingSize editMode\" name=\"$input_name\" id=\"$input_name\" onChange=\"";

			if ($postEditForm){
				$return_html .= "this.form.submit();\">";
			}
			else{
				$return_html .= "
					var selected_index_value = document.getElementById('" . $input_name . "').value;
					document.getElementById('" . $prefield . "_edit').style.display='none';
					document.getElementById('" . $prefield . "_save').style.display='';
					var req = '" . $this->ajax_file . "&ajaxAction=update&id=" . $unique_id . "&field=" . $field_name . "&table=" . $this->db_table . "&pk=" . $this->db_table_pk . "&dropdown_tbl=" . $dropdown_table . "&val=' + selected_index_value;
					sndUpdateReq(req);
					return false;
				\">";
			}

		if ($no_text || (isset($this->category_required[$field_name]) && $this->category_required[$field_name] != TRUE)){
			if ($this->fieldIsInt($this->getFieldDataType($field_name)) || $this->fieldIsDecimal($this->getFieldDataType($field_name))){
				$return_html .= "<option value='0'>--Select--</option>\n";
			}
			else{
				$return_html .= "<option value=''>--Select--</option>\n";
			}
		}

		foreach($array_list as $list){
			$selected = '';
			if (is_array($list)){
				$list_val = $list[0];
				$list_option = $list[1];
			}
			else{
				$list_val = $list;
				$list_option = $list;
			}

			if ($list_val == $field_value) $selected = " selected";
			$return_html .= "<option value=\"$list_val\" $selected >$list_option</option>";
		}
		$return_html .= "</select>";

		$return_html .= "<input type=\"hidden\" name=\"id\" value=\"$unique_id\">
			<input type=\"hidden\" name=\"field_name\" value=\"$field_name\">
			<input type=\"hidden\" name=\"table\" value=\"$this->db_table\">
			<input type=\"hidden\" name=\"paramName\" value=\"$input_name\">
			<input type=\"hidden\" name=\"action\" value=\"update\">
			<input type=\"button\" class=\"editingSize\" value=\"Cancel\" onClick=\"
				document.getElementById('" . $prefield . "_show').style.display = '';
				document.getElementById('" . $prefield . "_edit').style.display = 'none';
			\"/>

			</form>
			</span>
	        <span style=\"display: none;\" id=\"" . $prefield . "_save\" class=\"savingAjaxWithBackground\">Saving...</span>\n";

        return $return_html;

	}//makeAjaxDropdown


	function makeAjaxCheckbox($unique_id, $field_name, $field_value){
		$prefield = trim($this->db_table) . trim($field_name) . trim($unique_id);

        $return_html = "";

		$values = $this->checkbox[$field_name];
		$value_on = $values[0];
		$value_off = $values[1];

		$checked = '';
		if ($field_value == $value_on) $checked = "checked";

		$show_value = '';
		if ($checked == '') {
			$show_value = $value_off;
		} else {
			$show_value = $value_on;
		}

		//strip quotes
		$value_on = str_replace('"', "'", $value_on);
		$value_off = str_replace('"', "'", $value_off);

		$checkboxValue = 0;
		if (isset($this->checkboxall[$field_name])){
			$checkboxValue = (int)$this->checkboxall[$field_name];
		}

        $return_html .= "<input type=\"checkbox\" $checked name=\"$field_name" . "_fieldckbox\" id=\"$field_name$unique_id\" onClick=\"
			var " . $prefield . "_value = '';

			if (this.checked){
				" . $prefield . "_value = '$value_on';
				if (" . $checkboxValue . ") {
					document.getElementById('$field_name$unique_id" . "_label').innerHTML = '$value_on';
				}
			}
			else{
				". $prefield . "_value = '$value_off';
				if (" . $checkboxValue . ") {
					document.getElementById('$field_name$unique_id" . "_label').innerHTML = '$value_off';
				}
			}
			var req = '" . $this->ajax_file . "&ajaxAction=update&id=$unique_id&field=$field_name&table=$this->db_table&pk=$this->db_table_pk&val=' + " . $prefield . "_value;

			sndReqNoResponseChk(req);
		\">";

		if (isset($this->checkboxall[$field_name]) && $this->checkboxall[$field_name] == true) {
			$return_html .= "<label for=\"$field_name$unique_id\" id=\"" . $field_name . $unique_id . "_label\">$show_value</label>";
		}

        return $return_html;

	}//makeAjaxCheckbox

    function showUploadForm($field_name, $upload_folder, $row_id){
        $return_html = "";

        $return_html .= "<form action=\"\" class='file_upload' name=\"Uploader\" method=\"POST\" ENCTYPE=\"multipart/form-data\">\n";
        $return_html .=  "  <input type=\"file\" size=\"10\" name=\"$field_name\" />\n";
        $return_html .= "  <input type=\"hidden\" name=\"upload_folder\" value=\"$upload_folder\" />\n";
        $return_html .= "  <input type=\"hidden\" name=\"field_name\" value=\"$field_name\" />\n";
        $return_html .= "  <input type=\"hidden\" name=\"id\" value=\"$row_id\" />\n";
        $return_html .= "  <input type=\"hidden\" name=\"ajaxCrudactionUpload\" value=\"upload\" />\n";
        $return_html .= "  <input type=\"submit\" name=\"submit\" value=\"Upload\" />\n";
        $return_html .= "</form>\n";

        return $return_html;
    }

	function getThisPage(){
		if (stristr($_SERVER['REQUEST_URI'], "?")){
			return $_SERVER['REQUEST_URI'] . "&";
		}
		return $_SERVER['REQUEST_URI'] . "?";
	}



}//class


# In an effect to make ajaxCRUD thin we are attaching this (paging) class and a few functions all together

class paging{

	var $pRecordCount;
	var $pStartFile;
	var $pRowsPerPage;
	var $pRecord;
	var $pCounter;
	var $pPageID;
	var $pShowLinkNotice;
	var $tableName;

	function processPaging($rowsPerPage,$pageID){
       $record = $this->pRecordCount;
       if($record >=$rowsPerPage)
            $record=ceil($record/$rowsPerPage);
       else
            $record=1;
        if(empty($pageID) or $pageID==1){
            $pageID=1;
            $startFile=0;
        }
        if($pageID>1)
            $startFile=($pageID-1)*$rowsPerPage;

        $this->pStartFile   = $startFile;
        $this->pRowsPerPage = $rowsPerPage;
        $this->pRecord      = $record;
        $this->pPageID      = $pageID;

        return $record;
	}
	function myRecordCount($query){
		global $mysqliConn;


			$rs      			= $mysqliConn->query($query) or die("Database Error <br>".$query);
			$rsCount 			= mysqli_num_rows($rs);

		$this->pRecordCount = $rsCount;
		unset($rs);
		return $rsCount;
	}

	function startPaging($query){
		$query    = $query." LIMIT ".$this->pStartFile.",".$this->pRowsPerPage;
		$rs = q($query);
		return $rs;
	}

	function pageLinks($url){
        $cssclass = "paging_links";
		$this->pShowLinkNotice = "&nbsp;";
        $totalpages = ceil($this->pRecordCount / $this->pRowsPerPage);
        $currentpage = $this->pPageID;


        $min = max($currentpage - 5, 1); // there are no pages < 1
        $max = min($currentpage + 5, $totalpages); // and no pages > total_pages

        $link = '<div class="text-center"><div class="pagination"><ul>';
		if($this->pRecordCount>$this->pRowsPerPage){
			$this->pShowLinkNotice = "Page ".$this->pPageID. " of ".$this->pRecord;
			//Previous link
			//$link = "";
			if($this->pPageID !== 1){
                $prevPage = $this->pPageID - 1;
                $link .= "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=1") . "\" class=\"$cssclass\">|<<</a></li> ";
                $link .= "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$prevPage") ."\" class=\"$cssclass\"><<</a></li>";
			}


            for($ctr = $min; $ctr <= $max; ++$ctr) {




				if($this->pPageID==$ctr)
                $link .=  "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$ctr") . "\" class=\"$cssclass\"><b>$ctr</b></a></li>";
				else
                $link .= "  <li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$ctr") . "\" class=\"$cssclass\">$ctr</a></li>";
			}
			//Previous Next link
			if($this->pPageID<($ctr-1)){
                $nextPage = $this->pPageID + 1;
                $link .= "<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=$nextPage") . "\" class=\"$cssclass\">>></a></li>";
                $link .="<li><a href=\"javascript:;\" onClick=\"" . $this->getOnClick("&pid=".$this->pRecord) . "\" class=\"$cssclass\">>>|</a></li>";
			}
            $link .= '</ul></div></div>';
			return $link;
		}
	}

	function getOnClick($paging_query_string){
		global $db_table;
		//if any hardcoding is needed...(advanced feature for special needs)
		$extra_query_params = "";
		//$extra_query_params = "&Dealer=" . htmlentities($_REQUEST['Dealer']);
		return "pageTable('" . $extra_query_params . "$paging_query_string', '$this->tableName');";
	}

}




