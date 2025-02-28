<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 2/16/2018
 * Time: 5:05 PM
 */

if(!isset($_REQUEST['ajax_request'])){
    exit;
}


switch($_REQUEST['ajax_request']){

    case "edit_discount":
        $discount = isset($_POST['value']) ? $tool->GetInt($_POST['value']) : 0;
        $id = isset($_POST['pk']['id']) ? $tool->GetInt($_POST['pk']['id']) : 0;
        $orign = isset($_POST['pk']['orign']) ? $tool->GetInt($_POST['pk']['orign']) : 0;
        if(empty($id)){
            echo "Error!";
            exit;
        }

        if($discount > $orign){
            echo "Error!";
            exit;
        }
        Tools::getModel("FeeModel");
        $fs = new FeeModel();
        $fs->updateDiscountCol($id,array("discount" => $discount));
        echo $discount;
        unset($fs);
        exit;

    break;
}