<?php
session_start();
error_reporting(E_ALL);
date_default_timezone_set("Asia/Karachi");
define("DRS",DIRECTORY_SEPARATOR);
define("PATH",dirname(dirname(__DIR__)));
define("VENDOR",PATH . DRS . "vendor");
define("AMG",PATH . DRS . "amg");
define("MODELS",AMG . DRS . "models");
define("INCLUDES",AMG . DRS . "includes");
define("LIBS",AMG . DRS . "libs");
define("BUNDLES",AMG . DRS . "bundles");
define("TRANSLATIONS",AMG . DRS . "translations");
define("CACHE",PATH . DRS . "cache");
define("WEBPATH", PATH . DRS . "web");
define("CSSPATH", WEBPATH . DRS . "assets" . DRS . "css");
define("JSPATH", WEBPATH . DRS . "assets" . DRS . "js");
define("URL","http://".$_SERVER['HTTP_HOST']);
define("WEB",URL . "/assets");
define("DB_HOST","mariadb");
define("DB_USER","root");
define("DB_PASS","admin");
define("DB_NAME","albadar");
define("PR","jb_");
define("AJAXCRUD",WEB ."/ajax_crud/");

define("FRONTSITEROOT","C:/xampp/htdocs/zia/al-badar/parents");
define("FRONTSITECACHE",FRONTSITEROOT ."cache/ckfinder");
define("UPLFRONTSITE",FRONTSITEROOT."/web/assets/uploads");
define("URLFRONTSITE","http://banurisite/assets/uploads");
define("FRONT_SITE_URL","http://parents-albadar");
define("FRONT_SITE_WEB",FRONT_SITE_URL ."/assets/uploads");


define("DOMAIN_NAME","albadar_edu_pk");
define('SPACE_KEY','BJFXT6PUF7UTINRBLX3T');
define('SPACE_SECRET','dEJEPpTOALL2UZH1aW0WHUAcDjVpc56hUMVCw4H0zvk');
define('SPACE_NAME','amgsolutions');
define('SPACE_REGION','ams3');
