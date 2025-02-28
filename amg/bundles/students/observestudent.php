<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
//Tools::getModel("SmsModel");
//$sms = new SmsModel();
Tools::getModel("StudentsModel");
$stu = new StudentsModel();

function generateStrongPassword($length = 8)
{
    /*

    INSERT INTO `jb_student_credentials` (student_id, password, published)
SELECT
    id,
    CONCAT(
        CHAR(FLOOR(97 + (RAND() * 26))), -- Random lowercase letter
        CHAR(FLOOR(65 + (RAND() * 26))), -- Random uppercase letter
        CHAR(FLOOR(48 + (RAND() * 10))), -- Random digit
        SUBSTRING('!@#', FLOOR(1 + (RAND() * 3)), 1), -- Random special character (!, @, or #)
        CHAR(FLOOR(97 + (RAND() * 26))), -- Random lowercase letter
        CHAR(FLOOR(65 + (RAND() * 26))), -- Random uppercase letter
        CHAR(FLOOR(48 + (RAND() * 10))), -- Random digit
        SUBSTRING('!@#', FLOOR(1 + (RAND() * 3)), 1)  -- Random special character (!, @, or #)
    ) AS generatedPassword,
    1
FROM jb_students
WHERE branch_id = 14 AND student_status = 'current';



     */
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $digits = '0123456789';
    $specialChars = '!@#';

    // Ensure the password contains at least one character from each pool
    $password = '';
    $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
    $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
    $password .= $digits[rand(0, strlen($digits) - 1)];
    $password .= $specialChars[rand(0, strlen($specialChars) - 1)];

    // Fill the rest of the password with a random mix of all characters
    $allChars = $lowercase . $uppercase . $digits . $specialChars;
    for ($i = 4; $i < $length; $i++) {
        $password .= $allChars[rand(0, strlen($allChars) - 1)];
    }

    // Shuffle the password to randomize character positions
    return str_shuffle($password);
}

$tpl->setCanExport(false);

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$errors = array();


$param['session'] = $session;
$param['branch'] = $branch;
$param['class'] = $class;
$param['section'] = $section;


if (isset($_GET['del']) == 1) {
    if (isset($_GET['id'])) {
        if (is_numeric($_GET['id'])) {
            $id = $_GET['id'];
            $stu->removeStudentCredentials($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if (isset($_POST['_chk']) == 1) {
    $id = isset($_POST['id']) ? $tool->GetInt($_POST['id']) : 0;
    //$password = isset($_POST['password']) ? $stu->filter($_POST['password']) : 0;
    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";


    $data['student_id'] = $id;
    $data['password'] = generateStrongPassword();
    $data['published'] = 1;

    if (empty($id)) {
        $errors[] = $tool->transnoecho("please_enter_student_id");
    }


    /*if (empty($password)) {
        $errors[] = $tool->transnoecho("please_enter_password");
    }*/


    if (count($errors) == 0) {
        $a = 0;


        $res = $stu->insertCredentials($data);

        if ($res) {
            if (empty($url)) {
                Tools::Redir("students", "observestudent", "", "");
            } else {
                header("Location:" . $url);
            }
            exit;
        } else {
            echo $tool->Message("alert", $tool->transnoecho("insert_failed"));
        }
    }


}


$tpl->renderBeforeContent();


if (count($errors) > 0) {
    echo $tool->Message("alert", implode("<br />", $errors));
}

$qr->searchContentAbove();


?>
    <div class="row-fluid" id="student_res"></div>
    <div class="row-fluid">
        <div class="span2"><label class="fonts">
                <?php $tool->trans("session") ?>
            </label>
            <?php echo $tpl->getAllSession() ?>
        </div>
        <div class="span3"><label class="fonts">
                <?php $tool->trans("branch") ?>
            </label>
            <?php echo $tpl->userBranches() ?>
        </div>
        <div class="span3"><label class="fonts">
                <?php $tool->trans("class") ?>
            </label>
            <?php echo $tpl->getClasses() ?>
        </div>
        <div class="span2"><label class="fonts">
                <?php $tool->trans("section") ?>
            </label>
            <?php echo $tpl->getSecsions() ?>
        </div>
        <div class="span2"><label>&nbsp;</label><input type="submit" class="btn"
                                                       value="<?php $tool->trans("Search") ?>">
        </div>
    </div>


<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {


    if (empty($branch)) {
        echo $tool->Message("alert", $tool->transnoecho("branch_required"));
        exit;
    }

    if (empty($session)) {
        echo $tool->Message("alert", $tool->transnoecho("session_required"));
        exit;
    }


}
?>
    <div class="body">


        <div class="row-fluid">


            <div class="span3">
                <form method="post">

                    <?php
                    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);
                    echo $tpl->formHidden(); ?>
                    <input type="hidden" name="url" value="<?php echo $curPageUrl ?>">
                    <table>
                        <tr>
                            <td>
                                <label class="fonts">
                                    <?php $tool->trans("id") ?>
                                </label>
                                <input value="<?php if (isset($_POST['id']))
                                    echo $_POST['id'] ?>" type="number" name="id" id="id">
                            </td>
                        </tr>


                        <!--<tr>
                                <td>
                                    <label class="fonts">
                                    <?php /*$tool->trans("password") */ ?>
                                </label>
                                <input value="<?php /*if (isset($_POST['password']))
                                    echo $_POST['password'] */ ?>" type="text" name="password" id="password">
                                </td>
                            </tr>-->
                        <tr>
                            <td>
                                <input type="submit" class="btn btn-primary" value="Save">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>


            <div class="span9">
                <div id="printReady">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                <?php $tool->trans("s_no"); ?>
                            </th>
                            <th>
                                <?php $tool->trans("name_father_name"); ?>
                            </th>
                            <th>
                                <?php $tool->trans("username"); ?>
                            </th>
                            <th>
                                <?php $tool->trans("password"); ?>
                            </th>
                            <th>
                                <?php $tool->trans("published"); ?>
                            </th>
                            <th class="no-print">
                                <?php $tool->trans("delete"); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php


                        $res = $stu->getCredentials($param);

                        $i = 0;
                        foreach ($res as $row) {
                            $i++;
                            ?>
                            <tr>
                                <td class="fonts">
                                    <?php echo $i; ?>
                                </td>
                                <td class="fonts">
                                    <?php echo $row['name'] . " " . $row['fname']; ?>
                                </td>
                                <td class="fonts">
                                    <?php echo $row['student_id']; ?>
                                </td>
                                <td class="fonts">
                                    <?php echo $row['password']; ?>
                                </td>
                                <td class="fonts">
                                    <?php
                                    if ($row['published'] == 1) {
                                        echo '<span class="label label-success fonts">Enable</span>';
                                    } else {
                                        echo '<span class="label label-important fonts">Disabled</span>';
                                    }

                                    ?>
                                </td>
                                <td class="no-print"><a onclick="return confirm('Are you sure you want to delete?');"
                                                        href="<?php echo Tools::makeLink("students", "observestudent&del=1&id=" . $row['id'] . "&redir=" . $curPageUrl, "", "") ?>"><i
                                                class="icon-remove"></i></a></td>

                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>

<?php

$tpl->footer();
