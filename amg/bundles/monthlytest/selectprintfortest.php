<?php
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
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


                <form action="<?php echo $tool->makeLink("monthlytest", "printfortest", "", "") ?>" method="get" target="_blank">
                    <input type="hidden" name="menu" value="monthlytest" />
                    <input type="hidden" name="page" value="printfortest" />


                    <div class="row-fluid" id="student_res"></div>
                    <div class="row-fluid">
                        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
                        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
                        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
                        <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>

                        <div class="span3">
                            <label class="fonts"><?php $tool->trans("year") ?></label>
                            <input type="number" name="year" id="year" minlength="4" maxlength="4" required value="<?php echo $year ?>">

                        </div>
                        <div class="span3">
                            <label class="fonts"><?php $tool->trans("month") ?></label><select name="month" id="month">
                                <?php echo $tpf->NewMonthDropDown(); ?>
                            </select>
                        </div>

                        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
                        <div class="span3"><label>&nbsp;</label>&nbsp;</div>
                    </div>


                </form>
            </div>
        </section>
    </div>
</div>

<?php

$tpl->footer();
