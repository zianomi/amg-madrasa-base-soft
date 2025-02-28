<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
Tools::getModel("FeeModel");
$fee = new FeeModel();

$branch = (isset($_GET['branch'])) ? $tool->GetExplodedInt($_GET['branch']) : '';
$id = (!empty($_GET['student_id'])) ? ($_GET['student_id']) : '';
$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$section = (isset($_GET['section'])) ? $tool->GetExplodedInt($_GET['section']) : '';
$session = (isset($_GET['session'])) ? $tool->GetExplodedInt($_GET['session']) : '';
$refrence = (isset($_GET['refrence'])) ? $tool->GetExplodedInt($_GET['refrence']) : '';
$feeTypeSelected = (isset($_GET['type'])) ? $tool->GetExplodedInt($_GET['type']) : '';


if(isset($_GET['classes'])){
    foreach ($_GET['classes'] as $class){
        $classArr[] = $tool->GetExplodedInt($class);
    }
}

$tpl->setCanExport(false);

$qr->renderBeforeContent();



$qr->searchContentAbove();



$feeTypesStart = $fee->getFeeTypes();
?>

<div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><?php echo $tpl->getAllSession() ?></div>
    <div class="span3"><?php echo $tpl->userBranches() ?></div>
    <!--<div class="span3"><?php /*echo $tpl->getClasses() */?></div>-->

    <div class="span3"><label class="fonts"><?php $tool->trans("class") ?></label>
        <select name="classes[]" id="class" multiple="multiple">
            <?php
            $selClass = '';
            $selecedClass = array();

            if(isset($_GET['classes'])){
                foreach ($_GET['classes'] as $classRow){
                    $selecedClass[$tool->GetExplodedInt($classRow)] = ' selected';
                }
            }


            foreach ($sessionClasses as $sessionClass){
                if(isset($_GET['_chk'])==1){

                    if(isset($selecedClass[$sessionClass['id']])){
                        $selClass = ' selected';
                    }
                    else{
                        $selClass = '';
                    }
                    ?>
                    <option value="<?php echo $sessionClass['id'] ?>-<?php echo $sessionClass['title'] ?>"<?php echo $selClass ?>><?php echo $sessionClass['title'] ?></option>
                <?php } ?>
            <?php } ?>
        </select>

    </div>
    <div class="span3"><?php echo $tpl->getSecsions() ?></div>
</div>


<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label><?php $tool->trans("refrence")?></label><?php echo $tpl->getTable("discount_refrence","refrence"); ?></div>
    <div class="span3"><label><?php $tool->trans("fee_type")?></label>
        <?php echo $tpl->GetOptions(array("name" => "type", "data" => $feeTypesStart, "sel" => $feeTypeSelected)); ?>
    </div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>

    <div class="span3">&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){

    $paramStructure = array();


    if(empty($id)){
        if(empty($session) || empty($branch)){
            echo $tool->Message("alert",$tool->transnoecho("please_select_session_branch"));
            $tpl->footer();
            exit;
        }
    }

    if(!empty($id)){
        Tools::getModel("StudentsModel");
        $stu = new StudentsModel();
        $rowStuArr = $stu->studentSearch(array("id" => $id));
        $rowStu = $rowStuArr[0];
        $param = array("id" => $id, "branch" => $rowStu['branch_id'], "class" => $rowStu['class_id'], "session" => $rowStu['session_id'], "refrence" => $refrence, "fee_type" => $feeTypeSelected);
        $paramStructure['branch'] = $rowStu['branch_id'];
        $paramStructure['session'] = $rowStu['session_id'];

        $branchStuid = $stu->operatorBranchStudent($rowStu['branch_id']);

        if(empty($branchStuid)){
            echo $tool->Message("alert",$tool->transnoecho("no_student_found"));
            $tpl->footer();
            exit;
        }
    }
    else{
        $param = array("branch" => $branch, "classes" => $classArr, "section" => $section, "session" => $session, "refrence" => $refrence, "fee_type" => $feeTypeSelected);
        $paramStructure['branch'] = $branch;
        $paramStructure['session'] = $session;
    }

    $resStructure = $fee->getFeeStructure($paramStructure);



    //$data = $fee->discountsList($param);
    $data = $fee->newDisCountList($param);

    if(empty($data)){
        echo $tool->Message("alert",$tool->transnoecho("no_result_found"));
        $tpl->footer();
        exit;
    }

    $feeTypes = array();
    $students = array();
    $discounts = array();
    $structures = array();


    foreach ($data as $row){
        $feeTypes[$row['type_id']] = array("type_id" => $row['type_id'], "type_title" => $row['type_title']);

        $students[$row['student_id']] = array(
          "student_id" => $row['student_id']
        , "name" => $row['name']
        , "fname" => $row['fname']
        , "class_id" => $row['class_id']
        , "class_title" => $row['class_title']
        , "section_title" => $row['section_title']
        );
        $discounts[$row['student_id']][$row['type_id']] = array(
          "amount" => $row['amount']
        , "refrence" => $row['refrence']
        , "ref_title" => $row['ref_title']
        , "path" => $row['path']
        , "image" => $row['image']
        );
    }

    foreach ($resStructure as $structure){
        $structures[$structure['class_id']][$structure['fee_type_id']] = $structure['fees'];
    }


?>
    <div id="printReady">
    <div  class="body">
        <div class="alert alert-success fonts" style="font-size:20px;">
        <?php echo $tool->GetExplodedVar($_GET['branch']) ?>
        </div>
        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th colspan="6">&nbsp;</th>
                <?php foreach ($feeTypes as $feeType){?>
                <th class="fonts" colspan="4"><?php echo $feeType['type_title'] ?></th>
                <?php } ?>
            </tr>
            <tr>
                <th>S#</th>
                <th class="fonts"><?php $tool->trans("id") ?></th>
                <th class="fonts"><?php $tool->trans("name") ?></th>
                <th class="fonts"><?php $tool->trans("father_name") ?></th>
                <th class="fonts"><?php $tool->trans("class") ?></th>
                <th class="fonts"><?php $tool->trans("section") ?></th>
                <?php foreach ($feeTypes as $feeType){?>
                    <th class="fonts"><?php $tool->trans("refrence") ?></th>
                    <th class="fonts"><?php $tool->trans("total_fees") ?></th>
                    <th class="fonts"><?php $tool->trans("discount") ?></th>
                    <th class="fonts"><?php $tool->trans("File") ?></th>
            <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=0;
            foreach ($students as $student){
                $i++;
            ?>
                <tr>
                    <td><?php echo $i ?></td>
                    <td><?php echo $student['student_id'] ?></td>
                    <td class="fonts"><?php echo $student['name'] ?></td>
                    <td class="fonts"><?php echo $student['fname'] ?></td>
                    <td class="fonts"><?php echo $student['class_title'] ?></td>
                    <td class="fonts"><?php echo $student['section_title'] ?></td>
                <?php foreach ($feeTypes as $feeType){
                    $refrenceTitle = "-";
                    $discountAmount = 0;
                    $path = "";
                    $image = "";
                    $linkApplication = "";
                    if(isset($discounts[$student['student_id']][$feeType['type_id']]['ref_title'])){
                        $refrenceTitle = $discounts[$student['student_id']][$feeType['type_id']]['ref_title'];
                    }
                    if(isset($discounts[$student['student_id']][$feeType['type_id']]['amount'])){
                        $discountAmount = $discounts[$student['student_id']][$feeType['type_id']]['amount'];
                    }

                    if(isset($discounts[$student['student_id']][$feeType['type_id']]['path'])){
                        $path =  $discounts[$student['student_id']][$feeType['type_id']]['path'];
                    }

                    if(isset($discounts[$student['student_id']][$feeType['type_id']]['image'])){
                        $image =  $discounts[$student['student_id']][$feeType['type_id']]['image'];
                    }

                    if(!empty($image) && !empty($path)){
                        $linkApplication = Tools::getUrl() . "/uploads/" . $path . $image;
                    }
                    ?>
                    <td class="fonts"><?php echo $refrenceTitle ?></td>
                    <td><?php
                        if($discountAmount == 0){
                            echo '-';
                        }else{
                            echo $structures[$student['class_id']][$feeType['type_id']];
                        }
                         ?></td>
                    <td><?php if($discountAmount == 0){
                            echo '-';
                        }else{
                            echo $discountAmount;
                        } ?></td>

                        <?php if(!empty($linkApplication)){ ?>
                        <td class="file_link" data-link="<?php echo $linkApplication ?>" style="cursor: pointer"><i class="icon-book"></i></td>
                        <?php } else { echo '<td>-</td>'; }?>

                <?php } ?>
                </tr>

            <?php } ?>
            </tbody>
        </table>

        <script type="text/javascript">
            $(document).ready(function () {
               $(".file_link").click(function () {
                   var link = $(this).attr("data-link");
                   window.open(link, '_blank');
               }) ;
            });
        </script>
        <?php
        $tpl->footer();
        exit;
        ?>

           <table class="table table-bordered table-striped table-hover">

               <tbody>
               <?php
               foreach ($data as $row){
               ?>
                    <tr>
                        <td><?php echo $row['id'] ?></td>
                        <td class="fonts"><?php echo $row['name'] ?></td>
                        <td class="fonts"><?php echo $row['fname'] ?></td>
                        <td class="fonts"><?php echo $row['branch_title'] ?></td>
                        <td class="fonts"><?php echo $row['class_title'] ?></td>
                        <td class="fonts"><?php echo $row['section_title'] ?></td>
                        <td><?php echo $row['total_fees'] ?></td>
                        <td><?php echo $row['amount'] ?></td>
                        <td><?php echo $row['total_fees'] - $row['amount'] ?></td>
                        <td class="fonts"><?php echo $row['title'] ?></td>
                        <td class="fonts"><?php echo $row['ref_title'] ?></td>
                        <td class="fonts">
                            <a title="File" href="<?php echo Tools::getUrl() . "/uploads/"?><?php echo $row['path'] ?><?php echo $row['image'] ?>" target="_blank"><i class="icon-book"></i></a>

                        </td>
                    </tr>
               <?php } ?>
               </tbody>
           </table>
    </div>
    </div>
<?php
}
$tpl->footer();
