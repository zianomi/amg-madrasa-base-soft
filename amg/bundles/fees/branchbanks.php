<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$userBranchesData = $set->userBranches();
$userBranchArr = array();
foreach ($userBranchesData as $userBranch){
    $userBranchArr[] = $userBranch['id'];
}

$userBranches = implode(",",$userBranchArr);



$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_branch_banks");
$tblDemo->setItem($tool->transnoecho("branch_banks"));



$tblDemo->defineRelationship("branch_id", PR."branches", "id", "title", "position",1," AND jb_branches.id IN ( " . $userBranches . ")");
$tblDemo->defineRelationship("module_id", PR."gl_modules", "id", "title", "position",1,"");
$tblDemo->addWhereClause(" WHERE branch_id IN (" . $userBranches . ")");
$labels = array();
$labels["branch_id"] = $tool->transnoecho("branch");
$labels["branch_bank"] = $tool->transnoecho("branch_bank");
$labels["branch_bank_title"] = $tool->transnoecho("branch_bank_title");
$labels["branch_bank_ac_number"] = $tool->transnoecho("branch_bank_ac_number");
$labels["branch_bank_code"] = $tool->transnoecho("branch_bank_code");
$labels["branch_bank_phone"] = $tool->transnoecho("branch_bank_phone");
$labels["branch_bank_short_name"] = $tool->transnoecho("branch_bank_short_name");
$labels["branch_bank_account_title"] = $tool->transnoecho("branch_bank_account_title");
$labels["module_id"] = $tool->transnoecho("gl_module");
$labels["published"] = $tool->transnoecho("published");
$labels["bank_gl_code"] = $tool->transnoecho("bank_gl_code");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");
$tblDemo->displayAsArray($labels);

$tblDemo->displayAddFormTop();



$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();