<?php
/**
 * Motd
 *
 * @author Craft-Soft Team
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @package CS:Bans
 * @link http://craft-soft.ru/
 */

define('ROOTPATH', dirname(__FILE__));

// Подключаем ядро
require_once(ROOTPATH . '/include/yii/framework/yii.php');
// Создаем приложение
Yii::createWebApplication(ROOTPATH . '/protected/config/motd.php')->run();