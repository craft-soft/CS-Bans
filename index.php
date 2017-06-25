<?php
/**
 * Индекс
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

if(getenv('DEVEL')) {
    define('YII_DEBUG',true);
    define('YII_TRACE_LEVEL',3);
} else {
    $fileName = 'db.config.inc.php';
}

// Ядро
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

// Создаем приложение
Yii::createWebApplication(__DIR__ . DIRECTORY_SEPARATOR . 'protected' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'web.php')->run();
