<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();



$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_zones");
$tblDemo->setItem($tool->transnoecho("zones"));

$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");

$tblDemo->displayAsArray($labels);
$tblDemo->setFourFields(true);
$tblDemo->displayAddFormTop();

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("position",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("start_date",array("type" => "date", "required" => "true", "methodApply" => "date"));
$tblDemo->setAmgInputDataType("end_date",array("type" => "date", "required" => "true", "methodApply" => "date"));


$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();



$tpl->footer();

$mysqliConn->close();
