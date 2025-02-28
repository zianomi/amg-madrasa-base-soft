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


/*if(is_readable(BUNDLES . DRS . "exam" . DRS . "settings.txt")){
    $file_data = file_get_contents(BUNDLES . DRS . "exam" . DRS . "settings.txt");
}

if (!empty($file_data)) {
    $data = unserialize($file_data);
} else {
    $data = array();
}*/

$exam_insert_id = !empty($data['exam_insert_id']['exam']) ? $data['exam_insert_id']['exam'] : '';
$exam_insert_id_hide_show = !empty($data['exam_insert_id']['exam_hide_show']) ? $data['exam_insert_id']['exam_hide_show'] : '';
//$exam_insert_id_year = !empty($data['exam_insert_id']['year']) ? $data['exam_insert_id']['year'] : '';
//$exam_insert_id_year_show = !empty($data['exam_insert_id']['year_show']) ? $data['exam_insert_id']['year_show'] : '';
//$exam_insert_id_month = !empty($data['exam_insert_id']['month']) ? $data['exam_insert_id']['month'] : '';
//$exam_insert_id_month_show = !empty($data['exam_insert_id']['month_show']) ? $data['exam_insert_id']['month_show'] : '';


if(isset($_POST['_chk'])==1) {

    $inc = 0;
    $datains = array();
    //$dataSyll = array();


    //$prgress_id = !empty($_POST['prgress_id']) ? $tool->GetInt($_POST['prgress_id']) : "";
    //$syllabus_id = !empty($_POST['syllabus_id']) ? $tool->GetInt($_POST['syllabus_id']) : "";

    $id = !empty($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : '';
    $exam = !empty($_POST['exam']) ? $tool->GetInt($_POST['exam']) : '';
    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $class = !empty($_POST['class']) ? $tool->GetInt($_POST['class']) : '';
    $section = !empty($_POST['section']) ? $tool->GetInt($_POST['section']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    $date = !empty($_POST['date']) ? $_POST['date'] : '';
    //$required = !empty($_POST['required']) ? $_POST['required'] : '';
    //$current = !empty($_POST['current']) ? $_POST['current'] : '';
    $datelog_id = !empty($_POST['datelog_id']) ? $tool->GetInt($_POST['datelog_id']) : '';


    if(empty($id)){
        $errors[] = $tool->Message("alert","id_required");
    }

    /*if(empty($required)){
        $errors[] = $tool->Message("alert","required_syllabus_required");
    }

    if(empty($current)){
        $errors[] = $tool->Message("alert","current_syllabus_required");
    }*/

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

    if(empty($datelog_id)){
        $errors[] = $tool->Message("alert","datelog_required");
    }

    if(!$tool->checkDateFormat($date)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("date_invalid"));
    }

    $subjectNumbers = $exm->subjectNumbersById($datelog_id);


    if(isset($_POST['subjects'])){
        foreach($_POST['subjects'] as $key){
            $number = $_POST['numbers'][$key];
            $number = filter_var($number, FILTER_VALIDATE_FLOAT);
            //var_dump(filter_var($var, FILTER_VALIDATE_FLOAT));

            if(!empty($number) && !empty($key)){
                $vals[] = $tool->setInsertDefaultValues(array($id,$branch,$class,$section,$session,$exam,$key,$subjectNumbers[$key],$number,$date));
            }

        }
    }



    /*if(!empty($id) && !empty($exam) && !empty($date)){
        $data = array();
        $tempArr = array();
        foreach($_POST['title'] as $key => $val){

            $tempArr['title'] = $val;
            if(!empty($_POST['opt'][$key])){
            $tempArr['res'] = $_POST['opt'][$key];
            }else{
                $tempArr['res'] = '';
            }
            $data[$key] = $tempArr;
        }

        $datains['student_id'] = $id;
        $datains['progress'] = serialize($data);

        $dataSyll['branch_id'] = $branch;
        $dataSyll['class_id'] = $class;
        $dataSyll['section_id'] = $section;
        $dataSyll['session_id'] = $session;
        $dataSyll['student_id'] = $id;
        $dataSyll['exam_id'] = $exam;
        $dataSyll['required'] = $required;
        $dataSyll['current'] = $current;
        $dataSyll['date'] = $date;
    }else{
        $errors[] = $tool->Message("alert",$tool->transnoecho("all_fields_required"));
    }*/



    if(count($errors)==0){
        $res = array();
        if(!empty($vals)){
            $res = $exm->insertClassAllNumbers($vals);
        }


        $msg = "";
        $resId = false;
        /*if(!empty($prgress_id)){

            $resId = $exm->updateProgress($prgress_id,array("progress" => serialize($data)));
        }else{
            $resId = $exm->insertProgress($datains);
        }*/

        /*if(!empty($syllabus_id)){

            $resId = $exm->updateExamSyllabus($syllabus_id,$dataSyll);
        }else{
            $resId = $exm->insertExamSyllabus($dataSyll);
        }*/



        /*if($resId){
            $msg .= "Progress updated";
        }*/



        if(!empty($res)){
            if($res["status"]){
                $msg .= $res['msg'];
            }
        }

        $_SESSION['msg'] = $msg;

        $tool->Redir("exam","insertid","","list");
        exit;


    }

}

$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetInt($_GET['class']) : '';
$session = (isset($_GET['session'])) ? $tool->GetInt($_GET['session']) : '';
$section = (isset($_GET['section'])) ? $tool->GetInt($_GET['section']) : '';
$exam = (isset($_GET['exam_name'])) ? $tool->GetInt($_GET['exam_name']) : '';


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();
?>
    <p id="student_res">&nbsp;</p>

<div class="row-fluid">
    <div class="span3">
        <label class="fonts"><?php $tool->trans("id")?></label>
        <input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>" type="text" name="student_id" id="student_id">
    </div>



    <div class="span3">
        <label class="fonts"><?php $tool->trans("exam_name")?></label>
        <?php echo $tpl->examDropDown($exm->getExamNames(),$exam_insert_id); ?>
    </div>



    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
</div>



<?php
$qr->searchContentBottom();
?>

<div class="body">

            <div id="printReady">
                <?php
                if (isset($_GET['_chk']) == 1) {



                if(empty($id) || empty($branch) || empty($class) || empty($session) || empty($section) || empty($exam)){
                    echo $tool->Message("alert",$tool->transnoecho("all_fields_required"));
                    exit;
                }

                $resDate = array();



                $param = array(
                       "branch" => $branch,
                       "class" => $class,
                       "section" => $section,
                       "session" => $session,
                       "exam" => $exam
                    );

                $resDateArr = $exm->examDateLogs($param);



                if(!empty($resDateArr)){
                    $resDate = $resDateArr[0];
                }

                if(count($resDate)==0){
                    echo $tool->Message("alert",$tool->transnoecho("no_exam_log_inserted"));
                    return;
                }

                $paramStuSubs = array(
                    "stuid" => $id
                    ,"exam" => $exam
                    ,"session" => $session
                    ,"date_id" => $resDate['id']
                );

                $date =  $resDate['exam_start_date'];

                $resSubjects = $exm->ExamIDNumbers($paramStuSubs);

                if(count($resSubjects)==0){
                    echo $tool->Message("alert",$tool->transnoecho("numbers_inserted"));
                    //return;
                }

                    //echo '<pre>'; print_r($resSubjects); echo '</pre>';

                    ?>

              <form method="post" id="exam_numbers">

                  <input type="hidden" name="date" value="<?php echo $date ?>"/>
                  <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
                  <input type="hidden" name="exam" value="<?php echo $exam ?>"/>
                  <input type="hidden" name="class" value="<?php echo $class ?>"/>
                  <input type="hidden" name="section" value="<?php echo $section ?>">
                  <input type="hidden" name="session" value="<?php echo $session ?>">
                  <input type="hidden" name="student_id" value="<?php echo $id ?>">
                  <input type="hidden" name="datelog_id" value="<?php echo $resDate['id'] ?>">


                <?php echo $tpl->FormHidden();   ?>

                    <h2 class="fonts">

                        <br>
                    </h2>

                    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                        <table class="table table-bordered table-striped table-hover flip-scroll">
                            <thead>
                            <tr>
                                <th><input type="checkbox" onclick="checkAll(this)"></th>
                                <th class="fonts"><?php $tool->trans("subject")?></th>
                                <th class="fonts"><?php $tool->trans("numbers")?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            foreach($resSubjects as $row) { ?>
                                <tr>
                                    <td><input type="checkbox" checked="checked" name="subjects[<?php echo $row['id'] ?>]" value="<?php echo $row['id'] ?>"></td>
                                    <td class="fonts"><?php echo $row['title']; ?></td>
                                    <td class="avatar">
                                        <input type="number" placeholder="0.00" required name="numbers[<?php echo $row["id"] ?>]" min="0" onkeyup="CheckValue(this, <?php echo $row['numbers'] ?>)" value="0" step="0.01" title="Numbers" pattern="^\d+(?:\.\d{1,2})?$" onblur="
this.parentNode.parentNode.style.backgroundColor=/^\d+(?:\.\d{1,2})?$/.test(this.value)?'inherit':'red'
">


                                    </td>

                                </tr>
                            <?php } ?>
                            </tbody>

                            <td colspan="3" style="text-align: center">
                                <input type="submit" id="save_progress" name="save_progress" class="btn btn-success" value="Save"/>
                            </td>

                        </table>
                    </div>

                <?php
//jb_syllabus_students


                /*$row_label = $exm->GetExamLabel($class,$exam);
                $rowSyllabusRes = $exm->studentExamSyllabus(array("student" => $id, "exam" => $exam, "session" => $session));
                if(!empty($rowSyllabusRes)){
                    $rowSyllabus = $rowSyllabusRes[0];
                }

                $progress = $exm->IdProgress($id);*/
                ?>


                  <!--<table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php /*echo $row_label['title'] */?></th>
                                <th><?php /*echo $row_label['first'] */?></th>
                                <th><?php /*echo $row_label['second'] */?></th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="text" name="required" value="<?php /*if (!empty($rowSyllabus['required'])) echo $rowSyllabus['required'] */?>"></td>
                                <td><input type="text" name="current" value="<?php /*if (!empty($rowSyllabus['current'])) echo $rowSyllabus['current'] */?>"></td>
                            </tr>
                        </tbody>
                  </table>



                  <input type="hidden" name="syllabus_id" id="syllabus_id" value="<?php /*if (!empty($rowSyllabus['id'])) echo $rowSyllabus['id'] */?>"/>
                  <input type="hidden" name="prgress_id" id="prgress_id" value="<?php /*if (!empty($progress['id'])) echo $progress['id'] */?>"/>


                  <table class="table table-bordered">

                      <tr>
                          <td colspan="4" id="prog_div"></td>
                      </tr>


                      <tr>
                          <th class="fonts">کیفیت</th>
                          <th class="fonts">پہلا</th>
                          <th class="fonts">دوسرا</th>
                          <th class="fonts">تیسرا</th>
                      </tr>

                      <?php
/*

                      if (!empty($progress)) {
                          $progress_arr = unserialize($progress['progress']);

                      } else {
                          $progress_arr = '';
                      }

                      $res = $exm->ResultProgress();
                      foreach ($res as $row) {
                          */?>
                          <tr>
                              <td class="fonts"><?php /*echo $row['title'] . '<input type="hidden" value="' . $row['title'] . '" name="title[' . $row['id'] . ']" />' */?></td>
                              <td class="fonts">
                                  <?php
/*                                  if (!empty($row['result'])) {
                                      echo $row['result']; */?>
                                      <input type="radio" name="opt[<?php /*echo $row['id'] */?>]" value="<?php /*echo $row['result'] */?>"<?php /*if (!empty($progress_arr[$row['id']]['res']) && $progress_arr[$row['id']]['res'] == $row['result']) echo 'checked="checked"' */?> />                    <?php /*} */?>
                              </td>
                              <td class="fonts"><?php
/*                                  if (!empty($row['result2'])) {
                                      echo $row['result2'] */?>
                                      <input type="radio" name="opt[<?php /*echo $row['id'] */?>]" value="<?php /*echo $row['result2'] */?>"<?php /*if (!empty($progress_arr[$row['id']]['res']) && $progress_arr[$row['id']]['res'] == $row['result2']) echo 'checked="checked"' */?>/>

                                  <?php /*} */?></td>
                              <td class="fonts"><?php
/*                                  if (!empty($row['result3'])) {
                                      echo $row['result3'] */?>
                                      <input type="radio" name="opt[<?php /*echo $row['id'] */?>]" value="<?php /*echo $row['result3'] */?>"<?php /*if (!empty($progress_arr[$row['id']]['res']) && $progress_arr[$row['id']]['res'] == $row['result3']) echo 'checked="checked"' */?>/>


                                  <?php /*} */?></td>
                          </tr><?php /*} */?>


                      <tr>
                          <td colspan="4" style="text-align: center">
                              <input type="submit" id="save_progress" name="save_progress" class="btn btn-success" value="Save"/>
                          </td>
                      </tr>
                  </table>-->


              </form>
            <?php } ?>
            </div>

</div>

<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
