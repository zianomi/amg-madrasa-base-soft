<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tblDemo = new AmgCrud("hifz_stu_data", $tool->transnoecho("hifz_student_data"));
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$tpl->renderBeforeContent();
//$qr->searchContentAbove();
?>
<form action="" method="get">
<?php echo $tpl->formHidden() ?>
<div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3"><label>&nbsp;</label>&nbsp;</div>
        <div class="span3"><label>&nbsp;</label>&nbsp;</div>
    </div>

</form>
<?php
//$qr->searchContentBottom();
$id = isset($_GET['student_id']) ? $tool->GetInt($_GET['student_id']) : "";
$branch = isset($_GET['branch']) ? $tool->GetInt($_GET['branch']) : "";


if(isset($_GET['_chk'])==1){
    if(empty($id) || !is_numeric($id)){
        echo $tool->Message("alert",$tool->transnoecho("invalid_id"));
        exit;
    }

    if(empty($branch) || !is_numeric($branch)){
        echo $tool->Message("alert",$tool->transnoecho("invalid_id"));
        exit;
    }

$tblDemo->setDisableDelete(0);
$tblDemo->displayAs("student_id", "آئی ڈی،نام،جی آر، ولدیت");
$tblDemo->displayAs("date", "تاریخ");
$tblDemo->displayAs("start_date", "تاریخ ابتداء");

$tblDemo->displayAs("end_date", "تاریخ انتہاء");
$tblDemo->displayAs("para_id", "پارہ");
$tblDemo->displayAs('page_number', 'صفحہ نمبر');
$tblDemo->displayAs('line_number', 'لائن نمبر');
$tblDemo->displayAs('created_user_id', 'آپریٹر');

$tblDemo->displayAddFormTop();
$tblDemo->addWhereClause("WHERE student_id = $id");

$tblDemo->defineRelationship("student_id", PR."students", "id", "CONCAT(id, name, grnumber, fname)","",1, " WHERE id = $id");
$tblDemo->defineRelationship("branch_id", PR."branches", "id", "title");
$tblDemo->defineRelationship("class_id", PR."classes", "id", "title");
$tblDemo->defineRelationship("section_id", PR."sections", "id", "title");
$tblDemo->defineRelationship("session_id", PR."sessions", "id", "title");
$tblDemo->defineRelationship("para_id", PR."quran", "id", "title");
$tblDemo->defineRelationship("year_id", PR."hifz_years", "id", "title");



$tblDemo->disallowEdit("branch_id");
$tblDemo->disallowEdit("class_id");
$tblDemo->disallowEdit("section_id");
$tblDemo->disallowEdit("session_id");
$tblDemo->disallowEdit("para_id");
$tblDemo->disallowEdit("year_id");
$tblDemo->disallowEdit("student_id");
$tblDemo->disallowEdit("date");


$tblDemo->setLimit(300);
$tblDemo->omitPrimaryKey();
$tblDemo->omitAddField('created_user_id');
$tblDemo->omitAddField('student_id');
$tblDemo->addAjaxFilterBox("para_id");
$tblDemo->addAjaxFilterBox("date");
$tblDemo->addAjaxFilterBox("student_id");

$tblDemo->disallowAdd();

//$tblDemo->showCSVExportOption();

$tblDemo->showTable();
}
$tpl->footer();