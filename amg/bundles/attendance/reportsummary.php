<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$tpl->setCanExport(false);
$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";

if (isset($_GET['_chk']) == 1) {
    $param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "date" => $date, "to_date" => $to_date);

}

$tpl->renderBeforeContent();
$qr->searchContentAbove();



?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("session") ?>
        </label>
        <?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("branch") ?>
        </label>
        <?php echo $tpl->userBranches() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("class") ?>
        </label>
        <?php echo $tpl->getClasses() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("section") ?>
        </label>
        <?php echo $tpl->getSecsions() ?>
    </div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("date") ?>
        </label>
        <?php echo $tpl->getDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">
            <?php $tool->trans("to_date") ?>
        </label>
        <?php echo $tpl->getToDateInput() ?>
    </div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();


if (isset($_GET['_chk']) == 1) {

    if (empty($branch) || empty($session) || empty($session) || empty($date) || empty($to_date)) {
        echo $tool->Message("alert", $tool->transnoecho("please_select_session_branch_and_dates"));
        exit;
    }

    if (!$tool->checkDateFormat($date)) {
        $errors[] = $tool->Message("alert", "invalid_from_date.");
        exit;
    }

    if (!$tool->checkDateFormat($to_date)) {
        $errors[] = $tool->Message("alert", "invalid_to_date.");
        exit;
    }
    Tools::getModel("AttendanceModel");
    $atd = new AttendanceModel();



    ?>

    <div class="body">
        <table class="table table-bordered">
            <thead>

                <tr>
                    <th>S#</th>
                    <th>ID</th>
                    <th class="fonts">
                        <?php $tool->trans("name_fathername") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("branch") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("class") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("section") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("session") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("total_attand") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("attand") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("absent") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("leave") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("late") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("attand_percent") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("absent_percent") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("leave_percent") ?>
                    </th>
                </tr>
            </thead>


            <tbody>
                <?php
                $totalAttandArr = $atd->countSchoolDays($date, $to_date, $branch);
                $res = $atd->classAttandReportSUM($param);
                $a = 0;
                foreach ($res as $row) {
                    $total_attandance = 0;
                    if (isset($totalAttandArr[$row['class_id']])) {
                        $total_attandance = $totalAttandArr[$row['class_id']];
                    }
                    $a++;
                    $total_present = $total_attandance - ($row['absent'] + $row['leaves']);
                    @$presentper = ($total_present / $total_attandance) * 100;
                    @$absentper = ($row['absent'] / $total_attandance) * 100;
                    @$leaveper = ($row['late'] / $total_attandance) * 100;

                    ?>
                    <tr>
                        <td>
                            <?php echo $a; ?>
                        </td>
                        <td>
                            <?php echo $row['student_id']; ?>
                        </td>
                        <td><span class="fonts">
                                <?php echo $row['name'] . ' ' . $row['gender'] . ' ' . $row['fname']; ?>
                            </span></td>
                        <td class="fonts">
                            <?php echo $row['branch_title']; ?>
                        </td>
                        <td class="fonts">
                            <?php echo $row['class_title']; ?>
                        </td>
                        <td class="fonts">
                            <?php echo $row['section_title']; ?>
                        </td>
                        <td class="fonts">
                            <?php echo $row['session_title']; ?>
                        </td>
                        <td>
                            <?php echo $total_attandance; ?>
                        </td>
                        <td>
                            <?php echo $total_present ?>
                        </td>
                        <td>
                            <?php echo $row['absent']; ?>
                        </td>
                        <td>
                            <?php echo $row['leaves']; ?>
                        </td>
                        <td>
                            <?php echo $row['late']; ?>
                        </td>
                        <td>
                            <?php echo number_format($presentper, 2); ?>%
                        </td>
                        <td>
                            <?php echo number_format($absentper, 2) ?>%
                        </td>
                        <td>
                            <?php echo number_format($leaveper, 2); ?>%
                        </td>
                    </tr>
                <?php } ?>


            </tbody>
        </table>
    </div>

    <?php
}
$tpl->footer();
