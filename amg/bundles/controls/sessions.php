<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_sessions");
$tblDemo->setItem($tool->transnoecho("sessions"));
$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["published"] = $tool->transnoecho("published");
$labels["start_date"] = $tool->transnoecho("start_date");
$labels["end_date"] = $tool->transnoecho("end_date");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");

$tblDemo->displayAsArray($labels);

$tblDemo->displayAddFormTop();

$tblDemo->setFourFields(true);

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("start_date",array("type" => "text", "required" => "true", "methodApply" => "date"));
$tblDemo->setAmgInputDataType("end_date",array("type" => "text", "required" => "true", "methodApply" => "date"));

$tblDemo->modifyFieldWithClass("start_date", "date");
$tblDemo->modifyFieldWithClass("end_date", "date");
$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();
