<?php
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "conn" . DIRECTORY_SEPARATOR . "Config.php";
require LIBS . DRS . "Tools.php";
require LIBS . DRS . "Template.php";
$tool = Tools::Instance();
$tool->setUrl(URL);
$tool->setCacheDir(CACHE);
$tool->setVendorDir(VENDOR);
$tool->setWebUrl(WEB);
$tool->setModelsDir(MODELS);
$tpl = Template::getInstance($tool);
