<?php
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");

$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$errors = array();
$vals = array();



$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

if (isset($_GET['_chk']) == 1) {




}



$tpl->renderBeforeContent();


$tool->displayErrorArray($errors);
$qr->searchContentAbove();
?>

<div class="row-fluid">
    <div class="span3"><label><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>
    <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>

        <div class="span3">&nbsp</div>
</div>



<?php
$qr->searchContentBottom();


if(isset($_GET['_chk'])==1) {


    if (empty($session) || empty($exam)) {
        echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
        exit;
    }

    $param = array(
            "session" => $session,
            "exam" => $exam
    );


    $res = $exm->examSummary($param);

    if (count($res) == 0) {
        echo $tool->Message("alert", $tool->transnoecho("no_result_found"));
        exit;
    }


    $distingtBranches = array();
    $distingtModules = array();
    $moduleMumtazWithSharf = array();
    $moduleMumtaz = array();
    $moduleJayyad = array();
    $moduleJiddan = array();
    $moduleMaqbool = array();
    $moduleRasib = array();
    $moduleStudentTotal = array();

    $branchMumtazWithSharf = array();
    $branchMumtaz = array();
    $branchJayyad = array();
    $branchJiddan = array();
    $branchMaqbool = array();
    $branchRasib = array();
    $branchStudentTotal = array();

    foreach ($res as $row) {
        $distingtBranches[$row['branch_id']] = array("branch_id" => $row['branch_id'], "branch_name" => $row['branch_title']);
        $distingtModules[$row['class_id']] = array("module_id" => $row['class_id'], "module_name" => $row['class_title']);

        $totalNumbers = $row['total_numbers'];
        $obtainNumbers = $row['obtain_numbers'];

        $percent = number_format(($obtainNumbers / $totalNumbers) * 100, 2);
        $number = $exm->handelFloat($percent);

        @$moduleStudentTotal[$row['branch_id']][$row['class_id']]++;
        @$branchStudentTotal[$row['branch_id']]++;


        if (floor($number) >= 9500) {
            @$moduleMumtazWithSharf[$row['branch_id']][$row['class_id']]++;
            @$branchMumtazWithSharf[$row['branch_id']]++;
        } elseif (floor($number) <= 9400 && floor($number) >= 9000) {
            @$moduleMumtaz[$row['branch_id']][$row['class_id']]++;
            @$branchMumtaz[$row['branch_id']]++;
        } elseif (floor($number) <= 8900 && floor($number) >= 8500) {
            @$moduleJiddan[$row['branch_id']][$row['class_id']]++;
            @$branchJiddan[$row['branch_id']]++;
        } elseif (floor($number) <= 8400 && floor($number) >= 8000) {
            @$moduleJayyad[$row['branch_id']][$row['class_id']]++;
            @$branchJayyad[$row['branch_id']]++;
        } elseif (floor($number) <= 7900 && floor($number) >= 7500) {
            @$moduleMaqbool[$row['branch_id']][$row['class_id']]++;
            @$branchMaqbool[$row['branch_id']]++;
        } else {
            @$moduleRasib[$row['branch_id']][$row['class_id']]++;
            @$branchRasib[$row['branch_id']]++;
        }


    }

    asort($distingtModules);
    asort($distingtBranches);

    ?>

    <div id="printReady">


        <?php foreach ($distingtBranches as $branchKey) { ?>


            <div class="alert alert-info" style="font-size: 20px; ">
                <strong style="font-family: 'Jameel Noori Nastaleeq'"><?php echo $branchKey['branch_name'] ?></strong>
            </div>


            <table class="table table-bordered table-striped table-hover">


                <thead>
                <tr>
                    <th class="fonts">شعبہ جات</th>
                    <th class="fonts">کل تعداد</th>
                    <th class="fonts">ممتاز مع الشرف</th>
                    <th class="fonts">ممتاز</th>
                    <th class="fonts">جید جدا</th>
                    <th class="fonts">جید</th>
                    <th class="fonts">مقبول</th>
                    <th class="fonts">راسب</th>
                </tr>
                </thead>


                <tbody>
                <?php foreach ($distingtModules as $moduleKey) { ?>
                    <tr class="rows">
                        <td class="fonts"><?php echo $moduleKey['module_name'] ?></td>
                        <td><?php echo @$moduleStudentTotal[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>
                        <td><?php echo @$moduleMumtazWithSharf[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>
                        <td><?php echo @$moduleMumtaz[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>
                        <td><?php echo @$moduleJiddan[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>
                        <td><?php echo @$moduleJayyad[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>
                        <td><?php echo @$moduleMaqbool[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>
                        <td><?php echo @$moduleRasib[$branchKey['branch_id']][$moduleKey['module_id']] ?></td>

                    </tr>
                <?php } ?>

                <tr>
                    <td class="fonts" style="font-weight: bold;">مجموعی نتیجہ</td>
                    <td style="font-weight: bold;"><?php echo @$branchStudentTotal[$branchKey['branch_id']] ?></td>
                    <td style="font-weight: bold;"><?php echo @$branchMumtazWithSharf[$branchKey['branch_id']] ?></td>
                    <td style="font-weight: bold;"><?php echo @$branchMumtaz[$branchKey['branch_id']] ?></td>
                    <td style="font-weight: bold;"><?php echo @$branchJiddan[$branchKey['branch_id']] ?></td>
                    <td style="font-weight: bold;"><?php echo @$branchJayyad[$branchKey['branch_id']] ?></td>
                    <td style="font-weight: bold;"><?php echo @$branchMaqbool[$branchKey['branch_id']] ?></td>
                    <td style="font-weight: bold;"><?php echo @$branchRasib[$branchKey['branch_id']] ?></td>
                </tr>

                </tbody>


            </table>


        <?php } ?>


    </div>


    <?php
}
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);