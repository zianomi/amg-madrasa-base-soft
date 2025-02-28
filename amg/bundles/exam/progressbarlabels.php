<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");

function stageField($data){
        if($data == 'top'){
            return $tool->transnoecho("top");
        }
        elseif($data == 'middle'){
            return $tool->transnoecho("middle");
        }else{
            return $tool->transnoecho("low");
        }
    }

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
$tblDemo->setDbTable("jb_exam_progress_bar");
$tblDemo->setItem($tool->transnoecho("progress"));

    $tblDemo->displayAs("title", $tool->transnoecho("name_progress"));
    $tblDemo->displayAs("result", $tool->transnoecho("first"));
    $tblDemo->displayAs("result2", $tool->transnoecho("second"));
    $tblDemo->displayAs("result3", $tool->transnoecho("third"));


    $tblDemo->displayAs("lang", $tool->transnoecho("lang"));
    $tblDemo->displayAs("position", $tool->transnoecho("position"));

    $tblDemo->displayAddFormTop();


$tblDemo->setFourFields(false);



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
