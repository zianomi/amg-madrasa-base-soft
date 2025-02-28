<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();
$errors = array();
if (isset($_POST['attand']) == 1) {

    $branch = $tool->GetInt($_POST['branch']);
    $class = $tool->GetInt($_POST['class']);
    $section = $tool->GetInt($_POST['section']);
    $session = $tool->GetInt($_POST['session']);
    $date = ($_POST['date']);

    $vals = array();

    if (empty($branch) || empty($class) || empty($section) || empty($session) || empty($date)) {
        $errors[] = $tool->Message("alert", "All fields required.");
    }

    if (count($errors) == 0) {
        $a = 0;

        foreach ($_POST['attand'] as $key => $val) {

            if (!empty($val)) {
                $vals[] = $tool->setInsertDefaultValues(array("NULL", "$key", "$branch", "$class", "$section", "$session", "$date", "$val"));
            }

        }

        $res = $atd->insertClassAttand($vals);

        if ($res["status"]) {
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("attendance", "insertclass", "", "");
            exit;
        } else {
            echo $tool->Message("alert", $res["msg"]);
        }
    }


}

if (isset($_GET['_chk']) == 1) {
    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
    $section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
    $session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
    $date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : '';


    $param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "date" => $date);






}

$tpl->renderBeforeContent();
$qr->searchContentAbove();



?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?>
    </div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?>
    </div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label><?php echo $tpl->getDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if (isset($_GET['_chk']) == 1) {

    if (empty($param)) {
        echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
        exit;
    }

    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->Message("alert", "Invalid Date.");
        exit;
    }

    echo $tpl->formTag("post");
    echo $tpl->formHidden();
    ?>

    <input type="hidden" name="date" value="<?php echo $date ?>">
    <input type="hidden" name="branch" value="<?php echo $branch ?>">
    <input type="hidden" name="class" value="<?php echo $class ?>">
    <input type="hidden" name="section" value="<?php echo $section ?>">
    <input type="hidden" name="session" value="<?php echo $session ?>">


    <div class="body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S#</th>
                    <th>ID</th>
                    <th class="fonts"><?php $tool->trans("name_fathername") ?></th>
                    <th class="fonts"><?php $tool->trans("attand") ?></th>
                </tr>
            </thead>


            <tbody>


                <?php


                $res = $atd->checkClassAttand($param);

                $i = 0;
                foreach ($res as $row) {
                    $i++;
                    ?>
                    <tr>
                        <td class="fonts"><?php echo $i; ?></td>
                        <td class="fonts"><?php echo $row['id']; ?></td>
                        <td class="fonts"><?php echo $row['name'] . " " . $row['fname']; ?></td>
                        <td class="fonts">
                            <select name="attand[<?php echo $row['id']; ?>]">
                                <?php
                                echo $atd->attandPaaram();
                                ?>
                            </select>

                        </td>
                    </tr>
                <?php } ?>


                <tr>
                    <td colspan="4" style="text-align: center"><button type="submit" class="btn btn-success">
                            <i class="icon-filter"></i>Insert</button></td>
                </tr>
            </tbody>

        </table>
    </div>
    <?php
}

$tpl->footer();