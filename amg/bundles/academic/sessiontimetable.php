<?php
/* @var $tool Tools */
/* @var $tpl Template */
$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$errors = array();


Tools::getModel("AcademicModel");
$acd = new AcademicModel();

$timeTables = $acd->getTimeTables();


if (isset($_POST['_chk']) == 1) {



    $branch = $tool->intVal($_POST['branch']);
    $session = $tool->intVal($_POST['session']);
    $vals = array();

    if (empty($branch)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_insert_branch"));
    }

    if (empty($session)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_insert_session"));
    }


    foreach ($_POST['timetables'] as $class => $sectionsArr) {
        foreach ($sectionsArr as $section => $timeTable){
            //$timeTableVal = $tool->GetExplodedInt($timeTable);
            if(!empty($timeTable)){
                $vals[] = array($branch,$session,$class,$section,$timeTable);
            }
        }
    }

    if(empty($vals)){
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_select_timetables"));
    }




    if (count($errors) == 0) {

        $acd->deleteTimetableClassSections($session,$branch);
        $res = $acd->insertTimetableClassSections($vals);

        if ($res) {
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("record_inserted"));
            $tool->Redir("academic", "sessiontimetable", "", "list");
            exit;
        } else {
            echo $tool->Message("alert", $tool->transnoecho("failed_to_insert"));
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

                            <?php echo $tpl->getAllBranch(array("sel" => $branch)); ?>
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
                    <input type="hidden" name="_chk" value="1"/>
                    <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
                    <input type="hidden" name="session" value="<?php echo $session ?>"/>
                    <?php

                    if (empty($branch) || empty($session)) {
                        $tool->Message("alert", $tool->transnoecho("all_fields_required"));
                        return;
                    }


                    $insertedSessionArr = $acd->getSessionSectionsForTimeTable($session,$branch);
                    $sessionData = array();

                    $sessionClassArr = $set->sessionClasses($session, $branch);


                    $preDefinedData = $acd->getTimetableClassSections($session,$branch);

                    $definedDataArr = array();

                    foreach ($preDefinedData as $row){
                        $definedDataArr[$row['class_id']][$row['section_id']][$row['timetable_id']] = $row['timetable_id'];
                    }


                    foreach ($insertedSessionArr as $insertedSessionArray) {
                        $sessionData[$insertedSessionArray['class_id']][$insertedSessionArray['section_id']] = $insertedSessionArray['section_id'];
                    }


                    ?>


                    <div id="menu-collapse" class="ui-accordion ui-widget ui-helper-reset ui-sortable" role="tablist">


                        <?php
                        foreach($sessionClassArr as $sessionClass){
                            ?>

                            <div class="group">
                                <h3><a href="#" class="fonts"><?php echo $sessionClass['title'] ?></a></h3>

                                <section class="feeds social-box social-bordered social-blue">

                                    <div class="header"><h4><i class="icon-th-list"></i><?php echo $sessionClass['title']; ?></h4></div>

                                    <table class="table table-bordered table-striped table-hover flip-scroll">

                                        <thead>
                                        <tr>
                                            <th class="fonst"><?php $tool->trans("S#") ?></th>
                                            <th class="fonst"><?php $tool->trans("Section") ?></th>
                                            <th class="fonst"><?php $tool->trans("Time Table") ?></th>

                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        foreach($allSessions as $row){
                                            ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td><?php echo $row['title']; ?></td>
                                                <td>

                                                    <select name="timetables[<?php echo $sessionClass['id'] ?>][<?php echo $row['id'] ?>]">

                                                        <option value=""><?php $tool->trans("please_select") ?></option>
                                                        <?php foreach ($timeTables as $timeTable){

                                                            if(isset($definedDataArr[$sessionClass['id']][$row['id']][$timeTable['id']])){
                                                                $sel = ' selected';
                                                            }
                                                            else{
                                                                $sel = '';
                                                            }
                                                            ?>
                                                            <option value="<?php echo $timeTable['id']?>"<?php echo $sel?>><?php echo $timeTable['title']?></option>
                                                        <?php } ?>
                                                        <?php

                                                        //echo $tpl->GetOptionVals(array("name" => "timetables", "data" => $timeTables, "sel" => ""));
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>

                                        <?php } ?>
                                        </tbody>

                                    </table>


                                </section>
                            </div>

                        <?php } ?>




                    </div>

                    <div class="form-actions txtcenter">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>


                    <?php echo $tpl->formClose() ?>
                <?php } ?>
            </div>

        </div>
    </div>


<?php
$tpl->footer();
