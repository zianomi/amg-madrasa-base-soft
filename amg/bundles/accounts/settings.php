<?php
$errors = array();
Tools::getModel("Accounts");
$ac = new Accounts();



if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {

    $accountLevels = isset($_POST['account_levels']) ? $tool->GetInt($_POST['account_levels']) : '';


    if (empty($accountLevels)) {
        $errors[] = $tool->transnoecho("please_select_level_of_account");
    }




    if(count($errors) == 0){

        $data['setting_value'] = $accountLevels;
        $whereLevel = array("setting_key" => "account_levels");
        $table = $ac->getPrefix() . "ac_settings";
        $res = $ac->updateData($table,$data,$whereLevel);



        if($res){
            $_SESSION['msg'] = $tool->Message("succ", $tool->transnoecho("setting_updated"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("updation_failed"));
        }
        $tool->Redir("accounts","settings",$_POST['code'],$_POST['action']);
        exit;
    }


}




$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$maxLevel = $ac->getSettings(array("key" => "account_levels"));


?>


    <div class="social-box">
        <div class="header">
            <div class="tools">
            </div>
        </div>
        <div class="body">
            <div id="jamia_msg">&nbsp;</div>


            <div id="printReady">



                <div class="container text-center">

                    <div class="row-fluid">
                        <div class="span12">
                            <a href="javascript:void(0)" class="icon-btn icon-btn-green">
                                <i class="icon-edit icon-2x"></i>
                                <div><?php $tool->trans("account_settings") ?></div>
                            </a>

                        </div>
                    </div>


                    <form action="" method="post">
                            <?php echo $tpl->formHidden() ?>



                        <div class="control-group">
                            <label class="control-label"><span class="fonts"><?php $tool->trans("level_0f_accounts") ?></span></label>
                            <div class="controls">
                                <select name="account_levels" id="account_levels">
                                    <option value=""><?php $tool->trans("please_select") ?></option>
                                    <?php for($i=1; $i <= 9; $i++){ ?>
                                        <?php
                                        if($i == $maxLevel){
                                            $sel = ' selected';
                                        }else{
                                            $sel = '';
                                        }
                                        ?>
                                    <option value="<?php echo $i ?>"<?php echo $sel ?>><?php echo $i ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>




                        <div class="row">
                            <input type="submit" name="Submit" class="btn btn-success" value="Insert" />
                        </div>
                </div>
            </div>


        </div>
    </div>


<?php


$tpl->footer();