<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_branch_operators");
$tblDemo->setItem($tool->transnoecho("branch_operators"));


$labels = array();
$labels["branch_id"] = $tool->transnoecho("branch");
$labels["user_id"] = $tool->transnoecho("user");

$tblDemo->displayAsArray($labels);

$tblDemo->setFourFields(false);

$tblDemo->setDisableDelete(0);

$tblDemo->displayAddFormTop();

$tblDemo->addAjaxFilterBox("branch_id");
$tblDemo->showCSVExportOption();
$tblDemo->defineRelationship("branch_id", "jb_branches", "id", "title", "position", 1, " AND jb_branches.published = 1");
$tblDemo->defineRelationship("user_id", "jb_users", "id", "name");
$tblDemo->showTable();

$tpl->footer();