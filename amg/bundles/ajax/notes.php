<?php
if(!isset($_POST['ajax_request'])){
    exit;
}
Tools::getModel("NotesModel");
$note = new NotesModel();

switch($_POST['ajax_request']){

    case "delete_note":
        $noteId = $tool->GetInt($_POST['note_to_delete']);
        if(empty($noteId) || !is_numeric($noteId)){
            echo "Error in deleting";
            exit;
        }

        $note->removeNote($noteId);
        echo "OK";
        exit;

    break;

}