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
$valSubs = array();





if (isset($_POST['_chk']) == 1) {

    $branch = $tool->GetInt($_POST['branch']);
    $session = $tool->GetInt($_POST['session']);
    $examName = $tool->GetInt($_POST['exam_name']);


    if(empty($branch)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("branch_required"));
    }

    if(empty($session)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("session_required"));
    }

    if(empty($examName)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("exam_name_required"));
    }

    if(isset($_POST['class_ids'])){

        foreach($_POST['class_ids'] as $key){
            //$displayYear = $_POST['display_year'][$key];
            $examStartDate = $tool->ChangeDateFormat($_POST['exam_start_date'][$key]);
            $examEndDate = $tool->ChangeDateFormat($_POST['exam_end_date'][$key]);
            $attandStartDate = $tool->ChangeDateFormat($_POST['attand_start_date'][$key]);
            $attandEndDate = $tool->ChangeDateFormat($_POST['attand_end_date'][$key]);
            $year = $tool->GetInt($_POST['year'][$key]);
            $classTitle = $_POST['class_title'][$key];

            if(isset($_POST['subjects'][$key])){
                foreach($_POST['subjects'][$key] as $subs){
                    $valSubs[] = array("(NULL",$tool->GetExplodedInt($subs),"$key");
                }
            }


            if (empty($examStartDate) || !$tool->checkDateFormat($examStartDate)) {
                $errors[] =  $tool->transnoecho("invalid_exam_start_date." . " " . $classTitle);
            }

            if (empty($examEndDate) || !$tool->checkDateFormat($examEndDate)) {
                $errors[] = $tool->transnoecho("invalid_exam_end_date." . " " . $classTitle);
            }

            if (empty($attandStartDate) || !$tool->checkDateFormat($attandStartDate)) {
                $errors[] =  $tool->transnoecho("invalid_attand_start_date." . " " . $classTitle);
            }

            if (empty($attandEndDate) || !$tool->checkDateFormat($attandEndDate)) {
                $errors[] =  $tool->transnoecho("invalid_attand_end_date." . " " . $classTitle);
            }

            /*if (empty($displayYear)) {
                $errors[] = $tool->transnoecho("please_enter_display_year." . " " . $classTitle);
            }*/

            $vals[] = $tool->setInsertDefaultValues(array($branch,$key,$session,$examName,$examStartDate,$examEndDate,$year,$attandStartDate,$attandEndDate));
        }
    }


    if (count($errors) == 0) {
        $res = $exm->examDateLogInsert($vals);
        //$resSub = $exm->examSubjectInsert($valSubs);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("exam", "examdatelog", $_POST['code'],$_POST['action']);
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }
    }

}




$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$examName = (isset($_GET['exam_name'])) ? $tool->GetExplodedInt($_GET['exam_name']) : '';
$year = (!empty($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$examStartDate = ((isset($_GET['exam_start_date'])) && (!empty($_GET['exam_start_date']))) ? ($_GET['exam_start_date']) : "";
$examEndDate = ((isset($_GET['exam_end_date'])) && (!empty($_GET['exam_end_date']))) ? ($_GET['exam_end_date']) : "";
$attandStartDate = ((isset($_GET['attand_start_date'])) && (!empty($_GET['attand_start_date']))) ? ($_GET['attand_start_date']) : "";
$attandEndDate = ((isset($_GET['attand_end_date'])) && (!empty($_GET['attand_end_date']))) ? ($_GET['attand_end_date']) : "";



$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
$qr->searchContentAbove();
?>

<div class="row">
    <div class="span3"><label class="fonts"><?php $tool->trans("session")?></label><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("branch")?></label><?php echo $tpl->userBranches() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("exam_name")?></label><?php echo $tpl->examDropDown($exm->getExamNames()); ?></div>

    <div class="span3">
        <label class="fonts"><?php $tool->trans("year")?></label>
        <input type="number" name="year" id="year" value="<?php echo $displayYear ?>">
    </div>

    <div class="span3">
        <label class="fonts"><?php $tool->trans("exam_start_date")?></label>
        <?php echo $tpl->getDateInput("exam_start_date"); ?>
    </div>

    <div class="span3"><label class="fonts"><?php $tool->trans("exam_end_date")?></label>
        <?php echo $tpl->getDateInput("exam_end_date"); ?>
    </div>

    <div class="span3"><label class="fonts"><?php $tool->trans("attand_start_date")?></label>
        <?php echo $tpl->getDateInput("attand_start_date"); ?>
    </div>


        <div class="span3"><label class="fonts"><?php $tool->trans("attand_end_date")?></label>
            <?php echo $tpl->getDateInput("attand_end_date"); ?>
        </div>

        <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>



    </div>



<?php
$qr->searchContentBottom();
?>





                <?php
                if (isset($_GET['_chk']) == 1) {

                $res = $exm->getExamClasses($session, $branch, $examName);

                if(count($res)==0){
                    echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
                    return;
                }

                //$subData = $exm->getClassSubjects();




                    ?>

              <form method="post" class="formular">
                    <div id="printReady">
                <input type="hidden" name="branch" value="<?php echo $branch ?>">
                <input type="hidden" name="session" value="<?php echo $session ?>">
                <input type="hidden" name="exam_name" value="<?php echo $examName ?>">
                <input type="hidden" name="year" value="<?php echo $year ?>">


                <?php echo $tpl->FormHidden(); ?>

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

                                <th><input type="checkbox" onclick="checkAll(this)"></th>
                                <th class="fonts"><?php $tool->trans("classes")?></th>
                                <th class="fonts"><?php $tool->trans("year")?></th>
                                <th class="fonts"><?php $tool->trans("exam_start_date")?></th>
                                <th class="fonts"><?php $tool->trans("exam_end_date")?></th>
                                <th class="fonts"><?php $tool->trans("attand_start_date")?></th>
                                <th class="fonts"><?php $tool->trans("attand_end_date")?></th>


                                <!--<th>&nbsp;</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <?php




                            foreach($res as $row) {
                                /*if(isset($subData[$row['id']])){
                                    $classSubData[$row['id']] = $subData[$row['id']];
                                }
                                else{
                                    $classSubData[$row['id']] = array();
                                }*/
                                ?>
                                    <input type="hidden" name="class_title[<?php echo $row['id']; ?>]" value="<?php echo $row['title']; ?>">
                                <tr>

                                    <td class="avatar"><input type="checkbox" name="class_ids[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>"></td>
                                    <td class="fonts"><?php echo $row['title']; ?></td>
                                    <td class="fonts"><input style="width: 50%" type="number" name="year[<?php echo $row['id']; ?>]" value="<?php echo $year ?>" minlength="4" maxlength="4"></td>
                                    <td class="fonts"><input style="width: 50%" type="text" class="start_date" name="exam_start_date[<?php echo $row['id']; ?>]" value="<?php echo $examStartDate ?>"></td>
                                    <td class="fonts"><input style="width: 50%" type="text" class="start_date" name="exam_end_date[<?php echo $row['id']; ?>]" value="<?php echo $examEndDate ?>"></td>
                                    <td class="fonts"><input style="width: 50%" type="text" class="start_date" name="attand_start_date[<?php echo $row['id']; ?>]" value="<?php echo $attandStartDate ?>"></td>
                                    <td class="fonts"><input style="width: 50%" type="text" class="start_date" name="attand_end_date[<?php echo $row['id']; ?>]" value="<?php echo $attandEndDate ?>"></td>
                                    <!--<td>
                                        <?php
/*                                      echo $tpl->GetMultiOptions(array("name" => "subjects[".$row['id']."][]", "data" => $classSubData[$row['id']], "sel" => ""));
                                      */?>
                                    </td>-->
                                </tr>
                            <?php } ?>
                            </tbody>

                            <tr class="txtcenter">
                                <td colspan="4">

                                </td>
                            </tr>

                            <tr class="txtcenter">
                                <td colspan="7" class="txtcenter">
                                    <button type="submit" class="btn txtcenter">Save</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </div>
                <?php }
                echo $tpl->formClose();
                ?>


<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
