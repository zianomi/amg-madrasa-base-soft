<?php
Tools::getModel("FeeModel");
$fee = new FeeModel();
$tpl->setCanPrint(false);

if(isset($_GET['_chk'])
&& !empty($_GET['_chk'])
)
{


    $date = (isset($_GET['date'])) ? (!empty($_GET['date']) ? $tool->ChangeDateFormat($_GET['date']) : "") : '';
    $invoice_number = isset($_GET['invoice_number']) ? $fee->escape($_GET['invoice_number']) : '';
    $student_id = isset($_GET['student_id']) ? $tool->GetInt($_GET['student_id']) : '';

    $invoice = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : '';
    //$id = isset($_GET['student_id']) ? $tool->GetInt($_GET['student_id']) : '';


if(empty($invoice_number) && empty($date) && empty($student_id)){

    if (empty($invoice)) {
        $tpl->renderBeforeContent();
        echo "<h3>Invoice Required.</h3>";
        $tpl->footer();
        exit;
    }

}

if(empty($invoice)){
    if(empty($date) && empty($invoice_number) && empty($student_id)){
        $tpl->renderBeforeContent();
        echo "<h3>Please select id, date or invoice number.</h3>";
        $tpl->footer();
        exit;
    }

    if(
            !empty($student_id) && empty($date) ||
            !empty($date) && empty($student_id)

    ){
        $tpl->renderBeforeContent();
        echo "<h3>Please select id with date.</h3>";
        $tpl->footer();
        exit;
    }
}




Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();


$fsData = $fee->recpData(array(

         "invoice" => $invoice
        , "id" => $student_id
        , "date" => $date
        , "invoice_number" => $invoice_number
        )
);


if(count($fsData)<1){
    $tpl->renderBeforeContent();
    echo "<h3>No record found.</h3>";
    $tpl->footer();
    exit;
}

$students = array();
$feeTypes = array();
$feePaids = array();
$feeInvoices = array();
$feeDates = array();


foreach ($fsData as $feePaid) {

    if ($feePaid['gender'] == "بن") {
        $gender = " S/O ";
    } else {
        $gender = " D/O ";
    }
    $students = array(
            "id" => $feePaid['student_id']
    , "name" => $feePaid['name']
    , "gender" => $gender
    , "father_name" => $feePaid['father_name']
    , "grnumber" => $feePaid['grnumber']
    , "branch_name" => $feePaid['branch_title']
    , "branch_fone" => $feePaid['branch_fone']
    , "depart_name" => $feePaid['class_title']
    , "class_name" => $feePaid['section_title']
    //, "fees_date" => $feePaid['due_date']
    , "recp_date" => $feePaid['recp_date']
    , "invoice_id" => $feePaid['invoice_number']
    , "branch_title" => $feePaid['branch_title']
    , "username" => $feePaid['username']
    , "userid" => $feePaid['userid']
    );




    //$feePaids[] = $feePaid;

    $feeDates[$feePaid['type_id']][] = strtotime($feePaid['fee_date']);

    $feeInvoices[$feePaid['invoice_id']] = $feePaid['invoice_number'];
    $feeTypes[$feePaid['type_id']] = array("type_id" => $feePaid['type_id'], "type_title" => $feePaid['title_en']);

    $feeAmountData[$feePaid['type_id']][] = $feePaid['fees'] - $feePaid['discount'];

}




$tpl->renderBeforeContent();
$tpl->setCanExport(false);
$student = $students;

$qr->searchContentAbove();
$qr->searchContentBottom();
?>





<style type="text/css" media="all">
           .invoice-box{
                   max-width:420px;
                   max-height:298px;
                   margin:auto;
                   padding:5px;
                   border:1px solid #eee;
                   box-shadow:0 0 10px rgba(0, 0, 0, .15);
                   font-size:12px;
                   line-height:16px;
                   font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                -webkit-print-color-adjust: exact; color:#555 !important; -webkit-print-color-adjust: exact;
               direction: ltr;
               }

               .invoice-box table{
                   width:100%;

                   text-align:left;
                   direction: ltr;
               }

               .invoice-box table td{
                   padding:2px;
                   vertical-align:top;
                   direction: ltr;
               }

               .invoice-box table tr td:nth-child(2){
                   text-align:right;
                   direction: ltr;
               }

               .invoice-box table tr.top table td{
                   padding-bottom:1px;
                   direction: ltr;
               }

               .invoice-box table tr.top table td.title{
                   font-size:26px;
                   line-height:26px;
                   color:#333;
                   direction: ltr;
               }

               .invoice-box table tr.information table td{
                   padding-bottom:9px;
                   direction: ltr;
               }

               .invoice-box table tr.heading td{
                   background:#eee;
                   border-bottom:1px solid #ddd;
                   font-weight:bold;
                   direction: ltr;
                   background:#eee !important; -webkit-print-color-adjust: exact;
                   color:#000 !important; -webkit-print-color-adjust: exact;
               }

               .invoice-box table tr.details td{
                   padding-bottom:7px;
                   direction: ltr;
               }

               .invoice-box table tr.item td{
                   border-bottom:1px solid #eee;
                   direction: ltr;
               }

               .invoice-box table tr.item.last td{
                   border-bottom:none;
                   direction: ltr;
               }

               .invoice-box table tr.total td:nth-child(2){
                   border-top:1px solid #eee;
                   font-weight:bold;
                   direction: ltr;
               }

           .small_voucher_copy {
               font-size: 7px;
               border-top: #FFF !important;
               direction: ltr;
           }

           .note_copy {
               font-size: 9px;
               border-top: #FFF !important;
               direction: ltr;
           }

           .border_top_class {
               border-top: 1px solid #000000;
               direction: ltr;
           }

               @media only screen and (max-width: 500px) {
                   .invoice-box table tr.top table td{
                       width:100%;
                       display:block;
                       text-align:center;
                       direction: ltr;
                   }

                   .invoice-box table tr.information table td{
                       width:100%;
                       display:block;
                       text-align:center;
                       direction: ltr;
                   }
               }


           @media print {
               .no-print, .no-print * {
                   display: none !important;
               }

               body * {
                   visibility: hidden;
                   overflow: hidden;
                   max-height: 298px !important;
               }

               #printReady, #printReady * {
                   visibility: visible;
                   overflow: hidden;
               }

               #printReady {
                   position: absolute;
                   left: 3px;
                   right: 3px;
                   top: 3px;
                   bottom: 3px;
               }

           }

           @page {
               counter-increment: page;
               counter-reset: page 1;
               /*@top-right {
                   content: "Page " counter(page) " of " counter(pages);
               }*/
           }
       </style>

<div id="printReady" style="max-height:310px;">
    <div class="invoice-box" style="margin: 0 auto">
        <table cellpadding="0" cellspacing="0">

            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td style="width: 50%;">
                                <span style="font-weight: bold; font-size: 14px;">Iqra Rauzatul Atfal Trust</span><br/>
                                Branch: <?php echo $student['branch_name'] ?><br/>
                                Class: <?php echo $student['depart_name'] ?><br/>
                                <!--Tel: <?php /*echo $student['branch_fone'] */?><br/>-->

                            </td>

                            <td><span class="small_voucher_copy">(Student Copy)</span>
                                ID: <?php echo $student['id'] ?><br>
                                Section: <?php echo $student['class_name'] ?><br>
                                Invoice:
                                <?php
                                foreach($feeInvoices as $feeInvoice){ ?>
                                    <?php echo $feeInvoice ?><br>
                                <?php } ?>
                                Date: <?php echo date('F d, Y', strtotime($student['recp_date'])) ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr class="heading" style="background:#eee;">
                <td colspan="2">Student Informatin</td>
            </tr>

            <tr>
                <td colspan="2">
                    <!--ID# <b><?php /*echo $student['id'] */?></b><br>-->
                    Name:<b> <?php echo $student['name'] ?></b><br/>
                    Father:<b> <?php echo $student['father_name'] ?></b>
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
                    $rowCount = 0;
                    foreach($feeTypes as $feeType){
                        $rowCount++;

                    ?>
                        <tr class="item">
                            <td style="font-size: 12px;">
                                <?php

                                if(count($feeDates[$feeType['type_id']]) > 1){
                                    $firsDate = date('M Y', min($feeDates[$feeType['type_id']]));
                                    $lastDate = date('M Y', max($feeDates[$feeType['type_id']]));
                                   $dateToDis = $firsDate . " to " . $lastDate;
                                }
                                else{
                                    $dateToDis = date('M Y', $feeDates[$feeType['type_id']][0]);
                                }

                                echo $feeType['type_title'] . " " . $dateToDis;
                                $total = array_sum($feeAmountData[$feeType['type_id']]);

                                $completePayble += $total;
                                ?>
                            </td>
                            <td><?php echo $total ?></td>
                        </tr>
                    <?php } ?>




            <tr class="total" style="margin-top: 5px;">
                <td style="text-transform: capitalize; font-size: 13px;"><?php echo $fee->convertNumberToWord($completePayble) ?> Only</td>

                <?php
                if($rowCount == 1){
                    $pxs = "13px";
                }
                else if($rowCount == 2){
                    $pxs = "12px";
                }
                else if($rowCount == 3){
                    $pxs = "11px";
                }
                else{
                    $pxs = "10px";
                }
                ?>
                <td style="font-size: <?php echo $rowCount ?>">Total: RS <?php echo $completePayble ?></td>
            </tr>



            <tr>
                <td colspan="2" class="border_top_class item last">
                    <div>
                        <p style="font-size: 10px; font-weight: bold">By: <?php
                            echo $students['username']?> <?php echo $fee->cashierBranch($students['userid']);?></p>
                        <p class="note_copy" style="margin-top: -10px">Please save this voucher for future use.</p>
                    </div>
                </td>
            </tr>


        </table>
    </div>
</div>






<?php
$tpl->footer();

}
else{

    $tpl->renderBeforeContent();

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



                        <div class="row-fluid" id="student_res"></div>
                        <div class="row-fluid">
                            <div class="span3">
                                <label class="fonts"><?php $tool->trans("id") ?></label>
                                <input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>" type="text" name="student_id" id="student_id"/>
                            </div>

                            <div class="span3">
                                <label class="fonts"><?php $tool->trans("Recieve Date") ?></label>
                                <input value="<?php if(isset($_GET['date'])) echo $_GET['date'] ?>" class="date" type="text" name="date" id="date"/>
                            </div>


                            <div class="span3">
                                <label class="fonts"><?php $tool->trans("Invoice Number") ?></label>
                                <input value="<?php if(isset($_GET['invoice_number'])) echo $_GET['invoice_number'] ?>" type="text" name="invoice_number" id="invoice_number"/>
                            </div>


                            <div class="span3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-small" style="margin-bottom:10px;"><?php $tool->trans("search") ?></button>
                            </div>
                        </div>
                        </div>
                    </div>
                </form>
            </div>






        </div>
    </div>
<?php
$tpl->footer();
}
