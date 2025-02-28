<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");

function langField($data){
        if($data == 'en'){
            return $tool->transnoecho("english");
        }
        else{
            return $tool->transnoecho("urdu");
        }
    }

function LangArray(){
        return array(
            array("ur",$tool->transnoecho("urdu")),
            array("en",$tool->transnoecho("english"))
            );

                //array("ur"=>"اردو","en"=>"انگریزی");
    }

$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_exam_labels");
$tblDemo->setItem($tool->transnoecho("labels"));


$tblDemo->setFourFields(false);

    $tblDemo->displayAs("exam_id", $tool->transnoecho("exam"));
    $tblDemo->displayAs("class_id", $tool->transnoecho("class"));
    $tblDemo->displayAs("title", $tool->transnoecho("heading"));
    $tblDemo->displayAs("first", $tool->transnoecho("first_column"));
    $tblDemo->displayAs("second", $tool->transnoecho("second_column"));
    $tblDemo->displayAs("lang", $tool->transnoecho("lang"));

    $tblDemo->defineRelationship("exam_id", PR."exam_names", "id", "title");
    $tblDemo->defineRelationship("class_id", PR."classes", "id", "title");
    $tblDemo->displayAddFormTop();






    $tblDemo->defineAllowableValues("lang", LangArray());



$tblDemo->formatFieldWithFunction('lang', 'langField');


//$tblDemo->defineRelationship("ci_zone", PR."zones", "zone_id", "zone_name");

    $tblDemo->setLimit(30);
    $tblDemo->omitPrimaryKey();
    $tblDemo->addAjaxFilterBoxAllFields();
    //$tblDemo->disallowDelete();

    $tblDemo->showCSVExportOption();

$tpl->renderBeforeContent();
$tblDemo->showTable();
$tpl->footer();