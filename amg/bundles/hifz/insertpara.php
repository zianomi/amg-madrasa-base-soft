<?php
$errors = array();
Tools::getModel("NotesModel");
Tools::getLib("TemplateForm");
$tpf = new TemplateForm();
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$note = new NotesModel();
Tools::getModel("SmsModel");
$sms = new SmsModel();
Tools::getModel("HifzModel");
$hfz = new HifzModel();

//$hifzRecordId = isset($_GET['hifz_record_id']) ? $tool->GetInt($_GET['hifz_record_id']) : "";
$student_id = "";
$para_page = "";
$line_number = "";
$year = "";
$month = "";
$para_id = "";
$hifz_year = "";

if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {


    $year = $_POST['year'];
    $month = $_POST['month'];

    $student_id = isset($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : '';
    $para_page = isset($_POST['para_page']) ? $tool->GetInt($_POST['para_page']) : '';
    $line_number = isset($_POST['line_number']) ? $tool->GetInt($_POST['line_number']) : '';
    $hifz_year = isset($_POST['hifz_year']) ? $tool->GetInt($_POST['hifz_year']) : '';


    $date = $tpf->MakeEamDateFromMonthYear($year, $month);

    if(!$tool->checkDateFormat($date)){
        $errors[] = "Invalid Date";
    }


    $para_id = isset($_POST['quran']) ? $tool->GetExplodedInt($_POST['quran']) : '';



    if(empty($student_id) || empty($date) || empty($para_id) || empty($para_page) || empty($line_number) || empty($hifz_year)){
        $errors[] = $tool->transnoecho("all_fields_required.");
    }



    $msg = "";

        if(count($errors)==0){

            $data['student_id'] = $student_id;
            $data['date'] = $date;
            $data['para_id'] = $para_id;
            $data['page_number'] = $para_page;
            $data['line_number'] = $line_number;
            $data['hifz_year_id'] = $hifz_year;




            if($hfz->insertHifzRecord($data)){
                $msg .= $tool->Message("succ","Record inserted successfully.");

                if(isset($_POST['smscheck']) && $_POST['smscheck'] == 1){
                    $search = array('{name}','{para}','{para_page}','{date}', '<br />');
                    $replace = array($_POST['name'] . " " . $_POST['fname']
                        ,$tool->GetExplodedVar($_POST['quran'])
                        ,$tool->GetInt($_POST['para_page'])
                        ,(date("d-m-Y"))
                        ,"\r\n"
                    );
                    $smsText = str_replace($search, $replace, nl2br($_POST['desc']));
                    $number = $sms->getNumber(array("id" => $student_id, "phone" => $tool->explodedVal($_POST['smsnumbers'],0)));

                    $sms->SendSMS($number,$smsText);
                    $msg .= $tool->Message("succ","SMS TEXT: <br />". $smsText." <br /> sent to <br />" . $number);
                }


            }else{
                $msg .= $tool->Message("alert","erroe");
            }

            $_SESSION['msg'] = $msg;





            $tool->Redir("hifz", "insertpara", $_POST['code'],"");
            exit;
        }


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
                                    <div><?php $tool->trans("insert_para") ?></div>
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
                                <label class="control-label"><span class="fonts"><?php $tool->trans("year") ?></span></label>
                                <div class="controls">
                                    <select name="year" id="year">
                                      <?php echo $tpf->NewYearsDropDown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("month") ?></span></label>
                                <div class="controls">
                                    <select name="month" id="month">
                                      <?php echo $tpf->NewMonthDropDown(); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("hifz_year") ?></span></label>
                                <div class="controls">
                                    <select name="hifz_year" id="hifz_year">
                                        <?php echo $tpl->GetOptionVals(array("data" => $set->getTitleTable("hifz_years"), "sel" => $hifz_year)); ?>
                                    </select>
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("para") ?></span></label>
                                <div class="controls">
                                    <select name="quran" id="quran">
                                          <?php echo $tpl->GetOptionVals(array("data" => $hfz->quranStructure())); ?>
                                      </select>
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("page_number") ?></span></label>
                                <div class="controls">
                                    <input value="<?php echo $para_page  ?>" type="number" maxlength="2" max="20" name="para_page" id="para_page">
                                </div>
                            </div>

                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("line_number") ?></span></label>
                                <div class="controls">
                                    <input value="<?php echo $line_number  ?>" type="number" maxlength="2" max="15" name="line_number" id="line_number">
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("detail") ?></span></label>
                                <div class="controls">
                                    <textarea name="desc" id="desc" style="height: 100px" required="required">{name} کا سبق {para} کے صفحہ نمبر {para_page} پر پہنچ گیا ہے۔ {date}</textarea>
                                </div>
                            </div>


                            <div class="control-group">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("sms") ?></span></label>
                                <div class="controls">
                                    <input type="checkbox" value="1" name="smscheck" id="smscheck"/>
                                    <?php
                                    echo $tpl->GetOptions(array("name" => "smsnumbers", "data" => $sms->getNumbers()));
                                     ?>
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