<?php
Tools::getLib("QueryTemplate");
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
Tools::getLib("TemplateForm");
$qr = new QueryTemplate();
$fee = new FeeModel();
$stu = new StudentsModel();
$tpf = new TemplateForm();
$qr->renderBeforeContent();

$qr->searchContentAbove();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$feeTypes = isset($_GET['fee_type']) ? $tool->GetExplodedInt($_GET['fee_type']) : "";
$year = isset($_GET['year']) ? $tool->GetInt($_GET['year']) : "";
$month = isset($_GET['month']) ? $tool->GetInt($_GET['month']) : "";

$tpl->formHidden();
?>

    <div class="row-fluid">
        <div class="span3"><?php $tool->trans("session") ?><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><?php $tool->trans("branch") ?><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><?php $tool->trans("class") ?><?php echo $tpl->getClasses() ?></div>
        <div class="span3"><?php $tool->trans("section") ?><?php echo $tpl->getSecsions() ?></div>
    </div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("fee_type") ?></label>
        <select name="fee_type" id="fee_type">
            <?php $typeData = $set->getTitleTable("fee_type") ?>
            <?php echo $tpl->GetOptionVals(array("data" => $typeData, "sel" => $feeTypes)); ?>
        </select>
    </div>

    <div class="span3"><label class="fonts"><?php $tool->trans("year") ?></label>
        <select name="year" id="year">
                <?php echo $tpf->NewYearsDropDown($year); ?>
            </select>
    </div>
    <div class="span3">
    <label class="fonts"><?php $tool->trans("month") ?></label>
        <select name="month" id="month">
                <?php echo $tpf->NewMonthDropDown($month); ?>
            </select>
    </div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

</div>

<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){



    if (empty($branch)) {
        echo $tool->Message("alert", "branch_required");
        exit;
    }

    if (empty($year)) {
        echo $tool->Message("alert", "year_required");
        exit;
    }

    if (empty($month)) {
        echo $tool->Message("alert", "month_required");
        exit;
    }

    if (empty($session)) {
        echo $tool->Message("alert", "session_required");
        exit;
    }
    $month = str_pad($month,2,0,STR_PAD_LEFT);
    $invoiceDate = $year . "-" . $month . "-01";


    $param = array();
    $param['branch'] = $branch;
    $param['class'] = $class;
    $param['session'] = $session;
    $param['section'] = $section;
    $param['fee_type'] = $feeTypes;
    $param['fee_date'] = $invoiceDate;
    $param['invoice_date'] = $invoiceDate;


    $invoiceData = $fee->generatedInvoiceStudents($param);
    $countData = $stu->studentCount($param);
    $paidData = $fee->viewInvoices($param);

    $types = array();
    $stuPaids = array();


    //echo '<pre>';print_r($paidData );echo '</pre>';

    foreach($paidData as $paidRow){
        $types[$paidRow['type_id']] = array("fee_type" => $paidRow['type_id'], "type_title" => $paidRow['title']);
        $stuPaids[$paidRow['type_id']][$paidRow['student_id']] = array("fees" => $paidRow['fees'], "discount" => $paidRow['discount']);
    }


    ?>
    <div class="body">
    <div id="printReady">

        <div class="alert alert-success fonts"><span>

                <?php
                if(isset($_GET['branch'])){
                    if(!empty($_GET['branch'])){
                        echo $tool->GetExplodedVar($_GET['branch']);
                    }
                }

                if(isset($_GET['class'])){
                    if(!empty($_GET['class'])){
                        echo " - " . $tool->GetExplodedVar($_GET['class']);
                    }
                }

                if(isset($_GET['section'])){
                    if(!empty($_GET['section'])){
                        echo " - " . $tool->GetExplodedVar($_GET['section']);
                    }
                }
                ?>

            </span></div>

        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
            <table class="table table-bordered table-striped table-hover flip-scroll">
                <thead>
                <tr>

                    <th class="fonts"><?php $tool->trans("seriol_number") ?></th>
                    <th class="fonts"><?php $tool->trans("id") ?></th>
                    <th class="fonts"><?php $tool->trans("name") ?></th>
                    <th class="fonts"><?php $tool->trans("father_name") ?></th>
                    <?php foreach($types as $type){ ?>
                    <th class="fonts"><?php echo $type['type_title']; ?></th>
                    <th class="fonts"><?php $tool->trans("discount") ?></th>
                    <?php } ?>

                </tr>
                </thead>
                <tbody>
                <?php
                $i=0;
                foreach ($invoiceData as $row) {
                    $i++?>
                    <tr>

                        <td class="avatar"><?php echo $i; ?></td>
                        <td class="avatar"><?php echo $row['id']; ?></td>
                        <td class="fonts"><?php echo $row['name']; ?></td>
                        <td class="fonts"><?php echo $row['fname']; ?></td>
                        <?php foreach($types as $type){ ?>
                        <td class="fonts"><?php echo $stuPaids[$type['fee_type']][$row['id']]["fees"]; ?></td>
                        <td class="fonts"><?php echo $stuPaids[$type['fee_type']][$row['id']]["discount"]; ?></td>
                        <?php } ?>



                    </tr>
                <?php } ?>
                </tbody>


            </table>
        </div>
    </div>
    </div>
<?php
}

$tpl->footer();