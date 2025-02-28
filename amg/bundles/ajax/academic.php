<?php
Tools::getModel("AcademicModel");
Tools::getModel("TimeTableModel");
Tools::getLib("Ncrypt");
$acd = new AcademicModel();
$timeModel = new TimeTableModel();
$enc = new Ncrypt();

$branch = ( isset($_POST['branch']) && !empty($_POST['branch']) ) ? $tool->GetInt($_POST['branch']) : "";
$class = ( isset($_POST['class']) && !empty($_POST['class']) ) ? $tool->GetInt($_POST['class']) : "";
$session = ( isset($_POST['session']) && !empty($_POST['session']) ) ? $tool->GetInt($_POST['session']) : "";
$section = ( isset($_POST['section']) && !empty($_POST['section']) ) ? $tool->GetInt($_POST['section']) : "";
$params = ( isset($_POST['params']) && !empty($_POST['params']) ) ? $enc->decrypt($_POST['params']) : "";

switch($_REQUEST['ajax_request']){


    case "fetch":
        ob_start();

        $branch = (isset($_POST['branch'])) ? $tool->GetInt($_POST['branch']) : '';
        $session = (isset($_POST['session'])) ? $tool->GetInt($_POST['session']) : '';
        $class = (isset($_POST['class'])) ? $tool->GetInt($_POST['class']) : '';
        $section = (isset($_POST['section'])) ? $tool->GetInt($_POST['section']) : '';


        $param['branch'] = $branch;
        $param['session'] = $session;
        $param['class'] = $class;
        $param['section'] = $section;

        $timeTable = $timeModel->getTimeTableTable($param);
        $timetableForSession = $timeModel->getTimetableForSession($param);
        $timetableForSubjects = $timeModel->getTimetableSessionSubjects($param);




        $weekDays = array();
        $periods = array();
        $timetableForSessionArr = array();
        $timetableSubjectSessionArr = array();



        foreach ($timeTable as $row){
            $weekDays[$row['weekday_id']] = array("id" => $row['weekday_id'],"title"=>$row['week_day_title']);


            $tempPeriod['start_time'] = $row['start_time'];
            $tempPeriod['end_time'] = $row['end_time'];
            $tempPeriod['structure_id'] = $row['structure_id'];
            $tempPeriod['id'] = $row['period_name_id'];
            $tempPeriod['title'] = $row['period_name_title'];
            $tempPeriod['timetable_id'] = $row['timetable_id'];

            $periods[$row['weekday_id']][$row['period_name_id']] = $tempPeriod;

        }

        foreach ($timetableForSession as $row) {
            $person['staff_id'] = $row['staff_id'];
            $person['name'] = $row['name'];
            //$person['fname'] = $row['fname'];
            //$person['gr_number'] = $row['gr_number'];
            $person['structure_id'] = $row['timetable_structure_id'];
            $timetableForSessionArr[$row['period_name_id']][$row['weekday_id']][$row['staff_id']] = $person;
        }

        foreach ($timetableForSubjects as $row) {
            $sub['structure_id'] = $row['timetable_structure_id'];
            $sub['id'] = $row['subject_id'];
            $sub['title'] = $row['subject_title'];
            $timetableSubjectSessionArr[$row['period_name_id']][$row['weekday_id']][$row['subject_id']] = $sub;
        }



         foreach ($weekDays as $weekDay){
             echo $tool->MessageOnly("info",$weekDay['title'])
             ?>
        <table class="table table-bordered" style="margin-top: -15px">
            <thead>
            <tr>
                <th>Period Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Teacher</th>
                <th>Subject</th>
                <th>Add</th>
            </tr>
            </thead>
            <tbody>

            <?php
            if(isset($periods[$weekDay['id']])){
                foreach ($periods[$weekDay['id']] as $period){



                    $addText = "&start_time=" . $period['start_time'];
                    $addText .= "&end_time=" . $period['end_time'];
                    $addText .= "&period_name=" . $period['title'];
                    $addText .= "&period_id=" . $period['id'];
                    $addText .= "&day=" . $weekDay['title'];
                    $addText .= "&weekday=" . $weekDay['id'];
                    $addText .= "&structure_id=" . $period['structure_id'];
                    $addText .= "&timetable_id=" . $period['timetable_id'];
                    $addText .= "&branch=" . $branch;
                    $addText .= "&class=" . $class;
                    $addText .= "&session=" . $session;
                    $addText .= "&section=" . $section;
            ?>
                <tr>
                    <td><?php echo $period['title'] ?></td>
                    <td><?php echo $period['start_time'] ?></td>
                    <td><?php echo $period['end_time'] ?></td>
                    <td>
                        <?php

                        if(isset($timetableForSessionArr[$period['id']][$weekDay['id']])){
                            foreach ($timetableForSessionArr[$period['id']][$weekDay['id']] as $person){
                                $teacherText = "&staff_id=";
                                $teacherText .= $person['staff_id'];
                                $teacherText .= "&structure_id=";
                                $teacherText .= $person['structure_id'];
                                $teacherParam = $enc->encrypt($teacherText);
                                //$name = $person['name'] . " : " . $person['fname'];
                                $name = $person['name'];
                                echo '<label class="del_teacher fonts label label-success" data-text="'.$teacherParam.'">&nbsp;' . $name . '&nbsp;</label><br />';
                            }
                        }

                        ?>

                    </td>
                    <td>
                        <?php
                        if(isset($timetableSubjectSessionArr[$period['id']][$weekDay['id']])){
                            foreach ($timetableSubjectSessionArr[$period['id']][$weekDay['id']] as $subject){
                                echo '<label class="del_subject fonts label label-info" data-text="">&nbsp;' . $subject['title'] . '&nbsp;</label><br />';
                            }
                        }
                        ?>
                    </td>
                    <td><a data-text="<?php echo $enc->encrypt($addText) ?>" class="enter fonts label label-warning">Add More</a></td>
                </tr>

            <?php } ?>
            <?php } ?>
            </tbody>
        </table>
        <?php } ?>

        <?php






        $data = ob_get_contents();
        ob_get_clean();
        echo json_encode(array("data" => $data));
        break;





    case "del_teacher":
        $paramArray = array();

        if(empty($params)){
            exit;
        }
        if(empty($branch)){
            exit;
        }

        if(empty($class)){
            exit;
        }

        if(empty($session)){
            exit;
        }

        if(empty($section)){
            exit;
        }

        parse_str($params, $paramArray);

        $teacher = $paramArray['staff_id'];
        $structure = $paramArray['structure_id'];


        $timeModel->deleteTimetableForSession($branch,$class,$section,$session,$teacher,$structure);

        echo json_encode(array());


    break;

    case "save":

        $structureId = ( isset($_POST['structureId']) && !empty($_POST['structureId']) ) ? $tool->GetInt($_POST['structureId']) : "";
        $timetableId = ( isset($_POST['timetable_id']) && !empty($_POST['timetable_id']) ) ? $tool->GetInt($_POST['timetable_id']) : "";
        $weekdayId = ( isset($_POST['weekday_id']) && !empty($_POST['weekday_id']) ) ? $tool->GetInt($_POST['weekday_id']) : "";
        $periodId = ( isset($_POST['period_id']) && !empty($_POST['period_id']) ) ? $tool->GetInt($_POST['period_id']) : "";
        $start_time = ( isset($_POST['start_time']) && !empty($_POST['start_time']) ) ? ($_POST['start_time']) : "";
        $end_time = ( isset($_POST['end_time']) && !empty($_POST['end_time']) ) ? ($_POST['end_time']) : "";

        $val = array();
        $valSubs = array();
        foreach ($_POST['teachers'] as $teacher){
            $val[] = array($branch,$session,$class,$section,$teacher,$structureId,$timetableId,$weekdayId,$periodId,$start_time,$end_time);
            if(isset($_POST['subjects'][$teacher])){
                foreach ($_POST['subjects'][$teacher] as $subject){
                    $valSubs[] = array($branch,$session,$class,$section,$teacher,$subject,$structureId);
                }
            }
        }
        $timeModel->insertTimetableForSession($val,$valSubs);
        echo json_encode(array("data" => "OK"));

    break;

    case "enter":
        $paramArray = array();

        if(empty($params)){
            exit;
        }
        if(empty($branch)){
            exit;
        }

        if(empty($class)){
            exit;
        }

        if(empty($session)){
            exit;
        }

        if(empty($section)){
            exit;
        }

        $duplicate = ( isset($_POST['duplicate']) && !empty($_POST['duplicate']) ) ? $tool->GetInt($_POST['duplicate']) : "";


        parse_str($params, $paramArray);
        $startTime = $paramArray['start_time'];
        $endTime = $paramArray['end_time'];
        $weekDayId = $paramArray['weekday'];
        $periodId = $paramArray['period_id'];
        $timetableId = $paramArray['timetable_id'];
        $structureId = $paramArray['structure_id'];

        $paramTeachers['session'] = $session;
        $paramTeachers['branch'] = $branch;
        $paramTeachers['class'] = $class;
        $paramTeachers['start_time'] = $startTime;
        $paramTeachers['end_time'] = $endTime;
        $paramTeachers['weekday'] = $weekDayId;
        $paramTeachers['allow_duplicate'] = $duplicate;

        $teacherAndSubjects = $acd->getTeacherWithSubjects($paramTeachers);

        //$teacherArr = array();
        $subjectArr = array();

        foreach ($teacherAndSubjects as $row){
            //$tmp['id'] = $row['teacher_id'];
            //$tmp['gr_number'] = $row['gr_number'];
            //$tmp['name'] = $row['name'];
            //$tmp['fname'] = $row['fname'];
            //$teacherArr[$row['teacher_id']] = $tmp;
            $subjectArr[$row['teacher_id']][$row['subject_id']] = array("id" => $row['subject_id'], "title" => $row['subject_title']);
        }





        $teachers = $timeModel->checkAvailableTeachers($paramTeachers);


        ob_start();

        ?>
<form method="post" id="formTimetable">
    <input type="hidden" name="session" value="<?php echo $session?>">
    <input type="hidden" name="branch" value="<?php echo $branch?>">
    <input type="hidden" name="class" value="<?php echo $class?>">
    <input type="hidden" name="section" value="<?php echo $section?>">
    <input type="hidden" name="structureId" value="<?php echo $structureId?>">
    <input type="hidden" name="weekday_id" value="<?php echo $weekDayId?>">
    <input type="hidden" name="timetable_id" value="<?php echo $timetableId?>">
    <input type="hidden" name="period_id" value="<?php echo $periodId?>">
    <input type="hidden" name="start_time" value="<?php echo $startTime?>">
    <input type="hidden" name="end_time" value="<?php echo $endTime?>">
    <input type="hidden" name="ajax_request" value="save">
<div class="modal-body">
        <div class="row-fluid">
            <div class="col-12"><?php echo $paramArray['day']; ?></div>
            <div class="col-6"><?php echo date('h:i A', strtotime($startTime)) ?></div>
            <div class="col-6"><?php echo date('h:i A', strtotime($endTime)) ?></div>
        </div>

        <div class="row-fluid">
            <div class="col-12"><?php
                //echo '<pre>'; print_r($teachers); echo '</pre>';
                ?>

                <table class="table">
                    <?php foreach ($teachers as $teacherRow){?>
                    <tr class="alert alert-info">
                        <td><input type="checkbox" name="teachers[<?php echo $teacherRow['teacher_id'] ?>]" value="<?php echo $teacherRow['teacher_id'] ?>"></td>
                        <td colspan="2"><?php echo $teacherRow['name'] ?> <?php //echo $teacherRow['fname'] ?></td>
                    </tr>
                <?php if(isset($subjectArr[$teacherRow['teacher_id']])){?>
                <?php foreach ($subjectArr[$teacherRow['teacher_id']] as $row){
                    ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input name="subjects[<?php echo $teacherRow['teacher_id'] ?>][<?php echo $row['id'] ?>]" value="<?php echo $row['id'] ?>" type="checkbox"></td>
                        <td><?php echo $row['title'] ?></td>
                    </tr>
                <?php } ?>
                <?php } ?>
                    <?php } ?>
                </table>
            </div>

        </div>

</div>

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
            <button id="save_timetable" type="submit" class="btn btn-primary">Save changes</button>
        </div>
    </form>
        <?php
        $data = ob_get_contents();
        ob_get_clean();
        $arr['data'] = $data;
        $arr['period_name'] = $paramArray['period_name'];

        //
        //echo '<pre>'; print_r($paramArray); echo '</pre>';
        echo json_encode($arr);
    break;


}
