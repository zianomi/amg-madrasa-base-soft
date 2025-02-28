<?php
Tools::getModel("StudentsModel");
Tools::getModel("FeeModel");
$stu = new StudentsModel();
$fee = new FeeModel();

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$branchName = isset($_GET['branch']) ? $tool->GetExplodedVar($_GET['branch']) : '';

$dateTime = date("Y-m-d h:i:s");

$but_head = (isset($_GET['id'])) ? $tool->transnoecho("edit") : $tool->transnoecho("add");
$urlPassedID = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : '';


$profile_id = '';
$parents_id = '';

$error = array();

$student_id = '';
$branch = '';
$class = '';
$section = '';
$session = '';
$name = '';
$fname = '';
$gender = '';
$grnumber = '';
$admission_d = '';
$eng_name = '';
$eng_fname = '';
$stutype = '';
$module = '';

$date_of_birth = '';
$bform = '';
$doa = '';
$bloud_group = '';
$home_fone = '';
$sreet = '';
$block = '';
$postcode = '';
$current_address = '';
$injury = '';
$amergency_name = '';
$amergency_contact = '';
$amergency_mobile = '';
$father_nic = '';
$father_education = '';
$father_occupation = '';
$father_habits = '';
$father_email = '';
$father_mobile = '';
$mother_name = '';
$mother_nic = '';
$mother_education = '';
$mother_habits = '';
$mother_mobile = '';
$gargin_name = '';
$gargin_nic = '';
$gargin_education = '';
$gargin_mobile = '';
$gargin_habits = '';
$test_numbers = '';
$examin_opinion = '';
$shart = '';
$farde_mujaz = '';
$approval = '';
$author = '';
$instruc = '';
$city = '';
$transport = '';
$sessionClasses = array();


if (isset($_GET['id'])) {
    $studentEdit = $stu->SelectStudenProfiletById($urlPassedID);
    

    extract($studentEdit);
    $branch = $studentEdit['branch_id'];
    $class = $studentEdit['class_id'];
    $section = $studentEdit['section_id'];
    $session = $studentEdit['session_id'];

    $profile_id = $studentEdit['profile_id'];
    $parents_id = $studentEdit['parents_id'];


    $sessionClasses = $set->sessionClasses($session, $branch);
}


if (isset($_POST['submit'])) {

    $_POST['branch'] = $tool->GetExplodedInt($_POST['branch']);
    $_POST['session'] = $tool->GetExplodedInt($_POST['session']);
    $_POST['class'] = $tool->GetExplodedInt($_POST['class']);
    $_POST['section'] = $tool->GetExplodedInt($_POST['section']);
    $_POST['doa'] = $tool->ChangeDateFormat($_POST['doa']);
    $_POST['date_of_birth'] = $tool->ChangeDateFormat($_POST['date_of_birth']);
    extract($_POST);


    if (empty($grnumber)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("GR Number Required"));
    }

    if (empty($name)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Name Required"));
    }

    if (empty($fname)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Father Name Required"));
    }




    if (empty($branch)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Please Select Branch"));
    }

    if (empty($session)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Please Select Session"));
    }

    if (empty($class)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Please Select Class"));
    }

    if (empty($section)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Please Select Section"));
    }

    if (!empty($_POST['edit_student_id'])) {
        if (empty($_POST['edit_profile_id']) || empty($_POST['edit_parents_id'])) {
            $error[] = $tool->Message("alert", $tool->transnoecho("error_in_form"));
        }
    }

    $classType = "";

    if (!empty($class)) {
        $classType = $set->getClassType($class);
    }

    if ($classType != $stu->stuStatus("current")) {
        $error[] = $tool->Message("alert", $tool->transnoecho("only_current_class_can_change_in_admission_form"));
    }
    
    
    /*if(strlen($father_nic) < 13){
        $error[] = $tool->Message("alert", $tool->transnoecho("Please enter valid father NIC number."));
    }*/


    if (count($error) == 0) {
        $redirId = 0;
        if (empty($_POST['edit_student_id'])) {

            $stuid = $stu->addStudents($_POST);
            $_POST['student_id'] = $stuid;
            $parentsid = $stu->insertParents($_POST);
            $_POST['parents_id'] = $parentsid;
            $stu->insertProfile($_POST);
            $redirId = $stuid;
            $msg = $tool->Message("succ", $tool->transnoecho("id_inserted") . $tool->transnoecho("id_number") . " " . $redirId);
        } else {



            $editStudentId = $tool->GetInt($_POST['edit_student_id']);
            $editParentsId = $tool->GetInt($_POST['edit_parents_id']);
            $editProfileId = $tool->GetInt($_POST['edit_profile_id']);
            $stu->updateStudent($_POST, $editStudentId);
            $stu->updateProfile($_POST, $editProfileId);
            $stu->updateParents($_POST, $editParentsId);
            $redirId = $editStudentId;
            $msg = $tool->Message("succ", $tool->transnoecho("id_updated") . $tool->transnoecho("id_number") . " " . $redirId);
        }


        /*$paramTransfer['student_id'] = $redirId;
        $paramTransfer['old_branch'] = $tool->GetInt($_POST['current_branch']);
        $paramTransfer['old_class'] = $tool->GetInt($_POST['current_class']);
        $paramTransfer['old_section'] = $tool->GetInt($_POST['current_section']);
        $paramTransfer['old_session'] = $tool->GetInt($_POST['current_session']);
        $paramTransfer['current_branch'] = $branch;
        $paramTransfer['current_class'] = $class;
        $paramTransfer['current_section'] = $section;
        $paramTransfer['current_session'] = $session;*/

        //$stu->logTransferDetail($paramTransfer);

        $_SESSION['msg'] = $msg;
        $tool->Redir("students", "admissionfromshort&id=" . $redirId, "", "");
        exit;

    }

}
$tpl->renderBeforeContent();


if(!empty($urlPassedID)){
    $pendingExists = $fee->seePendingInvoince($urlPassedID);
    if($pendingExists){
        echo $tool->Message("alert",$tool->transnoecho("please_clear_pending_dues_to_edit"));
        $tpl->footer();
        exit;
    }
}
?>




    <div class="social-box">

    <div class="header">
        <div class="tools">
            <h4><?php $tool->trans("add_student_form"); ?></h4>
        </div>
    </div>
    <div class="body">

        <form action="" method="post">

            <input type="hidden" name="edit_student_id" value="<?php echo $urlPassedID ?>"/>
            <input type="hidden" name="edit_profile_id" value="<?php echo $profile_id ?>"/>
            <input type="hidden" name="edit_parents_id" value="<?php echo $parents_id ?>"/>
            <input type="hidden" name="current_branch" value="<?php echo $branch; ?>">
            <input type="hidden" name="current_class" value="<?php echo $class; ?>">
            <input type="hidden" name="current_section" value="<?php echo $section; ?>">
            <input type="hidden" name="current_session" value="<?php echo $session; ?>">
            <input type="hidden" name="pic" value=""/>
            <input type="hidden" value="" name="amergency_name" id="amergency_name" />
            <input value="" type="hidden" name="amergency_contact" id="amergency_contact"/>
            <input value="" type="hidden" name="amergency_mobile" id="amergency_mobile"/>
            <input value="" type="hidden" name="injury" id="injury" />
            <input name="current_address" type="hidden" id="current_address" value=""/>
            <input value="" type="hidden" name="postcode" id="postcode" />
            <input value="" type="hidden" name="bform" id="bform"/>
            <input value="" type="hidden" name="bloud_group" id="bloud_group"/>
            <input value="" type="hidden" name="home_fone" id="home_fone"/>
            <input size="" name="date_of_birth" type="hidden" value="" />
            <input value="" type="hidden" name="father_education" id="father_education"/>
            <input value="" type="hidden" name="father_email" id="father_email"/>
            <input value="" type="hidden" name="father_habits" id="father_habits"/>
            <input value="" type="hidden" name="father_occupation" id="father_occupation"/>
            <input value=""  type="hidden" name="father_nic" id="father_nic"/>
            <input value="" type="hidden" name="mother_name" id="mother_name" />
            <input value="" type="hidden" name="mother_nic" id="mother_nic" />
            <input value="" type="hidden" name="mother_education" id="mother_education" />
            <input value=""  type="hidden" name="mother_mobile" id="mother_mobile"/>
            <input value="" type="hidden" name="mother_habits" id="mother_habits"/>
            <input value="" type="hidden" name="gargin_name" id="gargin_name"/>
            <input value="" type="hidden" name="gargin_nic" id="gargin_nic" />
            <input value="" type="hidden" name="gargin_education" id="gargin_education" />
            <input value="" type="hidden" name="gargin_mobile" id="gargin_mobile" />
            <input value="" type="hidden" name="gargin_habits" id="gargin_habits" />
            <input value="" type="hidden" name="test_numbers" id="test_numbers"/>
            <input value="" type="hidden" name="examin_opinion" id="examin_opinion"/>
            <input value="" type="hidden" name="term" id="term"/>
            <input value="" type="hidden" name="instruc" id="instruc"/>
            <input value="" type="hidden" name="city" id="city"/>
            <input value="" type="hidden" name="sreet" id="sreet" />
            <input value="" type="hidden" name="block" id="block" />
            <input value="" type="hidden" name="approval" id="approval"/>
            <input value="" type="hidden" name="author" id="author" />
            <input value="" type="hidden" name="transport" id="transport" />


            <?php if (count($error) > 0) { ?><?php echo implode("<br />", $error); ?><?php } ?>

            <div class="row-fluid">
                <div class="span4">&nbsp;</div>
                <div class="span6">
                    <table class="table table-striped table-hover">
                        <tr>
                            <td class="fonts"> شناخت نمبر</td>
                            <td>
                                <input value="<?php echo $grnumber; ?>" type="text" name="grnumber" id="grnumber"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">نام</td>
                            <td>
                                <input value="<?php echo $name; ?>" type="text" name="name" id="name"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">ولدیت</td>
                            <td>
                                <input value="<?php echo $fname; ?>" type="text" name="fname" id="fname" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">Name</td>
                            <td>
                                <input value="<?php echo $eng_name; ?>" type="text" name="eng_name" id="eng_name"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">Father Name</td>
                            <td>
                                <input value="<?php echo $eng_fname; ?>" type="text" name="eng_fname" id="eng_fname"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">لڑکا / لڑکی</td>
                            <td>
                                <select id="gender" name="gender">
                                    <option value=""></option>
                                    <option value="1" <?php if ($gender == 1) echo "selected"; ?> >
                                        <?php $tool->trans("male") ?>
                                    </option>
                                    <option value="2" <?php if ($gender == 2) echo "selected"; ?>>
                                        <?php $tool->trans("female") ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">تاریخ داخلہ</td>
                            <td>
                                <input size="16" name="doa" type="text" value="<?php if (!empty($doa)) echo $tool->ChangeDateFormat($doa); ?>" class="datepicker" />
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">والد کا موبائل نمبر</td>
                            <td>

                                <input value="<?php echo $father_mobile; ?>" type="text" name="father_mobile" id="father_mobile"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">تعلیمی سال</td>
                            <td>
                                <?php echo $tpl->getAllSession(array("sel" => $session)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">شاخ</td>
                            <td>
                                <?php echo $tpl->userBranches($branch); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">شعبہ</td>
                            <td>
                                <?php echo $tpl->getClasses(array("branch" => $branch, "sel" => $class, "data" => $sessionClasses)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="fonts">کلاس</td>
                            <td>
                                <select name="section" id="section">
                                    <?php
                                    if (isset($_GET['id'])) {
                                        $sessionData = $set->sessionSections($session, $class, $branch);
                                        echo $tpl->GetOptionVals(array("data" => $sessionData, "sel" => $section));
                                        ?>

                                    <?php } ?>
                                    <option value=""></option>
                                </select>
                            </td>
                        </tr>
                        <tr>

                            <td style="text-align: center" colspan="2">
                                <input type="submit" name="submit" value="Submit" class='btn button-finish'>
                            </td>
                        </tr>

                    </table>
                </div>
                <div class="span2">&nbsp;</div>
            </div>



</form>
                </div>
            </div>





    <style type="text/css">
        [class*="span"] .chosen-container {
          width: 51%!important;
          min-width: 51%;
          max-width: 51%;
        }

    </style>



<?php


$tpl->footer();
