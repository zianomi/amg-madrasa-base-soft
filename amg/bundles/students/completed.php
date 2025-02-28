<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";
$branch = ((isset($_GET['branch'])) && (!empty($_GET['branch']))) ? $tool->GetExplodedInt($_GET['branch']) : "";

if(isset($_GET['_chk'])==1){

    $param = array("id" => $id, "date" => $date, "to_date" => $to_date, "branch" => $branch);

    $tableCols = array();

    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");
    $tableCols["session_title"] = $tool->transnoecho("session");
    $tableCols["date"] = $tool->transnoecho("date");
    $tableCols["note"] = $tool->transnoecho("note");
    $tableCols["roll_number"] = $tool->transnoecho("roll_number");
    $tableCols["grade"] = $tool->transnoecho("grade");
    $tableCols["numbers"] = $tool->transnoecho("numbers");
    $tableCols["certificate_number"] = $tool->transnoecho("certificate_number");

    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();

    $qr->setCols($tableCols);
    $qr->setData($stu->getCompletedStudents($param));
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
        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>


    </div>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id']; ?>" type="text" name="student_id" id="student_id"></div>

        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3">&nbsp;</div>
        <div class="span3">&nbsp;</div>
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
