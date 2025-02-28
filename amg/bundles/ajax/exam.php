<?php
if (!isset($_REQUEST['ajax_request'])) {
    exit;
}

Tools::getModel("ExamModel");
$exm = new ExamModel();


switch ($_REQUEST['ajax_request']) {

    case "delete_exam_subject_record":

        $id = isset($_POST['record_to_delete']) ? $tool->GetInt($_POST['record_to_delete']) : "";
        if (is_numeric($id)) {
            if ($exm->removeResult($id)) {
                echo "OK";
                exit;
            } else {
                echo "Error!";
            }
        }
        break;

    case "show_parent":
        $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
        $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';

        if (empty($branch) || empty($class)) {
            $msg["status"] = 0;
            $msg["msg"] = $tool->transnoecho("All fields required");
            echo json_encode($msg);
            exit;
        }


        $param = array(
            "branch" => $branch,
            "class" => $class
        );

        $res = $exm->getParentSubs($param);

        if (count($res) == 0) {
            $msg["status"] = 0;
            $msg["msg"] = $tool->transnoecho("No subject available");
            echo json_encode($msg);
            exit;
        }


        $htm = $tpl->GetOptionVals(array("data" => $res));
        $msg["status"] = 1;
        $msg["msg"] = $htm;


        echo json_encode($msg);
        break;

    case "show_subs":
        //Tools::getLib("TemplateForm");
        //$tpf = new TemplateForm();
        $msg = array();
        $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
        $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
        $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
        $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
        $exam = (isset($_POST['exam_name'])) ? $tool->GetInt($_POST['exam_name']) : '';

        if (empty($branch) || empty($class) || empty($session) || empty($exam)) {
            $msg["status"] = 0;
            $msg["msg"] = $tool->transnoecho("all_fields_required");
            echo json_encode($msg);
            exit;
        }


        $resDate = array();


        $param = array(
            "branch" => $branch,
            "class" => $class,
            "section" => $section,
            "session" => $session,
            "exam" => $exam
        );

        $resDate = array();


        $resDateArr = $exm->examDateLogs($param);

        if (!empty($resDateArr)) {
            $resDate = $resDateArr[0];
        }

        if (empty($resDate)) {
            $msg["status"] = 0;
            $msg["msg"] = $tool->transnoecho("no_exam_log_inserted");
            echo json_encode($msg);
            exit;
        }

        if (count($resDate) == 0) {
            $msg["status"] = 0;
            $msg["msg"] = $tool->transnoecho("no_exam_log_inserted");
            echo json_encode($msg);
            exit;
        }

        $param["date_id"] = $resDate['id'];

        $examSubject = $exm->examSubject($param);

        if (empty($examSubject)) {
            $msg["status"] = 0;
            $msg["msg"] = $tool->transnoecho("no_subject_available");
            echo json_encode($msg);
            exit;
        }

        $htm = $tpl->GetOptionVals(array("data" => $examSubject));
        $msg["status"] = 1;
        $msg["msg"] = $htm;
        $msg["date"] = $resDate['exam_start_date'];


        echo json_encode($msg);
        exit;

        break;

    case "delete_exam_date_log":
        $id = isset($_POST['record_to_delete']) ? $tool->GetInt($_POST['record_to_delete']) : "";
        if (is_numeric($id)) {
            if ($exm->removeDateLog($id)) {
                echo "OK";
                exit;
            } else {
                echo "Error!";
            }
        }
        break;


    case "edit_number":
        $number = $tool->GetInt($_POST['value']);
        $id = $tool->GetInt($_POST['pk']['id']);
        $nums = $tool->GetInt($_POST['pk']['nums']);

        if ($number > $nums) {
            echo 'Number cannot be greater than ' . $nums;
            http_response_code(404);
            die('');
        } else {
            $res = $exm->updateNumber($id, $number);
            if ($res) {
                die('');
            } else {
                echo $res;
                http_response_code(404);
            }

        }
        break;
}
