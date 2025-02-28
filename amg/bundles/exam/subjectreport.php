<?php
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$examCols = array();



$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';

if (!empty($_GET['exam_name'])) {
    foreach ($_GET['exam_name'] as $key) {
        $keyArr = explode("-", $key);
        $keys = $keyArr[0];
        $examCols[$keys] = $keys;
    }
}

$examData = $exm->getExamNames();

$tpl->renderBeforeContent();




$qr->searchContentAbove();
?>

<style type="text/css">
    .val input {
        width: 65% !important;
    }
</style>

<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3">
        <label class="fonts"><?php $tool->trans("fields_required") ?></label>
        <?php
        echo $tpl->GetMultiOptions(array("name" => "exam_name[]", "data" => $examData, "sel" => $examCols));
        ?>
    </div>

    <div class="span3"><label>&nbsp;</label>
        <input type="submit" class="btn">
    </div>

</div>





<?php
$qr->searchContentBottom();
?>
<div class="body">
    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {

            if (empty($session)) {
                echo $tool->Message("alert", $tool->transnoecho("please_select_session"));
                $tpl->footer();
                exit;
            }

            if (empty($examCols)) {
                echo $tool->Message("alert", $tool->transnoecho("please_select_exam"));
                $tpl->footer();
                exit;
            }



            $param['session'] = $session;
            $param['branch'] = $branch;



            foreach ($_GET['exam_name'] as $key) {
                $keyArr = explode("-", $key);
                $examId = $keyArr[0];
                $examName = $keyArr[1];

                $param['exam'] = $examId;

                $reportSubjects = $exm->getReportSubjects();
                $report = $exm->getSubjectReport($param);

                //$exams = array();
                $classes = array();
                $sections = array();
                $classPercents = array();
                $classRowPercents = array();
                $sumSubjectWise = array();


                foreach ($report as $row) {
                    //$exams[$row['exam_id']] = array("id" => $row['exam_id'], "title" => $row['exam_title']);
                    $classes[$row['class_id']] = array("id" => $row['class_id'], "title" => $row['class_title']);
                    $sections[$row['class_id']][$row['section_id']] = array("id" => $row['section_id'], "title" => $row['section_title']);
                    $percent = ($row['total_obtain_numbers'] * 100) /
                        $row['total_subject_numbers'];
                    $classPercents[$row['report_subject_id']][$row['class_id']][$row['section_id']] = $percent;

                    
                    
                    //$classRowPercents[$row['class_id']][$row['section_id']] = $percent;
                }
                //sort($sections);

                //echo '<pre>'; print_r($sumSubjectWise); echo '</pre>';


        ?>






                <h2 class="fonts">

                    <?php
                    echo $examName . ", ";
                    if (isset($_GET['branch'])) {
                        if (!empty($_GET['branch'])) {
                            echo $tool->GetExplodedVar($_GET['branch']);
                        }
                    }
                    ?>
                    <br>
                </h2>



                <div style="overflow: scroll;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S#</th>
                                <th>Class</th>
                                <th>Section</th>
                                <?php foreach ($reportSubjects as $reportSubject) { ?>
                                    <th style="inline-size: 25px !important;"><?php echo $reportSubject['title'] ?></th>
                                <?php } ?>
                                <th>Average</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            //$totalSubjectCount = count($reportSubjects);

                            foreach ($classes as $class) {
                              
                                //$totalRowWiseCount = array();
                            ?>
                                <?php if (isset($sections[$class['id']])) { ?>
                                    <?php foreach ($sections[$class['id']] as $section) {
                                        $i++;

                                        $subjectWiseTotal = 0;
                                        $totalSubjectCount = 0;
                                    ?>
                                        <tr>
                                            <td><?php echo $i ?></td>
                                            <td><?php echo $class['title'] ?></td>
                                            <td><?php echo $section['title'] ?></td>

                                            <?php foreach ($reportSubjects as $reportSubject) { ?>
                                                <td>
                                                    <?php

                                                    if (isset($classPercents[$reportSubject['id']][$class['id']][$section['id']])) {
                                                        $number = number_format($classPercents[$reportSubject['id']][$class['id']][$section['id']], 2);
                                                        $subjectWiseTotal += $number;
                                                        $totalSubjectCount ++;
                                                        //$totalRowWiseCount[$reportSubject['id']][] = $number;
                                                        echo $number;

                                                        $sumSubjectWise[$reportSubject['id']][] = $number;
                                                    }
                                                    ?>
                                                </td>
                                            <?php } ?>
                                            <td><b><?php echo number_format(($subjectWiseTotal / ($totalSubjectCount)), 2);
                                                    ?></b></td>
                                        <?php } ?>
                                    <?php } ?>
                                        </tr>
                                    <?php } ?>

                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <?php foreach ($reportSubjects as $reportSubject) { ?>
                                            <td>
                                                
                                            <?php
                                            $total = 0;
                                            if(isset($sumSubjectWise[$reportSubject['id']])){
                                                $total = array_sum($sumSubjectWise[$reportSubject['id']]);

                                                
                                            }
                                            ?>
                                            <b><?php echo number_format(($total / (count($sumSubjectWise[$reportSubject['id']]))), 2);
                                                    ?></b>
                                            </td>
                                        <?php } ?>
                                        <td>&nbsp;</td>
                                    </tr>


                        </tbody>
                    </table>
                </div>







            <?php } ?>
        <?php } ?>
    </div>
</div>
<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
