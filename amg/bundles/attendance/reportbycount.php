<?php
require_once VENDOR . DRS . "autoload.php";
use GuzzleHttp\Client;

$client = new Client();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$tpl->setCanExport(false);
Tools::getModel("SmsModel");
$smsModel = SmsModel::Instance();
Tools::getModel("AppModel");
$app = new AppModel();

Tools::getModel("SmsModel");
$smsModel = SmsModel::Instance();
Tools::getModel("AppModel");
$app = new AppModel();
//Tools::getModel("SmsModel");
//$sms = new SmsModel();

$error = array();



if (isset($_POST['_chk']) == 1) {



    $string = $_POST['sms_text'];
    $msg = '';

    $branch = (isset($_POST['branch'])) ? $tool->GetInt($_POST['branch']) : '';
    $class = (isset($_POST['class'])) ? $tool->GetInt($_POST['class']) : '';
    $section = (isset($_POST['section'])) ? $tool->GetInt($_POST['section']) : '';
    $session = (isset($_POST['session'])) ? $tool->GetInt($_POST['session']) : '';

    if (empty($branch)) {
        $error[] = "Please select branch";
    }

    if (empty($session)) {
        $error[] = "Please select session";
    }

    $param['branch'] = $branch;
    $param['class'] = $class;
    $param['section'] = $section;
    $param['session'] = $session;

    $res = $smsModel->getDevices($param);

    $devices = array();
    $messages = array();
    foreach ($res as $row) {
        $tmp['name'] = $row['name'];
        $tmp['fname'] = $row['fname'];
        $tmp['device_token'] = $row['device_token'];
        $devices[$row['id']] = $tmp;
    }

    foreach ($_POST['smsall'] as $key) {

        if (isset($devices[$key])) {
            $search = array('{name}', '{start_date}', '{end_date}', '{count_attand}');
            $replace = array($devices[$key]['name'], $_POST['date'], $_POST['to_date'], $_POST['atd_count'][$key]);
            $sms = str_replace($search, $replace, $string);
            //$numer = $_POST['fone'][$key] . ',';

            //$temp['msg'] = $sms;
            //$temp['device'] = $devices[$key]['device_token'];
            //$temp['name'] = $devices[$key]['name'];



            $payLoad = $app->makeQueryFcmPayload("", $devices[$key]['device_token'], "Attendance Notificatin", $sms, "attendance");


            $app->postFCm($payLoad);

            $msg .= $sms . " sent to <br />" . $devices[$key]['name'] . "<br /><br />";

        }




    }




    $_SESSION['msg'] = $tool->Message("succ", $msg);

    $tool->Redir("attendance", "reportbycount", "", "");
    exit;
}

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";
$count = isset($_GET['count']) ? $tool->GetInt($_GET['count']) : '';
$atd_type = isset($_GET['atd_type']) ? $set->escape($_GET['atd_type']) : '';
$equality = isset($_GET['equality']) ? $set->escape($_GET['equality']) : '';

if (isset($_GET['_chk']) == 1) {
    $param = array(
        "branch" => $branch
        ,
        "class" => $class,
        "section" => $section,
        "session" => $session,
        "date" => $date
        ,
        "to_date" => $to_date
        ,
        "type" => $atd_type
        ,
        "count" => $count
        ,
        "equality" => $equality
    );

}

$tpl->renderBeforeContent();
$qr->searchContentAbove();


if (count($error) > 0) {
    echo $tool->Message("alert", implode(",", $error));
}
?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("session") ?>
        </label>
        <?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("branch") ?>
        </label>
        <?php echo $tpl->userBranches() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("class") ?>
        </label>
        <?php echo $tpl->getClasses() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("section") ?>
        </label>
        <?php echo $tpl->getSecsions() ?>
    </div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("date") ?>
        </label>
        <?php echo $tpl->getDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("to_date") ?>
        </label>
        <?php echo $tpl->getToDateInput() ?>
    </div>

    <div class="span3"><label class="fonts">
            <?php $tool->trans("attand_type") ?>
        </label>
        <select name="atd_type" id="atd_type" required="required">
            <option value=""></option>
            <option value="grl" <?php if ($atd_type == "grl")
                echo 'selected'; ?>>Absent,Leave,Late</option>
            <option value="gr" <?php if ($atd_type == "gr")
                echo 'selected'; ?>>Absent,Leave</option>
            <option value="g" <?php if ($atd_type == "g")
                echo 'selected'; ?>>Absent</option>
        </select>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("quantity") ?>
        </label>
        <select name="equality" id="equality" required="required">
            <?php echo $tpl->Equality(); ?>
        </select>
    </div>
</div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("count") ?>
        </label><input value="<?php echo $count ?>" required="required" type="text" name="count" id="count" /></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label class="fonts">&nbsp;</label>&nbsp;</div>
    <div class="span3"><label class="fonts">&nbsp;</label>&nbsp;</div>
</div>
<?php
$qr->searchContentBottom();


if (isset($_GET['_chk']) == 1) {

    if (empty($branch) || empty($session) || empty($date) || empty($to_date)) {
        echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
        exit;
    }

    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->Message("alert", "invalid_from_date.");
        exit;
    }

    if (!$tool->checkDateFormat($to_date)) {
        $errors[] = $tool->Message("alert", "invalid_to_date.");
        exit;
    }
    Tools::getModel("AttendanceModel");
    $atd = new AttendanceModel();

    //$qr->contentHtml();

    ?>

    <div id="editable_wrapper" class="body">
        <form action="" method="post">
            <input type="hidden" name="branch" value="<?php echo $branch ?>" />
            <input type="hidden" name="class" value="<?php echo $class ?>" />
            <input type="hidden" name="section" value="<?php echo $section ?>" />
            <input type="hidden" name="session" value="<?php echo $session ?>" />
            <input type="hidden" name="_chk" id="_chk" value="1">
            <input type="hidden" name="date" id="date" value="<?php echo $_GET['date'] ?>">
            <input type="hidden" name="to_date" id="to_date" value="<?php echo $_GET['to_date'] ?>">
            <table class="table table-bordered">
                <thead>

                    <tr>
                        <th><input type="checkbox" onclick="checkAll(this)"></th>
                        <th style="text-align: center">ID</th>
                        <th style="text-align: center"><span class="fonts">Name</span></th>
                        <th style="text-align: center"><span class="fonts">Total Attendance</span></th>
                        <th style="text-align: center"><span class="fonts">Attendance</span></th>
                        <th style="text-align: center"><span class="fonts">Absent</span></th>
                        <th style="text-align: center"><span class="fonts">Leave</span></th>
                        <th style="text-align: center"><span class="fonts">Late</span></th>

                    </tr>
                </thead>


                <tbody>
                    <?php

                    //$total_attandance = $atd->CountNumberOfAttanbdDays($date, $to_date, $branch, $class);
                    $totalAttandArr = $atd->countSchoolDays($date, $to_date, $branch);
                    $res = $atd->attandByPercent($param);
                    $abs_stu = "";
                    foreach ($res as $row) {

                        $total_attandance = 0;
                        if (isset($totalAttandArr[$row['class_id']])) {
                            $total_attandance = $totalAttandArr[$row['class_id']];
                        }

                        if ($atd_type == 'grl') {
                            $abs_stu = $row['absent'] + $row['leaves'] + $row['late'];
                        } elseif ($atd_type == 'gr') {
                            $abs_stu = $row['absent'] + $row['leaves'];
                        } else {
                            $abs_stu = $row['absent'];
                        }



                        $total_present = $total_attandance - ($row['absent'] + $row['leaves']);
                        $presentper = ($total_present / $total_attandance) * 100;
                        //$absentper = ($row['COUNT_TYPE2'] / $total_attandance) * 100;
                        //$leaveper = ($row['COUNT_TYPE3'] / $total_attandance) * 100;
                
                        ?>
                        <tr>
                            <td class="eng_wri"><input type="checkbox" name="smsall[<?php echo $row['student_id']; ?>]"
                                    id="smsall" checked="checked" value="<?php echo $row['student_id']; ?>" /></td>
                            <td>
                                <?php echo $row['student_id'] ?>
                            </td>
                            <td><span class="fonts">
                                    <?php echo $row['name'] . ' ' . $row['gender'] . ' ' . $row['fname']; ?>
                                </span></td>
                            <td>
                                <?php echo $total_attandance ?>
                            </td>
                            <td>
                                <?php echo $total_present ?>
                            </td>
                            <td>
                                <?php echo $row['absent']; ?>
                            </td>
                            <td>
                                <?php echo $row['leaves']; ?>
                            </td>
                            <td>
                                <?php echo $row['late']; ?>
                            </td>

                        </tr>

                        <input type="hidden" name="atd_count[<?php echo $row['student_id']; ?>]" value="<?php echo $abs_stu ?>">
                        <!-- <input type="hidden" name="fone[<?php //echo $row['student_id']; ?>]"
                            value="<?php //echo $row['father_mobile'] ?>"> -->
                        <!-- <input type="hidden" name="name[<?php //echo $row['student_id']; ?>]"
                            value="<?php //echo $row['name'] . ' ' . $row['gender'] . ' ' . $row['fname']; ?>"> -->

                    <?php } ?>

                    <tr>

                        <td colspan="8">
                            <textarea name="sms_text" id="sms_text"
                                style="width: 95%">{name} total absent(s) from {start_date} to {end_date} are  {count_attand}.</textarea>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="8"><button type="submit" class="btn btn-success"><i
                                    class="icon-filter"></i>SMS</button></td>
                    </tr>

                </tbody>

            </table>
        </form>
    </div>

    <?php
}

$tpl->footer();