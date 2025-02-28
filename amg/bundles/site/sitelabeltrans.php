<?php
$fileDir = FRONTSITEROOT .'/Amg/Trans/';

if(isset($_POST['_chk'])==1){
    $dataString = "";
    $dataKeyString = "";
    $data = array();
    $dataKeys = array();



    foreach ($_POST['langval'] as $key => $val){

        if(!empty($_POST['langval'][$key])){
            $dataKey = $_POST['langkey'][$key];
            $dataVal = $_POST['langval'][$key];
            $data[$dataKey] = $dataVal;
            $dataKeys[$dataKey] = "";
        }


    }
    $dataString = json_encode($data);
    $dataKeyString = json_encode($dataKeys);

    $dataKeyFile = $fileDir . "transkeys.json";
    $dataFile = $fileDir . $_POST['file'] . ".json";


    file_put_contents($dataKeyFile,$dataKeyString,LOCK_EX);
    file_put_contents($dataFile,$dataString,LOCK_EX);
}

$tpl->renderBeforeContent();



$fileName = Tools::getLang();

$file = $fileDir . "transkeys.json";
$langFile = $fileDir . $fileName . ".json";

$fileData = json_decode(file_get_contents($file),true);
$langFileData = json_decode(file_get_contents($langFile),true);

$conutData = 0;

$userID = $tool->getUserId();
?>


<div class="row-fluid">
    <div class="span12">
    <section id="accordion" class="social-box">
    <div class="header">
                      <h4>Modules</h4>
                  </div>

    <div class="body">
    <form action="#" method="post" id="module_pages">

    <input type="hidden" name="_chk" value="1">

        <input type="hidden" value="<?php echo $fileName ?>" name="file">

      <?php echo $tpl->formHidden(); ?>


        <table align="center" class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S#</th>
                    <th><?php Tools::trans("Key") ?></th>
                    <th><?php Tools::trans("Trans") ?></th>
                </tr>
            </thead>
            <tbody>


            <?php
            if($userID != 1){
                $readOnly = ' readonly="readonly"';
            }
            else {
                $readOnly = "";
            }

            if(!empty($fileData)) {
                $conutData = count($fileData);
                $i = 0;
                foreach ($fileData as $res => $row) {
                    $i++;
                    if(isset($langFileData[$res])){
                        $inputVal = $langFileData[$res];
                    }
                    else{
                        $inputVal = $res;
                    }
                    ?>

                    <tr>
                        <td><?php echo $i ?></td>
                        <td><input value="<?php echo $res ?>" name="langkey[<?php echo $res ?>]"<?php echo $readOnly ?>></td>
                        <td><input value="<?php echo $inputVal ?>" name="langval[<?php echo $res ?>]"></td>
                    </tr>

                    <?php
                }
            }

    $start = $conutData+1;
    $end = $conutData+5;



    if($userID == 1){
    for ($i=$start; $i <=  $end; $i++){

        ?>

            <tr>
                <td><?php echo $i ?></td>
                <td><input value="" name="langkey[<?php echo $i ?>]"></td>
                <td><input value="" name="langval[<?php echo $i ?>]"></td>
            </tr>



    <?php
    }


}
?>

            </tbody>
        </table>

        <p style="text-align: center"><input type="submit" value="Save" class="btn btn-success"></p>

    </form>
    </div>
    </section>
    </div>
</div>

<?php


$tpl->footer();
