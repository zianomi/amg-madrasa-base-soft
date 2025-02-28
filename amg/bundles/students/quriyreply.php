<?php
$error = array();
require_once VENDOR . DRS . "autoload.php";
use GuzzleHttp\Client;
Tools::getModel("SmsModel");
$smsObj = SmsModel::Instance();
$client = new Client();
Tools::getModel("AppModel");
$app = new AppModel();
Tools::getModel("SmsModel");
$smsModel = SmsModel::Instance();

$name = "";
$message = "";
$date = "";
$studentId = "";

$id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;


if (isset($_GET['id'])) {

    $rows = $app->GetQuriesData(array("id" => $id));
    if (!empty($rows)) {
        $row = $rows[0];
        $name = $row['name'];
        $message = $row['query_desc'];
        $date = $row['date'];
        $studentId = $row['student_id'];
    }



}

if (isset($_POST['_chk']) == 1) {


    $id = isset($_POST['id']) ? $tool->GetInt($_POST['id']) : "";
    $studentId = isset($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : "";
    $name = isset($_POST['name']) ? $_POST['name'] : "";
    $message = isset($_POST['message']) ? $_POST['message'] : "";




    //$messageReply = Tools::transnoecho("your_question");
    //$messageReply .= "<br /><br />";
    $messageReply = $message;
    $messageReply .= "<br /><br /><br />";
    //$messageReply .= "<br /><br />";
    //$messageReply .= Tools::transnoecho("answer");

    $reply = isset($_POST['reply']) ? $set->escape(($_POST['reply'])) : "";

    $messageReply .= ($reply);

    $data['admin_id'] = $tool->getUserId();
    $data['reply'] = $reply;
    $data['is_replied'] = 1;
    $app->updateContact($data, array("id" => $id));

    $createdBy = Tools::getUserId();
    $title = "Query Reply";

    $vals['student_id'] = $studentId;
    $vals['date'] = date("Y-m-d");
    $vals['title'] = $title;
    $vals['notification_body'] = $messageReply;
    $vals['created_by'] = $createdBy;

    $lastId = $smsModel->insertNotification($vals);

    if (!empty($lastId)) {
        if ($lastId > 0) {
            $res = $smsModel->getDevices(array("id" => $studentId));
            $device = "";
            if (!empty($res)) {
                $device = $res[0]['device_token'];

                if (!empty($device)) {
                    $payLoad = $app->makeQueryFcmPayload($lastId, $device, $title, $reply, "notification");
                    $response = $app->postFCm($payLoad);
                }
            }
        }
    }



    $_SESSION['msg'] = $tool->Message("succ", "Reply sent.");
    $tool->Redir("students", "quries", "", "");
    exit;

}

$tpl->renderBeforeContent();
?>
<div class="social-box">
    <div class="header">
        <h4><span class="fonts">
                <?php Tools::trans("contact_form_reply") ?>
            </span></h4>
    </div>
    <div class="body">
        <form class="form-horizontal" id="register-form" action="" method="post">
            <input type="hidden" name="_chk" value="1">
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="hidden" name="name" value="<?php echo $name ?>">
            <input type="hidden" name="message" value="<?php echo $message ?>">
            <input type="hidden" name="student_id" value="<?php echo $studentId ?>">

            <p class="fonts">
                <?php Tools::trans("name") ?>
            </p>
            <p>
                <?php echo $name ?>
            </p>

            <p>&nbsp;</p>
            <p class="fonts">
                <?php Tools::trans("date") ?>
            </p>
            <p>
                <?php echo $date ?>
            </p>
            <p>&nbsp;</p>
            <p class="fonts">
                <?php Tools::trans("message") ?>
            </p>

            <p>
                <?php echo $message ?>
            </p>




            <p>
                <?php Tools::trans("answer") ?>
            </p>
            <div class="control-group">
                <textarea name="reply" id="reply" class="ckeditor" cols="80" rows="10"
                    style="width:99.5%; min-height:100px"></textarea>
            </div>

            <div class="clearfix"></div>
            <div class="form-actions">
                <div class="span6 offset2">
                    <button data-loading-text="sending info..." class="btn btn-primary" type="submit"
                        id="submit-button">Reply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
$tpl->footer();
?>