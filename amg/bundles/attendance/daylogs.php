<?php
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();



Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$param = array("date" => $date);



$tpl->renderBeforeContent();
$qr->searchContentAbove();



?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("date") ?>
        </label>
        <?php echo $tpl->getDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if (isset($_GET['_chk']) == 1) {

    if (empty($date)) {
        echo $tool->Message("alert", $tool->transnoecho("please_select_date"));
        exit;
    }

    if (!empty($date)) {
        if (!$tool->checkDateFormat($date)) {
            $errors[] = $tool->Message("alert", "invalid_from_date.");
            exit;
        }
    }


    $resBranch = $atd->branchStudentsAtd();
    $countClasses = $atd->countBranchClasses($param);
    $countStudents = $atd->countBranchesAttend($param);

    $classCounts = array();
    $studentRows = array();

    foreach ($countClasses as $row) {
        $classCounts[$row['branch_id']] = $row['tot'];
    }

    foreach ($countStudents as $row) {
        $studentRows[$row['branch_id']] = array(
            "absent_count" => $row['absent_count'],
            "leave_count" => $row['leave_count'],
            "late_count" => $row['late_count']
        );
    }





    ?>

    <div class="body">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>S#</th>
                    <th>Brnach</th>
                    <th>Total Classes</th>
                    <th>Students</th>
                    <th>Absent</th>
                    <th>Leave</th>
                    <th>Late</th>

                </tr>
            </thead>
            <tbody>
                <?php $i = 0;
                foreach ($resBranch as $row) {
                    $i++;

                    $late = "-";
                    $leave = "-";
                    $absent = "-";

                    if (isset($studentRows[$row['branch_id']])) {
                        if ($studentRows[$row['branch_id']]['late_count'] > 0) {
                            $late = $studentRows[$row['branch_id']]['late_count'];
                        }

                        if ($studentRows[$row['branch_id']]['leave_count'] > 0) {
                            $leave = $studentRows[$row['branch_id']]['leave_count'];
                        }

                        if ($studentRows[$row['branch_id']]['absent_count'] > 0) {
                            $absent = $studentRows[$row['branch_id']]['absent_count'];
                        }

                    }
                    ?>
                    <tr>
                        <td>
                            <?php echo $i ?>
                        </td>
                        <td>
                            <?php echo $row['branch_name'] ?>
                        </td>
                        <td>
                            <?php
                            if (isset($classCounts[$row['branch_id']])) {
                                echo $classCounts[$row['branch_id']];
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $row['tot'] ?>
                        </td>
                        <td>
                            <?php echo $absent ?>
                        </td>
                        <td>
                            <?php echo $leave ?>
                        </td>
                        <td>
                            <?php echo $late ?>
                        </td>
                    </tr>
                <?php } ?>

            </tbody>
        </table>
    </div>

    <?php

}

$tpl->footer();