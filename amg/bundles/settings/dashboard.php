<?php
$tpl->renderBeforeContent();
?>
    <h1 class="icon-btn" style="padding-bottom: 10px; text-align: center">Dashboard Under Development</h1>

    <div class="body">
        <div class="row-fluid">
            <div class="span4" style="text-align: center">
                <h1>رزلٹ کے مسائل کے لیے ویڈیو لازمی دیکھیں</h1>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/QQabHsV2dcg" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="span4" style="text-align: center">
                <h1>How to Pay | UBL | Mobile Banking</h1>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/fK3E_LB02j0?si=Ims9ugk9IP5ioYN9" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <div class="span4" style="text-align: center">
                <h1>How to Pay | Standard Chartered | Mobile Banking</h1>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/50Irja-CJW8?si=gAA4vUf2YhkHCL3W" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span4" style="text-align: center">
                <h1>How to Pay | Meezan Bank | Mobile Banking</h1>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/jovpzeVUtYU?si=xCwYQJBpfKYA5br6" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <div class="span4" style="text-align: center">
                <h1>How to Pay | MCB | Mobile Banking</h1>
                <iframe width="560" height="315" src="https://www.youtube.com/embed/7twEf2H09uw?si=2W9ZEu_IRJawyXhv" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>

        </div>

    </div>
<?php
$tpl->footer();

exit;
Tools::getModel("Dashboard");
Tools::getLib("RandomColor");
$dash = new Dashboard();

RandomColor::one();




/*$colorsArr = RandomColor::many(20, array(
    'hue' => 'green'
));*/

function getColorsArr(){
   return RandomColor::many(20, array(
       'hue' => 'green'
   ));
}

$colorsArr = getColorsArr();

$studentCount = $dash->countStudents();

/*function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}

function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}*/


$students = array();

foreach ($studentCount as $rowStu){
        $vals = array($rowStu['current'],$rowStu['completed'],$rowStu['terminateds'],$rowStu['dependent']);
        $students[] = array("label" => $rowStu['title'], "backgroundColor" => $colorsArr[$rowStu['zone_id']], "data" => $vals);
}


$tpl->renderBeforeContent();
?>



    <h1 align="center" class="icon-btn" style="padding-bottom: 10px"><img src="<?php echo URL ?>/assets/img/iqra_logo.png"></h1>

    <div class="social-box" style="background: #e2e2e2">
        <div class="header">&nbsp;</div>
        <div class="body">
            <canvas id="current_students_canvas"></canvas>

            <div class="container text-center">


                <div class="row-fluid">

                    <div class="span4">


                        <div class="social-box social-blue social-bordered">
                            <div class="header fonts">طلباء کا اعدادوشمار</div>




                            <table class="table table-bordered table-striped table-hover">

                                    <thead>
                                    <tr>
                                        <th class="fonts">زون</th>
                                        <th class="fonts">موجودہ</th>
                                        <th class="fonts">فاضل</th>
                                        <th class="fonts">خارج</th>
                                        <th class="fonts">موقوف</th>
                                        <th class="fonts">کل</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php

                                    $totalStudents = 0;
                                    foreach ($studentCount as $studentCountKey) {
                                        $rowTotal =0;
                                        $rowTotal += ($studentCountKey['current'] + $studentCountKey['completed'] + $studentCountKey['terminateds'] + $studentCountKey['dependent']);
                                        $totalStudents += $rowTotal;
                                        ?>
                                        <tr>
                                            <td class="fonts"><?php echo $studentCountKey['title'] ?></td>
                                            <td class="fonts"><?php echo $studentCountKey['current'] ?></td>
                                            <td class="fonts"><?php echo $studentCountKey['completed'] ?></td>
                                            <td class="fonts"><?php echo $studentCountKey['terminateds'] ?></td>
                                            <td class="fonts"><?php echo $studentCountKey['dependent'] ?></td>
                                            <td><?php echo $rowTotal ?></td>
                                        </tr>

                                    <?php } ?>
                                    <tr>
                                        <td colspan="5">&nbsp;</td>
                                        <td><?php echo $totalStudents?></td>
                                    </tr>
                                    </tbody>

                                </table>



                        </div>
                    </div>
                    <div class="span3">
                        <div class="social-box">
                            <div class="header fonts">طلباء کا اعدادوشمار</div>

                        </div>
                    </div>


                    <div class="span3">

                        <div class="social-box social-blue social-bordered">
                            <div class="header fonts">تعداد برائے شاخ</div>

                            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                                <table class="table table-bordered table-striped table-hover flip-scroll">


                                    <tbody>
                                    <?php
                                    foreach ($studentCountByBranch as $studentCountByBranchKey) {

                                        if (!empty($studentCountByBranchKey['tot'])) {
                                            $studentBranchCountName[] = $studentCountByBranchKey['short_name'];
                                            $studentBranchCountTotal[] = $studentCountByBranchKey['tot'];
                                        }

                                        ?>
                                        <tr>
                                            <td class="fonts"><?php echo $studentCountByBranchKey['title'] ?></td>
                                            <td><?php echo $studentCountByBranchKey['tot'] ?></td>
                                        </tr>

                                    <?php } ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>


                    </div>
                    <div class="span3">
                        <div class="header fonts">تعداد برائے شاخ</div>
                        <canvas id="zone_students_canvas" style="height: 500px !important;"></canvas>
                    </div>

                </div>


            </div>

        </div>


    </div>


<?php $notesUrl = URL . "?menu=dars&page=notes_by_class&code=48&submit_search=1"; ?>







    <script>
        $(document).ready(function(){
            new Chart(document.getElementById("current_students_canvas"), {
                type: 'bar',
                data: {
                    labels: ["<?php $tool->trans("current") ?>", "<?php $tool->trans("completed") ?>", "<?php $tool->trans("terminateds") ?>", "<?php $tool->trans("dependent") ?>"],
                    datasets: <?php echo json_encode($students) ?>
                }
            });







        });

    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

<?php
$tpl->footer();
