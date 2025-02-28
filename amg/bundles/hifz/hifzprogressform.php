<?php
Tools::getLib("QueryTemplate");
Tools::getModel("HifzModel");
Tools::getModel("MonthlyTestModel");
Tools::getModel("ExamModel");
Tools::getModel("AttendanceModel");
Tools::getModel("FeeModel");
$qr = new QueryTemplate();

$hfz = new HifzModel();
$test = new MonthlyTestModel();
$exm = new ExamModel();
$atd = new AttendanceModel();
$fee = new FeeModel();

$tpl->setCanExport(false);
$tpl->setCanPrint(false);


$student_id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : NULL;
$sessionId = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';

$session = $set->getCurrentSession($sessionId);

if(isset($_POST['_chk']) == 1){
    extract($_POST);
    $data = $_POST;
    $data['first_admission_class'] 	    = $tool->GetExplodedInt($_POST['first_admission_class']);
    $data['second_term_percent'] 		= $tool->GetExplodedInt($_POST['second_term_percent']);
    $data['third_term_percent'] 		= $tool->GetExplodedInt($_POST['third_term_percent']);
    $data['wifaq_percent'] 				= $tool->GetExplodedInt($_POST['wifaq_percent']);
    $data['student_id'] 				= $tool->GetInt($_POST['id']);
    $data['start_date_roza'] 			= $tool->ChangeDateFormat($_POST['start_date_roza']);
    $data['start_date_qaida'] 			= $tool->ChangeDateFormat($_POST['start_date_qaida']);
    $data['start_date_nazra'] 			= $tool->ChangeDateFormat($_POST['start_date_nazra']);
    $data['start_date_hifz'] 			= $tool->ChangeDateFormat($_POST['start_date_hifz']);
    $data['end_date_hifz'] 			    = $tool->ChangeDateFormat($_POST['end_date_hifz']);
    $data['end_date_hifz_hijri'] 		= $tool->ChangeDateFormat($_POST['end_date_hifz_hijri']);



    if($_POST['tableid'] == 0){
        $_SESSION['msg'] = $tool->Message("succ","Record inserted.");
        $hfz->insertProgress($data);
    }
    else{
        $_SESSION['msg'] = $tool->Message("succ","Record updated.");
        $hfz->updateProgress($data,$tool->GetInt($_POST['tableid']));

    }

    $tool->Redir("hifz","hifzprogressform","","");
    exit;

}



$tid="";
$age_april="";
$first_admission_class="";
$hifz_duration="";
$girdan_duration="";
$branch = "";
$street="";
$teacher="";
$overall_progress="";
$second_term_total="";
$second_term_numbers="";
$second_term_percent="";
$third_term_total="";
$third_term_numbers="";
$third_term_percent="";
$wifaq_total="";
$wifaq_numbers="";
$wifaq_percent="";


$type_attand="";
$parents_behave="";
$fixed_fees="";
$fees_discount="";
$discount_type="";
$any_specail_reason="";
$start_date_roza="";
$start_date_qaida="";
$start_date_nazra="";
$start_date_hifz="";
$end_date_hifz="";
$end_date_hifz_hijri="";

$tpl->renderBeforeContent();
$qr->searchContentAbove();


?>


<p id="student_res">&nbsp;</p>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id")?></label><input value="<?php if(isset($_GET['student_id'])) echo $_GET['student_id'] ?>" type="text" name="student_id" id="student_id"></div>

    <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
<div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

<div class="span3"><label>&nbsp;</label>&nbsp;</div>



</div>

    <div class="row-fluid">

    </div>


<?php

$qr->searchContentBottom();

if(isset($_GET['_chk']) == 1){

    $sessionStartDate = $session['start_date'];
    $sessionEndDate = $session['end_date'];
    $sessionId = $session['id'];











?>
    <form action="" method="post">
        <?php echo $tpl->formHidden() ?>
        <input type="hidden" name="id" value="<?php echo $student_id ?>">

    <div role="grid" class="dataTables_wrapper form-inline" id="editable_wrapper">
    <table class="table table-bordered table-striped table-hover">

       <tr><td colspan="6" class="fonts">
               <div class="alert alert-info" style="font-size: 20px; ">
                   <strong style="font-family: 'Jameel Noori Nastaleeq'">تکمیل حفظ فارم</div>
       </td>
       </tr>

        <?php

        if (isset($_GET['_chk']) == 1 && !empty($student_id)) {

            if (empty($student_id)) {
                echo $tool->Message("alert","آئی ڈی منتخب کریں");
                exit;
            }

            $rowComplete = $hfz->CheckCompleteStudent(array("id" => $student_id));
            if(!empty($rowComplete) && is_array($rowComplete)){
                extract($rowComplete);
                $tableId = $rowComplete['id'];
            }


            $row = $hfz->hifzCompleteStudents(array("id" => $student_id));
            extract($row);


            if(empty($third_term_total)){
                $finalExamData = $exm->getHifzProgressRes($student_id,$sessionId,3);
                if(!empty($finalExamData)){
                    $third_term_total = $finalExamData['subject_numbers'];
                    $third_term_numbers = $finalExamData['obtain_numbers'];
                    $finalPercent = ($third_term_numbers / $third_term_total) * 100;
                    $finalPercentFormated = number_format($finalPercent,2);
                    $third_term_percent = $exm->GetKefyatId($exm->handelFloat($finalPercentFormated));
                    $branch = $finalExamData['branch_id'];
                }
            }




            if(empty($second_term_total)){
                $secondExamData = $exm->getHifzProgressRes($student_id,$sessionId,2);

                if(!empty($secondExamData)){
                    $second_term_total = $secondExamData['subject_numbers'];

                    $second_term_numbers = $secondExamData['obtain_numbers'];
                    $secondPercent = ($second_term_numbers / $second_term_total) * 100;
                    $secondPercentFormated = number_format($secondPercent,2);
                    $second_term_percent = $exm->GetKefyatId($exm->handelFloat($secondPercentFormated));

                }
            }


            if(empty($overall_progress)){
                $monthlyTestData = $test->getIdTotal($student_id,$sessionId);
                $mnthlyPercent = ($monthlyTestData['obtain_numbers'] / $monthlyTestData['subject_numbers']) * 100;
                $mnthlyFrmated = number_format($mnthlyPercent,2);

                $mnthlyTestPercent = str_replace(".","",$mnthlyFrmated);
                $overall_progress = $hfz->HifzKefyatBetween($mnthlyTestPercent);
            }




            $dateOfBirth = $row['date_of_birth'];
            $dateOfBirthAr = explode("-",$dateOfBirth);
            if(empty($age_april)){

                $datetime1 = new DateTime($dateOfBirth);
                $datetime2 = new DateTime('1 April ' . date("Y"));
                $interval = $datetime1->diff($datetime2);
                $age_april = $interval->format('%y سال %m ماہ %d دن');

                //$age_april = date("Y") - $dateOfBirthAr[0];
            }






            if(empty($type_attand)){
                $resAttands = $atd->atdStudentReport($student_id,$sessionStartDate,$sessionEndDate);

                $resAttandSum = $atd->countNumberOfAttanbdDays($sessionStartDate,$sessionEndDate,$row['branch_id'],$row['class_id']);

                $absent = 0;



                foreach($resAttands as $rowAttands) {
                    /*if ($rowAttands['attand'] == 2 || $rowAttands['attand'] == 3) {
                        $absent++;
                    }*/

                    if ($rowAttands['attand'] == 2) {
                        $absent++;
                    }
                }

                $attandPercent = ( ($resAttandSum - $absent) * 100) / $resAttandSum;
                $type_attandPercent = number_format($attandPercent,2);


                if($type_attandPercent >= 90){
                    $type_attand = "بہترین";
                }
                else if($type_attandPercent >= 80){
                    $type_attand = "بہتر";
                }
                else if($type_attandPercent >= 70){
                    $type_attand = "مناسب";
                }
                else if($type_attandPercent < 70){
                    $type_attand = "کمزور";
                }
                else{
                    $type_attand = "";
                }


                //echo '<pre>'; print_r($attandPercent); echo '</pre>';
                //echo '<pre>'; print_r($resAttandSum); echo '</pre>';
                //echo '<pre>'; print_r($type_attand); echo '</pre>';

            }

            $params['branch'] = $row['branch_id'];
            $params['class'] = $row['class_id'];
            $params['session'] = $row['session_id'];
            $params['fee_type'] = array(1);


            if(empty($fixed_fees)){
                $moduleFee = $fee->getBranchFees(($params));
                $fixed_fees = $moduleFee[0]['fees'];
            }

            if(empty($fees_discount)){
                $discuntFee = $fee->discountsList(array("id" => $student_id));
                if(!empty($discuntFee)){
                    $fees_discount = $discuntFee[0]['amount'];
                    $discount_type = $discuntFee[0]['ref_title'];
                }
            }




        ?>
        <tr><td colspan="6" class="fonts">
               <a class="no-print" target="_blank" href="<?php echo $tool->makeLink("hifz","reportprogressform&id=".$student_id,"","") ?>" title="Hifz">
                       <img src="<?php echo $tool->getWebUrl() ?>/img/viewicon.png" width="20" height="20"></a>
       </td>
       </tr>
        <?php } ?>
       <tr>
           <td width="150" class="fonts" style="font-size: 18px;">نام</td>
           <td width="150"><input value="<?php echo $name; ?>"  type="text" name="name" id="name" required="required"/></td>
           <td width="150" class="fonts" style="font-size: 18px;">ولدیت</td>
           <td width="150"><input value="<?php echo $fname; ?>"  type="text" name="fname" id="fname" required="required" /></td>

           <td width="150" class="fonts" style="font-size: 18px;">شناخت نمبر</td>
           <td width="150"><input value="<?php echo $grnumber; ?>"  type="text" name="gr" id="gr" required="required" /></td>
       </tr>
       <tr>
           <td class="fonts" style="font-size: 18px;">پتہ</td>
           <td colspan="5"><input value="<?php echo $current_address; ?>" style="width: 96%" type="text" name="current_address" id="current_address"/></td>

           </tr>

       <tr>
                  <td class="fonts" style="font-size: 18px;">علاقہ</td>
                  <td><input value="<?php echo $sreet; ?>"  type="text" name="sreet" id="sreet" /></td>
                  <td class="fonts" style="font-size: 18px;">فون نمبر گھر</td>
                  <td><input value="<?php echo $home_fone; ?>"  type="text" name="bloud_group" id="bloud_group"/></td>
                  <td class="fonts" style="font-size: 18px;">دفتر/موبائل</td>
                  <td><input value="<?php echo $amergency_mobile; ?>"  type="text" name="home_fone" id="home_fone"/></td>
              </tr>

       <tr>
                  <td class="fonts" style="font-size: 18px;">تاریخ پیدائش</td>
                  <td colspan="3" class="fonts">سال  <?php echo $dateOfBirthAr[0] ?> مہینہ <?php echo $dateOfBirthAr[1] ?> دن <?php echo $dateOfBirthAr[2] ?></td>
                  <td class="fonts" style="font-size: 18px;">یکم اپریل <?php echo date("Y") ?> کو عمر کتنی ہے؟</td>
                  <td><input value="<?php echo $age_april ?>"  type="text" name="age_april" id="age_april" /></td>
              </tr>


        <tr>
            <td colspan="2" class="fonts">اقراء میں سب سے پہلے داخلہ کس شعبہ میں ہوا</td>
            <td colspan="2">


                <select name="first_admission_class" id="first_admission_class">
                    <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("classes"), "sel" => $first_admission_class)); ?>

                  </select>


            </td>
            <td class="fonts">تاریخ داخلہ</td>
            <td><input type="text" name="doa" class="date" value="<?php echo $tool->ChangeDateFormat($doa) ?>"></td>
        </tr>


        <tr><td colspan="6" class="fonts">
               <div class="alert alert-info" style="font-size: 20px; ">
                   <strong style="font-family: 'Jameel Noori Nastaleeq'">مراحل ترقی</div>
       </td> </tr>


        <tr>
            <td class="fonts" style="font-size: 18px;">(تاریخ آغاز) روضہ:</td>
            <td><input type="text" name="start_date_roza" id="start_date_roza" class="date" value="<?php
                if(!empty($start_date_roza)){
                    echo $tool->ChangeDateFormat($start_date_roza);
                }
                ?>"></td>
            <td class="fonts" style="font-size: 18px;">(تاریخ آغاز) قاعدہ:</td>
            <td><input type="text" name="start_date_qaida" id="start_date_qaida" class="date" value="<?php
                if(!empty($start_date_qaida)){
                    echo $tool->ChangeDateFormat($start_date_qaida);
                }
                ?>"></td>
            <td class="fonts" style="font-size: 18px;">(تاریخ آغاز) ناظرہ:</td>
            <td><input type="text" name="start_date_nazra" id="start_date_nazra" class="date" value="<?php
                if(!empty($start_date_nazra)){
                    echo $tool->ChangeDateFormat($start_date_nazra);
                }
                 ?>"></td>
        </tr>

        <tr>
            <td class="fonts" style="font-size: 18px;">تاریخ آغاز حفظ:</td>
            <td><input type="text" name="start_date_hifz" id="start_date_hifz" class="date" value="<?php
                if(!empty($start_date_hifz)){
                    echo $tool->ChangeDateFormat($start_date_hifz);
                }
                 ?>"></td>
            <td class="fonts" style="font-size: 18px;">تاریخ تکمیل حفظ :</td>
            <td><input type="text" name="end_date_hifz" id="end_date_hifz" class="date" value="<?php
                if(!empty($end_date_hifz)){
                    echo $tool->ChangeDateFormat($end_date_hifz);
                }
                 ?>"></td>
            <td class="fonts" style="font-size: 18px;">ہجری تاریخ:</td>
            <td><input type="text" name="end_date_hifz_hijri" id="end_date_hifz_hijri" placeholder="01-01-1439" value="<?php
                if(!empty($end_date_hifz_hijri)){
                    echo $tool->ChangeDateFormat($end_date_hifz_hijri);
                }
                 ?>"></td>
        </tr>



        <!--<tr>
          <td class="fonts" style="font-size: 18px;">تکمیل حفظ کی مدت</td>
          <td><input value="<?php /*//echo $hifz_duration */?>" disabled="disabled" readonly="readonly" type="text" name="hifz_duration" id="hifz_duration" /></td>
          <td class="fonts" style="font-size: 18px;">گردان کی مدت</td>
          <td colspan="3"><input value="<?php /*echo $girdan_duration */?>"  type="text" name="girdan_duration" id="girdan_duration"/></td>
      </tr>-->


        <tr>
          <td class="fonts" style="font-size: 18px;">شاخ</td>
          <td><select name="branch" id="branch">
                              <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("branches"), "sel" => $branch)); ?>

                            </select></td>
          <td class="fonts" style="font-size: 18px;">علاقہ</td>
          <td><input value="<?php echo $sreet; ?>"  type="text" name="street" id="street"/></td>
          <td class="fonts" style="font-size: 18px;">معلم/معلمہ</td>
          <td><input value="<?php echo $teacher ?>"  type="text" name="teacher" id="teacher"/></td>
      </tr>

        <tr>
            <td class="fonts">طالب علم/طالبہ کی مجموعی کیفیت</td>
            <td colspan="5"><input type="text" value="<?php if(!empty($overall_progress)) echo $overall_progress; ?>" name="overall_progress" id="overall_progress"></td>
        </tr>


        <tr><td colspan="6" class="fonts">
               <div class="alert alert-info" style="font-size: 20px; ">
                   <strong style="font-family: 'Jameel Noori Nastaleeq'">تکمیل حفظ کے سال کے نتائج</div>
       </td> </tr>



        <tr>
              <td class="fonts" style="font-size: 18px;">ششماہی امتحان کل نمبرات </td>
              <td><input value="<?php echo $second_term_total ?>"  type="text" name="second_term_total" id="second_term_total" /></td>
              <td class="fonts" style="font-size: 18px;">ششماہی حاصل کردہ نمبرات</td>
              <td><input value="<?php echo $second_term_numbers ?>"  type="text" name="second_term_numbers" id="second_term_numbers"/></td>
              <td class="fonts" style="font-size: 18px;">درجہ کامیابی</td>
              <td>
                  <select name="second_term_percent" id="second_term_percent">
                    <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("hifz_ranks"), "sel" => $second_term_percent)); ?>
                  </select>

              </td>
          </tr>



        <tr>
              <td class="fonts" style="font-size: 18px;">سالانہ  امتحان کل نمبرات </td>
              <td><input value="<?php echo $third_term_total ?>"  type="text" name="third_term_total" id="third_term_total" /></td>
              <td class="fonts" style="font-size: 18px;">سالانہ حاصل کردہ نمبرات</td>
              <td><input value="<?php echo $third_term_numbers ?>"  type="text" name="third_term_numbers" id="third_term_numbers"/></td>
              <td class="fonts" style="font-size: 18px;">درجہ کامیابی</td>
              <td>
                  <select name="third_term_percent" id="third_term_percent">
                      <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("hifz_ranks"), "sel" => $third_term_percent)); ?>
                  </select>

              </td>
        </tr>


        <tr>
              <td class="fonts" style="font-size: 18px;">وفاق  امتحان کل نمبرات </td>
              <td><input type="text" value="<?php echo $wifaq_total ?>" name="wifaq_total" id="wifaq_total" /></td>
              <td class="fonts" style="font-size: 18px;">وفاق حاصل کردہ نمبرات</td>
              <td><input type="text" value="<?php echo $wifaq_numbers ?>" name="wifaq_numbers" id="wifaq_numbers"/></td>
              <td class="fonts" style="font-size: 18px;">درجہ کامیابی</td>
              <td>
                  <select name="wifaq_percent" id="wifaq_percent">
                      <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("hifz_ranks"), "sel" => $wifaq_percent)); ?>



                  </select>

              </td>
        </tr>


        <tr>
            <td class="fonts" style="font-size: 18px;">حاضری کی نوعیت</td>
            <td colspan="3" class="fonts"><input type="text" value="<?php echo $type_attand ?>" name="type_attand" id="type_attand"></td>
            <td class="fonts" style="font-size: 18px;">والدین کا رویہ</td>
            <td class="fonts"><input type="text" value="<?php echo $parents_behave ?>" name="parents_behave" id="parents_behave"></td>
        </tr>


        <tr>
            <td class="fonts" style="font-size: 18px;">مقررہ فیس</td>
            <td class="fonts"><input type="text" value="<?php echo $fixed_fees ?>" name="fixed_fees" id="fixed_fees"></td>
            <td class="fonts" style="font-size: 18px;">اگر رعایت تھی تو کس قدر</td>
            <td class="fonts"><input type="text" value="<?php echo $fees_discount ?>" name="fees_discount" id="fees_discount"></td>
            <td class="fonts" style="font-size: 18px;">رعایت کی نوعیت</td>
            <td class="fonts"><input value="<?php echo $discount_type ?>" type="text" name="discount_type" id="discount_type"></td>
        </tr>

        <tr>
            <td class="fonts" style="font-size: 18px;">کوئی خاص بات</td>
            <td colspan="5"><input type="text" value="<?php echo $any_specail_reason ?>" name="any_specail_reason" id="any_specail_reason"></td>
        </tr>


        <tr>
            <td colspan="6" style="text-align: center">
                <button data-loading-text="sending info..." class="btn btn-primary" type="submit" id="submit-button">Save</button>
                <button class="btn btn-danger" type="reset" id="cancel-button">Reset</button>

            </td>
        </tr>


<input type="hidden" value="<?php if(isset($tableId) && $tableId > 0) echo $tableId; else echo 0; ?>" name="tableid" id="tableid">

   </table>
    </div>
</form>

<?php
}
$tpl->footer();
