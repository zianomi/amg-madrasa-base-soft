<?php
error_reporting(E_ALL);
$errors = array();
Tools::getModel("StudentsModel");
Tools::getModel("FeeExportModel");
$stu = new StudentsModel();
$feeExp = new FeeExportModel();



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
                        <div class="span12"><label class="fonts"><?php $tool->trans("invoice_number") ?></label>
                            <input value="<?php if(isset($_GET['invoice_number'])) echo $_GET['invoice_number'] ?>" type="text" name="invoice_number" id="invoice_number"/>
                            <button type="submit" class="btn btn-small" style="margin-bottom:10px;"><?php $tool->trans("search") ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div id="printReady">
                    <?php
                    $invoiceNumber = (isset($_GET['invoice_number'])) ? ($_GET['invoice_number']) : NULL;
                    if (isset($_GET['_chk']) == 1) {
                        if (empty($invoiceNumber)) {
                            echo $tool->Message("alert",$tool->transnoecho("please_insert_invoice_number"));
                            $tpl->footer();
                            exit;
                        }


                        $offlineInvoice = $feeExp->getOfflineInvoice($invoiceNumber);

                        if (empty($offlineInvoice)) {
                            echo $tool->Message("alert",$tool->transnoecho("no_invoice_found"));
                            $tpl->footer();
                            exit;
                        }

                        $id = $offlineInvoice['student_id'];


                        $parameters = array("id"=>$id);


                        $res = $stu->studentSearch($parameters);
                        $total = count($res);
                        if ($total < 1) {
                            echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
                            $tpl->footer();
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



                <table class="table table-bordered table-striped table-hover">

                    <thead>
                        <tr>
                            <th><?php $tool->trans("offline_invoice_number") ?></th>
                            <th><?php $tool->trans("invoice_id") ?></th>
                            <th><?php $tool->trans("recp_date") ?></th>
                            <th><?php $tool->trans("operator_name") ?></th>

                        </tr>
                    </thead>
                    <?php
                    $a=0;
                    $total=0;

                    ?>

                    <tr>
                        <td><?php echo $offlineInvoice['offline_invoice_number'] ?></td>
                        <td><?php echo $offlineInvoice['invoice_id'] ?></td>
                        <td><?php echo $offlineInvoice['recp_date'] ?></td>
                        <td><?php echo $offlineInvoice['name'] ?></td>
                    </tr>

                    <?php

                    $failedPaid = $feeExp->checkFailedPaid($offlineInvoice['id']);

                    if(!empty($failedPaid)){
                    ?>

                        <tr>
                            <td colspan="4" class="alert alert-info" style="text-align: center; font-size: 20px;"><?php $tool->trans("this_fees_was_already_taken_by") ?> <?php echo $failedPaid['name'] ?></td>
                        </tr>

                    <tr>
                        <td><?php echo $failedPaid['name'] ?></td>
                        <td><?php echo $failedPaid['recp_date'] ?></td>
                        <td><?php echo $failedPaid['invoice_id'] ?></td>
                        <td><?php echo $offlineInvoice['offline_invoice_number'] ?></td>
                    </tr>
                    <?php } ?>



                </table>




<?php } ?>
                </div>



    </div>
</div>

<?php
$tpl->footer();