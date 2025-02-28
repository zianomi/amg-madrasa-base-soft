<?php
if(!isset($_POST['ajax_request'])){
    exit;
}
Tools::getModel("SettingModel");
$set = new SettingModel();



switch($_POST['ajax_request']){

    case "parent_module":
        $parentId = isset($_POST['parent_id']) ? $tool->GetExplodedInt($_POST['parent_id']) : 0;
        if(!empty($parentId)){
            $subMenus = $set->getSystemModules(array("parent" => $parentId, "level" => 2));
            $htm = "";
            $htm .= "<option value='0'>Second Level</option>";

            foreach ($subMenus as $subMenu){
                $htm .= "<option value='".$subMenu['id']."'>".$subMenu['title']."</option>";
            }

            echo $htm;

        }
    break;
    case "student_by_gr":
        Tools::getModel("StudentsModel");
        $stu = new StudentsModel();
        $gr = $set->escape($_POST['gr']);
        $rows = $stu->studentSearch(array("gr" => $gr));
        if(empty($rows)){
            echo $tool->Message("alert","Gr not exists.");
            exit;
        }
        //$row = $rows[0];
        $html = '';
        foreach ($rows as $row){
            $output = "";
            $output .= "ID: ";
            $output .= $row['id'];
            $output .= "<br />";
            $output .= $row['name'];
            $output .= " ";
            $output .= $tpl->getGenderTrans($row['gender']);
            $output .= " ";
            $output .= $row['fname'];
            $output .= "<br />";
            $output .= $row['branch_title'];
            $output .= "<br />";
            $output .= $row['class_title'];
            $output .= "<br />";
            $output .= $row['section_title'];
            $output .= "<br />";
            $output .= $row['session_title'];
            $html .= $tool->Message("succ",$output);
        }
        echo $html;
        //echo '<input type="hidden" name="name" value="'.$row['name'].'"/>';
        //echo '<input type="hidden" name="gender" value="'.$row['gender'].'"/>';
        //echo '<input type="hidden" name="fname" value="'.$row['fname'].'"/>';
        //echo '<input type="hidden" name="branch" value="'.$row['branch_id'].'"/>';
        //echo '<input type="hidden" name="class" value="'.$row['class_id'].'"/>';
        //echo '<input type="hidden" name="section" value="'.$row['section_id'].'"/>';
        //echo '<input type="hidden" name="session" value="'.$row['session_id'].'"/>';

        exit;

    break;
    case "get_class":
        $branch = $tool->GetExplodedInt($_POST['branch']);

        $finalSession = "";
        $start_date = "";
        $end_date = "";

        $session = ((isset($_POST['session'])) && (!empty($_POST['session']))) ? $tool->ExplodedInt($_POST['session']) : "";



        if(!empty($session) && is_numeric($session)){
            $finalSession = $session;
        }

        if(!empty($session) && $session == "999999999999999"){
            $start_date = $tool->ChangeDateFormat($_POST['start_date']);
            $end_date = $tool->ChangeDateFormat($_POST['end_date']);
            if($tool->checkDateFormat($start_date) && $tool->checkDateFormat($end_date)){
                $finalSession = $set->getSessionByStartAndEnd($start_date,$end_date);
            }
        }

        if(empty($finalSession)){
            $finalSession = $tpl->getSelectedSessionVal();
        }




        $classData = $set->sessionClasses($finalSession,$branch);
        $htm = $tpl->GetOptionVals(array("data" => $classData, "sel" => ""));
        echo $htm;
        exit;
    break;

    case "get_section":
       $class = $tool->GetExplodedInt($_POST['class']);
       $branch = $tool->GetExplodedInt($_POST['branch']);
        $currentSession = $tpl->getSelectedSessionVal();

        $session = ((isset($_POST['session'])) && (!empty($_POST['session']))) ? $tool->GetExplodedInt($_POST['session']) : $currentSession;
       $sectionData = $set->sessionSections($session,$class,$branch);
       $htm = $tpl->GetOptionVals(array("data" => $sectionData, "sel" => ""));
       echo $htm;
       exit;
    break;

    case "get_notesubcat":
       $notecat = $tool->GetExplodedInt($_POST['notecat']);
        $data = $set->getTitleTable("notesubcats", " AND note_cat_id = $notecat");
       $htm = $tpl->GetOptionVals(array("data" => $data, "sel" => ""));
       echo $htm;
       exit;
    break;

    case "student_id":
        $id = $tool->GetInt($_POST['student_id']);
        $row = $set->getID($id);
        if(empty($row)){
            echo $tool->Message("alert","ID not exists.");
            exit;
        }
        echo $tool->Message("succ",$row['name'] . " " . $tpl->getGenderTrans($row['gender']) . " " . $row['fname']);
        echo '<input type="hidden" name="name" value="'.$row['name'].'"/>';
        echo '<input type="hidden" name="gender" value="'.$row['gender'].'"/>';
        echo '<input type="hidden" name="fname" value="'.$row['fname'].'"/>';
        echo '<input type="hidden" name="branch" value="'.$row['branch_id'].'"/>';
        echo '<input type="hidden" name="class" value="'.$row['class_id'].'"/>';
        echo '<input type="hidden" name="section" value="'.$row['section_id'].'"/>';
        echo '<input type="hidden" name="session" value="'.$row['session_id'].'"/>';

        exit;
    break;



}
