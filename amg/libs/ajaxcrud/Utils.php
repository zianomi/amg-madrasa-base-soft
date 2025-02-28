<?php
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 9/9/18
 * Time: 3:21 PM
 */

function make_filename_safe($filename) {
    $normalizeChars = array(
        '�' => 'S',
        '�' => 's',
        '�' => 'Dj',
        '�' => 'Z',
        '�' => 'z',
        '�' => 'A',
        '�' => 'A',
        '�' => 'A',
        '�' => 'A',
        '�' => 'A',
        '�' => 'A',
        '�' => 'A',
        '�' => 'C',
        '�' => 'E',
        '�' => 'E',
        '�' => 'E',
        '�' => 'E',
        '�' => 'I',
        '�' => 'I',
        '�' => 'I',
        '�' => 'I',
        '�' => 'N',
        '�' => 'O',
        '�' => 'O',
        '�' => 'O',
        '�' => 'O',
        '�' => 'O',
        '�' => 'O',
        '�' => 'U',
        '�' => 'U',
        '�' => 'U',
        '�' => 'U',
        '�' => 'Y',
        '�' => 'B',
        '�' => 'Ss',
        '�' => 'a',
        '�' => 'a',
        '�' => 'a',
        '�' => 'a',
        '�' => 'a',
        '�' => 'a',
        '�' => 'a',
        '�' => 'c',
        '�' => 'e',
        '�' => 'e',
        '�' => 'e',
        '�' => 'e',
        '�' => 'i',
        '�' => 'i',
        '�' => 'i',
        '�' => 'i',
        '�' => 'o',
        '�' => 'n',
        '�' => 'o',
        '�' => 'o',
        '�' => 'o',
        '�' => 'o',
        '�' => 'o',
        '�' => 'o',
        '�' => 'u',
        '�' => 'u',
        '�' => 'u',
        '�' => 'y',
        '�' => 'y',
        '�' => 'b',
        '�' => 'y',
        '�' => 'f');

    $strip = array(
        "~",
        "`",
        "!",
        "@",
        "#",
        "$",
        "%",
        "^",
        "&",
        "*",
        "(",
        ")",
        "=",
        "+",
        "[",
        "{",
        "]",
        "}",
        "\\",
        "|",
        ";",
        ":",
        "\"",
        "'",
        "&#8216;",
        "&#8217;",
        "&#8220;",
        "&#8221;",
        "&#8211;",
        "&#8212;",
        "—",
        "–",
        ",",
        "<",
        ">",
        "/",
        "?");

    $toClean = str_replace(' ', '_', $filename);
    $toClean = str_replace('--', '-', $toClean);
    $toClean = strtr(stripslashes($toClean), $normalizeChars);
    $toClean = str_replace($strip, "", $toClean);
    $toClean = rand(1, 9999) . "_" . $toClean;

    return $toClean;

}

function changeDateFormat($date){
    global $tool;
    return $tool->ChangeDateFormat($date);
}


function xeditableError($msg){
    header('HTTP/1.0 400 Bad Request', true, 400);
    echo $msg;
    exit;
}

class Utils{


    public function getThisPage(){
        if (stristr($_SERVER['REQUEST_URI'], "?")){
            return $_SERVER['REQUEST_URI'] . "&";
        }
        return $_SERVER['REQUEST_URI'] . "?";
    }



    public function echoMsgBox(){
        $errors = "";
        global $error_msg;
        global $report_msg;

        if (is_string($error_msg)){
            $error_msg = array();
        }
        if (is_string($report_msg)){
            $report_msg = array();
        }

        //for passing errors/reports over get variables
        if (isset($_REQUEST['err_msg']) && $_REQUEST['err_msg'] != ''){
            $error_msg[] = $_REQUEST['err_msg'];
        }
        if (isset($_REQUEST['rep_msg']) && $_REQUEST['rep_msg'] != ''){
            $report_msg[] = $_REQUEST['rep_msg'];
        }
        $reports = '';
        if(is_array($report_msg)){
            $first = true;
            foreach ($report_msg as $e){
                if($first){
                    $reports.= "&nbsp;&nbsp; $e";
                    $first = false;
                }
                else
                    $reports.= "<br /> $e";
            }
        }
        if(isset($reports) && $reports != ''){
            echo "<div class='report'>$reports</div>";
        }

        if(is_array($error_msg)){
            $first = true;
            foreach ($error_msg as $e){
                if($first){
                    $errors.= "&nbsp;&nbsp; $e";
                    $first = false;
                }
                else
                    $errors.= "<br />$e";
            }
        }
        if(isset($errors) && $errors != ''){
            echo "<div class='error'>$errors</div>";
        }
    }

}