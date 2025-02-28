<?php
/*Tools::getModel("BaseModel");
$db = new BaseModel();
$db = $db->getDb();
$mysqliConn = $db->link;*/

$mysqliConn = $tool->getMysqlCon();

Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();
$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_user_groups");
$tblDemo->setItem($tool->transnoecho("groups"));
$tblDemo->setFourFields(true);


$tblDemo->addWhereClause(" WHERE 1");
$tblDemo->defineRelationship("type_id", "jb_group_types", "id", "title");

$labels["title"] = $tool->transnoecho("title");
$labels["published"] = $tool->transnoecho("published");
$labels["position"] = $tool->transnoecho("position");
$labels["type_id"] = $tool->transnoecho("type");
$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));

$tblDemo->setCustomButtonStatus(true);

$tblDemo->addButtonToRow($tool->transnoecho("roles"),  Tools::makeLink("controls","moduletranslations&from=groups",$tpl->getFileCode(),$tpl->getFileAction()));


//$tblDemo->setCustomButton($tblDemo->addNewButton("controls","useredit&id=",$tool->transnoecho("add_new")));

$tblDemo->displayAsArray($labels);
$tblDemo->disallowEdit("username");
$tblDemo->disallowEdit("group_id");
$tblDemo->omitField('password');
$tblDemo->displayAddFormTop();
$tblDemo->modifyFieldWithClass("start_date", "datepicker");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
//$tblDemo->disallowAdd();
$tblDemo->omitPrimaryKey();
$tblDemo->addAjaxFilterBox('title');



$tblDemo->showTable();


$tpl->footer();

$mysqliConn->close();


