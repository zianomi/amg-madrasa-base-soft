<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 12/16/2018
 * Time: 2:15 PM
 */
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$tpf = new TemplateForm();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$tpl->renderBeforeContent();
?>

    <div class="row-fluid">

        <div class="span12">

            <section id="accordion" class="social-box">
                <div class="header">
                    <h4><?php $tool->trans("class_report") ?></h4>
                </div>
                <div class="body">


                    <form action="<?php echo $tool->makeLink("monthlytest", "printforentryprint", "", "") ?>" method="get" target="_blank">
                        <input type="hidden" name="menu" value="exam"/>
                        <input type="hidden" name="page" value="printforentryprint"/>


                        <div class="row-fluid" id="student_res"></div>
                        <div class="row-fluid">
                            <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
                            <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
                            <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
                            <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>
                        </div>


                        <div class="row-fluid">
                            <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label>
                                <?php echo $tpl->examDropDown($exm->getExamNames()); ?>
                            </div>



                            <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
                            <div class="span3"><label>&nbsp;</label>&nbsp;</div>

                            <div class="span3"><label class="fonts">&nbsp;</label>&nbsp;</div>
                        </div>


                    </form>
                </div>
            </section>
        </div>
    </div>

<?php

$tpl->footer();
