<?php
Tools::getModel("AcademicModel");
$set = new SettingModel();
$acd = new AcademicModel();


function validateUsername($username)
{
    // Remove spaces and convert to lowercase
    $cleanUsername = strtolower(str_replace(' ', '', $username));

    // Check for alphanumeric username
    if (ctype_alnum($cleanUsername)) {
        return $cleanUsername;
    }

    // Check for valid email format
    if (filter_var($cleanUsername, FILTER_VALIDATE_EMAIL)) {
        return $cleanUsername;
    }

    // Check for valid phone number format (assuming only digits and optional '+')
    if (preg_match('/^\+?\d+$/', $cleanUsername)) {
        return $cleanUsername;
    }

    // Invalid username
    return false;
}

/*if(!isset($_SESSION['UserBranchId'])){
    $tpl->removeMenuCache();
    unset($_SESSION['UserId']);
    session_destroy();
    $tool->Redir("settings","login","","");
    exit;
}*/

$id = isset($_GET['id']) ? $tool->intVal($_GET['id']) : '';
$name = "";
$group = "";
$username = "";
//$branch = "";

$row = array();

$row['name'] = "";

$selectedBranch = array();

if (!empty($id)) {
    $branchData = $set->getUserBranches(array("id" => $id));

    foreach ($branchData as $rB) {
        $selectedBranch[] = $rB['branch_id'];
    }

}





if (isset($_GET['id'])) {
    if (!empty($_GET['id'])) {
        $row = $set->UserEdit($id);
        $name = $row['name'];
        $username = $row['username'];
        //$branch = $row['branch_id'];
    }
}



$vals = array();
$errors = array();
$valBranches = array();
if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {


    $name = $_POST['name'];
    //$group = $tool->GetExplodedInt($_POST['group']);
    $username = $_POST['username'];
    //$branch = $_POST['branch'];





    if (empty($name)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("name_required"));
    }






    if (empty($username)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("username_required"));
    }

    if (empty($_POST['id']) && empty($_POST['password'])) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("password_required"));
    }



    /*if (empty($group)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_select_group"));
    }*/




    if (count($errors) == 0) {
        $data['name'] = $_POST['name'];
        $data['username'] = validateUsername($_POST['username']);
        $data['password'] = ($_POST['password']);
        $data['phone_number'] = '';
        $data['address'] = '';
        $data['group_id'] = 3;
        //$data['branch_id'] = $branch;
        $data['published'] = 1;
        $data['user_type'] = 'teacher';

        if (!empty($_POST['id'])) {
            unset($data['phone_number']);
            unset($data['address']);
            //unset($data['published']);
        }


        if (!empty($_POST['id']) && empty($_POST['password'])) {
            unset($data['password']);
        }





        $genId = 0;
        if (empty($_POST['id'])) {

            if ($set->insert($set->getPrefix() . "users", $data)) {
                $genId = $set->lastid();
                $_SESSION['msg'] = $tool->Message("succ", $_POST['username'] . " " . $tool->transnoecho("inserted"));
            } else {
                $_SESSION['msg'] = $tool->Message("alert", $set->getError());
            }

        } else {
            $genId = $tool->intVal($_POST['id']);
            $set->update($set->getPrefix() . "users", $data, array("id" => $tool->intVal($_POST['id'])));
            $_SESSION['msg'] = $tool->Message("succ", $_POST['username'] . " " . $tool->transnoecho("updated"));
        }

        /*foreach ($_POST['subject'] as $subject){
            $vals[] = array($genId,$subject);
        }
        $acd->removeTeacherSubjects($genId);
        $res = $acd->insertTeacherSubjects($vals);*/


        if (isset($_POST['branches'])) {
            foreach ($_POST['branches'] as $branch) {
                $branchId = $tool->GetExplodedInt($branch);
                $valBranches[] = $tool->setInsertDefaultValues(array($genId, "$branchId"));
            }
        }


        /*if (empty($valBranches)) {
            $errors[] = $tool->Message("alert", $tool->transnoecho("branch_required"));
        }*/



        $set->removeLastBranches($genId, "users");
        $resBranch = $set->insertBranches(false, $valBranches);




        $tool->Redir("academic", "editteacher&id=" . $genId, $_POST['code'], $_POST['action']);
        exit;

    }


}




$tpl->renderBeforeContent();


if (count($errors) > 0) {
    echo $tool->Message("alert", implode("<br />", $errors));
}
?>





<div class="social-box">
    <div class="header">
        <div class="tools">


        </div>
    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>




        <div class="container text-center">

            <div class="row alert">
                <div class="span12">
                    <?php
                    $teacherSubjects = array();
                    $teacherSubjectArr = array();
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        echo $tool->transnoecho("edit") . " " . $row['name'];
                        $teacherSubjects = $acd->getTeacherSubjects($tool->GetInt($_GET['id']));

                        foreach ($teacherSubjects as $teacherSubject) {
                            $teacherSubjectArr[$teacherSubject['subject_id']] = true;
                        }
                    } else {
                        $tool->trans("add_teacher");
                    }





                    ?>
                </div>
            </div>

            <?php
            echo $tpl->formTag("post");
            echo $tpl->formHidden();
            ?>

            <input type="hidden" name="_chk" value="1">
            <input type="hidden" name="id" value="<?php if (isset($_GET['id']))
                echo $_GET['id']; ?>">


            <div class="row-fluid">
                <div class="span12">
                    <label for="name" class="fonts">
                        <?php $tool->trans("name") ?>
                    </label>
                    <input value="<?php echo $name ?>" type="text" name="name" id="name">
                </div>
            </div>



            <div class="form-group">
                <label for="username" class="fonts">
                    <?php $tool->trans("username") ?>
                </label>
                <input value="<?php echo $username ?>" type="text" name="username" id="username">
            </div>


            <div class="form-group">
                <label for="name" class="fonts">
                    <?php $tool->trans("password") ?>
                </label>
                <input value="" type="password" name="password" id="password">
            </div>


            <?php
            $userBranches = $set->userBranches();
            $userBranchCount = count($userBranches);



            if ($userBranchCount == 1) {
                echo '<input type="hidden" name="branches[]" value="' . $userBranches[0]['id'] . '-' . $userBranches[0]['title'] . '" />';
            } else {
                ?>

                <div class="form-group">
                    <label class="fonts">
                        <?php $tool->trans("branch") ?>
                    </label>

                    <?php
                    echo $tpl->GetMultiOptions(array("name" => "branches[]", "data" => $set->userBranches(), "sel" => $selectedBranch));
                    ?>
                </div>

            <?php } ?>

            <div class="form-group">&nbsp;</div>


            <!--<div class="form-group">
                    <div class="row-fluid">
                        <div class="span4">&nbsp;</div>
                        <div class="span4">

                            <div id="menu-collapse" class="ui-accordion ui-widget ui-helper-reset ui-sortable" role="tablist">
                                <?php
                                /*                                $subjects = $acd->getSubjectWithClass();
                                                                $classes = array();
                                                                $subjectsArr = array();
                                                                foreach ($subjects as $subject){
                                                                    $classes[$subject['class_id']] = array("id" => $subject['class_id'],"title" => $subject['class_title']);
                                                                    $subjectsArr[$subject['class_id']][] = array("id" => $subject['id'],"title" => $subject['title']);
                                                                }
                                                                foreach ($classes as $class){ */?>
                                    <div class="group">
                                        <h3><a href="#" class="fonts"><?php /*echo $class['title'] */?></a></h3>
                                        <section class="feeds social-box social-bordered social-blue">
                                            <div class="header"><h4><i class="icon-th-list"></i><?php /*echo $class['title']; */?></h4></div>

                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>&nbsp;</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                /*                                                if(isset($subjectsArr[$class['id']])){
                                                                                                    foreach ($subjectsArr[$class['id']] as $subject){
                                                                                                */?>
                                                    <tr>
                                                        <td><input type="checkbox" <?php /*if(isset($teacherSubjectArr[$subject['id']])) echo ' checked'; */?> name="subject[<?php /*echo $subject['id'] */?>]" value="<?php /*echo $subject['id'] */?>"></td>
                                                        <td><?php /*echo $subject['title'] */?></td>
                                                    </tr>

                                                <?php /*} */?>
                                                <?php /*} */?>
                                                </tbody>
                                            </table>

                                        </section>

                                    </div>

                                <?php /*} */?>

                            </div>


                        </div>
                        <div class="span4">&nbsp;</div>

                    </div>

                </div>-->


            <div class="form-group">

                <input type="submit" name="Submit" class="btn btn-success" value="<?php if (empty($id))
                    $tool->trans("add");
                else
                    $tool->trans("edit"); ?>" />
            </div>

            <?php echo $tpl->formClose() ?>


        </div>



    </div>
</div>
<style type="text/css">
    .chosen-container {
        width: 19% !important;
    }

    [class*="span"] .chosen-container {
        width: 30% !important;
        min-width: 30%;
        max-width: 30%;
    }
</style>
<?php
$tpl->footer();
