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

    if (empty($_POST['date_of_birth'])) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Please enter date of birth"));
    }

    if(!$tool->checkDateFormat($date_of_birth)){
        $error[] = $tool->Message("alert", $tool->transnoecho("Please enter valid date of birth"));
    }

    /*
    if (empty($current_address)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Current Address Required"));
    }

    if (empty($amergency_name)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Amergency Name Required"));
    }

    if (empty($amergency_mobile)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Amergency Mobile Required"));
    }

    /*if (empty($father_nic)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Father NIC Required"));
    }*/

    if (empty($father_mobile)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Father Mobile Required"));
    }

    /*if (empty($mother_nic)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Mother NIC Required"));
    }

    if (empty($mother_name)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Mother Name Reqired"));
    }

    if (empty($mother_mobile)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Mother Mobile Reqired"));
    }

    if (empty($gargin_name)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Gargin Name Reqired"));
    }

    if (empty($gargin_nic)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Gargin NIC Required"));
    }

    if (empty($gargin_mobile)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Gargin Mobile Reqired"));
    }*/

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
        $classType = strtolower($set->getClassType($class));
    }

    if ($classType != $stu->stuStatus("current")) {
        $error[] = $tool->Message("alert", $tool->transnoecho("only_current_class_can_change_in_admission_form"));
    }


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

        $_SESSION['msg'] = $msg;
        $tool->Redir("students", "admissionfrom&id=" . $redirId, "", "");
        exit;

    }

}
$tpl->renderBeforeContent();



?>

    <div class="social-box">

        <div class="row-fluid">
            <div class="span12">
                <div class="social-box">
                    <div class="header">
                        <h4><?php $tool->trans("add_student_form"); ?></h4>
                    </div>
                    <div id="validationwizard" class="body">




                        <?php if (count($error) > 0) { ?><?php echo implode("<br />", $error); ?><?php } ?>


                        <div id="bar2" class="progress active">
                            <div class="bar bar-success"></div>
                        </div>

                        <form action="" method="post" id="form-validation" class="form-horizontal">
                            <input type="hidden" name="edit_student_id" value="<?php echo $urlPassedID ?>"/>
                            <input type="hidden" name="edit_profile_id" value="<?php echo $profile_id ?>"/>
                            <input type="hidden" name="edit_parents_id" value="<?php echo $parents_id ?>"/>
                            <input type="hidden" name="current_branch" value="<?php echo $branch; ?>">
                            <input type="hidden" name="current_class" value="<?php echo $class; ?>">
                            <input type="hidden" name="current_section" value="<?php echo $section; ?>">
                            <input type="hidden" name="current_session" value="<?php echo $session; ?>">
                            <input type="hidden" name="pic" value=""/>
                            <div class="tab-content"><!--offset2-->







                                <table class="table table-bordered table-striped table-hover">
                                    <tr><td colspan="6" class="fonts">
                                            <div class="alert alert-info" style="font-size: 20px; ">
                                                <strong ><?php Tools::trans("Student Information") ?></div>
                                        </td> </tr>


                                    <tr>
                                        <td width="150" class="fonts" style="font-size: 14px;"><?php Tools::trans("GR Number") ?></td>
                                        <td width="150">
                                            <input value="<?php echo $grnumber; ?>" type="text" name="grnumber" id="grnumber" />
                                        </td>
                                        <td width="150" class="fonts" style="font-size: 14px;"><?php Tools::trans("Name") ?></td>
                                        <td width="150">
                                            <input value="<?php echo $name; ?>" type="text" name="name" id="name" />
                                        </td>
                                        <td width="150" class="fonts" style="font-size: 14px;"><?php Tools::trans("Father Name") ?></td>
                                        <td width="150">
                                            <input value="<?php echo $fname; ?>" type="text" name="fname" id="fname" />
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("B Form") ?></td>
                                        <td><input value="<?php echo $bform; ?>" type="text" name="bform" id="bform" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Blood Group") ?></td>
                                        <td><input value="<?php echo $bloud_group; ?>" type="text" name="bloud_group" id="bloud_group" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Home Phone") ?></td>
                                        <td><input value="<?php echo $home_fone; ?>" type="text" name="home_fone" id="home_fone" /></td>
                                    </tr>

                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Current Address") ?></td>
                                        <td><input value="<?php echo $current_address; ?>" type="text" name="current_address" id="current_address" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Postal Code") ?></td>
                                        <td><input value="<?php echo $postcode; ?>" type="text" name="postcode" id="postcode" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Any Disability") ?></td>
                                        <td><input value="<?php echo $injury; ?>" type="text" name="injury" id="injury" /></td>
                                    </tr>


                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Emergency Contact Name") ?></td>
                                        <td><input value="<?php echo $amergency_name; ?>" type="text" name="amergency_name" id="amergency_name" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Emergency Contact Number") ?></td>
                                        <td><input value="<?php echo $amergency_contact; ?>" type="text" name="amergency_contact" id="amergency_contact" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Emergency Mobile Number") ?></td>
                                        <td><input value="<?php echo $amergency_mobile; ?>" type="text" name="amergency_mobile" id="amergency_mobile" /></td>
                                    </tr>

                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Date Of Birth") ?></td>
                                        <td>

                                            <input size="16" name="date_of_birth" type="text" value="<?php if (!empty($date_of_birth)) echo $tool->ChangeDateFormat($date_of_birth); ?>" class="datepicker">

                                        </td>
                                        <td class="fonts" style="font-size: 14px;"><?php //Tools::trans("Name") ?></td>
                                        <td>
                                           <!-- <input value="<?php /*echo $eng_name; */?>" type="text" name="eng_name" id="eng_name" />-->
                                        </td>
                                        <td class="fonts" style="font-size: 14px;"><?php //Tools::trans("Father Name") ?></td>
                                        <td>
                                            <!--<input value="<?php /*echo $eng_fname; */?>" type="text" name="eng_fname" id="eng_fname" />-->
                                        </td>


                                    </tr>

                                    <tr><td colspan="6" class="fonts">
                                            <div class="alert alert-info" style="font-size: 20px; ">
                                                <strong><?php Tools::trans("Father Information") ?></div>
                                        </td> </tr>

                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Name") ?></td>
                                        <td><input value="<?php echo $fname; ?>" id="fname_replica" name="fname_replica" readonly type="text" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Father CNIC") ?></td>
                                        <td><input value="<?php echo $father_nic; ?>" pattern=".{13}"   required title="13 characters minimum" type="number" name="father_nic" id="father_nic" /></td>
                                        <td class="fonts" style="text-align:left">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Father Education") ?></td>
                                        <td><input value="<?php echo $father_education; ?>" type="text" name="father_education" id="father_education" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Father Mobile") ?></td>
                                        <td><input value="<?php echo $father_mobile; ?>" type="text" name="father_mobile" id="father_mobile" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Father Email") ?></td>
                                        <td><input value="<?php echo $father_email; ?>" type="text" name="father_email" id="father_email" /></td>
                                    </tr>
                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Father Habits") ?></td>
                                        <td><input value="<?php echo $father_habits; ?>" type="text" name="father_habits" id="father_habits" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Father Occupation") ?></td>
                                        <td><input value="<?php echo $father_occupation; ?>" type="text" name="father_occupation" id="father_occupation" /></td>
                                        <td class="fonts" style="font-size: 14px;">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr><td colspan="6" class="fonts">
                                            <div class="alert alert-info" style="font-size: 20px; ">
                                                <strong ><?php Tools::trans("Mother Information") ?></div>
                                        </td> </tr>


                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Name") ?></td>
                                        <td><input value="<?php echo $mother_name; ?>" type="text" name="mother_name" id="mother_name" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Mother CNIC") ?></td>
                                        <td><input value="<?php echo $mother_nic; ?>" type="text" name="mother_nic" id="mother_nic" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Mother Education") ?></td>
                                        <td><input value="<?php echo $mother_education; ?>" type="text" name="mother_education" id="mother_education" /></td>
                                    </tr>
                                    <tr>

                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Mother Mobile Number") ?></td>
                                        <td><input value="<?php echo $mother_mobile; ?>" class="validate[optional,maxSize[15]minSize[9]] " type="text" name="mother_mobile" id="mother_mobile"/></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Mother Habits") ?></td>
                                        <td><input value="<?php echo $mother_habits; ?>" type="text" name="mother_habits" id="mother_habits" /></td>
                                        <td class="fonts" style="font-size: 14px;">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>


                                    <tr><td colspan="6" class="fonts">
                                            <div class="alert alert-info" style="font-size: 20px; ">
                                                <strong ><?php Tools::trans("Guardian Information") ?></div>

                                            <div class="alert alert-success" style="font-size: 20px; ">
                                                <strong ><?php Tools::trans("Same As Father") ?>
                                                       <input type="radio" name="copy" value='yes' onclick="data_copy()"; />&nbsp;&nbsp;
                                                    <?php Tools::trans("No") ?><input type="radio" name="copy" value='no' onclick="data_copy()"; />
                                            </div>



                                        </td> </tr>


                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Guardian Name") ?></td>
                                        <td><input value="<?php echo $gargin_name; ?>" type="text" name="gargin_name" id="gargin_name" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Guardian CNIC") ?></td>
                                        <td><input value="<?php echo $gargin_nic; ?>" type="text" name="gargin_nic" id="gargin_nic" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Guardian Education") ?></td>
                                        <td><input value="<?php echo $gargin_education; ?>" type="text" name="gargin_education" id="gargin_education" /></td>
                                    </tr>
                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Guardian Mobile") ?></td>
                                        <td><input value="<?php echo $gargin_mobile; ?>" type="text" name="gargin_mobile" id="gargin_mobile" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Guardian Habits") ?></td>
                                        <td><input value="<?php echo $gargin_habits; ?>" type="text" name="gargin_habits" id="gargin_habits" /></td>
                                        <td class="fonts" style="text-align:left">&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr><td colspan="6" class="fonts">
                                            <div class="alert alert-info" style="font-size: 20px; ">
                                                <strong ><?php Tools::trans("Office Use") ?></div>
                                        </td> </tr>


                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Test Numbers") ?></td>
                                        <td><input value="<?php echo $test_numbers; ?>" type="text" name="test_numbers" id="test_numbers" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Examiner Opinion") ?></td>
                                        <td><input value="<?php echo $examin_opinion; ?>" type="text" name="examin_opinion" id="examin_opinion" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Condition") ?></td>
                                        <td><select name="term" id="term">
                                                <option value="<?php Tools::trans("No") ?>" <?php if ($shart == 'No') echo 'selected="selected"'; ?>>
                                                    <?php Tools::trans("No") ?>
                                                </option>
                                                <option value="<?php Tools::trans("Yes") ?>" <?php if ($shart == 'Yes') echo 'selected="selected"'; ?>>
                                                    <?php Tools::trans("Yes") ?>
                                                </option>
                                            </select></td>
                                    </tr>

                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"> <?php Tools::trans("Year") ?></td>
                                        <td><?php echo $tpl->getAllSession(array("sel" => $session)); ?>

                                        </td>

                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Branch") ?></td>
                                        <td><?php echo $tpl->userBranches($branch); ?></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Class") ?></td>
                                        <td><?php echo $tpl->getClasses(array("branch" => $branch, "sel" => $class, "data" => $sessionClasses)) ?></td>
                                    </tr>

                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Section") ?></td>
                                        <td><select name="section" id="section">
                                                <?php
                                                if (isset($_GET['id'])) {
                                                    $sessionData = $set->sessionSections($session, $class, $branch);
                                                    echo $tpl->GetOptionVals(array("data" => $sessionData, "sel" => $section));
                                                    ?>

                                                <?php } ?>
                                                <option value=""></option>
                                            </select></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Instructions") ?></td>
                                        <td><input value="<?php echo $instruc; ?>" type="text" name="instruc" id="instruc" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("City") ?></td>
                                        <td><select name="city" id="city">
                                                <?php
                                                $cityData = $set->getTitleTable("zones");
                                                echo $tpl->GetOptionVals(array("data" => $cityData, "sel" => $city));
                                                ?>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Street") ?></td>
                                        <td><input value="<?php echo $sreet; ?>" type="text" name="sreet" id="sreet" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Block") ?></td>
                                        <td><input value="<?php echo $block; ?>" type="text" name="block" id="block" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Approval") ?></td>
                                        <td><input value="<?php echo $approval; ?>" type="text" name="approval" id="approval" /></td>
                                    </tr>
                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Auther Name") ?></td>
                                        <td><input value="<?php echo $author; ?>" type="text" name="author" id="author" /></td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Admission Date") ?></td>
                                        <td>
                                            <input size="16" name="doa" type="text" value="<?php if (!empty($doa)) echo $tool->ChangeDateFormat($doa); ?>" class="datepicker">
                                        </td>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Gender") ?></td>
                                        <td>
                                            <select id="gender" name="gender" style="width: 95%">
                                                <option value=""></option>
                                                <option value="1" <?php if ($gender == 1) echo "selected"; ?> ><?php $tool->trans("male") ?></option>
                                                <option value="2" <?php if ($gender == 2) echo "selected"; ?>><?php $tool->trans("female") ?></option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="fonts" style="font-size: 14px;"><?php Tools::trans("Transport") ?></td>
                                        <td>
                                            <select id="transport" name="transport">
                                                <option value=""></option>
                                                <option value="1" <?php if ($transport == 1) echo 'selected="selected"'; ?>><?php Tools::trans("Personal") ?></option>
                                                <option value="2" <?php if ($transport == 2) echo 'selected="selected"'; ?>><?php Tools::trans("School") ?></option>

                                            </select>

                                        </td>

                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>

                                    </tr>


                                    <tr>
                                        <td colspan="6" style="text-align: center"><input type="submit" name="submit" value="Submit" class='btn ui-button-success'></td>
                                    </tr>




                                </table>






                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="<?php echo $tool->getWebUrl() ?>/js/fahad.php"></script>

    <script>


        $(function () {

            $('#fname').change(function() {
                $('#fname_replica').val($(this).val());
            });


            //FormWizard.init();

            //$.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });

            addChosenValidator = function () {
                $.validator.addMethod("chosen", (function (value, element) {
                    if (value === 0) {
                        return false;
                    } else {
                        if (value.length === 0) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                }), "Please select an option");
            };

            $("#form-validation").validate({
                errorElement: "span",
                errorPlacement: function (error, element) {
                    element.css('border-color', "#b94a48");
                    //error.addClass("help-block");
                    //element.parents(".control-group").removeClass("success").addClass("error");
                    //return element.parents(".control-group").find("a.chzn-single").addClass("error");
                    return error.addClass("error");
                },
                success: function (label) {
                    //label.parents("td.fonts").removeClass("error");
                    //return label.parents(".control-group").find("a.chzn-single").removeClass("error");
                    return label.closest("input,select").css('border-color', "#bdc7d8"); // label.css('border-color', "#bdc7d8");
                },
                rules: {

                    grnumber: {
                        required: true,
                        minlength: 2,
                        digits: false
                    },
                    date_of_birth:{
                        required: true
                    },
                    home_fone: {
                        digits: true,
                        minlength: 11
                    },
                    name: {
                        required: true
                    },
                    gender: {
                        required: true,
                        chosen: true
                    },
                    fname: {
                        required: true
                    },
                    address: {
                        required: true,
                        minlength: 3
                    },
                    amergency_name: {
                        required: true
                    },
                    amergency_mobile: {
                        required: true,
                        digits: true,
                        minlength: 11

                    },
                    father_nic: {
                        required: true,
                        digits: true,
                        minlength: 13

                    },
                    father_mobile: {
                        required: true,
                        digits: true,
                        minlength: 11
                    },
                    mother_name: {
                        required: true
                    },
                    mother_nic: {
                        required: true,
                        digits: true,
                        minlength: 13
                    },
                    mother_mobile: {
                        required: true,
                        digits: true,
                        minlength: 11
                    },
                    gargin_name: {
                        required: true
                    },
                    gargin_nic: {
                        required: true,
                        digits: true,
                        minlength: 13
                    },
                    gargin_mobile: {
                        required: true,
                        digits: true,
                        minlength: 11
                    },
                    session: {
                        required: true,
                        chosen: true
                    },
                    branch: {
                        required: true,
                        chosen: true

                    },
                    class: {
                        required: false
                    },
                    section: {
                        required: true
                    }

                }
            });




            $('input:radio[name="copy"]').change(
                function () {
                    if ($(this).is(':checked') && $(this).val() == 'yes') {
                        //alert("Copy Father Info");

                        $("#gargin_nic").val($("#father_nic").val());
                        $('#gargin_name').val($("#fname_replica").val());
                        $('#gargin_education').val($("#father_education").val());
                        $('#gargin_mobile').val($("#father_mobile").val());
                        $('#gargin_habits').val($("#father_habits").val());

                        //$("#father_email").val;
                        //$('#').val()$("#father_occupation").val;


                    } else {
                        $("#gargin_nic").val("");
                        $('#gargin_name').val("");
                        $('#gargin_education').val("");
                        $('#gargin_mobile').val("");
                        $('#gargin_habits').val("");
                    }
                });

        });




    </script>

    <style type="text/css">
        [class*="span"] .chosen-container {
            width: 86%!important;
            min-width: 86%;
            max-width: 86%;
        }

        select{width: 86% !important;}
        /*.datepicker table tr td span {
            display: block;
            width: 23%;
            height: 54px;
            line-height: 54px;
            float: left;
            margin: 1%;
            cursor: pointer;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
        }*/
        /*.datepicker-months table tr td span.month{display: inline-block; float: left; text-align: center; width: 15%;}*/
    </style>
<?php


$tpl->footer();
