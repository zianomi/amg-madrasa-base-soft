<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "conn" . DIRECTORY_SEPARATOR . "configuration.php";

$branch = isset($_GET['branch']) ? $tool->GetExplodedInt($_GET['branch']) : "";
$amgBundle = ((isset($_GET['menu'])) && (Tools::alpha($_GET['menu']))) ? $_GET['menu'] : "";
$amgPhpFile = ((isset($_GET['page'])) && (Tools::alpha($_GET['page']))) ? $_GET['page'] : "";
$amgPassedcode = ((isset($_GET['code'])) && (Tools::numeric($_GET['code']))) ? $_GET['code'] : "";
$amgAction = ((isset($_GET['action'])) && (Tools::alpha($_GET['action']))) ? $_GET['action'] : "";
$lang = $_SESSION['lang'] ?? "en";
$transFile = require_once TRANSLATIONS . '/' . $lang . '.php';

Tools::setLang($lang);
Tools::setDirectionAuto();
Tools::setTransDir(TRANSLATIONS);
Tools::setLibsDir(LIBS);
Tools::setTransData($transFile);

$userId = Tools::getUserId();

if($amgBundle != "ws"){
    if(empty($userId)){
        $amgBundle = "settings";
        $amgPhpFile = "login";
    }
}

if(!empty($amgBundle) && !empty($amgPhpFile)){
    $phpfileToInclude = BUNDLES . DRS . $amgBundle . DRS . $amgPhpFile . ".php";
    //$transFileToInlcude = TRANSLATIONS . DRS . $amgBundle . DRS . Tools::getLang() . DRS . $amgPhpFile . ".txt";
    $tpl->setBundle($amgBundle);
    $tpl->setPhpFile($amgPhpFile);
    $tpl->setFileCode($amgPassedcode);
    $tpl->setFileAction($amgAction);
    /*if(is_readable($transFileToInlcude)){
        Tools::setTransData(json_decode(file_get_contents($transFileToInlcude),true));
    }*/
    if(is_readable($phpfileToInclude)){
        $allowedPages = $tool->excludedPages();

        if($amgBundle == "ajax" || $amgBundle == "ws"){
            $allowedPages[$amgPhpFile] = $amgPhpFile;
        }



        if(!isset($allowedPages[$amgPhpFile])){

            $set = $tpl->getSettingObj();
            $currentPage = $set->checkCurrenPage($amgBundle,$amgPhpFile);

            if(!empty($currentPage)){
                $tpl->setPageTitle($currentPage['title']);
                $tpl->setParentTitle($currentPage['parent_title']);
                $tpl->setCanAdd($currentPage['can_add']);
                $tpl->setCanEdit($currentPage['can_edit']);
                $tpl->setCanDelete($currentPage['can_delete']);
                $tpl->setCanPrint($currentPage['can_print']);
                $tpl->setCanExport($currentPage['can_export']);
            }



            if(empty($currentPage) || count($currentPage)<1){
                $tpl->setErrorCode(403);
                $tpl->renderError();
                exit;
            }
            else{
                include_once $phpfileToInclude;
                exit;
            }

        }
        else{
            include_once $phpfileToInclude;
            exit;
        }


    }
    else{
        $tpl->setErrorCode(500);
        $tpl->renderError();
    }
}
else{
    //$transFileToInlcude = TRANSLATIONS . DRS . "settings" . DRS . Tools::getLang() . DRS . "dashboard.txt";
    //Tools::setTransData(json_decode(file_get_contents($transFileToInlcude),true));
    include_once BUNDLES . DRS . "settings" . DRS . "dashboard.php";
}
