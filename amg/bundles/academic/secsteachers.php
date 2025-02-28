<?php
Tools::getModel("AcademicModel");
$set = new SettingModel();
$acd = new AcademicModel();



$id = isset($_GET['id']) ? $tool->intVal($_GET['id']) : '';
$name = isset($_GET['name']) ? $acd->filter($_GET['name']) : '';


$selectedSections = array();

if(!empty($id)){
    $sectionData = $acd->getTeacherSections($id);

    foreach ($sectionData as $rB){
        $selectedSections[$rB['class_id']][$rB['section_id']] = $rB['section_id'];
    }

}




$vals = array();
$errors = array();
$valSections = array();
if ((isset($_POST["_chk"])) && ($_POST["_chk"] == 1)) {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? $acd->filter($_POST['name']) : "";

    if(empty($id)){
        $errors[] = $tool->Message("alert","Please select teacher.");
    }


    if (count($errors) == 0) {

        foreach ($_POST['class'] as $key){
            if(isset($_POST['section'][$key])){
                foreach ($_POST['section'][$key] as $section){
                    $vals[] = array($id,$key,$section);
                }
            }
        }


        $acd->removeTeacherSections($id);
        $res = $acd->insertTeacherSections($vals);



        $tool->Redir("academic", "secsteachers&id=".$id."&name=".$name, $_POST['code'],$_POST['action']);
        exit;

    }


}




$tpl->renderBeforeContent();


if (count($errors) > 0) {
    echo $tool->Message("alert", $errors[0]);
}

?>





    <div class="social-box">
        <div class="header">
            <div class="tools">


            </div>
        </div>
        <div class="body">
            <div id="jamia_msg">&nbsp;</div>




            <div class="container text-center">

                <div class="row alert">
                    <div class="span12"> <?php
                        $teacherSubjects = array();
                        $teacherSubjectArr = array();
                        if (isset($_GET['id']) && !empty($_GET['id'])) {
                            echo $tool->transnoecho("sections_for") . " " . $name;
                        } else {
                            $tool->trans("add_sections");
                        }





                        ?>
                    </div>
                </div>

                <?php
                echo $tpl->formTag("post");
                echo $tpl->formHidden();
                ?>

                <input type="hidden" name="_chk" value="1">
                <input type="hidden" name="id" value="<?php if (isset($_GET['id'])) echo $_GET['id']; ?>">
                <input type="hidden" name="name" value="<?php if (isset($_GET['name'])) echo $_GET['name']; ?>">






                <div class="form-group">
                    <div class="row-fluid">
                        <div class="span4">&nbsp;</div>
                        <div class="span4">

                            <div id="menu-collapse" class="ui-accordion ui-widget ui-helper-reset ui-sortable" role="tablist">
                                <?php
                                $curSession = $set->getCurrentSession();
                                $sessionId = $curSession['id'];



                                $teachersData = $acd->teacherSessionSections($sessionId,$id);

                                $classes = $teachersData['classes'];
                                $sections = $teachersData['sections'];

                                if(empty($classes)){
                                    echo $tool->Message("alert","Please assign subjects first.");
                                    $tpl->footer();
                                    exit;
                                }

                                //$selectedSections
                                //if (isset($teacherSubjectArr[$subject['id']])) echo ' checked';
                                foreach ($classes as $class){ ?>
                                    <div class="group">
                                        <h3><a href="#" class="fonts"><?php echo $class['title'] ?></a></h3>
                                        <section class="feeds social-box social-bordered social-blue">
                                            <div class="header"><h4><i class="icon-th-list"></i><?php echo $class['title']; ?></h4></div>

                                            <table class="table table-bordered table-striped table-hover flip-scroll">
                                                <thead>
                                                <tr>
                                                    <th>&nbsp;</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <input type="hidden" name="class[<?php echo $class['id'] ?>]" value="<?php echo $class['id'] ?>" />
                                                <?php

                                                    if(isset($sections[$class['id']])){
                                                    foreach ($sections[$class['id']] as $section){

                                                        if(isset($selectedSections[$class['id']][$section['id']])){
                                                            $checked = ' checked';
                                                        }
                                                        else{
                                                            $checked = '';
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td><input type="checkbox" name="section[<?php echo $class['id'] ?>][<?php echo $section['id'] ?>]" <?php echo $checked ?> value="<?php echo $section['id'] ?>"></td>
                                                            <td><?php echo $section['title'] ?></td>
                                                        </tr>

                                                    <?php } ?>
                                                    <?php } ?>

                                                </tbody>
                                            </table>

                                        </section>

                                    </div>

                                <?php } ?>

                            </div>


                        </div>
                        <div class="span4">&nbsp;</div>

                    </div>

                </div>


                <div class="form-group">

                    <input type="submit" name="Submit" class="btn btn-success" value="<?php if (empty($id)) $tool->trans("add"); else $tool->trans("edit"); ?>"/>
                </div>

                <?php echo $tpl->formClose() ?>


            </div>



        </div>
    </div>
    <style type="text/css">
        .chosen-container{
            width: 19% !important;
        }
        [class*="span"] .chosen-container {
            width: 30%!important;
            min-width: 30%;
            max-width: 30%;
        }
    </style>
<?php
$tpl->footer();
