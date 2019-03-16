<?php

require __DIR__.'/../vendor/autoload.php';

function loadClass($classe)
{
    if (file_exists('../classes/'.$classe.'.php')) {
        require '../classes/'.$classe.'.php';
    }
}

spl_autoload_register('loadClass');

//Load environment
$dotenv = Dotenv\Dotenv::create(__DIR__.'/..');
$dotenv->load();

$oHelper = new Helper();
$sDir = 'web_upload';
