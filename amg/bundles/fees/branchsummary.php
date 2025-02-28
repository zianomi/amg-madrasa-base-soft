<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$excludedCols = array();
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
if(isset($_GET['_chk'])==1){
    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
    $year = isset($_GET['year']) ? $tool->GetInt($_GET['year']) : "";
    $month = isset($_GET['month']) ? $tool->GetInt($_GET['month']) : "";
    $month = str_pad($month,2,0,STR_PAD_LEFT);
    $feeDate = $year . "-" . $month . "-01";
    $feeTypesPassed = isset($_GET['fee_types']) ? ($_GET['fee_types']) : "";

}




$qr->renderBeforeContent();
$qr->searchContentAbove();



$discountTypeData = $set->getTitleTable("discount_refrence");

?>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
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

        <div class="span3"><label class="fonts"><?php $tool->trans("fee_type") ?></label>


            <?php
            $typeData = $set->getTitleTable("fee_type");
            echo $tpl->GetMultiOptions(array("name" => "fee_types[]", "data" => $typeData, "sel" => ""));
            ?>
        </div>

    </div>
    <div class="row-fluid">
        <div class="span12">&nbsp;<label class="fonts">&nbsp;<input type="submit" class="btn"></div>
    </div>



<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){
    $feeTypes = array();
    foreach ($feeTypesPassed as $rowFeeTypes){
        $feeTypes[] = array("id" => $tool->GetExplodedInt($rowFeeTypes), "title" => $tool->GetExplodedVar($rowFeeTypes));
    }

    if(empty($branch) || empty($year) || empty($month) || empty($feeTypes)){
        echo $tool->Message("alert",$tool->transnoecho("all_fields_required"));
        $tpl->footer();
        exit;
    }


    if(count($feeTypes) > 3){
        echo $tool->Message("alert",$tool->transnoecho("max_three_types_allowed"));
        $tpl->footer();
        exit;
    }




    Tools::getModel("FeeReport");
    Tools::getModel("FeeModel");
    $fs = new FeeReport();
    $fee = new FeeModel();

    $feeTypeArr = array();
    $feeStructureArr = array();
    $branchDiscountArr = array();
    $branchDiscountCountArr = array();



    $session = $fs->findSessionByDate($feeDate);

//$feeTypes = $fee->getFeeTypes();

    //$branchDiscounts = $fs->GetDiscounts(array("branch" => $branch));
    //$branchDiscountCounts = $fs->getDiscountCount(array("branch" => $branch));

    foreach ($feeTypes as $feeType){
        $feeTypeArr[] = $feeType['id'] . "-" . $feeType['title'];
    }

    $param = array();
    $param['branch'] = $branch;

    $param['session'] = $session['id'];

    $param['fee_type'] = $feeTypeArr;
    $param['fee_types'] = $feeTypeArr;
    $branchFeeData = $fee->getBranchFees($param);


    $branchStudents = $fs->BranchStudent($branch,$session['id']);


    $branchDiscounts = $fs->getBranchDiscounts($feeDate,$branch,$param);


    foreach ($branchFeeData as $row){
        $feeStructureArr[$row['class_id']][$row['fee_type_id']] = $row['fees'];
    }

    foreach ($branchDiscounts as $branchDiscount){
        $branchDiscountArr[$branchDiscount['class_id']][$branchDiscount['type_id']][] = $branchDiscount;
    }

    /*foreach ($branchDiscountCounts as $branchDiscountCount){
        $branchDiscountCountArr[$branchDiscountCount['class_id']][$branchDiscountCount['type_id']] = $branchDiscountCount['tot'];
    }*/



    ?>

    <div class="body">

        <div id="printReady">


            <div class="alert alert-info" style="font-size: 20px; ">
                <span class="fonts"><?php echo $tool->GetExplodedVar($_GET['branch']); ?></span></div>

            <div class="alert alert-info" style="font-size: 20px; ">
                <span class="fonts"><?php echo date('F, Y', strtotime($feeDate)); ?></span></div>



            <div class="row-fluid">

                <div class="span12">

                    <?php
                    if(!empty($branch)){
                        ?>

                    <?php } ?>


                    <table class="table table-bordered table-striped table-hover">

                        <thead>
                        <tr>
                            <th>S#</th>
                            <th class="fonts"><?php $tool->trans("class_title") ?></th>
                            <th class="fonts"><?php $tool->trans("count") ?></th>
                            <?php
                            foreach ($feeTypes as $feeType){
                                ?>
                                <th class="fonts" colspan="4"><?php echo $feeType['title'] ?></th>
                            <?php } ?>
                        </tr>

                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?php
                            foreach ($feeTypes as $feeType){
                                ?>
                                <th class="fonts"><?php $tool->trans("discounted_stu"); ?></th>
                                <th class="fonts"><?php $tool->trans("count_fee_month"); ?></th>
                                <th class="fonts"><?php $tool->trans("class_fees"); ?></th>
                                <th class="fonts"><?php $tool->trans("total_expected"); ?></th>

                            <?php } ?>


                        </tr>
                        </thead>

                        <tbody>

                        <?php
                        $i=0;
                        $monthlyStudentCount = array();
                        $totalStudents = 0;
                        $discountedStudents = array();
                        $totalExpectedFeesIncome = array();
                        $totalDsicountSum = array();
                        //echo '<pre>'; print_r($branchDiscountArr); echo '</pre>';
                        foreach ($branchStudents as $branchStudent){
                            $i++;
                            $totalStudents += $branchStudent['tot'];



                            ?>

                            <tr>
                                <td><?php echo $i ?></td>
                                <td class="fonts"><?php echo $branchStudent['class_title'] ?></td>
                                <td><?php echo $branchStudent['tot'] ?></td>
                                <?php

                                $feeToPayStudent = 0;


                                foreach ($feeTypes as $feeType){
                                    //$discountedStudents[$feeType['id']][] = $branchDiscountCountArr[$branchStudent['class_id']][$feeType['id']];
                                    $totalExpectedFeesIncome[$feeType['id']][] = $branchStudent['tot'] * $feeStructureArr[$branchStudent['class_id']][$feeType['id']];
                                    //$totalDsicountSum[$feeType['id']][] = $branchDiscountArr[$branchStudent['class_id']][$feeType['id']];
                                    ?>
                                    <td><?php

                                        if(isset($branchDiscountArr[$branchStudent['class_id']][$feeType['id']])){

                                            foreach ($branchDiscountArr[$branchStudent['class_id']][$feeType['id']] as $branchDiscountArrRow){
                                                //$discountPercent = ($branchDiscountArrRow['fees'] - $branchDiscountArrRow['discount']);

                                                $monthlyStudentCount[$branchStudent['class_id']][] += $branchDiscountArrRow['tot'];

                                                $string = $branchDiscountArrRow['tot'];
                                                $string .= " ";
                                                $string .= $tool->transnoecho("student_fees");
                                                $string .= " ";
                                                $string .= $branchDiscountArrRow['fees'];
                                                $string .= " ";
                                                $string .= $tool->transnoecho("discount");
                                                $string .= " ";
                                                $string .= $branchDiscountArrRow['discount'];
                                                $string .= " ";
                                                $string .= $tool->transnoecho("total_fees");
                                                $string .= ($branchDiscountArrRow['fees'] - $branchDiscountArrRow['discount']);

                                                echo '<p>'.$string.'</p>';
                                                //echo '<p>Fees: '.$discountPercent. '</p>';

                                            }



                                        }
                                        else{
                                            echo '-';
                                        }

                                        //echo $branchDiscountCountArr[$branchStudent['class_id']][$feeType['id']] ?></td>
                                    <td><?php echo array_sum($monthlyStudentCount[$branchStudent['class_id']]) ?></td>
                                    <td><?php echo $feeStructureArr[$branchStudent['class_id']][$feeType['id']] ?></td>
                                    <td><?php echo $branchStudent['tot'] * $feeStructureArr[$branchStudent['class_id']][$feeType['id']] ?></td>


                                <?php } ?>
                            </tr>


                        <?php } ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td><?php echo $totalStudents ?></td>
                            <?php

                            foreach ($feeTypes as $feeType){
                                ?>

                                <td><?php echo array_sum($discountedStudents[$feeType['id']]) ?></td>
                                <td>&nbsp;</td>
                                <td><?php echo array_sum($totalExpectedFeesIncome[$feeType['id']]) ?></td>
                                <td><?php echo array_sum($totalDsicountSum[$feeType['id']]) ?></td>


                            <?php } ?>
                        </tr>


                        </tbody>
                    </table>
                </div>



            </div>
        </div>


    </div>


    <style type="text/css">
        @media print{
            .table, .table tr, table th, .table td {
                border-color: black;
            }
        }

    </style>

    <?php

    $tpl->footer();
    exit;



}


$tpl->footer();
