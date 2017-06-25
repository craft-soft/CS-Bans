<?php

define('YII_DEBUG',true);
define('YII_TRACE_LEVEL',3);

require_once(realpath(__DIR__ . DIRECTORY_SEPARATOR . '../vendor' . DIRECTORY_SEPARATOR . 'autoload.php'));

// Создаем приложение
Yii::createConsoleApplication(__DIR__ . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'console.php')->run();
