<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();
$tpl->setCanEdit(false);
$tpl->setCanDelete(true);

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_session_classes");
$tblDemo->setItem($tool->transnoecho("session_classes"));
$labels = array();
$labels["session_id"] = $tool->transnoecho("sessios");
$labels["class_id"] = $tool->transnoecho("class");
$labels["branch_id"] = $tool->transnoecho("branch");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");

$tblDemo->displayAsArray($labels);

$tblDemo->defineRelationship("session_id", "jb_sessions", "id", "title");
$tblDemo->defineRelationship("class_id", "jb_classes", "id", "title");
$tblDemo->defineRelationship("branch_id", "jb_branches", "id", "title");

$tblDemo->omitPrimaryKey();

$tblDemo->disallowAdd();


$tblDemo->addAjaxFilterBox("session_id");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->addAjaxFilterBox("branch_id");
//$tblDemo->showCSVExportOption();
//$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();