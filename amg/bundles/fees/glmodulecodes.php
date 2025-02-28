<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");


$tpl->renderBeforeContent();



$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_gl_module_codes");
$tblDemo->setItem($tool->transnoecho("module_codes"));

$labels = array();
$labels["class_id"] = $tool->transnoecho("class");
$labels["gl_module_id"] = $tool->transnoecho("gl_module");

$tblDemo->displayAsArray($labels);


$tblDemo->displayAddFormTop();

$tblDemo->defineRelationship("class_id", PR."classes", "id", "title", "position");
//$tblDemo->defineRelationship("gl_module_id", PR."gl_modules", "id", "title", "position", 1, " AND jb_gl_modules.published = 1");
$tblDemo->defineRelationship("gl_module_id", PR."gl_modules", "id", "title", "position");


$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->showTable();



$tpl->footer();

