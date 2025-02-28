<?php
Tools::getModel("AcademicModel");
Tools::getModel("Accounts");
$set = new SettingModel();
$acd = new AcademicModel();
$ac = new Accounts();
$tpl->setCanExport(false);

Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$id = isset($_GET['id']) ? $tool->intVal($_GET['id']) : '';




$vals = array();
$errors = array();
$valSections = array();
if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($id)) {
        $errors[] = $tool->Message("alert", "Please select teacher.");
    }


    if (count($errors) == 0) {

        foreach ($_POST['subject'] as $subject) {
            $vals[] = array($id, $subject);
        }


        $acd->removeTeacherSubjects($id);
        $res = $acd->insertTeacherSubjects($vals);


        $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("sections_updated"));
        $tool->Redir("academic", "subjectteachers&id=" . $id, $_POST['code'], $_POST['action']);
        exit;

    }


}




$tpl->renderBeforeContent();




if (count($errors) > 0) {
    echo $tool->Message("alert", $errors[0]);
}



$qr->searchContentAbove();



?>


<div class="row-fluid">
    <div class="span3"><label>
            <?php $tool->trans("id"); ?>
        </label><input type="number" value="<?php echo $id ?>" name="id"></div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

</div>
<?php

$qr->searchContentBottom();



if (isset($_GET['_chk']) == 1) {

    if (empty($id)) {
        echo $tool->Message("alert", $tool->transnoecho("please_select_teacher"));
        $tpl->footer();
        exit;
    }




    $staffs = $acd->getUserTeachers(array("id" => $id));
    if (empty($staffs)) {
        echo $tool->Message("alert", $tool->transnoecho("no_teacher_found"));
        $tpl->footer();
        exit;
    }

    $staff = $staffs[0];
    $id = $staff['id'];




    ?>
    <div class="body">

        <div class="row-fluid">
            <div class="span12">
                <?php
                $arr[] = $staff['name'];
                //$arr[] = $staff['fname'];
                $arr[] = $staff['id'];
                $arr[] = $tool->transnoecho("add_subjects");
                echo $tpl->arrayBreadCrumbs($arr) ?>
            </div>
        </div>


    



        <div class="container text-center">



            <?php
            echo $tpl->formTag("post");
            echo $tpl->formHidden();
            ?>

            <input type="hidden" name="_chk" value="1">
            <input type="hidden" name="id" value="<?php echo $id; ?>">







            <div class="form-group">

                <input type="submit" name="Submit" class="btn btn-success" value="<?php $tool->trans("update"); ?>" />
            </div>




            <?php echo $tpl->formClose() ?>


        </div>



    </div>


    <?php
}
?>
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
