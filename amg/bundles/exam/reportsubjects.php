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
$labels["numbers"] = $tool->transnoecho("numbers");
$labels["is_core"] = $tool->transnoecho("is_core");


$tblDemo->displayAsArray($labels);
$tblDemo->setFourFields(true);
$tblDemo->displayAddFormTop();
$tblDemo->disallowEdit("id");

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));

$tblDemo->defineAllowableValues("is_core", $tblDemo->softStatus());
$tblDemo->formatFieldWithFunction('is_core', 'StatusField');

$tblDemo->setFourFields(false);

$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();



$tpl->footer();

$mysqliConn->close();
