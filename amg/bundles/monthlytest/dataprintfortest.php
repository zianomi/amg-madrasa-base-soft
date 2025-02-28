

<?php
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
//$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$hifz_year = (isset($_GET['hifz_year'])) ? $tool->GetExplodedInt($_GET['hifz_year']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';

function firstHeader(){

    global $tool;
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php if(isset($_GET['year']) && isset($_GET['months'])) echo $_GET['year'] . '-'.  $_GET['months']  ?> Result</title>
    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-rtl.css">
    <link rel="stylesheet" href="<?php echo $tool->getWebUrl() ?>/css/bootstrap-responsive-rtl.css">

    <!--<script src="../assets/js/combined.js"></script>-->

    <style type="text/css">
        @media print{
            body {-webkit-print-color-adjust: economy | exact background: #fff; font-family:"Jameel Noori Nastaleeq"; height: 800px !important; }

            .table, .table tr, .table td {
                border-color: black;
            }

            #print_div{display:none;}
            .breadcrumb{display: none}
            .navbar{display: none}
            .content{display: none}
            .user{display: none}
            .social-sidebar-content nano{display:none}

            report_bor {border-bottom: solid #000000 0.2em; border-left: none; border-right: none; border-top: none; text-align: center; border-width: 0.1em;
            }


            .nobor_for_prn {
                border: none;
            }

            .report_center {
                font-family: "Jameel Noori Nastaleeq";
                font-size: 20px;
                color: #FFF;
                text-align: center;
                vertical-align: middle;
                background-color: #063;
            }

            .report_subhead {
                font-family: "Jameel Noori Nastaleeq";
                font-size: 20px;
                color: #FFF;
                text-align: center;
                vertical-align: middle;
                background-color: #000;
            }


        }

        body{font-family:"Jameel Noori Nastaleeq"}

        #sidebar {
        .rounded-corners(5px);
        }

        .last_box_border{background:#f7f7f7 !important; -webkit-print-color-adjust: exact; color:#000 !important; -webkit-print-color-adjust: exact; font-size:16px;  text-align:center;"}
    </style>



    <style type="text/css">
        .tables {
            border-collapse: collapse;
        }
        .tables td, table th {
            border: 1px solid black;
        }
        .tables tr:first-child th {
            border-top: 0;
        }
        .tables tr:last-child td {
            border-bottom: 0;
        }
        .tables tr td:first-child,
        .tables tr th:first-child {
            border-left: 0;
        }
        .tables tr td:last-child,
        .tables tr th:last-child {
            border-right: 0;
        }

    </style>
</head>
<body>



<div id="printReady" style="direction: rtl; vertical-align: top;">


    <table width="1150" border="0" align="center" cellpadding="0" cellspacing="0" style="vertical-align: top">



    <tr>

        <td valign="top">
            <table style="width:100% !important">
                <tr style="border:none !important;">
                    <td style="border:none !important; width: 20%"><img style="margin-right:70px" src="<?php echo $tool->getWebUrl() ?>/img/logo_report.png" height="44" width="241" /></td>
                    <td style="border:none !important; width: 60%; text-align: center; font-size: 35px;">ماہانا تعلیمی جائزہ برائے طلباء/طالبات  <?php if(isset($_GET['class']))  echo $tool->GetExplodedVar($_GET['class']) ?> </td>
                    <td style="border:none !important; width: 20%"><img src="<?php echo $tool->getWebUrl() ?>/img/logo2.png" width="57" height="59" /></td>

                </tr>
            </table>
        </td>

    </tr>

<?php

}

function footer(){
?>
    </table>
    </td>
    </tr>
    <tr valign="top">
        <td valign="top">
            <table style="width:100% !important;" class="table table-bordered">
                <tr>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">ممتاز مع الشرف</td>
                    <td><?php //echo @$mumtazSharf ?></td>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">ممتاز</td>
                    <td><?php //echo @$mumtaz ?></td>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">جید جدا</td>
                    <td><?php //echo @$jayyad_jiddan ?></td>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">جید </td>
                    <td><?php //echo @$jayyad ?></td>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">مقبول </td>
                    <td><?php //echo @$maqbool ?></td>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">راسب</td>
                    <td><?php //echo @$rasib ?></td>
                    <td width="<?php //echo $sub_col ?>" class="last_box_border">غیر حاضر</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td width="<?php //echo @$sub_col ?>" class="last_box_border">مجموعی تعداد</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr valign="top" style="height: 35px;">
        <td valign="top">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="8%" style="text-align:right; padding-right:10px"><strong>مجموعی تعلیمی کیفیت:</strong></td>
                    <td width="92%" style="border-bottom:1px solid #000;">&nbsp;</td>
                </tr>
            </table>

        </td>
    </tr>



    <tr valign="top" style="height: 35px;">
        <td valign="top">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>

                    <td width="45%" style="border-bottom:1px solid #000;">&nbsp;</td>
                    <td width="6%" style="text-align:right; padding-right:10px"><strong>دستخط نگراں:</strong></td>
                    <td width="49%" style="border-bottom:1px solid #000;">&nbsp;</td>
                </tr>
            </table>

        </td>
    </tr>


    <tr valign="top" style="height: 25px;">
        <td valign="top">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="12%" style="text-align:right; padding-right:10px"><strong>کاروائی از دفتر ناظم تعلیمات مع دستخط:</strong></td>
                    <td width="88%" style="border-bottom:1px solid #000;">&nbsp;</td>
                </tr>
            </table>

        </td>
    </tr>


    </table>



    </div>








    </div>
    </body>
    </html>
<?php }


function secondHeader(){
global $tool, $tpf, $month, $year;
?>


    <tr>
        <td valign="top" style="text-align: justify">
            <table style="width:100% !important;">
                <tr style="border:none !important;">
                    <td>نام معلم/معلمہ:</td>
                    <td></td>
                    <td>کلاس نمبر:</td>
                    <td><?php echo $tool->GetExplodedVar($_GET['section']) ?></td>
                    <td>شعبہ:</td>
                    <td><?php echo $tool->GetExplodedVar($_GET['class']) ?></td>
                    <td>تاریخ جائزہ:</td>
                    <td></td>
                    <td>زون:</td>
                    <td></td>
                    <td>بابت ماہ/سال:</td>
                    <td><?php echo $tpf->UrduMonthName($month) ?>/<?php echo $year ?></td>
                    <td>شاخ:</td>
                    <td><?php echo $tool->GetExplodedVar($_GET['branch']) ?></td>
                </tr>
            </table>
        </td>

    </tr>

<?php }

function thirdHeader(){
    global $subs;
?>

<tr>
    <td valign="top" style="text-align: justify">
        <table style="width:100% !important;" class="tables">

    <tr>
        <td style="text-align: center; vertical-align: middle;">نمبر شمار</td>
        <td style="text-align: center;  vertical-align: middle;">آئ ڈی</td>
        <td style="text-align: center;  vertical-align: middle;">رجسٹریشن</td>
        <td style="text-align: center;  vertical-align: middle;">نام ولدیت</td>
        <td colspan="2" style="text-align: center;  vertical-align: middle;">مقدار خواندگی</td>
        <td style="text-align: center;  vertical-align: middle;">حاضری</td>
        <td style="text-align: center;  vertical-align: middle;">رخصت</td>
        <td style="min-width: 20px; max-width: 20px; overflow: hidden; text-align: center; vertical-align: middle">غیر حاضری</td>
        <?php
        foreach($subs as $sub){
            ?>
            <td style="min-width: 20px; overflow: hidden; text-align: center; vertical-align: middle"><?php echo $sub['title'] ?></td>
        <?php } ?>
        <td style="text-align: center; vertical-align: middle;">میزان</td>
        <td style="text-align: center; vertical-align: middle; width: 12%;">درجہ کامیابی</td>
        <td style="text-align: center; vertical-align: middle; width: 20%">کیفیت</td>
    </tr>
    <tr style="background-color: #f7f7f7">
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td style="width: 7%;">مطلوبہ</td>
        <td style="width: 7%;">موجودہ</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <?php
        $totalSubjectNumbers = null;
        foreach($subs as $sub){

            $totalSubjectNumbers += $sub['numbers'];
            ?>
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $sub['numbers'] ?></td>
        <?php } ?>
        <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $totalSubjectNumbers ?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>

<?php }


function mainData($i){
    global $subs, $tpl, $rows, $Attand, $resTotalAttand;
    switch ($i){
        case 0:
            $a = 0;
       break;

        case 1:
            $a = 15;
        break;

        case 2:
            $a = 30;
        break;

        case 3:
            $a = 45;
        break;

        case 4:
            $a = 60;
        break;

        case 5:
            $a = 75;
        break;
    }


    foreach($rows as $row){
        $a++;
        //echo '<pre>'; print_r($row); echo '</pre>';
        @$stuRukhsat = $Attand[$row['id']]['rukhsat'];
        @$stuAbsent = $Attand[$row['id']]['absent'];
        @$stuAttand = $resTotalAttand - ($stuRukhsat + $stuAbsent);


        ?>
        <tr style="height: 40px;">
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $a ?></td>
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $row['id'] ?></td>
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $row['grnumber'] ?></td>
            <td><?php echo $row['name'] ?> <?php echo $tpl->getGenderTrans($row['gender']) ?> <?php echo $row['fname'] ?></td>
            <td><?php //if(isset($reading[$id['id']]['required'])) echo $reading[$id['id']]['required'] ?></td>
            <td><?php //if(isset($reading[$id['id']]['current'])) echo $reading[$id['id']]['current'] ?></td>
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $stuAttand ?></td>
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $stuAbsent ?></td>
            <td style="text-align: center; font-family: Arial; font-size: 15px;"><?php echo $stuRukhsat ?></td>
            <?php
            $nnn = null;
            $mumtazSharf = 0 ;
            $mumtaz = 0 ;
            $jayyad_jiddan = 0 ;
            $jayyad = 0 ;
            $maqbool = 0 ;
            $rasib = 0 ;
            foreach($subs as $sub){

                ?>
                <td style="text-align: center; font-family: Arial; font-size: 15px; width: 30px !important;"><?php //echo $obtainedNumbers ?></td>
            <?php } ?>
            <td style="text-align: center; font-family: Arial; font-size: 15px; width: 35px !important;"><?php //echo $totalNumbers[$id['id']] ?></td>
            <td><?php //echo $test->numberBetween($test->handelFloat(@$termPercentage[$id['id']])) ?></td>
            <td><?php //echo $test->KefyatBetween($test->handelFloat(@$termPercentage[$id['id']])) ?></td>
        </tr>
    <?php } ?>



<?php } ?>
