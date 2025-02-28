<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$errors = array();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
$tpl->renderBeforeContent();


?>
    <div class="row-fluid">

    <div class="span12">

    <section id="accordion" class="social-box">
    <div class="header">
        <h4><?php $tool->trans("class_report") ?></h4>
    </div>
    <div class="body">

<form action="<?php echo $tool->makeLink("fees", "printbankinvoice", "", "") ?>" method="get" target="_blank">

    <input type="hidden" name="menu" value="fees"/>
    <input type="hidden" name="page" value="printbankinvoice"/>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>
    </div>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("year") ?></label>
            <select name="year" id="year">
                <?php echo $tpf->NewYearsDropDown(); ?>
            </select>
        </div>
        <div class="span3">
            <label class="fonts"><?php $tool->trans("month") ?></label>
            <select name="month" id="month">
                <?php echo $tpf->NewMonthDropDown(); ?>
            </select>
        </div>
        <div class="span3"><label class="fonts"><?php $tool->trans("id") ?></label>
            <input type="number" name="student_id" value="" id="student_id">
        </div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

    </div>

    <div class="row-fluid">
        <!--<div class="span3"><label class="fonts"><?php /*$tool->trans("id_name")*/?></label><input value="" type="text" name="student_id" id="student_id"></div>-->
        <div class="span12" style="text-align: center"></div>
        <!--<div class="span3">&nbsp;</div>
        <div class="span3">&nbsp;</div>-->
    </div>

</form>
    </div>
    </section>
    </div>
    </div>

    <style>
        @media print {
            .table, .table tr, .table td, .table th {border-color: black;}
            .page-break  { display: block; page-break-before: always; }
        }

    </style>
<?php
$tpl->footer();
