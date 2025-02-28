<?php
$errors = array();
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
$fee = new FeeModel();
$stu = new StudentsModel();


if(isset($_POST['_chk']) == 1){


    $userId = Tools::getUserId();

    $paid = $fee->paidStatus("paid");
    $date = date("Y-m-d");
    $dateFormated = date("d-m-Y");
    $stuid = $tool->GetInt($_POST['stuid']);
    $branch = $tool->GetInt($_POST['branch_id']);
    $class = $tool->GetInt($_POST['class_id']);
    $section = $tool->GetInt($_POST['section_id']);
    $session = $tool->GetInt($_POST['session_id']);
    $vals = array();
    $valInv = array();
    $year = date("Y");
    $month = str_pad(date("m"),2,0,STR_PAD_RIGHT);
    $invoiceDate = $year . "-" . $month . "-10";
    $dateTime = date("Y-m-d H:i:s");





    if(empty($stuid)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("ID Required"));
    }
    if(empty($branch)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("Branch Required"));
    }
    if(empty($class)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("Class Required"));
    }
    if(empty($section)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("Section Required"));
    }
    if(empty($session)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("Session Required"));
    }
    $status = $fee->invoiceStatus("paid");


    $invoiceNumber = $fee->genInvoiceNumber($invoiceDate,$stuid);



    $data['invoice_id'] = $invoiceNumber;
    $data['student_id'] = $stuid;
    $data['recp_date'] = $date;
    $data['invoice_status'] = $fee->paidStatus("paid");
    $data['fee_month'] = $invoiceDate;
    $data['created_user_id'] = $tool->getUserId();
    $data['created'] = $dateTime;


    $invoiceGeneratedId = $fee->insertSingleInvoice($data);
    $currentUserId = Tools::getUserId();
    $todayDateTime = date("Y-m-d H:i:s");


    foreach ($_POST['type_id'] as $typeKey){



        foreach ($_POST['months'] as $monthKey){
            $idDate = date("ym", strtotime($monthKey));
            $invoceNumber = $fee->genInvoiceNumber($monthKey,$stuid);
            $dueDate = $fee->setAdvancedInvoiceDueDate($monthKey);
            $postDateArr = explode("-",$monthKey);
            $postYear = $postDateArr[0];
            $postMonth = $postDateArr[1];
            $feeDate = $postYear . "-" . $postMonth . "-01";

            if(isset($_POST['amount'][$typeKey][$monthKey])){


                //$feeAmount = $_POST['amount'][$typeKey][$monthKey];

                $feeAmount = $_SESSION['amg_advanced_fee']['amount'][$stuid][$typeKey][$monthKey];

                    if(isset($_POST['discounts'][$typeKey][$monthKey])){
                        //$discount = $_POST['discounts'][$typeKey][$monthKey];
                        $discount =  $_SESSION['amg_advanced_fee']['discounts'][$stuid][$typeKey][$monthKey];;
                    }
                    else{
                        $discount = 0;
                    }

                $singleStudentFeeData = $fee->getStudentFees(array("student_id" => $stuid, "fee_type" => array($typeKey)));

                if(!empty($singleStudentFeeData)){
                    $rowSingleStudentFeeData = $singleStudentFeeData[0];
                    $feeAmount = $rowSingleStudentFeeData['fees'];
                    $discount = $rowSingleStudentFeeData['discount'];
                }

                //$vals[] = array("NULL",$stuid,$typeKey,$branch,$class,$section,$session,$feeAmount,$discount,$status,$feeDate,$monthKey,$invoiceGeneratedId,NULL);
                $vals[] = array($stuid,$typeKey,$branch,$class,$section,$session,$feeAmount,$discount,$status,$feeDate,$monthKey,$invoiceGeneratedId,$currentUserId,$currentUserId,$todayDateTime,$todayDateTime);
            }
        }
    }


    if(count($errors)==0) {
        unset($_SESSION['amg_advanced_fee']);
        try {
            $fee->query("START TRANSACTION");
            $fee->insertAdvancedFees($vals);
            $fee->query("COMMIT");
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("Fee recieved"));
            unset($_SESSION['amg_advanced_fee']);
            $tool->Redir("fees","printinvoice&_chk=1&student_id=".$stuid."&id=".$invoiceGeneratedId,"","");
        } catch (Exception $e) {
            $fee->query("ROLLBACK");
            $_SESSION['msg'] = $tool->Message("alert", $tool->transnoecho("Fee not recieved. Error"));
            $tool->Redir("fees","advancefee","","");
        }

    }



}


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);

?>


<div class="social-box">
    <div class="header">

    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>
        <div id="advanced-search" class="in collapse">
        <form action="" method="get">
                <?php echo $tpl->FormHidden(); ?>
                <div align="center">

                    <div class="row-fluid">
                        <div class="span12"><label class="fonts"><?php $tool->trans("id") ?></label>
                            <input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>" type="text" name="student_id" id="student_id"/>
                            <button type="submit" class="btn btn-small" style="margin-bottom:10px;"><?php $tool->trans("search") ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div id="printReady">
                    <?php
                    $id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : NULL;
                    if (isset($_GET['_chk']) == 1) {
                        if (empty($id)) {
                            echo $tool->Message("alert",$tool->transnoecho("please_insert_id"));
                            $tpl->footer();
                            exit;
                        }

                        $parameters = array("id"=>$id);

                        $pendingExists = $fee->seePendingInvoince($id);

                        if($pendingExists){
                            echo $tool->Message("alert",$tool->transnoecho("please_clear_pending_dues"));
                            $tpl->footer();
                            exit;
                        }

                        $res = $stu->studentSearch($parameters);
                        $total = count($res);
                        if ($total < 1) {
                            echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
                            $tpl->footer();
                            exit;
                        }


                        $row = $res[0];


                        $branchStuid = $stu->operatorBranchStudent($row['branch_id']);
                        if(empty($branchStuid)){
                            echo $tool->Message("alert",$tool->transnoecho("no_student_found"));
                            $tpl->footer();
                            exit;
                        }



                        ?>



                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th><?php $tool->trans("id") ?></th>
                                    <th><?php $tool->trans("gr") ?></th>
                                    <th><span class="fonts"><?php $tool->trans("name_father_name") ?></span></th>
                                    <th><span class="fonts"><?php $tool->trans("branch") ?></span></th>
                                    <th><span class="fonts"><?php $tool->trans("class") ?></span></th>
                                    <th><span class="fonts"><?php $tool->trans("section") ?></span></th>

                                </tr>
                                </thead>


                                <tbody>
                                <?php
                                $a=0;


                                    ?>
                                    <tr>

                                        <td class="avatar"><?php echo $row['id']; ?></td>
                                        <td class="avatar"><?php echo $row['grnumber']; ?></td>
                                        <td class="username"><span class="fonts">
                                  <?php echo $row['name']; ?> <?php echo $tpl->getGenderTrans($row['gender']); ?>  <?php echo $row['fname']; ?></span>
                                        </td>
                                        <td class="firstname"><span class="fonts">
                                  <?php echo $row['branch_title']; ?></span>
                                        </td>
                                        <td class="lastname"><span class="fonts">
                                  <?php echo $row['class_title']; ?></span>
                                        </td>
                                        <td class="sex"><span class="fonts">
                                  <?php echo $row['section_title']; ?></span>
                                        </td>
                                    </tr>



                                </tbody>
                            </table>
                        </div>
                    <?php
                    $sessionData = $set->getCurrentSession();
                    $discountData = $fee->discountsList(array("id" => $id));
                    //$invoiceData = $fee->getPendingInvoice($id);


                    $discountArr = array();
                    $paidData = array();

                    if(!empty($discountData)){
                        foreach ($discountData as $discount){
                            $discountArr[$discount['type_id']] = $discount;
                        }
                    }


                    $feeStructureData = $fee->genAdvanceFeeStructure($row['branch_id'],$row['class_id'],$row['session_id']);
                    $sessionStartDate = $sessionData['start_date'];
                    $sessionEndDate = $sessionData['end_date'];
                    $start = strtotime($sessionStartDate);
                    $end = strtotime($sessionEndDate);
                    $currentdate = $start;

                    $paidDataAll = $fee->getPaidStudentData(array("id" => $id, "start" => $sessionStartDate, "end" => $sessionEndDate));


                    foreach ($paidDataAll as $paidDataRow){
                        $dateArr = explode("-",$paidDataRow['fee_date']);
                        $year = $dateArr[0];
                        $month = $dateArr[1];
                        $paidData[$paidDataRow['type_id']][$year][$month][] = $paidDataRow['fees'] - $paidDataRow['discount'];
                    }



                    ?>


                    <?php
                    echo $tpl->FormTag("post");
                    echo $tpl->FormHidden();
                    ?>
                    <input type="hidden" name="stuid" value="<?php echo $id ?>">
                    <input type="hidden" name="branch_id" value="<?php echo $row['branch_id'] ?>">
                    <input type="hidden" name="class_id" value="<?php echo $row['class_id'] ?>">
                    <input type="hidden" name="section_id" value="<?php echo $row['section_id'] ?>">
                    <input type="hidden" name="session_id" value="<?php echo $row['session_id'] ?>">

                <table class="table table-bordered table-striped table-hover">

                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><?php $tool->trans("Sr#") ?></th>
                            <?php  foreach ($feeStructureData as $feeStructure){

                                if($feeStructure['type_id'] != 1){
                                    continue;
                                }

                                ?>
                            <th><?php echo $feeStructure['title'] ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <?php
                    $a=0;
                    $total=0;

                    $singleStudendFeeData = $fee->getStudentFees(array("student_id" => $id, "fee_type" => array(1)));
                    $singleFee = false;


                    if(!empty($singleStudendFeeData)){
                        $singleStudendFeeRow = $singleStudendFeeData[0];
                        $singleFee = true;
                    }

                    while($currentdate < $end) {
                        $cur_date = date('F, Y', $currentdate);
                        $sqlFormatedDate = date('Y-m-d', $currentdate);
                        $currentdate = strtotime('+1 month', $currentdate);

                        $a++;

                        $dateArr = explode("-",$sqlFormatedDate);
                        $year = $dateArr[0];
                        $month = $dateArr[1];
                        $dateVal = $year . "-" . $month . "-10";
                    ?>
                        <input type="hidden" name="months[]" value="<?php echo $dateVal ?>">
                    <tr>
                        <td><?php echo $a ?></td>
                        <td><?php echo $cur_date ?></td>
                        <?php  foreach ($feeStructureData as $feeStructure){

                            if($feeStructure['type_id'] != 1){
                                continue;
                            }

                            $discountTotal = 0;

                            if($singleFee){
                                $discountTotal = $singleStudendFeeRow['discount'];
                                $fees = $singleStudendFeeRow['fees'];

                            }
                            else{
                                if(isset($discountArr[$feeStructure['type_id']])){
                                    $discountAmount = $discountArr[$feeStructure['type_id']]['amount'];
                                    $discountTotal = $discountAmount;

                                }
                                $fees = $feeStructure['fees'];
                            }





                            $amount = $fees - $discountTotal;




                            ?>
                        <td>
                            <?php



                            if(isset($paidData[$feeStructure['type_id']][$year][$month])){
                               echo $paidData[$feeStructure['type_id']][$year][$month][0] . " Paid";
                           }
                           else{

                               if($discountTotal > 0){
                                   $str = $fees . "-" . $discountTotal;
                               }
                               else{
                                   $str = $amount;
                               }

                               echo $str . '<input type="checkbox" value="'.$fees.'" name="amount['.$feeStructure['type_id'].']['.$dateVal.']" rel="'.$amount.'">';
                               $_SESSION['amg_advanced_fee']['amount'][$id][$feeStructure['type_id']][$dateVal] = $fees;
                           }
                            ?>
                        </td>
                            <input type="hidden" name="discounts[<?php echo $feeStructure['type_id'] ?>][<?php echo $dateVal ?>]" value="<?php echo $discountTotal ?>">
                            <input type="hidden" name="type_id[<?php echo $feeStructure['type_id'] ?>]" value="<?php echo $feeStructure['type_id'] ?>">
                        <?php
                        $_SESSION['amg_advanced_fee']['discounts'][$id][$feeStructure['type_id']][$dateVal] = $discountTotal;
                        $_SESSION['amg_advanced_fee']['type_id'][$id][$feeStructure['type_id']] = $feeStructure['type_id'];

                        } ?>
                    </tr>
                    <?php } ?>

                    <tr>
                        <td colspan="7" id="output" style="text-align: left">Total: <?php echo $total ?></td>
                    </tr>

                    <tr>
                        <td colspan="7" id="output" style="text-align: center"><button class="btn btn-success">
                                                                    <i class="icon-filter"></i>Save</button></td>
                    </tr>
                </table>


                <?php $tpl->formClose() ?>

<?php } ?>
                </div>



    </div>
</div>


<script type="text/javascript">

       $(document).ready(function() {
           function recalculate() {
               var sum = 0;

               $("input[type=checkbox]:checked").each(function() {
                   sum += parseInt($(this).attr("rel"));
               });

               $("#output").html("Total: "+sum);
           }

           $("input[type=checkbox]").change(function() {
               recalculate();
           });
       });
   </script>
<?php
$tpl->footer();