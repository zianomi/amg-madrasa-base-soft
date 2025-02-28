<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 7/20/2018
 * Time: 6:04 PM
 */
Tools::getModel("FeeExportModel");
$feeExp = new FeeExportModel();
Tools::getLib("Ncrypt");
$ncrypt = new Ncrypt();
Tools::getModel("FeeModel");
$fee = new FeeModel();
$errors = array();
if (isset($_POST['_chk']) == 1) {
    $data = isset($_POST['data']) ? $_POST['data'] : "";

    if(empty($data)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_data"));
    }


    $ncrypt->set_secret_key(KEY);  // optional, but STRONGLY recommended
    $ncrypt->set_secret_iv(CHIPER);  // optional, but STRONGLY recommended
    $ncrypt->set_cipher(CHIPER_OPT);       // optional

    $originalData = $ncrypt->decrypt($data);
    $dataArr = json_decode($originalData,true);

    if(empty($dataArr)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("invalid Data"));
    }

    if(empty($dataArr['data'])){
        $errors[] = $tool->Message("alert",$tool->transnoecho("invalid Data"));
    }

    $dataArray = $dataArr['data'];
    $year = date("Y");
    $month = str_pad(date("m"),2,0,STR_PAD_RIGHT);
    $dateTime = date("Y-m-d H:i:s");



    foreach ($dataArray as $row){
        $insertNew = false;
        $paidIds = array();
        $alreadyPaidIds = array();
        $invoices = $row['invoices'];
        $paids = $row['paids'];
        $stuid = $invoices['student_id'];
        $userId = $invoices['user_id'];
        $date = $invoices['recp_date'];
        $offlineInvoiceId = $invoices['invoice_id'];
        $feeMonth = $invoices['fee_month'];

        $feeMonthArr = str_split($feeMonth);
        $feeMonthYear = $feeMonthArr[0] . $feeMonthArr[1] . $feeMonthArr[2] . $feeMonthArr[3];
        $feeMonthMonth = $feeMonthArr[4] . $feeMonthArr[5];
        $feeMonthDay = $feeMonthArr[6] . $feeMonthArr[7];

        $invoiceDate = $feeMonthYear . "-" . $feeMonthMonth . "-" . $feeMonthDay;

        $year = date("Y");
        $month = str_pad(date("m"),2,0,STR_PAD_RIGHT);
        $feemonthDate = $year . "-" . $month . "-10";



        $invoiceNumber = $fee->genInvoiceNumber($feemonthDate,$stuid);

        $dataInv['invoice_id'] = $invoiceNumber;
        $dataInv['student_id'] = $stuid;
        $dataInv['recp_date'] = $date;
        $dataInv['invoice_status'] = $fee->paidStatus("paid");
        $dataInv['fee_month'] = $invoiceDate;
        $dataInv['created_user_id'] = $userId;
        $dataInv['created'] = $dateTime;



        foreach ($paids as $paid){
            $paidId = $paid['id'];
            $check = $feeExp->checkAlreadyPaid($paidId);
            if(!$check){
                $insertNew = true;
                $paidIds[] = $paidId;
            }
            else{
                $alreadyPaidIds[] = $paidId;
            }
        }


        if($insertNew){
            $invoiceGeneratedId = $fee->insertSingleInvoice($dataInv);
            if($invoiceGeneratedId > 0){
                foreach($paidIds as $key){
                    if(!empty($key) && is_numeric($key)){
                        $fee->updatePaidReceive($key,$invoiceGeneratedId,$fee->paidStatus("paid"),$stuid);
                    }
                }
            }

            $dataOffline['offline_invoice_number'] = $offlineInvoiceId;
            $dataOffline['invoice_id'] = $invoiceGeneratedId;
            $dataOffline['user_id'] = $userId;
            $offlineLastId = $feeExp->insertOfflineInvoice($dataOffline);
        }

        if(count($alreadyPaidIds)>0){
            foreach ($alreadyPaidIds as $keyFailed){
                $dataPaidFailed['offline_failed_id'] = $offlineLastId;
                $dataPaidFailed['paid_failed_id'] = $keyFailed;
                $dataPaidFailed['user_id'] = $userId;
                $feeExp->insertPaidFailed($dataPaidFailed);
            }
        }



    }

    $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("records_updated"));
    $tool->Redir("fees","importfee&_chk=1","","");
    exit;
}

$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);
?>
    <div class="social-box">
        <div class="header">
            <h4 class="fonts"><?php $tool->trans("import_fees") ?></h4>
        </div>

        <div class="body">
            <div id="jamia_msg">&nbsp;</div>
            <form id="amg_form" name="amg_form" method="post" action="">
                <?php echo $tpl->formHidden() ?>
                <div class="container text-center">
                    <div class="row-fluid">

                        <div class="span12">
                            <textarea name="data" style="width: 95%; height: 300px;"></textarea>
                        </div>
                    </div>

                    <div class="row-fluid">
                        <div class="span12"><input type="submit" class="btn btn-success large"></div>
                    </div>

                </div>
            </form>
        </div>

    </div>
<?php

$tpl->footer();