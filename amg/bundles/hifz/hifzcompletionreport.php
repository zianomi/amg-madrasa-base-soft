<?php
Tools::getLib("QueryTemplate");
Tools::getModel("HifzModel");
$qr = new QueryTemplate();

$hoizModel = new HifzModel();

$tpl->setCanExport(false);

$tpl->renderBeforeContent();
$qr->searchContentAbove();



?>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("zone") ?></label><?php echo $tpl->getTable("zones","zones"); ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("date")?> *</label><?php echo $tpl->getDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?> *</label><?php echo $tpl->getToDateInput() ?></div>

    </div>

    <div class="row-fluid">
        <div class="span12"><label>&nbsp;</label><input type="submit" class="btn"></div>
    </div>


<?php
$qr->searchContentBottom();


if(isset($_GET['_chk'])==1){


    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $zone = (isset($_GET['zones'])) ? $tool->GetExplodedInt($_GET['zones']) : '';
    $date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
    $to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";


    if(empty($date) || empty($date)){
        echo $tool->Message("alert",$tool->transnoecho("please_select_both_dates"));
        $tpl->footer();
        exit;
    }


    $param = array(
        "branch" => $branch
    ,"zone" => $zone,
        "date" => $date,
        "to_date" => $to_date
    );

    $res = $hoizModel->getCompletionReport($param);

    //echo '<pre>'; print_r($res); echo '</pre>';
?>
    <div id="printReady">
    <div class="body">
    <div class="row-fluid">
        <div class="span12">
            <table class="table table-bordered table-striped table-hover">
                <thead>

                <tr>
                    <th class="fonts"><?php $tool->trans("S#") ?></th>
                    <th class="fonts"><?php $tool->trans("id") ?></th>
                    <th class="fonts"><?php $tool->trans("name") ?></th>
                    <th class="fonts"><?php $tool->trans("fname") ?></th>
                    <th class="fonts"><?php $tool->trans("branch_title") ?></th>
                    <th class="fonts"><?php $tool->trans("start_date_hifz") ?></th>
                    <th class="fonts"><?php $tool->trans("end_date_hifz") ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i=0;
                foreach ($res as $row){
                    $i++;
                ?>
                    <tr>

                        <td><?php echo $i ?></td>
                        <td><a href="<?php echo Tools::makeLink("hifz","reportprogressform","0","view&id=".$row['student_id']) ?>" target="_blank"><?php echo $row['student_id'] ?></a></td>
                        <td class="fonts"><?php echo $row['name'] ?></td>
                        <td class="fonts"><?php echo $row['fname'] ?></td>
                        <td class="fonts"><?php echo $row['branch_title'] ?></td>
                        <td><?php echo $tool->ChangeDateFormat($row['start_date_hifz']) ?></td>
                        <td><?php echo $tool->ChangeDateFormat($row['end_date_hifz']) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    </div>
    </div>
<style>
    @media print {
        a[href]:after {
            content: none !important;
        }
    }
</style>
<?php

}

$tpl->footer();
