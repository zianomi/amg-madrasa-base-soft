<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
//$columns = $set->getProfileCols();
$set = new SettingModel();


$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$note_cat = (isset($_GET['note_cat'])) ? $tool->GetExplodedInt($_GET['note_cat']) : '';
$note_sub_cat = (isset($_GET['note_sub_cat'])) ? $tool->GetExplodedInt($_GET['note_sub_cat']) : '';
$toDay = (isset($_GET[''])) ? $tool->ChangeDateFormat($_GET['to_day']) : '';

$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_day'])) && (!empty($_GET['to_day']))) ? $tool->ChangeDateFormat($_GET['to_day']) : "";


$noteSubCat = array();
$param = array();

$show = $tpl->handleSessionCustomDate();


if(isset($_GET['_chk'])==1){



    if(!empty($note_cat)){
        $noteSubCat = $set->getTitleTable("notesubcats", " AND note_cat_id = $note_cat");
    }




    $param["branch"] = $branch;
    $param["class"] =  $class;
    $param["section"] =  $section;
    $param["session"] =  $session;
    $param["date"] =  $date;
    $param["to_date"] =  $to_date;
    $param["cat"] =  $note_cat;
    $param["sub_cat"] =  $note_sub_cat;
    $param["today"] =  $toDay;
    $param["id"] =  $id;


    $tableCols = array();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");
    $tableCols["session_title"] = $tool->transnoecho("session");
    $tableCols["date"] = $tool->transnoecho("date");
    $tableCols["desc"] = $tool->transnoecho("detail");
    //$tableCols["cat"] = $tool->transnoecho("category");
    //$tableCols["sub_cat"] = $tool->transnoecho("type");


    Tools::getModel("NotesModel");
   $note = new NotesModel();






    //$qr->setDynamicParam(array("name" => "name","asdasd" => "username"));

    $qr->setCols($tableCols);

    //$qr->formatFieldWithFunction("date", "displayImage");
    /*function displayImage($value){
        global $tool;
         return $tool->ChangeDateFormat($value);
    }*/

    $qr->setData($note->NotesReport($param));





    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }


    //StudentdSearchWithProfile
}

//echo '<pre>';print_r($profileCols );echo '</pre>';die('Call');

$tpl->renderBeforeContent();
$qr->searchContentAbove();


?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session_or_date") ?></label><?php echo $tpl->sessionOrCustomDate($show);  ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("sections") ?></label><?php echo $tpl->getSecsions() ?></div>
</div>


<div class="row-fluid">
    <div class="span3"><?php echo $tpl->getTable("notecats","note_cat"); ?></div>
    <div class="span3">
        <label class="fonts"><?php $tool->trans("note_type")?></label>
    <?php echo $tpl->GetOptions(array("name" => "note_sub_cat", "data" => $noteSubCat, "sel" => $note_sub_cat)); ?>
    </div>
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if(isset($_GET['_chk'])==1){

    $noteLabel = "";
    if(!empty($_GET['note_cat'])){
        $noteLabel = $tool->GetExplodedVar($_GET['note_cat']);
    }

    if(!empty($_GET['sub_cat'])){
        $noteTypeLabel = $tool->GetExplodedVar($_GET['sub_cat']);
    }

    $labelToDisplay = $noteLabel;

    if(!empty($noteLabel) && !empty($noteTypeLabel)){
        $labelToDisplay .= " - " . $noteTypeLabel;
    }

    echo $tool->Message("succ",$labelToDisplay);
    $qr->contentHtml();
}

$tpl->footer();