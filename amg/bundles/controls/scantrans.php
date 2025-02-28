<?php
/**
 * Created by PhpStorm.
 * User: zia
 * Date: 3/31/2017
 * Time: 3:56 PM
 */

$set->setTrans(array(
    "insert_failed" => $tool->transnoecho("insert_failed")
   ,"inserted" => $tool->transnoecho("inserted")
));



function getDirContents($dir)
{
    $handle = opendir($dir);
    if ( !$handle ) return array();
    $contents = array();
    while ( $entry = readdir($handle) )
    {
        if ( $entry=='.' || $entry=='..' ) continue;

        $entry = $dir.DIRECTORY_SEPARATOR.$entry;
        if ( is_file($entry) )
        {
            $contents[] = $entry;
        }
        else if ( is_dir($entry) )
        {
            $contents = array_merge($contents, getDirContents($entry));
        }
    }
    closedir($handle);
    return $contents;
}



function getDirKeys($dirPath){

    global $tool;

    $re = '/tool->trans\(([^()]|(?R))*\)/m';
    $reAjax = '/tool->transnoecho\(([^()]|(?R))*\)/m';

    $dirData = getDirContents($dirPath);
    foreach($dirData as $dir){

        $dirAfterReplcaeBasePath = str_replace($dirPath.DRS,"",$dir);
        $dirFileArr = explode(DRS,$dirAfterReplcaeBasePath);


        $bundle = $dirFileArr[0];
        $phpFile = $dirFileArr[1];

        if (strpos($phpFile, '.php') === false) {
            continue;
        }


        $fileData = file_get_contents($dir);



        //echo '<pre>'; print_r($fileData); echo '</pre>';

        preg_match_all($re, $fileData, $matches);



        if(!empty($matches[0])){
            foreach($matches[0] as $key){
                $firstReplae = str_replace(array("tool->trans(","tool->transnoecho",")","("),"",$key);
                $secondReplace = str_replace(array("'", "\"", "&quot;"),"",$firstReplae);
                $afterReplaceFunName[] = array($bundle,$phpFile,$secondReplace);
                unset($afterReplaceFunName[0]);
            }
        }


        preg_match_all($reAjax, $fileData, $matches);

        if(!empty($matches[0])){
            foreach($matches[0] as $key){

                $firstReplae = str_replace(array("tool->trans(","tool->transnoecho",")","("),"",$key);
                $secondReplace = str_replace(array("'", "\"", "&quot;"),"",$firstReplae);
                $afterReplaceFunName[] = array($bundle,$phpFile,$secondReplace);
                unset($afterReplaceFunName[0]);
            }
        }


    }

    $transData = array();
    $dateTime = date("Y-m-d H:i:s");
    foreach ($afterReplaceFunName as $row){
        $bundle = $row[0];
        $phpFile = str_replace(".php","",$row[1]);
        $transKey = $row[2];
        $transData[] = $tool->setInsertDefaultValues(array($transKey,$bundle,$phpFile));
    }
    return $transData;
}




$transData = getDirKeys(BUNDLES);



//$res = $set->insertKeys($transData,"trans_keys");

$labelData = array();
$lang = Tools::getLangId();
foreach ($transData as $row){
    $labelKey = $row[0];
    $labelData[] = $tool->setInsertDefaultValues(array(null,$lang,$labelKey,""));
}


$res = $set->insertKeys("unique_labels",$labelData);


$_SESSION['msg'] = $res['msg'];
$tool->Redir("controls","labeltrans","","list");
exit;
