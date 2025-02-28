<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();
$tblDemo = new AmgCrud();
$pr = $tblDemo->getPrefix();



$tblDemo->setDbTable("jb_subject_reports");
$tblDemo->setItem($tool->transnoecho("Report Subjects"));

$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");


$tblDemo->displayAsArray($labels);
$tblDemo->setFourFields(true);
$tblDemo->displayAddFormTop();

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));




$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();



$tpl->footer();

$mysqliConn->close();
