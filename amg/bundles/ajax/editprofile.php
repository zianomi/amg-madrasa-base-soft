<?php
$set = new SettingModel();
$row = $set->UserEdit(Tools::getUserId());


$errors = array();
$msgSucc = "";


if (isset($_POST["_chk"]) == 1) {



    if (empty($_POST['name'])) {
        $errors[] = $tool->Message("alert", Tools::transnoecho("name_required"));
    }

    if (empty($_POST['phone_number'])) {
        $errors[] = $tool->Message("alert", Tools::transnoecho("phone_number_required"));
    }


    if (empty($_POST['phone_number'])) {
        $errors[] = $tool->Message("alert", Tools::transnoecho("phone_number_required"));
    }





    if (count($errors) == 0) {
        $data['name'] = $_POST['name'];
        $data['password'] = md5($_POST['password']);
        $data['phone_number'] = $_POST['phone_number'];
        $data['address'] = $_POST['address'];


        if (empty($_POST['password'])) {
            unset($data['password']);
        }

        $set->update($set->getPrefix() ."users", $data, array("id" => Tools::getUserId()));
        $msgSucc = $tool->Message("succ", $_POST['name'] . " " . Tools::transnoecho("updated"));
        unset($_POST);
        //$tool->Redir("ajax", "editprofile","","");

    }


}





$tpl->renderBeforeContent();


if(!empty($msgSucc)){
    echo $msgSucc;
}

if (count($errors) > 0) {
    echo $tool->Message("alert", implode("<br />", $errors));
}





$row = $set->UserEdit(Tools::getUserId());

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
                    <div class="span12"> <?php echo Tools::transnoecho("edit") . " " . $row['name'];  ?>
                    </div>
                </div>

                <?php
                echo $tpl->formTag("post");
                echo $tpl->formHidden();
                ?>


                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php Tools::trans("Name") ?></label>
                        <input value="<?php echo $row['name'] ?>" type="text" name="name" id="name">
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php Tools::trans("Phone Number") ?></label>
                        <input value="<?php echo $row['phone_number'] ?>" type="text" name="phone_number" id="phone_number">
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php Tools::trans("Address") ?></label>
                        <input value="<?php echo $row['address'] ?>" type="text" name="address" id="address">
                    </div>
                </div>

                <div class="form-group">
                    <label><?php Tools::trans("Password optinal") ?></label>
                    <label for="name" class="fonts"><?php Tools::trans("Password") ?></label>
                    <input value="" type="password" name="password" id="password">
                </div>

                <div class="row">
                    <div class="span4 offset4">
                        <input type="submit" name="Submit" class="btn btn-success" value="<?php Tools::trans("edit"); ?>"/>
                    </div>
                </div>

                <?php echo $tpl->formClose() ?>


            </div>



        </div>
    </div>

<?php
$tpl->footer();