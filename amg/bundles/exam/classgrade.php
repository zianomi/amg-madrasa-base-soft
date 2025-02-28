<?php
//$session = $tool->getCurrentSessionId();
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


$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';




if (isset($_POST['_chk']) == 1) {


    $session = (isset($_POST['session'])) ? $tool->GetInt($_POST['session']) : '';
    $branch = (isset($_POST['branch'])) ? $tool->GetInt($_POST['branch']) : '';

    if(empty($branch)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("branch_required"));
    }

    if(empty($session)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("session_required"));
    }



    if(isset($_POST['class_ids'])){

        foreach($_POST['class_ids'] as $key){
            $formula = $_POST['formula'][$key];


            if(!empty($formula)){
                $vals[] = array("(NULL",$branch,$key,$formula);
            }

        }
    }


    if (count($errors) == 0) {
        $exm->deleteGradeFormula($branch);
        $res = $exm->insertClassFormula($vals);
        //$resSub = $exm->examSubjectInsert($valSubs);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("exam", "classgrade", $_POST['code'],$_POST['action']);
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

    <div class="row">
        <div class="span3">
            <label class="fonts"><?php $tool->trans("session") ?></label>

            <?php echo $tpl->getAllSession(array("sel" => $session)); ?>
        </div>
        <<div class="span3">
            <label class="fonts"><?php $tool->trans("branch") ?></label>

            <?php echo $tpl->userBranches(array("sel" => $branch)); ?>
        </div>

        <div class="span3"><label>&nbsp;</label>
            <input type="submit" class="btn">
        </div>

    </div>





<?php
$qr->searchContentBottom();
?>



    <div class="body">

        <?php
        if (isset($_GET['_chk']) == 1) {

        $res = $set->sessionClasses($session, $branch);

        if(count($res)==0){
            echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
            $tpl->footer();
            return;
        }

        $formulas = $exm->getGradeFormulas();


        $insertedFormulas = $exm->getClassFormulas($branch);

        $insertArr = array();

        foreach ($insertedFormulas as $row){
            $insertArr[$row['class_id']] = $row;
        }


        //echo '<pre>'; print_r($insertArr); echo '</pre>';

        ?>

        <form method="post" class="formular">
            <div id="printReady">
                <input type="hidden" name="branch" value="<?php echo $branch ?>">
                <input type="hidden" name="session" value="<?php echo $session ?>">


                <?php echo $tpl->FormHidden(); ?>

                <h2 class="fonts"><?php if(isset($_GET['branch'])) echo $tool->GetExplodedVar($_GET['branch'])  ?></h2>

                <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                    <table class="table table-bordered table-striped table-hover flip-scroll">
                        <thead>
                        <tr>

                            <th><input type="checkbox" onclick="checkAll(this)"></th>
                            <th class="fonts"><?php $tool->trans("classes")?></th>
                            <th class="fonts"><?php $tool->trans("formula")?></th>


                        </tr>
                        </thead>
                        <tbody>
                        <?php




                        foreach($res as $row) {

                            if(isset($insertArr[$row['id']])){
                                $checked = ' checked';
                                $sel = $insertArr[$row['id']]['formula_id'];
                            }
                            else{
                                $checked = '';
                                $sel = '';
                            }
                            ?>
                            <tr>

                                <td class="avatar"><input type="checkbox"<?php echo $checked?> name="class_ids[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>"></td>
                                <td class="fonts"><?php echo $row['title']; ?></td>
                                <td class="fonts">

                                    <select name="formula[<?php echo $row['id']; ?>]">
                                        <?php echo $tpl->GetOptionVals(array("data" => $formulas, "sel" => $sel))?>
                                    </select>
                                </td>

                            </tr>
                        <?php } ?>
                        </tbody>


                        <tr class="txtcenter">
                            <td colspan="3" class="txtcenter" style="text-align: center">
                                <button type="submit" class="btn txtcenter">Save</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php }
            $tpl->formClose();
            ?>

    </div>
<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
