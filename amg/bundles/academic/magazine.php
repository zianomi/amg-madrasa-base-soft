<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");

$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_mag_magazine");
$tblDemo->setItem("Magazine");

$labels = array();
$labels["title"] = Tools::transnoecho("title");
$labels["position"] = Tools::transnoecho("position");
$labels["published"] = Tools::transnoecho("published");
$labels["date"] = Tools::transnoecho("date");
$labels["pdf"] = Tools::transnoecho("pdf");

$tblDemo->setFourFields(false);




$tblDemo->setAmgInputDataType("date",array("type" => "date", "required" => "true", "methodApply" => "hijridate"));
$tblDemo->setAmgInputDataType("idate",array("type" => "date", "required" => "true", "methodApply" => "date"));


$tblDemo->setLimit(30);

$tblDemo->displayAsArray($labels);
$tblDemo->displayAddFormTop();


$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("date");
$tblDemo->addAjaxFilterBox("idate");

$tblDemo->setExactSearchField("idate");
$tblDemo->setExactSearchField("date");

$tblDemo->showCSVExportOption();

$tblDemo->setFileUpload("image", "", "", array("image/png","image/jpg","image/jpeg"),array("path" => "", "required" => "true"));
$tblDemo->formatFieldWithFunction('image', 'makeImg');

//$tblDemo->setFileUpload("pdf", $completePath, $urlFile, array("pdf"),array("path" => $monthPath, "required" => "true"));
//$tblDemo->formatFieldWithFunction('pdf', 'makePdf');

$tblDemo->setFileUpload("pdf", "", "", array("application/pdf"),array("path" => "", "required" => "true"));
$tblDemo->formatFieldWithFunction('pdf', 'makePdf');




$tblDemo->addOrderBy("ORDER BY date DESC");

$tblDemo->showTable();
$tpl->footer();
