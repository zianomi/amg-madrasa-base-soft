<?php
//require_once __DIR__ . DIRECTORY_SEPARATOR . "data.php";
//exit;
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_classes");
$tblDemo->setItem($tool->transnoecho("classes"));
$tblDemo->setFourFields(true);
$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["class_type"] = $tool->transnoecho("class_type");
$labels["module_id"] = $tool->transnoecho("module_id");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");
$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->addWhereClause(" WHERE 1");
$tblDemo->defineRelationship("class_type", "jb_classs_type", "class_type", "class_value", "class_value",1," AND  lang =" . Tools::getLangId());
$tblDemo->defineRelationship("module_id", "jb_class_modules", "id", "title");
$tblDemo->displayAsArray($labels);
$tblDemo->displayAddFormTop();
$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->setLimit(50);
$tblDemo->showTable();
$tpl->footer();
