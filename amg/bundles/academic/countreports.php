<?php
Tools::getLib("QueryTemplate");
Tools::getModel("AcademicModel");
$acd = new AcademicModel();



$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$qr = new QueryTemplate();



$errors = array();




$date = isset($_GET['date']) ? $tool->ChangeDateFormat($_GET['date']) : "";
$toDate = isset($_GET['to_date']) ? $tool->ChangeDateFormat($_GET['to_date']) : "";


$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();

?>
    <div class="row-fluid">


        <div class="span3"><label class="fonts"><?php $tool->trans("date") ?></label>

            <input type="text" name="date" class="date">
        </div>

        <div class="span3"><label class="fonts"><?php $tool->trans("to_date") ?></label>

            <input type="text" name="to_date" class="date">
        </div>

        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>

    </div>

<?php
$qr->searchContentBottom();



if(isset($_GET['_chk'])==1){


    if(empty($date) || empty($toDate)){
        echo $tool->Message("alert","Please select dates.");
        $tpl->footer();
        exit;
    }

    if(!$tool->checkDateFormat($date) || !$tool->checkDateFormat($toDate)){
        echo $tool->Message("alert","Please enter dates in valid format.");
        $tpl->footer();
        exit;
    }

    $param['date'] = $date;
    $param['to_date'] = $toDate;
    $branches = $set->userBranches();

    foreach ($branches as $branch){
        $branchArr[] = $branch['id'];
    }
    //$param['branch'] = $branchArr;


    $res = $acd->countSessions($param);




    ?>

    <div class="body">
        <div id="printReady">





            <div class="row-fluid">


                <div class="span12">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                    <?php
                    $i=0;
                    $total = 0;

                    foreach ($res as $row){
                        $i++;


                        ?>

                        <tr>
                            <td><?php echo $row['branch_title']?></td>
                            <td><?php echo $row['tot']?></td>

                        </tr>

                    <?php
                        $total += $row['tot'];

                    } ?>
                        <tr>
                            <td><strong>Total</strong></td>
                            <td><strong><?php echo $total?></strong></td>
                        </tr>
                    </table>


                </div>
            </div>

        </div>
    </div>

    <?php
}
$tpl->footer();

