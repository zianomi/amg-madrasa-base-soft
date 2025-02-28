<?php
Tools::getModel("TeacherModel");
Tools::getLib("QueryTemplate");
$tec = new TeacherModel();
$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = (isset($_GET['date']) && !empty($_GET['date']) ) ? $tool->ChangeDateFormat($_GET['date']) : "";
$toDate = (isset($_GET['to_date']) && !empty($_GET['to_date']) ) ? $tool->ChangeDateFormat($_GET['to_date']) : "";

$qr = new QueryTemplate();
$qr->setPageHeading($tool->transnoecho("Attendance Report"));

$tpl->renderBeforeContent();

$qr->searchContentAbove();
?>
    <div class="row">

        <div class="span3">
            <label class="fonts"><?php $tool->trans("Session") ?></label>
            <?php echo $tpl->getAllSession() ?>
        </div>

        <div class="span3">
            <label class="fonts"><?php $tool->trans("Branch") ?></label>
            <?php echo $tpl->userBranches() ?>
        </div>

        <div class="span3">
            <label class="fonts"><?php $tool->trans("Date One") ?></label>
            <div class="controls">

                <div class="input-append date form_datetime">
                    <input size="16" id="postDate" name="date" type="text" value="" readonly>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>

        <div class="span3">
            <label class="fonts"><?php $tool->trans("Date Two") ?></label>
            <div class="controls">
                <div class="input-append date form_datetime">
                    <input size="16" id="postDate" name="to_date" type="text" value="" readonly>
                    <span class="add-on"><i class="icon-th"></i></span>
                </div>
            </div>
        </div>



        <div class="span3">
            <label class="fonts">&nbsp;</label>
            <button type="submit" id="searchRecord" class="btn btn-small"><?php $tool->trans("search") ?></button>
        </div>
    </div>
<?php
$qr->searchContentBottom();
?>




                <?php if (isset($_GET['_chk']) == 1) {

                if(empty($date) || empty($toDate) || empty($session) || empty($branch)){
                    echo $tool->Message("alert",$tool->transnoecho("All fields required."));
                    $tpl->footer();
                    exit;
                }

                if(!$tool->checkDateFormat($date) || !$tool->checkDateFormat($toDate)){
                    echo $tool->Message("alert",$tool->transnoecho("Date invalid"));
                    $tpl->footer();
                    exit;
                }

                 $params = array();

                $params['branch'] = $branch;
                $params['session'] = $session;
                $params['date'] = $date;
                $params['to_date'] = $toDate;

                ?>
                <div class="body">
                    <div class="row-fluid">
                    <div class="span12">




                    <div class="body">

                        <div class="alert alert-success"><?php echo $tool->GetExplodedVar($_GET['branch']) ?></div>
                        <div class="alert alert-success"><?php echo $tool->ChangeDateFormat($date) ?> <?php $tool->trans("TO") ?> <?php echo $tool->ChangeDateFormat($toDate) ?></div>

                        <table class="table table-bordered table-striped table-hover">
                              <thead>
                                <tr>
                                  <th><?php $tool->trans("Staff Name") ?></th>
                                  <th><?php $tool->trans("Total Present") ?></th>
                                  <th><?php $tool->trans("Total Absent") ?></th>
                                  <th><?php $tool->trans("Total Leave") ?></th>
                                  <th><?php $tool->trans("Total Late") ?></th>
                                </tr>
                              </thead>
                              <tbody>

                              <?php
                              $periodAttandData = $tec->staffAttendance($params);
                              foreach ($periodAttandData as $branchAttendance){?>
                                  <tr>
                                      <td style="cursor: pointer; color: #0b2c89" class="staff_data" data-id="&name=<?php echo $branchAttendance['staff_title'] ?>&id=<?php echo $branchAttendance['id'] ?>&date=<?php echo $_GET['date'] ?>&todate=<?php echo $_GET['to_date'] ?>"><?php echo $branchAttendance['staff_title'] ?></td>
                                      <td><?php echo $branchAttendance['present_staff'] ?></td>
                                      <td><?php echo $branchAttendance['absent_staff'] ?></td>
                                      <td><?php echo $branchAttendance['leave_staff'] ?></td>
                                      <td><?php echo $branchAttendance['late_staff'] ?></td>
                                  </tr>
                              <?php } ?>
                              </tbody>
                            </table>
                      </div>
            </div>
                </div>
                </div>
                <br style="clear: both;">

            <script type="text/javascript">
                $(".staff_data").click(function(){
                    location.href = "<?php echo $tool->makeLink("teachers","singleattandreport","","") ?>" + $(this).attr("data-id");
                });

            </script>
            <?php
}
$tpl->footer();
