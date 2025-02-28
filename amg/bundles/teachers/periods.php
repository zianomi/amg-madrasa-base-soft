<?php
$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';
$class = isset($_GET['class']) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = isset($_GET['section']) ? $tool->GetExplodedInt($_GET['section']) : '';
$errors = array();
Tools::getModel("TeacherModel");
Tools::getLib("QueryTemplate");
$tec = new TeacherModel();
$qr = new QueryTemplate();
$tpl->setJs(array("bootstrap-datetimepicker"));
$tpl->setJsFileName("base_jswith_time");
$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

    <div class="container text-center">

        <div class="row">

            <div class="span3">
                <label class="fonts"><?php $tool->trans("Session") ?></label>
                <?php echo $tpl->getAllSession(); ?>
            </div>
            <div class="span3">
                <label class="fonts"><?php $tool->trans("branch") ?></label>
                <?php echo $tpl->getAllBranch(); ?>
            </div>
            <div class="span3">
                <label class="fonts"><?php $tool->trans("class") ?></label>
                <?php echo $tpl->getClasses(); ?>
            </div>
            <div class="span3">
                <label class="fonts"><?php $tool->trans("sections") ?></label>
                <?php echo $tpl->getSecsions(); ?>
            </div>
        </div>

        <div class="row">
            <div class="span12">
                <label class="fonts">&nbsp;</label>
                <button type="submit" class="btn btn-small" id="search"><?php $tool->trans("search") ?></button>
            </div>
        </div>
    </div>
    <?php $qr->searchContentBottom(); ?>

<script type="text/javascript">

   $(document).ready(function(){
       $('#class').change(function(){
           var postData =
               { "ajax_request":"GetClassSubjects",
                   "class":$(this).val()
               };
           $.ajax({
               url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
               type:'POST',
               data:postData,
               success:function(response){
                   var returnedData = $.parseJSON(response);
                 if(returnedData.status == "true"){
                    $("#subjects").html(returnedData.data).trigger("chosen:updated");
                 }
                 else{
                     alert(response.msg);
                 }
               }
           });

       });

       <?php
       $queryString = "";
       $queryString .= "period&_chk=1";
       if(isset($_GET['branch'])){
           $queryString .= "&branch=" .$_GET['branch'];
       }
       if(isset($_GET['class'])){
           $queryString .= "&class=" .$_GET['class'];
       }
       if(isset($_GET['session'])){
           $queryString .= "&session=" .$_GET['session'];
       }
       $link = Tools::makeLink("teachers", $queryString,"","");
       ?>
       $('#postSection').change(function(){

           //window.location.href = "<?php //echo $link ?>&section=" + $("#postSection").val();
       });

       $(document).on("click", '.delete', function(event) {
           var id = $(this).attr("data-id");

           $.ajax({
               url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
               type:'POST',
               data:'&id='+id+"&ajax_request=delete_period",
               success:function(response){
                   var returnedData = $.parseJSON(response);
                   if(returnedData.status == "false"){
                       alert(returnedData.msg);
                     }
                     else{
                        $("#"+returnedData.msg).remove();
                     }
               }
           });
       });

       $(".timepicker").datetimepicker({
            format: 'HH:ii p',
            autoclose: true,
            showMeridian: true,
            startView: 1,
            maxView: 1
        });



       $('#submit').on("click", function(){


           var postData =
                       { "ajax_request":"createPeriods",
                        "branch_id":$("#postBranch").val(),
                        "session":$("#postSession").val(),
                        "class":$("#postClass").val(),
                        "section":$("#postSection").val(),
                        "teacher":$("#teachers").val(),
                        "time_start":$("#start_time").val(),
                        "time_end":$("#end_time").val(),
                        "subject":$("#subjects").val()
                       };


                   $.ajax({
                       url:"<?php echo Tools::makeLink("ajax", "teachers", "", "") ?>",
                       type:'POST',
                       data:postData,
                       success:function(response){
                           var returnedData = $.parseJSON(response);
                           if(returnedData.status == "false"){
                               alert(returnedData.msg);
                           }else{
                               $("#viewTabl").html(returnedData.data);
                           }
                       }
                   });

               })
   })

   </script>


            <div id="printReady">
                <?php
                if (isset($_GET['_chk']) == 1) {
                    ?>

                    <div class="row-fluid">
                    <div id='external-events' class="span3 well">


                    <input type="hidden" name="_chk" value="1"/>
                    <input type="hidden" id="postBranch" name="branch" value="<?php echo $branch ?>"/>
                    <input type="hidden" id="postSession" name="session" value="<?php echo $session ?>"/>
                    <input type="hidden" id="postClass" name="session" value="<?php echo $class ?>"/>


                <?php
                $count=0;
                ?>

                <div class='external-event' >


                    <div class="span12">
                        <label class="fonts"><?php $tool->trans("Sections") ?></label>
                        <?php
                        $secsions = $set->sessionSections($session,$class,$branch);
                        echo $tpl->GetOptions(array("data" => $secsions, "name" => "postSection", "sel" => $section)); ?>
                    </div>

                    <br style="clear: both;"><br style="clear: both;">

                    <div class="span12">
                        <label class="fonts"><?php $tool->trans("Teachers") ?></label>
                        <?php echo $tpl->GetOptions(array("name" => "teachers", "data" => $tec->periodsTeacher(array("branch" => $branch,"class" => $class, "groupy_type" => "teachers")), "sel" => "")); ?>
                    </div>

                    <br style="clear: both;"><br style="clear: both;">

                    <div class="span12">


                        <div class="control-group">
                            <label class="control-label"><?php /*$tool->trans("Subjects") */?></label>
                            <div class="controls" id="subDropDown">
                                <select data-placeholder="Select Subjects" id="subjects" name="subjects" class="chosen-select" multiple="multiple" tabindex="6">

                                    <?php
                                    $subjects = $tec->getClassSubjects($class);
                                    foreach ($subjects as $subject){
                                    ?>
                                    <option value="<?php echo $subject['id'] ?>"><?php echo $subject['title'] ?></option>
                                    <?php } ?>


                                </select>
                            </div>
                        </div>
                    </div>

                    <br style="clear: both;"><br style="clear: both;">

                    <div class="span3">
                        <label class="fonts"><?php $tool->trans("Start Time") ?></label>
                        <div class="controls">

                            <div class="input-append">
                                <input size="16" type="text" name="start_time" id="start_time" class="timepicker" value="" >
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>


                        </div>
                    </div>

                    <br style="clear: both;"><br style="clear: both;">

                    <div class="span3">
                        <label class="fonts"><?php $tool->trans("End Time") ?></label>
                        <div class="controls">

                            <div class="input-append">
                                <input size="16" name="end_time" id="end_time" type="text" class="timepicker" value="">
                               <span class="add-on pickTimeOnly"><i class="icon-th"></i></span>
                            </div>


                        </div>
                    </div>

                    <br style="clear: both;"><br style="clear: both;">

                    <div class="span3">
                            <label class="fonts">&nbsp;</label>
                            <button type="submit" id="submit" class="btn btn-small"><?php $tool->trans("insert") ?></button>
                        </div>

                </div>



                </div>

                <div class="span9">


              <div class="social-box">
                  <div class="header">
                      <h4><?php $tool->trans("Periods") ?></h4>
                  </div>
                  <div class="body">
                      <table class="table table-bordered table-striped table-hover flip-scroll">
                          <thead>
                            <tr>
                              <th><?php $tool->trans("Branch Name") ?></th>
                              <th><?php $tool->trans("Session") ?></th>
                              <th><?php $tool->trans("Class") ?></th>
                              <th><?php $tool->trans("Section") ?></th>
                              <th><?php $tool->trans("Teacher") ?></th>
                              <th><?php $tool->trans("Subject") ?></th>
                              <th><?php $tool->trans("Start Time") ?></th>
                              <th><?php $tool->trans("End Time") ?></th>
                               <th><?php $tool->trans("Delete") ?></th>
                            </tr>
                          </thead>
                          <tbody id="viewTabl">
                            <?php


                            $param = array("branch" => $branch, "session" => $session, "class" => $class, "section" => $section);
                            $teacherPeriodsArr = array();

                            $periodsData = $tec->getPeriodData($param);
                            $periodsSubjects = $tec->GetPeriodSubjects($param);

                            foreach($periodsSubjects as $periodsSubject){
                                $teacherPeriodsArr[$periodsSubject['staff_id']][$periodsSubject['period_id']][] = $periodsSubject;
                            }


                                foreach ($periodsData as $period):
                            ?>

                                    <tr id="<?php echo $period['period_id'] ?>">

                                        <td><?php echo $period['branch_title']; ?></td>
                                        <td><?php echo $period['session_title']; ?></td>
                                        <td><?php echo $period['class_title']; ?></td>
                                        <td><?php echo $period['section_title']; ?></td>
                                        <td><?php echo $period['staff_title']; ?></td>
                                        <td>
                                            <?php
                                            if(isset($teacherPeriodsArr[$period['staff_id']][$period['period_id']])){
                                                $i=0;
                                                foreach($teacherPeriodsArr[$period['staff_id']][$period['period_id']] as $periodsRow){
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
                                        <td><span class="label label-success"><?php echo date('h:i A', strtotime($period['start_time'])); ?></span></td>
                                        <td><span class="label label-success"><?php echo date('h:i A', strtotime($period['end_time'])); ?></span></td>

                                        <td class="delete" data-id="<?php echo $period['period_id'] ?>"><button class="btn btn-danger"><?php $tool->trans("Delete") ?><button</td>
                                    </tr>


                            <?php endforeach; ?>


                          </tbody>
                        </table>
                        <div id="reloadDiv">

                        </div>
                  </div>
              </div>


                </div>

                </div>

                <br style="clear: both;">
                <?php } ?>
            </div>







<?php
$tpl->footer();
