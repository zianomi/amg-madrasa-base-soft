<?php
$amgAddParamKeys = unserialize(urldecode($_REQUEST['amg_add_param_keys']));
$table = $amgAddParamKeys['table'];
$db_table_pk = $amgAddParamKeys['db_table_pk'];
$amgFields = $amgAddParamKeys['fields'];
$amgAddValues = $amgAddParamKeys['add_values'];
$amgFileUploads = $amgAddParamKeys['file_uploads'];
$onAddSpecifyPrimaryKey = $amgAddParamKeys['on_add_specify_primary_key'];
$primaryKeyAutoIncrement = $amgAddParamKeys['primaryKeyAutoIncrement'];
$onAddExecuteCallBackFunction = $amgAddParamKeys['onAddExecuteCallBackFunction'];
$fileUploadInfo = $amgAddParamKeys['file_upload_info'];
$amgAddParams = $amgAddParamKeys['amg_add_params'];

$uploads_on = false;

if (isset($_REQUEST['uploads_on'])) {
    $uploads_on = $_REQUEST['uploads_on'];
    if ($uploads_on == 'true' && $_FILES) {
        $uploads_on = true;
    }
}


if ($uploads_on) {
    foreach ($amgFileUploads as $field_name) {
        if (isset($fileUploadInfo[$field_name]['amg_path']['required'])) {
            $imageRequiredNot = $fileUploadInfo[$field_name]['amg_path']['required'];
            $amgFileWithPath = $fileUploadInfo[$field_name]['destination_folder'];

            if ($imageRequiredNot == "true") {
                if (empty($_FILES[$field_name]['name'])) {
                    $response = array("status" => "false", "msg" => $field_name . " file required");
                    echo json_encode($response);
                    exit;
                }
            }
        }

    }
}

$ajaxCRUD = new ajaxCRUD();
$ajaxCRUD->setDbTable($table);
$ajaxCRUD->setDbTablePk($db_table_pk);
$ajaxCRUD->file_upload_info = $fileUploadInfo;

global $tool;

$submitted_values = array();
$submitted_array = array();
$nullFieldArr = array();
$nullFieldFound = array();
//this new row has (a) file(s) coming with it


$nullFieldInput = $_REQUEST['amg_hidden'];
if(!empty($nullFieldInput)){
    $nullFieldArr = explode(",",$nullFieldInput);
}


foreach ($nullFieldArr as $nullFieldKey){

    $nullFieldFound[] = array_search($nullFieldKey, $amgFields);
}

foreach ($nullFieldFound as $nullFieldFoundKeys){
    if (is_numeric($nullFieldFoundKeys)) {
        unset($amgFields[$nullFieldFoundKeys]);
    }
}


$arrayKeyForFoundPK = array_search($db_table_pk, $amgFields);
foreach ($amgFields as $field) {


    if (isset($amgAddParams[$field])) {

        $amgFieldParamArr = $amgAddParams[$field];


        if (isset($amgFieldParamArr['required'])) {
            if (empty($_POST[$field])) {
                $response = array("status" => "false", "msg" => $field . " required");
                echo json_encode($response);
                exit;
            }
        }

        if (isset($amgFieldParamArr['methodApply'])) {
            $methodApply = $amgFieldParamArr['methodApply'];

            if ($methodApply == "date") {
                $date = $tool->ChangeDateFormat($_POST[$field]);
                $_REQUEST[$field] = $date;

                if (!$tool->checkDateFormat($date)) {
                    $response = array("status" => "false", "msg" => " date invalid");
                    echo json_encode($response);
                    exit;
                }
            }

            if ($methodApply == "hijridate") {
                $date = $tool->ChangeDateFormat($_POST[$field]);
                $_REQUEST[$field] = $date;
            }

            if ($methodApply == "slug") {
                Tools::getLib("Utf8StringHandling");
                $utf8 = new Utf8StringHandling();
                $_REQUEST[$field] = $utf8->create_slug($_REQUEST[$field]);
                unset($utf8);
            }
        }


    }

    $submitted_value_cleansed = "";
    if (@$_REQUEST[$field] == '') {
        if ($ajaxCRUD->fieldIsInt($ajaxCRUD->getFieldDataType($field)) || $ajaxCRUD->fieldIsDecimal($ajaxCRUD->getFieldDataType($field))) {
            $submitted_value_cleansed = 0;
        }
    } else {
        $submitted_value_cleansed = $_REQUEST[$field];
    }

    if ($uploads_on) {
        if ($ajaxCRUD->fieldInArray($field, $amgFileUploads)) {
            $submitted_value_cleansed = $_FILES[$field]["name"];
        }
    }

    $submitted_values[] = $submitted_value_cleansed;
    //also used for callback function
    $submitted_array[$field] = $submitted_value_cleansed;
}


//for adding values to the row which were not in the ADD row table - but are specified by ADD on INSERT
if (count($amgAddValues) > 0) {
    foreach ($amgAddValues as $add_value) {
        $field_name = $add_value[0];
        $the_add_value = $add_value[1];

        if ($submitted_array[$field_name] == '') {
            $submitted_array[$field_name] = $the_add_value;
        }


        //reshuffle numeric indexed array
        unset($submitted_values);
        $submitted_values = array();
        foreach ($submitted_array as $field) {
            //$field = str_replace('"', "'", $field);
            //$field = str_replace('\\', "/", $field);
            $field = addslashes($field);
            $submitted_values[] = $field;
        }

    }//foreach
}//if count add_values > 0

//get rid of the primary key in the fields column
if (!$onAddSpecifyPrimaryKey) {
    if (is_numeric($arrayKeyForFoundPK)) {
        unset($submitted_values[$arrayKeyForFoundPK]);
    }
}

//wrap each field in quotes
$string_submitted_values = "\"" . implode("\",\"", $submitted_values) . "\"";

//for getting datestamp of new row for mysql's "NOW" to work
$string_submitted_values = str_replace('"NOW()"', 'NOW()', $string_submitted_values);

if ($string_submitted_values != '') {
    if (!$onAddSpecifyPrimaryKey && $primaryKeyAutoIncrement) {
        //don't allow the primary key to be inputted
        $fields_array_without_pk = $amgFields;

        if (is_numeric($arrayKeyForFoundPK)) {
            unset($fields_array_without_pk[$arrayKeyForFoundPK]);
        }

        $string_fields_without_pk = implode(",", $fields_array_without_pk);

        $query = "INSERT INTO $table($string_fields_without_pk) VALUES ($string_submitted_values)";
    } else {
        if (!$primaryKeyAutoIncrement) {
            $primary_key_value = q1("SELECT MAX($db_table_pk) FROM $table");
            if ($primary_key_value > 0) {
                $primary_key_value++;
            }
            $primary_key_value = $primary_key_value . ", ";
        }

        $string_fields_with_pk = implode(",", $amgFields);
        $query = "INSERT INTO $table($string_fields_with_pk) VALUES ($primary_key_value $string_submitted_values)";
    }
    $success = qr($query);

    if ($success) {
        global $mysqliConn;

        $insert_id = $mysqliConn->insert_id;

        $response = array("status" => "true", "msg" => "Record Added");


        if ($uploads_on) {
            foreach ($amgFileUploads as $field_name) {
                $file_dest = $fileUploadInfo[$field_name]['destination_folder'];

                $allowedExts = "";
                if (isset($fileUploadInfo[$field_name]['permittedFileExts'])) {
                    $allowedExts = $fileUploadInfo[$field_name]['permittedFileExts'];
                }

                if ($_FILES[$field_name]['name'] != '') {
                    $ajaxCRUD->uploadFile($insert_id, $field_name, $file_dest, $allowedExts);
                }
            }
        }

        if ($onAddExecuteCallBackFunction != '') {
            $submitted_array['id'] = $insert_id;
            $submitted_array[$db_table_pk] = $insert_id;
            call_user_func($onAddExecuteCallBackFunction, $submitted_array);
        }
        $_SESSION['msg'] = $tool->Message("succ","Record Added");
        echo json_encode($response);
        exit;

    } else {
        $response = array("status" => "false", "msg" => $mysqliConn->error);
        echo json_encode($response);
        exit;
        //$error_msg[] = "$item could not be added. Please try again.";
    }
} else {
    //$error_msg[] = "All fields were omitted.";
    $response = array("status" => "false", "msg" => "All fields were omitted.");
    echo json_encode($response);
    exit;
}

