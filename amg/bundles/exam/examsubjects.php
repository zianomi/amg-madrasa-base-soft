<?php
$dateLogId = $tool->GetInt($_GET['id']);
$classId = $tool->GetInt($_GET['class_id']);
Tools::getModel("ExamModel");
$exm = new ExamModel();
$vals = array();
$errors = array();

if(isset($_POST['_chk'])==1){
    $dateLogid = $tool->GetInt($_POST['datelogid']);
    $class_id = $tool->GetInt($_POST['class_id']);

    if(empty($dateLogId)){
        $errors[] = $tool->Message("alert","date_log_id_required");
    }
    if(empty($class_id)){
        $errors[] = $tool->Message("alert","class_id_required");
    }

    foreach($_POST['subject_id'] as $key){
        if(!empty($_POST['subject_numbers'][$key])){
            $vals[] = array("NULL",$key,intval($_POST['subject_numbers'][$key]),$dateLogid);
        }

    }


    if(count($errors)==0){
        $res = $exm->examSubjectInsert($dateLogid,$vals);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("exam","examsubjects","","list&id=".$dateLogid."&class_id=".$class_id);
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }
    }




}

$tpl->renderBeforeContent();

if(empty($classId) || !is_numeric($classId)){
    echo $tool->Message("alert",$tool->transnoecho("class_id_required"));
    exit;
}

if(empty($dateLogId) || !is_numeric($dateLogId)){
    echo $tool->Message("alert",$tool->transnoecho("error_id_id"));
    exit;
}

$row = array();

$logData = $exm->examDateLogs(array("id" => $dateLogId));
if(isset($logData[0])){
    $row = $logData[0];
}




?>




    <div class="social-box">
        <div class="header">
            <div class="tools">&nbsp;</div>
        </div>
        <div class="body">


            <div id="printReady">




                    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">

                        <table class="table table-bordered table-striped table-hover flip-scroll">
                            <tr>
                                <td><?php $tool->trans("exam") ?></td>
                                <td><?php echo $row['exam_title'] ?></td>
                                <td><?php $tool->trans("branch") ?></td>
                                <td><?php echo $row['branch_title'] ?></td>
                            </tr>

                            <tr>
                                <td><?php $tool->trans("exam_start_date") ?></td>
                                <td><?php echo $tool->ChangeDateFormat($row['exam_start_date']) ?></td>
                                <td><?php $tool->trans("exam_end_date") ?></td>
                                <td><?php echo $tool->ChangeDateFormat($row['exam_end_date']) ?></td>
                            </tr>

                            <tr>
                                <td><?php $tool->trans("attand_start_date") ?></td>
                                <td><?php echo $tool->ChangeDateFormat($row['attand_start_date']) ?></td>
                                <td><?php $tool->trans("attand_end_date") ?></td>
                                <td><?php echo $tool->ChangeDateFormat($row['attand_end_date']) ?></td>
                            </tr>
                            <tr>
                                <td><?php $tool->trans("year") ?></td>
                                <td><?php echo $row['year'] ?></td>
                                <td><?php //$tool->trans("display_year") ?></td>
                                <td><?php //echo $row['display_year'] ?></td>
                            </tr>
                         </table>

                        <form method="post">
                            <input type="hidden" name="datelogid" value="<?php echo $dateLogId ?>">
                            <input type="hidden" name="class_id" value="<?php echo $classId ?>">
                            <?php echo $tpl->formHidden(); ?>
                        <table class="table table-bordered table-striped table-hover flip-scroll">
                            <thead>
                            <tr>
                                <th><input type="checkbox" onclick="checkAll(this)"></th>
                                <th><?php $tool->trans("subjects") ?></th>
                                <th><?php $tool->trans("numbers") ?></th>

                            </tr>
                            </thead>

                            <tbody>

                            <?php
                            $classSubs = $exm->getExamClassSubjectsByClassId($classId,$row['branch_id']);
                            $examSubs = $exm->examSubjects($dateLogId);

                            $subData = array();
                            $subNumbers = array();

                            foreach($examSubs as $rowSubs){
                                $subData[$rowSubs['subject_id']] = $rowSubs['subject_id'];
                                $subNumbers[$rowSubs['subject_id']] = $rowSubs['numbers'];
                            }

                            foreach($classSubs as $row){

                                if(isset($subData[$row['id']])){
                                    $checked = ' checked="checked"';
                                }
                                else{
                                    $checked = '';
                                }

                                if(isset($subNumbers[$row['id']])){
                                    $subjectNumbers = $subNumbers[$row['id']];
                                }
                                else{
                                    $subjectNumbers = "";
                                }

                            ?>

                                <tr>
                                    <td><input type="checkbox"<?php echo $checked ?> name="subject_id[<?php echo $row['id'] ?>]" value="<?php echo $row['id'] ?>"></td>
                                    <td class="fonts"><?php echo $row['title'] ?></td>
                                    <td class="fonts">
                                        <input type="text" name="subject_numbers[<?php echo $row['id'] ?>]" value="<?php echo $subjectNumbers ?>">
                                    </td>
                                </tr>
                            <?php } ?>

                            </tbody>

                            <tr>
                                <td colspan="3" class="centered"><input type="submit" class="btn btn-success"></td>
                            </tr>

                        </table>
                        </form>

                    </div>
                        </div>
        </div>
    </div>



<?php
$tpl->footer();
