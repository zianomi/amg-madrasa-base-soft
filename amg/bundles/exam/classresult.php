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


$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$examName = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$year = (!empty($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$date = $tpl->makeDateByExam($exam, $year);











$tpl->renderBeforeContent();


$tool->displayErrorArray($errors);
$qr->searchContentAbove();


?>

<div class="row-fluid">
    <div class="span3"><label><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>

</div>


<div class="row-fluid">

    <div class="span3"><label><?php $tool->trans("exam_name") ?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>
    <div class="span3"><label><?php $tool->trans("year") ?></label><?php echo $tpf->IslamicYearDropDown(); ?></div>

    <div class="span3"><label>&nbsp;</label>
        <input type="submit" class="btn">
    </div>

    <div class="span3">&nbsp;</div>

</div>



<?php
$qr->searchContentBottom();
?>

<div id="printReady">
    <div class="body">
        <?php
        if (isset($_GET['_chk']) == 1) {


            if (empty($branch) || empty($class) || empty($exam) || empty($date)) {
                echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
                exit;
            }

            if (!$tool->checkDateFormat($date)) {
                echo $tool->Message("alert", $tool->transnoecho("invalid_date"));
                exit;
            }


            $param = array(
                "branch" => $branch,
                "class" => $class,
                "section" => $section,
                "session" => $session,
                "exam" => $exam
            );


            $res = $exm->result($param);





            if (count($res) == 0) {
                echo $tool->Message("alert", $tool->transnoecho("no_result_found"));
                return;
            }


            $subjectGroups = $exm->getSubjectGroups($param);

            $groupArr = array();

            foreach ($subjectGroups as $subjectGroup) {
                $groupArr[$subjectGroup['id']] = array("id" => $subjectGroup['id'], "title" => $subjectGroup['title']);
            }



            $subjectGroups = array();
            $numbers = array();
            $students = array();

            foreach ($res as $row) {
                $subjectGroups[$row['subject_group_id']] = $row['subject_group_id'];
                $subjects[$row['subject_group_id']][$row['subject_id']] =  array(
                    "id" => $row['subject_id'], "title" => $row['subject_title'], "subject_numbers" => $row['subject_numbers']
                );

                $numbers[$row['subject_group_id']][$row['subject_id']][$row['id']] = array(
                    "row_id" => $row['row_id'], "student_numbers" => $row['student_numbers']
                );

                $students[$row['id']] = array("id" => $row['id'], "name" => $row['name'], "fname" => $row['fname']);
            }

            $i = 0;



        ?>




            <table class="table table-bordered">


                <!--<tr>
                                <td colspan="2" style="text-align: center; ">
                                    <img src="<?php /*echo Tools::getWebUrl() */ ?>/img/logo_report.png"><img src="<?php /*echo Tools::getWebUrl() */ ?>/img/logo.png">
                                </td>
                                <td colspan="2" style="border-right: none !important;">&nbsp;&nbsp</td>
                                <td colspan="2" style="text-align: center; font-size: 25px; vertical-align: middle; border-right: none !important;" class="fonts"><?php /*echo $tool->GetExplodedVar($_GET['branch']) */ ?></td>
                            </tr>-->

                <tr>
                    <td class="green_bg fonts" style="width: 17%;"><?php Tools::trans("class"); ?></td>
                    <td class="fonts" style="font-size: 19px; text-align: center; width: 16%;"><?php echo $tool->GetExplodedVar($_GET['class']) ?></td>
                    <td class="green_bg fonts" style="width: 17%;"><?php Tools::trans("section"); ?></td>
                    <td class="fonts" style="font-size: 19px; text-align: center; width: 16%;"><?php echo $tool->GetExplodedVar($_GET['section']) ?></td>
                    <td class="green_bg fonts" style="width: 17%;"><?php Tools::trans("year"); ?></td>
                    <td class="fonts" style="font-size: 19px; text-align: center; width: 16%;"><?php echo $year ?></td>
                </tr>
            </table>

            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid" style="overflow: scroll">
                <table class="table table-bordered table-striped table-hover" style="overflow: scroll">
                    <thead>

                        <tr>

                            <th class="fonts" colspan="4" style="text-align: center;"><?php $tool->trans("student_information") ?></th>
                            <?php foreach ($subjectGroups as $subjectGroup) {
                            ?>
                                <th colspan="<?php echo count($subjects[$subjectGroup])  ?>" class="fonts" style="text-align: center;"><?php if (isset($groupArr[$subjectGroup])) echo $groupArr[$subjectGroup]['title'] ?></th>
                            <?php } ?>



                            <th class="fonts" colspan="5" style="text-align: center;"><?php $tool->trans("totals") ?></th>

                        </tr>
                        <tr>

                            <th class="fonts"><?php $tool->trans("#") ?></th>
                            <th class="fonts"><?php $tool->trans("id") ?></th>
                            <th class="fonts"><?php $tool->trans("name") ?></th>
                            <th class="fonts"><?php $tool->trans("father_name") ?></th>

                            <?php $substotal = 0;


                            foreach ($subjectGroups as $subjectGroup) {
                                if (isset($subjects[$subjectGroup])) {
                                    foreach ($subjects[$subjectGroup] as $subject) {
                                        $substotal++; ?>
                                        <th class="fonts"><?php echo $subject['title'] ?></th>
                            <?php
                                    }
                                }
                            }

                            ?>

                            <th class="tdcenter" style="vertical-align: middle; text-align: center; font-size: 18px;"><span class="fonts"><?php $tool->trans("total") ?></span></th>
                            <th class="tdcenter" style="vertical-align: middle; text-align: center; font-size: 18px;"><span class="fonts"><?php $tool->trans("percentage") ?></span></th>

                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $i = 0;

                        foreach ($students as $student) {
                            $totalStudentNumber = 0;
                            $totalSubjectNumber = 0;
                            $i++;
                        ?>
                            <tr>

                                <td class="avatar"><?php echo $i; ?></td>
                                <td class="avatar"><?php echo $student['id']; ?></td>
                                <td class="fonts"><?php echo $student['name']; ?></td>
                                <td class="fonts"><?php echo $student['fname']; ?></td>


                                <?php


                                foreach ($subjectGroups as $subjectGroup) {
                                    if (isset($subjects[$subjectGroup])) {
                                        foreach ($subjects[$subjectGroup] as $subject) {

                                            $id = 0;
                                            $studentNumbers = 0;
                                            if (isset($numbers[$subjectGroup][$subject['id']])) {
                                                $id = $numbers[$subjectGroup][$subject['id']][$student['id']]['row_id'];
                                                $studentNumbers = $numbers[$subjectGroup][$subject['id']][$student['id']]['student_numbers'];

                                                $totalStudentNumber += $studentNumbers;
                                                $totalSubjectNumber += $subject['subject_numbers'];
                                            }
                                ?>
                                            <td class="numbers" data-type="text" data-pk="{id: <?php echo $id ?>, nums: '<?php echo $subject['subject_numbers'] ?>'}" data-original-title="Enter number"><?php echo $studentNumbers ?></td>
                                <?php
                                        }
                                    }
                                }
                                ?>
                                <td><?php echo $totalStudentNumber ?></td>
                                <td><?php
                                    $percent = ($totalStudentNumber * 100) / $totalSubjectNumber;
                                    echo number_format($percent, 2) ?></td>



                            </tr>
                        <?php

                        }


                        ?>
                    </tbody>


                </table>
            </div>
        <?php } ?>
    </div>
</div>

<style>
    .green_bg {
        background: #063 !important;
        -webkit-print-color-adjust: exact;
        color: #FFF !important;
        -webkit-print-color-adjust: exact;
        font-size: 18px;
        border-left: #FFF 1px solid !important;
        text-align: center !important;
    }

    .numbers {
        font-family: Arial;
        font-weight: bold;
        text-align: center
    }

    .table,
    .table tr,
    .table td,
    .table th {
        border-color: black !important;
        ;
    }

    .h1heading {
        text-align: center;
        font-size: 22px;
        text-align: center !important;
    }

    .green_bg_small {
        height: 7px !important;
        line-height: 7px !important;
        background: #063 !important;
        -webkit-print-color-adjust: exact;
        color: #FFF !important;
        -webkit-print-color-adjust: exact;
        font-size: 15px;
        border-left: #FFF 1px solid !important;
        text-align: center !important;
    }

    .green_numbers {
        height: 7px !important;
        line-height: 7px !important;
        text-align: center !important;
        font-weight: bold;
    }

    .numbers_td {
        height: 6px !important;
        line-height: 6px !important;
        text-align: center !important;
        font-size: 15px;
    }

    .names_td {
        height: 6px !important;
        line-height: 6px !important;
        text-align: center !important;
        font-size: 15px;
        font-weight: bold;
    }




    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #printReady,
            #printReady * {
                visibility: visible;
            }

            #printReady {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    }
</style>



<?php
if ($tpl->isCanEdit()) {
?>
    <script type="text/javascript" src="<?php echo Tools::getWebUrl() ?>/js/bootstrap-editable.js"></script>

    <script type="text/javascript">
        $(function() {
            $.fn.editable.defaults.mode = 'popup';
            $('.numbers').editable({
                url: makeJsLink("ajax", "exam&ajax_request=edit_number"),
                type: 'text',
                pk: 1,
                name: 'numbers',
                title: 'Enter number'
            });
        });
    </script>
<?php
}
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
