<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 10/20/2018
 * Time: 7:25 PM
 */

use Money\Money;

class AmgMoney{

    public function makeMoney($amount){
        $finalFigure = sprintf("%.2f", $amount);
        return str_replace(".","",$finalFigure);
    }

    public function displayMoney($str){
        $startString = substr($str, 0, -2);
        $end = strlen($str);
        $start = strlen($str)-2;
        $endString = substr($str, $start, $end);
        return $startString . "." . $endString;
    }

    public function userInput($amount){
        $price = Money::EUR($money->makeMoney($this->makeMoney($amount)));
    }

}