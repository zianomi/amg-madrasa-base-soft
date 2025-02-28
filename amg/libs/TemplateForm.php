<?php

/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 2/16/2017
 * Time: 11:01 AM
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . "HijriConvert.php";

class TemplateForm
{

    public function getHijriObject(){
        //if ( ($DateConv instanceof HijriConvert) != true ) {
            $DateConv = new HijriConvert();
        //}
        return $DateConv;
    }

    public function toolObj(){
        global $tool;
        return $tool;
    }

    public function IslamicYearDropDown()
    {

        $tool = $this->toolObj();
        $DateConv = $this->getHijriObject();;

        $format = "dd-mm-YYYY";



        $hijriDate = $DateConv->GregorianToHijri(date("d-m-Y"), $format);
        $dateArr = explode("-", $hijriDate);
        $hijriYear = $dateArr[2];
        //$hijriMonth = $dateArr[1];

        $year = (isset($_REQUEST['year'])) ? $tool->GetInt($_REQUEST['year']) : $hijriYear;

        $dateArr = explode("-", $hijriDate);

        $hijriYear = $dateArr[2] + 1;

        $html = '';

        $html .= '<select name="year" id="year">';
        $html .= '<option value=""></option>';


        for ($i = 1432; $i < $hijriYear; $i++) {

            if ($year == $i) {

                $selected = ' selected="selected"';

            } else {

                $selected = '';

            }

            $html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';

        }

        $html .= '</select>';

        return $html;

    }


    public function IslamicMonthDropDown($maxVal = false)
    {

        $tool = $this->toolObj();

        $html = '';
        $html .= '<select name="month" id="month">';
        $html .= '<option value=""></option>';

        $DateConv = $this->getHijriObject();;
        $format = "dd-mm-YYYY";
        $hijriDate = $DateConv->GregorianToHijri(date("d-m-Y"), $format);
        $dateArr = explode("-", $hijriDate);
        $hijriMonth = $dateArr[1];

        if($maxVal){
            $max = $hijriMonth;
        }

        $month = (isset($_REQUEST['month'])) ? $tool->GetInt($_REQUEST['month']) : $hijriMonth;

        $monthEntryArr = array(

            "01" => array("10","11","12")
           ,"02" => array("10","11","12","01")
           ,"03" => array("10","11","12","01","02")
           ,"04" => array("10","11","12","01","02","03")
           ,"05" => array("10","11","12","01","02","03","04")
           ,"06" => array("10","11","12","01","02","03","04","05")
           ,"07" => array("10","11","12","01","02","03","04","05","06")
           ,"08" => array("10","11","12","01","02","03","04","05","06","07")
           ,"09" => array("10","11","12","01","02","03","04","05","06","07")
           ,"10" => array("10","11","12","01","02","03","04","05","06","07")
           ,"11" => array("10")
           ,"12" => array("10","11")

        );


        if(!empty($max)){
            $allowedMonths = $monthEntryArr[str_pad($max,2,"0",STR_PAD_LEFT)];

            foreach($allowedMonths as $allowedMonthKey => $allowedMonthVal){

                $montVal = str_pad($allowedMonthVal,2,"0",STR_PAD_LEFT);

                if($month == $montVal){
                    $selected = 'selected="selected"';
                }
                else{
                    $selected = "";
                }

                $html .= '<option value="'.$montVal.'" '.$selected.'>'.$this->IslamicMonthName($montVal).'</option>';

            }

        }


        else{

            for($i =0; $i <= 12; $i++){

                $montVal = str_pad($i,2,"0",STR_PAD_LEFT);
                if($month == $montVal){
                    $selected = 'selected="selected"';
                }
                else{
                    $selected = "";
                }


                $html .= '<option value="'.$montVal.'" '.$selected.'>'.$this->IslamicMonthName($montVal).'</option>';

            }

        }


        $html .= '</select>';

        return $html;

    }



    public function IslamicMonthName($month)
    {
            $newMonth = str_pad($month,2,"0",STR_PAD_LEFT);
            $mon = "";
            switch ($newMonth) {

                case '01':
                    $mon = 'محرم';
                    break;
                case '02':
                    $mon = 'صفر';
                    break;
                case '03':
                    $mon = 'ربیع الاول';
                    break;
                case '04':
                    $mon = 'ربیع الثانی';
                    break;
                case '05':
                    $mon = 'جمادی الاول';
                    break;
                case '06':
                    $mon = 'جمادی الثانی';
                    break;
                case '07':
                    $mon = 'رجب';
                    break;
                case '08':
                    $mon = 'شعبان';
                    break;
                case '09':
                    $mon = 'رمضان';
                    break;
                case '10':
                    $mon = 'شوال';
                    break;
                case "11":
                    $mon = 'ذوالقعدہ';
                    break;
                case "12":
                    $mon = 'ذوالحجۃ';
                    break;
            }

            return $mon;
        }


    public function UrduMonthName($month)
    {

        $mon = '';
        switch ($month) {
            case 1:
            case '01':
                $mon = 'جنوری';
                break;
            case 2:
            case '02':
                $mon = 'فروری';
                break;
            case 3:
            case '03':
                $mon = 'مارچ';
                break;
            case 4:
            case '04':
                $mon = 'اپریل';
                break;
            case 5:
            case '05':
                $mon = 'مئی';
                break;
            case 6:
            case '06':
                $mon = 'جون';
                break;
            case 7:
            case '07':
                $mon = 'جولائی';
                break;
            case 8:
            case '08':
                $mon = 'اگست';
                break;
            case 9:
            case '09':
                $mon = 'ستمبر';
                break;
            case 10:
                $mon = 'اکتوبر';
                break;
            case 11:
                $mon = 'نومبر';
                break;
            case 12:
                $mon = 'دسمبر';
                break;
        }
        return $mon;
    }


    public function IslamicMonthYear($date)
    {

        $date_arr = explode("-", $date);

        $year = $date_arr[0];

        $month = $date_arr[1];

        return $this->IslamicMonthName($month) . "  <span style='padding-right:5px;'>" . $year . "</span>";

    }


    public function Equality($sel = "")
    {

        $equalArr= array(
            "=" => "برابر"
           ,">=" => "برابر یا زیادہ"
           ,"<=" => "برابر یا کم"
           ,"!=" => "برابر نہیں"
        );

        $htm = '';
        foreach($equalArr as $key => $val){

            if($sel == $key){
                $selected = ' selected="selected"';
            }
            else{
                $selected = '';
            }

            $htm .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
        }

        return $htm;

    }


    public function NewYearsDropDown($sel=""){


        $html = '';


        $html .= '<option value=""></option>';

        $Year = date("Y");


        for($i=2014; $i <= $Year+1; $i++){
            $a = '';
            if(!empty($sel)){
                if($sel == $i){
                    $a = ' selected="selected"';
                }
            }

            if(empty($sel)){
                if($Year == $i){
                    $a = ' selected="selected"';
                }
                else{
                    $a = '';
                }
            }
            $html .= '<option value="'.$i.'" '.$a.'>'.$i.'</option>';
        }



        return $html;

    }


    public function NewMonthDropDown($sel = ""){
        $html = '';
        $month_options = '<option value=""></option>';

        $formattedMonthArray = array(
            "01" => "January", "02" => "February", "03" => "March", "04" => "April",
            "05" => "May", "06" => "June", "07" => "July", "08" => "August",
            "09" => "September", "10" => "October", "11" => "November", "12" => "December",
        );

        $Month = date("m");

        for ($i = 1; $i <= 12; $i++) {

            $a = '';
            $month_num = str_pad( $i, 2, 0, STR_PAD_LEFT );

            if(!empty($sel)){
                if($sel == $i){
                    $a = ' selected="selected"';
                }
            }

            if(empty($sel)){
                if($Month == $month_num){
                    $a = ' selected="selected"';
                }
            }


            //$month_name = date( 'F', mktime( 0, 0, 0, $i + 1, 0, 0, 0 ) );
            $month_options .= '<option value="' .  $month_num . '" '.$a.'>' . $formattedMonthArray[$month_num] . '</option>';
        }

        return $month_options;
    }


    function MakeEamDateFromMonthYear($year,$month){

        return $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) .'-25';

    }

    public function isTimeValid($time,$format='H:i:s'){
        //return preg_match("/^(?:2[0-4]|[01][1-9]|10):([0-5][0-9])$/", $time);

        $date = date("Y-m-d");
        $d = DateTime::createFromFormat("Y-m-d $format", "$date $time");
        return $d && $d->format($format) == $time;
    }

    public function makeTime($hour,$minute){
        $hour = str_pad($hour,2,0,STR_PAD_LEFT);
        $minute = str_pad($minute,2,0,STR_PAD_LEFT);
        $time = $hour . ":" . $minute . ":00";
        return $time;
    }

    public function hourOptions($name = ""){
        $hours = $this->hours();
        $html = '';

        foreach ($hours as $k => $v) {
            if(isset($_REQUEST[$name]) && !empty($_REQUEST[$name])){
                if($_REQUEST[$name] == $k){
                    $sel = ' selected';
                }
                else{
                    $sel = '';
                }
            }
            $html .= '<option value="'.$k.'".'.$sel.'>'.$v.'</option>';
        }


        return $html;

    }

    public function minuteOptions($name = ""){
        $hours = $this->minutes();
        $html = '';

        foreach ($hours as $k => $v) {
            if(isset($_REQUEST[$name]) && !empty($_REQUEST[$name])){
                if($_REQUEST[$name] == $k){
                    $sel = ' selected';
                    echo '<pre>'; print_r($_REQUEST[$name] . " " . $k); echo '</pre>';
                }
                else{
                    $sel = '';
                }
            }
            $html .= '<option value="'.$k.'" '.$sel.'>'.$v.'</option>';
        }


        return $html;

    }

    public function formatTime($time){
        $date = date("Y-m-d");
        $currentDateTime = "$date $time";
        return date('h:i A', strtotime($currentDateTime));
    }

    public function hours(){
        $arr[7] = "7 AM";
        $arr[8] = "8 AM";
        $arr[9] = "9 AM";
        $arr[10] = "10 AM";
        $arr[11] = "11 AM";
        $arr[12] = "12 PM";
        $arr[13] = "1 PM";
        $arr[14] = "2 PM";
        $arr[15] = "3 PM";
        $arr[16] = "4 PM";
        $arr[17] = "5 PM";
        $arr[18] = "6 PM";
        $arr[19] = "7 PM";
        $arr[20] = "8 PM";
        $arr[21] = "9 PM";
        $arr[22] = "10 PM";
        $arr[23] = "11 PM";
        $arr[00] = "12 AM";
        return $arr;
    }

    public function minutes(){
        $arr = array();
        for($i=0; $i<=59; $i++){
            $arr[$i] = str_pad($i,2,0,STR_PAD_LEFT);
        }

        return $arr;
    }

}
