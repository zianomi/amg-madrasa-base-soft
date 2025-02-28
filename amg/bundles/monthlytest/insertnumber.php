<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
Tools::getModel("MonthlyTestModel");
Tools::getModel("StudentsModel");
$test = new MonthlyTestModel();
$stu = new StudentsModel();

$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$hifz_year = (isset($_GET['hifz_year'])) ? $tool->GetExplodedInt($_GET['hifz_year']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';
$student_id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$errors = array();

if (isset($_POST['_chk']) == 1) {


    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    if (empty($branch)) {
        $errors[] = $tool->Message("alert", "branch_required");
    }

    if (empty($class)) {
        $errors[] = $tool->Message("alert", "class_required");
    }

    if (empty($section)) {
        $errors[] = $tool->Message("alert", "section_required");
    }

    if (empty($session)) {
        $errors[] = $tool->Message("alert", "session_required");
    }





    if (!$tool->checkDateFormat($date)) {
        $error[] = $tool->Message("alert", "invalid date");
    }
    $inc = 0;
    foreach ($_POST['ids'] as $key) {

        $stuid = $tool->GetInt($_POST['ids'][$key]);


        foreach ($_POST['sub_keys'] as $subkey) {
            $inc++;

            $numbers = $tool->GetInt($_POST['numbers'][$key][$subkey]);
            $subNumbers = $tool->GetInt($_POST['sub_numbers'][$subkey]);
            $passingMarks = $tool->GetInt($_POST['passing_marks'][$subkey]);
            //$subject_ids = $tool->GetInt($_POST['sub_ids'][$key][$subkey]);
            $subject_ids = $subkey;

            if (empty($stuid)) {
                $errors[] = $tool->Message("alert", "Student Required, ID#: " . $key);
            }
            if (empty($subject_ids)) {
                $errors[] = $tool->Message("alert", "Student ID# Required: " . $stuid . " Subject ID#: " . $subkey);
            }
            $vals[] = array($branch, $class, $section, $session, $stuid, $subject_ids, $subNumbers, $passingMarks, $numbers, $date);
        }
    }





    if (count($errors) == 0) {
        $test->deleteData(array('branch' => $branch, 'class' => $class, 'section' => $section, 'session' => $session, 'date' => $date));
        $res = $test->insertClassNumbers($vals);
        //$resSyll = $test->insertSyllabus($valSyllabus);
        if ($res["status"]) {
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("monthlytest", "insertnumber", "", "list");
            exit;
        } else {
            echo $tool->Message("alert", $res["msg"]);
        }
    }
}


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();


?>
<div class="row-fluid" id="student_res"></div>
<div class="row">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>

    <div class="span3">
        <label class="fonts"><?php $tool->trans("year") ?></label>
        <input type="number" name="year" id="year" minlength="4" maxlength="4" required value="<?php echo $year ?>">
    </div>
    <div class="span3"><label class="fonts"><?php $tool->trans("month") ?></label><select name="month" id="month">
            <?php echo $tpf->NewMonthDropDown($month); ?>
        </select></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("id") ?></label><input value="<?php echo $student_id ?>" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

</div>

<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {

    $date = $tpf->MakeEamDateFromMonthYear($year, $month);


    if (empty($branch)) {
        echo $tool->Message("alert", $tool->transnoecho("branch_required"));
        exit;
    }

    if (empty($class)) {
        echo $tool->Message("alert", $tool->transnoecho("class_required"));
        exit;
    }

    if (empty($section)) {
        echo $tool->Message("alert", $tool->transnoecho("section_required"));
        exit;
    }

    if (empty($session)) {
        echo $tool->Message("alert", $tool->transnoecho("session_required"));
        exit;
    }

    if (!$tool->checkDateFormat($date)) {
        echo $tool->Message("alert", $tool->transnoecho("date_invalid"));
        exit;
    }
}
?>

<div id="printReady">
    <?php
    if (isset($_GET['_chk']) == 1) {


        $param = array(
            "branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "id" => $student_id

        );

        $res = $stu->studentSearch($param);


        if (count($res) == 0) {
            echo $tool->Message("alert", $tool->transnoecho("no_students_found"));
            return;
        }

        $param['date'] = $date;

        $resNumbs = $test->getCurrentMonthNumber($param);

    ?>

        <form method="post">

            <input type="hidden" name="date" value="<?php echo $date ?>" />
            <input type="hidden" name="class" value="<?php echo $class ?>" />
            <input type="hidden" name="section" value="<?php echo $section ?>">
            <input type="hidden" name="student_id" value="<?php echo $student_id ?>">


            <?php echo $tpl->FormHidden(); ?>



            <div class="body">

                <h2 class="fonts">

                    <?php


                    echo $tool->getBranchName();

                    $subjects = $test->monthlyTestSubjects($class);
                    //$res = $stu->studentSearch($param);
                    $colsPan = count($subjects) + 4;

                    ?>
                    <br>
                </h2>


                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>

                            <th class="fonts"><?php $tool->trans("id") ?></th>
                            <th class="fonts"><?php $tool->trans("name_father_name") ?></th>
                            <?php
                            foreach ($subjects as $subject) {
                            ?>
                                <th class="fonts"><?php echo $subject['title'] ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($res as $row) { ?>
                            <tr>

                                <td class="avatar"><?php echo $row['id']; ?></td>
                                <td class="fonts"><?php echo $row['name']; ?> <?php echo $tpl->getGenderTrans($row['gender']) ?> <?php echo $row['fname']; ?></td>

                                <?php
                                foreach ($subjects as $val) {


                                    //else echo rand(1,$val['numbers'])
                                ?>
                                    <td class="fonts">
                                        <input value="<?php if (isset($resNumbs[$row['id']][$val["id"]])) echo $resNumbs[$row['id']][$val["id"]]; ?>" onkeyup="CheckValue(this, <?php echo $val['numbers'] ?>)" type="text" name="numbers[<?php echo $row['id']; ?>][<?php echo $val["id"] ?>]" style="width: 40%" maxlength="3" min="0" max="<?php echo $val["numbers"] ?>" pattern="\d+" />
                                        <input type="hidden" name="ids[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>" />
                                        <input type="hidden" name="sub_keys[<?php echo $val["id"] ?>]" value="<?php echo $val["id"] ?>" />
                                        <input type="hidden" name="sub_numbers[<?php echo $val["id"] ?>]" value="<?php echo $val["numbers"] ?>" />
                                        <input type="hidden" name="passing_marks[<?php echo $val["id"] ?>]" value="<?php echo $val["passing_marks"] ?>" />
                                    </td>
                                <?php } ?>


                            </tr>
                        <?php } ?>
                    </tbody>

                    <tr class="txtcenter">
                        <td colspan="<?php echo $colsPan ?>" class="txtcenter" style="text-align: center">
                            <button type="submit" class="btn txtcenter">Save</button>
                        </td>
                    </tr>
                </table>
            </div>
        <?php }
    echo $tpl->formClose();
        ?>
</div>

<?php

$tpl->footer();
