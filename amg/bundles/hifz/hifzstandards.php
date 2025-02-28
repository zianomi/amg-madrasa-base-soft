<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_hifz_statndards");
$tblDemo->setItem($tool->transnoecho("hifz_standards"));



$tblDemo->setDisableDelete(0);
$labels = array();
$labels["quran_id"] = $tool->transnoecho("quran_id");
$labels["year_id"] = $tool->transnoecho("year_id");
$labels["class_id"] = $tool->transnoecho("class_id");
$labels["month_number"] = $tool->transnoecho("month_number");
$labels["month_name"] = $tool->transnoecho("month_name");
$labels["required_pages"] = $tool->transnoecho("required_pages");
$labels["required_lines"] = $tool->transnoecho("required_lines");
$labels["required_lines_perday"] = $tool->transnoecho("required_lines_perday");
$labels["para_with_page"] = $tool->transnoecho("para_with_page");
$labels["total_days"] = $tool->transnoecho("total_days");
$labels["working_days"] = $tool->transnoecho("working_days");

$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");

$tblDemo->displayAsArray($labels);
$tblDemo->displayAddFormTop();
$tblDemo->defineRelationship("class_id", PR."classes", "id", "title");
$tblDemo->defineRelationship("quran_id", PR."quran", "id", "title");
$tblDemo->defineRelationship("hifz_year_id", PR."hifz_years", "id", "title");

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));

$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->addAjaxFilterBox("hifz_year_id");
$tblDemo->addAjaxFilterBox("quran_id");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();