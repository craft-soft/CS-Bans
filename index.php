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

date_default_timezone_set('Europe/Moscow');

define('ROOTPATH', __DIR__);

// Ядро
require_once(ROOTPATH . '/vendor/autoload.php');

// Создаем приложение
Yii::createWebApplication(ROOTPATH . '/protected/config/main.php')->run();
