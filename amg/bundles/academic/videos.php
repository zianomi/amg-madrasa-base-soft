<?php
Tools::getLib("QueryTemplate");
Tools::getLib("Upload");
Tools::getModel("AcademicModel");
Tools::getLib("Youtube");
$acd = new AcademicModel();

$youtube = new Youtube();

$tpl->setCanExport(false);
$tpl->setCanPrint(false);

$qr = new QueryTemplate();

$class = (isset($_GET['class'])) ? $tool->GetExplodedInt($_GET['class']) : '';
$subject = (isset($_GET['subject'])) ? $tool->GetExplodedInt($_GET['subject']) : '';

//$date = (isset($_GET['date'])) ? $tool->ChangeDateFormat($_GET['date']) : "";

$param = array("class" => $class, "subject" => $subject);

/*if($tool->checkDateFormat($date)){
    $param['date'] = $date;
}*/

$subjectsData = $acd->getSubjects($param);
$classesData = $set->getTitleTable("classes");

$errors = array();

if(isset($_GET['del'])==1){
    if(isset($_GET['id'])){
        if(is_numeric($_GET['id'])){
            $id = $_GET['id'];
            $acd->removeHomeWork($id);
            $url = isset($_GET['redir']) ? urldecode($_GET['redir']) : "";
            header("Location:" . $url);
            exit;
        }
    }
}


if(isset($_POST['_chk'])==1) {
    $targetFileName = "";

    $url = isset($_POST['url']) ? urldecode($_POST['url']) : "";
    $class = isset($_POST['class']) ? $tool->GetInt($_POST['class']) : 0;
    $subject = isset($_POST['subject']) ? $tool->GetExplodedInt($_POST['subject']) : 0;
    $title = isset($_POST['title']) ? $acd->filter($_POST['title']) : 0;
    $date = isset($_POST['date']) ? $tool->ChangeDateFormat($_POST['date']) : 0;
    $youtubeUrl = isset($_POST['youtube_url']) ? $youtube->checkYoutubeUrl($_POST['youtube_url']) : 0;





    if(empty($subject)){
        $errors[] = Tools::transnoecho("please_select_subject");
    }

    if(empty($class)){
        $errors[] = Tools::transnoecho("please_enter_class");
    }


    if(!$youtubeUrl){
        $errors[] = Tools::transnoecho("please_enter_valid_url");
    }

    if(!$tool->checkDateFormat($date)){
        $errors[] = Tools::transnoecho("please_enter_valid_date");
    }



    if(count($errors)==0){
        $data['title'] = $title;
        $data['class_id'] = $class;
        $data['subject_id'] = $subject;
        $data['date'] = $date;
        $data['video_url'] = $youtubeUrl;
        $data['created_user_id'] = Tools::getUserId();
        $data['created'] = date("Y-m-d H:i:s");




        if($acd->insertVideoLesson($data)){
            $_SESSION['msg'] = $tool->Message("succ",$tool->transnoecho("video_inserted"));
        }
        else{
            $_SESSION['msg'] = $tool->Message("alert",$tool->transnoecho("error_in_insertion"));
        }
        if(empty($url)){
            Tools::Redir("academic","videos","","");
        }
        else{
            header("Location:" . $url);
        }
        exit;
    }




}





$tpl->renderBeforeContent();

$tool->displayErrorArray($errors);


$qr->searchContentAbove();



$tpl->formHidden();

?>
    <div class="row-fluid">

        <div class="span3"><label class="fonts"><?php $tool->trans("class") ?>*</label>
            <select name="class" id="class">
                <?php

                echo $tpl->GetOptionVals(array("name" => "class", "data" => $classesData, "sel" => $class));
                ?>
            </select>
        </div>
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

    if(empty($class)){
        echo $tool->Message("alert",Tools::transnoecho("please_select_class"));
        $tpl->footer();
        exit;
    }



    $res = $acd->getVideoLessons($param);



    $curPageUrl = urlencode($_SERVER['REQUEST_URI']);

    ?>

    <div class="body">
        <div id="printReady">





            <div class="row-fluid">

                <div class="span3">

                    <div class="row-fluid">
                        <div class="span12">
                            <h3><?php $tool->trans("Enter Videos") ?></h3>
                        </div>
                    </div>

                    <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                        <?php echo $tpl->formHidden(); ?>
                        <input type="hidden" name="url" value="<?php echo $curPageUrl ?>">
                        <input type="hidden" name="class" value="<?php echo $class ?>">

                        <table class="table table-bordered table-striped">
                            <tbody>


                            <tr>
                                <td>
                                    <label><?php $tool->trans("title") ?></label>
                                    <input type="text" name="title" id="title" value="" style="width: 94%">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label><?php $tool->trans("subject") ?></label>
                                    <select name="subject" id="subject" style="width: 98%">
                                        <?php
                                        echo $tpl->GetOptionVals(array("name" => "subject", "data" => $subjectsData, "sel" => $subject));
                                        ?>
                                    </select>
                                </td>
                            </tr>


                            <tr>
                                <td>
                                    <label><?php $tool->trans("date") ?></label>
                                    <input type="text" name="date" class="date" style="width: 94%">
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <label><?php $tool->trans("youtube_url") ?></label>
                                    <input value="" type="url" name="youtube_url" class="" style="width: 95%">
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

                        <?php
                        $i=0;

                        foreach ($res as $row){
                            $i++;
                            ?>

                            <div class="row">
                                <div class="span4" style="height: 400px; max-height: 400px; overflow: hidden;">

                                    <div class="thumbnail">
                                        <div class="caption">
                                            <p><?php echo $row['title'] ?></p>
                                        </div>
                                        <a href="https://www.youtube.com/watch?v=<?php echo $row['video_url'] ?>" target="_blank">
                                            <img src="<?php echo $youtube->getYouTubeThumbnail($row['video_url']) ?>" height="100" alt="Lights">
                                        </a>
                                            <div class="caption">
                                                <p><?php echo $row['date'] ?>
                                                    <a onclick="return confirm('Are you sure you want to delete?');" href="<?php echo Tools::makeLink("academic","inserthomework&del=1&id=".$row['id']."&redir=".$curPageUrl,"","") ?>"><i class="icon-remove"></i></a>
                                                </p>
                                                <p><?php echo $row['class_title'] ?> - <?php echo $row['subject_title'] ?>
                                                </p>
                                            </div>

                                    </div>

                                </div>

                            </div>

                        <?php } ?>



                </div>
            </div>

        </div>
    </div>

    <?php
}
$tpl->footer();

