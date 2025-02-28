<?php
/* @var $tool Tools */
$headers = getallheaders();
//$headers = apache_request_headers();
$appVersion = $headers["X-App-Version"] ?? "";
$appOs = $headers["X-App-Os"] ?? ""; // 1 Android, 2 iOS
//$appLang = $headers["X-App-Lang"] ?? "en"; // 1 Android, 2 iOS
$appLang = "en"; // 1 Android, 2 iOS
$request = $_REQUEST['folder'] ?? "";
$teacher = $_REQUEST['teacher'] ?? "";
$session = $_REQUEST['session'] ?? "";

Tools::getLib("BaseWs");
$bs = new BaseWs();

if (empty($appVersion) || empty($appOs)) {
    //print_r($appVersion);
    //exit;
    //print_r($headers);
    //exit;
    echo $bs->errorResponse("Unauthorized");
    exit;
}

if (empty($request)) {
    echo $bs->errorResponse("Url not valid.");
    exit;
}


Tools::getModel("ApiStudentModel");
$model = new ApiStudentModel();

$tool::setLang($appLang);



/**
 * @param BaseWs $bs
 * @return array|void
 */
function getParam(BaseWs $bs)
{
    $id = $_GET['id'] ?? "";
    $branch = $_GET['branch'] ?? "";
    $session = $_GET['session'] ?? "";
    $class = $_GET['class'] ?? "";
    $section = $_GET['section'] ?? "";

    if (
        empty($branch)
        || empty($session)
        || empty($class)
        || empty($section)
        || empty($id)
    ) {
        echo $bs->errorResponse("Please provide all detail.");
        exit;
    }

    $param['id'] = $id;
    $param['session'] = $session;
    $param['branch'] = $branch;
    $param['class'] = $class;
    $param['section'] = $section;
    return $param;
}


switch ($request) {

    case "markread":
        $id = $_POST['id'] ? intval($_POST['id']) : "";

        if (!empty($id)) {
            $model->updateNotification($id);
        }


        break;
    case "notifications":
        $id = $_GET['id'] ? intval($_GET['id']) : "";
        $pageApi = 0;
        if (isset($_GET['paging'])) {
            if (!empty($_GET['paging'])) {
                if (is_numeric($_GET['paging'])) {
                    $pageApi = $_GET['paging'];
                }
            }
        }

        if ($pageApi > 0) {
            $pageApi = $pageApi + 50;
        }
        if (empty($id)) {
            echo $bs->errorResponse("Please enter student id.");
            exit;
        }


        echo $bs->successResponse($model->getNotifications($id, $pageApi));
        break;
    case "register-device":
        $deviceToken = $_POST['device_token'] ? $model->clean($_POST['device_token']) : "";
        $id = $_POST['id'] ? intval($_POST['id']) : "";
        $os = $_POST['os'] ? $model->clean($_POST['os']) : "";

        if (empty($deviceToken)) {
            echo $bs->errorResponse("Please enter device token.");
            exit;
        }

        if (empty($id)) {
            echo $bs->errorResponse("Please enter student id.");
            exit;
        }


        if (empty($os)) {
            echo $bs->errorResponse("Please enter os.");
            exit;
        }

        $res = $model->insertDevice($id, $deviceToken, $os);
        echo $bs->successResponse("success");
        break;
    case "query":

        $query = $_POST['query'] ? $model->clean($_POST['query']) : "";
        $id = $_POST['id'] ? intval($_POST['id']) : "";

        if (empty($query)) {
            echo $bs->errorResponse("Please enter your query.");
            exit;
        }

        if (empty($id)) {
            echo $bs->errorResponse("Please enter student id.");
            exit;
        }

        $res = $model->insertQuery($id, $query);

        if (!$res) {
            echo $bs->errorResponse("Failed to received query.");
            exit;
        }

        echo $bs->successResponse("Query received.");

        break;

    case "paid-fees":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please select id.");
            exit;
        }

        if (!is_numeric($id)) {
            echo $bs->errorResponse("Please select id.");
            exit;
        }
        $res = $model->getPaidData($id);
        if (empty($res)) {
            echo $bs->errorResponse("No data available.");
            exit;
        }

        echo $bs->successResponse($res);

        break;

    case "unpaid-fees":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please select id.");
            exit;
        }

        if (!is_numeric($id)) {
            echo $bs->errorResponse("Please select id.");
            exit;
        }
        $res = $model->getUnpaidFees($id);
        if (empty($res)) {
            echo $bs->errorResponse("No data available.");
            exit;
        }

        echo $bs->successResponse($res);

        break;

    case "contact":
        $branch = $_GET['branch'] ?? "";
        if (empty($branch)) {
            echo $bs->errorResponse("Please select branch.");
            exit;
        }

        if (!is_numeric($branch)) {
            echo $bs->errorResponse("Please select branch.");
            exit;
        }

        $res = $model->getContact($branch);
        if (empty($res)) {
            echo $bs->errorResponse("No data available.");
            exit;
        }
        echo $bs->successResponse($res);

        break;

    case "homework-detail":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please select homework.");
            exit;
        }

        if (!is_numeric($id)) {
            echo $bs->errorResponse("Please select homework.");
            exit;
        }

        $res = $model->getHomeworkDetail($id);
        echo $bs->successResponse($res);
        break;
    case "homework-listing":

        $subject = $_GET['subject'] ?? "";
        $param = getParam($bs);
        $param['subject'] = $subject;


        $res = $model->getHomeworkListing($param);

        echo $bs->successResponse($res);

        break;
    case "syllabus-data":
        $subject = $_GET['subject'] ?? "";
        if (empty($subject)) {
            echo $bs->errorResponse("Please provide subject detail.");
        }
        $param = getParam($bs);
        $param['subject'] = $subject;
        echo $bs->successResponse($model->getSyllabusLessons($param));
        break;
    case "syllabus-subjects":
        $param = getParam($bs);
        echo $bs->successResponse($model->getSyllabusSubjects($param));
        break;
    case "time-table":

        $param = getParam($bs);

        Tools::getModel("TimeTableModel");
        Tools::getModel("AcademicModel");
        $timeModel = new TimeTableModel();
        $acd = new AcademicModel();


        $res = $model->getTimeTable($param);



        $days = array();
        $periods = array();

        foreach ($res as $row) {
            $tmp['id'] = $row['id'];
            $tmp['teacher_name'] = $row['teacher_name'];
            $tmp['period_name_title'] = $row['period_name'];
            $tmp['subject_title'] = $row['subject_title'];

            $tmp['start_time'] = date("h:iA", strtotime($todayDate . " " . $row['start_time']));
            $tmp['end_time'] = date("h:iA", strtotime($todayDate . " " . $row['end_time']));
            $periods[$row['weekday_id']][] = $tmp;
            $days[$row['weekday_id']] = array("id" => $row['weekday_id'], "title" => $acd->getWeekdayNameById($row['weekday_id']));
        }

        $data = array();
        foreach ($days as $day) {
            if (!empty($day)) {


                if (isset($periods[$day['id']])) {
                    $day['periods'] = $periods[$day['id']];
                } else {
                    $day['periods'] = array();
                }
                $data[] = $day;
            }
        }

        echo $bs->successResponse($data);
        break;
    case "test-numbers":
        $id = $_GET['id'] ?? "";
        $session = $_GET['session'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        if (empty($session)) {
            echo $bs->errorResponse("Please provide session.");
            exit;
        }
        $res = $model->getMonthlyTest($id, $session);


        $totalSubjectNumber = 0;
        $totalStudentNumber = 0;
        $data = array();

        foreach ($res as $row) {
            if (!empty($row['date'])) {
                $tmp['id'] = $row['id'];
                $totalSubjectNumber += $row['subject_numbers'];
                $totalStudentNumber += $row['obtained_marks'];
                $tmp['subject_numbers'] = $row['subject_numbers'];
                $tmp['obtained_marks'] = $row['obtained_marks'];
                $tmp['date'] = date("M Y", strtotime($row['date']));
                if ($row['obtained_marks'] < $row['passing_marks']) {
                    $tmp['fail'] = true;
                } else {
                    $tmp['fail'] = false;
                }
                //$tmp['sql_date'] = $row['date'];
                //$tmp['passing_marks'] = $row['passing_marks'];
            }


            $data[] = $tmp;
        }

        $response['total_subject_number'] = $totalSubjectNumber;
        $response['total_student_number'] = $totalStudentNumber;
        $percentage = ($totalStudentNumber / $totalSubjectNumber) * 100;
        $response['percentage'] = number_format($percentage, 2);
        $response['numbers'] = $data;

        echo $bs->successResponse($response);

        break;
    case "test-sessions":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        echo $bs->successResponse($model->getMonthlyTestSessions($id));
        break;
    case "exam-results":

        $id = $_GET['id'] ?? "";
        $session = $_GET['session'] ?? "";
        $exam = $_GET['exam'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        if (empty($session)) {
            echo $bs->errorResponse("Please provide session.");
            exit;
        }
        if (empty($exam)) {
            echo $bs->errorResponse("Please provide exam.");
            exit;
        }
        echo $bs->successResponse($model->getStudentResults($id, $session, $exam));
        break;
    case "exam-sessions":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        echo $bs->successResponse($model->getExamSessions($id));

        break;
    case "logs":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        echo $bs->successResponse($model->getLogs(intval($id)));



        break;
    case "attendance":
        $id = $_GET['id'] ?? "";
        $date = $_GET['date'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        if (empty($date)) {
            echo $bs->errorResponse("Please provide date.");
            exit;
        }
        if (!$tool->checkDateFormat($date)) {
            echo $bs->errorResponse("Please provide valid date");
            exit;
        }

        //$arr[2] = "absent";
        //$arr[3] = "leave";
        //$arr[4] = "late";

        $dateArr = explode("-", $date);
        $year = $dateArr[0];
        $month = $dateArr[1];

        $startDate = $year . "-" . $month . "-01";
        $endDate = date("Y-m-t", strtotime($date));

        $atdRes = $model->getAttendance($id, $startDate, $endDate);

        $absents = array();
        $lates = array();
        $leaves = array();

        foreach ($atdRes as $row) {
            $tmp['date'] = $row['date'];

            if ($row['attand'] == 2) {
                $absents[] = $tmp;
            }

            if ($row['attand'] == 3) {
                $leaves[] = $tmp;
            }

            if ($row['attand'] == 4) {
                $lates[] = $tmp;
            }
        }

        echo $bs->successResponse(array("absent" => $absents, "leave" => $leaves, "late" => $lates));
        break;
    case "profile":
        $id = $_GET['id'] ?? "";
        if (empty($id)) {
            echo $bs->errorResponse("Please provide student ID.");
            exit;
        }
        echo $bs->successResponse($model->getProfile(intval($id)));
        break;
    case "login":

        $username = $_POST['username'] ?? "";
        $password = $_POST['password'] ?? "";
        if (empty($username)) {
            echo $bs->errorResponse("Enter user name");
            exit;
        }
        if (empty($password)) {
            echo $bs->errorResponse("Enter user password");
            exit;
        }

        $user = $model->login($username);
        if (empty($user)) {
            echo $bs->errorResponse("Please enter valid user name.");
            exit;
        }

        if (($password) != $user['password']) {
            echo $bs->errorResponse("Please enter valid password.");
            exit;
        }

        $response = array("user" => $user);

        echo $bs->successResponse($response);

        break;

    default:
        echo $bs->errorResponse("Request is empty.");
        exit;
}
