<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");


$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_gl_branch_codes");
$tblDemo->setItem($tool->transnoecho("gl_branch_codes"));


$labels = array();
$labels["branch_id"] = $tool->transnoecho("branch");
$labels["glcode"] = $tool->transnoecho("glcode");

$tblDemo->displayAsArray($labels);


$tblDemo->displayAddFormTop();

$tblDemo->defineRelationship("branch_id", PR."branches", "id", "title", "position", 1, " AND jb_branches.published = 1");


$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->showTable();



$tpl->footer();

