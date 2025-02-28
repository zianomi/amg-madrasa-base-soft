<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("FeeModel");
$fs = new FeeModel();
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";


$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
</div>


<?php
$qr->searchContentBottom();
if(isset($_GET['_chk'])==1){




?>


    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">


        <table class="table table-bordered table-striped table-hover flip-scroll">
            <thead>
            <tr>
                <th>S#</th>
                <th class="fonts"><?php $tool->trans("Date") ?></th>
                <th><?php $tool->trans("Amount") ?></th>
            </tr>
            </thead>


            <tbody>


            <?php

            if(empty($date) || empty($to_date)){
                $tool->Message("alert",$tool->transnoecho("All Fields Required."));
                exit;
            }

            $res = $fs->cashierData(array("rcp_start_date" => $date, "rcp_end_date" => $to_date));
            $i=0;
            $total = 0;
            foreach($res as $row){
                $i++;
                $amount = ($row['fees'] - $row['discount']);
                $total += $amount;
            ?>
                <tr>
                <td><?php echo $i; ?></td>
                <td><?php  echo date('F d, Y', strtotime($row['recp_date'])); ?></td>
                <td><?php echo $amount ?></td>
            </tr>

            <?php } ?>

            <tr class="alert alert-success">
                <td>&nbsp;</td>
                <td><?php $tool->trans("Total") ?></td>
                <td><?php echo $total ?></td>
            </tr>

            </tbody>

        </table>
    </div>
<?php }


$tpl->footer();