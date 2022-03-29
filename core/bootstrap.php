<?php
/**
 * Created by PhpStorm.
 * User: Nikolay
 * Date: 21.10.2019
 * Time: 10:19
 */

requireFiles(__DIR__ . '/../configs');


require_once('db.php');
require_once('baseModel.php');
require_once('modelsBootstrap.php');
require_once('baseClass.php');
require_once('baseController.php');

requireFiles(__DIR__ . '/../controllers');
requireFiles(__DIR__ . '/../models');
requireFiles(__DIR__ . '/../helpers');
requireFiles(__DIR__ . '/../services');

require_once('requestResponse.php');
require_once('request.php');
require_once('response.php');

function requireFiles($dir)
{
    foreach (glob($dir . '/*.php') as $file) {
        require_once($file);
    }
}