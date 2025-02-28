<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 1/19/2018
 * Time: 9:07 PM
 */

$request = isset($_POST['requestType']) ? $_POST['requestType'] : "";
$sessionToken = isset($_POST['sessionToken']) ? $_POST['sessionToken'] : "";
Tools::getLib("BaseWs");
$bs = new BaseWs();
if (empty($request)) {
    echo $bs->getErrorResponse("", "Request empty");
    exit;
}

if (empty($sessionToken)) {
    echo $bs->getErrorResponse("", "Session token required");
    exit;
}

if($sessionToken != $bs->GlToken()){
    echo $bs->getErrorResponse("", "Invalid Session Token");
    exit;
}


switch($request){
    case "unread_deposits":
        Tools::getModel("FeeModel");
        $fee = new FeeModel();
        $zoneId = isset($_POST['zoneId']) ? $tool->GetInt($_POST['zoneId']) : 0;
        $unreadData = $fee->unreadGlDeposits($zoneId);
        $deposits = array();
        $deposiData = array();
        $temp = array();
        $tempDep = array();

        $data = array();
        foreach ($unreadData as $row){
            $tempDep['deposit_id'] = $row['deposit_id'];
            $tempDep['bank'] = $row['bank'];
            $tempDep['bank_short_name'] = $row['bank_short_name'];
            $tempDep['bank_code'] = $row['bank_code'];
            $tempDep['bank_gl_code'] = $row['bank_gl_code'];
            $tempDep['account_title'] = $row['account_title'];
            $tempDep['account_number'] = $row['account_number'];
            $tempDep['deposit_number'] = $row['deposit_number'];
            $tempDep['gl_module_code'] = $row['gl_module_code'];
            $tempDep['gl_branch_code'] = $row['gl_branch_code'];
            $tempDep['created'] = $row['created'];
            $deposits[$row['deposit_id']] = $tempDep;
            $temp['orign_fee'] = $row['orign_fee'];
            $temp['discount'] = $row['discount'];
            $temp['fee_type_id'] = $row['gl_fee_type_id'];
            $temp['paid_id'] = $row['paid_id'];
            $temp['paid_amount'] = ($row['orign_fee'] - $row['discount']);
            $deposiData[$row['deposit_id']][] = $temp;

        }

        foreach ($deposits as $deposit){
            $data[] = array("deposits" => $deposit, "deposit_data" => $deposiData[$deposit['deposit_id']]);;
        }

        echo $bs->getSuccResponse($data);
        unset($fs);
        exit;

        break;

    case "mark_deposit_as_read":
        $depositId = isset($_POST['deposit_id']) ? $tool->GetInt($_POST['deposit_id']) : "";
        if (empty($depositId)) {
            echo $bs->getErrorResponse("", "Deposit id required");
            exit;
        }
        Tools::getModel("FeeModel");
        $fee = new FeeModel();
        $res = $fee->markDepositReadByGl($depositId);
        if($res){
            echo $bs->getSuccResponse("Data updated");
            unset($fs);
            exit;
        }
        else{
            echo $bs->getErrorResponse("", "Already updated.");
            unset($fs);
            exit;
        }
        break;

}
