<?php
/* @var $tool Tools */
/* @var $tpl Template */
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("AcademicModel");

$qr = new QueryTemplate();
$tpf = new TemplateForm();

$acd = new AcademicModel();
$errors = array();

$timeTablesData = $acd->getTimeTables();
$weekDays = $acd->getWeekDays();
$selectedDays = array();



if(isset($_GET['del'])==1){
    if(isset($_GET['id'])){
        if(is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $acd->removeTimeTableStructure($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if(isset($_POST['_chk'])==1) {
    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";
    $timetable = isset($_POST['timetable']) ? $tool->GetInt($_POST['timetable']) : 0;
    $periodName = isset($_POST['period_name']) ? $tool->GetInt($_POST['period_name']) : 0;
    $startTime = $tpf->makeTime($_POST['start_hour'],$_POST['start_minutes']);
    $endTime = $tpf->makeTime($_POST['end_hour'],$_POST['end_minutes']);
    $weekdays = isset($_POST['days']) ? $_POST['days'] : 0;


    if(empty($weekdays)){
        $errors[] = $tool->transnoecho("please_select_week_day");
    }

    if(empty($timetable)){
        $errors[] = $tool->transnoecho("please_timetable");
    }

    if(empty($periodName)){
        $errors[] = $tool->transnoecho("please_enter_period_name");
    }

    if(empty($startTime) || !$tpf->isTimeValid($startTime)){
        $errors[] = $tool->transnoecho("please_enter_start_time") . $startTime;
    }

    if(empty($endTime) || !$tpf->isTimeValid($endTime)){
        $errors[] = $tool->transnoecho("please_enter_end_time");
    }

    if(isset($_POST['days'])){
        foreach ($_POST['days'] as $key => $val){
            $dayid = $tool->GetExplodedInt($val);
            $selectedDays[$key] = $dayid;
        }
    }


    $vals = array();

    if(count($errors)==0){
        foreach ($_POST['days'] as $key => $val){
            $dayid = $tool->GetExplodedInt($val);
            $vals[] = array($timetable,$dayid,$periodName,$startTime,$endTime);
        }






        if($acd->insertPeriodStructure($vals)){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("period_inserted"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("period_already_exists"));
        }
        if(empty($url)){
            Tools::Redir("academic","timestructure","","");
        }
        else{
            header("Location:" . $url);
        }
        exit;
    }




}

$timetableId = (isset($_GET['id'])) ? ($tool->GetExplodedInt($_GET['id'])) : 0;


if(isset($_GET['timetables']) && !empty($_GET['timetables'])){
    $timetableId = $tool->GetExplodedInt($_GET['timetables']);
}


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();



?>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("timetables") ?></label>

            <select name="timetables" id="timetables">
                <?php

                echo $tpl->GetOptionVals(array("name" => "timetables", "data" => $timeTablesData, "sel" => $timetableId));
                ?>
            </select>
        </div>





        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

    </div>

<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){


    $data = array();
    if(empty($timetableId)){
        echo $tool->Message("alert",$tool->transnoecho("Please select timetable"));
        $tpl->footer();
        exit;
    }


    $res = $acd->getTimeTableStructure(array("timetableId" => $timetableId));

    $days = array();
    $data = array();

    foreach ($res as $row){
        $days[$row['day_id']] = array("id" => $row['day_id'], "title" => $row['day_title']);
        $data[$row['day_id']][] = $row;
    }





    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);



    $periodNames = $acd->getPeriodName();

    ?>

    <div class="body">
        <div id="printReady">






            <form method="post">
                <div class="row-fluid">

                    <div class="span3 well">
                        <div class="row-fluid">
                            <div class="span12">
                                <h4><?php $tool->trans("Add New") ?></h4>
                                <hr />
                            </div>
                        </div>

                        <form method="post">
                            <?php echo $tpl->formHidden(); ?>
                            <input type="hidden" name="url" value="<?php echo $curPageUrl ?>">
                            <input type="hidden" name="timetable" value="<?php echo $timetableId ?>">






                            <div class="row-fluid">
                                <div class="span12">
                                    <label class="fonts"><?php $tool->trans("Period Name") ?></label>
                                    <div class="controls">


                                            <select name="period_name" style="width: 100%">
                                                <?php echo $tpl->GetOptionVals(array("name" => "period_name", "data" => $periodNames, "sel" => "")); ?>



                                            </select>


                                    </div>
                                </div>
                            </div>

                           <div class="row-fluid">
                               <div class="span12">
                                       <label class="fonts"><?php $tool->trans("start_time") ?></label>
                                       <div class="controls">

                                               <div class="row-fluid">
                                                   <div class="span6"><label><?php $tool->trans("hour") ?></label>
                                                       <select name="start_hour" style="width: 100%">
                                                           <?php echo $tpf->hourOptions("start_hour") ?>
                                                       </select>

                                                   </div>
                                                   <div class="span6"><label><?php $tool->trans("minutes") ?></label>
                                                       <select name="start_minutes" style="width: 100%">
                                                           <?php echo $tpf->minuteOptions("start_minutes") ?>



                                                       </select>

                                                   </div>
                                               </div>



                                   </div>
                               </div>
                           </div>


                            <div class="row-fluid">
                                <div class="span12">
                                    <label class="fonts"><?php $tool->trans("end_time") ?></label>
                                    <div class="controls">
                                        <div class="row-fluid">
                                            <div class="span6"><label><?php $tool->trans("hour") ?>
                                                    <select name="end_hour" style="width: 100%">
                                                        <?php echo $tpf->hourOptions("end_hour") ?>

                                                    </select>
                                                </label>
                                            </div>
                                            <div class="span6"><label><?php $tool->trans("minutes") ?>
                                                    <select name="end_minutes" style="width: 100%">
                                                        <?php echo $tpf->minuteOptions("end_minutes") ?>

                                                    </select>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row-fluid">
                                <div class="span12">
                                    <label class="fonts"><?php $tool->trans("Day") ?></label>
                                    <div class="controls">
                                        <div class="input-append">
                                            <!--<select name="weekdays" id="weekdays">-->
                                                <?php

                                                echo $tpl->GetMultiOptions(array("name" => "days[]", "data" => $weekDays, "sel" => $selectedDays));
                                                ?>
                                            <!--</select>-->
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row-fluid">
                                <div class="span12">
                                    <label class="fonts">&nbsp;</label>
                                    <div class="controls">
                                        <div class="input-append">
                                            <input type="submit" class="btn btn-success" value="Save">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="span9">

                        <?php
                        foreach ($days as $day){
                            echo $tool->MessageOnly("info",$day['title']);
                        ?>

                        <table class="table table-bordered" style="margin-top: -17px">
                            <thead>
                            <tr>
                                <th><?php $tool->trans("s_no") ?></th>
                                <th><?php $tool->trans("plan_title") ?></th>
                               <!-- <th><?php /*$tool->trans("day_title") */?></th>-->
                                <th><?php $tool->trans("period_name") ?></th>
                                <th><?php $tool->trans("start_time") ?></th>
                                <th><?php $tool->trans("end_time") ?></th>
                                <th><?php $tool->trans("action") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i=0;

                            if(isset($data[$day['id']]))

                            foreach ($data[$day['id']] as $row){
                                $i++;
                                ?>
                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td><?php echo $row['plan_title'] ?></td>
                                    <!--<td><?php /*echo $row['day_title'] */?></td>-->
                                    <td><?php echo $row['period_name'] ?></td>
                                    <td><?php echo $tpf->formatTime($row['start_time']) ?></td>
                                    <td class="fonts"><?php echo $tpf->formatTime($row['end_time']) ?></td>
                                    <td><a onclick="return confirm('Are you sure you want to delete?');" href="<?php echo Tools::makeLink("academic","timestructure&del=1&id=".$row['id']."&redir=".$curPageUrl,"","") ?>"><i class="icon-remove"></i></a> </td>
                                </tr>
                            <?php } ?>

                            </tbody>
                        </table>

                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--<script src="<?php /*echo Tools::getWebUrl() */?>/js/bootstrap-datetimepicker.js" type="text/javascript"></script>

    <script type="text/javascript">

        $(document).ready(function(){



            /*$(".timepicker").datetimepicker({
                format: 'HH:ii',
                autoclose: true,
                showMeridian: true,
                startView: 1,
                maxView: 1
            });*/

            $('.timepicker').datetimepicker({
                minuteStep: 5,
                showInputs: true,
                use24hours: true,
                format: 'HH:ii:A',
                startView: 1,
                maxView: 1
            });


        })

    </script>-->


    <?php
}
$tpl->footer();

