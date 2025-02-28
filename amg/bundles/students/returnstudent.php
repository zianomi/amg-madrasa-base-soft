<?php
$errors = array();
Tools::getModel("StudentsModel");
$stu = new StudentsModel();

if ((isset($_POST["_chk"])) && ($_POST["_chk"] == "1")) {

    $student_id = isset($_POST['id']) ? $tool->GetInt($_POST['id']) : '';

    $typeNum = $tool->GetInt($_POST['returntype']);

    $newBranch = (isset($_POST['branch'])) ? $tool->GetExplodedInt($_POST['branch']) : '';
    $newClass = (isset($_POST['class'])) ? $tool->GetExplodedInt($_POST['class']) : '';
    $newSection = (isset($_POST['section'])) ? $tool->GetExplodedInt($_POST['section']) : '';
    $newSession = (isset($_POST['session'])) ? $tool->GetExplodedInt($_POST['session']) : '';

    if(empty($typeNum)){
        $errors[] = $tool->transnoecho("please_select_type");
    }

    if($typeNum == 1){
        $type = $stu->stuStatus("completed");
    }
    else{
        $type = $stu->stuStatus("terminated");
    }

    /*



    $retDataArr = $stu->checkReturn($type,$student_id);
    if(!empty($retDataArr)){
        $dataId = $retDataArr['id'];
        $dataDate = $retDataArr['date'];

    }
    $todayDate = date("Y-m-d");

    if(empty($dataId) || !is_numeric($dataId)){
        $errors[] = $tool->transnoecho("id_not_exists");
    }*/


    $todayDate = date("Y-m-d");

    if(empty($student_id)){
	    $errors[] = $tool->transnoecho("please_insert_id");
    }

    if(empty($_POST['branch'])){
	    $errors[] = $tool->transnoecho("please_select_branch");
    }

    if(empty($_POST['class'])){
        $errors[] = $tool->transnoecho("please_select_class");
    }

    if(empty($_POST['section'])){
        $errors[] = $tool->transnoecho("please_select_section");
    }

    if(empty($_POST['session'])){
        $errors[] = $tool->transnoecho("please_select_session");
    }



    if(count($errors) == 0){
        $stu->transferStudents($newBranch,$newClass,$newSection,$newSession,$student_id);
        $data['student_id'] = $student_id;
        $data['date'] = $todayDate;
        $data['reason'] = $todayDate;
        $stu->insertReturn($data,$type,0);
        $stu->updateStudentStatus($student_id);

        $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("transfered"));
        $tool->Redir("students","returnstudent",$_POST['code'],$_POST['action']);
        exit;
    }

}


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);

echo $tpl->formTag("post");
echo $tpl->FormHidden();
?>


<div class="social-box">
    <div class="header">
        <div class="tools">
        </div>
    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>


        <div id="printReady">

                    <div class="container text-center">

                        <div class="row-fluid">
                            <div class="span12">
                                <a href="javascript:void(0)" class="icon-btn icon-btn-green">
                                    <i class="icon-edit icon-2x"></i>
                                    <div><?php $tool->trans("complete_student") ?></div>
                                  </a>

                            </div>
                        </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("select_type") ?></span></label>
                                <div class="controls">
                                    <select name="returntype">
                                        <option value="1" <?php if(isset($_POST['returntype']) == 1) echo ' selected="selected"'; ?>><?php $tool->trans("completed") ?></option>
                                        <option value="2" <?php if(isset($_POST['returntype']) == 2) echo ' selected="selected"'; ?>><?php $tool->trans("terminated") ?></option>
                                    </select>
                                </div>
                            </div>



                                <div class="control-group">
                                    <label class="control-label"><span class="fonts"><?php $tool->trans("ID") ?></span></label>
                                    <div class="controls">
                                        <input value="<?php if(isset($_POST['id'])) echo $_POST['id'] ?>" type="text" name="id" id="id">
                                    </div>
                                </div>

                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("session") ?></span></label>
                                <div class="controls">
                                    <?php echo $tpl->getAllSession() ?>
                                </div>
                            </div>




                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("branches") ?></span></label>
                               <div class="controls">
                                   <?php echo $tpl->userBranches() ?>
                               </div>
                           </div>

                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("classes") ?></span></label>
                               <div class="controls">
                                   <?php echo $tpl->getClasses() ?>
                               </div>
                           </div>


                            <div class="control-group">
                               <label class="control-label"><span class="fonts"><?php $tool->trans("section") ?></span></label>
                               <div class="controls">
                                   <?php echo $tpl->getSecsions() ?>
                               </div>
                           </div>




                            <div class="row">
                                <input type="submit" name="Submit" class="btn btn-success" value="Insert" />
                            </div>
                    </div>
                </div>


    </div>
</div>


<?php
echo $tpl->formClose();
?>


<style type="text/css">
    .chosen-container {width: 18%!important;}

 </style>

<?php
$tpl->footer();
