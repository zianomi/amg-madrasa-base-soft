<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_employess");
$tblDemo->setItem($tool->transnoecho("employess"));


$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["cnic"] = $tool->transnoecho("cnic");
$labels["fone"] = $tool->transnoecho("fone");
$labels["date_of_joining"] = $tool->transnoecho("date_of_joining");
$labels["designation"] = $tool->transnoecho("designation");
$labels["salary"] = $tool->transnoecho("salary");

$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");


$tblDemo->setAmgInputDataType("date_of_joining",array("type" => "date", "required" => "true", "methodApply" => "date"));

$tblDemo->displayAddFormTop();

$tblDemo->displayAsArray($labels);
$tblDemo->defineRelationship("designation", PR."destinations", "id", "title");

$tblDemo->addAjaxFilterBox('name');
$tblDemo->addAjaxFilterBox('salary');
$tblDemo->addAjaxFilterBox('date_of_joining');
$tblDemo->addAjaxFilterBox('designation');
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();