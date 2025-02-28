<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$excludedCols = array();
$sessionClasses = array();
$classArr = array();


if(isset($_GET['classes'])){
    foreach ($_GET['classes'] as $class){
        $classArr[] = $tool->GetExplodedInt($class);
    }
}


if(!empty($classArr)){
    $classArr[] = 11;
    $classArr[] = 10;
}



if(isset($_GET['_chk'])==1){
    $id = (!empty($_GET['student_id'])) ? ($_GET['student_id']) : '';
    $branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';

    $section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
    $session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
    $feeType = (isset($_GET['fee_type'])) ? $tool->GetExplodedInt($_GET['fee_type']) : '';
    $date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : '';
    $to_date = (isset($_GET['to_date'])) ? $tool->ChangeDateFormat($_GET['to_date']) : '';

    if(!empty($branch) && !empty($session)){
        $sessionClasses = $set->sessionClasses($session,$branch);
    }

    if(!empty($_GET['excluded'])){
        foreach($_GET['excluded'] as $key => $val){
            $keyArr = explode("-",$val);
            $keys = $keyArr[0];
            $excludedCols[$keys] = $keys;
        }
    }

}
$qr->renderBeforeContent();
$qr->searchContentAbove();

$discountTypeData = $set->getTitleTable("discount_refrence");

?>
<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">

    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>

    <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>

    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label>
        <select name="classes[]" id="class" multiple="multiple">
            <?php
            $selClass = '';
            $selecedClass = array();

            if(isset($_GET['classes'])){
                foreach ($_GET['classes'] as $classRow){
                    $selecedClass[$tool->GetExplodedInt($classRow)] = ' selected';
                }
            }


            foreach ($sessionClasses as $sessionClass){
                if(isset($_GET['_chk'])==1){

                    if(isset($selecedClass[$sessionClass['id']])){
                        $selClass = ' selected';
                    }
                    else{
                        $selClass = '';
                    }
            ?>
                <option value="<?php echo $sessionClass['id'] ?>-<?php echo $sessionClass['title'] ?>"<?php echo $selClass ?>><?php echo $sessionClass['title'] ?></option>
            <?php } ?>
            <?php } ?>
        </select>

    </div>

    <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>
</div>





<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("date")?></label><?php echo $tpl->getDateInput() ?></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("to_date")?></label><?php echo $tpl->getToDateInput() ?></div>
    <div class="span3">
        <label class="fonts"><?php $tool->trans("excluded")?></label>
            <?php
          echo $tpl->GetMultiOptions(array("name" => "excluded[]", "data" => $discountTypeData, "sel" => $excludedCols));
          ?></div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("fee_type") ?></label>

        <select name="fee_type" id="fee_type">
            <?php
            $typeData = $set->getTitleTable("fee_type");
            ?>
                <?php echo $tpl->GetOptionVals(array("data" => $typeData, "sel" => $feeType)); ?>
            </select>
        <?php
        //$typeData = $set->getTitleTable("fee_type");
        //echo $tpl->GetMultiOptions(array("name" => "fee_types[]", "data" => $typeData, "sel" => ""));
        ?>
    </div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label>&nbsp;</label>&nbsp;</div>
    <div class="span3"><label>&nbsp;</label>&nbsp;</div>
</div>
<div class="row-fluid">

</div>
<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){

if(empty($branch) || empty($date) || empty($to_date)){
    echo $tool->Message("alert",$tool->transnoecho("branch_and_from_and_todate_required"));
    exit;
}


if(!$tool->checkDateFormat($date)){
    echo $tool->Message("alert",$tool->transnoecho("from_date_invalid"));
    exit;
}

if(!$tool->checkDateFormat($to_date)){
    echo $tool->Message("alert",$tool->transnoecho("to_date_invalid"));
    exit;
}



Tools::getModel("FeeModel");
$fs = new FeeModel();


$param = array("branch" => $branch
       , "class" => $classArr
       , "section" => $section
       , "session" => $session
       , "recp_start_date" => $date
       , "recp_end_date" => $to_date
       , "not_paid_status" => $fs->paidStatus("pending")
       , "paid_status" => $fs->paidStatus("paid")
       , "feeType" => $feeType
       , "id" => $id
       );

$discountRef = $fs->GetStudentDiscountRef($param);

$res = $fs->GetPaidData($param);
$total = count($res);


if($total==0){
       echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
       exit;
   }

    $students = array();
   $feePaids = array();
   $feeTypes = array();

foreach ($res as $row) {



    if ($row['gender'] == 1) {
        $gender = " S/O ";
    } else {
        $gender = " D/O ";
    }

    $students[$row['student_id']] = array(
               "id" => $row['student_id']
       , "name" => $row['name']
       , "gender" => $gender
       , "father_name" => $row['fname']
       , "grnumber" => $row['grnumber']
       , "class_title" => $row['class_title']
    , "section_title" => $row['section_title']
       , "invoice_id" => $row['invoice_id']
       , "fees_date" => $row['fee_date']

       );

    $feeTypes[$row['type_id']] = array(
                "type_id" => $row['type_id']
        , "title" => $row['title_en']
        , "duration_type" => $row['duration_type']);

    $feePaids[$row['student_id']][$row['type_id']][] = array(
                "fees" => $row['fees']
        , "discount" => $row['discount']
        , "paid_status" => $row['paid_status']
        , "fee_date" => $row['fee_date']
        , "type_id" => $row['type_id']
        , "title_en" => $row['title_en']
        , "duration_type" => $row['duration_type']
        , "invoice_id" => $row['invoice_id']
        , "stuid" => $row['student_id']

        );

}

$disCountStudents = array();
foreach ($discountRef as $disKey){
    $disCountStudents[$disKey['student_id']] = $disKey['refrence'];
}




?>

    <div class="body">
<div id="printReady">

    <div class="row-fluid">
            <div class="span7 text-center">
                        <img class="logo" src="<?php echo $tool->getWebUrl() ?>/img/iqra_logo.png" alt="Amazon" height="77" width="400">
            </div>

            <div class="span5">

                <div class="alert alert-success fonts" style="font-size: 25px; "><?php $tool->trans("paid_students") ?></div>

              <dl class="dl-horizontal">
                <dt class="fonts"><?php $tool->trans("branch") ?></dt>
                <dd  class="fonts"><?php if(!empty($branch)){
                        echo $tool->GetExplodedVar($_GET['branch']);
                    }
                       ?></dd>


              <?php  if(isset($_GET['classes'])){ ?>
                <dt class="fonts"><?php $tool->trans("class") ?></dt>
                <dd class="fonts"><?php
                    foreach ($_GET['classes'] as $class){
                        echo $tool->GetExplodedVar($class) . " ";
                    }

                    ?></dd>
              <?php } ?>
                <dt class="fonts"><?php $tool->trans("from_date") ?></dt>
                <dd><?php echo date('F d, Y', strtotime($date)); ?></dd>
                <dt class="fonts"><?php $tool->trans("to_date") ?></dt>
                  <dd><?php echo date('F d, Y', strtotime($to_date)); ?></dd>

                  <dt class="fonts datetimeprint"><?php $tool->trans("print_time") ?></dt>
                  <dd id="datetimePrint">&nbsp;</dd>
              </dl>
            </div>

          </div>




                    <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                       <table class="table table-bordered table-striped table-hover">
                           <thead>
                           <tr>
                               <th><?php $tool->trans("S#") ?></th>
                               <th class="fonts"><?php $tool->trans("ID") ?></th>
                               <th class="fonts"><?php $tool->trans("GR") ?></th>
                               <th class="fonts"><?php $tool->trans("Name") ?></th>
                               <th class="fonts"><?php $tool->trans("Father Name") ?></th>
                               <th class="fonts"><?php $tool->trans("Class") ?></th>
                               <th class="fonts"><?php $tool->trans("Section") ?></th>
                               <?php
                               foreach($feeTypes as $feeType){
                               ?>
                               <th><?php echo $feeType['title'] ?></th>
                               <?php } ?>
                               <th class="fonts"><?php $tool->trans("Total") ?></th>

                           </tr>
                           </thead>
                           <tbody>
                           <?php
                           $i = 0;
                           $overAllTotal = 0;
                           $studentAmount = array();
                           $typeViceTotal = array();
                            //echo '<pre>';print_r($feePaids );echo '</pre>';

                           foreach($students as $student) {

                               if(isset($disCountStudents[$student['id']])){
                                   $discountRefNumber = $disCountStudents[$student['id']];
                                   if(in_array($discountRefNumber,$excludedCols)){
                                       foreach($feeTypes as $feeType){
                                           foreach($feePaids[$student['id']][$feeType['type_id']] as $feePaid){
                                               if(empty($feePaid['invoice_id'])){
                                                   continue;
                                               }

                                           }
                                       }

                                   }
                               }
                               $i++;
                               ?>
                               <tr>
                                   <td><?php echo $i ?></td>
                                   <td><?php echo $student['id'] ?></td>
                                   <td><?php echo $student['grnumber'] ?></td>
                                   <td class="fonts"><?php echo $student['name']; ?></td>
                                   <td class="fonts"><?php echo $student['father_name']; ?></td>
                                   <td class="fonts"><?php echo $student['class_title']; ?></td>

                                   <td class="fonts"><?php echo $student['section_title']; ?></td>
                                   <?php
                                   $stuTotal = 0;
                                  foreach($feeTypes as $feeType){
                                  ?>
                                   <td style="direction: ltr"><?php

                                       if(isset($feePaids[$student['id']][$feeType['type_id']])){

                                           foreach($feePaids[$student['id']][$feeType['type_id']] as $feePaid){
                                               $dueAmount = ($feePaid['fees'] - $feePaid['discount']);
                                               $stuTotal += $dueAmount;
                                               $overAllTotal += $dueAmount;
                                               $feeDate[$student['id']][$feeType['type_id']][] = $feePaid['fee_date'];
                                               $studentAmount[$student['id']][$feeType['type_id']][] = $dueAmount;
                                               $typeViceTotal[$feeType['type_id']][] = $dueAmount;
                                           }



                                            if(count($feeDate[$student['id']][$feeType['type_id']]) > 1){
                                               $firsDate = date('F Y', strtotime(min($feeDate[$student['id']][$feeType['type_id']])));
                                               $lastDate = date('F Y', strtotime(max($feeDate[$student['id']][$feeType['type_id']])));
                                               $dateToDis = $firsDate . " to " . $lastDate;
                                            }
                                            else{
                                                $dateToDis = date('F Y', strtotime($feeDate[$student['id']][$feeType['type_id']][0]));
                                            }
                                           echo $dateToDis . " : <b>" . array_sum($studentAmount[$student['id']][$feeType['type_id']]). "</b>";
                                       }


                                       ?></td>
                                  <?php } ?>
                                   <td><?php echo $stuTotal; ?></td>
                               </tr>
                           <?php } ?>
                           <tr>
                               <td colspan="7">&nbsp;</td>
                                <?php foreach($feeTypes as $feeType){?>
                                    <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo @array_sum($typeViceTotal[$feeType['type_id']]) ?></td>
                                <?php } ?>
                               <td><span class="fonts"><?php $tool->trans("Total") ?></span> <?php echo $overAllTotal ?></td>
                           </tr>
                           </tbody>
                       </table>
                   </div>

           </div>
    </div>


    <style type="text/css">
        .datetimeprint{display: none;}
        #datetimePrint{display: none;}
        @media print{
            .table, .table tr, table th, .table td {
               border-color: black;
            }
            .datetimeprint{display: block;}
            #datetimePrint{display: block;}
        }

    </style>

    <script>
        $("#datetimePrint").text("<?php echo date("d/m/Y h:i") ?>");
    </script>
<?php



}


$tpl->footer();
