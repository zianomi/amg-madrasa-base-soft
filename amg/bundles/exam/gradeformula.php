<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");


$tpl->renderBeforeContent();

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_exam_grade_formulas");
$tblDemo->setItem($tool->transnoecho("grade_formula"));
$tblDemo->setFourFields(true);

$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["formula_calculation"] = $tool->transnoecho("calculation");


$tblDemo->displayAsArray($labels);

//$tblDemo->setBranchFilter();




$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowEdit("id");
$tblDemo->setFourFields(false);

$tblDemo->displayAddFormTop();

if($tpl->isCanEdit()){
    $tblDemo->addButtonToRow($tool->transnoecho("define"),  Tools::makeLink("exam","gradelist",$tpl->getFileCode(),$tpl->getFileAction()));
}

function calcFunc($data){
    switch($data){
        case 1:
            $txt = 'Round';
            break;

        case 2:
            $txt = 'Ceil';
        break;

        case 3:
            $txt = 'Floor';
        break;
    }
    return $txt;
}

$calArr = array(
    array("1", "Round")
, array("2", "Ceil")
, array("3", "Floor")
);

$tblDemo->defineAllowableValues("formula_calculation", $calArr);
$tblDemo->formatFieldWithFunction('formula_calculation', 'calcFunc');

$tblDemo->showTable();


$tpl->footer();

