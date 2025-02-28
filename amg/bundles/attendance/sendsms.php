<?php
error_reporting(E_ALL); // Report all errors and warnings
ini_set('display_errors', 1); // Display errors on the screen
ini_set('display_startup_errors', 1); // Display errors during PHP startup
require_once VENDOR . DRS . "autoload.php";
use GuzzleHttp\Client;
$client = new Client();

Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();
Tools::getModel("SmsModel");
$smsObj = SmsModel::Instance();
Tools::getModel("AppModel");
$app = new AppModel();
$numbers = array();
$msg = "";
$fcmData = array();
$ids = array();
$dataToSend = array();
$values = array();

function autoLineBreak() {
    if (php_sapi_name() === 'cli') {
        return PHP_EOL; // Command-line
    } elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
        return "<br>"; // Browser
    } else {
        return "\n"; // Default for notifications/SMS
    }
}

if (isset($_POST)) {

    $branch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $session = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';
    //$date = ((isset($_POST['date'])) && (!empty($_POST['date']))) ? $tool->ChangeDateFormat($_POST['date']) : "";
    $date = date("Y-m-d");
    $user = Tools::getUserId();

    if (!empty($branch) && !empty($session) && $tool->checkDateFormat($date)) {
        $param['session'] = $session;
        $param['branch'] = $branch;
        $param['date'] = $date;
        $data = $atd->GetAbsentStudentsByDate($param);

        $branchName = isset($_POST['branch']) ? $tool->GetExplodedVar($_POST['branch']) : '';



        $lineBreak = autoLineBreak();

        if (!empty($data)) {
            foreach ($data as $row) {
                $sms = $atd->generateAttendanceMessage($row['name'],$row['fname'],$branchName);
                $fcmData[$row['student_id']] = array("id" =>$row['student_id'], "msg" => $sms);
                $ids[] = $row['student_id'];

                $values[] = [$row['student_id'], $date, "Attendance Notification", $sms, $user, 0, ''];
            }
        }


        $devicesArr = array();

        if(!empty($ids)){
            $devices = $smsObj->GetDevices(array("ids" => implode(",",$ids)));
            foreach ($devices as $device) {
                $devicesArr[$device['id']] = $device;
            }
        }

        foreach ($devicesArr as $device) {


            $msg = "Attendance Notification.";

            if(isset($fcmData[$device['id']])){
                $msg = $fcmData[$device['id']]["msg"];
            }

            $dataToSend[] = [

                        "devices" => [$device['device_token']],
                        "msgbody" => [
                            "actionId" => "",
                            "priority" => "HIGH",
                            "title" => "Attendance Alert!",
                            "content_available" => true,
                            "bodyText" => $msg,
                            "clickAction" => "attendance"
                        ],
                        "compony" => $app->fcmCompany(),
                        "datetime" => date("Y-m-d H:i:s")

            ];



        }


        if(count($values) > 0){
            $smsObj->insertNotifications($values);
        }


        $payLoad = json_encode(array("token" => $app->fcmApiToken(),"data"=>$dataToSend));
        $response = $app->postFCm($payLoad);
        $_SESSION['msg'] = $tool->Message("succ", "Message sent to server");
        $tool->Redir("attendance", "sendsms", "", "");
        exit;

    }

}


$tpl->renderBeforeContent();

$qr->setFormMethod("post");
$qr->searchContentAbove();
//INSERT INTO `jb_system_modules` (`id`, `title`, `position`, `published`, `parent_id`, `level`, `bundle`, `phpfile`, `extra`) VALUES (NULL, 'Send SMS', '999', '1', '38', '2', 'attendance', 'sendsms', 'none');
//INSERT INTO `jb_system_module_translations` (`id`, `title`, `lang_id`, `module_id`) VALUES (NULL, 'Send SMS', '3', '139');
//INSERT INTO `jb_system_module_translations` (`id`, `title`, `lang_id`, `module_id`) VALUES (NULL, 'ایس ایم ایس بھیجیں', '2', '139');


?>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span3"><label
                    class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label
                    class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <!--<div class="span3"><label class="fonts"><?php /*$tool->trans("date") */?></label><?php /*echo $tpl->getDateInput() */?>
        </div>-->
        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    </div>


<?php
$qr->searchContentBottom();


$tpl->footer();