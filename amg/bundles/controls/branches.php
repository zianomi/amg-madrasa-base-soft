<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");


$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_branches");
$tblDemo->setItem($tool->transnoecho("branches"));
$tblDemo->setFourFields(true);

$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["short_name"] = $tool->transnoecho("short_name");
$labels["zone_id"] = $tool->transnoecho("zone_id");
$labels["branch_code"] = $tool->transnoecho("branch_code");
$labels["branch_address"] = $tool->transnoecho("branch_address");
$labels["branch_nazim"] = $tool->transnoecho("branch_nazim");
$labels["branch_fone"] = $tool->transnoecho("branch_fone");
$labels["branch_date"] = $tool->transnoecho("branch_date");
$labels["email"] = $tool->transnoecho("email");
$labels["latitude"] = $tool->transnoecho("latitude");
$labels["longitude"] = $tool->transnoecho("longitude");


$tblDemo->displayAsArray($labels);

$tblDemo->defineRelationship("zone_id", "jb_zones", "id", "title", "position", 1, " AND jb_zones.published = 1 ");

$tblDemo->displayAddFormTop();

$tblDemo->setAmgInputDataType("title", array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("short_name", array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("eng_name", array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("branch_date", array("type" => "date", "required" => "true", "methodApply" => "date"));


$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("zone_id");
$tblDemo->showCSVExportOption();
$tblDemo->showTable();



$tpl->footer();
