<?php
Tools::getModel("ExamModel");

$exm = new ExamModel();

$tpl->setCanExport(false);
$tpl->renderBeforeContent();


$formAction = Tools::makeLink("exam","multipleprintcards","","");
?>
<div class="social-box">
    <div class="header">

    </div>

    <div class="body">

<form method="get" action="<?php echo FRONT_SITE_URL ?>/exam-bulk-report?" target="_blank">
    <input type="hidden" name="bulk" value="1">
<div class="body">
 <div id="jamia_msg">&nbsp;</div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class")?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("section")?></label><?php echo $tpl->getSecsions() ?></div>

</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("Template")?></label>

        <select name="template" id="template">
            <option value="">Please select</option>

            <option value="first_monthly">Single Exam</option>
            <option value="mid_term">Mid Term</option>
            <option value="final_exam">Final Term</option>

        </select>

    </div>

    <div class="span3"><label>&nbsp;</label>
        <input type="submit" class="btn">
    </div>

</div>

</div>
</form>

    </div>
</div>
<?php
$tpl->footer();
