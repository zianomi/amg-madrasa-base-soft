<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
Tools::getModel("StudentsModel");
$stu = new StudentsModel();
$tblDemo = new AmgCrud();

$id = isset($_GET['student_id']) ? $tool->GetInt($_GET['student_id']) : '';
$branchStuid = $set->getID($id);

if(empty($branchStuid)){
    echo $tool->Message("alert",$tool->transnoecho("no_student_found"));
    exit;
}


$tblDemo->setDbTable("jb_results");
$tblDemo->setItem($tool->transnoecho("results"));
$pr = $tblDemo->getPrefix();
$labels = array();
$tpl->setCanEdit(false);
$tpl->setCanAdd(false);
$tpl->setCanPrint(false);



$labels = array();
$labels["branch_id"] = $tool->transnoecho("branch");
$labels["class_id"] = $tool->transnoecho("class");
$labels["section_id"] = $tool->transnoecho("section");
$labels["session_id"] = $tool->transnoecho("session");
$labels["subject_id"] = $tool->transnoecho("subject");
$labels["exam_id"] = $tool->transnoecho("exam");
$labels["student_id"] = $tool->transnoecho("name");
$labels["numbers"] = $tool->transnoecho("numbers");

$tblDemo->defineRelationship("class_id", $pr."classes", "id", "title", "position", 1);
$tblDemo->defineRelationship("branch_id", $pr."branches", "id", "title", "position", 1);
$tblDemo->defineRelationship("section_id", $pr."sections", "id", "title", "position", 1);
$tblDemo->defineRelationship("session_id", $pr."sessions", "id", "title", "", 1);
$tblDemo->defineRelationship("student_id", $pr."students", "id", "name");
$tblDemo->defineRelationship("exam_id", $pr."exam_names", "id", "title");
$tblDemo->defineRelationship("subject_id", $pr."subjects", "id", "title");

$tblDemo->disallowEdit("session_id");
$tblDemo->disallowEdit("exam_id");
$tblDemo->disallowEdit("subject_id");
$tblDemo->disallowEdit("student_id");
$tblDemo->disallowEdit("branch_id");
$tblDemo->disallowEdit("class_id");
$tblDemo->disallowEdit("section_id");
$tblDemo->disallowEdit("numbers");
$tblDemo->disallowEdit("date");
$tblDemo->addWhereClause("WHERE student_id = $id");
$tblDemo->addOrderBy(" ORDER BY date DESC");
$tblDemo->displayAsArray($labels);
$tblDemo->displayAddFormTop();
$tblDemo->setDisableDelete(0);
$tblDemo->addAjaxFilterBox("exam_id");
$tblDemo->addAjaxFilterBox("session_id");
$tblDemo->addAjaxFilterBox("student_id");
$tblDemo->showCSVExportOption();
$tpl->renderBeforeContent();
$tblDemo->showTable();
$tpl->footer();