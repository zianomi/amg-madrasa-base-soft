<?php
/* @var $tool Tools */
/* @var $tpl Template */
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_timetables");
$tblDemo->setItem($tool->transnoecho("timetables"));


$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$tblDemo->displayAsArray($labels);

$tblDemo->displayAddFormTop();

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));

$tblDemo->addButtonToRow($tool->transnoecho("timetable"),  Tools::makeLink("academic","timestructure&_chk=1",$tpl->getFileCode(),$tpl->getFileAction()));



$tblDemo->setFourFields(false);

$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();
