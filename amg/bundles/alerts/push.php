<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("SmsModel");
$smsModel = SmsModel::Instance();



$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$errors = array();

$classArr = array();
$succMessage = "";



if (isset($_POST['_chk']) == 1) {


    $branch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $class = (isset($_POST['class'])) ? $tool->GetExplodedInt($_POST['class']) : '';
    $section = (isset($_POST['section'])) ? $tool->GetExplodedInt($_POST['section']) : '';
    $session = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';
    $smstext = (isset($_POST['smstext'])) ? $smsModel->filter($_POST['smstext']) : '';
    $title = (isset($_POST['title'])) ? $smsModel->filter($_POST['title']) : '';

    if (empty($title)) {
        $errors[] = "Please type heading.";
    }

    if (empty($smstext)) {
        $errors[] = "Please type message.";
    }

    if (empty($branch)) {
        $errors[] = "Please select branch.";
    }

    if (empty($session)) {
        $errors[] = "Please select session.";
    }

    $param = array(
        "branch" => $branch, "classes" => $class, "section" => $section, "session" => $session

    );

    $res = $smsModel->getDevices($param);

    $deviceArr = array();
    foreach ($res as $row) {
        $deviceArr[$row['id']] = $row['device_token'];
    }

    $sendDevices = array();
    $notificationData = array();
    $date = date("Y-m-d");
    $createdBy = Tools::getUserId();
    if (isset($_POST['ids'])) {
        if (count($errors) == 0) {
            foreach ($_POST['ids'] as $key) {
                if (isset($deviceArr[$key])) {
                    $sendDevices[] = $deviceArr[$key];
                }

                $vals[] = array($key, $date, $title, $smstext, $createdBy, 0);
            }

            $_SESSION['msg'] = $tool->Message("succ", "Notifications sent.");

            if (!empty($vals)) {
                $smsModel->insertNotifications($vals);
            }

            $data = [
                "actionId" => "",
                "priority" => "priority",
                "title" => $title,
                "content_available" => true,
                "bodyText" => $smstext,
                "clickAction" => "notification"
            ];
            if (!empty($sendDevices)) {
                $smsModel->sendNotificationToMultiple($sendDevices, $data);
            }



            $tool->Redir("students", "smsstudent", "", "list");
            exit;
        }
    }
}


$tpl->renderBeforeContent();


if (count($errors) > 0) {
    echo $tool->Message("alert", implode("<br />", $errors));
}

$qr->searchContentAbove();


?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span2"><label class="fonts">
            <?php $tool->trans("session") ?>
        </label>
        <?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("branch") ?>
        </label>
        <?php echo $tpl->userBranches() ?>
    </div>
    <!--<div class="span3"><label class="fonts"><?php /*$tool->trans("class") */?></label><?php /*echo $tpl->getClasses() */?></div>-->

    <div class="span3"><label class="fonts">
            <?php $tool->trans("class") ?>
        </label>
        <select name="classes[]" id="class" multiple="multiple">
            <?php
            $selClass = '';
            $selecedClass = array();

            if (isset($_GET['classes'])) {
                foreach ($_GET['classes'] as $classRow) {
                    $selecedClass[$tool->GetExplodedInt($classRow)] = ' selected';
                }
            }


            foreach ($sessionClasses as $sessionClass) {
                if (isset($_GET['_chk']) == 1) {

                    if (isset($selecedClass[$sessionClass['id']])) {
                        $selClass = ' selected';
                    } else {
                        $selClass = '';
                    }
                    ?>
                    <option value="<?php echo $sessionClass['id'] ?>-<?php echo $sessionClass['title'] ?>" <?php echo $selClass ?>>
                        <?php echo $sessionClass['title'] ?>
                    </option>
                <?php } ?>
            <?php } ?>
        </select>

    </div>
    <div class="span2"><label class="fonts">
            <?php $tool->trans("section") ?>
        </label>
        <?php echo $tpl->getSecsions() ?>
    </div>
    <div class="span2"><label>&nbsp;</label><input type="submit" class="btn" value="<?php $tool->trans("Search") ?>">
    </div>
</div>


<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {


    if (empty($branch)) {
        echo $tool->Message("alert", $tool->transnoecho("branch_required"));
        exit;
    }



    if (empty($session)) {
        echo $tool->Message("alert", $tool->transnoecho("session_required"));
        exit;
    }
}
?>
<div class="body">
    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {


            $param = array(
                "branch" => $branch, "classes" => $classArr, "section" => $section, "session" => $session

            );

            $res = $smsModel->getDevices($param);


            if (count($res) == 0) {
                echo $tool->Message("alert", $tool->transnoecho("no_students_found"));
                return;
            }


            ?>

            <form method="post">


                <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                <input type="hidden" name="class" value="<?php echo $class ?>" />
                <input type="hidden" name="section" value="<?php echo $section ?>">
                <input type="hidden" name="session" value="<?php echo $session ?>">


                <?php echo $tpl->FormHidden(); ?>

                <h2 class="fonts">

                    <?php


                    if (isset($_GET['branch'])) {
                        if (!empty($_GET['branch'])) {
                            echo $tool->GetExplodedVar($_GET['branch']);
                        }
                    }





                    ?>
                    <br>
                </h2>

                <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                    <table class="table table-bordered table-striped table-hover flip-scroll">
                        <thead>
                            <tr>

                                <th class="fonts">S#</th>
                                <th class="fonts"><input type="checkbox" onclick="checkAll(this)"></th>
                                <th class="fonts">
                                    <?php $tool->trans("id") ?>
                                </th>
                                <th class="fonts">
                                    <?php $tool->trans("name_father_name") ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($res as $row) {
                                $i++ ?>
                                <tr>
                                    <td class="">
                                        <?php echo $i; ?>
                                    </td>
                                    <td class="fonts"><input type="checkbox" checked="checked" value="<?php echo $row['id']; ?>"
                                            name="ids[<?php echo $row['id']; ?>]"></td>
                                    <td class="avatar">
                                        <?php echo $row['id']; ?>
                                    </td>
                                    <td class="fonts">
                                        <?php echo $row['name']; ?>
                                        <?php echo $tpl->getGenderTrans($row['gender']) ?>
                                        <?php echo $row['fname']; ?>
                                    </td>



                                </tr>
                            <?php } ?>
                        </tbody>


                        <tr class="txtcenter">
                            <td colspan="4"><input type="text" name="title" placeholder="Heading" style="width: 99%;" />
                            </td>
                        </tr>

                        <tr class="txtcenter">
                            <td colspan="4"><textarea name="smstext" placeholder="Message"
                                    style="width: 99%; height: 100px;"></textarea> </td>
                        </tr>

                        <tr class="txtcenter">
                            <td colspan="4" class="txtcenter">
                                <button type="submit" class="btn txtcenter">Save</button>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php }
        $tpl->formClose();
        ?>
    </div>
</div>
<?php

$tpl->footer();
