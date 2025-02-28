<?php

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$errors = array();


if(isset($_POST['_chk'])==1){
    $branch = $tool->intVal($_POST['branch']);
    $session = $tool->intVal($_POST['session']);
    $set = new SettingModel();
    if(empty($branch)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_branch"));
    }

    if(empty($session)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_session"));
    }

    $deletedIds = array();

    foreach($_POST['all_class_id'] as $keys) {
         if(!in_array($keys, $_POST['class_id'])){
             $deletedIds[] = $keys;
         }
    }


    foreach($_POST['class_id'] as $key){
        $vals[] = $tool->setInsertDefaultValues(array("NULL","$session","$key","$branch"));
    }

    if(count($errors) == 0){


        $set->removeSessionClasses($branch,$session,$deletedIds);
        $res = $set->insertSessionClasses($vals);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("settings","sessionclasses","19","list");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }
    }
}
$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);
?>
<div class="social-box">
        <div class="header">
            <div class="tools">

                <button class="btn btn-success" data-toggle="collapse" data-target="#advanced-search">
                    <i class="icon-filter"></i><?php $tool->trans("search") ?></button>
            </div>
        </div>
        <div class="body">
            <div id="jamia_msg">&nbsp;</div>
            <div id="advanced-search" class="collapse">

                <?php
                echo $tpl->formTag();
                echo $tpl->formHidden();
                ?>
                <input type="hidden" name="_chk" value="1"/>
                <div class="container text-center">


                    <div class="row">

                        <div class="span3">
                            <label class="fonts"><?php $tool->trans("session") ?></label>

                            <?php echo $tpl->getAllSession(array("sel" => $session)); ?>
                        </div>
                        <div class="span3">
                            <label class="fonts"><?php $tool->trans("branch") ?></label>

                            <?php echo $tpl->userBranches(array("sel" => $branch)); ?>
                        </div>




                        <div class="span3">
                            <label class="fonts">&nbsp;</label>
                            <button type="submit" class="btn btn-small"><?php $tool->trans("search") ?></button>
                        </div>
                    </div>
                </div>
                <?php echo $tpl->formClose() ?>


            </div>


            <?php
            echo $tpl->formTag("post");
            echo $tpl->formHidden();
            ?>
            <input type="hidden" name="_chk" value="1"/>
            <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
            <input type="hidden" name="session" value="<?php echo $session ?>"/>
            <div id="printReady">
                <?php
                if (isset($_GET['_chk']) == 1) {

                    if(empty($branch) || empty($session)){
                        $tool->Message("alert",$tool->transnoecho("all_fields_required"));
                        return;
                    }


                    $classArr = $set->allClasses();
                    $sessionClassArr = $set->sessionClasses($session,$branch);

                    foreach($sessionClassArr as $sessionClassArrKey){
                        $sessionClassArray[$sessionClassArrKey['id']] = $sessionClassArrKey['id'];
                    }


                    ?>
                    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                        <table class="table table-bordered table-striped table-hover flip-scroll">
                            <thead>
                            <tr>
                                <th class="hidden-print"><input type="checkbox" onclick="checkAll(this)"></th>
                                <th class="fonts"><?php $tool->trans("class") ?></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php

                            foreach($classArr as $row){
                                if(isset($sessionClassArray[$row['id']])){
                                    $checked= ' checked="checked"';
                                }
                                else{
                                    $checked = "";
                                }
                            ?>
                                <input type="hidden" name="all_class_id[]" value="<?php echo $row['id']; ?>"/>
                                <tr>
                                    <td class="avatar">
                                        <input type="checkbox"<?php echo $checked ?> name="class_id[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>">
                                    </td>
                                    <td class="fonts"><?php echo $row['title']; ?></td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <td colspan="2"><input type="submit" value="<?php $tool->trans("save") ?>" class="btn btn-primary fonts"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
            <?php echo $tpl->formClose() ?>
        </div>
</div>
<?php
$tpl->footer();
