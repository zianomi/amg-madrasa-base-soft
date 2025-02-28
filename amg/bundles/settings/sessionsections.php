<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();
$tpl->setCanEdit(false);
$tpl->setCanDelete(true);

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_session_sections");
$tblDemo->setItem($tool->transnoecho("session_sections"));

$labels = array();
$labels["session_id"] = $tool->transnoecho("sessios");
$labels["class_id"] = $tool->transnoecho("class");
$labels["section_id"] = $tool->transnoecho("section");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");

$tblDemo->displayAsArray($labels);

$tblDemo->defineRelationship("session_id", "jb_sessions", "id", "title");
$tblDemo->defineRelationship("section_id", "jb_sections", "id", "title");
$tblDemo->defineRelationship("class_id", "jb_classes", "id", "title");
$tblDemo->defineRelationship("branch_id", "jb_branches", "id", "title");

$tblDemo->omitPrimaryKey();

$tblDemo->disallowAdd();

$tblDemo->setLimit(30);
$tblDemo->addAjaxFilterBox("session_id");
$tblDemo->addAjaxFilterBox("section_id");
$tblDemo->addAjaxFilterBox("class_id");
//$tblDemo->showCSVExportOption();
$tblDemo->showTable();

$tpl->footer();