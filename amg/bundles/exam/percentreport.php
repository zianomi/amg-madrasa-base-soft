<?php
Tools::getLib("QueryTemplate");
Tools::getLib("TemplateForm");
Tools::getModel("ExamModel");
$tpf = new TemplateForm();
$qr = new QueryTemplate();
$exm = new ExamModel();
$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$examCols = array();



$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';

if (!empty($_GET['exam_name'])) {
    foreach ($_GET['exam_name'] as $key) {
        $keyArr = explode("-", $key);
        $keys = $keyArr[0];
        $examCols[$keys] = $keys;
    }
}

$examData = $exm->getExamNames();

$tpl->renderBeforeContent();




$qr->searchContentAbove();
?>

<style type="text/css">
    .val input {
        width: 65% !important;
    }
</style>

<div class="row-fluid">
    <div class="span3"><label class="fonts">
            <?php $tool->trans("session") ?>
        </label>
        <?php echo $tpl->getAllSession() ?>
    </div>
    <div class="span3">
        <label class="fonts">
            <?php $tool->trans("fields_required") ?>
        </label>
        <?php
        echo $tpl->GetMultiOptions(array("name" => "exam_name[]", "data" => $examData, "sel" => $examCols));
        ?>
    </div>

    <div class="span3"><label>&nbsp;</label>
        <input type="submit" class="btn">
    </div>
    <div class="span3"><label>&nbsp;</label>&nbsp;</div>
</div>





<?php
$qr->searchContentBottom();
?>
<div class="body">
    <div id="printReady">
        <?php
        if (isset($_GET['_chk']) == 1) {

            if (empty($session)) {
                echo $tool->Message("alert", $tool->transnoecho("please_select_session"));
                $tpl->footer();
                exit;
            }

            if (empty($examCols)) {
                echo $tool->Message("alert", $tool->transnoecho("please_select_exam"));
                $tpl->footer();
                exit;
            }


            $param['session'] = $session;


            $branches = $exm->getBranchNames();
            $branchArr = array();


            foreach ($_GET['exam_name'] as $key) {
                $keyArr = explode("-", $key);
                $examId = $keyArr[0];
                $examName = $keyArr[1];

                $param['exam'] = $examId;

                $res = $exm->getExamPercentages($param);


                $arr1to30[] = array();
                $arr31to40[] = array();
                $arr41to50[] = array();
                $arr51to60[] = array();
                $arr61to70[] = array();
                $arr71to80[] = array();
                $arr81to90[] = array();
                $arr91to100[] = array();

                if ($number >= 1 && $number <= 30) {
                    $arr30[] = $number;
                } elseif ($number >= 31 && $number <= 40) {
                    $arr40[] = $number;
                } elseif ($number >= 41 && $number <= 100) {
                    $arr50to100[] = $number;
                }

                foreach ($res as $row) {
                    $branchArr[$row['branch_id']] = $row['branch_id'];

                    $perncentage = $row['percentage'];


                    if ($perncentage >= 0 && $perncentage <= 30) {
                        $arr1to30[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 31 && $perncentage <= 40) {
                        $arr31to40[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 41 && $perncentage <= 50) {
                        $arr41to50[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 51 && $perncentage <= 60) {
                        $arr51to60[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 61 && $perncentage <= 70) {
                        $arr61to70[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 71 && $perncentage <= 80) {
                        $arr71to80[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 81 && $perncentage <= 90) {
                        $arr81to90[$row['branch_id']][] = $perncentage;
                    } elseif ($perncentage >= 91 && $perncentage <= 100) {
                        $arr91to100[$row['branch_id']][] = $perncentage;
                    }
                }
                ?>






                <h2 class="fonts">
                    <?php echo $examName; ?>
                </h2>



                <div style="overflow: scroll;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>S#</th>
                                <th>Branch</th>
                                <th>1-30%</th>
                                <th>31-40%</th>
                                <th>41-50%</th>
                                <th>51-60%</th>
                                <th>61-70%</th>
                                <th>71-80%</th>
                                <th>81-90%</th>
                                <th>91-100%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $oneTo30 = 0;
                            $thirtyOneTo40 = 0;
                            $fourtyOneTo50 = 0;
                            $fifttyOneTo60 = 0;
                            $sixtyOneTo70 = 0;
                            $seventyOneTo80 = 0;
                            $eightyOneTo90 = 0;
                            $ninetyOneTo100 = 0;
                            foreach ($branchArr as $branch) {


                                $i++; ?>
                                <tr>
                                    <td>
                                        <?php echo $i; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($branches[$branch]['title']))
                                            echo $branches[$branch]['title']; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($arr1to30[$branch])) {
                                            $oneTo30 += count($arr1to30[$branch]);
                                            echo count($arr1to30[$branch]);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (isset($arr31to40[$branch])) {
                                            $thirtyOneTo40 += count($arr31to40[$branch]);
                                            echo count($arr31to40[$branch]);
                                        }
                                        ?>
                                    </td>
                                    <td>

                                        <?php if (isset($arr41to50[$branch])) {
                                            $fourtyOneTo50 += count($arr41to50[$branch]);
                                            echo count($arr41to50[$branch]);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (isset($arr51to60[$branch])) {
                                            $fifttyOneTo60 += count($arr51to60[$branch]);
                                            echo count($arr51to60[$branch]);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (isset($arr61to70[$branch])) {
                                            $sixtyOneTo70 += count($arr61to70[$branch]);
                                            echo count($arr61to70[$branch]);
                                        }
                                        ?>

                                    </td>
                                    <td>
                                        <?php if (isset($arr71to80[$branch])) {
                                            $seventyOneTo80 += count($arr71to80[$branch]);
                                            echo count($arr71to80[$branch]);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php if (isset($arr81to90[$branch])) {
                                            $eightyOneTo90 += count($arr81to90[$branch]);
                                            echo count($arr81to90[$branch]);
                                        }
                                        ?>
                                    </td>
                                    <td>

                                        <?php if (isset($arr91to100[$branch])) {
                                            $ninetyOneTo100 += count($arr91to100[$branch]);
                                            echo count($arr91to100[$branch]);
                                        }
                                        ?>
                                    </td>







                                </tr>
                            <?php } ?>

                            <tr>
                                <td colspan="2"><strong>Total</strong></td>
                                <td>
                                    <strong>
                                        <?php echo $oneTo30 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $thirtyOneTo40 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $fourtyOneTo50 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $fifttyOneTo60 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $sixtyOneTo70 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $seventyOneTo80 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $eightyOneTo90 ?>
                                    </strong>
                                </td>
                                <td>
                                    <strong>
                                        <?php echo $ninetyOneTo100 ?>
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>







            <?php } ?>
        <?php } ?>
    </div>
</div>
<?php
$tpl->footer();
unset($tpf);
unset($atd);
unset($tpf);
