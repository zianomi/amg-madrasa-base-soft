<?php
$switchtdLang = isset($_GET['lang']) ? $_GET['lang'] : "";

if(!empty($switchtdLang)){
    $_SESSION['lang'] = $switchtdLang;
}
Tools::Redir("","","","");