<?php
Tools::getModel("StudentsModel");
$stu = new StudentsModel();

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

    if (empty($current_address)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Current Address Required"));
    }

    if (empty($amergency_name)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Amergency Name Required"));
    }

    if (empty($amergency_mobile)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Amergency Mobile Required"));
    }

    if (empty($father_nic)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Father NIC Required"));
    }

    if (empty($father_mobile)) {
        $error[] = $tool->Message("alert", $tool->transnoecho("Father Mobile Reqired"));
    }

    if (empty($mother_nic)) {
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


        $paramTransfer['student_id'] = $redirId;
        $paramTransfer['old_branch'] = $tool->GetInt($_POST['current_branch']);
        $paramTransfer['old_class'] = $tool->GetInt($_POST['current_class']);
        $paramTransfer['old_section'] = $tool->GetInt($_POST['current_section']);
        $paramTransfer['old_session'] = $tool->GetInt($_POST['current_session']);
        $paramTransfer['current_branch'] = $branch;
        $paramTransfer['current_class'] = $class;
        $paramTransfer['current_section'] = $section;
        $paramTransfer['current_session'] = $session;

        $stu->logTransferDetail($paramTransfer);

        $_SESSION['msg'] = $msg;
        $tool->Redir("students", "admissionfrom&id=" . $redirId, "", "");
        exit;

    }

}
$tpl->renderBeforeContent();
?>


    <style>
        .inputWidth {
            width: 190px;
        }

        .inputWidthDate {
            width: 165px;
        }

        .control-label {
            text-align: left !important;
        }
    </style>

    <div class="social-box">

        <div class="row-fluid">
            <div class="span12">
                <div class="social-box">
                    <div class="header">
                        <h4><?php $tool->trans("add_student_form"); ?></h4>
                    </div>
                    <div id="validationwizard" class="body">
                        <!-- BEGIN TABS CONTROLS WIZARD -->
                        <div class="navbar form-wizard">
                            <div class="navbar-inner">
                                <div class="container-fluid">
                                    <ul>
                                        <li class="fonts">
                                            <a href="#tab-validation1" data-toggle="tab"><i class="icon-user"></i><?php $tool->trans("student_infomation") ?>
                                            </a></li>
                                        <li class="fonts">
                                            <a href="#tab-validation2" data-toggle="tab"><i class="icon-envelope"></i> <?php $tool->trans("father_infomation") ?>
                                            </a></li>
                                        <li class="fonts">
                                            <a href="#tab-validation3" data-toggle="tab"><i class="icon-credit-card"></i> <?php $tool->trans("mother_infomation") ?>
                                            </a></li>
                                        <li class="fonts">
                                            <a href="#tab-validation4" data-toggle="tab"><i class="icon-check"></i> <?php $tool->trans("gargin_infomation") ?>
                                            </a></li>
                                        <li class="fonts">
                                            <a href="#tab-validation5" data-toggle="tab"><i class="icon-check"></i> <?php $tool->trans("office_use") ?>
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>


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

                                <div class="tab-pane" id="tab-validation1">


                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("gr") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $grnumber; ?>" type="text" name="grnumber" id="grnumber" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("name") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $name; ?>" type="text" name="name" id="name" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("fname") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $name; ?>" type="text" name="fname" id="fname" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">


                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("gender") ?></label>
                                                <div class="controls">
                                                    <select id="gender" name="gender" style="width: 95%">
                                                        <option value=""></option>
                                                        <option value="1" <?php if ($gender == 1) echo "selected"; ?> ><?php $tool->trans("male") ?></option>
                                                        <option value="2" <?php if ($gender == 2) echo "selected"; ?>><?php $tool->trans("female") ?></option>
                                                    </select>
                                                    <p class="help-block"></p>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("eng_name") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $eng_name; ?>" type="text" name="eng_name" id="eng_name" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("eng_fname") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $eng_fname; ?>" type="text" name="eng_fname" id="eng_fname" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br style="clear: both;">


                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("date_of_birth") ?></label>
                                                <div class="controls">
                                                    <div class="input-append date form_datetime2">
                                                        <input size="16" name="date_of_birth" type="text" value="<?php if (!empty($date_of_birth)) echo $tool->ChangeDateFormat($date_of_birth); ?>" class="inputWidthDate" readonly>
                                                        <span class="add-on"><i class="icon-th"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("b_form") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $bform; ?>" type="text" name="bform" id="bform" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("blood_group") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $bloud_group; ?>" type="text" name="bloud_group" id="bloud_group" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("home_phone") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $home_fone; ?>" type="text" name="home_fone" id="home_fone" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("current_address") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $current_address; ?>" type="text" name="current_address" id="current_address" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("postal_code") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $postcode; ?>" type="text" name="postcode" id="postcode" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("injury") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $injury; ?>" type="text" name="injury" id="injury" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("amergency_name") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $amergency_name; ?>" type="text" name="amergency_name" id="amergency_name" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("amergency_contact") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $amergency_contact; ?>" type="text" name="amergency_contact" id="amergency_contact" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("amergency_mobile") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $amergency_mobile; ?>" type="text" name="amergency_mobile" id="amergency_mobile" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">

                                        </div>

                                    </div>

                                    <br style="clear: both;">
                                </div>
                                <!-- END TAB1 CONTAINER -->
                                <!-- BEGIN TAB2 CONTAINER -->
                                <div class="tab-pane" id="tab-validation2">
                                    <h3><?php $tool->trans("father_infomation"); ?></h3>
                                    <!-- Text input-->

                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("father_nic") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $father_nic; ?>" type="text" name="father_nic" id="father_nic" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("name") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $fname; ?>" id="fname_replica" name="fname" type="text" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("education") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $father_education; ?>" type="text" name="father_education" id="father_education" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("father_mobile") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $father_mobile; ?>" type="text" name="father_mobile" id="father_mobile" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("father_email") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $father_email; ?>" type="text" name="father_email" id="father_email" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("father_habits") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $father_habits; ?>" type="text" name="father_habits" id="father_habits" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("father_occupation") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $father_occupation; ?>" type="text" name="father_occupation" id="father_occupation" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">

                                        </div>
                                        <div class="span4">

                                        </div>

                                    </div>


                                </div>
                                <!-- END TAB2 CONTAINER -->
                                <!-- BEGIN TAB3 CONTAINER -->
                                <div class="tab-pane" id="tab-validation3">

                                    <h3><?php $tool->trans("mother_information"); ?></h3>


                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("name") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $mother_name; ?>" type="text" name="mother_name" id="mother_name" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("mother_nic") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $mother_nic; ?>" type="text" name="mother_nic" id="mother_nic" class="inputWidth"/>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("education") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $mother_education; ?>" type="text" name="mother_education" id="mother_education" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("mobile") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $mother_mobile; ?>" class="validate[optional,maxSize[15]minSize[9]] " type="text" name="mother_mobile" id="mother_mobile"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("mother_habits") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $mother_habits; ?>" type="text" name="mother_habits" id="mother_habits" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">

                                        </div>

                                    </div>


                                </div>
                                <!-- END TAB3 CONTAINER -->
                                <!-- BEGIN TAB4 CONTAINER -->
                                <div class="tab-pane" id="tab-validation4">

                                    <h3><?php $tool->trans("gargian_information"); ?></h3>


                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("copy_father_info") ?></label>
                                                <div class="controls">
                                                    <input type="radio" name="copy" value='yes' id="copy_father"/>&nbsp;&nbsp;&nbsp;
                                                    <?php $tool->trans("new_info"); ?>
                                                    <input type="radio" name="copy" value='no' id="copy_father"/>
                                                    <!--onclick="data_copy()";-->
                                                </div>

                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("name") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $gargin_name; ?>" type="text" name="gargin_name" id="gargin_name" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("gargin_nic") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $gargin_nic; ?>" type="text" name="gargin_nic" id="gargin_nic" class="inputWidth"/>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("education") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $gargin_education; ?>" type="text" name="gargin_education" id="gargin_education" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("mobile") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $gargin_mobile; ?>" type="text" name="gargin_mobile" id="gargin_mobile" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("gargin_habits") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $gargin_habits; ?>" type="text" name="gargin_habits" id="gargin_habits" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>


                                </div>
                                <!-- END TAB4 CONTAINER -->
                                <!-- BEGIN TAB5 CONTAINER -->
                                <div class="tab-pane" id="tab-validation5">

                                    <h3><?php $tool->trans("office_use") ?></h3>

                                    <div class="row-fluid">


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("test_number") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $test_numbers; ?>" type="text" name="test_numbers" id="test_numbers" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("examin_opinion") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $examin_opinion; ?>" type="text" name="examin_opinion" id="examin_opinion" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("term") ?></label>
                                                <div class="controls">
                                                    <select name="term" id="term">
                                                        <option value="غیرمشروط" <?php if ($shart == 'غیرمشروط') echo 'selected="selected"'; ?>>
                                                            غیرمشروط
                                                        </option>
                                                        <option value="مشروط" <?php if ($shart == 'مشروط') echo 'selected="selected"'; ?>>
                                                            مشروط
                                                        </option>
                                                    </select>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">


                                        <div class="span4">
                                            <div class="control-group">

                                                <label class="control-label fonts"><?php $tool->trans("session") ?></label>

                                                <div class="controls"><?php echo $tpl->getAllSession(array("sel" => $session)); ?></div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">

                                                <label class="control-label fonts"><?php $tool->trans("branch") ?></label>

                                                <div class="controls"><?php echo $tpl->userBranches($branch); ?></div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("class") ?></label>

                                                <div class="controls"><?php echo $tpl->getClasses(array("branch" => $branch, "sel" => $class, "data" => $sessionClasses)) ?></div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("section") ?></label>

                                                <div class="controls">
                                                    <select name="section" id="section" style="width: 98%">
                                                        <?php
                                                        if (isset($_GET['id'])) {
                                                            $sessionData = $set->sessionSections($session, $class, $branch);
                                                            echo $tpl->GetOptionVals(array("data" => $sessionData, "sel" => $section));
                                                            ?>

                                                        <?php } ?>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("instruction") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $instruc; ?>" type="text" name="instruc" id="instruc" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("transport") ?></label>
                                                <div class="controls">

                                                    <select id="transport" name="transport">
                                                        <option value=""></option>
                                                        <option value="1" <?php if ($transport == 1) echo 'selected="selected"'; ?>>
                                                            ذاتی
                                                        </option>
                                                        <option value="2" <?php if ($transport == 2) echo 'selected="selected"'; ?>>
                                                            اسکول کی
                                                        </option>

                                                    </select>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("city") ?></label>
                                                <div class="controls">
                                                    <select name="city" id="city">

                                                        <?php
                                                        $cityData = $set->getTitleTable("zones");

                                                        echo $tpl->GetOptionVals(array("data" => $cityData, "sel" => $city));
                                                        ?>

                                                    </select>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("resident") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $sreet; ?>" type="text" name="sreet" id="sreet" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("block") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $block; ?>" type="text" name="block" id="block" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <br style="clear: both;">

                                    <div class="row-fluid">


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("approval") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $approval; ?>" type="text" name="approval" id="approval" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("author") ?></label>
                                                <div class="controls">
                                                    <input value="<?php echo $author; ?>" type="text" name="author" id="author" class="inputWidth"/>
                                                    <p class="help-block"></p>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="span4">
                                            <div class="control-group">
                                                <label class="control-label fonts"><?php $tool->trans("date_of_admission") ?></label>
                                                <div class="controls">
                                                    <div class="input-append date form_datetime2">
                                                        <input size="16" name="doa" type="text" value="<?php if (!empty($doa)) echo $tool->ChangeDateFormat($doa); ?>" class="inputWidthDate" readonly>
                                                        <span class="add-on"><i class="icon-th"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row-fluid">


                                    </div>

                                </div>
                                <!-- END TAB5 CONTAINER -->
                            </div>
                            <!-- BEGIN FORM BUTTONS ACTION CONTROLS -->
                            <div id="action-container" class="form-actions">
                                <div class="offset2">
                                    <button type='button' class='btn button-previous' name='previous'>
                                        <i class="icon-angle-left"></i> <?php $tool->trans("previous"); ?></button>
                                    <button type='button' class='btn button-next' name='next'><?php $tool->trans("next"); ?>
                                        <i class="icon-angle-right"></i></button>
                                    <input type="submit" name="submit" value="Submit" class='btn button-finish'>
                                    <!--<button type="submit" class='btn button-finish' name='finish'>Finish <i class="icon-ok"></i></button>-->
                                </div>
                            </div>
                            <!-- END FORM BUTTONS ACTION CONTROLS -->
                        </form>
                        <!-- END FORM WIZARD  -->
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="<?php echo $tool->getWebUrl() ?>/js/fahad.php"></script>

    <script>


        $(function () {
            FormWizard.init();

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
                    error.appendTo(element.parents("div.controls"));
                    error.addClass("help-block");
                    element.parents(".control-group").removeClass("success").addClass("error");
                    return element.parents(".control-group").find("a.chzn-single").addClass("error");
                },
                success: function (label) {
                    label.parents(".control-group").removeClass("error");
                    return label.parents(".control-group").find("a.chzn-single").removeClass("error");
                },
                rules: {

                    gr: {
                        required: true,
                        minlength: 2,
                        digits: false
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


            $('#father_nic').focusout(function () {

                var father_nic = $(this).val();

                var postData =
                    {
                        "form": "getFatherInfo",
                        "father_nic": father_nic
                    }

                $.ajax({
                    url: "<?php echo Tools::makeLink("ajax", "students", "", "") ?>",
                    type: 'POST',
                    data: postData,
                    dataType: 'json',
                    success: function (response) {
                        //alert(response.father_education);
                        $("#fname_replica").val(response.fname);
                        $("#father_education").val(response.father_education);
                        $("#father_mobile").val(response.father_mobile);
                        $("#father_email").val(response.father_email);
                        $("#father_habits").val(response.father_habits);
                        $("#father_occupation").val(response.father_occupation);

                    }
                });

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


<?php


$tpl->footer();
