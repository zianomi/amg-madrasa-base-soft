<?php
Tools::getModel("TeacherModel");
Tools::getLib("QueryTemplate");
$tec = new TeacherModel();
$qr = new QueryTemplate();
$tpl->setJs(array("bootstrap-datetimepicker"));
$tpl->setJsFileName("base_jswith_time");
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>



<script>
       $(document).ready(function(){

           // GET Report Branch Wise
           $('#filterBranch').change(function(){

               var branch_id = $(this).val();

               var postData =
                   { "form":"GetReportBranchWise",
                       "branch":branch_id
                   };

               $.ajax({
                   url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
                   type:'POST',
                   data:postData,
                   dataType:'json',
                   success:function(response){
                       //alert(response);
                       $("#viewTabl").html(response);

                   }
               });
           });

           // GET Report Session Wise
           $('#filterSession').change(function(){

               var session_id = $(this).val();

               var postData =
                   { "form":"GetReportSessionWise",
                       "session":session_id
                   };

               $.ajax({
                   url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
                   type:'POST',
                   data:postData,
                   dataType:'json',
                   success:function(response){
                       //alert(response);
                           $("#viewTabl").html(response);

                   }
               });
           });

           // GET Period Data Class Wise
           $('#filterClass').change(function(){

               var class_id = $(this).val();

               var postData =
                   { "form":"GetReportClassWise",
                       "class":class_id
                   };

               $.ajax({
                   url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
                   type:'POST',
                   data:postData,
                   dataType:'json',
                   success:function(response){
                       //alert(response);

                       if(response == "error"){

                           //alert("Already exist");

                       }else{
                           $("#viewTab2").hide();
                           $("#viewTabl").html(response);

                       }

                   }

               });

           });

           // GET Report Section Wise
           $('#filterSection').change(function(){

               var section_id = $(this).val();

               var postData =
                   { "form":"GetReportSectionWise",
                       "section":section_id
                   };

               $.ajax({
                   url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
                   type:'POST',
                   data:postData,
                   dataType:'json',
                   success:function(response){
                       //alert(response);
                       $("#viewTabl").html(response);

                   }
               });
           });

       })

   </script>


        <div class="row-fluid">

            <div class="row">
                <div class="span3">
                    <label><?php $tool->trans("Session") ?></label>
                    <?php echo $tpl->getAllSession(); ?>
                </div>

                <div class="span3">
                    <label><?php $tool->trans("Branch") ?></label>
                    <?php echo $tpl->getAllBranch(); ?>
                </div>

                <div class="span3">
                    <label><?php $tool->trans("Class") ?></label>
                    <?php echo $tpl->getClasses() ?>
                </div>

                <div class="span3">
                    <label><?php $tool->trans("Section") ?></label>
                    <?php echo $tpl->getSecsions() ?>
                </div>

            </div>

            <div class="row">
                <div class="span12">
                    <label>&nbsp;</label>
                    <button type="submit" id="submit" class="btn btn-small"><?php $tool->trans("Select") ?></button>
                </div>
            </div>


        </div>

<?php $qr->searchContentBottom(); ?>


                <div class="body">
                    <?php
                    if (isset($_GET['_chk']) == 1) {
                    ?>
                    <div id="printReady">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?php $tool->trans("Session") ?></th>
                            <th><?php $tool->trans("Branch") ?></th>
                            <th><?php $tool->trans("Class") ?></th>
                            <th><?php $tool->trans("Section") ?></th>
                            <th><?php $tool->trans("Teacher") ?></th>
                            <th><?php $tool->trans("Subject") ?></th>
                            <th><?php $tool->trans("Start") ?></th>
                            <th><?php $tool->trans("End") ?></th>
                        </tr>
                        </thead>
                        <tbody id="viewTabl">
                        <?php

                            $param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session);
                        $teacherPeriodsArr = array();
                            $getPeriodReport = $tec->periodReport($param);
                        $periodsSubjects = $tec->GetPeriodSubjects($param);

                        foreach($periodsSubjects as $periodsSubject){
                            $teacherPeriodsArr[$periodsSubject['staff_id']][$periodsSubject['period_id']][] = $periodsSubject;
                        }

                            foreach ($getPeriodReport AS $periodRe){
                        ?>
                        <tr>

                            <td><?php echo $periodRe['session_title']; ?></td>

                            <td><?php echo $periodRe['branch_title']; ?></td>

                            <td><?php echo $periodRe['class_title']; ?></td>

                            <td><?php echo $periodRe['section_title']; ?></td>

                            <td><?php echo $periodRe['teacher_title']; ?></td>

                            <td>
                                <?php


                                if(isset($teacherPeriodsArr[$periodRe['staff_id']][$periodRe['period_id']])){
                                    $i=0;
                                    foreach($teacherPeriodsArr[$periodRe['staff_id']][$periodRe['period_id']] as $periodsRow){
                                        $i++;
                                        if($i>1){
                                            echo "<br />" . $periodsRow['title'];
                                        }
                                        else{
                                            echo $periodsRow['title'];
                                        }
                                    }

                                }
                                ?>
                            </td>

                            <td><span class="label label-success"><?php echo date('h:i A', strtotime($periodRe['start_time'])) ?></span></td>

                            <td><span class="label label-success"><?php echo date('h:i A', strtotime($periodRe['end_time'])) ?></span></td>

                        </tr>

                            <?php } ?>

                        </tbody>
                    </table>
                    </div>
                    <?php } ?>
                </div>

<?php
$tpl->footer();


