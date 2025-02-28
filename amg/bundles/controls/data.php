<?php
Tools::getModel("DataModel");
$dtm = new DataModel();
$res = $dtm->getData();




//echo '<pre>'; print_r($res); echo '</pre>';

//setGr($res);

function setGrCol($res){
    global $dtm;
    foreach ($res as $row){
        $update = array(  'gr_unique' => trim(str_replace(" ","",$row['gr_unique'])) );
        $update_where = array( 'id' => $row['id'] );
        $dtm->update( 'data', $update, $update_where, 1 );
    }
}

function setGr($res){
    //echo '<pre>'; print_r($res); echo '</pre>';
    global $dtm;
    foreach ($res as $row){


        $gr_new = $row['gr_new'];
        $grArr = explode("/",$gr_new);

        $grExists = $grArr[0];
        $grUnique = $grArr[1];


        $update = array( 'gr_exists' => $grExists, 'gr_unique' => $grUnique );
        $update_where = array( 'id' => $row['id'] );
        $dtm->update( 'data', $update, $update_where, 1 );

        echo '<pre>'; print_r($update); echo '</pre>';
    }

}

//setDateFormat($res);

insertDataNew($res);

function insertDataNew($res){
    global $dtm;

    $i=8349;
    foreach ($res as $row){
        $i++;
        $updateCols = array("id" => $i, "student_id" => $i, "student_id1" => $i, "parents_id" => $i);
        $updateWhere = array("id" => $row['id']);

        $dtm->updateDatesCol($updateCols,$updateWhere);
        echo '<pre>'; print_r($updateCols); echo '</pre>';
    }


}

// Step 2
function insertData($res){
    global $dtm;





    foreach ($res as $row){
        $students['name'] = ucwords(strtolower($row['name']));
        $students['fname'] = ucwords(strtolower($row['fname']));
        $students['gender'] = $row['gender'];
        $students['grnumber'] = $row['grnumber'];
        $students['branch_id'] = $row['branch_id'];
        $students['class_id'] = $row['class_id'];
        $students['section_id'] = $row['section_id'];
        $students['session_id'] = $row['session_id'];
        $students['doa'] = $row['doa'];
        $students['student_status'] = $row['student_status'];
        $students['created_user_id'] = 1;
        //$students['updated_user_id'] = $row[''];
        $students['created'] = date("Y-m-d H:i:s");
        //$students['updated'] = $row[''];




        if($dtm->insert( 'jb_students', $students )){

            $lastId = $dtm->lastid();


            $profile['student_id'] = $lastId;
            $profile['parents_id'] = $lastId;
            $profile['date_of_birth'] = $row['date_of_birth'];
            $profile['bloud_group'] = $row['blood_group'];
            $profile['bform'] = '';
            $profile['city'] = 1;
            $profile['sreet'] = '';
            $profile['block'] = '';
            $profile['postcode'] = '';
            $profile['current_address'] = $dtm->filter($row['postal_address']);
            $profile['injury'] = $row['any_diseases'];
            $profile['pic'] = "";
            $profile['instruc'] = "";
            $profile['approval'] = "Abdur Rahman";
            $profile['author'] = "Abdur Rahman";
            $profile['test_numbers'] = 0;
            $profile['examin_opinion'] = "OK";
            $profile['term'] = "No";
            $profile['transport'] = 1;
            $profile['created_user_id'] = 1;
            $profile['created'] = date("Y-m-d H:i:s");

            if(!$dtm->insert( 'jb_student_profile', $profile )){
                echo '<pre>'; print_r("Profile: " . $row['name'] . " " . $row['fname']. " " . $lastId . " " . $dtm->getError()); echo '</pre>';
            }

            $parents['username'] = "";
            $parents['password'] = "";
            $parents['student_id'] = $lastId;
            $parents['amergency_name'] = $row['mother_name'];
            $parents['amergency_contact'] = $row['postal_emergency_no'];
            $parents['amergency_mobile'] = $row['postal_emergency_no'];
            $parents['home_fone'] = $row['postal_tel_res'];
            $parents['father_nic'] = '';
            $parents['father_education'] = '';
            $parents['father_occupation'] = '';
            $parents['father_habits'] = '';
            $parents['father_mobile'] = $row['father_contact'];
            $parents['father_email'] = $row['email'];
            $parents['mother_name'] = $row['mother_name'];
            $parents['mother_nic'] = '';
            $parents['mother_education'] = '';
            $parents['mother_habits'] = '';
            $parents['mother_mobile'] = '';
            $parents['gargin_name'] = $students['fname'];
            $parents['gargin_nic'] = "";
            $parents['gargin_education'] = '';
            $parents['gargin_mobile'] = $row['father_contact'];
            $parents['gargin_habits'] = "";
            $parents['created_user_id'] = 1;
            $parents['created'] = date("Y-m-d H:i:s");

            if(!$dtm->insert( 'jb_student_parents', $parents )){
                echo '<pre>'; print_r("Parents: " . $row['name'] . " " . $row['fname']. " " . $lastId . " " . $dtm->getError()); echo '</pre>';
            }


            $updateCols = array("student_id" => $lastId);
            $updateWhere = array("id" => $row['id']);

            $dtm->updateDatesCol($updateCols,$updateWhere);

        }
        else{
            echo '<pre>'; print_r($students); echo '</pre>';
            //die('CALL');
        }



        //$lastId = $dtm->lastid();
    }




}


// Step 1

function setDateFormat($res){
    global $dtm;
    foreach ($res as $row){
        $id = $row['id'];
        $date_of_birth = $row['date_of_birth'];
        $admission_date = $row['admission_date'];

        $admissionDate = date("Y-m-d", strtotime($admission_date));
        $dateOfBirth = date("Y-m-d", strtotime($date_of_birth));

        $updateCols = array("date_of_birth" => $dateOfBirth, "admission_date" => $admissionDate);
        $updateWhere = array("id" => $id);

        $dtm->updateDatesCol($updateCols,$updateWhere);

        //echo '<pre>'; print_r($dateOfBirth); echo '</pre>';
    }
}
