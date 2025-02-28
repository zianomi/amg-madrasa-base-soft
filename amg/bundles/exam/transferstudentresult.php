<?php
/* @var Template $tpl */
/* @var Tools $tool */
Tools::getLib("QueryTemplate");
Tools::getModel("ExamModel");
$qr = new QueryTemplate();
$exm = new ExamModel();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$examName = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

Tools::getModel("StudentsModel");
$stu = new StudentsModel();
$ids = array();
$errors = array();
$vals = array();
$defaulterIds = array();

if (isset($_POST['_chk']) == 1) {


    $newBranch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $newClass = (isset($_POST['class'])) ? $tool->GetExplodedInt($_POST['class']) : '';
    $newSection = (isset($_POST['section'])) ? $tool->GetExplodedInt($_POST['section']) : '';
    $newSession = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';
    $examName = (isset($_POST['exam_name'])) ? $tool->GetExplodedInt($_POST['exam_name']) : '';


    $oldbranch = (isset($_POST['oldbranch'])) ? $tool->GetInt($_POST['oldbranch']) : '';
    $oldclass = (isset($_POST['oldclass'])) ? $tool->GetInt($_POST['oldclass']) : '';
    $oldsection = (isset($_POST['oldsection'])) ? $tool->GetInt($_POST['oldsection']) : '';
    $oldsession = (isset($_POST['oldsession'])) ? $tool->GetInt($_POST['oldsession']) : '';
    $oldexam = (isset($_POST['oldexam'])) ? $tool->GetInt($_POST['oldexam']) : '';

    $vals['exam'] = $examName;
    $vals['session'] = $newSession;
    $vals['branch'] = $newBranch;
    $vals['class'] = $newClass;
    $vals['section'] = $newSection;
    $vals['old_exam'] = $oldexam;
    $vals['old_session'] = $oldsession;
    $vals['old_branch'] = $oldbranch;
    $vals['old_class'] = $oldclass;
    $vals['old_section'] = $oldsection;



    if(empty($examName)){
        $errors[] = Tools::transnoecho("exam_required");
    }

    if(empty($newBranch)){
        $errors[] = Tools::transnoecho("branch_required");
    }

    if(empty($newClass)){
        $errors[] = Tools::transnoecho("class_required");
    }

    if(empty($newSection)){
        $errors[] = Tools::transnoecho("section_required");
    }

    if(empty($newSession)){
        $errors[] = Tools::transnoecho("session_required");
    }


    if(empty($oldexam)){
        $errors[] = Tools::transnoecho("exam_required");
    }

    if(empty($oldbranch)){
        $errors[] = Tools::transnoecho("branch_required");
    }

    if(empty($oldclass)){
        $errors[] = Tools::transnoecho("class_required");
    }

    if(empty($oldsection)){
        $errors[] = Tools::transnoecho("section_required");
    }

    if(empty($oldsession)){
        $errors[] = Tools::transnoecho("session_required");
    }


    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $val) {
            $ids[] = $val;
        }
    }

    $vals['ids'] = $ids;


    if (empty($ids)) {
        $errors[] = Tools::transnoecho("please_select_atleast_one_student");
    }




    if (count($errors) == 0) {


        $exm->transferNumbers($vals);
        $_SESSION['msg'] = $tool->Message("succ", count($ids) . " " . $tool->transnoecho("student_numbers_transferd"));
        $tool->Redir("exam", "transferstudentresult", "", "list");
        exit;
    }
}


$param['branch'] = $branch;
$param['class'] = $class;
$param['section'] = $section;
$param['session'] = $session;
$param['exam'] = $examName;


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);

$qr->searchContentAbove();


if (!isset($_GET['_chk']) == 1) {
    ?>

    <div class="row-fluid">
        <div class="span3"><label
                    class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label
                    class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?>
        </div>
        <div class="span3"><label
                    class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>

    </div>

    <div class="row-fluid">

        <div class="span3"><label
                    class="fonts"><?php $tool->trans("exam_name") ?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?>
        </div>
        <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>
        <div class="span3"><label>&nbsp;</label></div>
        <div class="span3"><label>&nbsp;</label></div>

    </div>

<?php } ?>



<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {
    if (empty($branch) || empty($class) || empty($section) || empty($session) || empty($examName)) {
        echo $tool->Message("alert", $tool->transnoecho("all_fields_required"));
        exit;
    }

    $stuData = $exm->getNumbersForTransfer($param);
    ?>

    <div class="body">

        <table class="table table-bordered">
            <?php if (!empty($examName)) { ?>
                <tr>
                    <td><?php Tools::trans("exam"); ?></td>
                    <td><?php echo $tool->GetExplodedVar($_GET['exam_name']) ?></td>
                </tr>
            <?php } ?>
            <?php if (!empty($branch)) { ?>
                <tr>
                    <td><?php Tools::trans("branch"); ?></td>
                    <td><?php echo $tool->GetExplodedVar($_GET['branch']) ?></td>
                </tr>
            <?php } ?>
            <?php if (!empty($class)) { ?>
                <tr>
                    <td><?php Tools::trans("class"); ?></td>
                    <td><?php echo $tool->GetExplodedVar($_GET['class']) ?></td>
                </tr>
            <?php } ?>

            <?php if (!empty($section)) { ?>
                <tr>
                    <td><?php Tools::trans("section"); ?></td>
                    <td><?php echo $tool->GetExplodedVar($_GET['section']) ?></td>
                </tr>
            <?php } ?>

            <?php if (!empty($section)) { ?>
                <tr>
                    <td><?php Tools::trans("section"); ?></td>
                    <td><?php echo $tool->GetExplodedVar($_GET['section']) ?></td>
                </tr>
            <?php } ?>

            <?php if (!empty($session)) {
                list($f,$s,$t) = explode("-", $_GET['session']);
                ?>
                <tr>
                    <td><?php Tools::trans("session"); ?></td>
                    <td><?php echo $s ?>-<?php echo $t ?></td>
                </tr>
            <?php } ?>
        </table>





        <form method="post" action="">
            <?php echo $tpl->formHidden() ?>
            <input type="hidden" name="oldbranch" value="<?php echo $branch; ?>">
            <input type="hidden" name="oldclass" value="<?php echo $class; ?>">
            <input type="hidden" name="oldsection" value="<?php echo $section; ?>">
            <input type="hidden" name="oldsession" value="<?php echo $session; ?>">
            <input type="hidden" name="oldexam" value="<?php echo $examName; ?>">


            <div>


                <div class="row-fluid">
                    <div class="span3"><label
                                class="fonts"><?php $tool->trans("target_session") ?></label><?php echo $tpl->getAllSession() ?>
                    </div>
                    <div class="span3"><label
                                class="fonts"><?php $tool->trans("target_branch") ?></label><?php echo $tpl->getAllBranch() ?>
                    </div>
                    <div class="span3"><label
                                class="fonts"><?php $tool->trans("target_class") ?></label><?php echo $tpl->getClasses() ?>
                    </div>
                    <div class="span3"><label
                                class="fonts"><?php $tool->trans("target_section") ?></label><?php echo $tpl->getSecsions() ?>
                    </div>
                </div>

                <div class="row-fluid">

                    <div class="span3"><label
                                class="fonts"><?php $tool->trans("target_exam") ?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?>
                    </div>
                    <div class="span3"><label>&nbsp;</label></div>
                    <div class="span3"><label>&nbsp;</label></div>
                    <div class="span3"><label>&nbsp;</label></div>

                </div>

                <div class="row-fluid">&nbsp;</div>


                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th><input type="checkbox" onclick="checkAll(this)"></th>
                        <th>ID</th>
                        <th class="fonts"><?php $tool->trans("name") ?></th>
                        <th class="fonts"><?php $tool->trans("fname") ?></th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stuData as $row) {


                        ?>

                        <tr>
                            <td class="eng_wri"><input type="checkbox" name="ids[<?php echo $row['student_id'] ?>]" id="ids"
                                                       checked="checked" value="<?php echo $row['student_id']; ?>"/></td>


                            <td class="avatar"><?php echo $row['student_id']; ?></td>
                            <td class="fonts"><?php echo $row['name']; ?></td>
                            <td class="fonts"><?php echo $row['fname']; ?></td>


                        </tr>

                    <?php } ?>
                    </tbody>

                    <tr>
                        <td colspan="7" style="text-align: center"><input type="submit" value="Transfer"
                                                                          class="btn btn-medium"></td>
                    </tr>
                </table>
            </div>

        </form>


    </div>
<?php }


$tpl->footer();
?>


