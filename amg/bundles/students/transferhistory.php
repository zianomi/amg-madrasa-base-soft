<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_day'])) && (!empty($_GET['to_day']))) ? $tool->ChangeDateFormat($_GET['to_day']) : "";
$show = $tpl->handleSessionCustomDate();

if(isset($_GET['_chk'])==1){

    $param = array("id" => $id, "date" => $date, "to_date" => $to_date);

    $tableCols = array();

    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["gender"] = $tool->transnoecho("gender");
    $tableCols["fname"] = $tool->transnoecho("fname");
    $tableCols["grnumber"] = $tool->transnoecho("grnumber");
    $tableCols["bo_title"] = $tool->transnoecho("old_branch");
    $tableCols["co_title"] = $tool->transnoecho("old_class");
    $tableCols["sco_title"] = $tool->transnoecho("old_section");
    $tableCols["bc_title"] = $tool->transnoecho("new_branch");
    $tableCols["cc_title"] = $tool->transnoecho("new_class");
    $tableCols["sc_title"] = $tool->transnoecho("new_section");
    $tableCols["date"] = $tool->transnoecho("date");

    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();

    $qr->setCols($tableCols);
    $qr->setData($stu->TransferHistory($param));
    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }
    //StudentdSearchWithProfile
}

$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id']; ?>" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
</div>


<?php
$qr->searchContentBottom();
if(isset($_GET['_chk'])==1){



    if(empty($id) && empty($date) && empty($to_date)){
        echo $tool->Message("alert",$tool->transnoecho("please_select_id_or_date"));
        exit;
    }
    $qr->contentHtml();
}

$tpl->footer();