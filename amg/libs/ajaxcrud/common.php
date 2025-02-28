<?php
define('EXECUTING_SCRIPT', "");
global $tool;

if(isset($_REQUEST['amgaction'])){
    if($_REQUEST['amgaction'] == "delete_file"){

        $action = $_REQUEST['amgaction'];
        $paramPassed = unserialize(urldecode($_REQUEST['param']));
        $delDataArr = array();
        foreach ($paramPassed as $paramPass){
            $delDataArr[] = $paramPass;

        }
        $amgSpace = new AmgSpace();
        $file_uploads = $paramPassed['file_uploads'];
        $file_upload_info = $paramPassed['file_upload_info'];
        $table = $paramPassed['table'];
        //$table_pk = $paramPassed['table_pk'];
        $table_pk = $delDataArr[3];



        if ($action == 'delete_file' && $_REQUEST['field_name'] && $_REQUEST['id'] != ''){
            $delete_id      = $_REQUEST['id'];
            $file_field     = $_REQUEST['field_name'];
            //$upload_folder  = $_REQUEST['upload_folder'];
            $filename = q1("SELECT $file_field FROM $table WHERE $table_pk = $delete_id");
            //$file_dest  = $file_upload_info[$file_field]['destination_folder'];




            //$fileNameArr = explode("___",$filename);

            //@$amgFileToDelete = UPL . DRS . $fileNameArr[0] . DRS . $fileNameArr[1] . DRS . $filename;


            //2017___06___9144_logo_badge.png

            $success = qr("UPDATE $table SET $file_field = \"\" WHERE $table_pk = $delete_id");

            if ($success){
                //$file_dest  = $file_upload_info[$file_field]['destination_folder'];

                $spaceObj = $amgSpace->getSpaceObj();

                $spaceObj->DeleteObject($amgSpace->getFileUrl($filename));
                //@unlink($amgSpace->getFileUrl($filename));
                //$report_msg[] = "File Deleted Sucessfully.";

                /*if ($this->onDeleteFileExecuteCallBackFunction != ''){
                    $delete_file_array = array();
                    $delete_file_array[id]        = $delete_id;
                    $delete_file_array[field]     = $file_field;
                    call_user_func($this->onDeleteFileExecuteCallBackFunction, $delete_file_array);
                }*/

                echo json_encode(array("status"=>"true","msg"=>"File Deleted Sucessfully."));
                exit;

            }
            else{
                echo json_encode(array("status"=>"false","msg" => "There was an error deleting your file."));
                exit;
                //$error_msg[] = "There was an error deleting your file.";
            }

        }//action = delete_file

    }
}


if(isset($_REQUEST['amgTextboxAjaxEdit'])){

        if(!empty($_REQUEST['amgTextboxAjaxEdit']) && is_numeric($_REQUEST['amgTextboxAjaxEdit'])){

            $pk = $_REQUEST['pk'];
            $value = $_REQUEST['value'];
            $id = "";
            $table = "";
            $field = "";
            $type = "";
            $required = "";
            $methodApply = "";
            $pkcol = "";

            if(isset($pk['id'])){
                $id = intval($pk['id']);
            }

            if(isset($pk['table'])){
                $table = $pk['table'];
            }

            if(isset($pk['field'])){
                $field = $pk['field'];
            }


            if(isset($pk['type'])){
                $type = $pk['type'];
            }

            if(isset($pk['required'])){
                $required = $pk['required'];
            }

            if(isset($pk['methodApply'])){
                $methodApply = $pk['methodApply'];
            }

            if(isset($pk['pkcol'])){
                $pkcol = $pk['pkcol'];
            }

            if(empty($type)){
                $type = "text";
            }

            if(empty($required)){
                $required = "true";
            }



            if(empty($id)){
                xeditableError("Error in data.");
                exit;
            }

            if(empty($table)){
                xeditableError("Error in data.");
                exit;
            }

            if(empty($field)){
                xeditableError("Error in data.");
                exit;
            }

            if(empty($pkcol)){
                xeditableError("Error in data.");
                exit;
            }




            if($required == "true"){
                if(empty($_REQUEST['value'])){
                    xeditableError($field . " required");
                }
            }



            if($type == "date" || $methodApply == "date"){

                if(!$tool->checkDateFormat($value)){
                    xeditableError("invalid date");
                    exit;
                }
            }


            if(!empty($value)){
                $val = addslashes($value);
            }

            $pkID = $id;

            $field_name = $field;


            $amgDateTimeNow = date("Y-m-d H:i:s");

            $dataAllsArr = amgQ("SELECT * FROM `$table` LIMIT 1");
            $dataAlls = array();

            if(!empty($dataAllsArr)){
                $dataAlls = $dataAllsArr[0];
            }


            $amgUpdateQuery = "UPDATE $table SET $field_name  = '$val'";

            if(array_key_exists('updated_user_id',$dataAlls)){
                $amgUpdateQuery .= ", updated_user_id = " . Tools::getUserId();
            }

            if(array_key_exists('updated',$dataAlls)){
                $amgUpdateQuery .= ", updated = '$amgDateTimeNow'";
            }


            $amgUpdateQuery .= " WHERE $pkcol = $pkID";



            $success = qr($amgUpdateQuery);

            if ($success){
                echo $val;
                exit;

                /*if ($this->onUpdateExecuteCallBackFunction[$field_name] != ''){
                    $updatedRowArray = qr("SELECT * FROM $field_name WHERE $pkcol = $pkID");
                    $callBackSuccess = call_user_func($this->onUpdateExecuteCallBackFunction[$field_name], $updatedRowArray);
                    if (!$callBackSuccess){
                    }
                }*/

            }
            else{
                xeditableError("Field updation failed");
            }

            exit;
        }
        else{
            header('HTTP/1.0 400 Bad Request', true, 400);
            echo "Error in request";
            exit;
        }

        exit;
    }





if(isset($_REQUEST['amgSelectGetData'])){
        if(!empty($_REQUEST['amgSelectGetData']) && is_numeric($_REQUEST['amgSelectGetData'])){

            /*$contract_types = array();
            $contract_types[] = array('value' => 'M', 'text' => 'male');
            $contract_types[] = array('value' => 'F', 'text' => 'female');

            echo json_encode($contract_types);*/

            $table = $_GET['amgtable'];
            $col = $_GET['amgtablecol'];
            $id = $_GET['amgtableidCol'];
            $where = $_GET['amgwhere'];

            $sql = "SELECT $id, $col FROM $table WHERE 1 $where";

            //echo $sql;

            $res = amgQ($sql);
            $data = array();
            foreach ($res as $row){
                $data[] = array("value" => $row[$id], "text" => $row[$col]);
            }


            echo json_encode($data);

            exit;
        }
        else{
            header('HTTP/1.0 400 Bad Request', true, 400);
            echo "Error in request";
        }

        exit;
    }



if(isset($_REQUEST['ajaxCrudAction'])){
    if(!empty($_REQUEST['ajaxCrudAction'])){
        if($_REQUEST['ajaxCrudAction'] == "add"){
            require ajaxcrudextra . "add.php";
            exit;

        }
    }
}

if (isset($_REQUEST['ajaxAction'])){
		$ajaxAction = $_REQUEST['ajaxAction'];

		if ($ajaxAction != ""){



			# these lines make sure caching do not cause ajax saving/displaying issues
			header("Cache-Control: no-cache, must-revalidate"); //this is why ajaxCRUD.php must be before any other headers (html) are outputted
			# a date in the past
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

			$table		= isset($_REQUEST["table"]) ? $_REQUEST["table"] : "";
			$pk			= isset($_REQUEST["pk"]) ? trim($_REQUEST["pk"]) : "";
			$field		= isset($_REQUEST["field"]) ? trim($_REQUEST["field"]) : "";
			$id 		= isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
			$val		= isset($_REQUEST["val"]) ? $_REQUEST["val"] : "";
			$table_num	= isset($_REQUEST["table_num"]) ? $_REQUEST["table_num"] : "";



			if (!is_numeric($id)){
				$sql_id = "\"$id\"";
			}
			else{
				$sql_id = $id;
			}





			if ($ajaxAction == 'update'){

				$val = addslashes($val);
				//check to see if  record exists
				$row_current_value = q1("SELECT $pk FROM $table WHERE $pk = $sql_id");
				if ($row_current_value  == ''){

					//qr("INSERT INTO $table ($pk) VALUES (\"$id\")");
				}

				$success = qr("UPDATE $table SET $field = \"$val\" WHERE $pk = $sql_id");

				if ($val == '') $val = "&nbsp;&nbsp;";

				//when updating, we use the Table name, Field name, & the Primary Key (id) to feed back to client-side-processing
				$prefield = trim($table . $field . $id);

				if (isset($_REQUEST['dropdown_tbl'])){
					$val = "{selectbox}";
				}

				if ($success){
					echo $prefield . "|" . stripslashes($val);
				}
				else{
					echo "error|" . $prefield . "|" . stripslashes($val);
				}
			}

			if ($ajaxAction == 'delete'){
				qr("DELETE FROM $table WHERE $pk = $sql_id");
				echo $table . "|" . $id;
			}

			exit();
		}
	}



if(isset($_REQUEST['ajaxCrudactionUpload'])){
    $ajaxCRUD = new ajaxCRUD();
    //$ajaxCRUD->setDbTable($table);
    //$ajaxCRUD->setDbTablePk($db_table_pk);
    //$ajaxCRUD->file_upload_info = $fileUploadInfo;
    $params = unserialize(urldecode($_REQUEST['upload_params']));

    $fileFieldName = "";

    if(isset($params['file_uploads'])){
        foreach ($params['file_uploads'] as $paramKey => $paramVal){
            if(isset($_FILES[$paramVal])){
                //$fileFieldName = @$params['file_uploads'][$paramVal];
                $fileFieldName = @$_FILES[$paramVal]['name'];
            }
        }
    }


    @$fileRequiredNull = $params['file_upload_info'][$fileFieldName]['amg_path']['required'];
    //@$fileSizeIfImage = $params['file_upload_info'][$fileFieldName]['amg_path']['path'];

    //echo '<pre>'; print_r($params['file_upload_info'][$fileFieldName]['amg_path']); echo '</pre>';
    //die('CALL');
    if($fileRequiredNull == "true"){
        if(empty($_FILES[$fileFieldName]['name'])){
            echo json_encode(array("status" => "false","msg"=>"File required."));
            exit;
        }
    }



    $table = $params['table'];
    $table_pk = $params['table_pk'];
    $ajaxCRUD->setDbTable($table);
    $ajaxCRUD->setDbTablePk($table_pk);
    $ajaxCRUD->file_uploads = $params['file_uploads'];
    $ajaxCRUD->file_upload_info = $params['file_upload_info'];


    $ajaxCRUD->doAction($_REQUEST['ajaxCrudactionUpload']);
    unset($ajaxCRUD);
}


if (isset($_REQUEST['customAction'])){
    $customAction = $_REQUEST['customAction'];
    if ($customAction != ""){
        if ($customAction == 'exportToCSV'){
            //$csvData = strip_tags($_REQUEST['tableData']);
            //$csvData = strip_tags($_REQUEST['tableData']);
            $ajaxCrud = new ajaxCRUD();
            $csvData = $ajaxCrud->createCSVOutput($_REQUEST['amgCsvParam']);
            $fileName = $_REQUEST['fileName'] .time() . '.csv';
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=$fileName");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo "\xEF\xBB\xBF";
            echo str_replace('\"','"',$csvData);
            unset($ajaxCrud);
        }
        exit();
    }
}


function xeditableError($msg){
    header('HTTP/1.0 400 Bad Request', true, 400);
    echo $msg;
    exit;
}

function changeDateFormat($date){
    global $tool;
    return $tool->ChangeDateFormat($date);
}




function make_filename_safe($filename) {
      $normalizeChars = array(
         '�' => 'S',
         '�' => 's',
         '�' => 'Dj',
         '�' => 'Z',
         '�' => 'z',
         '�' => 'A',
         '�' => 'A',
         '�' => 'A',
         '�' => 'A',
         '�' => 'A',
         '�' => 'A',
         '�' => 'A',
         '�' => 'C',
         '�' => 'E',
         '�' => 'E',
         '�' => 'E',
         '�' => 'E',
         '�' => 'I',
         '�' => 'I',
         '�' => 'I',
         '�' => 'I',
         '�' => 'N',
         '�' => 'O',
         '�' => 'O',
         '�' => 'O',
         '�' => 'O',
         '�' => 'O',
         '�' => 'O',
         '�' => 'U',
         '�' => 'U',
         '�' => 'U',
         '�' => 'U',
         '�' => 'Y',
         '�' => 'B',
         '�' => 'Ss',
         '�' => 'a',
         '�' => 'a',
         '�' => 'a',
         '�' => 'a',
         '�' => 'a',
         '�' => 'a',
         '�' => 'a',
         '�' => 'c',
         '�' => 'e',
         '�' => 'e',
         '�' => 'e',
         '�' => 'e',
         '�' => 'i',
         '�' => 'i',
         '�' => 'i',
         '�' => 'i',
         '�' => 'o',
         '�' => 'n',
         '�' => 'o',
         '�' => 'o',
         '�' => 'o',
         '�' => 'o',
         '�' => 'o',
         '�' => 'o',
         '�' => 'u',
         '�' => 'u',
         '�' => 'u',
         '�' => 'y',
         '�' => 'y',
         '�' => 'b',
         '�' => 'y',
         '�' => 'f');

      $strip = array(
         "~",
         "`",
         "!",
         "@",
         "#",
         "$",
         "%",
         "^",
         "&",
         "*",
         "(",
         ")",
         "=",
         "+",
         "[",
         "{",
         "]",
         "}",
         "\\",
         "|",
         ";",
         ":",
         "\"",
         "'",
         "&#8216;",
         "&#8217;",
         "&#8220;",
         "&#8221;",
         "&#8211;",
         "&#8212;",
         "—",
         "–",
         ",",
         "<",
         ">",
         "/",
         "?");

      $toClean = str_replace(' ', '_', $filename);
      $toClean = str_replace('--', '-', $toClean);
      $toClean = strtr(stripslashes($toClean), $normalizeChars);
      $toClean = str_replace($strip, "", $toClean);
      $toClean = rand(1, 9999) . "_" . $toClean;

      return $toClean;

   }





/* Random functions which may or may not be used */
if (!function_exists('echo_msg_box')){
    function echo_msg_box(){
        $errors = "";
        global $error_msg;
        global $report_msg;

        if (is_string($error_msg)){
            $error_msg = array();
        }
        if (is_string($report_msg)){
            $report_msg = array();
        }

        //for passing errors/reports over get variables
        if (isset($_REQUEST['err_msg']) && $_REQUEST['err_msg'] != ''){
            $error_msg[] = $_REQUEST['err_msg'];
        }
        if (isset($_REQUEST['rep_msg']) && $_REQUEST['rep_msg'] != ''){
            $report_msg[] = $_REQUEST['rep_msg'];
        }
        $reports = '';
        if(is_array($report_msg)){
            $first = true;
                foreach ($report_msg as $e){
                    if($first){
                        $reports.= "&nbsp;&nbsp; $e";
                        $first = false;
                    }
                    else
                        $reports.= "<br /> $e";
                }
        }
        if(isset($reports) && $reports != ''){
            echo "<div class='report'>$reports</div>";
        }

        if(is_array($error_msg)){
            $first = true;
                foreach ($error_msg as $e){
                    if($first){
                        $errors.= "&nbsp;&nbsp; $e";
                        $first = false;
                    }
                    else
                        $errors.= "<br />$e";
                }
        }
        if(isset($errors) && $errors != ''){
            echo "<div class='error'>$errors</div>";
        }
    }
}
