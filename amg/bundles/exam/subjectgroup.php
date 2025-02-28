<?php
$mysqliConn = $tool->getMysqlCon();
Tools::getLib("AmgCrud");
$tblDemo = new AmgCrud();
$tblDemo->setDbTable("jb_subject_groups");
$tblDemo->setItem($tool->transnoecho("subject_groups"));
$pr = $tblDemo->getPrefix();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : "";
$class = isset($_GET['class']) ? $tool->GetExplodedInt($_GET['class']) : "";


if(!isset($_GET['_chk'])==1){
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
$tblDemo->setExactSearchField('class_id');

$tblDemo->setFourFields(false);


$tblDemo->displayAsArray($labels);
$tblDemo->defineRelationship("class_id", $pr."classes", "id", "title", "", ""," AND jb_classes.id = $class");
//$tblDemo->defineRelationship("exam_id", $pr."exam_names", "id", "title", "", "", "");
$tblDemo->defineRelationship("branch_id", $pr."branches", "id", "title", "", "", " AND jb_branches.id = $branch");

$tblDemo->displayAddFormTop();

$tblDemo->disallowDelete();

$tblDemo->setAmgInputDataType("title",array("type" => "text", "required" => "true", "methodApply" => "text"));
$tblDemo->setAmgInputDataType("position",array("type" => "text", "required" => "true", "methodApply" => "text"));


$tblDemo->addAjaxFilterBox("title");
$tblDemo->addAjaxFilterBox("class_id");
$tblDemo->showCSVExportOption();

    $tblDemo->disallowEdit("id");


$tblDemo->defineAllowableValues("is_core", $tblDemo->softStatus());
$tblDemo->formatFieldWithFunction('is_core', 'StatusField');

$tblDemo->addWhereClause(" WHERE jb_subject_groups.branch_id = $branch AND jb_subject_groups.class_id = $class");

$tblDemo->setLimit(100);

$tblDemo->addOrderBy(" ORDER BY class_id,position ASC");

$tpl->renderBeforeContent();

$tblDemo->showTable();

}

$tpl->footer();

