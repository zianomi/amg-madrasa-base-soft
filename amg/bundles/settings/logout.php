<?php
$tpl->removeMenuCache();
unset($_SESSION['UserId']);
session_destroy();
$tool->Redir("settings","login","","");