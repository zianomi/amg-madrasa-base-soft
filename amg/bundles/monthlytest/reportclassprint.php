<?php
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$hifz_year = (isset($_GET['hifz_year'])) ? $tool->GetExplodedInt($_GET['hifz_year']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';

if (empty($branch)) {
    echo  $tool->Message("alert", $tool->transnoecho("branch_required"));
    exit;
}

if (empty($class)) {
    echo  $tool->Message("alert", $tool->transnoecho("class_required"));
    exit;
}

if (empty($section)) {
    echo  $tool->Message("alert", $tool->transnoecho("section_required"));
    exit;
}

if (empty($session)) {
    echo  $tool->Message("alert", $tool->transnoecho("session_required"));
    exit;
}


Tools::getLib("TemplateForm");
Tools::getModel("MonthlyTestModel");
Tools::getModel("AttendanceModel");
Tools::getModel("ExamModel");

$tpf = new TemplateForm();
$test = new MonthlyTestModel();
$atd = new AttendanceModel();
$exm = new ExamModel();




$totalSubjectNumbers = 0;
$data = array();
$subjectName = array();
$subjectids = array();
$subjectNumber = array();
$numbers = array();
$totalNumbers = array();
$ids = array();
$reading = array();
$Attand = array();
$dateSyll = $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) . '-25';
$date = $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT);
$date = date("Y-m-t", strtotime($date));
$dateStart = $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) . '-01';

if (!$tool->checkDateFormat($dateStart)) {
    echo  $tool->Message("alert", $tool->transnoecho("date_invalid"));
    exit;
}

if (!$tool->checkDateFormat($date)) {
    echo  $tool->Message("alert", $tool->transnoecho("date_invalid"));
    exit;
}



$param = array("date" => $dateStart, "to_date" => $date, "branch" => $branch, "class" => $class, "section" => $section, "session" => $session);

$res = $test->classReport($param);


foreach ($res as $row) {
    $subjectids[$row['subject_id']] = array("name" => $row['subject_name'], "subject_number" => $row['subject_number'], "id" => $row['subject_id']);
    $numbers[$row['subject_id']][$row['id']] = array("numbers" => $row['number']);
    $ids[$row['id']] = array("id" => $row['id'], "name" => $row['name'], "fname" => $row['fname'], "gender" => $row['gender'], "grnumber" => $row['grnumber'], "subject_id" => $row['subject_id']);
}



$param['start'] = $dateStart;
$param['end'] = $date;
unset($param['section']);
unset($param['session']);
$resAttand = $atd->stuAttand($param);
foreach ($resAttand as $rowAttand) {
    $Attand[$rowAttand['student_id']] = $rowAttand;
}

$resTotalAttand = $atd->countNumberOfAttanbdDays($dateStart, $date, $branch, $class);


//$tpl->setReportsData($branch);
//$settingKeys = $tpl->getKeyVal($branch);
//$bgImage = $settingKeys['monthlytest_printclasswisereport_bg'];


$mumtazSharf = 0;
$mumtaz = 0;
$jayyad_jiddan = 0;
$jayyad = 0;
$maqbool = 0;
$rasib = 0;


//$tpl->setReportsData($branch);
//$settingKeys = $tpl->getKeyVal($branch);



$classRanks = array();

if (!empty($class)) {
    //$classRanks = $exm->getClassRanks($class,0,"monthly_test");
}


foreach ($classRanks as $classRank) {
    $rankNames[] = array("id" => $classRank['id'], "title" => $classRank['grade']);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if (isset($_GET['year']) && isset($_GET['months'])) echo $_GET['year'] . '-' .  $_GET['months']  ?> Result</title>

    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/table.css">


    <style type="text/css">
        <?php
        if ($lang == 'en') { ?>body {
            font-family: sans-serif;
            font-size: 13px;
            line-height: 18px;
            direction: ltr !important;
        }

        <?php } else { ?>body {
            font-family: "Jameel Noori Nastaleeq", serif;
            font-size: 13px;
            direction: rtl !important;
        }

        <?php } ?>body {
            background: rgb(204, 204, 204);
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
        }


        <?php



        $defaultBg = Tools::getWebUrl() . '/images/monthly_test_report.png';



        ?>.main_area {
            display: block;
            text-align: center;

        }

        .image_area {
            background: url("<?php echo $defaultBg ?>") no-repeat;
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

<body>



    <body class="A4 landscape">

        <page size="A4">
            <div class="main_area">




                <div class="image_area">

                    <div class="content_area" style="margin-top: 50px">

                        <table class="table">
                            <tr>
                                <td><strong><?php $tool->trans("class"); ?>: <?php echo $tool->GetExplodedVar($_GET['class']) ?></strong></td>
                                <td><strong><?php $tool->trans("section"); ?>: <?php echo $tool->GetExplodedVar($_GET['section']) ?></strong></td>
                                <!--<td><strong><?php /*$tool->trans("test_date"); */ ?>: </strong></td>-->
                                <td><strong><?php $tool->trans("branch"); ?>: <?php echo $tool->GetExplodedVar($_GET['branch']) ?></strong></td>

                            </tr>
                        </table>


                        <table class="table table-bordered">

                            <tr style="height: 100px">
                                <td style="text-align: center; width: auto; vertical-align: middle"><?php $tool->trans("s_no"); ?></td>
                                <td style="vertical-align: middle; width: auto; font-weight: bold"><?php $tool->trans("id"); ?></td>
                                <td style="vertical-align: middle; width: auto; font-weight: bold"><?php $tool->trans("name"); ?></td>
                                <td style="vertical-align: middle; width: auto; font-weight: bold"><?php $tool->trans("fname"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("total_attend"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("stu_attend"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("leave"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("absent"); ?></td>


                                <?php
                                foreach ($subjectids as $subjectname) {
                                ?>

                                    <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php echo $subjectname['name'] ?></td>

                                <?php } ?>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("total"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("grade"); ?></td>
                                <td style="transform: rotate(-90deg); width: auto; padding: 2px; text-align: center; vertical-align: middle; font-weight: bold"><?php $tool->trans("remarks"); ?></td>
                            </tr>


                            <tbody>


                                <tr>
                                    <td colspan="4" class="fonts"><?php $tool->trans("stu_inof"); ?></td>
                                    <td colspan="4" class="fonts"><?php $tool->trans("attendance"); ?></td>

                                    <?php

                                    foreach ($subjectids as $subjectnumber) {

                                        $totalSubjectNumbers += $subjectnumber['subject_number'];
                                    ?>
                                        <td><strong><?php echo $subjectnumber['subject_number'] ?></strong></td>
                                    <?php } ?>
                                    <td><strong><?php echo $totalSubjectNumbers ?></strong></td>
                                    <td colspan="2" class="fonts"><?php $tool->trans("remarks"); ?></td>
                                </tr>
                                <?php
                                $a = 0;
                                foreach ($ids as $id) {
                                    $a++;
                                    @$stuRukhsat = $Attand[$id['id']]['rukhsat'];
                                    @$stuAbsent = $Attand[$id['id']]['absent'];
                                    @$stuAttand = $resTotalAttand - ($stuAbsent);


                                    foreach ($classRanks as $classRank) {
                                        $gradeCount[$classRank['id']][$id['id']] = 0;
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $a ?></td>
                                        <td><?php echo $id['id'] ?></td>
                                        <td><?php echo $id['name'] ?> </td>
                                        <td><?php echo $id['fname'] ?></td>
                                        <td><?php echo $resTotalAttand ?></td>
                                        <td><?php echo $stuAttand ?></td>
                                        <td><?php echo $stuRukhsat ?></td>
                                        <td><?php echo $stuAbsent ?></td>
                                        <?php
                                        $nnn = null;


                                        foreach ($subjectids as $subjectid) {
                                            $obtainedNumbers = $numbers[$subjectid['id']][$id['id']]['numbers'];
                                            $totalNumbers[$id['id']] = ($nnn += $obtainedNumbers);
                                            $termPercentage[$id['id']] = number_format(($totalNumbers[$id['id']] / $totalSubjectNumbers) * 100, 2);
                                            $rmoveDotPercent[$id['id']] = $test->handelFloat(@$termPercentage[$id['id']]);

                                        ?>
                                            <td><?php echo $obtainedNumbers ?></td>
                                        <?php } ?>
                                        <td><?php echo $totalNumbers[$id['id']] ?></td>
                                        <td><?php
                                            foreach ($classRanks as $classRank) {
                                                if ($exm->checkPercentage(@$termPercentage[$id['id']], $classRank['min_val'], $classRank['max_val'])) {
                                                    echo $classRank['grade'];

                                                    $gradeCount[$classRank['id']][$id['id']] += 1;
                                                    break;
                                                }
                                            }

                                            //echo $test->numberBetween($test->handelFloat(@$termPercentage[$id['id']])) 
                                            ?></td>
                                        <td><?php


                                            //echo '<pre>'; var_dump($classRanks); echo '</pre>';
                                            //echo '<pre>'; var_dump($termPercentage[$id['id']]); echo '</pre>';

                                            foreach ($classRanks as $classRank) {
                                                if ($exm->checkPercentage(@$termPercentage[$id['id']], $classRank['min_val'], $classRank['max_val'])) {
                                                    echo $classRank['comments'];
                                                    break;
                                                }
                                            }


                                            //echo $test->KefyatBetween($test->handelFloat(@$termPercentage[$id['id']])) 
                                            ?></td>
                                    </tr>
                                <?php

                                    /*$number = $test->handelFloat(@$termPercentage[$id['id']]);

                        if (($number) >= 9500){
                            $mumtazSharf++;
                        }
                        elseif(($number) < 9500 && ($number) >= 9000){
                            $mumtaz++;
                        }
                        elseif($number >= 8500 && $number < 9000){
                            $jayyad_jiddan++;
                        }
                        elseif(($number) < 8500 && ($number) >= 8000){
                            $jayyad++;
                        }
                        elseif(($number) < 8000 && ($number) >= 7500){
                            $maqbool++;
                        }
                        elseif(($number) < 7500){
                            $rasib++;
                        }*/




                                } ?>

                            </tbody>
                        </table>



                        <table class="table table-bordered">
                            <tr>

                                <?php
                                //foreach ($rankNames as $rankName) {
                                ?>

                                <td class="alignCenter"><span class="arabicBottom"><strong><?php //echo $rankName['title'] 
                                                                                            ?></strong></span></td>
                                <?php //} 
                                ?>
                            </tr>
                            <tr>
                                <?php
                                //foreach ($rankNames as $rankName) {
                                ?>

                                <td class="alignCenter"><strong>
                                        <?php
                                        //if (isset($gradeCount[$rankName['id']])) {
                                        //echo array_sum($gradeCount[$rankName['id']]);
                                        //} else {
                                        //echo '0';
                                        //}
                                        ?></strong>
                                </td>
                                <?php
                                //}
                                ?>
                            </tr>
                        </table>


                        <table>





                            <tr valign="top">
                                <td valign="top">

                                </td>
                            </tr>




                        </table>

                    </div>

                </div>

            </div>

        </page>

        </div>







    </body>

</html>