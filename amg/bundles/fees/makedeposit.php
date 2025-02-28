<?php
Tools::getLib("QueryTemplate");
Tools::getModel("FeeModel");
$fs = new FeeModel();
$qr = new QueryTemplate();
$excludedCols = array();
$sessionClasses = array();
$classArr = array();
$errors = array();
if (isset($_POST['_chk']) == 1) {
    $depositNumber = isset($_POST['deposit_number']) ? $_POST['deposit_number'] : "";
    $branch = isset($_SESSION['amg_deposits']['branch']) ? $tool->GetInt($_SESSION['amg_deposits']['branch']) : "";
    $class = isset($_SESSION['amg_deposits']['gl_module']) ? $tool->GetInt($_SESSION['amg_deposits']['gl_module']) : "";

    if (empty($depositNumber)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter deposit number"));
    }
    if (empty($branch)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter branch"));
    }
    if (empty($class)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("Please enter class"));
    }

    $bankDetail = $fs->GetBranchBank($branch, $class);

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
    foreach ($_SESSION['amg_deposits']['paid_ids'] as $key) {
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
    $vals['gl_zone_id'] = $fs->getZoneIdByBranchId($branch);
    $vals['gl_branch_code'] = $branchGlCode;
    $vals['user_id'] = Tools::getUserId();
    $vals['is_gl_read'] = 0;
    $vals['date'] = date("Y-m-d");
    $vals['created'] = date("Y-m-d H:i:s");


    if (count($errors) == 0) {


        try {
            $fs->query("START TRANSACTION");
            $depositId = $fs->insertDeposit($vals);
            $fs->updateDeposit($depositId, implode(",", $ids));
            $fs->query("COMMIT");
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("Deposit Updated"));
            unset($_SESSION['amg_deposits']);
            $tool->Redir("fees", "printdeposit&id=$depositId", "", "");
            exit;
        } catch (Exception $e) {
            $fee->query("ROLLBACK");
            $errors[] = $tool->Message("alert", $e->getMessage());
        }


    }
}


$glModules = $fs->GetGlModules();

if(isset($_GET['classes'])){
    foreach ($_GET['classes'] as $class){
        $classArr[] = $tool->GetExplodedInt($class);
    }
}

$glModule = (isset($_GET['gl_module'])) ? $tool->GetExplodedInt($_GET['gl_module']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : '';
$to_date = (isset($_GET['to_date'])) ? $tool->ChangeDateFormat($_GET['to_date']) : '';


$qr->renderBeforeContent();
$qr->searchContentAbove();

$discountTypeData = $set->getTitleTable("discount_refrence");

?>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>

        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label>
            <select name="classes[]" id="class" multiple="multiple">
                <?php
                $selClass = '';
                $selecedClass = array();

                if(isset($_GET['classes'])){
                    foreach ($_GET['classes'] as $classRow){
                        $selecedClass[$tool->GetExplodedInt($classRow)] = ' selected';
                    }
                }


                foreach ($sessionClasses as $sessionClass){
                    if(isset($_GET['_chk'])==1){

                        if(isset($selecedClass[$sessionClass['id']])){
                            $selClass = ' selected';
                        }
                        else{
                            $selClass = '';
                        }
                        ?>
                        <option value="<?php echo $sessionClass['id'] ?>-<?php echo $sessionClass['title'] ?>"<?php echo $selClass ?>><?php echo $sessionClass['title'] ?></option>
                    <?php } ?>
                <?php } ?>
            </select>

        </div>

        <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>

    </div>


    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label>
            <select name="gl_module" id="gl_module"><?php echo $tpl->GetOptionVals(array("data" => $glModules)); ?></select>
        </div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    </div>
<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){

    if(empty($branch) || empty($date) || empty($to_date) || empty($glModule)){
        echo $tool->Message("alert",$tool->transnoecho("branch_gl_module_and_from_and_todate_required"));
        exit;
    }


    if(!$tool->checkDateFormat($date)){
        echo $tool->Message("alert",$tool->transnoecho("from_date_invalid"));
        exit;
    }

    if(!$tool->checkDateFormat($to_date)){
        echo $tool->Message("alert",$tool->transnoecho("to_date_invalid"));
        exit;
    }



    $bankDetail = $fs->GetBranchBank($branch, $glModule);



    if (empty($bankDetail)) {
        echo $tool->Message("alert", $tool->transnoecho("Bank account not exists for this branch"));
        $tpl->footer();
        exit;
    }

    if (empty($classArr)) {
        echo $tool->Message("alert", $tool->transnoecho("Please select classes."));
        $tpl->footer();
        exit;
    }





    $param = array("branch" => $branch
    , "class" => $classArr
    , "recp_start_date" => $date
    , "recp_end_date" => $to_date
    , "for_deposit" => "yes"
    //, "depositer" => Tools::getUserId()
    , "paid_status" => $fs->paidStatus("paid")

    );


    //echo '<pre>'; print_r($bankDetail); echo '</pre>';
    //echo '<pre>'; print_r($param); echo '</pre>';


    $res = $fs->GetPaidData($param);
    $total = count($res);


    if($total==0){
        echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
        exit;
    }

    $students = array();
    $feePaids = array();
    $feeTypes = array();

    foreach ($res as $row) {



        if ($row['gender'] == 1) {
            $gender = " S/O ";
        } else {
            $gender = " D/O ";
        }

        $students[$row['student_id']] = array(
            "id" => $row['student_id']
        , "name" => $row['name']
        , "gender" => $gender
        , "father_name" => $row['fname']
        , "grnumber" => $row['grnumber']
        , "class_title" => $row['class_title']
        , "section_title" => $row['section_title']
        , "invoice_id" => $row['invoice_id']
        , "fees_date" => $row['fee_date']

        );

        $feeTypes[$row['type_id']] = array(
            "type_id" => $row['type_id']
        , "title" => $row['title_en']
        , "duration_type" => $row['duration_type']);

        $feePaids[$row['student_id']][$row['type_id']][] = array(
            "fees" => $row['fees']
        , "discount" => $row['discount']
        , "paid_status" => $row['paid_status']
        , "fee_date" => $row['fee_date']
        , "type_id" => $row['type_id']
        , "title_en" => $row['title_en']
        , "duration_type" => $row['duration_type']
        , "invoice_id" => $row['invoice_id']
        , "stuid" => $row['student_id']
        , "paid_id" => $row['paid_id']

        );

    }





    ?>

    <div class="body">
        <div id="printReady">

            <div class="row-fluid">
                <div class="span7 text-center">
                    <img class="logo" src="<?php echo $tool->getWebUrl() ?>/img/iqra_logo.png" alt="Amazon" height="77" width="400">
                </div>

                <div class="span5">

                    <div class="alert alert-success fonts" style="font-size: 25px; "><?php $tool->trans("paid_students") ?></div>

                    <dl class="dl-horizontal">
                        <dt class="fonts"><?php $tool->trans("branch") ?></dt>
                        <dd  class="fonts"><?php if(!empty($branch)){
                                echo $tool->GetExplodedVar($_GET['branch']);
                            }
                            ?></dd>


                        <?php  if(isset($_GET['classes'])){ ?>
                            <dt class="fonts"><?php $tool->trans("class") ?></dt>
                            <dd class="fonts"><?php
                                foreach ($_GET['classes'] as $class){
                                    echo $tool->GetExplodedVar($class) . " ";
                                }

                                ?></dd>
                        <?php } ?>
                        <dt class="fonts"><?php $tool->trans("from_date") ?></dt>
                        <dd><?php echo date('F d, Y', strtotime($date)); ?></dd>
                        <dt class="fonts"><?php $tool->trans("to_date") ?></dt>
                        <dd><?php echo date('F d, Y', strtotime($to_date)); ?></dd>
                    </dl>
                </div>

            </div>


            <form method="post" action="">
                <?php
                echo $tpl->formHidden();
                ?>
            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-striped table-hover">


                    <?php
                    unset($_SESSION['amg_deposits']);

                    $_SESSION['amg_deposits']['branch'] = $branch;
                    $_SESSION['amg_deposits']['gl_module'] = $glModule;

                    ?>
                    <thead>
                    <tr>
                        <th><?php $tool->trans("S#") ?></th>
                        <th class="fonts"><?php $tool->trans("ID") ?></th>
                        <th class="fonts"><?php $tool->trans("GR") ?></th>
                        <th class="fonts"><?php $tool->trans("Name") ?></th>
                        <th class="fonts"><?php $tool->trans("Father Name") ?></th>
                        <th class="fonts"><?php $tool->trans("Class") ?></th>
                        <th class="fonts"><?php $tool->trans("Section") ?></th>
                        <?php
                        foreach($feeTypes as $feeType){
                            ?>
                            <th><?php echo $feeType['title'] ?></th>
                        <?php } ?>
                        <th class="fonts"><?php $tool->trans("Total") ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i = 0;
                    $overAllTotal = 0;
                    $studentAmount = array();
                    $typeViceTotal = array();

                    $paidIds = array();
                    //unset($_SESSION['amg_deposits']['paid_ids']);

                    foreach($students as $student) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $student['id'] ?></td>
                            <td><?php echo $student['grnumber'] ?></td>
                            <td class="fonts"><?php echo $student['name']; ?></td>
                            <td class="fonts"><?php echo $student['father_name']; ?></td>
                            <td class="fonts"><?php echo $student['class_title']; ?></td>

                            <td class="fonts"><?php echo $student['section_title']; ?></td>
                            <?php
                            $stuTotal = 0;
                            foreach($feeTypes as $feeType){
                                ?>
                                <td style="direction: ltr"><?php


                                    if(isset($feePaids[$student['id']][$feeType['type_id']])){

                                        foreach($feePaids[$student['id']][$feeType['type_id']] as $feePaid){

                                            //echo '<pre>'; print_r($feePaid); echo '</pre>';

                                            $_SESSION['amg_deposits']['paid_ids'][] = $feePaid["paid_id"];
                                            $dueAmount = ($feePaid['fees'] - $feePaid['discount']);
                                            $stuTotal += $dueAmount;
                                            $overAllTotal += $dueAmount;
                                            $feeDate[$student['id']][$feeType['type_id']][] = $feePaid['fee_date'];
                                            $studentAmount[$student['id']][$feeType['type_id']][] = $dueAmount;
                                            $typeViceTotal[$feeType['type_id']][] = $dueAmount;
                                        }



                                        if(count($feeDate[$student['id']][$feeType['type_id']]) > 1){
                                            $firsDate = date('F Y', strtotime(min($feeDate[$student['id']][$feeType['type_id']])));
                                            $lastDate = date('F Y', strtotime(max($feeDate[$student['id']][$feeType['type_id']])));
                                            $dateToDis = $firsDate . " to " . $lastDate;
                                        }
                                        else{
                                            $dateToDis = date('F Y', strtotime($feeDate[$student['id']][$feeType['type_id']][0]));
                                        }
                                        echo $dateToDis . " : <b>" . array_sum($studentAmount[$student['id']][$feeType['type_id']]). "</b>";
                                    }


                                    ?></td>
                            <?php } ?>
                            <td><?php echo $stuTotal; ?></td>
                        </tr>
                    <?php }

                    ?>
                    <tr>
                        <td colspan="7">&nbsp;</td>
                        <?php foreach($feeTypes as $feeType){?>
                            <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo @array_sum($typeViceTotal[$feeType['type_id']]) ?></td>
                        <?php } ?>
                        <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo $overAllTotal ?></td>
                    </tr>

                    <?php
                    $colsPan = 4 + count($feeTypes);
                    ?>
                    <tr>
                        <td colspan="<?php echo $colsPan ?>">&nbsp;</td>
                        <td>Total <?php echo $overAllTotal ?></td>
                        <td><?php $tool->trans("Deposit Number") ?></td>
                        <td><input type="text" name="deposit_number"></td>
                        <td>
                            <button type="submit" class="btn btn-success">Submit <i class="icon-save"></i></button>
                        </td>

                    </tr>


                    </tbody>
                </table>
            </div>

            </form>
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



}


$tpl->footer();
