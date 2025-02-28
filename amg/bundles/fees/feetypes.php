<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_fee_type");
$tblDemo->setItem($tool->transnoecho("fee_type"));


$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["title_en"] = $tool->transnoecho("title_en");
$labels["duration_type"] = $tool->transnoecho("duration_type");
$labels["gl_code"] = $tool->transnoecho("gl_code");
$labels["created_user_id"] = $tool->transnoecho("created_user_id");
$labels["updated_user_id"] = $tool->transnoecho("updated_user_id");
$labels["created"] = $tool->transnoecho("created");
$labels["updated"] = $tool->transnoecho("updated");
$tblDemo->displayAsArray($labels);

$tblDemo->displayAddFormTop();

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));


function Statusfee($data){
    switch($data){
        case 'monthly':
            return 'Monthly';
        break;
        case 'once':
            return 'Once';
        break;
        case 'half_yearl':
            return 'Half Yearly';
        break;
        default:
            return 'Yearly';
    }

}


$feeTypeStatus = array(
array("monthly","Monthly"),
array("yearly","Yearly"),
array("half_yearl","Half Yearly"),
array("once","Once")
);



$tblDemo->defineAllowableValues("duration_type", $feeTypeStatus);


$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowDelete();
$tblDemo->showTable();

$tpl->footer();