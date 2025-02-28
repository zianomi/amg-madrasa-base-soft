<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";
$param = array("id" => $id, "date" => $date, "to_date" => $to_date);
if(isset($_GET['_chk'])==1){



}
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$tpl->renderBeforeContent();
//$qr->searchContentAbove();
?>

    <div class="row-fluid">

            <div class="span12">

                <section id="accordion" class="social-box">
                  <div class="header">
                      <h4><?php $tool->trans("id_report")?></h4>
                  </div>
                  <div class="body">


                  <form action="<?php echo $tool->makeLink("monthlytest","reportstudentprint","","") ?>" method="get" target="_blank">
                      <input type="hidden" name="menu" value="monthlytest" />
                      <input type="hidden" name="page" value="reportstudentprint" />

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id']; ?>" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
</div>

</form>
    </div>
    </section>
    </div>
    </div>

<?php

$tpl->footer();