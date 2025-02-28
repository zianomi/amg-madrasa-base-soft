<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_sms_numbers");
$tblDemo->setItem($tool->transnoecho("sms_numbers"));


$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["group_id"] = $tool->transnoecho("created_user_id");
$labels["fone"] = $tool->transnoecho("fone");
$tblDemo->defineRelationship("group_id", PR."sms_groups", "id", "title");
$tblDemo->displayAsArray($labels);


$tblDemo->setFourFields(false);

$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();

$tblDemo->displayAddFormTop();

$tblDemo->setDisableDelete(0);
$tblDemo->showTable();

$tpl->footer();