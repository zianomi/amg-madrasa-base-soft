<?php
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
Tools::getModel("FeeReport");
$fee = new FeeModel();
$stu = new StudentsModel();
$frt = new FeeReport();
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$id = (!empty($_GET['student_id'])) ? ($_GET['student_id']) : '';

$year = isset($_GET['year']) ? $tool->GetInt($_GET['year']) : "";
$month = isset($_GET['month']) ? $tool->GetInt($_GET['month']) : "";

$month = str_pad($month,2,0,STR_PAD_LEFT);

$invoiceDate = $year . "-" . $month . "-01";
$invoiceNewDate = $year . "-" . $month . "-10";
//$dueDate = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : '';


if(empty($id)){
    if(empty($session) || empty($branch) || empty($class) || empty($section)){
        echo $tool->transnoecho("please_select_session_branch_class_section");
        exit;
    }
}




if(!$tool->checkDateFormat($invoiceDate)){
    echo $tool->transnoecho("please_enter_valid_date");
    exit;
}



$param = array(
    "branch" => $branch
, "class" => $class
, "section" => $section
, "session" => $session
, "id" => $id
, "status" => 'current'
);
$valLogs = array();



$invoiceIds = array();


    $res = $stu->studentSearch($param);



    foreach ($res as $row){
        $stuid = $row['id'];

        if($frt->checkPendingDues($stuid) == "-1"){
            continue;
        }

        $invoiceGeneratedId = $frt->checkBankInvoice($stuid,$invoiceNewDate);

        if($invoiceGeneratedId == "-1"){
            $invoiceNumber = $fee->genInvoiceNumber($invoiceNewDate,$stuid);
            $data['invoice_id'] = $invoiceNumber;
            $data['student_id'] = $stuid;
            $data['recp_date'] = $invoiceDate;
            $data['invoice_status'] = $fee->paidStatus("bank");
            $data['fee_month'] = $invoiceNewDate;
            $data['created_user_id'] = $tool->getUserId();
            $data['created'] = date("Y-m-d H:i:s");
            $invoiceGeneratedId = $fee->insertSingleInvoice($data);
        }


         $update = array( 'invoice_id' => $invoiceGeneratedId );
         $update_where = array( 'student_id' => $stuid, 'paid_status' => $fee->paidStatus("pending") );
         $frt->update( 'jb_fee_paid', $update, $update_where );


        $invoiceIds[] = $invoiceGeneratedId;
    }




$branchDetail = $fee->getBranchBanck($branch);

$param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "student_id" => $id);

$invoiceData = $fee->getBankCopy($param);
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>ID# Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style type="text/css">
        .invoice-box {
            max-width: 390px;
            float: left;
            margin: 3px;
            padding: 8px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 15px;
            line-height: 20px;
            color: #555;
        }

        .invoice-box table {
            width: 340px;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 3px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 15px;
            line-height: 15px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 3px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            -webkit-print-color-adjust: exact;
            color: #000 !important;
            border-bottom: 3px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 3px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        #printReady {
            margin-top: 10px;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }

        }
    </style>


    <style type="text/css">
        .table, .table tr, .table td {
            border-color: black;
        }

        .small_voucher_copy {
            font-size: 9px;
            border-top: #FFF !important;
        }

        .note_copy {
            font-size: 11px;
            border-top: #FFF !important;
        }

        .small_pc_number {
            font-size: 12px;
        }

        .border_top_class {
            border-top: 1px solid #000000;
        }

        .border_bottom_class {
            border-bottom: 1px solid #000000;
        }

        .border_top_bottom {
            border-bottom: 1px solid #000000;
            border-top: 1px solid #000000;
        }

        .two_px_margin {
            margin-top: 4px;
        }

        .amount_detail {
            font-family: Verdana;
            font-weight: bold;
            font-size: 12px;
        }


    </style>
</head>


<body>


<?php


$students = array();
$feePaids = array();
$feeTypes = array();
$feeDates = array();



$voucherLabels = array(2 => "(Bank Copy)", 0 => "(School Copy)", 1 => "(Student Copy)");

foreach ($invoiceData as $invoiceRow) {

    if ($invoiceRow['gender'] == "بن") {
        $gender = " S/O ";
    } else {
        $gender = " D/O ";
    }


    $students[$invoiceRow['student_id']] = array(
      "id" => $invoiceRow['id']
    , "name" => $invoiceRow['name']
    , "gender" => $gender
    , "father_name" => $invoiceRow['father_name']
    , "grnumber" => $invoiceRow['grnumber']
    , "branch_name" => $invoiceRow['branch_title']
    , "branch_fone" => $invoiceRow['branch_fone']
    , "depart_name" => $invoiceRow['class_title']
    , "class_name" => $invoiceRow['section_title']
    , "invoice_id" => ""
    , "fees_date" => $invoiceRow['fee_date']
    , "student_id" => $invoiceRow['student_id']
    , "due_date" => ""
    );

    $feeDates[$invoiceRow['type_id']][$invoiceRow['student_id']][] = strtotime($invoiceRow['fee_date']);

    $feeAmountData[$invoiceRow['type_id']][$invoiceRow['student_id']][] = $invoiceRow['fees'] - $invoiceRow['discount'];


    $feeTypes[$invoiceRow['type_id']] = array(
        "type_id" => $invoiceRow['type_id']
    , "title" => $invoiceRow['title_en']
    , "duration_type" => $invoiceRow['duration_type']);


}



foreach ($students as $student) {

    $studentID = $student['student_id'];


    ?>

    <div id="printReady" style="margin-top: 4px; ">


        <div id="amgbox" style="width: 1250px !important; text-align: center; margin: 0 auto; ; ">


            <?php for ($i = 0; $i <= 2; $i++) { ?>


                <div class="invoice-box">
                    <table cellpadding="0" cellspacing="0">
                        <tr class="top">
                            <td colspan="2">
                                <table>
                                    <tr>
                                        <td style="width: 50%;">
                                            <span style="font-weight: bold; font-size: 14px;">Iqra Rauzatul Atfal Trust</span><br/>
                                            <?php echo $student['branch_name'] ?><br/>
                                            Tel: <?php echo $student['branch_fone'] ?><br/>

                                        </td>

                                        <td>
                                            <span class="small_voucher_copy"><?php echo $voucherLabels[$i] ?></span><br>
                                            Invoice #: <b><?php //echo $student['invoice_id'] ?></b><br>
                                            ID# <?php echo $student['student_id'] ?><br>
                                            Due: <?php //echo date('F d, Y', strtotime($student['due_date'])) ?>
                                            <br>

                                        </td>
                                    </tr>


                                </table>
                            </td>
                        </tr>


                        <tr class="heading">
                            <td colspan="2">Student Informatin</td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                ID# <b><?php echo $student['student_id'] ?></b><br>
                                Name:<b> <?php echo $student['name'] ?></b><br/>
                                Father:<b> <?php echo $student['father_name'] ?></b>
                            </td>
                        </tr>


                        <tr class="heading">
                            <td colspan="2">Bank Informatin</td>
                        </tr>

                        <tr class="details">
                            <td><?php echo $branchDetail['branch_bank'] ?><br/>

                                Br. Code <?php echo $branchDetail['branch_bank_code'] ?><br/>
                                Ph: <?php echo $branchDetail['branch_bank_phone'] ?>
                            </td>

                            <td>
                                <!--<span>A/C Title:</span>-->
                                <?php echo $branchDetail['branch_bank_title'] ?>
                                <br/><b>A/C No: <?php echo $branchDetail['branch_bank_ac_number'] ?></b>

                            </td>
                        </tr>


                        <tr class="heading">
                            <td>Payment Detail</td>

                            <td>
                                Amount
                            </td>
                        </tr>

                        <?php

                        $total = 0;
                        $completePayble = 0;
                        foreach($feeTypes as $feeType){

                            ?>

                            <tr class="item">
                                <td><?php

                                    if(isset($feeDates[$feeType['type_id']][$studentID])){
                                        if(count($feeDates[$feeType['type_id']][$studentID]) > 1){
                                            $firsDate = date('M Y', min($feeDates[$feeType['type_id']][$studentID]));
                                            $lastDate = date('M Y', max($feeDates[$feeType['type_id']][$studentID]));
                                            $dateToDis = $firsDate . " to " . $lastDate;
                                        }
                                        else{
                                            $dateToDis = date('M Y', $feeDates[$feeType['type_id']][$studentID][0]);
                                        }

                                        echo $feeType['title'] . " " . $dateToDis;
                                        $total = array_sum($feeAmountData[$feeType['type_id']][$studentID]);
                                        $completePayble += $total;

                                    }
                                    ?> </td>

                                <td><?php echo $total;  ?></td>
                            </tr>
                        <?php } ?>


                        <tr class="total" style="margin-top: 5px;">
                            <td style="text-transform: capitalize; font-size: 9px;"><?php echo $fee->convertNumberToWord($completePayble) ?> Only</td>

                            <td>Total: RS.<?php echo $completePayble ?></td>
                        </tr>


                        <tr>
                            <td colspan="2" class="border_top_class item last">
                                <div>
                                    <span style="font-weight: bold">Note:</span>
                                    <ul style="margin-top: -1px;">
                                        <!--<li class="note_copy">This voucher can be deosited in any MBL branch in
                                            Karachi.
                                        </li>
                                        <li class="note_copy">Bank will not accept this voucher after due date.</li>-->
                                        <li class="note_copy">No one authorized to collect cash fee at school.</li>
                                        <li class="note_copy">This voucher is issued without any alteration and correction.
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>


                    </table>
                </div>

            <?php } ?>
        </div>

    </div>
    <div class="page-break" style="padding-bottom: 15px"></div>

<?php } ?>

</body>
</html>


