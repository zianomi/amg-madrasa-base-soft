<?php
Tools::getLib("QueryTemplate");
Tools::getModel("AcademicModel");
$acd = new AcademicModel();



$tpl->setCanExport(false);
$tpl->setCanPrint(true);

$qr = new QueryTemplate();



$errors = array();




$date = isset($_GET['date']) ? $tool->ChangeDateFormat($_GET['date']) : "";
$branch = isset($_GET['branch']) ? $tool->GetInt($_GET['branch']) : "";
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();

?>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label>

           <?php echo $tpl->userBranches($branch) ?>
        </div>

        <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label>

            <input type="text" name="date" class="date">
        </div>

        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

    </div>

<?php
$qr->searchContentBottom();



if(isset($_GET['_chk'])==1){


    if(empty($date) || empty($session)){
        echo $tool->Message("alert","Please select date and session.");
        $tpl->footer();
        exit;
    }

    if(!$tool->checkDateFormat($date)){
        echo $tool->Message("alert","Please enter date in valid format.");
        $tpl->footer();
        exit;
    }








    $branches = $set->userBranches();



   foreach ($branches as $branchRow){

       if(!empty($branch) && ($branch != $branchRow['id']) ){
           continue;
       }

    $param['date'] = $date;
    $param['branch'] = $branchRow['id'];
    $param['session'] = $session;

    $res = $acd->getDetailCount($param);
    $resStu = $acd->branchStudentCount($session,$branchRow['id']);

    $classArr = array();
    $subjectArr = array();
    $teacherArr = array();
    $timeArr = array();
    foreach ($res as $row){
        $classArr[$row['class_id']] = array("id" => $row['class_id'], "title" => $row['class_title']);
        $subjectArr[$row['class_id']][$row['subject_id']] = array("id" => $row['subject_id'], "title" => $row['subject_title']);
        $teacherArr[$row['class_id']][$row['subject_id']] = array("id" => $row['teacher_id'], "title" => $row['name']);
        $timeArr[$row['class_id']][$row['subject_id']] = $row['time'];
    }


    ?>

    <div class="body">
        <div id="printReady">





            <div class="row-fluid">


                <div class="span12">

                    <div class="alert alert-info">
                        <strong>Daily Online Classes Progress & Attendace Report</strong>
                        <br />
                        <?php echo $branchRow['title']?>
                    </div>


                    <table class="table table-bordered">


                        <?php foreach ($classArr as $class){?>
                            <tr class="alert alert-success">
                                <td colspan="5"><?php echo $class['title']?></td>
                            </tr>
                            <tr>
                                <td><strong>Subject</strong></td>
                                <td><strong>Teacher</strong></td>
                                <td><strong>Date</strong></td>
                                <td><strong>Time</strong></td>
                                <td><strong>Students</strong></td>
                            </tr>
                            <?php
                            if(isset($subjectArr[$class['id']])){
                            foreach($subjectArr[$class['id']] as $subject){

                                $teacher = "-";
                                if(isset($teacherArr[$class['id']][$subject['id']])){
                                    $teacher = $teacherArr[$class['id']][$subject['id']]['title'];
                                }else{
                                    $teacher = "-";
                                }

                                if(isset($timeArr[$class['id']][$subject['id']])){
                                    $time = $timeArr[$class['id']][$subject['id']];
                                }else{
                                    $time = "-";
                                }



                            ?>
                                <tr>
                                    <td><?php echo $subject['title']?></td>
                                    <td><?php echo  $teacher?></td>
                                    <td><?php echo $tool->ChangeDateFormat($date) ?></td>
                                    <td><?php echo $time ?></td>
                                    <td><?php echo $resStu ?></td>

                                </tr>
                            <?php } ?>
                            <?php } ?>
                        <?php } ?>


                    </table>


                </div>
            </div>

        </div>
    </div>

    <?php

}
}
$tpl->footer();

