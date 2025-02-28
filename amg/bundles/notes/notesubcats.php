<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_notesubcats");
$tblDemo->setItem($tool->transnoecho("note_sub_cat"));


$pr = $tblDemo->getPrefix();
$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["note_cat_id"] = $tool->transnoecho("note_cat_id");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");
$tblDemo->defineRelationship("note_cat_id", $pr."notecats", "id", "title", "position", 1);
$tblDemo->displayAsArray($labels);

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->displayAddFormTop();

$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();