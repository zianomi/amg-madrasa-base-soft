<?php
$errors = array();
Tools::getModel("StudentsModel");
Tools::getModel("FeeModel");
$stu = new StudentsModel();
$fee = new FeeModel();

if ((isset($_POST["_chk"])) && ($_POST["_chk"] == "1")) {

    $student_id = isset($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : '';


    /*if(!empty($student_id)){
        $pendingExists = $fee->seePendingInvoince($student_id);
        if($pendingExists){
            $errors[] = $tool->transnoecho($tool->transnoecho("please_clear_pending_dues_to_edit"));
        }
    }*/

    $date = $tool->ChangeDateFormat($_POST['date']);

    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->transnoecho("invalid_date");
    }

    if(empty($student_id)){
	    $errors[] = $tool->transnoecho("insert_id");
    }

    if(empty($_POST['branch'])){
	    $errors[] = $tool->transnoecho("please_select_branch");
    }

    if(empty($_POST['class'])){
        $errors[] = $tool->transnoecho("please_select_class");
    }

    if(empty($_POST['section'])){
        $errors[] = $tool->transnoecho("please_select_section");
    }

    if(empty($_POST['session'])){
        $errors[] = $tool->transnoecho("please_select_session");
    }

    if(empty($_POST['desc'])){
	    $errors[] = $tool->transnoecho("please_insert_detail");
    }

    if(count($errors) == 0){
        $data['student_id'] = $student_id;
        $data['branch_id'] = $_POST['branch'];
        $data['class_id'] = $_POST['class'];
        $data['section_id'] = $_POST['section'];
        $data['session_id'] = $_POST['session'];
        $data['date'] = $date;
        $data['numbers'] = ($_POST['numbers']);
        $data['grade'] = ($_POST['grade']);
        $data['roll_number'] = ($_POST['roll_number']);
        $data['certificate_number'] = ($_POST['certificate_number']);
        $data['note'] = ($_POST['desc']);
        $classID = 0;



        $stu->insertCompleted($data);
        $_SESSION['msg'] = $tool->Message("succ"," " . $tool->transnoecho(" transfered"));
        $tool->Redir("students","completed",$_POST['code'],$_POST['action']);
        exit;
    }


}


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);

echo $tpl->formTag("post");
echo $tpl->FormHidden();
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
                                    <div><?php $tool->trans("complete_student") ?></div>
                                  </a>

                            </div>
                        </div>



                                <p id="student_res">&nbsp;</p>

                                <div class="control-group">
                                    <label class="control-label"><span class="fonts"><?php $tool->trans("ID") ?></span></label>
                                    <div class="controls">
                                        <input value="<?php if(isset($_POST['student_id'])) echo $_POST['student_id'] ?>" type="text" name="student_id" id="student_id">
                                    </div>
                                </div>

                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("date") ?></span></label>
                                <div class="controls">
                                    <?php echo $tpl->getDateInput() ?>
                                </div>
                            </div>




                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("numbers") ?></span></label>
                               <div class="controls">
                                   <input value="<?php if(isset($_POST['numbers'])) echo $_POST['numbers'] ?>" type="number" name="numbers" id="numbers">
                               </div>
                           </div>

                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("grade") ?></span></label>
                               <div class="controls">
                                   <input value="<?php if(isset($_POST['grade'])) echo $_POST['grade'] ?>" type="text" name="grade" id="grade">
                               </div>
                           </div>


                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("roll_number") ?></span></label>
                               <div class="controls">
                                   <input value="<?php if(isset($_POST['roll_number'])) echo $_POST['roll_number'] ?>" type="text" name="roll_number" id="roll_number">
                               </div>
                           </div>


                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("certificate_number") ?></span></label>
                               <div class="controls">
                                   <input value="<?php if(isset($_POST['certificate_number'])) echo $_POST['certificate_number'] ?>" type="text" name="certificate_number" id="certificate_number">
                               </div>
                           </div>


                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("detail") ?></span></label>
                               <div class="controls">
                                   <textarea name="desc" id="desc" style="height: 100px" required="required"><?php if(isset($_POST['desc'])) echo $_POST['desc'] ?></textarea>
                               </div>
                           </div>





                            <div class="row">
                                <input type="submit" name="Submit" class="btn btn-success" value="Insert" />
                            </div>
                    </div>
                </div>


    </div>
</div>


<?php
echo $tpl->formClose();
$tpl->footer();
