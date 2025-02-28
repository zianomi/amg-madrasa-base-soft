<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 5/1/2019
 * Time: 10:49 AM
 */
Tools::getModel("FeeModel");
$fee = new FeeModel();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$glModule = (isset($_GET['gl_module'])) ? $tool->GetExplodedInt($_GET['gl_module']) : '';
//$user = isset($_GET['users']) ? $tool->GetExplodedInt($_GET['users']) : "";
$date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : '';
$to_date = (isset($_GET['to_date'])) ? $tool->ChangeDateFormat($_GET['to_date']) : '';

$tpl->renderBeforeContent();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);
$qr->searchContentAbove();

$glModules = $fee->GetGlModules();
$users = $set->getUsers();
?>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("date")?>*</label><?php echo $tpl->getDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?>*</label><?php echo $tpl->getToDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label>
            <select name="gl_module" id="gl_module"><?php echo $tpl->GetOptionVals(array("data" => $glModules)); ?></select>
        </div>
    </div>
    <div class="row-fluid">

        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    </div>

<?php
$qr->searchContentBottom();


if (isset($_GET['_chk']) == 1) {
    if (empty($date) || empty($to_date)) {
        echo $tool->Message("alert", $tool->transnoecho("please_select_date_range"));
        $tpl->footer();
        exit;
    }

    $param['branch'] = $branch;
    $param['class'] = $glModule;
    $param['user'] = Tools::getUserId();
    $param['date'] = $date;
    $param['to_date'] = $to_date;

    $res = $fee->depositReport($param);

    foreach ($res as $rows){



        $resPaid = $fee->paidAndDefaulterList(array("deposit_id" => $rows['id']));





        ?>

        <div class="body">
            <div id="printReady">

                <div class="row-fluid">
                    <table class="table table-bordered table-striped">

                        <tr>
                            <td><?php echo $rows['user_name'] ?></td>
                            <td><?php echo $rows['bank'] ?></td>
                            <td><?php echo $rows['account_title'] ?></td>
                            <td><?php echo $rows['account_number'] ?></td>
                            <td><?php echo $rows['deposit_number'] ?></td>
                        </tr>

                        <?php
                        $students = array();
                        $feePaids = array();
                        $feeTypes = array();

                        foreach ($resPaid as $row) {
                            if ($row['gender'] == 1) {
                                $gender = " S/O ";
                            } else {
                                $gender = " D/O ";
                            }

                            $students[$row['student_id']] = array(
                                "id" => $row['student_id']
                            , "name" => $row['name']
                            , "gender" => $gender
                            , "father_name" => $row['fname']
                            , "grnumber" => $row['grnumber']
                            , "class_title" => $row['class_title']
                            , "section_title" => $row['section_title']
                            , "invoice_id" => $row['invoice_id']
                            , "fees_date" => $row['fee_date']

                            );

                            $feeTypes[$row['type_id']] = array(
                                "type_id" => $row['type_id']
                            , "title" => $row['title_en']
                            , "duration_type" => $row['duration_type']);

                            $feePaids[$row['student_id']][$row['type_id']][] = array(
                                "fees" => $row['fees']
                            , "discount" => $row['discount']
                            , "paid_status" => $row['paid_status']
                            , "fee_date" => $row['fee_date']
                            , "type_id" => $row['type_id']
                            , "title_en" => $row['title_en']
                            , "duration_type" => $row['duration_type']
                            , "invoice_id" => $row['invoice_id']
                            , "stuid" => $row['student_id']

                            );
                            $i = 0;
                            $overAllTotal = 0;
                            $studentAmount = array();
                            $typeViceTotal = array();

                        }

                        ?>

                        <tr>
                            <td colspan="5">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th><?php $tool->trans("S#") ?></th>
                                        <th class="fonts"><?php $tool->trans("ID") ?></th>
                                        <th class="fonts"><?php $tool->trans("GR") ?></th>
                                        <th class="fonts"><?php $tool->trans("Name") ?></th>
                                        <th class="fonts"><?php $tool->trans("Father Name") ?></th>
                                        <th class="fonts"><?php $tool->trans("Father Fone") ?></th>
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



                                    foreach($students as $student) {


                                        if(isset($disCountStudents[$student['id']])){
                                            $discountRefNumber = $disCountStudents[$student['id']];
                                            if(in_array($discountRefNumber,$excludedCols)){
                                                continue;
                                            }
                                        }

                                        $i++;
                                        ?>


                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $student['id'] ?></td>
                                            <td><?php echo $student['grnumber'] ?></td>
                                            <td class="fonts"><?php echo $student['name']; ?></td>
                                            <td class="fonts"><?php echo $student['father_name']; ?></td>
                                            <td><?php if(isset($numberArr[$student['id']])) echo $numberArr[$student['id']]; else echo "-";?></td>
                                            <td class="fonts"><?php echo $student['class_title']; ?></td>
                                            <td class="fonts"><?php echo $student['section_title']; ?></td>
                                            <?php
                                            $stuTotal = 0;
                                            foreach($feeTypes as $feeType){
                                                ?>
                                                <td><?php

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
                                        <td colspan="8">&nbsp;</td>
                                        <?php
                                        foreach($feeTypes as $feeType){?>
                                            <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo array_sum($typeViceTotal[$feeType['type_id']]) ?></td>
                                        <?php } ?>

                                        <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo $overAllTotal ?></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>




                    </table>
                </div>

            </div>
        </div>
        <?php
    }
}

$tpl->footer();