<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tblDemo = new AmgCrud("hifz_record", $tool->transnoecho("Hifz Record"));
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
$tblDemo->defineRelationship("created_user_id", PR."users", "id", "name");
$tblDemo->defineRelationship("quran_id", PR."quran", "id", "title");
$tblDemo->defineRelationship("hifz_year_id", PR."hifz_years", "id", "title");



$tblDemo->disallowEdit("created_user_id");
$tblDemo->disallowEdit("date");
$tblDemo->disallowEdit("para_id");
$tblDemo->disallowEdit("page_number");
$tblDemo->disallowEdit("line_number");
$tblDemo->disallowEdit("student_id");


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