<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();
$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_date'])) && (!empty($_GET['to_date']))) ? $tool->ChangeDateFormat($_GET['to_date']) : "";
//$session = isset($_GET['session']) ? $tool->GetExplodedInt($_GET['session']) : '';


$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("id_name") ?>
        </label><input value="<?php if (isset($_GET['student_id']))
            echo $_GET['student_id']; ?>" type="text"
            name="student_id" id="student_id"></div>
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
</div>


<?php
$qr->searchContentBottom();
if (isset($_GET['_chk']) == 1) {
    if (empty($id) && empty($date) && empty($to_date)) {
        echo $tool->Message("alert", $tool->transnoecho("please_select_id_or_date"));
        exit;
    }
    echo $tpl->formTag("post");
    echo $tpl->formHidden();

    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();
    $resStu = $stu->studentSearch(array("id" => $id));
    $rowStu = $resStu[0];


    ?>

    <input type="hidden" name="date" value="<?php if (isset($_GET['date']))
        echo $_GET['date'] ?>">
        <input type="hidden" name="branch" value="<?php echo $branch ?>">
    <!--<input type="hidden" name="session" value="<?php /*echo $session */?>">-->

    <dl class="dl-horizontal ">
        <dt class="fonts">
            <?php $tool->trans("name_fathername") ?>
        </dt>
        <dd class="fonts">
            <?php echo $rowStu['name'] ?>
            <?php echo $tpl->getGenderTrans($rowStu['gender']) ?>
            <?php echo $rowStu['fname'] ?>
        </dd>
        <dt class="fonts">
            <?php $tool->trans("branch") ?>
        </dt>
        <dd class="fonts">
            <?php echo $rowStu['branch_title'] ?>
        </dd>
        <dt class="fonts">
            <?php $tool->trans("class") ?>
        </dt>
        <dd class="fonts">
            <?php echo $rowStu['class_title'] ?>
        </dd>
        <dt class="fonts">
            <?php $tool->trans("section") ?>
        </dt>
        <dd class="fonts">
            <?php echo $rowStu['section_title'] ?>
        </dd>
        <dt class="fonts">
            <?php $tool->trans("session") ?>
        </dt>
        <dd class="fonts">
            <?php echo $rowStu['session_title'] ?>
        </dd>
    </dl>


    <div class="body">


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>S#</th>
                    <th>
                        <?php $tool->trans("attand") ?>
                    </th>
                    <th class="fonts">
                        <?php $tool->trans("date") ?>
                    </th>
                </tr>
            </thead>


            <tbody>


                <?php

                $res = $atd->atdStudentReport($id, $date, $to_date);

                $i = 0;
                $absent = 0;
                $leave = 0;
                $late = 0;
                foreach ($res as $row) {
                    $i++;

                    if ($row['attand'] == 2) {
                        $absent++;
                    }
                    if ($row['attand'] == 3) {
                        $leave++;
                    }
                    if ($row['attand'] == 4) {
                        $late++;
                    }
                    ?>
                    <tr>
                        <td>
                            <?php echo $i; ?>
                        </td>
                        <td class="fonts attands" data-type="select" data-value="<?php echo $row['attand'] ?>"
                            data-pk="<?php echo $row['id'] ?>" data-original-title="Enter number">
                            <?php $atand = $atd->ReturnAtdName($row['attand']);
                            echo $atand; ?>
                        </td>
                        <td class="fonts atddate" data-value="<?php echo date('d-m-Y', strtotime($row['date'])); ?>"
                            data-pk="<?php echo $row['id'] ?>" data-type="text">
                            <?php echo date('d-m-Y', strtotime($row['date'])); ?>
                        </td>
                    </tr>

                <?php } ?>


                <tr>
                    <td colspan="3">
                        <span class="fonts">
                            <?php $tool->trans("total_attands") ?>
                            <?php echo $atd->countNumberOfAttanbdDays($date, $to_date, $rowStu['branch_id'], $rowStu['class_id']) ?>
                        </span>
                        <br /><span class="fonts">
                            <?php $tool->trans("absent") ?>
                            <?php echo $absent; ?>
                        </span>
                        <br /><span class="fonts">
                            <?php $tool->trans("late") ?>
                            <?php echo $late; ?>
                        </span>
                        <br /><span class="fonts">
                            <?php $tool->trans("leave") ?>
                            <?php echo $leave ?>
                        </span>
                    </td>
                </tr>



            </tbody>

        </table>
    </div>
<?php }

echo $tpl->formClose();

?>
<script type="text/javascript" src="<?php echo Tools::getWebUrl() ?>/js/bootstrap-editable.js"></script>
<script type="text/javascript">
    $(function () {
        $.fn.editable.defaults.mode = 'popup';
        $('.attands').editable({

            source: [
                { value: 1, text: 'Present' },
                { value: 2, text: 'Absent' },
                { value: 3, text: 'Leave' },
                { value: 4, text: 'Late' }
            ],

            url: makeJsLink("ajax", "attendance&ajax_request=edit_atd"),
            type: 'text',
            name: 'attand',
            title: 'Enter attand'
        });


        $('.atddate').editable({
            url: makeJsLink("ajax", "attendance&ajax_request=date_edit_atd"),
            type: 'text',
            name: 'attand',
            title: 'Enter attand'
        });


    });
</script>

<?php
$tpl->footer();
