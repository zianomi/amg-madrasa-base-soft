<?php
$student_id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;
if (empty($student_id)) {
    echo $tool->Message("alert","Please select an ID.");
    exit;
}

Tools::getLib("QueryTemplate");
Tools::getModel("HifzModel");
Tools::getModel("ExamModel");
$qr = new QueryTemplate();
$exm = new ExamModel();

$hfz = new HifzModel();
$row_stu = $hfz->printProgress($student_id);
$hr = $hfz->HifzRanks();
$ranks = array();
foreach ($hr as $rank){
    $ranks[$rank['id']] = $rank['title'];
}

if (empty($row_stu)) {
    echo $tool->Message("alert","No result found.");
    exit;
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>ID <?php  echo $student_id  ?> Progress Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

      <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-rtl.css">
      <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-responsive-rtl.css">


      <style>
          body {direction: rtl; font-family: "Jameel Noori Nastaleeq"}

          .green_bg{background:#063 !important; -webkit-print-color-adjust: exact; color:#FFF !important; -webkit-print-color-adjust: exact; font-size:20px; border-left:#FFF 1px solid !important;  text-align:center !important;}
          .green_bg2{background:#eceff6 !important; -webkit-print-color-adjust: exact; color:#5d87ad !important; -webkit-print-color-adjust: exact; font-size:16px; border-left:#FFF 1px solid !important;  text-align:right !important;}
          .numbers{font-family: Arial; font-weight: bold; text-align: center}

          .normal_heading{font-size: 17px; border-bottom: none !important; height: 30px; vertical-align: bottom !important;}

          .td_line{border-bottom: 1px solid #000000 !important; font-size: 18px; height: 30px; vertical-align: bottom !important;}
          .td_full_line{vert-align:middle; border: 1px solid #747273 !important; font-size: 18px; height: 30px; vertical-align: bottom !important;}
          .table, .table tr, .table td {border: 1px solid #ffffff;}




          .date_td_line{border-bottom: 1px solid #000000 !important; font-size: 18px; height: 30px; vertical-align: bottom !important;}
          .normal_date{font-size: 13px; height: 30px; vertical-align: bottom !important;
              font-family: Arial, sans-serif; font-weight: bold; text-align: center !important; border-bottom: 1px solid #000000 !important;}


          @media print {
           .page-break  { display: block; page-break-before: always; }
          }

          .number_heading{font-size: 17px; height: 30px; text-align: center !important; vertical-align: middle !important;}
          table.sample {
          	border-width: 5px;
          	border-spacing: 1px;
          	border-style: double;
          	border-color: gray;
          	border-collapse: separate;
          	background-color: white;
          }

          .table_borderd tr, .table_borderd td{
              border: 1px solid #a09f9f;
        }

          .div_circle {
              border: 1px solid #000; border-radius: 10px; width: 65px; height: 30px !important; text-align: center; vertical-align: middle
          }
          .div_circle_span{display: block; padding-top: 9px; text-align: center;}
          .table{margin-bottom: 7px !important;}
          table th, .table td{line-height: 14px !important;}
          .table{padding: 5px !important;}

      </style>


  </head>


  <body>





<div id="printReady">
   <table border="0" class="sample" style="width: 745px; margin-left: auto; margin-right: auto; padding: 0 !important; border-spacing: 0;" cellpadding="0" cellspacing="0">

   		<tr>
         <td>&nbsp;</td>
         	<td>
             <table style="width:100% !important;">
             <tr>

                     <td style="border:none !important; vertical-align: top; width: 42%;">
                         <img style="" src="<?php echo $tool->getWebUrl() ?>/img/logo_report.png" height="70" width="241" />
                     </td>


                     <td style="border:none !important; text-align: center; width: 25%;">
                         <img style="margin-left: 60px; height: 75px; margin-bottom: 5px;" src="<?php echo $tool->getWebUrl() ?>/img/monogram.png"  />
                     </td>

                        <td style="text-align: left; vertical-align: top; width: 33%;">
                            <span style="font-weight: bold; font-size: 28px;">IQRA </span><br />
                            <span style="font-weight: bold; font-size: 20px;">IQRA RAUZATAL <br />ATFAL TRUST</span>
                        </td>
                    </tr>
                  </table>
                   </td>
            <td>&nbsp;</td>
         </tr>

       <tr>
           <td colspan="3">
                <table class="table">
                    <tr>
                        <td style="width:25%;">
                            <span style="position: absolute; margin-right: 78px; margin-top: 10px;"><?php echo $student_id; ?></span>
                            <img style="" src="<?php echo $tool->getWebUrl() ?>/img/com_id.png" />
                        </td>
                        <td class="green_bg" style="border: 3px solid #000; width:55%; height: 35px; vertical-align: middle;">کارکردگی فارم برائے طلباء وطالبات حفظ</td>
                        <td style="border-right: 3px solid #000; width:20%;">&nbsp;</td>
                    </tr>
                </table>

           </td>
       </tr>
       <tr>



           <td colspan="3" style="border:none !important;">
               <table class="table" style="margin-left: auto; margin-right: auto;">

                   <tr>
                       <td class="normal_heading"><?php if($row_stu['gender'] == 2){
                        						echo 'نام طالبہ';
                        					}else{
                        						echo 'نام طالب علم';
                        					}
                        					?>:</td>
                       <td class="td_line" colspan="2"><?php echo $row_stu['name']; ?></td>

                       <td class="normal_heading">ولدیت:</td>
                       <td class="td_line" colspan="2"><?php echo $row_stu['fname']; ?></td>
                       <td class="normal_heading">رجسٹریشن نمبر:</td>
                       <td class="td_line"><?php echo $row_stu['grnumber']; ?></td>
                   </tr>

                   <tr >
                      <td class="normal_heading">پتہ:</td>
                      <td colspan="7" class="td_line"><?php echo $row_stu['current_address']; ?></td>
                  </tr>

                   <tr>
                       <td class="normal_heading">علاقہ:</td>
                       <td colspan="2"><div class="td_full_line"><span style="padding-top: 5px; display: block"><?php echo $row_stu['street']; ?></span></div></td>
                       <td class="normal_heading" style="ma">فون نمبر گھر:</td>
                       <td class="td_line" colspan="2"><?php echo $row_stu['home_fone']; ?></td>
                       <td class="normal_heading">دفتر/موبائل:</td>
                       <td class="td_line"><?php echo $row_stu['amergency_mobile']; ?></td>
                   </tr>

                   <?php
                  $dateOfBirthArr = explode("-",$row_stu['date_of_birth']);
                  $curYear = date("Y");
                  $aprilAge = $curYear - $dateOfBirthArr[0];



                   if(!empty($row_stu['age_april'])){
                       $disApril = $row_stu['age_april'];
                   }else{
                       $disApril = $aprilAge;
                   }

                   $data['age_april'] = $disApril;


                  ?>

                   <tr>
                       <td rowspan="2" style="vertical-align: middle !important;" class="normal_heading">تاریخ پیدائش:</td>

                       <td class="normal_heading" style="text-align: center">سال</td>

                       <td class="normal_heading" style="text-align: center">مہینہ</td>

                       <td class="normal_heading" style="text-align: center">دن</td>

                       <td class="normal_heading" colspan="3" rowspan="2" style="vertical-align: middle !important;">یکم اپریل <?php echo date("Y") ?> کو عمر کتنی ہے؟</td>
                       <td  rowspan="2" style="vertical-align: middle !important;"><span style="display block; border-bottom: 1px solid #000; width: 100%;"><?php echo $disApril; ?></span></td>

                   </tr>



                   <tr>
                      <td style="text-align: center"><div class="div_circle" >
                              <span class="div_circle_span numbers"><?php echo $dateOfBirthArr[0] ?></span>
                          </div></td>

                      <td style="text-align: center"><div class="div_circle">
                         <span class="div_circle_span numbers"><?php echo $dateOfBirthArr[1] ?></span>
                     </div></td>

                       <td style="text-align: center"><div class="div_circle">
                            <span class="div_circle_span numbers"><?php echo $dateOfBirthArr[2] ?></span>
                        </div></td>

                   </tr>
                   <?php
                   $classArr = $set->getTitleTable("classes", " AND id = "  . $row_stu['first_admission_class'] );
                   $classTitle = $classArr[0]['title'];
                   ?>

                   <tr>
                       <td colspan="3" class="normal_heading">اقراء میں سب سے پہلے داخلہ کس شعبہ میں ہوا:</td>
                       <td class="td_line"><?php echo $classTitle; ?></td>
                       <td colspan="2" class="normal_heading">تاریخ داخلہ:</td>
                       <td colspan="2" class="td_line"><?php echo $tool->ChangeDateFormat($row_stu['doa']) ?></td>
                   </tr>





                   <tr>
                       <td colspan="2" class="green_bg">مراحل ترقی</td>
                       <td colspan="6">&nbsp;</td>
                   </tr>


                   <tr>
                       <td colspan="8">

                           <table style="width: 100% !important;">
                               <tr>
                                  <td class="normal_heading" style="width: 20%;">(تاریخ آغاز) روضہ:</td>
                                  <td class="normal_date" style="width: 17%;"><?php if(isset($row_stu['start_date_roza'])) echo $tool->ChangeDateFormat($row_stu['start_date_roza']); ?></td>
                                  <td class="normal_heading" style="width: 10%;">قاعدہ:</td>
                                  <td class="normal_date" style="width: 23%;"><?php if(isset($row_stu['start_date_qaida'])) echo $tool->ChangeDateFormat($row_stu['start_date_qaida']); ?></td>
                                  <td class="normal_heading" style="width: 10%;"> ناظرہ:</td>
                                  <td class="normal_date" style="width: 20%;"><?php if(isset($row_stu['start_date_nazra'])) echo $tool->ChangeDateFormat($row_stu['start_date_nazra']); ?></td>
                              </tr>
                           </table>

                       </td>
                   </tr>


                   <tr>
                      <td colspan="8">

                          <table style="width: 100% !important;">
                              <tr>
                                 <td class="normal_heading" style="width: 13%;">تاریخ آغاز حفظ:</td>
                                 <td class="normal_date" style="width: 18%;"><?php if(isset($row_stu['start_date_hifz'])) echo $tool->ChangeDateFormat($row_stu['start_date_hifz']); ?></td>
                                 <td class="normal_heading" style="width: 13%;">تاریخ تکمیل حفظ :</td>
                                 <td class="normal_date" style="width: 21%;"><?php if(isset($row_stu['end_date_hifz'])) echo $tool->ChangeDateFormat($row_stu['end_date_hifz']); ?></td>
                                 <td class="normal_heading" style="width: 11%;">ہجری تاریخ:</td>
                                 <td class="normal_date" style="width: 24%;"><?php if(isset($row_stu['end_date_hifz_hijri'])) echo $tool->ChangeDateFormat($row_stu['end_date_hifz_hijri']); ?></td>
                             </tr>
                          </table>

                      </td>
                  </tr>

                   <?php
                   $startDateHifz1 = new DateTime($row_stu['start_date_hifz']);
                   $endDateHifz2 = new DateTime($row_stu['end_date_hifz']);
                   $intervalHifzTotal = $startDateHifz1->diff($endDateHifz2);
                   $hifzDuration = $intervalHifzTotal->format('%y سال %m ماہ %d دن');


                   $girdanStartDate = new DateTime($row_stu['end_date_hifz']);
                   $girdanEndDate = new DateTime('1 April ' . date("Y"));
                   $intervalGirdanTotal = $girdanStartDate->diff($girdanEndDate);
                   $girdanDuration = $intervalGirdanTotal->format('%y سال %m ماہ %d دن');
                   ?>


                   <tr>
                         <td colspan="8">

                             <table style="width: 100% !important;">
                                 <tr>
                                    <td class="normal_heading" style="width: 15%;">تکمیل حفظ کی مدت:</td>
                                    <td class="td_line" style="width: 35%;"><?php echo $hifzDuration //echo $row_stu['hifz_duration'] ?></td>
                                    <td class="normal_heading" style="width: 13%;">گردان کی مدت:</td>
                                    <td class="td_line" style="width: 37Z%;"><?php echo $girdanDuration //echo $row_stu['girdan_duration'] ?></td>
                                 </tr>
                             </table>

                         </td>
                     </tr>



                   <tr>
                     <td colspan="8">

                         <table style="width: 100% !important;">
                             <tr>
                                <td class="normal_heading" style="width: 13%;">شاخ:</td>
                                <td class="td_line" style="width: 18%;"><?php  echo $row_stu['branch_title']; ?></td>
                                <td class="normal_heading" style="width: 13%;">علاقہ:</td>
                                <td class="td_line" style="width: 21%;"><?php echo $row_stu['street']; ?></td>
                                <td class="normal_heading" style="width: 11%;">معلم/معلمہ:</td>
                                <td class="td_line" style="width: 24%;"><?php echo $row_stu['teacher']; ?></td>
                            </tr>
                         </table>

                     </td>
                 </tr>


                   <tr>
                        <td colspan="8">

                            <table style="width: 100% !important;">
                                <tr>
                                   <td class="normal_heading" style="width: 12%;">مجموعی کیفیت:</td>
                                   <td class="td_line" style="width: 88%;"><?php echo $row_stu['overall_progress']; ?></td>
                               </tr>
                            </table>

                        </td>
                    </tr>





                   <tr>
                     <td colspan="2" class="green_bg">تکمیل حفظ کے سال کے نتائج</td>
                     <td colspan="6">&nbsp;</td>
                 </tr>


                   <tr>
                    <td colspan="8">

                        <table class="table table_borderd" style="width: 100% !important; border: 1px solid #000">
                            <tr>
                               <td class="green_bg" style="width: 16%;">ششماہی امتحان</td>
                               <td style="width: 7%;">نمبرات</td>
                               <td class="numbers" style="width: 7%;"><?php  echo $row_stu['second_term_numbers']; ?><?php //echo $row_stu['second_term_total']; ?></td>
                                <td class="green_bg" style="width: 19%;">امتحان سالانہ/تکمیل حفظ</td>
                                <td class="" style="width: 7%;">نمبرات</td>
                                <td class="numbers" style="width: 7%;"><?php  echo $row_stu['third_term_numbers']; ?><?php //echo $row_stu['third_term_total']; ?></td>
                                <td class="green_bg" style="width: 18%;">امتحان وفاق المدارس</td>
                                <td style="width: 7%;">نمبرات</td>
                                <td class="numbers" style="width: 7%;"><?php  echo $row_stu['wifaq_numbers']; ?><?php //echo $row_stu['wifaq_total']; ?></td>
                           </tr>

                            <tr>
                               <td class="number_heading" style="width: 16%;">درجہ کامیابی</td>
                               <td class="number_heading" style="width: 14%;" colspan="2"><?php
                                   if(isset($ranks[$row_stu['second_term_percent']])) echo $ranks[$row_stu['second_term_percent']];

                                   ?></td>
                                <td class="number_heading" style="width: 16%;">درجہ کامیابی</td>
                                <td class="number_heading" style="width: 14%;" colspan="2"><?php
                                    if(isset($ranks[$row_stu['third_term_percent']])) echo $ranks[$row_stu['third_term_percent']];
                                    ?></td>
                                <td class="number_heading" style="width: 16%;">درجہ کامیابی</td>
                                <td class="number_heading" style="width: 14%;" colspan="2"><?php
                                    if(isset($ranks[$row_stu['wifaq_percent']])) echo $ranks[$row_stu['wifaq_percent']];
                                    ?></td>
                            </tr>




                        </table>

                    </td>
                </tr>


                   <tr>
                    <td colspan="8">

                        <table style="width: 100% !important;">
                            <tr>
                               <td class="normal_heading" style="width: 15%;">حاضری کی نوعیت:</td>
                               <td class="td_line" style="width: 35%;"><?php echo $row_stu['type_attand']; ?></td>
                               <td class="normal_heading" style="width: 13%;">والدین کا رویہ:</td>
                               <td class="td_line" style="width: 37Z%;"><?php  echo $row_stu['parents_behave']; ?></td>
                            </tr>
                        </table>

                    </td>
                </tr>


                   <tr>
                     <td colspan="8">

                         <table style="width: 100% !important;">
                             <tr>
                                <td class="normal_heading" style="width: 14%;">مقررہ فیس:</td>
                                <td class="normal_date" style="width: 17%;"><?php echo $row_stu['fixed_fees']; ?></td>
                                <td class="normal_heading" style="width: 21%;">اگر رعایت تھی تو کس قدر:</td>
                                <td class="normal_date" style="width: 13%;"><?php echo $row_stu['fees_discount']; ?></td>
                                <td class="normal_heading" style="width: 14%;">رعایت کی نوعیت:</td>
                                <td class="td_line" style="width: 21%;"><?php echo $row_stu['discount_type']; ?></td>
                            </tr>
                         </table>

                     </td>
                 </tr>




                   <tr>
                       <td colspan="1" class="normal_heading">کوئی خاص بات</td>
                       <td colspan="7" class="td_line"><?php  echo $row_stu['any_specail_reason']; ?></td>
                   </tr>


                   <tr>
                       <td colspan="8">
                           <table style="width: 100% !important;">
                               <tr>
                                   <td class="normal_heading" style="width: 13%;">دستخط مرتب:</td>
                                   <td class="td_line" style="width: 19%;"></td>
                                   <td class="normal_heading" style="width: 15%;">دستخط ناظم تعلیمات:</td>
                                   <td class="td_line" style="width: 15%;"></td>
                                   <td style="width: 40%; vertical-align: middle; font-size: 25px; padding-bottom: 5px;" rowspan="2">شاخ کی مہر:</td>
                               </tr>
                               <tr>
                                  <td class="normal_heading" style="width: 13%;">دستخط ناظم الامور:</td>
                                  <td class="td_line" style="width: 19%;"></td>
                                  <td class="normal_heading" style="width: 15%;">تاریخ:</td>
                                  <td class="td_line" style="width: 15%;"></td>

                              </tr>
                           </table>

                       </td>
                   </tr>


               </table>



           </td>

       </tr>












   </table>
 </div>
  </body>

</html>
