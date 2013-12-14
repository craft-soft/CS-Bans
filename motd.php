<?php
/**
 * Motd
 */

/**
 * @author Craft-Soft Team
 * @package CS:Bans
 * @version 1.0 beta
 * @copyright (C)2013 Craft-Soft.ru.  Все права защищены.
 * @link http://craft-soft.ru/
 * @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru  «Attribution-NonCommercial-ShareAlike»
 */

define('ROOTPATH', dirname(__FILE__));

// Подключаем ядро
require_once(ROOTPATH . '/include/yii/framework/yii.php');
// Создаем приложение
Yii::createWebApplication(ROOTPATH . '/protected/config/motd.php')->run();