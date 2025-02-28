<?php
$tpl->setShowJsExport(true);

Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");

$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);



$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$examName = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class")?></label><?php echo $tpl->getClasses() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("section")?></label><?php echo $tpl->getSecsions() ?></div>

    </div>


    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>
        <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>
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
                echo $tool->Message("alert",$tool->transnoecho("all_fields_required"));
                exit;
            }

            $examStudentData = array();
            $syllabusStudentData = array();


            $param = array(
                "branch" => $branch,
                "class" => $class,
                "section" => $section,
                "session" => $session,
                "exam" => $exam
            );


            $resDateArr = $exm->examDateLogs($param);

            if(empty($resDateArr)){

                echo $tool->Message("alert",$tool->transnoecho("no_exam_log_inserted"));
                $tpl->footer();
                exit;
            }


            if(!empty($resDateArr)){
                $resDate = $resDateArr[0];
            }

            if(count($resDate)==0){
                echo $tool->Message("alert",$tool->transnoecho("no_exam_log_inserted"));
                //return;
            }


            $date =  $resDate['exam_start_date'];

            $res = $exm->examAllClassStudents($param);





            if(count($res)==0){
                echo $tool->Message("alert",$tool->transnoecho("no_students_found"));
                return;
            }

            $resDateLogArr = $exm->examDateLogs($param);

            if(count($resDateLogArr)==0){
                echo $tool->Message("alert",$tool->transnoecho("no_subject_found"));
                return;
            }


            $resDateLog = $resDateLogArr[0];
            $dateLogId = $resDateLog['id'];

            $subjects = $exm->examSubjects($dateLogId);
            $totalSubjects = count($subjects);
            $colspan = $totalSubjects + 3;
            $subloop = array();
            $countThorLoop = 0;



            ?>

            <form method="post">




                <h2 class="fonts">

                    <?php


                    if(isset($_GET['branch'])){
                        if(!empty($_GET['branch'])){
                            echo $tool->GetExplodedVar($_GET['branch']);
                        }
                    }



                    ?>
                    <br>
                </h2>

                <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid"  style="overflow: scroll">
                    <table class="table table-bordered table-striped table-hover flip-scroll" id="table">
                        <thead>
                        <tr>

                            <th class="fonts">&nbsp;</th>
                            <th class="fonts">&nbsp;</th>
                            <th class="fonts"><?php $tool->trans("Subjects")?></th>



                            <?php
                            foreach($subjects as $key){
                                $temp["sub_number"] = $key['numbers'];
                                $temp["sub_id"] = $key['subject_id'];
                                $subloop[] = $temp;
                                ?>
                                <th><?php echo $key['title']?></th>
                            <?php } ?>

                        </tr>

                        <tr>
                            <th class="fonts"><?php $tool->trans("gr")?></th>
                            <th class="fonts"><?php $tool->trans("name_father_name")?></th>
                            <th>ID</th>
                            <?php
                            foreach($subjects as $key){
                                ?>
                                <th><?php echo $key['subject_id']?></th>
                            <?php } ?>
                        </tr>


                        </thead>
                        <tbody>
                        <?php

                        foreach($res as $row) { ?>
                            <tr>

                                <td class="avatar"><?php echo $row['grnumber']; ?></td>

                                <td class="username"><span class="fonts">
                                    <?php echo $row['name']; ?>  <?php echo $row['fname']; ?></span>
                                </td>

                                <td class="avatar"><?php echo $row['id']; ?></td>


                                <?php foreach($subloop as $key => $val){
                                    $countThorLoop++;



                                    ?>
                                    <td class="fonts">


                                    </td>
                                <?php }


                                ?>


                            </tr>
                        <?php } ?>
                        </tbody>


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
