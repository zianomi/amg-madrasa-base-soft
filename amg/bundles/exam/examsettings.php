<?php
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$qr = new QueryTemplate();
$exm = new ExamModel();
$qrf = new TemplateForm();
if(isset($_POST['_chk']) == 1) {
    $post_data = serialize($_POST);
    file_put_contents(BUNDLES . DRS . "exam" . DRS . "settings.txt", $post_data);
    $_SESSION['msg'] = $tool->Message("succ","Settings Saved.");
    $tool->Redir($_POST['menu'],$_POST['page'],$_POST['code'],"");
}



if(is_readable(BUNDLES . DRS . "exam" . DRS . "settings.txt")){
    $file_data = file_get_contents(BUNDLES . DRS . "exam" . DRS . "settings.txt");
}


    if(!empty($file_data)){
        $data = unserialize($file_data);
    }else{
        $data = array();
    }



    //$result_id_date = !empty($data['result_id']['result_id_date']) ? $data['result_id']['result_id_date'] : '';
    //$result_id_date_show = !empty($data['result_id']['result_id_date_show']) ? $data['result_id']['result_id_date_show'] : '';
    //$result_id_to_date = !empty($data['result_id']['result_id_to_date']) ? $data['result_id']['result_id_to_date'] : '';
    //$result_id_to_date_show = !empty($data['result_id']['result_id_to_date_show']) ? $data['result_id']['result_id_to_date_show'] : '';
    $result_id_session = !empty($data['result_id']['session']) ? $data['result_id']['session'] : '';
    $result_id_session_show = !empty($data['result_id']['session_show']) ? $data['result_id']['session_show'] : '';
    $result_id_cur_exam = !empty($data['result_id']['cur_exam']) ? $data['result_id']['cur_exam'] : '';
    $result_id_cur_exam_hide_show = !empty($data['result_id']['cur_exam_hide_show']) ? $data['result_id']['cur_exam_hide_show'] : '';
    $result_id_exams = !empty($data['result_id']['result_id_exams']) ? $data['result_id']['result_id_exams'] : '';
    $result_id_show_hide_template = !empty($data['result_id']['show_hide_template']) ? $data['result_id']['show_hide_template'] : '';
    $result_id_template = !empty($data['result_id']['template']) ? $data['result_id']['template'] : '';
    $result_id_total_exams = !empty($data['result_id']['total_exams']) ? $data['result_id']['total_exams'] : '';
    $exam_insert_id = !empty($data['exam_insert_id']['exam']) ? $data['exam_insert_id']['exam'] : '';
    $exam_insert_id_hide_show = !empty($data['exam_insert_id']['exam_hide_show']) ? $data['exam_insert_id']['exam_hide_show'] : '';

    $exam_insert_id_year = !empty($data['exam_insert_id']['year']) ? $data['exam_insert_id']['year'] : '';
    $exam_insert_id_year_show = !empty($data['exam_insert_id']['year_show']) ? $data['exam_insert_id']['year_show'] : '';


    $exam_insert_id_month = !empty($data['exam_insert_id']['month']) ? $data['exam_insert_id']['month'] : '';
    $exam_insert_id_month_show = !empty($data['exam_insert_id']['month_show']) ? $data['exam_insert_id']['month_show'] : '';


$tpl->renderBeforeContent();
?>

<div class="social-box">
    <div class="header">
        <div class="tools">

        </div>
    </div>
    <div class="body">


        <form action="" method="post" id="formID2">
        <input type="hidden" name="_chk" value="1">
            <?php echo $tpl->formHidden() ?>
        <div id="printReady">





                <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                    <table class="table table-bordered table-striped table-hover flip-scroll">



                        <tbody>

                        <tr>
                            <td colspan="4" class="fonts" style="text-align: center; font-size: 20px;"><?php Tools::trans("Single Student Result Setting") ?></td>
                        </tr>


                        <tr>
                            <td class="fonts"><?php $tool->trans("Session") ?></td>
                            <td>
                                <select name="result_id[session]">
                                    <option value=""></option>
                                    <?php
                                    $sessions = $set->allSessions();
                                    foreach($sessions as $session){

                                        if($result_id_session == $session['id']){
                                            $sel = ' selected="selected"';
                                        }
                                        else{
                                            $sel = '';
                                        }
                                    ?>
                                    <option value="<?php echo $session['id'] ?>"<?php echo $sel?>><?php echo $session['title'] ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="fonts"><?php $tool->trans("show_session") ?></td>
                            <td><?php echo $exm->HideShowDropDown("result_id[session_show]","",$result_id_session_show); ?></td>
                        </tr>





                        <tr>
                            <td class="fonts"><?php Tools::trans("Result End Date") ?></td>
                            <td><input type="text" value="<?php /*echo $result_id_to_date */?>" class="date" name="result_id[result_id_to_date]" id="result_id_to_date"></td>
                            <td class="fonts"><?php Tools::trans("Show End Date?") ?></td>
                            <td><?php /*echo $exm->HideShowDropDown("result_id[result_id_to_date_show]","",$result_id_to_date_show); */?></td>
                        </tr>-->

                        <tr>
                           <td class="fonts"><?php Tools::trans("Current Exam") ?></td>
                           <td><select name="result_id[cur_exam]" id="cur_exam">
                              <option value=""></option>
                              <option value="1" <?php if($result_id_cur_exam == 1) echo ' selected="selected"'; ?>><?php Tools::trans("First Term") ?></option>
                              <option value="2" <?php if($result_id_cur_exam == 2) echo ' selected="selected"'; ?>><?php Tools::trans("Second Term") ?></option>
                              <option value="3" <?php if($result_id_cur_exam == 3) echo ' selected="selected"'; ?>><?php Tools::trans("Third Term") ?></option>
                          </select></td>
                           <td class="fonts"><?php Tools::trans("Show Current Exam?") ?></td>
                           <td><?php echo $exm->HideShowDropDown("result_id[cur_exam_hide_show]","",$result_id_cur_exam_hide_show); ?></td>
                       </tr>

                        <tr>
                            <td class="fonts"><?php Tools::trans("All Exams") ?></td>
                            <td>
                                <span class="fonts"><?php Tools::trans("First Term") ?></span><input type="checkbox" class="result_id_exam" value="1" <?php if( isset($result_id_exams[1]) && $result_id_exams[1] == 1) echo ' checked="checked"'; else echo ''; ?>  name="result_id[result_id_exams][1]" onclick="return testcheck()">
                                <span class="fonts"><?php Tools::trans("Second Term") ?></span><input type="checkbox" class="result_id_exam" value="2" <?php if( isset($result_id_exams[2]) && $result_id_exams[2] == 2) echo ' checked="checked"'; else echo ''; ?> name="result_id[result_id_exams][2]" onclick="return testcheck()">
                                <span class="fonts"><?php Tools::trans("Third Term") ?></span><input type="checkbox" class="result_id_exam" value="3" <?php if( isset($result_id_exams[3]) &&  $result_id_exams[3] == 3) echo ' checked="checked"'; else echo ''; ?> name="result_id[result_id_exams][3]" onclick="return testcheck()">

                            </td>
                            <td class="fonts"><?php Tools::trans("Show all exams") ?></td>
                            <td><?php echo $exm->HideShowDropDown("result_id[total_exams]","",$result_id_total_exams); ?></td>
                        </tr>


                        <!--<tr>
                          <td class="fonts"><?php /*Tools::trans("Template") */?></td>
                          <td><select name="result_id[template]">
                                  <option value="full" <?php /*if($result_id_template == 'full') echo ' selected="selected"'; */?>>Hifz</option>
                                  <option value="roza" <?php /*if($result_id_template == 'roza') echo ' selected="selected"'; */?>>Roza</option>
                          </select> </td>
                          <td class="fonts">ٹیمپلیٹ شو نہیں؟</td>
                          <td><?php /*echo $exm->HideShowDropDown("result_id[show_hide_template]","",$result_id_show_hide_template); */?></td>
                      </tr>-->


                        <tr>
                            <td colspan="4" class="fonts" style="text-align: center; font-size: 20px;">ترتیبات  نمبرات برائے طالب علم</td>
                        </tr>

                        <tr>
                           <td class="fonts">امتحان</td>
                           <td><select name="exam_insert_id[exam]" id="exam">
                                   <option value=""></option>
                                   <option value="1" <?php if($exam_insert_id == 1) echo ' selected="selected"'; ?>>سہ ماہی</option>
                                   <option value="2" <?php if($exam_insert_id == 2) echo ' selected="selected"'; ?>>ششماہی</option>
                                   <option value="3" <?php if($exam_insert_id == 3) echo ' selected="selected"'; ?>>سالانہ</option>
                               </select></td>
                           <td class="fonts">موجودہ امتحان ظاہر ہو؟</td>
                           <td><?php echo $exm->HideShowDropDown("exam_insert_id[exam_hide_show]","",$exam_insert_id_hide_show); ?></td>
                       </tr>




                        <tr>
                            <td colspan="4" class="fonts" style="text-align: center;"><button type="submit" class="btn btn-success">Save Settings</button> </td>
                        </tr>

                        </tbody>

                    </table>
                </div>

        </div>


        </form>
    </div>
</div>
<script>
    function testcheck()
    {
        if (!jQuery(".result_id_exam").is(":checked")) {
            alert("One Exam check required.");
            return false;
        }
        return true;
    }
</script>

<?php
$tpl->footer();
