<?php
//$re = '/$tool->trans\(([^()]|(?R))*\)/m';
//$reAjax = '/$tool->transnoecho\(([^()]|(?R))*\)/m';


$re = '/tool->trans\(([^()]|(?R))*\)/m';
$reAjax = '/tool->transnoecho\(([^()]|(?R))*\)/m';

$afterReplaceFunName = array();
$afterReplaceFunNameFinal = array();
$finalLabelData = array();

$set = new SettingModel();


$bundle = ((isset($_GET['bundle'])) && (Tools::alpha($_GET['bundle']))) ? $_GET['bundle'] : "";
$phpfile = ((isset($_GET['phpfile'])) && (Tools::alpha($_GET['phpfile']))) ? $_GET['phpfile'] : "";
$finalUserData = array();

if(isset($_POST['_chk'])==1){

    $bundle = $_POST['bundle'];
    $phpfile = $_POST['phpfile'];

    foreach($_POST['key'] as $key){
        $finalUserData[$key] = $_POST['value'][$key];
    }
    file_put_contents(TRANSLATIONS.DRS.$bundle.DRS.Tools::getLang() . DRS.$phpfile.".txt",json_encode($finalUserData));
    $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("data_updated"));
    $tool->Redir("controls","users","12","list");

    exit;
}



if(!empty($bundle) && !empty($phpfile)){

    $transDir = TRANSLATIONS . DRS . $bundle . DRS . Tools::getLang() . DRS;

    $fileForTranslate = BUNDLES . DRS . $bundle . DRS . $phpfile . ".php";






    $translatedFile = $transDir . $phpfile . ".txt";

    if(is_readable($fileForTranslate)){
        $extra = ($_GET['extra']);

        $fileData = file_get_contents($fileForTranslate);

        if(!file_exists($transDir)){
            mkdir($transDir,0777,true);
        }

        /*if(!file_exists($translatedFile)){
            file_put_contents($translatedFile,json_encode(array()));
        }*/

        $transFileFData =array();

        if(file_exists($translatedFile)){
            $transFileFData  = json_decode(file_get_contents($translatedFile),true);
        }


        $keysArray = array();



        preg_match_all($re, $fileData, $matches);







        foreach($matches[0] as $key){
            $afterReplaceFunName[] = str_replace(array("'", "\"", "&quot;"),"",str_replace(array("tool->trans(","tool->transnoecho",")","("),"",$key));
        }


        preg_match_all($reAjax, $fileData, $matches);



        foreach($matches[0] as $key){
            $afterReplaceFunName[] = str_replace(array("'", "\"", "&quot;"),"",str_replace(array("tool->trans(","tool->transnoecho",")","("),"",$key));
        }

        $savedFileKeys = $set->getFileKeys($bundle,$phpfile);



        $afterReplaceFunName = array_unique(array_merge($savedFileKeys,$afterReplaceFunName));

        $tansDBLabels = $set->getTransLabels();

        foreach($afterReplaceFunName as $afterReplaceFunNameKey => $afterReplaceFunNameVal){
            $afterReplaceFunNameFinal[$afterReplaceFunNameVal] = $afterReplaceFunNameVal;

        }





        foreach($afterReplaceFunNameFinal as $afterReplaceName){

            $finalLabelData[$afterReplaceName] = $afterReplaceName;


            if(isset($tansDBLabels[$afterReplaceName])){

                if(!empty($tansDBLabels[$afterReplaceName])){
                    $finalLabelData[$afterReplaceName] = $tansDBLabels[$afterReplaceName];
                }

            }

            if(isset($transFileFData[$afterReplaceName])){

                if(!empty($transFileFData[$afterReplaceName])){
                    $finalLabelData[$afterReplaceName] = $transFileFData[$afterReplaceName];
                }
            }













        }




        $tpl->renderBeforeContent();
        ?>

        <form id="amg_form" class="formular" method="post">

        <input type="hidden" name="_chk" value="1">
        <input type="hidden" name="bundle" value="<?php echo $bundle ?>">
        <input type="hidden" name="phpfile" value="<?php echo $phpfile ?>">

        <?php echo $tpl->formHidden(); ?>

        <section id="feeds" class="feeds social-box social-bordered social-blue">
          <div class="header">
              <h4><?php $tool->trans("label_translations") ?></h4>
          </div>
          <div class="body">

              <?php
              //ucwords(str_replace("_"," ", $val))
              foreach($finalLabelData as $key => $val){
              ?>
              <input type="hidden" name="key[<?php echo $key ?>]" value="<?php echo $key ?>">

              <div class="row col-center">
                  <div class="span3">&nbsp;</div>
                  <div class="span3"><?php echo $key ?></div>
                  <div class="span3"><input type="text" name="value[<?php echo $key ?>]" value="<?php echo $val ?>"></div>
                  <div class="span3">&nbsp;</div>
              </div>
            <?php } ?>

              <div class="form-actions txtcenter">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="reset" class="btn btn-danger">Cancel</button>
             </div>

          </div>
      </section>

        <?php
        $tpl->footer();
    }
    else{
        include_once BUNDLES . DRS . "settings" . DRS . "dashboard.php";
    }

}
else{
    include_once BUNDLES . DRS . "settings" . DRS . "ErrorPage.php";
}
