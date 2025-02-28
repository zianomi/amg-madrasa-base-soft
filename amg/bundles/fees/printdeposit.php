<?php
$id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : "";

if(empty($id)){
    echo 'Error.';
    exit;
}
Tools::getModel("FeeModel");
$fee = new FeeModel();
$tpl->renderBeforeContent();

$total = $fee->GetDepositAmount($id);
$depositData = $fee->GetDepositDetail($id);

?>
    <div id="printReady">
    <div class="social-box">
        <div class="header"></div>
        <div class="body">
            <div class="row-fluid">
                <div class="invoice-box">
                <table cellpadding="0" cellspacing="0" style="width: 500px;">
                    <tr class="top">
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td style="width: 50%;">
                                        <span style="font-weight: bold; font-size: 14px;"><?php echo $depositData['bank'] ?></span><br/>
                                        A/C Title: <?php echo $depositData['account_title'] ?><br/>
                                        A/C Number: <?php echo $depositData['account_number'] ?><br/>
                                        Date: <?php echo date('F d, Y') ?><br/>
                                    </td>
                                </tr>
                                <tr class="heading" style="background:#eee;">
                                    <td colspan="2">Total: <?php echo $total; ?></td>
                                </tr>

                                <tr class="total" style="margin-top: 5px;">
                                   <td style="text-transform: capitalize; font-size: 13px;"><?php echo $fee->convertNumberToWord($total) ?> Only</td>

                                   <td style="font-size: 13px">Total: RS <?php echo $total ?></td>
                               </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                </div>
            </div>
        </div>
    </div>
    </div>

    <style type="text/css" media="all">

        @media print {
          body * {
            visibility: hidden;
          }
          #printReady, #printReady * {
            visibility: visible;
          }
          #printReady {
            position: absolute;
            left: 0;
            top: 0;
          }
        }

               .invoice-box{
                       max-width:500px;
                       margin:auto;
                       padding:15px;
                       border:1px solid #eee;
                       box-shadow:0 0 10px rgba(0, 0, 0, .15);
                       font-size:14px;
                       line-height:20px;
                       font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                    -webkit-print-color-adjust: exact; color:#555 !important; -webkit-print-color-adjust: exact;
                   direction: ltr;
                   }

                   .invoice-box table{
                       width:100%;
                       line-height:inherit;
                       text-align:left;
                       direction: ltr;
                   }

                   .invoice-box table td{
                       padding:3px;
                       vertical-align:top;
                       direction: ltr;
                   }

                   .invoice-box table tr td:nth-child(2){
                       text-align:right;
                       direction: ltr;
                   }

                   .invoice-box table tr.top table td{
                       padding-bottom:10px;
                       direction: ltr;
                   }

                   .invoice-box table tr.top table td.title{
                       font-size:35px;
                       line-height:35px;
                       color:#333;
                       direction: ltr;
                   }

                   .invoice-box table tr.information table td{
                       padding-bottom:20px;
                       direction: ltr;
                   }

                   .invoice-box table tr.heading td{
                       background:#eee;
                       border-bottom:1px solid #ddd;
                       font-weight:bold;
                       direction: ltr;
                       background:#eee !important; -webkit-print-color-adjust: exact; color:#000 !important; -webkit-print-color-adjust: exact;
                   }

                   .invoice-box table tr.details td{
                       padding-bottom:10px;
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
                       border-top:2px solid #eee;
                       font-weight:bold;
                       direction: ltr;
                   }

                   @media only screen and (max-width: 600px) {
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
           </style>

   <style type="text/css" media="all">


                   .small_voucher_copy {
                       font-size: 9px;
                       border-top: #FFF !important;
                       direction: ltr;
                   }

                   .note_copy {
                       font-size: 11px;
                       border-top: #FFF !important;
                       direction: ltr;
                   }

                   .border_top_class {
                       border-top: 1px solid #000000;
                       direction: ltr;
                   }






           </style>
<?php
$tpl->footer();

