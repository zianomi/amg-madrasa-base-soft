<?php
$zone = (isset($_GET['zone'])) ? $tool->GetExplodedInt($_GET['zone']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';

Tools::getModel("StudentsModel");
$stu = new StudentsModel();


Tools::getLib("TemplateForm");
$tpf = new TemplateForm();

$date = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT);
$dateEnd = date("Y-m-t", strtotime($date));
$dateStart = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT).'-01';

if(!$tool->checkDateFormat($dateStart)){
    echo  $tool->Message("alert",$tool->transnoecho("date_invalid"));
    exit;
}

if(!$tool->checkDateFormat($dateEnd)){
    echo  $tool->Message("alert",$tool->transnoecho("date_invalid"));
    exit;
}


if(empty($zone)){
    echo  $tool->Message("alert",$tool->transnoecho("zone_required"));
    exit;
}


$classModules = $set->getClassModules();
$classess = $set->allClasses();

$branches = $set->getZoneBranches(array("zone" => $zone));
$classesArr = array();

$stuCounts = $stu->countZoneStudents($zone);

foreach ($classess as $class){
    if(!empty($class['module_id'])){
        $classesArr[$class['module_id']][] = array("id" => $class['id'], "title" => $class['title']);
    }

}


$countArr = array();
$branchMale = array();
$branchFemale = array();

$countTerminates = array();
$countCompletes = array();


foreach ($stuCounts as $row){
    $countArr[$row['branch_id']][$row['class_id']][$row['gender']] = $row['tot'];

}

$terminates = $stu->countTermintedStudents($dateStart,$dateEnd);
$completes = $stu->countHifzCompletion($dateStart,$dateEnd);

foreach ($terminates as $terminate){
    $countTerminates[$terminate['old_branch']][$terminate['old_class']] = $terminate['tot'];
}


foreach ($completes as $complete){
    $countCompletes[$complete['branch_id']][$complete['gender']] = $complete['tot'];
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($_GET['year']) && isset($_GET['months'])) echo $_GET['year'] . '-'.  $_GET['months']  ?> Result</title>


    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-rtl.css">
    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-responsive-rtl.css">

    <!--<script src="../assets/js/combined.js"></script>-->

    <style type="text/css">

        @media print{
            body {-webkit-print-color-adjust: economy | exact background: #fff; font-family:"Jameel Noori Nastaleeq"; height: 800px !important; }

            @page {size: A4 landscape; }


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

        body{font-family:"Jameel Noori Nastaleeq"; text-align: center;}

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
            border: 1px solid black; text-align: center
        }
        .tables tr:first-child th {
            border-top: 0; text-align: center;
        }
        .tables tr:last-child td {
            border-bottom: 0; text-align: center;
        }
        .tables tr td:first-child,
        .tables tr th:first-child {
            border-left: 0; text-align: center
        }
        .tables tr td:last-child,
        .tables tr th:last-child {
            border-right: 0; text-align: center
        }

    </style>
</head>
<body>



<div id="printReady">


    <table border="0" align="center" cellpadding="0" cellspacing="0" style="width: 1300px !important;">


        <tr>

            <td valign="top">
                <table style="width:100% !important">
                    <tr style="border:none !important;">
                        <td style="border:none !important; width: 20%"><img style="margin-right:70px" src="<?php echo $tool->getWebUrl() ?>/img/logo_report.png" height="44" width="241" /></td>
                        <td style="border:none !important; width: 60%; text-align: center; font-size: 35px;">ماہانا تعلیمی جائزہ برائے طلباء/طالبات  <?php if(isset($_GET['class']))  echo $tool->GetExplodedVar($_GET['class']) ?> </td>
                        <td style="border:none !important; width: 20%"><img src="<?php echo $tool->getWebUrl() ?>/img/logo2.png" width="57" height="59" /></td>

                    </tr>
                </table>
            </td>

        </tr>


        <tr>
            <td valign="top" style="text-align: justify">
                <table style="width:100% !important;">
                    <tr style="border:none !important;">
                        <td>بابت ماہ/سال:</td>
                        <td><?php echo $tpf->UrduMonthName($month) ?>/<?php echo $year ?></td>
                        <td>زون:</td>
                        <td><?php echo $tool->GetExplodedVar($_GET['zone']) ?></td>
                    </tr>
                </table>
            </td>

        </tr>


        <tr>
            <td valign="top" style="

            text-align: justify">
                <table style="width:100% !important;" class="tables">

                    <tr>
                        <td>#</td>
                        <td style="text-align: center;  vertical-align: middle; width: 16%">شاخ</td>
                        <?php $leaveColsPan = 0; ?>
                        <?php foreach($classModules as $classModule){
                            $colsPan = 3 * (count($classesArr[$classModule['id']]));
                            $leaveColsPan += count($classesArr[$classModule['id']]);
                            ?>
                            <td colspan="<?php echo $colsPan ?>"><?php echo $classModule['title'] ?></td>
                        <?php } ?>
                        <td colspan="6">میزان طلبہ</td>
                        <td colspan="<?php echo $leaveColsPan ?>">ترک مدرسہ</td>
                        <td colspan="6">حفاظ کرام</td>
                        <td style="text-align: center; vertical-align: middle; width: 10%" rowspan="3">کیفیت</td>
                    </tr>

                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php $countSubjectTd = 0; ?>
                        <?php foreach($classModules as $classModule){ ?>
                        <?php foreach($classesArr[$classModule['id']] as $classKey){ ?>
                        <td colspan="3"><?php echo $classKey['title'] ?></td>
                        <?php $countSubjectTd ++; ?>
                        <?php } ?>
                        <?php } ?>
                        <td colspan="4">میزان</td>
                        <td colspan="2">کل میزان</td>
                        <td colspan="<?php echo $countSubjectTd; $countSubjectTd = 0; ?>"></td>
                        <td colspan="3">اس ماہ حفظ والے</td>
                        <td colspan="3">آغازسے تا حال</td>

                    </tr>


                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <?php $countSubjectTd = 0; ?>
                        <?php foreach($classModules as $classModule){ ?>
                            <?php foreach($classesArr[$classModule['id']] as $classKey){ ?>
                                <td>بچے</td>
                                <td>بچیاں</td>
                                <td>میزان</td>
                                <?php $countSubjectTd ++; ?>
                            <?php } ?>
                        <?php } ?>
                        <td colspan="2">بچے	</td>
                        <td colspan="2">بچیاں</td>
                        <td colspan="2">**</td>
                        <?php foreach($classModules as $classModule){ ?>
                            <?php foreach($classesArr[$classModule['id']] as $classKey){ ?>
                                <td style="font-size: 11px;"><?php echo $classKey['title'] ?></td>
                            <?php } ?>
                        <?php } ?>
                        <td>بچے</td>
                        <td>بچیاں</td>
                        <td>میزان</td>
                        <td>بچے</td>
                        <td>بچیاں</td>
                        <td>میزان</td>

                    </tr>


                    <?php
                    $i=0;
                    foreach ($branches as $branch){
                        $i++;
                    ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td style="text-align: right;"><?php echo $branch['title'] ?></td>
                        <?php foreach($classModules as $classModule){ ?>
                            <?php foreach($classesArr[$classModule['id']] as $classKey){ ?>
                                <td><?php
                                    if(isset($countArr[$branch['id']][$classKey['id']][1])){

                                        $totalMale = $countArr[$branch['id']][$classKey['id']][1];
                                        echo $totalMale;
                                        @$branchMale[$branch['id']][] += $totalMale;
                                    }
                                    else{
                                        echo "-";
                                    }
                                     ?></td>
                                <td><?php
                                    if(isset($countArr[$branch['id']][$classKey['id']][2])){
                                        $totalFemale = $countArr[$branch['id']][$classKey['id']][2];
                                        echo $totalFemale;
                                        @$branchFemale[$branch['id']][] += $totalFemale;
                                    }
                                    else{
                                        echo "-";
                                    }
                                    ?></td>
                                <td><?php
                                        if(isset($countArr[$branch['id']][$classKey['id']])){
                                            echo array_sum($countArr[$branch['id']][$classKey['id']]);
                                        }
                                     ?></td>
                            <?php } ?>
                        <?php } ?>
                        <td colspan="2"><?php echo @array_sum($branchMale[$branch['id']]) ?></td>
                        <td colspan="2"><?php echo @array_sum($branchFemale[$branch['id']]) ?></td>
                        <td colspan="2"><?php echo @(array_sum($branchMale[$branch['id']]) + array_sum($branchFemale[$branch['id']])) ?></td>
                        <?php foreach($classModules as $classModule){ ?>
                        <?php foreach($classesArr[$classModule['id']] as $classKey){
                            ?>
                            <td style="text-align: center;"><?php
                                if(isset($countTerminates[$branch['id']][$classKey['id']])){
                                    echo $countTerminates[$branch['id']][$classKey['id']];
                                }
                                else{
                                    echo '-';
                                }

                            ?></td>
                        <?php } ?>
                        <?php } ?>

                        <td>
                            <?php
                            $totalFazil = 0;
                            if(isset($countCompletes[$branch['id']][1])){
                                $totalFazil += $countCompletes[$branch['id']][1];
                                echo $countCompletes[$branch['id']][1];
                            }
                            else{
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?php
                            if(isset($countCompletes[$branch['id']][2])){
                                $totalFazil += $countCompletes[$branch['id']][2];
                                echo $countCompletes[$branch['id']][2];
                            }
                            else{
                                echo '-';
                            }
                            ?></td>
                        <td><?php echo $totalFazil ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php } ?>



                </table>
            </td>
        </tr>













    </table>



</div>

</body>
</html>