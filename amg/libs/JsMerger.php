<?php
require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";
use MatthiasMullie\Minify;
/**
 * Created by PhpStorm.
 * User: mzia
 * Date: 1/12/2017
 * Time: 5:18 PM
 */
class JsMerger
{

    private $jsFiles = array();
    private $jsFileName;
    private $jsPath;
    private $assetJsDir;


    public function setJsFileName($jsFileName){
        $this->jsFileName = $jsFileName;
    }

    public function getJsFileName(){
       return $this->jsFileName;
    }

    public function setJsFiles($jsFilesArr){
        $this->jsFiles = $jsFilesArr;
    }

    public function getJsFiles(){
        return $this->jsFiles;
    }

    public function setJsDir($jsDir){
        $this->assetJsDir = $jsDir;
    }

    public function getJsDir(){
        return $this->assetJsDir;
    }

    public function setPath($jsPath){
        $this->jsPath = $jsPath;
    }

    public function getPath(){
        return $this->jsPath;
    }

    public function writeJsFile(){
        $minifier = new Minify\JS();

        $drs = DIRECTORY_SEPARATOR;

        $dir = $this->getJsDir() . $drs;

        $cacheFileToWrite = $this->getPath() . $drs . $this->getJsFileName();

        foreach($this->getJsFiles() as $file){
            $minifier->add(file_get_contents($dir . $file . '.js'));
        }

        $minifier->minify($cacheFileToWrite);


    }





}