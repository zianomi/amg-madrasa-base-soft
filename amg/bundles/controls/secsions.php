<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_sections");
$tblDemo->setItem($tool->transnoecho("sections"));

$labels = array();
$labels["published"] = $tool->transnoecho("published");
//$labels["short_name"] = $tool->transnoecho("short_name");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");
$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->displayAsArray($labels);
$tblDemo->displayAddFormTop();
$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();
