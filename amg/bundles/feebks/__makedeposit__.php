<?php
Tools::getModel("FeeModel");
$fee = new FeeModel();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$errors = array();


$branchBank = "";
$branchBankAcNumber= "";
$branchBankAccountTitle= "";
$branchBankShortName= "";
$branchBankCode= "";
$bankGlCode= "";
$glModuleCode= "";
$branchGlCode= "";

if (isset($_POST['_chk']) == 1) {
    $depositNumber = isset($_POST['deposit_number']) ? $_POST['deposit_number'] : "";
    $branch = isset($_POST['branch']) ? $tool->GetInt($_POST['branch']) : "";
    $class = isset($_POST['class']) ? $tool->GetInt($_POST['class']) : "";
    if (empty($depositNumber)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter deposit number"));
    }
    if (empty($branch)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter branch"));
    }
    if (empty($class)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter class"));
    }

    $bankDetail = $fee->GetBranchBank($branch, $class);

    if (empty($bankDetail)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter bank detail"));
    }


    if (!empty($bankDetail)) {
        $branchBank = $bankDetail['branch_bank'];
        $branchBankAcNumber = $bankDetail['branch_bank_ac_number'];
        $branchBankAccountTitle = $bankDetail['branch_bank_account_title'];
        $branchBankShortName = $bankDetail['branch_bank_short_name'];
        $branchBankCode = $bankDetail['branch_bank_code'];
        $bankGlCode = $bankDetail['bank_gl_code'];
        $glModuleCode = $bankDetail['gl_module_code'];
        $branchGlCode = $bankDetail['branch_gl_code'];
    }


    if (empty($branchBank)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter bank name"));
    }

    if (empty($branchBankAcNumber)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter account number"));
    }

    if (empty($branchBankAccountTitle)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter account title"));
    }

    if (empty($branchBankShortName)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter branch short name"));
    }
    
    if (empty($branchBankCode)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter bank code"));
    }

    if (empty($bankGlCode)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter GL bank code"));
    }

    if (empty($glModuleCode)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter GL module code"));
    }

    if (empty($branchGlCode)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter GL branch code"));
    }

    $ids = array();
    foreach ($_SESSION['amg_deposit'] as $key) {
        $ids[] = $key;
    }


    $vals['bank'] = $branchBank;
    $vals['bank_short_name'] = $branchBankShortName;
    $vals['bank_code'] = $branchBankCode;
    $vals['bank_gl_code'] = $bankGlCode;
    $vals['account_title'] = $branchBankAccountTitle;
    $vals['account_number'] = $branchBankAcNumber;
    $vals['deposit_number'] = $depositNumber;
    $vals['gl_modue_id'] = $class;
    $vals['gl_module_code'] = $glModuleCode;
    $vals['gl_branch_id'] = $branch;
    $vals['gl_branch_code'] = $branchGlCode;
    $vals['user_id'] = Tools::getUserId();
    $vals['is_gl_read'] = 0;
    $vals['created'] = date("Y-m-d H:i:s");


    if (count($errors) == 0) {


        try {
            $fee->query("START TRANSACTION");
            $depositId = $fee->insertDeposit($vals);
            $fee->updateDeposit($depositId, implode(",", $ids));
            $fee->query("COMMIT");
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("Deposit Updated"));
            $tool->Redir("fees", "printdeposit&id=$depositId", "", "");
            exit;
        } catch (Exception $e) {
            $fee->query("ROLLBACK");
            $errors[] = $tool->Message("alert", $e->getMessage());
        }


    }
}


$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$glModule = (isset($_GET['gl_module'])) ? $tool->GetExplodedInt($_GET['gl_module']) : '';
$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);
$qr->setPageHeading($tool->transnoecho("make_deposit"));
$qr->searchContentAbove();

$glModules = $fee->GetGlModules();
?>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span3">
            <label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label>
            <select name="gl_module" id="gl_module"><?php echo $tpl->GetOptionVals(array("data" => $glModules)); ?></select>
        </div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    </div>


<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {
    //echo '<pre>';print_r($fee->unreadGlDeposits() );echo '</pre>';

    if (empty($glModule) || empty($branch) || empty($session)) {
        echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
        $tpl->footer();
        exit;
    }

    $data = $fee->notDepositedFee($session, $branch);
    $types = $fee->getFeeTypes();


    $moduleCodes = $fee->GetGlModuleCodes();

    if (empty($data)) {
        echo $tool->Message("alert", $tool->transnoecho("no_record_found"));
        $tpl->footer();
        exit;
    }

    foreach ($types as $type) {
        $feeType[$type['id']] = $type['title'];
    }

    $moduleCodeArr = array();

    foreach ($moduleCodes as $moduleCode){
        $moduleCodeArr[$moduleCode['gl_module_id']][$moduleCode['class_id']] = $moduleCode['class_id'];
    }


    ?>
    <form method="post" action="">
        <input type="hidden" name="branch" value="<?php echo $branch ?>">
        <input type="hidden" name="class" value="<?php echo $glModule ?>">
        <?php
        echo $tpl->formHidden();
        ?>
        <div class="body">
            <div id="printReady">

                <div class="row-fluid">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                           <!-- <th><input type="checkbox" rel="0" onclick="checkAll(this);"></th>-->
                            <th>S#</th>
                            <th><?php $tool->trans("student_id") ?></th>
                            <th><?php $tool->trans("name") ?></th>
                            <th><?php $tool->trans("fname") ?></th>
                            <th><?php $tool->trans("fee_types") ?></th>
                            <th><?php $tool->trans("Invaoice Number") ?></th>
                            <th><?php $tool->trans("Amount") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 0;
                        $total = 0;
                        $todayTotal = 0;
                        foreach ($data as $row) {
                            $amount2 = ($row['fees'] - $row['discount']);
                            $todayTotal += $amount2;
                            if(isset($moduleCodeArr[$glModule][$row['class_id']])){
                            $i++;
                            $amount = ($row['fees'] - $row['discount']);
                            $total += $amount;

                            $_SESSION['amg_deposit'][$row['paid_id']] = $row['paid_id'];
                            ?>
                            <tr>
                                <!--<td>
                                    <input type="checkbox" name="paids[<?php /*echo $row['paid_id'] */?>]" rel="<?php /*echo $amount*/?>" value="<?php /*echo $row['paid_id'] */?>">
                                </td>-->
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row['student_id'] ?></td>
                                <td><?php echo $row['student_name']; ?></td>
                                <td><?php echo $row['student_fname']; ?></td>
                                <td class="fonts"><?php echo $feeType[$row['type_id']]; ?></td>
                                <td><?php echo $row['invoice_id']; ?></td>
                                <td><?php echo $amount; ?></td>
                            </tr>
                        <?php } ?>
                        <?php } ?>
                        </tbody>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                            <td><?php $tool->trans("Deposit Number") ?></td>
                            <td><input type="text" name="deposit_number"></td>
                            <td>
                                <button type="submit" class="btn btn-success">Submit <i class="icon-save"></i></button>
                            </td>
                            <td id="output">
                                <span style="text-align: left" id="output">Total: <?php echo $total ?></span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="7">Today Total: <?php echo $todayTotal ?></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </form>
    <script type="text/javascript">



        $(document).ready(function() {


            function recalculate() {
                var sum = 0;

                $("input[type=checkbox]:checked").each(function() {
                    //var curPos = $(this).attr("data-pos");
                    sum += parseInt($(this).attr("rel"));
                });

                $("#output").html("Total: "+sum);
            }

            $("input[type=checkbox]").change(function() {
                recalculate();
                checkedLow();
            });
        });
    </script>

    <?php
}


$tpl->footer();