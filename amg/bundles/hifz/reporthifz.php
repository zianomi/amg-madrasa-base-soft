<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("HifzModel");
$hfz = new HifzModel();
$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';


$tpl->renderBeforeContent();
$qr->searchContentAbove();
?>

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id']; ?>" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label>&nbsp;</label>&nbsp;</div>
    <div class="span3"><label>&nbsp;</label>&nbsp;</div>
</div>


<?php
$qr->searchContentBottom();
if(isset($_GET['_chk'])==1){
    if(empty($id)){
        echo $tool->Message("alert",$tool->transnoecho("please_select_id_or_date"));
        exit;
    }


    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();
    $resStu = $stu->studentSearch(array("id" => $id));
    $rowStu = $resStu[0];


?>



    <dl class="dl-horizontal ">
        <dt class="fonts"><?php $tool->trans("name_fathername") ?></dt>
        <dd class="fonts"><?php echo $rowStu['name'] ?> <?php echo $tpl->getGenderTrans($rowStu['gender']) ?> <?php echo $rowStu['fname'] ?></dd>
        <dt class="fonts"><?php $tool->trans("branch") ?></dt>
        <dd class="fonts"><?php echo $rowStu['branch_title'] ?></dd>
        <dt class="fonts"><?php $tool->trans("class") ?></dt>
        <dd class="fonts"><?php echo $rowStu['class_title'] ?></dd>
        <dt class="fonts"><?php $tool->trans("section") ?></dt>
        <dd class="fonts"><?php echo $rowStu['section_title'] ?></dd>
        <dt class="fonts"><?php $tool->trans("session") ?></dt>
        <dd class="fonts"><?php echo $rowStu['session_title'] ?></dd>
      </dl>


    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">


        <table class="table table-bordered table-striped table-hover flip-scroll">
            <thead>
            <tr>
                <th class="fonts">پارہ</th>
                <th class="fonts">ابتداء</th>
                <th class="fonts">انتہا</th>
                <th class="fonts">کل ایام</th>
                <th class="fonts">بچے کے ایام</th>
                <th class="fonts">مطلوبہ لائن</th>
                <th class="fonts">رفتار  دن  زیادہ  کم</th>
                <th class="fonts">ایک دن کی رفتار</th>
            </tr>
            </thead>


            <tbody>


            <?php

            $res = $hfz->viewIDHifzProgres(array("id" => $id));

            $i=0;
            $absent = 0;
            $leave = 0;
            $late = 0;
            foreach($res as $row){
                $i++;

                if($row['start_date'] == '0000-00-00' && $row['end_date'] == '0000-00-00'){
                    continue;
                }

                $startdate = strtotime($row['start_date']);
                $enddate = strtotime($row['end_date']);
                $difference = abs($enddate - $startdate);
                $days = floor($difference / (60 * 60 * 24));
            ?>
                <tr>
                    <td class="edk" data-stuid="<?php echo $row['student_id'] ?>" style="cursor: pointer" title="Edit"><span class="fonts"><?php echo $row['title']; ?></span> </td>
                    <td><?php echo date("d-m-Y", strtotime($row['start_date'])); ?></td>
                    <td><?php echo date("d-m-Y", strtotime($row['end_date'])); ?></td>
                    <td><?php echo $row['total_days']; ?></td>
                    <td><?php echo $days; ?></td>
                    <td><?php echo $row['lines_perday']; ?></td>
                    <td><?php $speed = $row['total_days'] - $days;
                    if ($speed < 0) {
                        echo '<span style="color:#FF0000">' . abs($speed) . '</span>';
                    } else if ($speed == 0) {
                        echo $speed;
                    } else {
                        echo '<span style="color:#008800"> +' . $speed . '</span>';
                    }
                    ?></td>
                    <td><?php echo @number_format(($row['total_days'] * $row['lines_perday']) / $days, 2); ?>%</td>
                </tr>

            <?php } ?>



            </tbody>

        </table>
    </div>
<?php }

?>

    <script type="text/javascript">
        $(document).ready(function(){
            $(".edk").click(function(){
                var stuid = $(this).attr('data-stuid');
                   window.location = "<?php echo URL ?>?menu=hifz&page=editreporthifz&_chk=1&student_id="+<?php echo $id ?> + "&branch=" +<?php echo $rowStu['branch_id'] ?>;
            });




        })
    </script>

<?php
$tpl->footer();