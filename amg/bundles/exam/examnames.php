<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_exam_names");
$tblDemo->setItem($tool->transnoecho("exam_names"));



function examTemplate($data){
    switch($data){
        case "first_monthly":
            $txt = 'First Monthly';
            break;

        case "mid_term":
            $txt = 'Mid Term Exam';
        break;

        case "second_monthly":
            $txt = 'Second Monthly Exam';
        break;

        case "final_exam":
            $txt = 'Final Exam';
            break;

    }
    return $txt;
}


$mainTemp = array(
    array("first_monthly","First Monthly")
,array("mid_term","Mid Term Exam")
,array("second_monthly","Second Monthly Exam")
,array("final_exam","Final Exam")
);


$tblDemo->defineAllowableValues("template", $mainTemp);
$tblDemo->formatFieldWithFunction('template', 'examTemplate');

$pr = $tblDemo->getPrefix();
$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$tblDemo->displayAsArray($labels);
$tblDemo->displayAddFormTop();
$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->disallowDelete();
$tblDemo->addAjaxFilterBox("title");
$tblDemo->showCSVExportOption();
$tpl->renderBeforeContent();
$tblDemo->showTable();
$tpl->footer();
