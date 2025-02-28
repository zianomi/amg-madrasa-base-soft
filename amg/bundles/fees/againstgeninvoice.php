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

if(isset($_POST['_chk']) == 1){

    $branch = isset($_POST['branch']) ? $tool->GetExplodedInt($_POST['branch']) : "";

    $session = isset($_POST['session']) ? $tool->GetExplodedInt($_POST['session']) : "";
    $feeType = isset($_POST['fee_type']) ? $tool->GetExplodedInt($_POST['fee_type']) : "";
    $year = isset($_POST['year']) ? $tool->GetInt($_POST['year']) : "";
    $month = isset($_POST['month']) ? $tool->GetInt($_POST['month']) : "";
    //$dueDate = isset($_POST['date']) ? $tool->ChangeDateFormat($_POST['date']) : "";

    $month = str_pad($month,2,0,STR_PAD_LEFT);

    $invoiceDate = $year . "-" . $month . "-01";
    $dueDate = $year . "-" . $month . "-10";
    //$idDate = date("ym", strtotime($invoiceDate));;

    if(empty($branch) || empty($session) || empty($feeType)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("all_fields_required"));
    }


    if(!$tool->checkDateFormat($invoiceDate)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("invalid_invoice_date"));
    }





    $param = array();
    $param['branch'] = $branch;

    $param['session'] = $session;
    $param['fee_type'] = $feeType;
    $param['date'] = $invoiceDate;


    $del = $fee->cancelChalan($session,$branch,$feeType,$invoiceDate);


    $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("invoice_deleted"));
    $tool->Redir("fees","againstgeninvoice","","");
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
    <div class="span3"><label class="fonts"><?php $tool->trans("year") ?></label>
        <select name="year" id="year">
                <?php echo $tpf->NewYearsDropDown(); ?>
            </select>
    </div>
    <div class="span3">
        <label class="fonts"><?php $tool->trans("month") ?></label>
        <select name="month" id="month">
            <?php echo $tpf->NewMonthDropDown(); ?>
        </select>
    </div>




</div>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("fee_type") ?></label>
            <select name="fee_type" id="fee_type">

                <?php
                $typeData = $set->getTitleTable("fee_type");
                echo $tpl->GetOptionVals(array("data" => $typeData, "sel" => "")); ?>
            </select>
        </div>


        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3">&nbsp;</div>
        <div class="span3">&nbsp;</div>


    </div>
<?php
$qr->searchContentBottom();


$tpl->footer();

