<?php

namespace App;

use Brace\Core\BraceApp;
use Brace\Mod\Request\Zend\BraceRequestLaminasModule;

require __DIR__ . "/../vendor/autoload.php";


$app = new BraceApp();
$app->addModule(new BraceRequestLaminasModule());



