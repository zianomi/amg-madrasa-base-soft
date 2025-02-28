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
$valSyl = array();

if(isset($_POST['_chk'])==1) {


    $inc = 0;

    $exam = !empty($_POST['exam']) ? $tool->GetInt($_POST['exam']) : '';
    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    $date = !empty($_POST['date']) ? $_POST['date'] : '';

    $subjectNumbers = $exm->examSubjectsByClassBranch($session,$exam,$branch,$class);


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





    if(!$tool->checkDateFormat($date)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("date_invalid"));
    }

    foreach($_POST['ids'] as $key){

        $stuid = $tool->GetInt($_POST['ids'][$key]);

        /*if(empty($_POST['required'][$stuid])){
            $errors[] = $tool->Message("alert","required_field_required");
        }

        if(empty($_POST['current'][$stuid])){
            $errors[] = $tool->Message("alert","current_field_required");
        }*/

        //$valSyl[] = $tool->setInsertDefaultValues(array("NULL",$branch,$class,$section,$session,$stuid,$exam,$_POST['required'][$stuid],$_POST['current'][$stuid],"$date"));


        foreach($_POST['sub_keys'] as $subkey){
            $inc++;

            //$numbers = $tool->GetInt($_POST['numbers'][$key][$subkey]);

            //$numbers = ($_POST['numbers'][$key][$subkey]);
            //$numbers = filter_var($_POST['numbers'][$key][$subkey], FILTER_VALIDATE_FLOAT);
            $numbers = filter_var($_POST['numbers'][$key][$subkey], FILTER_VALIDATE_FLOAT);

            $subject_ids = $tool->GetInt($_POST['sub_ids'][$key][$subkey]);


            if(empty($stuid)){
                $errors[] = $tool->Message("alert","Student Required");
            }

            if(empty($subject_ids)){
                $errors[] = $tool->Message("alert","Subject Required " . $stuid);
            }

            //if(is_numeric($numbers) && is_numeric($subject_ids)){
                $vals[] = $tool->setInsertDefaultValues(array($stuid,$branch,$class,$section,$session,$exam,$subject_ids,$subjectNumbers[$subject_ids],$numbers,"$date"));

            //}



        }

    }





    $where['branch_id'] = $branch;
    $where['class_id'] = $class;
    $where['section_id'] = $section;
    $where['session_id'] = $session;
    $where['exam_id'] = $exam;



    if(count($errors)==0){

        if(!empty($branch)
            && !empty($class)
            && !empty($section)
            && !empty($session)
            && !empty($exam)
        ){


            $exm->deleteExamData($where);
        }


        $res = $exm->insertClassAllNumbers($vals);
        //$res2 = $exm->insertClassAllSyllabus($valSyl);



        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("exam","insertallclass","","list");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }

    }



}

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$examName = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';

$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);



$qr->searchContentAbove();
?>

<style type="text/css">
    .val input{width: 65% !important;}
</style>

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


                $subjectNumbers = $exm->examSubjectsByClassBranch($session,$exam,$branch,$class);



                $resDateArr = $exm->examDateLogs($param);

                if(empty($resDateArr)){

                    echo $tool->Message("alert",$tool->transnoecho("no_exam_log_inserted"));
                    $tpl->footer();
                    exit;
                }

                if(!empty($resDateArr)){
                    $resDate = $resDateArr[0];
                }


                $date =  $resDate['exam_start_date'];

                $res = $exm->examAllClassStudents($param);
                $resExam = $exm->ExamSubjectNumbers($param);



                foreach ($resExam as $rowExam){
                    $examStudentData[$rowExam['student_id']][$rowExam['subject_id']] = $rowExam['numbers'];
                }




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
                $colspan = $totalSubjects + 2;
                $subloop = array();
                $countThorLoop = 0;



                    ?>

              <form method="post">

                  <input type="hidden" name="date" value="<?php echo $date ?>"/>
                  <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
                  <input type="hidden" name="exam" value="<?php echo $examName ?>"/>
                  <input type="hidden" name="class" value="<?php echo $class ?>"/>
                  <input type="hidden" name="section" value="<?php echo $section ?>">
                  <input type="hidden" name="session" value="<?php echo $session ?>">
                  <input type="hidden" name="datelog_id" value="<?php echo $dateLogId ?>">








                <?php echo $tpl->FormHidden();   ?>

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

                    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid" style="overflow: scroll">
                        <table class="table table-bordered table-striped table-hover flip-scroll">
                            <thead>
                            <tr>

                                <th class="fonts"><?php $tool->trans("id")?></th>
                                <th class="fonts"><?php $tool->trans("name_father_name")?></th>


                                <?php
                                foreach($subjects as $key){
                                    $temp["sub_number"] = $key['numbers'];
                                    $temp["sub_id"] = $key['subject_id'];
                                    $subloop[] = $temp;
                                    ?>
                                <th><?php echo $key['title']?></th>
                                <?php } ?>

                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            /*function random_float ($min,$max) {
                                return ($min+lcg_value()*(abs($max-$min)));
                            }*/

                            //echo '<pre>'; print_r($param); echo '</pre>';

                            foreach($res as $row) { ?>
                                <tr>

                                    <td class="avatar"><?php echo $row['id']; ?></td>
                                    <td class="username"><span class="fonts">
                                    <?php echo $row['name']; ?>  <?php echo $row['fname']; ?></span>
                                    </td>


                                    <?php foreach($subloop as $key => $val){
                                        $countThorLoop++;

                                        $stuObtainnumbers = 0;

                                        if(isset($examStudentData[$row['id']][$val["sub_id"]])){
                                            $stuObtainnumbers = $examStudentData[$row['id']][$val["sub_id"]];
                                        }






                                        ?>
                                    <td class="fonts val">

                                        <?php echo $exm->examNumberInput('numbers['. $row['id'].']['.$val["sub_id"].']',$val['sub_number'],$stuObtainnumbers)?>

                                        <input type="hidden" name="ids[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>">

                                        <input type="hidden" name="sub_keys[<?php echo $val["sub_id"] ?>]" value="<?php echo $val["sub_id"] ?>">
                                        <input type="hidden" name="sub_ids[<?php echo $row['id']; ?>][<?php echo $val["sub_id"] ?>]" value="<?php echo $val["sub_id"] ?>">
                                    </td>
                                    <?php }


                                    ?>


                                </tr>
                            <?php } ?>
                            </tbody>

                            <tr class="txtcenter">
                                <td colspan="<?php echo $colspan?>" class="txtcenter">
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
