<?php
/* @var Template $tpl */
/* @var Tools $tool */

Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$errors = array();
$vals = array();
$valSyl = array();
$msg = "";

if(isset($_POST['_chk'])==1) {


    $exam = !empty($_POST['exam_name']) ? $tool->GetExplodedInt($_POST['exam_name']) : '';
    $session = !empty($_POST['session']) ? $tool->GetExplodedInt($_POST['session']) : '';

    if(empty($exam)){
        $errors[] = $tool->Message("alert","exam_required");
    }


    if(empty($session)){
        $errors[] = $tool->Message("alert","session_required");
    }




    if(!empty($session) && !empty($exam)){
        $exm->updateNumberAsPerLog($session,$exam);
        $msg = $tool->Message("succ",Tools::transnoecho("log_number_updated"));
    }
    else{
        $msg = $tool->Message("alert",Tools::transnoecho("update_failed"));
    }


    $_SESSION['msg'] = $msg;
    Tools::Redir("exam","updateexammaxmarks","","");
    exit;

}


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);

$qr->setFormMethod("post");

$qr->searchContentAbove();
?>

    <style type="text/css">
        .val input{width: 65% !important;}
    </style>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>

        <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>
        <div class="span3"><label>&nbsp;</label>&nbsp;</div>

    </div>






<?php
$qr->searchContentBottom();

$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
