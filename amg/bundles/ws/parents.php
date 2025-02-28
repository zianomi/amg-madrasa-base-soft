<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 9/30/2017
 * Time: 9:08 AM
 */
Tools::getLib("BaseWs");
$bs = new BaseWs();
Tools::getModel("WsModel");
$request = isset($_POST['ajaxRequest']) ? $_POST['ajaxRequest'] : "";
$sessionToken = isset($_POST['sessionToken']) ? $_POST['sessionToken'] : "";
$studentId = isset($_POST['student']) ? $tool->GetInt($_POST['student']) : "";
$branch = isset($_POST['branch']) ? $tool->GetInt($_POST['branch']) : "";
$class = isset($_POST['class']) ? $tool->GetInt($_POST['class']) : "";
$section = isset($_POST['section']) ? $tool->GetInt($_POST['section']) : "";
$session = isset($_POST['session']) ? $tool->GetInt($_POST['session']) : "";
$offset = (isset($_POST['offset']) && is_numeric($_POST['offset'])) ? $tool->GetInt($_POST['offset']) : 0;
$limit = (isset($_POST['limit']) && is_numeric($_POST['limit'])) ? $tool->GetInt($_POST['limit']) : 30;


if (empty($request)) {
    echo $bs->getErrorResponse("", "Request empty");
    exit;
}

if($request == "login"){
   $nicNumber = isset($_POST['nic_number']) ? preg_replace('/\D/', '', $_POST['nic_number']) : "";
   $password = isset($_POST['paswword']) ? $_POST['paswword'] : "";
    if(empty($nicNumber)){
        $msg = $tool->transnoecho("Father CNIC number required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if((strlen($nicNumber)<13) || strlen($nicNumber)>13){
        $msg = $tool->transnoecho("CNIC number invalid.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if(empty($password)){
        $msg = $tool->transnoecho("Password required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    Tools::getModel("WebMobileAppModel");
    $app = new WebMobileAppModel();
    $res = $app->checkLogin($nicNumber,md5($password));

    if(empty($res)){
        $msg = $tool->transnoecho("Invalid username or password");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    //TODO provide login session data
    echo '<pre>';print_r($_REQUEST );echo '</pre>';

    exit;

}

if($request == "forgot_password"){

    $nicNumber = isset($_POST['nic_number']) ? preg_replace('/\D/', '', $_POST['nic_number']) : "";
    if(empty($nicNumber)){
        $msg = $tool->transnoecho("Father CNIC number required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if((strlen($nicNumber)<13) || strlen($nicNumber)>13){
        $msg = $tool->transnoecho("CNIC number invalid.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    $email= isset($_POST['email']) ? $_POST['email'] : "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = $tool->transnoecho("Invalid email address");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }


    Tools::getModel("WebMobileAppModel");
    $app = new WebMobileAppModel();
    $res = $app->ForgotPassword($nicNumber,$email);

    if(empty($res)){
        $msg = $tool->transnoecho("Unable to validate user data");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    //TODO send email to user for password resetting
    echo '<pre>';print_r($_REQUEST );echo '</pre>';
    exit;
}

if($request == "update_profile"){

    $parentsId = isset($_POST['parents_id']) ? $tool->GetInt($_POST['parents_id']) : "";
    $oldPassword = (isset($_POST['old_password']) && !empty($_POST['old_password'])) ? md5($_POST['old_password']) : "";
    $newPassword = (isset($_POST['old_password']) && !empty($_POST['new_password'])) ? md5($_POST['new_password']) : "";


    if(empty($oldPassword)){
        $msg = $tool->transnoecho("Old password required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if(empty($newPassword)){
        $msg = $tool->transnoecho("New password required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if(empty($parentsId) || !is_numeric($parentsId)){
        $msg = $tool->transnoecho("Invalid parents data");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    Tools::getModel("WebMobileAppModel");
    $app = new WebMobileAppModel();

    if($app->checkProfile($parentsId,$oldPassword)){
        $app->UpdateProfile($parentsId,$newPassword);
        echo $bs->getSuccResponse($tool->transnoecho("Password updated"));
        exit;
    }
    else{
        $msg = $tool->transnoecho("Unable to update password");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

}


if($request == "register"){
    $fatherNic = isset($_POST['father_nic']) ? preg_replace('/\D/', '', $_POST['father_nic']) : "";
    $motherNic = isset($_POST['mother_nic']) ? preg_replace('/\D/', '', $_POST['mother_nic']) : "";
    $fatherFone = isset($_POST['father_mobile']) ? preg_replace('/\D/', '', $_POST['father_mobile']) : "";



    if(empty($fatherNic)){
        $msg = $tool->transnoecho("Father CNIC number required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if((strlen($fatherNic)<13) || strlen($fatherNic)>13){
        $msg = $tool->transnoecho("Father CNIC number invalid.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if(empty($motherNic)){
        $msg = $tool->transnoecho("Mother CNIC number required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if((strlen($motherNic)<13) || strlen($motherNic)>13){
        $msg = $tool->transnoecho("Mother CNIC number invalid.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if(empty($fatherFone)){
        $msg = $tool->transnoecho("Father mobile number required.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    if((strlen($fatherFone)<11) || strlen($fatherFone)>11){
        $msg = $tool->transnoecho("Father mobile number invalid.");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    Tools::getModel("WebMobileAppModel");
    $app = new WebMobileAppModel();

    if($app->checkFirstTimeParents($fatherNic,$motherNic,$fatherFone)){
        echo $bs->getSuccResponse($tool->transnoecho("Parent data validated."));
        exit;
    }
    else{
        $msg = $tool->transnoecho("Unable to verify provided detail");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

}

if($request == "find_childrens"){
    $parentsId = isset($_POST['parents_id']) ? $tool->GetInt($_POST['parents_id']) : "";

    if (empty($parentsId) || !is_numeric($parentsId)) {
        echo $bs->getErrorResponse("", "ID Required");
        exit;
    }

    Tools::getModel("WebMobileAppModel");
    $app = new WebMobileAppModel();
    $rows = $app->FindParentsChildren($parentsId);

    if(empty($rows)){
        $msg = $tool->transnoecho("No record found");
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    $data = array();

    if(count($rows)>1){
        $data["select_children"] = "yes";
    }
    else{
        $data["select_children"] = "no";
    }

    $data["stu_profile_data"] = $rows;

    echo $bs->getSuccResponse($data);
    exit;
}


if (empty($studentId) || !is_numeric($studentId)) {
    echo $bs->getErrorResponse("", "ID Required");
    exit;
}
if (empty($branch) || !is_numeric($branch)) {
    echo $bs->getErrorResponse("", "Branch Required");
    exit;
}
if (empty($class) || !is_numeric($class)) {
    echo $bs->getErrorResponse("", "Class Required");
    exit;
}
if (empty($section) || !is_numeric($section)) {
    echo $bs->getErrorResponse("", "Section Required");
    exit;
}
if (empty($session) || !is_numeric($session)) {
    echo $bs->getErrorResponse("", "Session Required");
    exit;
}

$bs->setStudent($studentId);
$bs->setBranch($branch);
$bs->setClass($class);
$bs->setSection($section);
$bs->setSession($session);

if (empty($sessionToken)) {
    echo $bs->getErrorResponse("", "Session token required");
    exit;
}

if (($sessionToken) != $bs->sessionToken()) {
    echo $bs->getErrorResponse("", "Session token not valid");
    exit;
}

if (empty($studentId)) {
    echo $bs->getErrorResponse("", "ID Required");
    exit;
}

switch ($request) {

    case "send_suggestions":
        $parents = isset($_POST['parents']) ? $tool->GetInt($_POST['parents']) : "";
        if(empty($_POST['parents'])){
            $msg = $tool->transnoecho("Parent not defined.");
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }
        if(empty($_POST['short_desc'])){
            $msg = $tool->transnoecho("Description required.");
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }
        $desc = filter_var($_POST['short_desc'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $data["parents_id"] = $parents;
        $data["date"] = date("Y-m-d");
        $data["short_desc"] = $desc;

        $ins = $app->InsertSuggestions($data);
        unset($app);
        if($ins){
            echo $bs->getSuccResponse("Suggestion submitted");
            exit;
        }
        $msg = $tool->transnoecho("Unable to proccess your request");
        $bs->getErrorResponse($msg,$msg);
        exit;
    break;
    case "send_leave":
        if(empty($_POST['from_date']) || empty($_POST['to_date'])){
            $msg = $tool->transnoecho("From and to date required.");
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }
        $fromDate = (isset($_POST['from_date']) && !empty($_POST['from_date'])) ? $tool->ChangeDateFormat($_POST['from_date']) : "";
        $toDate = (isset($_POST['to_date']) && !empty($_POST['to_date'])) ? $tool->ChangeDateFormat($_POST['to_date']) : "";

        if(!$tool->checkDateFormat($fromDate) || !$tool->checkDateFormat($toDate)){
            $msg = $tool->transnoecho("From or to date not valid.");
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }

        if(empty($_POST['short_desc'])){
            $msg = $tool->transnoecho("Description required.");
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }
        $desc = filter_var($_POST['short_desc'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $data["student_id"] = $studentId;
        $data["from_date"] = $fromDate;
        $data["to_date"] = $toDate;
        $data["short_desc"] = $desc;

        $ins = $app->InsertLeaverRequest($data);
        unset($app);
        if($ins){
            echo $bs->getSuccResponse("Leave request submitted");
            exit;
        }
        $msg = $tool->transnoecho("Unable to proccess your request");
        $bs->getErrorResponse($msg,$msg);
        exit;

    break;
    case "get_fees":

        Tools::getModel("FeeModel");
        $fs = new FeeModel();
        $paidData = array();
        $unPaidData = array();
        //$paidData = $fs->GetIDPaidData(array("id" => $studentId, "session" => $session));
        $feeData = $fs->paidAndDefaulterList(array("id" => $studentId, "session" => $session));

        foreach($feeData as $key){
            $date = date("M,Y",strtotime($key["fee_date"]));
            if($key['paid_status'] == $fs->paidStatus("pending")){
                $temp["fees"] = $key["fees"];
                $temp["discount"] = $key["discount"];
                $temp["fee_date"] = $date;
                $temp["title"] = $key["title"];
                $temp["paid_status"] = $key["paid_status"];
                $unPaidData[] = $temp;
            }
            else{
                $temp["fees"] = $key["fees"];
                $temp["discount"] = $key["discount"];
                $temp["fee_date"] = $date;
                $temp["title"] = $key["title"];
                $temp["paid_status"] = $key["paid_status"];
                $paidData[] = $temp;
            }
        }

        echo $bs->getSuccResponse(array("paid_data" => $paidData, "unpaid_data" => $unPaidData));
        exit;
    break;
    case "get_home_works":
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $date = date("Y-m-d");
        if(isset($_POST['date'])){
            if($_POST['date'] != ""){
                $postedDate = $tool->ChangeDateFormat($_POST['date']);
                if($tool->checkDateFormat($postedDate)){
                    $date = $postedDate;
                }
            }
        }
        $homeWorks = $app->GetHomeWorks(array("section" => $section, "date" => $date));
        unset($app);
        echo $bs->getSuccResponse(array("home_works" => $homeWorks));
        exit;
    break;
    case "get_all_syllabus":
        $subjectId = isset($_POST['subject_id']) ? $tool->GetInt($_POST['subject_id']) : "";
        if (empty($subjectId) || !is_numeric($subjectId)) {
            echo $bs->getErrorResponse("", "Subject Required");
            exit;
        }
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $completeSubjectList = $app->GetCompletedSyllabusList($subjectId);
        $data = array();
        $temp = array();
        foreach ($completeSubjectList as $row){
            $temp["teacher_completed_date"] = $row["teacher_completed_date"];
            $temp["teacher_id"]= $row["teacher_id"];
            $temp["session_syllabus_date"] = $row["session_syllabus_date"];
            $temp["title"] = $row["title"];
            $temp["start_page_no"] = $row["start_page_no"];
            $temp["end_page_no"] = $row["end_page_no"];
            $temp["subject_id"] = $row["subject_id"];
            if($row["teacher_completed_date"] >= $row["session_syllabus_date"]){
                $temp["syllabus_status"] = "FALSE";
            }
            else{
                $temp["syllabus_status"] = "OK";
            }
            $data[] = $temp;
        }

        $uncompleteSubjectList = $app->GetSubjectSyllabusList($subjectId);
        unset($app);
        echo $bs->getSuccResponse(array("uncomplete_syllabus" => $uncompleteSubjectList, "complete_syllabus" => $data, "limit" => 20));
        exit;
    break;
    case "get_complete_syllabus":
        $subjectId = isset($_POST['subject_id']) ? $tool->GetInt($_POST['subject_id']) : "";
        if (empty($subjectId) || !is_numeric($subjectId)) {
            echo $bs->getErrorResponse("", "Subject Required");
            exit;
        }

        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $completeSubjectList = $app->GetCompletedSyllabusList($subjectId);
        $data = array();
        $temp = array();
        foreach ($completeSubjectList as $row){
            $temp["teacher_completed_date"] = $row["teacher_completed_date"];
            $temp["teacher_id"]= $row["teacher_id"];
            $temp["session_syllabus_date"] = $row["session_syllabus_date"];
            $temp["title"] = $row["title"];
            $temp["start_page_no"] = $row["start_page_no"];
            $temp["end_page_no"] = $row["end_page_no"];
            $temp["subject_id"] = $row["subject_id"];
            if($row["teacher_completed_date"] >= $row["session_syllabus_date"]){
                $temp["syllabus_status"] = "FALSE";
            }
            else{
                $temp["syllabus_status"] = "OK";
            }
            $data[] = $temp;
        }
        unset($app);
        echo $bs->getSuccResponse(array("syllabus" => $data));
        exit;


    break;
    case "get_uncomplete_syllabus":
        $subjectId = isset($_POST['subject_id']) ? $tool->GetInt($_POST['subject_id']) : "";
        if (empty($subjectId) || !is_numeric($subjectId)) {
            echo $bs->getErrorResponse("", "Subject Required");
            exit;
        }
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $uncompleteSubjectList = $app->GetSubjectSyllabusList($subjectId);
        unset($app);
        echo $bs->getSuccResponse(array("syllabus" => $uncompleteSubjectList));
        exit;
    break;
    case "get_syllabus_list":
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $rows = $app->GetTimeTableSyllabusList(array("class" => $class, "section" => $section, "session" => $session));
        $data = array();
        $temp = array();
        $i=0;
        foreach ($rows as $row){
            $i++;
            $temp['period'] = $i;
            $temp["subject_id"]= $row['subject_id'];
            $temp["subject_title"]= $row['subject_title'];
            $temp["start_time"]= $row['start_time'];
            $temp["end_time"]= $row['end_time'];
            $temp["class_id"]= $row['class_id'];
            $temp["class_title"]= $row['class_title'];
            $temp["section_id"]= $row['section_id'];
            $temp["section_title"]= $row['section_title'];
            $data[] = $temp;
        }
        unset($app);
        echo $bs->getSuccResponse(array("syllabus" => $data));
        exit;
    break;
    case "get_id_exams":
      $settings = $bs->GetParentAppsettings();
      $lastExam = $settings['last_exam'];
      $lastSession = $settings['last_session'];
      $ids = (isset($_POST['ids'])) ? $_POST['ids'] : "";
    if (!empty($ids)) {
        $idsArr = explode("-", $ids);
        if(is_array($idsArr)){
            if (is_numeric($idsArr[0])) {
                $lastExam = $idsArr[0];
            }
            if (is_numeric($idsArr[1])) {
                $lastSession = $idsArr[1];
            }
        }
    }
      Tools::getModel("ExamModel");
      $exm = new ExamModel();
      $totalExams = $exm->GetIdExams($studentId);
      $lastExamNumbers = $exm->SelectIDresult($studentId,$lastExam,$lastSession);
      $examData = array();
      $temp = array();
      $sumTotalNumbers = 0;
      $sumObtainNumbers = 0;
      foreach ($lastExamNumbers as $lastExamNumber){
          $temp["subject_name"] = $lastExamNumber['subject_name'];
          $temp["subject_number"] = $lastExamNumber['subject_number'];
          $temp["obtained_number"] = $lastExamNumber['exam_numbers'];
          $sumTotalNumbers += $lastExamNumber['subject_number'];
          $sumObtainNumbers += $lastExamNumber['exam_numbers'];
          $examData[] = $temp;
      }
        $examPercentage = ($sumObtainNumbers / $sumTotalNumbers) * 100;
        $examPercentageFormated = number_format( $examPercentage ,2);
        $grade = $exm->numberBetween($exm->handelFloat($examPercentageFormated));
      echo $bs->getSuccResponse(array("exams" => $totalExams, "numbers" => $examData, "percentage" => $examPercentage, "grade" => $grade));
      unset($exm);
      exit;

    break;
    case "parents_app_settings":
        echo $bs->getSuccResponse($bs->GetParentAppsettings());
        exit;
    break;
    case "get_student_detail":
        echo $bs->getSuccResponse($bs->GetMainStudentsData());
        exit;
    break;
    case "get_session_months":
        echo $bs->getSuccResponse($bs->GetSessionMonths());
        exit;
    break;
    case "current_session_months":
        echo $bs->getSuccResponse($bs->GetCurrentSessionMonths());
        exit;
    break;
    case "get_sessions":
        echo $bs->getSuccResponse($bs->GetSessions());
        exit;
    break;
    case "events_gallery":
        $event = (isset($_POST['event_id']) && is_numeric($_POST['event_id'])) ? $tool->GetInt($_POST['event_id']) : 0;

        if (empty($event)) {
            echo $bs->getErrorResponse("", "Error in event data");
            exit;
        }
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $eventGallery = $app->GetEventGallery(1);
        $data = array();
        $temp = array();
        foreach ($eventGallery as $row){
            $image = UPLS . "/" . $row['path'] . "/" . $row['image'];
            $temp["image"] = $image;
            $data[] = $temp;
        }
        echo $bs->getSuccResponse(array("events" => $data));
        unset($app);
        exit;

    break;
    case "get_events":
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $events = $app->GetEvents(array("limit" => "$offset,$limit"));
        $data = array();
        $temp = array();
        foreach ($events as $row){
            $date = $tool->ChangeDateFormat($row['date']);
            $image = UPLS . "/" . $row['path'] . "/" . $row['image'];
            $temp["id"] = $row['id'];
            $temp["title"] = $row['title'];
            $temp["date"] = $date;
            $temp["image"] = $image;
            $data[] = $temp;
        }
        echo $bs->getSuccResponse(array("events" => $data));
        unset($app);
        exit;
    break;
    case "get_meetings":
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $meetings = $app->GetMeetings(array("id" => $studentId, "limit" => "$offset,$limit"));
        echo $bs->getSuccResponse(array("meetings" => $meetings));
        unset($app);
        exit;
    break;
    case "get_circulars":
        Tools::getModel("WebMobileAppModel");
        $app = new WebMobileAppModel();
        $circulars = $app->GetCirculars(array("limit" => "$offset,$limit"));
        echo $bs->getSuccResponse(array("circulars" => $circulars));
        unset($app);
        exit;
    break;
    case "get_profile":
        echo $bs->getSuccResponse($bs->GetMainStudentsData());
        exit;
    break;
    case "get_monthly_attand":
        $date = $bs->GetCurrentMonthStartDate();
        $toDate = $bs->GetCurrentMonthEndDate();
        if (isset($_POST['date'])) {
            if ($tool->checkDateFormat($_POST['date'])) {
                $date = $_POST['date'];
                $toDate = $bs->GetSessionMonthEndDate($date);
            }
        }
        Tools::getModel("AttendanceModel");
        $atd = new AttendanceModel();
        $totalDays = $atd->countNumberOfAttanbdDays($date,$toDate,$branch,$class);
        $attandData = $atd->atdStudentReport($studentId, $date, $toDate);
        $absentsArr = array();
        $leavesArr = array();
        $latesArr = array();

        foreach ($attandData as $row){
            $dateArr = explode("-",$row['date']);
            if($row['attand'] == 2){
                $absentsArr[] = $dateArr[2];
            }
            if($row['attand'] == 3){
                $leavesArr[] = $dateArr[2];
            }
            if($row['attand'] == 4){
                $latesArr[] = $dateArr[2];
            }
        }

        $totalLeaves = count($leavesArr);
        $totalAbsent = count($absentsArr);
        $totalLates = count($latesArr);
        $totalPresents = $totalDays - ($totalAbsent + $totalLeaves);
        $ret["session_months"] = $bs->GetCurrentSessionMonths();
        $ret["total_attands"] = $totalDays;
        $ret["total_present"] = $totalPresents;
        $ret["total_leaves"] = $totalLeaves;
        $ret["total_lates"] = $totalLates;
        $ret["total_absents"] = $totalAbsent;
        $ret["absent_days"] = $absentsArr;
        $ret["leave_days"] = $leavesArr;
        $ret["late_days"] = $latesArr;
        unset($atd);
        echo $bs->getSuccResponse($ret);
        exit;
    break;
    case "get_session_attand":
        Tools::getModel("AttendanceModel");
        $atd = new AttendanceModel();
        $session = $bs->getSession();
        if (isset($_POST['postsession'])) {
            $session = $tool->GetInt($_POST['postsession']);
        }

        $data = array();
        $temp = array();
        $stuDataArr = array();

        $countByMonths = $atd->sessionTotalAttandDaysByMonth(array("branch" => $branch, "class" => $class, "session" => $session));
        $countStuByMonths = $atd->idSessionTotalAttandDaysByMonth(array("student" => $studentId, "session" => $session));

        foreach ($countStuByMonths as $row){
            $dateArr = explode("-",$row['date']);
            $stuDataArr[$dateArr[0]][$dateArr[1]] = $row;
        }

        foreach ($countByMonths as $row){
            $dateArr = explode("-",$row['date']);
            $year = $dateArr[0];
            $month = $dateArr[1];
            $temp['month'] = date('F, Y', strtotime($row['date']));
            $temp['total_attand'] = $row['tot'];

            if(isset($stuDataArr[$year][$month])){
                $temp['absent'] = $stuDataArr[$year][$month]['absent'];
            }
            else{
                $temp['absent'] = 0;
            }

            if(isset($stuDataArr[$year][$month])){
                $temp['laeves'] = $stuDataArr[$year][$month]['laeves'];
            }
            else{
                $temp['laeves'] = 0;
            }

            if(isset($stuDataArr[$year][$month])){
                $temp['lates'] = $stuDataArr[$year][$month]['lates'];
            }
            else{
                $temp['lates'] = 0;
            }


            $data[] = $temp;
        }

        $sessions = $atd->GetStudentSessions($studentId);

        unset($atd);
        echo $bs->getSuccResponse(array("attand"=>$data,"sessions" => $sessions));
        exit;
    break;
}

unset($bs);
