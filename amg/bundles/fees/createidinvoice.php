<?php
$errors = array();
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
Tools::getLib("TemplateForm");
$fee = new FeeModel();
$stu = new StudentsModel();
$tpf = new TemplateForm();

if ((isset($_POST["_chk"])) && ($_POST["_chk"] == "1")) {

    $student_id = isset($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : '';
    $fee_type = isset($_POST['fee_type']) ? $tool->GetInt($_POST['fee_type']) : '';
    $branch = isset($_POST['branch']) ? $tool->GetExplodedInt($_POST['branch']) : "";
    $class = isset($_POST['class']) ? $tool->GetExplodedInt($_POST['class']) : "";
    $session = isset($_POST['session']) ? $tool->GetExplodedInt($_POST['session']) : "";
    $section = isset($_POST['section']) ? $tool->GetExplodedInt($_POST['section']) : "";
    $year = isset($_POST['year']) ? $tool->GetInt($_POST['year']) : "";
    $month = isset($_POST['month']) ? $tool->GetInt($_POST['month']) : "";
    $month = str_pad($month,2,0,STR_PAD_LEFT);
    $invoiceDate = $year . "-" . $month . "-01";
    



    if(empty($branch) || empty($class) || empty($section) || empty($session) || empty($fee_type) || empty($student_id)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("all_fields_required"));
    }

    $date = $tool->ChangeDateFormat($_POST['date']);
    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->transnoecho("invalid_date");
    }

    $param = array();
    $param['branch'] = $branch;

    $param['session'] = $session;
    $param['class'] = $class;
    $param['fee_type'] = array($fee_type);
    $branchFeeData = $fee->getBranchFees($param);

    $discountStudent = $fee->discountedStudents(array("student_id" => $student_id, "fee_types" => array($fee_type)));

    $symbol = "";
    $fees = "";
    $discount = 0;

    if(!empty($discountStudent)){
        $rowDiscount = $discountStudent[0];
        $discount = $rowDiscount['amount'];
    }

    if(empty($branchFeeData)){
        $errors[] = $tool->transnoecho("no_fee_defined");
    }


    if(!empty($branchFeeData)){
        $row = $branchFeeData[0];
        $fees = $row['fees'];
    }

    $singleStudentFeeData = $fee->getStudentFees(array("student_id" => $student_id, "fee_type" => array($fee_type)));

    if(!empty($singleStudentFeeData)){
        $rowSingleStudentFeeData = $singleStudentFeeData[0];
        $fees = $rowSingleStudentFeeData['fees'];
        $discount = $rowSingleStudentFeeData['discount'];
    }

    if(empty($fees)){
        $errors[] = $tool->transnoecho("no_fee_defined");
    }


    if($discount >= $fees){
        $paidStatus = $fee->paidStatus("exempt");
    }
    else{
        $paidStatus = $fee->paidStatus("pending");
    }

    $data['student_id'] = $student_id;
    $data['type_id'] = $fee_type;
    $data['branch_id'] = $branch;
    $data['class_id'] = $class;
    $data['section_id'] = $section;
    $data['session_id'] = $session;
    $data['fees'] = $fees;
    $data['discount'] = $discount;
    $data['paid_status'] = $paidStatus;
    $data['fee_date'] = $paidStatus;
    $data['fee_date'] = $invoiceDate;
    $data['due_date'] = $date;

    if(count($errors)==0){
        $fee->insertSingleChalan($data);
        $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("invoice_generated"));
        //$tool->Redir("fees","createidinvoice","","");
        //exit;
    }



}


$noteSubCat = array();


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);


?>


<div class="social-box">
    <div class="header">
        <div class="tools">
        </div>
    </div>
    <div class="body">


        <div id="jamia_msg">&nbsp;</div>


        <div id="printReady">



                    <div class="container text-center">

                        <div class="row-fluid">
                            <div class="span12">
                                <a href="javascript:void(0)" class="icon-btn icon-btn-green">
                                    <i class="icon-edit icon-2x"></i>
                                    <div><?php $tool->trans("insert_note") ?></div>
                                  </a>

                            </div>
                        </div>



                        <div class="row-fluid">
                            <div class="span4">&nbsp;</div>
                            <div class="span4">
                                <form action="" method="post">
                                    <p id="student_res">&nbsp;</p>
                                    <?php echo $tpl->FormHidden(); ?>
                                    <div class="control-group">
                                        <label class="control-label"><span class="fonts"><?php $tool->trans("ID") ?></span></label>
                                        <div class="controls">
                                            <input value="<?php if(isset($_POST['student_id'])) echo $_POST['student_id'] ?>" type="text" name="student_id" id="student_id">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="fonts"><?php $tool->trans("fee_type") ?></label>
                                            <?php $typeData = $set->getTitleTable("fee_type"); ?>
                                            <select name="fee_type" id="fee_type">
                                                <option value=""></option>
                                                <?php
                                                foreach ($typeData as $type){
                                                ?>
                                                <option value="<?php echo $type['id'] ?>"><?php echo $type['title'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="fonts"><?php $tool->trans("year") ?></label>

                                            <select name="year" id="year">
                                                <?php echo $tpf->NewYearsDropDown(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <div class="controls">
                                            <label class="fonts"><?php $tool->trans("month") ?></label>

                                            <select name="month" id="month">
                                                <?php echo $tpf->NewMonthDropDown(); ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label"><span class="fonts"><?php $tool->trans("date") ?></span></label>
                                        <div class="controls">
                                            <?php echo $tpl->getDateInput() ?>
                                        </div>
                                    </div>


                                    <input type="submit" name="Submit" class="btn btn-success" value="Insert" />



                                </form>

                            </div>
                            <div class="span4">&nbsp;</div>
                        </div>
                    </div>
                </div>


    </div>
</div>


<?php
$tpl->footer();