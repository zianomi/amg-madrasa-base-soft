<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("ExamModel");
Tools::getModel("StudentsModel");
$stu = new StudentsModel();
$exm = new ExamModel();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

$errors = array();



if(isset($_POST['_chk'])==1) {

    $branch = (isset($_POST['branch'])) ? $tool->GetInt($_POST['branch']) : '';
    $class = (isset($_POST['class'])) ? $tool->GetInt($_POST['class']) : '';
    $session = (isset($_POST['session'])) ? $tool->GetInt($_POST['session']) : '';
    $section = (isset($_POST['section'])) ? $tool->GetInt($_POST['section']) : '';
    $exam = (isset($_POST['exam'])) ? $tool->GetInt($_POST['exam']) : '';


    if(empty($branch)){
        $errors[] = $tool->Message("alert",Tools::transnoecho("Please select branch"));
    }

    if(empty($class)){
        $errors[] = $tool->Message("alert",Tools::transnoecho("Please select class"));
    }

    if(empty($session)){
        $errors[] = $tool->Message("alert",Tools::transnoecho("Please select session"));
    }

    if(empty($section)){
        $errors[] = $tool->Message("alert",Tools::transnoecho("Please select section"));
    }

    if(empty($exam)){
        $errors[] = $tool->Message("alert",Tools::transnoecho("Please select exam"));
    }



    if(isset($_POST['ids'])){
        foreach ($_POST['ids'] as $key){
            $vals[] = $tool->setInsertDefaultValues(array("NULL",$key,$session,$branch,$class,$section,$exam));
        }
    }


    if(count($errors) == 0){
        $exm->removePublishedResult($session,$branch,$class,$section,$exam);
        if(!empty($vals)){
            $res = $exm->insertPublishedResult($vals);
        }

    }


    $_SESSION['msg'] = $tool->Message("succ","Record updated");
    $tool->Redir("exam","publishresult","","");
    exit;


}


$tpl->renderBeforeContent();


$tool->displayErrorArray($errors);

$qr->searchContentAbove();


?>

    <div class="row">
        <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class")?></label><?php echo $tpl->getClasses() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("section")?></label><?php echo $tpl->getSecsions() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>

        <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>
    </div>


<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {


    if (empty($branch)) {
        echo $tool->Message("alert", $tool->transnoecho("branch_required"));
        exit;
    }

    if (empty($class)) {
        echo $tool->Message("alert", $tool->transnoecho("class_required"));
        exit;
    }

    if (empty($section)) {
        echo $tool->Message("alert", $tool->transnoecho("section_required"));
        exit;
    }

    if (empty($session)) {
        echo $tool->Message("alert", $tool->transnoecho("session_required"));
        exit;
    }


}
?>
<div class="body">
    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {


        $param = array(
        "branch" => $branch
        , "class" => $class
        , "section" => $section
        , "session" => $session
        , "status" => 'current'

        );

        $res = $stu->studentSearch($param);


        if (count($res) == 0) {
            echo $tool->Message("alert", $tool->transnoecho("no_students_found"));
            return;
        }


        $param['exam'] = $exam;

        $results = $exm->getPublishedResult($param);



        $stuPublished = array();

        foreach ($results as $result){
            $stuPublished[$result['student_id']] = true;
        }



        ?>

        <form method="post">

            <input type="hidden" name="exam" value="<?php echo $exam ?>"/>
            <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
            <input type="hidden" name="session" value="<?php echo $session ?>">
            <input type="hidden" name="class" value="<?php echo $class ?>">
            <input type="hidden" name="section" value="<?php echo $section ?>">


            <?php echo $tpl->FormHidden(); ?>

            <h2 class="fonts">

                <?php


                if (isset($_GET['branch'])) {
                    if (!empty($_GET['branch'])) {
                        echo $tool->GetExplodedVar($_GET['branch']);
                    }
                }





                ?>
                <br>
            </h2>

            <div id="editable_wrapper">
                <table class="table table-bordered">
                    <thead>
                    <tr>

                        <th class="fonts">S#</th>
                        <th class="fonts"><input type="checkbox" onclick="checkAll(this)"></th>
                        <th class="fonts"><?php $tool->trans("id") ?></th>
                        <th class="fonts"><?php $tool->trans("gr") ?></th>
                        <th class="fonts"><?php $tool->trans("name_father_name") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i=0;

                    //echo '<pre>'; print_r($res); echo '</pre>';

                    $classes = array();
                    $sessions = array();
                    $students = array();

                    foreach ($res as $row) {
                        $classes[$row['class_id']] = array("id" => $row['class_id'], "title" => $row['class_title']);
                        $sections[$row['class_id']][$row['section_id']] = array("id" => $row['section_id'], "title" => $row['section_title']);
                        $students[$row['class_id']][$row['section_id']][$row['id']] = array("id" => $row['id']
                        ,"name" => $row['name']
                        ,"fname" => $row['fname']
                        ,"grnumber" => $row['grnumber']
                        ,"gender" => $row['gender']
                        );
                    }

                    foreach ($classes as $class) {


                        if(isset($stuPublished[$row['id']])){
                            $checked = ' checked="checked"';
                        }
                        else{
                            $checked = '';
                        }





                        ?>

                            <tr>
                                <td colspan="5" class="alert alert-success"><?php echo $class['title']?></td>
                            </tr>

                            <?php
                        if(isset($sections[$class['id']])){
                            foreach ($sections[$class['id']] as $section) {
                            ?>

                                <tr>
                                    <td colspan="5" class="alert alert-info"><?php echo $section['title']?></td>
                                </tr>


                                <?php
                        if(isset($students[$class['id']][$section['id']])){
                            foreach ($students[$class['id']][$section['id']] as $row) {
                                $i++;
                            ?>
                        <tr>
                            <td class=""><?php echo $i; ?></td>
                            <td class="fonts"><input type="checkbox"<?php echo $checked ?> value="<?php echo $row['id']; ?>" name="ids[<?php echo $row['id']; ?>]"></td>
                            <td class="avatar"><?php echo $row['id']; ?></td>
                            <td class="avatar"><?php echo $row['grnumber']; ?></td>
                            <td class="fonts"><?php echo $row['name']; ?> <?php echo $tpl->getGenderTrans($row['gender']) ?> <?php echo $row['fname']; ?></td>



                        </tr>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    <?php } ?>
                    </tbody>



                    <tr class="txtcenter">
                        <td colspan="5" class="txtcenter">
                            <button type="submit" class="btn txtcenter">Save</button>
                        </td>
                    </tr>
                </table>
            </div>
            <?php }
            echo $tpl->formClose();
            ?>
    </div>
</div>
<?php

$tpl->footer();
