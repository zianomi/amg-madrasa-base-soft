<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 9/21/2017
 * Time: 10:28 PM
 */
Tools::getModel("Dashboard");
Tools::getModel("ExamModel");
Tools::getLib("BaseWs");
$dashboard = new Dashboard();
$exm = new ExamModel();
$bs = new BaseWs();

$branchStudentCounts = $dashboard->countStudents();
$totalStudentCount = $dashboard->countTotalStudents();

$examDatas = $exm->examSummaryData(4,2);

echo '<pre>';print_r($bs->getSuccResponse($branchStudentCounts) );echo '</pre>';