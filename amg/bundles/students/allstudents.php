<?php
$errors = array();
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$id = (!empty($_GET['student_id'])) ? ($_GET['student_id']) : '';
$gr = (!empty($_GET['gr'])) ? ($_GET['gr']) : '';

$tpl->setCanPrint(false);
$tpl->setCanAdd(false);
$tpl->setCanExport(false);


$tpl->renderBeforeContent();



$qr->searchContentAbove();

?>
    <div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("gr")?></label><input value="" type="text" name="gr" id="gr"></div>
    <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label class="fonts">&nbsp;</label>&nbsp;</div>
    <div class="span3"><label class="fonts">&nbsp;</label>&nbsp;</div>

</div>



<?php
$qr->searchContentBottom();



if(isset($_GET['_chk'])==1){

    if(empty($id) && empty($gr)){
        echo $tool->Message("alert","Please enter ID or GR");
        $tpl->footer();
        exit;
    }



    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();
    $studentArr = $stu->allStudentSearch(array("id" => $id, "gr" => $gr));
    $student = $studentArr[0];

?>

    <table class="table">
        <thead>
            <tr>
                <th><?php $tool->trans("id") ?></th>
                <th><?php $tool->trans("grnumber") ?></th>
                <th><?php $tool->trans("name") ?></th>
                <th><?php $tool->trans("father_name") ?></th>
                <th><?php $tool->trans("branch") ?></th>
                <th><?php $tool->trans("class") ?></th>
                <th><?php $tool->trans("section") ?></th>
                <th><?php $tool->trans("session") ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $student['id'] ?></td>
                <td><?php echo $student['grnumber'] ?></td>
                <td><?php echo $student['name'] ?></td>
                <td><?php echo $student['fname'] ?></td>
                <td><?php echo $student['branch_title'] ?></td>
                <td><?php echo $student['class_title'] ?></td>
                <td><?php echo $student['section_title'] ?></td>
                <td><?php echo $student['session_title'] ?></td>
            </tr>
        </tbody>
    </table>

<?php
}
$tpl->footer();
