<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$branch = (isset($_GET['branch']) & !empty($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : "";
$date = (isset($_GET['date']) & !empty($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = (isset($_GET['to_date']) & !empty($_GET['to_date'])) ? $tool->ChangeDateFormat($_GET['to_date']) : "";




$qr->renderBeforeContent();
$qr->searchContentAbove();

//$users = $set->getUsers();

?>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label><?php echo $tpl->getDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("to_date") ?></label><?php echo $tpl->getToDateInput() ?></div>
        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>



    </div>





<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){



    Tools::getModel("FeeReport");
    $fs = new FeeReport();


    if(empty($branch)){
        echo $tool->Message("alert",$tool->transnoecho("branch_required"));
        $tpl->footer();
        exit;
    }

    if(!$tool->checkDateFormat($date)){
        echo $tool->Message("alert",$tool->transnoecho("invalid_date"));
        $tpl->footer();
        exit;
    }

    if(!$tool->checkDateFormat($to_date)){
        echo $tool->Message("alert",$tool->transnoecho("invalid_date"));
        $tpl->footer();
        exit;
    }

    $param['branch'] = $branch;
    $param['start_date'] = $date;
    $param['end_date'] = $to_date;


    $deposits = $fs->getSumOfDeposit($param);
    $collectons = $fs->getSumOfColeection($param);

    $depositArr = array();
    $collectonArr = array();

    $users = array();


    foreach ($deposits as $deposit){
        $users[$deposit['user_id']] = array("id" => $deposit['user_id'], "name" => $deposit['name']);
        $depositArr[$deposit['date']][$deposit['user_id']] = $deposit['fees'];
    }

    foreach ($collectons as $collecton){
        $users[$collecton['created_user_id']] = array("id" => $collecton['created_user_id'], "name" => $collecton['name']);
        $collectonArr[$collecton['recp_date']][$collecton['created_user_id']] = $collecton['fees'];
    }



    ?>

    <div class="body">
        <div id="printReady">



            <div class="row-fluid">
                <div class="span7 text-center">
                    <img class="logo" src="<?php echo $tool->getWebUrl() ?>/img/iqra_logo.png" alt="Amazon" height="77" width="400">
                </div>

                <div class="span5">
                    <div class="alert alert-success fonts" style="font-size: 25px; "><?php $tool->trans("daily_statement") ?></div>

                    <dl class="dl-horizontal">



                        <dt class="fonts"><?php $tool->trans("date") ?></dt>
                        <dd><?php echo date('F d, Y', strtotime($date)); ?></dd>

                        <dt class="fonts"><?php $tool->trans("to_date") ?></dt>
                        <dd><?php echo date('F d, Y', strtotime($to_date)); ?></dd>

                    </dl>
                </div>

            </div>





            <div id="editable_wrapper" class="dataTables_wrapper form-inline">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th><?php $tool->trans("S#") ?></th>
                        <th class="fonts"><?php $tool->trans("date") ?></th>
                        <?php
                        foreach ($users as $user){
                        ?>
                        <th class="fonts" colspan="2"><?php echo $user['name'] ?></th>
                        <?php } ?>


                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <?php
                        foreach ($users as $user){
                            ?>
                        <td><?php $tool->trans("collection") ?></td>
                        <td><?php $tool->trans("deposit") ?></td>
                        <?php } ?>
                    </tr>
                    <?php
                    $i = 0;


                    $start = strtotime($date);
                    $end = strtotime($to_date);
                    $currentdate = $start;

                    $userCollectionSum = array();
                    $userDepositSum = array();
                    while($currentdate <= $end) {
                        $i++;
                        $cur_date = date('F, Y', $currentdate);
                        $sqlFormatedDate = date('Y-m-d', $currentdate);
                        $formatedDate = date('l d F, Y', $currentdate);
                        $currentdate = strtotime('+1 day', $currentdate);
                        ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $formatedDate ?></td>
                        <?php foreach ($users as $user){ ?>

                            <td>
                                <?php
                                if(isset($collectonArr[$sqlFormatedDate][$user['id']])){
                                    $userCollectionSum[$user['id']][] = $collectonArr[$sqlFormatedDate][$user['id']];
                                    echo $collectonArr[$sqlFormatedDate][$user['id']];
                                }
                                else{
                                    echo 0;
                                }
                                ?>
                            </td>
                            <td><?php
                                if(isset($depositArr[$sqlFormatedDate][$user['id']])){
                                    $userDepositSum[$user['id']][] = $depositArr[$sqlFormatedDate][$user['id']];
                                    echo $depositArr[$sqlFormatedDate][$user['id']];

                                }
                                else{
                                    echo 0;
                                }
                                 ?></td>

                        <?php } ?>
                        </tr>

                    <?php } ?>

                    <tr>
                        <td colspan="2"><b>Total</b></td>
                        <?php foreach ($users as $user){ ?>
                        <td><?php if(isset($userCollectionSum[$user['id']])) echo array_sum($userCollectionSum[$user['id']]) ?></td>
                        <td><?php if(isset($userDepositSum[$user['id']])) echo array_sum($userDepositSum[$user['id']]) ?></td>
                        <?php } ?>
                    </tr>




                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <?php

}
$tpl->footer();


