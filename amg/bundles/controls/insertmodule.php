<?php
$set = new SettingModel();
$bundles = $set->getSystemBundles();
$parentMenus = $tpl->parentMenuArray();

$dires = array_diff(scandir(BUNDLES), array('.', '..'));


$errors = array();
if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {


    $level = isset($_POST['level']) ? $_POST['level'] : 1;
    $phpFile = isset($_POST['php_file']) ? $_POST['php_file'] : "";
    $mainName = isset($_POST['main_name']) ? $_POST['main_name'] : "";
    $position = isset($_POST['position']) ? $_POST['position'] : "";
    $parent = isset($_POST['parent']) ? $_POST['parent'] : 0;
    $sub = isset($_POST['sub']) ? $_POST['sub'] : 0;
    $published = isset($_POST['published']) ? $_POST['published'] : 0;
    $bundle = isset($_POST['bundle']) ? $_POST['bundle'] : '';
    $extra = isset($_POST['extra']) ? $_POST['extra'] : '';

    $parentId = 0;

    if($level == 1){
        $parentId = 0;
    }
    elseif ($level == 2){
        $parentId = $parent;
    }
    elseif($level == 3){
        $parentId = $sub;
    }






    foreach ($_POST['lang'] as $key => $val){

        if (empty($val)) {
            $errors[] = $tool->Message("alert", $tool->transnoecho("please_insert_all_languages_label"));
        }
    }


    if (empty($phpFile)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("php_file_required"));
    }

    if (empty($mainName)) {
        $errors[] = $tool->Message("alert", $tool->transnoecho("mainName_required"));
    }




    $data['title'] = $mainName;
    $data['position'] = $position;
    $data['published'] = $published;
    $data['parent_id'] = $parentId;
    $data['level'] = $level;
    $data['bundle'] = $bundle;
    $data['phpfile'] = $phpFile;
    $data['extra'] = $extra;


    $set->insert( 'jb_system_modules', $data );
    $last = $set->lastid();

    foreach ($_POST['lang'] as $key => $val){
        $data2['title'] = $val;
        $data2['lang_id'] = $key;
        $data2['module_id'] = $last;
        $set->insert( 'jb_system_module_translations', $data2 );
    }


    $list = 1;
    $add = 1;
    $edit = 1;
    $delete = 1;
    $print = 1;
    $export = 1;
    $vals[] = $tool->setInsertDefaultValues(array(Tools::getUserId(),$last,"$add","$edit","$delete","$print","$export"));
    $res = $set->insertSystemModules(false,$vals);
    $tpl->removeMenuCache();
    $tool->Redir("controls","insertmodule","","");
    exit;


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

                            $tool->trans("add_module");

                        ?>
                    </div>
                </div>

                <?php
                echo $tpl->formTag("post");
                echo $tpl->formHidden();
                ?>

                <input type="hidden" name="_chk" value="1">




                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("bundle") ?></label>
                        <select name="bundle" id="bundle">
                            <?php foreach ($dires as $dir){ ?>

                                <option value="<?php echo $dir ?>"><?php echo $dir ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>





                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php $tool->trans("php_file") ?></label>
                        <input value="" type="text" name="php_file" id="php_file">
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("published") ?></label>
                        <select name="published" id="published">
                            <option value="1">published</option>
                            <option value="0">Un published</option>
                        </select>
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php $tool->trans("position") ?></label>
                        <input value="" type="text" name="position" id="position">
                    </div>
                </div>




                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php $tool->trans("main_name") ?></label>
                        <input value="" type="text" name="main_name" id="main_name">
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("level") ?></label>
                        <select name="level" id="level">
                            <option value="1">Level 1</option>
                            <option value="2">Level 2</option>
                            <option value="3">Level 3</option>
                        </select>
                    </div>
                </div>



                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("extra") ?></label>
                        <select name="extra" id="extra">
                            <option value="dev">Dev</option>
                            <option value="none">None</option>
                            <option value="alert">Alert</option>
                        </select>
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("parent") ?></label>
                        <select name="parent" id="parent">
                            <option value="0">Parent Module</option>
                            <?php foreach ($parentMenus as $parentMenu){ ?>

                            <option value="<?php echo $parentMenu['id'] ?>"><?php echo $parentMenu['title'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row-fluid">
                    <div class="span4">&nbsp;</div>
                    <div class="span4">
                        <label for="name" class="fonts"><?php $tool->trans("sub") ?></label>
                        <select name="sub" id="sub">

                        </select>
                    </div>
                </div>


                <?php
                $langs = $set->SystemLanguages();
                foreach ($langs as $lang){
                    //echo '<pre>'; print_r($lang); echo '</pre>';
                ?>
                <div class="row-fluid">
                    <div class="span12">
                        <label for="name" class="fonts"><?php echo $lang['title'] ?></label>
                        <input value="" type="text" name="lang[<?php echo $lang['id'] ?>]">
                    </div>
                </div>
                <?php } ?>















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

<script>
    if ($('#parent').size) {
        var getStudent = makeJsLink("ajax","settings");
        $('#parent').change(function () {
            var data = 'ajax_request=parent_module&parent_id=' + $("#parent").val();
            $.ajax({
                type: "POST",
                url: getStudent,
                data: data,
                success: function (data) {
                    $("#sub").html(data);
                }
            })
        });

    }
</script>
    <style type="text/css">
        [class*="span"] .chosen-container {
            width: 60%!important;
            min-width: 60%;
            max-width: 60%;
        }
    </style>
<?php
$tpl->footer();
