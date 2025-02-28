<?php
$url = $tool->GetCurrentUrl();
$url .= "lang_" . Tools::getLang();

Tools::getLib("FileCache");
$cache = new FileCache();
$outputFile = $cache->get($url);

if(!empty($outputFile)){
    $tpl->renderBeforeContent();
    echo ($outputFile);
    $tpl->footer();
    exit;
}


$tpl->renderBeforeContent();
Tools::getModel("Dashboard");
Tools::getModel("ExamModel");
$dashboard = new Dashboard();
$exm = new ExamModel();

function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}


$branchStudentCounts = $dashboard->countStudents();
$totalStudentCount = $dashboard->countTotalStudents();

$pieData = array();
$pieDataTotal = array();

foreach ($branchStudentCounts as $branchStudentCount){
    $pieDataName[] = $branchStudentCount['title'];
    $pieDataColors[] = "#".random_color();
    $pieDataCount[] = $branchStudentCount['current'];
}


    $pieDataTotal = array(
            $totalStudentCount['current']
,$totalStudentCount['completed']
,$totalStudentCount['terminateds']
    );

    $pieDataTotalLabels = array(
            $tool->transnoecho("current")
            ,$tool->transnoecho("completed")
,$tool->transnoecho("terminated")
    );

$pieDataTotalColors = array("#".random_color(),"#".random_color(),"#".random_color());


$examDatas = $exm->examSummaryData(4,2);

$examBranchName = array();
$examGrades = array();
$branchTotalSummary = array();
$i=0;
foreach ($examDatas as $examData){

    if($i < 6){
        $examBranchName[] = $examData['title'];
        $vals = array($examData['mumtaz_ma_sharf'],$examData['mumtaz'],$examData['jayyad_jiddan']
         ,$examData['jayyad'],$examData['maqbool'],$examData['rasib']
        );

        $examGrades[] = array("label" => $examData['title'], "backgroundColor" => "#".random_color(), "data" => $vals);
    }

    $branchTotalSummary[$examData['branch_id']] = array(
"mumtaz_ma_sharf" => $examData['mumtaz_ma_sharf']
,"mumtaz" => $examData['mumtaz']
,"jayyad_jiddan" => $examData['jayyad_jiddan']
,"jayyad" => $examData['jayyad']
,"maqbool" => $examData['maqbool']
,"rasib" => $examData['rasib']
    );


    $i++;
}

?>
    <h1 align="center" class="icon-btn "><img src="<?php echo $tool->getWebUrl() ?>/img/iqra_logo.png"></h1>
    <div class="social-box">
        <div class="header">&nbsp;</div>
        <div class="body">
            <div class="container text-center">




                <div class="row-fluid">
                    <div class="span6">
                        <div class="social-box social-bordered social-blue">
                            <div class="header">
                                <h4 class="fonts"><?php $tool->trans("student_counts_by_branch") ?></h4>
                            </div>
                            <div class="body">
                                <canvas id="branch_students" height="300">
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="social-box social-bordered social-blue">
                            <div class="header">
                                <h4 class="fonts"><?php $tool->trans("total_students") ?></h4>
                            </div>
                            <div class="body">
                                <canvas id="total_students" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                </div>


                <div class="row-fluid">
                    <div class="span12">
                        <div class="social-box social-bordered social-green">
                            <div class="header">
                                <h4 class="fonts"><?php $tool->trans("result_summary") ?></h4>
                            </div>
                            <div class="body">
                                <canvas id="exam_chart" ></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span6">
                        <div class="social-box social-bordered social-orange">
                            <div class="header">
                                <h4 class="fonts"><?php $tool->trans("present_ratio") ?></h4>
                            </div>
                                <div class="body">
                                    <div class="progress progress-striped active">
                                      <div class="bar" style="width: 88%;"></div>
                                    </div>
                                </div>

                        </div>
                    </div>

                    <div class="span6">
                        <div class="social-box social-bordered social-orange">
                            <div class="header">
                                <h4 class="fonts"><?php $tool->trans("absent_ration") ?></h4>
                            </div>
                                <div class="body">
                                    <div class="progress progress-danger active progress-striped">
                                      <div class="bar" style="width: 12%"></div>
                                    </div>
                                </div>

                        </div>
                    </div>
                </div>



                <div class="row-fluid">

                <div class="span12">
                    <table class="table table-bordered table-striped table-hover flip-scroll">
                        <thead>
                            <tr>
                                <th class="fonts"><?php $tool->trans("branch_title") ?></th>
                                <th class="fonts"><?php $tool->trans("current") ?></th>
                                <th class="fonts"><?php $tool->trans("completed") ?></th>
                                <th class="fonts"><?php $tool->trans("dependent") ?></th>
                                <th class="fonts"><?php $tool->trans("terminated") ?></th>
                                <th class="fonts"><?php $tool->trans("mumtaz_ma_sharf") ?></th>
                                <th class="fonts"><?php $tool->trans("mumtaz") ?></th>
                                <th class="fonts"><?php $tool->trans("jayyad_jiddan") ?></th>
                                <th class="fonts"><?php $tool->trans("jayyad") ?></th>
                                <th class="fonts"><?php $tool->trans("maqbool") ?></th>
                                <th class="fonts"><?php $tool->trans("rasib") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($branchStudentCounts as $branchStudentCount){
                            $branchName[] = $branchStudentCount['title'];
                            $branchCurrent[] = $branchStudentCount['current'];
                            $branchCompleted[] = $branchStudentCount['completed'];
                            $branchDependent[] = $branchStudentCount['dependent'];
                            $branchTerminateds[] = $branchStudentCount['terminateds'];
                        ?>
                            <tr id="<?php echo $branchStudentCount['branch_id'] ?>">
                                <td class="fonts"><?php echo $branchStudentCount['title'] ?></td>
                                <td><?php echo $branchStudentCount['current'] ?></td>
                                <td><?php echo $branchStudentCount['completed'] ?></td>
                                <td><?php echo $branchStudentCount['dependent'] ?></td>
                                <td><?php echo $branchStudentCount['terminateds'] ?></td>
                                <td><?php echo @$branchTotalSummary[$branchStudentCount['branch_id']]['mumtaz_ma_sharf'] ?></td>
                                <td><?php echo @$branchTotalSummary[$branchStudentCount['branch_id']]['mumtaz'] ?></td>
                                <td><?php echo @$branchTotalSummary[$branchStudentCount['branch_id']]['jayyad_jiddan'] ?></td>
                                <td><?php echo @$branchTotalSummary[$branchStudentCount['branch_id']]['jayyad'] ?></td>
                                <td><?php echo @$branchTotalSummary[$branchStudentCount['branch_id']]['maqbool'] ?></td>
                                <td><?php echo @$branchTotalSummary[$branchStudentCount['branch_id']]['rasib'] ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>





            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            new Chart(document.getElementById("exam_chart"), {
                type: 'bar',
                data: {
                  labels: ["<?php $tool->trans("Mumtaz Sharf") ?>", "<?php $tool->trans("Mumtaz") ?>", "<?php $tool->trans("Jayyad Jiddan") ?>", "<?php $tool->trans("Jayyad") ?>", "<?php $tool->trans("Maqbool") ?>", "<?php $tool->trans("Rasib") ?>"],
                  datasets: <?php echo json_encode($examGrades) ?>
                }
            });
            new Chart(document.getElementById("branch_students"), {
                type: 'pie',
                data: {
                  labels: <?php echo json_encode($pieDataName)?>,
                  datasets: [{
                    label: "Population (millions)",
                    backgroundColor: <?php echo json_encode($pieDataColors)?>,
                    data: <?php echo json_encode($pieDataCount)?>
                  }]
                },
                options: {
                  title: {
                    display: true,
                    text: 'Branch Students'
                  }
                }
            });

            new Chart(document.getElementById("total_students"), {
                type: 'bar',
                data: {
                  labels: ['<?php echo implode("','",$pieDataTotalLabels)?>'],
                  datasets: [{
                    label: "Student Counts",
                    backgroundColor: ['<?php echo implode("','",$pieDataTotalColors)?>'],
                    data: ['<?php echo implode("','",$pieDataTotal)?>']
                  }]
                },
                options: {
                  title: {
                    display: true,
                    text: ''
                  }
                }
            });

    });
        
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<?php
$tpl->footer();


$data = ob_get_clean();
$lifetime = 3600 * 2; // cache lifetime (default: 3600)
$cache->save($url, $data, $lifetime);
echo ($data);

$tpl->footer();