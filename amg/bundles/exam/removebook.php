<?php
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$errors = array();
$vals = array();


$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$subject = (isset($_GET['subject'])) ? $tool->GetExplodedInt($_GET['subject']) : '';
$date = !empty($_GET['date']) ? ($_GET['date']) : "";

if(isset($_POST['_chk'])==1) {


    $inc = 0;

    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    $exam = !empty($_POST['exam']) ? $tool->GetInt($_POST['exam']) : '';
    $subject_post = !empty($_POST['subject_post']) ? $tool->GetInt($_POST['subject_post']) : '';
    $datePost = !empty($_POST['date_post']) ? $_POST['date_post'] : '';
    if(empty($exam)){
        $errors[] = $tool->Message("alert","exam_required");
    }

    if(empty($branch)){
        $errors[] = $tool->Message("alert","branch_required");
    }

    if(empty($class)){
        $errors[] = $tool->Message("alert","class_required");
    }

    if(empty($section)){
        $errors[] = $tool->Message("alert","section_required");
    }

    if(empty($session)){
        $errors[] = $tool->Message("alert","session_required");
    }


    foreach ($_POST['subject_id'] as $subjectId){
        $where = array( 'branch_id' => $branch, 'class_id' => $class, "section_id" => $section, "session_id" => $session, "exam_id" => $exam, "subject_id" => $subjectId);
        $exm->delete( 'jb_results', $where );
    }




    if(count($errors)==0){


        $_SESSION['msg'] = $tool->Message("succ",Tools::transnoecho("Records Deleted"));
        $tool->Redir("exam","removebook","","list");
        exit;
    }
    else{
        echo $tool->Message("alert","Error");
    }





}



$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();
?>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php Tools::trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php Tools::trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php Tools::trans("class")?></label><?php echo $tpl->getClasses() ?></div>
        <div class="span3"><label class="fonts"><?php Tools::trans("section")?></label><?php echo $tpl->getSecsions() ?></div>
        <input type="hidden" name="date" id="date">
    </div>


    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php Tools::trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>

        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3"><label>&nbsp;</label></div>
        <div class="span3"><label>&nbsp;</label></div>

    </div>


<?php
$qr->searchContentBottom();
?>

<div class="body">
    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {

        if(empty($branch) || empty($class) || empty($session) || empty($exam)){
            echo $tool->Message("alert",Tools::transnoecho("all_fields_required"));
            exit;
        }



        $param = array(
            "branch" => $branch,
            "class" => $class,
            "section" => $section,
            "session" => $session,
            "subject_type" => "exam",
            "exam" => $exam
        );

        $res = $exm->getExamSubjects($param);


        if(count($res)==0){
            echo $tool->Message("alert",Tools::transnoecho("no_subjects_found"));
            return;
        }





        ?>

        <form method="post">

            <input type="hidden" name="date_post" value="<?php echo $date ?>"/>
            <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
            <input type="hidden" name="exam" value="<?php echo $exam ?>"/>
            <input type="hidden" name="class" value="<?php echo $class ?>"/>
            <input type="hidden" name="section" value="<?php echo $section ?>">
            <input type="hidden" name="session" value="<?php echo $session ?>">










            <?php echo $tpl->FormHidden();   ?>

            <h2 class="fonts">
                <?php
                if(isset($_GET['branch'])){
                    if(!empty($_GET['branch'])){
                        echo $tool->GetExplodedVar($_GET['branch']);
                    }
                }
                ?>
            </h2>

            <h2 class="fonts">
                <?php
                if(isset($_GET['class'])){
                    if(!empty($_GET['class'])){
                        echo $tool->GetExplodedVar($_GET['class']);
                    }
                }
                ?>
            </h2>

            <h2 class="fonts">
                <?php
                if(isset($_GET['section'])){
                    if(!empty($_GET['section'])){
                        echo $tool->GetExplodedVar($_GET['section']);
                    }
                }
                ?>
            </h2>

            <h2 class="fonts">
                <?php

                if(isset($_GET['session'])){
                    if(!empty($_GET['session'])){
                        $sessionArr = explode("-",$_GET['session']);
                        echo @$sessionArr[1] . "-" . $sessionArr[2];
                    }
                }
                ?>
            </h2>

            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-striped table-hover flip-scroll">
                    <thead>
                    <tr>

                        <th class="fonts"><?php Tools::trans("id")?></th>
                        <th class="fonts"><?php Tools::trans("subject_name")?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach($res as $row) { ?>
                        <tr>
                            <td class="avatar"><input type="checkbox" name="subject_id[<?php echo $row['subject_id'] ?>]" value="<?php echo $row['subject_id'] ?>"></td>

                            <td class="fonts"><?php echo $row['title']; ?></td>



                        </tr>
                    <?php } ?>
                    </tbody>

                    <tr class="txtcenter">
                        <td colspan="3" class="txtcenter">
                            <button type="submit" class="btn txtcenter">Save</button>
                        </td>
                    </tr>
                </table>
            </div>
            <?php }
            $tpl->formClose();
            ?>
    </div>
</div>
<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
