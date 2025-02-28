<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_subjects");
$tblDemo->setItem($tool->transnoecho("subject"));

$pr = $tblDemo->getPrefix();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : "";
$class = isset($_GET['class']) ? $tool->GetExplodedInt($_GET['class']) : "";

if(!isset($_GET['_chk'])==1) {
    $tpl->renderBeforeContent();
    $qr->searchContentAbove();
?>

    <div class="span2"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn" value="<?php $tool->trans("Search") ?>"></div>

<?php
    $qr->searchContentBottom();
}
if(isset($_GET['_chk'])==1){
$labels = array();
$labels["title"] = $tool->transnoecho("title");
$labels["position"] = $tool->transnoecho("position");
$labels["published"] = $tool->transnoecho("published");
$labels["class_id"] = $tool->transnoecho("class");
$labels["numbers"] = $tool->transnoecho("numbers");
$labels["report_subject_id"] = $tool->transnoecho("report_subject_id");
$labels["subject_group_id"] = $tool->transnoecho("subject_group_id");
$labels["branch_id"] = $tool->transnoecho("branch_id");
//$labels["exam_id"] = $tool->transnoecho("exam_id");
$labels["id"] = $tool->transnoecho("id");
$tblDemo->setExactSearchField('class_id');


$tblDemo->displayAsArray($labels);
$tblDemo->defineRelationship("class_id", $pr."classes", "id", "title", "", ""," AND jb_classes.id = $class");
$tblDemo->defineRelationship("subject_group_id", $pr."subject_groups", "id", "title", "", "", " AND jb_subject_groups.branch_id = $branch AND jb_subject_groups.class_id = $class");
//$tblDemo->defineRelationship("exam_id", $pr."exam_names", "id", "title", "position", 1);
$tblDemo->defineRelationship("branch_id", $pr."branches", "id", "title", "", "", " AND jb_branches.id = $branch");
$tblDemo->defineRelationship("report_subject_id", $pr."subject_reports", "id", "title");

$tblDemo->displayAddFormTop();

$tblDemo->addWhereClause(" WHERE jb_subjects.branch_id = $branch AND jb_subjects.class_id = $class");


$tblDemo->disallowDelete();


$tblDemo->omitFieldCompletely("subject_type");
$tblDemo->addValueOnInsert("subject_type","exam");


/*$tblDemo->formatFieldWithFunction('subject_type', 'typeFunc');
$tblDemo->defineAllowableValues("subject_type", $main_sub);*/



$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("position",array("type" => "text", "required" => "true", "methodApply" => "text"));

$tblDemo->disallowEdit("id");

$tblDemo->addWhereClause(" WHERE jb_subjects.subject_type = 'exam'");


$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->showCSVExportOption();


$tblDemo->setLimit(100);

$tblDemo->addOrderBy(" ORDER BY class_id,position ASC");

$tpl->renderBeforeContent();

$tblDemo->showTable();
}


$tpl->footer();

