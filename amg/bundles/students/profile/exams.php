<div class="print_area">
        <?php


        $curPageUrl = urlencode($_SERVER['REQUEST_URI']);

        $sessionData = $set->allSessions();
        $sessionsArr = array();
        foreach ($sessionData as $sesRow){
            $sessionsArr[$sesRow['id']] = $sesRow['title'];
        }
        $examClasses = array();
        $exams = array();
        $sessions = array();
        $examSub = array();
        $examNameArr[2] = "First Monthly Exam";
        $examNameArr[3] = "Mid Term";
        $examNameArr[4] = "Second Monthly Exam";
        $examNameArr[5] = "Final Exam";


        foreach ($examData as $rowExam){





            $sessions[$rowExam['session_id']] = $rowExam['session_id'];
            $examClasses[$rowExam['session_id']][$rowExam['class_id']] = $rowExam['class_title'];


            $examSub[$rowExam['session_id']][$rowExam['class_id']][$rowExam['exam_id']][$rowExam['subject_id']] = array(
                "subject_title" => $rowExam['subject_title']
                ,"subject_numbers" => $rowExam['subject_numbers']
                ,"subject_id" => $rowExam['subject_id']
                ,"exam_numbers" => $rowExam['numbers']
                ,"section_title" => $rowExam['section_title']
                ,"result_table_id" => $rowExam['result_table_id']
                ,"branch_id" => $rowExam['branch_id']
                ,"class_id" => $rowExam['class_id']
                ,"section_id" => $rowExam['section_id']
                ,"date" => $rowExam['date']
            );

            //$exams[$rowExam['session_id']][$rowExam['class_id']][$rowExam['exam_id']] = $examData;
        }

foreach ($sessions as $session){
        foreach ($examClasses[$session] as $examClasKey => $examClasVal){


        ?>
    <div class="alert alert-info fonts" style="font-size: 20px; "><strong><?php echo $examClasVal ?> <?php echo $sessionsArr[$session] ?></strong></div>
            <?php

            ?>
        <div class="row-fluid">

            <?php

            for($i=2; $i<6; $i++){




            ?>
            <div class="span3">

                <div class="alert alert-success fonts" style="font-size: 20px; "><strong>
                        <a href="<?php echo FRONT_SITE_URL ?>/exam-report?student=<?php echo $urlPassedID?>" target="_blank">
                        <?php echo $examNameArr[$i] ?></a></strong></div>





                <table class="table table-bordered">

                    <tbody>
                    <?php
                    $sumExam = 0;
                    $sumExamSubs = 0;
                if(isset($examSub[$session][$examClasKey][$i])){
                    foreach ($examSub[$session][$examClasKey][$i] as $subRow){
                        //echo '<pre>';print_r($row );echo '</pre>';
                        $sumExam += $subRow['exam_numbers'];
                        $sumExamSubs += $subRow['subject_numbers'];
                    ?>

                        <?php
                        if(!isset($stuPubResults[$session][$subRow['branch_id']][$subRow['class_id']][$subRow['section_id']][$i])){

                            $publishedLink = Tools::makeLink("students","profile","","");
                            $publishedLink .= "&session=";
                            $publishedLink .= $session;
                            $publishedLink .= "&branch=";
                            $publishedLink .= $subRow['branch_id'];
                            $publishedLink .= "&class=";
                            $publishedLink .= $subRow['class_id'];
                            $publishedLink .= "&section=";
                            $publishedLink .= $subRow['section_id'];
                            $publishedLink .= "&exam=";
                            $publishedLink .= $i;
                            $publishedLink .= "&student=";
                            $publishedLink .= $urlPassedID;
                            $publishedLink .= "&ins=1";
                            $publishedLink .= "&redir=";
                            $publishedLink .= $curPageUrl;

                            ?>
                    <tr class="alert alert-error">
                        <td colspan="3">Result not published </td>
                        <td colspan="2"><a href="<?php echo $publishedLink ?>" class="btn btn-success">Publish</a></td>
                    </tr>
                    <?php

                        //$stuPubResults[$stuPubResults[$session][$subRow['branch_id']][$subRow['class_id']][$subRow['section_id']][$i]] = true;
                        }

                        ?>
                    <tr id="row_<?php echo $subRow['result_table_id'] ?>">
                        <td><?php echo $subRow['subject_id'] ?></td>
                        <td><?php echo $subRow['section_title'] ?></td>
                        <td class="fonts"><?php echo $subRow['subject_title'] ?></td>
                        <td><?php echo $subRow['exam_numbers'] ?></td>
                        <!--<td><?php /*echo $tool->ChangeDateFormat($subRow['date']) */?></td>-->

                        <td><a href="javascript:void(0)" class="del" data-id="<?php echo $subRow['result_table_id'] ?>"><i class="icon-remove"></i></a></td>
                    </tr>
                    <?php } ?>
                    <?php }

                    if($sumExam > 0){

                    //echo '<pre>'; print_r($sumExamSubs); echo '</pre>';
                        $percentage = number_format( ($sumExam / $sumExamSubs) * 100,2);
                    ?>
                    <!--<tr>
                        <td class="fonts">Total Numbers</td>
                        <td><b><?php /*echo $sumExam */?></b></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>

                    </tr>-->
                    </tbody>
                    <!--<tr>
                        <td class="fonts"><?php /*echo $exm->numberBetween($percentage); */?></td>
                        <td><?php /*echo $percentage */?></td>
                        <td><?php /* */?></td>

                    </tr>-->
            <?php } ?>

                </table>


            </div>

            <?php } ?>
        </div>
        <?php } ?>
<?php } ?>

    </div>


<script>
    $(document).ready(function () {
        $('.del').click(function () {
            if (confirm('Are you sure you want to delete this?')) {
                var id = $(this).data("id");
                var data = 'ajax_request=delete_exam_subject_record&record_to_delete=' + id;
                var row = $(this).closest("tr");
                $.ajax({
                    url: makeJsLink("ajax","exam"),

                    type: "POST",
                    data: data,
                    success: function (data) {
                        if(data === "OK"){
                            row.remove();
                        }
                        else{
                            alert(data);
                        }
                    }
                });
            }

        });
    });
</script>
