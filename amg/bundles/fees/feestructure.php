<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->setCanPrint(false);
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_fee_structure");
$tblDemo->setItem($tool->transnoecho("fee_structure"));



$userBranchesData = $set->userBranches();
$userBranchArr = array();
foreach ($userBranchesData as $userBranch){
    $userBranchArr[] = $userBranch['id'];
}

$userBranches = implode(",",$userBranchArr);


$tblDemo->setCustomButtonStatus(true);
$tblDemo->addWhereClause(" WHERE branch_id IN (" . $userBranches . ")");



if($tpl->isCanAdd()){
    $tblDemo->setCustomButton($tblDemo->addNewButton("fees","addsessionfees",$tool->transnoecho("add_new")));
}

$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["branch_id"] = $tool->transnoecho("branch_id");
$labels["class_id"] = $tool->transnoecho("class_id");
$labels["session_id"] = $tool->transnoecho("session_id");
$labels["fee_type_id"] = $tool->transnoecho("fee_type_id");
$labels["fees"] = $tool->transnoecho("fees");

$tblDemo->displayAsArray($labels);





$tblDemo->defineRelationship("branch_id", "jb_branches", "id", "title", "position",""," AND jb_branches.id IN ( " . $userBranches . ")");
$tblDemo->defineRelationship("class_id", "jb_classes", "id", "title", "position");
$tblDemo->defineRelationship("session_id", "jb_sessions", "id", "title");
$tblDemo->defineRelationship("fee_type_id", "jb_fee_type", "id", "title", "position");
$tblDemo->setDisableDelete(false);

$tblDemo->addAjaxFilterBox("fee_type_id");
$tblDemo->addAjaxFilterBox("session_id");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->addAjaxFilterBox("branch_id");
$tblDemo->showCSVExportOption();

$tblDemo->disallowAdd();
$tblDemo->disallowEdit("branch_id");
$tblDemo->disallowEdit("class_id");
$tblDemo->disallowEdit("session_id");
$tblDemo->disallowEdit("fee_type_id");
$tblDemo->disallowEdit("fees");




$tblDemo->showTable();

$tpl->footer();