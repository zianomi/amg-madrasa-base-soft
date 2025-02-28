<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_students");
$tblDemo->setItem($tool->transnoecho("students"));
$labels = array();
$labels["name"] = $tool->transnoecho("name");
$labels["fname"] = $tool->transnoecho("fname");
$labels["grnumber"] = $tool->transnoecho("grnumber");

$resBrnach = $set->loginUserBranches();

foreach ($resBrnach as $row){
    $branches[] = $row['branch_id'];
}


$branchIds = implode(",",$branches);

$tblDemo->addWhereClause("WHERE branch_id IN ($branchIds)");

$tblDemo->addAjaxFilterBox("id");
$tblDemo->addAjaxFilterBox("branch_id");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->addAjaxFilterBox("section_id");

$tblDemo->defineRelationship("branch_id","jb_branches","id","title");
$tblDemo->defineRelationship("class_id","jb_classes","id","title");
$tblDemo->defineRelationship("section_id","jb_sections","id","title");

//$tblDemo->disallowEdit("name");
//$tblDemo->disallowEdit("fname");
$tblDemo->disallowEdit("branch_id");
$tblDemo->disallowEdit("class_id");
$tblDemo->disallowEdit("section_id");
$tblDemo->disallowEdit("session_id");
$tblDemo->disallowEdit("gender");
$tblDemo->addAjaxFilterBox("branch_id");
$tblDemo->omitFieldCompletely("session_id");
$tblDemo->omitFieldCompletely("parents_id");
$tblDemo->omitFieldCompletely("doa");
$tblDemo->omitFieldCompletely("eng_name");
$tblDemo->omitFieldCompletely("eng_fname");
$tblDemo->omitFieldCompletely("student_status");
//$tblDemo->exactSearchField("id");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();