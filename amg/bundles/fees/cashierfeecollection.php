<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("FeeModel");
$fs = new FeeModel();
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";
$type = ((isset($_GET['type'])) && (!empty($_GET['type']))) ? $tool->GetInt($_GET['type']) : "";


$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label class="fonts">&nbsp;</label>&nbsp;</div>


</div>


<?php
$qr->searchContentBottom();
if(isset($_GET['_chk'])==1){


if(empty($date) || empty($to_date)){
       $tool->Message("alert",$tool->transnoecho("All Fields Required."));
       exit;
   }

   $data = array();
   $distinctData = array();
   $operatorData = array();
   $operatorTotal = array();

   $res = $fs->cashierCollections(array("rcp_start_date" => $date, "rcp_end_date" => $to_date));

   foreach($res as $row){

   $distinctData[$row['recp_date']] = $row['recp_date'];
       $operatorData[$row['recp_user_id']] = array("id" => $row['recp_user_id'], "name" => $row['name']);
       $data[$row['recp_user_id']][$row['recp_date']] = ($row['fees'] - $row['discount']);
   }


?>


    <div class="body">


        <table class="table table-bordered table-striped table-hover flip-scroll">
            <thead>
            <tr>
                <th>S#</th>
                <th class="fonts"><?php $tool->trans("Cashier") ?></th>
                <?php foreach($distinctData as $dateLabel){ ?>
                <th><?php echo date('F d, Y', strtotime($dateLabel)) ?></th>
            <?php } ?>
                <th><?php $tool->trans("Total") ?></th>
            </tr>
            </thead>


            <tbody>

            <?php

            $i=0;
            $total=0;

            foreach ($operatorData as $row){
                $i++;
            ?>

            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['name'] ?></td>
                <?php foreach($distinctData as $dateLabel){
                    if(isset($data[$row['id']][$dateLabel])){
                        $amountOpt = $data[$row['id']][$dateLabel];
                        $operatorTotal[$row['id']][] = $amountOpt;
                    }
                    else{
                        $amountOpt = 0;
                    }

                    ?>
                <td><?php echo $amountOpt; ?></td>
                <?php } ?>
                <td><?php echo array_sum($operatorTotal[$row['id']]); ?></td>
            </tr>
            <?php } ?>


            </tbody>

        </table>
    </div>
    <?php } ?>


<?php
$tpl->footer();