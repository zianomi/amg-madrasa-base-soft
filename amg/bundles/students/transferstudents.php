<?php
Tools::getLib("QueryTemplate");
Tools::getModel("FeeModel");
$qr = new QueryTemplate();
$fee = new FeeModel();
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';

Tools::getModel("StudentsModel");
$stu = new StudentsModel();
$ids = array();
$errors = array();
$vals = array();
$defaulterIds = array();

if(isset($_POST['_chk'])==1){


    $newBranch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $newClass = (isset($_POST['class'])) ? $tool->GetExplodedInt($_POST['class']) : '';
    $newSection = (isset($_POST['section'])) ? $tool->GetExplodedInt($_POST['section']) : '';
    $newSession = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';


    $oldbranch = (isset($_POST['oldbranch'])) ? $tool->GetInt($_POST['oldbranch']) : '';
    $oldclass = (isset($_POST['oldclass'])) ? $tool->GetInt($_POST['oldclass']) : '';
    $oldsection = (isset($_POST['oldsection'])) ? $tool->GetInt($_POST['oldsection']) : '';
    $oldsession = (isset($_POST['oldsession'])) ? $tool->GetInt($_POST['oldsession']) : '';



    if(!empty($_POST['ids'])){
        foreach($_POST['ids'] as $key => $val){

            $ids[] = $val;

            /*if(!empty($val)){
                $pendingExists = $fee->seePendingInvoince($val);
                if(!$pendingExists){
                    $ids[] = $val;
                }
            }*/


            //$vals[] = array("NULL",$val,date("Y-m-d"),$oldbranch,$oldclass,$oldsection,$oldsession,$newBranch,$newClass,$newSection,$newSession);
        }
    }

    if(empty($newBranch) || empty($newClass) || empty($newSection) || empty($newSession)){
        $errors[] = $tool->transnoecho("all_fields_required");
    }

    if(empty($ids)){
        $errors[] = $tool->transnoecho("please_select_atleast_one_student");
    }

    $classType = "";

    if(!empty($newClass)){
        $classType = $set->getClassType($newClass);
    }




    if( ($classType != $stu->stuStatus("current"))){
        $errors[] = $tool->Message("alert",$tool->transnoecho("only_current_classes_allowed_here"));
    }


    if(count($errors)==0){
        $studentIds = implode(",",$ids);

        $stu->transferStudents($newBranch,$newClass,$newSection,$newSession,$studentIds);
        $_SESSION['msg'] = $tool->Message("succ", count($ids) . " " . $tool->transnoecho("student_transferd"));
        $tool->Redir("students","transferstudents","","list");
        exit;
    }
}


if(isset($_GET['_chk'])==1){

    $param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session);


}

$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);

$qr->searchContentAbove();


if(!isset($_GET['_chk'])==1){
?>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span2"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses() ?></div>
        <div class="span2"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>
        <div class="span2"><label>&nbsp;</label><input type="submit" class="btn"></div>
    </div>

<?php } ?>



<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){
    if(empty($branch) || empty($class) || empty($section) || empty($session)){
        echo $tool->Message("alert",$tool->transnoecho("all_fields_required"));
        exit;
    }

$stuData = $stu->StudentdSearchWithProfile($param);
?>

    <form method="post" action="">
    <?php echo $tpl->formHidden() ?>
    <input type="hidden" name="oldbranch" value="<?php echo $branch; ?>">
    <input type="hidden" name="oldclass" value="<?php echo $class; ?>">
    <input type="hidden" name="oldsection" value="<?php echo $section; ?>">
    <input type="hidden" name="oldsession" value="<?php echo $session; ?>">


<div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">

    <div class="alert alert-info"><?php $tool->trans("terminated_and_completed_not_accapted_here") ?></div>
    <div class="row-fluid">&nbsp;</div>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("target_session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("target_branch") ?></label><?php echo $tpl->getAllBranch() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("target_class") ?></label><?php echo $tpl->getClasses() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("target_section") ?></label><?php echo $tpl->getSecsions() ?></div>
    </div>

    <div class="row-fluid">&nbsp;</div>



    <table class="table table-bordered table-striped table-hover flip-scroll">
        <thead>
        <tr>
            <th><input type="checkbox" onclick="checkAll(this)"></th>
            <th>ID</th>
            <th class="fonts"><?php $tool->trans("name_fname") ?></th>
            <th class="fonts"><?php $tool->trans("branch") ?></th>
            <th class="fonts"><?php $tool->trans("class") ?></th>
            <th class="fonts"><?php $tool->trans("section") ?></th>
            <th class="fonts"><?php $tool->trans("session") ?></th>


        </tr>
        </thead>
        <tbody>
        <?php foreach ($stuData as $row) {


            //$pendingExists = $fee->seePendingInvoince($row['id']);
            ?>

            <tr>
                <td class="eng_wri"><input type="checkbox" name="ids[<?php echo $row['id'] ?>]" id="ids" checked="checked" value="<?php echo $row['id']; ?>"/></td>

                <?php
/*                if($pendingExists){ */?><!--
                    <td>NO</td>
                <?php /*} else { */?>
                    <td class="eng_wri"><input type="checkbox" name="ids[<?php /*echo $row['id'] */?>]" id="ids" checked="checked" value="<?php /*echo $row['id']; */?>"/></td>
                --><?php /*} */?>


                <td class="avatar"><?php echo $row['id']; ?></td>
                <td class="fonts"><?php echo $row['name']; ?> <?php echo $tpl->getGenderTrans($row['gender'])?> <?php echo $row['fname']; ?></td>
                <td class="fonts"><?php echo $row['branch_title']; ?></td>
                <td class="fonts"><?php echo $row['class_title']; ?></td>
                <td class="fonts"><?php echo $row['section_title']; ?></td>
                <td class="fonts"><?php echo $row['session_title']; ?></td>

            </tr>

        <?php } ?>
        </tbody>

        <tr>
            <td colspan="7" style="text-align: center"><input type="submit" value="Transfer" class="btn btn-medium"></td>
        </tr>
    </table>
</div>

    </form>
<?php }



$tpl->footer();
?>


