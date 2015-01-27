<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
set_time_limit(0) or die("Can not run with safe_mod = ON");

define("BASE_DIR", dirname(__FILE__).'/');

/**
 * Alle benötigten Klassen automatisch aus der Libary
 * laden.
 */
require_once(BASE_DIR.'/lib/functions.php');
function __autoload($class_name) {
    require_once (BASE_DIR.'lib/class.'.$class_name.'.php');
}

$phpBot = new phpBot();
$phpBot->check_login();

?>