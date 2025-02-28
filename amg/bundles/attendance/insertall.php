<?php
$errors = array();
Tools::getModel("AttendanceModel");
$atd = new AttendanceModel();

if(isset($_POST['_chk'])== 1){

    $vals = array();

    $date = $tool->ChangeDateFormat($_POST['date']);

    if(!$tool->ChangeDateFormat($date)){
        $errors[] = $tool->Message("alert","invalid date");
    }

    foreach($_POST['id'] as $key => $val){

        $branch = $_POST['branch_atd'][$key];
        $class = $_POST['class_atd'][$key];
        $section = $_POST['section_atd'][$key];
        $session = $_POST['session_atd'][$key];
        $attand = $_POST['attand'][$key];

        if(empty($branch)){
            $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_branch_for" . " " . $val));
        }
        if(empty($class)){
            $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_class_for" . " " . $val));
        }

        if(empty($section)){
            $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_section_for" . " " . $val));
        }

        if(empty($session)){
            $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_session_for" . " " . $val));
        }

        if(empty($attand)){
            $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_attand_for" . " " . $val));
        }

        if(empty($val)){
            $errors[] = $tool->Message("alert",$tool->transnoecho("please_select_id"));
        }
        if(!empty($branch) && !empty($class) && !empty($section) && !empty($session) && !empty($attand) && !empty($date) && !empty($val)){
            $vals[] = $tool->setInsertDefaultValues(array("NULL","$val","$branch","$class","$section","$session","$date","$attand"));
        }



    }



    if(count($errors) == 0){
        $res = $atd->insertClassAttand($vals);
        if($res["status"]){
            $_SESSION['msg'] = $res['msg'];
            $tool->Redir("attendance","insertall","","");
            exit;
        }
        else{
            echo $tool->Message("alert",$res["msg"]);
        }
    }





}






$tpl->renderBeforeContent();

if(count($errors) > 0){
    echo implode("<br />",$errors);
}
?>



<?php
echo $tpl->FormTag("post");
echo $tpl->formHidden();
?>

<div class="social-box">
    <div class="header">
        <div class="tools">


        </div>
    </div>
    <div class="body">
        <div id="jamia_msg">&nbsp;</div>

        <div id="printReady">

                <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">

                    <table id="POITable" class="insert_table_form" style="margin: 0 auto">



                        <tbody>



                        <tr>

                        <td>&nbsp;</td>
                        <td class="fonts"><?php $tool->trans("date") ?></td>
                        <td><?php $tool->trans("branch") ?></td>
                        <td>&nbsp;</td>
                      </tr>

                      <tr>
                        <td>&nbsp;</td>

                        <td><input value="<?php if(isset($_POST['date'])) echo $_POST['date'] ?>" class="validate[required] text-input" type="text" name="date" id="date" required="required"></td>
                        <td><?php echo $tpl->userBranches() ?></td>
                        <td>&nbsp;</td>
                      </tr>

                        <tr>

                        <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        <td>&nbsp;</td>
                      </tr>





                        <?php
                        if(isset($_POST['id'])){
                            foreach($_POST['id'] as $key => $val){
                        ?>
                                <input type="hidden" name="branch_atd[]" value="<?php if(isset($_POST['branch_atd'][$key])) echo $_POST['branch_atd'][$key]?>"/>
                                <input type="hidden" name="class_atd[]" value="<?php if(isset($_POST['class_atd'][$key])) echo $_POST['class_atd'][$key]?>"/>
                                <input type="hidden" name="section_atd[]" value="<?php if(isset($_POST['section_atd'][$key])) echo $_POST['section_atd'][$key]?>"/>
                                <input type="hidden" name="session_atd[]" value="<?php if(isset($_POST['session_atd'][$key])) echo $_POST['session_atd'][$key]?>"/>
                        <tr>
                            <td class="id_names"></td>
                            <td class="input_ids"><input type="number" name="id[]" id="id" value="<?php if(isset($_POST['id'][$key])) echo $_POST['id'][$key]?>" class="ids" /></td>
                            <td><select name="attand[]">
                                <?php
                                if(isset($_POST['attand'][$key])){
                                    $atdSel = $_POST['attand'][$key];
                                }
                                else{
                                    $atdSel = "";
                                }
                                echo $atd->attandPaaram($atdSel);
                                ?>
                            </select>

                            </td>
                            <td>
                                <input type="button" id="delPOIbutton" value="Delete" class="btn btn-small" onclick="deleteRow(this)"/>
                                <input type="button" id="addmorePOIbutton" class="btn btn-small" value="Add More" onclick="insRow()"/>
                            </td>
                        </tr>
                        <?php } }
                        else{
                        ?>

                        <tr>
                            <td class="id_names"></td>
                            <td class="input_ids"><input type="number" name="id[]" id="id" value="" class="ids" /></td>
                            <td><select name="attand[]">
                                <?php
                                echo $atd->attandPaaram($atdSel);
                                ?>
                            </select>

                            </td>
                            <td>
                                <input type="button" id="delPOIbutton" value="Delete" class="btn btn-small" onclick="deleteRow(this)"/>
                                <input type="button" id="addmorePOIbutton" class="btn btn-small" value="Add More" onclick="insRow()"/>
                            </td>
                        </tr>
                        <?php } ?>



                        </tbody>

                    </table>

                    <p style="text-align: center"><input type="submit" value="Save Attandance" class="btn btn-medium"></p>
                </div>







        </div>
    </div>
</div>


<?php $tpl->formClose() ?>

<script type="text/javascript">

    function deleteRow(row){
           var i=row.parentNode.parentNode.rowIndex;
           if(i > 3){
           document.getElementById('POITable').deleteRow(i);
           }
    }

    function insRow(){

        var x=document.getElementById('POITable');
        var new_row = x.rows[1].cloneNode(true);

        $('#POITable tbody > tr:last').clone(true).insertAfter('#POITable tbody>tr:last');
    }


    $(document).ready(function(){


    $('.ids').change(function(){
        var ids = $(this).val();
        var updattd = $(this).closest('tr').find('td.id_names');
        $.ajax({
           type: "POST",
            url: "<?php echo $tool->getUrl() ?>/?menu=ajax&page=attendance",
           data: "&student_id=" + ids + "&ajax_request=attandall&branch=" + $('#branch').val(),
           success: function (data) {
               updattd.html(data);
           }
       });
    })
});


</script>









<?php
    $tpl->footer();