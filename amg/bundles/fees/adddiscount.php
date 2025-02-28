<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$errors = array();


Tools::getModel("FeeModel");
$fee = new FeeModel();

$tpl->setCanExport(false);
$zone = isset($_GET['zones']) ? $tool->GetExplodedInt($_GET['zones']) : "";

if(isset($_GET['approve'])==1){
    $Id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;
    if(!empty($Id) && is_numeric($Id)){
        $requestData = $fee->getSingleRequest($Id);

        $studentId = $requestData['student_id'];
        $typeId = $requestData['type_id'];
        $refrence = $requestData['refrence'];
        $requestUserId = $requestData['user_id'];
        $amount = $requestData['amount'];
        $path = $requestData['path'];
        $image = $requestData['image'];

        $res = $fee->approveDiscount($studentId,$amount,$typeId,$refrence,$requestUserId,$path,$image);
        if($res){
            $fee->removeRequest($requestData['id']);
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("discount_approved"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("discount_approve_error"));
        }

        Tools::Redir("fees","adddiscount","","");
        exit;
    }
}

if(isset($_GET['delreq'])==1){
    $Id = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : 0;
    if(!empty($Id) && is_numeric($Id)){
            $fee->removeRequest($Id);
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("record_deleted"));
            Tools::Redir("fees","adddiscount","","");
            exit;
    }
}


$tpl->renderBeforeContent();

$param['zone'] = $zone;

$lists = $fee->approveRequestList($param);

$zoneData = array();



if(!empty($zone)){
    $zoneData = $set->getTitleTable("zones");
}

$qr->searchContentAbove();
?>


    <div class="row-fluid">
    <div class="span3"><label><?php $tool->trans("zone") ?></label>
        <?php echo $tpl->getTable("zones","zones"); ?>
    </div>
        <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

    </div>
<?php
$qr->searchContentBottom();
?>


    <div class="body">
        <div class="row-fluid">
            <div id="printReady">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                    <tr>
                        <th><?php $tool->trans("S#") ?></th>
                        <th class="fonts"><?php $tool->trans("id") ?></th>
                        <th class="fonts"><?php $tool->trans("name") ?></th>
                        <th class="fonts"><?php $tool->trans("fname") ?></th>
                        <th class="fonts"><?php $tool->trans("fees") ?></th>
                        <th class="fonts"><?php $tool->trans("amount") ?></th>
                        <th class="fonts"><?php $tool->trans("refrence") ?></th>
                        <th class="fonts"><?php $tool->trans("branch") ?></th>
                        <th class="fonts"><?php $tool->trans("class") ?></th>
                        <th class="fonts"><?php $tool->trans("section") ?></th>
                        <th class="fonts"><?php $tool->trans("user") ?></th>
                        <th><?php $tool->trans("action") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $i=0;
                    foreach ($lists as $list){
                        $i++;
                    ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo $list['student_id'] ?></td>
                            <td class="fonts"><?php echo $list['name'] ?></td>
                            <td class="fonts"><?php echo $list['fname'] ?></td>
                            <td class="fonts"><?php echo $list['type_title'] ?></td>
                            <td><?php echo $list['amount'] ?></td>
                            <td class="fonts"><?php echo $list['ref_title'] ?></td>
                            <td class="fonts"><?php echo $list['branch_title'] ?></td>
                            <td class="fonts"><?php echo $list['class_title'] ?></td>
                            <td class="fonts"><?php echo $list['section_title'] ?></td>
                            <td><?php echo $list['user_name'] ?></td>
                            <td>
                                <a title="File" href="<?php echo Tools::getUrl() . "/uploads/"?><?php echo $list['path'] ?><?php echo $list['image'] ?>" target="_blank"><i class="icon-file"></i></a>
                                <a title="Delete" onclick="return confirm('Are you sure you want to delete this request?');" href="<?php echo Tools::makeLink("fees","adddiscount&delreq=1&id=".$list['id'],"","") ?>"><i class="icon-remove"></i></a>
                                <a title="Approve" href="<?php echo Tools::makeLink("fees","adddiscount&approve=1&id=".$list['id'],"","") ?>"><i class="icon-adn"></i></a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<?php



$tpl->footer();
