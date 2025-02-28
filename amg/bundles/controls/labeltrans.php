<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_unique_labels");
$tblDemo->setItem($tool->transnoecho("unique_labels"));
$labels = array();
$labels["label_key"] = $tool->transnoecho("label_key");
$labels["label_title"] = $tool->transnoecho("label_title");
$labels["lang_id"] = $tool->transnoecho("lang");

$tblDemo->disallowEdit("label_key");
$tblDemo->disallowEdit("lang_id");

$tblDemo->omitPrimaryKey();


$tblDemo->displayAsArray($labels);

$tblDemo->displayAddFormTop();

$tblDemo->defineRelationship("lang_id", "jb_languages", "id", "title");

$tblDemo->disallowAdd();

$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();