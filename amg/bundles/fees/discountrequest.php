<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$errors = array();

$student_id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';

Tools::getModel("FeeModel");
$fee = new FeeModel();


if(isset($_GET['del'])==1){
    $discountId = isset($_GET['discount_id']) ? $tool->GetInt($_GET['discount_id']) : 0;
    $studentId = isset($_GET['student_id']) ? $tool->GetInt($_GET['student_id']) : 0;
    if(!empty($discountId) && is_numeric($discountId)){
        if(!empty($studentId) && is_numeric($studentId)){
            $fee->removeDiscount($discountId);
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("record_deleted"));
            Tools::Redir("fees","discountrequest&_chk=1&student_id=$studentId","","");
            exit;
        }

    }
}


if(isset($_GET['delreq'])==1){
    $Id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;

    $studentId = isset($_GET['student_id']) ? $tool->GetInt($_GET['student_id']) : 0;
    if(!empty($Id) && is_numeric($Id)){
        if(!empty($studentId) && is_numeric($studentId)){
            $fee->removeRequest($Id);
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("record_deleted"));
            Tools::Redir("fees","discountrequest&_chk=1&student_id=$studentId","","");
            exit;
        }

    }
}

if(isset($_POST['_chk'])==1){
    $amount = $tool->GetInt($_POST['amount']);
    $refrence = $tool->GetInt($_POST['refrence']);
    $studentId = $tool->GetInt($_POST['student_id']);
    $typeId = $tool->GetInt($_POST['type_id']);
    //$discountId = isset($_POST['discount_id']) ? $tool->GetInt($_POST['discount_id']) : 0;
    $path = $tpl->getDatedPath();
    $imageFileName = "";

    if(empty($amount)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_amount"));
    }
    if(empty($typeId)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_fee_type"));
    }
    if(empty($refrence)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_refrence"));
    }

    if(empty($studentId)){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_insert_student"));
    }

    if(empty($_FILES['image']['name'])){
        $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_image"));
    }

    if(!empty($_FILES['image']['name'])){
        $imageFileName = $tpl->uploadFile(array("name" => "image", "extra_name" => "discount_request_image"));
    }

    if(count($errors)==0){
        $data['student_id'] = $studentId;

            $data['type_id'] = $typeId;
            $data['refrence'] = $refrence;
            $data['user_id'] = Tools::getUserId();
            $data['amount'] = $amount;
            $data['path'] = $path;
            $data['image'] = $imageFileName;


        $res = $fee->insertDiscountRequest($data);
        if($res){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("request_inserted"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("request_already_exists"));
        }
        Tools::Redir("fees","discountrequest&_chk=1&student_id=$studentId","","");
        exit;
    }

}



$tpl->renderBeforeContent();
$tool->displayErrorArray($errors);


$qr->searchContentAbove();
?>


    <div class="row-fluid" id="student_res"></div>



    <div class="row-fluid">
        <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
        <div class="span3">&nbsp;</div>
        <div class="span3">&nbsp;</div>
    </div>
<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1) {



    if(empty($student_id)){
        $tool->trans("id_required");
        $tpl->footer();
        exit;
    }
    Tools::getModel("StudentsModel");
    $stu = new StudentsModel();
    $stuData = $stu->studentSearch(array("id" => $student_id));
    $rowStu = $stuData[0];


    $branchStuid = $stu->operatorBranchStudent($rowStu['branch_id']);

    if(empty($branchStuid)){
        echo $tool->Message("alert",$tool->transnoecho("no_student_found"));
        $tpl->footer();
        exit;
    }

    ?>

    <?php
    $discountExists = false;
    $param = array("id" => $student_id, "branch" => $rowStu['branch_id'], "class" => $rowStu['class_id'], "session" => $rowStu['session_id']);
    if(!empty($student_id)){
        $datas = $fee->discountsList($param);
        $discountExists = true;
    }
    else{
        $datas = array();
    }

    $requestData = $fee->discountRequests(array("student_id" => $student_id));

    ?>

    <div  class="body">

        <div class="row-fluid">
            <div class="span4">
                <div class="alert alert-success fonts">
                    <?php $tool->trans("student_detail") ?>
                </div>
                <table class="table table-bordered table-striped table-hover">
                    <tbody>
                        <tr>
                            <td><?php $tool->trans("id") ?></td>
                            <td><?php echo $rowStu['id'] ?></td>
                        </tr>
                        <tr>
                            <td><?php $tool->trans("name") ?></td>
                            <td class="fonts"><?php echo $rowStu['name'] ?></td>
                        </tr>
                        <tr>
                            <td><?php $tool->trans("father_name") ?></td>
                            <td class="fonts"><?php echo $rowStu['fname'] ?></td>
                        </tr>
                        <tr>
                            <td><?php $tool->trans("branch") ?></td>
                            <td class="fonts"><?php echo $rowStu['branch_title'] ?></td>
                        </tr>
                        <tr>
                            <td><?php $tool->trans("class") ?></td>
                            <td class="fonts"><?php echo $rowStu['class_title'] ?></td>
                        </tr>
                        <tr>
                            <td><?php $tool->trans("section") ?></td>
                            <td class="fonts"><?php echo $rowStu['section_title'] ?></td>
                        </tr>


                    </tbody>
                </table>
            </div>
            <div class="span4">
                <div class="row-fluid">
                    <?php
                    if(count($datas)>0){
                        ?>

                        <div class="alert alert-success fonts">
                            <?php $tool->trans("given_discounts") ?>
                        </div>
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                            <tr>
                                <th class="fonts"><?php $tool->trans("type_title") ?></th>
                                <th class="fonts"><?php $tool->trans("total_fees") ?></th>
                                <th class="fonts"><?php $tool->trans("discount") ?></th>
                                <th class="fonts"><?php $tool->trans("payble") ?></th>
                                <th class="fonts"><?php $tool->trans("refrence") ?></th>
                                <th class="fonts"><?php $tool->trans("Action") ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($datas as $row){
                                ?>
                                <tr>
                                    <td class="fonts"><?php echo $row['title'] ?></td>
                                    <td class="fonts"><?php echo $row['total_fees'] ?></td>
                                    <td><?php echo $row['amount'] ?></td>
                                    <td><?php echo $row['total_fees'] - $row['amount'] ?></td>
                                    <td class="fonts"><?php echo $row['ref_title'] ?></td>
                                    <td class="fonts">
                                        <a href="<?php echo Tools::makeLink("fees","discountrequest&_chk=1&discount_id=".$row['discount_primary_id']."&fee_title=".$row['title']."&student_id=".$student_id,"","") ?>"><i class="icon-edit"></i></a>
                                        <a onclick="return confirm('Are you sure you want to delete this discount?');" href="<?php echo Tools::makeLink("fees","discountrequest&del=1&discount_id=".$row['discount_primary_id']."&student_id=".$student_id,"","") ?>"><i class="icon-remove"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                    <?php } else { ?>
                        <div class="alert alert-success  fonts">
                            <?php $tool->trans("no_discount_exists") ?>
                        </div>
                    <?php } ?>
                </div>


            </div>
            <div class="span4">

                <div class="row-fluid">
    <?php
    if(count($requestData)>0){
        ?>
                    <div class="alert alert-success  fonts">
                        <?php $tool->trans("discount_requests") ?>
                    </div>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th class="fonts"><?php $tool->trans("total_fees") ?></th>
                            <th class="fonts"><?php $tool->trans("user") ?></th>
                            <th class="fonts"><?php $tool->trans("discount") ?></th>
                            <th class="fonts"><?php $tool->trans("Action") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($requestData as $row){
                            ?>
                            <tr>

                                <td class="fonts"><?php echo $row['fee_type_title'] ?></td>

                                <td><?php echo $row['user_name'] ?></td>
                                <td><?php echo $row['amount'] ?></td>

                                <td class="fonts">
                                    <a href="<?php echo Tools::getUrl() . "/uploads/"?><?php echo $row['path'] ?><?php echo $row['image'] ?>" target="_blank"><i class="icon-file"></i></a>
                                    <a onclick="return confirm('Are you sure you want to delete this request?');" href="<?php echo Tools::makeLink("fees","discountrequest&student_id=$student_id&delreq=1&id=".$row['id'],"","") ?>"><i class="icon-remove"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>

        <?php } else {?>
        <div class="alert alert-success  fonts">
            <?php $tool->trans("no_discount_requests_exists") ?>
        </div>
        <?php } ?>
            </div>

            </div>
        </div>


    </div>




    <div class="social-box">

        <div class="body">

            <?php
            $refrence = 0;
            $typeId = 0;
            $amount = 0;
            $currentDiscout = array();
            $discountId = isset($_GET['discount_id']) ? $tool->GetInt($_GET['discount_id']) : 0;

            if(!empty($discountId)){
                $currentDiscout = $fee->getDiscountByID($discountId);
                $refrence = $currentDiscout['refrence'];
                $amount = $currentDiscout['amount'];
                $typeId = $currentDiscout['type_id'];
            }


            ?>


            <div class="row-fluid">
                <div class="span12">
                    <a href="javascript:void(0)" class="icon-btn icon-btn-green fonts">
                        <i class="icon-edit icon-2x"></i>
                        <div><span class="fonts"><?php $tool->trans("insert_request"); ?></span></div>
                    </a>

                </div>
            </div>

            <div class="row-fluid" style="text-align: center">
                <div class="span12">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="discount_id" value="<?php echo $discountId ?>">
                        <input type="hidden" name="student_id" value="<?php echo $student_id ?>">



                        <?php echo $tpl->formHidden() ?>


                        <div class="control-group">
                            <div class="controls">

                                <?php if(isset($_GET['discount_id'])){ ?>
                                    <input type="hidden" name="type_id" value="<?php echo $typeId ?>">
                                <p class="fonts"><?php if(isset($_GET['fee_title'])) echo $_GET['fee_title']; ?></p>
                                <?php } else { ?>
                                    <label class="control-label"><span class="fonts"><?php $tool->trans("fee_type") ?></span></label>
                                    <select name="type_id" id="type_id" class="full-width">
                                        <?php
                                        $dataTypes = $set->getTitleTable("fee_type");
                                        echo $tpl->GetOptionVals(array("data" => $dataTypes, "sel" => ""));
                                        ?>
                                    </select>
                                <?php } ?>

                            </div>
                        </div>


                        <div class="control-group">
                            <label class="control-label"><span class="fonts"><?php $tool->trans("amount") ?></span></label>
                            <div class="controls">
                                <input type="text" value="<?php echo $amount ?>" name="amount" id="amount">
                            </div>
                        </div>

                        <?php if(isset($_GET['discount_id'])){ ?>
                            <input type="hidden" value="<?php echo $refrence ?>" name="refrence" id="refrence">
                        <?php } else { ?>
                        <div class="control-group">
                            <div class="controls">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("any_refrence") ?></span></label>
                                <select name="refrence" id="refrence" class="full-width">
                                    <?php
                                    $dataDiscountRefrence = $set->getTitleTable("discount_refrence");
                                    echo $tpl->GetOptionVals(array("data" => $dataDiscountRefrence, "sel" => $refrence));
                                    ?>
                                </select>
                            </div>

                        <?php } ?>
                        </div>

                        <div class="control-group">
                            <div class="controls">
                                <label class="control-label"><span class="fonts"><?php $tool->trans("image") ?></span></label>
                                <input type="file" name="image" value="image">
                            </div>
                        </div>



                        <div class="row">
                            <input type="submit" name="Submit" class="btn btn-success" value="Insert"/>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <?php
}
echo $tpl->formClose();
$tpl->footer();