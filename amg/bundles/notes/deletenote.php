<?php
Tools::getLib("QueryTemplate");
$qr = new QueryTemplate();

$id = (isset($_GET['student_id'])) ? $tool->GetInt($_GET['student_id']) : '';
$date = ((isset($_GET['date'])) && (!empty($_GET['date']))) ? $tool->ChangeDateFormat($_GET['date']) : "";
$to_date = ((isset($_GET['to_day'])) && (!empty($_GET['to_day']))) ? $tool->ChangeDateFormat($_GET['to_day']) : "";

$noteSubCat = array();
$param = array();

if(isset($_GET['_chk'])==1){

}

if(isset($_GET['_chk'])==1){


    $tableCols = array();
    $tableCols["id"] = $tool->transnoecho("id");
    $tableCols["name"] = $tool->transnoecho("name");
    $tableCols["fname"] = $tool->transnoecho("father_name");
    $tableCols["branch_title"] = $tool->transnoecho("branch");
    $tableCols["class_title"] = $tool->transnoecho("class");
    $tableCols["section_title"] = $tool->transnoecho("section");
    $tableCols["session_title"] = $tool->transnoecho("session");
    $tableCols["date"] = $tool->transnoecho("date");
    $tableCols["desc"] = $tool->transnoecho("detail");
    //$tableCols["note_id"] = $tool->transnoecho("note_id");

    Tools::getModel("NotesModel");
   $note = new NotesModel();


    $param["date"] =  $date;
    $param["to_date"] =  $to_date;
    $param["id"] =  $id;

    //$qr->setPostFormAttribute('method="post" action=""');
    //$qr->setChecBoxparam(array("note_id"));
    $qr->setRemoveCsvCols(array("note_id"));


    $qr->setAction(true);
    $qr->setCustomActions(array
            (
            array("label" => '<i class="icon-cut"></i>',"link" => "", "class" => "class=\"delete_record\"")
    )
    );

    $qr->setAnchorDataId("note_id");

    $qr->setCols($tableCols);
    $qr->setData($note->NotesReport($param));

    if(isset($_GET['export_csv'])==1){
        $qr->exportData();
    }
}

$tpl->renderBeforeContent();
$qr->searchContentAbove();


?>
<div class="row-fluid" id="student_res"></div>

    <div class="row-fluid" id="student_res"></div>
<div class="row-fluid">
    <div class="span3"><label class="fonts"><?php $tool->trans("id_name")?></label><input value="" type="text" name="student_id" id="student_id"></div>
    <div class="span3"><label class="fonts"><?php $tool->trans("from_date") ?></label><?php echo $tpl->getDateInput(); ?></div>
        <div class="span3"><label class="fonts"><?php $tool->trans("to_date") ?></label><?php echo $tpl->getToDateInput() ?></div>
    <div class="span3"><label>&nbsp;</label><input type="submit" class="btn"></div>
</div>



<?php
$qr->searchContentBottom();

if(isset($_GET['_chk'])==1){
    $qr->contentHtml();
}

?>


<script type="text/javascript">
    $(document).ready(function(){
        $('.delete_record').click(function(){
            var getClassUrl = makeJsLink("ajax","notes");


            var data = 'ajax_request=delete_note&note_to_delete=' + $(this).data("id");
            var row = $(this).parent().parent();
           $.ajax({
               type: "POST",
               url: getClassUrl,
               data: data,
               async: false,
               success: function (data) {
                   if(data == "OK"){
                       row.remove();
                   }
                   else{
                       alert(data);
                   }

               }
           });

        })
    })
</script>



<?php
$tpl->footer();
