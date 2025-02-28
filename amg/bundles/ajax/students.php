<?php
/* @var Tools $tool */
if(!isset($_POST['name'])){
    exit;
}




switch($_POST['name']){

    case "block_student":
        //echo '<pre>'; print_r($_POST); echo '</pre>';
        Tools::getModel("StudentsModel");
        $stu = new StudentsModel();
        $value = isset($_POST['value']) ? $_POST['value'] : "";
        $pk = isset($_POST['pk']) ? $tool->GetInt($_POST['pk']) : "";
        if( ($value == "current" || $value == "blocked") && is_numeric($pk)){
            $stu->blockStudentToggle($value,$pk);
        }

    break;


    case "block_teacher":
        //echo '<pre>'; print_r($_POST); echo '</pre>';
        Tools::getModel("AcademicModel");
        $academic = new AcademicModel();
        $value = isset($_POST['value']) ? $_POST['value'] : "";

        $pk = isset($_POST['pk']) ? $tool->GetInt($_POST['pk']) : "";
        if( is_numeric($pk)){

            $academic->blockTeacherToggle($value,$pk);
            echo $value;
            exit();
        }

    break;

}
