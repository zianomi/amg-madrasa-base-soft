<?php
$errors = array();
Tools::getModel("NotesModel");
$note = new NotesModel();
Tools::getModel("SmsModel");
$sms = SmsModel::Instance();


if ((isset($_POST["_chk"])) && ($_POST["_chk"] == "1")) {

    $student_id = isset($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : '';

    $date = $tool->ChangeDateFormat($_POST['date']);
    if (!$tool->checkDateFormat($date)) {
        $errors[] =$tool->transnoecho("invalid_date");
    }


    if(empty($student_id)){
	    $errors[] = $tool->transnoecho("insert_id");

    }

    if(empty($_POST['note_cat'])){
	    $errors[] = $tool->transnoecho("please_select_cat");
    }

    if(empty($_POST['note_sub_cat'])){
        $errors[] = $tool->transnoecho("please_select_note_type");
    }

    if(empty($_POST['desc'])){
	    $errors[] = $tool->transnoecho("please_insert_detail");
    }


    if(count($errors) == 0){

        $data['student_id'] = $student_id;
        $data['branch_id'] = $tool->GetInt($_POST['branch']);
        $data['class_id'] = $tool->GetInt($_POST['class']);
        $data['section_id'] = $tool->GetInt($_POST['section']);
        $data['session_id'] = $tool->GetInt($_POST['session']);
        $data['note_sub_cat_id'] = $tool->GetExplodedInt($_POST['note_sub_cat']);
        $data['date'] = $date;
        $data['desc'] = ($_POST['desc']);

        //$number = $sms->getNumber(array("id" => $student_id));



        $note->insertNote($data);

        //$sms->SendSMS($number,$data['desc']);

        $_SESSION['msg'] = $tool->Message("succ",$tool->GetExplodedVar($_POST['note_cat']) . " " . $tool->transnoecho("inserted"));
        $tool->Redir("notes","noteinsert",$_POST['code'],$_POST['action']);
        exit;
    }


}


$noteSubCat = array();

$noteCat = isset($_POST['note_cat']) ? $tool->GetExplodedInt($_POST['note_cat']) : "";

if(!empty($noteCat)){
    $noteSubCat = $set->getTitleTable("notesubcats", " AND note_cat_id = $noteCat");
}


$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);

echo $tpl->formTag("post");
echo $tpl->FormHidden();
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
                                    <div><?php $tool->trans("insert_note") ?></div>
                                  </a>

                            </div>
                        </div>


                        <form action="" method="post">
                            <input type="hidden" name="MM_insert" value="form1">

                                <p id="student_res">&nbsp;</p>

                                <div class="control-group">
                                    <label class="control-label"><span class="fonts"><?php $tool->trans("ID") ?></span></label>
                                    <div class="controls">
                                        <input value="<?php if(isset($_POST['student_id'])) echo $_POST['student_id'] ?>" type="text" name="student_id" id="student_id">
                                    </div>
                                </div>

                            <div class="control-group">
                                <div class="controls">
                                    <?php echo $tpl->getTable("notecats","note_cat"); ?>
                                </div>
                            </div>

                            <div class="control-group">

                            </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("note_type") ?></span></label>
                                <div class="controls">
                                    <?php
                                    $selectd = "";
                                    if(isset($_POST['note_sub_cat'])){
                                        $selectd = $tool->GetExplodedInt($_POST['note_sub_cat']);
                                    }
                                    ?>
                                    <?php echo $tpl->GetOptions(array("name" => "note_sub_cat", "data" => $noteSubCat, "sel" => $selectd)); ?>
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("date") ?></span></label>
                                <div class="controls">
                                    <?php echo $tpl->getDateInput() ?>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("detail") ?></span></label>
                                <div class="controls">
                                    <textarea name="desc" id="desc" style="height: 100px" required="required"><?php if(isset($_POST['desc'])) echo $_POST['desc'] ?></textarea>
                                </div>
                            </div>


                            <!--<div class="control-group">
                                <label class="control-label"><span class="fonts"><?php /*$tool->trans("sms") */?></span></label>
                                <div class="controls">
                                    <input type="checkbox" value="1" name="sms" id="sms"/>
                                    <?php
/*                                    echo $tpl->GetOptions(array("name" => "sms", "data" => $sms->getNumbers()));
                                     */?>
                                </div>
                            </div>-->


                            <div class="row">
                                <input type="submit" name="Submit" class="btn btn-success" value="Insert" />
                            </div>
                    </div>
                </div>


    </div>
</div>


<?php
echo $tpl->formClose();


?>
<style type="text/css">
    .chosen-container {
      width: 18%!important;
      min-width: 18%;
      max-width: 18%;
    }
</style>
<?php
$tpl->footer();