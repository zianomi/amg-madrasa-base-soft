<?php
/**
 * Created by PhpStorm.
 * User: ZIA
 * Date: 7/20/2018
 * Time: 6:04 PM
 */

Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();
$branchIds = array();
if(isset($_GET['_chk'])==1){


    $branchesPost = isset($_GET['branches']) ? $_GET['branches'] : "";

    if(empty($branchesPost)){
        echo "Please select branch.";
        exit;
    }

    foreach ($branchesPost as $branchesPostRow){
        $branchIds[] = $tool->GetExplodedInt($branchesPostRow);
    }

Tools::getModel("FeeExportModel");
$fee = new FeeExportModel();


$classIdsArr = array();
$valClass = array();
$valBranches = array();
$valSection = array();
$valStudents = array();
$valPaids = array();
$valTypes = array();
$valUsers = array();
$valOperators = array();






$fee->setBranch($branchIds);



if(empty($fee->getBranch())){
    echo 'Nothing to export. Branches Empty.';
    exit;
}

$curSession = $fee->getCurrentSession();
$sessionId = $curSession['id'];

$fee->setSession($sessionId);

if(empty($sessionId)){

    echo 'Nothing to export. Session Not Defined.';
    exit;
}

$branchesArr = $fee->getBranches();
if(empty($branchesArr)){
    echo 'Nothing to export. Branches not defined.';
    exit;
}


$sessionClasses = $fee->sessionClasses();

if(empty($sessionClasses)){
    echo 'Nothing to export. Classes not defined.';
    exit;
}

foreach ($sessionClasses as $class){
    $classIdsArr[] = $class['id'];
}
$classIds = implode(",",$classIdsArr);

$sessionSections = $fee->sessionSections($classIds);

if(empty($sessionSections)){
    echo 'Nothing to export. Sections not defined.';
    exit;
}


$branchStudents = $fee->branchStudents();

if(empty($branchStudents)){
    echo 'Nothing to export. Students not exists.';
    exit;
}


$typesData = $fee->getFeeTypes();

$unpaidData = $fee->getUnpaidData();


if(empty($unpaidData)){
    echo 'Nothing to export.';
    exit;
}

$rowUser = $fee->getUser();
$id = $rowUser['id'];
$name = $rowUser['name'];
$username = $rowUser['username'];
$password = $rowUser['password'];

$sql = "";


$operators = $fee->getBranchOperators();


$sql .=  "INSERT INTO `users` (`id`, `name`,`username`,`password`) VALUES ";
$sql .= "('$id','$name','$username','$password')" . ";";

foreach ($operators as $operator){
    $name = $operator['name'];
    $userId = $operator['user_id'];
    $branchId = $operator['branch_id'];
    $valOperators[] = "('$name','$userId','$branchId')";
}

$sql .=  "INSERT INTO `branch_operators` (`name`, `user_id`,`branch_id`) VALUES ";
$sql .= implode(",",$valOperators) . ";";



foreach ($sessionClasses as $sessionClass){
    $id = $sessionClass['id'];
    $title = $sessionClass['eng_name'];
    $valClass[] = "($id, '$title')";
}


foreach ($branchesArr as $brancheRow){
    $id = $brancheRow['id'];
    $title = $brancheRow['eng_name'];
    $branchFone = $brancheRow['branch_fone'];
    $branchDate = $brancheRow['branch_date'];
    $valBranches[] = "($id, '$title','$branchFone','$branchDate')";
}


$sql .=  "INSERT INTO `branches` (`id`, `eng_name`,`branch_fone`,`branch_date`) VALUES ";
$sql .= implode(",",$valBranches) . ";";



$sql .=  "INSERT INTO `classes` (`id`, `title`) VALUES ";
$sql .= implode(",",$valClass) . ";";

foreach ($sessionSections as $sessionSection){
    $id = $sessionSection['id'];
    $title = $sessionSection['title'];
    $valSection[] = "($id, '$title')";
}


$sql .=  "INSERT INTO `sections` (`id`, `title`) VALUES ";
$sql .= implode(",",$valSection) . ";";


foreach ($typesData as $typesDataRow){
    $id = $typesDataRow['id'];
    $title = $typesDataRow['title_en'];
    $durationType = $typesDataRow['duration_type'];
    $valTypes[] = "($id, '$title', '$durationType')";
}

$sql .=  "INSERT INTO `fee_type` (`id`, `title`, `duration_type`) VALUES ";
$sql .= implode(",",$valTypes) . ";";


foreach ($branchStudents as $branchStudent){
    $id = $branchStudent['id'];
    $name = $branchStudent['eng_name'];
    $fname = $branchStudent['eng_fname'];
    $gender = $branchStudent['gender'];
    $grnumber = $branchStudent['grnumber'];
    $class_id = $branchStudent['class_id'];
    $branch_id = $branchStudent['branch_id'];
    $section_id = $branchStudent['section_id'];
    $valStudents[] = "($id, '$name', '$fname', $gender, '$grnumber', $branch_id, $class_id, $section_id)";
}

$valStudentsArr = array_chunk($valStudents,100);


foreach ($valStudentsArr as $rowStu){
    $sql .=  "INSERT INTO `students` (`id`, `name`, `fname`, `gender`, `grnumber`, `branch`, `class`, `section`) VALUES ";
    $sql .= implode(",",$rowStu) . ";";
}


if(empty($unpaidData)){
    echo 'Nothing to export.';
    exit;
}



foreach ($unpaidData as $row){
    $id = $row['id'];
    $student = $row['student_id'];
    $type = $row['type_id'];
    $branch = $row['branch_id'];
    $class = $row['class_id'];
    $section = $row['section_id'];
    $fees = $row['fees'];
    $discount = $row['discount'];
    $paidStatus = $row['paid_status'];
    $feeDate = ($row['fee_date']);
    $dueDate = ($row['due_date']);

    $valPaids[] = "($id,$student,$type,$branch,$class,$section,$fees,$discount,'$paidStatus','$feeDate','$dueDate',NULL,NULL,NULL)";
}


$valPaidsArr = array_chunk($valPaids,100);

foreach ($valPaidsArr as $rowPaid){

    $sql .=  "INSERT INTO `fee_paid` 
(`id`, `student_id`, `type_id`, `branch_id`, `class_id`, `section_id`, `fees`, `discount`, `paid_status`, `fee_date`, `due_date`, `invoice_id`, `user_id`, `updated`)
 VALUES ";
    $sql .= implode(",",$rowPaid) . ";";
}


$filePath = __DIR__ . DIRECTORY_SEPARATOR;

$stringData = "";
$quriesData = file_get_contents($filePath . "iqfee.sql");

$temp['tables'] = $quriesData;
$temp['rows'] = $sql;

$stringData .= json_encode($temp);

Tools::getLib("Ncrypt");
$ncrypt = new Ncrypt();
$ncrypt->set_secret_key(KEY);  // optional, but STRONGLY recommended
$ncrypt->set_secret_iv(CHIPER);  // optional, but STRONGLY recommended
$ncrypt->set_cipher(CHIPER_OPT);       // optional

// Maximum size of chunks (in bytes).
$maxRead = 1 * 1024 * 8192; // 8MB
$file = $filePath. Tools::getUserId() .'data.txt';



$fileData = $ncrypt->encrypt( $stringData );

// Give a nice name to your download.
file_put_contents($file,$fileData);
header('Content-type: text/plain');
header('Content-Length: '.filesize($file));
header('Content-Disposition: attachment; filename=data.txt');
readfile($file);
@unlink($file);
exit;
}

$tpl->renderBeforeContent();
$qr->searchContentAbove();
$branches = $set->userBranches();


?>

<div class="row-fluid">

    <div class="span6">
        <label class="fonts"><?php $tool->trans("branches") ?></label>
        <?php
        echo $tpl->GetMultiOptions(array("name" => "branches[]", "data" => $branches, "sel" => ""));
        ?>
    </div>

    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
    <div class="span3"><label>&nbsp;</label>&nbsp;</div>
</div>

<?php
$qr->searchContentBottom();
$tpl->footer();