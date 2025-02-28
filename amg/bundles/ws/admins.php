<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 10/16/2017
 * Time: 10:36 AM
 */

Tools::getLib("BaseWs");
$bs = new BaseWs();
Tools::getModel("AdminModel");
$app = new AdminModel();
$request = isset($_POST['ajaxRequest']) ? $_POST['ajaxRequest'] : "";
$sessionToken = isset($_POST['sessionToken']) ? $_POST['sessionToken'] : "";

if (empty($request)) {
    echo $bs->getErrorResponse("", "Request empty");
    exit;
}
if (empty($sessionToken)) {
    echo $bs->getErrorResponse("", "Session token required");
    exit;
}

if($sessionToken != $bs->sessionToken()){
    echo $bs->getErrorResponse("", "Invalid Session Token");
    exit;
}

switch($request){

    case "admin_users":
        echo $bs->getSuccResponse(array("users" => $app->AdminUsers()));
        exit;
    break;
    case "insert_message":
        $userId = isset($_POST['user_id']) ? $tool->GetInt($_POST['user_id']) : "";
        $messageType = isset($_POST['message_type']) ? $tool->GetInt($_POST['message_type']) : "";
        $message = isset($_POST['message']) ? filter_var($_POST['message'], FILTER_SANITIZE_STRING) : "";

        if(empty($messageType) || empty($message)){
            $msg = "Please type message and select message type.";
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }

        if($messageType == 2){
          $adminUsers = $app->AdminUsers(array("user" => $userId));
          $foneNumbers = array();
          foreach ($adminUsers as $adminUser){
              $foneNumbers[] = $adminUser['phone_number'];
          }
          if(empty($foneNumbers)){
              $msg = "Unable to send sms. Number not correct";
              echo $bs->getErrorResponse($msg,$msg);
              exit;
          }

          $numbers = implode(",",$foneNumbers);
            Tools::getModel("SmsModel");
            $sms = new SmsModel();
            $sms->SendSMS($numbers,$message);
            echo $bs->getSuccResponse("Message sent.");
            exit;
        }


        if($messageType == 2){
            $title = "New Message";
        }
        else{
            $title = "New Notification";
        }

        if($messageType == 1){
			
			if(!empty($userId)){
				$device = $app->deviceIds($userId);
				$response = $bs->sendPushNotification($title, $message, "individual", "https://api.androidhive.info/images/minion.jpg",$device[0]);
                echo $bs->getSuccResponse(array("message" => "Message sent", "response" => $response));
                exit;
			}
			else{
				$devices = $app->deviceIds();
				$response = $bs->sendPushNotification($title, $message, "multiple", "https://api.androidhive.info/images/minion.jpg",$devices);
                echo $bs->getSuccResponse(array("message" => "Messages sent", "response" => $response));
                exit;
			}
            



          
                
        }



        //$data['user_id'] = $userId;
        //$data['message_type'] = $messageType;
        //$data['message'] = $message;



        //if($app->insertMessage($data)){

        //}
    break;
    case "messages":
        $lists = $app->messagesList();
        $messages = array();
        $notification = array();
        $temp = array();
        foreach ($lists as $list){
            $temp['name'] = $list['name'];
            $temp['message'] = $list['message'];
            $temp['created'] = date("d-m-y H:i:A", strtotime($list['created']));
            if($list['message_type'] == 1){
                $messages[] = $temp;
            }
            else{
                $notification[] = $temp;
            }
        }

        echo $bs->getSuccResponse(array("messages" => $messages, "notifications" => $notification));
        exit;

    break;
    case "login":
        $username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : "";
        $pssword = isset($_POST['pssword']) ? filter_var($_POST['pssword'], FILTER_SANITIZE_STRING) : "";
        $deviceToken = isset($_POST['device_token']) ? filter_var($_POST['device_token'], FILTER_SANITIZE_STRING) : "";

        if(empty($username) || empty($pssword)){
            $msg = "All fields Required.";
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }

        $adminData = $app->AdminLogin($username,md5($pssword));

        if(empty($adminData)){
            $msg = "Login failed.";
            echo $bs->getErrorResponse($msg,$msg);
            exit;
        }

        $app->insertDeviceToken($adminData['id'],$deviceToken);
        echo $bs->getSuccResponse(array("id" => $adminData['id']));
        exit;

    break;
    case "student_counts":
        $data = $app->GetOvealAllCountStudents();
        echo $bs->getSuccResponse($data);
        exit;
    break;
    case "count_by_branch":
        $res = $app->GetStudentCountByBranch();
        echo $bs->getSuccResponse($res);
        exit;
    break;
    case "attand_ratio":
        $temp = array();
        $data = array();
        $date = date("Y-m-d");
        if(isset($_POST['date'])){
            $passDate = $tool->ChangeDateFormat($_POST['date']);
            if($tool->checkDateFormat($passDate)){
                $date = $passDate;
            }
        }

        $todayDate = date("Y-m-d");

        if($date == $todayDate){
            $cacheTime = 1800;
        }
        else{
            $cacheTime = 257600;
        }
        $retData = null;
        $dir = $bs->GetCacheDir();
        $file = $dir . "admin_attands_".$date;

        if (file_exists($file) && (filemtime($file) > (time() - 1800 ))){
            $data = unserialize(file_get_contents($file));
            echo $bs->getSuccResponse($data);
            exit;
        }
        $resStu = $app->GetCurrentStudents();
        $resAttand = $app->GetAttandByBranch($date);

        foreach($resAttand as $row){
            $branchAbsent[$row['branch_id']] = $row['tot'];
        }

        foreach($resStu as $rowStu){
            $temp['title'] = $rowStu['title'];
            $temp['total'] = $rowStu['tot'];
            $percent = 0;
            $temp['ratio'] = 0;
            if(isset($branchAbsent[$rowStu['branch_id']])){
                $branchTotalStudents = $rowStu['tot'];
                $branchTotalAbsents = $branchAbsent[$rowStu['branch_id']];
                $percent = $branchTotalStudents - $branchTotalAbsents;
                $temp['ratio'] = $percent;
            }
            $data[] = $temp;
        }
        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($data),LOCK_EX);
        unset($app);
        echo $bs->getSuccResponse($data);
       exit;
    break;
    case "get_exams":
        /*$settingData = array();
       $settingData["last_exam_id"] = "3";
       $settingData["last_exam_title"] = "Salana";
       $settingData["current_session_start_date"] = "2017-04-01";
       $settingData["current_session_end_date"] = "2018-03-31";
       $settingData["current_session"] = 5;
       $settingData["current_session_title"] = "2017-2018";
        $settingData["last_session"] = 4;
       $settingData["last_session_title"] = "2016-2017";
       $settingDataString = serialize($settingData);
       echo $settingDataString;

       die('');*/







        if(isset($_POST['exam']) && isset($_POST['session'])){
            if(is_numeric($_POST['exam'])){
                $lastExamId = $tool->GetInt($_POST['exam']);
            }
            if(is_numeric($_POST['session'])){
                $lastSession = $tool->GetInt($_POST['session']);
            }
        }
        else{
			
            $appSettings = $bs->GetAdminAppsettings();
            $lastExamId = $appSettings['last_exam_id'];
            $lastExamTitle = $appSettings['last_exam_title'];
            $lastSession = $appSettings['last_session'];
            $lastSessionTitle = $appSettings['last_session_title'];
        }


        $dir = $bs->GetCacheDir();
        $file = $dir . "admin_exams_" . $lastExamId . "_" . $lastSession;
        if (file_exists($file) && (filemtime($file) > (time() - 86400 ))){
            $data = unserialize(file_get_contents($file));
            echo $bs->getSuccResponse($data);
            exit;
        }


        Tools::getModel("ExamModel");
        $exm = new ExamModel();
        $examData = $exm->examSummaryData($lastSession,$lastExamId);
        $exams = $app->GetUniqueExams();

        $data = array();
        $temp = array();
        $examArr = array();
        $examArr[] = array("exam_id" => 0, "session_id" => 0, "session" => "", "title" => "امتحان منتخب کریں");

        foreach($exams as $exam){
            if($exam['exam_id'] == 2){
                $temp['title'] = "ششماہی";
            }
            if($exam['exam_id'] == 3){
                $temp['title'] = "سالانہ";
            }
            $temp['session'] = $exam['title'];
            $temp['session_id'] = $exam['session_id'];
            $temp['exam_id'] = $exam['exam_id'];

            $examArr[] = $temp;
        }


        $data = array("exams" => $examArr, "exam_data" => $examData);

        if(!file_exists($dir)){
            mkdir($dir,0777,true);
        }
        file_put_contents($file,serialize($data),LOCK_EX);
        unset($exm);
        unset($app);
        echo $bs->getSuccResponse($data);
        exit;

    break;
    case "fee_structure":
        $appSettings = $bs->GetAdminAppsettings();
        $currentSession = $appSettings['current_session'];
        $feesData = $app->GetFeeStructure($currentSession);
        $branchData = array();
        $classData = array();
        $structureData = array();
        $typeData = array();
        $temp = array();
        $data = array();
        foreach($feesData as $row){
            $structureData[$row['branch_id']][$row['class_id']][$row['fee_type_id']] = array("fees" => $row['fees'], "fee_id" => $row['fee_type_id'], "fee_title" => $row['fee_title']);
        }
        foreach($feesData as $key => $row){

            $branchData[$row['branch_id']] = array("branch_id" => $row['branch_id'], "branch_title" => $row['branch_title']);

            if(isset($structureData[$row['branch_id']][$row['class_id']][1])){
                $temp["monthly"] = $structureData[$row['branch_id']][$row['class_id']][1];
            }
            else{
                $temp["monthly"] =  array("fees" => 0, "fee_id" => 0, "fee_title" => "");
            }
            if(isset($structureData[$row['branch_id']][$row['class_id']][2])){
                $temp["yearly"] = $structureData[$row['branch_id']][$row['class_id']][2];
            }
            else{
                $temp["yearly"] = array("fees" => 0, "fee_id" => 0, "fee_title" => "");;
            }
            $classData[$row['branch_id']][$row['class_id']] = array(
                    "class_id" => $row['class_id']
                   ,"class_title" => $row['class_title']
                  ,"amount" => array($temp)
            );

        }
        $branchData = array_values($branchData);
        foreach($branchData as $key => $row){
            $data[$key]['branch_info'] = $row;
            $data[$key]['classes'] = array_values($classData[$row['branch_id']]);
        }
        echo $bs->getSuccResponse($data);
        exit;

        /*
        $feeTypes = $app->GetFeeTypes();

        $stucounts = $app->countStudentsByBranchClass();
        $stuDiscounts = $app->studentDiscounts();




        $feeMonth = date("Y-m-01");

        if(isset($_POST['fee_month'])){
            if($tool->checkDateFormat($_POST['fee_month'])){
                $feeMonth = $_POST['fee_month'];
            }
        }

        $branchTotalRecieved = $app->feePaidDetail($feeMonth);
        $branchTotalPending = $app->feePendingDetail($feeMonth);




        $start = strtotime($currentSessionStartDate);
        $end = strtotime(date("Y-m-d"));
        $currentdate = $start;



        $branchRecievedData = array();
        $branchPendingData = array();
        $feeData = array();
        $discountData = array();
        $stuData = array();
        $classBranchFee = array();
        $calcFee = array();
        $monthCallArr = array();


        foreach($branchTotalPending as $rowPending){
            $branchPendingData[$rowPending['branch_id']] = $rowPending['fees'];
        }

        foreach($branchTotalRecieved as $rowRecieved){
            $branchRecievedData[$rowRecieved['branch_id']] = $rowRecieved['fees'];
        }

        foreach($feesData as $rowFee){
            $feeData[$rowFee['branch_id']][$rowFee['class_id']] = $rowFee['fees'];
        }

        foreach($stuDiscounts as $rowDiscount){
            $discountData[$rowDiscount['branch_id']][] = $rowDiscount['amount'];
        }

        foreach($stucounts as $rowStudents){
            $stuData[$rowStudents['branch_id']][$rowStudents['class_id']] = $rowStudents['tot'];
            if(isset($feeData[$rowStudents['branch_id']][$rowStudents['class_id']])){
                $calcFee[$rowStudents['branch_id']][$rowStudents['class_id']] = ($stuData[$rowStudents['branch_id']][$rowStudents['class_id']] * $feeData[$rowStudents['branch_id']][$rowStudents['class_id']]);
            }
        }


        while($currentdate < $end) {
            $cur_date = date('F, Y', $currentdate);
            $sqlFormatedDate = date('Y-m-d', $currentdate);
            $currentdate = strtotime('+1 month', $currentdate);
            $monthCallArr[$sqlFormatedDate] = $cur_date;
        }




        $branchTotalFee = array_sum($calcFee[1]);
        $branchTotalDiscount = array_sum($discountData[6]);



        $data = array("fee_types" => $feeTypes, "fee_data" => $app->GetFeeStructure($currentSession));
        unset($app);
        echo $bs->getSuccResponse($data);
        exit;*/
    break;
    case "fee_paid_detail":


        $feeMonth = date("Y-m-01");
        //$feeMonth = date("Y-10-01");
        if(isset($_POST['fee_month'])){
            if($tool->checkDateFormat($_POST['fee_month'])){
                $feeMonth = $_POST['fee_month'];
            }
        }
		
		
        $paidData = $app->GetPaidData($feeMonth);
        $students = array();
        $exempt = array();
        $totalDiscounts = array();
        $paidStudents = array();
        $pendingStudents = array();
        $branches = array();
        $brancheExpectedIncome = array();
        $brancheRecivedIncome = array();
        $branchePendingIncome = array();
        $brancheExemptAmount = array();

        foreach($paidData as $row){
            if($row['paid_status'] == "exempt"){
                @$exempt[$row['branch_id']] += 1;
                @$brancheExemptAmount[$row['branch_id']] += $row['fees'];
            }
            if($row['paid_status'] != "pending"){
                @$paidStudents[$row['branch_id']] ++;
                @$brancheRecivedIncome[$row['branch_id']]  += $row['fees'];
            }
            if($row['paid_status'] == "pending"){
                @$pendingStudents[$row['branch_id']] ++;
                @$branchePendingIncome[$row['branch_id']]  += $row['fees'];
            }
            $branches[$row['branch_id']] = $row['title'];
            @$brancheExpectedIncome[$row['branch_id']] += $row['fees'];
            @$totalDiscounts[$row['branch_id']] += $row['discount'];
            @$students[$row['branch_id']] ++;


        }

        $data = array();
        $temp = array();

        foreach($branches as $key => $val){
            $temp["branch"]                 = $val;
            $temp["students"]               = $students[$key];
            $temp["expected_income"]        = $brancheExpectedIncome[$key];
            $temp["recived_income"]         = $brancheRecivedIncome[$key];
            $temp["pending_income"]         = $branchePendingIncome[$key];
            $temp["exempt_amount"]          = $brancheExemptAmount[$key];
            $temp["paid_students"]          = $paidStudents[$key];
            $temp["pending_students"]       = ($pendingStudents[$key] - $exempt[$key]);
            $temp["exempt_students"]        = $exempt[$key];
            $temp["total_discounts"]        = $totalDiscounts[$key];
            $data[] = $temp;
        }

        $sessionMonths = $bs->GetCurrentSessionMonths();
        echo $bs->getSuccResponse(array("fee_data" =>$data,"session_months" => $sessionMonths, "current_month" => $feeMonth, "cur_month_heading" => date('F, Y', strtotime($feeMonth))));
        exit;

    break;
	case "branch_list":
		echo $bs->getSuccResponse($app->BranchList());
    exit;
	break;
	case "branch_detail":
    $id = isset($_POST['branch_id']) ? $tool->GetInt($_POST['branch_id']) : "";

    if(empty($id) || !is_numeric($id)){
        $msg = "Branch ID Required.";
        echo $bs->getErrorResponse($msg,$msg);
        exit;
    }

    $currenBranchStudents = $app->GetStudentCountByBranch($id);


    $appSettings = $bs->GetAdminAppsettings();

    $currentSession = $appSettings['current_session'];
    $structure = $app->BranchFeeStructure($id,$currentSession);
    $res = $app->GetBranchDetail($id);
    echo $bs->getSuccResponse(array("detail" => $res, "fee_structure" => $structure, "students" => $currenBranchStudents[0]));
    exit;
	break;
}
