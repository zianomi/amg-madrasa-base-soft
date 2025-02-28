<?php
$set = new SettingModel();

$id = isset($_GET['id']) ? $tool->intVal($_GET['id']) : '';
$name = "";
$group = "";
$username = "";
//$branch = 0;

$row = array();
$row['group_id'] = "";
$row['name'] = "";
//$row['branch_id'] = "";

if (isset($_GET['id'])) {
    if(!empty($_GET['id'])){
        $row = $set->UserEdit($id);
        $name = $row['name'];
        $group = $row['group_id'];
        $username = $row['username'];
        //$branch = $row['branch_id'];
    }
}




$errors = array();
if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {


    $name = $_POST['name'];
    $group = $tool->GetExplodedInt($_POST['group']);
    $username = $_POST['username'];
    //$branch = $_POST['branch'];


    if (empty($name)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("name_required"));
    }

    /*if (empty($branch)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("branch_required"));
    }*/

    if (empty($username)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("username_required"));
    }

    if (empty($_POST['id']) && empty($_POST['password'])) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("password_required"));
    }

    if (empty($group)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("please_select_group"));
    }


    if (count($errors) == 0) {
        $data['name'] = $_POST['name'];
        $data['username'] = $_POST['username'];
        $data['password'] = md5($_POST['password']);
        $data['phone_number'] = '';
        $data['address'] = '';
        $data['group_id'] = $group;
        //$data['branch_id'] = $branch;
        $data['published'] = 1;
        $data['user_type'] = 'admin';

        if (!empty($_POST['id'])) {
            unset($data['phone_number']);
            unset($data['address']);
            //unset($data['published']);
        }


        if (!empty($_POST['id']) && empty($_POST['password'])) {
            unset($data['password']);
        }


        if (empty($_POST['id'])) {

            if ($set->insert($set->getPrefix() ."users", $data)) {
                $_SESSION['msg'] = $tool->Message("succ", $_POST['username'] . " " . $tool->transnoecho("inserted"));
            } else {
                $_SESSION['msg'] = $tool->Message("alert", $set->getError());
            }

        } else {

            $set->update($set->getPrefix() ."users", $data, array("id" => $tool->intVal($_POST['id'])));
            $_SESSION['msg'] = $tool->Message("succ", $_POST['username'] . " " . $tool->transnoecho("updated"));
        }


        $tool->Redir("controls", "users", $_POST['code'],$_POST['action']);

    }


}





$tpl->renderBeforeContent();


if (count($errors) > 0) {
    echo $tool->Message("alert", implode("<br />", $errors));
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
                   <div class="span12"> <?php
                    if (isset($_GET['id'])) {
                        echo $tool->transnoecho("edit") . " " . $row['name'];
                    } else {
                        $tool->trans("add_user");
                    }
                    ?>
                   </div>
                </div>

                <?php
                echo $tpl->formTag("post");
                echo $tpl->formHidden();
                ?>

                <input type="hidden" name="_chk" value="1">
                <input type="hidden" name="id" value="<?php if (isset($_GET['id'])) echo $_GET['id']; ?>">


                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php $tool->trans("name") ?></label>
                        <input value="<?php echo $name ?>" type="text" name="name" id="name">
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("group") ?></label>
                        <?php
                        echo $tpl->GetOptions(array("name" => "group", "data" => $set->UserGroups(), "sel" => $group));
                         ?>
                    </div>
                </div>


                <!--<div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                    <label class="fonts"><?php /*$tool->trans("branch") */?></label>
                    <?php /*echo $tpl->userBranches( $branch); */?>
                    </div>

                </div>-->



                <div class="form-group">
                    <label for="username" class="fonts"><?php $tool->trans("username") ?></label>
                    <input value="<?php echo $username ?>" type="text" name="username" id="username">
                </div>


                <div class="form-group">
                    <label for="name" class="fonts"><?php $tool->trans("password") ?></label>
                    <input value="" type="password" name="password" id="password">
                </div>

                <div class="form-group">
                    <label for="name" class="fonts"></label>

                    <div class="controls span3 offset4">

                    </div>
                </div>





                <div class="row">
                    <div class="span4 offset4">
                        <input type="submit" name="Submit" class="btn btn-success" value="<?php if (empty($id)) $tool->trans("add"); else $tool->trans("edit"); ?>"/>
                    </div>
                </div>

                <?php echo $tpl->formClose() ?>


            </div>



    </div>
</div>
<style type="text/css">
    .chosen-container{
        width: 19% !important;
    }
    [class*="span"] .chosen-container {
      width: 60%!important;
      min-width: 60%;
      max-width: 60%;
    }
</style>
<?php
$tpl->footer();
