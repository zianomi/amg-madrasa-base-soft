<?php
Tools::getLib("QueryTemplate");
Tools::getModel("AppModel");
$qr = new QueryTemplate();
$app = new AppModel();


if (isset($_GET['act'])) {
    if (!empty($_GET['act'])) {
        if ($_GET['act'] == "del") {
            if (isset($_GET['id'])) {
                if (is_numeric($_GET['id'])) {
                    $app->deleteQuery($_GET['id']);
                    Tools::Redir("students", "quries", "", "");
                    exit;
                }
            }


        }
    }
}



$tableCols = array();

$tableCols["student_id"] = $tool->transnoecho("student_id");
$tableCols["name"] = $tool->transnoecho("name");
$tableCols["class_title"] = $tool->transnoecho("class_title");
$tableCols["date"] = $tool->transnoecho("date");


$data = $app->GetQuriesData();


$qr->setCols($tableCols);





$qr->setAction(true);
$qr->setRemoveCsvCols(array("id"));

$qr->setCustomActions(array
(
    array("label" => '<i class="icon-book"></i>', "link" => Tools::makeLink("students", "quriyreply", "", ""))
    //, array("label" => '<i class="icon-archive"></i>', "link" => Tools::makeLink("students", "quries&act=del", "", ""))
)
);

$qr->setDynamicParam(array("id" => "id"));
$qr->setAnchorDataId("id");



$qr->setData($data);




if (isset($_GET['export_csv']) == 1) {
    $qr->exportData();
}

$tpl->renderBeforeContent();
?>


<div class="social-box">
    <div class="body">
        <?php $qr->contentHtml(); ?>

    </div>
</div>
<?php

$tpl->footer();
