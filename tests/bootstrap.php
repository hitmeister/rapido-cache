<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 11:46 PM
 */

error_reporting(-1);
date_default_timezone_set('UTC');

define('TESTS_PATH', dirname(__FILE__) . '/../src/');

$loader = require(__DIR__ .'/../vendor/autoload.php');
