<?php
$errors = array();
Tools::getModel("ExamModel");
$exm = new ExamModel();

$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);




?>


<div class="social-box">
    <div class="header">
        <div class="tools">
        </div>
    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>


        <form method="get" target="_blank" action="<?php echo FRONT_SITE_URL ?>/exam-report?">





                    <div class="container text-center">

                        <div class="row-fluid">
                            <div class="span12">
                                <a href="javascript:void(0)" class="icon-btn icon-btn-green">
                                    <i class="icon-edit icon-2x"></i>
                                    <div><?php $tool->trans("student_result") ?></div>
                                  </a>

                            </div>
                        </div>




                                <div class="control-group">
                                    <label class="control-label"><span class="fonts"><?php $tool->trans("ID") ?></span></label>
                                    <div class="controls">
                                        <input value="<?php if(isset($_POST['student_id'])) echo $_POST['student_id'] ?>" type="text" name="student" id="student">
                                    </div>
                                </div>





                                <div class="control-group">
                                    <label class="control-label"><span class="fonts"><?php $tool->trans("exam") ?></span></label>
                                    <div class="controls">

                                        <select name="exam" id="exam">
                                            <option value=""></option>
                                            <?php
                                            $resExam = $exm->examNames();
                                            foreach($resExam as $exam) {

                                            ?>
                                            <option value="<?php echo $exam['id']?>"><?php echo $exam['title']?></option>
                                            <?php } ?>
                                        </select>


                                    </div>
                                </div>


                        <div class="control-group">
                            <label class="control-label"><span class="fonts"><?php $tool->trans("session") ?></span></label>
                            <div class="controls">

                                <?php
                                echo $tpl->getAllSession();
                                ?>


                            </div>
                        </div>


                        <div class="control-group">
                            <label class="control-label"><span class="fonts"><?php $tool->trans("template") ?></span></label>
                            <div class="controls">

                                <select name="template" id="template">
                                    <option value="">Please select</option>

                                      <option value="first_monthly">Single Exam</option>
                                      <option value="mid_term">Mid Term</option>
                                      <option value="final_exam">Final Term</option>

                                </select>


                            </div>
                        </div>





                            <div class="row">
                                <input type="submit" name="Submit" class="btn btn-success" value="Show" />
                            </div>
                    </div>

       </form>

    </div>
</div>



<style type="text/css">
    .chosen-container {
      width: 18%!important;
      min-width: 18%;
      max-width: 18%;
    }
</style>
<?php
$tpl->footer();
