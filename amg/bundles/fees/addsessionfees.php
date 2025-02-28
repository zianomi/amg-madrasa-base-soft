<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("FeeModel");
$fee = new FeeModel();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$hifz_year = (isset($_GET['hifz_year'])) ? $tool->GetExplodedInt($_GET['hifz_year']) : '';
$year = (isset($_GET['year'])) ? $tool->GetInt($_GET['year']) : '';
$month = (isset($_GET['month'])) ? $tool->GetInt($_GET['month']) : '';
$types = $fee->getFeeTypes();





if(isset($_POST['_chk'])==1) {


    $branch = !empty($_POST['branch']) ? $tool->GetInt($_POST['branch']) : '';
    $session = !empty($_POST['session']) ? $tool->GetInt($_POST['session']) : '';
    if (empty($branch)) {
        $errors[] = $tool->Message("alert", "branch_required");
    }

    if (empty($session)) {
        $errors[] = $tool->Message("alert", "session_required");
    }







    foreach($_POST['ids'] as $key){

        $classIds = $tool->GetInt($_POST['ids'][$key]);

        foreach($_POST['fee_keys'] as $feeKeys){

            $amounts = $tool->GetInt($_POST['amounts'][$key][$feeKeys]);
            $feeTypes = $tool->GetInt($_POST['fee_ids'][$key][$feeKeys]);

            if(empty($classIds)){
                $error[] = $tool->Message("alert","Class Required, ID#: " . [$key]);
            }
            if(empty($feeTypes)){
                $error[] = $tool->Message("alert","Fee Type Required: ID#: " . [$key] . " Fee ID#: " . [$feeKeys]);
            }
            if(!empty($amounts)){
                $vals[] = $tool->setInsertDefaultValues(array("NULL",$branch,$classIds,$session,$feeKeys,$amounts));
            }

        }

    }



    if(count($error)==0){
        $res = $fee->insertFeeStructure($vals);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("fees","feestructure","","list");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }
    }



}


$qr->renderBeforeContent();
$qr->searchContentAbove();


?>
    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branches") ?></label><?php echo $tpl->userBranches() ?></div>

        <div class="span3"><label class="fonts">&nbsp;</label><?php
              echo $tpl->GetMultiOptions(array("name" => "types[]", "data" => $types));
              ?></div>
        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
    </div>




<?php
$qr->searchContentBottom();

if (isset($_GET['_chk']) == 1) {


    if (empty($branch)) {
        echo $tool->Message("alert", $tool->transnoecho("branch_required"));
        exit;
    }


    if (empty($session)) {
        echo $tool->Message("alert", $tool->transnoecho("session_required"));
        exit;
    }


}
?>

    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {


        $param = array(
        "branch" => $branch
        , "session" => $session

        );

        $res = $set->sessionCurrentClasses($session,$branch);


        if (count($res) == 0) {
            echo $tool->Message("alert", $tool->transnoecho("no_classes_found"));
            return;
        }




        ?>

        <form method="post">


            <input type="hidden" name="branch" value="<?php echo $branch ?>"/>
            <input type="hidden" name="session" value="<?php echo $session ?>">


            <?php echo $tpl->FormHidden(); ?>

            <h2 class="fonts">

                <?php


                if (isset($_GET['branch'])) {
                    if (!empty($_GET['branch'])) {
                        echo $tool->GetExplodedVar($_GET['branch']);
                    }
                }

                $types = $_GET['types'];

                if (count($types) == 0) {
                    echo $tool->Message("alert", $tool->transnoecho("no_fees_found"));
                    return;
                }



                $colspan = count($types) + 2;

                $resFee = $fee->getFeeStructure($param);

                $feeData = array();

                if(!empty($resFee)){
                    foreach($resFee as $rowFee)
                    $feeData[$branch][$session][$rowFee['class_id']][$rowFee['fee_type_id']] = $rowFee['fees'];
                }

                //echo '<pre>';print_r($resFee );echo '</pre>';

                ?>
                <br>
            </h2>

            <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
                <table class="table table-bordered table-striped table-hover flip-scroll">
                    <thead>
                    <tr>

                        <th class="fonts"><?php $tool->trans("id") ?></th>
                        <th class="fonts"><?php $tool->trans("class") ?></th>
                        <?php
                        foreach($types as $type){
                                //

                        ?>
                        <th class="fonts"><?php echo $tool->GetExplodedVar($type) ?></th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($res as $row) { ?>
                        <tr>

                            <td class="avatar"><?php echo $row['id']; ?></td>
                            <td class="fonts"><?php echo $row['title']; ?></td>

                            <?php
                            foreach($types as $type){
                                $typeId = $tool->GetExplodedInt($type);
                                $amount = "";
                                if(isset($feeData[$branch][$session][$row['id']][$typeId])){
                                    $amount = $feeData[$branch][$session][$row['id']][$typeId];
                                }
                            ?>
                            <td class="fonts">
                                <input type="text" value="<?php echo $amount ?>" name="amounts[<?php echo $row['id']; ?>][<?php echo $typeId ?>]" style="width: 40%" maxlength="5" min="0" pattern="\d+" />
                                <input type="hidden" name="ids[<?php echo $row['id']; ?>]" value="<?php echo $row['id']; ?>" />
                                <input type="hidden" name="fee_keys[<?php echo $typeId ?>]" value="<?php echo $typeId ?>" />
                                <input type="hidden" name="fee_ids[<?php echo $row['id']; ?>][<?php echo $typeId ?>]" value="<?php echo $typeId ?>" />
                            </td>
                            <?php } ?>


                        </tr>
                    <?php } ?>
                    </tbody>

                    <tr >
                        <td colspan="<?php echo $colspan?>" style="text-align: center">
                            <button type="submit" class="btn txtcenter">Save</button>
                        </td>
                    </tr>
                </table>
            </div>
            <?php }
            $tpl->formClose();
            ?>
    </div>

<?php

$tpl->footer();
