<?php
include_once __DIR__ . DIRECTORY_SEPARATOR . 'dataprintfortest.php';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

if(empty($branch)){
    echo  $tool->Message("alert",$tool->transnoecho("branch_required"));
    exit;
}

if(empty($class)){
    echo  $tool->Message("alert",$tool->transnoecho("class_required"));
    exit;
}

if(empty($section)){
    echo  $tool->Message("alert",$tool->transnoecho("section_required"));
    exit;
}

if(empty($session)){
    echo  $tool->Message("alert",$tool->transnoecho("session_required"));
    exit;
}

if(empty($exam)){
    echo  $tool->Message("alert",$tool->transnoecho("exam_required"));
    exit;
}

Tools::getModel("ExamModel");
Tools::getLib("TemplateForm");
Tools::getModel("StudentsModel");

$tpf = new TemplateForm();

$exm = new ExamModel();
$stu = new StudentsModel();

$param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session);


$resDateArr = $exm->examDateLogs($param);


if(!empty($resDateArr)){
    $resDate = $resDateArr[0];
}

if(count($resDate)==0){
    echo $tool->Message("alert",$tool->transnoecho("no_exam_log_inserted"));
    return;
}

$date =  $resDate['exam_start_date'];


$resDateLog = $resDateArr[0];
$dateLogId = $resDateLog['id'];

$subjects = $exm->examSubjects($dateLogId);

$rows = $stu->StudentdSearchWithProfile($param);
$newRows = array_chunk($rows, 15, true);

$subjects = $exm->examSubjects($dateLogId);

for ($i = 0; $i < count($newRows); $i++) {
    echo firstHeader();
    echo secondHeader();
    echo thirdHeader();


    // echo '<pre>'; print_r($newRows[$i]); echo '</pre>';


    $rows = $newRows[$i];
    /*if($i==0){
        $start = 1;
    }
    else{
        $start += 14 + $i;
    }*/
    echo mainData($i);

    if (($i + 1) == count($newRows)) {
        echo footer();
    }

    echo '<p style="page-break-before: always">';

}

die('CALL');



















exit;
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 12/16/2018
 * Time: 2:20 PM
 */
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';


if(empty($branch)){
    echo  $tool->Message("alert",$tool->transnoecho("branch_required"));
    exit;
}

if(empty($class)){
    echo  $tool->Message("alert",$tool->transnoecho("class_required"));
    exit;
}

if(empty($section)){
    echo  $tool->Message("alert",$tool->transnoecho("section_required"));
    exit;
}

if(empty($session)){
    echo  $tool->Message("alert",$tool->transnoecho("session_required"));
    exit;
}

if(empty($exam)){
    echo  $tool->Message("alert",$tool->transnoecho("exam_required"));
    exit;
}


Tools::getModel("ExamModel");
Tools::getModel("StudentsModel");
$exm = new ExamModel();
$stu = new StudentsModel();

$param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session);

$rows = $stu->StudentdSearchWithProfile($param);







$param = array(
    "branch" => $branch,
    "class" => $class,
    "section" => $section,
    "session" => $session,
    "exam" => $exam
);


$resDateArr = $exm->examDateLogs($param);

if(!empty($resDateArr)){
    $resDate = $resDateArr[0];
}

if(count($resDate)==0){
    echo $tool->Message("alert",$tool->transnoecho("no_exam_log_inserted"));
    return;
}

$date =  $resDate['exam_start_date'];


$resDateLog = $resDateArr[0];
$dateLogId = $resDateLog['id'];

$subjects = $exm->examSubjects($dateLogId);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($_GET['year']) && isset($_GET['months'])) echo $_GET['year'] . '-'.  $_GET['months']  ?> Result</title>
    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-rtl.css">
    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-responsive-rtl.css">

    <!--<script src="../assets/js/combined.js"></script>-->

    <style type="text/css">
        @media print{
            body {-webkit-print-color-adjust: economy | exact background: #fff; font-family:"Jameel Noori Nastaleeq"; height: 800px !important; }

            .table, .table tr, .table td {
                border-color: black;
            }

            #print_div{display:none;}
            .breadcrumb{display: none}
            .navbar{display: none}
            .content{display: none}
            .user{display: none}
            .social-sidebar-content nano{display:none}

            report_bor {border-bottom: solid #000000 0.2em; border-left: none; border-right: none; border-top: none; text-align: center; border-width: 0.1em;
            }


            .nobor_for_prn {
                border: none;
            }

            .report_center {
                font-family: "Jameel Noori Nastaleeq";
                font-size: 20px;
                color: #FFF;
                text-align: center;
                vertical-align: middle;
                background-color: #063;
            }

            .report_subhead {
                font-family: "Jameel Noori Nastaleeq";
                font-size: 20px;
                color: #FFF;
                text-align: center;
                vertical-align: middle;
                background-color: #000;
            }


        }

        body{font-family:"Jameel Noori Nastaleeq"}

        #sidebar {
        .rounded-corners(5px);
        }

        .last_box_border{background:#f7f7f7 !important; -webkit-print-color-adjust: exact; color:#000 !important; -webkit-print-color-adjust: exact; font-size:16px;  text-align:center;"}
    </style>



    <style type="text/css">
        .tables {
            border-collapse: collapse;
        }
        .tables td, table th {
            border: 1px solid black;
        }
        .tables tr:first-child th {
            border-top: 0;
        }
        .tables tr:last-child td {
            border-bottom: 0;
        }
        .tables tr td:first-child,
        .tables tr th:first-child {
            border-left: 0;
        }
        .tables tr td:last-child,
        .tables tr th:last-child {
            border-right: 0;
        }

    </style>
</head>
<body>



<div id="printReady" style="direction: rtl; vertical-align: top;">


    <table width="1150" border="0" align="center" cellpadding="0" cellspacing="0" style="vertical-align: top">


        <tr>

            <td valign="top">
                <table style="width:100% !important">
                    <tr style="border:none !important;">
                        <td style="border:none !important; width: 20%"><img style="margin-right:70px" src="<?php echo $tool->getWebUrl() ?>/img/logo_report.png" height="44" width="241" /></td>
                        <td style="border:none !important; width: 60%; text-align: center; font-size: 35px;"> امتحانی نمبرات کا گوشوارہ  <?php if(isset($_GET['class']))  echo $tool->GetExplodedVar($_GET['class']) ?> </td>
                        <td style="border:none !important; width: 20%"><img src="<?php echo $tool->getWebUrl() ?>/img/logo2.png" width="57" height="59" /></td>

                    </tr>
                </table>
            </td>

        </tr>


        <tr>
            <td valign="top" style="text-align: justify; margin-top: 10px;">
                <table style="width:100% !important;">
                    <tr style="border:none !important;">

                        <td>نتائج امتحان :<?php echo $tool->GetExplodedVar($_GET['exam_name']) ?></td>
                        <td>معلم /معلمہ:</td>
                        <td>&nbsp;</td>
                        <td>کلاس: <?php echo $tool->GetExplodedVar($_GET['class']) ?></td>
                        <td>تاریخ امتحان:</td>
                        <td><?php //echo $tool->GetExplodedVar($_GET['exam_name']) ?></td>
                        <td>ممتحن/ممتحنہ:</td>
                        <td><?php echo $tool->GetExplodedVar($_GET['branch']) ?></td>
                    </tr>
                </table>
            </td>

        </tr>
        <tr>
            <td valign="top" style="text-align: justify">
                <table style="width:100% !important;" class="tables">
                    <tr>
                        <td style="text-align: center; vertical-align: middle;">نمبر شمار</td>
                        <td style="text-align: center;  vertical-align: middle;">آئ ڈی</td>
                        <td style="text-align: center;  vertical-align: middle;">رجسٹریشن</td>
                        <td style="text-align: center;  vertical-align: middle;">نام ولدیت</td>
                        <td style="text-align: center;  vertical-align: middle;">مقدار خواندگی</td>
                        <td style="text-align: center;  vertical-align: middle;">سوال 1</td>
                        <td style="text-align: center;  vertical-align: middle;">سوال 2</td>
                        <td style="text-align: center;  vertical-align: middle;">سوال 3</td>
                        <?php if($class != 3){  ?>
                        <td style="text-align: center;  vertical-align: middle;">سوال 4</td>
                        <td style="text-align: center;  vertical-align: middle;">سوال 5</td>
                        <?php } ?>
                        <td style="text-align: center;  vertical-align: middle;">میزان حفظ</td>
                        <?php
                        foreach($subjects as $sub){

                            if($sub['compulsory_sub'] == 1){
                                continue;
                            }
                            ?>
                            <td style="min-width: 20px; max-width: 20px; overflow: hidden; text-align: center; vertical-align: middle"><?php echo $sub['title'] ?></td>
                        <?php } ?>
                        <td style="text-align: center; vertical-align: middle;">میزان</td>
                        <td style="text-align: center; vertical-align: middle;">کل نمبرات</td>
                        <td rowspan="2" style="text-align: center; vertical-align: middle;">درجہ کامیابی</td>
                        <td rowspan="2" style="text-align: center; vertical-align: middle;">پوزیشن</td>
                        <td rowspan="2" style="text-align: center; vertical-align: middle; width: 15%">کیفیت</td>

                    </tr>
                    <tr style="background-color: #f7f7f7">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td style="text-align: center;">حفظ</td>
                        <td style="text-align: center;  vertical-align: middle;">12</td>
                        <td style="text-align: center;  vertical-align: middle;">12</td>
                        <td style="text-align: center;  vertical-align: middle;">12</td>
                        <?php if($class != 3){  ?>
                        <td style="text-align: center;  vertical-align: middle;">12</td>
                        <td style="text-align: center;  vertical-align: middle;">12</td>
                        <?php } ?>
                        <td style="text-align: center;  vertical-align: middle;">60</td>
                        <?php
                        $totalSubjectNumbers = null;
                        foreach($subjects as $sub){

                            $totalSubjectNumbers += $sub['numbers'];
                            if($sub['compulsory_sub'] == 1){
                                continue;
                            }
                            ?>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $sub['numbers'] ?></td>
                        <?php } ?>
                        <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $totalSubjectNumbers ?></td>
                        <td style="text-align: center; font-family: Arial; font-size: 15px;">60</td>


                    </tr>


                    <?php
                    $a=0;
                    foreach($rows as $row){
                        $a++;

                        ?>
                        <tr>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $a ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $row['id'] ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $row['grnumber'] ?></td>
                            <td><?php echo $row['name'] ?> <?php echo $tpl->getGenderTrans($row['gender']) ?> <?php echo $row['fname'] ?></td>
                            <td style="text-align: center;  vertical-align: middle;">&nbsp;</td>
                            <td style="text-align: center;  vertical-align: middle;">&nbsp;</td>
                            <td style="text-align: center;  vertical-align: middle;">&nbsp;</td>
                            <td style="text-align: center;  vertical-align: middle;">&nbsp;</td>
                            <?php if($class != 3){  ?>
                            <td style="text-align: center;  vertical-align: middle;">&nbsp;</td>
                            <td style="text-align: center;  vertical-align: middle;">&nbsp;</td>
                            <?php } ?>
                            <?php
                            $nnn = null;
                            $mumtazSharf = 0 ;
                            $mumtaz = 0 ;
                            $jayyad_jiddan = 0 ;
                            $jayyad = 0 ;
                            $maqbool = 0 ;
                            $rasib = 0 ;
                            foreach($subjects as $sub){
                                if($sub['compulsory_sub'] == 1){
                                    continue;
                                }
                                ?>
                                <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $obtainedNumbers ?></td>
                            <?php } ?>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $totalNumbers[$id['id']] ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $totalNumbers[$id['id']] ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $totalNumbers[$id['id']] ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $totalNumbers[$id['id']] ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $totalNumbers[$id['id']] ?></td>
                            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php //echo $totalNumbers[$id['id']] ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>






    </table>



</div>








</div>
</body>
</html>
