<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();



$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$note_cat = (isset($_GET['note_cat'])) ? $tool->GetExplodedInt($_GET['note_cat']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_day'])) && (!empty($_GET['to_day']))) ? $tool->ChangeDateFormat($_GET['to_day']) : "";
$count = (isset($_GET['count'])) ? $tool->GetInt($_GET['count']) : '';



$param = array();




if(isset($_GET['_chk'])==1){




    $param["branch"] = $branch;
    $param["date"] =  $date;
    $param["to_date"] =  $to_date;
    $param["cat"] =  $note_cat;
    $param["count"] =  $count;

    $tableCols = array();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");
    $tableCols["session_title"] = $tool->transnoecho("session");
    $tableCols["counts"] = $tool->transnoecho("counts");



    Tools::getModel("NotesModel");
    $note = new NotesModel();




    $qr->setCols($tableCols);



    $qr->setData($note->MoreNotes($param));





    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }

}



$tpl->renderBeforeContent();
$qr->searchContentAbove();


?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><?php echo $tpl->getTable("notecats",$tool->transnoecho("note_cat")); ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("from_date") ?></label><?php echo $tpl->getDateInput(); ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date") ?></label><?php echo $tpl->getToDateInput() ?></div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("count") ?></label><input type="number" name="count" id="count" value="<?php if(isset($_GET['count'])) echo $_GET['count']; else echo 1; ?>"></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
    <div class="span3">&nbsp;</div>

</div>

<?php
$qr->searchContentBottom();


if(isset($_GET['_chk'])==1){

    $noteLabel = "";
    if(!empty($_GET['note_cat'])){
        $noteLabel = $tool->GetExplodedVar($_GET['note_cat']);
    }

    $labelToDisplay = $noteLabel;


    echo $tool->Message("succ",$labelToDisplay);
    $qr->contentHtml();
}

$tpl->footer();