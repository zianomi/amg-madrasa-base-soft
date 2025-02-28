<?php
die("Page Removed");
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_quran");
$tblDemo->setItem($tool->transnoecho("quran_reading_standards"));

$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["para_number"] = $tool->transnoecho("para_number");
$labels["total_pages"] = $tool->transnoecho("total_pages");
$labels["total_lines"] = $tool->transnoecho("total_lines");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");

$tblDemo->displayAsArray($labels);

$tblDemo->defineRelationship("class_id", "jb_classes", "id", "title");

$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();