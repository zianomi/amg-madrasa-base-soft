<?php
Tools::getLib("QueryTemplate");
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
Tools::getLib("TemplateForm");
$qr = new QueryTemplate();
$fee = new FeeModel();
$stu = new StudentsModel();
$tpf = new TemplateForm();
$errors = array();
$vals = array();
$valPaid = array();
$stuInvoice = array();
$invoiceDueDate = array();
$discounts = array();
$discount = array();
$singleStudentDataArr = array();

if(isset($_POST['_chk']) == 1){

    $branch = isset($_POST['branch']) ? $tool->GetExplodedInt($_POST['branch']) : "";
    $class = isset($_POST['class']) ? $tool->GetExplodedInt($_POST['class']) : "";
    $session = isset($_POST['session']) ? $tool->GetExplodedInt($_POST['session']) : "";
    $feeTypes = isset($_POST['fee_types']) ? ($_POST['fee_types']) : "";
    $year = isset($_POST['year']) ? $tool->GetInt($_POST['year']) : "";
    $month = isset($_POST['month']) ? $tool->GetInt($_POST['month']) : "";
    //$dueDate = isset($_POST['date']) ? $tool->ChangeDateFormat($_POST['date']) : "";
    $feeTypeArr = array();
    foreach ($feeTypes as $rowFeeTypes){
        $feeTypeArr[] = $tool->GetExplodedInt($rowFeeTypes);
    }



    $month = str_pad($month,2,0,STR_PAD_LEFT);

    $invoiceDate = $year . "-" . $month . "-01";
    $dueDate = $year . "-" . $month . "-10";
    //$idDate = date("ym", strtotime($invoiceDate));;

    if(empty($branch) || empty($session) || empty($feeTypes)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("all_fields_required"));
    }

    /*if(!$tool->checkDateFormat($dueDate)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("invalid_due_date"));
    }*/

    if(!$tool->checkDateFormat($invoiceDate)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("invalid_invoice_date"));
    }

    if(!is_array($feeTypes)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_atleast_one_fee_type"));
    }





    $param = array();
    $param['branch'] = $branch;

    $param['session'] = $session;
    $param['class'] = $class;
    $param['fee_type'] = $feeTypes;
    $param['fee_types'] = $feeTypeArr;
    $branchFeeData = $fee->getBranchFees($param);
    //$currentClass = $set->sessionCurrentClasses($session,$branch);
    $singleStudendFeeData = $fee->getStudentFees($param);

    $countBranchFee = count($branchFeeData);

    foreach ($singleStudendFeeData as $key){
        $singleStudentDataArr[$key['student_id']][$key['type_id']] = array("fees" => $key['fees'], "discount" => $key['discount']);
    }




    $feeData = array();
    $typesData = array();
    $classesData = array();

    foreach($branchFeeData as $row){
        $typesData[$row['fee_type_id']] = $row['fee_type_id'];
        $classesData[$row['class_id']] = $row['class_id'];
        $feeData[$row['class_id']][$row['fee_type_id']] = $row['fees'];
    }

    $param['class'] = $class;
    $branchStudents = $fee->invoiceStudents($branch,$session,$param);



    $discountData = $fee->discountedStudents($param);

    foreach($discountData as $rowDiscount){
       $discounts[$rowDiscount['id']][$rowDiscount['type_id']] = array("amount" => $rowDiscount['amount'], "type_id" => $rowDiscount['type_id']);
    }




    foreach($branchStudents as $branchStudent){
        $class = $branchStudent['class_id'];
        $section = $branchStudent['section_id'];
        $stuid = $branchStudent['id'];

        foreach($typesData as $typeData){
            $typeId = $typeData;
            $fees = $feeData[$branchStudent['class_id']][$typeId];

            if(isset($discounts[$branchStudent['id']][$typeId])){
                //$symbol 			= $discounts[$branchStudent['id']][$typeId]['symbol'];
                $amount 			= $discounts[$branchStudent['id']][$typeId]['amount'];

                /*if(($symbol) == 2){
                   $discount = (($fees * $amount) / 100);
                }else{
                   $discount = $amount;
                }*/


                $discount = $amount;

                /*if($discount == $fees){
                   $status = $fee->paidStatus("exempt");
                }*/
            }
            else{
                $symbol = "";
                $amount = 0;
                $discount = 0;
            }


            if(isset($singleStudentDataArr[$stuid][$typeId])){
                $singleFeeRow = $singleStudentDataArr[$stuid][$typeId];
                $fees = $singleFeeRow['fees'];
                $discount = $singleFeeRow['discount'];
            }


            if($discount >= $fees){
                $paidStatus = $fee->paidStatus("exempt");
                $discount = $fees;
            }
            else{
                $paidStatus = $fee->paidStatus("pending");
            }

            if(!empty($fees)){
                $vals[] = $tool->setInsertDefaultValues(array("NULL",$stuid,$typeId,$branch,$class,$section,$session,$fees,"$discount",$paidStatus,$invoiceDate,$dueDate));
            }

        }
    }

    if(count($errors)==0) {
       try {
           $fee->query("START TRANSACTION");
           $fee->insertFees($vals);
           $fee->query("COMMIT");
       } catch (Exception $e) {
           $fee->query("ROLLBACK");
       }
    }


    /*$countData = $fee->countRecords($branch,$invoiceDate);
    $typeCount  = count($_POST['fee_types']);
    $totalStudents = $countData['totstu'];
    $totalPaid = $countData['totpaid'];
    $needPaid = $totalStudents * $typeCount;


    if($totalPaid != $needPaid){
        $fee->deleteChalan($branch,$invoiceDate);
        $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("chalan_not_generaed"));
        $tool->Redir("fees","geninvoice","","");
        exit;
    }*/




    $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("invoice_generated"));
    $tool->Redir("fees","geninvoice","","");
    exit;

}

$qr->setFormMethod("post");
$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();


?>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses(); ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("year") ?></label>
        <select name="year" id="year">
                <?php echo $tpf->NewYearsDropDown(); ?>
            </select>
    </div>



</div>
    <div class="row-fluid">
        <div class="span3">
            <label class="fonts"><?php $tool->trans("month") ?></label>
                <select name="month" id="month">
                        <?php echo $tpf->NewMonthDropDown(); ?>
                    </select>
            </div>
        <div class="span3"><label class="fonts"><?php $tool->trans("fee_type") ?></label>
            <!--<select name="fee_type" id="fee_type">
                <?php /* */?>
                <?php /*echo $tpl->GetOptionVals(array("data" => $typeData, "sel" => "")); */?>
            </select>-->

            <?php
            $typeData = $set->getTitleTable("fee_type");
                  echo $tpl->GetMultiOptions(array("name" => "fee_types[]", "data" => $typeData, "sel" => ""));
              ?>
        </div>

    <!--<div class="span3"><label class="fonts"><?php /*$tool->trans("due_date") */?></label><?php /*echo $tpl->getDateInput(); */?></div>-->


        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3">&nbsp;</div>


    </div>
<?php
$qr->searchContentBottom();


$tpl->footer();

