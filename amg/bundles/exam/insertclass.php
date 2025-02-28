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

    if(!$tool->checkDateFormat($datePost)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("date_invalid"));
    }

    $subjectNumber = $exm->subjectExamNumberBySubjectId($subject_post,$exam);

    if(empty($subjectNumber)){
        $errors[] = $tool->Message("alert","subject_number_not_inserted_in_setup");
    }

    foreach($_POST['numbers'] as $key => $val){
        $val = filter_var($val, FILTER_VALIDATE_FLOAT);
        $vals[] = $tool->setInsertDefaultValues(array("NULL",$key,$branch,$class,$section,$session,$exam,$subject_post,$subjectNumber,$val,"$datePost"));
    }



    $where['branch_id'] = $branch;
    $where['class_id'] = $class;
    $where['section_id'] = $section;
    $where['session_id'] = $session;
    $where['exam_id'] = $exam;
    $where['subject_id'] = $subject_post;

    if(count($errors)==0){
        if(!empty($branch)
        && !empty($class)
        && !empty($section)
        && !empty($session)
        && !empty($exam)
        && !empty($subject_post)
        ){
            $exm->deleteExamData($where);
        }

        $res = $exm->insertClassAllNumbers($vals);


        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("exam","insertclass","","list");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }

    }



}



$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();
?>

<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("class")?></label><?php echo $tpl->getClasses() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("section")?></label><?php echo $tpl->getSecsions() ?></div>
    <input type="hidden" name="date" id="date">
</div>


<div class="row-fluid">

    <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>

    <div class="span3">
        <label class="fonts"><?php $tool->trans("subject")?></label>
            <select name="subject" id="subject">
                <option value=""></option>
            </select>
    </div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3"><label>&nbsp;</label></div>

    </div>


<?php
$qr->searchContentBottom();
?>
<div class="body">
            <div id="printReady">
                <?php
                if (isset($_GET['_chk']) == 1) {
                if(empty($branch) || empty($class) || empty($session) || empty($date) || empty($exam) || empty($subject)){
                    echo $tool->Message("alert",$tool->transnoecho("please_select_all_fields"));
                    exit;
                }

                $subjectNumber = $exm->subjectExamNumberBySubjectId($subject,$exam);

                //echo '<pre>'; print_r($subjectNumber); echo '</pre>';


                $param = array(
                       "branch" => $branch,
                       "class" => $class,
                       "section" => $section,
                       "session" => $session
                    );

                $res = $exm->examAllClassStudents($param);

                $param['exam'] = $exam;
                $param['subject'] = $subject;
                $resSubs = $exm->ExamSubjectNumbers($param);

                $inserted = array();

                foreach ($resSubs as $resSub){
                    $inserted[$resSub['student_id']] = $resSub['numbers'];
                }

                //echo '<pre>'; print_r($subjectNumber); echo '</pre>';


                if(count($res)==0){
                    echo $tool->Message("alert",$tool->transnoecho("no_students_found"));
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
                  <input type="hidden" name="subject_post" value="<?php echo $subject ?>">









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

                    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                        <table class="table table-bordered table-striped table-hover flip-scroll">
                            <thead>
                            <tr>

                                <th class="fonts"><?php $tool->trans("id")?></th>
                                <th class="fonts"><?php $tool->trans("name_father_name")?></th>
                                <th class="fonts"><?php $tool->trans("number")?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            foreach($res as $row) {

                                $stuNumber[$row['id']] = 0;
                                //echo '<pre>'; print_r($inserted); echo '</pre>';

                                if(isset($inserted[$row['id']])){
                                    $stuNumber[$row['id']] = $inserted[$row['id']];
                                }

                                ?>
                                <tr>

                                    <td class="avatar"><?php echo $row['id']; ?></td>
                                    <td class="fonts"><?php echo $row['name']; ?>  <?php echo $row['fname']; ?></td>
                                    <td class="avatar">


                                        <?php echo $exm->examNumberInput('numbers['. $row['id'].']',$subjectNumber,$stuNumber[$row['id']]) ?>

                                    </td>


                                </tr>
                            <?php } ?>
                            </tbody>

                            <tr class="txtcenter">
                                <td colspan="4" class="txtcenter">
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
    <script>
        var sessionObj = $("#session");
        var branchObj = $("#branch");
        var classObj = $("#class");
        var sectionObj = $("#section");
        var examNameObj = $("#exam_name");

        function callRequest(){
            var session = sessionObj.val();
            var branch = branchObj.val();
            var Class = classObj.val();
            var section = sectionObj.val();
            var exam_name = examNameObj.val();
            var datastring = $("#amg_form").serialize();
            var saveProgress = makeJsLink("ajax","exam&ajax_request=show_subs");

            if(session !== "" && branch !== "" && Class !== "" && section !== "" && exam_name !== ""){


                $.ajax({
                    type: "POST",
                    url: saveProgress,
                    data: datastring,
                    dataType: "json",
                    success: function(data) {
                        if(data.status == 1){
                            $('#subject').html(data.msg);
                            $('#date').val(data.date);

                        }
                        else{
                            alert(data.msg);
                        }
                    },
                    error: function() {
                        alert('error handing here');
                    }
                });
            }

        }

        sessionObj.change(function(){
            callRequest();
        });

        branchObj.change(function(){
            callRequest();
        });

        classObj.change(function(){
            callRequest();
        });

        sectionObj.change(function(){
            callRequest();
        });

        examNameObj.change(function(){
            callRequest();
        });

    </script>
<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
