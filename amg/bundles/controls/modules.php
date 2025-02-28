<?php

//deleteDefindeDiscount();

function deleteDefindeDiscount(){
    global $set;
    $sql = "SELECT * FROM jb_student_fees";
    $res = $set->getResults($sql);
    $table = "jb_fee_discounts";
    foreach ($res as $row){


        $where = array( "student_id" => $row['student_id'], "type_id" => $row['type_id']);


        if($this->delete( $table, $where, 1 )){
            echo '<pre>'; print_r($row); echo '</pre>';
        }

    }

}

function updateCompleted(){
    $sql = "SELECT
    MAX(`jb_transfer_history`.`created`) date
    , `jb_transfer_history`.`old_branch`
    , `jb_transfer_history`.`old_class`
    , `jb_transfer_history`.`old_section`
    , `jb_transfer_history`.`old_session`
    , `jb_completed`.`student_id`
FROM
    `jb_transfer_history`
    INNER JOIN `jb_completed` 
        ON (`jb_transfer_history`.`student_id` = `jb_completed`.`student_id`)
WHERE 1 GROUP BY `jb_completed`.`student_id`";

    global $set;

    $res = $set->getResults($sql);

    foreach ($res as $row){

        if(
            ( $row['old_branch'] > 0 )
            && ($row['old_class'] > 0 )
            && ($row['old_section'] > 0 )
            && ($row['old_session'] > 0 )

        ){
            $id = $row['student_id'];
            $branch_id = $row['old_branch'];
            $class_id = $row['old_class'];
            $section_id = $row['old_section'];
            $session_id = $row['old_session'];


            $update = array( 'branch_id' => $branch_id, 'class_id' => $class_id, 'section_id' => $section_id, 'session_id' =>  $session_id);
            $update_where = array( 'student_id' => $id);
            $set->update( 'jb_completed', $update, $update_where, 1 );

            echo '<pre>'; print_r($row); echo '</pre>';
            //$set->query("UPDATE `jb_terminated` SET `branch_id` = $branch_id, `class_id` = $class_id, `section_id` = $section_id, `session_id` = $session_id WHERE `jb_terminated`.`student_id` = $id;");
        }

        //
    }




}

function updateTerminated(){
    $sql = "SELECT
    MAX(`jb_transfer_history`.`created`) date
    , `jb_transfer_history`.`old_branch`
    , `jb_transfer_history`.`old_class`
    , `jb_transfer_history`.`old_section`
    , `jb_transfer_history`.`old_session`
    , `jb_terminated`.`student_id`
FROM
    `jb_transfer_history`
    INNER JOIN `jb_terminated` 
        ON (`jb_transfer_history`.`student_id` = `jb_terminated`.`student_id`)
WHERE 1 GROUP BY `jb_terminated`.`student_id`";

    global $set;

    $res = $set->getResults($sql);

    foreach ($res as $row){

        if(
                ( $row['old_branch'] > 0 )
                && ($row['old_class'] > 0 )
                && ($row['old_section'] > 0 )
                && ($row['old_session'] > 0 )

        ){
            $id = $row['student_id'];
            $branch_id = $row['old_branch'];
            $class_id = $row['old_class'];
            $section_id = $row['old_section'];
            $session_id = $row['old_session'];


         $update = array( 'branch_id' => $branch_id, 'class_id' => $class_id, 'section_id' => $section_id, 'session_id' =>  $session_id);
         $update_where = array( 'student_id' => $id);
            $set->update( 'jb_terminated', $update, $update_where, 1 );

            echo '<pre>'; print_r($row); echo '</pre>';
            //$set->query("UPDATE `jb_terminated` SET `branch_id` = $branch_id, `class_id` = $class_id, `section_id` = $section_id, `session_id` = $session_id WHERE `jb_terminated`.`student_id` = $id;");
        }

        //
    }




}

// update studen terminated branch,class,section
//updateTerminated();
//updateCompleted();
exit;
$tpl->renderBeforeContent();


$rowsParentMenus = $tpl->parentMenuArray("all", array("published" => "yes"));
?>

<div class="social-box">
    <div class="header">
        <div class="tools">
        </div>
    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>


        <div id="printReady">



            <div class="container text-center">




                <form action="" method="post">


                    <div class="row-fluid">

                    <ul>
                        <?php foreach ($rowsParentMenus as $parentMenu){ ?>
                            <li>asd</li>
                       <?php } ?>
                    </ul>

                    </div>









                    <div class="row">
                        <input type="submit" name="Submit" class="btn btn-success" value="Insert" />
                    </div>
            </div>
        </div>


    </div>
</div>
<script>
    $(document).ready(function () {
        if ($('#note_cat').length) {
            var getNoteSubCat = makeJsLink("ajax","settings");
            $("#note_cat").change(function () {

                $("#note_sub_cat").html(optionNulVal());
                var data = 'ajax_request=get_notesubcat&notecat=' + $("#note_cat").val();
                $.ajax({
                    type: "POST",
                    url: getNoteSubCat,
                    data: data,
                    success: function (data) {
                        $("#note_sub_cat").html(data);
                    }
                })
            });
        }
    });
</script>

<?php
$tpl->footer();
