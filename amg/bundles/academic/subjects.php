<?php
exit;
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_subjects");
$tblDemo->setItem($tool->transnoecho("subject"));

$pr = $tblDemo->getPrefix();




$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["class_id"] = $tool->transnoecho("class");
$tblDemo->setExactSearchField('class_id');


$tblDemo->displayAsArray($labels);
$tblDemo->defineRelationship("class_id", $pr . "classes", "id", "title", "position", 1);

$tblDemo->displayAddFormTop();



$tblDemo->disallowDelete();

$tblDemo->omitFieldCompletely("branch_id");
$tblDemo->addValueOnInsert("branch_id", 0);

$tblDemo->omitFieldCompletely("subject_type");
$tblDemo->addValueOnInsert("subject_type", "online");

$tblDemo->omitFieldCompletely("subject_group_id");
$tblDemo->addValueOnInsert("subject_group_id", 0);



$tblDemo->omitFieldCompletely("report_subject_id");
$tblDemo->addValueOnInsert("report_subject_id", 1);


/*$tblDemo->formatFieldWithFunction('subject_type', 'typeFunc');
$tblDemo->defineAllowableValues("subject_type", $main_sub);*/



$tblDemo->setAmgInputDataType("title", array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("position", array("type" => "text", "required" => "true", "methodApply" => "text"));


$tblDemo->addWhereClause(" WHERE jb_subjects.subject_type = 'online'");


$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->showCSVExportOption();


$tblDemo->setLimit(100);

$tblDemo->addOrderBy(" ORDER BY class_id,position ASC");

$tpl->renderBeforeContent();

$tblDemo->showTable();



$tpl->footer();
