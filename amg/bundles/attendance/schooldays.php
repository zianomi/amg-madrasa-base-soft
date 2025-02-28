<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();
$tpl->setCanExport(false);


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_attand_date_log");
$tblDemo->setItem($tool->transnoecho("attand_date_log"));
$pr = $tblDemo->getPrefix();
$branches = $set->loginUserBranchesIds();

$labels = array();
$labels["branch_id"] = $tool->transnoecho("branch");
$labels["class_id"] = $tool->transnoecho("class");
$labels["session_id"] = $tool->transnoecho("session");
$labels["date"] = $tool->transnoecho("date");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");
$tblDemo->displayAsArray($labels);

$tblDemo->defineRelationship("branch_id", $pr."branches", "id", "title");

$tblDemo->defineRelationship("class_id", PR."classes", "id", "title");
$tblDemo->defineRelationship("session_id", PR."sessions", "id", "title");


$tblDemo->addAjaxFilterBox('branch_id');
$tblDemo->addAjaxFilterBox('class_id');
$tblDemo->addAjaxFilterBox('session_id');
$tblDemo->addAjaxFilterBox('date');
$tblDemo->modifyFieldWithClass("date","ajaxcruddatepicker");
$tblDemo->setLimit(30);
$tblDemo->showCSVExportOption();

$tblDemo->disallowEdit("branch_id");
$tblDemo->disallowEdit("class_id");
$tblDemo->disallowEdit("session_id");
$tblDemo->disallowEdit("date");

$tblDemo->displayAddFormTop();
$tblDemo->setDisableDelete(1);



$tblDemo->addOrderBy("ORDER BY jb_attand_date_log.date DESC");

$tblDemo->showTable();

$tpl->footer();