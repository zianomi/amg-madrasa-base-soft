<?php
$multiPrint = false;
if (isset($_GET['multi_print'])) {
    if ($_GET['multi_print'] == "yes") {
        $multiPrint = true;
    }
}




if (!$multiPrint) {




    $student_id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
    $date = isset($_GET['date']) ? $tool->ChangeDateFormat($_GET['date']) : '';
    $to_date = isset($_GET['to_date']) ? $tool->ChangeDateFormat($_GET['to_date']) : '';


    if (!$tool->checkDateFormat($date)) {
        echo $tool->Message("alert", $tool->transnoecho("Invalid Date" . $date));
        return;
    }

    if (!$tool->checkDateFormat($to_date)) {
        echo $tool->Message("alert", $tool->transnoecho("Invalid To Date"));
        return;
    }

    if (empty($student_id) || empty($date) || empty($to_date)) {
        echo $tool->Message("alert", "All fields required");
        return;
    }



    Tools::getModel("StudentsModel");
    Tools::getModel("AttendanceModel");
    Tools::getModel("MonthlyTestModel");
    Tools::getModel("HifzModel");
    Tools::getLib("TemplateForm");
    Tools::getModel("ExamModel");

    $stu = new StudentsModel();
    $atd = new AttendanceModel();
    $test = new MonthlyTestModel();
    $hfz = new HifzModel();
    $tpf = new TemplateForm();
    $exm = new ExamModel();
}


$examNumbers = $test->getExamNumberSum($student_id, $date, $to_date);


$examSums = array();
$exams = array();

foreach ($examNumbers as $row) {

    $examSums[$row['exam_id']] = array("total_obtained" => $row['total_obtained'], "total_numbers" => $row['total_numbers']);

    $exams[$row['exam_id']] = array("id" => $row['exam_id'], "title" => $row['exam_title']);
}


$row_stu = $test->monthlyTestStudentDetail(array("start" => $date, "end" => $to_date, "student" => $student_id));

$branch = $row_stu['branch_id'];
$class = $row_stu['class_id'];



$classRanks = array();


if (!empty($class)) {
    //$classRanks = $exm->getClassRanks($class,200,"monthly_test");
}






$sumAtd = $atd->StuTotalAttand(array("branch" => $branch, "class" => $class, "start" => $date, "end" => $to_date));
$sumMonthYear = array();



foreach ($sumAtd as $rowAtd) {
    $sumDateArr = explode("-", $rowAtd['date']);
    $sumMonthYear[$sumDateArr[0]][$sumDateArr[1]] = $rowAtd['tot'];
}


$stuMonthYear = array();
$stuAtd = $atd->stuAttand(array("id" => $student_id, "start" => $date, "end" => $to_date));



foreach ($stuAtd as $rowStu) {
    $stuDateArr = explode("-", $rowStu['date']);
    $stuMonthYear[$stuDateArr[0]][$stuDateArr[1]] = array("tot" => $rowStu['tot'], "absent" => $rowStu['absent'], "rukhsat" => $rowStu['rukhsat']);
}



$rescom = $test->IDReport(
    array(
        "student_id" => $student_id,
        "date" => $date,
        "to_date" => $to_date
    )
);

$a = 1;
if (count($rescom) == 0) {
    echo $tool->Message("alert", $tool->transnoecho("no_result_found"));
    exit;
}


$absent = 0;
$leave = 0;
$late = 0;

$subIds = array();
$monthlyNumbers = array();
$disTinctMonth = array();
$disTinctYear = array();
$subjectTotalNumbers = 0;
foreach ($rescom as $rowcom) {
    $dateArr = explode("-", $rowcom['date']);
    $subIds[$rowcom['subject_id']] = array("subject_name" => $rowcom['subject_name'], "subject_numbers" => $rowcom['subject_numbers']);
    $monthlyNumbers[$rowcom['subject_id']][$dateArr[0]][$dateArr[1]] = $rowcom['number'];
    $disTinctMonth[$dateArr[0]][$dateArr[1]] = array("year" => $dateArr[0], "month" => $dateArr[1], "date" => $rowcom['date']);
    $disTinctYear[$dateArr[0]] = $dateArr[0];
}

//$hifzStuData = $hfz->HifzStuData(array("id" => $student_id, "start" => $date, "end" => $to_date));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>ID#<?php echo $student_id  ?> Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/table.css">



    <style type="text/css">
        .rotate {
            /* FF3.5+ */
            -moz-transform: rotate(-90.0deg);
            /* Opera 10.5 */
            -o-transform: rotate(-90.0deg);
            /* Saf3.1+, Chrome */
            -webkit-transform: rotate(-90.0deg);
            /* IE6,IE7 */
            filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=0.083);
            /* IE8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
            /* Standard */
            transform: rotate(-90.0deg);
            display: inline-block;
            white-space: nowrap;
            width: 5px;
            margin: 0 auto;

            position: relative;
            top: 8px;
            left: -5px;

            text-align: center;
            vertical-align: middle;


        }

        .rotate_sub {
            /* FF3.5+ */
            -moz-transform: rotate(-90.0deg);
            /* Opera 10.5 */
            -o-transform: rotate(-90.0deg);
            /* Saf3.1+, Chrome */
            -webkit-transform: rotate(-90.0deg);
            /* IE6,IE7 */
            filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=0.083);
            /* IE8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
            /* Standard */
            transform: rotate(-90.0deg);
            display: inline-block;
            white-space: nowrap;
            width: 5px;
            margin: 0 auto;

            position: relative;
            top: -12px;
            left: -5px;


        }

        .exam_name_heading {
            text-align: center;
            vertical-align: middle
        }

        <?php
        if ($lang == 'en') { ?>body {
            font-family: sans-serif;
            font-size: 13px;
            line-height: 18px;
            direction: ltr !important;
            background: rgb(204, 204, 204);
        }

        <?php } else { ?>body {
            font-family: "Jameel Noori Nastaleeq", serif;
            font-size: 13px;
            direction: rtl !important;
            background: rgb(204, 204, 204);
        }

        <?php } ?>@media print {
            a[href]:after {
                visibility: hidden !important;
            }

            .sheet {
                margin-top: 5px;
                margin-bottom: 5px;
            }
        }


        page {
            background: white;
            display: block;
            margin: 0 auto;
            margin-bottom: 0.5cm;
            box-shadow: 0 0 0.5cm rgba(0, 0, 0, 0.5);

            overflow: visible;
            height: auto !important;
        }

        page[size="A4"] {
            width: 29.7cm;
            height: 21cm;
        }

        .main_area {
            display: block;
            text-align: center;

        }

        .image_area {
            background: url("<?php //echo $defaultBg 
                                ?>") no-repeat;
            width: 29.7cm;

            background-size: contain;
        }

        .content_area {
            padding-top: 150px;
            padding-bottom: 5px;
            padding-right: 30px;
            padding-left: 30px;
        }
    </style>


</head>


<body class="A4 landscape">

    <page size="A4">

        <div class="main_area">




            <div class="image_area">

                <div class="content_area">


                    <div id="printReady">




                        <table class="table table-bordered">
                            <tr>

                                <td class="stu_head"><?php $tool->trans("name"); ?></td>
                                <td class="border_line"><strong><?php echo $row_stu['name'] ?></strong></td>
                                <td class="stu_head"><?php $tool->trans("fname"); ?></td>
                                <td class="border_line"><strong><?php echo $row_stu['fname'] ?></strong></td>
                                <!--<td class="stu_head"><?php /*$tool->trans("gr_number"); */ ?></td>
                    <td class="border_line"><?php /*echo $row_stu['grnumber'] */ ?></td>-->
                                <td class="stu_head"><?php $tool->trans("id"); ?></td>
                                <td class="border_line"><strong><?php echo $row_stu['student_id'] ?></strong></td>
                                <!-- <td class="stu_head">تاریخ پیدائش:</td>
                    <td class="border_line"><?php /*echo $tool->ChangeDateFormat($row_stu['date_of_birth']) */ ?></td>
                    <td class="stu_head">تاریخ آغاز حفظ:</td>
                    <td class="border_line">&nbsp;</td>-->
                                <td class="stu_head"><?php $tool->trans("branch"); ?></td>
                                <td class="border_line"><strong><?php echo $row_stu['branch_title'] ?></strong></td>
                                <!--<td class="stu_head">علاقہ:</td>
                    <td class="border_line"><?php /*echo $row_stu['block'] */ ?></td>-->

                            </tr>


                        </table>




                        <table class="table table-bordered">
                            <tr style="height: 100px">
                                <td style="vertical-align: middle; text-align: center; font-weight: bold;"><?php $tool->trans("month"); ?></td>


                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold;"><?php $tool->trans("total_attend"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle;  font-weight: bold;"><?php $tool->trans("presents"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle;  font-weight: bold;"><?php $tool->trans("leave"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle;  font-weight: bold;"><?php $tool->trans("absent"); ?></td>

                                <?php foreach ($subIds as $subId) {
                                    $subjectTotalNumbers += $subId['subject_numbers'];
                                ?>
                                    <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle;  font-weight: bold;">
                                        <?php echo $subId['subject_name']; ?>
                                    </td>
                                <?php } ?>

                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle;  font-weight: bold;"><?php $tool->trans("total"); ?></td>

                                <td style="vertical-align: middle; text-align: center; width: auto; font-weight: bold;"><?php $tool->trans("grade"); ?></td>


                            </tr>



                            <?php
                            foreach ($disTinctYear as $rowYear) {



                                foreach ($disTinctMonth[$rowYear] as $disTinctMonthKey => $disTinctMonthVal) {

                                    $dateString = "";
                                    if (isset($disTinctMonthVal['date'])) {
                                        $dateTest = $disTinctMonthVal['date'];

                                        if (!empty($dateTest)) {
                                            $dateTestArr = explode("-", $dateTest);
                                            $dateString = $tpf->EngMonthName($dateTestArr[1]) . " " . $dateTestArr[0];
                                        }
                                    }


                            ?>
                                    <tr>
                                        <td><?php echo $dateString ?></td>
                                        <td><?php if (isset($sumMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']])) echo $sumMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]  ?> </td>
                                        <td><?php

                                            echo @$sumMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']] - (@$stuMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]['rukhsat'] + @$stuMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]['absent'])
                                            ?></td>
                                        <td><?php if (isset($stuMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]['rukhsat'])) echo $stuMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]['rukhsat'] ?></td>
                                        <td><?php if (isset($stuMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]['absent'])) echo $stuMonthYear[$disTinctMonthVal['year']][$disTinctMonthVal['month']]['absent'] ?></td>

                                        <?php
                                        $total = 0;
                                        foreach ($monthlyNumbers as $monthlyNumber) {
                                            if (isset($monthlyNumber[$rowYear][$disTinctMonthKey])) {
                                                $total += $monthlyNumber[$rowYear][$disTinctMonthKey];
                                            }


                                            $termPercentage = ($total / 100) * 100;
                                            $termWisePercentage = number_format($termPercentage, 2);
                                        ?>
                                            <td><?php
                                                if (isset($monthlyNumber[$rowYear][$disTinctMonthKey])) {
                                                    echo $monthlyNumber[$rowYear][$disTinctMonthKey];
                                                }
                                                ?></td>
                                        <?php } ?>
                                        <td><?php echo $total ?></td>
                                        <td><?php

                                            //echo $subjectTotalNumbers;
                                            foreach ($classRanks as $classRank) {
                                                if ($exm->checkPercentage($termWisePercentage, $classRank['min_val'], $classRank['max_val'])) {
                                                    echo $classRank['grade'];
                                                    break;
                                                }
                                            }


                                            //echo $test->numberBetween($test->handelFloat($termWisePercentage)); 
                                            ?></td>


                                    </tr>
                            <?php
                                    $total = 0;
                                    $termPercentage = 0;
                                    $termWisePercentage = 0;
                                }
                            }   ?>
                        </table>






                        <?php
                        $totalShashMahi = 0;
                        $totalSalana = 0;
                        ?>

                        <table class="table table-bordered">

                            <tr>
                                <td><?php //echo $examVal['title']; 
                                    ?></td>
                                <td><?php $tool->trans("total_numbers"); ?></td>
                                <td><?php $tool->trans("obtain_numbers"); ?></td>
                                <td><?php $tool->trans("percentage"); ?></td>
                            </tr>
                            <?php

                            foreach ($exams as $examKey => $examVal) {

                                $examDataRow = array();

                                if (isset($examSums[$examKey])) {
                                    $examDataRow = $examSums[$examKey];
                                }
                                //echo '<pre>'; print_r($examSums[$examKey]); echo '</pre>';
                            ?>
                                <tr>
                                    <td><?php echo $examVal['title']; ?></td>
                                    <td><?php echo $examDataRow['total_numbers']; ?></td>
                                    <td><?php echo $examDataRow['total_obtained']; ?></td>
                                    <td><?php echo number_format(($examDataRow['total_obtained'] * 100) / $examDataRow['total_numbers'], 2); ?></td>
                                </tr>
                            <?php } ?>
                        </table>


                        <table class="table table-bordered">
                            <tr>
                                <td style="width: 15%; text-align: center"><?php $tool->trans("overall_progress"); ?></td>
                                <td style="width: 35%;">&nbsp;</td>
                                <td style="width: 15%; text-align: center"><?php $tool->trans("signature"); ?></td>
                                <td style="width: 35%">&nbsp;</td>
                            </tr>

                            <tr>
                                <td style="width: 15%; text-align: center"><?php $tool->trans("signature_office"); ?></td>
                                <td style="width: 35%;">&nbsp;</td>
                                <td style="width: 15%; text-align: center"><?php $tool->trans("signature_principal"); ?></td>
                                <td style="width: 35%;">&nbsp;</td>
                            </tr>
                        </table>








                    </div>


                </div>

            </div>

        </div>


    </page>
</body>

</html>