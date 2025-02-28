<?php
$mysqliConn = $tool->getMysqlCon();

Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();


$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_users");
$tblDemo->setItem($tool->transnoecho("users"));
$tblDemo->setFourFields(true);

$labels["name"] = $tool->transnoecho("name");
$labels["username"] = $tool->transnoecho("username");
$labels["phone_number"] = $tool->transnoecho("phone_number");
$labels["address"] = $tool->transnoecho("address");
$labels["group_id"] = $tool->transnoecho("group_id");
$labels["published"] = $tool->transnoecho("published");
$labels["branch_id"] = $tool->transnoecho("Branch");

$tblDemo->defineRelationship("group_id", "jb_user_groups", "id", "title");

$tblDemo->setCustomButtonStatus(true);

$tblDemo->omitFieldCompletely("user_type");
$tblDemo->omitFieldCompletely("user_img");
//$tblDemo->defineRelationship("branch_id", "jb_branches", "id", "title");


if($tpl->isCanEdit()){
    $tblDemo->addButtonToRow($tool->transnoecho("edit"),  Tools::makeLink("controls","useredit",$tpl->getFileCode(),$tpl->getFileAction()));
    $tblDemo->addButtonToRow($tool->transnoecho("roles"),  Tools::makeLink("controls","moduletranslations",$tpl->getFileCode(),$tpl->getFileAction()));
}

if($tpl->isCanAdd()){
    $tblDemo->setCustomButton($tblDemo->addNewButton("controls","useredit&id=",$tool->transnoecho("add_new")));
}


$tblDemo->addWhereClause(" WHERE user_type != 'teacher'");


$tblDemo->displayAsArray($labels);
$tblDemo->disallowEdit("username");
$tblDemo->disallowEdit("group_id");
$tblDemo->omitField('password');
$tblDemo->displayAddFormTop();
$tblDemo->modifyFieldWithClass("start_date", "datepicker");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->disallowAdd();
$tblDemo->omitPrimaryKey();
$tblDemo->addAjaxFilterBox('name');
$tblDemo->addAjaxFilterBox('username');
$tblDemo->addAjaxFilterBox('phone_number');
$tblDemo->addAjaxFilterBox('address');



$tblDemo->showTable();


$tpl->footer();

$mysqliConn->close();


