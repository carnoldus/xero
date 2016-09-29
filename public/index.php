<?php

/**
 * The root directory
 */
define('ROOTDIR', dirname(dirname(__FILE__)));
/**
 * The public directory (web root). This name could change depending
 * on install environment, so we determine it programmatically
 */
define('PUBLICDIR', dirname(__FILE__));
/**
 * The path to the backup docs directory, relative to the base url
 */
define('DOCSPATH', '/docs/');

require ROOTDIR.'/vendor/autoload.php';
require ROOTDIR.'/config/config.php';

use carnoldus\controllers\WebController;
use carnoldus\controllers\DataController;
use carnoldus\controllers\CLIController;

if(isset($argv)){
    $controller = new CLIController();
    echo $controller->exec($argv);
    exit(0);
}

//  If request is set, it means we're looking for data. Otherwise, just show
//  the interface page
if(isset($_GET['request'])){

    $controller = new DataController();
    echo $controller->performBackup($_GET['request']);

}else{

    $controller = new WebController(ROOTDIR.'/src/templates');
    echo $controller->html();

}