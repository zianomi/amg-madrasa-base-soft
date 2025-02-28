<?php
$errors = array();
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
$fee = new FeeModel();
$stu = new StudentsModel();


/*if(isset($_GET['cancel'])==1){
    $invoiceId = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;
    $studentId = isset($_GET['student']) ? $tool->GetInt($_GET['student']) : 0;
    if(empty($invoiceId)){
        echo $tool->Message("alert","Please select invoice");
        exit;
    }

    if(empty($studentId)){
        echo $tool->Message("alert","Please select student id");
        exit;
    }

    $ins = $fee->insertCancel($invoiceId);
    $rows = $fee->cancelInvoice($invoiceId);
    $_SESSION['msg'] = $tool->Message("","rows cancelled: " . $rows);
    Tools::Redir("fees","idhistory&_chk=1&student_id=".$studentId,"","");

    //
}*/

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
                            exit;
                        }

                        $parameters = array("id"=>$id);

                        $res = $stu->studentSearch($parameters);
                        $total = count($res);
                        if ($total < 1) {
                            echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
                            exit;
                        }

                        $row = $res[0];
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


                    $fsData = $fee->getIdHistory($id);

                        if(count($fsData) < 1){
                            echo $tool->Message("alert",$tool->transnoecho("no_fee_found"));
                            exit;
                        }


                    ?>


                    <?php
                    echo $tpl->FormTag("post");
                    echo $tpl->FormHidden();
                    ?>
                    <input type="hidden" name="stuid" value="<?php echo $id ?>">

                <table class="table table-bordered table-striped table-hover">

                    <thead>
                        <tr>
                            <th><?php $tool->trans("Invoice") ?></th>
                            <th><?php $tool->trans("Sr#") ?></th>
                            <th class="fonts"><?php $tool->trans("Fee Type") ?></th>
                            <th class="fonts"><?php $tool->trans("Amount") ?></th>
                            <th class="fonts"><?php $tool->trans("Discount") ?></th>
                            <th class="fonts"><?php $tool->trans("Paid Amount") ?></th>
                            <th class="fonts"><?php $tool->trans("Paid Date") ?></th>
                            <th class="fonts"><?php $tool->trans("Fee Date") ?></th>
                        </tr>
                    </thead>
                    <?php
                    $a=0;
                    $total=0;
                    $feeData = array();
                    $invoices = array();
                    $cancels = array();

                    foreach($fsData as $rowFee) {

                        $feeData[$rowFee['invoice_db_id']][] = array(
                          "paid_invoice_id" => $rowFee['paid_invoice_id']
                        , "invoice_number" => $rowFee['invoice_number']
                        , "title_en" => $rowFee['title_en']
                        , "fees" => $rowFee['fees']
                        , "discount" => $rowFee['discount']
                        , "recp_date" => $rowFee['recp_date']
                        , "created_user_id" => $rowFee['created_user_id']
                        , "fee_date" => $rowFee['fee_date']
                        , "invoice_db_id" => $rowFee['invoice_db_id']
                        );
                        $invoices[$rowFee['invoice_db_id']] = $rowFee['invoice_db_id'];
                        $cancels[$rowFee['invoice_db_id']] = array("recp_date" => $rowFee['recp_date'], "created_user_id" => $rowFee['created_user_id']);
                    }

                        foreach($invoices as $invoice){
                            $checkExists = true;
                    ?>

                    <?php

                        foreach ($feeData[$invoice] as $rowFee){
                            $totalPaidUnderOneInvoice = count($feeData[$rowFee['invoice_db_id']]);
                            $a++;
                            $amount = $rowFee['fees'] - $rowFee['discount'];
                            $total += $amount;
                    ?>
                    <tr>
                        <?php
                        if($checkExists && $totalPaidUnderOneInvoice > 1) {
                            $rowSpanString = ' rowspan="' . $totalPaidUnderOneInvoice . '"';
                        ?>
                            <td<?php echo $rowSpanString ?> style="text-align: center; vertical-align: middle">
                                <a href="<?php $tool->getUrl() ?>?menu=fees&page=printinvoice&_chk=1&student_id=<?php echo $id ?>&id=<?php echo $rowFee['paid_invoice_id'] ?>"> <?php echo $rowFee['invoice_number'] ?></a>
                                <?php
                                if(
                                        $cancels[$invoice]['created_user_id'] == Tools::getUserId()
                                        && $cancels[$invoice]['recp_date'] == date("Y-m-d")
                                ){
                                ?><br />

                                <?php } ?>
                            </td>

                            <?php
                            $checkExists = false;
                        }
                        else{
                            if($checkExists){
                                ?>
                                <td style="text-align: center;">
                                    <a href="<?php $tool->getUrl() ?>?menu=fees&page=printinvoice&_chk=1&student_id=<?php echo $id ?>&id=<?php echo $rowFee['paid_invoice_id'] ?>"> <?php echo $rowFee['invoice_number'] ?></a>

                                    <?php
                                    if(
                                            $cancels[$invoice]['created_user_id'] == Tools::getUserId()
                                            && $cancels[$invoice]['recp_date'] == date("Y-m-d")
                                    ){
                                    ?><br />

                                    <?php } ?>
                                </td>

                            <?php

                            }

                        }
                        ?>


                        <td><?php echo $a ?></td>
                        <td><?php echo $rowFee['title_en'] ?></td>
                        <td><?php echo $rowFee['fees'] ?></td>
                        <td><?php echo $rowFee['discount'] ?></td>
                        <td><?php echo $amount ?></td>
                        <td><?php echo $tool->ChangeDateFormat($rowFee['recp_date']); ?></td>
                        <td><?php echo date("M,Y",strtotime($rowFee['fee_date']))//echo $tool->ChangeDateFormat($rowFee['fee_date']); ?></td>

                    </tr>
                    <?php } ?>
                    <?php } ?>

                    <tr>
                        <td colspan="8" id="output" style="text-align: left">Total: <?php echo $total ?></td>
                    </tr>


                </table>






                <?php $tpl->formClose() ?>

<?php } ?>
                </div>



    </div>
</div>
<?php
$tpl->footer();