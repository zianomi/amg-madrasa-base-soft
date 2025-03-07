<?php
session_start();
error_reporting(E_ALL);
date_default_timezone_set("Asia/Karachi");
const DRS = DIRECTORY_SEPARATOR;
define("PATH",dirname(dirname(__DIR__)));
const VENDOR = PATH . DRS . "vendor";
const AMG = PATH . DRS . "amg";
const MODELS = AMG . DRS . "models";
const INCLUDES = AMG . DRS . "includes";
const LIBS = AMG . DRS . "libs";
const BUNDLES = AMG . DRS . "bundles";
const TRANSLATIONS = AMG . DRS . "trans";
const CACHE = PATH . DRS . "cache";
const WEBPATH = PATH . DRS . "web";
const CSSPATH = WEBPATH . DRS . "assets" . DRS . "css";
const JSPATH = WEBPATH . DRS . "assets" . DRS . "js";
define("URL","http://".$_SERVER['HTTP_HOST']);
const WEB = URL . "/assets";
const DB_HOST = "mariadb";
const DB_USER = "root";
const DB_PASS = "admin";
const DB_NAME = "mad_base";
const PR = "jb_";
const AJAXCRUD = WEB . "/ajax_crud/";

const FRONTSITEROOT = "C:/xampp/htdocs/zia/al-badar/parents";
const FRONTSITECACHE = FRONTSITEROOT . "cache/ckfinder";
const UPLFRONTSITE = FRONTSITEROOT . "/web/assets/uploads";
const URLFRONTSITE = "http://banurisite/assets/uploads";
const FRONT_SITE_URL = "http://parents-albadar";
const FRONT_SITE_WEB = FRONT_SITE_URL . "/assets/uploads";


const DOMAIN_NAME = "albadar_edu_pk";
const SPACE_KEY = 'BJFXT6PUF7UTINRBLX3T';
const SPACE_SECRET = 'dEJEPpTOALL2UZH1aW0WHUAcDjVpc56hUMVCw4H0zvk';
const SPACE_NAME = 'amgsolutions';
const SPACE_REGION = 'ams3';
const PASSWORD_SECRET = 'Trans21#Object12@#';
