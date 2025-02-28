<?php
Tools::getModel("AcademicModel");
Tools::getModel("Accounts");
$set = new SettingModel();
$acd = new AcademicModel();
$ac = new Accounts();
$tpl->setCanExport(false);

Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$errors = array();
$val = array();

if(isset($_POST['_chk'])==1){
    $session = (isset($_POST['session'])) ? $tool->GetInt($_POST['session']) : '';
    $branch = (isset($_POST['branch'])) ? $tool->GetInt($_POST['branch']) : '';
    $originalTeacher = (isset($_POST['original_teacher'])) ? $tool->GetInt($_POST['original_teacher']) : '';
    $date = (isset($_POST['date'])) ? ($_POST['date']) : '';



    if(empty($session)){
        $errors[] = $tool->transnoecho("please_select_session");
    }

    if(empty($branch)){
        $errors[] = $tool->transnoecho("please_select_branch");
    }

    if(empty($date)){
        $errors[] = $tool->transnoecho("please_enter_date");
    }

    foreach ($_POST['structure_id'] as $key){
        $data['id'] = 'NULL';
        $data['branch_id'] = $branch;
        $data['session_id'] = $session;
        $data['class_id'] = $_POST['class'][$key];
        $data['section_id'] = $_POST['section'][$key];
        $data['teacher_id'] = $_POST['teachers'][$key];
        $data['original_teacher_id'] = $originalTeacher;
        $data['period_id'] = $_POST['period_id'][$key];
        $data['start_time'] = $_POST['start_time'][$key];
        $data['end_time'] = $_POST['end_time'][$key];
        $data['date'] = $date;

        echo '<pre>'; print_r($data); echo '</pre>';

        if(!empty($data['branch_id'])
            && !empty($data['session_id'])
            && !empty($data['class_id'])
            && !empty($data['section_id'])
            && !empty($data['teacher_id'])
            && !empty($data['period_id'])
            && !empty($data['start_time'])
            && !empty($data['end_time'])
        ){
            $val[] = $data;



        }


    }

    if(count($errors) == 0 && count($val) > 0){
        $acd->insertBulk(AcademicModel::TIME_TABLE_FIXTURES,$val,false);
        $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("fixtures_updated"));
        $tool->Redir("academic", "teachers","","");
        exit;
    }
    else{
        echo '<pre>'; print_r($errors); echo '</pre>';
    }


}

$id = isset($_GET['id']) ? $tool->intVal($_GET['id']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : "";


if(isset($_GET['_chk'])==1){
    if(empty($id)){
        $tpl->renderBeforeContent();
        echo $tool->Message("alert",$tool->transnoecho("please_select_teacher"));
        $tpl->footer();
        exit;
    }

    $staffs = $acd->getUserTeachers(array("id" => $id));
    if(empty($staffs)){
        echo $tool->Message("alert",$tool->transnoecho("no_teacher_found"));
        $tpl->footer();
        exit;
    }


    $staff = $staffs[0];
    $id = $staff['id'];
    $branch = $staff['branch_id'];

}




$tpl->renderBeforeContent();


$tool->displayErrorArray($errors);


$qr->searchContentAbove();


?>


    <div class="row-fluid">
    <div class="span3"><label><?php $tool->trans("id"); ?></label><input type="number" value="<?php echo $id ?>" name="id"></div>
        <div class="span3">
            <label class="fonts"><?php $tool->trans("session") ?></label>

            <?php echo $tpl->getAllSession(array("sel" => $session)); ?>
        </div>
        <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label>
        <input type="text" class="date" name="date" value="<?php if(isset($_GET['date'])) echo $_GET['date']?>"></div>

        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

    </div>
<?php

$qr->searchContentBottom();



if(isset($_GET['_chk'])==1){

    if(empty($session)){

        echo $tool->Message("alert",$tool->transnoecho("please_select_session"));
        $tpl->footer();
        exit;
    }



?>
        <div class="body">

            <div class="row-fluid">
                <div class="span12">
                    <?php
                    //$arr[] = $staff['gr_number'];
                    $arr[] = $staff['name'];
                    //$arr[] = $staff['fname'];
                    $arr[] = $tool->transnoecho("time_table");
                    echo $tpl->arrayBreadCrumbs($arr) ?>
                </div>
            </div>










                <?php
                $param['teacher'] = $id;
                $param['session'] = $session;
                $teacherTimeTable = $acd->getTeacherTimeTable($param);
                $subjects = $acd->teacherTimeTableSubjects($param);

                $weekDays = array();
                $periods = array();
                $teacherClasses = array();
                $teacherTimes = array();
                foreach ($teacherTimeTable as $row){
                    $weekDays[$row['weekday_id']] = array("id" => $row['weekday_id'], "title" => $row['week_day_title']);
                    $period['id'] = $row['period_name_id'];
                    $period['title'] = $row['period_name_title'];
                    $period[$row['weekday_id']][$row['period_name_id']]['start_time'] = $row['start_time'];
                    $period[$row['weekday_id']][$row['period_name_id']]['end_time'] = $row['end_time'];


                    $periods[$row['period_name_id']] = $period;

                    $cls['class_id'] = $row['class_id'];
                    $cls['section_id'] = $row['section_id'];
                    $cls['class_title'] = $row['class_title'];
                    $cls['section_title'] = $row['section_title'];
                    $cls['timetable_structure_id'] = $row['timetable_structure_id'];

                    $teacherClasses[$row['period_name_id']][$row['weekday_id']][$row['timetable_structure_id']] = $cls;


                    $times['start_time'] = $row['start_time'];
                    $times['end_time'] = $row['end_time'];
                    $times['structure_id'] = $row['timetable_structure_id'];
                    $times['period_name_title'] = $row['period_name_title'];
                    $times['period_name_id'] = $row['period_name_id'];
                    $times['class_id'] = $row['class_id'];
                    $times['section_id'] = $row['section_id'];


                    $teacherTimes[$row['weekday_id']][$row['timetable_structure_id']] = $times;

                }


                $subjectArr = array();

                foreach ($subjects as $r){
                    $subjectArr[$r['timetable_structure_id']][$r['class_id']][$r['section_id']][$r['subject_id']] = $r;
                }



                ?>








                <div class="row-fluid">

                        <div class="span12">


                            <table class="table table-bordered">
                                <tr>
                                    <th></th>
                                    <?php foreach ($weekDays as $weekDay){?>
                                    <th><?php echo $weekDay['title']?></th>
                                    <?php } ?>
                                </tr>
                                <?php foreach ($periods as $period){

                                    ?>
                                <tr>
                                    <td rowspan="2">
                                        <?php //echo $period['start_time']?><br />
                                        <b><?php echo $period['title']?></b><br />
                                        <?php //echo $period['end_time']?>
                                    </td>
                                    <?php foreach ($weekDays as $weekDay){?>
                                        <td style="text-align: center">

                                            <?php

                                            if(isset($teacherClasses[$period['id']][$weekDay['id']])){
                                                foreach ($teacherClasses[$period['id']][$weekDay['id']] as $classRow){
                                                    echo '<label class="fonts label label-success">' . $classRow['class_title'] . " " . $classRow['section_title'] . "</label><br />";

                                                    if(isset($subjectArr[$classRow['timetable_structure_id']][$classRow['class_id']][$classRow['section_id']])){
                                                        foreach ($subjectArr[$classRow['timetable_structure_id']][$classRow['class_id']][$classRow['section_id']] as $row){
                                                            echo '<label class="fonts label label-info">' . $row['subject_title'] . "</label><br />";
                                                        }
                                                    }
                                                }
                                            }
                                            //echo $weekDay['title']?></td>
                                    <?php } ?>
                                </tr>

                                    <tr>
                                        <?php
                                        foreach ($weekDays as $weekDay){
                                        ?>
                                        <td style="text-align: center"><?php
                                            if(isset($period[$weekDay['id']][$period['id']])){
                                                echo '<b>' .$period[$weekDay['id']][$period['id']]['start_time'] . "</b> to <b>" . $period[$weekDay['id']][$period['id']]['end_time'] . "</b>";
                                            }
                                            ?></td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </table>



                            <?php

                            $slots = array();
                            if(!empty($date)){



                                $dayName = date('D', strtotime($date));
                                $day = $acd->getDayNameById($dayName);



                            if(isset($teacherTimes[$day]) && count($teacherTimes[$day]) > 0){


                                $paramTeachers['session'] = $session;
                                $paramTeachers['branch'] = $branch;
                                $paramTeachers['weekday'] = $day;
                                $paramTeachers['date'] = $date;
                                $paramTeachers['original_teacher'] = $id;

                                $enteredFixtures = $acd->getEnteredFixtures($paramTeachers);

                                $fixtureArr = array();



                                //if(count($enteredFixtures)>0){
                                ?>
                                    <table class="table table-bordered">
                                        <?php
                                        foreach ($enteredFixtures as $row){
                                        ?>
                                        <tr>
                                            <td><?php echo $row['period_title']?></td>
                                            <td><?php echo $row['gr_number']?></td>
                                            <td><?php echo $row['name']?></td>
                                            <td><?php echo $row['fname']?></td>
                                            <td><?php echo $row['start_time']?></td>
                                            <td><?php echo $row['end_time']?></td>
                                        </tr>

                                        <?php } ?>
                                    </table>
                                <?php //} ?>


                              <form method="post" action="">
                                  <?php
                                  echo $tpl->formHidden();

                                  ?>
                                <table class="table table-bordered">
                                    <tr>
                                        <th><?php $tool->trans("period_name_title"); ?></th>
                                        <th><?php $tool->trans("start_time"); ?></th>
                                        <th><?php $tool->trans("end_time"); ?></th>
                                        <th><?php $tool->trans("teachers"); ?></th>

                                    </tr>
                                    <?php

                                    foreach ($teacherTimes[$day] as $k => $v){



                                        $paramTeachers['start_time'] = $v['start_time'];
                                        $paramTeachers['end_time'] = $v['end_time'];







                                        $teachers = $acd->availableTeacherForFixture($paramTeachers);





                                        ?>

                                        <input type="hidden" name="start_time[<?php echo $v['structure_id'] ?>]" value="<?php echo $v['start_time'] ?>" />
                                        <input type="hidden" name="end_time[<?php echo $v['structure_id'] ?>]" value="<?php echo $v['end_time'] ?>" />
                                        <input type="hidden" name="structure_id[<?php echo $v['structure_id'] ?>]" value="<?php echo $v['structure_id'] ?>" />
                                        <input type="hidden" name="period_id[<?php echo $v['structure_id'] ?>]" value="<?php echo $v['period_name_id'] ?>" />
                                        <input type="hidden" name="original_teacher" value="<?php echo $id ?>" />
                                        <input type="hidden" name="session" value="<?php echo $session ?>" />
                                        <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                                        <input type="hidden" name="date" value="<?php echo $date ?>" />
                                        <input type="hidden" name="class[<?php echo $v['structure_id'] ?>]" value="<?php echo $v['class_id'] ?>" />
                                        <input type="hidden" name="section[<?php echo $v['structure_id'] ?>]" value="<?php echo $v['section_id'] ?>" />

                                    <tr>

                                        <td><?php echo $v['period_name_title']?></td>
                                        <td><?php echo $v['start_time']?></td>
                                        <td><?php echo $v['end_time']?></td>
                                        <td>
                                            <label>
                                                <select name="teachers[<?php echo $v['structure_id'] ?>]">
                                                    <option value=""><?php $tool->trans("please_select"); ?></option>
                                                    <?php foreach ($teachers as $teacher){?>
                                                        <option value="<?php echo $teacher['id'] ?>"><?php echo $teacher['name'] ?> <?php echo $teacher['fname'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </label>
                                        </td>
                                    </tr>


                                <?php } ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center"><input class="btn btn-primary" type="submit" value="<?php $tool->trans("save"); ?>"></td>
                                    </tr>
                                </table>
                              </form>

                        <?php } ?>
                        <?php } ?>


                        </div>


                    </div>











            </div>





<?php
}
?>
    <style type="text/css">
        .chosen-container{
            width: 19% !important;
        }
        [class*="span"] .chosen-container {
            width: 30%!important;
            min-width: 30%;
            max-width: 30%;
        }
    </style>
<?php
$tpl->footer();
