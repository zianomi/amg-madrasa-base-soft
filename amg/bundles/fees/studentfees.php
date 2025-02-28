<?php
Tools::getLib("QueryTemplate");
Tools::getModel("FeeModel");
Tools::getModel("StudentsModel");
$qr = new QueryTemplate();
$fee = new FeeModel();
$stu = new StudentsModel();
$typeData = $set->getTitleTable("fee_type");
$errors = array();

if(isset($_GET['del'])==1){
    if(isset($_GET['id'])){
        if(is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $fee->removeStudentFees($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if(isset($_POST['_chk'])==1) {
    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";
    $id = isset($_POST['student_id']) ? $tool->GetInt($_POST['student_id']) : 0;
    $fees = isset($_POST['fees']) ? $tool->GetInt($_POST['fees']) : 0;
    $discount = isset($_POST['discount']) ? $tool->GetInt($_POST['discount']) : 0;
    $feeType = isset($_POST['fee_type']) ? $tool->GetExplodedInt($_POST['fee_type']) : 0;

    if(empty($id)){
        $errors[] = Tools::transnoecho("please_insert_id");
    }

    if(empty($fees)){
        $errors[] = Tools::transnoecho("please_insert_fees");
    }

    if(empty($feeType)){
        $errors[] = Tools::transnoecho("please_select_fee_type");
    }

    if(count($errors)==0){
        $data['student_id'] = $id;
        $data['type_id'] = $feeType;
        $data['fees'] = $fees;
        $data['discount'] = $discount;

        try {
            $fee->query("START TRANSACTION");
            $res = $fee->insertStudentFees($data);
            $res2 = $fee->removeDefinedDiscount($id,$feeType);
            $fee->query("COMMIT");
        } catch (Exception $e) {
            $errors[] = Tools::transnoecho("Failed");
            $fee->query("ROLLBACK");
        }


        //

        if($res){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("fees_inserted"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("fees_already_exists"));
        }
        if(empty($url)){
            Tools::Redir("fees","studentfees","","");
        }
        else{
            header("Location:" . $url);
        }
        exit;
    }




}



$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$feeType = (isset($_GET['fee_type'])) ? ($tool->GetExplodedInt($_GET['fee_type'])) : 0;

$param = array("branch" => $branch, "class" => $class, "section" => $section, "session" => $session, "fee_type" => array($feeType));

$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();


?>
    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("session") ?></label><?php echo $tpl->getAllSession() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("branch") ?></label><?php echo $tpl->userBranches() ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label><?php echo $tpl->getClasses(); ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("section") ?></label><?php echo $tpl->getSecsions() ?></div>



    </div>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("fee_type") ?></label>

            <select name="fee_type" id="fee_type">
            <?php

            echo $tpl->GetOptionVals(array("name" => "fee_types", "data" => $typeData, "sel" => $feeType));
            ?>
            </select>
        </div>



        <div class="span3"><label class="fonts">&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3">&nbsp;</div>


    </div>
<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){

    if(empty($session) || empty($branch)){
        echo $tool->Message("alert",Tools::transnoecho("please_select_branch_and_session"));
        $tpl->footer();
        exit;
    }
    $data = $fee->getStudentFees($param);



    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);
?>

    <div class="body">
        <div id="printReady">

            <?php if(!empty($branch)){ ?>
            <div class="alert alert-success fonts"><?php  echo $tool->GetExplodedVar($_GET['branch']); ?></div>
            <?php } ?>

            <?php if(!empty($class)){ ?>
                <div class="alert alert-success fonts"><?php  echo $tool->GetExplodedVar($_GET['class']); ?></div>
            <?php } ?>

            <?php if(!empty($section)){ ?>
                <div class="alert alert-success fonts"><?php  echo $tool->GetExplodedVar($_GET['section']); ?></div>
            <?php } ?>
            <form method="post">
                <div class="row-fluid">

                    <div class="span3">
                        <div class="row-fluid" id="student_res"></div>
                        <form method="post">
                            <?php echo $tpl->formHidden(); ?>
                            <input type="hidden" name="url" value="<?php echo $curPageUrl ?>">
                        <table class="table table-bordered table-striped">
                            <tbody>
                            <tr>
                                <td>
                                    <label><?php Tools::trans("id") ?></label>
                                    <input value="" type="text" name="student_id" id="student_id">
                                </td>
                            </tr>
                                <tr>
                                    <td>
                                        <label><?php Tools::trans("fees") ?></label>
                                        <input type="number" name="fees" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label><?php Tools::trans("discount") ?></label>
                                        <input type="number" name="discount" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label><?php Tools::trans("fee_type") ?></label>
                                        <select name="fee_type" id="fee_type">
                                            <?php
                                            echo $tpl->GetOptionVals(array("name" => "fee_types", "data" => $typeData, "sel" => ""));
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="submit" class="btn btn-primary" value="Save">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </form>
                    </div>
                    <div class="span9">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th><?php Tools::trans("s_no") ?></th>
                            <th><?php Tools::trans("id") ?></th>
                            <th><?php Tools::trans("gr_number") ?></th>
                            <th><?php Tools::trans("name") ?></th>
                            <th><?php Tools::trans("fname") ?></th>
                            <th><?php Tools::trans("fee_type") ?></th>
                            <th><?php Tools::trans("fees") ?></th>
                            <th><?php Tools::trans("discount") ?></th>
                            <th><?php Tools::trans("delete") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i=0;

                    foreach ($data as $row){
                        $i++;
                    ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $row['student_id'] ?></td>
                            <td><?php echo $row['grnumber'] ?></td>
                            <td class="fonts"><?php echo $row['name'] ?></td>
                            <td class="fonts"><?php echo $row['fname'] ?></td>
                            <td class="fonts"><?php echo $row['title'] ?></td>
                            <td><?php echo $row['fees'] ?></td>
                            <td><?php echo $row['discount'] ?></td>
                            <td><a onclick="return confirm('Are you sure you want to delete?');" href="<?php echo Tools::makeLink("fees","studentfees&del=1&id=".$row['id']."&redir=".$curPageUrl,"","") ?>"><i class="icon-remove"></i></a> </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>

                    </div>
            </div>
            </form>
        </div>
    </div>

<?php
}
$tpl->footer();

