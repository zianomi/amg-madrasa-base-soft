<?php
Tools::getLib("QueryTemplate");
Tools::getModel("ExamModel");
$qr = new QueryTemplate();
$exm = new ExamModel();





$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$examName = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';


if(isset($_GET['_chk'])==1){


    $param["branch"] = $branch;
    $param["session"] = $session;
    $param["exam"] = $examName;


    $tableCols = array();

    $tableCols["branch_title"] = $tool->transnoecho("branch_title");
    $tableCols["class_title"] = $tool->transnoecho("class_title");
    $tableCols["session_title"] = $tool->transnoecho("session_title");

    $tableCols["exam_title"] = $tool->transnoecho("exam_title");
    $tableCols["exam_start_date"] = $tool->transnoecho("exam_start_date");
    $tableCols["exam_end_date"] = $tool->transnoecho("exam_end_date");
    $tableCols["attand_start_date"] = $tool->transnoecho("attand_start_date");
    $tableCols["attand_end_date"] = $tool->transnoecho("attand_end_date");
    $tableCols["year"] = $tool->transnoecho("year");
    //$tableCols["display_year"] = $tool->transnoecho("display_year");


    $qr->setAction(true);
    $qr->setRemoveCsvCols(array("id"));

    $qr->setCustomActions(array
              (
              array("label" => '<i class="icon-cut"></i>',"link" => "", "class" => " class='delete_exam_date_log'"),
              array("label" => '<i class="icon-book"></i>',"link" => Tools::makeLink("exam","examsubjects","",""), "class" => " class='asdasds'")
      )
      );

    $qr->setDynamicParam(array("id" => "id", "class_id" => "class_id"));
    $qr->setAnchorDataId("id");

    function displayDate($date){
        global $tool;
       return $tool->ChangeDateFormat($date);
   }


    $qr->formatFieldWithFunction("exam_start_date", "displayDate");
    $qr->formatFieldWithFunction("exam_end_date", "displayDate");
    $qr->formatFieldWithFunction("attand_start_date", "displayDate");
    $qr->formatFieldWithFunction("attand_end_date", "displayDate");


    $qr->setCols($tableCols);
    $qr->setData($exm->examDateLogs($param));

    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }

}

$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
</div>




<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){
    $qr->contentHtml();
}
?>



<?php
$tpl->footer();
unset($exm);
unset($tpf);
