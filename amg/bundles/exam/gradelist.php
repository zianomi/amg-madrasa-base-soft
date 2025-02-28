<?php
$id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;

$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
Tools::getModel("ExamModel");
$exm = new ExamModel();

$tpl->renderBeforeContent();

if (!empty($id)) {
    echo $tool->MessageOnly("succ", $exm->getFormulaName($id));
} else {
    echo 'Error';
    exit;
}

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_exam_grade_formula_list");
$tblDemo->setItem($tool->transnoecho("grade_list"));
$tblDemo->setFourFields(true);

$labels = array();
$labels["min_val"] = $tool->transnoecho("min_value");
$labels["max_val"] = $tool->transnoecho("max_value");
$labels["grade"] = $tool->transnoecho("grade");
$labels["comments"] = $tool->transnoecho("comments");


$tblDemo->displayAsArray($labels);


$tblDemo->setAmgInputDataType("min_val", array("type" => "number", "methodApply" => "number"));
$tblDemo->setAmgInputDataType("max_val", array("type" => "number", "required" => "true", "methodApply" => "number"));

$tblDemo->setDisableDelete(0);

$tblDemo->addWhereClause("WHERE formula_id = $id");


$tblDemo->omitFieldCompletely("formula_id");
$tblDemo->addValueOnInsert("formula_id", $id);

$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tblDemo->disallowEdit("id");
$tblDemo->setFourFields(false);

$tblDemo->displayAddFormTop();



function calcFunc($data)
{
    switch ($data) {
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
    ,
    array("2", "Ceil")
    ,
    array("3", "Floor")
);

$tblDemo->defineAllowableValues("formula_calculation", $calArr);
$tblDemo->formatFieldWithFunction('formula_calculation', 'calcFunc');

$tblDemo->showTable();


$tpl->footer();
