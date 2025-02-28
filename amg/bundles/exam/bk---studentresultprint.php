<?php
$singleResult = false;
$one  = '';
$two = '';
$three = '';



/*if(isset($_GET['exam_name']) && !empty($_GET['exam_name'])){
    $bulk = true;
}
else{
    $bulk = false;
}*/
    /*$subjectExamBulk = json_decode(urldecode($_GET['exams']),true);
    $exam_count = count(array_filter($subjectExamBulk));
    $subject_exam2 = implode(",",$subjectExamBulk);
    $totalExamIds = trim($subject_exam2,',');*/



if(!@$bulk){
    $countSubmitExam = array_filter($_GET['exams']);
    $exam_count = count($countSubmitExam);
    $subject_exam2 = implode(",",$_GET['exams']);
    $totalExamIds = trim($subject_exam2,',');
    $result_session = $tool->GetInt($_GET['result_session']);
    $exam = $tool->GetInt($_GET['current_exam']);
    $template = isset($_GET['template']) ? $_GET['template'] : '';
    $student_id = $tool->GetInt($_GET['student_id']);
    $branch = (isset($_GET['branch'])) ? $tool->GetInt($_GET['branch']) : '';
    $class = (isset($_GET['class'])) ? $tool->GetInt($_GET['class']) : '';
    $section = (isset($_GET['section'])) ? $tool->GetInt($_GET['section']) : '';
    $session = (isset($_GET['result_session'])) ? $tool->GetInt($_GET['result_session']) : '';

    Tools::getLib("HijriConvert");
    Tools::getModel("ExamModel");
    Tools::getLib("TemplateForm");
    $tpf = new TemplateForm();
    $exm = new ExamModel();
    $DateConv= new HijriConvert;

    Tools::getModel("StudentsModel");
    Tools::getModel("AttendanceModel");
    $stu = new StudentsModel();
    $atd = new AttendanceModel();


    function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? 1 : -1;
    }
}



if (in_array(1, $countSubmitExam)){
  $one = "yes";
}
else{
  $one  = 'no';
}

if (in_array(2, $countSubmitExam)){
  $two = "yes";
}
else{
  $two  = 'no';
}

if (in_array(3, $countSubmitExam)){
  $three = "yes";
}
else{
  $three  = 'no';
}







if(empty($student_id)
        || empty($branch)
        || empty($class)
        || empty($section)
        || empty($session)
        || empty($exam)
        || empty($template)
        || empty($result_session )
        || empty($countSubmitExam) ){

    echo $tool->Message("alert","تمام فیلڈز کو پر کریں");
exit;
}


$progress = $exm->IdProgress($student_id);

if(empty($progress)){
    echo $tool->Message("alert","طالب علم کی کیفیت کا اندراج کریں۔");
    exit;
}

switch($exam_count){
    case 1:
        $sub_col = '40%';
        $num_col = '30%';
		$colspan = 3;
    break;

    case 2:
        $sub_col = '30%';
        $num_col = '23%';
		$colspan = 4;
    break;

    case 3:
        $sub_col = '25%';
        $num_col = '20%';
		$colspan = 5;
    break;
}








$format="YYYY-mm-dd";



$heading_keys = $exm->ExamHeadings();

$examBranchParam = array();
$examClassParam = array();
$examSectionParam = array();
$examSessionParam = array();

$res = $exm->SelectIDresult($student_id,$totalExamIds,$result_session);

foreach ($res as $row){
    $examBranchParam[$row['exam_exam']]['branch_id'] = $row['branch_id'];
    $examClassParam[$row['exam_exam']]['class_id'] = $row['class_id'];
    $examSectionParam[$row['exam_exam']]['section_id'] = $row['section_id'];
    $examSessionParam[$row['exam_exam']]['session_id'] = $row['session_id'];
}



$param = array(
   "branch" => $examBranchParam[$exam]['branch_id'],
   "class" => $examClassParam[$exam]['class_id'],
   "section" => $examSectionParam[$exam]['section_id'],
   "session" => $examSessionParam[$exam]['session_id'],
   "exam" => $exam
);

$resDate = array();
$resDateArr = $exm->examDateLogs($param);




if(!empty($resDateArr)){
    $resDate = $resDateArr[0];
}

if(count($resDate)==0){
    echo $tool->Message("alert",("Exam Date Log Not Inserted"));
    return;
}



$date = $resDate['exam_start_date'];
$exam_end_date = $resDate['exam_end_date'];
$attand_start_date = $resDate['attand_start_date'];
$attand_end_date = $resDate['attand_end_date'];
$session_end_date = $resDate['session_end_date'];
$year = $resDate['year'];
$display_year = $resDate['display_year'];

$dateExplode = explode("-",$date);
$easwyYear = $dateExplode[0];



$hijri_date = $DateConv->GregorianToHijri($date,$format);
$hijri_date_arr = explode("-",$hijri_date);
$hijri_year = $hijri_date_arr[2];
$hijri_month = $tpf->IslamicMonthName($hijri_date_arr[1]);

$row_id_syllabus = $exm->GetIdSyllabus($student_id,$exam,$attand_start_date,$session_end_date);

$row_label = $exm->GetExamLabel($examClassParam[$exam]['class_id'],$exam);




if(count($res) < 1){
    echo $tool->Message("alert","No Result Found.");
return;
}


switch ($exam) {
    case 1:
        $exam_name_dis = 'سہ ماہی';
        break;
    case 2:
        $exam_name_dis = 'ششماہی';
        break;
    case 3:
        $exam_name_dis = 'سالانہ';
        break;
}




$row_students = $stu->studentSearch(array("id"=>$student_id));
$row_stu = $row_students[0];



$totalSubjectNumbers = '';
$totalObtainedNumbers = '';


$subject_ids = array();
$subject_names = array();
$subject_numbers = array();
$obtained_numbers = array();
$teacherIdByExam = array();
$positionByExam = array();
$subject_groups = array();
$subjectGroupNumbers = array();
$examGroupTotalObtainedNumbers = array();
$totalExamWiseSubjectNumbers = array();
$allGroupSubjectNumbers = '';
$termWiseObtainedNumbers = array();
$termPercentage = array();
$countStudentByExam = array();
$examYearArrs = array();
$examMonthsArrs = array();
$examDateHijriYear = array();
$examDateHijriMonth = array();
$examModuleId = array();
$examBranchId = array();
$total_attand = array();
$student_attand = array();
$attanStartDate = array();
$attanEndDate = array();


$incr = 0;

foreach($res as $row) {
    $incr++;
    $examDateArr = explode("-",$row['exam_date']);
    @$totalSubjectNumbers += $row['subject_number'];
    @$totalObtainedNumbers += intval($row['exam_numbers']);
    $subject_names[$row['subject_group']][$row['exam_subjectid']] = $row['subject_name'];
    $subject_numbers[$row['subject_group']][$row['exam_subjectid']] = $row['subject_number'];
    $totalExamWiseSubjectNumbers[$row['main_sub']][$row['exam_exam']][$row['exam_subjectid']] = $row['subject_number'];
    $termWiseObtainedNumbers[$row['main_sub']][$row['exam_exam']][$row['exam_subjectid']] = $row['exam_numbers'];
    $teacherIdByExam[$row['exam_exam']] = $row['section_id'];
    $examYearArrs[$row['exam_exam']] = $examDateArr[0];
    $examMonthsArrs[$row['exam_exam']] = $examDateArr[1];
    $examModuleId[$row['exam_exam']] = $row['class_id'];
    $examBranchId[$row['exam_exam']] = $row['branch_id'];
    $subject_groups[$row['subject_group'] ][]=  $row['exam_subjectid'];
    $subjectGroupNumbers[$row['subject_group']][$row['exam_subjectid']] = $row['subject_number'];
    $obtainedGroupNumbers[$row['subject_group']][$row['exam_subjectid']][$row['exam_exam']] = $row['exam_numbers'];
    $examGroupTotalObtainedNumbers[$row['subject_group']][$row['exam_exam']][$row['exam_subjectid']] = $row['exam_numbers'];
}



foreach($countSubmitExam as $keyExam => $valExam){



    $res_pos = $exm->RankFunction($valExam, $result_session, $examBranchParam[$valExam]['branch_id'], $examClassParam[$valExam]['class_id'],$examSectionParam[$valExam]['section_id']);
    //$res_pos = $rs->RankFunction($date, $to_date, $valExam, $teacher_id);
    $countStudentByExam[$valExam] = count($res_pos);

    foreach ( $res_pos as $row_pos) {
      $alltotals[$valExam][$row_pos['exam_stuid']] = $row_pos['marks_student'];

    }


    uasort($alltotals[$valExam], "cmp");
    $a = 1;
    $prevval = -1;

    foreach ($alltotals[$valExam] as $key => $val) {


      if ($prevval != -1 && $prevval != $val)
          $a++;
      if ($student_id == $key) {
          $positionByExam[$valExam] = $a;

      }
      $prevval = $val;
    }




    //$rowExamDateLog = $exm->getExamDateLog(array("exam" => $valExam, "exam_year" => $examYearArrs[$valExam], "branch" => $examBranchId[$valExam], "class" => $examModuleId[$valExam]));
    //$examDateHijriArr = explode("-",$rowExamDateLog['exam_end_date']);
    //$examDateHijriYear[$valExam] = $examDateHijriArr[0];
    //$examDateHijriMonth[$valExam] = $examDateHijriArr[1];
    //$attanStartDate[$valExam] = $rowExamDateLog['attand_start_date'];
    //$attanEndDate[$valExam] = $rowExamDateLog['attand_end_date'];



}


$total_attand[$exam] = $atd->CountNumberOfAttanbdDays($attand_start_date,$attand_end_date,$examBranchId[$exam],$examModuleId[$exam]);
$row_atd = $atd->IDTotalAttandBetweenDate($student_id,$attand_start_date,$attand_end_date);
$student_attand[$exam] = $total_attand[$exam] - ($row_atd['absent'] + $row_atd['rukhsat']);


if($exam == 3){

$paramSecond = array(
   "branch" => $examBranchParam[2]['branch_id'],
   "class" => $examClassParam[2]['class_id'],
   "section" => $examSectionParam[2]['section_id'],
   "session" => $examSessionParam[2]['session_id'],
   "exam" => 2
);

$resDateSecond = array();
$resDateArrSecond = $exm->examDateLogs($paramSecond);

if(!empty($resDateArrSecond)){
    $resDateSecond = $resDateArrSecond[0];
}

$attand_start_date_second = $resDateSecond['attand_start_date'];
$attand_end_date_second = $resDateSecond['attand_end_date'];


    $total_attand[2] = $atd->CountNumberOfAttanbdDays($attand_start_date_second,$attand_end_date_second,$examBranchId[2],$examModuleId[2]);
    $row_atd_second = $atd->IDTotalAttandBetweenDate($student_id,$attand_start_date_second,$attand_end_date_second);
    $student_attand[2] = $total_attand[2] - ($row_atd_second['absent'] + $row_atd_second['rukhsat']);

}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>ID#<?php  echo $student_id  ?> Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

      <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-rtl.css">
      <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-responsive-rtl.css">


      <style>
          body {direction: rtl; font-family: "Jameel Noori Nastaleeq"}
          .font{font-family: "Jameel Noori Nastaleeq"}
          .green_bg{background:#063 !important; -webkit-print-color-adjust: exact; color:#FFF !important; -webkit-print-color-adjust: exact; font-size:18px; border-left:#FFF 1px solid !important;  text-align:center !important;}
          .numbers{font-family: Arial; font-weight: bold; text-align: center}
		  .table, .table tr, .table td {border-color: black;}
.h1heading{text-align:center; font-size:22px; text-align:center !important;}
.green_bg_small{height: 7px !important; line-height: 7px !important; background:#063 !important; -webkit-print-color-adjust: exact; color:#FFF !important; -webkit-print-color-adjust: exact; font-size:15px; border-left:#FFF 1px solid !important;  text-align:center !important;}
.green_numbers{height: 7px !important; line-height: 7px !important; text-align:center !important; font-weight:bold;}
.numbers_td{height: 6px !important; line-height: 6px !important; text-align:center !important; font-size:15px; font-family:Arial; font-weight:bold}
.names_td{height: 6px !important; line-height: 6px !important; text-align:center !important; font-size:15px; font-family:Arial; font-weight:bold; font-family:"Jameel Noori Nastaleeq"}


          @media print {
           .page-break  { display: block; page-break-before: always; }
          }

      </style>


  </head>


  <body>





<div id="printReady">
  <table border="0" style="width: 750px; margin: 0 auto; padding: 0 !important; border-spacing: 0; border:none !important;" cellpadding="0" cellspacing="0">

  		<tr>
        <td>&nbsp;</td>
        	<td>
            <table style="width:100% !important">
            <tr style="border:none !important;">
		            <td style="border:none !important;">&nbsp;</td>
                    <td style="border:none !important;"><img src="<?php echo $tool->getWebUrl() ?>/img/logo2.png" width="57" height="59" /></td>

                    <td style="border:none !important;" class="numbers">&nbsp;</td>

                    <td style="border:none !important;">&nbsp;</td>
                    <td style="border:none !important;">&nbsp;</td>

                    <td style="border:none !important;">&nbsp;</td>
                    <td style="border:none !important;">&nbsp;</td>
                    <td style="border:none !important;"><img style="margin-right:70px" src="<?php echo $tool->getWebUrl() ?>/img/logo_report.png" height="44" width="241" /></td>
                  </tr>
                 </table>
                  </td>
           <td>&nbsp;</td>
        </tr>
      <tr>
          <td style="width: 2%; height: 7px !important; line-height: 7px !important; border:none !important;">&nbsp;</td>
          <td style="width: 98%; height: 7px !important; line-height: 7px !important; border:none !important;">
              <table class="table table-bordered">

                  <tr>
                    <td class="green_bg" colspan="2">نتائج امتحان <?php echo $exam_name_dis; ?></td>
	            <td>&nbsp;&nbsp;منعقدہ&nbsp;&nbsp;&nbsp;<?php echo $tpf->UrduMonthName($examMonthsArrs[$exam]) .  '' . $examYearArrs[$exam];  ?></td>
                    <td>بمطابق <?php //echo $hijri_month ?>&nbsp;&nbsp;<?php echo $hijri_month ?> <?php echo $hijri_year ?> ھ</td>

                    <td class="green_bg">شعبہ</td>
                    <td style="font-size:18px"><?php echo $row_stu['class_title']; ?></td>
                    <td class="green_bg">سال</td>
                    <td style="font-size:18px"><?php echo $easwyYear; ?>ء</td>
                  </tr>
                  <tr>
	                  <td style="font-size:16px;">کمپیوٹر آئی ڈی:</td>
                      <td class="numbers" style="border-right: none !important;"><?php echo $row_stu['id']; ?></td>
                      <td style="font-size:16px">رجسٹریشن نمبر:</td>
                      <td class="numbers" style="border-right: none !important;"><?php echo $row_stu['grnumber']; ?></td>
                      <td style="font-size:16px"><?php if($row_stu['gender'] == 2){
						echo 'نام طالبہ';
					}else{
						echo 'نام طالب علم';
					}
					?>:</td>
                      <td style="font-size:16px; border-right: none !important;"><?php echo $row_stu['name']; ?></td>
                      <td style="font-size:16px">ولدیت:</td>
                      <td style="font-size:16px; border-right: none !important;"><?php echo $row_stu['fname']; ?></td>

                  </tr>
                  <tr>
                    <td style="font-size:14px">تاریخ پیدائش:</td>
                    <td class="numbers" style="border-right: none !important; font-size: 12px;"><?php $row_dates = $stu->RozaStudentDates($student_id); echo $tool->ChangeDateFormat($row_dates['date_of_birth']); ?></td>
                    <td style="font-size:14px">تاریخ داخلہ:</td>
                    <td class="numbers" style="border-right: none !important; font-size: 12px;"><?php echo $tool->ChangeDateFormat($row_dates['doa']); ?></td>
                    <td style="font-size:16px">کلاس:</td>
                    <td class="numbers" style="border-right: none !important;"><?php echo $row_stu['section_title']; ?></td>
                    <td style="font-size:16px">شاخ:</td>
                    <td style="font-size:13px; border-right: none !important;"><?php echo $row_stu['branch_title']; ?></td>
                </tr>
              </table>




			  <?php
                if($template == 'full'){
					$cosplanGap = $exam_count + 2;
                ?>

				<table class="table table-bordered">

                <tr>
                    <td rowspan="2" colspan="<?php echo $exam_count ?>" style="color: #006600; vertical-align: middle; font-size: 17px; text-align: center">مقدار خواندگی</td>
                    <td style="color: #006600; font-size: 17px; text-align: center">
                        <?php
                        //if($module == 2) echo 'موجودہ'; else echo 'پارہ';
                        ?>
                        <?php echo $row_label['first'] ?>
                    </td>
                    <td style="color: #006600; font-size: 17px; text-align: center"><?php
                                            //if($module == 2) echo 'مطلوبہ'; else echo 'سورۃ';
                                            ?><?php echo $row_label['second'] ?></td>
                </tr>

                <tr>
                    <td style="text-align: center"><?php echo $row_id_syllabus['required'] ?></td>
                    <td style="text-align: center"><?php echo $row_id_syllabus['current'] ?></td>
                </tr>



				</table>

                <?php } ?>



          </td>
          <td style="height: 7px !important; line-height: 7px !important; width:2%; border:none !important;">&nbsp;</td>
      </tr>


      <tr>
        <td style="height: 7px !important; line-height: 7px !important;">&nbsp;</td>
        <td style="height: 7px !important; line-height: 7px !important;">
            <table class="table table-bordered" cellpadding="0" cellspacing="0" style="width: 100% !important; padding:0; padding: 0;">








            <tr>

			<td style="height: 10px !important; line-height: 10px !important;" width="<?php echo $sub_col ?>" class="green_bg">مضامین</td>

            <td style="height: 10px !important; line-height: 10px !important;" width="<?php echo $num_col ?>" class="green_bg">کل نمبرات</td>

            <?php if($one == 'yes'){ ?>
            	<td style="height: 10px !important; line-height: 10px !important;" width="<?php echo $num_col ?>" class="green_bg">حاصل کردہ نمبرات سہ ماہی</td>
            <?php } ?>

            <?php if($two == 'yes'){ ?>
            	<td style="height: 10px !important; line-height: 10px !important;" width="<?php echo $num_col ?>" class="green_bg">حاصل کردہ نمبرات ششماہی</td>
            <?php } ?>

            <?php if($three == 'yes'){ ?>
            	<td style="height: 10px !important; line-height: 10px !important;" width="<?php echo $num_col ?>" class="green_bg">حاصل کردہ نمبرات سالانہ</td>
            <?php } ?>


            </tr>




                    <?php



              foreach($subject_groups as $groups => $subject_ids){
                  $subject_ids = array_unique($subject_ids);



              ?>


<tr>
                     <td style="height: 10px !important; line-height: 10px !important; text-align: right !important; padding-right: 90px; background: #a8cf45" colspan="<?php echo $colspan ?>" class="h1heading"><?php if(!empty($heading_keys[$groups])) echo $heading_keys[$groups]; else echo $groups; ?></td>
                   </tr>





              <?php



              foreach($subject_ids as $subjectskey => $subjects_val){


              ?>
              			<tr <?php if($template == 'full') { ?> style="height: 30px;" <?php } ?>>

                     <td  width="<?php echo $sub_col ?>" class="names_td" <?php if($template == 'full') { ?> style="font-size: 18px;" <?php } ?>><?php if(!empty($subject_names[$groups][$subjects_val])) echo $subject_names[$groups][$subjects_val] ?></td>

                     <td width="<?php echo $num_col ?>" class="numbers_td" <?php if($template == 'full') { ?> style="font-size: 18px;" <?php } ?>><?php if(!empty($subject_numbers[$groups][$subjects_val])) echo $subject_numbers[$groups][$subjects_val]; else echo '-'; ?></td>


                     <?php if($one == 'yes'){
                    ?>
                        <td class="numbers_td" <?php if($template == 'full') { ?> style="font-size: 17px;" <?php } ?>><?php if(!empty($obtainedGroupNumbers[$groups][$subjects_val][1])) { echo $obtainedGroupNumbers[$groups][$subjects_val][1]; } else { echo '-';} ?></td>
                 	<?php } ?>


                    <?php if($two == 'yes'){

                        ?>
                        <td class="numbers_td" <?php if($template == 'full') { ?> style="font-size: 17px;" <?php } ?>><?php if(!empty($obtainedGroupNumbers[$groups][$subjects_val][2])) { echo $obtainedGroupNumbers[$groups][$subjects_val][2]; } else { echo '-';} ?></td>
                 	<?php } ?>


                    <?php if($three == 'yes'){
                  			?>
                        <td class="numbers_td" <?php if($template == 'full') { ?> style="font-size: 17px;" <?php } ?>><?php if(!empty($obtainedGroupNumbers[$groups][$subjects_val][3])) { echo $obtainedGroupNumbers[$groups][$subjects_val][3]; } else { echo '-'; } ?></td>
                 	<?php } ?>



                 </tr>

<?php }





                    $allGroupSubjectNumbers += array_sum($subjectGroupNumbers[$groups] );

                  ?>





                  <tr>
                      <td class="green_bg_small"  style="font-size: 17px;">میزان</span></td>
                      <td class="numbers_td" style="font-size: 17px;"><?php echo array_sum($subjectGroupNumbers[$groups] ); ?></td>
                      <?php if($one == 'yes'){ ?>
                            <td class="green_numbers"><?php echo array_sum($examGroupTotalObtainedNumbers[$groups][1]) ?></td>
                    <?php } ?>
                    <?php if($two == 'yes'){ ?>
                            <td class="numbers_td"><?php echo array_sum($examGroupTotalObtainedNumbers[$groups][2]); ?></td>
                     <?php } ?>
                     <?php if($three == 'yes'){ ?>
                            <td class="numbers_td"><?php echo array_sum($examGroupTotalObtainedNumbers[$groups][3]); ?></td>
                     <?php } ?>
                    </tr>










<?php } ?>




                <tr>
                    <td class="green_bg_small" style="font-size: 17px;">کل میزان</span></td>
                    <td class="numbers_td" style="font-size: 17px;"><?php echo $allGroupSubjectNumbers; ?></td>
                    <?php if($one == 'yes'){ ?>
                          <td class="green_numbers"><?php echo array_sum($termWiseObtainedNumbers[0][1]) + array_sum($termWiseObtainedNumbers[1][1]); ?></td>
                  <?php } ?>
                  <?php if($two == 'yes'){ ?>
                          <td class="numbers_td"><?php echo array_sum($termWiseObtainedNumbers[0][2]) + array_sum($termWiseObtainedNumbers[1][2]); ?></td>
                   <?php } ?>
                   <?php if($three == 'yes'){ ?>
                          <td class="numbers_td"><?php echo array_sum($termWiseObtainedNumbers[0][3]) + array_sum($termWiseObtainedNumbers[1][3]); ?></td>
                   <?php } ?>
                  </tr>


            </table>

        </td>
        <td style="height: 7px !important; line-height: 7px !important;">&nbsp;</td>
      </tr>

      <?php
      $colsCountKefyat = array();
      $colsCountKefyatCounting = 0;
      if(!$singleResult) {



          $res = $exm->ResultProgress();
            foreach($res as $row){
                $colsCountKefyatCounting++;
                $colsCountKefyat[$row['title']] = $row['title'];
            }

          $kefyat_coll_space = $colsCountKefyatCounting;

      }
      else{
          $kefyat_coll_space = !empty($_GET['title']) ? count($_GET['title']) : array();
      }


      //var_dump($kefyat_coll_space);
	  ?>

      <tr>
         <td>&nbsp;</td>
         <td valign="top" style="vertical-align:top !important">
         	<table class="table table-bordered" style="width: 100% !important; padding:0 !important; padding:0 !important;">
            	<tr <?php if($template == 'full') { ?> style="height: 40px;" <?php } ?>>
                	<td style="vertical-align: middle;" colspan="<?php echo $kefyat_coll_space ?>" class="green_bg_small">طالب علم/طالبہ کی مکمل رپورٹ</td>
                </tr>
                <tr <?php if($template == 'full') { ?> style="height: 30px;" <?php } ?>>
                	<?php
                    if(!$singleResult){

                        if(!empty($progress)){
                            $progress_arr = unserialize($progress['progress']);

                        }else{
                            $progress_arr = '';
                        }



                    //while($row = $db->fetch_array($res)){
                    foreach($colsCountKefyat as $keycolsCountKefyat){
                        echo '<td style="font-size:12px" class="green_numbers">';
                        echo $keycolsCountKefyat;
                        echo '</td>';

                    }

                    }else{

					foreach($_GET['title'] as $key => $val){
                        if($val == 'رفتارِ ترقی'){
                            continue;
                        }
						echo '<td style="font-size:12px" class="green_numbers">';

                    		echo $val;

						echo '</td>';
					?>

                    <?php }

                    }?>
                </tr>
                <tr>

                	<?php

                    if(!$singleResult){
                        //echo '<pre>';print_r($progress_arr );echo '</pre>';
                        $res = $exm->ResultProgress();
                        foreach($res as $row){
                            echo '<td style="font-size:12px" class="green_numbers">';


                            if(!empty($progress_arr[$row['id']]['res']) && $progress_arr[$row['id']]['res'] == $row['result']){
                                echo $row['result'];
                            }
                            elseif(!empty($progress_arr[$row['id']]['res']) && $progress_arr[$row['id']]['res'] == $row['result2']){
                                echo $row['result2'];
                            }
                            elseif(!empty($progress_arr[$row['id']]['res']) && $progress_arr[$row['id']]['res'] == $row['result3']){
                                echo $row['result3'];
                            }
                            else{
                                echo '-';
                            }

                            /*if(!empty($row['result'])){
                                echo $row['result'];
                            }elseif(!empty($row['result2'])){
                                echo $row['result2'];
                            }elseif(!empty($row['result3'])){
                                echo $row['result3'];
                            }else{
                                echo '-';
                            }*/


                            echo '</td>';

                        }

                    }
                    else{


					    foreach($_GET['title'] as $key => $val){

                        if($val == 'رفتارِ ترقی'){
                            continue;
                        }

						echo '<td style="font-size:12px" class="green_numbers">';

                    	if(isset($_GET['opt'][$key]) && !empty($_GET['opt'][$key])){
                    			echo $_GET['opt'][$key];
                		}else{
                    			echo '-';
                		}

						echo '</td>';
					?>
                	<?php }

                    }?>
                </tr>
            </table>
         </td>
         <td>&nbsp;</td>
       </tr>

      <tr>
         <td>&nbsp;</td>
         <td>

             <table class="table table-bordered">

                  <tr>
                        <th class="green_bg"></th>
                        <th class="green_bg">کل نمبرات</th>
                        <th class="green_bg">حاصل کردہ</th>
                        <th class="green_bg">فیصد</th>
                        <th class="green_bg">درجہ کامیابی</th>
                        <th class="green_bg">حاضری</th>
                        <th class="green_bg">پوزیشن</th>
                        <th class="green_bg">مجموعی کیفیت</th>
                  </tr>

                     <?php
                     $alltotalNuberSalana = 0;
                     $allobtainSalana = 0;
                     $allpercentSalana = 0;
                     foreach($countSubmitExam as $keyExam => $valExam){

                         switch ($valExam) {
                             case 1:
                                 $exam_name_dis = 'سہ ماہی';
                                 break;
                             case 2:
                                 $exam_name_dis = 'ششماہی';
                                 break;
                             case 3:
                                 $exam_name_dis = 'سالانہ';
                                 break;
                         }

                         $termPercentage[$valExam] = (array_sum($termWiseObtainedNumbers[0][$valExam]) / array_sum($totalExamWiseSubjectNumbers[0][$valExam])) * 100;

                         $termWisePercentage[$valExam] = number_format($termPercentage[$valExam],2);

                         $alltotalNuberSalana += array_sum($totalExamWiseSubjectNumbers[0][$valExam]);
                         $allobtainSalana += array_sum($termWiseObtainedNumbers[0][$valExam]);
                         $allpercentSalana = number_format(($allobtainSalana / $alltotalNuberSalana) * 100,2);


                         ?>

                  <tr>
                      <td class="green_bg">مجموعی رپورٹ  <?php echo $exam_name_dis ?></td>
                      <td style="text-align: center; font-weight: bold"><?php echo array_sum($totalExamWiseSubjectNumbers[0][$valExam]) ?></td>
                      <td style="text-align: center; font-weight: bold"><?php echo array_sum($termWiseObtainedNumbers[0][$valExam]) ?></td>
                      <td style="text-align: center; font-weight: bold"><?php echo $termWisePercentage[$valExam]; //$current_percentage2 =  (@$shashmahiObtainNumber / @$shashmahiTotalNumber) * 100; echo number_format($current_percentage2,2); $current_percentage2 = round($current_percentage2); //echo @$percentage ?>%</td>
                      <td style="text-align: center; font-weight: bold"><?php echo $exm->numberBetween($exm->handelFloat($termWisePercentage[$valExam])); ?></td>
                      <td style="text-align: center; font-weight: bold">
                          <?php

                          //if($valExam == 3 && $template == "roza"){
                              //if(!empty($_GET['all_hazri'])) echo $_GET['all_hazri'] . '/' . $_GET['hazri'];
                          //}
                          //else{
                              echo $total_attand[$valExam] . '/' . $student_attand[$valExam];
                          //}

                          //if($exam == 3){

                          //}

                          ?>
                      </td>
                      <td style="text-align: center; font-weight: bold">
                          <?php echo $exm->CheckRankCriteria($template,$positionByExam[$valExam],$countStudentByExam[$valExam],$exm->handelFloat($termWisePercentage[$valExam])); //echo $positionByExam[$valExam]; //if($current_percentage2 >= 79) echo $Rankname2; else echo '-'; ?></td>
                      <td><?php

                          if($template == 'roza'){

                              if($valExam == 3){
                                  if ($exm->handelFloat($termWisePercentage[$valExam]) < 7500) {
                                      echo $exm->KefyatBetween(@$termWisePercentage) . '<br />';
                                      echo 'شعبہ قاعدہ میں ترقی روک دی گئی ہے۔';
                                  }else{
                                      echo $exm->KefyatBetween(@$exm->handelFloat($termWisePercentage[$valExam])) . '<br />';
                                      echo 'شعبہ قاعدہ میں ترقی دیدی گئی ہے۔';
                                  }
                              }else{
                                  echo $exm->KefyatBetween(@$exm->handelFloat($termWisePercentage[$valExam]));
                              }

                          }else{
                              echo $exm->KefyatBetween(@$exm->handelFloat($termWisePercentage[$valExam]));
                          }


                           ?></td>

                  </tr>



                 <?php }


                     if($exam == 3){

					 ?>


                         <tr>
                       <td class="green_bg">مجموعی رپورٹ  </td>
                       <td style="text-align: center; font-weight: bold"><?php echo $alltotalNuberSalana ?></td>
                       <td style="text-align: center; font-weight: bold"><?php echo $allobtainSalana ?></td>
                       <td colspan="2" style="text-align: center; font-weight: bold"><?php echo $allpercentSalana ?>%</td>
                       <td colspan="3" style="text-align: center; font-weight: bold"><?php

                           $finalKefyat = $exm->numberBetween($exm->handelFloat($allpercentSalana));

                           if($template == "roza" && $finalKefyat == "راسب"){
                               echo '';
                           }else{
                               echo $finalKefyat;
                           }

                            ?></td>

                   </tr>


                     <?php } ?>






            </table></td>
         <td>&nbsp;</td>
       </tr>

      <tr>
         <td>&nbsp;</td>
         <td>
         	<table cellpadding="0" cellspacing="0" width="100%">

        	<tr>
            	<td colspan="9">&nbsp;</td>
            </tr>



        	<!--<tr>

        	  <td width="5%" style="text-align:right; padding-right:15px"><strong>دستخط معلم/معلمہ</strong></td>
              <td width="12%" style="border-bottom:1px solid #000;">&nbsp;</td>
        	  <td width="7%" style="text-align:right;"><strong>دستخط ناظم/صدر معلمہ</strong></td>
        	  <td width="10%" style="border-bottom:1px solid #000;">&nbsp;</td>

        	  <td width="5%" style="text-align:right; padding-right:35px"><strong>دستخط سرپرست</strong></td>
              <td width="12%" style="border-bottom:1px solid #000;">&nbsp;</td>
          </tr>
		  -->


		  <tr>
        	  <td width="10%" style="text-align:right; padding-right:10px"><strong>دستخط معلم/معلمہ</strong></td>
              <td width="15%" style="border-bottom:1px solid #000;">&nbsp;</td>
        	  <td width="12%" style="text-align:right;"><strong>دستخط ناظم/صدر معلمہ</strong></td>
        	  <td width="13%" style="border-bottom:1px solid #000;">&nbsp;</td>
        	  <td width="12%" style="text-align:right; padding-right:15px"><strong>دستخط سرپرست</strong></td>
              <td width="13%" style="border-bottom:1px solid #000;">&nbsp;</td>
			  <td width="10%" style="text-align:right; padding-right:25px"><strong>شاخ کی مہر</strong></td>
              <td width="15%" style="border-bottom:1px solid #000;">&nbsp;</td>
          </tr>



        </table>
         </td>
         <td>&nbsp;</td>
       </tr>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
  </table>
</div>
  </body>

</html>


