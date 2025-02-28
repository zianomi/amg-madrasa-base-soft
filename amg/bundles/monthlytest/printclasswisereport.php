<?php

/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/13/2018
 * Time: 7:18 PM
 */
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$student_id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$date = isset($_GET['date']) ? $tool->ChangeDateFormat($_GET['date']) : '';
$to_date = isset($_GET['to_date']) ? $tool->ChangeDateFormat($_GET['to_date']) : '';


if (
    empty($branch)
    || empty($class)
    || empty($session)
    || empty($section)
    || empty($date)
    || empty($to_date)
) {
    echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
    exit;
}

if (!$tool->checkDateFormat($date)) {
    echo $tool->Message("alert", $tool->transnoecho("Invalid Date" . $date));
    return;
}

if (!$tool->checkDateFormat($to_date)) {
    echo $tool->Message("alert", $tool->transnoecho("Invalid To Date"));
    return;
}
Tools::getModel("StudentsModel");
$stu = new StudentsModel();



$param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session);
$data = $stu->StudentdSearchWithProfile($param);
$file = __DIR__ . DRS . "reportstudentprint.php";

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

foreach ($data as $row) {
    $student_id = $row['id'];
    $_GET['multi_print'] = "yes";
    include $file;
    echo '<div class="page-break" style="padding-bottom: 15px"></div>';
}
