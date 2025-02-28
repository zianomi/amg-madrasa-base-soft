<?php
/* @var Template $tpl */
/* @var Tools $tool */
Tools::getModel("StudentsModel");
Tools::getModel("ExamModel");
Tools::getModel("ProfileModel");
$stu = new StudentsModel();
$exm = new ExamModel();
$pro = new ProfileModel();


//$exm->insertUnPublished();

//die('');

$urlPassedID = isset($_GET['id']) ? $tool->GetInt($_GET['id']) : '';



if(isset($_GET['ins'])){
    if(!empty($_GET['ins'])){
        if(is_numeric($_GET['ins'])){
            $branch = (isset($_GET['branch'])) ? $tool->GetInt($_GET['branch']) : '';
            $class = (isset($_GET['class'])) ? $tool->GetInt($_GET['class']) : '';
            $session = (isset($_GET['session'])) ? $tool->GetInt($_GET['session']) : '';
            $section = (isset($_GET['section'])) ? $tool->GetInt($_GET['section']) : '';
            $exam = (isset($_GET['exam'])) ? $tool->GetInt($_GET['exam']) : '';
            $student = (isset($_GET['student'])) ? $tool->GetInt($_GET['student']) : '';



            $vals[] = $tool->setInsertDefaultValues(array("NULL",$student,$session,$branch,$class,$section,$exam));
            $exm->insertPublishedResult($vals);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


$row_stu = $stu->SelectStudenProfiletById($urlPassedID);

$students = $stu->studentSearch(array("id" => $urlPassedID));

$student = $students[0];

$examData = $pro->GetExam($urlPassedID);
$pubResults = $exm->studentPublishedResults($urlPassedID);

$stuPubResults = array();

foreach ($pubResults as $row){
    $stuPubResults[$row['session_id']][$row['branch_id']][$row['class_id']][$row['section_id']][$row['exam_id']] = true;
}



extract($row_stu);

$tpl->renderBeforeContent();
?>


    <div class="social-box">
        <div class="body">
            <div class="row-fluid">
                <div class="span12">
                    <div id="tabdrop" class="social-box">
                        <div class="header">
                            <h4 class="fonts">ID# <?php echo $urlPassedID ?>  <?php echo $row_stu['name'] ?> <?php echo $tpl->getGenderTrans($row_stu['gender']); ?> <?php echo $row_stu['fname'] ?></h4>
                        </div>
                        <div class="body">
                            <div class="well">
                                <div class="tabbable">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a data-toggle="tab" href="#tab1" class="fonts">Profile</a></li>
                                        <li><a data-toggle="tab" href="#tab2" class="fonts">Exams</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div id="tab1" class="tab-pane active">
                                            <?php include __DIR__ . DRS . 'profile/profile.php'; ?>
                                        </div>

                                        <div id="tab2" class="tab-pane">
                                            <?php include __DIR__ . DRS . 'profile/exams.php'; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php


$tpl->footer();
