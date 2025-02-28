<?php

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$errors = array();


if(isset($_POST['_chk'])==1){

    $branch = $tool->intVal($_POST['branch']);
    $session = $tool->intVal($_POST['session']);
    $vals = array();

    if(empty($branch)){
       $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_branch"));
   }

   if(empty($session)){
       $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_session"));
   }

    foreach($_POST['section'] as $section){


        foreach($_POST['section_id'] as $classSection){

            if(isset($classSection[$section])){
                $sectionClass = $classSection[$section];
                $vals[] = $tool->setInsertDefaultValues(array("NULL","$session","$section","$sectionClass","$branch"));

            }
        }


    }

    $set = new SettingModel();


    if(count($errors) == 0){

        $set->removeSessionSections($session,$branch);
        $res = $set->insertSessionSections($vals);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("settings","sessionsections","20","list");
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
    <style type="text/css">
        th{
            text-align: center !important;
        }
        td{
            text-align: center !important;
        }
    </style>


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

            <div id="printReady">
                <?php
                if (isset($_GET['_chk']) == 1) {

                    echo $tpl->formTag("post");
                    echo $tpl->formHidden();
                    ?>
                    <input type="hidden" name="_chk" value="1"/>
                    <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
                    <input type="hidden" name="session" value="<?php echo $session ?>"/>
                   <?php

                    if(empty($branch) || empty($session)){
                        $tool->Message("alert",$tool->transnoecho("all_fields_required"));
                        return;
                    }


                    $insertedSessionArr = $set->getSessionSections($session,$branch);
                    $sessionData = array();

                    $sessionClassArr = $set->sessionClasses($session,$branch);

                    $allSessions = $set->allSections();


                    foreach($insertedSessionArr as $insertedSessionArray){
                        $sessionData[$insertedSessionArray['class_id']][$insertedSessionArray['section_id']] = $insertedSessionArray['section_id'];
                    }



                    ?>









                <div class="datacontainer">
                        <table id="tableHeader" class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <?php foreach($sessionClassArr as $sessionClass){ ?>
                                    <th>
                                        <input type="checkbox" class="col_check" data-col="<?php echo $sessionClass['id']; ?>" />
                                        <br />
                                        <?php echo $sessionClass['title']; ?>

                                    </th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>

                            <?php

                            foreach($allSessions as $row){
                                $checked ="";
                            ?>
                                    <input type="hidden" name="section[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>"/>
                                <tr>
                                    <td><input type="checkbox" class="row_check" data-row="<?php echo $row['id']; ?>" /></td>
                                    <td class="fonts"><?php echo $row['title']; ?></td>
                                <?php
                                foreach($sessionClassArr as $sessionClass){

                                    if(isset($sessionData[$sessionClass['id']][$row['id']])){
                                        $checked= ' checked="checked"';
                                    }
                                    else{
                                        $checked = "";
                                    }
                                ?>
                                    <td class="avatar">
                                       <input data-col="<?php echo $sessionClass['id'] ?>" data-row="<?php echo $row['id']; ?>" type="checkbox"<?php echo $checked ?> name="section_id[<?php echo $sessionClass['id'] ?>][<?php echo $row['id']; ?>]" value="<?php echo $sessionClass['id'] ?>">
                                   </td>
                               <?php } ?>
                                </tr>
                            <?php } ?>


                            </tbody>
                        </table>
                </div>

                <div class="row">
                    <div class="span12 text-center"><input type="submit" value="<?php $tool->trans("save") ?>" class="btn btn-primary fonts"></div>
                </div>
                    <?php echo $tpl->formClose() ?>
                <?php } ?>
            </div>

        </div>
</div>

<script>
    $('.col_check').on('click', function(){
        var colnum = $(this).attr('data-col');
        $('input[data-col='+colnum+']').prop('checked', this.checked);
    })

    $('.row_check').on('click', function(){
        var rownum = $(this).attr('data-row');
        $('input[data-row='+rownum+']').prop('checked', this.checked);
    })
</script>
<?php
$tpl->footer();
