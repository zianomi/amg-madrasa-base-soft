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
$numbers = array();
$succMessage = "";


if (isset($_POST['_chk']) == 1) {



    $branch = isset($_POST['branch']) ? $tool->GetInt($_POST['branch']) : "";
    $session = isset($_POST['session']) ? $tool->GetInt($_POST['session']) : "";
    $section = isset($_POST['section']) ? $tool->GetInt($_POST['section']) : "";
    $class = isset($_POST['class']) ? $tool->GetInt($_POST['class']) : "";

    $message = isset($_POST['smstext']) ? ($_POST['smstext']) : "";
    $phoneToSend = isset($_POST['number_type']) ? ($_POST['number_type']) : "";


    $param = array(
        "branch" => $branch,
        "session" => $session,
        "section" => $section,
        "class" => $class

    );

    if (empty($message)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_type_message"));
    }

    if (empty($phoneToSend)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_select_number_of_father_mother"));
    }


    if (empty($_POST['ids'])) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_select_atleast_one_id"));
    }


    $ids = array();
    $res = $smsModel->getNumber($param);

    foreach ($res as $row) {
        $ids[$row['id']] = $row;
    }

    $smsText = "";
    $succMessage = "";

    $search = array(
        "{id}",
        "{name}",
        "{father_name}",
        "{gender}",
        "{gr_number}",
        "{doa}",
        "{date_of_birth}",
        "{emergency_contact}",
        "{emergency_mobile}",
        "{home_phone}",
        "{father_mobile}",
        "{mother_mobile}",
        "{guardian_mobile}",
        "{class_title}",
        "{section_title}"
    );



    if (count($errors) == 0) {
        foreach ($_POST['ids'] as $key) {
            $messageRow = $ids[$key];

            $replace['{id}'] = $messageRow["id"];
            $replace['{name}'] = $messageRow["name"];
            $replace['{father_name}'] = $messageRow["fname"];
            $replace['{gr_number}'] = $messageRow["grnumber"];

            $smsText = str_replace($search, $replace, $message);

            $number = $messageRow[$phoneToSend];

            if ($phoneToSend == 'guardian_mobile') {
                $phoneToSend = 'gargin_mobile';
            }

            $number = substr($messageRow[$phoneToSend], -10);


            if (!is_numeric($number) || (strlen($number) < 10) || strlen($number) > 10) {
                $errors[] = "ID: " . $key . " number: " . $number . " is not correct.<br />";
            } else {

                $number = "+92" . $number;


                $smsModel->SendSMS($number, $smsText);


                //$msgFinal = "Message: " . $smsText;
                //$msgFinal .= "<br /> Number: " . $number;

                $succMessage .= $tool->Message("succ", "Msg sent to ID: " . $key . " Number: " . $number) . "<br />";
                //$succMessage .= $tool->Message("succ", $msgFinal) . "<br />";
            }
        }
    }


    // if (count($errors) == 0) {
    //     if (isset($_POST['ids'])) {
    //         foreach ($_POST['ids'] as $key) {
    //             $numbers[] = $_POST['number'][$key];
    //             $number = $_POST['number'][$key];

    //             if (!is_numeric($number) || (strlen($number) < 11) || strlen($number) > 11) {
    //                 $errors[] = "ID: " . $key . " number: " . $number . " is not correct.<br />";
    //             } else {
    //                 $msgText = $_POST['smstext'];
    //                 $succMessage .= "Msg sent to ID: " . $key . " Number: " . $number . "<br />";
    //             }
    //         }
    //     }
    // }




}


$tpl->renderBeforeContent();

echo $succMessage;

if (count($errors) > 0) {
    echo $tool->Message("alert", implode("<br />", $errors));
}

$qr->searchContentAbove();


?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span2"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?>
    </div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?>
    </div>


    <div class="span2"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?>
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

    /*if (empty($class)) {
        echo $tool->Message("alert", $tool->transnoecho("class_required"));
        exit;
    }*/

    /*if (empty($section)) {
        echo $tool->Message("alert", $tool->transnoecho("section_required"));
        exit;
    }*/

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
                "branch" => $branch
                ,
                "class" => $class
                ,
                "section" => $section
                ,
                "session" => $session

            );

            $res = $smsModel->getNumber($param);


            if (count($res) == 0) {
                echo $tool->Message("alert", $tool->transnoecho("no_students_found"));
                return;
            }


            ?>

            <form method="post">

                <input type="hidden" name="date" value="<?php echo $date ?>" />
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
                                <th class="fonts"><?php $tool->trans("id") ?></th>
                                <th class="fonts"><?php $tool->trans("name_father_name") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            foreach ($res as $row) {
                                $i++ ?>

                                <tr>
                                    <td class=""><?php echo $i; ?></td>
                                    <td class="fonts"><input type="checkbox" checked="checked" value="<?php echo $row['id']; ?>"
                                            name="ids[<?php echo $row['id']; ?>]"></td>
                                    <td class="avatar"><?php echo $row['id']; ?></td>
                                    <td class="fonts"><?php echo $row['name']; ?>
                                        <?php echo $tpl->getGenderTrans($row['gender']) ?>         <?php echo $row['fname']; ?>
                                    </td>



                                </tr>
                            <?php } ?>
                        </tbody>

                        <tr class="text-center">
                            <td colspan="4">
                                <p>Keywords</p>
                                <p>{id} {name} {father_name} {gr_number}</p>




                            </td>
                        </tr>

                        <tr class="txtcenter">
                            <td colspan="4">
                                <label class="fonts"><?php $tool->trans("number") ?></label>
                                <select name="number_type" style="width: 99%" required>

                                    <option value="">Please select</option>
                                    <?php
                                    $numbers = $smsModel->getNumbers();
                                    foreach ($numbers as $number) {
                                        ?>
                                        <option value="<?php echo $number['id'] ?>"><?php echo $number['title'] ?></option>
                                    <?php } ?>
                                </select>
                                <?php
                                //echo $tpl->GetOptions(array("name" => "sms", "data" => $sms->getNumbers()));
                                ?>
                            </td>
                        </tr>


                        <tr class="txtcenter">
                            <td colspan="4"><textarea name="smstext" style="width: 99%; height: 100px;"></textarea> </td>
                        </tr>

                        <tr class="txtcenter">
                            <td colspan="4" class="txtcenter">
                                <button type="submit" class="btn txtcenter">Save</button>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php }
        echo $tpl->formClose();
        ?>
    </div>
</div>
<?php

$tpl->footer();
