<?php

Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$excludedCols = array();

if(isset($_GET['_chk'])==1){
    $id = (!empty($_GET['student_id'])) ? ($_GET['student_id']) : '';
    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
    $section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
    $session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
    $date = (isset($_GET['date']) && !empty($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : '';
    $to_date = (isset($_GET['to_date'])  && !empty($_GET['to_date'])) ? $tool->ChangeDateFormat($_GET['to_date']) : '';
    if(!empty($_GET['excluded'])){
        foreach($_GET['excluded'] as $key => $val){
            $keyArr = explode("-",$val);
            $keys = $keyArr[0];
            $excludedCols[$keys] = $keys;
        }
    }
}

$qr->renderBeforeContent();
$qr->searchContentAbove();

$users = $set->getUsers();

?>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label><?php echo $tpl->getDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("operator") ?></label>
            <?php
            echo $tpl->GetOptions(array("name" => "users", "data" => $users, "sel" => ""));
            ?>
        </div>
        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>


    </div>





<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){



    Tools::getModel("FeeModel");
    $fs = new FeeModel();

    $branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : "";
    $user = isset($_GET['users']) ? $tool->GetExplodedInt($_GET['users']) : "";
    $date = (isset($_GET['date']) & !empty($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : "";

    if(!$tool->checkDateFormat($date)){
        echo $tool->Message("alert",$tool->transnoecho("invalid_date"));
        $tpl->footer();
        exit;
    }

    if(empty($branch)){
        echo $tool->Message("alert",$tool->transnoecho("please_select_branch"));
        $tpl->footer();
        exit;
    }

    if(empty($user)){
        echo $tool->Message("alert",$tool->transnoecho("please_select_user"));
        $tpl->footer();
        exit;
    }

    /*$operator = $fs->branchOperator($branch);

    if(empty($operator)){
        echo $tool->Message("alert",$tool->transnoecho("there_is_no_operator_of_this_branch"));
        $tpl->footer();
        exit;
    }

    $userId = $operator['id'];
    $userName = $operator['name'];*/

    $res = $fs->dailyStatement($date,$branch,$user);
    $total = count($res);


    if($total==0){
        echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
        $tpl->footer();
        exit;
    }

    $students = array();
    $feePaids = array();
    $feeTypes = array();

    foreach($res as $row) {


        $students[$row['id']] = array(
            "id" => $row['id']
        , "name" => $row['name']
        , "father_name" => $row['fname']
        , "grnumber" => $row['grnumber']
        , "class_title" => $row['class_title']
        , "section_title" => $row['section_title']
        );

        $feeTypes[$row['type_id']] = array(
            "type_id" => $row['type_id']
        , "title" => $row['type_title']
        , "duration_type" => $row['duration_type']);


        $feePaids[$row['id']][$row['type_id']][] = array(
            "fees" => $row['fees']
        , "discount" => $row['discount']
        , "fee_date" => $row['fee_date']
        , "type_id" => $row['type_id']
        , "title" => $row['type_title']
        , "duration_type" => $row['duration_type']
        , "invoice_id" => $row['invoice_id']
        , "stuid" => $row['id']

        );

        $i = 0;
        $overAllTotal = 0;
        $studentAmount = array();
        $typeViceTotal = array();
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

                        <?php
                        if(isset($_GET['users'])){
                        ?>
                            <dt class="fonts"><?php $tool->trans("user") ?></dt>
                            <dd><?php echo $tool->GetExplodedVar($_GET['users']); ?></dd>
                        <?php } ?>
                    </dl>
                </div>

            </div>





            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th><?php $tool->trans("S#") ?></th>
                        <th class="fonts"><?php $tool->trans("ID") ?></th>
                        <th class="fonts"><?php $tool->trans("GR") ?></th>
                        <th class="fonts"><?php $tool->trans("Name") ?></th>
                        <th class="fonts"><?php $tool->trans("Father Name") ?></th>
                        <th class="fonts"><?php $tool->trans("Class") ?></th>
                        <th class="fonts"><?php $tool->trans("Section") ?></th>
                        <?php
                        foreach($feeTypes as $feeType){
                            ?>
                            <th><?php echo $feeType['title'] ?></th>
                        <?php } ?>
                        <th class="fonts"><?php $tool->trans("Total") ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;

                    $overAllTotal = 0;
                    $studentAmount = array();
                    $typeViceTotal = array();



                    foreach($students as $student) {
                        ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $student['id'] ?></td>
                        <td><?php echo $student['grnumber'] ?></td>
                        <td class="fonts"><?php echo $student['name']; ?></td>
                        <td class="fonts"><?php echo $student['father_name']; ?></td>
                        <td class="fonts"><?php echo $student['class_title']; ?></td>

                        <td class="fonts"><?php echo $student['section_title']; ?></td>
                        <?php
                        $stuTotal = 0;
                        foreach($feeTypes as $feeType){
                            ?>
                            <td style="direction: ltr"><?php

                                if(isset($feePaids[$student['id']][$feeType['type_id']])){

                                    foreach($feePaids[$student['id']][$feeType['type_id']] as $feePaid){
                                        $dueAmount = ($feePaid['fees'] - $feePaid['discount']);
                                        $stuTotal += $dueAmount;
                                        $overAllTotal += $dueAmount;
                                        $feeDate[$student['id']][$feeType['type_id']][] = $feePaid['fee_date'];
                                        $studentAmount[$student['id']][$feeType['type_id']][] = $dueAmount;
                                        $typeViceTotal[$feeType['type_id']][] = $dueAmount;
                                    }



                                    if(count($feeDate[$student['id']][$feeType['type_id']]) > 1){
                                        $firsDate = date('F Y', strtotime(min($feeDate[$student['id']][$feeType['type_id']])));
                                        $lastDate = date('F Y', strtotime(max($feeDate[$student['id']][$feeType['type_id']])));
                                        $dateToDis = $firsDate . " to " . $lastDate;
                                    }
                                    else{
                                        $dateToDis = date('F Y', strtotime($feeDate[$student['id']][$feeType['type_id']][0]));
                                    }
                                    echo $dateToDis . " : <b>" . array_sum($studentAmount[$student['id']][$feeType['type_id']]). "</b>";
                                }


                                ?></td>
                        <?php } ?>
                        <td><?php echo $stuTotal; ?></td>
                    </tr>

                    <?php } ?>

                    <tr>
                        <td colspan="7">&nbsp;</td>
                        <?php foreach($feeTypes as $feeType){?>
                            <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo @array_sum($typeViceTotal[$feeType['type_id']]) ?></td>
                        <?php } ?>
                        <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo $overAllTotal ?></td>
                    </tr>


                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <?php
}
$tpl->footer();