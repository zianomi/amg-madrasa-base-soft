<?php
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$hifz_year = (isset($_GET['hifz_year'])) ? $tool->GetExplodedInt($_GET['hifz_year']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';

include_once __DIR__ . DIRECTORY_SEPARATOR . 'dataprintfortest.php';

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


$Attand = array();

$date = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT);
$dateEnd = date("Y-m-t", strtotime($date));
$dateStart = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT).'-01';



Tools::getLib("TemplateForm");
Tools::getModel("MonthlyTestModel");
Tools::getModel("AttendanceModel");
$tpf = new TemplateForm();
$test = new MonthlyTestModel();
$atd = new AttendanceModel();
Tools::getModel("StudentsModel");
$stu = new StudentsModel();

$param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session);

$rows = $stu->StudentdSearchWithProfile($param);
$subs = $test->monthlyTestSubjects($class);



$dateSyll = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT).'-25';
$date = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT);
$date = date("Y-m-t", strtotime($date));
$dateStart = $year.'-'.str_pad($month, 2, 0, STR_PAD_LEFT).'-01';

if(!$tool->checkDateFormat($dateStart)){
    echo  $tool->Message("alert",$tool->transnoecho("date_invalid"));
    exit;
}

if(!$tool->checkDateFormat($date)){
    echo  $tool->Message("alert",$tool->transnoecho("date_invalid"));
    exit;
}



$param = array("date" => $dateStart, "to_date" => $date, "branch" => $branch,"class" => $class, "section" => $section, "session" => $session);

$param['start'] = $dateStart;
$param['end'] = $date;
$resAttand = $atd->stuAttand($param);
foreach($resAttand as $rowAttand){
    $Attand[$rowAttand['student_id']] = $rowAttand;
}


$resTotalAttand = $atd->countNumberOfAttanbdDays($dateStart,$date,$branch,$class);

$newRows = array_chunk($rows,15,true);

for ($i=0; $i< count($newRows); $i++){
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

    if( ($i+1) == count($newRows)){
        echo footer();
    }

    echo '<p style="page-break-before: always">';

}















