<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$set = new SettingModel();

    $tableCols = array();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");


    Tools::getModel("StudentsModel");
   $stu = new StudentsModel();
    $qr->setAction(true);
    $qr->setCustomActions(array
            (
            array("label" => '<i class="icon-edit"></i>',"link" => Tools::makeLink("students","admissionfrom","",""))
           ,array("label" => '<i class="icon-user"></i>', "link" => Tools::makeLink("students","profile","",""))
    )
    );

    $qr->setDynamicParam(array("id" => "id"));

    $qr->setCols($tableCols);
    $qr->setData($stu->newAdmissions());


    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }


$tpl->renderBeforeContent();
$qr->searchContentAbove();




$qr->searchContentBottom();



$qr->contentHtml();


$tpl->footer();