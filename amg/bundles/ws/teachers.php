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
    echo $bs->errorResponse("Unauthorized");
    exit;
}

if (empty($request)) {
    echo $bs->errorResponse("Url not valid.");
    exit;
}

if ($request != "login") {
    if (empty($teacher)) {
        echo $bs->errorResponse("Please provide valid teacher login.");
        exit;
    }
}



Tools::getModel("ApiTeacherModel");
$model = new ApiTeacherModel();




switch ($request) {


    case "books":
        $res = $model->getTeacherBooks($teacher);
        echo $bs->successResponse(array("data" => $res));

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

        if ($password != $user['password']) {
            echo $bs->errorResponse("Please enter valid password. ");
            exit;
        }
        $config = $model->getConfig();

        unset($user['username']);
        unset($user['password']);

        $id = $user['id'];
        $selectedBranch = array();

        if (!empty($id)) {
            $selectedBranch = $model->getTeacherBranches($id);


        }

        $config['branches'] = $selectedBranch;

        $response = array("user" => $user, "config" => $config);

        echo $bs->successResponse($response);


        break;
}


