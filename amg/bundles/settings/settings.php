<?php
$errors = array();
if (isset($_POST["_chk"]) == 1) {

    $branch = isset($_POST['branch']) ? $_POST['branch']: "";
    $session = isset($_POST['session']) ? $_POST['session']: "";

    if(!empty($session)){
        $sessionID = $tool->GetExplodedInt($session);
        $sessionName = $tool->GetExplodedVar($session);
        $_SESSION['AmgSettingsData']['sessionId'] = $sessionID;
        $_SESSION['AmgSettingsData']['sessionName'] = $sessionName;
    }
    if(!empty($branch)){
        $branchName = $tool->GetExplodedVar($branch);
        $branchID = $tool->GetExplodedInt($branch);
        $_SESSION['AmgSettingsData']['branchName'] = $branchName;
        $_SESSION['AmgSettingsData']['branchID'] = $branchID;
    }

    $tool->Redir("settings","settings","23","list");
    exit;

}
$tpl->renderBeforeContent();

$set = new SettingModel();



$selectedBranchId = "";

if(isset($_SESSION['AmgSettingsData']['branchID'])){
    $selectedBranchId = $_SESSION['AmgSettingsData']['branchID'];
}

?>





<div class="social-box">
    <div class="header">
        <div class="tools">


        </div>
    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>




            <div class="container text-center">

                <div class="row alert">
                   <div class="span12"><?php $tool->trans("settings") ?></div>
                </div>

                <?php
                echo $tpl->formTag("post");
                echo $tpl->formHidden();


                ?>

                <input type="hidden" name="_chk" value="1">


                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php $tool->trans("set_branch") ?></label>
                        <?php
                        echo $tpl->GetOptions(array("name" => "branch", "data" => $set->userBranches(), "sel" => $selectedBranchId));
                         ?>
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php $tool->trans("set_session") ?></label>
                        <?php
                        echo $tpl->getAllSession();
                         ?>
                    </div>
                </div>
















                <div class="row">
                    <div class="span4 offset4">
                        <input type="submit" name="Submit" class="btn btn-success" value="<?php $tool->trans("save"); ?>"/>
                    </div>
                </div>

                <?php echo $tpl->formClose() ?>


            </div>



    </div>
</div>
<style type="text/css">
    [class*="span"] .chosen-container {
      width: 60%!important;
      min-width: 60%;
      max-width: 60%;
    }
</style>
<?php
$tpl->footer();