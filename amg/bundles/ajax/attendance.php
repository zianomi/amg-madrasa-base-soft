<?php
if(!isset($_REQUEST['ajax_request'])){
    exit;
}

Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();

    switch($_REQUEST['ajax_request']){

        case "date_edit_atd":
            $id = $tool->GetInt($_POST['pk']);
            $value = $_POST['value'];

            if(empty($id) || empty($value)){
                $tool->trans("Error");
                http_response_code(404);
                die('');
            }

            $date = $tool->ChangeDateFormat($value);

            if(!$tool->checkDateFormat($date)){
                $tool->trans("invalid_date");
                http_response_code(404);
                die('');
            }

            $update = array( 'date' => $date );
            $atd->updateAttand($id,$update);
            die('');

        break;
        case "edit_atd":
            $id = $tool->GetInt($_POST['pk']);
            $value = $tool->GetInt($_POST['value']);

            if(empty($id) || empty($value)){
                $tool->trans("Error");
                http_response_code(404);
                die('');
            }

            if($value == 1){
                $atd->removeAtd($id);
                die('');
            }
            else{
                $update = array( 'attand' => $value );
                $atd->updateAttand($id,$update);
                die('');
            }
        break;


        case "attandall":
            $id = !empty($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : '';
            $branch = !empty($_POST['branch']) ? $tool->GetExplodedInt($_POST['branch']) : '';

            if(empty($branch)){
                echo $tool->Message("alert",$tool->transnoecho("please_select_branch"));
                die();
            }


            $row = $atd->branchStudent($id,$branch);

            if(!empty($row)){
                echo $tool->Message("succ",$row['name'] . " " . $tpl->getGenderTrans($row['gender']) . " " . $row['fname']);
                echo '<input type="hidden" name="branch_atd[]" value="'.$row['branch_id'].'"/>';
                echo '<input type="hidden" name="class_atd[]" value="'.$row['class_id'].'"/>';
                echo '<input type="hidden" name="section_atd[]" value="'.$row['section_id'].'"/>';
                echo '<input type="hidden" name="session_atd[]" value="'.$row['session_id'].'"/>';
                die();
            }else{
                echo $tool->Message("alert",$tool->transnoecho("id_not_exists"));
                die();
            }
        break;
    }